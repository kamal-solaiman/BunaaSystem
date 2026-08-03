# 29 — Project Decisions

## Document Scope

This document records every important architectural and business decision made for Version 1 of the Unified Education Platform. Each decision includes its context, the chosen option, the reasoning, alternatives considered, consequences, and related documents.

This document does not define source code, APIs, database tables, UI implementation, or physical configuration. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found.

Decisions are organized into thematic sections. All decisions reference the canonical business rule identifiers (BR-xxx), decision identifiers (D-xxx), and open question identifiers (Q-xxx) from the Project Context where applicable. Decisions are categorized as **CONFIRMED**, **PROPOSED**, or **PENDING** to preserve the status conventions established in the Project Context.

---

# 1. Technology Stack Decisions

## D-001: Technology Stack Selection

| Field | Value |
|-------|-------|
| **Decision ID** | D-001 |
| **Status** | CONFIRMED |
| **Title** | Version 1 Technology Stack |

### Context

The Unified Education Platform requires a technology stack that supports a multi-tenant SaaS Web Application with five roles, strict data isolation, and cPanel Shared Hosting deployment. The stack must be widely adopted, well-documented, and maintainable by a small team.

### Decision

The Version 1 technology stack is:

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12, PHP 8.3 |
| Frontend | React 19, TypeScript, Vite, Tailwind CSS |
| Database | MySQL 8 |
| Communication | REST API |
| Authentication | Laravel Sanctum |
| Authorization | Laravel Gates & Policies, Custom RBAC |
| Cache | File Cache |
| Queue | Database Queue |
| Session Driver | Database |
| Storage | Laravel Public Storage |
| Scheduler | Laravel Scheduler with Cron Jobs |
| Mail Transport | SMTP |
| Web Server | Apache or LiteSpeed |
| Platform Scope | Web Application only (BR-017) |

### Reason

Laravel 12 provides a mature, full-featured PHP framework with built-in support for authentication, authorization, queue management, task scheduling, and database management — all optimized for MySQL 8. React 19 offers a modern component-based frontend model with TypeScript for type safety. Both frameworks have large ecosystems, extensive documentation, and active communities. MySQL 8 is the standard relational database for cPanel Shared Hosting. Laravel Sanctum provides first-party, framework-integrated authentication suitable for SPAs.

### Alternatives Considered

1. **Node.js / Express backend** — Rejected because the team's expertise and the project's requirements (database queue, scheduler, Eloquent ORM) favor Laravel's integrated ecosystem.
2. **Vue.js frontend** — Rejected because React 19 offers a wider ecosystem, stronger TypeScript integration through Vite, and more available component libraries.
3. **PostgreSQL database** — Rejected because MySQL 8 is the standard on cPanel Shared Hosting and provides all features needed for Version 1.
4. **Redis for caching and queues** — Deferred to future versions because cPanel Shared Hosting does not provide Redis by default. File Cache and Database Queue are sufficient for Version 1.
5. **S3 Storage** — Deferred because Laravel Public Storage is sufficient for Version 1 and compatible with cPanel Shared Hosting.

### Consequences

- All backend development uses Laravel 12 conventions, Eloquent ORM, and PHP 8.3 features.
- All frontend development uses React 19 functional components, TypeScript, Vite, and Tailwind CSS.
- Database schema and queries must be optimized for MySQL 8.
- Deployment targets cPanel Shared Hosting without requiring Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices.
- Future migration to VPS / Cloud is supported but not required for Version 1.

### Related Documents

- `00_Project_Context.md` §13 (Technology Stack)
- `03_System_Architecture.md` §4 (High-Level System Overview)
- `04_Project_Structure.md`
- `11_Backend_Architecture.md`
- `12_Frontend_Architecture.md`
- `26_Deployment_Plan.md` §3–§4

---

## D-014: PHP Runtime Version

| Field | Value |
|-------|-------|
| **Decision ID** | D-014 |
| **Status** | CONFIRMED |
| **Title** | PHP 8.3 Runtime |

### Context

Laravel 12 requires a modern PHP runtime. The chosen PHP version must be supported by cPanel Shared Hosting and compatible with all required PHP extensions.

### Decision

PHP 8.3 is the official Version 1 runtime.

### Reason

PHP 8.3 is the latest stable PHP version supported by Laravel 12 and available on most cPanel Shared Hosting providers. It provides performance improvements, typed features, and security patches that benefit a new SaaS project.

### Alternatives Considered

1. **PHP 8.2** — Supported by Laravel 12 but older; PHP 8.3 offers better performance and features.
2. **PHP 8.4** — Not yet widely available on cPanel Shared Hosting at the time of decision.

### Consequences

- All PHP code must use PHP 8.3 compatible syntax and features.
- Required PHP extensions (openssl, pdo_mysql, mbstring, fileinfo, gd/imagick, curl, zip, intl) must be verified on the hosting account.
- cPanel Shared Hosting must support PHP 8.3 or allow version selection.

### Related Documents

- `03_System_Architecture.md` §4.1
- `11_Backend_Architecture.md` (Target Backend Stack)
- `26_Deployment_Plan.md` §4.2

---

## D-015: Frontend Build Tool

| Field | Value |
|-------|-------|
| **Decision ID** | D-015 |
| **Status** | CONFIRMED |
| **Title** | Vite as Frontend Build Tool |

### Context

The React 19 frontend requires a build tool for development, bundling, and production asset compilation. The tool must support TypeScript, React Fast Refresh, code splitting, and asset fingerprinting.

### Decision

Vite is the official Version 1 frontend build tool.

### Reason

Vite is the recommended build tool for React 19 and provides fast development server startup, optimized production builds, native TypeScript support, and built-in code splitting. It produces fingerprinted static assets suitable for deployment alongside the Laravel backend on cPanel Shared Hosting.

### Alternatives Considered

1. **Create React App (CRA)** — Deprecated and no longer recommended by the React team.
2. **Webpack** — More complex configuration; Vite offers a simpler developer experience with equivalent production output.
3. **Next.js** — Not suitable because Version 1 is a client-side SPA, not a server-rendered application. Next.js would add unnecessary server-side rendering complexity.

### Consequences

- Frontend build produces static assets in `frontend/dist/` (or configured output directory).
- Build output is deployed to `backend/public/build/` for serving by Apache or LiteSpeed.
- Vite environment variables are limited to browser-safe values with the `VITE_` prefix.
- Node.js and npm are required for frontend build but not for production server operation.

### Related Documents

- `04_Project_Structure.md` §3 (Frontend Structure)
- `12_Frontend_Architecture.md` (Target Frontend Stack)
- `26_Deployment_Plan.md` §9.3

---

## D-016: Styling System

| Field | Value |
|-------|-------|
| **Decision ID** | D-016 |
| **Status** | CONFIRMED |
| **Title** | Tailwind CSS as Styling System |

### Context

The React 19 frontend requires a styling system that supports rapid UI development, responsive design, RTL support potential, and semantic design tokens.

### Decision

Tailwind CSS is the official Version 1 styling system.

### Reason

Tailwind CSS provides utility-first CSS that integrates natively with React and Vite. It supports theming through configuration, produces small production bundles through purging, and enables rapid development without naming conflicts. It supports RTL layouts through logical properties and plugins.

### Alternatives Considered

1. **CSS Modules** — More manual work for responsive design and theming; lacks Tailwind's utility-first rapid development.
2. **Styled Components** — Runtime CSS-in-JS adds bundle size and performance overhead.
3. **Bootstrap** — Heavier framework with opinionated visual style; less flexible for custom design systems.

### Consequences

- All styling uses Tailwind utility classes and semantic design tokens defined in the Tailwind configuration.
- A small semantic token layer is maintained for colors, spacing, typography, borders, and focus indicators.
- RTL support can be achieved through Tailwind's logical properties and directional utilities.

### Related Documents

- `12_Frontend_Architecture.md` §17 (Theme Strategy)
- `13_UI_UX_Guidelines.md`
- `14_UI_Components.md`

---

## D-017: Client-Side Routing

| Field | Value |
|-------|-------|
| **Decision ID** | D-017 |
| **Status** | CONFIRMED |
| **Title** | React Router for Client-Side Routing |

### Context

The React SPA needs client-side routing to support navigation between role-specific areas, feature modules, and contextual views.

### Decision

React Router is the official Version 1 client-side routing library.

### Reason

React Router is the standard routing library for React applications. It supports route-level lazy loading, nested layouts, URL parameters, and route guards — all needed for the Platform's multi-role, multi-context navigation model.

### Alternatives Considered

1. **TanStack Router** — Newer but less mature ecosystem at the time of decision.
2. **Custom routing** — Unnecessary complexity when React Router provides all needed features.

### Consequences

- Routes are grouped by role context (Super Admin, Teacher/Teacher Staff, Student, Parent).
- Route guards use authenticated role and permission metadata as usability boundaries; backend authorization remains authoritative.
- Lazy loading is applied at feature and layout boundaries.

### Related Documents

- `12_Frontend_Architecture.md` §4 (Routing Strategy)
- `05_User_Flows.md`

---

## D-018: Server-State Management

| Field | Value |
|-------|-------|
| **Decision ID** | D-018 |
| **Status** | CONFIRMED |
| **Title** | TanStack Query for Server-State Management |

### Context

The frontend needs a mechanism to fetch, cache, and synchronize server data without duplicating backend state in a global client store.

### Decision

TanStack Query (React Query) is the official Version 1 server-state management library.

### Reason

TanStack Query provides declarative data fetching, automatic caching, background refetching, stale-time management, and mutation invalidation. It keeps server data out of global client state, reduces boilerplate, and supports scoped query keys for multi-tenant data isolation.

### Alternatives Considered

1. **Redux Toolkit Query** — More boilerplate and couples the application to Redux for server state.
2. **SWR** — Similar concept but TanStack Query offers more features for pagination, infinite queries, and mutation handling.
3. **Custom fetch hooks** — Would duplicate caching, stale management, and invalidation logic that TanStack Query provides out of the box.

### Consequences

- All backend data is managed through TanStack Query hooks.
- Query keys must include every access-defining context (role, Teacher Workspace, linked Student, Teacher relationship, resource identity, list criteria).
- Cache must be cleared or invalidated when any context changes.
- Mutations invalidate only the affected scoped queries.

### Related Documents

- `12_Frontend_Architecture.md` §8 (State Management Strategy)

---

## D-019: Form Management and Validation

| Field | Value |
|-------|-------|
| **Decision ID** | D-019 |
| **Status** | CONFIRMED |
| **Title** | React Hook Form + Zod for Forms and Validation |

### Context

The frontend needs a form management solution that minimizes re-renders, supports client-side validation, and integrates with TypeScript.

### Decision

React Hook Form for form state management and Zod for client-side schema validation.

### Reason

React Hook Form keeps form state local and avoids unnecessary React re-renders. Zod provides a single source of validation schemas with TypeScript type inference, enabling both client-side validation and type-safe form handling. The combination is the most popular and well-integrated approach in the React ecosystem.

### Alternatives Considered

1. **Formik** — More re-renders; less TypeScript-first.
2. **Yup** — Less TypeScript-native than Zod; Zod provides both validation and type inference.
3. **Custom form handling** — Would duplicate state management, validation, and error handling logic.

### Consequences

- All interactive forms use React Hook Form with Zod schemas.
- Client-side validation provides immediate feedback; backend validation remains authoritative.
- Zod schemas must reflect confirmed rules without hardening PENDING decisions.

### Related Documents

- `12_Frontend_Architecture.md` §10 (Form Management), §11 (Validation Strategy)

---

# 2. Architecture Decisions

## D-020: Multi-Tenant Architecture

| Field | Value |
|-------|-------|
| **Decision ID** | D-020 |
| **Status** | CONFIRMED |
| **Title** | Multi-Tenant Architecture with Teacher Workspace Isolation |

### Context

The Platform serves multiple Teachers, each with completely private data. Students may study with multiple Teachers, creating a need for per-Teacher data partitioning within a single Student account. The architecture must ensure no Teacher can see another Teacher's data under any circumstance.

### Decision

The Platform uses a Multi-Tenant architecture where each Teacher Workspace is a tenant. Tenant isolation is enforced at every layer: database queries, API responses, file access, search results, reports, and error messages.

### Reason

Multi-tenant architecture allows a single application and database to serve all Teachers while maintaining complete data isolation. This is more cost-effective and operationally simpler than per-Teacher database instances while providing the same isolation guarantees through application-level and query-level scoping.

### Alternatives Considered

1. **Database-per-tenant** — Too expensive and operationally complex for cPanel Shared Hosting. Would require managing hundreds of database instances.
2. **Schema-per-tenant** — Not well-supported by MySQL 8 and would complicate shared Student accounts.
3. **Separate application instances** — Impractical for a SaaS platform; would eliminate the benefits of shared infrastructure.

### Consequences

- Every Teacher Workspace-owned record carries the Teacher's identity.
- Every query to workspace-owned data must include the Teacher Workspace scope.
- No cross-tenant foreign keys are allowed except through approved global identity relationships (e.g., Student identity).
- Reports, search results, file access, and error messages must preserve isolation.
- The frontend must use scoped query keys and cache invalidation.

### Related Documents

- `00_Project_Context.md` §12 (Architecture Principles)
- `03_System_Architecture.md` §11 (Multi-Tenant Architecture)
- `06_Database_Design.md` §6 (Tenant Isolation Strategy)
- `08_RBAC.md` §10 (Tenant Isolation Rules)
- `23_Security_Standards.md` §5 (Multi-Tenant Isolation)

---

## D-021: Backend as Sole Security Authority

| Field | Value |
|-------|-------|
| **Decision ID** | D-021 |
| **Status** | CONFIRMED |
| **Title** | Backend-Only Authorization Enforcement |

### Context

The Platform uses a React SPA frontend that communicates with a Laravel backend. Security decisions must be centralized and cannot rely on frontend-only controls.

### Decision

The Laravel backend is the sole authority for authentication, authorization, tenant isolation, business rule enforcement, Audit Log creation, and persistence decisions. The frontend presents authorized data and collects user input but never replaces backend enforcement.

### Reason

Frontend code runs in the user's browser and can be modified, bypassed, or inspected. Security controls must be enforced server-side where they cannot be tampered with. Frontend controls (hidden buttons, disabled inputs, omitted menu items) are usability aids, not security boundaries.

### Alternatives Considered

1. **Frontend + Backend dual enforcement** — Adds complexity without improving security; frontend checks can be bypassed.
2. **API Gateway-level authorization** — Not compatible with cPanel Shared Hosting; would add infrastructure complexity.

### Consequences

- Every protected API request undergoes backend authorization regardless of frontend state.
- Frontend route guards and capability checks are usability measures only.
- Every API response must be independently authorized.
- The frontend must never treat URL parameters, hidden fields, or cached data as authorization proof.

### Related Documents

- `03_System_Architecture.md` §7.3
- `11_Backend_Architecture.md` §11 (Policies & Gates)
- `12_Frontend_Architecture.md` §7 (Authorization Handling)
- `23_Security_Standards.md` §4.4

---

## D-022: REST API Architecture

| Field | Value |
|-------|-------|
| **Decision ID** | D-022 |
| **Status** | CONFIRMED |
| **Title** | REST API with Versioned Endpoints |

### Context

The React frontend and Laravel backend need a standardized communication protocol that supports authentication, authorization, CRUD operations, file uploads, and structured error responses.

### Decision

The Platform uses a RESTful HTTP API with all Version 1 endpoints under the `/api/v1` prefix. The API uses JSON for request and response bodies and follows RESTful resource-oriented conventions.

### Reason

REST is the standard architectural style for web APIs. It is well-supported by Laravel, React, and the broader ecosystem. The `/api/v1` prefix enables future API versioning without breaking existing clients. JSON is the standard data interchange format for web applications.

### Alternatives Considered

1. **GraphQL** — Adds complexity for a SaaS platform with well-defined resource-oriented operations; harder to implement authorization at the query level.
2. **gRPC** — Not suitable for browser-based communication; requires additional infrastructure.
3. **WebSocket-based API** — Real-time communication is out of scope for Version 1; REST is sufficient for all confirmed operations.

### Consequences

- All frontend-backend communication uses the documented REST API.
- Breaking changes require a future API version.
- Version 1 endpoints must not require WebSockets, S3 Storage, Redis, Docker, Kubernetes, or Microservices.
- Endpoints use standard HTTP methods and status codes.

### Related Documents

- `10_API_Design.md`
- `03_System_Architecture.md` §4.1

---

## D-023: Modular Monolith Architecture

| Field | Value |
|-------|-------|
| **Decision ID** | D-023 |
| **Status** | CONFIRMED |
| **Title** | Modular Monolith (Not Microservices) |

### Context

The Platform needs a backend architecture that is maintainable, supports clear feature boundaries, and operates within cPanel Shared Hosting constraints.

### Decision

The backend is a Laravel 12 modular monolith organized by feature areas. It is not a microservices system.

### Reason

A modular monolith provides clear feature boundaries and code organization without the operational complexity, infrastructure requirements, and inter-service communication overhead of microservices. It is compatible with cPanel Shared Hosting, which does not support container orchestration or service mesh infrastructure. The feature-based organization keeps code discoverable and maintainable.

### Alternatives Considered

1. **Microservices** — Requires Docker, Kubernetes, service discovery, inter-service communication, and distributed transaction management. Not compatible with cPanel Shared Hosting.
2. **Service-Oriented Architecture (SOA)** — Adds unnecessary complexity for Version 1's scope.
3. **Simple layered architecture** — Less maintainable at scale; feature-based organization provides better ownership boundaries.

### Consequences

- The backend is deployed as a single Laravel application.
- Feature boundaries exist at the code organization level (app/Features/) but not as separate deployable services.
- Shared resources (models, services, repositories, policies) are deliberately small and explicit.
- The monolith can be decomposed into services in the future if needed.

### Related Documents

- `04_Project_Structure.md` §2 (Backend Structure)
- `11_Backend_Architecture.md` §3 (Feature-Based Organization)

---

## D-024: Frontend Feature-Based Organization

| Field | Value |
|-------|-------|
| **Decision ID** | D-024 |
| **Status** | CONFIRMED |
| **Title** | Feature-Based Frontend Module Organization |

### Context

The React frontend serves five roles with many feature areas. Code must be organized for maintainability without creating a single coupled module.

### Decision

The frontend is organized by feature areas (authentication, platform-administration, teacher-workspace, educational-grades, groups, students, parents, attendance, homework, lessons, exams, reports, payments, subscriptions, users, settings, files, archive, audit-log). Each feature owns its screens, components, hooks, query definitions, form schemas, types, and API adapters.

### Reason

Feature-based organization keeps code discoverable, prevents cross-feature coupling, and enables route-level lazy loading. A feature module should not import another feature's internal implementation, preventing the application from becoming a single coupled module.

### Alternatives Considered

1. **Layer-based organization** (components/, hooks/, services/) — Leads to large, uncategorized directories and cross-feature imports.
2. **Role-based organization** — Creates duplication because many features are shared across roles.

### Consequences

- Feature modules are self-contained and lazily loaded.
- Cross-feature reuse belongs in shared domain-neutral components or common technical utilities.
- Feature names use lower-case kebab case (e.g., `teacher-workspace`, `educational-grades`).

### Related Documents

- `04_Project_Structure.md` §3 (Frontend Structure)
- `12_Frontend_Architecture.md` §3 (Feature-Based Organization)

---

## D-025: Layered Backend Architecture

| Field | Value |
|-------|-------|
| **Decision ID** | D-025 |
| **Status** | CONFIRMED |
| **Title** | Layered Backend Architecture with Thin Controllers |

### Context

The backend needs a clear separation of concerns to ensure business rules are enforced consistently, authorization is centralized, and code is testable.

### Decision

The backend follows a layered architecture: Controllers (thin request coordinators) → Services (business workflow orchestration) → Repositories (complex query abstraction) → Models (Eloquent ORM). Policies and Gates enforce authorization. Form Requests handle input validation.

### Reason

Thin controllers prevent business logic from leaking into the HTTP layer. Services provide a clear home for business workflows that span multiple models. Repositories encapsulate complex query logic. This separation makes the code testable, maintainable, and consistent.

### Alternatives Considered

1. **Fat controllers** — Leads to duplicated business logic, hard-to-test code, and inconsistent rule enforcement.
2. **Action classes** — An alternative to services; services provide better grouping for related workflows.
3. **CQRS** — Adds complexity not justified for Version 1's scope.

### Consequences

- Controllers do not own complex business rules.
- Services must be transaction-aware where multiple changes must succeed together.
- Repositories always scope queries to the resolved Teacher Workspace context.
- Policies and Gates use the permission names from the Permission Matrix as the logical catalog.

### Related Documents

- `11_Backend_Architecture.md` §2–§12

---

# 3. Business Rule Decisions

## D-002: V1 Payment Handling

| Field | Value |
|-------|-------|
| **Decision ID** | D-002 |
| **Status** | CONFIRMED |
| **Title** | Payments Handled Outside Platform (Status-Only Recording) |

### Context

The Platform tracks two money flows: Flow A (Teacher → Platform Subscription) and Flow B (Student/Parent → Teacher fees). Processing online payments requires payment gateway integration, security compliance, and additional infrastructure.

### Decision

Version 1 records payment status only. Actual payments for both Flow A and Flow B are handled outside the Platform. Online payment gateways are out of scope for Version 1.

### Reason

Online payment processing adds significant complexity (PCI compliance, gateway integration, transaction management, refund handling, dispute resolution) that is not needed for Version 1's core value proposition. Recording payment status is sufficient for financial tracking and reporting at launch.

### Alternatives Considered

1. **Stripe integration** — Adds PCI compliance requirements, transaction fees, and integration complexity. Deferred to a future version.
2. **PayPal integration** — Similar concerns to Stripe.
3. **Manual payment + in-platform confirmation** — This is the chosen approach; the platform records status while actual payments happen externally.

### Consequences

- No payment gateway API endpoints exist in Version 1.
- No payment processing code exists in Version 1.
- The platform records payment status values (paid, unpaid, pending) but does not initiate, confirm, or verify transactions.
- Historical payment-status records are preserved for reporting.

### Related Documents

- `00_Project_Context.md` §5.3 (Version 1 Payment Handling)
- `17_Subscription_Billing.md`
- `10_API_Design.md` §24 (Payments Endpoints)

---

## D-006: Billing Cycle Period

| Field | Value |
|-------|-------|
| **Decision ID** | D-006 |
| **Status** | CONFIRMED |
| **Title** | Calendar-Month Billing Cycle |

### Context

The Teacher Subscription requires a defined billing period for calculating Billable Students and generating invoices.

### Decision

The billing cycle starts on the first day of every calendar month and ends on the last day of the same month. A new billing cycle begins automatically on the first day of the next month.

### Reason

Calendar-month billing is standard, predictable, and easy for Teachers to understand. It aligns with common business accounting practices and simplifies the Billable Student calculation window.

### Alternatives Considered

1. **Rolling 30-day cycle** — Creates misaligned billing dates that are harder for Teachers to track.
2. **Weekly billing** — Too frequent; adds administrative overhead without proportional benefit.
3. **Annual billing** — Too long a cycle for a subscription service that depends on monthly Student enrollment counts.

### Consequences

- Billing Cycle initialization runs as a scheduled task on the first day of each calendar month.
- Billable Student calculation evaluates enrollment duration within the calendar-month window.
- Subscription snapshot generation runs on the last day of each calendar month.
- Historical invoices preserve the pricing as of their billing period.

### Related Documents

- `00_Project_Context.md` §5.1
- `17_Subscription_Billing.md`
- `21_Background_Jobs.md`

---

## D-007: Billable Student Calculation Rule

| Field | Value |
|-------|-------|
| **Decision ID** | D-007 |
| **Status** | CONFIRMED |
| **Title** | Billable Student Based on Enrollment Duration Only (>15 Days) |

### Context

The Platform needs a fair, measurable, and auditable rule for determining which Students count toward a Teacher's monthly Subscription.

### Decision

A Student becomes a Billable Student if enrolled in a Teacher's Group for **more than 15 calendar days** during the billing cycle. Students enrolled for 15 days or less are NOT counted. The calculation is based on enrollment duration only — attendance and login activity are NOT used.

### Reason

Enrollment duration is objective, automatically measurable, and directly tied to the Teacher-Student relationship. It avoids ambiguity from attendance methods (a Student might be absent but still enrolled) and login frequency (a Student might not log in daily but still be actively studying). The 15-day threshold provides a fair grace period for short trial enrollments.

### Alternatives Considered

1. **Attendance-based calculation** — Rejected because Attendance methods may be inconsistent and do not accurately reflect enrollment status.
2. **Login-based calculation** — Rejected because login frequency does not reflect educational engagement or enrollment.
3. **Active enrollment (any day)** — Too aggressive; would count Students who join and leave quickly.
4. **30-day full-month only** — Too restrictive; would not account for mid-month enrollments fairly.

### Consequences

- Billable Student calculation uses enrollment start and end dates within the billing cycle.
- Attendance records are not queried for billing purposes.
- Login activity is not queried for billing purposes.
- The formula is: `Monthly Subscription = Billable Students × Price Per Student`.

### Related Documents

- `00_Project_Context.md` §5.1, BR-008
- `17_Subscription_Billing.md`
- `21_Background_Jobs.md`

---

## D-008: One Teaching Subject Per Teacher

| Field | Value |
|-------|-------|
| **Decision ID** | D-008 |
| **Status** | CONFIRMED |
| **Title** | One Teaching Subject Per Teacher Account |

### Context

Teachers may teach multiple subjects. The Platform must decide whether to support multiple subjects under one Teacher account or require separate accounts.

### Decision

Each Teacher account represents exactly one Teaching Subject. The Teaching Subject is selected during registration and cannot be changed after account creation. If a Teacher wants to teach another subject, a separate Teacher account must be created. Teaching Subjects are independent from Educational Grades (BR-016).

### Reason

One-subject-per-account simplifies the Teacher Workspace model: the workspace, Question Bank, Lessons, and all content naturally belong to one subject. It avoids confusion in Student-facing views where content from different subjects would mix within one workspace. Separate accounts for separate subjects maintain clear boundaries.

### Alternatives Considered

1. **Multiple subjects per account** — Adds complexity to content organization, Student views, Question Bank scoping, and reporting. Deferred to a future version if needed.
2. **Subject changeable after registration** — Rejected because it would require migrating or reorganizing all existing content.

### Consequences

- The Teaching Subject is immutable after Teacher account creation.
- The Teacher Workspace is implicitly scoped to one subject.
- A Teacher who teaches Mathematics and Physics must create two separate accounts.
- Teaching Subjects are independent from Educational Grades — a Mathematics Teacher can have First Preparatory and Second Preparatory Educational Grades.

### Related Documents

- `00_Project_Context.md` §8, BR-016
- `02_Software_Requirements.md` Part 2 §9 (Settings)

---

## D-009: One Parent Per Student

| Field | Value |
|-------|-------|
| **Decision ID** | D-009 |
| **Status** | CONFIRMED |
| **Title** | One Parent Account Per Student (V1) |

### Context

Students may have multiple guardians who want to monitor their progress. The Platform must decide how many Parent accounts can be linked to one Student.

### Decision

Version 1 supports exactly one Parent account per Student. One Parent account may be linked to multiple Students. The Parent Panel includes a Student Switcher for navigation between linked Students (BR-020).

### Reason

One-Parent-per-Student simplifies the Parent-Student link model, avoids authorization conflicts (which Parent can make decisions?), and reduces account management complexity for Version 1. Multiple Parents per Student can be added in a future version if needed.

### Alternatives Considered

1. **Multiple Parents per Student** — Adds complexity to linked-Student management, notification routing, and conflict resolution. Deferred to a future version.
2. **Parent only viewing, no link model** — Would not support the Student Switcher or per-Student monitoring scope.

### Consequences

- A Student can have only one linked Parent account.
- A Parent account may be linked to multiple Students.
- The Student Switcher allows the Parent to navigate between linked Students.
- Parent access is read-only everywhere.

### Related Documents

- `00_Project_Context.md` §6.5, BR-020
- `02_Software_Requirements.md` Part 4 (Parent Module)

---

## D-010: Bubble Sheet Exam Format

| Field | Value |
|-------|-------|
| **Decision ID** | D-010 |
| **Status** | CONFIRMED |
| **Title** | Bubble Sheet as Electronic On-Screen Exam with Auto-Grading |

### Context

Traditional paper-based bubble sheet exams are familiar to Teachers. The Platform must decide how to support this format digitally.

### Decision

Bubble Sheet is an electronic exam format that simulates traditional paper bubble sheets. Students answer by selecting bubbles on screen. Automatic grading is supported for Bubble Sheet questions (BR-011).

### Reason

Electronic Bubble Sheet eliminates the need for physical paper, manual grading, and scanner devices while preserving the familiar exam experience. Automatic grading reduces Teacher workload for objective questions.

### Alternatives Considered

1. **Traditional paper Bubble Sheet with scanner integration** — Requires physical scanner hardware and adds operational complexity.
2. **Only Multiple Choice** — Would not provide the Bubble Sheet visual experience that Teachers and Students are familiar with.

### Consequences

- Bubble Sheet is one of four supported question types (Multiple Choice, True/False, Essay, Bubble Sheet).
- Bubble Sheet questions use automatic grading.
- The Question Bank supports all four types.
- Bubble Sheet is electronic-only; no physical paper scanning is needed.

### Related Documents

- `00_Project_Context.md` §7.1, BR-011
- `15_Exam_Engine.md`

---

## D-011: Homework Format Restrictions

| Field | Value |
|-------|-------|
| **Decision ID** | D-011 |
| **Status** | CONFIRMED |
| **Title** | Homework Supports Text, Image, and PDF Only |

### Context

Homework submissions can include various media types. The Platform must decide which formats are supported in Version 1.

### Decision

Homework supports Text, Image, and PDF formats only. Video homework is NOT supported in Version 1 (BR-021). Student submissions accept Image and PDF only.

### Reason

Text, Image, and PDF cover the most common homework formats while keeping file storage, upload validation, and preview capabilities manageable for cPanel Shared Hosting. Video homework adds significant storage, bandwidth, and streaming complexity.

### Alternatives Considered

1. **Video homework** — Requires video hosting, streaming, storage quota management, and potentially third-party video services. Deferred to a future version.
2. **Audio homework** — Similar complexity to video; deferred.
3. **All file types** — Security risk from executable files; validation complexity increases significantly.

### Consequences

- Homework creation accepts Text, Image, and PDF content.
- Student Homework submissions accept Image and PDF files.
- Video homework uploads are rejected at the backend.
- Parent file uploads are denied entirely.
- File storage uses Laravel Public Storage with workspace ownership.

### Related Documents

- `00_Project_Context.md` §7.1, BR-021
- `02_Software_Requirements.md` Part 2 §6 (Homework)
- `10_API_Design.md` §20 (Homework Endpoints)

---

## D-012: Notifications Out of Scope

| Field | Value |
|-------|-------|
| **Decision ID** | D-012 |
| **Status** | CONFIRMED |
| **Title** | Notifications Excluded from Version 1 |

### Context

Push, email, and SMS notifications can enhance user engagement but add infrastructure complexity.

### Decision

Push notifications, email notifications, and SMS notifications are out of scope for Version 1. No notification endpoints, database entities, queued jobs, or scheduled tasks exist for notifications.

### Reason

Notifications add infrastructure requirements (push notification services, email delivery services, SMS gateways), increase the testing surface, and introduce user experience complexity. Version 1 focuses on core operational features without notification dependency.

### Alternatives Considered

1. **Email notifications only** — Still adds SMTP integration complexity, template management, and user preference handling.
2. **Push notifications** — Requires WebSockets or a push notification service; not compatible with Version 1 scope.
3. **SMS notifications** — Requires SMS gateway integration and adds per-message costs.

### Consequences

- No notification API endpoints exist.
- No notification database entities exist.
- No queued notification sending is part of Version 1.
- SMTP is included in the technical baseline as mail transport availability only, not for notification features.

### Related Documents

- `00_Project_Context.md` §4.2
- `19_Notification_System.md` (documents the exclusion)

---

## D-013: Student Registration Methods

| Field | Value |
|-------|-------|
| **Decision ID** | D-013 |
| **Status** | CONFIRMED |
| **Title** | Two Student Registration Methods with Duplicate Prevention |

### Context

Students need to enter the Platform through either self-registration or Teacher-initiated creation. Both methods must produce a single, unique Student account.

### Decision

Student Registration supports two methods: (1) the Student registers their own account, or (2) the Teacher creates the Student account manually. If the Teacher creates the account, the Student can later activate and use the same account. Duplicate Student accounts are NOT allowed — both methods create only one Student account (BR-022).

### Reason

Two registration methods serve different operational needs: some Students independently join the platform, while others are onboarded by their Teachers. Duplicate prevention ensures the one-global-account principle (BR-001) is maintained regardless of registration path.

### Alternatives Considered

1. **Self-registration only** — Would not allow Teachers to onboard Students who are not yet on the platform.
2. **Teacher-creation only** — Would not allow Students to proactively join the platform.
3. **Allow duplicate accounts with merge** — Adds significant complexity for account reconciliation.

### Consequences

- Duplicate account detection must be enforced server-side.
- Teacher-created accounts are activatable by the Student later.
- Both registration paths go through the same duplicate prevention logic.
- The backend is the sole authority for duplicate detection.

### Related Documents

- `00_Project_Context.md` §6.4, BR-022
- `02_Software_Requirements.md` Part 2 §4 (Students)
- `23_Security_Standards.md` §3.4

---

## D-030: One Group Per Student Per Teacher

| Field | Value |
|-------|-------|
| **Decision ID** | D-030 |
| **Status** | CONFIRMED |
| **Title** | One Group Per Student Per Teacher at Any Time |

### Context

A Student may study with multiple Teachers. The Platform must decide whether a Student can belong to multiple Groups under the same Teacher.

### Decision

A Student belongs to only ONE Group per Teacher at any time (BR-002). Group moves close one enrollment period and open another (BR-007). Historical records are preserved.

### Reason

One-Group-per-Teacher simplifies scheduling, attendance tracking, homework assignment, and exam delivery. It creates a clear enrollment boundary that prevents data fragmentation within a Teacher Workspace. Student transfers between Groups preserve all historical data.

### Alternatives Considered

1. **Multiple Groups per Student per Teacher** — Adds complexity to attendance tracking, homework assignment, and report generation without clear educational benefit.
2. **No Group model (direct Student-Teacher only)** — Would not support scheduling, pricing, and cohort-based operations.

### Consequences

- Group assignment enforces one active Group per Student per Teacher.
- Moving a Student between Groups closes the previous enrollment and opens a new one.
- Historical Attendance, Homework, Exams, and grades remain linked to the original enrollment context.
- Reports include data from all enrollment periods.

### Related Documents

- `00_Project_Context.md` §9.1, BR-002, BR-007
- `06_Database_Design.md` §12 (Data Integrity Rules)

---

## D-031: Student Transfer History Preservation

| Field | Value |
|-------|-------|
| **Decision ID** | D-031 |
| **Status** | CONFIRMED |
| **Title** | Student Transfers Preserve All Historical Data |

### Context

When a Student moves between Groups under the same Teacher, the Platform must decide what happens to the Student's historical records.

### Decision

Student transfers preserve historical Attendance, Homework, Exams, and grades. History is never moved, deleted, or rewritten by structural changes (BR-007).

### Reason

Historical data integrity is essential for accurate reporting, Student progress tracking, and Teacher accountability. Deleting or moving historical records during a Group transfer would create data loss and break reporting accuracy.

### Alternatives Considered

1. **Move historical records to new Group** — Would rewrite history and create confusion about which Group the Student was in when the records were created.
2. **Archive old records on transfer** — Would hide relevant historical data from reports.
3. **Delete old records** — Directly violates the Archive and historical data rules (BR-005, BR-014).

### Consequences

- Enrollment records are time-bounded periods.
- Historical records reference the enrollment period and structure as of recording time.
- Reports and history queries work correctly across enrollment changes.
- Group archival does not affect historical Student records.

### Related Documents

- `00_Project_Context.md` §9.3, BR-007, BR-014
- `06_Database_Design.md` §9 (Versioning Strategy)

---

## D-032: Pricing Ownership

| Field | Value |
|-------|-------|
| **Decision ID** | D-030 |
| **Status** | CONFIRMED |
| **Title** | Super Admin Owns Platform Pricing |

### Context

The Platform Subscription model requires pricing to be configured. The question is who controls pricing and what model is used.

### Decision

Pricing is owned by the Super Admin (BR-015). Historical invoices keep the price as of their period. Flat price versus volume tiers remains PENDING (Q-013) and must not be silently assumed.

### Reason

Centralized pricing control by the Super Admin ensures consistent commercial operations and prevents individual Teachers from modifying Platform revenue parameters.

### Alternatives Considered

1. **Teacher-configurable pricing** — Rejected because the Platform Subscription is a SaaS business decision, not a Teacher decision.
2. **Volume tiers** — PENDING; may be adopted in a future decision. The system must be tier-ready.

### Consequences

- Only the Super Admin can configure pricing.
- Historical invoices preserve the price as of their billing period.
- The pricing model (flat vs. tiers) is not hardened until Q-013 is resolved.

### Related Documents

- `00_Project_Context.md` §5.1, BR-015, Q-013
- `17_Subscription_Billing.md`

---

# 4. Lifecycle & Data Integrity Decisions

## D-033: Archive Instead of Deletion

| Field | Value |
|-------|-------|
| **Decision ID** | D-033 |
| **Status** | CONFIRMED |
| **Title** | No Permanent Deletion — Archive Replaces Delete Everywhere |

### Context

The Platform stores educational records with long-term value. Permanent deletion would destroy historical data needed for reporting, Student progress tracking, and accountability.

### Decision

No permanent deletion exists anywhere in the system. Archive replaces deletion for all records, by all actors, everywhere (BR-005). Archived records never appear in normal searches or active selection lists, remain available in reports, and can be restored by authorized users.

### Reason

Permanent deletion destroys data that may be needed for historical reporting, Student transfer history, audit trail integrity, and Teacher accountability. Archive preserves all data while removing it from active workflows, providing the best of both worlds: clean active views and complete historical availability.

### Alternatives Considered

1. **Hard delete with backup** — Recovery is unreliable and backup restoration is all-or-nothing; selective recovery is complex.
2. **Soft delete with periodic purge** — Still eventually loses data; violates the permanent historical retention requirement.
3. **Archive-only, no restore** — Would prevent Teachers from recovering accidentally archived records.

### Consequences

- Archived records are excluded from active searches and dropdown lists.
- Archived records remain available in reports (clearly indicated).
- Archived records can be restored by authorized users.
- Archive and restore actions are recorded in the Audit Log.
- Historical relationships are never detached by archival.
- No role receives hard-delete permission.

### Related Documents

- `00_Project_Context.md` §11 (Archive Policy), BR-005, BR-014
- `06_Database_Design.md` §7, §15
- `23_Security_Standards.md` §2.1

---

## D-034: Immutable Audit Log

| Field | Value |
|-------|-------|
| **Decision ID** | D-034 |
| **Status** | CONFIRMED |
| **Title** | Append-Only, Immutable, Permanent Audit Log |

### Context

The Platform needs a reliable mechanism for tracking important actions across all roles and modules.

### Decision

The Audit Log is a first-class, platform-wide subsystem. Every important action (create, update, archive, restore, login, permission change, Attendance change, Exam modification, Homework modification, Subscription change) produces an Audit Log entry. Entries are append-only, immutable, and permanently retained (BR-006).

### Reason

An immutable Audit Log provides accountability, traceability, forensic capability, and compliance support. It ensures that actions cannot be hidden by editing or deleting log entries. Permanent retention supports long-term historical analysis and incident investigation.

### Alternatives Considered

1. **Mutable log with edit permissions** — Would allow hiding of actions; defeats the purpose of auditing.
2. **Log rotation with archival** — Would eventually lose audit data; violates permanent retention.
3. **External logging service** — Adds infrastructure complexity and costs; not compatible with Version 1 scope.

### Consequences

- Audit Log entries cannot be edited or deleted by any user, including the Super Admin.
- Teacher Staff actions are attributed to the Teacher Staff user, not the Teacher.
- The Audit Log entry is written in the same database transaction as the action it describes.
- Audit Log data grows continuously; the system must be designed for permanent retention.
- Audit Log visibility respects scope boundaries (Teacher Workspace and Platform level).

### Related Documents

- `00_Project_Context.md` §10 (Audit Log Policy), BR-006
- `06_Database_Design.md` §8 (Audit Strategy)
- `23_Security_Standards.md` §15 (Audit Logging)

---

## D-035: Historical Data Permanent Retention

| Field | Value |
|-------|-------|
| **Decision ID** | D-035 |
| **Status** | CONFIRMED |
| **Title** | Historical Data Never Deleted |

### Context

Educational records have long-term value for Students, Parents, Teachers, and the Platform. Historical data must remain available regardless of structural changes.

### Decision

Historical data is never deleted and must always remain available. Reports and history queries include archived records (clearly indicated). Student transfers preserve historical Attendance, Homework, Exams, and grades (BR-014).

### Reason

Historical data supports Student progress tracking, Teacher accountability, Parent monitoring, and platform reporting. Deleting historical data would break reports, lose Student records, and undermine trust in the platform.

### Alternatives Considered

1. **Data retention with expiry** — Would eventually lose valuable educational records.
2. **Summary-only retention** — Would lose the detail needed for individual Student progress tracking.

### Consequences

- Reports include archived records when applicable.
- Historical pricing is preserved as of the billing period.
- Student transfer history is preserved across Group movements.
- File references tied to historical records are retained.
- Database design must accommodate continuous growth.

### Related Documents

- `00_Project_Context.md` §9.3, BR-014
- `06_Database_Design.md` §16 (Data Retention Policy)
- `25_Performance_Scalability.md` §17 (Capacity Planning)

---

## D-036: Flow A and Flow B Separation

| Field | Value |
|-------|-------|
| **Decision ID** | D-036 |
| **Status** | CONFIRMED |
| **Title** | Two Money Flows Never Conflated |

### Context

The Platform involves two distinct financial relationships: Teachers paying the Platform (Subscription) and Students/Parents paying Teachers (fees). Mixing these flows would create reporting confusion and accountability gaps.

### Decision

Flow A (Teacher → Platform Subscription) and Flow B (Student/Parent → Teacher fees) are separate architectural concerns. They must never be conflated in data, logic, reporting, or authorization.

### Reason

Clear separation ensures that Platform revenue (Flow A) is never confused with Teacher income (Flow B). Teachers, Parents, and the Super Admin need distinct views of their respective financial relationships. Mixing flows would undermine financial transparency.

### Alternatives Considered

1. **Unified payment system** — Would create ambiguity about who pays whom and what the Platform is responsible for.
2. **Single "payment" model** — Would not distinguish between SaaS revenue and Teacher income.

### Consequences

- Separate database entities for Flow A and Flow B.
- Separate API endpoints for Subscriptions (Flow A) and payment status (Flow B).
- Separate frontend feature modules for each flow.
- Reports never mix Flow A and Flow B data.
- Labels, routes, query keys, and cache entries must not conflate the two flows.

### Related Documents

- `00_Project_Context.md` §5.2 (Two Distinct Money Flows)
- `17_Subscription_Billing.md`
- `10_API_Design.md` §24–§25

---

# 5. Security Decisions

## D-037: Authentication via Laravel Sanctum

| Field | Value |
|-------|-------|
| **Decision ID** | D-037 |
| **Status** | CONFIRMED |
| **Title** | Laravel Sanctum for Authentication |

### Context

The Platform needs a secure, first-party authentication mechanism for five roles across a React SPA and Laravel backend.

### Decision

Laravel Sanctum is the confirmed authentication technology. It provides session-based authentication for the Web Application and token-based authentication where applicable.

### Reason

Laravel Sanctum is a first-party Laravel package designed specifically for SPA authentication. It provides CSRF protection through the X-XSRF-TOKEN header, session-based authentication with HttpOnly cookies, and lightweight token management. It integrates natively with Laravel's authentication infrastructure.

### Alternatives Considered

1. **Laravel Passport (OAuth2)** — Overkill for a first-party SPA; adds unnecessary complexity with OAuth2 flows.
2. **JWT (JSON Web Tokens)** — Adds token storage, refresh, and revocation complexity; Sanctum's session approach is simpler for SPAs.
3. **Third-party auth service (Auth0, Firebase)** — Adds external dependency and cost; not needed for Version 1.

### Consequences

- Authentication context is established through Sanctum session or token model.
- CSRF protection is handled through Sanctum's SPA authentication flow.
- Session cookies have HttpOnly, Secure, and SameSite flags.
- Successful and failed login events are recorded in the Audit Log.

### Related Documents

- `03_System_Architecture.md` §9 (Authentication Architecture)
- `23_Security_Standards.md` §3

---

## D-038: RBAC with Gates and Policies

| Field | Value |
|-------|-------|
| **Decision ID** | D-038 |
| **Status** | CONFIRMED |
| **Title** | Laravel Gates & Policies with Custom RBAC |

### Context

The Platform needs a role-based access control model that supports five roles, Teacher Workspace isolation, Teacher Staff permission assignment, Student self-scope, and Parent linked-Student scope.

### Decision

Authorization uses Laravel Gates & Policies with Custom RBAC based on project requirements. The logical permission catalog is defined in the Permission Matrix.

### Reason

Laravel Gates and Policies provide the standard Laravel authorization mechanism with fine-grained permission checks. Custom RBAC extends this with role-based access rules specific to the Platform's multi-tenant, multi-role model. This combination supports all confirmed authorization requirements.

### Alternatives Considered

1. **Third-party RBAC package (Spatie, Bouncer)** — Adds dependency; the Platform's RBAC model is specific enough to warrant custom implementation.
2. **Simple role-check middleware** — Too coarse; cannot support Teacher Staff permission assignment or linked-Student scoping.

### Consequences

- Authorization decisions are centralized in Policies and Gates.
- The Permission Matrix defines the logical catalog of all permissions.
- Teacher Staff permissions are assigned by the Teacher within the creating Teacher Workspace.
- Teacher Staff permission granularity remains PENDING (Q-011).

### Related Documents

- `08_RBAC.md`
- `09_Permission_Matrix.md`
- `11_Backend_Architecture.md` §11

---

## D-039: HTTPS Enforcement

| Field | Value |
|-------|-------|
| **Decision ID** | D-039 |
| **Status** | CONFIRMED |
| **Title** | HTTPS Required in Production |

### Context

The Platform handles sensitive data including Student records, Teacher content, authentication credentials, and payment status.

### Decision

HTTPS is required in production for all Platform communication. HTTP requests must be redirected to HTTPS. Session cookies must have the Secure flag set. CSRF cookies must be transmitted over HTTPS.

### Reason

HTTPS encrypts data in transit, preventing eavesdropping, man-in-the-middle attacks, and credential theft. It is a fundamental security requirement for any web application handling personal and educational data.

### Alternatives Considered

1. **HTTP with selective HTTPS** — Would leave some endpoints unprotected; not acceptable for an educational platform.
2. **HTTPS optional** — Would create inconsistent security posture.

### Consequences

- An SSL certificate must be installed on the production server.
- The APP_URL environment variable must use the HTTPS scheme.
- All API endpoints must be served over HTTPS.
- Security headers (HSTS, X-Content-Type-Options, X-Frame-Options) must be configured.

### Related Documents

- `23_Security_Standards.md` §8.1
- `26_Deployment_Plan.md` §22 (SSL Requirements)

---

## D-040: Database Session Driver

| Field | Value |
|-------|-------|
| **Decision ID** | D-040 |
| **Status** | CONFIRMED |
| **Title** | Database Session Driver for Session Management |

### Context

The Platform needs a session storage mechanism that supports shared hosting, session isolation, and cleanup.

### Decision

Version 1 uses the Database session driver. Session data is stored in MySQL 8.

### Reason

The Database session driver stores sessions in MySQL, which is available on all cPanel Shared Hosting accounts. It supports shared hosting where file-based sessions may have locking issues across multiple PHP processes. It enables session cleanup through standard database operations.

### Alternatives Considered

1. **File session driver** — May have locking issues on shared hosting with concurrent PHP processes.
2. **Redis session driver** — Not available on cPanel Shared Hosting.
3. **Cookie-based sessions** — Limited size; sensitive data in cookies increases security risk.

### Consequences

- Sessions are stored in a MySQL table.
- Expired sessions must be cleaned up periodically.
- Session isolation per user and per role context is enforced.
- No Redis dependency is introduced.

### Related Documents

- `23_Security_Standards.md` §7
- `26_Deployment_Plan.md` §8.2

---

## D-041: File Cache Driver

| Field | Value |
|-------|-------|
| **Decision ID** | D-041 |
| **Status** | CONFIRMED |
| **Title** | File Cache for Application Caching |

### Context

The Platform needs a caching mechanism for frequently accessed, slowly changing data.

### Decision

Version 1 uses File Cache as the official cache driver.

### Reason

File Cache is compatible with cPanel Shared Hosting and does not require additional infrastructure. It is sufficient for caching reference data, configuration, and dashboard summaries.

### Alternatives Considered

1. **Redis cache** — Not available on cPanel Shared Hosting; deferred to VPS/Cloud migration.
2. **Memcached** — Not available on all cPanel accounts.
3. **No cache** — Would result in unnecessary database queries for slowly changing data.

### Consequences

- Cache entries are stored as files in the cache directory.
- Cache entries must respect scope boundaries (Teacher Workspace, Student, Platform).
- Cache must be invalidated when underlying data changes.
- Sensitive data must not be stored in cache.
- Redis may be considered after VPS/Cloud migration.

### Related Documents

- `25_Performance_Scalability.md` §8 (Caching Strategy)
- `26_Deployment_Plan.md` §17 (Cache Configuration)

---

## D-042: Database Queue

| Field | Value |
|-------|-------|
| **Decision ID** | D-042 |
| **Status** | CONFIRMED |
| **Title** | Database Queue for Background Jobs |

### Context

The Platform needs a background job processing mechanism for report preparation, Billing Cycle processing, Exam grading, and maintenance tasks.

### Decision

Version 1 uses the Laravel Database Queue driver. Jobs are stored in the MySQL 8 database and processed by a queue worker triggered through cPanel Cron Jobs.

### Reason

The Database Queue driver is compatible with cPanel Shared Hosting and does not require additional infrastructure (Redis, SQS, Beanstalkd). It provides reliable job processing with MySQL as the storage backend.

### Alternatives Considered

1. **Redis Queue** — Not available on cPanel Shared Hosting.
2. **SQS** — Requires AWS infrastructure; not part of Version 1 scope.
3. **Synchronous processing** — Would block user requests for long-running tasks.

### Consequences

- Queue jobs are stored in a MySQL table.
- Processed jobs must be cleaned up periodically.
- Jobs must respect cPanel execution time limits.
- Long-running jobs must be chunked into smaller batches.
- High-priority queues (billing, grading) are processed before low-priority queues (reports, cleanup).

### Related Documents

- `21_Background_Jobs.md`
- `26_Deployment_Plan.md` §15 (Queue Configuration)

---

## D-043: Laravel Public Storage

| Field | Value |
|-------|-------|
| **Decision ID** | D-043 |
| **Status** | CONFIRMED |
| **Title** | Laravel Public Storage for File Storage |

### Context

The Platform stores Lesson videos, Homework files, and Student submissions. File storage must be compatible with cPanel Shared Hosting.

### Decision

Version 1 uses Laravel Public Storage for all file storage. Application-level authorization and ownership checks control access to stored files.

### Reason

Laravel Public Storage is the standard file storage mechanism for cPanel Shared Hosting. It provides file organization through directory structure and integrates natively with Laravel's storage API. Application-level authorization ensures that file privacy is enforced regardless of the storage backend.

### Alternatives Considered

1. **S3 Storage** — Requires AWS infrastructure; not available on cPanel Shared Hosting by default.
2. **Local private storage** — More complex to configure on cPanel; public storage with application-level auth is simpler.
3. **CDN-based storage** — Requires additional infrastructure.

### Consequences

- Files are stored in `storage/app/public/` with workspace-scoped directory structure.
- A symbolic link or cPanel mapping exposes the storage directory through the web server.
- Every file request must pass through backend authorization.
- Cross-Teacher file access is denied.
- S3 Storage may be considered after VPS/Cloud migration.

### Related Documents

- `20_File_Storage.md`
- `04_Project_Structure.md` §5 (Storage Structure)
- `26_Deployment_Plan.md` §13 (Storage Configuration)

---

# 6. Deployment Decisions

## D-044: cPanel Shared Hosting as Primary Deployment Target

| Field | Value |
|-------|-------|
| **Decision ID** | D-044 |
| **Status** | CONFIRMED |
| **Title** | cPanel Shared Hosting for Version 1 |

### Context

The Platform needs a hosting environment that is accessible, affordable, and compatible with the confirmed technology stack.

### Decision

cPanel Shared Hosting is the primary Version 1 deployment target. VPS / Cloud is the future deployment target.

### Reason

cPanel Shared Hosting is widely available, affordable, and provides all the infrastructure needed for Version 1 (PHP 8.3, MySQL 8, Apache/LiteSpeed, Cron Jobs, SSL). It enables rapid deployment without infrastructure management overhead. The architecture is designed to be migration-ready for VPS/Cloud when needed.

### Alternatives Considered

1. **VPS from launch** — Higher cost, more infrastructure management, and not needed for Version 1's initial user base.
2. **Cloud (AWS, DigitalOcean)** — Higher complexity and cost; infrastructure management overhead not justified for Version 1.
3. **PaaS (Heroku, Railway)** — Adds platform dependency and may not support all Laravel features needed.

### Consequences

- Version 1 must not require Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices.
- Database Queue, File Cache, and Database sessions are the standard infrastructure choices.
- Laravel Scheduler runs through cPanel Cron Jobs.
- Deployment is repeatable and documented.
- Future VPS/Cloud migration path is preserved.

### Related Documents

- `03_System_Architecture.md` §4.1, §20
- `26_Deployment_Plan.md` §3

---

## D-045: Environment Separation

| Field | Value |
|-------|-------|
| **Decision ID** | D-045 |
| **Status** | CONFIRMED |
| **Title** | Three Environments: Local, Staging, Production |

### Context

The development and release process requires separate environments for development, pre-release validation, and live operation.

### Decision

Version 1 uses three environments: Local Development, Staging, and Production. Each environment has its own database, configuration, and storage paths. Production data is never copied to staging or local without sanitization.

### Reason

Separate environments prevent development work from affecting live users, enable pre-release validation, and support rollback procedures. Staging mirrors production configuration for accurate testing.

### Alternatives Considered

1. **Two environments (local + production)** — No pre-release validation increases production risk.
2. **Four environments (local + CI + staging + production)** — CI can be added later; three environments are sufficient for Version 1.

### Consequences

- Each environment has its own `.env` file with environment-specific values.
- Staging uses a separate database and subdomain.
- Test data is clearly identifiable and separable from real user data.
- Staging must be validated before every production deployment.

### Related Documents

- `26_Deployment_Plan.md` §3.3
- `24_Testing_Strategy.md` §19 (Testing Environments)

---

# 7. Development Process Decisions

## D-046: Documentation-First Development

| Field | Value |
|-------|-------|
| **Decision ID** | D-046 |
| **Status** | CONFIRMED |
| **Title** | Architecture and Documentation Come Before Code |

### Context

The Project needs a disciplined development approach that prevents scope creep, maintains consistency, and ensures every feature traces to a confirmed requirement.

### Decision

Architecture and documentation come before code. Every feature traces to the canonical document set (AI_DOCS/). No feature is implemented without a confirmed requirement, and no business rule is silently assumed.

### Reason

Documentation-first development ensures that product decisions are deliberate, reviewable, and traceable. It prevents scope creep by requiring formal confirmation before implementation. It provides a shared understanding for all team members and AI sessions.

### Alternatives Considered

1. **Code-first with documentation after** — Leads to undocumented decisions, inconsistencies, and scope creep.
2. **Agile sprints without formal documentation** — Would not provide the consistency and traceability needed for a multi-tenant SaaS platform with complex business rules.

### Consequences

- The canonical document set (AI_DOCS/00 through AI_DOCS/30) is the authoritative reference for all development.
- `00_Project_Context.md` is the Single Source of Truth; conflicts are resolved in its favor.
- PENDING items must not be silently assumed.
- Canonical terminology must be used consistently.

### Related Documents

- `00_Project_Context.md` §17 (Collaboration Protocol)
- `27_Development_Roadmap.md` §2 (Development Philosophy)

---

## D-047: Phased Development Plan

| Field | Value |
|-------|-------|
| **Decision ID** | D-047 |
| **Status** | CONFIRMED |
| **Title** | Ten-Phase Development Roadmap |

### Context

The Platform has a large feature surface across five roles. Development must be sequenced to minimize dependency risk and produce testable increments.

### Decision

Development follows a ten-phase roadmap: Phase 1 (Foundation), Phase 2 (Authentication & RBAC), Phase 3 (Teacher Workspace), Phase 4 (Student & Parent), Phase 5 (Attendance), Phase 6 (Homework), Phase 7 (Exam Engine), Phase 8 (Reporting), Phase 9 (Subscription & Billing), Phase 10 (Optimization & Release Readiness).

### Reason

Phased development in dependency order ensures that foundational layers (authentication, authorization, database schema, tenant isolation) are built and validated before domain features that depend on them. Each phase produces a testable increment.

### Alternatives Considered

1. **Feature-by-feature vertical slices** — Would require building infrastructure repeatedly; less efficient for a platform with many shared cross-cutting concerns.
2. **No phases (build everything at once)** — Too risky; would not provide incremental validation.

### Consequences

- Each phase has clear scope, deliverables, and acceptance criteria.
- Testing milestones define quality gates at each phase boundary.
- Deployment milestones ensure staging is updated at each phase.
- Documentation milestones verify consistency at key checkpoints.

### Related Documents

- `27_Development_Roadmap.md`

---

## D-048: Canonical Terminology

| Field | Value |
|-------|-------|
| **Decision ID** | D-048 |
| **Status** | CONFIRMED |
| **Title** | Mandatory Canonical Terminology Across All Artifacts |

### Context

The Platform involves concepts that may be referred to by different names. Inconsistent terminology creates confusion in code, documentation, and user interfaces.

### Decision

The following canonical terms are mandatory across every document, interface, code artifact, and conversation:

| Canonical Term | Avoid |
|----------------|-------|
| Teacher Workspace | "tenant" (in UI/product contexts) |
| Educational Grade | "Class" |
| Teaching Subject | "Course" |
| Group | — |
| Lesson | "Course" |
| Archive | "Delete" |
| Subscription | (Flow A only) |
| payment status | (Flow B only) |
| Dynamic QR Code | — |
| ID Card | — |
| Question Bank | — |
| Bubble Sheet | — |
| Student Switcher | — |
| Billable Student | — |
| Billing Cycle | — |
| Homework | — |

### Reason

Consistent terminology prevents miscommunication between product owners, designers, developers, and users. It ensures that code, documentation, APIs, and UI all refer to the same concepts with the same names.

### Alternatives Considered

1. **Allow domain-specific aliases** — Would create confusion when code uses one term and the UI uses another.
2. **Use technical names everywhere** — Would not be user-friendly for Teachers, Students, and Parents.

### Consequences

- All code variables, class names, API endpoints, database concepts, and documentation use canonical terms.
- The term "Educational Grade" is used instead of "Class" everywhere.
- The term "Lesson" is used instead of "Course" everywhere.
- "Archive" is used instead of "Delete" in all product-facing contexts.

### Related Documents

- `00_Project_Context.md` §19 (Canonical Terminology)

---

# 8. Scope Exclusion Decisions

## D-049: Native Mobile Excluded from V1

| Field | Value |
|-------|-------|
| **Decision ID** | D-049 |
| **Status** | CONFIRMED |
| **Title** | Native Mobile Application Out of Scope for V1 |

### Context

Students may want to use mobile devices for Attendance scanning and other features. The question is whether to build a native mobile app for Version 1.

### Decision

Version 1 is a Web Application only. Native mobile applications are out of scope. All V1 capabilities, including daily Dynamic QR Code attendance scanning, are delivered through the web application (BR-017).

### Reason

Native mobile applications require separate development effort (iOS, Android), app store submission, different authentication flows, and additional maintenance. The Web Application provides all V1 functionality through the browser, including camera-based QR scanning.

### Alternatives Considered

1. **Native iOS and Android apps** — Deferred to a future version with separate architecture decisions.
2. **Progressive Web App (PWA)** — Could be considered as a future enhancement; not a V1 requirement.
3. **Hybrid app (React Native, Flutter)** — Deferred to a future version.

### Consequences

- No mobile-specific code exists in Version 1.
- QR Code scanning uses browser camera APIs.
- All UI is responsive web design.
- Mobile support may be added in a future version with separate approval.

### Related Documents

- `00_Project_Context.md` §4.2, BR-017
- `02_Software_Requirements.md` Part 6 §15–§16

---

## D-050: Marketplace Behavior Excluded

| Field | Value |
|-------|-------|
| **Decision ID** | D-050 |
| **Status** | CONFIRMED |
| **Title** | Not an Online Course Marketplace |

### Context

Some educational platforms allow Teachers to sell courses to unknown Students. The Platform must decide its commercial model.

### Decision

The Platform is NOT an online course marketplace. Teachers do NOT sell courses through the platform. There is no course discovery or browsing across Teachers, and there is no mechanism by which one Teacher's content reaches another Teacher's Students.

### Reason

The Platform's value proposition is private Teacher-led education with unified Student and Parent accounts. Marketplace behavior would contradict Teacher Workspace isolation, private content ownership, and the existing Teacher-Student relationship model.

### Alternatives Considered

1. **Marketplace with opt-in** — Would create complex privacy and content ownership boundaries; fundamentally different product model.
2. **Teacher directory** — Would expose Teacher information for discovery; contradicts privacy-first approach.

### Consequences

- No marketplace endpoints, database entities, or UI components exist.
- Lessons are private to each Teacher's Students.
- Question Banks are private to each Teacher Workspace.
- No cross-Teacher content browsing exists.
- The term "Course" is never used for Lesson content.

### Related Documents

- `00_Project_Context.md` §4.1 (Explicit Non-Goals)

---

## D-051: Multiple Subjects Per Teacher Excluded from V1

| Field | Value |
|-------|-------|
| **Decision ID** | D-051 |
| **Status** | CONFIRMED |
| **Title** | One Teaching Subject Per Teacher Account for V1 |

### Context

Some Teachers may teach multiple subjects. The Platform must decide whether to support multiple subjects under one account.

### Decision

Version 1 supports exactly one Teaching Subject per Teacher account (BR-016). If a Teacher wants to teach another subject, a separate Teacher account must be created.

### Reason

One subject per account simplifies workspace organization, content management, and Student-facing views. It avoids mixing Question Banks, Lessons, and content from different subjects within one workspace.

### Related Documents

- `00_Project_Context.md` §8, BR-016

---

# 9. PENDING Decisions (Not Silently Assumed)

The following decisions remain PENDING and must not be hardened as confirmed rules. They are documented here to preserve transparency about unresolved topics.

## Q-005: Non-Payment Enforcement

| Field | Value |
|-------|-------|
| **Question ID** | Q-005 |
| **Status** | PENDING |
| **Title** | Non-Payment Enforcement Behavior |

### Context

When a Teacher does not pay their Platform Subscription, the Platform must decide how to enforce payment.

### Proposed Default

7-day grace period → Teacher Workspace read-only; Students keep read access; nothing auto-archives.

### Why PENDING

The enforcement ladder (grace period duration, read-only scope, reactivation behavior) requires Product Owner confirmation. The proposed default is a working assumption that must not be silently implemented as confirmed behavior.

### Related Documents

- `00_Project_Context.md` §15.1

---

## Q-010: Lesson Video Hosting and Protection

| Field | Value |
|-------|-------|
| **Question ID** | Q-010 |
| **Status** | PENDING |
| **Title** | Lesson Video Hosting and Protection Mechanism |

### Context

Lesson videos are Teacher-owned and private. The Platform must decide how to host, protect, and deliver video content.

### Proposed Default

Private storage with signed short-lived playback URLs; streaming-only; per-Teacher quota.

### Why PENDING

Video hosting involves streaming, signed URL generation, storage quota management, and potentially third-party video services. These details require Product Owner and technical confirmation.

### Related Documents

- `00_Project_Context.md` §15.1
- `20_File_Storage.md`

---

## Q-011: Teacher Staff Permission Granularity

| Field | Value |
|-------|-------|
| **Question ID** | Q-011 |
| **Status** | PENDING |
| **Title** | Teacher Staff Permission Model Granularity |

### Context

Teacher Staff hold only permissions assigned by the Teacher. The Platform must decide the granularity of the permission model.

### Proposed Default

Fixed capability-flag catalog per module; saveable named presets.

### Why PENDING

Permission granularity affects the RBAC implementation, the Teacher Staff management UI, and the authorization logic. The proposed default must be formally confirmed before implementation.

### Related Documents

- `00_Project_Context.md` §15.1
- `08_RBAC.md`
- `09_Permission_Matrix.md`

---

## Q-012: Super Admin Content Visibility

| Field | Value |
|-------|-------|
| **Question ID** | Q-012 |
| **Status** | PENDING |
| **Title** | Super Admin Visibility into Teacher-Private Content |

### Context

The Super Admin manages the Platform at the Platform level. The question is whether the Super Admin can see Teacher-private content (Lessons, Question Banks, Homework, Exams, Student records).

### Proposed Default

Aggregates, finances, and metadata only; no browsing of Teacher-private content.

### Why PENDING

Content visibility boundaries affect authorization logic, API design, and the Super Admin's operational capabilities. The proposed default must be formally confirmed.

### Related Documents

- `00_Project_Context.md` §15.1
- `08_RBAC.md` §9

---

## Q-013: Flat Price vs. Volume Tiers

| Field | Value |
|-------|-------|
| **Question ID** | Q-013 |
| **Status** | PENDING |
| **Title** | Pricing Model — Flat Price or Volume Tiers |

### Context

The Super Admin owns pricing. The question is whether the pricing model uses a flat price per Student or volume-based tiers.

### Proposed Default

Flat price per Student at launch; tier-ready engine.

### Why PENDING

The pricing model affects the Subscription calculation, billing reports, and platform settings UI. The proposed default must be formally confirmed.

### Related Documents

- `00_Project_Context.md` §15.1, BR-015

---

## Q-015: Languages, Timezone, Currency

| Field | Value |
|-------|-------|
| **Question ID** | Q-015 |
| **Status** | PENDING |
| **Title** | Localization and Regional Configuration |

### Context

The Platform may serve multiple markets with different languages, timezones, and currencies.

### Proposed Default

Arabic-first with full RTL + English; i18n architecture from day one; per-Teacher timezone; platform-level display currency for Flow B.

### Why PENDING

Localization decisions affect UI design, date/time handling, currency display, and the overall user experience. The proposed default must be formally confirmed by the Product Owner.

### Related Documents

- `00_Project_Context.md` §14, §15.1

---

# 10. Decision Index

The following table provides a quick-reference index of all decisions in this document.

| Decision ID | Title | Status | Section |
|-------------|-------|--------|---------|
| D-001 | Technology Stack Selection | CONFIRMED | §1 |
| D-002 | V1 Payment Handling (Status-Only) | CONFIRMED | §3 |
| D-003 | Subscription Invoicing as Immutable Snapshots | PROPOSED | — |
| D-004 | Non-Payment Enforcement Ladder | PROPOSED | — |
| D-005 | Super Admin Privacy Boundary | PROPOSED | — |
| D-006 | Calendar-Month Billing Cycle | CONFIRMED | §3 |
| D-007 | Billable Student >15 Days Enrollment Only | CONFIRMED | §3 |
| D-008 | One Teaching Subject Per Teacher | CONFIRMED | §3 |
| D-009 | One Parent Per Student (V1) | CONFIRMED | §3 |
| D-010 | Bubble Sheet = Electronic On-Screen + Auto-Grading | CONFIRMED | §3 |
| D-011 | Homework = Text, Image, PDF Only | CONFIRMED | §3 |
| D-012 | Notifications Out of Scope for V1 | CONFIRMED | §3 |
| D-013 | Two Student Registration Methods | CONFIRMED | §3 |
| D-014 | PHP 8.3 Runtime | CONFIRMED | §1 |
| D-015 | Vite as Frontend Build Tool | CONFIRMED | §1 |
| D-016 | Tailwind CSS as Styling System | CONFIRMED | §1 |
| D-017 | React Router for Client-Side Routing | CONFIRMED | §1 |
| D-018 | TanStack Query for Server-State | CONFIRMED | §1 |
| D-019 | React Hook Form + Zod for Forms | CONFIRMED | §1 |
| D-020 | Multi-Tenant Architecture | CONFIRMED | §2 |
| D-021 | Backend-Only Authorization Enforcement | CONFIRMED | §2 |
| D-022 | REST API with Versioned Endpoints | CONFIRMED | §2 |
| D-023 | Modular Monolith (Not Microservices) | CONFIRMED | §2 |
| D-024 | Feature-Based Frontend Organization | CONFIRMED | §2 |
| D-025 | Layered Backend Architecture | CONFIRMED | §2 |
| D-030 | One Group Per Student Per Teacher | CONFIRMED | §3 |
| D-031 | Student Transfers Preserve History | CONFIRMED | §3 |
| D-032 | Super Admin Owns Pricing | CONFIRMED | §3 |
| D-033 | Archive Replaces Delete Everywhere | CONFIRMED | §4 |
| D-034 | Immutable Audit Log | CONFIRMED | §4 |
| D-035 | Historical Data Permanent Retention | CONFIRMED | §4 |
| D-036 | Flow A / Flow B Never Conflated | CONFIRMED | §4 |
| D-037 | Laravel Sanctum for Authentication | CONFIRMED | §5 |
| D-038 | Laravel Gates & Policies + Custom RBAC | CONFIRMED | §5 |
| D-039 | HTTPS Required in Production | CONFIRMED | §5 |
| D-040 | Database Session Driver | CONFIRMED | §5 |
| D-041 | File Cache Driver | CONFIRMED | §5 |
| D-042 | Database Queue for Background Jobs | CONFIRMED | §5 |
| D-043 | Laravel Public Storage for Files | CONFIRMED | §5 |
| D-044 | cPanel Shared Hosting (V1 Primary) | CONFIRMED | §6 |
| D-045 | Three Environments (Local, Staging, Production) | CONFIRMED | §6 |
| D-046 | Documentation-First Development | CONFIRMED | §7 |
| D-047 | Ten-Phase Development Roadmap | CONFIRMED | §7 |
| D-048 | Mandatory Canonical Terminology | CONFIRMED | §7 |
| D-049 | Native Mobile Out of Scope (V1) | CONFIRMED | §8 |
| D-050 | Not an Online Course Marketplace | CONFIRMED | §8 |
| D-051 | One Subject Per Teacher (V1) | CONFIRMED | §8 |

| Question ID | Title | Status |
|-------------|-------|--------|
| Q-005 | Non-Payment Enforcement | PENDING |
| Q-010 | Lesson Video Hosting/Protection | PENDING |
| Q-011 | Teacher Staff Permission Granularity | PENDING |
| Q-012 | Super Admin Content Visibility | PENDING |
| Q-013 | Flat Price vs. Volume Tiers | PENDING |
| Q-015 | Timezone/Currency | PENDING |

---

# 11. Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|-------------|--------|
| Project Context alignment | Passed — all decisions are derived from confirmed rules in `00_Project_Context.md`. No new business rules are invented. |
| Decision ID continuity | Passed — existing D-001 through D-013 from the Project Context are preserved. New decisions (D-014 onward) continue the numbering. |
| PENDING item preservation | Passed — Q-005, Q-010, Q-011, Q-012, Q-013, and Q-015 are documented as PENDING with proposed defaults clearly labeled. No PENDING item is silently hardened. |
| BR reference accuracy | Passed — all Business Rule references (BR-001 through BR-022) are accurate and consistent with the Project Context. |
| No invented rules | Passed — every decision traces to a confirmed statement in the canonical document set. |
| No source code | Passed — no source code, APIs, database tables, SQL, or UI implementation is defined. |
| No API definitions | Passed — API design decisions reference `10_API_Design.md` without duplicating endpoint specifications. |
| No database tables | Passed — database architecture decisions reference `06_Database_Design.md` without defining physical schema. |
| Canonical terminology | Passed — Teacher Workspace, Educational Grade, Teaching Subject, Group, Student, Parent, Teacher Staff, Super Admin, Subscription, Flow A, Flow B, Enrollment, Archive, Audit Log, Dynamic QR Code, ID Card, Question Bank, Bubble Sheet, Student Switcher, Lesson, Billable Student, Billing Cycle, and Homework are used consistently. |
| Technology stack consistency | Passed — all technology references (Laravel 12, PHP 8.3, React 19, TypeScript, Vite, Tailwind CSS, MySQL 8, Laravel Sanctum, Laravel Gates & Policies, Custom RBAC, File Cache, Database Queue, Database sessions, Laravel Public Storage, Laravel Scheduler, Cron Jobs, SMTP, Apache/LiteSpeed, cPanel Shared Hosting) are consistent across all documents. |
| Version 1 scope exclusions | Passed — native mobile, online payment gateways, notifications, multiple Teaching Subjects, marketplace behavior, video homework, Docker, Redis, Kubernetes, S3 Storage, WebSockets, and Microservices are consistently excluded from V1. |
| cPanel compatibility | Passed — all infrastructure decisions are compatible with cPanel Shared Hosting. No decision introduces requirements that exceed shared hosting capabilities. |
| Flow A / Flow B separation | Passed — financial flow separation is consistently preserved across all relevant decisions. |
| Teacher Workspace isolation | Passed — tenant isolation is consistently enforced and preserved across all architectural and security decisions. |
| Student account rules | Passed — one global account, duplicate prevention, per-Teacher partitioning, and two registration methods are consistently referenced. |
| Parent access rules | Passed — linked-Student read-only access and one Parent per Student are consistently referenced. |
| Archive policy | Passed — no decision references permanent deletion. Archive replaces deletion everywhere. |
| Audit Log policy | Passed — immutability, append-only property, permanent retention, and mandatory events are consistently referenced. |
| History preservation | Passed — Student transfer history, archived record availability, and historical data retention are consistently referenced. |

---

*End of document. **REVISION 1.0** — This file records all important architectural and business decisions for the Unified Education Platform Version 1. Docs before code; consistency over convenience; Archive — never delete.*


## Confirmed Audit Clarifications

The following confirmed clarifications are owned by `00_Project_Context.md` §9.9 (BR-023 through BR-025). They do not alter Flow A, Archive, Audit Log, or the external-payment boundary.

- **Parent workflow:** a Parent creates an account through Parent registration and submits a Parent–Student link request. The Teacher responsible for the Student’s active Enrollment approves or rejects it. Approval is allowed only when the Student has no other linked Parent; it creates read-only access. The Parent may request unlinking, and that Teacher approves the unlink. Request, approval, rejection, and unlinking are Audit Log events; historical records remain preserved.
- **Per Lesson Flow B:** a billable lesson is a Lesson completed for a Group while the Student has an active Enrollment in that Group. The obligation is recorded on completion at the Group’s recorded Price. Drafting, scheduling, publishing, viewing, Attendance, or assignment alone is not billable; one Student has at most one obligation for the same completed lesson. The Platform records payment status only, and payment remains outside the Platform.
- **Group transfer:** Flow A enrollment duration is accumulated across Groups of the same Teacher in the same Billing Cycle; a transfer does not reset the more-than-15-calendar-days test. Flow B obligations remain tied to the Group and completed Lesson that created them.
