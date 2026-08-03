# 04 — Project Structure

## Document Scope

This document defines the Version 1 repository and deployment-oriented directory structure for the **Unified Education Platform**. It is a structure and ownership document only. It does not define source code, implementation logic, database tables, migrations, UI implementation, CSS, API implementation, or deployment procedures.

The structure supports the confirmed Version 1 baseline: Laravel 12 / PHP 8.3, React 19 / TypeScript / Vite / Tailwind CSS, MySQL 8, Laravel Sanctum, Laravel Gates & Policies with Custom RBAC, File Cache, Database Queue, Database sessions, Laravel Public Storage, Laravel Scheduler with Cron Jobs, and cPanel Shared Hosting.

`AI_DOCS/00_Project_Context.md` remains the official Single Source of Truth. This structure must preserve Teacher Workspace isolation, one global Student account, Parent linked-Student read-only access, Flow A / Flow B separation, Archive instead of permanent deletion, and permanent Audit Log retention.

---

# 1. Root Directory Structure

The repository is a modular monolith with separate `backend` and `frontend` applications. This separation keeps Laravel’s server responsibilities and the React browser bundle independently maintainable while allowing them to be deployed together on cPanel Shared Hosting.

```text
BunaaSystem/
├── AI_DOCS/                         # Canonical architecture, requirements, and planning documents
├── backend/                         # Laravel 12 application and API
├── frontend/                        # React 19 + Vite browser application
├── deployment/                      # Versioned deployment-supporting templates and release guidance
├── scripts/                         # Safe repository-level maintenance and build helper scripts
├── .editorconfig                    # Repository-wide editor conventions
├── .gitattributes                   # Git file-handling conventions
├── .gitignore                       # Excludes dependencies, secrets, generated assets, and runtime data
├── README.md                        # Repository entry point and non-secret local setup overview
└── LICENSE                           # License, when selected and approved
```

| Root area | Purpose |
|---|---|
| `AI_DOCS/` | The documented source set for business, architecture, data, API, frontend, backend, and future planning decisions. |
| `backend/` | The Laravel 12 modular monolith. It is the only application that directly accesses MySQL, sessions, queues, and Laravel Public Storage. |
| `frontend/` | The React 19 Web Application. It communicates with the backend using the documented REST API only. |
| `deployment/` | Non-secret deployment templates, cPanel-compatible release references, and server-entrypoint mapping guidance. It does not contain live credentials. |
| `scripts/` | Optional repository-level developer and release helper scripts. They must not duplicate Laravel commands or embed production secrets. |

The repository must not commit dependency directories, generated frontend build output, runtime logs, caches, uploaded files, environment files containing secrets, backups, or database dumps unless a separately approved operational policy explicitly requires a sanitized artifact.

---

# 2. Backend Structure (Laravel)

The backend follows Laravel 12 conventions while grouping application responsibilities by feature and scope. Laravel framework directories remain recognizable so the project is maintainable by Laravel developers and compatible with shared hosting.

```text
backend/
├── app/
│   ├── Console/
│   │   └── Commands/                # Scheduled and administrative Laravel commands
│   ├── Exceptions/                  # Application exception mapping and safe error normalization
│   ├── Features/                    # Feature-owned application organization
│   │   ├── Authentication/          # Login, logout, current context, Student activation
│   │   ├── Authorization/           # Custom RBAC coordination and permission assignment
│   │   ├── PlatformAdministration/  # Super Admin Platform-level operations
│   │   ├── TeacherWorkspace/        # Teacher and Teacher Staff workspace context
│   │   ├── EducationalGrades/       # Educational Grade workflows
│   │   ├── Groups/                  # Groups, schedules, pricing, and Student movement
│   │   ├── Students/                # Student identity relationship and enrollment workflows
│   │   ├── Parents/                 # Linked-Student Parent monitoring boundary
│   │   ├── Attendance/              # Dynamic QR Code, ID Card, manual Attendance, history
│   │   ├── Homework/                # Homework, submissions, review, and permitted files
│   │   ├── Lessons/                 # Private Teacher-owned Lessons and file references
│   │   ├── Exams/                   # Question Bank, Exams, attempts, answers, and grading
│   │   ├── Reports/                 # Role- and scope-appropriate reporting
│   │   ├── Payments/                # Flow B payment-status recording only
│   │   ├── Subscriptions/           # Flow A Subscription, Billing Cycle, Billable Students
│   │   ├── Users/                   # Teacher Staff and user-context management
│   │   ├── Settings/                # Platform, Teacher Workspace, Student, Parent settings
│   │   ├── Files/                   # File references, ownership, access, Archive, restore
│   │   ├── Archive/                 # Archive and restore policy coordination
│   │   └── AuditLog/                # Append-only Audit Log recording and scoped visibility
│   ├── Http/
│   │   ├── Controllers/             # Thin REST request coordinators, grouped by scope/feature
│   │   ├── Middleware/              # Authentication, scope, context, and error middleware
│   │   ├── Requests/                # Form Requests grouped by feature
│   │   └── Resources/               # API response transformation classes
│   ├── Jobs/                        # Database Queue jobs compatible with cPanel limits
│   ├── Models/                      # Laravel model layer for logical business entities
│   ├── Policies/                    # Resource ownership and authorization policies
│   ├── Providers/                   # Laravel application and authorization provider registration
│   ├── Repositories/                # Complex persistence/query abstractions where justified
│   ├── Services/                    # Cross-feature or shared business workflow services
│   └── Support/                     # Shared backend value objects, helpers, and conventions
├── bootstrap/
│   └── cache/                       # Generated Laravel bootstrap/cache files; not source-owned
├── config/                          # Laravel configuration files; no environment secrets
├── database/
│   ├── factories/                   # Laravel model factories for test data
│   ├── migrations/                  # Versioned Laravel schema changes
│   └── seeders/                     # Deliberate non-production and approved reference seed data
├── public/                          # Laravel web document root exposed by Apache/LiteSpeed
│   ├── build/                       # Deployed Vite frontend build output; generated, not committed
│   ├── storage/                     # Laravel public-storage link or equivalent hosting mapping
│   └── index.php                    # Laravel front controller
├── resources/
│   ├── lang/                        # Laravel language resources if localization is approved later
│   └── views/                       # Minimal server-rendered fallback/operational views only
├── routes/
│   ├── api.php                      # `/api/v1` REST route registration
│   ├── console.php                  # Laravel scheduled command registration
│   └── web.php                      # Minimal browser, fallback, or framework web routes
├── storage/
│   ├── app/
│   │   └── public/                  # Laravel Public Storage file root; runtime, never committed
│   ├── framework/                   # Runtime framework cache and session artifacts as applicable
│   └── logs/                        # Runtime Laravel logs; never committed
├── tests/
│   ├── Feature/                     # HTTP, authorization, workflow, and persistence integration tests
│   ├── Unit/                        # Isolated domain/service tests
│   └── Support/                     # Backend test helpers, fixtures, and builders
├── artisan                           # Laravel command-line entry point
├── composer.json                    # PHP dependencies and Laravel scripts declaration
├── composer.lock                    # Locked PHP dependency versions
├── phpunit.xml                      # PHPUnit test configuration
└── .env.example                     # Non-secret environment variable template
```

### Backend folder responsibilities

- `app/Features/` owns feature-specific application coordination. A feature may contain its own service, request, policy-support, query, or resource-facing classes when that keeps ownership clear. It must not become a separate service or independently deployed application.
- `app/Http/` owns HTTP adaptation only: route middleware, validation handoff, controllers, and response transformation. Controllers stay thin and do not become the final source of business rules.
- `app/Models/`, `app/Repositories/`, and `app/Services/` provide shared Laravel model, persistence, and cross-feature workflow boundaries. Repositories are used only where complex query or tenant-scoped retrieval clarity warrants them.
- `app/Policies/` and authorization registration preserve final backend enforcement. No frontend folder or route grouping replaces these checks.
- `app/Jobs/` uses the Database Queue only. `app/Console/Commands/` supports Laravel Scheduler execution by cPanel Cron Jobs; neither adds notifications, payment processing, or unsupported infrastructure.
- `routes/api.php` groups the documented `/api/v1` endpoints by scope and feature. `routes/web.php` remains minimal and must not become an alternate unprotected API surface.
- `resources/views/` is not the React application UI. It is reserved for minimal Laravel-owned operational or fallback needs when required.

The names **EducationalGrades** and **Lessons** are intentional: Version 1 uses *Educational Grade*, not non-canonical alternatives, and *Lesson*, not Course.

---

# 3. Frontend Structure (React)

The frontend uses React 19, TypeScript, Vite, Tailwind CSS, React Router, TanStack Query, React Hook Form, and Zod. It is feature-based and treats the Laravel REST API as the only data and file access boundary.

```text
frontend/
├── public/                          # Static public build inputs only; no user-uploaded content
├── src/
│   ├── app/                         # React bootstrap, providers, root boundary, app configuration
│   ├── assets/                      # Versioned static application assets imported by the bundle
│   ├── auth/                        # Sanctum session coordination, current user, role context helpers
│   ├── components/
│   │   ├── primitives/              # Accessible domain-neutral reusable controls
│   │   └── shared/                  # Reusable composites: states, pagination, filters, selectors
│   ├── config/                      # Typed public environment and frontend configuration
│   ├── features/
│   │   ├── authentication/          # Login, logout, account activation experience
│   │   ├── platform-administration/ # Super Admin Platform-level features
│   │   ├── teacher-workspace/       # Teacher/Teacher Staff workspace features
│   │   ├── educational-grades/      # Educational Grade feature UI and client coordination
│   │   ├── groups/                  # Group feature UI and client coordination
│   │   ├── students/                # Student feature UI and per-Teacher relationship context
│   │   ├── parents/                 # Parent monitoring and Student Switcher
│   │   ├── attendance/              # QR scanner adapter, ID Card input, manual Attendance views
│   │   ├── homework/                # Homework and permitted submission-file flows
│   │   ├── lessons/                 # Private authorized Lesson views
│   │   ├── exams/                   # Question Bank, Exam, attempt, and results views
│   │   ├── reports/                 # Scope-aware report views
│   │   ├── payments/                # Flow B payment-status views only
│   │   ├── subscriptions/           # Flow A Subscription views only
│   │   ├── users/                   # Teacher Staff and permission-assignment views
│   │   ├── settings/                # Role-appropriate settings views
│   │   ├── files/                   # Authorized upload, access, Archive, and restore coordination
│   │   ├── archive/                 # Shared Archive and restore presentation coordination
│   │   └── audit-log/               # Permitted scope-aware Audit Log views
│   ├── layouts/                     # Public, Platform, Teacher Workspace, Student, Parent layouts
│   ├── lib/                         # HTTP boundary, query conventions, safe technical utilities
│   ├── routes/                      # React Router composition, guards, access metadata, lazy routes
│   ├── styles/                      # Tailwind entrypoint and approved semantic theme token definitions
│   ├── test/                        # Shared frontend test setup and utilities
│   └── types/                       # Stable shared TypeScript contracts
├── tests/
│   ├── integration/                 # Cross-feature frontend integration tests
│   └── e2e/                         # Browser-level role and workflow tests when adopted
├── index.html                       # Vite HTML entry point
├── package.json                     # JavaScript dependencies and scripts declaration
├── package-lock.json                # Locked JavaScript dependency versions, if npm is selected
├── tsconfig.json                    # TypeScript compiler configuration
├── vite.config.ts                   # Vite build and development-server configuration
├── tailwind.config.ts               # Tailwind semantic token and content configuration
└── .env.example                     # Non-secret Vite public configuration template
```

### Frontend folder responsibilities

- `src/app/` composes providers and root-level recovery boundaries; it does not own feature workflows.
- `src/features/` owns feature-specific route modules, components, query hooks, form definitions, Zod schemas, local types, and API adapters. Internal feature details are not imported by unrelated features.
- `src/auth/`, `src/routes/`, and `src/layouts/` preserve authenticated role and context handling. Their controls are usability boundaries only; Laravel remains the authorization authority.
- `src/lib/` contains shared technical boundaries, including REST response normalization and TanStack Query conventions. Direct requests from arbitrary presentation components are avoided.
- `src/components/` contains domain-neutral reuse only. Teacher Workspace isolation, Parent read-only rules, and feature workflows remain feature/backend responsibilities.
- `src/styles/` contains the Tailwind entry and semantic theme definition boundary. It does not contain feature business logic or authorization decisions.
- `public/` and `src/assets/` may hold application-owned static assets. Lesson videos, Homework files, and any uploaded file remain backend-controlled Laravel Public Storage resources and never become Vite assets.

Feature names are lower-case kebab case in the frontend. The folder `payments` represents **Flow B payment status** and `subscriptions` represents **Flow A Subscription**; they must remain separate.

---

# 4. Database Structure

MySQL 8 is the system of record. The database directory contains Laravel’s version-controlled database artifacts, not a duplicate database implementation or a physical schema in this document.

```text
backend/database/
├── factories/                       # Factories for deterministic backend test data
├── migrations/                      # Ordered Laravel schema-change history
└── seeders/                         # Explicit seed composition for local/test environments
```

| Directory | Purpose |
|---|---|
| `migrations/` | Records schema evolution for the logical entities specified by the Database Design and Data Dictionary. Each change preserves Archive, history, tenant boundaries, and Audit Log requirements. |
| `factories/` | Creates controlled test records that can represent roles, Teacher Workspaces, linked Students, Archive state, and other valid contexts without relying on production data. |
| `seeders/` | Provides deliberate local, testing, or approved reference data only. Production seeding must never quietly create unauthorized accounts, permissions, payment records, or business data. |

Logical database areas remain aligned with the approved design: global identity and roles; Teacher Workspace ownership; Students and Parent links; Educational Grades, Groups, and Enrollment history; Attendance; Homework; Lessons; Exams and Question Banks; Flow A Subscriptions and Billing Cycles; Flow B payment status; file references; settings; Audit Log; database sessions; and database queue data.

No database dump, generated database file, production backup, or credentials are stored in the source repository. File binaries are not database records: their logical references are persisted in MySQL while their bytes reside in Laravel Public Storage.

---

# 5. Storage Structure

Version 1 uses Laravel Public Storage for cPanel Shared Hosting compatibility. Stored files remain private by business rule through backend authorization and ownership checks, even where Laravel’s public-storage convention or server mapping is used.

```text
backend/storage/
├── app/
│   └── public/
│       ├── teacher-workspaces/      # Runtime Teacher Workspace-owned file namespace
│       │   ├── lessons/             # Private Teacher-owned Lesson video files/references
│       │   ├── homework/            # Teacher-provided Homework attachments where permitted
│       │   └── files/               # Other authorized Teacher Workspace file resources
│       └── student-homework/        # Student Homework Image/PDF submission files
├── framework/                       # Laravel runtime framework data; not repository source
└── logs/                            # Operational logs; not repository source

backend/public/
└── storage/                         # Public-storage link or cPanel-equivalent mapping; no direct trust boundary
```

The runtime namespaces make ownership legible but do not establish access rights. Every file request must pass through backend authorization, Teacher Workspace scope, Student relationship, Parent linked-Student scope, Archive state, and resource ownership checks. Paths must not be accepted from the browser as authorization proof.

Storage constraints:

- Lesson videos are Teacher-owned and private.
- Student Homework submissions allow Image and PDF only; video homework is out of scope.
- Parent uploads are denied.
- File Archive and restore preserve historical references; no hard deletion workflow is introduced.
- Runtime storage, logs, framework cache, sessions, and generated file links are excluded from Git.
- S3 Storage is not required for Version 1.

The precise final protected-file delivery mapping remains subject to the documented PENDING lesson-video hosting/protection decision. This folder structure must not resolve that decision prematurely.

---

# 6. Public Assets Structure

The cPanel web document root is Laravel’s `backend/public/`. The Vite build is deployed into the `build/` subdirectory so Apache or LiteSpeed can serve browser assets while Laravel continues to receive application and API requests.

```text
backend/public/
├── build/                           # Generated Vite production assets; deploy artifact, not Git source
│   └── assets/                      # Fingerprinted JavaScript, CSS, and static bundle assets
├── storage/                         # Laravel public-storage mapping; application access rules still apply
├── .htaccess                        # Apache routing configuration where the hosting server uses Apache
└── index.php                        # Laravel front controller

frontend/public/
└── ...                              # Versioned static assets copied into the Vite build when appropriate

frontend/src/assets/
└── ...                              # Versioned assets processed by Vite and emitted into build/assets
```

`backend/public/build/` is generated by the frontend production build and is not edited manually. It is a release artifact and is excluded from normal source commits. `frontend/public/` and `frontend/src/assets/` must contain only application-owned static resources; they must not contain user uploads, private Lessons, QR attendance secrets, or configuration secrets.

Apache-specific `.htaccess` files are used only when the cPanel server is Apache. LiteSpeed-compatible routing follows the host’s equivalent configuration behavior. This document does not prescribe server configuration contents.

---

# 7. Configuration Structure

Configuration is separated by application ownership and environment sensitivity. Committed configuration defines safe defaults and variable names; environment-specific values and secrets exist only in deployment-managed environment files.

```text
BunaaSystem/
├── backend/
│   ├── config/                      # Laravel framework and application configuration
│   ├── bootstrap/cache/              # Generated Laravel config/route cache; not committed
│   └── .env.example                 # Non-secret Laravel variable template
├── frontend/
│   ├── .env.example                 # Non-secret Vite public-variable template
│   ├── vite.config.ts               # Vite configuration boundary
│   ├── tailwind.config.ts           # Tailwind configuration boundary
│   └── tsconfig.json                # TypeScript configuration boundary
├── deployment/
│   ├── cpanel/                      # Non-secret cPanel mapping and Cron reference templates
│   ├── apache/                      # Non-secret Apache deployment references where applicable
│   └── litespeed/                   # Non-secret LiteSpeed deployment references where applicable
└── .gitignore                       # Repository secret/runtime/generated-artifact exclusions
```

| Configuration boundary | Purpose |
|---|---|
| Laravel `config/` | Framework-level configuration for MySQL, Sanctum, sessions, queues, cache, storage, logging, mail transport baseline, and application behavior. Configuration values do not create out-of-scope notification or payment features. |
| Laravel environment file | Deployment-managed values such as database credentials, application key, mail transport credentials, and other secrets. It is never committed. |
| Vite environment file | Browser-safe public values only, such as a public API base URL. It must never contain tokens, application keys, database values, storage credentials, or authorization decisions. |
| `deployment/` | Versioned non-secret references that make cPanel deployment mapping repeatable without embedding operational credentials or live host details. |

The final Sanctum session or token transport mechanics are deployment decisions. This directory structure supports the approved model without hardening unconfirmed cookie, token, domain, or cross-origin details.

---

# 8. Documentation Structure

`AI_DOCS/` is retained as the canonical documentation root. Existing numbered documents are not renamed by this structure; new approved documents follow the same numeric, descriptive naming pattern.

```text
AI_DOCS/
├── 00_Project_Context.md            # Frozen Version 1 Single Source of Truth
├── 01_Project_Vision.md             # Product vision and scope
├── 02_Software_Requirements.md      # Software requirements specification
├── 03_System_Architecture.md        # Cross-system architecture
├── 04_Project_Structure.md          # This repository/project structure document
├── 05_User_Flows.md                 # Role and product flow documentation
├── 06_Database_Design.md            # Logical database design
├── 07_Data_Dictionary.md            # Logical data dictionary
├── 08_RBAC.md                       # Role-based access control architecture
├── 09_Permission_Matrix.md          # Permission matrix
├── 10_API_Design.md                 # REST API design
├── 11_Backend_Architecture.md       # Laravel backend architecture
├── 12_Frontend_Architecture.md      # React frontend architecture
├── 13_UI_UX_Guidelines.md           # UI/UX guidance
├── 14_UI_Components.md              # UI component guidance
├── 15_Exam_Engine.md                # Exam Engine documentation
├── 16_QR_Attendance_System.md       # QR Attendance documentation
├── 17_Subscription_Billing.md       # Subscription and billing documentation
├── 18_Reporting_Analytics.md        # Reporting and analytics documentation
├── 19_Notification_System.md        # Notification scope document; V1 remains out of scope
├── 20_File_Storage.md               # File storage documentation
├── 21_Background_Jobs.md            # Queue and scheduled-work documentation
├── 22_Search_Filtering.md           # Search and filtering documentation
├── 23_Security_Standards.md         # Security standards
├── 24_Testing_Strategy.md           # Testing strategy
├── 25_Performance_Scalability.md    # Performance and scalability guidance
├── 26_Deployment_Plan.md            # Deployment planning
├── 27_Development_Roadmap.md        # Development roadmap
├── 28_Coding_Standards.md           # Coding standards
├── 29_Project_Decisions.md          # Approved project decisions
├── 30_Project_Glossary.md           # Canonical terminology
├── 31_Master_Index.md               # Documentation index and governance
├── 32_Business_Rules.md             # Consolidated business rules reference
├── 33_Validation_Rules.md           # Consolidated validation rules reference
├── 34_Error_Codes.md                # Application error code registry
├── 35_Environment_Configuration.md  # Environment configuration standards
├── 36_Git_Workflow.md                # Git collaboration workflow
├── 37_Release_Management.md          # Release governance
├── 38_Backup_Recovery.md             # Backup and recovery governance
├── 39_Developer_Guide.md             # Developer onboarding guide
├── 40_AI_Development_Guide.md        # AI-assisted development guide
├── 41_Internationalization_i18n.md   # Arabic/English i18n and RTL/LTR strategy
└── README.md                         # Documentation index
```

Documentation changes must preserve the priority of `00_Project_Context.md`. A document that records future possibilities must label them as future or PENDING rather than silently changing Version 1 scope.

---

# 9. Testing Structure

Testing is separated by application boundary while keeping feature-level tests close to the code they protect. Test data must represent valid scope contexts without using production records or secrets.

```text
BunaaSystem/
├── backend/
│   ├── tests/
│   │   ├── Feature/                 # API, policy, workflow, persistence, Archive integration tests
│   │   ├── Unit/                    # Isolated Laravel domain/service tests
│   │   └── Support/                 # Test helpers, fixtures, builders, authentication helpers
│   └── database/
│       ├── factories/               # Test-data factories
│       └── seeders/                 # Explicit test/local seed composition
└── frontend/
    ├── src/
    │   └── test/                    # Shared frontend test setup and test utilities
    └── tests/
        ├── integration/             # Feature composition, routing, forms, and query-state tests
        └── e2e/                     # Browser workflow tests when the approved test tooling is added
```

| Test area | Required architectural coverage |
|---|---|
| Backend Feature | Authentication, `/api/v1` behavior, validation, Archive/restore, audit-triggering workflows, and error normalization. |
| Backend authorization | Teacher Workspace isolation, Teacher Staff assigned permissions, Student self scope, Parent linked-Student read-only scope, and constrained Super Admin visibility. |
| Backend Unit | Business rules such as duplicate Student prevention, one Group per Teacher, Flow A / Flow B separation, and Billable Student calculation based on Enrollment duration only. |
| Frontend integration | Route guards as usability behavior, context switching, scoped query cache invalidation, forms, error/loading states, and file/QR browser coordination. |
| Frontend end-to-end | Approved critical browser journeys for each role, including QR Attendance, when test tooling and environment support are formally chosen. |
| Accessibility | Keyboard, focus, semantic, validation, route, file, and QR scanner fallback behavior across role-specific flows. |

Tests must not bypass server authorization to make assertions easier, and no test suite may treat frontend visibility as a substitute for backend access enforcement.

---

# 10. Build & Deployment Files

Build and deployment files are kept at the relevant application boundary. Their presence makes cPanel-compatible releases repeatable without requiring Docker, containers, Redis, WebSockets, Kubernetes, S3 Storage, or microservices.

```text
BunaaSystem/
├── backend/
│   ├── artisan                       # Laravel command entry point
│   ├── composer.json                 # PHP dependency/build script declaration
│   ├── composer.lock                 # Locked PHP dependency graph
│   ├── phpunit.xml                   # Backend test-runner configuration
│   ├── .env.example                 # Non-secret environment template
│   └── public/                       # cPanel web document-root content
├── frontend/
│   ├── package.json                  # Frontend dependency/build script declaration
│   ├── package-lock.json             # Locked package graph, if npm is selected
│   ├── vite.config.ts                # Vite build configuration boundary
│   ├── tsconfig.json                 # TypeScript configuration boundary
│   ├── tailwind.config.ts            # Tailwind configuration boundary
│   ├── index.html                    # Vite application entry point
│   └── .env.example                 # Non-secret public-variable template
├── deployment/
│   ├── cpanel/                       # cPanel release, document-root, and Cron reference templates
│   ├── apache/                       # Apache-compatible deployment references
│   └── litespeed/                    # LiteSpeed-compatible deployment references
├── scripts/                          # Optional non-secret repository automation helpers
└── .gitignore                        # Generated artifacts, dependencies, runtime files, and secrets excluded
```

The deployment package provides the compiled Vite assets to `backend/public/build/` and configures the host document root to Laravel’s `backend/public/` where hosting supports that mapping. Laravel Scheduler is triggered by cPanel Cron Jobs; queued work uses the Database Queue; sessions use the database driver; cache uses the File Cache. These are deployment constraints, not an implementation of a release pipeline.

Live `.env` files, `vendor/`, `node_modules/`, frontend build output, Laravel caches, logs, storage runtime files, and credentials are not source-controlled. The detailed deployment sequence, environment inventory, and backup/rollback policy belong to `26_Deployment_Plan.md`.

---

# 11. Naming Conventions

Names must be descriptive, canonical, and stable across backend, frontend, documentation, tests, and deployment references.

| Area | Convention |
|---|---|
| Documentation files | Two-digit numeric prefix followed by Pascal-style descriptive words separated by underscores, for example `04_Project_Structure.md`. |
| Laravel PHP namespaces/classes | PSR-4 PascalCase names. Feature directories use canonical PascalCase feature names, for example `EducationalGrades` and `TeacherWorkspace`. |
| Laravel configuration/routes | Laravel conventions: lower-case configuration filenames and route naming that reflects resource/scope without inventing permissions. |
| React feature folders | Lower-case kebab case, for example `teacher-workspace`, `educational-grades`, and `audit-log`. |
| React components/types | PascalCase names. Hooks use the `use` prefix. |
| React utilities/configuration | Lower-case kebab case filenames where a file name is needed; names remain explicit and domain-neutral. |
| Test files | Match the feature or subject under test and use the selected test tool’s normal suffix convention. |
| Environment variables | Upper-case snake case. Only browser-safe variables use the Vite public prefix. |
| Database migrations | Laravel’s chronological migration naming convention; migration names describe schema intent without leaking data. |
| Storage namespaces | Lower-case kebab case, scoped first by owning context where appropriate. Storage paths are implementation references, not user-visible identifiers or authorization grants. |
| API terminology | Follow `10_API_Design.md`: `/api/v1`, lower-case resource paths, `teacher-workspace` in descriptive endpoint context, and canonical resource names. |

Canonical terminology is mandatory: **Teacher Workspace**, **Educational Grade**, **Lesson**, **Attendance**, **Subscription** for Flow A unless qualified, **payment status** for Flow B, **Archive**, and **Audit Log**. Avoid “course” for Lesson, “delete” for product Archive behavior, and ambiguous “payment” labels that blur the two money flows.

---

# 12. Feature-Based Organization

The same logical features exist across backend, frontend, tests, and documentation, but each application owns only its appropriate layer. This makes a workflow discoverable without duplicating business enforcement.

```text
Feature ownership across the repository

AI_DOCS/                         # Requirements and architecture source for every feature
backend/app/Features/            # Authoritative server workflows and enforcement
backend/app/Http/                # HTTP adaptation for feature endpoints
backend/tests/                   # Backend behavior and boundary verification
frontend/src/features/           # Browser presentation and client coordination
frontend/tests/                  # Browser and client-state verification
```

| Feature | Backend ownership | Frontend ownership |
|---|---|---|
| Authentication | Sanctum integration, account activation, duplicate prevention | Session lifecycle, login/activation forms, safe context bootstrap |
| Authorization | Policies, Gates, Custom RBAC, Teacher Workspace and relationship checks | Capability-aware navigation and presentation only |
| Platform Administration | Super Admin workflows, Flow A administration, constrained reports | Platform-scoped views only |
| Teacher Workspace | Tenant-scoped workflows and Teacher Staff controls | Teacher / Teacher Staff workspace experience |
| Attendance | QR validation, ID Card/manual recording, Audit Log events | Camera/scanner integration and Attendance interaction |
| Files and Lessons | Ownership, storage references, authorized delivery | Browser file selection and authorized display requests |
| Payments / Subscriptions | Separate Flow B status and Flow A Subscription logic | Separate feature modules, labels, route groups, and cache keys |
| Archive / Audit Log | Archive/restore enforcement and append-only Audit Log persistence | Historical and permitted read-only presentation |

Feature boundaries must not create microservices, a second database, a second authorization model, or frontend-only business rules. Shared code is extracted only when it has a stable cross-feature responsibility.

---

# 13. Shared Resources

Shared resources are intentionally small, explicit, and free of feature-specific policy decisions.

```text
backend/app/
├── Support/                        # Shared backend value objects, safe helpers, conventions
├── Services/                       # Shared cross-feature workflow services
├── Repositories/                   # Shared complex query abstractions where justified
├── Policies/                       # Shared authorization policy location
└── Providers/                      # Laravel provider registration

frontend/src/
├── components/
│   ├── primitives/                 # Domain-neutral accessible controls
│   └── shared/                     # Reusable states, pagination, filters, context selectors
├── lib/                            # HTTP boundary, query keys, formatting, browser capability helpers
├── types/                          # Stable shared TypeScript contracts
├── config/                         # Typed public configuration
├── auth/                           # Current-user and active-context coordination
└── test/                           # Test setup and common test utilities
```

Shared resources must not:

- Encode unconfirmed Teacher Staff permission granularity or Super Admin content visibility.
- Accept raw Teacher Workspace, Student, Parent, or file identifiers as evidence of authorization.
- Mix Flow A Subscription and Flow B payment-status concepts.
- Perform payment processing, notifications, marketplace discovery, or cross-Teacher browsing.
- Provide a hard-delete utility or a bypass around Archive, Audit Log, or backend authorization requirements.

---

# 14. Future Expansion Strategy

The Version 1 structure is intentionally extensible without prebuilding future scope. Future changes should add a bounded feature folder, corresponding tests, documentation, and approved configuration rather than fragmenting the modular monolith.

| Future area | Structure approach after formal approval |
|---|---|
| VPS / Cloud | Add environment-specific deployment references under `deployment/` while retaining `backend/` and `frontend/` ownership boundaries. |
| Advanced caching/queues | Extend Laravel configuration and operational documentation only after infrastructure approval; Version 1 does not require Redis. |
| Private object storage | Add a storage-adapter boundary behind the Files feature while preserving file-reference history, ownership, and Teacher Workspace isolation. |
| Localization | Add approved translation resources in `backend/resources/lang/` and frontend locale resources under a dedicated frontend localization boundary after language, timezone, currency, and market decisions are confirmed. |
| Native applications | Introduce a separately approved client application at a future root boundary; do not alter the Version 1 Web Application assumption. |
| Notifications | Add a distinct feature only after separate approval. Version 1 retains no notification routes, jobs, data entity, or client module. |
| Payment gateways | Add a separately approved payment integration boundary only after formal scope approval; preserve Flow A / Flow B separation. |
| RBAC refinement | Extend authorization feature and related UI capability metadata only after Teacher Staff granularity and Super Admin visibility are confirmed. |

Future expansion must preserve the frozen Version 1 rules: complete Teacher Workspace isolation, one global Student account, exactly one Parent account per Student, Parent read-only access, private Teacher-owned Lessons and Question Banks, one Teaching Subject per Teacher, Archive instead of permanent deletion, historical retention, immutable Audit Log records, and no marketplace behavior.

---

# 15. Consistency Review

A consistency review was performed before saving this document.

| Review area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority. |
| Requested scope | Passed — complete repository, backend, frontend, database, storage, assets, configuration, documentation, testing, build/deployment, naming, feature, shared-resource, and future-expansion structure is defined. |
| No implementation content | Passed — no source code, API implementation, UI implementation, CSS implementation, migrations, physical database tables, or deployment procedure is generated. |
| Target stack | Passed — Laravel 12, PHP 8.3, React 19, TypeScript, Vite, Tailwind CSS, MySQL 8, Sanctum, and Custom RBAC boundaries are reflected. |
| cPanel compatibility | Passed — Laravel public document root, generated Vite build placement, File Cache, Database Queue, Database sessions, Laravel Public Storage, Cron Jobs, and Apache/LiteSpeed compatibility are preserved. |
| Teacher Workspace isolation | Passed — feature, storage, test, cache-facing frontend, and backend ownership boundaries maintain Teacher Workspace scope. |
| Student and Parent rules | Passed — one global Student account, per-Teacher partitioning, Parent linked-Student scope, exactly one Parent account per Student, and Parent read-only access are preserved. |
| RBAC constraints | Passed — frontend is non-authoritative; Teacher Staff permissions and Super Admin private-content visibility remain PENDING where required. |
| Flow A / Flow B separation | Passed — Subscription and payment-status folders, features, tests, names, and responsibilities are separated. |
| Files, Archive, and Audit Log | Passed — backend-controlled private file access, Image/PDF Student Homework submissions, Archive over deletion, historical references, and immutable permanent Audit Log requirements are preserved. |
| Version 1 exclusions | Passed — no native mobile requirement, payment gateway, notification system, marketplace, video homework, Docker, Redis, Kubernetes, S3 Storage, WebSockets, or microservices are introduced. |
| Terminology | Passed — Teacher Workspace, Educational Grade, Lesson, Subscription, payment status, Archive, and Audit Log are used consistently. |
