<?php

declare(strict_types=1);

use App\Http\Controllers\Authentication\CurrentUserController;
use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Authentication\LogoutController;
use App\Http\Controllers\Authentication\StudentActivationController;
use App\Http\Controllers\Authentication\StudentRegistrationController;
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
| The authentication endpoints of §13 are registered. The remaining scope
| groups stay reserved and empty until their own phases. Endpoints are only
| ever added from the catalog in 10_API_Design.md §13–§30 — never invented.
|
| Version 1 defines no notification endpoints (10_API_Design.md §29).
|
*/

/*
| Authentication — 10_API_Design.md §13.
|
| Exactly the five documented endpoints; no other authentication surface
| exists. "Login as Teacher" impersonation is explicitly not part of Version 1
| (10 §3 rule 5).
|
| The public endpoints are rate-limited: 33_Validation_Rules.md AUT-05 confirms
| that a limit must exist while deliberately leaving the threshold unconfirmed,
| so the numeric value lives in configuration rather than being presented as a
| product number.
*/
Route::prefix('auth')->name('auth.')->group(static function (): void {
    Route::middleware('throttle:auth')->group(static function (): void {
        Route::post('login', LoginController::class)->name('login');

        Route::prefix('students')->name('students.')->group(static function (): void {
            Route::post('register', StudentRegistrationController::class)->name('register');
            // Activation is the documented authentication exception path: the
            // account cannot be logged into until it is activated.
            Route::post('activate', StudentActivationController::class)->name('activate');
        });
    });

    Route::middleware('auth:sanctum')->group(static function (): void {
        Route::post('logout', LogoutController::class)->name('logout');
        Route::get('me', CurrentUserController::class)->name('me');
    });
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
