import { Component, type ErrorInfo, type JSX, type ReactNode } from 'react';

interface Props {
    children: ReactNode;
}

interface State {
    hasError: boolean;
}

/**
 * Top-level recovery boundary.
 *
 * Contains an unexpected client failure and offers recovery. It must never
 * display request headers, credentials, raw backend payloads, stack traces,
 * Teacher Workspace identifiers, file paths, or private record data
 * (AI_DOCS/34_Error_Codes.md §26.2; 28_Coding_Standards.md §5.8).
 *
 * React requires a class component for error boundaries; this is the single
 * documented exception to the functional-component rule (§5.1).
 */
export class AppErrorBoundary extends Component<Props, State> {
    override state: State = { hasError: false };

    static getDerivedStateFromError(): State {
        return { hasError: true };
    }

    override componentDidCatch(error: Error, errorInfo: ErrorInfo): void {
        // Development only. Production diagnostics go through the approved
        // operational channel, never to the user's screen.
        if (import.meta.env.DEV) {
            console.error('Unhandled application error:', error, errorInfo);
        }
    }

    private readonly handleReload = (): void => {
        window.location.reload();
    };

    override render(): ReactNode {
        if (!this.state.hasError) {
            return this.props.children;
        }

        return this.renderFallback();
    }

    private renderFallback(): JSX.Element {
        return (
            <div
                role="alert"
                className="flex min-h-screen items-center justify-center bg-surface-sunken p-6"
            >
                <div className="w-full max-w-md rounded-card border border-border bg-surface-raised p-6 text-center">
                    <h1 className="text-lg font-semibold text-content">
                        Something went wrong
                    </h1>
                    <p className="mt-2 text-sm text-content-muted">
                        An unexpected error occurred. Please reload the page and try again.
                    </p>
                    <button
                        type="button"
                        onClick={this.handleReload}
                        className="mt-5 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-content"
                    >
                        Reload
                    </button>
                </div>
            </div>
        );
    }
}
