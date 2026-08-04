<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use App\Models\AuditLogEntry;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Phase 44 readiness — zero-migration proof.
 *
 * `10_API_Design.md` §13 defines exactly five authentication endpoints. This
 * suite asserts that the storage each one needs already exists, so Phase 44
 * can be implemented without adding or altering a single column.
 *
 * If a later change breaks one of these guarantees, this suite fails and the
 * database contract has been violated.
 */
final class Phase44ReadinessTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_has_the_storage_it_needs(): void
    {
        // Credentials, database sessions (D-040), Sanctum tokens, and the
        // audit trail for successful and failed attempts (23 §15.2 item 5).
        $this->assertTrue(Schema::hasColumn('users', 'email'));
        $this->assertTrue(Schema::hasColumn('users', 'password'));
        $this->assertTrue(Schema::hasColumn('users', 'account_status'));
        $this->assertTrue(Schema::hasTable('sessions'));
        $this->assertTrue(Schema::hasTable('personal_access_tokens'));
        $this->assertTrue(Schema::hasTable('audit_log_entries'));
    }

    #[Test]
    public function logout_has_the_storage_it_needs(): void
    {
        // Session destruction and token revocation.
        $this->assertTrue(Schema::hasTable('sessions'));
        $this->assertTrue(Schema::hasTable('personal_access_tokens'));
    }

    #[Test]
    public function the_current_user_endpoint_has_the_storage_it_needs(): void
    {
        // "Current user identity, active role, and permitted scopes."
        $this->assertTrue(Schema::hasTable('roles'));
        $this->assertTrue(Schema::hasTable('role_user'));
        $this->assertTrue(Schema::hasTable('permissions'));
        $this->assertTrue(Schema::hasTable('permission_teacher_staff'));

        // Workspace-scoped role contexts (07 §2).
        $this->assertTrue(Schema::hasColumn('role_user', 'teacher_workspace_id'));
    }

    #[Test]
    public function student_self_registration_has_the_storage_it_needs(): void
    {
        // 10 §13 POST /auth/students/register; 07 §6; BR-022 Method 1.
        $this->assertTrue(Schema::hasTable('students'));
        $this->assertTrue(Schema::hasColumn('students', 'created_by_method'));
        $this->assertTrue(Schema::hasColumn('students', 'activation_status'));

        $student = Student::create([
            'user_id' => User::factory()->create()->id,
            'activation_status' => 'active',
            'created_by_method' => 'self_registration',
        ]);

        $this->assertTrue($student->exists);
    }

    #[Test]
    public function student_activation_has_the_storage_it_needs(): void
    {
        // 10 §13 POST /auth/students/activate; 33 AUT-13; BR-022 Method 2.
        // The full Teacher-created to activated cycle, with no schema change.
        $user = User::factory()->create();

        $created = Student::create([
            'user_id' => $user->id,
            'activation_status' => 'pending_activation',
            'created_by_method' => 'teacher_created',
        ]);

        // AUT-13: resolve exactly one pending-activation Teacher-created account.
        $located = Student::query()
            ->where('user_id', $user->id)
            ->where('activation_status', 'pending_activation')
            ->where('created_by_method', 'teacher_created')
            ->first();

        $this->assertSame($created->id, $located?->id);

        $located->update(['activation_status' => 'active']);

        // The activation is recordable in the Audit Log.
        AuditLogEntry::create([
            'actor_user_id' => $user->id,
            'actor_role' => 'student',
            'scope_context' => 'platform',
            'event_type' => 'update',
            'affected_entity_name' => 'Student',
            'affected_entity_id' => $created->id,
            'occurred_at' => now(),
        ]);

        $this->assertSame('active', Student::find($created->id)?->activation_status);
        $this->assertDatabaseCount('audit_log_entries', 1);
    }

    #[Test]
    public function the_password_reset_flow_has_the_storage_it_needs(): void
    {
        // 23 §6.2 password reset via time-limited single-use token.
        $this->assertTrue(Schema::hasTable('password_reset_tokens'));
    }

    #[Test]
    public function every_mandatory_audit_event_is_already_representable(): void
    {
        // 23 §15.2 lists ten mandatory events. All are in the enum already, so
        // no future phase needs to widen it.
        $user = User::factory()->create();

        $events = [
            'create', 'update', 'archive', 'restore', 'login',
            'permission_change', 'attendance_change', 'exam_modification',
            'homework_modification', 'subscription_change',
        ];

        foreach ($events as $event) {
            $entry = AuditLogEntry::create([
                'actor_user_id' => $user->id,
                'actor_role' => 'teacher',
                'scope_context' => 'platform',
                'event_type' => $event,
                'affected_entity_name' => 'Probe',
                'occurred_at' => now(),
            ]);

            $this->assertTrue($entry->exists, "Audit event type not storable: {$event}.");
        }
    }

    #[Test]
    public function all_five_role_contexts_are_already_representable(): void
    {
        // 07 §2: Version 1 has exactly five roles. Authentication must be able
        // to establish any of them without a schema change.
        foreach (['super_admin', 'teacher', 'teacher_staff', 'student', 'parent'] as $role) {
            $entry = AuditLogEntry::create([
                'actor_user_id' => User::factory()->create()->id,
                'actor_role' => $role,
                'scope_context' => 'platform',
                'event_type' => 'login',
                'affected_entity_name' => 'User',
                'occurred_at' => now(),
            ]);

            $this->assertTrue($entry->exists, "Role context not storable: {$role}.");
        }
    }
}
