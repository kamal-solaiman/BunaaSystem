<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Features\Authentication\Services\RoleContextResolver;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The authenticated user context.
 *
 * `GET /auth/me` returns "Current user identity, active role, and permitted
 * scopes", and the response "must not expose unauthorized role contexts"
 * (10_API_Design.md §13).
 *
 * Only role contexts the user actually holds are reported, read from the
 * frozen `role_user` table. The authentication secret is never present: it is
 * hidden on the model and absent here (23_Security_Standards.md §3.6).
 *
 * @mixin User
 */
final class AuthenticatedUserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        $resolver = app(RoleContextResolver::class);

        $payload = [
            'id' => $user->id,
            'name' => $user->name,
            'identifier' => $user->email,
            'account_status' => $user->account_status,
            // Every context the user holds; empty until the authorization
            // phase assigns roles.
            'role_contexts' => $resolver->contextsFor($user),
            'permitted_scopes' => $resolver->permittedScopesFor($user),
            // Teacher Staff capabilities. Empty while Q-011 is PENDING, and
            // populated automatically once the catalog is confirmed.
            'permissions' => $resolver->assignedPermissionsFor($user),
        ];

        // The Student context is reported only when one exists, so no role is
        // implied for a user who does not hold it.
        $student = Student::query()->where('user_id', $user->id)->first();

        if ($student instanceof Student) {
            $payload['student'] = [
                'id' => $student->id,
                'activation_status' => $student->activation_status,
                'account_status' => $student->account_status,
                'created_by_method' => $student->created_by_method,
            ];
        }

        return $payload;
    }
}
