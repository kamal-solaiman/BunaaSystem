# Foundation Changelog — Phase 42

Complete record of what the foundation created, modified, and deliberately did
not create, with the reason for each and the `AI_DOCS` section that required it.

**Baseline:** commit `4b7c205`, a repository containing only `AI_DOCS/` and
`.gitattributes`.
**Result at freeze:** **150 files added**, **0 deleted**, **0 renamed**.

Of those 150: 146 in the Phase 42 + audit commits, plus `eslint.config.js` and
the three freeze documents (`FOUNDATION_CHANGELOG.md`,
`FOUNDATION_DECISIONS.md`, `PROJECT_CONSTRAINTS.md`).

**Post-freeze:** exactly one file under `AI_DOCS/` has been modified —
`04_Project_Structure.md`, by explicit approval, to bring the canonical
structure map into line with the frozen foundation. See §7. No other
`AI_DOCS` file has been touched, and no source code changed with it.

Files were not written from memory. The Laravel 12 skeleton, the Laravel 12
framework, and Laravel Sanctum 4 were fetched from their official repositories,
and every deviation below was produced by diffing against those sources.

---

# 1. Files created

## 1.1 Application bootstrap and entry points

| File | Why it exists | AI_DOCS |
|---|---|---|
| `artisan` | Laravel CLI entry point. Unmodified from stock. | 04 §2, §10 |
| `bootstrap/app.php` | Wires routing, the `/api/v1` prefix, global middleware, and exception normalization. | 04 §2; 10 §5; 34 §26.1 |
| `bootstrap/providers.php` | Registers `AppServiceProvider` and `AuthServiceProvider`. | 11 §2 |
| `public/index.php` | Front controller; the only PHP file intended to receive HTTP traffic. | 04 §6 |
| `bootstrap/cache/.gitignore` | Keeps the generated bootstrap cache out of history. | 36 §18 |

## 1.2 HTTP layer

| File | Why it exists | AI_DOCS |
|---|---|---|
| `app/Http/Controllers/Controller.php` | Thin base controller. Controllers coordinate; they never own business rules. | 28 §3.2 |
| `app/Http/Middleware/AssignRequestId.php` | Assigns the correlation id behind the optional `request_id` envelope field. Runs first and globally, so an id exists even for a failure raised during routing. | 10 §6; 34 §25.2 |
| `app/Http/Middleware/ForceJsonResponse.php` | Forces `Accept: application/json` on the API surface so framework failures return the documented envelope instead of an HTML page or a login redirect. | 10 §2; 34 §26.1 |
| `app/Http/Middleware/SecurityHeaders.php` | Applies `X-Content-Type-Options`, `X-Frame-Options`, and `Referrer-Policy`. | 23 §14 |
| `app/Http/Middleware/SetRequestLocale.php` | Resolves request language: preference, then `Accept-Language`, then the Arabic default. Reads the supported set from configuration, never from `if/else` branches. | 41 §4, §6, §17 |

## 1.3 Support layer

| File | Why it exists | AI_DOCS |
|---|---|---|
| `app/Support/Api/ApiResponse.php` | The single place the success, pagination, and error envelopes are built, so no controller hand-assembles a response array. | 10 §6, §7, §10; 34 §26.1 |
| `app/Support/Api/ErrorCode.php` | Enum registering the foundation subset of the error registry, each case carrying its documented HTTP status. | 34 §3, §5–§7, §19, §22 |
| `app/Support/Http/ApiExceptionRenderer.php` | Normalizes every exception into the envelope and guarantees no stack trace, SQL, path, or framework internal reaches a client. | 34 §2, §26.1; 23 §18 |
| `app/Support/Http/RequestId.php` | Opaque correlation identifier stored on the request, not in static state, so it cannot leak between requests in a shared process. | 10 §6; 34 §25.2 |

## 1.4 Models, providers, database

| File | Why it exists | AI_DOCS |
|---|---|---|
| `app/Models/User.php` | The framework authentication contract only. No role, permission, workspace, or Archive attribute. | 08; 07 |
| `app/Providers/AppServiceProvider.php` | Strict model behavior and date handling. | 25; 28 §3.5 |
| `app/Providers/AuthServiceProvider.php` | Deny-by-default authorization boundary and the policy registration point. | 08; 09; 23 §2.1 |
| `database/migrations/0001_01_01_000000_create_users_table.php` | `users`, `sessions`, `password_reset_tokens`. Database sessions are a confirmed decision. | 06; D-040 |
| `database/migrations/0001_01_01_000002_create_jobs_table.php` | Database Queue tables. | 21; D-042 |
| `database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php` | Sanctum tokens, published from Sanctum 4. | 10 §3 |
| `database/factories/UserFactory.php` | Deterministic test data. | 04 §4, §9 |
| `database/seeders/DatabaseSeeder.php` | Seed composition root, deliberately empty. | 04 §4 |
| `database/.gitignore` | Keeps local SQLite artifacts out of history. | 36 §18 |

## 1.5 Configuration

`config/app.php`, `auth.php`, `cache.php`, `database.php`, `filesystems.php`,
`logging.php`, `mail.php`, `queue.php`, `sanctum.php`, `services.php`,
`session.php` — published so every confirmed driver decision is visible in the
repository rather than implied by framework defaults (35 §10.4; D-040…D-043).

| File | Why it exists | AI_DOCS |
|---|---|---|
| `config/localization.php` | **Project-authored.** Declares the supported languages, the Arabic default, and the direction derived from each language code. Adding a language is a configuration change, not a code change. | 41 §3, §4, §6, §17, §20 |

## 1.6 Routes

| File | Why it exists | AI_DOCS |
|---|---|---|
| `routes/api.php` | Declares the five `/api/v1` scope boundaries and the JSON fallback. Registers **zero endpoints**. | 10 §5; 28 §13.1 |
| `routes/web.php` | Serves the application shell and lets React Router own client-side navigation. | 04 §2; 12 §4 |
| `routes/console.php` | Scheduler registration point for the single cPanel Cron entry. | 21; 26 |

## 1.7 Views and backend translations

| File | Why it exists | AI_DOCS |
|---|---|---|
| `resources/views/app.blade.php` | The only server-rendered document. Carries the locale, direction, CSRF token, and base path — no data, no user context, no authorization state. | 04 §2; 12; 41 §6 |
| `resources/lang/ar/errors.php` · `resources/lang/en/errors.php` | Registry user messages, keyed by the stable error code. | 34 §24; 41 §10 |
| `resources/lang/ar/app.php` · `resources/lang/en/app.php` | Shell strings. | 41 §20 |

## 1.8 React application (all under `resources/js`)

| File | Why it exists | AI_DOCS |
|---|---|---|
| `app/main.tsx` | Browser entry point. | 12 §2 |
| `app/App.tsx` | Composes providers and the root boundary; owns no feature workflow. | 12 §2 |
| `app/AppErrorBoundary.tsx` | Contains unexpected failures without exposing request data. | 34 §26.2; 28 §5.8 |
| `routes/AppRouter.tsx` | Route composition grouped by access boundary. | 12 §4; 28 §5.6 |
| `routes/basename.ts` | Reads the base path from a meta tag so one build serves a domain root or a subdirectory. | 26 §7 |
| `lib/http.ts` | The single HTTP boundary. Exposes no `delete` helper. | 12 §9; 28 §5.7 |
| `lib/api-error.ts` | Normalizes every failure into one stable taxonomy. | 34 §26.2 |
| `lib/query-client.ts` | TanStack Query defaults; never auto-retries a rejected decision or a mutation. | 28 §5.4 |
| `lib/query-keys.ts` | Scope-first cache keys, so cached data cannot cross an access boundary. | 12 §8; 28 §5.3 |
| `locales/ar.ts` · `locales/en.ts` · `locales/index.ts` | Frontend translation boundary with the documented fallback chain. | 41 §7, §20, §23 |
| `config/env.ts` | Typed public configuration; API base defaults to a relative path. | 35 §11 |
| `types/api.ts` | Transport contracts only. | 10 §6–§10 |
| `styles/app.css` | Tailwind entry and semantic tokens. | 13; 04 §3 |
| `test/setup.ts` | Shared frontend test setup. | 04 §9 |
| `lib/api-error.test.ts` | 13 tests over the error taxonomy. | 24; 34 §26.2 |
| `vite-env.d.ts` | Types the `VITE_`-prefixed variables. | 35 §11.2 |

## 1.9 Build, tooling, quality gates

| File | Why it exists | AI_DOCS |
|---|---|---|
| `composer.json` | PHP 8.3 constraint, Sanctum, and the `lint`/`test` gates. | 04 §10; 35 §8.1 |
| `package.json` | React 19 stack and the frontend gates. | 04 §10; 35 §8.1 |
| `vite.config.ts` | Builds into Laravel's own `public/build`; no output outside Laravel. | 04 §6; 26 §7 |
| `tsconfig.json` | Strict TypeScript; `any` prohibited. | 28 §6.1, §6.6 |
| `eslint.config.js` | Enforces the React and TypeScript rules a type-checker cannot: Rules of Hooks, no `any`, no unused bindings. | 28 §5.1, §5.3, §6.6 |
| `tailwind.config.ts` | Semantic tokens; logical properties for RTL/LTR. | 13; 04 §3; 41 §6 |
| `postcss.config.js` | Tailwind and autoprefixer. | 04 §3 |
| `vitest.config.ts` | Frontend test runner. | 24 |
| `phpunit.xml` | Backend test runner. | 04 §10; 24 |
| `pint.json` | PSR-12 plus `declare(strict_types=1)`. | 28 §4 |
| `.editorconfig` · `.gitignore` | Editor conventions; excludes dependencies, secrets, build output, runtime data. | 04 §1; 36 §18 |
| `.env.example` | Non-secret template. Every credential key present and empty. | 35 §10.1 |

## 1.10 Deployment and structure

| File | Why it exists | AI_DOCS |
|---|---|---|
| `.htaccess` (application root) | Denies HTTP access to `app/`, `bootstrap/`, `config/`, `database/`, `resources/`, `routes/`, `storage/`, `tests/`, `vendor/`, and all dotfiles, then forwards remaining traffic into `public/`. Required because `public_html/113` places the application root inside the document root. | **26 §7** |
| `public/.htaccess` | Front-controller routing, security headers, long-lived caching for fingerprinted assets. | 04 §6; 23 §14 |
| `public/robots.txt` | `Disallow: /`. A private education platform is not offered for indexing. | 01; 23 |
| 44 × `.gitkeep` | Keeps the 20 backend features, 19 frontend features, and 5 shared frontend boundaries in version control. Each states what the directory owns and cites its AI_DOCS section. | 04 §2, §3 |
| 18 × `.gitignore` | Preserves runtime directories while excluding their contents. | 04 §5; 36 §18 |
| `README.md` | Repository entry point and non-secret setup overview. | 04 §1 |
| `FOUNDATION_AUDIT.md` | File-by-file audit record. | Governance |

---

# 2. Files modified

No pre-existing repository file was modified. Every file below was modified
**relative to its upstream source** during the foundation, or corrected during
the audit.

## 2.1 Corrected during the Foundation Audit

| File | Change | Reason |
|---|---|---|
| `config/filesystems.php` | `'links'` emptied | A storage symlink would expose files by URL and bypass the authorization every file request must pass (04 §5). |
| `routes/api.php` | Removed invented `/api/v1/session` | Only the five documented `auth/*` endpoints exist (10 §13); returning a user is also a later phase's work. |
| `resources/js/routes/AppRouter.tsx` | Literals replaced with `t()` | All UI text must be translatable (41 §7). |
| `resources/js/app/AppErrorBoundary.tsx` | Literals replaced with `t()` | 41 §7. |
| `resources/js/lib/http.ts` · `lib/api-error.ts` | Messages replaced with `t()` | All system messages must be translatable (41 §10). |
| `public/robots.txt` | `Disallow:` → `Disallow: /` | The stock value permits indexing a private platform. |
| `public/index.php` | Added `declare(strict_types=1)` | 28 §4.6; `pint.json` would otherwise fail. |
| `README.md` | Deployment section rewritten | It described a root `.htaccess` that did not exist, and assumed server-side Composer. |
| `.gitignore` | Clarified the `public/storage` entry | It implied a symlink is created; none is. |
| `resources/js/lib/api-error.ts` | Narrowed a header read through `unknown` | ESLint found an unsafe `any` the type-checker missed (28 §6.6). |
| `eslint.config.js` | **Created during freeze** | `npm run lint` was declared with no config and failed outright — a gate that could never run. |

## 2.2 Corrected during Phase 42 implementation

Five defects were found by running the application rather than reading it.

| File | Defect | Fix |
|---|---|---|
| `routes/web.php` | `^`/`$` anchors inside a route constraint are embedded in Symfony's compiled pattern, so the SPA route matched **nothing** — every deep link would 404. | Constraint changed to `(?!api/).*`. |
| `app/Support/Http/ApiExceptionRenderer.php` | Laravel's `prepareException()` runs **before** custom render callbacks, so the `AuthorizationException` and `ModelNotFoundException` branches were unreachable and fell through to 500. | Mapping keyed on prepared exception types. |
| `app/Support/Http/ApiExceptionRenderer.php` | A wrong HTTP verb (405) mapped to 500. | Mapped to `API_MALFORMED_REQUEST` (400). |
| `app/Support/Http/RequestId.php` | Static state leaked the correlation id between requests in a shared process. | Stored on the request. |
| `bootstrap/app.php` | The id was missing on failures raised during routing. | `AssignRequestId` prepended globally. |

---

# 3. Files intentionally NOT created

## 3.1 Forbidden by the brief

| Not created | Reason |
|---|---|
| `frontend/` | The project is one Laravel application. |
| `backend/` | Same. |
| `laravel_app/` | Same. |
| `deployment/` | No deployment package; the repository *is* the deployable unit. |
| `scripts/` | No deployment scripts. |
| `Dockerfile`, `docker-compose.yml`, `compose.yaml` | No Docker (03 §4.1). |
| Nginx configuration | cPanel provides Apache or LiteSpeed (26 §4.1). |
| Apache virtual hosts | Not a tenant of the application; `.htaccess` is sufficient. |
| Any symlink | Shared hosting frequently disallows them. |

`AI_DOCS/04_Project_Structure.md` §1 describes a repository with separate
`backend/` and `frontend/` roots. The brief supersedes it with a single
application. This is recorded as **D-F01** in `FOUNDATION_DECISIONS.md` and is
the one deliberate, approved divergence from that document.

## 3.2 Stock Laravel files deliberately dropped

| Not kept | Reason |
|---|---|
| `resources/views/welcome.blade.php` | Replaced by `app.blade.php`; the browser surface is React. |
| `resources/js/app.js`, `resources/js/bootstrap.js` | Replaced by the TypeScript entry point. |
| `resources/css/app.css` | Relocated to `resources/js/styles/app.css`, beside the code that imports it. |
| `vite.config.js` | Replaced by `vite.config.ts`; the project is TypeScript. |
| `tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php` | Placeholder tests assert nothing. |
| `database/migrations/0001_01_01_000001_create_cache_table.php` | Cache is **File**, not database (D-041). The table would never be used. |
| `public/favicon.ico` | Stock ships a 0-byte file; referencing it guarantees a broken request. |
| `laravel/sail` | A Docker helper (03 §4.1). |

## 3.3 Deferred to later phases, deliberately absent now

No API endpoint, controller, service, repository, policy, gate, job, console
command, form request, API resource, domain model, domain migration, seed data,
role, or permission exists. The brief required the foundation only, and
`AI_DOCS/29_Project_Decisions.md` keeps Q-005, Q-010, Q-011, Q-012, Q-013, and
the timezone/currency part of Q-015 **PENDING** — none is hardened here.

---

# 4. Deviations from stock Laravel

Every deviation below was produced by diffing against `laravel/laravel` 12.x.

## 4.1 Configuration defaults

| Setting | Stock | Ours | Why |
|---|---|---|---|
| `app.locale` | `en` | `ar` | Arabic is the default language (41 §3, §4). |
| `app.fallback_locale` | `en` | `ar` | Fallback is Arabic, not English (41 §23). |
| `cache.default` | `database` | `file` | File Cache confirmed (D-041). |
| `queue.default` | `database` | `database` | Unchanged; already correct (D-042). |
| `session.driver` | `database` | `database` | Unchanged; already correct (D-040). |
| `database.default` | `sqlite` | `mysql` | MySQL 8 is the system of record (26 §4.3). |
| `database…mysql.engine` | `null` | `InnoDB` | InnoDB confirmed (26 §4.3). |
| `filesystems.default` | `local` | `public` | Laravel Public Storage confirmed (D-043). |
| `filesystems.links` | maps `public/storage` | `[]` | No symlink; files served through an authorized controller (04 §5). |
| `mail.default` | `log` | `smtp` | SMTP transport baseline (35 §10.5). Transport only — notifications remain out of scope (D-012). |

## 4.2 Structural deviations

| Deviation | Why |
|---|---|
| `app/Features/` added | Feature-Based Architecture (04 §2; 11 §3). |
| `app/Support/` added | Shared value objects and API/HTTP conventions (04 §2). |
| `config/localization.php` added | Language set and direction as configuration (41 §17). |
| `resources/lang/{ar,en}/` added | Localization is approved (41 §20). |
| Application-root `.htaccess` added | Required when the app root is inside the document root (26 §7). |
| `public/.htaccess` extended | Security headers and asset caching (23 §14). |
| PHP constraint `^8.2` → `^8.3` | PHP 8.3 is the confirmed runtime (D-014). |
| `laravel/sanctum` added | Confirmed authentication (D-001). |
| `laravel/sail` removed | Docker helper. |
| `composer platform.php` pinned to `8.3.0` | Prevents resolving packages that need a newer runtime than production. |
| `declare(strict_types=1)` in all 47 PHP files | 28 §4.6. |
| `phpunit.xml`: added `APP_LOCALE`/`APP_FALLBACK_LOCALE=ar` | Tests must run under the real default language. |
| `DatabaseSeeder` emptied | Stock creates a `test@example.com` user; seeding must never quietly create accounts (04 §4). |
| API prefix `api` → `api/v1` | 10 §5. |
| `routes/api.php` added | Stock Laravel 12 ships no API routes file. |

---

# 5. Deviations from stock React

There is no "stock React" scaffold in Laravel 12 — it ships a plain
`resources/js/app.js`. The following records how the React application differs
from a default Vite React template.

| Deviation | Why |
|---|---|
| Lives in `resources/js`, not a separate project | Single Laravel application. |
| No `index.html` | Laravel renders the shell; Vite's HTML entry would bypass CSRF, locale, and base-path injection. |
| Build output to `public/build` | No build folder outside Laravel (04 §6). |
| Feature-first tree, not type-first | Feature-Based Architecture (12 §3). |
| `react-router` v8, not `react-router-dom` | `react-router-dom` is deprecated, and the version in range carried a CSRF advisory. Migration removed 7 vulnerabilities, 1 critical. |
| No global state library | TanStack Query owns server state; duplicating it in a store is prohibited (28 §5.4). |
| No i18n library | Two languages and a documented fallback do not justify a dependency (41 §23). |
| `strict` plus `noUncheckedIndexedAccess` and `exactOptionalPropertyTypes` | Beyond template defaults; `any` prohibited (28 §6.6). |
| Single HTTP boundary; no ad hoc `fetch` | 28 §5.7. |
| No `delete()` transport helper | Archive replaces deletion (28 §2.4). |
| Router basename resolved at runtime | Supports `public_html/113` without compiling a path into the bundle. |
| One class component (`AppErrorBoundary`) | React requires a class for error boundaries; the single documented exception to 28 §5.1. |

---

# 6. Verification

| Gate | Result |
|---|---|
| Runtime assertions against a booted Laravel 12.64 / PHP 8.3.32 | 120 passed, 0 failed |
| PHP syntax, all source files | 0 errors |
| `declare(strict_types=1)` | 47 / 47 tracked PHP files |
| `npm run lint` (ESLint) | 0 errors, 0 warnings |
| `npm run typecheck` | PASS |
| `npm test` (Vitest) | 13 passed |
| `npm run build` | PASS |
| `npm audit` | 0 vulnerabilities |
| Fresh-clone structural integrity | 20 backend + 19 frontend feature directories present |

---

# 7. Post-freeze documentation change

One change has been made since the `foundation-v1` tag. It is
**documentation-only**: no source code, configuration, test, or build file was
modified, and no behavior changed.

## 7.1 What changed and why

**File:** `AI_DOCS/04_Project_Structure.md` — the only `AI_DOCS` file modified.

**Why.** §1 of that document described a repository with separate `backend/`
and `frontend/` roots. The Phase 42 brief superseded that with a single Laravel
application, so the canonical structure map contradicted the frozen code. This
was recorded at freeze as decision **D-F01** and listed as the one known
documentation debt in `PROJECT_CONSTRAINTS.md` §3.10, deliberately left for
explicit approval rather than amended silently.

`PROJECT_CONSTRAINTS.md` §1.10 requires that structure and documentation never
drift apart. This change discharges that obligation: the map now matches the
territory.

## 7.2 Sections updated

Thirty path references across twelve sections were re-rooted from the
two-application layout to the single-application layout.

| Section | Change |
|---|---|
| §1 Root Directory Structure | Rewritten. Documents the single Laravel application, the real root tree, the prohibition on `frontend/`, `backend/`, `laravel_app/`, `deployment/`, and `scripts/`, and the deployment characteristics that follow (no symlink, no server-side Node.js, no Docker, no compiled base path). |
| §2 Backend Structure | Tree re-rooted from `backend/` to the repository root. |
| §3 Frontend Structure | Tree re-rooted from `frontend/src/` to `resources/js/`. Records that React lives inside the Laravel application, that there is no separate `index.html` because Laravel renders the shell, that build configuration sits at the application root, and adds the `locales/` boundary. |
| §4 Database Structure | Tree re-rooted to `database/`. |
| §5 Storage Structure | Tree re-rooted to `storage/`. `public/storage` documented as never created. |
| §6 Public Assets Structure | Document root corrected to `public/`. Records the application-root `.htaccess` for `public_html/113` and the absence of a storage symlink. |
| §7 Configuration Structure | Consolidated to one environment file, with server and browser values separated by the `VITE_` prefix rather than by directory. |
| §9 Testing Structure | Test trees re-rooted to `tests/` and `resources/js/`. |
| §10 Build & Deployment Files | Single root tree. Records that the repository is the deployable unit uploaded into `public_html/113`, and that the asset build never runs on the server. |
| §12 Feature-Based Organization | Ownership tree re-rooted to `app/Features/`, `app/Http/`, `tests/`, `resources/js/features/`. |
| §13 Shared Resources | Trees re-rooted to `app/` and `resources/js/`. |
| §14 Future Expansion Strategy | Removed the `deployment/` reference; localization row updated to the confirmed Arabic/English boundaries, with timezone and currency still PENDING (Q-015). |
| §15 Consistency Review | Two review rows updated to describe the single-application structure. |

## 7.3 What was deliberately NOT changed

- No requirement was changed.
- No architecture decision was changed.
- No business rule was changed.
- All 15 section headings and the Document Scope are byte-identical.
- Every rule sentence is intact, verified by keyword count against the
  pre-change file: Teacher Workspace isolation (6), one global Student account
  (3), Parent read-only (3), Flow A (12), Archive instead of permanent deletion
  (2), Audit Log (12), Image and PDF Homework (1).
- No other file under `AI_DOCS/` was touched.
- No source code was modified.

Every removed line was verified to be a path description or layout prose. The
diff contains no removed requirement, decision, or rule.

## 7.4 Synchronization confirmed

`AI_DOCS` is once again fully synchronized with the codebase. Verified against
the working tree:

| Check | Result |
|---|---|
| Every root directory §1 lists exists | 9 / 9 |
| Every root file §1, §7, §10 list exists | 12 / 12 (plus `artisan`, `.htaccess`) |
| Every `resources/js` boundary §3 lists exists | 14 / 14 |
| Forbidden directories absent | `frontend/`, `backend/`, `laravel_app/`, `deployment/`, `scripts/` — none present |
| `public/storage` absent, as §6 states | Confirmed |
| Remaining `backend/` or `frontend/` path references | 0 (the single mention is the prohibition itself) |

**Documentation debt at freeze: cleared.** `PROJECT_CONSTRAINTS.md` §3.10 no
longer has an outstanding item.
