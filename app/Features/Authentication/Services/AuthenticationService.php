<?php

declare(strict_types=1);

namespace App\Features\Authentication\Services;

use App\Models\User;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditRecorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Login, logout, and the authenticated context.
 *
 * The backend is the sole authority for credential validation; the frontend
 * sends credentials and never decides anything
 * (23_Security_Standards.md §3.2).
 */
final readonly class AuthenticationService
{
    public function __construct(
        private AuditRecorder $audit,
        private RoleContextResolver $roleContext,
    ) {
    }

    /**
     * Attempt a login.
     *
     * Returns the authenticated User, or null on any failure. The caller
     * converts null into one generic 401, because a failure must never reveal
     * whether the account exists, whether the secret was wrong, or whether the
     * account is archived (23 §3.3; 33 AUT-04).
     *
     * Both outcomes are audited (23 §15.2 item 5; 33 AUT-06).
     */
    public function attemptLogin(string $identifier, string $secret, Request $request): ?User
    {
        // Archived accounts are excluded from the active scope by the
        // Archivable trait, so an archived user is simply not found — which is
        // exactly the indistinguishable outcome §3.3 requires.
        $user = User::query()->where('email', $identifier)->first();

        // A constant-ish comparison path: when no user matches we still run a
        // hash check against a dummy value, so a missing account and a wrong
        // secret take comparable time and cannot be told apart by timing.
        $verified = $user !== null
            ? Hash::check($secret, $user->password)
            : Hash::check($secret, '$2y$12$'.str_repeat('0', 53));

        if ($user === null || ! $verified) {
            $this->recordFailedLogin($identifier, $request);

            return null;
        }

        // Session fixation defence: a new session identifier is issued on
        // authentication (23 §7.2).
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        $this->audit->record(
            event: AuditEvent::Login,
            affectedEntityName: 'User',
            affectedEntityId: $user->id,
            actorUserId: $user->id,
            actorRole: $this->roleContext->primaryRoleFor($user),
            details: ['outcome' => 'success'],
            request: $request,
        );

        return $user;
    }

    /**
     * End the authenticated context.
     *
     * All session data is destroyed on logout (23 §7.2). Historical login
     * Audit Log records are never removed (10 §13).
     */
    public function logout(Request $request): void
    {
        $user = $request->user();

        if ($user instanceof User) {
            $this->audit->record(
                event: AuditEvent::Login,
                affectedEntityName: 'User',
                affectedEntityId: $user->id,
                actorUserId: $user->id,
                actorRole: $this->roleContext->primaryRoleFor($user),
                details: ['outcome' => 'logout'],
                request: $request,
            );

            // Revoke the current token when the request authenticated with one
            // rather than with a session cookie.
            $token = $user->currentAccessToken();

            if ($token !== null && method_exists($token, 'delete')) {
                $token->delete();
            }
        }

        if ($request->hasSession()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }

    /**
     * Record a failed attempt.
     *
     * The attempted identifier is recorded, but nothing that would confirm
     * whether the account exists, and never the secret (23 §3.3; 33 AUT-02).
     */
    private function recordFailedLogin(string $identifier, Request $request): void
    {
        $this->audit->record(
            event: AuditEvent::Login,
            affectedEntityName: 'User',
            actorUserId: null,
            actorRole: null,
            details: [
                'outcome' => 'failure',
                'attempted_identifier' => $identifier,
            ],
            request: $request,
        );
    }
}
