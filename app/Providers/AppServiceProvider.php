<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
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
