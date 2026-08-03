<?php

declare(strict_types=1);

namespace App\Features\Authorization\Services;

use App\Features\Authorization\Support\PermissionMatrix;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Registers every confirmed permission as a Laravel Gate ability.
 *
 * Version 1 uses "Laravel Gates & Policies with Custom RBAC" (08_RBAC.md §1),
 * so each of the 215 permission names becomes an ability resolved through
 * PermissionResolver.
 *
 * A gate returns true only for an **Allowed** grant. A Conditional grant
 * returns false here on purpose: the condition is a resource-level rule that
 * only the owning policy can evaluate against a specific record. A policy asks
 * the resolver directly with `holds()` and then applies its own ownership
 * check. Treating Conditional as allowed at the gate would grant access the
 * matrix never granted.
 */
final readonly class PermissionRegistrar
{
    public function __construct(private PermissionResolver $resolver)
    {
    }

    public function register(): void
    {
        foreach (PermissionMatrix::allPermissions() as $permission) {
            Gate::define(
                $permission,
                function (User $user, ?int $teacherWorkspaceId = null) use ($permission): bool {
                    return $this->resolver->allows($user, $permission, $teacherWorkspaceId);
                },
            );
        }
    }
}
