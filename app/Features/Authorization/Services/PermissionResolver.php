<?php

declare(strict_types=1);

namespace App\Features\Authorization\Services;

use App\Features\Authorization\Support\AuthorizationContext;
use App\Features\Authorization\Support\PermissionMatrix;
use App\Models\Role;
use App\Models\TeacherStaff;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Resolves whether an actor holds a permission, and in which context.
 *
 * Implements the resolution flow of `08_RBAC.md` §14 as frozen in
 * `RBAC_CONTRACT.md` §5. It answers "does this actor hold this capability, and
 * where" — it does not decide ownership of a specific record, which belongs to
 * the policy that owns that resource.
 *
 * Deny by default (08 §2.1): every method returns the restrictive answer unless
 * a grant is found.
 */
final readonly class PermissionResolver
{
    /**
     * Every role context the actor holds.
     *
     * Reads the frozen `role_user` table. Archived roles are excluded by the
     * Archivable scope, so an archived Role grants nothing (07 §2).
     *
     * @return list<AuthorizationContext>
     */
    public function contextsFor(User $user): array
    {
        return $user->roles()
            ->get()
            ->map(static fn (Role $role): AuthorizationContext => new AuthorizationContext(
                role: $role->role_name,
                scope: $role->role_scope,
                teacherWorkspaceId: $role->pivot?->teacher_workspace_id !== null
                    ? (int) $role->pivot->teacher_workspace_id
                    : null,
            ))
            ->values()
            ->all();
    }

    /**
     * Whether the actor holds the permission in any context.
     *
     * A Conditional grant is **not** an allow. The matrix condition is a
     * resource-level rule — own workspace, own account, linked Student — that
     * only the calling policy can evaluate, so this method reports the
     * capability while `conditionFor()` reports that a condition applies.
     */
    public function allows(User $user, string $permission, ?int $teacherWorkspaceId = null): bool
    {
        foreach ($this->contextsFor($user) as $context) {
            if ($this->grantIn($user, $context, $permission, $teacherWorkspaceId) === PermissionMatrix::ALLOWED) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the actor holds the permission subject to a condition.
     */
    public function isConditional(User $user, string $permission, ?int $teacherWorkspaceId = null): bool
    {
        foreach ($this->contextsFor($user) as $context) {
            if ($this->grantIn($user, $context, $permission, $teacherWorkspaceId) === PermissionMatrix::CONDITIONAL) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the actor holds the permission at all, allowed or conditional.
     *
     * Used where a policy will itself evaluate the condition against a specific
     * record.
     */
    public function holds(User $user, string $permission, ?int $teacherWorkspaceId = null): bool
    {
        return $this->allows($user, $permission, $teacherWorkspaceId)
            || $this->isConditional($user, $permission, $teacherWorkspaceId);
    }

    /**
     * How one context grants a permission: ALLOWED, CONDITIONAL, or null.
     *
     * Scope is checked before the permission (08 §14 step 4 precedes step 6),
     * so a workspace-scoped grant can never reach another workspace. Isolation
     * is not a capability that can be granted away.
     */
    public function grantIn(
        User $user,
        AuthorizationContext $context,
        string $permission,
        ?int $teacherWorkspaceId = null,
    ): ?string {
        // Step 4: the requested workspace must match the context's workspace.
        if ($context->isWorkspaceScoped()
            && $teacherWorkspaceId !== null
            && $context->teacherWorkspaceId !== $teacherWorkspaceId) {
            return null;
        }

        // A permission is meaningful only inside its own scope. The name prefix
        // `parent_linked_student` maps to the scope `parent_linked_students`;
        // both spellings are frozen and neither is normalized.
        if (! $this->permissionBelongsToScope($permission, $context->scope)) {
            return null;
        }

        // Teacher Staff hold no matrix row of their own (09 §5).
        if ($context->role === 'teacher_staff') {
            return $this->staffGrant($user, $context, $permission);
        }

        return PermissionMatrix::grant($context->role, $permission);
    }

    /**
     * Teacher Staff access is an intersection, never an inheritance.
     *
     * Effective access requires all three:
     *   1. the capability was explicitly assigned by the Teacher;
     *   2. the capability is one the Teacher themselves holds;
     *   3. the request is inside the creating Teacher Workspace.
     *
     * (08 §2.6; 09 §5; RBAC_CONTRACT.md §7.3.)
     *
     * While Q-011 is PENDING the catalogue carries no staff assignment, so this
     * returns null for every permission. It starts granting the moment the
     * Teacher assigns one, with no change here.
     */
    private function staffGrant(User $user, AuthorizationContext $context, string $permission): ?string
    {
        // A staff member cannot exceed what a Teacher could hold.
        $teacherGrant = PermissionMatrix::grant('teacher', $permission);

        if ($teacherGrant === null) {
            return null;
        }

        if (! $this->assignedPermissionsFor($user, $context->teacherWorkspaceId)->contains($permission)) {
            return null;
        }

        // Staff access is "always Conditional on Teacher-assigned permissions
        // inside the creating Teacher Workspace" (09 §5), so an assignment
        // never produces a stronger grant than the Teacher's own.
        return $teacherGrant;
    }

    /**
     * Capabilities the Teacher assigned to this staff member in one workspace.
     *
     * Archived staff contexts and archived permissions are excluded by the
     * Archivable scope: "Archived staff cannot act actively" (07 §30) and a
     * permission "must be active to be assigned" (07 §3).
     *
     * @return Collection<int, string>
     */
    public function assignedPermissionsFor(User $user, ?int $teacherWorkspaceId): Collection
    {
        if ($teacherWorkspaceId === null) {
            return collect();
        }

        return TeacherStaff::query()
            ->where('user_id', $user->id)
            ->where('teacher_workspace_id', $teacherWorkspaceId)
            ->get()
            ->flatMap(static fn (TeacherStaff $staff): Collection => $staff->permissions()
                ->where('permission_status', 'active')
                ->pluck('permission_name'))
            ->unique()
            ->values();
    }

    /**
     * Whether a permission name belongs to a scope.
     *
     * The name prefix and the scope enum differ for Parent: names use
     * `parent_linked_student.`, the enum is `parent_linked_students`. That
     * mapping is frozen (PERMISSION_REGISTRY.md) and is applied here rather
     * than by rewriting either value.
     */
    private function permissionBelongsToScope(string $permission, string $scope): bool
    {
        $prefix = match ($scope) {
            'platform' => 'platform.',
            'teacher_workspace' => 'teacher_workspace.',
            'student_account' => 'student_account.',
            'parent_linked_students' => 'parent_linked_student.',
            default => null,
        };

        return $prefix !== null && str_starts_with($permission, $prefix);
    }
}
