import { defineConfig } from 'vitest/config';
import react from '@vitejs/plugin-react';
import { fileURLToPath, URL } from 'node:url';

/**
 * Frontend test configuration.
 *
 * Feature tests live beside the feature they protect; shared setup lives in
 * resources/js/test (AI_DOCS/04_Project_Structure.md §9).
 */
export default defineConfig({
    plugins: [react()],

    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },

    test: {
        globals: true,
        environment: 'jsdom',
        setupFiles: ['./resources/js/test/setup.ts'],
        include: ['resources/js/**/*.{test,spec}.{ts,tsx}'],
        css: false,
    },
});
