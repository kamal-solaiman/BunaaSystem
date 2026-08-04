<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Support\Api\ApiResponse;
use App\Support\Api\ErrorCode;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Exception normalization layer.
 *
 * Every exception reaching the API surface is mapped to the documented error
 * envelope (AI_DOCS/34_Error_Codes.md §26.1). No stack trace, SQL, server path,
 * framework internal, Teacher-private data, or unlinked Student data may ever
 * appear in a response (§2.1, §2.8; 23_Security_Standards.md §18).
 *
 * Not-found and not-visible are indistinguishable by design (§2.8).
 *
 * Ordering note: Laravel calls this renderer *after* its own prepareException()
 * step, which has already converted AuthorizationException to
 * AccessDeniedHttpException, ModelNotFoundException and RecordsNotFoundException
 * to NotFoundHttpException, and TokenMismatchException to a 419 HttpException.
 * The mapping below therefore keys on the prepared exception types rather than
 * the original ones, so no branch is unreachable.
 */
final class ApiExceptionRenderer
{
    /**
     * Returns the normalized JSON response, or null to let Laravel handle
     * non-API (browser) rendering.
     */
    public static function render(Throwable $e, Request $request): ?JsonResponse
    {
        if (! self::expectsApiResponse($request)) {
            return null;
        }

        $requestId = RequestId::get($request);

        return match (true) {
            $e instanceof ValidationException => ApiResponse::error(
                code: ErrorCode::ValidationFailed,
                errors: $e->errors(),
                requestId: $requestId,
            ),

            // 401 never confirms whether an account exists.
            $e instanceof AuthenticationException => ApiResponse::error(
                code: ErrorCode::AuthUnauthenticated,
                requestId: $requestId,
            ),

            // Covers a genuinely undefined route as well as a model that was
            // not found or was filtered out of the actor's scope. Both collapse
            // to the same neutral response.
            $e instanceof NotFoundHttpException => self::notFound($e, $requestId),

            $e instanceof HttpExceptionInterface => self::fromHttpStatus($e, $requestId),

            default => ApiResponse::error(
                code: ErrorCode::SystemUnexpected,
                requestId: $requestId,
            ),
        };
    }

    /**
     * Requests to the API surface always receive the JSON envelope.
     */
    public static function expectsApiResponse(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }

    /**
     * A NotFoundHttpException raised by the router carries no previous
     * exception; one produced from a missing model does. That distinction
     * chooses the code without ever revealing which record was addressed.
     */
    private static function notFound(NotFoundHttpException $e, ?string $requestId): JsonResponse
    {
        $code = $e->getPrevious() === null
            ? ErrorCode::ApiUnsupportedRoute
            : ErrorCode::ResourceNotFound;

        return ApiResponse::error(code: $code, requestId: $requestId);
    }

    private static function fromHttpStatus(HttpExceptionInterface $e, ?string $requestId): JsonResponse
    {
        $status = $e->getStatusCode();

        $code = match ($status) {
            // A wrong HTTP verb is an invalid operation, not a server fault.
            400, 405 => ErrorCode::ApiMalformedRequest,
            401 => ErrorCode::AuthUnauthenticated,
            403 => ErrorCode::AuthzUnauthorized,
            404 => ErrorCode::ResourceNotFound,
            // Laravel maps an expired CSRF token to 419; to the client this is
            // simply an expired session.
            419 => ErrorCode::AuthSessionExpired,
            429 => ErrorCode::ApiRateLimitExceeded,
            default => ErrorCode::SystemUnexpected,
        };

        // The documented status set is authoritative: a code always carries its
        // registered status, and an unmapped status collapses to 500.
        $resolvedStatus = $code === ErrorCode::SystemUnexpected ? 500 : $code->status();

        $response = ApiResponse::error(
            code: $code,
            // Never echo the framework's own exception text back to the client.
            status: $resolvedStatus,
            requestId: $requestId,
        );

        // Retry-After is communicated; internal thresholds never are.
        $retryAfter = $e->getHeaders()['Retry-After'] ?? null;

        if ($retryAfter !== null) {
            $response->headers->set('Retry-After', (string) $retryAfter);
        }

        return $response;
    }
}
