<?php

declare(strict_types=1);

namespace App\Http\Controllers\Authentication;

use App\Features\Authentication\Exceptions\StudentDuplicateAccountException;
use App\Features\Authentication\Services\StudentAccountService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\RegisterStudentRequest;
use App\Support\Api\ApiResponse;
use App\Support\Api\ErrorCode;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/auth/students/register — 10_API_Design.md §13.
 *
 * Student self-registration (BR-022 Method 1). Documented error responses are
 * 409 and 422.
 */
final class StudentRegistrationController extends Controller
{
    public function __construct(private readonly StudentAccountService $students)
    {
    }

    public function __invoke(RegisterStudentRequest $request): JsonResponse
    {
        try {
            $student = $this->students->register(
                name: $request->string('name')->toString(),
                identifier: $request->string('identifier')->toString(),
                secret: $request->string('secret')->toString(),
                request: $request,
            );
        } catch (StudentDuplicateAccountException) {
            // 409. The message never reveals where, or with which Teacher, the
            // existing account studies (33 AUT-12).
            return ApiResponse::error(ErrorCode::StudentDuplicateAccount);
        }

        return ApiResponse::success([
            'id' => $student->id,
            'activation_status' => $student->activation_status,
            'created_by_method' => $student->created_by_method,
        ], status: 201);
    }
}
