import { ar, type TranslationKey } from '@/locales/ar';
import { en } from '@/locales/en';

/**
 * Frontend translation boundary.
 *
 * Arabic is the default language and English is fully supported; direction is
 * derived from the language code, never from a separate setting
 * (AI_DOCS/41_Internationalization_i18n.md §3, §4, §6).
 *
 * Adding an approved language means adding a locale file and one entry in the
 * registry below — no existing component, hook, or query key changes (§17, §20,
 * §24). No translation library is introduced; Version 1 needs two languages and
 * a documented fallback chain, not a dependency.
 */

export const DEFAULT_LOCALE = 'ar';

export type Locale = 'ar' | 'en';
export type Direction = 'rtl' | 'ltr';

interface LocaleDefinition {
    readonly direction: Direction;
    readonly messages: Record<TranslationKey, string>;
}

const locales: Record<Locale, LocaleDefinition> = {
    ar: { direction: 'rtl', messages: ar },
    en: { direction: 'ltr', messages: en },
};

export function isSupportedLocale(value: string): value is Locale {
    return Object.prototype.hasOwnProperty.call(locales, value);
}

/**
 * Resolves the active locale from the document element, which Laravel renders
 * from the server-side language decision, so client and server never disagree.
 */
export function activeLocale(): Locale {
    const lang = document.documentElement.lang.trim();

    return isSupportedLocale(lang) ? lang : DEFAULT_LOCALE;
}

export function directionFor(locale: Locale): Direction {
    return locales[locale].direction;
}

/**
 * Translates a key for the active locale.
 *
 * Fallback chain per §23: current language, then the default language (Arabic),
 * then the key itself as a last resort. A missing translation must never break
 * the interface or reveal implementation detail.
 */
export function t(key: TranslationKey, locale: Locale = activeLocale()): string {
    return locales[locale].messages[key] ?? locales[DEFAULT_LOCALE].messages[key] ?? key;
}

export type { TranslationKey };
