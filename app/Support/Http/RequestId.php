<?php

declare(strict_types=1);

namespace App\Support\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Correlation identifier for support and tracing.
 *
 * The optional `request_id` envelope field (AI_DOCS/10_API_Design.md §6) lets an
 * operator find the internal diagnostic in the operational log without ever
 * returning that diagnostic to the client (34_Error_Codes.md §25.2).
 *
 * The value is opaque and carries no user, Teacher Workspace, or record data.
 *
 * It is stored on the request rather than in a static property, so it cannot
 * leak from one request into the next when several requests share a process
 * (queue workers, test runs).
 */
final class RequestId
{
    public const HEADER = 'X-Request-Id';

    private const ATTRIBUTE = 'request_id';

    /**
     * Returns the identifier for this request, assigning one on first call.
     */
    public static function ensure(Request $request): string
    {
        $existing = self::get($request);

        if ($existing !== null) {
            return $existing;
        }

        $requestId = (string) Str::uuid();
        $request->attributes->set(self::ATTRIBUTE, $requestId);

        return $requestId;
    }

    /**
     * Returns the identifier already assigned to this request, if any.
     */
    public static function get(Request $request): ?string
    {
        $value = $request->attributes->get(self::ATTRIBUTE);

        return is_string($value) && $value !== '' ? $value : null;
    }
}
