@php
    /**
     * Application shell.
     *
     * The only server-rendered document in the project. It mounts the React 19
     * application and carries nothing else — no data, no user context, no
     * authorization state. Every piece of data is fetched by the browser
     * application through the authenticated /api/v1 REST API
     * (AI_DOCS/04_Project_Structure.md §2; 12_Frontend_Architecture.md).
     *
     * Direction is derived from the active language, so Arabic renders RTL and
     * English renders LTR with no separate setting
     * (41_Internationalization_i18n.md §6).
     */
    $locale = app()->getLocale();
    $direction = config("localization.supported.{$locale}.direction", 'rtl');

    /**
     * Base path the application is served from.
     *
     * Empty at a domain root, or a subdirectory such as "113" when deployed to
     * public_html/113. React Router reads this so one build works in both cases
     * without any hardcoded path.
     */
    $basePath = trim(parse_url((string) config('app.url'), PHP_URL_PATH) ?? '', '/');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $direction }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-base" content="{{ $basePath }}">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ config('app.name') }}</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">

    @viteReactRefresh
    @vite(['resources/js/styles/app.css', 'resources/js/app/main.tsx'])
</head>
<body class="min-h-screen bg-surface text-content antialiased">
    <div id="app"></div>
    <noscript>{{ __('app.javascript_required') }}</noscript>
</body>
</html>
