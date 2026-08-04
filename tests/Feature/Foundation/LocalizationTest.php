<?php

declare(strict_types=1);

namespace Tests\Feature\Foundation;

use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Language and direction foundation.
 *
 * Arabic is the default language and English is fully supported; direction is
 * derived from the language code rather than a separate setting
 * (AI_DOCS/41_Internationalization_i18n.md §3, §4, §6, §17).
 */
final class LocalizationTest extends TestCase
{
    #[Test]
    public function arabic_is_the_default_language(): void
    {
        $this->assertSame('ar', config('localization.default'));
        $this->assertSame('ar', config('app.locale'));
    }

    #[Test]
    public function missing_translations_fall_back_to_arabic(): void
    {
        $this->assertSame('ar', config('app.fallback_locale'));
    }

    #[Test]
    public function arabic_is_right_to_left_and_english_is_left_to_right(): void
    {
        $this->assertSame('rtl', config('localization.supported.ar.direction'));
        $this->assertSame('ltr', config('localization.supported.en.direction'));
    }

    #[Test]
    public function request_language_follows_the_browser_when_supported(): void
    {
        $this->withHeader('Accept-Language', 'en')->get('/up');

        $this->assertSame('en', App::getLocale());
    }

    #[Test]
    public function unsupported_browser_language_falls_back_to_arabic(): void
    {
        $this->withHeader('Accept-Language', 'fr-FR')->get('/up');

        $this->assertSame('ar', App::getLocale());
    }

    #[Test]
    public function every_error_code_has_a_message_in_every_supported_language(): void
    {
        /** @var array<string, array<string, string>> $supported */
        $supported = config('localization.supported');

        foreach (array_keys($supported) as $locale) {
            foreach (\App\Support\Api\ErrorCode::cases() as $code) {
                $this->assertNotSame(
                    $code->messageKey(),
                    trans($code->messageKey(), [], $locale),
                    "Missing {$locale} message for {$code->value}."
                );
            }
        }
    }
}
