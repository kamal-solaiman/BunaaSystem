import { QueryClient } from '@tanstack/react-query';
import { ApiError } from '@/lib/api-error';

/**
 * TanStack Query owns all server state.
 *
 * Retry policy is deliberately conservative: a 401, 403, 404, 409, or 422 is a
 * decision, not a transient fault, so retrying would only repeat a rejected
 * request (AI_DOCS/28_Coding_Standards.md §5.4, §5.8).
 */
export function createQueryClient(): QueryClient {
    return new QueryClient({
        defaultOptions: {
            queries: {
                staleTime: 30_000,
                gcTime: 5 * 60_000,
                refetchOnWindowFocus: false,
                retry: (failureCount, error) => {
                    if (error instanceof ApiError) {
                        return error.isRetryable && failureCount < 2;
                    }

                    return false;
                },
            },
            mutations: {
                // A mutation is never retried automatically: a duplicate write
                // could double-apply a recorded action.
                retry: false,
            },
        },
    });
}
