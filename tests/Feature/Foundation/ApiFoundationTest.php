<?php

declare(strict_types=1);

namespace Tests\Feature\Foundation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
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
    public function the_sanctum_guard_is_available_for_protected_scopes(): void
    {
        // The scope groups in routes/api.php are protected by auth:sanctum.
        // No endpoint exists yet, so this verifies the guard itself resolves —
        // if it did not, every protected route added later would fault as 500
        // instead of answering 401.
        $this->assertNotNull(config('auth.guards.sanctum'));
        $this->assertSame('sanctum', config('auth.guards.sanctum.driver'));
    }

    #[Test]
    public function the_versioned_prefix_is_api_v1(): void
    {
        // Any unmatched request under the prefix reaches the API fallback and
        // returns the JSON envelope rather than the HTML shell.
        $this->getJson('/api/v1/anything')
            ->assertNotFound()
            ->assertJson(['error' => ['code' => 'API_UNSUPPORTED_ROUTE']]);
    }

    #[Test]
    public function no_notification_endpoint_exists(): void
    {
        // Notifications are out of scope for Version 1 (10_API_Design.md §29).
        foreach (Route::getRoutes() as $route) {
            $this->assertStringNotContainsString('notification', $route->uri());
        }
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
