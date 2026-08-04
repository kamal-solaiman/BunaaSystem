<?php

declare(strict_types=1);

use App\Http\Middleware\AssignRequestId;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetRequestLocale;
use App\Support\Http\ApiExceptionRenderer;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Bunaa System — Application Bootstrap
|--------------------------------------------------------------------------
|
| Single Laravel 12 application. The React 19 browser application is served
| from within this same application (resources/js), so there is no separate
| frontend project, document root, or deployment package.
|
| Foundation only: no business logic, no feature routes, no domain rules.
|
*/

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        apiPrefix: 'api/v1',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /*
         * Runs first on every request so a correlation id exists even for a
         * failure raised during routing, before group middleware executes.
         */
        $middleware->prepend(AssignRequestId::class);

        // Sanctum stateful (cookie) authentication for the first-party SPA.
        $middleware->statefulApi();

        // The API surface returns JSON, never an HTML page or login redirect.
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);

        // Baseline safe headers on every response.
        $middleware->append(SecurityHeaders::class);

        // Arabic default, English fully supported; direction derives from language.
        $middleware->append(SetRequestLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // All API failures are normalized into the documented error envelope.
        $exceptions->render(
            static fn (Throwable $e, $request) => ApiExceptionRenderer::render($e, $request)
        );
    })
    ->create();
