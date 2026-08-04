<?php

declare(strict_types=1);

namespace Tests\Feature\Authorization;

use App\Features\Authorization\Exceptions\PermissionNotAssignableException;
use App\Features\Authorization\Services\PermissionAssignmentService;
use App\Features\Authorization\Services\PermissionResolver;
use App\Models\AuditLogEntry;
use App\Models\Permission;
use App\Models\Role;
use App\Models\TeacherStaff;
use App\Models\TeacherWorkspace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Teacher Staff resolution — an intersection, never an inheritance.
 *
 * "Teacher Staff hold only permissions assigned by the Teacher" (08 §2.6), and
 * access is "always Conditional on Teacher-assigned permissions inside the
 * creating Teacher Workspace" (09 §5).
 */
final class TeacherStaffPermissionTest extends TestCase
{
    use RefreshDatabase;

    private PermissionResolver $resolver;

    private PermissionAssignmentService $assignment;

    private TeacherWorkspace $workspaceA;

    private TeacherWorkspace $workspaceB;

    private User $teacher;

    private User $staffUser;

    private TeacherStaff $staff;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--force' => true]);

        $this->resolver = app(PermissionResolver::class);
        $this->assignment = app(PermissionAssignmentService::class);

        $this->workspaceA = TeacherWorkspace::create(['workspace_status' => 'active']);
        $this->workspaceB = TeacherWorkspace::create(['workspace_status' => 'active']);

        $this->teacher = $this->makeUser('teacher@example.test', 'teacher', $this->workspaceA->id);
        $this->staffUser = $this->makeUser('staff@example.test', 'teacher_staff', $this->workspaceA->id);

        $this->staff = TeacherStaff::create([
            'user_id' => $this->staffUser->id,
            'teacher_workspace_id' => $this->workspaceA->id,
        ]);
    }

    private function makeUser(string $email, string $role, ?int $workspaceId): User
    {
        $user = User::create(['name' => 'Actor', 'email' => $email, 'password' => 'Passw0rdX']);
        $user->roles()->attach(
            Role::where('role_name', $role)->value('id'),
            ['teacher_workspace_id' => $workspaceId],
        );

        return $user->fresh();
    }

    private function permission(string $name): Permission
    {
        return Permission::where('permission_name', $name)->firstOrFail();
    }

    #[Test]
    public function staff_hold_nothing_until_the_teacher_assigns(): void
    {
        // Q-011 is PENDING: no preset, no default grant.
        $this->assertFalse(
            $this->resolver->holds($this->staffUser, 'teacher_workspace.group.create', $this->workspaceA->id)
        );
    }

    #[Test]
    public function an_assigned_capability_becomes_available(): void
    {
        $this->assignment->assign(
            $this->staff,
            $this->permission('teacher_workspace.group.create'),
            $this->teacher,
        );

        $this->assertTrue(
            $this->resolver->allows($this->staffUser->fresh(), 'teacher_workspace.group.create', $this->workspaceA->id)
        );
    }

    #[Test]
    public function an_unassigned_capability_stays_denied(): void
    {
        // Staff do not inherit the Teacher's full set.
        $this->assignment->assign(
            $this->staff,
            $this->permission('teacher_workspace.group.create'),
            $this->teacher,
        );

        $this->assertFalse(
            $this->resolver->allows($this->staffUser->fresh(), 'teacher_workspace.student.create', $this->workspaceA->id)
        );
    }

    #[Test]
    public function staff_access_does_not_reach_another_workspace(): void
    {
        $this->assignment->assign(
            $this->staff,
            $this->permission('teacher_workspace.group.create'),
            $this->teacher,
        );

        $this->assertFalse(
            $this->resolver->allows($this->staffUser->fresh(), 'teacher_workspace.group.create', $this->workspaceB->id)
        );
    }

    #[Test]
    public function self_assignment_can_never_be_delegated(): void
    {
        // 09 §12: "Teacher Staff cannot grant themselves permissions."
        $this->expectException(PermissionNotAssignableException::class);

        $this->assignment->assign(
            $this->staff,
            $this->permission('teacher_workspace.teacher_staff.self_assign_permission'),
            $this->teacher,
        );
    }

    #[Test]
    public function a_teacher_cannot_delegate_a_platform_capability(): void
    {
        // A Teacher can only delegate what a Teacher holds.
        $this->expectException(PermissionNotAssignableException::class);

        $this->assignment->assign(
            $this->staff,
            $this->permission('platform.teacher.create'),
            $this->teacher,
        );
    }

    #[Test]
    public function revoking_removes_access(): void
    {
        $permission = $this->permission('teacher_workspace.group.create');

        $this->assignment->assign($this->staff, $permission, $this->teacher);
        $this->assignment->revoke($this->staff, $permission, $this->teacher);

        $this->assertFalse(
            $this->resolver->allows($this->staffUser->fresh(), 'teacher_workspace.group.create', $this->workspaceA->id)
        );
    }

    #[Test]
    public function an_archived_staff_account_cannot_act(): void
    {
        // 07 §30: "Archived staff cannot act actively."
        $this->assignment->assign(
            $this->staff,
            $this->permission('teacher_workspace.group.create'),
            $this->teacher,
        );

        $this->staff->archive();

        $this->assertFalse(
            $this->resolver->allows($this->staffUser->fresh(), 'teacher_workspace.group.create', $this->workspaceA->id)
        );
        $this->assertDatabaseHas('teacher_staff', ['id' => $this->staff->id]);
    }

    #[Test]
    public function restoring_an_archived_staff_account_returns_access(): void
    {
        $this->assignment->assign(
            $this->staff,
            $this->permission('teacher_workspace.group.create'),
            $this->teacher,
        );

        $this->staff->archive();
        TeacherStaff::withTrashed()->find($this->staff->id)->unarchive();

        $this->assertTrue(
            $this->resolver->allows($this->staffUser->fresh(), 'teacher_workspace.group.create', $this->workspaceA->id)
        );
    }

    #[Test]
    public function assignment_is_audited_as_a_permission_change(): void
    {
        // 23 §15.2 item 6.
        $this->assignment->assign(
            $this->staff,
            $this->permission('teacher_workspace.group.create'),
            $this->teacher,
        );

        $entry = AuditLogEntry::where('event_type', 'permission_change')->latest('id')->first();

        $this->assertNotNull($entry);
        $this->assertSame($this->teacher->id, $entry->actor_user_id);
        $this->assertSame('teacher_workspace', $entry->scope_context);
        $this->assertSame($this->workspaceA->id, $entry->teacher_workspace_id);
        $this->assertSame('Teacher Staff', $entry->affected_entity_name);
        $this->assertSame($this->staff->id, $entry->affected_entity_id);
        $this->assertSame('assigned', $entry->event_details['outcome'] ?? null);
        $this->assertSame(
            'teacher_workspace.group.create',
            $entry->event_details['permission_name'] ?? null
        );
    }

    #[Test]
    public function revocation_is_audited_as_a_permission_change(): void
    {
        $permission = $this->permission('teacher_workspace.group.create');

        $this->assignment->assign($this->staff, $permission, $this->teacher);
        $this->assignment->revoke($this->staff, $permission, $this->teacher);

        $entry = AuditLogEntry::where('event_type', 'permission_change')->latest('id')->first();

        $this->assertSame('revoked', $entry?->event_details['outcome'] ?? null);
    }

    #[Test]
    public function a_repeated_assignment_produces_no_duplicate_or_second_audit(): void
    {
        $permission = $this->permission('teacher_workspace.group.create');

        $this->assignment->assign($this->staff, $permission, $this->teacher);
        $this->assignment->assign($this->staff, $permission, $this->teacher);

        $this->assertSame(1, \Illuminate\Support\Facades\DB::table('permission_teacher_staff')->count());
        $this->assertSame(1, AuditLogEntry::where('event_type', 'permission_change')->count());
    }
}
