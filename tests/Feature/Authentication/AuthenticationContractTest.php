<?php

declare(strict_types=1);

namespace Tests\Feature\Authentication;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Phase 44 contract guarantees.
 *
 * Authentication was implemented against the frozen database contract. These
 * tests fail if a later change adds an endpoint that no document defines, or
 * alters the schema the contract froze.
 */
final class AuthenticationContractTest extends TestCase
{
    use RefreshDatabase;

    /** @return list<string> */
    private function authRoutes(): array
    {
        return collect(Route::getRoutes())
            ->map(static fn ($route): string => $route->methods()[0].' '.$route->uri())
            ->filter(static fn (string $route): bool => str_contains($route, 'api/v1/auth'))
            ->values()
            ->all();
    }

    #[Test]
    public function exactly_the_five_documented_endpoints_exist(): void
    {
        // 10_API_Design.md §13 defines five and only five.
        $expected = [
            'POST api/v1/auth/login',
            'POST api/v1/auth/logout',
            'GET api/v1/auth/me',
            'POST api/v1/auth/students/register',
            'POST api/v1/auth/students/activate',
        ];

        $actual = $this->authRoutes();

        sort($expected);
        sort($actual);

        $this->assertSame($expected, $actual, 'The authentication surface must match the documented catalog exactly.');
    }

    #[Test]
    public function no_impersonation_endpoint_exists(): void
    {
        // 10 §3 rule 5: "Login as Teacher" is not part of Version 1.
        foreach (Route::getRoutes() as $route) {
            $uri = strtolower($route->uri());
            $this->assertStringNotContainsString('impersonat', $uri);
            $this->assertStringNotContainsString('login-as', $uri);
        }
    }

    #[Test]
    public function the_schema_is_unchanged_by_this_phase(): void
    {
        // Phase 44 required zero migrations. These are the exact column counts
        // frozen by DATABASE_CONTRACT.md.
        $frozen = [
            'users' => 8,
            'students' => 8,
            'roles' => 7,
            'permissions' => 7,
            'teachers' => 8,
            'teacher_workspaces' => 6,
            'teaching_subjects' => 8,
            'teacher_staff' => 8,
            'audit_log_entries' => 13,
        ];

        foreach ($frozen as $table => $columns) {
            $this->assertCount(
                $columns,
                Schema::getColumnListing($table),
                "The frozen table {$table} has changed shape."
            );
        }
    }

    #[Test]
    public function authentication_introduced_no_new_table(): void
    {
        foreach (['login_attempts', 'activation_tokens', 'password_resets', 'auth_sessions'] as $table) {
            $this->assertFalse(Schema::hasTable($table), "Unexpected table introduced: {$table}.");
        }
    }

    #[Test]
    public function the_migration_count_is_unchanged(): void
    {
        $migrations = glob(database_path('migrations/*.php')) ?: [];

        $this->assertCount(15, $migrations, 'Phase 44 must add no migration.');
    }

    #[Test]
    public function the_rate_limit_threshold_is_configuration_not_a_product_number(): void
    {
        // AUT-05 confirms a limit exists but leaves the threshold unconfirmed.
        $this->assertIsInt(config('auth.rate_limit.attempts'));
        $this->assertStringContainsString(
            'AUTH_RATE_LIMIT_ATTEMPTS',
            (string) file_get_contents(config_path('auth.php'))
        );
    }
}
