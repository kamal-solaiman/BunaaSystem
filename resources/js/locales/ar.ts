/**
 * Arabic locale resources — the default language.
 *
 * Keys are English technical identifiers; only values are translated
 * (AI_DOCS/41_Internationalization_i18n.md §21, §22). Keys are grouped by
 * feature and scope rather than by page, so a feature owns its own strings.
 *
 * Foundation phase: only application-shell strings exist. Feature strings are
 * added by their own phases.
 */
export const ar = {
    'common.loading': 'جارٍ التحميل…',
    'common.not_found': 'غير موجود.',
    'common.reload': 'إعادة التحميل',

    'error.unexpected.title': 'حدث خطأ ما',
    'error.unexpected.body': 'حدث خطأ غير متوقع. يرجى إعادة تحميل الصفحة والمحاولة مرة أخرى.',
    'error.request_failed': 'تعذّر إكمال الطلب.',
    'error.unexpected_response': 'أرسل الخادم استجابة غير متوقعة.',
    'error.request_cancelled': 'تم إلغاء الطلب.',
} as const;

/** The key set every locale must provide. */
export type TranslationKey = keyof typeof ar;
