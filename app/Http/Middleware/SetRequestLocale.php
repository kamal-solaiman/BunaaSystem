<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the request language.
 *
 * Arabic is the default language and English is fully supported
 * (AI_DOCS/41_Internationalization_i18n.md §3, §4). Detection order follows
 * §17: authenticated user preference first, then the browser Accept-Language
 * header, then the default language.
 *
 * The supported set is read from configuration rather than hard-coded branches,
 * so a future approved language is added by configuration and translation files
 * without modifying this class (§17, §20).
 *
 * Foundation note: no user language-preference column exists yet, so the
 * preference step is a documented extension point, not an assumed schema.
 */
final class SetRequestLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolve($request);

        App::setLocale($locale);

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('Content-Language', $locale);

        return $response;
    }

    private function resolve(Request $request): string
    {
        /** @var array<string, array<string, string>> $supported */
        $supported = config('localization.supported', []);
        /** @var string $default */
        $default = config('localization.default', 'ar');

        $preferred = $request->getPreferredLanguage(array_keys($supported));

        return is_string($preferred) && array_key_exists($preferred, $supported)
            ? $preferred
            : $default;
    }
}
