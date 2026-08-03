<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The API surface speaks JSON only.
 *
 * Forcing the Accept header guarantees that framework-generated failures are
 * normalized into the documented error envelope instead of rendering an HTML
 * error page or a login redirect (AI_DOCS/10_API_Design.md §2;
 * 34_Error_Codes.md §26.1).
 */
final class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
