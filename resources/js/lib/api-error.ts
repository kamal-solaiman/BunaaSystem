import axios from 'axios';
import type { ApiErrorEnvelope, ApiErrorKind } from '@/types/api';

/**
 * Normalized error raised by the HTTP boundary.
 *
 * Every failure — HTTP, network, or cancellation — reaches feature code as this
 * one shape, so no component has to inspect raw axios internals
 * (AI_DOCS/28_Coding_Standards.md §5.8).
 *
 * It deliberately carries no request headers, credentials, raw payloads, stack
 * traces, Teacher Workspace identifiers, or file paths
 * (34_Error_Codes.md §26.2).
 */
export class ApiError extends Error {
    readonly kind: ApiErrorKind;
    readonly status: number | null;
    /** Stable machine-readable registry code, when the server supplied one. */
    readonly code: string | null;
    /** 422 field-level messages, keyed by field name. */
    readonly fieldErrors: Readonly<Record<string, string[]>>;
    /** Correlation id for support lookup. */
    readonly requestId: string | null;
    /** Seconds to wait, from Retry-After, when rate limited. */
    readonly retryAfter: number | null;

    constructor(init: {
        kind: ApiErrorKind;
        message: string;
        status?: number | null;
        code?: string | null;
        fieldErrors?: Record<string, string[]>;
        requestId?: string | null;
        retryAfter?: number | null;
    }) {
        super(init.message);
        this.name = 'ApiError';
        this.kind = init.kind;
        this.status = init.status ?? null;
        this.code = init.code ?? null;
        this.fieldErrors = Object.freeze(init.fieldErrors ?? {});
        this.requestId = init.requestId ?? null;
        this.retryAfter = init.retryAfter ?? null;
    }

    /** True when the failure is worth retrying without user correction. */
    get isRetryable(): boolean {
        return this.kind === 'rate-limited' || this.kind === 'network' || this.kind === 'server';
    }
}

function kindFromStatus(status: number): ApiErrorKind {
    if (status === 401 || status === 419) return 'unauthenticated';
    if (status === 403) return 'unauthorized';
    if (status === 404) return 'not-found';
    if (status === 409) return 'conflict';
    if (status === 422) return 'validation';
    if (status === 429) return 'rate-limited';
    return 'server';
}

function isErrorEnvelope(value: unknown): value is ApiErrorEnvelope {
    if (typeof value !== 'object' || value === null) return false;
    const candidate = value as { error?: unknown };
    if (typeof candidate.error !== 'object' || candidate.error === null) return false;
    return typeof (candidate.error as { message?: unknown }).message === 'string';
}

/**
 * Converts any thrown value into an ApiError.
 */
export function normalizeError(error: unknown, fallbackMessage: string): ApiError {
    if (error instanceof ApiError) {
        return error;
    }

    if (axios.isCancel(error)) {
        return new ApiError({ kind: 'cancelled', message: 'Request cancelled.' });
    }

    if (axios.isAxiosError(error)) {
        const response = error.response;

        // No response means the request never completed: offline, DNS, timeout.
        if (!response) {
            return new ApiError({ kind: 'network', message: fallbackMessage });
        }

        const envelope = isErrorEnvelope(response.data) ? response.data : null;
        const retryAfterHeader = response.headers?.['retry-after'];
        const retryAfter =
            typeof retryAfterHeader === 'string' && retryAfterHeader.trim() !== ''
                ? Number.parseInt(retryAfterHeader, 10)
                : null;

        return new ApiError({
            kind: kindFromStatus(response.status),
            // Prefer the server's safe user message; never surface raw payloads.
            message: envelope?.error.message ?? fallbackMessage,
            status: response.status,
            code: envelope?.error.code ?? null,
            fieldErrors: envelope?.errors ?? {},
            requestId: envelope?.request_id ?? null,
            retryAfter: Number.isFinite(retryAfter) ? retryAfter : null,
        });
    }

    return new ApiError({ kind: 'server', message: fallbackMessage });
}
