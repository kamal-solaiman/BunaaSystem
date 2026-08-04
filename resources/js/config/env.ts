/**
 * Typed public configuration.
 *
 * Only browser-safe values appear here. A secret, credential, storage path, or
 * authorization decision must never reach the bundle, because the bundle is
 * publicly readable (AI_DOCS/35_Environment_Configuration.md §11.2).
 *
 * The API base URL defaults to a relative path so the application works
 * unchanged when served from a subdirectory such as public_html/113, with no
 * localhost or absolute-host assumption.
 */

interface AppEnv {
    /** Base path of the versioned REST API. */
    readonly apiBaseUrl: string;
    /** True when running the Vite dev server. */
    readonly isDevelopment: boolean;
}

const rawApiBaseUrl = import.meta.env.VITE_API_BASE_URL ?? '/api/v1';

export const env: AppEnv = {
    // Trailing slashes are trimmed so request paths join predictably.
    apiBaseUrl: rawApiBaseUrl.replace(/\/+$/, ''),
    isDevelopment: import.meta.env.DEV,
};
