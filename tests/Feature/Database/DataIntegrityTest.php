<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\TeacherStaff;
use App\Models\TeacherWorkspace;
use App\Models\TeachingSubject;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Data integrity rules enforced at the schema level.
 *
 * 06_Database_Design.md §12 lists integrity rules that "the logical database
 * design must enforce". These tests confirm the physical schema enforces the
 * ones in scope for this phase, rather than trusting application code alone.
 */
final class DataIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function makeTeacher(): Teacher
    {
        $workspace = TeacherWorkspace::create(['workspace_status' => 'active']);
        $subject = TeachingSubject::create(['subject_name' => 'Mathematics', 'selected_at' => now()]);

        $teacher = Teacher::create([
            'user_id' => User::factory()->create()->id,
            'teacher_workspace_id' => $workspace->id,
            'teaching_subject_id' => $subject->id,
        ]);

        $workspace->update(['teacher_id' => $teacher->id]);
        $subject->update(['teacher_id' => $teacher->id, 'teacher_workspace_id' => $workspace->id]);

        return $teacher;
    }

    #[Test]
    public function each_teacher_account_has_exactly_one_teacher_workspace(): void
    {
        // 06 §12: "Each Teacher account has one Teacher Workspace."
        $teacher = $this->makeTeacher();

        $this->expectException(QueryException::class);

        Teacher::create([
            'user_id' => User::factory()->create()->id,
            'teacher_workspace_id' => $teacher->teacher_workspace_id,
            'teaching_subject_id' => $teacher->teaching_subject_id,
        ]);
    }

    #[Test]
    public function each_teacher_account_has_exactly_one_teaching_subject(): void
    {
        // 06 §12 and 07 §31: one Teaching Subject per Teacher account.
        $teacher = $this->makeTeacher();

        $this->expectException(QueryException::class);

        TeachingSubject::create([
            'subject_name' => 'Physics',
            'selected_at' => now(),
            'teacher_id' => $teacher->id,
            'teacher_workspace_id' => $teacher->teacher_workspace_id,
        ]);
    }

    #[Test]
    public function a_role_name_identifies_exactly_one_role(): void
    {
        // 07 §2: Version 1 has exactly five roles.
        Role::create(['role_name' => 'teacher', 'role_scope' => 'teacher_workspace']);

        $this->expectException(QueryException::class);

        Role::create(['role_name' => 'teacher', 'role_scope' => 'platform']);
    }

    #[Test]
    public function a_staff_member_holds_one_context_per_workspace(): void
    {
        // 07 §30: Teacher Staff exist only inside the creating workspace.
        $teacher = $this->makeTeacher();
        $user = User::factory()->create();

        TeacherStaff::create([
            'user_id' => $user->id,
            'teacher_workspace_id' => $teacher->teacher_workspace_id,
        ]);

        $this->expectException(QueryException::class);

        TeacherStaff::create([
            'user_id' => $user->id,
            'teacher_workspace_id' => $teacher->teacher_workspace_id,
        ]);
    }

    #[Test]
    public function a_permission_cannot_be_assigned_twice_to_the_same_staff_member(): void
    {
        // A duplicate assignment would make "Permission Change" audit entries
        // ambiguous (23 §15.2 item 6).
        $teacher = $this->makeTeacher();

        $staff = TeacherStaff::create([
            'user_id' => User::factory()->create()->id,
            'teacher_workspace_id' => $teacher->teacher_workspace_id,
        ]);

        $permission = Permission::create([
            'permission_name' => 'example.capability',
            'permission_scope' => 'teacher_workspace',
        ]);

        $row = [
            'teacher_staff_id' => $staff->id,
            'permission_id' => $permission->id,
            'teacher_workspace_id' => $teacher->teacher_workspace_id,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('permission_teacher_staff')->insert($row);

        $this->expectException(QueryException::class);
        DB::table('permission_teacher_staff')->insert($row);
    }

    #[Test]
    public function a_reference_to_a_nonexistent_record_is_rejected(): void
    {
        // 06 §13: referential integrity must preserve relationships.
        $this->expectException(QueryException::class);

        DB::table('teacher_staff')->insert([
            'user_id' => 999_999,
            'teacher_workspace_id' => 999_999,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    #[Test]
    public function the_permission_catalog_is_empty_because_q011_is_pending(): void
    {
        // 07 §3 and 06 §4: "Teacher Staff permission granularity remains
        // PENDING and must not be silently assumed."
        $this->assertSame(0, Permission::withTrashed()->count());
        $this->assertSame(0, DB::table('permission_teacher_staff')->count());
    }

    #[Test]
    public function no_role_is_seeded_by_the_migrations(): void
    {
        // Seeding roles would assert a permission model before Q-011 resolves.
        $this->assertSame(0, Role::withTrashed()->count());
    }
}
