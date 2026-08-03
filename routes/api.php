<?php

declare(strict_types=1);

use App\Support\Api\ApiResponse;
use App\Support\Api\ErrorCode;
use Illuminate\Http\Request;
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
|   /api/v1/auth/{action}
|   /api/v1/platform/{resource}          Super Admin
|   /api/v1/teacher-workspace/{resource} Teacher and Teacher Staff
|   /api/v1/student/{resource}
|   /api/v1/parent/{resource}
|
| Foundation phase: no feature endpoints are registered. Scope groups are
| declared as empty, reserved boundaries so later phases attach routes to an
| already-correct access structure. No notification routes exist in Version 1.
|
*/

Route::prefix('auth')->name('auth.')->group(static function (): void {
    // Registered in the authentication phase.
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

    /*
    | Returns the authenticated context and is the reference implementation of
    | a protected endpoint: it proves the Sanctum guard, the JSON error
    | envelope, and the scope groups above are wired correctly before any
    | feature route exists. It exposes only the framework identity fields the
    | User model already declares — no role, permission, or workspace data,
    | which belong to the authentication and authorization phases.
    */
    Route::get('session', static fn (Request $request) => ApiResponse::success([
        'id' => $request->user()?->getAuthIdentifier(),
    ]))->name('session');
});

/*
| Any unmatched /api/v1 request is an unsupported route. Returning the
| documented envelope keeps scanner and outdated-client traffic from receiving
| an HTML error page (34_Error_Codes.md API-02).
*/
Route::fallback(static fn () => ApiResponse::error(ErrorCode::ApiUnsupportedRoute));
