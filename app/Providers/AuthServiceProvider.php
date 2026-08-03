<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Authorization registration boundary.
 *
 * Version 1 uses Laravel Gates & Policies with a Custom RBAC model
 * (AI_DOCS/08_RBAC.md; 09_Permission_Matrix.md). The backend is the sole
 * authority for every access decision; the frontend never substitutes for it
 * (28_Coding_Standards.md §1.3).
 *
 * Foundation only: no roles, permissions, gates, or policies are defined here.
 * They arrive with the authorization phase, driven by the permission matrix.
 */
final class AuthServiceProvider extends ServiceProvider
{
    /**
     * Policy registrations: model class => policy class.
     *
     * @var array<class-string, class-string>
     */
    public array $policies = [];

    public function boot(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        /*
         * Deny by default (23_Security_Standards.md §2.1).
         *
         * An ability with no registered gate or policy resolves to denied
         * rather than to an accidental allow. Returning null here leaves an
         * explicitly registered result untouched.
         */
        Gate::after(static fn (): ?bool => null);
    }
}
