<?php

declare(strict_types=1);

namespace App\Support\Api;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * The single place where the documented API envelopes are built.
 *
 * Success and error envelopes are defined by AI_DOCS/10_API_Design.md §6, §7,
 * §10 and restated in AI_DOCS/34_Error_Codes.md §26.1. Controllers must not
 * hand-assemble response arrays; they return through this boundary so the
 * contract stays consistent across every endpoint.
 */
final class ApiResponse
{
    /**
     * Success envelope.
     *
     * @param  array<string, mixed>  $meta
     */
    public static function success(mixed $data = null, array $meta = [], int $status = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'data' => $data,
        ];

        if ($meta !== []) {
            $payload['meta'] = $meta;
        }

        return self::json($payload, $status);
    }

    /**
     * Paginated success envelope.
     *
     * Pagination metadata is limited to the documented fields; pagination is
     * always applied after authorization and scope filtering
     * (10_API_Design.md §7).
     *
     * @param  LengthAwarePaginator<int, mixed>  $paginator
     * @param  array<int, mixed>|null  $data
     */
    public static function paginated(LengthAwarePaginator $paginator, ?array $data = null): JsonResponse
    {
        return self::json([
            'success' => true,
            'data' => $data ?? $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    /**
     * Error envelope.
     *
     * @param  array<string, mixed>|null  $details  Non-sensitive detail only.
     * @param  array<string, array<int, string>>|null  $errors  422 field messages only.
     */
    public static function error(
        ErrorCode $code,
        ?string $message = null,
        ?array $details = null,
        ?array $errors = null,
        ?int $status = null,
        ?string $requestId = null,
    ): JsonResponse {
        $payload = [
            'success' => false,
            'error' => [
                'code' => $code->value,
                'message' => $message ?? self::message($code),
            ],
        ];

        if ($details !== null && $details !== []) {
            $payload['error']['details'] = $details;
        }

        if ($requestId !== null) {
            $payload['request_id'] = $requestId;
        }

        if ($errors !== null && $errors !== []) {
            $payload['errors'] = $errors;
        }

        return self::json($payload, $status ?? $code->status());
    }

    /**
     * Resolve the translated registry user message for a code.
     */
    public static function message(ErrorCode $code): string
    {
        $key = $code->messageKey();
        $translated = trans($key);

        // Missing translations must never surface a raw key to the user.
        return is_string($translated) && $translated !== $key
            ? $translated
            : trans('errors.'.ErrorCode::SystemUnexpected->value);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private static function json(array $payload, int $status): JsonResponse
    {
        return new JsonResponse($payload, $status, [], JSON_UNESCAPED_UNICODE);
    }
}
