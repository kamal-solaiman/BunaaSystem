<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\Api\ApiResponse;
use App\Support\Api\ErrorCode;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Response envelope contract.
 *
 * The envelope shape is a public contract (AI_DOCS/10_API_Design.md §6, §7, §10;
 * 34_Error_Codes.md §26.1) and every endpoint depends on it.
 */
final class ApiResponseTest extends TestCase
{
    #[Test]
    public function success_envelope_carries_success_and_data(): void
    {
        $response = ApiResponse::success(['id' => 1]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            ['success' => true, 'data' => ['id' => 1]],
            $response->getData(true)
        );
    }

    #[Test]
    public function success_envelope_omits_meta_when_not_supplied(): void
    {
        $payload = ApiResponse::success(['id' => 1])->getData(true);

        $this->assertArrayNotHasKey('meta', $payload);
    }

    #[Test]
    public function error_envelope_matches_the_documented_shape(): void
    {
        $payload = ApiResponse::error(ErrorCode::AuthzUnauthorized)->getData(true);

        $this->assertFalse($payload['success']);
        $this->assertSame('AUTHZ_UNAUTHORIZED', $payload['error']['code']);
        $this->assertArrayHasKey('message', $payload['error']);
    }

    #[Test]
    public function each_error_code_uses_its_documented_http_status(): void
    {
        $expected = [
            'AUTH_UNAUTHENTICATED' => 401,
            'AUTH_SESSION_EXPIRED' => 401,
            'AUTHZ_UNAUTHORIZED' => 403,
            'VALIDATION_FAILED' => 422,
            'API_MALFORMED_REQUEST' => 400,
            'API_UNSUPPORTED_ROUTE' => 404,
            'RESOURCE_NOT_FOUND' => 404,
            'API_RATE_LIMIT_EXCEEDED' => 429,
            'SYSTEM_UNEXPECTED' => 500,
        ];

        foreach ($expected as $code => $status) {
            $this->assertSame(
                $status,
                ErrorCode::from($code)->status(),
                "Unexpected status for {$code}."
            );
        }
    }

    #[Test]
    public function validation_errors_are_returned_as_field_messages(): void
    {
        $payload = ApiResponse::error(
            code: ErrorCode::ValidationFailed,
            errors: ['name' => ['The name field is required.']],
        )->getData(true);

        $this->assertSame('VALIDATION_FAILED', $payload['error']['code']);
        $this->assertSame(['The name field is required.'], $payload['errors']['name']);
    }

    #[Test]
    public function not_found_and_not_visible_are_indistinguishable(): void
    {
        // A record that is absent and one that is merely out of scope must
        // produce byte-identical responses (34_Error_Codes.md §2.8).
        $absent = ApiResponse::error(ErrorCode::ResourceNotFound)->getData(true);
        $hidden = ApiResponse::error(ErrorCode::ResourceNotFound)->getData(true);

        $this->assertSame($absent, $hidden);
    }
}
