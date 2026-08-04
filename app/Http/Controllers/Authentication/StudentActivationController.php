<?php

declare(strict_types=1);

namespace App\Http\Controllers\Authentication;

use App\Features\Authentication\Exceptions\StudentAccountAlreadyActiveException;
use App\Features\Authentication\Exceptions\StudentActivationMismatchException;
use App\Features\Authentication\Services\StudentAccountService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\ActivateStudentRequest;
use App\Support\Api\ApiResponse;
use App\Support\Api\ErrorCode;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/auth/students/activate — 10_API_Design.md §13.
 *
 * Activates a Teacher-created Student account (BR-022 Method 2). Activation is
 * the documented authentication exception path, so the endpoint is reachable
 * without an authenticated context: the account cannot be logged into until it
 * is activated.
 */
final class StudentActivationController extends Controller
{
    public function __construct(private readonly StudentAccountService $students)
    {
    }

    public function __invoke(ActivateStudentRequest $request): JsonResponse
    {
        try {
            $student = $this->students->activate(
                identifier: $request->string('identifier')->toString(),
                secret: $request->string('secret')->toString(),
                request: $request,
            );
        } catch (StudentActivationMismatchException) {
            // 404 — no matching pending-activation Teacher-created account.
            return ApiResponse::error(ErrorCode::StudentActivationMismatch);
        } catch (StudentAccountAlreadyActiveException) {
            // 409 — activation is a one-way transition.
            return ApiResponse::error(ErrorCode::StudentAccountAlreadyActive);
        }

        return ApiResponse::success([
            'id' => $student->id,
            'activation_status' => $student->activation_status,
            'created_by_method' => $student->created_by_method,
        ]);
    }
}
