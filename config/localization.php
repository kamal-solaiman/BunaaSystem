<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Localization
|--------------------------------------------------------------------------
|
| Arabic is the default language; English is fully supported
| (AI_DOCS/41_Internationalization_i18n.md §3, §4). Text direction is derived
| from the language code, never from a separate user setting (§6).
|
| Adding an approved language means adding an entry here plus its translation
| files — no existing code changes (§17, §20, §24).
|
*/

return [

    'default' => env('APP_LOCALE', 'ar'),

    // Missing translations fall back to the default language (§23).
    'fallback' => env('APP_FALLBACK_LOCALE', 'ar'),

    'supported' => [
        'ar' => [
            'name' => 'Arabic',
            'native_name' => 'العربية',
            'direction' => 'rtl',
        ],
        'en' => [
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
        ],
    ],

];
