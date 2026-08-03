<?php

declare(strict_types=1);

namespace Tests\Feature\Authentication;

use App\Models\AuditLogEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * POST /api/v1/auth/login — 10_API_Design.md §13.
 *
 * The backend is the sole authority for credential validation, failures are
 * generic and non-disclosing, and every attempt is audited
 * (23_Security_Standards.md §3.3; 33_Validation_Rules.md AUT-01…AUT-06).
 */
final class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $email = 'user@example.test', string $secret = 'Passw0rdX'): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => $email,
            'password' => $secret,
        ]);
    }

    #[Test]
    public function valid_credentials_authenticate(): void
    {
        $this->makeUser();

        $this->postJson('/api/v1/auth/login', [
            'identifier' => 'user@example.test',
            'secret' => 'Passw0rdX',
        ])
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('data.identifier', 'user@example.test')
            ->assertJsonStructure(['data' => ['id', 'name', 'identifier', 'role_contexts', 'permitted_scopes']]);
    }

    #[Test]
    public function the_response_never_contains_the_secret(): void
    {
        $this->makeUser();

        $response = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'user@example.test',
            'secret' => 'Passw0rdX',
        ]);

        $this->assertStringNotContainsString('Passw0rdX', $response->getContent() ?: '');
        $this->assertStringNotContainsString('password', $response->getContent() ?: '');
    }

    #[Test]
    public function a_wrong_secret_is_rejected_generically(): void
    {
        $this->makeUser();

        $this->postJson('/api/v1/auth/login', [
            'identifier' => 'user@example.test',
            'secret' => 'WrongPass1',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_INVALID_CREDENTIALS');
    }

    #[Test]
    public function an_unknown_identifier_is_indistinguishable_from_a_wrong_secret(): void
    {
        // AUT-04: no account-existence disclosure.
        $this->makeUser();

        $wrongSecret = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'user@example.test',
            'secret' => 'WrongPass1',
        ])->json();

        $unknown = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'nobody@example.test',
            'secret' => 'WrongPass1',
        ])->json();

        unset($wrongSecret['request_id'], $unknown['request_id']);

        $this->assertSame($wrongSecret, $unknown);
    }

    #[Test]
    public function an_archived_account_cannot_authenticate(): void
    {
        // Archive replaces deletion; an archived account must not log in, and
        // the failure must not reveal that it is archived (23 §3.3).
        $user = $this->makeUser('archived@example.test');
        $user->archive();

        $this->postJson('/api/v1/auth/login', [
            'identifier' => 'archived@example.test',
            'secret' => 'Passw0rdX',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_INVALID_CREDENTIALS');

        $this->assertDatabaseHas('users', ['email' => 'archived@example.test']);
    }

    #[Test]
    public function missing_credentials_fail_validation(): void
    {
        $this->postJson('/api/v1/auth/login', [])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED');
    }

    #[Test]
    public function a_successful_login_is_audited(): void
    {
        // 23 §15.2 item 5; AUT-06.
        $user = $this->makeUser();

        $this->postJson('/api/v1/auth/login', [
            'identifier' => 'user@example.test',
            'secret' => 'Passw0rdX',
        ])->assertOk();

        $entry = AuditLogEntry::where('event_type', 'login')->latest('id')->first();

        $this->assertNotNull($entry);
        $this->assertSame($user->id, $entry->actor_user_id);
        $this->assertSame('success', $entry->event_details['outcome'] ?? null);
    }

    #[Test]
    public function a_failed_login_is_audited_without_an_actor(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'identifier' => 'ghost@example.test',
            'secret' => 'WrongPass1',
        ])->assertUnauthorized();

        $entry = AuditLogEntry::where('event_type', 'login')->latest('id')->first();

        $this->assertNotNull($entry);
        $this->assertNull($entry->actor_user_id);
        $this->assertSame('failure', $entry->event_details['outcome'] ?? null);
        $this->assertSame('ghost@example.test', $entry->event_details['attempted_identifier'] ?? null);
    }

    #[Test]
    public function the_audit_log_never_records_a_secret(): void
    {
        // AUT-02: the secret is never logged.
        $this->makeUser();

        $this->postJson('/api/v1/auth/login', [
            'identifier' => 'user@example.test',
            'secret' => 'Passw0rdX',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'identifier' => 'user@example.test',
            'secret' => 'WrongPass1',
        ]);

        $recorded = AuditLogEntry::pluck('event_details')->toJson();

        $this->assertStringNotContainsString('Passw0rdX', $recorded);
        $this->assertStringNotContainsString('WrongPass1', $recorded);
    }
}
