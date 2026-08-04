<?php

declare(strict_types=1);

namespace App\Features\Authorization\Services;

use App\Features\Authorization\Exceptions\PermissionNotAssignableException;
use App\Features\Authorization\Support\PermissionMatrix;
use App\Models\Permission;
use App\Models\TeacherStaff;
use App\Models\User;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditRecorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Grants and revokes Teacher Staff permissions.
 *
 * "Teacher Staff hold only permissions assigned by the Teacher"
 * (08_RBAC.md §2.6). Every change is a mandatory Permission Change audit event
 * (23_Security_Standards.md §15.2 item 6).
 *
 * This service performs the assignment. Deciding whether the caller may assign
 * is the Teacher Staff policy's job, driven by
 * `teacher_workspace.teacher_staff.assign_permission`.
 */
final readonly class PermissionAssignmentService
{
    public function __construct(private AuditRecorder $audit)
    {
    }

    /**
     * Assign a capability to a Teacher Staff member.
     *
     * @throws PermissionNotAssignableException
     */
    public function assign(
        TeacherStaff $staff,
        Permission $permission,
        User $actor,
        ?Request $request = null,
    ): void {
        $this->guardAssignable($permission);

        DB::transaction(function () use ($staff, $permission, $actor, $request): void {
            // Idempotent: the frozen unique constraint already forbids a
            // duplicate row, and a repeated assignment is not a state change.
            if ($staff->permissions()->where('permissions.id', $permission->id)->exists()) {
                return;
            }

            $staff->permissions()->attach($permission->id, [
                'teacher_workspace_id' => $staff->teacher_workspace_id,
            ]);

            $this->recordChange($staff, $permission, $actor, 'assigned', $request);
        });
    }

    /**
     * Revoke a capability from a Teacher Staff member.
     */
    public function revoke(
        TeacherStaff $staff,
        Permission $permission,
        User $actor,
        ?Request $request = null,
    ): void {
        DB::transaction(function () use ($staff, $permission, $actor, $request): void {
            if (! $staff->permissions()->where('permissions.id', $permission->id)->exists()) {
                return;
            }

            $staff->permissions()->detach($permission->id);

            $this->recordChange($staff, $permission, $actor, 'revoked', $request);
        });
    }

    /**
     * A Teacher may only delegate what a Teacher can hold.
     *
     * Two guards, both from the matrix rather than from invention:
     *
     *  - `teacher_workspace.teacher_staff.self_assign_permission` is Denied for
     *    every role: "Teacher Staff cannot grant themselves permissions"
     *    (09 §12). It can never be assigned to anyone.
     *  - A permission the Teacher does not hold cannot be delegated, because
     *    staff access is an intersection, not an inheritance (09 §5).
     *
     * @throws PermissionNotAssignableException
     */
    private function guardAssignable(Permission $permission): void
    {
        if (PermissionMatrix::grant('teacher', $permission->permission_name) === null) {
            throw new PermissionNotAssignableException();
        }

        if ($permission->permission_status !== 'active') {
            // "Must be active to be assigned" (07 §3).
            throw new PermissionNotAssignableException();
        }
    }

    /**
     * Record the mandatory Permission Change event.
     *
     * The entry is written in the same transaction as the change, so a grant
     * can never exist without its audit record (23 §15.4).
     *
     * Attribution follows 08 §15: the actor is whoever performed the change —
     * the Teacher — while the affected entity is the staff member.
     */
    private function recordChange(
        TeacherStaff $staff,
        Permission $permission,
        User $actor,
        string $outcome,
        ?Request $request,
    ): void {
        $this->audit->record(
            event: AuditEvent::PermissionChange,
            affectedEntityName: 'Teacher Staff',
            affectedEntityId: $staff->id,
            actorUserId: $actor->id,
            actorRole: 'teacher',
            scopeContext: 'teacher_workspace',
            teacherWorkspaceId: $staff->teacher_workspace_id,
            details: [
                'outcome' => $outcome,
                'permission_name' => $permission->permission_name,
                'permission_scope' => $permission->permission_scope,
            ],
            request: $request,
        );
    }
}
