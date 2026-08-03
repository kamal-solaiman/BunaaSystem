<?php

declare(strict_types=1);

use App\Support\Api\ApiResponse;
use App\Support\Api\ErrorCode;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — /api/v1
|--------------------------------------------------------------------------
|
| Every Version 1 endpoint lives under the /api/v1 prefix, which is applied by
| bootstrap/app.php (AI_DOCS/10_API_Design.md §5).
|
| Scope groups follow AI_DOCS/28_Coding_Standards.md §13.1:
|
|   /api/v1/auth/{action}                Authentication
|   /api/v1/platform/{resource}          Super Admin
|   /api/v1/teacher-workspace/{resource} Teacher and Teacher Staff
|   /api/v1/student/{resource}
|   /api/v1/parent/{resource}
|
| Foundation phase: no endpoint is registered. The scope groups below are
| reserved, empty access boundaries so each later phase attaches its documented
| routes to an already-correct structure. Endpoints are only ever added from the
| catalog in 10_API_Design.md §13–§30 — never invented.
|
| Version 1 defines no notification endpoints (10_API_Design.md §29).
|
*/

Route::prefix('auth')->name('auth.')->group(static function (): void {
    /*
     * Registered in the Authentication phase, per 10_API_Design.md §13:
     * POST auth/login, POST auth/logout, GET auth/me,
     * POST auth/students/register, POST auth/students/activate.
     */
});

Route::middleware('auth:sanctum')->group(static function (): void {
    Route::prefix('platform')->name('platform.')->group(static function (): void {
        // Super Admin, Platform-scoped.
    });

    Route::prefix('teacher-workspace')->name('teacher-workspace.')->group(static function (): void {
        // Teacher and authorized Teacher Staff, single workspace scope.
    });

    Route::prefix('student')->name('student.')->group(static function (): void {
        // Authenticated Student, own account scope.
    });

    Route::prefix('parent')->name('parent.')->group(static function (): void {
        // Parent, linked-Student read-only scope.
    });
});

/*
| Any unmatched /api/v1 request is an unsupported route. Returning the
| documented envelope keeps scanner and outdated-client traffic from receiving
| an HTML error page (34_Error_Codes.md API-02).
*/
Route::fallback(static fn () => ApiResponse::error(ErrorCode::ApiUnsupportedRoute));
