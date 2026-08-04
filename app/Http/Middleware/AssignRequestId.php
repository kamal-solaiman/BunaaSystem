<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Http\RequestId;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Assigns the correlation identifier carried by the `request_id` envelope field
 * and the X-Request-Id response header (AI_DOCS/10_API_Design.md §6).
 *
 * Runs globally and first, so the identifier exists even for a failure raised
 * during routing — before any route-group middleware has had a chance to run.
 */
final class AssignRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = RequestId::ensure($request);

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set(RequestId::HEADER, $requestId);

        return $response;
    }
}
