# Bunaa System

Unified education platform — a **single Laravel 12 application** with the React 19
browser application living inside `resources/js`.

`AI_DOCS/` is the only source of truth. Code follows the documents; the documents
are never inferred from the code.

---

## Stack

| Area | Version 1 standard |
|---|---|
| Backend | Laravel 12 / PHP 8.3 |
| Frontend | React 19 / TypeScript / Vite / Tailwind CSS |
| Database | MySQL 8 (InnoDB, `utf8mb4_unicode_ci`) |
| Authentication | Laravel Sanctum |
| Authorization | Laravel Gates & Policies with Custom RBAC |
| Cache / Queue / Sessions | File Cache / Database Queue / Database sessions |
| Storage | Laravel Public Storage |
| Scheduler | Laravel Scheduler via cPanel Cron |
| Hosting | cPanel Shared Hosting |

Version 1 requires no Docker, Redis, Kubernetes, S3, WebSockets, or microservices.

---

## Structure

```text
app/          Application code; app/Features/ groups work by business capability
bootstrap/    Application bootstrap and generated cache
config/       Configuration; no secrets
database/     Migrations, factories, seeders
public/       The only web-exposed directory; Vite output lands in public/build
resources/    js/ (React), views/ (application shell), lang/ (ar, en)
routes/       api.php (/api/v1), web.php (shell), console.php (schedule)
storage/      Runtime files, logs, framework cache
tests/        Feature/, Unit/, Support/
vendor/       Composer dependencies (installed, never committed)
```

There is no `frontend/`, `backend/`, `laravel_app/`, or `deployment/` directory —
the project is one Laravel application.

---

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

npm install
npm run dev      # Vite dev server
```

Production assets:

```bash
npm run build    # type-checks, then writes public/build
```

---

## Quality gates

```bash
composer lint          # Pint (PSR-12 + strict types)
composer test          # PHPUnit
npm run typecheck      # TypeScript strict mode
npm test               # Vitest
```

---

## Deployment (cPanel, `public_html/113`)

The application is uploaded as one directory. Because the application root sits
inside the document root, the `public/` directory is the only web-exposed part
and the root `.htaccess` denies direct access to `app/`, `config/`, `database/`,
`storage/`, `vendor/`, and `.env` (`AI_DOCS/26_Deployment_Plan.md` §7).

Prepare the release locally, so the server needs no build toolchain:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

Then:

1. Upload the project — including `vendor/` and `public/build/` — into
   `public_html/113`. Both are produced locally, so neither Composer nor Node is
   required on the server. If the host does provide Composer over SSH,
   `composer install --no-dev --optimize-autoloader` may be run there instead.
2. Create `.env` from `.env.example`. Set `APP_URL` to the deployed URL
   including the `/113` path, set `APP_ENV=production` and `APP_DEBUG=false`,
   and fill the database credentials.
3. Generate the application key. Over SSH: `php artisan key:generate`. Without
   SSH, generate it with the cPanel PHP CLI or paste a
   `base64:`-encoded 32-byte value into `APP_KEY`.
4. Run the migrations: `php artisan migrate --force`, or import the schema
   through phpMyAdmin if CLI access is unavailable.
5. Point a cPanel Cron Job at `php artisan schedule:run` once per minute.

`APP_URL` carries the base path, so the same build works at a domain root or in
a subdirectory — no path is hardcoded into the bundle, and no symlink is used:
`public/storage` is served through the application rather than a filesystem
link, which shared hosting often disallows.

---

## Conventions

- **Canonical terminology is mandatory**: Educational Grade (never Class),
  Lesson (never Course), Teacher Workspace (never tenant), Archive (never
  delete), Subscription for Flow A, payment status for Flow B.
- **Archive, never delete.** No code path may permanently remove a record.
- **The backend is the authority** for authentication, authorization, Teacher
  Workspace isolation, and business rules. Frontend guards are usability only.
- **Arabic is the default language**, English is fully supported, and direction
  is derived from the language code.

See `AI_DOCS/28_Coding_Standards.md` for the full standard.
