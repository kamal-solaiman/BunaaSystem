<?php

declare(strict_types=1);

namespace App\Http\Controllers\Authentication;

use App\Features\Authentication\Services\AuthenticationService;
use App\Http\Controllers\Controller;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * POST /api/v1/auth/logout — 10_API_Design.md §13.
 *
 * Destroys all session data. Historical login Audit Log records are never
 * removed (10 §13; 23 §7.2).
 */
final class LogoutController extends Controller
{
    public function __construct(private readonly AuthenticationService $authentication)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $this->authentication->logout($request);

        return ApiResponse::success(['logged_out' => true]);
    }
}
