<?php

declare(strict_types=1);

namespace App\Support\Api;

/**
 * Foundation subset of the error code registry defined in
 * AI_DOCS/34_Error_Codes.md.
 *
 * Codes are a public contract: unique, stable, never reused or reassigned
 * (34_Error_Codes.md §2.7). Only the transport, authentication, authorization,
 * validation, and system codes required by the foundation are registered here.
 * Feature/domain codes are added in their own phases, never invented ad hoc.
 */
enum ErrorCode: string
{
    // §5 Authentication
    case AuthUnauthenticated = 'AUTH_UNAUTHENTICATED';
    case AuthSessionExpired = 'AUTH_SESSION_EXPIRED';

    // §6 Authorization
    case AuthzUnauthorized = 'AUTHZ_UNAUTHORIZED';

    // §7 Validation
    case ValidationFailed = 'VALIDATION_FAILED';

    // §19 API contract / transport
    case ApiMalformedRequest = 'API_MALFORMED_REQUEST';
    case ApiUnsupportedRoute = 'API_UNSUPPORTED_ROUTE';
    case ApiRateLimitExceeded = 'API_RATE_LIMIT_EXCEEDED';
    case ResourceNotFound = 'RESOURCE_NOT_FOUND';

    // §22 System
    case SystemUnexpected = 'SYSTEM_UNEXPECTED';

    /**
     * The HTTP status this code is documented to carry.
     */
    public function status(): int
    {
        return match ($this) {
            self::AuthUnauthenticated, self::AuthSessionExpired => 401,
            self::AuthzUnauthorized => 403,
            self::ApiUnsupportedRoute, self::ResourceNotFound => 404,
            self::ValidationFailed => 422,
            self::ApiRateLimitExceeded => 429,
            self::ApiMalformedRequest => 400,
            self::SystemUnexpected => 500,
        };
    }

    /**
     * Translation key for the registry user message.
     *
     * Error codes stay English and stable; the user-facing message is
     * translatable (AI_DOCS/41_Internationalization_i18n.md §10, §22).
     */
    public function messageKey(): string
    {
        return 'errors.'.$this->value;
    }
}
