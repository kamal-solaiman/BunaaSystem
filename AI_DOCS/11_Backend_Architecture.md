# 11 — Backend Architecture

## Document Scope

This document defines the backend architecture for Version 1 of the Unified Education Platform.

Source-of-truth documents reviewed before authoring:

- `AI_DOCS/00_Project_Context.md`
- `AI_DOCS/01_Project_Vision.md`
- `AI_DOCS/02_Software_Requirements.md`
- `AI_DOCS/03_System_Architecture.md`
- `AI_DOCS/06_Database_Design.md`
- `AI_DOCS/07_Data_Dictionary.md`
- `AI_DOCS/08_RBAC.md`
- `AI_DOCS/09_Permission_Matrix.md`
- `AI_DOCS/10_API_Design.md`

This document describes backend architecture only. It does not define source code, database tables, migrations, API implementation, Laravel controller code, or physical schema.

## Target Backend Stack

| Area | Version 1 Standard |
|---|---|
| Backend Framework | Laravel 12 |
| Runtime | PHP 8.3 |
| Database | MySQL 8 |
| Authentication | Laravel Sanctum |
| Authorization | Laravel Gates & Policies with Custom RBAC |
| Primary Hosting Target | cPanel Shared Hosting |
| Cache | File Cache |
| Queue | Database Queue |
| Session Driver | Database |
| Storage | Laravel Public Storage |
| Scheduler | Laravel Scheduler with Cron Jobs |
| Mail Transport Baseline | SMTP |
| Web Server | Apache or LiteSpeed |

Version 1 must not require Docker, Redis, Kubernetes, S3 Storage, WebSockets, Microservices, native mobile applications, online payment gateways, or notification features.

---

# 1. Backend Overview

The backend is a Laravel 12 modular monolith running on PHP 8.3 and optimized for cPanel Shared Hosting. It serves the React 19 Web Application through REST API endpoints defined in `AI_DOCS/10_API_Design.md`.

The backend owns final enforcement of:

- Authentication.
- Authorization.
- Teacher Workspace isolation.
- Parent read-only access.
- Student account ownership.
- Teacher Staff assigned permissions.
- Archive and restore behavior.
- Audit Log recording.
- Flow A and Flow B separation.
- File ownership and access control.
- Validation and business-rule enforcement.

The backend must never trust the frontend as the final security authority. All sensitive decisions must be enforced server-side.

The backend is intentionally not a microservice system. Laravel 12 provides the application framework, request handling, validation, authorization, background jobs, scheduling, file storage integration, logging, and database integration needed for Version 1.

---

# 2. Folder Structure

The backend folder structure should follow Laravel conventions while organizing business logic by feature area.

This section is architectural guidance only. It does not define source code or implementation files.

| Area | Responsibility |
|---|---|
| Application HTTP layer | Routes, controllers, middleware, request validation, and response coordination. |
| Domain feature areas | Business workflows grouped by Platform, Teacher Workspace, Student, Parent, Attendance, Homework, Lessons, Exams, Reports, Payments, Subscriptions, Users, Settings, Files, Archive, and Audit Log. |
| Services | Business operations and workflow orchestration. |
| Repositories | Persistence-oriented access abstraction where useful for complex querying or tenant-scoped retrieval. |
| Models | Laravel model layer representing business entities without exposing database implementation details in documentation. |
| Policies and Gates | Authorization rules and ownership checks. |
| Form Requests | Request validation and authorization pre-checks. |
| Jobs | Database Queue jobs for asynchronous or deferred work compatible with cPanel Shared Hosting. |
| Console Commands | Scheduled or administrative backend tasks used by Laravel Scheduler. |
| Storage | Laravel Public Storage integration and file reference management. |
| Exceptions | Standardized error handling and API error responses. |
| Logging and Audit | Operational logging and business Audit Log recording. |

Recommended high-level organization should preserve Laravel defaults and avoid excessive abstraction. Feature separation should improve maintainability without creating unnecessary complexity.

---

# 3. Feature-Based Organization

The backend should be organized around confirmed product features rather than technical layers alone.

Recommended feature areas:

| Feature Area | Backend Responsibility |
|---|---|
| Identity and Authentication | Login, logout, current user context, Student self-registration, Teacher-created Student activation. |
| Authorization and RBAC | Role checks, permissions, policies, gates, ownership, Teacher Staff permission assignments. |
| Platform Administration | Super Admin management of Teachers, Flow A Subscriptions, pricing, Platform Settings, global reports, and Platform Audit Logs. |
| Teacher Workspace | Workspace-scoped operations for Teachers and Teacher Staff. |
| Educational Grades | Teacher-created academic levels inside one Teacher Workspace. |
| Groups | Group management, schedule, Price, Pricing Type, and Student Group movement. |
| Students | Student relationships with Teacher Workspaces, duplicate prevention, assignment, movement, and activation support. |
| Parents | Parent linked-Student read-only monitoring. |
| Attendance | Dynamic QR Code, ID Card scan, manual Attendance, and Attendance history. |
| Homework | Homework creation, submission, grading/review, supported file formats, and history. |
| Lessons | Teacher-owned private Lesson video metadata and file references. |
| Exams | Question Bank, Questions, Exams, attempts, answers, grading, and Bubble Sheet support. |
| Reports | Role-appropriate reporting with archived records clearly indicated where applicable. |
| Payments | Flow A and Flow B payment-status recording without payment processing. |
| Subscriptions | Flow A Teacher Platform Subscription, Billing Cycle, Billable Student calculation. |
| Users | Teacher Staff management and permission assignment. |
| Settings | Platform Settings, Teacher Workspace Settings, Student account Settings, Parent account context. |
| Files | File upload, file references, ownership, access control, and historical retention. |
| Archive | Archive and restore policy enforcement across resources. |
| Audit Log | Append-only permanent record of important actions. |

Feature-based organization must preserve canonical terminology. The backend should use **Educational Grade**, not non-canonical wording, and **Lesson**, not Course.

---

# 4. Request Lifecycle

A typical backend request lifecycle is:

1. The browser-based Web Application sends a request to the Laravel backend.
2. Laravel receives the request through the routing layer.
3. Authentication context is resolved through Laravel Sanctum where required.
4. The active role context is determined.
5. Tenant or access scope is resolved:
   - Platform scope for Super Admin.
   - Teacher Workspace scope for Teacher and Teacher Staff.
   - Student account and Teacher relationship scope for Student.
   - Parent linked-Student scope for Parent.
6. Middleware applies general access constraints.
7. Form Request validation checks input shape and basic authorization where appropriate.
8. Policies, Gates, and Custom RBAC enforce permission, ownership, and role boundaries.
9. Controllers coordinate the request without owning complex business logic.
10. Services execute business workflows.
11. Repositories or models retrieve and persist authorized data.
12. Archive rules, Audit Log rules, and history preservation rules are applied.
13. Files, queue jobs, or scheduler-related operations are delegated where needed.
14. The backend returns a standardized success or error response.

The backend must reject invalid, unauthorized, cross-Teacher, out-of-scope, or unsupported requests without exposing restricted data.

---

# 5. Routing Strategy

The routing strategy must align with the REST API specification and Laravel 12 conventions.

Routing principles:

1. All Version 1 REST endpoints are grouped under `/api/v1`.
2. Routes are grouped by role scope and feature area.
3. Platform Administration routes are reserved for Super Admin access.
4. Teacher Workspace routes are reserved for Teachers and authorized Teacher Staff inside the current Teacher Workspace.
5. Student routes are reserved for the authenticated Student's own account and own per-Teacher records.
6. Parent routes are reserved for linked-Student read-only access.
7. Archive and restore use explicit action endpoints because permanent deletion is not available.
8. Notification routes are not defined in Version 1.
9. Payment processing and gateway routes are not defined in Version 1.
10. Marketplace and course discovery routes are not defined in Version 1.

Routing must not implement business decisions by URL structure alone. Authorization must still be enforced by middleware, policies, gates, and Custom RBAC.

---

# 6. Controllers

Controllers act as thin request coordinators.

Controller responsibilities:

- Receive validated requests.
- Use authenticated role and scope context.
- Call the appropriate service layer operation.
- Return standardized API responses.
- Avoid embedding complex business rules directly.
- Avoid direct cross-feature data access.
- Avoid bypassing authorization or validation.

Controllers should be grouped by feature and role scope where that improves clarity.

Controller boundaries:

- Controllers must not contain core business-rule ownership.
- Controllers must not independently decide Teacher Workspace access.
- Controllers must not calculate Billable Students directly.
- Controllers must not process payments.
- Controllers must not directly expose Teacher-private content beyond authorization boundaries.
- Controllers must not implement notification behavior for Version 1.

---

# 7. Services

Services own business workflows and domain orchestration.

Service responsibilities include:

| Service Area | Responsibility |
|---|---|
| Authentication services | Coordinate login, logout, current user context, Student activation, and duplicate prevention workflows. |
| Authorization services | Support Custom RBAC checks, permission assignment rules, and Teacher Staff permission evaluation. |
| Teacher Workspace services | Enforce Teacher Workspace scoping and tenant-aware workflows. |
| Student services | Manage Student creation, assignment, activation, duplicate prevention, and Teacher relationship context. |
| Parent services | Enforce linked-Student read-only access. |
| Attendance services | Coordinate Dynamic QR Code, ID Card scan, manual Attendance, and Audit Log recording. |
| Homework services | Manage Homework creation, supported formats, submissions, grading/review, and history. |
| Lesson services | Manage Teacher-owned private Lesson metadata and file references. |
| Exam services | Coordinate Question Bank, Exams, attempts, answers, grading, and Bubble Sheet behavior. |
| Report services | Build role-appropriate report data while preserving isolation and historical visibility rules. |
| Payment services | Record Flow A and Flow B payment status without transaction processing. |
| Subscription services | Manage Billing Cycle, Billable Student calculation, and Flow A Subscription status. |
| File services | Validate file ownership, allowed types, access permissions, and storage references. |
| Archive services | Centralize Archive and restore rules. |
| Audit services | Record mandatory Audit Log events consistently. |

Services must be transaction-aware where multiple business changes must succeed together. For auditable actions, the business action and Audit Log entry should be coordinated so that required auditability is not silently lost.

---

# 8. Repositories

Repositories may be used to isolate complex persistence and query logic from services.

Repository responsibilities:

- Retrieve records scoped to the correct Teacher Workspace, Student, Parent linked Student, or Platform context.
- Encapsulate repeated query criteria.
- Apply active versus archived filtering according to the operation context.
- Support historical report retrieval without breaking Archive rules.
- Support MySQL 8-compatible access patterns.
- Prevent accidental cross-Teacher access through query design.

Repository constraints:

1. Repositories must not bypass authorization.
2. Repositories must not expose records outside the caller's resolved scope.
3. Repositories must not hide business-rule violations by returning broader data for filtering later.
4. Repositories must not depend on Redis, external search infrastructure, or non-MySQL storage for Version 1.
5. Repositories should be introduced where they reduce duplication or complexity, not as mandatory ceremony for every simple model operation.

---

# 9. Models

Models represent backend business entities and relationships at the Laravel application layer.

Model responsibilities:

- Represent confirmed logical entities from the Data Dictionary.
- Support relationship navigation in a controlled and scoped way.
- Represent Archive state for archivable records.
- Support timestamps and historical context where required.
- Support file references through storage-related models or abstractions.
- Support Audit Log references to actors, roles, contexts, and affected records.

Model constraints:

1. Models must not be treated as permission boundaries by themselves.
2. Models must not expose cross-Teacher relationships without explicit authorization and scope checks.
3. Models must not define hard deletion as an application behavior.
4. Models must preserve Teaching Subject immutability after Teacher account creation.
5. Models must preserve Student identity uniqueness and duplicate prevention rules.
6. Models must preserve one Parent account per Student in Version 1.

This document does not define model fields, database tables, migrations, or physical relationships.

---

# 10. Form Requests

Form Requests define request validation and may perform early authorization checks according to Laravel conventions.

Form Request responsibilities:

- Validate required inputs.
- Validate enum values such as Pricing Type, Attendance method, Homework format, Question Type, and status values.
- Validate date ranges and Billing Cycle constraints.
- Validate file presence and file type where applicable.
- Validate resource references without exposing unauthorized records.
- Reject unsupported Version 1 actions.
- Provide consistent validation error responses.

Validation examples by business rule:

| Business Area | Validation Responsibility |
|---|---|
| Student creation | Prevent duplicate Student accounts. |
| Group assignment | Enforce one active Group per Student per Teacher. |
| Teaching Subject | Reject changes after Teacher account creation. |
| Homework | Allow Text, Image, and PDF only; reject video homework. |
| Exams | Allow Multiple Choice, True/False, Essay, and Bubble Sheet question types only. |
| Payments | Accept payment status only; reject transaction processing fields. |
| Subscription | Enforce calendar-month Billing Cycle and enrollment-duration calculation context. |
| Parent access | Require linked Student relationship for Parent views. |
| Files | Validate allowed file type for owning resource. |

Form Requests must not replace Policies, Gates, or Custom RBAC. Validation and authorization work together but remain distinct responsibilities.

---

# 11. Policies & Gates

Policies and Gates are the core Laravel authorization mechanisms for Version 1, combined with Custom RBAC.

Authorization responsibilities:

- Enforce role boundaries.
- Enforce Teacher Workspace ownership.
- Enforce Teacher Staff assigned permissions.
- Enforce Student self-scope.
- Enforce Parent linked-Student read-only access.
- Enforce Super Admin Platform scope.
- Enforce Archive and restore permissions.
- Enforce file access ownership.
- Enforce Audit Log visibility boundaries.

Policy and Gate design must use the required permission names from the Permission Matrix as the logical catalog.

Important authorization constraints:

1. Teacher can access only own Teacher Workspace.
2. Teacher Staff can access only creating Teacher Workspace and only assigned permissions.
3. Student can access only own account and own per-Teacher records.
4. Parent can access only linked Students and cannot modify Student educational records.
5. Super Admin cannot operate inside Teacher Workspaces as a Teacher.
6. Super Admin content visibility remains PENDING and must not be silently expanded.
7. No role receives hard-delete permission.

---

# 12. Middleware

Middleware applies cross-cutting request checks before feature logic executes.

Recommended middleware responsibilities:

| Middleware Concern | Responsibility |
|---|---|
| Authentication | Ensure protected requests have authenticated context. |
| Role context | Resolve active role context for the request. |
| Teacher Workspace context | Resolve and verify current Teacher Workspace for Teacher and Teacher Staff routes. |
| Student context | Ensure Student routes operate on the authenticated Student's own account. |
| Parent linked-Student context | Ensure Parent routes reference only linked Students. |
| Platform scope | Ensure Platform Administration routes are Super Admin only. |
| Archive state handling | Prevent archived resources from being treated as active records. |
| Request throttling | Protect sensitive endpoints such as login and scanning where appropriate. |
| Error normalization | Ensure consistent API error responses without exposing internals. |

Middleware constraints:

- Middleware must not replace feature-specific authorization.
- Middleware must not assume Teacher Staff permission granularity beyond assigned permissions.
- Middleware must not implement unconfirmed non-payment enforcement.
- Middleware must not enable notification or payment gateway behavior.

---

# 13. Authentication Flow

The authentication flow uses Laravel Sanctum.

Flow steps:

1. User submits authentication credentials through the Web Application.
2. Backend validates the authentication request.
3. Laravel Sanctum establishes authenticated context.
4. Backend resolves the user's available role contexts.
5. Backend records successful or failed login in the Audit Log.
6. Authenticated requests carry the user context to protected endpoints.
7. Authorization is evaluated for every protected action.

Student registration and activation rules:

- A Student may self-register.
- A Teacher may create a Student manually.
- A Teacher-created Student account can later be activated by the Student.
- Duplicate Student accounts are not allowed.

Authentication constraints:

- No native mobile-specific authentication requirement exists in Version 1.
- No “Login as Teacher” or impersonation flow is confirmed for Version 1.
- Authentication must not create multiple Parent accounts for one Student.

---

# 14. Authorization Flow

The authorization flow runs after authentication and before business action execution.

Flow steps:

1. Identify authenticated user.
2. Resolve active role context.
3. Resolve request scope.
4. Identify requested resource and action.
5. Check required permission from the Permission Matrix.
6. Check ownership or relationship:
   - Teacher Workspace ownership for Teacher.
   - Creating Teacher Workspace and assigned permissions for Teacher Staff.
   - Student ownership for Student.
   - Linked Student relationship for Parent.
   - Platform-level scope for Super Admin.
7. Check Archive state and whether the action is active, historical, Archive, or restore.
8. Reject unauthorized access without exposing restricted data.
9. Continue to service layer only when authorization passes.

Authorization must be applied server-side and must not rely on frontend visibility.

---

# 15. Validation Strategy

The validation strategy combines Form Requests, service-level business validation, and persistence-level integrity constraints.

Validation layers:

| Layer | Responsibility |
|---|---|
| Request validation | Required fields, formats, basic values, file rules, dates, enum values. |
| Authorization validation | Role, scope, ownership, permission, linked relationship, Teacher Workspace access. |
| Business validation | Confirmed rules such as one Group per Student per Teacher, duplicate prevention, Flow A / Flow B separation, Archive policy. |
| Persistence integrity | Prevent invalid saved state and preserve logical relationships. |

Key validation rules:

- Student duplicate accounts must be rejected.
- Teacher Staff must belong to the creating Teacher Workspace.
- Parent can reference only linked Students.
- Student can reference only own data.
- Group must belong to the current Teacher Workspace.
- Educational Grade must belong to the current Teacher Workspace.
- Homework file formats must be Text, Image, or PDF only.
- Exam Question Type must be Multiple Choice, True/False, Essay, or Bubble Sheet.
- Billing Cycle must be calendar month.
- Billable Student calculation must use Enrollment duration only.
- Payment requests must be status-only.
- Archive must be used instead of permanent deletion.

---

# 16. File Upload Strategy

Version 1 uses Laravel Public Storage and must remain compatible with cPanel Shared Hosting.

File upload responsibilities:

- Validate file type according to owning resource.
- Store file references with ownership context.
- Preserve Teacher Workspace ownership.
- Enforce Student and Parent access through relationship checks.
- Retain file references for historical records and archived records.
- Prevent cross-Teacher file access.

Supported file rules:

| File Context | Version 1 Rule |
|---|---|
| Homework assignment | Text, Image, and PDF only. |
| Homework submission | Text, Image, and PDF only. |
| Lesson video | Teacher-owned private video Lesson for own Students. |
| Parent uploads | Denied. |
| Video homework | Denied. |
| S3 Storage | Not required for Version 1. |

Lesson video hosting and protection details remain PENDING. The backend must not silently harden unconfirmed streaming, signed URL, or hosting mechanics beyond existing architecture decisions.

---

# 17. Error Handling

The backend must provide consistent API error handling aligned with `AI_DOCS/10_API_Design.md`.

Error handling responsibilities:

- Return standardized error responses.
- Use validation responses for invalid input.
- Deny unauthorized access safely.
- Hide restricted data.
- Reject unsupported Version 1 actions.
- Preserve previous valid state when updates fail.
- Record failed login events in the Audit Log.
- Ensure required auditability is not silently lost.

Common error scenarios:

| Scenario | Expected Handling |
|---|---|
| Unauthenticated protected request | Reject with authentication-required response. |
| Unauthorized role or permission | Reject without exposing resource existence where appropriate. |
| Cross-Teacher access attempt | Reject and do not disclose private data. |
| Duplicate Student account attempt | Reject with business conflict or validation failure. |
| Parent modification attempt | Reject because Parent access is read-only. |
| Unsupported file format | Reject with validation failure. |
| Payment processing attempt | Reject as out of scope. |
| Hard-delete attempt | Reject; require Archive where applicable. |
| Notification request | Reject because notifications are out of scope for Version 1. |

Errors must not expose implementation details, stack traces, SQL details, server paths, secrets, or Teacher-private data.

---

# 18. Logging

Backend logging has two distinct responsibilities: operational logging and the business Audit Log.

## Operational Logging

Operational logs support troubleshooting, runtime diagnostics, and hosting support.

Operational logging must:

- Avoid storing sensitive secrets.
- Avoid exposing Teacher-private content unnecessarily.
- Support cPanel-compatible file-based logging where appropriate.
- Not replace business Audit Log requirements.

## Audit Log

The Audit Log is mandatory, append-only, immutable, and permanently retained.

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

Audit attribution rules:

- Teacher Staff actions are attributed to the Teacher Staff user, not the Teacher.
- Super Admin actions are attributed to the Super Admin.
- Student and Parent actions are attributed to the authenticated account.
- Audit Log entries must preserve Platform or Teacher Workspace context.

Audit Log entries must not be edited, archived, or deleted.

---

# 19. Queue Strategy

Version 1 uses Laravel Database Queue for cPanel Shared Hosting compatibility.

Queue responsibilities may include deferred backend work that does not need to block the request immediately, such as:

- Report preparation where appropriate.
- File-related post-processing compatible with cPanel resources.
- Audit-supporting non-critical enrichment where it does not risk losing mandatory audit records.
- Scheduled Subscription or billing-related background work.
- Other confirmed backend tasks that remain within Version 1 scope.

Queue constraints:

1. Database Queue is the official Version 1 queue mechanism.
2. Redis is not required.
3. Queue design must be safe for shared hosting limits.
4. Mandatory business actions must not be considered complete if required persistence or required Audit Log recording failed.
5. Queue jobs must preserve Teacher Workspace scope and authorization context where relevant.
6. Queue jobs must not introduce notifications, payment processing, WebSockets, or microservice behavior.

---

# 20. Scheduler

Version 1 uses Laravel Scheduler triggered by Cron Jobs on cPanel Shared Hosting.

Scheduler responsibilities may include:

- Starting or preparing calendar-month Billing Cycles.
- Supporting Flow A Subscription calculations based on Enrollment duration.
- Running maintenance tasks compatible with shared hosting.
- Processing queued jobs where the hosting model requires scheduled queue execution.
- Supporting periodic report preparation where appropriate.

Scheduler constraints:

1. Billing Cycle starts on the first day of the calendar month and ends on the last day of the same month.
2. Billable Student calculation is based on Enrollment duration only.
3. Attendance and login activity must not be used for Billable Student calculation.
4. Non-payment enforcement remains PENDING and must not be implemented as confirmed scheduled behavior.
5. Scheduler tasks must preserve Teacher Workspace isolation.
6. Scheduler tasks must not hard delete data.
7. Scheduler tasks must not send Version 1 notifications.

---

# 21. Notifications

Notifications are explicitly out of scope for Version 1.

Backend notification rules:

1. No push notification feature is implemented in Version 1.
2. No email notification feature is implemented in Version 1.
3. No SMS notification feature is implemented in Version 1.
4. No notification API endpoints are provided in Version 1.
5. No notification database entity is defined for Version 1.
6. No queued notification sending is part of Version 1.
7. No scheduled notification sending is part of Version 1.

SMTP is included in the official technical baseline as mail transport availability, but it must not be interpreted as approval for Version 1 notification features.

Future notification features may be considered only in a separately approved future scope.

---

# 22. Performance Guidelines

The backend must be optimized for Laravel 12, PHP 8.3, MySQL 8, and cPanel Shared Hosting.

Performance guidelines:

| Area | Guideline |
|---|---|
| Query scoping | Always scope Teacher Workspace records before retrieval. |
| Pagination | Use pagination for list endpoints. |
| Filtering | Apply filters after authorization and scope resolution. |
| Sorting | Restrict sorting to supported and indexed logical fields in future physical design. |
| Reports | Keep reports scoped and avoid cross-tenant data exposure. |
| File handling | Validate files early and avoid unnecessary processing in the request cycle. |
| Queue usage | Use Database Queue for appropriate deferred work. |
| Cache | Use File Cache for safe shared-hosting-compatible caching. |
| Sessions | Use Database session driver. |
| Scheduler | Use cPanel Cron Jobs to trigger Laravel Scheduler. |
| Audit Log | Record mandatory audit events reliably without weakening business operations. |

Performance constraints:

- No unconfirmed response-time, throughput, or concurrency targets are defined.
- Performance optimization must never bypass authorization.
- Archived records must not be treated as active to simplify queries.
- Teacher Workspace isolation must not be relaxed for performance.
- Redis, WebSockets, S3 Storage, Docker, Kubernetes, and Microservices are not Version 1 requirements.

---

# 23. Coding Principles

Backend coding principles must preserve consistency, maintainability, and business-rule correctness.

Principles:

1. Documentation and architecture come before code.
2. Use canonical terminology consistently.
3. Keep controllers thin and services responsible for business workflows.
4. Enforce authorization server-side for every protected action.
5. Scope Teacher Workspace data before access.
6. Deny by default.
7. Preserve Parent read-only access.
8. Preserve Student self-scope and per-Teacher partitioning.
9. Preserve Flow A and Flow B separation.
10. Use Archive instead of hard deletion.
11. Record mandatory Audit Log events.
12. Do not introduce unconfirmed features.
13. Avoid overengineering for Version 1 shared hosting constraints.
14. Keep Laravel conventions where they support clarity and maintainability.
15. Keep feature boundaries clear without creating microservices.
16. Prefer readable, testable, traceable backend structure.
17. Reject unsupported payment processing, notification, marketplace, and native mobile assumptions.

Terminology constraints:

- Use Educational Grade, not non-canonical alternatives.
- Use Lesson, not Course.
- Use Archive, not delete as product behavior.
- Use Subscription only for Flow A unless explicitly qualified.
- Use payment status for Flow B.

---

# 24. Future Improvements

Future backend improvements may be considered after Version 1 without changing the confirmed Version 1 scope.

Potential future improvements:

| Future Area | Notes |
|---|---|
| VPS / Cloud deployment | Future target may allow more advanced infrastructure and scaling options. |
| Advanced cache | Redis may be considered in the future but is not required for Version 1. |
| Advanced queues | External queue infrastructure may be considered after shared-hosting constraints change. |
| Advanced storage | S3 Storage or private object storage may be considered later; Version 1 uses Laravel Public Storage. |
| Payment gateways | Online payment integration may be considered in future scope only. |
| Notifications | Push, email, or SMS notifications may be considered only in future approved scope. |
| Native mobile support | Native applications are outside Version 1 and may be considered later. |
| Super Admin visibility | Pending content-visibility rules may be refined after Product Owner confirmation. |
| Teacher Staff permissions | Detailed permission granularity may be expanded after Product Owner confirmation. |
| Non-payment enforcement | Enforcement behavior remains PENDING until formally resolved. |
| Pricing model | Flat price versus volume tiers remains PENDING until formally resolved. |
| Localization | Arabic (default) and English (fully supported), with automatic RTL/LTR, are confirmed; timezone, currency, and market settings remain PENDING. |

Future improvements must preserve Teacher Workspace isolation, one global Student account, one Parent account per Student, Parent read-only access, private Teacher-owned Lessons and Question Banks, Flow A / Flow B separation, Archive instead of permanent deletion, and permanent Audit Log retention.

---

# 25. Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Project Context alignment | Passed — backend rules follow the frozen Version 1 source of truth. |
| Target stack alignment | Passed — Laravel 12, PHP 8.3, MySQL 8, Laravel Sanctum, and cPanel Shared Hosting are used as the baseline. |
| cPanel optimization | Passed — File Cache, Database Queue, Database sessions, Laravel Public Storage, Cron Jobs, SMTP baseline, Apache/LiteSpeed compatibility are preserved. |
| Architecture scope | Passed — backend architecture only; no frontend architecture, source code, database tables, or API implementation is defined. |
| Teacher Workspace isolation | Passed — all backend layers preserve tenant scope. |
| Student account rules | Passed — one global Student account, duplicate prevention, and per-Teacher partitioning are preserved. |
| Parent access | Passed — Parent access remains linked-Student scoped and read-only. |
| Teacher Staff access | Passed — access remains conditional on Teacher-assigned permissions; detailed granularity remains PENDING. |
| Super Admin scope | Passed — Super Admin remains Platform-scoped; content visibility remains PENDING. |
| Flow A / Flow B separation | Passed — Subscription and payment-status responsibilities remain separate. |
| Payment handling | Passed — Version 1 records status only and does not process payments. |
| Archive policy | Passed — hard deletion is not included; Archive and restore are preserved. |
| Audit Log policy | Passed — mandatory audit events, immutability, and permanent retention are preserved. |
| Notifications | Passed — notifications are documented as out of scope for Version 1. |
| Excluded technologies | Passed — Docker, Redis, Kubernetes, S3 Storage, WebSockets, and Microservices are not required for Version 1. |
| Terminology | Passed — Educational Grade, Teacher Workspace, Lesson, Subscription, Flow A, Flow B, Archive, and Audit Log are used consistently. |
