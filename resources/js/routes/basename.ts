/**
 * Resolves the React Router basename at runtime.
 *
 * The application may be served from a subdirectory (for example
 * `public_html/113`), so the client route prefix cannot be a build-time
 * constant — that would bake a path into the bundle and break if the
 * subdirectory changes.
 *
 * Laravel renders the real base path into a meta tag, so the same build works
 * whether the application is served from a domain root or a subdirectory.
 */
export function routerBasename(): string {
    const meta = document.querySelector<HTMLMetaElement>('meta[name="app-base"]');
    const base = meta?.content?.trim() ?? '';

    if (base === '' || base === '/') {
        return '/';
    }

    // Normalize to a leading slash with no trailing slash.
    return `/${base.replace(/^\/+/, '').replace(/\/+$/, '')}`;
}
