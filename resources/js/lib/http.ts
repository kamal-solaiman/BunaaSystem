import axios, { type AxiosInstance, type AxiosRequestConfig } from 'axios';
import { env } from '@/config/env';
import { ApiError, normalizeError } from '@/lib/api-error';
import { t } from '@/locales';
import type { ApiPaginated, ApiSuccess } from '@/types/api';

/**
 * The single HTTP boundary to the Laravel REST API.
 *
 * Feature modules reach the API through their own typed adapters, which call
 * this module. Presentation components never issue requests directly
 * (AI_DOCS/12_Frontend_Architecture.md; 28_Coding_Standards.md §5.7).
 *
 * Sanctum cookie authentication requires credentials on every request and the
 * XSRF header on every state-changing request; axios handles the latter from
 * the XSRF-TOKEN cookie.
 */
const client: AxiosInstance = axios.create({
    baseURL: env.apiBaseUrl,
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

/**
 * Called when the server reports the session is gone.
 *
 * The auth layer registers a handler that clears protected context and the
 * query cache before redirecting to login (34_Error_Codes.md §26.2).
 */
type UnauthenticatedHandler = () => void;

let onUnauthenticated: UnauthenticatedHandler | null = null;

export function setUnauthenticatedHandler(handler: UnauthenticatedHandler | null): void {
    onUnauthenticated = handler;
}

client.interceptors.response.use(
    (response) => response,
    (error: unknown) => {
        const normalized = normalizeError(error, t('error.request_failed'));

        if (normalized.kind === 'unauthenticated') {
            onUnauthenticated?.();
        }

        return Promise.reject(normalized);
    },
);

/**
 * Laravel Sanctum requires a CSRF cookie before the first stateful request.
 * The endpoint sits outside the versioned API prefix.
 */
export async function initializeCsrf(): Promise<void> {
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
}

async function unwrap<TData>(request: Promise<{ data: unknown }>): Promise<TData> {
    const response = await request;
    const body = response.data as ApiSuccess<TData>;

    // A success envelope always carries `data`; anything else is a contract break.
    if (typeof body !== 'object' || body === null || !('data' in body)) {
        throw new ApiError({
            kind: 'server',
            message: t('error.unexpected_response'),
        });
    }

    return body.data;
}

export const http = {
    /** GET returning the unwrapped `data` payload. */
    get<TData>(url: string, config?: AxiosRequestConfig): Promise<TData> {
        return unwrap<TData>(client.get(url, config));
    },

    /** GET returning the full paginated envelope, including `meta`. */
    async getPaginated<TItem>(
        url: string,
        config?: AxiosRequestConfig,
    ): Promise<ApiPaginated<TItem>> {
        const response = await client.get<ApiPaginated<TItem>>(url, config);

        return response.data;
    },

    post<TData>(url: string, payload?: unknown, config?: AxiosRequestConfig): Promise<TData> {
        return unwrap<TData>(client.post(url, payload, config));
    },

    put<TData>(url: string, payload?: unknown, config?: AxiosRequestConfig): Promise<TData> {
        return unwrap<TData>(client.put(url, payload, config));
    },

    patch<TData>(url: string, payload?: unknown, config?: AxiosRequestConfig): Promise<TData> {
        return unwrap<TData>(client.patch(url, payload, config));
    },

    /**
     * Archive and restore are actions, not deletions. The API exposes no hard
     * delete, so no `delete` helper exists here
     * (28_Coding_Standards.md §2.4; 10_API_Design.md §12).
     */

    /** Multipart upload for endpoints that accept a binary file. */
    upload<TData>(url: string, formData: FormData, config?: AxiosRequestConfig): Promise<TData> {
        return unwrap<TData>(
            client.post(url, formData, {
                ...config,
                headers: { ...config?.headers, 'Content-Type': 'multipart/form-data' },
            }),
        );
    },
};
