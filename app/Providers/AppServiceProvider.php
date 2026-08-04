<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/**
 * Application-wide framework configuration.
 *
 * Foundation only: framework posture and safety defaults. No business rules,
 * no bindings for feature services, no domain registration.
 */
final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureModels();
        $this->configureDates();
        $this->configureRateLimiting();
    }

    /**
     * Rate limiting for the public authentication endpoints.
     *
     * 33_Validation_Rules.md AUT-05 confirms that login endpoints must be
     * rate-limited to prevent brute force, while stating the numeric threshold
     * and window are **not confirmed** and "must not be presented as product
     * values".
     *
     * The limit therefore lives in configuration, not in a documented product
     * number, and is never echoed to the client: exceeding it produces the
     * generic 429 envelope with a Retry-After header and no internal detail
     * (23_Security_Standards.md §3.3).
     *
     * Keyed by identifier and IP together, so one attacker cannot lock out a
     * legitimate account by exhausting its allowance from elsewhere.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('auth', static function (Request $request): Limit {
            /** @var int $attempts */
            $attempts = config('auth.rate_limit.attempts');
            /** @var int $minutes */
            $minutes = config('auth.rate_limit.decay_minutes');

            $identifier = (string) $request->input('identifier', '');

            return Limit::perMinutes($minutes, $attempts)
                ->by(mb_strtolower($identifier).'|'.$request->ip());
        });
    }

    /**
     * Strict model behavior.
     *
     * Mass-assignment protection stays on and lazy loading is prevented outside
     * production, so an accidental N+1 or an unguarded attribute is caught in
     * development rather than in a shared-hosting production request
     * (AI_DOCS/25_Performance_Scalability.md; 28_Coding_Standards.md §3.5).
     */
    private function configureModels(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());
        Model::unguard(false);
    }

    private function configureDates(): void
    {
        Date::use(\Illuminate\Support\Carbon::class);
    }
}
