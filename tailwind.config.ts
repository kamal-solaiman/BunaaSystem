import type { Config } from 'tailwindcss';

/**
 * Tailwind configuration boundary.
 *
 * Defines semantic design tokens only. Colors are referenced by role
 * (surface, content, primary, danger) rather than by literal value, so a theme
 * change never requires editing feature components
 * (AI_DOCS/13_UI_UX_Guidelines.md; 04_Project_Structure.md §3).
 *
 * Arabic RTL and English LTR are handled with CSS logical properties
 * (ms-/me-/ps-/pe-, text-start/end) rather than a duplicate stylesheet, so the
 * same markup renders correctly in both directions
 * (41_Internationalization_i18n.md §6).
 */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{ts,tsx}',
    ],

    theme: {
        extend: {
            colors: {
                surface: 'rgb(var(--color-surface) / <alpha-value>)',
                'surface-raised': 'rgb(var(--color-surface-raised) / <alpha-value>)',
                'surface-sunken': 'rgb(var(--color-surface-sunken) / <alpha-value>)',
                content: 'rgb(var(--color-content) / <alpha-value>)',
                'content-muted': 'rgb(var(--color-content-muted) / <alpha-value>)',
                border: 'rgb(var(--color-border) / <alpha-value>)',
                primary: 'rgb(var(--color-primary) / <alpha-value>)',
                'primary-content': 'rgb(var(--color-primary-content) / <alpha-value>)',
                success: 'rgb(var(--color-success) / <alpha-value>)',
                warning: 'rgb(var(--color-warning) / <alpha-value>)',
                danger: 'rgb(var(--color-danger) / <alpha-value>)',
                info: 'rgb(var(--color-info) / <alpha-value>)',
            },

            fontFamily: {
                // A single stack serves both scripts so switching language does
                // not change typographic rhythm.
                sans: ['"Noto Sans Arabic"', '"Inter"', 'system-ui', 'sans-serif'],
            },

            borderRadius: {
                card: '0.75rem',
            },
        },
    },

    plugins: [],
} satisfies Config;
