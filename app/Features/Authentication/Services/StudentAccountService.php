<?php

declare(strict_types=1);

namespace App\Features\Authentication\Services;

use App\Features\Authentication\Exceptions\StudentAccountAlreadyActiveException;
use App\Features\Authentication\Exceptions\StudentActivationMismatchException;
use App\Features\Authentication\Exceptions\StudentDuplicateAccountException;
use App\Models\Student;
use App\Models\User;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditRecorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Student self-registration and Teacher-created account activation.
 *
 * Two confirmed registration methods exist and no third
 * (BR-022; 33_Validation_Rules.md STU-01):
 *
 *   Method 1 — the Student registers their own account.
 *   Method 2 — the Teacher creates the account; the Student activates it later.
 *
 * This service owns Method 1 and the activation half of Method 2. Teacher-side
 * creation is a Teacher Workspace endpoint (10 §15) and belongs to a later
 * phase.
 */
final readonly class StudentAccountService
{
    public function __construct(private AuditRecorder $audit)
    {
    }

    /**
     * Register a Student account (Method 1).
     *
     * A Student has exactly one global account and duplicates are prohibited
     * (BR-001, BR-022; 33 AUT-12). Duplicate detection is server-side and the
     * rejection never reveals where, or with which Teacher, the existing
     * account studies.
     *
     * @throws StudentDuplicateAccountException
     */
    public function register(string $name, string $identifier, string $secret, Request $request): Student
    {
        // Checked against every account, archived included: an archived
        // Student still holds the global identity, so reusing it would create
        // the duplicate BR-022 forbids.
        $existing = User::withTrashed()->where('email', $identifier)->exists();

        if ($existing) {
            throw new StudentDuplicateAccountException();
        }

        // The account and its audit entry are written in one transaction, so
        // an action can never be persisted without its Audit Log record
        // (23_Security_Standards.md §15.4 transactional guarantee).
        return DB::transaction(function () use ($name, $identifier, $secret, $request): Student {
            $user = User::create([
                'name' => $name,
                'email' => $identifier,
                'password' => $secret,
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                // Self-registration is immediately usable; there is nothing to
                // activate (07_Data_Dictionary.md §6).
                'activation_status' => 'active',
                'created_by_method' => 'self_registration',
            ]);

            $this->audit->record(
                event: AuditEvent::Create,
                affectedEntityName: 'Student',
                affectedEntityId: $student->id,
                actorUserId: $user->id,
                actorRole: 'student',
                details: ['after' => ['created_by_method' => 'self_registration']],
                request: $request,
            );

            return $student;
        });
    }

    /**
     * Activate a Teacher-created Student account (Method 2).
     *
     * The supplied data must match exactly one pending-activation
     * Teacher-created account (33 AUT-13). Activation never creates an
     * account, so it cannot produce a duplicate.
     *
     * @throws StudentActivationMismatchException
     * @throws StudentAccountAlreadyActiveException
     */
    public function activate(string $identifier, string $secret, Request $request): Student
    {
        $user = User::query()->where('email', $identifier)->first();
        $student = $user !== null
            ? Student::query()->where('user_id', $user->id)->first()
            : null;

        if ($user === null || $student === null || $student->created_by_method !== 'teacher_created') {
            // No account, no Student context, or a self-registered account:
            // all collapse to the same neutral outcome, so activation cannot
            // be used to probe which identities exist.
            throw new StudentActivationMismatchException();
        }

        if (! $student->isPendingActivation()) {
            // Activation is a one-way Pending Activation → Active transition
            // (34_Error_Codes.md STU-04).
            throw new StudentAccountAlreadyActiveException();
        }

        return DB::transaction(function () use ($student, $user, $secret, $request): Student {
            $before = $student->activation_status;

            // The Student sets their own secret when taking ownership of the
            // Teacher-created account.
            $user->update(['password' => $secret]);
            $student->update(['activation_status' => 'active']);

            $this->audit->record(
                event: AuditEvent::Update,
                affectedEntityName: 'Student',
                affectedEntityId: $student->id,
                actorUserId: $user->id,
                actorRole: 'student',
                details: [
                    'before' => ['activation_status' => $before],
                    'after' => ['activation_status' => 'active'],
                ],
                request: $request,
            );

            return $student->refresh();
        });
    }
}
