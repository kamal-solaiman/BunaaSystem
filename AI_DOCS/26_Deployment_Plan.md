# 26 — Deployment Plan

## Document Scope

This document defines the complete deployment strategy for Version 1 of the Unified Education Platform. It establishes deployment objectives, environment requirements, build processes, configuration standards, backup and rollback procedures, monitoring and logging guidelines, and future migration paths.

This document does not define source code, shell scripts, deployment commands, APIs, database tables, or UI implementation. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found.

The deployment architecture is built on the confirmed Version 1 technology baseline: **Laravel 12** backend with **PHP 8.3**, **React 19** frontend with **TypeScript** and **Vite**, **MySQL 8** for persistence, **Laravel Sanctum** for authentication, **Laravel Gates & Policies with Custom RBAC** for authorization, **File Cache**, **Database Queue**, **Database session driver**, **Laravel Public Storage**, **Laravel Scheduler with Cron Jobs**, **SMTP** for mail transport, and **Apache or LiteSpeed** as the web server. The primary deployment target is **cPanel Shared Hosting**, with **VPS / Cloud** as the future deployment target.

---

# 1. Deployment Overview

The Unified Education Platform is a multi-tenant SaaS Web Application that serves five confirmed roles: Super Admin, Teacher, Teacher Staff, Student, and Parent. Each Teacher operates a completely isolated Teacher Workspace. The deployment must preserve all confirmed business rules, tenant isolation, Archive policy, Audit Log integrity, and Flow A / Flow B separation.

The Platform is deployed as two coordinated applications:

1. **React 19 Frontend** — a single-page browser application built with Vite that communicates exclusively with the Laravel backend through the documented REST API.
2. **Laravel 12 Backend** — a modular monolith that owns all business logic, authentication, authorization, tenant scoping, validation, persistence, file storage access control, background job processing, scheduling, and Audit Log recording.

The frontend build output is compiled into static assets and served alongside the Laravel backend through the same web server (Apache or LiteSpeed) on cPanel Shared Hosting. The React application never directly accesses the database, file storage, or any server-side resource; all data and file access passes through the backend's authenticated and authorized REST API.

Version 1 must not require Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices. The deployment architecture must remain compatible with cPanel Shared Hosting resource constraints while supporting future migration to VPS or Cloud.

---

# 2. Deployment Objectives

The confirmed deployment objectives are to:

1. **Deliver a reliable cPanel Shared Hosting deployment.** The Platform must operate correctly on the confirmed hosting baseline without requiring infrastructure beyond what cPanel Shared Hosting provides.

2. **Preserve Teacher Workspace isolation in every deployment state.** Tenant isolation (BR-003) must be maintained during build, deployment, migration, backup, restore, and runtime operations.

3. **Maintain separation between the frontend build and the backend application.** The React frontend is compiled into static assets by Vite. The Laravel backend serves the API and the compiled frontend. The two applications have independent build lifecycles but are deployed together.

4. **Support repeatable deployments.** The deployment process must produce consistent, verifiable results across releases without manual configuration drift.

5. **Protect sensitive configuration.** Environment variables, database credentials, application keys, mail credentials, and other secrets must never be committed to the source repository or exposed in build artifacts.

6. **Preserve historical data and Audit Log integrity.** Deployment must never permanently delete data. Archive replaces deletion everywhere (BR-005). Audit Log entries are append-only, immutable, and permanently retained (BR-006).

7. **Support zero-downtime principles where shared hosting allows.** While cPanel Shared Hosting does not guarantee zero-downtime deployment, the process should minimize service interruption.

8. **Enable future VPS / Cloud migration.** Deployment decisions must not prevent future migration to VPS or Cloud infrastructure.

9. **Maintain cPanel Shared Hosting compatibility.** All deployment components must operate within shared hosting resource limits, including process execution time, memory usage, disk space, and concurrent connections.

10. **Support rollback capability.** If a deployment introduces a Critical bug, violates Teacher Workspace isolation, corrupts data, or breaks core functionality, the system must be restorable to a known-good state.

---

# 3. Target Environment

## 3.1 Primary Target: cPanel Shared Hosting

Version 1 is deployed to cPanel Shared Hosting as the primary deployment target.

| Concern | Version 1 Standard |
|---|---|
| Hosting type | cPanel Shared Hosting |
| Web server | Apache or LiteSpeed |
| PHP version | PHP 8.3 |
| Database | MySQL 8 |
| PHP processor | PHP-FPM or CGI/FastCGI (as provided by cPanel) |
| SSL | Required in production (cPanel-provided or custom certificate) |
| Cron Jobs | cPanel Cron Jobs for Laravel Scheduler |
| File storage | Laravel Public Storage |
| Cache | File Cache |
| Queue | Database Queue |
| Session driver | Database |
| Mail transport | SMTP |

## 3.2 Future Target: VPS / Cloud

VPS / Cloud is the confirmed future deployment target (per `AI_DOCS/03_System_Architecture.md` §4.1). VPS / Cloud migration must preserve all confirmed business rules, tenant isolation, and security boundaries.

Future migration considerations are addressed in §24 (Future VPS Migration) and §25 (Future Cloud Migration).

## 3.3 Environment Separation

| Environment | Purpose | Hosting |
|---|---|---|
| **Local development** | Developer-level testing during feature implementation. | Local machine with PHP 8.3, MySQL 8, and compatible web server. |
| **Staging** | Pre-release validation, UAT, and regression testing. | cPanel Shared Hosting (separate account or subdomain) mirroring production configuration. |
| **Production** | Live Platform for all confirmed users. | cPanel Shared Hosting with confirmed stack. |

Each environment must have its own database instance, its own `.env` configuration, and its own storage paths. Production data must never be copied to staging or local environments without sanitization. Test data must be clearly identifiable and separable from real user data.

---

# 4. Server Requirements

## 4.1 cPanel Shared Hosting Requirements

| Requirement | Minimum Standard |
|---|---|
| PHP version | 8.3 |
| MySQL version | 8.0+ |
| Web server | Apache 2.4+ or LiteSpeed |
| PHP memory limit | 256 MB minimum (512 MB recommended) |
| PHP execution time | 60 seconds minimum for web requests; 300 seconds minimum for CLI/Cron |
| PHP post max size | As required by file upload limits (Teacher Homework, Lesson videos) |
| PHP upload max filesize | As required by file upload limits |
| Disk space | Sufficient for application code, MySQL database, Laravel Public Storage files, and operational logs |
| SSL certificate | Required for HTTPS in production |
| Cron Job support | Required for Laravel Scheduler |
| SSH access | Recommended for deployment and artisan commands; not strictly required |
| Composer | Required for PHP dependency installation |
| Node.js and npm | Required for frontend build (build performed locally or in CI, not on the production server) |

## 4.2 PHP Extensions

The following PHP extensions are required by Laravel 12 and the Platform's confirmed feature set:

| Extension | Purpose |
|---|---|
| `openssl` | Encryption, Laravel Sanctum, HTTPS support. |
| `pdo` | Database abstraction layer. |
| `pdo_mysql` | MySQL 8 connectivity. |
| `mbstring` | Multibyte string handling (required by Laravel). |
| `tokenizer` | PHP token parsing (required by Laravel). |
| `xml` | XML processing (required by Laravel). |
| `ctype` | Character type checking (required by Laravel). |
| `json` | JSON encoding/decoding (required by Laravel). |
| `bcmath` | Arbitrary precision mathematics (required by Laravel). |
| `fileinfo` | File type detection for upload validation. |
| `gd` or `imagick` | Image processing for Homework Image files and QR Code generation. |
| `curl` | HTTP client operations (SMTP, external service communication where applicable). |
| `zip` | Archive handling for file operations where applicable. |
| `exif` | Image metadata extraction where applicable. |
| `intl` | Internationalization support for future localization (Q-015 PENDING). |

Extensions must be verified on the cPanel hosting account before deployment. Missing extensions must be enabled through cPanel's PHP Extension Manager or requested from the hosting provider.

## 4.3 MySQL Requirements

| Requirement | Standard |
|---|---|
| Engine | InnoDB (default for MySQL 8) |
| Character set | utf8mb4 |
| Collation | utf8mb4_unicode_ci |
| User privileges | CREATE, SELECT, INSERT, UPDATE, DELETE, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES (minimum for application operation) |
| Max connections | Sufficient for concurrent web requests, queue processing, and Scheduler execution |
| Query cache | Not required (MySQL 8 deprecates query cache; application-level File Cache is used) |

The application database user must have the minimum required privileges. The database user must not have DROP, ALTER DATABASE, or GRANT privileges in production.

---

# 7. Folder Structure After Deployment

The deployment structure on cPanel Shared Hosting follows the Project Structure defined in `AI_DOCS/04_Project_Structure.md`.

The cPanel web document root is set to the Laravel `public/` directory. The compiled Vite frontend assets are deployed into `public/build/`.

```
/home/username/public_html/           ← cPanel document root (mapped to backend/public/)
├── index.php                         ← Laravel front controller
├── .htaccess                         ← Apache routing configuration
├── build/                            ← Compiled Vite frontend assets (generated, not committed)
│   └── assets/                       ← Fingerprinted JS, CSS, and static bundle assets
├── storage/                          ← Laravel public-storage symlink or mapping
└── (other Laravel public files)

/home/username/laravel_app/           ← Laravel application root (above document root)
├── app/                              ← Application code
├── bootstrap/                        ← Bootstrap and cache
├── config/                           ← Configuration files
├── database/                         ← Migrations, factories, seeders
├── public/                           ← Document root (symlinked or mapped to public_html)
├── resources/                        ← Views, language resources
├── routes/                           ← Route definitions
├── storage/                          ← Application storage (logs, framework cache, public storage root)
│   ├── app/public/                   ← Laravel Public Storage file root
│   │   ├── teacher-workspaces/       ← Teacher Workspace-owned files
│   │   └── student-homework/         ← Student Homework submission files
│   ├── framework/                    ← Framework cache and session artifacts
│   └── logs/                         ← Operational logs
├── vendor/                           ← Composer dependencies (generated, not committed)
├── .env                              ← Environment configuration (never committed)
├── artisan                           ← Laravel CLI entry point
├── composer.json
└── composer.lock
```

The application root is placed above the document root so that sensitive files (`.env`, `vendor/`, `storage/`, `database/`) are not directly accessible through the web server. The `public/` directory is the only directory exposed to HTTP traffic.

If the cPanel hosting model requires the application root to be within `public_html/`, additional `.htaccess` rules must deny access to sensitive directories (`app/`, `config/`, `database/`, `storage/`, `vendor/`, `.env`).

---

# 8. Environment Variables (.env)

## 8.1 Configuration Principles

- Environment-specific values and secrets exist only in deployment-managed `.env` files.
- The `.env` file is never committed to the source repository.
- The `.env.example` file in the repository documents required variable names with non-secret placeholder values.
- Sensitive values (database credentials, application key, mail credentials) must not appear in source code, configuration files committed to version control, build artifacts, or operational logs.

## 8.2 Required Environment Variables

| Variable | Purpose | Notes |
|---|---|---|
| `APP_NAME` | Application display name. | Non-secret. |
| `APP_ENV` | Environment identifier (local, staging, production). | Must be `production` in the live environment. |
| `APP_KEY` | Laravel encryption key. | Generated uniquely per environment. Never committed. |
| `APP_DEBUG` | Debug mode flag. | Must be `false` in production. Stack traces must not be exposed. |
| `APP_URL` | Application base URL. | Must use HTTPS in production. |
| `DB_CONNECTION` | Database driver. | `mysql` for Version 1. |
| `DB_HOST` | Database host. | cPanel MySQL hostname. |
| `DB_PORT` | Database port. | Default MySQL port (3306). |
| `DB_DATABASE` | Database name. | cPanel MySQL database name. |
| `DB_USERNAME` | Database user. | Minimum required privileges only. |
| `DB_PASSWORD` | Database password. | Never committed or logged. |
| `SESSION_DRIVER` | Session driver. | `database` for Version 1. |
| `CACHE_STORE` | Cache driver. | `file` for Version 1. |
| `QUEUE_CONNECTION` | Queue driver. | `database` for Version 1. |
| `FILESYSTEM_DISK` | Storage disk. | `public` for Laravel Public Storage. |
| `MAIL_MAILER` | Mail transport. | `smtp` for Version 1. |
| `MAIL_HOST` | SMTP host. | cPanel-provided or configured SMTP server. |
| `MAIL_PORT` | SMTP port. | Standard SMTP port. |
| `MAIL_USERNAME` | SMTP username. | Never committed or logged. |
| `MAIL_PASSWORD` | SMTP password. | Never committed or logged. |
| `MAIL_ENCRYPTION` | SMTP encryption. | `tls` recommended. |
| `MAIL_FROM_ADDRESS` | Sender email address. | Configured per environment. |
| `SANCTUM_STATEFUL_DOMAINS` | Sanctum stateful domains. | Must include the production domain. |

## 8.3 Vite Environment Variables

Vite environment variables are browser-safe configuration values only. They must use the `VITE_` prefix.

| Variable | Purpose | Notes |
|---|---|---|
| `VITE_API_BASE_URL` | Backend API base URL. | Browser-safe public value only. Must not contain tokens, keys, or secrets. |

No secret, credential, private storage path, authorization decision, or server-only configuration may be included in the Vite environment file or frontend build.

---

# 9. Build Process

## 9.1 Build Overview

The build process produces two artifacts:

1. **Backend dependencies** — installed via Composer on the production server.
2. **Frontend static assets** — compiled locally or in a CI environment by Vite, then deployed to the production server's `public/build/` directory.

The frontend build is not performed on the cPanel production server because Node.js and npm may not be available or may be resource-constrained on shared hosting. The build is performed locally or in a dedicated CI environment and the compiled output is transferred to the server.

## 9.2 Backend Build

1. Install PHP dependencies using Composer.
2. Generate the Laravel application key if not already set.
3. Run database migrations.
4. Cache configuration, routes, and views for production performance.
5. Verify that `storage/` and `bootstrap/cache/` directories are writable.

## 9.3 Frontend Build

1. Install JavaScript dependencies using npm.
2. Run the Vite production build.
3. The output is generated in `frontend/dist/` (or the configured output directory).
4. Transfer the compiled output to the production server's `public/build/` directory.

The compiled frontend assets are fingerprinted by Vite for cache-friendly delivery and are excluded from normal source commits.

## 9.4 Build Constraints

- Build artifacts must not contain secrets, credentials, or environment-specific values.
- The `vendor/` directory is generated by Composer on the server and is not committed or transferred.
- The `node_modules/` directory is not deployed to the production server.
- The frontend build must not bundle user-uploaded files, Lesson videos, or private content.

---

# 10. Frontend Deployment

## 10.1 Deployment Process

1. Build the React 19 application using Vite in the local or CI environment.
2. Transfer the compiled build output (`public/build/`) to the production server.
3. The web server (Apache or LiteSpeed) serves the compiled static assets from `public/build/`.
4. The Laravel front controller (`public/index.php`) handles all non-static routes, enabling client-side routing through React Router.

## 10.2 Frontend Configuration

- The `VITE_API_BASE_URL` must point to the production backend API endpoint.
- The frontend must use HTTPS in production.
- Static assets are fingerprinted by Vite for cache-friendly delivery.
- The `.htaccess` or LiteSpeed configuration must route all non-file requests to Laravel's front controller to support React Router's client-side routing.

## 10.3 Frontend Constraints

- The frontend must not access the database, file storage, or any server-side resource directly.
- All data and file access must pass through the backend's authenticated and authorized REST API.
- The frontend must not bypass backend authorization, tenant scoping, Archive rules, or Audit Log requirements.
- Frontend build output must not be manually edited on the production server.

---

# 11. Backend Deployment

## 11.1 Deployment Process

1. Transfer the Laravel application code to the server (excluding `vendor/`, `node_modules/`, `.env`, and generated artifacts).
2. Install PHP dependencies using Composer on the server.
3. Configure the `.env` file with production values.
4. Generate the Laravel application key if this is the first deployment.
5. Run database migrations.
6. Create the storage symlink for Laravel Public Storage if not already present.
7. Cache configuration, routes, and views for production performance.
8. Verify file permissions on `storage/` and `bootstrap/cache/`.
9. Configure the Cron Job for Laravel Scheduler.
10. Configure the queue worker for Database Queue processing.

## 11.2 Laravel Production Optimization

Laravel provides built-in optimization commands for production:

- **Configuration caching** — caches all configuration files into a single file for faster loading.
- **Route caching** — caches the route registration for faster route resolution.
- **View caching** — pre-compiles Blade templates.
- **Event caching** — caches event-to-listener mappings.

These optimizations must be run after every deployment that changes configuration, routes, views, or events.

## 11.3 Backend Deployment Constraints

- The `.env` file must never be overwritten by the deployment process without explicit confirmation.
- Database migrations must be backward-compatible where possible to support rollback.
- The application key must not change after initial generation; changing it invalidates all encrypted data, sessions, and tokens.
- Production debugging (`APP_DEBUG`) must be set to `false`.

---

# 12. Database Migration Process

## 12.1 Migration Strategy

Database migrations are version-controlled schema changes managed by Laravel's migration system. Migrations are applied during deployment using Laravel's artisan commands.

## 12.2 Migration Principles

1. **Migrations must be backward-compatible** where possible to support rollback without data loss.
2. **Migrations must not permanently delete data.** Archive replaces deletion everywhere (BR-005). Schema changes must not drop tables or columns that contain historical data without preserving the data.
3. **Migrations must preserve Teacher Workspace isolation.** Schema changes must not weaken tenant-scoped access patterns.
4. **Migrations must preserve Archive state, Audit Log integrity, and historical data relationships.**
5. **Migrations must be idempotent where possible** so that re-running them does not create duplicate data or inconsistent state.
6. **Migrations must be tested in the staging environment** before production execution.

## 12.3 Migration Execution Order

1. Run pending migrations.
2. Verify that migrations completed successfully.
3. Run seeders only where approved reference data is required (never in production without explicit approval).
4. Verify database schema consistency.

## 12.4 Migration Rollback

- Laravel provides migration rollback capability for recent migrations.
- Rollback must not permanently delete data; if a migration adds a column, rollback removes the column, but the underlying data strategy must be defined to preserve historical requirements.
- Rollback must preserve Teacher Workspace isolation and Audit Log integrity.

---

# 13. Storage Configuration

## 13.1 Laravel Public Storage

Version 1 uses Laravel Public Storage for all file storage. The storage configuration must:

1. Create the `storage/app/public/` directory structure for Teacher Workspace-owned files and Student Homework submissions.
2. Create the symbolic link from `public/storage/` to `storage/app/public/` (or configure the equivalent cPanel mapping).
3. Verify that the storage directories are writable by the web server process.
4. Verify that file access passes through backend authorization and ownership checks.

## 13.2 Storage Directory Structure

The deployed storage structure follows the Project Structure defined in `AI_DOCS/04_Project_Structure.md` §5:

- `teacher-workspaces/lessons/` — Private Teacher-owned Lesson video files.
- `teacher-workspaces/homework/` — Teacher-provided Homework attachments.
- `teacher-workspaces/files/` — Other authorized Teacher Workspace file resources.
- `student-homework/` — Student Homework Image/PDF submission files.

## 13.3 Storage Security

- Storage paths, filenames, and directory structures are not authorization proofs.
- Every file request must pass through backend authorization, Teacher Workspace scope, Student relationship, Parent linked-Student scope, Archive state, and resource ownership checks.
- Cross-Teacher file access must be denied.
- S3 Storage is not required for Version 1.

---

# 14. File Permissions

## 14.1 Directory Permissions

| Directory | Required permission | Purpose |
|---|---|---|
| `storage/` | 755 or 775 (writable by web server) | Application storage root. |
| `storage/app/` | 755 or 775 | Application file storage. |
| `storage/app/public/` | 755 or 775 | Laravel Public Storage root. |
| `storage/framework/` | 755 or 775 | Framework cache, sessions, views. |
| `storage/framework/cache/` | 755 or 775 | Framework cache data. |
| `storage/framework/sessions/` | 755 or 775 | Database session fallback (if file sessions are used temporarily). |
| `storage/framework/views/` | 755 or 775 | Compiled Blade views. |
| `storage/logs/` | 755 or 775 | Operational logs. |
| `bootstrap/cache/` | 755 or 775 | Bootstrap and cached configuration. |

## 14.2 File Permissions

| File | Required permission | Purpose |
|---|---|---|
| `.env` | 600 or 640 (owner-readable only) | Environment configuration with secrets. |
| `artisan` | 755 (executable) | Laravel CLI entry point. |

## 14.3 Permission Constraints

- The `.env` file must not be world-readable.
- Storage directories must be writable by the web server process but not world-writable where the hosting environment supports stricter permissions.
- Uploaded files must inherit the storage directory's permission model.
- File permissions must not expose sensitive application files to HTTP access.

---

# 15. Queue Configuration

## 15.1 Queue Driver

Version 1 uses the **Laravel Database Queue** driver. Jobs are stored in the MySQL 8 database and processed by a queue worker.

| Concern | Version 1 Standard |
|---|---|
| Queue driver | Database |
| Database | MySQL 8 |
| Worker trigger | Laravel Scheduler / cPanel Cron Jobs |
| External dependencies | None — no Redis, SQS, or Beanstalkd |
| Hosting compatibility | cPanel Shared Hosting |

## 15.2 Queue Worker Execution

On cPanel Shared Hosting, the queue worker is triggered through a Cron Job that runs the Laravel queue processing command at a regular interval.

Worker constraints:

- The worker must not require a persistent daemon process.
- Execution must respect cPanel's process execution time limits.
- Long-running jobs must be chunked or use Laravel's batchable jobs pattern.
- The worker must not consume resources that degrade user-facing request performance.
- Processed jobs should be cleaned up periodically to prevent the queue table from growing excessively.

## 15.3 Queue Names

Logical queue names separate work by priority and domain (per `AI_DOCS/21_Background_Jobs.md` §4):

| Queue name | Purpose | Priority |
|---|---|---|
| `default` | General background work | Medium |
| `billing` | Flow A Subscription and Billing Cycle processing | High |
| `grading` | Exam automatic grading and Bubble Sheet processing | High |
| `reports` | Deferred report preparation | Low |
| `cleanup` | File reference cleanup and maintenance | Low |
| `audit-support` | Non-critical Audit Log enrichment | Medium |

## 15.4 Queue Constraints

- Database Queue is the official Version 1 queue mechanism. Redis is not required.
- Queue jobs must preserve Teacher Workspace scope and authorization context.
- Mandatory business actions must not be considered complete if required persistence or Audit Log recording failed.
- Queue jobs must not introduce notifications, payment processing, WebSockets, or microservice behavior.

---

# 16. Scheduler (Cron Jobs)

## 16.1 Scheduler Configuration

Version 1 uses Laravel Scheduler triggered by Cron Jobs on cPanel Shared Hosting. The Scheduler runs at a configured interval and dispatches scheduled tasks based on their defined schedule.

A single Cron Job entry runs the Laravel Scheduler command at a one-minute interval. The Scheduler itself coordinates all scheduled tasks.

## 16.2 Scheduled Tasks

| Task | Schedule | Description |
|---|---|---|
| Billing Cycle Initialization | First day of each calendar month | Starts a new Billing Cycle and prepares Subscription records. |
| Billable Student Calculation | After Billing Cycle initialization, then periodically | Calculates Billable Students per Teacher based on Enrollment duration. |
| Subscription Snapshot Generation | Last day of each calendar month | Generates the immutable Subscription snapshot for the completed Billing Cycle. |
| Expired QR Context Cleanup | Daily | Cleans up expired Dynamic QR Code Attendance contexts. |
| Exam Auto-Grading Queue Processing | Every 5 minutes | Processes pending automatic grading jobs. |
| Deferred Report Processing | Every 15 minutes | Processes queued report preparation jobs. |
| File Reference Integrity Check | Weekly | Verifies file reference consistency. |
| Audit Log Retention Verification | Monthly | Verifies Audit Log integrity. |
| Queue Table Maintenance | Weekly | Cleans up processed job records. |

## 16.3 Cron Job Constraints

- The Cron entry must not contain production credentials or secrets.
- Only one Scheduler instance must run at a time; overlapping runs must be prevented.
- Scheduled tasks must preserve Teacher Workspace isolation.
- Scheduled tasks must not hard delete data.
- Scheduled tasks must not send Version 1 notifications.

---

# 17. Cache Configuration

## 17.1 Cache Driver

Version 1 uses **File Cache** as the official cache driver, compatible with cPanel Shared Hosting.

| Concern | Version 1 Standard |
|---|---|
| Cache driver | File Cache |
| External dependencies | None — no Redis, Memcached, or external cache service |
| Hosting compatibility | cPanel Shared Hosting |

## 17.2 Cache Usage

Cache is used for:

- Frequently accessed, slowly changing reference data (e.g., Educational Grade lists, Group lists, Teaching Subject list).
- Pricing configuration.
- Dashboard summary data.
- Report aggregation results.
- Search filter options.
- Rate limiting state.

## 17.3 Cache Scoping

Cache entries must respect scope boundaries:

- Teacher Workspace cache entries must be scoped to the specific Teacher Workspace.
- Student cache entries must be scoped to the Student's own account.
- Platform-level cache entries (e.g., pricing) are shared across authorized Super Admin requests only.

## 17.4 Cache Invalidation

Cache must be invalidated when underlying data changes. Event-driven invalidation must be used where the framework supports it. Stale cache data must not violate business rules.

## 17.5 Cache Constraints

- File Cache must not be used to store sensitive data such as passwords, tokens, or credentials.
- Cache must not bypass authorization.
- Redis is not required for Version 1.

---

# 18. Backup Strategy

## 18.1 Backup Scope

Backup must cover:

- **MySQL database** — all data including Teacher Workspace records, Student identity, Parent links, Enrollment history, Attendance, Homework, Exams, Lessons, Subscription records, payment-status records, Archive state, session data, queue jobs, and Audit Log entries.
- **Laravel Public Storage files** — Teacher-owned Lesson videos, Homework files, Student Homework submissions, and file references.
- **Application configuration** — `.env` file (encrypted or access-restricted) and any environment-specific configuration.

## 18.2 Backup Frequency

| Component | Frequency |
|---|---|
| MySQL database | Daily minimum; more frequent where hosting supports it. |
| Laravel Public Storage files | Daily minimum for file references; file binary backup frequency depends on hosting capabilities. |
| Application code | Version-controlled in Git; no backup needed beyond the repository. |

## 18.3 Backup Storage

- Backups must be stored in a location separate from the production server.
- Backup artifacts must not be committed to the source repository.
- Backup access must be restricted to authorized personnel only.
- Backup credentials must not be stored in source code or version control.

## 18.4 Backup Encryption

- Database and file backups must be encrypted where the hosting environment supports it.
- Backup encryption keys must be managed separately from the application encryption key.

## 18.5 Backup Integrity

- Backup integrity must be verified periodically through test restores.
- A test restore must confirm that the restored database preserves Teacher Workspace isolation, Archive state, Audit Log integrity, historical data relationships, and Flow A / Flow B separation.

## 18.6 Backup Isolation

- Backups must preserve Teacher Workspace isolation; a backup restore must not mix data across Teacher Workspaces.
- Backups must preserve Archive state, Audit Log immutability, and historical data relationships.
- Backup handling must not make protected files public or expose storage paths or credentials.

---

# 19. Rollback Strategy

## 19.1 Rollback Triggers

A deployment must be rolled back if:

- A Critical bug is discovered after deployment.
- Teacher Workspace isolation is violated.
- Historical data is lost or corrupted.
- Audit Log entries are lost or modified.
- Flow A and Flow B data is conflated.
- Authentication or authorization is broken.
- The Platform becomes unavailable for an extended period.

## 19.2 Rollback Procedures

### Application Code Rollback

1. Restore the previous version of the application code from the version control tag or release archive.
2. Reinstall Composer dependencies for the previous version.
3. Re-cache configuration, routes, and views.
4. Verify that the restored application connects to the database and storage correctly.

### Database Rollback

1. If migrations were run as part of the failed deployment, run Laravel's migration rollback for the affected migrations.
2. If rollback is not possible through migrations, restore the database from the most recent pre-deployment backup.
3. Verify that the restored database preserves Teacher Workspace isolation, Archive state, Audit Log integrity, and historical data relationships.
4. Verify that the restored database is consistent with the restored application code.

### Frontend Rollback

1. Restore the previous version of the compiled frontend build.
2. Replace the `public/build/` directory with the previous build output.
3. Verify that the frontend loads correctly and communicates with the backend.

### Storage Rollback

1. If file storage changes were made during the failed deployment, verify that file references remain valid.
2. Restore files from backup if necessary, preserving Teacher Workspace ownership and historical references.

## 19.3 Rollback Constraints

- Rollback must not permanently delete data. Archive replaces deletion everywhere (BR-005).
- Rollback must preserve Teacher Workspace isolation.
- Rollback must preserve Audit Log integrity.
- Rollback must preserve the separation between Flow A and Flow B.
- Rollback must not weaken authentication or authorization.

---

# 20. Monitoring

## 20.1 Monitoring Scope

Monitoring supports operational awareness without introducing Version 1 notification features (out of scope per D-012) or exposing Teacher-private data.

## 20.2 Monitored Indicators

| Indicator | Purpose |
|---|---|
| Application availability | Detect downtime or unresponsive endpoints. |
| Database connectivity | Detect database connection failures. |
| Queue processing status | Detect job backlog or processing failures. |
| Cron Job execution | Verify that scheduled tasks are running successfully. |
| Disk usage | Monitor storage consumption against cPanel limits. |
| Error rates | Detect spikes in application errors. |
| Failed login attempts | Detect brute-force attacks (per `AI_DOCS/23_Security_Standards.md` §19). |

## 20.3 Monitoring Access

| Role | Monitoring visibility |
|---|---|
| Super Admin | Platform-level operational awareness within confirmed scope. |
| Other roles | No direct monitoring access. |

## 20.4 Monitoring Constraints

- Monitoring must not expose Teacher-private data.
- Monitoring must not introduce push, email, or SMS notification features.
- Monitoring tools, dashboards, and alert thresholds are not confirmed and must not be invented.
- Monitoring must not require Redis, external monitoring services, or unconfirmed infrastructure.

---

# 21. Logging

## 21.1 Operational Logging

Operational logs support troubleshooting, runtime diagnostics, and hosting support. Laravel's file-based logging is compatible with cPanel Shared Hosting.

Operational logging must:

- Avoid storing sensitive credentials, raw passwords, or application secrets.
- Avoid exposing Teacher-private content unnecessarily.
- Support cPanel-compatible file-based logging.
- Not replace business Audit Log requirements.

## 21.2 Audit Log

The Audit Log is mandatory, append-only, immutable, and permanently retained. All confirmed important actions must produce Audit Log entries (per `AI_DOCS/00_Project_Context.md` §10.1).

Mandatory Audit Log events:

- Create.
- Update.
- Archive.
- Restore.
- Login success and failure.
- Permission Change.
- Attendance Change.
- Exam Modification.
- Homework Modification.
- Subscription Change.

Audit Log entries must not be edited, archived, or deleted.

## 21.3 Log Rotation

- Operational logs must be rotated to prevent disk space exhaustion on cPanel Shared Hosting.
- Audit Log entries are stored in the MySQL database, not in file-based logs, and are subject to permanent retention rules.
- Log rotation must not delete or modify Audit Log entries.

## 21.4 Log Cleanup

- Old operational log files should be cleaned up periodically to stay within cPanel disk space limits.
- Cleanup must not affect Audit Log entries stored in the database.
- The queue table cleanup scheduled task removes processed job records weekly.

---

# 22. SSL Requirements

## 22.1 HTTPS Enforcement

HTTPS is required in production for all Platform communication:

- All API endpoints must be served over HTTPS.
- HTTP requests must be redirected to HTTPS.
- The `APP_URL` environment variable must use the HTTPS scheme.
- Session cookies must have the `Secure` flag set in production.
- CSRF cookies must be transmitted over HTTPS.

## 22.2 SSL Certificate

- An SSL certificate must be installed on the production server.
- cPanel Shared Hosting typically provides SSL through Let's Encrypt or the hosting provider's certificate.
- The certificate must cover the production domain.
- Certificate renewal must be automated where the hosting provider supports it.

## 22.3 Security Headers

The following security headers must be configured at the web server level:

- `Strict-Transport-Security` — enforce HTTPS.
- `X-Content-Type-Options: nosniff` — prevent MIME type sniffing.
- `X-Frame-Options: DENY` or `SAMEORIGIN` — prevent clickjacking.
- `Referrer-Policy` — control referrer information leakage.
- `Content-Security-Policy` — restrict resource loading (applied at web server level for the frontend).

---

# 23. Domain Configuration

## 23.1 Production Domain

- The Platform must be accessible through a single production domain.
- The `APP_URL` environment variable must reflect the production domain with HTTPS.
- Sanctum's `SANCTUM_STATEFUL_DOMAINS` must include the production domain.
- CORS configuration (if applicable) must restrict allowed origins to the production domain.

## 23.2 Staging Domain

- The staging environment must use a separate domain or subdomain (e.g., `staging.example.com`).
- Staging configuration must mirror production except for environment-specific values (database credentials, APP_DEBUG, APP_ENV).
- Staging must not share a database with production.

## 23.3 API Routing

- All Version 1 REST endpoints use the `/api/v1` prefix.
- The web server must route all non-file requests through Laravel's front controller to support both API endpoints and React Router client-side routing.
- Apache `.htaccess` or LiteSpeed rewrite rules must handle this routing.

## 23.4 Domain Constraints

- The Platform must not use multiple domains for the same environment.
- Cross-origin requests must be properly configured if the frontend and backend are served from different subdomains (not expected in the standard cPanel deployment).
- Teacher Workspace isolation must be preserved regardless of domain configuration.

---

# 24. Future VPS Migration

## 24.1 Migration Readiness

When the Platform outgrows cPanel Shared Hosting, migration to VPS is the first step toward the confirmed future deployment target. VPS migration provides:

- Dedicated server resources (CPU, RAM, disk).
- Full control over PHP, MySQL, and web server configuration.
- Ability to run persistent processes (queue workers, scheduler daemons).
- Improved performance and reliability.

## 24.2 VPS Requirements

| Concern | VPS Standard |
|---|---|
| Operating system | Linux (Ubuntu or CentOS recommended) |
| PHP | 8.3 with required extensions |
| MySQL | 8.0+ |
| Web server | Nginx or Apache |
| SSL | Let's Encrypt or purchased certificate |
| Queue worker | Supervisor-managed Laravel queue worker |
| Scheduler | System-level Cron Job for Laravel Scheduler |
| Firewall | Basic firewall configuration (UFW or equivalent) |

## 24.3 VPS Migration Steps

1. Provision a VPS with the required software stack.
2. Deploy the Laravel application and React frontend.
3. Migrate the MySQL database from cPanel to the VPS.
4. Migrate Laravel Public Storage files.
5. Configure the web server, SSL, Cron Jobs, and queue worker.
6. Update DNS to point to the VPS.
7. Verify that all functionality works correctly on the VPS.
8. Decommission the cPanel hosting account after successful verification.

## 24.4 VPS Migration Constraints

- Migration must preserve all confirmed business rules.
- Migration must preserve Teacher Workspace isolation.
- Migration must preserve Archive policy and Audit Log integrity.
- Migration must preserve Flow A / Flow B separation.
- Migration must not introduce Docker, Kubernetes, or Microservices as mandatory components.
- Database migration must preserve historical data, Archive state, and referential integrity.

---

# 25. Future Cloud Migration

## 25.1 Cloud Migration Readiness

Cloud deployment (AWS, DigitalOcean, Linode, etc.) provides advanced scaling, managed services, and geographic distribution. Cloud migration is a future consideration that requires separate approval.

## 25.2 Cloud Opportunities

| Opportunity | Benefit | Required approval |
|---|---|---|
| Managed database (RDS, etc.) | Automated backups, scaling, high availability. | Infrastructure approval. |
| Object storage (S3, etc.) | Scalable file storage with CDN delivery. | Infrastructure approval + file ownership rules preserved. |
| Redis cache | Faster cache access, shared cache across instances. | Infrastructure approval. |
| Redis queue | Improved queue throughput and reliability. | Infrastructure approval. |
| Load balancing | Distribute requests across multiple instances. | Infrastructure approval + session sharing strategy. |
| CDN for static assets | Improved global performance. | Infrastructure approval. |
| Auto-scaling | Automatic resource scaling based on demand. | Infrastructure approval. |

## 25.3 Cloud Migration Constraints

- Teacher Workspace isolation must be preserved at every layer.
- Archive policy, Audit Log immutability, and historical data retention must be preserved.
- Flow A / Flow B separation must be preserved.
- Docker, Kubernetes, and Microservices must not be required as mandatory V1 components.
- Migration must be compatible with the confirmed technology stack or approved future stack changes.

---

# 26. Deployment Checklist

The following checklist summarizes the verification steps that must be completed before and after each deployment.

## 26.1 Pre-Deployment Checklist

- [ ] All automated tests pass (backend Feature, Unit, frontend integration).
- [ ] No Critical or High bugs are open.
- [ ] Database migrations are tested in staging.
- [ ] Frontend build is compiled and verified.
- [ ] Environment variables are configured correctly in `.env`.
- [ ] `APP_DEBUG` is set to `false`.
- [ ] `APP_ENV` is set to `production`.
- [ ] `APP_KEY` is generated and unique.
- [ ] `APP_URL` uses HTTPS.
- [ ] Database credentials are correct and the user has minimum required privileges.
- [ ] Sanctum configuration is correct for the production domain.
- [ ] SMTP configuration is verified.
- [ ] File permissions are set correctly on `storage/` and `bootstrap/cache/`.
- [ ] SSL certificate is installed and valid.
- [ ] DNS is configured correctly.
- [ ] Backup of current database and files is taken (for rollback purposes).

## 26.2 Post-Deployment Checklist

- [ ] Application loads correctly in the browser.
- [ ] Authentication works for all five roles.
- [ ] Teacher Workspace isolation is verified (cross-Teacher access denied).
- [ ] Student self-registration and Teacher-created Student activation work correctly.
- [ ] Duplicate Student account prevention is verified.
- [ ] Attendance recording works for all three methods (Dynamic QR Code, ID Card, manual).
- [ ] Homework creation, submission, and grading work correctly.
- [ ] Exam creation, Student attempt, automatic grading, and Essay pending work correctly.
- [ ] Lesson upload and authorized Student access work correctly.
- [ ] Reports generate correctly for all roles.
- [ ] Flow A and Flow B are clearly separated.
- [ ] Archive and restore work correctly.
- [ ] Audit Log entries are recorded for all mandatory events.
- [ ] File uploads work correctly for supported types.
- [ ] Background jobs are processing (queue worker is running).
- [ ] Cron Jobs are executing scheduled tasks.
- [ ] Cache is functioning (File Cache).
- [ ] Sessions are stored in the Database session driver.
- [ ] Search and filtering work correctly with scope-aware results.
- [ ] Parent read-only access is enforced.
- [ ] Error responses do not expose sensitive data.
- [ ] HTTPS is enforced on all pages.
- [ ] Security headers are present in HTTP responses.

## 26.3 Rollback Checklist

- [ ] Previous application code version is identified and available.
- [ ] Pre-deployment database backup is available.
- [ ] Pre-deployment file storage backup is available.
- [ ] Rollback procedure is documented and understood.
- [ ] Post-rollback verification steps are defined.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Project Context alignment | Passed — deployment plan follows the frozen Version 1 rules. All BR references, role definitions, scope boundaries, and confirmed/pending statuses are consistent with `AI_DOCS/00_Project_Context.md`. |
| System Architecture alignment | Passed — technology baseline (Laravel 12, PHP 8.3, React 19, Vite, MySQL 8, File Cache, Database Queue, Database sessions, Laravel Public Storage, Laravel Scheduler with Cron Jobs, SMTP, Apache/LiteSpeed, cPanel Shared Hosting, VPS/Cloud future target) is consistent with `AI_DOCS/03_System_Architecture.md` §4.1. |
| Project Structure alignment | Passed — deployment folder structure is consistent with `AI_DOCS/04_Project_Structure.md`. Document root mapping, storage structure, frontend build output placement, and environment file conventions are preserved. |
| Backend Architecture alignment | Passed — deployment configuration is consistent with `AI_DOCS/11_Backend_Architecture.md`. Queue strategy, Scheduler configuration, cache driver, session driver, file storage, and production optimization are aligned. |
| Frontend Architecture alignment | Passed — frontend deployment approach is consistent with `AI_DOCS/12_Frontend_Architecture.md`. Vite build, static asset delivery, API communication boundary, and environment variable constraints are preserved. |
| Security Standards alignment | Passed — deployment security requirements are consistent with `AI_DOCS/23_Security_Standards.md`. HTTPS enforcement, session cookie flags, security headers, credential storage, password hashing, and the security checklist are aligned. |
| Background Jobs alignment | Passed — queue and Scheduler deployment configuration is consistent with `AI_DOCS/21_Background_Jobs.md`. Database Queue, Cron Job configuration, queue names, worker constraints, and scheduled task definitions are preserved. |
| Subscription/Billing alignment | Passed — Billing Cycle scheduled tasks are consistent with `AI_DOCS/17_Subscription_Billing.md`. Calendar-month Billing Cycle, Billable Student calculation based on Enrollment duration only, and Flow A/Flow B separation are preserved. |
| Testing Strategy alignment | Passed — deployment environment separation and staging validation are consistent with `AI_DOCS/24_Testing_Strategy.md`. Testing environments, environment data isolation, and release acceptance criteria are aligned. |
| Performance & Scalability alignment | Passed — deployment optimization (configuration caching, route caching, view caching) and resource constraints are consistent with `AI_DOCS/25_Performance_Scalability.md`. |
| File Storage alignment | Passed — storage configuration is consistent with `AI_DOCS/20_File_Storage.md`. Laravel Public Storage, directory structure, application-level authorization, and file access control are preserved. |
| QR Attendance alignment | Passed — Dynamic QR Code daily generation and Attendance cleanup scheduled tasks are consistent with `AI_DOCS/16_QR_Attendance_System.md`. |
| Reporting alignment | Passed — deferred report processing and report preparation are consistent with `AI_DOCS/18_Reporting_Analytics.md`. |
| Backup alignment | Passed — backup requirements are consistent with `AI_DOCS/23_Security_Standards.md` §17. Backup encryption, access control, isolation, and integrity verification are preserved. |
| Teacher Workspace isolation | Passed — deployment, migration, backup, restore, and rollback procedures all preserve tenant isolation. |
| Student account rules | Passed — one global Student account, duplicate prevention, and per-Teacher partitioning are preserved. |
| Parent access rules | Passed — linked-Student read-only access is preserved across all deployment states. |
| Archive policy | Passed — no permanent deletion is referenced in any deployment, migration, or rollback procedure. Archive replaces deletion per BR-005. |
| Audit Log policy | Passed — Audit Log integrity is preserved during deployment, migration, backup, and rollback. Immutability and permanent retention are maintained. |
| Payment handling | Passed — Version 1 records payment status only. Flow A and Flow B separation is preserved in all deployment contexts. |
| Version 1 scope | Passed — no native mobile, payment gateway, notification, marketplace, video homework, Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices are introduced as V1 requirements. |
| PENDING items | Passed — non-payment enforcement (Q-005), Lesson video hosting/protection (Q-010), Teacher Staff permission granularity (Q-011), Super Admin content visibility (Q-012), pricing model (Q-013), and localization (Q-015) are preserved as PENDING and not silently hardened. |
| Canonical terminology | Passed — Teacher Workspace, Educational Grade, Teaching Subject, Group, Student, Parent, Teacher Staff, Super Admin, Subscription, Flow A, Flow B, Enrollment, Archive, Audit Log, Dynamic QR Code, ID Card, Question Bank, Bubble Sheet, Lesson, Billable Student, Billing Cycle, and Homework are used consistently. |
| No source code | Passed — no source code, shell scripts, deployment commands, APIs, database tables, or UI implementation is defined. |
