<?php

declare(strict_types=1);

namespace Tests\Feature\Authorization;

use App\Features\Authorization\Services\PermissionResolver;
use App\Models\Role;
use App\Models\TeacherWorkspace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Permission resolution — `08_RBAC.md` §14, frozen as `RBAC_CONTRACT.md` §5.
 *
 * Deny by default, scope before permission, and no inheritance between roles.
 */
final class PermissionResolutionTest extends TestCase
{
    use RefreshDatabase;

    private PermissionResolver $resolver;

    private TeacherWorkspace $workspaceA;

    private TeacherWorkspace $workspaceB;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--force' => true]);

        $this->resolver = app(PermissionResolver::class);
        $this->workspaceA = TeacherWorkspace::create(['workspace_status' => 'active']);
        $this->workspaceB = TeacherWorkspace::create(['workspace_status' => 'active']);
    }

    private function userWithRole(string $role, ?int $workspaceId = null): User
    {
        $user = User::create([
            'name' => 'Actor',
            'email' => $role.'-'.uniqid().'@example.test',
            'password' => 'Passw0rdX',
        ]);

        $user->roles()->attach(
            Role::where('role_name', $role)->value('id'),
            ['teacher_workspace_id' => $workspaceId],
        );

        return $user->fresh();
    }

    #[Test]
    public function a_user_without_a_role_holds_nothing(): void
    {
        // Deny by default (08 §2.1).
        $user = User::create(['name' => 'N', 'email' => 'n@example.test', 'password' => 'Passw0rdX']);

        $this->assertSame([], $this->resolver->contextsFor($user));
        $this->assertFalse($this->resolver->allows($user, 'teacher_workspace.group.create'));
    }

    #[Test]
    public function a_teacher_is_allowed_inside_their_own_workspace(): void
    {
        $teacher = $this->userWithRole('teacher', $this->workspaceA->id);

        $this->assertTrue(
            $this->resolver->allows($teacher, 'teacher_workspace.group.create', $this->workspaceA->id)
        );
    }

    #[Test]
    public function a_teacher_is_denied_in_another_workspace(): void
    {
        // 08 §2.7: no cross-Teacher access. Scope resolves before permission,
        // so holding the capability is not enough.
        $teacher = $this->userWithRole('teacher', $this->workspaceA->id);

        $this->assertFalse(
            $this->resolver->allows($teacher, 'teacher_workspace.group.create', $this->workspaceB->id)
        );
    }

    #[Test]
    public function a_super_admin_is_not_a_teacher_inside_a_workspace(): void
    {
        // 08 §3: "does not operate inside Teacher Workspaces as a Teacher."
        $superAdmin = $this->userWithRole('super_admin');

        $this->assertTrue($this->resolver->allows($superAdmin, 'platform.teacher.create'));
        $this->assertFalse(
            $this->resolver->allows($superAdmin, 'teacher_workspace.group.create', $this->workspaceA->id)
        );
    }

    #[Test]
    public function a_teacher_holds_no_platform_permission(): void
    {
        $teacher = $this->userWithRole('teacher', $this->workspaceA->id);

        $this->assertFalse($this->resolver->allows($teacher, 'platform.teacher.create'));
    }

    #[Test]
    public function a_student_is_limited_to_their_own_scope(): void
    {
        $student = $this->userWithRole('student');

        $this->assertTrue($this->resolver->allows($student, 'student_account.dashboard.view'));
        $this->assertFalse(
            $this->resolver->allows($student, 'teacher_workspace.group.create', $this->workspaceA->id)
        );
    }

    #[Test]
    public function a_parent_is_read_only(): void
    {
        // 08 §3: a Parent cannot modify anything.
        $parent = $this->userWithRole('parent');

        $this->assertTrue($this->resolver->allows($parent, 'parent_linked_student.dashboard.view'));
        $this->assertFalse($this->resolver->allows($parent, 'parent_linked_student.group.update'));
        $this->assertFalse($this->resolver->allows($parent, 'parent_linked_student.attendance.update'));
    }

    #[Test]
    public function a_conditional_grant_is_not_an_allow(): void
    {
        // The matrix condition is a resource-level rule the resolver cannot
        // evaluate, so Conditional never becomes a blanket allow.
        $superAdmin = $this->userWithRole('super_admin');
        $permission = 'platform.dashboard.view_teacher_private_content';

        $this->assertFalse($this->resolver->allows($superAdmin, $permission));
        $this->assertTrue($this->resolver->isConditional($superAdmin, $permission));
        $this->assertTrue($this->resolver->holds($superAdmin, $permission));
    }

    #[Test]
    public function a_pending_decision_stays_denied_at_the_gate(): void
    {
        // Q-012 remains PENDING; Conditional-pending must not become access.
        $superAdmin = $this->userWithRole('super_admin');

        $this->assertTrue(
            Gate::forUser($superAdmin)->denies('platform.dashboard.view_teacher_private_content')
        );
    }

    #[Test]
    public function an_unknown_permission_is_denied(): void
    {
        $teacher = $this->userWithRole('teacher', $this->workspaceA->id);

        $this->assertFalse(
            $this->resolver->allows($teacher, 'teacher_workspace.invented.capability', $this->workspaceA->id)
        );
        $this->assertTrue(Gate::forUser($teacher)->denies('teacher_workspace.invented.capability'));
    }

    #[Test]
    public function an_archived_role_grants_nothing(): void
    {
        // 07 §2: Archive state applies to Role.
        $teacher = $this->userWithRole('teacher', $this->workspaceA->id);
        $role = Role::where('role_name', 'teacher')->first();

        $role->archive();

        $this->assertFalse(
            $this->resolver->allows($teacher->fresh(), 'teacher_workspace.group.create', $this->workspaceA->id)
        );
    }

    #[Test]
    public function every_confirmed_permission_is_registered_as_a_gate(): void
    {
        $abilities = array_keys(Gate::abilities());

        foreach (\App\Features\Authorization\Support\PermissionMatrix::allPermissions() as $permission) {
            $this->assertContains($permission, $abilities, "Missing gate: {$permission}.");
        }
    }
}
