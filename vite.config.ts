import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { fileURLToPath, URL } from 'node:url';

/**
 * Vite build configuration.
 *
 * The React application is built into Laravel's own public/build directory, so
 * a release is a single Laravel application with no separate frontend project
 * and no build output living outside Laravel.
 *
 * Asset URLs are resolved by Laravel's @vite directive from the manifest, so
 * the bundle works unchanged when the application is served from a
 * subdirectory such as public_html/113. No absolute host or localhost origin is
 * baked into the build.
 */
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/styles/app.css', 'resources/js/app/main.tsx'],
            refresh: true,
        }),
        react(),
    ],

    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },

    build: {
        // Fingerprinted assets under public/build, served by Apache or LiteSpeed.
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: 'manifest.json',
        sourcemap: false,
        rollupOptions: {
            output: {
                // Keep the framework in its own long-lived chunk so feature
                // code can change without invalidating it.
                manualChunks: {
                    react: ['react', 'react-dom', 'react-router'],
                    query: ['@tanstack/react-query'],
                    forms: ['react-hook-form', 'zod', '@hookform/resolvers'],
                },
            },
        },
    },

    server: {
        watch: {
            ignored: ['**/storage/framework/views/**', '**/vendor/**'],
        },
    },
});
