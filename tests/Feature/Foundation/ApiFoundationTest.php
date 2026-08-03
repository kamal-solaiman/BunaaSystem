<?php

declare(strict_types=1);

namespace Tests\Feature\Foundation;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Foundation-level API contract tests.
 *
 * Verifies the transport guarantees every later phase depends on: the versioned
 * prefix, the documented error envelope, safe headers, and the rule that an
 * unknown route never leaks an HTML error page
 * (AI_DOCS/24_Testing_Strategy.md T1).
 */
final class ApiFoundationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unknown_api_route_returns_the_documented_error_envelope(): void
    {
        $response = $this->getJson('/api/v1/does-not-exist');

        $response
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'error' => ['code' => 'API_UNSUPPORTED_ROUTE'],
            ])
            ->assertJsonStructure([
                'success',
                'error' => ['code', 'message'],
            ]);
    }

    #[Test]
    public function protected_endpoint_rejects_a_guest_without_redirecting(): void
    {
        // A guest reaching a protected endpoint must receive 401 JSON, never an
        // HTML login redirect.
        $response = $this->getJson('/api/v1/session');

        $response
            ->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'error' => ['code' => 'AUTH_UNAUTHENTICATED'],
            ]);
    }

    #[Test]
    public function protected_endpoint_serves_an_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/session')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['id' => $user->id],
            ]);
    }

    #[Test]
    public function a_wrong_http_verb_is_rejected_as_an_invalid_operation(): void
    {
        // 405 must not surface as a server fault.
        $this->postJson('/api/v1/session')
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => ['code' => 'API_MALFORMED_REQUEST'],
            ]);
    }

    #[Test]
    public function api_responses_carry_the_baseline_security_headers(): void
    {
        $response = $this->getJson('/api/v1/does-not-exist');

        $response
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    #[Test]
    public function api_responses_carry_a_correlation_identifier(): void
    {
        $response = $this->getJson('/api/v1/does-not-exist');

        $response->assertHeader('X-Request-Id');
    }

    #[Test]
    public function error_responses_never_expose_internals(): void
    {
        $response = $this->getJson('/api/v1/does-not-exist');

        $body = $response->json();

        // Debug keys must never appear in an API failure.
        $this->assertArrayNotHasKey('exception', $body);
        $this->assertArrayNotHasKey('trace', $body);
        $this->assertArrayNotHasKey('file', $body);
        $this->assertArrayNotHasKey('line', $body);
    }

    #[Test]
    public function health_endpoint_is_available(): void
    {
        $this->get('/up')->assertOk();
    }
}
