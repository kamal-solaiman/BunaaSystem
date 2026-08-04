import { Suspense, type JSX } from 'react';
import { BrowserRouter, Route, Routes } from 'react-router';
import { routerBasename } from '@/routes/basename';
import { t } from '@/locales';

/**
 * Route composition.
 *
 * Routes are grouped by access boundary — public, Platform (Super Admin),
 * Teacher Workspace, Student, Parent — rather than by visual similarity, and
 * are lazily loaded at feature and layout boundaries
 * (AI_DOCS/12_Frontend_Architecture.md §4; 28_Coding_Standards.md §5.6).
 *
 * A route guard is a usability measure only. Laravel remains the authorization
 * authority for every request.
 *
 * Foundation phase: the access-boundary groups are reserved but empty. Feature
 * routes are registered in their own phases.
 */
export function AppRouter(): JSX.Element {
    return (
        <BrowserRouter basename={routerBasename()}>
            <Suspense fallback={<RouteFallback />}>
                <Routes>
                    {/* Public routes are registered in the authentication phase. */}

                    {/* Scoped route groups:
                        /platform/*          Super Admin
                        /teacher-workspace/* Teacher and Teacher Staff
                        /student/*           Student
                        /parent/*            Parent */}

                    <Route path="*" element={<NotFoundRoute />} />
                </Routes>
            </Suspense>
        </BrowserRouter>
    );
}

/** Shown while a lazily loaded route module is being fetched. */
function RouteFallback(): JSX.Element {
    return (
        <div
            role="status"
            aria-live="polite"
            className="flex min-h-screen items-center justify-center"
        >
            <span className="text-sm text-content-muted">{t('common.loading')}</span>
        </div>
    );
}

/**
 * Neutral not-found state.
 *
 * It never distinguishes an inaccessible resource from an absent one
 * (34_Error_Codes.md §26.2).
 */
function NotFoundRoute(): JSX.Element {
    return (
        <div className="flex min-h-screen items-center justify-center p-6">
            <p className="text-sm text-content-muted">{t('common.not_found')}</p>
        </div>
    );
}
