# 27 — Development Roadmap

## Document Scope

This document defines the complete development roadmap for Version 1 of the Unified Education Platform. It establishes the phased development plan, milestone structure, testing and documentation milestones, deployment milestones, release strategy, versioning strategy, risks and dependencies, and future version outlines.

This document does not define source code, APIs, database tables, UI implementation, or implementation-level details. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found.

The roadmap is built on the confirmed Version 1 technology baseline: **Laravel 12** backend with **PHP 8.3**, **React 19** frontend with **TypeScript**, **Vite**, and **Tailwind CSS**, **MySQL 8** for persistence, **Laravel Sanctum** for authentication, **Laravel Gates & Policies with Custom RBAC** for authorization, **File Cache**, **Database Queue**, **Database session driver**, **Laravel Public Storage**, **Laravel Scheduler with Cron Jobs**, and **cPanel Shared Hosting** as the primary deployment target.

---

# 1. Roadmap Overview

This roadmap defines the phased development plan for Version 1 of the Unified Education Platform. It translates the confirmed product requirements, architecture, and business rules into a structured sequence of development phases.

The roadmap is organized into ten development phases, each building upon the foundations established by previous phases. The phases are ordered to minimize dependency risk: foundational infrastructure and authentication are built first, followed by core domain features, cross-cutting concerns, and optimization work.

Every phase must preserve the confirmed business rules, including:

- Teacher Workspace isolation (BR-003).
- One global Student account with no duplicates (BR-001, BR-022).
- One Group per Student per Teacher (BR-002).
- Parent read-only access to linked Students only (BR-004).
- Archive replaces permanent deletion everywhere (BR-005).
- Audit Log records all important actions (BR-006).
- Historical data is never deleted (BR-014).
- Flow A and Flow B are never conflated.
- Version 1 records payment status only (BR-019).
- Homework supports Text, Image, and PDF only (BR-021).
- Each Teacher account represents exactly one Teaching Subject (BR-016).
- Notifications, online payment gateways, and native mobile applications are out of scope.

---

# 2. Development Philosophy

The development philosophy follows the confirmed Project Context collaboration protocol and architecture principles.

## 2.1 Documentation First

Architecture and documentation come before code. Every feature traces to the canonical document set (`AI_DOCS/`). No feature is implemented without a confirmed requirement, and no business rule is silently assumed.

## 2.2 Business Rule Enforcement at the Backend

The Laravel 12 backend is the sole authority for authentication, authorization, tenant isolation, validation, business rule enforcement, Audit Log creation, and persistence decisions. The React 19 frontend presents authorized data and collects user input but never replaces backend enforcement.

## 2.3 Multi-Tenant Isolation First

Teacher Workspace isolation is the highest-priority architectural invariant. Every query, every API response, every file access, every search result, and every report must preserve workspace boundaries. No optimization, performance improvement, or convenience shortcut may weaken tenant isolation.

## 2.4 Archive Instead of Deletion

No permanent deletion exists anywhere in the system. All data lifecycle operations use Archive. Historical records remain available for reports and history queries. Archived records are excluded from active searches and active selection lists.

## 2.5 Audit Log as a First-Class Subsystem

Every important action produces an Audit Log entry. The Audit Log is append-only, immutable, and permanently retained. Development must not introduce code paths that bypass Audit Log requirements.

## 2.6 Incremental and Testable Delivery

Each phase produces a testable increment. Features are developed in dependency order so that foundational layers (authentication, authorization, database schema, tenant isolation) are validated before domain features that depend on them.

## 2.7 Canonical Terminology

All development artifacts — code, tests, documentation, comments, and variable names — must use the canonical terminology defined in `AI_DOCS/00_Project_Context.md` §19.

## 2.8 Version 1 Scope Discipline

Development must not introduce features outside confirmed Version 1 scope. Native mobile applications, online payment gateways, notifications, marketplace behavior, video homework, multiple Teaching Subjects per Teacher, and multiple Parent accounts per Student are out of scope.

---

# 3. Project Milestones

The project milestones define the major checkpoints that mark the completion of each development phase. Each milestone has a clear scope, a set of deliverables, and acceptance criteria.

| Milestone | Phase | Description | Key deliverables |
|---|---|---|---|
| **M0** | — | Project foundation and infrastructure setup | Repository structure, CI/CD baseline, environment configuration, database provisioning, cPanel deployment baseline |
| **M1** | Phase 1 | Foundation complete | Database schema, Laravel application shell, React application shell, authentication, core middleware, Archive and Audit Log infrastructure |
| **M2** | Phase 2 | Authentication & RBAC complete | All five role authentications, role-based authorization, Teacher Workspace isolation enforcement, permission matrix implementation |
| **M3** | Phase 3 | Teacher Workspace complete | Educational Grades, Groups, Students, Student movement, Teacher Staff, Teacher Workspace Settings |
| **M4** | Phase 4 | Student & Parent Modules complete | Student Panel, Parent Panel, Student Switcher, per-Teacher data partitioning, linked-Student monitoring |
| **M5** | Phase 5 | Attendance System complete | Dynamic QR Code, ID Card scanning, manual Attendance, Attendance history, Attendance reports |
| **M6** | Phase 6 | Homework System complete | Homework creation, Student submission, grading, Text/Image/PDF support, video rejection, Homework reports |
| **M7** | Phase 7 | Exam Engine complete | Question Bank, Exam creation, all four question types, Bubble Sheet, automatic grading, Essay pending state, Exam reports |
| **M8** | Phase 8 | Reporting complete | Teacher Workspace reports, Student reports, Parent reports, Platform reports, historical data inclusion, Flow A/Flow B separation |
| **M9** | Phase 9 | Subscription & Billing complete | Billing Cycle management, Billable Student calculation, Subscription snapshots, payment-status recording, billing reports |
| **M10** | Phase 10 | Optimization & Release Readiness complete | Performance optimization, security hardening, comprehensive testing, documentation validation, deployment readiness |
| **M11** | — | Version 1 Release | Production deployment, UAT completion, release acceptance |

---

# 4. Phase 1 — Foundation

## 4.1 Purpose

Phase 1 establishes the foundational infrastructure that all subsequent phases depend on. It includes database schema creation, Laravel backend shell, React frontend shell, authentication, Archive infrastructure, Audit Log infrastructure, and deployment baseline.

## 4.2 Scope

Phase 1 covers:

- Repository structure aligned with `AI_DOCS/04_Project_Structure.md`.
- Laravel 12 application bootstrap with PHP 8.3.
- React 19 application bootstrap with TypeScript, Vite, and Tailwind CSS.
- MySQL 8 database provisioning and initial schema migrations for foundational entities: User Identity, Role Context, Teacher Workspace, Archive State, and Audit Log Entry.
- Laravel Sanctum authentication integration.
- Database session driver configuration.
- File Cache configuration.
- Database Queue configuration.
- Laravel Scheduler with Cron Jobs setup for cPanel Shared Hosting.
- Archive infrastructure: active/archived state representation for all archivable entities.
- Audit Log infrastructure: append-only, immutable, permanent Audit Log subsystem.
- Error handling framework with standardized API error responses.
- Operational logging baseline.
- Initial deployment to cPanel Shared Hosting staging environment.

## 4.3 MVP Features

Phase 1 MVP features are:

- User authentication via Laravel Sanctum for all five roles.
- Basic Teacher account creation and Teacher Workspace initialization.
- Database schema supporting tenant-scoped data access.
- Archive and restore mechanism for all archivable entities.
- Audit Log recording for create, update, Archive, restore, and login events.
- Standardized API error response structure.

## 4.4 Dependencies

- Confirmed technology stack (D-001).
- Confirmed deployment target: cPanel Shared Hosting.
- Canonical document set for architecture and requirements reference.

## 4.5 Acceptance Criteria

- A Teacher can register, authenticate, and access an empty Teacher Workspace.
- A Student can self-register and authenticate.
- A Parent account can authenticate.
- The Super Admin can authenticate at Platform level.
- Archive and restore operations work for at least one entity type.
- Audit Log entries are created for login events and at least one CRUD operation.
- API error responses follow the standardized structure.
- The application deploys successfully to cPanel Shared Hosting staging.
- All queries to Teacher Workspace-owned data are workspace-scoped.

---

# 5. Phase 2 — Authentication & RBAC

## 5.1 Purpose

Phase 2 completes the authentication and authorization infrastructure for all five roles, implements the Custom RBAC model, and enforces the confirmed permission matrix across all API endpoints.

## 5.2 Scope

Phase 2 covers:

- Complete authentication flows for Super Admin, Teacher, Teacher Staff, Student, and Parent.
- Student self-registration (BR-022, Method 1).
- Teacher-created Student account creation (BR-022, Method 2).
- Student account activation by the Student for Teacher-created accounts.
- Duplicate Student account prevention (BR-022).
- One Parent account per Student enforcement (BR-020).
- Laravel Gates & Policies implementation for all confirmed permissions from `AI_DOCS/09_Permission_Matrix.md`.
- Teacher Workspace context resolution middleware for Teacher and Teacher Staff routes.
- Student self-scope middleware for Student routes.
- Parent linked-Student middleware for Parent routes.
- Platform scope middleware for Super Admin routes.
- Teacher Staff permission evaluation based on Teacher-assigned permissions.
- Password policy enforcement.
- Rate limiting on login, registration, and password reset endpoints.
- Session management with Database session driver.
- Security headers on API responses.

## 5.3 MVP Features

Phase 2 MVP features are:

- All five roles can authenticate and access their authorized areas.
- Teacher cannot access another Teacher's data under any circumstance.
- Teacher Staff access is limited to the creating Teacher Workspace and assigned permissions.
- Student access is limited to own account and own per-Teacher records.
- Parent access is limited to linked Students and is read-only everywhere.
- Super Admin operates at Platform level only.
- Duplicate Student accounts are prevented at the backend.

## 5.4 Dependencies

- Phase 1 complete (authentication infrastructure, database schema, Audit Log).
- RBAC architecture defined in `AI_DOCS/08_RBAC.md`.
- Permission matrix defined in `AI_DOCS/09_Permission_Matrix.md`.

## 5.5 Acceptance Criteria

- Each role can authenticate and access only their authorized scope.
- Cross-Teacher access attempts are denied without exposing restricted data.
- Duplicate Student account creation is rejected.
- Parent can only see linked Students.
- Teacher Staff actions are attributed to the Teacher Staff user in the Audit Log.
- Rate limiting returns 429 on login endpoint after threshold exceeded.
- Password reset uses time-limited, single-use tokens.
- Failed login attempts are recorded in the Audit Log.

---

# 6. Phase 3 — Teacher Workspace

## 6.1 Purpose

Phase 3 implements the core Teacher Workspace domain features: Educational Grades, Groups, Students, Student movement, Teacher Staff management, and Teacher Workspace Settings.

## 6.2 Scope

Phase 3 covers:

- Educational Grades: create, view, update, Archive, restore, workspace isolation.
- Groups: create under active Educational Grade, view, update, Archive, restore, Pricing Type validation (Monthly or Per Lesson).
- Student management: register new Student, assign existing Student, search Students, move Students between Groups.
- Enrollment management: time-bounded Enrollment periods, history preservation on Group movement (BR-007).
- One Group per Student per Teacher enforcement (BR-002).
- Teacher Staff: create, view, update, assign permissions, Archive, restore.
- Teacher Workspace Settings: Teacher profile, center information, phone numbers, address.
- Teaching Subject immutability enforcement (BR-016).
- Audit Log recording for all create, update, Archive, restore, and Student movement actions.
- File upload validation for Homework-supported formats (Text, Image, PDF only; video rejected).
- Dashboard summary for Teacher Workspace.

## 6.3 MVP Features

Phase 3 MVP features are:

- Teacher can create Educational Grades and Groups.
- Teacher can register Students and assign them to Groups.
- Student movement between Groups preserves historical records.
- Teacher Staff can be created with assigned permissions.
- Teaching Subject cannot be changed after account creation.
- All operations are workspace-scoped and audited.

## 6.4 Dependencies

- Phase 2 complete (authentication and RBAC for all roles).
- Database schema for Educational Grades, Groups, Enrollments, Students, Teacher Staff.

## 6.5 Acceptance Criteria

- Educational Grades and Groups are workspace-scoped.
- A Student cannot belong to more than one Group per Teacher at the same time.
- Student movement between Groups preserves historical Attendance, Homework, Exams, and grades.
- Teacher Staff can access only the creating Teacher Workspace with assigned permissions.
- Teaching Subject cannot be changed after account creation.
- Video homework is rejected.
- All important actions are recorded in the Audit Log.
- Archived records do not appear in active searches or dropdown lists.

---

# 7. Phase 4 — Student & Parent Modules

## 7.1 Purpose

Phase 4 implements the Student Panel and Parent Panel, enabling Students to access their own per-Teacher-partitioned records and Parents to monitor linked Students in read-only mode.

## 7.2 Scope

Phase 4 covers:

- Student Panel: Dashboard, My Schedule, Homework view, Exam view, Lesson view, Subscriptions (Flow B) view, Settings.
- Per-Teacher data partitioning for all Student-facing views.
- Student self-registration flow (BR-022, Method 1).
- Student account activation flow for Teacher-created accounts (BR-022, Method 2).
- Parent Panel: Dashboard, Student Switcher, Homework view, Attendance view, Exam view, Teachers view, Payments (Flow B) view, Settings.
- Parent linked-Student enforcement (BR-004, BR-020).
- Parent read-only access enforcement across all views.
- One Parent account per Student enforcement (BR-020).
- Student and Parent Dashboard summaries.

## 7.3 MVP Features

Phase 4 MVP features are:

- Student can view Homework, Exams, Lessons, schedule, and per-Teacher Flow B status.
- Student content is partitioned per Teacher; no cross-Teacher data mixing.
- Parent can switch between linked Students using the Student Switcher.
- Parent sees only linked Student data in read-only mode.
- Parent cannot modify any educational records.

## 7.4 Dependencies

- Phase 3 complete (Teacher Workspace features, Student management, Group management).
- Database schema for Parent-Student links, per-Teacher data partitioning.

## 7.5 Acceptance Criteria

- Student Dashboard shows only data belonging to the Student account.
- Student Homework, Exams, and Lessons are distinguished by Teacher.
- Parent can view only linked Student data.
- Parent cannot modify Attendance, grades, Homework, Exams, or payment status.
- Student Switcher shows only linked Students.
- One Parent account per Student is enforced.
- Flow B status is shown as payment status only, not as an online payment action.

---

# 8. Phase 5 — Attendance System

## 8.1 Purpose

Phase 5 implements the complete Attendance system with all three confirmed methods: Dynamic QR Code, ID Card scanning, and manual entry.

## 8.2 Scope

Phase 5 covers:

- Attendance Session management within Teacher Workspaces.
- Dynamic QR Code generation (daily, per Teacher Workspace context).
- Student QR Code scanning through the Web Application (BR-010, Method 1).
- ID Card scanning through QR scanner device integration (BR-010, Method 2).
- Manual Attendance entry by Teacher or authorized Teacher Staff (BR-010, Method 3).
- Duplicate scan prevention.
- Attendance history preservation through Student Group movement.
- Attendance exclusion from Billable Student calculation (BR-008).
- Attendance reports for Teacher Workspace, Student, and Parent views.
- Audit Log recording for all Attendance changes.
- Expired Dynamic QR Code context cleanup (background job).

## 8.3 MVP Features

Phase 5 MVP features are:

- Teacher can generate a daily Dynamic QR Code for Attendance.
- Student can scan the QR Code through the Web Application to record Attendance.
- Teacher can record manual Attendance.
- Attendance records are workspace-scoped and audited.
- Attendance history is preserved through Student Group movement.

## 8.4 Dependencies

- Phase 3 complete (Groups, Students, Teacher Workspace).
- Phase 4 complete (Student Panel for QR scanning access).
- QR Attendance architecture from `AI_DOCS/16_QR_Attendance_System.md`.
- Background job infrastructure from `AI_DOCS/21_Background_Jobs.md`.

## 8.5 Acceptance Criteria

- All three Attendance methods function correctly.
- Student scanning is limited to the Student's own Attendance for valid Teacher relationships.
- Duplicate Attendance for the same context is prevented.
- Attendance records are workspace-scoped.
- Attendance changes are recorded in the Audit Log.
- Attendance is not used for Billable Student calculation.
- Historical Attendance remains available after Student Group movement.

---

# 9. Phase 6 — Homework System

## 9.1 Purpose

Phase 6 implements the complete Homework system supporting Text, Image, and PDF formats only.

## 9.2 Scope

Phase 6 covers:

- Homework creation by Teacher or authorized Teacher Staff with Text, Image, and PDF support (BR-021).
- Homework assignment to Students or Groups within the Teacher Workspace.
- Student Homework submission for assigned Homework.
- Homework grading by Teacher or authorized Teacher Staff.
- Video homework rejection (BR-021).
- Homework file storage through Laravel Public Storage with workspace ownership.
- Homework submission file validation (Image and PDF only for Student submissions).
- Homework Archive and restore.
- Homework history preservation through Student Group movement.
- Homework reports for Teacher Workspace, Student, and Parent views.
- Audit Log recording for Homework creation, modification, grading, and archival.

## 9.3 MVP Features

Phase 6 MVP features are:

- Teacher can create Homework assignments with Text, Image, and PDF content.
- Student can submit Homework responses in supported formats.
- Video homework is rejected.
- Teacher can grade Homework submissions.
- Homework is workspace-scoped and audited.

## 9.4 Dependencies

- Phase 3 complete (Groups, Students, file upload infrastructure).
- Phase 4 complete (Student Panel for submission, Parent Panel for monitoring).
- File storage architecture from `AI_DOCS/20_File_Storage.md`.

## 9.5 Acceptance Criteria

- Homework supports only Text, Image, and PDF formats.
- Video homework is rejected.
- Student submissions are limited to Image and PDF.
- Homework is workspace-scoped.
- Homework changes are recorded in the Audit Log.
- Historical Homework is preserved through Student Group movement.
- Parent can view Homework for linked Students in read-only mode.

---

# 10. Phase 7 — Exam Engine

## 10.1 Purpose

Phase 7 implements the complete Exam Engine including the Question Bank, all four question types, Bubble Sheet automatic grading, and the Exam lifecycle.

## 10.2 Scope

Phase 7 covers:

- Question Bank management: create, view, update, Archive, restore questions within the Teacher Workspace.
- Question type support: Multiple Choice, True/False, Essay, Bubble Sheet (BR-011).
- Exam creation from the Teacher's own private Question Bank only.
- Exam publishing/making available to authorized Students.
- Student Exam attempt and answer submission for all question types.
- Bubble Sheet electronic on-screen selection and automatic grading (D-010).
- Essay question submission and pending Teacher grading state.
- Exam grade storage and result visibility for Student and Parent.
- Exam attempt and grade history preservation through Student Group movement (BR-007).
- Exam reports for Teacher Workspace, Student, and Parent views.
- Background job for automatic grading of objective question types.
- Audit Log recording for all Exam, Question Bank, and grading modifications.

## 10.3 MVP Features

Phase 10 MVP features are:

- Teacher can manage a private Question Bank with all four question types.
- Teacher can create Exams from the Question Bank.
- Student can attempt Exams and submit answers.
- Bubble Sheet questions are automatically graded.
- Essay answers are marked as pending Teacher review.
- Exam results are workspace-scoped and audited.

## 10.4 Dependencies

- Phase 3 complete (Groups, Students, Teacher Workspace).
- Phase 4 complete (Student Panel for Exam attempts, Parent Panel for monitoring).
- Exam Engine architecture from `AI_DOCS/15_Exam_Engine.md`.
- Background job infrastructure for automatic grading.

## 10.5 Acceptance Criteria

- Exams can be created only from the Teacher's own private Question Bank.
- Supported question types are limited to Multiple Choice, True/False, Essay, and Bubble Sheet.
- Bubble Sheet uses electronic on-screen selection with automatic grading.
- Essay answers show as pending until Teacher grading is complete.
- Teachers cannot access another Teacher's Question Bank, Exams, or grades.
- Exam attempts and grades are workspace-scoped.
- Historical Exam data is preserved through Student Group movement.
- Exam modifications are recorded in the Audit Log.

---

# 11. Phase 8 — Reporting

## 11.1 Purpose

Phase 8 implements the complete reporting system for all roles and reporting domains.

## 11.2 Scope

Phase 8 covers:

- Teacher Workspace reports: Attendance, Homework, Exam results, Flow B payment status, Student performance.
- Student summary reports: per-Teacher records, partitioned by Teacher.
- Parent linked-Student reports: read-only summaries for linked Students.
- Super Admin Platform-level reports: Teacher administration, Flow A Subscriptions, pricing, payment status.
- Report filtering: date/period, Educational Grade, Group, Student, status, and report-specific criteria.
- Report sorting: supported fields, ascending/descending order.
- Report pagination for large datasets.
- Historical data inclusion with archived record indication.
- Flow A and Flow B separation in all payment-related reports.
- Dashboard statistics for all roles.
- Deferred report preparation through background jobs where needed.
- Report error handling and empty-state management.

## 11.3 MVP Features

Phase 8 MVP features are:

- Teacher can view Attendance, Homework, Exam, payment, and Student performance reports.
- Student can view own summary report partitioned per Teacher.
- Parent can view linked Student reports in read-only mode.
- Super Admin can view Platform-level reports within confirmed visibility boundaries.
- Reports include historical data with archived records clearly indicated.
- Flow A and Flow B remain separate in all reports.

## 11.4 Dependencies

- Phases 3–7 complete (all domain features providing report source data).
- Reporting architecture from `AI_DOCS/18_Reporting_Analytics.md`.
- Search and filtering standards from `AI_DOCS/22_Search_Filtering.md`.

## 11.5 Acceptance Criteria

- Reports include only data from the authorized scope.
- Archived records are clearly indicated when included in reports.
- Flow A and Flow B are separated in payment-related reporting.
- Reports do not process payments.
- Reports do not expose Teacher-private content to unauthorized roles.
- Filtering and sorting work correctly across all report types.
- Empty report states are handled without error.

---

# 12. Phase 9 — Subscription & Billing

## 12.1 Purpose

Phase 9 implements the complete Flow A Subscription and Billing system, including Billing Cycle management, Billable Student calculation, and payment-status recording.

## 12.2 Scope

Phase 9 covers:

- Billing Cycle management: calendar-month cycles starting on the first day and ending on the last day (D-006).
- Billable Student calculation based on Enrollment duration only (BR-008).
- Billable Student exclusion rule: Students enrolled for 15 calendar days or less are not counted.
- Monthly Subscription calculation: Billable Students × Price Per Student.
- Subscription snapshot generation (immutable monthly snapshots — D-003, PROPOSED).
- Platform-level pricing configuration by Super Admin (BR-015).
- Flow A payment-status recording by Super Admin (status only, no transaction processing).
- Teacher view of own Flow A Subscription information.
- Flow B payment-status recording within Teacher Workspaces.
- Student and Parent views of per-Teacher Flow B status.
- Billing reports for Super Admin.
- Background jobs for Billing Cycle initialization, Billable Student calculation, and Subscription snapshot generation.
- Audit Log recording for all Subscription and payment-status changes.

## 12.3 MVP Features

Phase 9 MVP features are:

- Billing Cycle follows the calendar-month rule.
- Billable Student calculation uses Enrollment duration only.
- Attendance and login activity are excluded from billing.
- Super Admin can view and manage Teacher Subscriptions under Flow A.
- Teachers can view their own Subscription status.
- Payment status is recorded only; no online payment processing.

## 12.4 Dependencies

- Phase 3 complete (Groups, Students, Enrollments).
- Phase 4 complete (Student and Parent views for Flow B status).
- Subscription & Billing architecture from `AI_DOCS/17_Subscription_Billing.md`.
- Background job infrastructure from `AI_DOCS/21_Background_Jobs.md`.

## 12.5 Acceptance Criteria

- Billing Cycle follows the calendar-month rule (first day to last day).
- Billable Student calculation uses Enrollment duration only.
- Students enrolled for 15 days or less are not Billable.
- Attendance and login activity are not used in billing.
- Flow A and Flow B remain separate in all contexts.
- Payment processing is not available; status is recorded only.
- Historical Subscription records are preserved.
- Subscription changes are recorded in the Audit Log.

---

# 13. Phase 10 — Optimization

## 13.1 Purpose

Phase 10 optimizes the Platform for performance, security, and release readiness. It includes performance tuning, security hardening, comprehensive testing, documentation validation, and deployment preparation.

## 13.2 Scope

Phase 10 covers:

- Performance optimization: query optimization, eager loading, pagination tuning, caching strategy refinement.
- Security hardening: input validation review, SQL injection prevention verification, XSS prevention verification, CSRF protection verification, rate limiting review, session security review, error message audit.
- Comprehensive testing: regression testing across all modules, authorization testing for all roles, tenant isolation testing, Archive and historical data testing, Audit Log completeness testing, Flow A/Flow B separation testing.
- Documentation validation: consistency review of all AI_DOCS documents against the Project Context.
- Deployment preparation: cPanel Shared Hosting configuration, Cron Job setup, queue worker configuration, backup strategy verification.
- Search and filtering optimization per `AI_DOCS/22_Search_Filtering.md`.
- File storage optimization per `AI_DOCS/20_File_Storage.md`.
- Background job optimization per `AI_DOCS/21_Background_Jobs.md`.

## 13.3 MVP Features

Phase 10 MVP features are:

- All automated tests pass without failures.
- No Critical or High bugs are open.
- Security checklist from `AI_DOCS/23_Security_Standards.md` passes.
- All business rules have automated test coverage.
- Staging environment mirrors production configuration.
- UAT environment is ready for stakeholder testing.

## 13.4 Dependencies

- Phases 1–9 complete (all features implemented).
- Testing strategy from `AI_DOCS/24_Testing_Strategy.md`.
- Performance and scalability guidance from `AI_DOCS/25_Performance_Scalability.md`.
- Security standards from `AI_DOCS/23_Security_Standards.md`.
- Deployment plan from `AI_DOCS/26_Deployment_Plan.md`.

## 13.5 Acceptance Criteria

- All automated tests pass (backend Feature, Unit; frontend integration).
- No Critical or High severity bugs are open.
- Security checklist passes for all affected areas.
- Teacher Workspace isolation is verified across all modules.
- Archive behavior is verified for all archivable entities.
- Audit Log completeness is verified for all mandatory events.
- Flow A and Flow B separation is verified.
- Historical data preservation is verified.
- Staging environment is validated.

---

# 14. Testing Milestones

Testing milestones define the quality gates that must be met at each phase boundary.

| Milestone | Phase | Testing scope | Quality gate |
|---|---|---|---|
| **T1** | After Phase 1 | Foundation tests: authentication, database connectivity, Archive infrastructure, Audit Log infrastructure, API error responses. | All foundation tests pass. |
| **T2** | After Phase 2 | Authorization tests: role-based access for all five roles, Teacher Workspace isolation, Student self-scope, Parent linked-Student scope, Teacher Staff permission boundaries. | Complete authorization matrix verified. |
| **T3** | After Phase 3 | Teacher Workspace domain tests: Educational Grade CRUD, Group CRUD, Student management, Student movement, Teacher Staff management, workspace isolation. | All Teacher Workspace business rules enforced. |
| **T4** | After Phase 4 | Student and Parent module tests: per-Teacher partitioning, linked-Student scope, Student Switcher, read-only enforcement, one Parent per Student. | Student and Parent boundaries verified. |
| **T5** | After Phase 5 | Attendance tests: all three methods, duplicate prevention, workspace scoping, history preservation, exclusion from billing. | Attendance system verified. |
| **T6** | After Phase 6 | Homework tests: format validation, submission, grading, video rejection, workspace scoping, history preservation. | Homework system verified. |
| **T7** | After Phase 7 | Exam Engine tests: Question Bank privacy, all question types, Bubble Sheet auto-grading, Essay pending state, workspace scoping, history preservation. | Exam Engine verified. |
| **T8** | After Phase 8 | Reporting tests: all report domains, role-appropriate visibility, historical data inclusion, Flow A/Flow B separation, archived record indication. | Reporting system verified. |
| **T9** | After Phase 9 | Billing tests: Billable Student calculation, Billing Cycle rules, price per Student, payment-status recording, Flow A/Flow B separation. | Billing system verified. |
| **T10** | After Phase 10 | Full regression: all automated tests pass, security checklist passes, UAT readiness confirmed. | Release acceptance criteria met. |

---

# 15. Documentation Milestones

Documentation milestones ensure that the canonical document set remains consistent and complete throughout development.

| Milestone | Phase | Documentation scope |
|---|---|---|
| **D1** | Before Phase 1 | Canonical document set established: `AI_DOCS/00` through `AI_DOCS/27`. |
| **D2** | After Phase 1 | Database schema documentation (migrations) consistent with `AI_DOCS/06_Database_Design.md` and `AI_DOCS/07_Data_Dictionary.md`. |
| **D3** | After Phase 2 | RBAC implementation consistent with `AI_DOCS/08_RBAC.md` and `AI_DOCS/09_Permission_Matrix.md`. |
| **D4** | After Phase 3 | Teacher Workspace features consistent with `AI_DOCS/02_Software_Requirements.md` Part 2. |
| **D5** | After Phase 4 | Student and Parent features consistent with `AI_DOCS/02_Software_Requirements.md` Parts 3–4. |
| **D6** | After Phase 5 | Attendance system consistent with `AI_DOCS/16_QR_Attendance_System.md`. |
| **D7** | After Phase 7 | Exam Engine consistent with `AI_DOCS/15_Exam_Engine.md`. |
| **D8** | After Phase 9 | Billing system consistent with `AI_DOCS/17_Subscription_Billing.md`. |
| **D9** | After Phase 10 | Full documentation consistency review against all AI_DOCS documents. |

---

# 16. Deployment Milestones

Deployment milestones define the infrastructure and release checkpoints.

| Milestone | Phase | Deployment scope |
|---|---|---|
| **DE1** | Before Phase 1 | cPanel Shared Hosting environment provisioned. MySQL 8 database created. Laravel Public Storage configured. Cron Jobs configured for Laravel Scheduler. |
| **DE2** | After Phase 1 | Staging environment deployed with foundation code. Database migrations applied. Basic authentication functional. |
| **DE3** | After Phase 2 | Staging environment updated with complete authentication and RBAC. All five roles can authenticate. |
| **DE4** | After Phase 3 | Staging environment updated with Teacher Workspace features. Educational Grades, Groups, Students, and Teacher Staff functional. |
| **DE5** | After Phase 4 | Staging environment updated with Student and Parent modules. Full role coverage functional. |
| **DE6** | After Phase 5 | Staging environment updated with Attendance system. Dynamic QR Code scanning functional. |
| **DE7** | After Phase 7 | Staging environment updated with Exam Engine. All question types and Bubble Sheet functional. |
| **DE8** | After Phase 9 | Staging environment updated with Billing system. Subscription calculation and payment-status recording functional. |
| **DE9** | After Phase 10 | Production environment prepared. Staging fully validated. UAT environment ready. |
| **DE10** | M11 | Production deployment. Release acceptance. |

---

# 17. Release Strategy

## 17.1 Release Approach

Version 1 is released as a single cohesive release. All ten phases contribute to the Version 1 release. There are no intermediate production releases; staging environment updates occur at each phase boundary for validation.

## 17.2 Release Criteria

A release is ready for production deployment when:

1. All automated tests pass (backend Feature, Unit; frontend integration).
2. No Critical or High severity bugs are open.
3. Every confirmed business rule has passing automated test coverage.
4. The complete authorization matrix has been tested for all five roles.
5. Teacher Workspace isolation is verified across all modules.
6. Archive and restore behavior is verified for all archivable entities.
7. Audit Log completeness is verified for all mandatory events.
8. Flow A and Flow B separation is verified.
9. Historical data preservation is verified.
10. The security checklist from `AI_DOCS/23_Security_Standards.md` passes.
11. UAT is completed by relevant role representatives.
12. The staging environment has been validated.

## 17.3 Rollback Criteria

A release must be rolled back if:

1. A Critical bug is discovered after deployment.
2. Teacher Workspace isolation is violated.
3. Historical data is lost or corrupted.
4. Audit Log entries are lost or modified.
5. Flow A and Flow B data is conflated.

## 17.4 Post-Release

After production release:

- Monitor operational logs and Audit Log for anomalies.
- Verify background job processing (Billing Cycle, Attendance cleanup, Exam grading).
- Verify that cPanel Cron Jobs are executing the Laravel Scheduler correctly.
- Verify that the Database Queue worker is processing jobs.
- Confirm that no Critical or High bugs are reported within the first operational period.

---

# 18. Versioning Strategy

## 18.1 Version 1

Version 1 is the confirmed product scope defined by the frozen `AI_DOCS/00_Project_Context.md`. It is a Web Application only with the confirmed technology stack, five roles, and all confirmed business rules.

## 18.2 Version Numbering

Version numbering follows semantic versioning principles:

- **Major version** (e.g., 1.0, 2.0): Indicates a significant product scope change, potentially including new roles, new modules, or confirmed infrastructure changes.
- **Minor version** (e.g., 1.1, 1.2): Indicates feature additions or improvements within the confirmed major version scope.
- **Patch version** (e.g., 1.0.1, 1.0.2): Indicates bug fixes, security patches, or minor corrections that do not change confirmed product scope.

## 18.3 Version 1 Scope Lock

Version 1 scope is locked by the frozen Project Context. No new features, roles, modules, or infrastructure requirements are added to Version 1 after the Project Context is frozen. All future scope changes require a separate approved decision.

## 18.4 API Versioning

All Version 1 API endpoints use the `/api/v1` prefix. Breaking API changes require a future API version. Version 1 API behavior must remain consistent with the frozen Project Context.

---

# 19. Risks & Dependencies

## 19.1 Development Risks

| Risk | Impact | Mitigation |
|---|---|---|
| Teacher Workspace isolation breach | Critical: data leak between Teachers. | Tenant isolation is the highest-priority test invariant. Every query, API response, and file access must be workspace-scoped. |
| PENDING decisions blocking development | Medium: non-payment enforcement (Q-005), Teacher Staff permission granularity (Q-011), Super Admin content visibility (Q-012), pricing model (Q-013), localization (Q-015). | Implement only confirmed rules. Use PENDING proposed defaults only as architectural placeholders that can be refined without contradicting confirmed rules. |
| cPanel Shared Hosting resource limits | Medium: process execution time, memory, disk, concurrent connections. | Design background jobs for chunked processing. Use pagination for all list endpoints. Optimize queries with workspace-scoped indexing. |
| Duplicate Student account edge cases | High: violating BR-022. | Implement duplicate prevention at the database level and the application level. Test both registration methods thoroughly. |
| Historical data loss | Critical: violating BR-007 and BR-014. | No permanent deletion anywhere. Archive replaces all deletion. Test that Student Group movement preserves all historical records. |
| Audit Log gaps | High: violating BR-006. | Audit Log recording is mandatory for all confirmed event types. Test Audit Log completeness for every important action. |
| Flow A / Flow B conflation | High: violating the two money flows separation. | Separate database entities, separate API endpoints, separate report sections, separate authorization rules. Test that no endpoint or report mixes the two flows. |
| Scope expansion pressure | Medium: introducing unconfirmed features. | The Project Context is frozen. All scope additions require formal Product Owner approval and separate documentation. |

## 19.2 External Dependencies

| Dependency | Impact | Status |
|---|---|---|
| cPanel Shared Hosting availability | Required for Version 1 deployment. | Confirmed primary deployment target. |
| MySQL 8 availability | Required for data persistence. | Confirmed database engine. |
| PHP 8.3 availability | Required for Laravel 12 backend. | Confirmed backend runtime. |
| Node.js availability | Required for React 19 frontend build. | Build-time dependency only. |
| SMTP availability | Required for mail transport baseline. | Confirmed; no notification features in V1. |
| QR scanner device compatibility | Required for ID Card Attendance method. | Hardware dependency; browser-based scanning also supported. |
| Super Admin pricing decisions | Required before Subscription calculation can be finalized. | Q-013 PENDING; flat price per Student proposed as default. |

## 19.3 Internal Dependencies

| Dependency | Dependent phase | Providing phase |
|---|---|---|
| Authentication infrastructure | Phase 2+ | Phase 1 |
| RBAC and permission matrix | Phase 3+ | Phase 2 |
| Database schema for domain entities | Phase 3+ | Phase 1 |
| Teacher Workspace isolation middleware | Phase 3+ | Phase 2 |
| Student management | Phases 4, 5, 6, 7, 8, 9 | Phase 3 |
| Group management | Phases 5, 6, 7, 8, 9 | Phase 3 |
| File upload infrastructure | Phases 6, 7 | Phase 3 |
| Background job infrastructure | Phases 5, 7, 8, 9 | Phase 1 |
| Archive infrastructure | All phases | Phase 1 |
| Audit Log infrastructure | All phases | Phase 1 |

---

# 20. Future Versions (v1.1, v2.0)

## 20.1 Version 1.1 — Enhanced Operations

Version 1.1 may include feature additions within the confirmed Version 1 architecture and technology stack. Potential areas include:

- **Enhanced Teacher Staff permissions** — after Q-011 is resolved with finer-grained capability-flag catalog and saveable named presets.
- **Super Admin content visibility refinement** — after Q-012 is resolved with confirmed visibility boundaries.
- **Non-payment enforcement** — after Q-005 is resolved with confirmed grace period, read-only enforcement, and reactivation behavior.
- **Pricing model refinement** — after Q-013 is resolved with flat price versus volume tier decision.
- **Enhanced reporting** — additional report types, filtering criteria, and export capabilities (requires separate approval).
- **Search and filtering enhancements** — saved filters, autocomplete, cross-module search (requires separate approval).
- **Background job refinement** — advanced queue monitoring, job prioritization refinement, batch operations.

## 20.2 Version 2.0 — Expanded Platform

Version 2.0 may include significant scope expansions that require separate architectural decisions. Potential areas include:

- **Native mobile application** — requires separate scope approval, architecture decisions, and authentication considerations (BR-017 currently excludes this).
- **Online payment gateway integration** — requires separate scope approval, security architecture, and payment processing design (BR-019 currently excludes this).
- **Notifications** — push, email, and SMS notifications require separate scope approval, delivery mechanism decisions, and infrastructure (currently out of scope).
- **Advanced media protection** — Lesson video hosting/protection with signed playback, streaming, and per-Teacher quota (Q-010 PENDING).
- **Localization** — Arabic-first RTL + English, per-Teacher timezone, platform-level currency (Q-015 PENDING).
- **Advanced analytics** — metrics, trends, forecasting, comparisons, and data-interpretation rules.
- **Infrastructure upgrades** — VPS/Cloud deployment, Redis cache and queue, S3 Storage, CDN, load balancing.
- **Multiple Teaching Subjects per Teacher** — requires separate scope approval and architectural decisions (BR-016 currently limits to one subject).
- **Advanced RBAC** — platform staff roles beyond the five confirmed roles.

## 20.3 Future Version Constraints

All future versions must preserve:

- Teacher Workspace isolation (BR-003).
- One global Student account (BR-001).
- One Parent account per Student in Version 1 (BR-020).
- Parent read-only access (BR-004).
- Archive instead of permanent deletion (BR-005).
- Immutable permanent Audit Log (BR-006).
- Historical data preservation (BR-014).
- Flow A and Flow B separation.
- Canonical terminology from `AI_DOCS/00_Project_Context.md` §19.
- No marketplace behavior.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Project Context alignment | Passed — roadmap follows the frozen Version 1 rules. All BR references, role definitions, scope boundaries, and confirmed/pending statuses are consistent with `AI_DOCS/00_Project_Context.md`. |
| Software Requirements alignment | Passed — phase scope maps to the module requirements defined in `AI_DOCS/02_Software_Requirements.md` Parts 2–5. Non-functional requirements from Part 6 are reflected in Phase 10 optimization. |
| System Architecture alignment | Passed — technology baseline (Laravel 12, PHP 8.3, React 19, TypeScript, Vite, Tailwind CSS, MySQL 8, Sanctum, Custom RBAC, File Cache, Database Queue, Database sessions, Laravel Public Storage, Laravel Scheduler with Cron Jobs, SMTP, Apache/LiteSpeed, cPanel Shared Hosting) is consistent with `AI_DOCS/03_System_Architecture.md` §4.1. |
| Project Structure alignment | Passed — development phases respect the repository structure defined in `AI_DOCS/04_Project_Structure.md`. Backend and frontend separation is preserved. |
| User Flows alignment | Passed — phase scope covers all 27 user flows defined in `AI_DOCS/05_User_Flows.md`. |
| Database Design alignment | Passed — entity coverage and tenant isolation strategy are consistent with `AI_DOCS/06_Database_Design.md`. Archive, Audit Log, and historical data requirements are preserved. |
| RBAC alignment | Passed — Phase 2 implements the authorization model from `AI_DOCS/08_RBAC.md` and `AI_DOCS/09_Permission_Matrix.md`. All five roles and their boundaries are covered. |
| API Design alignment | Passed — API endpoint coverage is consistent with `AI_DOCS/10_API_Design.md`. REST conventions, error responses, pagination, filtering, sorting, and authorization boundaries are preserved. |
| Backend Architecture alignment | Passed — development phases follow the layered architecture from `AI_DOCS/11_Backend_Architecture.md`. Feature-based organization, request lifecycle, middleware, and validation strategy are aligned. |
| Frontend Architecture alignment | Passed — frontend development approach is consistent with `AI_DOCS/12_Frontend_Architecture.md`. Role context handling, routing, state management, and API communication are aligned. |
| Exam Engine alignment | Passed — Phase 7 scope is consistent with `AI_DOCS/15_Exam_Engine.md`. Question Bank privacy, all four question types, Bubble Sheet, Essay pending state, and workspace scoping are preserved. |
| QR Attendance alignment | Passed — Phase 5 scope is consistent with `AI_DOCS/16_QR_Attendance_System.md`. All three Attendance methods, Dynamic QR Code generation, workspace scoping, and history preservation are preserved. |
| Subscription/Billing alignment | Passed — Phase 9 scope is consistent with `AI_DOCS/17_Subscription_Billing.md`. Calendar-month Billing Cycle, Billable Student calculation, Enrollment-duration-only rule, and Flow A/Flow B separation are preserved. |
| Reporting alignment | Passed — Phase 8 scope is consistent with `AI_DOCS/18_Reporting_Analytics.md`. All report domains, role-appropriate visibility, historical data inclusion, and Flow A/Flow B separation are preserved. |
| File Storage alignment | Passed — file storage references in Phases 6 and 7 are consistent with `AI_DOCS/20_File_Storage.md`. Laravel Public Storage, workspace ownership, and file type restrictions are preserved. |
| Background Jobs alignment | Passed — background job references in Phases 5, 7, 8, and 9 are consistent with `AI_DOCS/21_Background_Jobs.md`. Database Queue, scheduled tasks, idempotency, and failure handling are preserved. |
| Search & Filtering alignment | Passed — Phase 10 references are consistent with `AI_DOCS/22_Search_Filtering.md`. Scope resolution before filtering, cross-Teacher discovery prevention, and Archive-aware results are preserved. |
| Security Standards alignment | Passed — Phase 2 and Phase 10 references are consistent with `AI_DOCS/23_Security_Standards.md`. Authentication, authorization, input validation, and security checklist are aligned. |
| Testing Strategy alignment | Passed — testing milestones are consistent with `AI_DOCS/24_Testing_Strategy.md`. Testing layers, coverage expectations, and release acceptance criteria are aligned. |
| Performance & Scalability alignment | Passed — Phase 10 references are consistent with `AI_DOCS/25_Performance_Scalability.md`. Query optimization, caching, pagination, and cPanel compatibility are preserved. |
| Deployment Plan alignment | Passed — deployment milestones are consistent with `AI_DOCS/26_Deployment_Plan.md`. Environment setup, staging validation, and production deployment procedures are aligned. |
| Teacher Workspace isolation | Passed — isolation is the highest-priority development principle. Every phase preserves tenant boundaries. Cross-Teacher access prevention is a mandatory acceptance criterion for every phase. |
| Student account rules | Passed — one global Student account, duplicate prevention, two registration methods, per-Teacher partitioning, and Group movement history are preserved across all relevant phases. |
| Parent access rules | Passed — linked-Student read-only access, one Parent per Student, and Student Switcher are preserved in Phase 4 and subsequent phases. |
| Archive policy | Passed — no permanent deletion is referenced in any phase. Archive replaces deletion per BR-005. Historical data preservation is a mandatory acceptance criterion. |
| Audit Log policy | Passed — all 10 mandatory event types are covered across the relevant phases. Immutability, permanent retention, and Teacher Staff attribution are preserved. |
| Payment handling | Passed — Version 1 records payment status only. Flow A and Flow B separation is tested in Phase 9 and verified in Phase 10. |
| Version 1 scope | Passed — no native mobile, payment gateway, notification, marketplace, video homework, Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices are introduced as V1 requirements. |
| PENDING items | Passed — non-payment enforcement (Q-005), Teacher Staff permission granularity (Q-011), Super Admin content visibility (Q-012), pricing model (Q-013), and localization (Q-015) are preserved as PENDING and not silently hardened. |
| Canonical terminology | Passed — Teacher Workspace, Educational Grade, Teaching Subject, Group, Student, Parent, Teacher Staff, Super Admin, Subscription, Flow A, Flow B, Enrollment, Archive, Audit Log, Dynamic QR Code, ID Card, Question Bank, Bubble Sheet, Lesson, Billable Student, Billing Cycle, and Homework are used consistently. |
| No source code | Passed — no source code, APIs, SQL, database tables, UI implementation, or implementation details are defined. |
