<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use App\Models\AuditLogEntry;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Student foundation — 07_Data_Dictionary.md §6.
 *
 * Phase 43B delivers the Student entity only: table, model, relationships,
 * constraints, indexes, and Archive/Audit integration. No API, no
 * authentication, no registration or activation workflow, no business logic.
 *
 * These tests confirm the storage contract the Phase 44 authentication
 * endpoints depend on.
 */
final class StudentFoundationTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudent(string $activation = 'active', string $method = 'self_registration'): Student
    {
        return Student::create([
            'user_id' => User::factory()->create()->id,
            'activation_status' => $activation,
            'created_by_method' => $method,
        ]);
    }

    #[Test]
    public function the_table_carries_exactly_the_documented_attributes(): void
    {
        $expected = [
            'id', 'user_id', 'activation_status', 'account_status',
            'created_by_method', 'created_at', 'updated_at', 'archived_at',
        ];

        foreach ($expected as $column) {
            $this->assertTrue(Schema::hasColumn('students', $column), "Missing students.{$column}.");
        }

        $this->assertSame(
            [],
            array_values(array_diff(Schema::getColumnListing('students'), $expected)),
            'students contains an undocumented column.'
        );
    }

    #[Test]
    public function the_student_is_global_and_carries_no_workspace(): void
    {
        // BR-001: one global account, may study with multiple Teachers.
        // A workspace column here would contradict the entity (07 §6 Notes).
        $this->assertFalse(Schema::hasColumn('students', 'teacher_workspace_id'));
    }

    #[Test]
    public function both_confirmed_registration_methods_are_storable(): void
    {
        // BR-022: Method 1 self-registration, Method 2 Teacher-created.
        $selfRegistered = $this->makeStudent('active', 'self_registration');
        $teacherCreated = $this->makeStudent('pending_activation', 'teacher_created');

        $this->assertSame('self_registration', $selfRegistered->created_by_method);
        $this->assertTrue($teacherCreated->isPendingActivation());
    }

    #[Test]
    public function an_invalid_activation_status_is_rejected(): void
    {
        // 33 STU-02: only Active or Pending Activation are valid.
        $this->expectException(QueryException::class);

        DB::table('students')->insert([
            'user_id' => User::factory()->create()->id,
            'activation_status' => 'not_a_real_state',
            'created_by_method' => 'self_registration',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    #[Test]
    public function an_invalid_registration_method_is_rejected(): void
    {
        // 33 STU-01: "No third method exists in Version 1."
        $this->expectException(QueryException::class);

        DB::table('students')->insert([
            'user_id' => User::factory()->create()->id,
            'activation_status' => 'active',
            'created_by_method' => 'imported',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    #[Test]
    public function one_identity_cannot_hold_two_student_accounts(): void
    {
        // BR-001 / BR-022: exactly one global account; duplicates prohibited.
        $student = $this->makeStudent();

        $this->expectException(QueryException::class);

        Student::create([
            'user_id' => $student->user_id,
            'activation_status' => 'active',
            'created_by_method' => 'teacher_created',
        ]);
    }

    #[Test]
    public function a_student_requires_a_real_user_identity(): void
    {
        $this->expectException(QueryException::class);

        DB::table('students')->insert([
            'user_id' => 999_999,
            'activation_status' => 'active',
            'created_by_method' => 'self_registration',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    #[Test]
    public function a_pending_teacher_created_account_is_resolvable(): void
    {
        // 33 AUT-13 requires locating "exactly one pending-activation
        // Teacher-created account" — the lookup Phase 44 activation performs.
        $this->makeStudent('active', 'self_registration');
        $pending = $this->makeStudent('pending_activation', 'teacher_created');

        $matches = Student::query()
            ->where('activation_status', 'pending_activation')
            ->where('created_by_method', 'teacher_created')
            ->get();

        $this->assertCount(1, $matches);
        $this->assertSame($pending->id, $matches->first()?->id);
    }

    #[Test]
    public function archiving_a_student_retains_the_record_and_its_identity_link(): void
    {
        $student = $this->makeStudent();
        $userId = $student->user_id;

        $student->archive();

        $this->assertDatabaseHas('students', ['id' => $student->id]);
        $this->assertNull(Student::find($student->id));
        $this->assertNotNull(Student::withTrashed()->find($student->id));
        $this->assertSame(
            $userId,
            (int) DB::table('students')->where('id', $student->id)->value('user_id')
        );
    }

    #[Test]
    public function an_archived_student_can_be_restored(): void
    {
        $student = $this->makeStudent();
        $student->archive();

        Student::withTrashed()->find($student->id)->unarchive();

        $this->assertNotNull(Student::find($student->id));
    }

    #[Test]
    public function the_student_and_user_relationship_resolves_both_ways(): void
    {
        // 07 §6: "Student has one global User identity."
        $student = $this->makeStudent();

        $this->assertInstanceOf(User::class, $student->user);
        $this->assertSame($student->id, User::find($student->user_id)?->student?->id);
    }

    #[Test]
    public function student_actions_are_auditable(): void
    {
        $student = $this->makeStudent();

        $entry = AuditLogEntry::create([
            'actor_user_id' => $student->user_id,
            'actor_role' => 'student',
            'scope_context' => 'platform',
            'event_type' => 'create',
            'affected_entity_name' => 'Student',
            'affected_entity_id' => $student->id,
            'occurred_at' => now(),
        ]);

        $this->assertTrue($entry->exists);
        $this->assertSame('Student', $entry->fresh()->affected_entity_name);
    }
}
