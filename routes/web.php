<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| The browser surface is the React 19 application. Laravel serves the single
| application shell and lets React Router own client-side navigation, so a
| deep link or a refresh on any client route still resolves.
|
| This file must remain minimal and must never become an alternate,
| unprotected API surface (AI_DOCS/04_Project_Structure.md §2).
|
*/

Route::view('/', 'app')->name('app');

/*
| Catch-all for client-side routes.
|
| The constraint is embedded inside Symfony's own compiled pattern, so it must
| not carry its own ^ or $ anchors. Excluding the api prefix keeps an unknown
| /api/v1 request on the JSON fallback instead of returning the HTML shell.
*/
Route::view('/{path}', 'app')
    ->where('path', '(?!api/).*')
    ->name('app.spa');
