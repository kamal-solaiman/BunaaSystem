<?php

declare(strict_types=1);

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthenticatedUserResource;
use App\Models\User;
use App\Support\Api\ApiResponse;
use App\Support\Api\ErrorCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GET /api/v1/auth/me — 10_API_Design.md §13.
 *
 * Returns the current identity, role contexts, and permitted scopes. The
 * route is protected by auth:sanctum, so an unauthenticated caller never
 * reaches this controller; the guard below is defence in depth (33 AUT-14).
 */
final class CurrentUserController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(ErrorCode::AuthUnauthenticated);
        }

        return ApiResponse::success(
            (new AuthenticatedUserResource($user))->toArray($request)
        );
    }
}
