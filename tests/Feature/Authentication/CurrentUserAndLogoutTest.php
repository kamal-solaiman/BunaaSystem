<?php

declare(strict_types=1);

namespace Tests\Feature\Authentication;

use App\Models\AuditLogEntry;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Student;
use App\Models\TeacherStaff;
use App\Models\TeacherWorkspace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * GET /api/v1/auth/me and POST /api/v1/auth/logout — 10_API_Design.md §13.
 *
 * The current-user response must not expose unauthorized role contexts, and
 * logout must destroy session data without removing historical Audit Log
 * records.
 */
final class CurrentUserAndLogoutTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $email = 'user@example.test'): User
    {
        return User::create(['name' => 'Test User', 'email' => $email, 'password' => 'Passw0rdX']);
    }

    #[Test]
    public function a_guest_cannot_read_the_current_user(): void
    {
        // AUT-14: every protected request needs a valid context.
        $this->getJson('/api/v1/auth/me')
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_UNAUTHENTICATED');
    }

    #[Test]
    public function a_guest_receives_json_rather_than_a_login_redirect(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $this->assertFalse($response->isRedirect());
        $this->assertJson($response->getContent() ?: '');
    }

    #[Test]
    public function an_authenticated_user_reads_their_own_context(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.identifier', 'user@example.test')
            ->assertJsonStructure(['data' => ['role_contexts', 'permitted_scopes', 'permissions']]);
    }

    #[Test]
    public function the_current_user_response_never_contains_the_secret(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/auth/me');

        $this->assertStringNotContainsString('Passw0rdX', $response->getContent() ?: '');
        $this->assertStringNotContainsString('password', $response->getContent() ?: '');
    }

    #[Test]
    public function assigned_role_contexts_are_reported(): void
    {
        $user = $this->makeUser();
        $role = Role::create(['role_name' => 'student', 'role_scope' => 'student_account']);
        $user->roles()->attach($role->id, ['teacher_workspace_id' => null]);

        $this->actingAs($user->fresh(), 'sanctum')
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.role_contexts.0.role', 'student')
            ->assertJsonPath('data.role_contexts.0.scope', 'student_account');
    }

    #[Test]
    public function a_workspace_scoped_role_reports_its_workspace(): void
    {
        // 07 §2: a role is assigned in a specific context.
        $user = $this->makeUser('staff@example.test');
        $workspace = TeacherWorkspace::create(['workspace_status' => 'active']);
        $role = Role::create(['role_name' => 'teacher_staff', 'role_scope' => 'teacher_workspace']);
        $user->roles()->attach($role->id, ['teacher_workspace_id' => $workspace->id]);

        $this->actingAs($user->fresh(), 'sanctum')
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.role_contexts.0.teacher_workspace_id', $workspace->id);
    }

    #[Test]
    public function no_unassigned_role_context_is_exposed(): void
    {
        // 10 §13: the response must not expose unauthorized role contexts.
        $user = $this->makeUser();
        Role::create(['role_name' => 'super_admin', 'role_scope' => 'platform']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/auth/me');

        $response->assertOk()->assertJsonPath('data.role_contexts', []);
        $this->assertStringNotContainsString('super_admin', $response->getContent() ?: '');
    }

    #[Test]
    public function teacher_staff_permissions_are_empty_while_q011_is_pending(): void
    {
        $user = $this->makeUser('staff2@example.test');
        $workspace = TeacherWorkspace::create(['workspace_status' => 'active']);
        TeacherStaff::create(['user_id' => $user->id, 'teacher_workspace_id' => $workspace->id]);

        $this->actingAs($user->fresh(), 'sanctum')
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.permissions', []);
    }

    #[Test]
    public function an_assigned_permission_surfaces_without_a_code_change(): void
    {
        // When the authorization phase seeds the catalog, capabilities appear
        // automatically — nothing here needs to change.
        $user = $this->makeUser('staff3@example.test');
        $workspace = TeacherWorkspace::create(['workspace_status' => 'active']);
        $staff = TeacherStaff::create(['user_id' => $user->id, 'teacher_workspace_id' => $workspace->id]);
        $permission = Permission::create([
            'permission_name' => 'probe.capability',
            'permission_scope' => 'teacher_workspace',
        ]);

        DB::table('permission_teacher_staff')->insert([
            'teacher_staff_id' => $staff->id,
            'permission_id' => $permission->id,
            'teacher_workspace_id' => $workspace->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user->fresh(), 'sanctum')
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.permissions', ['probe.capability']);
    }

    #[Test]
    public function a_student_context_is_reported_when_one_exists(): void
    {
        $user = $this->makeUser('student@example.test');
        Student::create([
            'user_id' => $user->id,
            'activation_status' => 'active',
            'created_by_method' => 'self_registration',
        ]);

        $this->actingAs($user->fresh(), 'sanctum')
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.student.activation_status', 'active');
    }

    #[Test]
    public function a_guest_cannot_log_out(): void
    {
        $this->postJson('/api/v1/auth/logout')
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_UNAUTHENTICATED');
    }

    #[Test]
    public function an_authenticated_user_can_log_out(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJsonPath('data.logged_out', true);
    }

    #[Test]
    public function logout_does_not_remove_historical_login_records(): void
    {
        // 10 §13: logout "does not remove historical login Audit Log records".
        $user = $this->makeUser();

        $this->postJson('/api/v1/auth/login', [
            'identifier' => 'user@example.test',
            'secret' => 'Passw0rdX',
        ])->assertOk();

        $before = AuditLogEntry::where('event_type', 'login')->count();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/auth/logout')->assertOk();

        $this->assertGreaterThanOrEqual($before, AuditLogEntry::where('event_type', 'login')->count());
    }
}
