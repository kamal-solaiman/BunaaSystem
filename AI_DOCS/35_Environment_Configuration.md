# 35 — Environment Configuration

## Document Scope

This document defines the official environment configuration standards for Version 1 of the Unified Education Platform. It standardizes how **Development**, **Testing**, **Staging (Future)**, and **Production** environments are configured for required software, PHP, Laravel, React, MySQL 8, storage, queue, scheduler, cache, mail, file permissions, logging, debug behavior, and security, while remaining fully compatible with the confirmed Version 1 primary deployment target: **cPanel Shared Hosting**.

This document is the single consolidation point for environment configuration **values and standards**. It gathers requirements that are distributed across governing documents into one reference. It invents nothing: where a configuration requirement is already defined elsewhere, that document remains the source of record and is cited.

This document does **not** contain deployment scripts, shell commands, Cron command lines, source code, Form Requests, CI definitions, infrastructure-as-code, database tables, SQL, or API definitions, and it does **not** expose secrets, real credentials, hostnames, domains, or IP addresses. All values are described as named variables with non-secret placeholder meanings.

The baseline is the confirmed Version 1 stack defined in `03_System_Architecture.md` §4.1 and `29_Project_Decisions.md` D-001, D-014 through D-016, D-040 through D-044: **Laravel 12** with **PHP 8.3**, **React 19** with **TypeScript**, **Vite**, and **Tailwind CSS**, **MySQL 8**, **Laravel Sanctum**, **Laravel Gates & Policies with Custom RBAC**, **File Cache**, **Database Queue**, **Database session driver**, **Laravel Public Storage**, **Laravel Scheduler with Cron Jobs**, **SMTP** transport baseline, **Apache or LiteSpeed**, on **cPanel Shared Hosting**, with **VPS / Cloud** as the confirmed future target. Version 1 must not require Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices in any environment.

Every standard in this document must preserve in every environment: Teacher Workspace isolation (BR-003), one global Student account (BR-001) with duplicate prevention (BR-022), one Group per Student per Teacher (BR-002), Parent linked-Student read-only access (BR-004) with exactly one Parent per Student in V1 (BR-020), Archive instead of permanent deletion (BR-005), immutable and permanently retained Audit Log (BR-006), Flow A / Flow B separation (BR-008, BR-009, BR-015, BR-019), and the five-role model (Super Admin, Teacher, Teacher Staff, Student, Parent). PENDING items Q-005, Q-010, Q-011, Q-012, Q-013, Q-015 are never resolved by configuration.

`AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if any conflict is found. Deployment process ownership (build, release, migration, rollback, backup, monitoring) remains with `26_Deployment_Plan.md`; this document owns configuration values.

**Authoritative sources:** `00_Project_Context.md`, `03_System_Architecture.md`, `04_Project_Structure.md`, `11_Backend_Architecture.md`, `12_Frontend_Architecture.md`, `20_File_Storage.md`, `21_Background_Jobs.md`, `23_Security_Standards.md`, `24_Testing_Strategy.md`, `25_Performance_Scalability.md`, `26_Deployment_Plan.md`, `27_Development_Roadmap.md`, `28_Coding_Standards.md`, `29_Project_Decisions.md`, `30_Project_Glossary.md`, `31_Master_Index.md`.

---

# 1. Document Purpose

The purpose of this document is to provide one authoritative reference for every environment configuration question for the Unified Education Platform.

It answers:

- Which environments exist, their purpose, and how isolation is enforced between them.
- Which software versions must run in every environment, optimized for Laravel 12, React 19, and MySQL 8.
- Which PHP configuration values apply and how they differ per environment within cPanel Shared Hosting constraints.
- Which Laravel environment variables exist, their purpose, and the mandatory per-environment values, including fixed driver decisions for session, cache, queue, and storage.
- Which React environment variables exist, that they are browser-safe only, and how they are handled at build time.
- How database, storage, queue, scheduler, cache, mail, file permissions, logging, debug, and security are configured per environment.
- How secrets are managed so that no credential is committed, logged, bundled in the frontend, or exposed in an error response.
- How to verify environments through a consolidated checklist and how to maintain them over time.
- How the configuration model evolves to the Future Cloud Environment without redesign.

The document is optimized for Laravel 12, React 19, and MySQL 8, deployed primarily on cPanel Shared Hosting, with future VPS / Cloud considered.

Explicitly out of scope:

- Deployment procedures, release steps, rollback execution, and backup execution — owned by `26_Deployment_Plan.md`.
- Deployment scripts, Cron command contents, server configuration file contents, or executable instructions — prohibited by this document's commission.
- Real credentials, domains, hostnames, or secret values — only placeholder meanings appear.
- Source code, business rules, validation rules, and error codes — owned by `00`, `02`, `06`, `07`, `08`, `09`, `10`, `32`, `33`, `34`.

---

# 2. Environment Philosophy

The environment model follows these principles consolidated from governing documents:

1. **One codebase, environment-specific configuration.** The same Laravel 12 / React 19 application runs everywhere. Differences — credentials, URLs, debug posture — live only in deployment-managed environment files, never in committed source (`26_Deployment_Plan.md` §8.1; `04_Project_Structure.md` §7; `28_Coding_Standards.md` §3.14).

2. **Secrets exist only in deployment-managed environment files.** Laravel `.env` is never committed. `.env.example` documents names with non-secret placeholders. Database credentials, `APP_KEY`, mail credentials must never appear in source, committed config, build artifacts, or operational logs (`23_Security_Standards.md` §16.2; `26_Deployment_Plan.md` §8.1).

3. **Parity with cPanel Shared Hosting.** Every environment runs PHP 8.3, MySQL 8, File Cache, Database Queue, Database session driver, Laravel Public Storage, Apache or LiteSpeed. No environment substitutes Redis, external queues, Docker, S3, or alternative databases, because parity makes staging and testing results trustworthy (`24_Testing_Strategy.md` §19.2; D-040 through D-044).

4. **Strict isolation.** Each environment has its own database instance, its own `.env`, its own storage paths. Production data never moves to lower environments without sanitization. Test data is clearly identifiable and separable from real user data (`26_Deployment_Plan.md` §3.3; `24_Testing_Strategy.md` §19.3).

5. **Configuration must never weaken invariants.** In every environment: Teacher Workspace isolation (BR-003), Archive over deletion (BR-005), Audit Log immutability (BR-006), Flow A / Flow B separation, five-role model. No variable may enable payment gateways, notifications, marketplace, video homework, or other exclusions (`19_Notification_System.md`).

6. **Production-safe defaults.** Every value defaults toward production safety: debug off, HTTPS enforced, least privilege, generic errors. Development conveniences are localized to Development only.

7. **Future-proof without overbuilding.** Configuration must not block migration to VPS / Cloud (`26_Deployment_Plan.md` §24-§25) but Version 1 never requires future infrastructure (D-044).

8. **PENDING stays PENDING.** Q-005 (non-payment enforcement), Q-010 (Lesson video hosting/protection), Q-011 (Teacher Staff permission granularity), Q-012 (Super Admin content visibility), Q-013 (flat vs tier pricing), Q-015 (timezone/currency) are never hardcoded or resolved by configuration.

---

# 3. Supported Environments

The Platform operates four environment classes. Local, Staging, Production are CONFIRMED by D-045. Testing is the dedicated automated-test context defined by `24_Testing_Strategy.md` §19.1.

| Environment | Purpose | Hosting / Location | Identifier (`APP_ENV`) |
|---|---|---|---|
| **Development** | Developer feature implementation and developer-level testing. | Local machine with PHP 8.3, MySQL 8, Composer, Node.js/npm, compatible web server — local cPanel-compatible stack. | `local` |
| **Testing** | Automated backend Feature/Unit and frontend integration suites (developer machines and CI). | Same stack as Development; dedicated test database. | `testing` (Laravel-standard) |
| **Staging (Future)** | Pre-release validation, regression testing, UAT; mirrors Production. | cPanel Shared Hosting, separate account or subdomain. Provisioned and refreshed per roadmap milestones DE1-DE10. | `staging` |
| **Production** | Live Platform for five confirmed roles. | cPanel Shared Hosting with confirmed stack. | `production` (mandatory) |

Standards across all environments:

- **Isolation mandatory:** own database instance, own `.env`, own `APP_KEY`, own storage paths per `26_Deployment_Plan.md` §3.3. Staging must not share database with Production `§23.2`. Production data never copied down without sanitization. Test data clearly identifiable.
- **Stack parity mandatory:** MySQL 8, Database Queue, File Cache, Database session driver, Laravel Public Storage, no Docker/Redis/Kubernetes/S3/WebSockets/Microservices `24_Testing_Strategy.md` §19.2.
- **Variables only:** Staging mirrors Production except environment-specific values (database credentials, `APP_DEBUG`, `APP_ENV`) `26_Deployment_Plan.md` §23.2. Same principle for Development and Testing.
- **No testing in Production:** Production serves live users only; no seeding or test data permitted there `24_Testing_Strategy.md` §19.1.

---

# 4. Development Environment

The Development environment is a developer's local machine for feature implementation and developer-level testing `24_Testing_Strategy.md` §19.1; `26_Deployment_Plan.md` §3.3.

## 4.1 Purpose and Shape

| Concern | Standard |
|---|---|
| Purpose | Feature implementation, developer testing. |
| Location | Developer's local machine. |
| Runtime | PHP 8.3 with extensions in §8.2, MySQL 8, Composer, Node.js/npm, Git, compatible local web server. |
| Environment identifier | `APP_ENV=local` |
| Debug posture | `APP_DEBUG=true` locally is standard; detailed error output is a local convenience confined to this environment. |
| Data | Factories and seeders produce clearly identifiable test data; never production data. |
| HTTPS | Not required locally; Production requirement is HTTPS. |

## 4.2 Required Local Tooling

- PHP 8.3 with extensions listed in §8.2 must be verified.
- Composer for PHP dependencies, managed on server and locally `26_Deployment_Plan.md` §4.1, §9.1.
- MySQL 8 local instance with own database.
- Node.js and npm for installing frontend dependencies and running Vite dev server and production build. The official documents do not fix Node version; selected version must support Vite build for React 19. No Node.js runtime required on cPanel servers — frontend build is produced locally or in CI and deployed as static assets `26_Deployment_Plan.md` §9.1.
- Git for version control `§18.2`.

## 4.3 Development Configuration Standards

1. Developer creates local `.env` from non-secret `.env.example`; local `.env` never committed `04_Project_Structure.md` §7.
2. Unique local `APP_KEY` generated per developer environment; never shared.
3. All framework drivers use Version 1 fixed values even locally: `SESSION_DRIVER=database` (D-040), `CACHE_STORE=file` (D-041), `QUEUE_CONNECTION=database` (D-042), `FILESYSTEM_DISK=public` (D-043), `MAIL_MAILER=smtp` transport baseline. Development must not substitute Redis or in-memory drivers — preserves parity `24_Testing_Strategy.md` §19.2.
4. `VITE_API_BASE_URL` points to developer's local backend.
5. Must be able to run full automated test suites against dedicated Testing configuration in §5.
6. Committed configuration holds safe defaults and variable names only; secrets exist only in deployment-managed files.

---

# 5. Testing Environment

The Testing environment is the dedicated context where automated tests execute — backend Feature/Unit and frontend integration — on developer machines and CI `24_Testing_Strategy.md` §19.1.

## 5.1 Purpose and Shape

| Concern | Standard |
|---|---|
| Purpose | Execute full automated suites for every code change. |
| Stack | Same as Development: Laravel 12, PHP 8.3, MySQL 8; local cPanel-compatible. |
| Identifier | `testing` — Laravel-standard automated test context. Official docs name `local`/`staging`/`production`; this document adopts `testing` for completeness without creating a fifth class. |
| Database | Dedicated MySQL 8 test database instance, separate from all others `§19.3`. |
| Drivers | Database Queue, File Cache, Database session driver — identical to Production `§19.2`. |
| Data | Factories/seeders; clearly identifiable; never production data. |
| Debug | Disabled so error handling matches production-shaped responses; tests verify standardized error structure. |
| No Docker/Redis | Constraint applies equally here. |

## 5.2 Testing Configuration Standards

1. **Dedicated configuration:** Testing uses its own environment configuration kept separate from Development `.env`, via Laravel-standard testing environment file mechanism. Credentials in non-committed files `24_Testing_Strategy.md` §19.3.
2. **Parity constraints identical to Production:**
   - MySQL 8 only — no in-memory SQLite or alternative substitutes.
   - Database Queue — no Redis, SQS, Beanstalkd.
   - File Cache — no Redis/Memcached.
   - Database session driver.
   - No Docker, Kubernetes, S3, WebSockets, Microservices.
3. **Persistence realism:** Feature and integration tests use real database, not mocked persistence, to verify Teacher Workspace isolation (BR-003), Archive (BR-005), Audit Log transactional guarantees (BR-006), and multi-step workflows.
4. **Isolation:** Test data clearly separable; production data never copied without sanitization.
5. **What runs:** Full backend Feature/Unit and frontend integration suites per code change. UAT belongs to Staging, not here `§16.4`.

---

# 6. Staging Environment (Future)

**Reconciliation note:** The environment class Staging is CONFIRMED — D-045 confirms three environments (Local, Staging, Production), `26_Deployment_Plan.md` §3.3, §23.2 defines Staging on cPanel, and `27_Development_Roadmap.md` deploys and refreshes it at every phase boundary (DE2-DE9). The "(Future)" qualifier records provisioning timing, not decision status: Staging exists to be validated before the single Version 1 production release `§17.1`. This document does not downgrade D-045.

## 6.1 Purpose and Shape

| Concern | Standard |
|---|---|
| Purpose | Pre-release validation, regression, User Acceptance Testing (UAT). |
| Hosting | cPanel Shared Hosting — separate account or subdomain mirroring Production `26_Deployment_Plan.md` §3.3. |
| Domain | Separate domain or subdomain (staging subdomain of production). |
| Identifier | `APP_ENV=staging` `§8.2`. |
| Config rule | Mirrors Production except environment-specific values (DB credentials, `APP_DEBUG`, `APP_ENV`) `§23.2`. |
| Data | Dedicated test data; production data never used without sanitization. |
| Lifecycle | Deployed after Phase 1 (DE2), updated at each phase boundary DE2-DE9, fully validated before Production (DE9-DE10) `27_Development_Roadmap.md`. |
| HTTPS | HTTPS with own certificate covering staging domain, because staging validates real browser flows. |

## 6.2 Staging Configuration Standards

1. **Mirror of Production:** Identical stack and framework drivers. `APP_ENV=staging`; `APP_DEBUG` may differ during active diagnosis but must be `false` for release-candidate validation because release acceptance requires mirroring production `27_Development_Roadmap.md` §13.3; `24_Testing_Strategy.md` §16.4.
2. **Complete isolation:** Own database instance, own `.env`, own storage paths, own `APP_KEY`; must not share database with Production `§3.3`.
3. **Migrations proven first:** Migrations tested in Staging before Production `26_Deployment_Plan.md` §12.2.
4. **UAT home:** UAT must be performed in Staging environment that mirrors production configuration without production data or credentials `24_Testing_Strategy.md` §16.4.
5. **No notification leakage:** Staging mail uses staging-specific credentials and sender identity. It does not enable Version 1 email notification behavior, which remains out of scope `§17; 19_Notification_System.md`.
6. **Security parity:** Same security headers, HTTPS enforcement, and least-privilege database user posture as Production.

---

# 7. Production Environment

The Production environment is the live Platform serving Super Admin, Teacher, Teacher Staff, Student, Parent on cPanel Shared Hosting `26_Deployment_Plan.md` §3.1.

## 7.1 Target Baseline

| Concern | Production Standard |
|---|---|
| Hosting type | cPanel Shared Hosting |
| Web server | Apache 2.4+ or LiteSpeed |
| PHP processor | PHP-FPM or CGI/FastCGI (cPanel-provided) |
| PHP | 8.3 (mandatory) |
| Database | MySQL 8 |
| SSL | Required (cPanel-provided or custom certificate) |
| Cron Jobs | cPanel Cron Jobs for Laravel Scheduler |
| File storage | Laravel Public Storage |
| Cache | File Cache |
| Queue | Database Queue |
| Session driver | Database |
| Mail transport | SMTP transport baseline only — no V1 email notifications (D-012) |
| Environment identifier | `APP_ENV=production` mandatory |
| Debug posture | `APP_DEBUG=false` mandatory absolute — stack traces never exposed |
| Domain | Single production domain; `APP_URL` uses HTTPS |

## 7.2 Topology and Document Root

1. cPanel web document root maps to Laravel `public/` directory. Compiled Vite frontend assets deployed into `public/build/` `26_Deployment_Plan.md` §7; `04_Project_Structure.md` §6.
2. Laravel application root sits **above** document root so that `.env`, `vendor/`, `storage/`, `database/` are not directly accessible through web server. Only `public/` exposed to HTTP `26_Deployment_Plan.md` §7; `23_Security_Standards.md` §21.10.
3. If hosting model forces application root inside `public_html/`, web-server access rules must deny HTTP access to sensitive directories (`app/`, `config/`, `database/`, `storage/`, `vendor/`) and to `.env`. No server configuration contents are defined here; policy only.
4. Node.js toolchain is **not** installed or required on Production server: frontend build performed locally or CI and transferred as compiled fingerprinted static assets `§9.1, §9.3`.

## 7.3 Production Configuration Standards

1. `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` uses HTTPS with single production domain `§8.2, §22.1, §23.1`.
2. `SANCTUM_STATEFUL_DOMAINS` includes production domain; CORS (if applicable) restricts origins to production domain `§8.2, §23.1`.
3. Laravel production caches built after every deployment that changes configuration, routes, views, events: configuration caching, route caching, view caching, event caching `§11.2`. Generated caches live under `bootstrap/cache/` and `storage/framework/`, never committed.
4. `APP_KEY` generated once per environment and never changes after; changing it invalidates encrypted data, sessions, tokens `§11.3`.
5. Cron Job for Scheduler and Cron-triggered queue worker configured as standardized in §14-§15 `§11.1`.
6. Database user holds minimum required privileges only §12; `§4.3; §11.3`.
7. File permissions follow §18; storage symlink or cPanel-equivalent mapping follows §13.
8. Production serves single domain; must not use multiple domains for same environment `§23.4`.

---

# 8. Required Software Versions

Every environment runs the same confirmed baseline consolidated from `26_Deployment_Plan.md` §4, `03_System_Architecture.md` §4.1, `29_Project_Decisions.md` D-001, D-014-D-016.

## 8.1 Core Versions

| Software | Required Version | Where | Source |
|---|---|---|---|
| PHP | **8.3** | All environments (web, CLI, Cron, queue worker) | D-014; `26_Deployment_Plan.md` §4.1 |
| Laravel | **12** | All environments — optimized for PHP 8.3, MySQL 8, File Cache, Database Queue, Database sessions | D-001; `03_System_Architecture.md` §4.1 |
| MySQL | **8.0+** — InnoDB engine, utf8mb4 | All environments, each with own instance | `26_Deployment_Plan.md` §4.1, §4.3 |
| Web Server | **Apache 2.4+** or **LiteSpeed** | All hosted environments (cPanel-provided) | `§4.1` |
| PHP Processor | PHP-FPM or CGI/FastCGI as provided by cPanel | Hosted | `§3.1` |
| React | **19** with **TypeScript**, **Vite**, **Tailwind CSS** | Browser application; built locally or CI | D-001, D-015, D-016 |
| React Router | For client-side routing | Frontend | D-017 |
| TanStack Query | Server-state management | Frontend | D-018 |
| React Hook Form + Zod | Forms and validation | Frontend | D-019 |
| Composer | Required for PHP dependencies | Development, Testing, CI, hosted (backend deps installed on server) | `§4.1, §9.1` |
| Node.js + npm | Required for frontend build; no fixed version in official docs — must support Vite build for React 19 | Development, Testing, CI only — **never required on cPanel Production/Staging** | `§4.1, §9.1` |
| Git | Repository version control | Development, CI | `§18.2` |
| SSH Access | Recommended for deployment and artisan commands; not strictly required | Hosted | `§4.1` |
| SSL Certificate | Required in Production (and Staging for validation) | Hosted | `§22` |

Laravel 12 optimization notes:
- Laravel 12 is the modular monolith framework owning business logic, authentication via Sanctum, authorization via Gates & Policies + Custom RBAC, tenant scoping, validation, Audit Log recording, reporting orchestration, file access control, QR Attendance, Exam Engine.
- React 19 uses functional components, TypeScript strict mode, Vite for fast HMR and fingerprinted production builds, Tailwind for utility-first styling.
- MySQL 8 provides full-text search where needed, JSON support, utf8mb4_unicode_ci collation.

## 8.2 Required PHP Extensions

Verified on every environment before use; missing extensions enabled via cPanel PHP Extension Manager or requested from provider `26_Deployment_Plan.md` §4.2.

| Extension | Purpose |
|---|---|
| `openssl` | Encryption, Sanctum, HTTPS |
| `pdo` | Database abstraction |
| `pdo_mysql` | MySQL 8 connectivity |
| `mbstring` | Multibyte handling (Laravel required) |
| `tokenizer` | PHP token parsing (Laravel required) |
| `xml` | XML processing (Laravel required) |
| `ctype` | Character type checking (Laravel required) |
| `json` | JSON encode/decode (Laravel required) |
| `bcmath` | Arbitrary precision math (Laravel required) |
| `fileinfo` | File type detection for upload validation |
| `gd` or `imagick` | Image processing for Homework Image files and QR Code generation |
| `curl` | HTTP client (SMTP, external communication where applicable) |
| `zip` | Archive handling |
| `exif` | Image metadata extraction |
| `intl` | Future localization support (Q-015 PENDING) |

## 8.3 Version Discipline

1. Version parity mandatory: feature developed, tested, staged, released against same PHP 8.3 / Laravel 12 / MySQL 8 / React 19 baseline `24_Testing_Strategy.md` §19.
2. No environment may introduce infrastructure outside baseline (no Docker, Redis, Kubernetes, S3, WebSockets, Microservices) `03_System_Architecture.md` §4.1.
3. `intl` prepares future only; Q-015 remains PENDING and no localization config is defined.

---

# 9. PHP Configuration

PHP 8.3 configuration applies in every environment, with resource limits tuned to cPanel Shared Hosting constraints and error-display governed by `APP_DEBUG` posture (§20).

## 9.1 Resource Limits

| Setting | Standard (all environments) | Applies To |
|---|---|---|
| Memory limit | 256 MB minimum; 512 MB recommended | Web, CLI, Cron, queue worker |
| Max execution time web | 60 seconds minimum | Web requests |
| Max execution time CLI/Cron | 300 seconds minimum | Scheduler, queue worker Cron runs |
| Post max size | Sized as required by file upload behavior (Teacher Homework, Lesson videos, Student Homework Image/PDF). No numeric file-size limit confirmed for V1 — any future limit must be separately approved; no limit fabricated `20_File_Storage.md` §12 | All environments |
| Upload max filesize | Same rule as post max size above — no confirmed V1 limit invented | All environments |
| Character encoding | UTF-8 (project files UTF-8 without BOM per `28_Coding_Standards.md` §4.1) | All environments |
| Max input vars | Sufficient for complex forms but not hardened here — relies on host default; governance via validation rules | All environments |

## 9.2 PHP Configuration Standards

1. **Extension verification:** All extensions in §8.2 enabled and verified before environment use.
2. **CLI parity:** CLI PHP used by Cron must be PHP 8.3 with same extensions as web PHP, because scheduled and queued work executes full Laravel 12 stack §14-§15.
3. **Opcode caching:** Official documents do not require specific OPcache config; Platform must operate correctly whether or not host provides opcode caching. No behavior may depend on it.
4. **Session PHP settings:** Governed at framework level by Database session driver and cookie security flags §10, §21, not by PHP file-session settings; Database driver mandatory everywhere D-040.
5. **Error display must agree with APP_DEBUG:** Production never displays errors and never exposes stack traces `23_Security_Standards.md` §18.4. PHP-level display_errors must be off when `APP_DEBUG=false`.
6. **Resource respect:** Must respect shared-hosting limits; long-running work chunked or batched rather than pushing limits `§15.2; 25_Performance_Scalability.md` §1-§2.
7. **File upload handling:** Large files validated early to prevent resource exhaustion; processing respects memory limits.

---

# 10. Laravel Environment Variables

Laravel environment variables are the single mechanism for backend environment-specific configuration. Required set consolidated from `26_Deployment_Plan.md` §8.2.

## 10.1 Management Rules (all environments)

1. Environment-specific values and secrets exist only in deployment-managed `.env` files; `.env` never committed `§8.1`.
2. `.env.example` documents required names with non-secret placeholder values `§8.1`. This document mirrors that: no real values appear below — placeholder meaning column explains purpose without disclosing any credential.
3. Configuration files in `config/` contain no environment-specific values or secrets; all secrets referenced through env variables `28_Coding_Standards.md` §3.14.
4. Sensitive values must not appear in source, committed config, build artifacts, or operational logs `§8.1; §16.2`.
5. Configuration caching in Production bakes env values into optimized caches; caches regenerated whenever config changes `§11.2`. Generated artifacts never committed `04_Project_Structure.md` §7.
6. `APP_KEY` generated uniquely per environment and never changes after initial generation `§8.2, §11.3`.
7. No variable may enable payment gateways, notification features, marketplace behavior, or other V1 exclusions; config values must not create out-of-scope features `§3.14; 19_Notification_System.md`.

## 10.2 Application Identity Variables

| Variable | Purpose | Development | Testing | Staging (Future) | Production |
|---|---|---|---|---|---|
| `APP_NAME` | Display name — non-secret | Platform name placeholder | Platform name placeholder | Platform name placeholder | Platform name placeholder |
| `APP_ENV` | Environment identifier | `local` | `testing` | `staging` | `production` mandatory |
| `APP_KEY` | Encryption key — never committed/logged | Unique local key placeholder meaning | Unique test key placeholder | Unique staging key placeholder | Unique production key; never changed after generation |
| `APP_DEBUG` | Debug flag | `true` local convenience | Debug disabled | Environment-specific; `false` for release-candidate validation | `false` mandatory absolute |
| `APP_URL` | Base URL | Local URL placeholder | Test URL placeholder | HTTPS staging domain placeholder | HTTPS production domain placeholder mandatory `§22.1` |

## 10.3 Database Variables

| Variable | Purpose | Development | Testing | Staging | Production |
|---|---|---|---|---|---|
| `DB_CONNECTION` | Driver | `mysql` | `mysql` | `mysql` | `mysql` V1 driver |
| `DB_HOST` | Host | Local MySQL host placeholder | Test MySQL host placeholder | Staging cPanel MySQL hostname placeholder | Production cPanel MySQL hostname placeholder |
| `DB_PORT` | Port | 3306 | 3306 | 3306 | 3306 |
| `DB_DATABASE` | Database name — distinct instance per env | Local dev DB placeholder | Dedicated test DB placeholder | Staging DB placeholder (never shared with Production) | Production DB placeholder |
| `DB_USERNAME` | User — least privilege only §12.2 | Local user placeholder | Test user placeholder | Staging user placeholder | Production user with minimal privileges; no DROP, ALTER DATABASE, GRANT `§4.3` |
| `DB_PASSWORD` | Password — never committed/logged | Local secret placeholder | Test secret placeholder | Staging secret placeholder | Production secret placeholder |

## 10.4 Framework Driver Variables — Fixed by Confirmed Decisions

These values are fixed everywhere — uniformity preserves parity `24_Testing_Strategy.md` §19.2:

| Variable | Value All Environments | Confirmed By |
|---|---|---|
| `SESSION_DRIVER` | `database` — Database session driver stores sessions in MySQL 8 | D-040; `§8.2` |
| `CACHE_STORE` | `file` — File Cache compatible with cPanel | D-041; `§8.2` |
| `QUEUE_CONNECTION` | `database` — Database Queue, no Redis/SQS | D-042; `§8.2` |
| `FILESYSTEM_DISK` | `public` — Laravel Public Storage | D-043; `§8.2` |
| `MAIL_MAILER` | `smtp` transport baseline only — does NOT enable notifications | Baseline; `§8.2`; D-012 guards scope |

These driver choices optimize for Laravel 12 on cPanel: no daemon required, no external service, compatible with shared hosting file-system and MySQL-backed queue.

## 10.5 Mail Variables

| Variable | Purpose | Value Rule (every environment) |
|---|---|---|
| `MAIL_MAILER` | Transport | `smtp` for V1 |
| `MAIL_HOST` | SMTP host | cPanel-provided or configured SMTP server for that environment — placeholder meaning |
| `MAIL_PORT` | SMTP port | Standard SMTP port placeholder |
| `MAIL_USERNAME` | SMTP username | Env-specific; never committed/logged — placeholder |
| `MAIL_PASSWORD` | SMTP password | Env-specific; never committed/logged — placeholder |
| `MAIL_ENCRYPTION` | Encryption | `tls` recommended `§8.2` |
| `MAIL_FROM_ADDRESS` | Sender address | Configured per environment placeholder |

Scope guard: these configure transport availability only. They do not authorize or create V1 email notification §17; `19_Notification_System.md` §4. SMTP in baseline does not equal notification feature.

## 10.6 Authentication Domain Variable

| Variable | Purpose | Development | Testing | Staging | Production |
|---|---|---|---|---|---|
| `SANCTUM_STATEFUL_DOMAINS` | Domains trusted for Sanctum stateful (cookie-based) auth | Local domains placeholder | Test domains placeholder | Staging domain placeholder | Must include production domain placeholder `§8.2, §23.1` |

CORS configuration (if applicable) restricts allowed origins to environment's own domain.

## 10.7 Deliberately Undefined Variables (V1)

| Area | Status |
|---|---|
| Timezone, locale/language, currency config | Arabic (default), English (fully supported), and automatic RTL/LTR are CONFIRMED; per-Teacher timezone and currency remain **PENDING Q-015**. |
| Redis, SQS, Beanstalkd, S3, external cache/session/queue/storage endpoints | Not part of V1 (D-040…D-044); adding requires future infrastructure approvals in §23. |
| Mail notification templates, notification channels, SMS/push providers | Out of scope V1 (D-012; `19_Notification_System.md`). |
| Payment gateway credentials | Out of scope V1 — payments external, status-only (BR-019, D-002). |
| Teaching Subject change, multiple subjects per account | Not configurable — one Teaching Subject per account immutable after creation BR-016. |
| Marketplace or course discovery flags | Not configurable — marketplace behavior excluded BR-018, D-050. |

---

# 11. React Environment Variables

React environment variables configure browser application at build time. They are Vite environment variables and carry only browser-safe public values `26_Deployment_Plan.md` §8.3; `12_Frontend_Architecture.md`; `04_Project_Structure.md` §7.

## 11.1 Variable Set — Optimized for React 19 + Vite

| Variable | Purpose | Development | Testing | Staging (Future) | Production |
|---|---|---|---|---|---|
| `VITE_API_BASE_URL` | Backend API base URL used by frontend. Browser-safe public value only. | Local backend URL placeholder (e.g., local API endpoint) | Test backend URL placeholder | HTTPS staging backend URL placeholder | HTTPS production backend URL placeholder — must point to production backend API endpoint; HTTPS mandatory `§10.2`; uses `/api/v1` prefix per `10_API_Design.md` |

This is complete confirmed Vite variable set for V1 `§8.3`. Any future addition must remain browser-safe and be approved through documentation governance.

React 19 specific considerations:
- Vite produces fingerprinted static assets for deployment to `backend/public/build/` for Apache/LiteSpeed serving.
- TypeScript strict mode enabled; `any` type prohibited per coding standards.
- TanStack Query query keys include every access-defining context (role, Teacher Workspace, linked Student, Teacher relationship, resource identity, list criteria).
- React Hook Form + Zod provide client-side validation; backend validation remains authoritative.
- Tailwind CSS semantic tokens defined in configuration boundary.

## 11.2 React Environment Variable Standards

1. **`VITE_` prefix mandatory.** Vite exposes only `VITE_`-prefixed variables to browser bundle `§8.3`.
2. **Browser-safe values only.** No secret, credential, private storage path, authorization decision, or server-only config may be in Vite env file or frontend build `§8.3; 12_Frontend_Architecture.md; 04_Project_Structure.md` §7.
3. **Build-time semantics.** Vite variables compiled into static bundle when frontend build runs locally or in CI `§9.1`. Changing value requires new build; no runtime injection on cPanel. Compiled assets excluded from source commits `§9.3; §6`.
4. **Template discipline.** Frontend `.env.example` documents names with non-secret placeholders; environment-specific frontend env files not committed `§7`.
5. **No trust boundary.** Variable presence in browser never conveys authority: every data and file access still passes through backend's authenticated and authorized REST API `§10.3; §4.4`.
6. **Frontend secrets prohibition absolute** because bundle publicly readable: nothing in frontend env may duplicate Laravel-side secrets (`APP_KEY`, database values, mail credentials, storage credentials) `§7`.

---

# 12. Database Configuration

MySQL 8 is persistence layer for every environment: application data, sessions, queue jobs, failed jobs, rate-limit state (via cache), Audit Log.

## 12.1 Server and Schema Standards (every environment)

| Concern | Standard | Source |
|---|---|---|
| Engine | InnoDB (default MySQL 8) | `§4.3` |
| Character set | `utf8mb4` | `§4.3` |
| Collation | `utf8mb4_unicode_ci` | `§4.3` |
| Driver value | `DB_CONNECTION=mysql` | `§8.2` |
| Port | Default MySQL 8 port 3306 | `§8.2` |
| Query cache | Not required — MySQL 8 deprecates query cache; application-level File Cache used instead | `§4.3` |
| Max connections | Sufficient for concurrent web requests, queue processing, Scheduler execution — tuned per environment | `§4.3` |
| Connection encryption | Encrypted connections where supported by hosting environment | `23_Security_Standards.md` §11.3 |
| Version-specific optimization | MySQL 8 supports window functions, CTEs, JSON functions used by reporting where approved; no MySQL 5 compatibility mode | Laravel 12 + MySQL 8 optimization |

Database configuration optimized for Laravel 12 + MySQL 8:
- Eloquent ORM uses parameterized queries; prevents SQL injection.
- Migrations use Laravel 12 conventions.
- Factories produce deterministic test data representing valid scope contexts without production data.

## 12.2 Database User Privileges

| Environment | Privilege Posture |
|---|---|
| Development / Testing | Local/test users with privileges needed for migration and test workflow; no production credentials appear here. |
| Staging (Future) | Minimum privileges mirroring Production: `CREATE`, `SELECT`, `INSERT`, `UPDATE`, `DELETE`, `INDEX`, `ALTER`, `CREATE TEMPORARY TABLES`, `LOCK TABLES` `§4.3`. |
| Production | Minimum required privileges only — list above. Must **not** have `DROP`, `ALTER DATABASE`, or `GRANT` in Production `§4.3; §11.3`. |

Database credentials live only in environment variables, never in source or version control `§11.3`.

## 12.3 Instances Per Environment

1. Each environment has its own database instance `§3.3; §19.3`: Development (local), Testing (dedicated test DB), Staging (separate from Production — sharing prohibited `§23.2`), Production.
2. Production data never copied to lower environments without sanitization `§3.3; §19.3`.
3. Teacher Workspace isolation (BR-003) is data-layer invariant in every environment's database; configuration must never create schema, user, or path crossing Teacher Workspace boundaries `23_Security_Standards.md` §5.

## 12.4 What Lives in the Database

Configuration must account for single MySQL 8 instance per environment hosting more than application tables `§18.1; §4`:

- All application data (Teacher Workspace records, Students, Parents, Parent Student Links, Educational Grades, Groups, Enrollments, Attendance Sessions, Attendance, Homework, Homework Submissions, Lessons, Lesson Videos, Question Banks, Questions, Exams, Exam Attempts, Exam Answers, File Attachments, Subscriptions (Flow A), payment-status records (Flow B), Archive state, Platform Settings, Billing Cycles).
- **Session data** (Database session driver D-040) — table cleanup required.
- **Queue jobs and failed jobs** (Database Queue D-042) — processed jobs cleaned weekly.
- **Audit Log entries** — append-only, immutable, permanently retained; stored in database, not file logs `§21.3`.
- **Cache rate-limit state** via File Cache backed by file system, but rate limiting may use cache store.

Maintenance implications (queue table cleanup, session table cleanup) are scheduled tasks standardized in §15.

## 12.5 Migration Management (configuration posture)

1. Migrations are version-controlled schema changes applied during deployment using Laravel's artisan commands `§12.1`. This document does not define command contents; only policy.
2. Migrations tested in Staging before Production execution `§12.2`.
3. Seeders run only where approved reference data required — never in Production without explicit approval `§12.3`.
4. Migrations must not permanently delete data and must preserve Teacher Workspace isolation, Archive state, Audit Log integrity, historical relationships `§12.2`.
5. Migrations must be backward-compatible where possible to support rollback without data loss.
6. Idempotency where possible so re-running does not create duplicate state.

---

# 13. Storage Configuration

Version 1 uses **Laravel Public Storage** for all file storage (`FILESYSTEM_DISK=public`, D-043). S3 not required for V1 `§13.3; 20_File_Storage.md`.

## 13.1 Storage Layout (every environment — ownership-oriented, not access-control mechanism)

Logical organization from `04_Project_Structure.md` §5 and `26_Deployment_Plan.md` §13.2:

| Path (logical) | Contents | Scope Rule |
|---|---|---|
| `storage/app/public/teacher-workspaces/lessons/` | Private Teacher-owned Lesson video files / references | Teacher Workspace owned, private to owning Teacher's Students and authorized Teacher-side users (BR-018) |
| `storage/app/public/teacher-workspaces/homework/` | Permitted Teacher-provided Homework attachments (Text/Image/PDF) | Teacher Workspace owned, format-restricted BR-021 |
| `storage/app/public/teacher-workspaces/files/` | Other authorized Teacher Workspace file resources | Teacher Workspace scoped |
| `storage/app/public/student-homework/` | Student Homework Image/PDF submission files | Assigned Homework, valid Teacher relationship, Image/PDF only |
| `storage/framework/` | Framework cache and session artifacts | Internal |
| `storage/framework/cache/` | Framework cache data | Internal, File Cache root |
| `storage/framework/sessions/` | Session fallback if file sessions temporarily used (but Database driver is standard) | Internal |
| `storage/framework/views/` | Compiled Blade views | Internal |
| `storage/logs/` | Operational logs §19 | Internal |

## 13.2 Storage Configuration Standards

1. **Public-storage mapping:** Each hosted environment creates symbolic link from `public/storage/` to `storage/app/public/` or configures equivalent cPanel mapping `§13.1; §6`.
2. **Writability:** Storage directories writable by web server process (permissions in §18) `§13.1`.
3. **Per-environment isolation:** Each environment has own storage paths; nothing shared between Development, Testing, Staging, Production `§3.3`.
4. **Authorization is application-level always:** Paths, filenames, directory structures are **not** authorization proofs; every file request passes through backend authorization and ownership checks — Teacher Workspace scope, Student relationship, Parent linked-Student scope, Archive state, resource ownership `§13.3; 20_File_Storage.md` §10; `23_Security_Standards.md` §9.3. This holds in every environment, and files remain private by business rule even where Laravel's public-storage convention or server mapping is used `04_Project_Structure.md` §5.
5. **PENDING protection model:** Lesson video hosting/protection details remain **PENDING Q-010**: no streaming, download, public-URL, signed-URL, format, transcoding, quota, preview, watermarking, or cloud-video config defined, and storage configuration must not resolve that decision prematurely `§5; 20_File_Storage.md`.
6. **No upload size invention:** Storage configuration does not introduce file-size quotas; no numeric size limit confirmed for V1 `§12; §9.1`.
7. **Git exclusion:** Runtime storage, logs, framework cache, sessions, generated file links excluded from version control `§5`.
8. **Denied contexts:** Parent uploads denied; video homework denied; S3 not required.

---

# 14. Queue Configuration

Version 1 uses **Laravel Database Queue** driver (`QUEUE_CONNECTION=database`, D-042). Jobs stored in environment's MySQL 8 database and processed by Cron-triggered worker. No Redis, SQS, Beanstalkd anywhere `§15.1; 21_Background_Jobs.md` §4.

## 14.1 Queue Baseline — Optimized for Laravel 12 on cPanel Shared Hosting

| Concern | Standard (every environment) |
|---|---|
| Queue driver | Database — jobs in MySQL 8 database |
| Worker trigger | Laravel Scheduler / cPanel Cron Jobs (hosted); manual or Scheduler-triggered locally |
| Persistent daemon | None — worker must not require persistent daemon process per cPanel limits `21_Background_Jobs.md` §4.3 |
| External dependencies | None — no Redis, SQS, Beanstalkd |
| Hosting compatibility | cPanel Shared Hosting |
| Future VPS trigger | Supervisor-managed worker replaces Cron-triggered in future VPS model §23 |

Laravel 12 optimization for queue:
- Batchable jobs pattern for long-running tasks.
- Idempotency mandatory — retry must not create duplicate records `21_Background_Jobs.md` §13.3.
- Scoped query keys and workspace isolation preserved inside jobs.

## 14.2 Queue Names — Priority and Domain Separation

Logical queue names separate work by priority and domain `§15.3; §4.2`:

| Queue Name | Purpose | Priority | Business Rule Link |
|---|---|---|---|
| `default` | General background work | Medium | — |
| `billing` | Flow A Subscription and Billing Cycle processing | High | BR-008, BR-015, D-006, D-007 |
| `grading` | Exam automatic grading and Bubble Sheet processing | High | BR-011, BR-012 |
| `reports` | Deferred report preparation (Attendance, Homework, exam results, payments, Student performance) | Low | BR-014 historical reports |
| `cleanup` | File reference cleanup and maintenance (not binary deletion) | Low | BR-005 Archive policy |
| `audit-support` | Non-critical Audit Log enrichment (not mandatory Audit Log creation) | Medium | BR-006 |

## 14.3 Retry and Failure Configuration

Per-environment constants configured identically everywhere `21_Background_Jobs.md` §13.1:

| Job Category | Retry Attempts | Backoff Strategy | Failure Handling |
|---|---|---|---|
| Billing Cycle / Subscription (Flow A) | 3 attempts | Exponential backoff | Failed jobs remain in failed jobs table; Super Admin or authorized operator reviews and manually retries via Laravel built-in failed job management |
| Automatic Exam grading (including Bubble Sheet) | 3 attempts | Exponential backoff | Same as above; grading backlog prevented via 5-min schedule |
| Report preparation | 2 attempts | Linear backoff | Report generation deferred; user may request again |
| File reference integrity check | 1 attempt | Fixed delay | Integrity verified weekly; failures logged operationally |
| Audit Log verification | 1 attempt | Fixed delay | Integrity verified monthly; immutable log never purged |
| Attendance cleanup (Expired QR Context Cleanup) | 2 attempts | Linear backoff | Daily cleanup |

Failure handling policy `§13.2, §14`:
- Failed jobs that exhaust retries remain in failed jobs table of environment's database.
- Version 1 sends no push, email, or SMS notifications for job failures `§14.3`; queue config must not introduce failure notifications.
- Retrying a job must not create duplicate records — idempotency mandatory.
- Mandatory business actions must not be considered complete if required persistence or Audit Log recording failed.

## 14.4 Queue Configuration Constraints

1. Worker execution must respect cPanel process execution time limits; long-running jobs chunked or use batchable pattern `§15.2; §4.3`.
2. Worker must not consume resources degrading user-facing request performance `§15.2`.
3. Processed jobs cleaned up periodically so queue table does not grow excessively — weekly Queue Table Maintenance scheduled task §15.
4. Queue jobs must preserve Teacher Workspace scope and authorization context `§15.4; §3`.
5. Must not introduce notifications, payment processing, WebSockets, microservice behavior `§15.4`.
6. Testing parity: Database Queue used in Development and Testing — tests dispatch and run real queued jobs against test database `§19.2`.

---

# 15. Scheduler Configuration

Version 1 uses **Laravel Scheduler triggered by Cron Jobs** on cPanel Shared Hosting. Single Cron Job entry invokes Laravel Scheduler at one-minute interval; Scheduler itself dispatches each scheduled task according to its defined schedule `§16.1; §5.2`.

## 15.1 Cron Model

| Concern | Standard |
|---|---|
| Trigger mechanism | One cPanel Cron Job entry that invokes Laravel Scheduler every minute. Content described at policy level only; no literal command line reproduced per commission (scripts prohibited). Structure defined in `21_Background_Jobs.md` §5.2 for implementation reference only. |
| Entry contents | References project path for that cPanel deployment and contains **no production credentials or secrets** `§5.2`. |
| Overlap protection | Only one Scheduler instance runs at a time; overlapping runs prevented `§16.3; §5.2`. |
| Environments | Registered for hosted environments (Staging, Production). In Development/Testing, developers and tests invoke Scheduler and its tasks explicitly; no machine-wide Cron assumption. |
| CLI PHP | Must be PHP 8.3 with full extension set and higher CLI execution-time floor §9.1-§9.2. |

## 15.2 Scheduled Tasks (all hosted environments)

Confirmed scheduled task set `§16.2; §5.1`:

| Task | Schedule | Description | Business Rule |
|---|---|---|---|
| Billing Cycle Initialization | First day of each calendar month | Starts new Billing Cycle and prepares Subscription records | BR-008, D-006 |
| Billable Student Calculation | After Billing Cycle initialization, then periodically | Calculates Billable Students per Teacher based on Enrollment duration >15 days; Attendance and login NOT used | BR-008, D-007 |
| Subscription Snapshot Generation | Last day of each calendar month | Generates immutable Subscription snapshot for completed Billing Cycle — never mutates; corrections are adjustment records | BR-015, D-003 proposed mechanics |
| Expired QR Context Cleanup | Daily | Cleans up expired Dynamic QR Code Attendance contexts | BR-010, `16_QR_Attendance_System.md` |
| Exam Auto-Grading Queue Processing | Every 5 minutes | Processes pending automatic grading jobs (Multiple Choice, True/False, Bubble Sheet) | BR-011 |
| Deferred Report Processing | Every 15 minutes | Processes queued report preparation jobs | `18_Reporting_Analytics.md` |
| File Reference Integrity Check | Weekly | Verifies file reference consistency; no binary deletion — preserves historical references | BR-005, `20_File_Storage.md` |
| Audit Log Retention Verification | Monthly | Verifies Audit Log integrity — append-only, immutable, permanent | BR-006, `00_Project_Context.md` §10 |
| Queue Table Maintenance | Weekly | Cleans up processed job records to prevent unbounded growth §14.4 | D-042 |

## 15.3 Scheduler Configuration Constraints

1. Scheduled tasks must preserve Teacher Workspace isolation `§16.3`.
2. Must not hard delete data — cleanup follows Archive and retention policy everywhere BR-005 `§16.3`.
3. Must not send Version 1 notifications `§16.3; D-012; 19_Notification_System.md`.
4. CLI PHP that runs Scheduler is PHP 8.3 with full extension set and higher CLI execution-time floor §9.1-§9.2.
5. Scheduler configuration part of environment provisioning: no environment considered ready until Cron Job configured and verified `§11.1; §22 checklist; roadmap DE1`.
6. Scheduled tasks respect shared-hosting resource limits; long-running aggregation chunked.
7. PENDING enforcement not implemented as scheduled behavior — Q-005 non-payment enforcement remains PENDING.

---

# 16. Cache Configuration

Version 1 uses **File Cache** as official cache driver in every environment (`CACHE_STORE=file`, D-041). No Redis, Memcached, or external cache service required or permitted `§17.1; §8.1`.

## 16.1 Cache Baseline — Optimized for Laravel 12 + cPanel

| Concern | Standard (every environment) |
|---|---|
| Cache driver | File Cache — files in `storage/framework/` area §13.1 |
| Storage location | Environment's `storage/framework/cache/` — writable per §18 |
| External dependencies | None — no Redis, Memcached, external cache service |
| Hosting compatibility | cPanel Shared Hosting |
| Future consideration | Redis may be considered after VPS/Cloud migration `25_Performance_Scalability.md` §8.6 but not V1 requirement |

## 16.2 What Cache Holds

Cache for slowly changing, scope-safe data `§17.2; §8.2`:

- Frequently accessed, slowly changing reference data (Educational Grade lists, Group lists, Teaching Subject list) — Teacher Workspace scoped.
- Pricing configuration (Super Admin owned) — Platform scoped.
- Dashboard summary data — scoped per role/Teacher Workspace/Student.
- Report aggregation results — scoped per Teacher Workspace.
- Search filter options — scoped per Teacher Workspace.
- Rate limiting state — File Cache backs Laravel's rate limiting `23_Security_Standards.md` §14.4; `25_Performance_Scalability.md` §7.3.

## 16.3 Cache Configuration Standards

1. **Scope-aware entries:** Teacher Workspace cache entries scoped to specific Teacher Workspace; Student cache entries scoped to Student's own account; Platform-level cache entries (e.g., pricing) shared across authorized Super Admin requests only `§17.3`. This preserves Teacher Workspace isolation in cache layer.
2. **Invalidation on change:** Cache invalidated when underlying data changes; event-driven invalidation used where framework supports it. Stale cache data must not violate business rules `§17.4`.
3. **No sensitive data:** File Cache must not store sensitive data such as passwords, tokens, credentials `§17.5; §8.5`.
4. **No authorization bypass:** Cache must not bypass authorization; cached data served only to authorized users within scope `§17.5; §8.5`.
5. **Bounded content:** Must not hold unbounded result sets; pagination used for large collections `§8.5`.
6. **File-system awareness:** File Cache performance depends on file-system I/O; excessive cache fragmentation avoided `§8.5`.
7. **Framework optimization caches:** In Production (and Staging when validating release), Laravel's configuration, route, view, event caches built after every relevant deployment §7.3; `§11.2`. These generated caches live under `bootstrap/cache/` and `storage/framework/`, never committed `§7`, and refreshed whenever env config changes. In Development, these caches normally left off so code/config changes take effect immediately.

Laravel 12 specific: config cache, route cache, view cache, event cache are built via built-in optimization capabilities. This improves performance on cPanel without requiring external cache.

---

# 17. Mail Configuration

Version 1 configures **SMTP as mail-transport baseline** (`MAIL_MAILER=smtp`). This is transport availability only: Version 1 sends **no** push, email, or SMS notifications (D-012; `19_Notification_System.md`).

## 17.1 Mail Baseline — Same in Every Environment

| Concern | Standard (every environment) |
|---|---|
| Mail transport | SMTP (`MAIL_MAILER=smtp`) |
| SMTP host/port | cPanel-provided or configured SMTP server; standard SMTP port — placeholder meaning only |
| Encryption | `MAIL_ENCRYPTION=tls` recommended |
| Credentials | `MAIL_USERNAME` / `MAIL_PASSWORD` per environment; never committed or logged — placeholder |
| Sender identity | `MAIL_FROM_ADDRESS` configured per environment — placeholder |
| Future cloud potential | Managed SMTP or SES may be considered future but not V1 required |

Full per-environment matrix for these variables appears in §10.5.

## 17.2 Mail Configuration Rules

1. **No notification feature unlocked by configuration:** SMTP availability does not authorize or create any Version 1 email notification — there are no notification routes, entities, settings, templates, jobs, schedules, or UI surfaces in any environment `19_Notification_System.md` §2, §4. Config values must not create out-of-scope notification features `28_Coding_Standards.md` §3.14.
2. **Per-environment identity and credentials:** Development and Testing use non-production sender identities; Staging uses own; Production uses production sender identity `§8.2`. Where Development/Testing captures rather than sends mail, it does so with same variable names and non-secret placeholder meaning — no new variables invented.
3. **Credentials protection:** Mail credentials must not appear in source, committed config, build artifacts, or operational logs `§8.1; §16.2`.
4. **Background-job silence:** Failed jobs do not produce email alerts in V1 `§14.3`; mail config must not wire failure notifications.
5. **Future mail behavior** (any notification capability) requires separately approved future scope `19_Notification_System.md` §2, and would then be configured under that future decision — never by editing V1 variables.

---

# 18. File Permissions

File and directory permissions protect sensitive files while allowing web server process to operate. Standards consolidated from `26_Deployment_Plan.md` §14.

## 18.1 Directory Permissions (hosted environments — cPanel Shared Hosting)

| Directory | Required Permission | Purpose |
|---|---|---|
| `storage/` | 755 or 775 (writable by web server) | Application storage root |
| `storage/app/` | 755 or 775 | Application file storage |
| `storage/app/public/` | 755 or 775 | Laravel Public Storage root — Teacher Workspace owned files, Student Homework submissions |
| `storage/framework/` | 755 or 775 | Framework cache, sessions, views |
| `storage/framework/cache/` | 755 or 775 | File Cache data — must be writable for Laravel 12 File Cache |
| `storage/framework/sessions/` | 755 or 775 | Database session fallback if file sessions temporarily used (Database driver is standard) |
| `storage/framework/views/` | 755 or 775 | Compiled Blade views |
| `storage/logs/` | 755 or 775 | Operational logs §19 |
| `bootstrap/cache/` | 755 or 775 | Bootstrap and cached configuration — Laravel 12 optimization caches |

Local Development uses same model with developer's own user as process owner; permissions may differ but writability requirement same.

## 18.2 File Permissions

| File | Required Permission | Purpose |
|---|---|---|
| `.env` | 600 or 640 (owner-readable only) | Environment configuration containing secrets — must never be world-readable, never accessible via HTTP |
| `artisan` | 755 (executable) | Laravel CLI entry point for Scheduler, queue worker, migrations |

## 18.3 Permission Constraints (every hosted environment)

1. `.env` must not be world-readable `§14.3` and must be placed outside web root / never accessible via HTTP `23_Security_Standards.md` §21.10; `§7`. Application root above document root protects it.
2. Storage directories writable by web server but not world-writable where hosting supports stricter permissions `§14.3`.
3. Uploaded files inherit storage directory's permission model `§14.3`.
4. File permissions must not expose sensitive application files to HTTP access `§14.3`; combined with document-root topology §7.2, this keeps `app/`, `config/`, `database/`, `storage/`, `vendor/`, `.env` unreachable from browser.
5. Permission setup part of environment readiness: `storage/` and `bootstrap/cache/` writability verified at deployment and re-verified during maintenance `§11.1; §24`.

---

# 19. Logging Configuration

Two distinct logging concerns exist and must never be conflated: **operational logs** (file-based, troubleshooting-oriented) and **Audit Log** (database-resident business audit trail owned by `00_Project_Context.md` §10).

## 19.1 Operational Logging (every environment)

| Concern | Standard |
|---|---|
| Mechanism | Laravel's file-based logging, compatible with cPanel Shared Hosting `§21.1` — optimized for Laravel 12 |
| Location | Environment's `storage/logs/` directory §13.1; `§21.1` |
| Content discipline | No sensitive credentials, raw passwords, tokens, API keys, application secrets; no Teacher-private content beyond troubleshooting needs; no full file content, Question Bank content, or unnecessary Student personal data `§21.1; §16.5`. Never log `APP_KEY`, `DB_PASSWORD`, `MAIL_PASSWORD`. |
| Rotation | Operational logs must be rotated to prevent disk space exhaustion on cPanel `§21.3` — primary lever for disk watch. |
| Cleanup | Old operational log files cleaned up periodically to stay within cPanel disk limits `§21.4`. |
| Purpose boundary | Operational logs support troubleshooting, runtime diagnostics, hosting support; they do not replace business Audit Log requirements `§21.1`. |
| Format | Laravel 12 default log channel writes structured logs; detailed diagnostic context (stack traces, SQL context, request details) goes here, never to user-facing responses. |

## 19.2 Audit Log (database-resident — not file-log concern)

1. Audit Log is mandatory, append-only, immutable, permanently retained; ten mandatory events (Create, Update, Archive, Restore, Login success and failure, Permission Change, Attendance Change, Exam Modification, Homework Modification, Subscription Change) recorded without exception `§21.2; §10`.
2. Audit Log entries stored in **MySQL 8 database** (same instance per environment), not in file-based logs, subject to permanent retention rules `§21.3`.
3. Operational log rotation and cleanup must **never** delete or modify Audit Log entries `§21.3-§21.4`.
4. Security-relevant events beyond mandatory set — repeated failed logins, authorization failures, cross-Teacher access attempts, rate-limit violations, file-upload validation failures, password-reset requests/completions, session creation/destruction — should be logged `§15.5`.
5. Teacher Staff actions attributed to Teacher Staff user, not Teacher; Super Admin actions to Super Admin; Student/Parent actions to authenticated account.
6. Audit Log entries must preserve Platform or Teacher Workspace context, actor, role, event type, affected record reference, before/after snapshots where applicable, timestamp (server time), IP, device/client information.
7. Transactional guarantee (proposed): audit entry written in same database transaction as action it describes — action cannot succeed without its audit record.

## 19.3 Background-Job and Scheduler Logging

- Job failures, retry attempts, scheduled task execution logged for operational review within same content discipline `21_Background_Jobs.md; §21.1`.
- Failed-job detail lives in failed jobs table §14.3 and operational log; no failure notifications sent `§14.3`.

## 19.4 Logging Configuration Standards

1. Detailed diagnostic context goes to operational logs — never to user-facing responses `§18.4; §20`.
2. What gets logged for failures and at what severity follows consolidated logging standards in `34_Error_Codes.md` (LOG-xx entries) and `28_Coding_Standards.md` §16; this document configures where and how logs live, not per-error logging rules.
3. Development and Testing keep same content discipline (no real credentials in any environment's logs); local debugging verbosity does not justify logging secrets anywhere.
4. Laravel 12 logging optimized for MySQL 8 environment: no external log aggregator required for V1; future cloud may consider managed logging.

---

# 20. Debug Configuration

Debug configuration is strictest per-environment divergence and highest-risk. Rules consolidated from `26_Deployment_Plan.md` §8.2 and `23_Security_Standards.md` §18.4.

## 20.1 Per-Environment Debug Matrix

| Environment | `APP_DEBUG` | Error Display Posture | Source |
|---|---|---|---|
| Development | `true` | Detailed error output is local development convenience §4.1. Must never be committed into shared configuration and never travel downstream. Frontend error boundaries may show detailed info locally for developer convenience but must not show in hosted envs. | `§8.2` |
| Testing | Debug disabled (`false`) | Tests verify production-shaped error behavior §5.1; error responses follow standardized structure exactly as Production would emit them per `10_API_Design.md` §6 and `34_Error_Codes.md`. | `§19.1` |
| Staging (Future) | Environment-specific value — may differ from Production during active diagnosis `§23.2`; **`false` whenever release candidate being validated**, because Staging must mirror Production for release acceptance §6.2 | Mirrors Production posture for validation; diagnosis-time deviations temporary and reverted before acceptance. | `§23.2; 27_Development_Roadmap.md` §13.3 |
| Production | **`false` — mandatory and absolute** `§8.2` | Never display errors; never expose stack traces `§18.4`. Error responses use standardized envelope with generic messages. | `§8.2; §21.1` |

## 20.2 Debug Configuration Rules

1. **Production stack-trace prohibition:** `APP_DEBUG=false` so Laravel never exposes stack traces in error responses `§18.4`. Verified in pre-deployment checklist `§26.1` and §22.
2. **Detail goes to logs, not responses:** Detailed error information (stack traces, SQL context, request details) written to operational logs for troubleshooting; error responses to users contain only safe generic information per standardized error structure `§18.4; §6; §19; 34_Error_Codes.md`.
3. **PHP-level agreement:** PHP error-display behavior must agree with environment's `APP_DEBUG` posture §9.2: Production never displays PHP errors to browser; Development `display_errors` may be on locally but never committed.
4. **No debug tooling assumptions:** Official documents define no debug toolbar, telescope, or additional debug variables; none introduced here. Any future developer tooling requires approved decision and must never be enabled on hosted environments without one.
5. **Frontend parallel:** Browser application follows same posture — frontend error boundaries must not display request headers, credentials, raw backend payloads, stack traces, Teacher Workspace identifiers, file paths, or private record data in any hosted environment `12_Frontend_Architecture.md` error-handling standards; React 19 error boundaries provide safe fallback UI.
6. **Sensitive data never in debug output:** Even in Development, real credentials must never be logged — debugging verbosity does not justify logging secrets §19.4.
7. **Environment parity for error handling:** Testing environment validates that error handling behaves production-like even though developer experience locally includes detailed output.

---

# 21. Security Configuration

Security configuration is union of transport, session, authentication-domain, headers, credential, privilege settings each environment must carry. It consolidates configuration-facing parts of `23_Security_Standards.md` and `26_Deployment_Plan.md` §22-§23.

## 21.1 Transport Security

| Concern | Development | Testing | Staging (Future) | Production |
|---|---|---|---|---|
| HTTPS | Not required locally (D-039 targets Production) | Not required for automated context | HTTPS with certificate covering staging domain §6.2 | **Required.** All API endpoints over HTTPS; HTTP redirected to HTTPS; `APP_URL` uses HTTPS scheme (D-039; `§22.1`). |
| SSL certificate | — | — | Staging certificate placeholder | Installed on production server (cPanel-provided Let's Encrypt or provider cert); covers production domain; renewal automated where provider supports it `§22.2` — placeholder meaning only |
| `Cache-Control: no-store` | On sensitive responses per API standard | Same | Same | Same — API responses containing sensitive data never browser-cached `§16.3, §8.7` |
| HSTS | Not enforced locally | Not enforced | Enforced via header when HTTPS active | Enforced via `Strict-Transport-Security` header at web server level `§22.3` |

## 21.2 Security Headers (web server level, hosted environments)

Headers must be configured at web server level `§22.3`, with API responses additionally carrying API security headers `§8.7`:

- `Strict-Transport-Security` — enforce HTTPS, required in Production.
- `X-Content-Type-Options: nosniff` — prevent MIME type sniffing.
- `X-Frame-Options: DENY` or `SAMEORIGIN` — prevent clickjacking.
- `Referrer-Policy` — control referrer information leakage.
- `Content-Security-Policy` — restrict resource loading (applied at web server level for frontend, compatible with React 19 build).
- `Cache-Control: no-store` for sensitive API responses `§8.7`.

Apache `.htaccess` or LiteSpeed equivalent handling; content of header configuration is out of scope, policy requiring headers is in scope.

## 21.3 Session Security Configuration

| Concern | Standard (every environment; Production-mandatory flags noted) | Source |
|---|---|---|
| Session driver | Database — sessions stored in MySQL 8 | D-040; `§7.1` |
| Cookie flags | `HttpOnly`, `Secure` in production, `SameSite=Lax` or `Strict` — Session and CSRF cookies transmitted over HTTPS in Production | `§7.2; §22.1` |
| Expiration | Sessions expire after defined period of inactivity; absolute maximum lifetime applies regardless of activity | `§7.2` |
| Invalidation | All session data destroyed on logout; existing sessions invalidated on password change | `§7.2` |
| Concurrent sessions | Bounded or configurable per user | `§7.2` |
| Session table cleanup | Periodic removal of expired sessions via scheduled work `15; §7.4` | `§7.4` |
| CSRF protection | Laravel provides built-in CSRF protection via CSRF tokens on state-changing requests; `SameSite` cookie attribute; Sanctum SPA auth provides CSRF via `XSRF-TOKEN` cookie / `X-XSRF-TOKEN` header pair; frontend reads cookie and includes header | `§13.3; §22.1` |
| Sanctum stateful domains | Must include environment's own domain; Production includes production domain placeholder | `§8.2, §23.1` |

Laravel 12 Sanctum optimization: session-based authentication for SPA, cookie-based with `HttpOnly`, `Secure`, `SameSite` flags; no JWT complexity; first-party package.

## 21.4 Authentication-Domain and Credential Configuration

1. `SANCTUM_STATEFUL_DOMAINS` must include production domain (Production) or corresponding environment domain §10.6; `§8.2, §23.1`.
2. CORS configuration (if applicable) restricts allowed origins to environment's own domain `§23.1`.
3. Password policy fixed: minimum 8 characters with at least one uppercase, one lowercase, one digit; hashing via bcrypt or Argon2id through Laravel's Hash facade; never stored, logged, returned in plain text `23_Security_Standards.md` §6.1, §3.6. No weakening overrides in any environment — consistent across Dev/Test/Staging/Production.
4. Rate limiting is cache-based throttling on File Cache — no Redis or external service — applied to sensitive endpoints defined by security policy: login, Student registration, password reset, QR scanning, file upload, general API `§14.4, §8.6`. Must be tunable without code changes and must preserve Teacher Workspace isolation.
5. Secrets handling absolute every environment: environment variables only; never committed; never in logs, responses, frontend bundle §2, §10.1, §11.2; `§16.2`.
6. Database least privilege per §12.2 `§11.3`; `.env` outside web root `§21.10`.
7. Error-message safety per §20.2 `§18` — generic "Invalid credentials", "Access denied", "Resource not found" without revealing existence or private data.
8. Laravel 12 Sanctum authentication: CSRF tokens on state-changing POST/PUT/PATCH/DELETE; GET safe and idempotent.
9. Security monitoring indicators: failed logins, repeated authorization failures, cross-Teacher access attempts, rate limit violations, unusual file upload patterns, session anomalies, Audit Log integrity, background job failures `23_Security_Standards.md` §19.2 — monitoring must not expose Teacher-private data or require Redis.

---

# 22. Environment Checklist

This checklist consolidates environment-configuration verifications for each environment. It complements — never replaces — full deployment checklists owned by `26_Deployment_Plan.md` §26 and security checklist owned by `23_Security_Standards.md` §21.

## 22.1 Development Environment Checklist

- [ ] PHP 8.3 with all required extensions §8.2 installed and verified.
- [ ] Composer, MySQL 8, Node.js and npm, Git available §8.1.
- [ ] Local `.env` created from `.env.example`; `APP_ENV=local`; `APP_DEBUG=true`; unique local `APP_KEY` generated §4.3.
- [ ] Framework drivers at Version 1 fixed values: `SESSION_DRIVER=database`, `CACHE_STORE=file`, `QUEUE_CONNECTION=database`, `FILESYSTEM_DISK=public`, `MAIL_MAILER=smtp` §10.4 — validates Laravel 12 + cPanel compatibility locally.
- [ ] Local MySQL 8 database created with `utf8mb4` / `utf8mb4_unicode_ci`, InnoDB §12.1 — optimized for MySQL 8.
- [ ] `VITE_API_BASE_URL` points to local backend §11.1 — React 19 dev server communicates via `/api/v1`.
- [ ] Automated test suites run against dedicated Testing configuration §5.
- [ ] No real credentials or production data anywhere in local setup §2.
- [ ] `storage/` and `bootstrap/cache/` writable locally — File Cache functional.
- [ ] Operational logging active locally with same content discipline (no secrets).

## 22.2 Testing Environment Checklist

- [ ] Dedicated test database instance exists and contains only test data §5.1 — MySQL 8, not SQLite.
- [ ] Test configuration uses MySQL 8, Database Queue, File Cache, Database session driver — full parity constraints hold §5.2.
- [ ] Backend Feature and Unit suites and frontend integration suites execute in this environment for every change §5.2.
- [ ] Test data clearly identifiable and separable; no production data copied §5.2.
- [ ] Environment credentials in non-committed environment files §5.2.
- [ ] `APP_DEBUG` disabled so error behavior matches production shape §5.1.
- [ ] No Docker, Redis, S3, WebSockets, Microservices used in test path.

## 22.3 Staging (Future) Environment Checklist

- [ ] Provisioned on cPanel Shared Hosting as separate account or subdomain mirroring Production §6.1.
- [ ] Own database instance (MySQL 8 utf8mb4), own `.env`, own storage paths, own `APP_KEY`; database not shared with Production §6.2.
- [ ] `APP_ENV=staging`; configuration mirrors Production except environment-specific values §6.2.
- [ ] `APP_DEBUG=false` whenever release candidate being validated §20.1.
- [ ] HTTPS active with certificate covering staging domain §6.2; security headers configured §21.2.
- [ ] Same framework drivers and security posture as Production §10.4, §21.
- [ ] Cron Job for Laravel Scheduler every minute and queue worker Cron configured and verified §14-§15 — Laravel 12 Scheduler dispatches 9 tasks.
- [ ] Storage symlink or equivalent mapping created; `storage/` and `bootstrap/cache/` writable 755/775 §13, §18.
- [ ] Migrations tested here before any Production execution §12.5.
- [ ] UAT performed here with dedicated test data, no production data or credentials §6.2.
- [ ] Operational logging with rotation active; no secrets in logs §19.
- [ ] SSL verification and domain configuration match production pattern §21.1.

## 22.4 Production Environment Checklist — Optimized for Laravel 12 / React 19 / MySQL 8 / cPanel

- [ ] `APP_ENV=production`; `APP_DEBUG=false`; `APP_URL` uses HTTPS single production domain §7.3.
- [ ] `APP_KEY` generated once, unique, unchanged after generation — changing invalidates encrypted data, sessions, tokens §7.3.
- [ ] Confirmed stack verified: PHP 8.3 + extensions §8.2, MySQL 8 `utf8mb4_unicode_ci` InnoDB §12.1, Apache or LiteSpeed, PHP-FPM or CGI/FastCGI §7.1, §8.1.
- [ ] PHP resource limits meet floors: memory 256 MB minimum (512 MB recommended), execution time 60s web / 300s CLI §9.1.
- [ ] Framework drivers at Version 1 fixed values §10.4; `SANCTUM_STATEFUL_DOMAINS` includes production domain §10.6; CORS restricted to production domain.
- [ ] Database user holds minimum privileges; no `DROP`, `ALTER DATABASE`, or `GRANT` §12.2 — least privilege for MySQL 8.
- [ ] Document root maps to `public/`; sensitive directories and `.env` unreachable via HTTP §7.2; `.env` permission 600/640 §18.2.
- [ ] Laravel configuration, route, view, event caches built after deployment — Laravel 12 production optimization §7.3.
- [ ] Storage symlink or mapping present; Public Storage layout in place §13.1; `storage/` and `bootstrap/cache/` writable 755/775 §18.1.
- [ ] File permissions verified: `storage/`, `bootstrap/cache/` writable; `.env` owner-readable only §18.
- [ ] Cron Job for Laravel Scheduler running every minute; queue worker Cron configured §14-§15; overlap protection verified.
- [ ] Queue names `default`, `billing`, `grading`, `reports`, `cleanup`, `audit-support` configured with retries per §14.3.
- [ ] Scheduled tasks 9 tasks verified: Billing Cycle Initialization, Billable Student Calculation (Enrollment duration >15 days only BR-008), Subscription Snapshot, Expired QR Context Cleanup, Exam Auto-Grading, Deferred Report Processing, File Reference Integrity, Audit Log Retention Verification, Queue Table Maintenance §15.2.
- [ ] SSL certificate installed and valid; HTTPS enforced; HTTP redirected §21.1; security headers `HSTS`, `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `CSP` present §21.2.
- [ ] SMTP variables configured (transport baseline; no notification behavior introduced) §17; `MAIL_ENCRYPTION=tls` recommended.
- [ ] Operational logging active with rotation; no secrets in logs; `storage/logs/` writable §19.1.
- [ ] Audit Log mandatory events verified: Create, Update, Archive, Restore, Login success/failure, Permission Change, Attendance Change, Exam Modification, Homework Modification, Subscription Change — append-only, immutable, permanent in MySQL 8 §19.2.
- [ ] React 19 frontend: `VITE_API_BASE_URL` points to production API `/api/v1` over HTTPS; build output fingerprinted in `public/build/`; no secrets in bundle §11.
- [ ] Rate limiting file-cache backed active on login, Student registration, password reset, QR scanning, file upload, general API §14.4.
- [ ] Session cookies `HttpOnly`, `Secure`, `SameSite=Lax|Strict`; Database session driver active §21.3.
- [ ] Full pre-deployment, post-deployment, rollback checklists in `26_Deployment_Plan.md` §26 pass (environment subset above verified as part of them).

---

# 23. Future Cloud Environment

Version 1 runs entirely on cPanel Shared Hosting. VPS / Cloud is confirmed future deployment target `03_System_Architecture.md` §4.1. This section records how environment configuration model evolves when migration is separately approved — without committing V1 to any future infrastructure. Optimized for Laravel 12 and future scale while preserving business invariants.

## 23.1 Step One — Future VPS Environment

When Platform outgrows cPanel Shared Hosting, migration to VPS is first step `§24.1`. VPS provides dedicated resources, full control over PHP, MySQL, web server, ability to run persistent processes, improved performance.

Future VPS baseline:

| Concern | Future VPS Standard |
|---|---|
| Operating System | Linux (Ubuntu or CentOS recommended) |
| PHP | 8.3 with required extensions §8.2 — Laravel 12 optimized |
| MySQL | 8.0+ utf8mb4 InnoDB |
| Web Server | Nginx or Apache — serves Laravel public root and React 19 `public/build/` assets |
| SSL | Let's Encrypt or purchased certificate — renewal automated |
| Queue Worker | Supervisor-managed Laravel queue worker (replaces Cron-triggered worker of §14) — processes `billing`, `grading`, `reports` etc. with same retry table |
| Scheduler | System-level Cron Job for Laravel Scheduler (replaces cPanel Cron entry of §15) — still invokes Scheduler every minute; overlap protection remains |
| Firewall | Basic firewall configuration (UFW or equivalent) |
| Composer | PHP dependency management |
| Node.js | Still only for build — built locally or CI, not required on VPS runtime for frontend assets |

Variable deltas (future only): driver values (`SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION`, `FILESYSTEM_DISK`) remain V1 values until separately approved infrastructure decision changes them; what changes on VPS is trigger mechanism (Supervisor/system Cron instead of cPanel Cron), not confirmed drivers. PHP 8.3 and MySQL 8 remain.

## 23.2 Step Two — Future Cloud Environment

Cloud deployment (managed providers — AWS, DigitalOcean, Linode, etc.) is future consideration requiring separate approval `§25.1`. Documented opportunities, each gated on infrastructure approval:

| Opportunity | Benefit | Configuration Area It Touches | Constraint |
|---|---|---|---|
| Managed database (RDS etc.) | Automated backups, scaling, high availability | §12 `DB_*` endpoints — still MySQL 8, `utf8mb4_unicode_ci` | Must preserve Teacher Workspace isolation BR-003, Archive BR-005, Audit Log immutability BR-006 |
| Object storage (S3-compatible) | Scalable file storage with CDN delivery — file ownership rules preserved | §13 `FILESYSTEM_DISK` — may change from `public` to cloud disk after approval, but private Lesson ownership BR-018 remains | Storage paths still not authorization proofs; backend auth mandatory; cross-Teacher denied |
| Redis cache | Faster cache access, shared cache across instances | §16 `CACHE_STORE` — may change from `file` to Redis after approval | Cache scoping (Teacher Workspace, Student, Platform), invalidation, no sensitive data, no auth bypass must be preserved `25_Performance_Scalability.md` §8.6 |
| Redis queue | Improved queue throughput and reliability | §14 `QUEUE_CONNECTION` | Retry table §14.3, idempotency, scope preservation remain |
| Load balancing | Requests distributed across instances — requires session-sharing strategy | §21.3 `SESSION_DRIVER` — Database driver may need to become shared or Redis after approval | Teacher Workspace isolation, Parent linked-Student scope preserved across instances |
| CDN for static assets | Improved global performance for React 19 fingerprinted assets | §11 asset delivery — `public/build/` assets served via CDN | No private Lessons or Homework files via CDN without auth check |
| Auto-scaling | Automatic resource scaling on demand | §7/§8 runtime topology | Must preserve Archive, Audit Log, Flow A/B separation |
| Managed log aggregation | Centralized operational logs | §19 mechanism may evolve | Content discipline (no secrets) remains |

## 23.3 Migration Invariants (bind every future environment)

1. All confirmed business rules preserved including Teacher Workspace isolation (BR-003) — enforced at data layer, service layer, cache layer, queue layer `§24.4`.
2. Archive policy (BR-005), Audit Log immutability and permanent retention (BR-006), historical data integrity (BR-007, BR-014) preserved `§24.4-§25.3`.
3. Flow A / Flow B separation preserved — separate entities, endpoints, feature modules, reports `§25.3`.
4. Docker, Kubernetes, Microservices not introduced as mandatory V1 components; if introduced future, must be optional and not required for baseline functionality `§24.4`.
5. Migration stays compatible with confirmed technology stack Laravel 12, React 19, MySQL 8 or with separately approved stack changes `§25.3`.
6. Four-environment model §3 and per-environment isolation rules §2 carry forward unchanged: future platforms still separate Development, Testing, Staging, Production with own instances and secrets.
7. One global Student account BR-001, one Parent per Student BR-020, Parent read-only BR-004, Teacher Staff permission assignment BR-013, one Teaching Subject immutable BR-016, Homework Text/Image/PDF only BR-021, Question Bank private BR-011 — all invariants survive future topology changes.
8. Laravel 12, React 19, MySQL 8 optimizations remain: Eloquent query scoping before access, File Cache scoping or future Redis scoping, Database Queue or future queue scoping, database session driver or future shared strategy — all preserve tenant isolation.

---

# 24. Environment Maintenance Guidelines

Ongoing maintenance keeps every environment consistent with this document between releases. These guidelines consolidate configuration-adjacent maintenance duties defined across governing documents; deployment-time and incident-time procedures remain in `26_Deployment_Plan.md`.

## 24.1 Recurring Scheduled Maintenance (delivered by Platform's own Scheduler)

These tasks run via environment's Laravel Scheduler §15 and must be verified as executing `§16.2, §20.2`:

| Cadence | Maintenance Effect | Configuration Area |
|---|---|---|
| Daily | Expired Dynamic QR Code Attendance contexts cleaned up | §15 — Attendance Session cleanup, preserves history |
| Every 5 minutes | Pending Exam automatic-grading jobs processed (prevents grading backlog) — React 19 students see updated results per Teacher | §14 `grading` queue, §15 |
| Every 15 minutes | Queued report-preparation jobs processed — Teacher Workspace scoped reports, Student per-Teacher views, Parent linked-Student read-only views | §14 `reports` queue, §15 |
| Weekly | Queue Table Maintenance removes processed job records (prevents unbounded queue-table growth §14.4); File Reference Integrity Check verifies storage consistency §13 | §14, §13 — no binary deletion, preserves historical references |
| Monthly | Audit Log Retention Verification confirms Audit Log integrity — append-only, immutable, permanent §19.2 | §19, BR-006 |
| Periodic (per §19.1, §12.4) | Operational log rotation/cleanup and expired-session cleanup protect disk space and session-table health `§21.3-§21.4; §7.4` — Laravel 12 file logs rotated | §19, §12.4 |
| Periodic | Disk usage monitoring against cPanel limits; primary levers: log rotation, queue-table maintenance, storage growth | §19, §13, `§20.2` |

Laravel 12 optimization: Scheduler overlap protection prevents duplicate billing or grading; batchable jobs prevent exceeding 300s CLI limit.

## 24.2 Configuration Hygiene (performed by operator)

1. **Template parity:** Whenever variable added, renamed, retired through documentation change process, `.env.example` templates (backend and frontend) updated in same change so committed placeholders always match required variable names `§8.1; §7`.
2. **Framework cache refresh:** After any deployment or maintenance action that changes configuration, routes, views, events, Laravel optimization caches rebuilt §7.3; `§11.2` — critical for Laravel 12 production performance (`config`, `route`, `view`, `event` caches under `bootstrap/cache/` and `storage/framework/`).
3. **Application-key stability:** `APP_KEY` never rotated after initial generation on any environment `§11.3` — rotation invalidates encrypted data, sessions, tokens.
4. **Extension re-verification:** PHP extensions §8.2 re-verified whenever hosting provider changes platform, before each deployment onto new hosting account `§4.2`.
5. **Credential discipline:** Any credential change (database, SMTP) applied by editing environment's deployment-managed `.env` only — never source files, never version control, never frontend bundle §10.1, §11.2 — placeholder meanings only.
6. **SSL continuity:** Certificate validity and automated renewal checked as part of release readiness §21.1; `§22.2, §26.1` — HTTPS mandatory Production.
7. **Cron health:** Cron Job execution monitored so scheduled tasks keep running §15; `§20.2`; silent Cron failure is availability defect (billing, grading, cleanup depend on it).
8. **Disk watch:** Disk usage monitored against cPanel limits; log rotation, queue-table maintenance, storage growth primary levers §19.1, §14.4, §13; `§20.2, §21.3`.
9. **Failed-job review:** Failed jobs table reviewed by Super Admin or authorized platform operator; manual retry follows `21_Background_Jobs.md` §13.2 — no failure notifications exist to automate this §14.3; Laravel 12 built-in failed job management.
10. **Backup-adjacent duties:** Backup scope (MySQL 8 database with all Teacher Workspace records, Student identity, Parent links, Enrollment history, Attendance, Homework, Exams, Lessons, Subscription records, payment-status records, Archive state, session data, queue jobs, Audit Log entries + Laravel Public Storage files), encryption, storage separate from production, integrity verification through periodic test restores, sanitization before downward copy governed by `§18` and `23_Security_Standards.md` §17 and reserved `38_Backup_Recovery.md`. This document's maintenance rule is configuration-facing only: backup artifacts and `.env` contents must never enter version control, and restores must reproduce environment's isolation guarantees.
11. **React 19 build hygiene:** Frontend build produced locally or CI; output `public/build/` fingerprinted; not manually edited on server; Node.js not present on hosted servers; `VITE_API_BASE_URL` points to correct `/api/v1` per environment.

## 24.3 Environment Drift Control

1. **One definition of each value:** Configuration value's meaning defined in this document exactly once; owning documents define behavior, this document defines environment values §1 Document Purpose.
2. **No silent environment drift:** Deviations discovered between environment and this document are corrected, or document amended through documentation governance process `31_Master_Index.md` §8. Staging deviations must be resolved before release-candidate validation §20.1.
3. **Temporary diagnosis posture temporary:** Any diagnosis-time change (e.g., enabling debug output on Staging) reverted immediately after diagnosis §6.2, §20.1.
4. **PENDING watch:** Maintenance must not harden PENDING items: no timezone/localization variables Q-015, no Lesson-video protection configuration Q-010, no enforcement, pricing, or visibility configuration tied to Q-005/Q-011/Q-012/Q-013 §2.8, §10.7.
5. **Change recording:** Environment-standard changes follow same governance as all documentation: modifications to this document subject to `31_Master_Index.md` §8 and Architect ownership, with product-scope changes requiring Product Owner confirmation.
6. **Laravel 12 / React 19 / MySQL 8 drift watch:** If host upgrades PHP, MySQL, or introduces optional Redis, configuration must still require PHP 8.3 and MySQL 8 and File Cache/Database Queue as V1 baseline; optional Redis use requires separate approved decision per §23. No silent upgrade to require Redis, S3, Docker.

---

# Consistency Review

A complete consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Project Context alignment | Passed — every standard preserves frozen V1 rules: BR-001 through BR-022, Archive Policy §11, Audit Log Policy §10, five-role model, technology stack §13, open-question statuses. Single Source of Truth statement honored. No new business rule invented. |
| System Architecture alignment | Passed — technology baseline Laravel 12, PHP 8.3, React 19, TypeScript, Vite, Tailwind CSS, MySQL 8, Sanctum, Gates & Policies with Custom RBAC, File Cache, Database Queue, Database sessions, Laravel Public Storage, Scheduler + Cron, SMTP, Apache/LiteSpeed, cPanel primary, VPS/Cloud future matches `03_System_Architecture.md` §4.1. |
| Deployment Plan alignment | Passed — every value consolidated here matches `26_Deployment_Plan.md`: environment set and isolation §3.3, server requirements and PHP limits/extensions §4, topology §7, variables §8.2-§8.3, storage §13, permissions §14, queue §15, scheduler §16, cache §17, logging §21, SSL/headers/domains §22-§23, future VPS/Cloud §24-§25. Deployment process ownership remains with `26`; this document owns configuration values only. No scripts generated. |
| Testing Strategy alignment | Passed — four-environment table reproduces `24_Testing_Strategy.md` §19.1; parity constraints §19.2 and data isolation §19.3 enforced §3-§5; UAT placement §16.4 honored §6; Testing env `testing` identifier annotated as Laravel-standard beyond documented `local`/`staging`/`production` set. |
| Backend Architecture alignment | Passed — backend stack row including Mail Transport Baseline SMTP matches `11_Backend_Architecture.md` target stack; queue, scheduler, cache, session, storage, logging standards aligned. |
| Frontend Architecture alignment | Passed — Vite browser-safe variables, build-time semantics, frontend build placement into `backend/public/build/`, TypeScript strict, TanStack Query scoping, React Hook Form + Zod match `12_Frontend_Architecture.md`, `04_Project_Structure.md` §6-§7, `26_Deployment_Plan.md` §9-§10. Optimized for React 19. |
| Project Structure alignment | Passed — folder structure, document-root mapping, storage layout `teacher-workspaces/lessons/`, `homework/`, `files/`, `student-homework/`, `storage/framework/`, `storage/logs/`, `bootstrap/cache/`, frontend `public/build/` matches `04_Project_Structure.md` §5-§7. |
| Security Standards alignment | Passed — password policy 8 chars with upper/lower/digit, hashing bcrypt/Argon2id, session flags `HttpOnly`, `Secure`, `SameSite`, HTTPS required Production, headers HSTS/X-Content-Type-Options/X-Frame-Options/Referrer-Policy/CSP/Cache-Control no-store, CSRF XSRF-TOKEN/X-XSRF-TOKEN, Sanctum domains/CORS, rate limiting through File Cache, credential handling, error-message safety, `.env` outside web root match `23_Security_Standards.md` §3, §6-§8, §13-§14, §16, §18, §21.10. No secrets exposed. |
| File Storage alignment | Passed — storage layout, public-storage mapping, application-level authorization mandatory, Q-010 PENDING (no protection config), absence of any confirmed file-size limit, Parent upload denial, video homework denial matches `20_File_Storage.md` §10-§12 and `04_Project_Structure.md` §5; no size limit invented §9.1, §13. |
| Background Jobs alignment | Passed — queue baseline Database Queue, queue names `default`/`billing`/`grading`/`reports`/`cleanup`/`audit-support`, retry table 3/3/2/1/1/2 attempts with exponential/linear/fixed backoff, failed-job handling in failed jobs table, no failure notifications, Cron model single entry Scheduler every minute with overlap protection, 9 scheduled tasks reproduce `21_Background_Jobs.md` §4-§5, §13-§14 exactly; no literal Cron command line generated (scripts prohibited). |
| Notification System alignment | Passed — SMTP configured strictly as transport baseline; no email/push/SMS notification capability created anywhere `19_Notification_System.md`; D-012 preserved; mail variables purpose-limited §10.5, §17. |
| Performance & Scalability alignment | Passed — File Cache usage, scope-aware entries Teacher Workspace/Student/Platform, scoping, invalidation, no sensitive data, bounded content, fragmentation caution, framework optimization caches `bootstrap/cache/` and `storage/framework/` match `25_Performance_Scalability.md` §7-§8; Laravel 12 config/route/view/event caching for Production performance. |
| Database Design alignment | Passed — MySQL 8, InnoDB, utf8mb4/utf8mb4_unicode_ci, character set, privileges CREATE/SELECT/INSERT/UPDATE/DELETE/INDEX/ALTER/CREATE TEMPORARY TABLES/LOCK TABLES without DROP/ALTER DATABASE/GRANT in Production, per-environment isolation, sessions/queue/Audit Log in MySQL match `06_Database_Design.md` and `26_Deployment_Plan.md` §4.3; MySQL 8 optimizations noted. |
| Business/Validation/Error alignment | Passed — no new business rule, validation limit, HTTP status, error code invented; behavior ownership stays with `00`, `02`, `07`, `10`, feature docs, catalogs `32`, `33`, `34` which §19 and §20 cross-reference for logging and error envelopes. |
| Decisions alignment | Passed — D-001 technology stack Laravel 12/React 19/MySQL 8, D-002 status-only payments, D-012 notifications out of scope, D-014 PHP 8.3, D-015 Vite, D-016 Tailwind, D-017 React Router, D-018 TanStack Query, D-019 Hook Form+Zod, D-037 Sanctum, D-039 HTTPS, D-040-043 drivers database/file/database/public/smtp, D-044 cPanel primary, D-045 three environments applied exactly as recorded in `29_Project_Decisions.md`. D-045 preserved with reconciliation note in §6. |
| Coding Standards alignment | Passed — secrets-in-env-vars and committed-config-without-secrets follow `28_Coding_Standards.md` §3.14 and `04_Project_Structure.md` §7; config must not create out-of-scope features applied to mail §17 and drivers §10.7. |
| Master Index alignment | Passed — scope, SSOT statement, exclusions, closing consistency review follow `31_Master_Index.md` §13.5; no subject owned per §9.2 reassigned (deployment process stays with `26`; this document consolidates configuration values). |
| Environment map accuracy | Passed — Development/Testing/Staging/Production identifiers, isolation rules, per-environment values consistent across §3-§11 and checklist §22; `testing` identifier expressly annotated as Laravel-standard value beyond documented `local`/`staging`/`production` set (§3, §5). |
| PENDING items protection | Passed — Q-005 non-payment enforcement, Q-010 Lesson video hosting/protection unresolved, Q-011 Teacher Staff permission granularity, Q-012 Super Admin content visibility, Q-013 flat price vs tier, Q-015 localization/timezone/currency remain PENDING and explicitly protected in §2, §8.3, §10.7, §13, §24.3. No hardening. |
| Version 1 scope | Passed — no Docker, Redis, Kubernetes, S3 Storage, WebSockets, Microservices, payment gateway, notification capability, marketplace behavior, native mobile configuration introduced as V1 requirement; future items gated on separate approval in §23. |
| Secrets hygiene | Passed — no real credentials, hostnames, domains, keys, IP addresses appear anywhere; all values placeholder meanings §10.1, §11.2; checked via scan. |
| No prohibited artifacts | Passed — no deployment scripts, no Cron command lines, no source code, no CI pipelines, no SQL, no database tables, no Form Requests included per commission. |
| Laravel 12 optimization | Passed — configuration caching, route caching, view caching, event caching, Database session driver, File Cache, Database Queue, Laravel Public Storage, Scheduler with Cron, Sanctum SPA auth, Gates & Policies, batchable jobs, overlap protection all optimized for Laravel 12 on cPanel Shared Hosting. |
| React 19 optimization | Passed — Vite build-time env `VITE_` prefix, fingerprinted static assets `public/build/`, TypeScript strict, React Router lazy routes, TanStack Query scoped keys, Tailwind semantic tokens, Hook Form + Zod, no secrets in bundle — all optimized for React 19. |
| MySQL 8 optimization | Passed — InnoDB, utf8mb4/utf8mb4_unicode_ci, utf8mb4 charset, dedicated instances per env, Database Queue and Database sessions and Audit Log in same MySQL 8 instance, full-text search where needed, no SQLite substitutes — optimized for MySQL 8. |
| Canonical terminology | Passed — Platform, Teacher Workspace, Educational Grade, Teaching Subject, Group, Student, Parent, Teacher Staff, Super Admin, Subscription (Flow A), Payment Status / payment status (Flow B), Price Per Student, Pricing Type, Enrollment, Archive, Audit Log, Dynamic QR Code, ID Card, Question Bank (Teacher-owned private), Bubble Sheet, Student Switcher, Lesson (never Course), Lesson Video, Billable Student (Enrollment duration >15 days BR-008), Billing Cycle (calendar month D-006), Homework (Text/Image/PDF only BR-021), Dashboard, Report, Attendance Session, Homework Submission, Exam, Exam Attempt, Exam Answer, File Attachment, Background Job, Cron Job, File Cache, Database Queue, Laravel Public Storage, Laravel Scheduler, cPanel Shared Hosting used exactly as defined in `30_Project_Glossary.md`; non-canonical terms Class, Course, sub-teacher, Delete as permanent removal not present. |

---

*End of document. **REVISION 1.0** — Initial consolidated environment configuration standards, aligned with frozen Project Context Rev 2.0 FINAL (00_Project_Context.md) and governing documents 03, 04, 11, 12, 20, 21, 23, 24, 25, 26, 27, 28, 29, 30, 31 and catalogs 32, 33, 34. Optimized for Laravel 12, React 19, MySQL 8 on cPanel Shared Hosting with future VPS/Cloud path preserved. Changes follow documentation governance process in 31_Master_Index.md §8.*
