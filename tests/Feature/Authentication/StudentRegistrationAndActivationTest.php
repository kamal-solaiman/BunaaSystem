<?php

declare(strict_types=1);

namespace Tests\Feature\Authentication;

use App\Models\AuditLogEntry;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * POST /api/v1/auth/students/register and /activate — 10_API_Design.md §13.
 *
 * The two confirmed registration methods (BR-022): the Student registers their
 * own account, or the Teacher creates it and the Student activates it later.
 * Neither path may ever produce a duplicate global Student account
 * (BR-001; 33_Validation_Rules.md AUT-11, AUT-12, AUT-13).
 */
final class StudentRegistrationAndActivationTest extends TestCase
{
    use RefreshDatabase;

    /** Creates the Teacher-created, pending-activation account of Method 2. */
    private function makeTeacherCreatedStudent(string $email = 'omar@example.test'): Student
    {
        $user = User::create(['name' => 'Omar', 'email' => $email, 'password' => 'TempPass1']);

        return Student::create([
            'user_id' => $user->id,
            'activation_status' => 'pending_activation',
            'created_by_method' => 'teacher_created',
        ]);
    }

    #[Test]
    public function a_student_can_register_their_own_account(): void
    {
        $this->postJson('/api/v1/auth/students/register', [
            'name' => 'Sara',
            'identifier' => 'sara@example.test',
            'secret' => 'Passw0rdX',
        ])
            ->assertCreated()
            ->assertJsonPath('data.created_by_method', 'self_registration')
            ->assertJsonPath('data.activation_status', 'active');

        $this->assertDatabaseHas('students', ['created_by_method' => 'self_registration']);
    }

    #[Test]
    public function the_secret_is_hashed_and_never_echoed(): void
    {
        $response = $this->postJson('/api/v1/auth/students/register', [
            'name' => 'Sara',
            'identifier' => 'sara@example.test',
            'secret' => 'Passw0rdX',
        ]);

        $this->assertStringNotContainsString('Passw0rdX', $response->getContent() ?: '');
        $this->assertTrue(Hash::check('Passw0rdX', User::where('email', 'sara@example.test')->first()->password));
    }

    #[Test]
    public function a_duplicate_identity_is_rejected_with_conflict(): void
    {
        // BR-001, BR-022, AUT-12.
        User::create(['name' => 'Existing', 'email' => 'sara@example.test', 'password' => 'Passw0rdX']);

        $this->postJson('/api/v1/auth/students/register', [
            'name' => 'Sara',
            'identifier' => 'sara@example.test',
            'secret' => 'Passw0rdX',
        ])
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'STUDENT_DUPLICATE_ACCOUNT');

        $this->assertSame(0, Student::count());
    }

    #[Test]
    public function a_duplicate_rejection_reveals_no_teacher_context(): void
    {
        User::create(['name' => 'Existing', 'email' => 'sara@example.test', 'password' => 'Passw0rdX']);

        $response = $this->postJson('/api/v1/auth/students/register', [
            'name' => 'Sara',
            'identifier' => 'sara@example.test',
            'secret' => 'Passw0rdX',
        ]);

        $this->assertStringNotContainsString('teacher', strtolower($response->getContent() ?: ''));
    }

    #[Test]
    public function a_weak_secret_is_rejected(): void
    {
        // AUT-03: at least 8 characters with upper, lower, and a digit.
        $this->postJson('/api/v1/auth/students/register', [
            'name' => 'Sara',
            'identifier' => 'sara@example.test',
            'secret' => 'short',
        ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED');
    }

    #[Test]
    public function registration_is_audited(): void
    {
        $this->postJson('/api/v1/auth/students/register', [
            'name' => 'Sara',
            'identifier' => 'sara@example.test',
            'secret' => 'Passw0rdX',
        ])->assertCreated();

        $this->assertDatabaseHas('audit_log_entries', [
            'event_type' => 'create',
            'affected_entity_name' => 'Student',
        ]);
    }

    #[Test]
    public function a_teacher_created_account_can_be_activated(): void
    {
        $student = $this->makeTeacherCreatedStudent();

        $this->postJson('/api/v1/auth/students/activate', [
            'identifier' => 'omar@example.test',
            'secret' => 'NewPassw0rd',
        ])
            ->assertOk()
            ->assertJsonPath('data.activation_status', 'active')
            ->assertJsonPath('data.created_by_method', 'teacher_created');

        $this->assertSame('active', $student->fresh()->activation_status);
    }

    #[Test]
    public function activation_creates_no_second_account(): void
    {
        // AUT-13: activation must never create a duplicate Student account.
        $student = $this->makeTeacherCreatedStudent();

        $this->postJson('/api/v1/auth/students/activate', [
            'identifier' => 'omar@example.test',
            'secret' => 'NewPassw0rd',
        ])->assertOk();

        $this->assertSame(1, Student::count());
        $this->assertSame(1, Student::where('user_id', $student->user_id)->count());
    }

    #[Test]
    public function the_student_can_log_in_after_activating(): void
    {
        $this->makeTeacherCreatedStudent();

        $this->postJson('/api/v1/auth/students/activate', [
            'identifier' => 'omar@example.test',
            'secret' => 'NewPassw0rd',
        ])->assertOk();

        $this->postJson('/api/v1/auth/login', [
            'identifier' => 'omar@example.test',
            'secret' => 'NewPassw0rd',
        ])->assertOk();
    }

    #[Test]
    public function activating_twice_is_rejected(): void
    {
        // Activation is a one-way Pending Activation to Active transition.
        $this->makeTeacherCreatedStudent();

        $this->postJson('/api/v1/auth/students/activate', [
            'identifier' => 'omar@example.test',
            'secret' => 'NewPassw0rd',
        ])->assertOk();

        $this->postJson('/api/v1/auth/students/activate', [
            'identifier' => 'omar@example.test',
            'secret' => 'NewPassw0rd',
        ])
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'STUDENT_ACCOUNT_ALREADY_ACTIVE');
    }

    #[Test]
    public function an_unknown_identity_cannot_be_activated(): void
    {
        $this->postJson('/api/v1/auth/students/activate', [
            'identifier' => 'ghost@example.test',
            'secret' => 'NewPassw0rd',
        ])
            ->assertNotFound()
            ->assertJsonPath('error.code', 'STUDENT_ACTIVATION_MISMATCH');
    }

    #[Test]
    public function a_self_registered_account_cannot_be_activated(): void
    {
        // Activation applies only to Teacher-created accounts, and the refusal
        // is indistinguishable from an unknown identity, so activation cannot
        // be used to probe which identities exist.
        $this->postJson('/api/v1/auth/students/register', [
            'name' => 'Sara',
            'identifier' => 'sara@example.test',
            'secret' => 'Passw0rdX',
        ])->assertCreated();

        $selfRegistered = $this->postJson('/api/v1/auth/students/activate', [
            'identifier' => 'sara@example.test',
            'secret' => 'NewPassw0rd',
        ])->json();

        $unknown = $this->postJson('/api/v1/auth/students/activate', [
            'identifier' => 'ghost@example.test',
            'secret' => 'NewPassw0rd',
        ])->json();

        unset($selfRegistered['request_id'], $unknown['request_id']);

        $this->assertSame($unknown, $selfRegistered);
    }

    #[Test]
    public function activation_is_audited_with_a_before_and_after_snapshot(): void
    {
        $this->makeTeacherCreatedStudent();

        $this->postJson('/api/v1/auth/students/activate', [
            'identifier' => 'omar@example.test',
            'secret' => 'NewPassw0rd',
        ])->assertOk();

        $entry = AuditLogEntry::where('event_type', 'update')
            ->where('affected_entity_name', 'Student')
            ->latest('id')
            ->first();

        $this->assertNotNull($entry);
        $this->assertSame('pending_activation', $entry->event_details['before']['activation_status'] ?? null);
        $this->assertSame('active', $entry->event_details['after']['activation_status'] ?? null);
    }

    #[Test]
    public function activation_requires_no_authenticated_context(): void
    {
        // The documented authentication exception path: the account cannot be
        // logged into until it has been activated.
        $this->makeTeacherCreatedStudent();

        $this->postJson('/api/v1/auth/students/activate', [
            'identifier' => 'omar@example.test',
            'secret' => 'NewPassw0rd',
        ])->assertOk();
    }
}
