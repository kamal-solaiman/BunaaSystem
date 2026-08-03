<?php

declare(strict_types=1);

namespace App\Features\Authorization\Support;

/**
 * One role context an actor holds, resolved for an authorization decision.
 *
 * A User may hold several (07_Data_Dictionary.md §1). Contexts are evaluated
 * independently and never blended: "Role switching must establish a new session
 * context, not blend roles" (23_Security_Standards.md §7.3).
 */
final readonly class AuthorizationContext
{
    /**
     * @param  string  $role  One of the five confirmed roles.
     * @param  string  $scope  The role's boundary.
     * @param  int|null  $teacherWorkspaceId  Set for workspace-scoped contexts only.
     */
    public function __construct(
        public string $role,
        public string $scope,
        public ?int $teacherWorkspaceId = null,
    ) {
    }

    public function isWorkspaceScoped(): bool
    {
        return $this->scope === 'teacher_workspace';
    }
}
