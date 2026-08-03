<?php

declare(strict_types=1);

namespace App\Features\Authentication\Services;

use App\Models\Role;
use App\Models\TeacherStaff;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Resolves the role contexts and permitted scopes of an authenticated user.
 *
 * `GET /auth/me` returns "Current user identity, active role, and permitted
 * scopes", and the response "must not expose unauthorized role contexts"
 * (10_API_Design.md §13).
 *
 * This resolver reports what the database already records. It grants nothing
 * and decides nothing: authorization stays with Gates and Policies
 * (08_RBAC.md; 23 §4). Role assignment itself belongs to the authorization
 * phase, so this reads the frozen `role_user` structure without writing to it.
 */
final readonly class RoleContextResolver
{
    /**
     * Every role context held by the user.
     *
     * A user may hold more than one (07_Data_Dictionary.md §1). Each context
     * carries the Teacher Workspace it applies to, or null for Platform,
     * Student Account, and Parent scopes.
     *
     * @return list<array{role: string, scope: string, teacher_workspace_id: int|null}>
     */
    public function contextsFor(User $user): array
    {
        return $user->roles()
            ->get()
            ->map(static fn (Role $role): array => [
                'role' => $role->role_name,
                'scope' => $role->role_scope,
                'teacher_workspace_id' => $role->pivot?->teacher_workspace_id !== null
                    ? (int) $role->pivot->teacher_workspace_id
                    : null,
            ])
            ->values()
            ->all();
    }

    /**
     * The role recorded against audited actions.
     *
     * Where a user holds several contexts the first assigned one is used, so
     * an Audit Log entry always names a real role the actor holds. Active-role
     * selection for multi-context users is a later-phase concern; nothing here
     * assumes a precedence the documents do not define.
     */
    public function primaryRoleFor(User $user): ?string
    {
        /** @var Collection<int, Role> $roles */
        $roles = $user->roles()->get();

        return $roles->first()?->role_name;
    }

    /**
     * Permitted scopes, derived from the assigned role contexts.
     *
     * @return list<string>
     */
    public function permittedScopesFor(User $user): array
    {
        return $user->roles()
            ->get()
            ->map(static fn (Role $role): string => $role->role_scope)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Capabilities granted to a Teacher Staff context.
     *
     * Teacher Staff hold only the permissions the Teacher assigned
     * (07 §30). The catalog is empty while Q-011 is PENDING, so this returns
     * an empty list today and starts reporting real capabilities the moment
     * the authorization phase seeds them — with no change here.
     *
     * @return list<string>
     */
    public function assignedPermissionsFor(User $user): array
    {
        // Queried directly rather than through a new User relationship: the
        // frozen models are left untouched, and this reads the documented
        // Teacher Staff → User reference (07 §30) as it already exists.
        return TeacherStaff::query()
            ->where('user_id', $user->id)
            ->get()
            ->flatMap(static fn (TeacherStaff $context): Collection => $context->permissions()->pluck('permission_name'))
            ->unique()
            ->values()
            ->all();
    }
}
