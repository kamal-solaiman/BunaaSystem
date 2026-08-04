/// <reference types="vite/client" />

/**
 * Typed Vite environment variables.
 *
 * Only browser-safe public values may be declared here
 * (AI_DOCS/35_Environment_Configuration.md §11).
 */
interface ImportMetaEnv {
    readonly VITE_API_BASE_URL?: string;
}

interface ImportMeta {
    readonly env: ImportMetaEnv;
}
