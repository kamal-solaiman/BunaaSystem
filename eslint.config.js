import js from '@eslint/js';
import tseslint from 'typescript-eslint';
import reactHooks from 'eslint-plugin-react-hooks';
import reactRefresh from 'eslint-plugin-react-refresh';

/**
 * ESLint configuration.
 *
 * Enforces the React 19 and TypeScript rules that AI_DOCS/28_Coding_Standards.md
 * §5–§6 state but a type-checker alone cannot catch: the prohibition on `any`
 * (§6.6), the Rules of Hooks (§5.3), and functional-component discipline (§5.1).
 *
 * Scope is resources/js only — the browser application is the sole JavaScript
 * surface in this repository.
 */
export default tseslint.config(
    {
        ignores: [
            'public/build/**',
            'node_modules/**',
            'vendor/**',
            'storage/**',
            'bootstrap/cache/**',
        ],
    },

    js.configs.recommended,
    ...tseslint.configs.recommendedTypeChecked,

    {
        files: ['resources/js/**/*.{ts,tsx}'],

        languageOptions: {
            parserOptions: {
                projectService: true,
                tsconfigRootDir: import.meta.dirname,
            },
        },

        plugins: {
            'react-hooks': reactHooks,
            'react-refresh': reactRefresh,
        },

        rules: {
            ...reactHooks.configs.recommended.rules,

            // `any` is prohibited; `unknown` plus a type guard is the documented
            // alternative (28_Coding_Standards.md §6.6).
            '@typescript-eslint/no-explicit-any': 'error',

            // Unused code is a defect, not a warning. Leading underscore marks a
            // deliberately ignored binding.
            '@typescript-eslint/no-unused-vars': [
                'error',
                { argsIgnorePattern: '^_', varsIgnorePattern: '^_' },
            ],

            // Keep type-only imports out of the emitted bundle.
            '@typescript-eslint/consistent-type-imports': [
                'error',
                { prefer: 'type-imports', fixStyle: 'inline-type-imports' },
            ],

            'react-refresh/only-export-components': [
                'warn',
                { allowConstantExport: true },
            ],
        },
    },

    // Config files run in Node and are not part of the typed application graph.
    {
        files: ['*.config.{js,ts}', 'vite.config.ts', 'vitest.config.ts', 'tailwind.config.ts'],
        extends: [tseslint.configs.disableTypeChecked],
    },
);
