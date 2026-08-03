import { AxiosError, AxiosHeaders } from 'axios';
import { describe, expect, it } from 'vitest';
import { ApiError, normalizeError } from '@/lib/api-error';

/**
 * HTTP boundary error normalization.
 *
 * Confirms that every backend outcome maps to the documented frontend taxonomy
 * (AI_DOCS/34_Error_Codes.md §26.2; 28_Coding_Standards.md §5.8).
 */
function axiosErrorWithStatus(status: number, data: unknown = {}): AxiosError {
    const error = new AxiosError('Request failed');

    error.response = {
        status,
        statusText: '',
        data,
        headers: new AxiosHeaders(),
        config: { headers: new AxiosHeaders() },
    };

    return error;
}

describe('normalizeError', () => {
    it.each([
        [401, 'unauthenticated'],
        [403, 'unauthorized'],
        [404, 'not-found'],
        [409, 'conflict'],
        [422, 'validation'],
        [429, 'rate-limited'],
        [500, 'server'],
    ])('maps HTTP %i to the %s class', (status, expected) => {
        const error = normalizeError(axiosErrorWithStatus(status), 'fallback');

        expect(error.kind).toBe(expected);
        expect(error.status).toBe(status);
    });

    it('treats an expired CSRF session as unauthenticated', () => {
        expect(normalizeError(axiosErrorWithStatus(419), 'fallback').kind).toBe('unauthenticated');
    });

    it('reports a missing response as a network failure', () => {
        expect(normalizeError(new AxiosError('offline'), 'fallback').kind).toBe('network');
    });

    it('keeps the server error code and safe message', () => {
        const error = normalizeError(
            axiosErrorWithStatus(403, {
                success: false,
                error: {
                    code: 'AUTHZ_UNAUTHORIZED',
                    message: 'You do not have permission to perform this action.',
                },
            }),
            'fallback',
        );

        expect(error.code).toBe('AUTHZ_UNAUTHORIZED');
        expect(error.message).toBe('You do not have permission to perform this action.');
    });

    it('exposes 422 field messages for the active form', () => {
        const error = normalizeError(
            axiosErrorWithStatus(422, {
                success: false,
                error: { code: 'VALIDATION_FAILED', message: 'The submitted data is invalid.' },
                errors: { name: ['The name field is required.'] },
            }),
            'fallback',
        );

        expect(error.kind).toBe('validation');
        expect(error.fieldErrors['name']).toEqual(['The name field is required.']);
    });

    it('marks only transient failures as retryable', () => {
        expect(normalizeError(axiosErrorWithStatus(429), 'f').isRetryable).toBe(true);
        expect(normalizeError(axiosErrorWithStatus(500), 'f').isRetryable).toBe(true);
        // A rejected decision must never be retried automatically.
        expect(normalizeError(axiosErrorWithStatus(403), 'f').isRetryable).toBe(false);
        expect(normalizeError(axiosErrorWithStatus(422), 'f').isRetryable).toBe(false);
    });

    it('returns an ApiError unchanged', () => {
        const original = new ApiError({ kind: 'server', message: 'boom' });

        expect(normalizeError(original, 'fallback')).toBe(original);
    });
});
