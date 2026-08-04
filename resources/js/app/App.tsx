import { useState, type JSX } from 'react';
import { QueryClientProvider } from '@tanstack/react-query';
import { createQueryClient } from '@/lib/query-client';
import { AppErrorBoundary } from '@/app/AppErrorBoundary';
import { AppRouter } from '@/routes/AppRouter';

/**
 * Application root.
 *
 * Composes providers and the top-level recovery boundary. It owns no feature
 * workflow (AI_DOCS/12_Frontend_Architecture.md §2).
 *
 * Provider order matters: the error boundary wraps the providers so a failure
 * raised while a provider initializes is still contained.
 */
export function App(): JSX.Element {
    // Created once per application instance rather than at module scope, so the
    // cache never leaks between tests or renders.
    const [queryClient] = useState(createQueryClient);

    return (
        <AppErrorBoundary>
            <QueryClientProvider client={queryClient}>
                <AppRouter />
            </QueryClientProvider>
        </AppErrorBoundary>
    );
}
