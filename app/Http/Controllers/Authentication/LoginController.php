<?php

declare(strict_types=1);

namespace App\Http\Controllers\Authentication;

use App\Features\Authentication\Services\AuthenticationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Resources\AuthenticatedUserResource;
use App\Support\Api\ApiResponse;
use App\Support\Api\ErrorCode;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/auth/login — 10_API_Design.md §13.
 *
 * Thin coordinator: validated request in, service call, standardized response
 * out (28_Coding_Standards.md §3.2).
 */
final class LoginController extends Controller
{
    public function __construct(private readonly AuthenticationService $authentication)
    {
    }

    public function __invoke(LoginRequest $request): JsonResponse
    {
        $user = $this->authentication->attemptLogin(
            identifier: $request->string('identifier')->toString(),
            secret: $request->string('secret')->toString(),
            request: $request,
        );

        if ($user === null) {
            // One generic failure for every cause: unknown identifier, wrong
            // secret, or archived account (23 §3.3; 33 AUT-04).
            return ApiResponse::error(ErrorCode::AuthInvalidCredentials);
        }

        return ApiResponse::success(
            (new AuthenticatedUserResource($user))->toArray($request)
        );
    }
}
