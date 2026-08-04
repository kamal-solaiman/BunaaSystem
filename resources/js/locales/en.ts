import type { TranslationKey } from '@/locales/ar';

/**
 * English locale resources.
 *
 * Typed against the Arabic key set, so a key added to the default language
 * without an English translation is a compile-time error rather than a string
 * that silently falls back at runtime
 * (AI_DOCS/41_Internationalization_i18n.md §3, §23).
 */
export const en: Record<TranslationKey, string> = {
    'common.loading': 'Loading…',
    'common.not_found': 'Not found.',
    'common.reload': 'Reload',

    'error.unexpected.title': 'Something went wrong',
    'error.unexpected.body': 'An unexpected error occurred. Please reload the page and try again.',
    'error.request_failed': 'The request could not be completed.',
    'error.unexpected_response': 'The server returned an unexpected response.',
    'error.request_cancelled': 'Request cancelled.',
};
