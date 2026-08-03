# 24 — Testing Strategy

## Document Scope

This document defines the complete testing strategy for Version 1 of the Unified Education Platform. It establishes testing standards, approaches, coverage expectations, environment requirements, and acceptance criteria across all layers of the system.

This document does not define source code, test scripts, test fixtures, APIs, database tables, UI implementation, or physical configuration. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found.

The testing strategy is built on the confirmed Version 1 technology baseline: **Laravel 12** backend with **PHP 8.3**, **React 19** frontend with **TypeScript**, **MySQL 8** for persistence, **Laravel Sanctum** for authentication, **Laravel Gates & Policies with Custom RBAC** for authorization, **File Cache**, **Database Queue**, **Database session driver**, **Laravel Public Storage**, and **cPanel Shared Hosting** as the primary deployment target.

---

# 1. Testing Overview

Testing is a mandatory quality gate for every feature, business rule, and non-functional requirement in the Unified Education Platform. The testing strategy ensures that confirmed business rules are enforced, Teacher Workspace isolation is preserved, and the Platform behaves correctly across all five confirmed roles: Super Admin, Teacher, Teacher Staff, Student, and Parent.

Testing covers the following layers:

- **Backend logic** — business rules, authorization, tenant isolation, validation, Archive behavior, Audit Log recording, and background job correctness.
- **Frontend behavior** — role-based rendering, route guards, form validation, error handling, and client-side state management.
- **API contracts** — request/response validation, authentication, authorization, error responses, pagination, filtering, sorting, and tenant-scoped data access.
- **Database integrity** — data constraints, referential integrity, Archive state, historical preservation, and tenant isolation at the persistence layer.
- **Cross-layer integration** — end-to-end workflows that span frontend, API, backend services, and database.
- **Non-functional qualities** — performance, security, accessibility, and compatibility with the confirmed cPanel Shared Hosting baseline.

The testing strategy must preserve all confirmed business rules from the Project Context, including:

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

# 2. Testing Objectives

The confirmed testing objectives are to:

1. **Verify business rule enforcement** — confirm that every confirmed business rule (BR-xxx) is correctly enforced at the backend, regardless of frontend behavior.
2. **Validate Teacher Workspace isolation** — ensure that no Teacher can see, modify, or discover another Teacher's data through any access path.
3. **Confirm RBAC correctness** — verify that all five roles have exactly the permissions defined in `AI_DOCS/08_RBAC.md` and `AI_DOCS/09_Permission_Matrix.md`.
4. **Protect historical integrity** — confirm that Student transfers, Archive operations, and structural changes never destroy, detach, or rewrite historical records.
5. **Ensure Audit Log completeness** — verify that every mandatory Audit Log event (§10.1 of the Project Context) is recorded for every role and every surface.
6. **Validate financial separation** — confirm that Flow A and Flow B data, filters, reports, and authorization never mix.
7. **Support cPanel Shared Hosting compatibility** — verify that tests execute correctly within the MySQL 8, Laravel 12, Database Queue, File Cache, and Cron Jobs baseline.
8. **Prevent regressions** — ensure that new changes do not break existing confirmed behavior.
9. **Guide release acceptance** — provide clear criteria for determining when a feature or release is ready for deployment.
10. **Enable future automation** — establish patterns that support gradual test automation expansion without requiring unconfirmed infrastructure.

---

# 3. Testing Scope

## 3.1 In-Scope for Version 1 Testing

| Area | Testing coverage |
|---|---|
| Authentication | Login, logout, session management, Student self-registration, Teacher-created Student activation, duplicate prevention. |
| Authorization | Role-based access for all five roles, Teacher Workspace scope, Student self-scope, Parent linked-Student scope, Teacher Staff assigned permissions, Super Admin Platform scope. |
| Teacher Workspace isolation | Cross-Teacher data access prevention across all modules: Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Users, Settings, Files, payment status. |
| Educational Grades | Create, view, update, Archive, restore, workspace isolation, Audit Log. |
| Groups | Create, view, update, Archive, restore, Pricing Type validation (Monthly / Per Lesson), Student movement, history preservation. |
| Students | Registration (self and Teacher-created), duplicate prevention, assignment, Group movement, history preservation, Archive/restore. |
| Attendance | Dynamic QR Code, ID Card, manual entry, workspace scoping, history preservation, Audit Log, exclusion from Billable Student calculation. |
| Homework | Create, submit, grade, Text/Image/PDF format enforcement, video rejection, Archive/restore, Audit Log. |
| Exams | Question Bank ownership, question types (MC/TF/Essay/Bubble Sheet), Exam composition, attempts, automatic grading, Essay pending state, workspace scoping. |
| Lessons | Teacher-owned privacy, student access, cross-Teacher prevention, Archive/restore. |
| Reports | Workspace-scoped Teacher reports, Student self-reports, Parent linked-Student reports, Super Admin Platform reports, Flow A/Flow B separation, archived record indication. |
| Payments (Flow B) | Status recording only, no transaction processing, workspace isolation, Student/Parent visibility. |
| Subscriptions (Flow A) | Billable Student calculation (>15 days, Enrollment duration only), Billing Cycle (calendar month), price per Student, snapshot immutability, Super Admin management. |
| Teacher Staff | Creation, permission assignment, workspace isolation, Audit Log attribution. |
| Settings | Teacher profile, Teaching Subject immutability, Platform Settings, Student/Parent account settings. |
| Files | Upload validation (type, size, MIME), workspace ownership, cross-Teacher prevention, Parent upload denial, video homework rejection, Archive/restore. |
| Archive | Active/Archived state for all archivable entities, exclusion from active searches, inclusion in reports, restoration, Audit Log recording. |
| Audit Log | All 10 mandatory event types, immutability, permanent retention, Teacher Staff attribution, scope visibility. |
| Background Jobs | Billing Cycle initialization, Billable Student calculation, Subscription snapshot, QR cleanup, Exam auto-grading, report preparation, file reference integrity, idempotency. |
| Search & Filtering | Scope resolution before filtering, cross-Teacher discovery prevention, Archive-aware results, Flow A/Flow B separation, pagination, sorting. |
| Security | Authentication enforcement, authorization enforcement, input validation, SQL injection prevention, XSS prevention, CSRF protection, rate limiting, session management, password policy. |

## 3.2 Out-of-Scope for Version 1 Testing

| Area | Reason |
|---|---|
| Native mobile application testing | Out of scope for Version 1 (BR-017). |
| Online payment gateway testing | Out of scope; Version 1 records status only (BR-019). |
| Notification testing (push, email, SMS) | Out of scope for Version 1. |
| Marketplace behavior testing | The Platform is not a marketplace. |
| Video homework testing | Homework supports Text, Image, and PDF only (BR-021). |
| Multiple Teaching Subjects per Teacher | Version 1 supports exactly one subject per Teacher (BR-016). |
| Multiple Parent accounts per Student | Version 1 supports exactly one Parent per Student (BR-020). |
| Docker, Redis, Kubernetes, S3, WebSockets testing | Not required for Version 1. |
| Non-payment enforcement testing | PENDING (Q-005). |
| Teacher Staff permission granularity testing | PENDING (Q-011). |
| Super Admin content visibility testing | PENDING (Q-012). |
| Localization/regional testing | PENDING (Q-015). |
| Performance benchmark thresholds | No confirmed numeric targets exist in the Project Context. |

---

# 4. Testing Principles

The following principles govern all testing in Version 1:

1. **Backend is the final authority.** No test may treat frontend visibility, hidden controls, or UI state as a substitute for backend authorization or business-rule enforcement.

2. **Teacher Workspace isolation is the highest-priority invariant.** Every test that involves Teacher-owned data must verify that the data is accessible only within the correct Teacher Workspace.

3. **Tests must not bypass authorization.** Test helpers that create authenticated contexts must use the same authentication and authorization paths as production requests.

4. **Test data must be deterministic.** Tests must use factories, fixtures, or builders that produce known, reproducible data states. Tests must not depend on production data or shared mutable state.

5. **Tests must be isolated.** Each test must start with a clean data state or explicitly manage its own setup and teardown. Tests must not depend on execution order.

6. **Archive replaces deletion in tests.** Tests must verify Archive behavior, not hard-delete behavior. No test should assert that a record is permanently deleted.

7. **Historical data must be testable.** Tests must verify that Student transfers, Group movements, and Archive operations preserve historical records.

8. **Flow A and Flow B must be tested separately.** Tests must verify that Subscription (Flow A) and payment-status (Flow B) data, logic, and authorization remain separate.

9. **Canonical terminology must be used in tests.** Test names, descriptions, and assertions must use canonical terms: Teacher Workspace, Educational Grade, Lesson, Archive, Subscription (Flow A), payment status (Flow B), Billing Cycle, Billable Student.

10. **Tests must be compatible with cPanel Shared Hosting.** Test infrastructure must use MySQL 8, Database Queue, File Cache, and Database sessions. Tests must not require Redis, Docker, external search engines, or unconfirmed infrastructure.

11. **Tests must not introduce unconfirmed features.** Test scenarios must not assume non-payment enforcement, notification behavior, payment gateway integration, or other PENDING items.

12. **Test coverage must be traceable.** Every confirmed business rule (BR-xxx) should have at least one test that verifies its enforcement.

---

# 5. Unit Testing

## 5.1 Purpose

Unit tests verify individual business logic components in isolation. They confirm that domain rules, calculations, validators, and value objects behave correctly without depending on HTTP requests, database state, or frontend rendering.

## 5.2 Scope

| Area | Unit test focus |
|---|---|
| Billable Student calculation | Verify that only Enrollment duration > 15 calendar days counts. Verify that Attendance, login activity, Homework, Exam, and Lesson activity are excluded. Verify that 15 days exactly is not Billable. Verify that 16 days is Billable. |
| Billing Cycle validation | Verify calendar-month boundaries (first day to last day). Verify automatic new-cycle behavior. |
| Flow A / Flow B separation | Verify that Subscription logic never reads Flow B data, and payment-status logic never reads Flow A data. |
| Pricing calculation | Verify Monthly Subscription = Billable Students × Price Per Student. Verify historical price preservation. |
| Duplicate Student prevention | Verify that registration logic rejects duplicate accounts for both self-registration and Teacher-created methods. |
| One Group per Teacher per Student | Verify that assignment logic prevents a Student from belonging to more than one Group for the same Teacher. |
| Teaching Subject immutability | Verify that the Teaching Subject cannot be changed after account creation. |
| Homework format validation | Verify that only Text, Image, and PDF are accepted. Verify that video formats are rejected. |
| Question type validation | Verify that only Multiple Choice, True/False, Essay, and Bubble Sheet are accepted. |
| Pricing Type validation | Verify that only Monthly and Per Lesson are accepted. |
| Archive state logic | Verify that active/archived state is correctly represented and that archived records are excluded from active queries. |
| Date range validation | Verify that from_date must not be after to_date. Verify that dates must be valid calendar dates. |

## 5.3 Backend Unit Testing Framework

Backend unit tests use the testing framework provided by Laravel 12 (PHPUnit or Pest, as configured in `phpunit.xml`). Unit tests must:

- Test individual service methods, value objects, and calculation logic in isolation.
- Mock external dependencies where appropriate.
- Not require database state for pure logic tests.
- Use the `tests/Unit/` directory structure defined in `AI_DOCS/04_Project_Structure.md`.

## 5.4 Frontend Unit Testing

Frontend unit tests verify individual React components, hooks, utility functions, and TypeScript type guards in isolation. Frontend unit tests must:

- Test component rendering, state management, and event handling.
- Verify that role-based conditional rendering produces the correct output for each role.
- Not make real API calls; API responses should be mocked.
- Use the `frontend/src/test/` shared test utilities.

---

# 6. Feature Testing

## 6.1 Purpose

Feature tests verify complete feature workflows through the backend's HTTP layer. They confirm that authentication, authorization, business logic, validation, persistence, Audit Log recording, and error handling work correctly together for a single feature area.

## 6.2 Scope

Feature tests must cover every confirmed feature module:

| Module | Key feature test scenarios |
|---|---|
| **Authentication** | Login success, login failure, Student self-registration, Teacher-created Student activation, duplicate account prevention, logout, session behavior. |
| **Educational Grades** | Create, view, update, Archive, restore, workspace isolation, Audit Log recording. |
| **Groups** | Create under active Educational Grade, update, Archive, restore, Pricing Type validation, Student assignment, Student movement, history preservation. |
| **Students** | Register new Student, assign existing Student, search, move between Groups, Archive/restore, duplicate prevention, Audit Log. |
| **Attendance** | Dynamic QR Code generation and scan, ID Card scan, manual entry, workspace scoping, duplicate scan prevention, history preservation, Audit Log. |
| **Homework** | Create (Text/Image/PDF), submit (Student), grade (Teacher), Archive/restore, video format rejection, Audit Log. |
| **Exams** | Question Bank CRUD, Exam creation from own Question Bank, publish, Student attempt, automatic grading (MC/TF/Bubble Sheet), Essay pending, grade history preservation, workspace isolation. |
| **Lessons** | Create, upload video, Student access, cross-Teacher prevention, Archive/restore. |
| **Reports** | Attendance reports, Homework reports, Exam result reports, payment-status reports, Student performance reports, workspace scoping, archived record indication, Flow A/Flow B separation. |
| **Payments (Flow B)** | Record payment status, update status, workspace isolation, Student/Parent visibility, no transaction processing. |
| **Subscriptions (Flow A)** | View Subscription records, calculate Billable Students, Billing Cycle management, Super Admin management, payment status recording. |
| **Teacher Staff** | Create, update, assign permissions, Archive/restore, workspace isolation, Audit Log attribution. |
| **Settings** | Teacher profile update, Teaching Subject immutability, Platform Settings, Student account settings. |
| **Files** | Upload validation, workspace ownership, cross-Teacher prevention, Parent upload denial, video homework rejection, Archive/restore. |
| **Archive** | Archive and restore for every archivable entity, active search exclusion, report inclusion, historical relationship preservation. |
| **Audit Log** | Verify recording for all 10 mandatory event types, immutability, Teacher Staff attribution, scope visibility. |
| **Search & Filtering** | Scope resolution before filtering, cross-Teacher discovery prevention, Archive-aware results, pagination, sorting, empty result handling. |

## 6.3 Backend Feature Testing Framework

Backend feature tests use Laravel's built-in HTTP testing capabilities. Feature tests must:

- Send authenticated requests through the API layer defined in `AI_DOCS/10_API_Design.md`.
- Verify response status codes, response structure, and data correctness.
- Verify that Audit Log entries are created for mandatory events.
- Verify that workspace-scoped data is correctly isolated.
- Use the `tests/Feature/` directory structure.
- Use factories and seeders from `database/factories/` and `database/seeders/`.

## 6.4 Frontend Integration Testing

Frontend integration tests verify feature composition, routing, forms, query-state management, and component interaction. Frontend integration tests must:

- Test feature modules defined in `frontend/src/features/`.
- Verify route guards as usability behavior (not as security enforcement).
- Verify that role-based context switching produces the correct UI state.
- Verify form validation using Zod schemas and React Hook Form.
- Mock API responses rather than connecting to a live backend.
- Use the `frontend/tests/integration/` directory structure.

---

# 7. Integration Testing

## 7.1 Purpose

Integration tests verify that multiple components work together correctly across layers. They confirm that the frontend, API, backend services, and database interact as expected for complete workflows.

## 7.2 Scope

| Integration area | Key scenarios |
|---|---|
| **Authentication + Authorization** | Login → role resolution → workspace context → protected resource access. Verify that a Teacher cannot access another Teacher's data after authentication. |
| **Student lifecycle** | Registration → assignment to Group → Attendance → Homework → Exam → Group movement → history preservation → Archive/restore. |
| **Teacher Workspace isolation** | Create data in Workspace A → attempt to access from Workspace B → verify denial across all modules. |
| **Exam workflow** | Question Bank creation → Exam composition → publish → Student attempt → automatic grading → Essay pending → Teacher grading → result visibility for Student and Parent. |
| **Attendance workflow** | Dynamic QR Code generation → Student scan → Attendance recording → duplicate prevention → Audit Log. |
| **Homework workflow** | Teacher creates Homework → Student submits → Teacher grades → Archive → history preservation. |
| **Subscription lifecycle** | Billing Cycle initialization → Billable Student calculation → Subscription amount → Snapshot generation → payment status recording. |
| **Parent monitoring** | Parent login → linked Student selection → Homework/Attendance/Exam/Payment view → read-only enforcement. |
| **Archive lifecycle** | Create record → Archive → verify active search exclusion → verify report inclusion → restore → verify active search inclusion. |
| **Search & Filtering** | Scope resolution → filter application → Archive-aware results → pagination → sorting → empty result handling. |
| **Background jobs** | Job dispatch → processing → idempotency verification → failure handling → Audit Log recording. |

## 7.3 Integration Testing Approach

Integration tests must:

- Test complete workflows that span multiple features and layers.
- Use the database (MySQL 8 in a testing environment) rather than mocking persistence.
- Verify that background jobs execute correctly and produce expected results.
- Verify that Audit Log entries are created in the correct transactional context.
- Verify that Archive and historical data rules are preserved across multi-step workflows.

---

# 8. API Testing

## 8.1 Purpose

API tests verify that the REST API endpoints defined in `AI_DOCS/10_API_Design.md` behave correctly, return proper responses, enforce authorization, and preserve data integrity.

## 8.2 Scope

Every confirmed API endpoint must be tested for:

| Concern | Verification |
|---|---|
| **Authentication** | Unauthenticated requests are rejected with 401. |
| **Authorization** | Unauthorized roles receive 403. Teacher cannot access another Teacher's endpoints. Student cannot access another Student's data. Parent cannot access unlinked Student data. |
| **Validation** | Invalid input receives 422 with field-level error messages. Required fields are enforced. Enum values are validated. Date ranges are validated. |
| **Success responses** | Correct HTTP status codes (200, 201). Correct response structure per `AI_DOCS/10_API_Design.md` §6. Correct data within authorized scope. |
| **Pagination** | Paginated endpoints return correct `meta.current_page`, `meta.per_page`, `meta.total`, `meta.last_page`. Pagination reflects only authorized records. |
| **Filtering** | Filters apply after authorization and scope resolution. Cross-Teacher filters are rejected. Archived records are excluded from active filters. |
| **Sorting** | Ascending and descending order work correctly. Unsupported sort fields are rejected. Sorting does not expose unauthorized data. |
| **Error responses** | Error responses use the standardized structure. Error responses do not expose Teacher-private data, implementation details, or credentials. |
| **Archive/restore** | Archive endpoints correctly mark records as archived. Restore endpoints correctly reactivate records. Archived records are excluded from active list endpoints. |
| **Workspace isolation** | Every Teacher Workspace-scoped endpoint returns only data from the correct workspace. |

## 8.3 API Testing Approach

API tests are implemented as backend Feature tests that send HTTP requests to `/api/v1` endpoints. API tests must:

- Test every endpoint listed in `AI_DOCS/10_API_Design.md`.
- Verify response status codes, headers, and body structure.
- Verify that pagination, filtering, and sorting work correctly per `AI_DOCS/22_Search_Filtering.md`.
- Verify that error responses follow the standardized error structure.
- Verify that rate limiting returns 429 where applicable.

---

# 9. Authentication Testing

## 9.1 Purpose

Authentication tests verify that the Laravel Sanctum-based authentication system correctly identifies users, establishes sessions, records login events, and prevents unauthorized access.

## 9.2 Test Scenarios

| Scenario | Expected behavior |
|---|---|
| Valid credentials for each role | Successful login, authenticated context established, Audit Log entry recorded. |
| Invalid credentials | Login rejected, generic error message, failed login recorded in Audit Log. |
| Unauthenticated access to protected endpoint | 401 response. |
| Student self-registration with unique data | Account created, no duplicate. |
| Student self-registration with duplicate data | Account creation rejected. |
| Teacher-created Student account activation | Account activated, no duplicate created. |
| Activation with invalid identifier | Activation rejected. |
| Logout | Session destroyed, authenticated context cleared. |
| Session expiration | Protected access denied after session timeout. |
| Password change | Old sessions invalidated. |
| Password reset | Time-limited token, single use, rate-limited, Audit Log entry. |
| Rate limiting on login | 429 response after threshold exceeded. |
| Login does not reveal account existence | Generic error for both non-existent and wrong-password cases. |

---

# 10. Authorization Testing

## 10.1 Purpose

Authorization tests verify that the Laravel Gates & Policies with Custom RBAC system correctly enforces role boundaries, scope boundaries, and permission assignments.

## 10.2 Test Scenarios by Role

### Super Admin

| Scenario | Expected behavior |
|---|---|
| Access Platform-level Teacher management | Allowed. |
| Access another Teacher's Teacher Workspace | Denied. |
| Access Teacher-private content beyond confirmed visibility | Denied (PENDING Q-012). |
| Attempt hard deletion | Denied; Archive required. |
| Process online payment | Denied; out of scope. |

### Teacher

| Scenario | Expected behavior |
|---|---|
| Access own Teacher Workspace data | Allowed. |
| Access another Teacher's data | Denied. |
| Change Teaching Subject after registration | Denied. |
| Create duplicate Student account | Denied. |
| Assign Student to more than one Group | Denied. |

### Teacher Staff

| Scenario | Expected behavior |
|---|---|
| Access creating Teacher Workspace with assigned permission | Allowed. |
| Access creating Teacher Workspace without assigned permission | Denied. |
| Access another Teacher Workspace | Denied. |
| Self-assign permissions | Denied. |

### Student

| Scenario | Expected behavior |
|---|---|
| Access own account and per-Teacher records | Allowed. |
| Access another Student's records | Denied. |
| Access Teacher Workspace management areas | Denied. |
| Access Teacher's private Question Bank outside assigned Exam | Denied. |
| Manually modify Attendance | Denied. |
| Submit video homework | Denied. |

### Parent

| Scenario | Expected behavior |
|---|---|
| View linked Student data (read-only) | Allowed. |
| View unlinked Student data | Denied. |
| Modify Attendance, Homework, Exams, grades, payment status | Denied. |
| Upload files | Denied. |
| Access Teacher Workspace operations | Denied. |

---

# 11. Database Testing

## 11.1 Purpose

Database tests verify that the MySQL 8 persistence layer correctly enforces data integrity, tenant isolation, Archive behavior, and historical preservation.

## 11.2 Test Scenarios

| Scenario | Expected behavior |
|---|---|
| Teacher Workspace isolation at query level | Queries scoped to correct Teacher Workspace; no cross-tenant data returned. |
| Student global uniqueness | Duplicate Student accounts prevented at database level. |
| One Group per Student per Teacher | Database constraint prevents violation. |
| One Parent per Student | Database constraint prevents violation. |
| Enrollment history preservation | Student Group movement creates new Enrollment without deleting prior records. |
| Archive state consistency | Archived records are excluded from active queries; included in historical queries. |
| Audit Log immutability | Audit Log entries cannot be modified or deleted. |
| Historical data retention | No query or operation permanently deletes records. |
| Flow A / Flow B separation | Subscription data and payment-status data are stored and queried separately. |
| Referential integrity on Archive | Archiving a parent record does not break historical child record references. |
| File reference integrity | File references maintain valid ownership context. |

## 11.3 Database Testing Approach

Database tests are implemented as backend Feature or Unit tests that verify persistence behavior through Laravel's Eloquent ORM and Query Builder. Database tests must:

- Use a dedicated test database (not production).
- Use factories for deterministic test data.
- Verify that database constraints enforce business rules at the persistence layer.
- Verify that Archive and historical queries produce correct results.
- Not define or require physical schema changes beyond the confirmed migration set.

---

# 12. UI Testing

## 12.1 Purpose

UI tests verify that the React 19 frontend correctly presents role-specific experiences, handles user interactions, communicates with the backend, and manages client-side state.

## 12.2 Scope

| Area | UI test focus |
|---|---|
| Role-based rendering | Each role sees only their authorized navigation, modules, and data. |
| Route guards | Unauthorized route access redirects or denies appropriately. |
| Form validation | Client-side validation using Zod schemas and React Hook Form provides immediate feedback. |
| Error states | API errors, validation failures, and empty states are handled gracefully. |
| Loading states | Async operations show appropriate loading indicators. |
| Context switching | Student Switcher (Parent), Teacher Workspace context (Teacher/Teacher Staff), and role context (multi-role users) work correctly. |
| Search & Filtering UI | Search inputs, filter controls, sort controls, and pagination navigation function correctly per `AI_DOCS/22_Search_Filtering.md`. |
| Accessibility | Keyboard navigation, screen reader compatibility, focus management, and clear labeling work correctly. |
| RTL support | Layout supports right-to-left rendering where applicable (Q-015 PENDING). |

## 12.3 UI Testing Approach

Frontend UI tests use the testing framework configured in the React 19 project (e.g., Vitest with React Testing Library). UI tests must:

- Test component rendering and interaction in isolation.
- Mock API responses rather than connecting to a live backend.
- Verify that disabled buttons, hidden controls, and omitted UI elements are usability aids, not security enforcement.
- Use the `frontend/tests/integration/` and `frontend/tests/e2e/` directory structures.

---

# 13. Performance Testing

## 13.1 Purpose

Performance tests verify that the Platform operates within acceptable resource bounds on the confirmed cPanel Shared Hosting baseline.

## 13.2 Scope

| Area | Performance test focus |
|---|---|
| API response time | Endpoints respond within acceptable bounds for typical workloads. |
| Database query efficiency | Queries use appropriate indexes and scope constraints; no full-table scans for common operations. |
| Pagination performance | Large result sets are paginated; large offset values do not cause excessive query time. |
| Background job execution | Jobs complete within shared hosting time limits; long-running jobs are chunked. |
| File upload handling | Uploads respect shared hosting memory and execution time limits. |
| Cache effectiveness | File Cache reduces repeated query load for frequently accessed data. |
| Concurrent user handling | Multiple authenticated users can operate simultaneously without degradation. |

## 13.3 Performance Testing Approach

Performance tests must:

- Use realistic data volumes that represent expected Version 1 workloads.
- Measure response times, query counts, and memory usage.
- Identify bottlenecks in query execution, background job processing, and file handling.
- Verify that performance does not degrade authorization, tenant isolation, or Archive behavior.
- Not define unconfirmed numeric targets; the Project Context does not include response-time, throughput, or concurrency thresholds.

## 13.4 Performance Constraints

- Performance tests must not require Redis, external search engines, S3 Storage, Docker, Kubernetes, WebSockets, or Microservices.
- Performance optimization must never bypass authorization, Teacher Workspace isolation, Archive policy, or historical data retention.

---

# 14. Security Testing

## 14.1 Purpose

Security tests verify that the Platform's security controls correctly protect data, enforce access boundaries, and prevent common vulnerabilities.

## 14.2 Scope

| Area | Security test focus |
|---|---|
| Authentication bypass | Unauthenticated requests cannot access protected endpoints. |
| Authorization bypass | Users cannot access resources outside their role, scope, or permissions. |
| Tenant isolation bypass | Teachers cannot access another Teacher's data through any access path. |
| SQL injection | Parameterized queries prevent injection; no raw SQL with unsanitized input. |
| XSS prevention | User input is properly encoded; React auto-escaping is not bypassed. |
| CSRF protection | State-changing requests require valid CSRF tokens. |
| Rate limiting | Login, registration, QR scanning, and file upload endpoints are rate-limited. |
| Session security | Session cookies have HttpOnly, Secure, and SameSite flags. Sessions expire correctly. |
| Password security | Passwords are hashed (bcrypt/Argon2id), never stored in plain text, never logged. |
| File upload security | File type, size, and MIME validation. Path traversal prevention. Cross-Teacher file access denial. |
| Error message safety | Error responses do not expose Teacher-private data, credentials, SQL, stack traces, or implementation details. |
| Sensitive data handling | Passwords, tokens, and credentials are never exposed in responses, logs, or error messages. |

## 14.3 Security Testing Approach

Security tests must:

- Verify every security control listed in `AI_DOCS/23_Security_Standards.md`.
- Test for common web application vulnerabilities (OWASP Top 10).
- Verify that security controls are enforced server-side, not just in the frontend.
- Verify that the security checklist in `AI_DOCS/23_Security_Standards.md` §21 passes.

---

# 15. Regression Testing

## 15.1 Purpose

Regression tests ensure that new changes do not break existing confirmed behavior. They protect against unintended side effects in business rules, authorization, tenant isolation, and historical data integrity.

## 15.2 Regression Strategy

| Approach | Description |
|---|---|
| **Automated test suite** | The complete backend Feature and Unit test suite must pass before any release. The frontend integration test suite must pass before any frontend release. |
| **Business rule regression** | Every confirmed business rule (BR-xxx) must have at least one automated test that verifies its enforcement. Changes to any feature must not break existing BR tests. |
| **Authorization regression** | The complete authorization test matrix (§10) must pass after any change to authentication, authorization, middleware, policies, or gates. |
| **Tenant isolation regression** | Cross-Teacher isolation tests must pass after any change to queries, services, repositories, or middleware that touches Teacher Workspace data. |
| **Archive regression** | Archive and restore tests must pass after any change to data lifecycle logic. |
| **Audit Log regression** | Audit Log recording tests must pass after any change to service layers that produce mandatory Audit Log events. |
| **Flow A / Flow B regression** | Financial separation tests must pass after any change to Subscription or payment-status logic. |

## 15.3 Regression Execution

- Full regression must execute before every release.
- Partial regression (affected feature areas) may execute during development iterations.
- Regression failures must block release until resolved.

---

# 16. User Acceptance Testing (UAT)

## 16.1 Purpose

UAT verifies that the Platform meets the confirmed functional requirements from the perspective of each user role. UAT is performed by stakeholders who represent or are the actual users.

## 16.2 UAT by Role

| Role | UAT focus | UAT participants |
|---|---|---|
| **Super Admin** | Teacher management, Subscription management, pricing, Platform Settings, global reports, Audit Log visibility. | Platform owner or designated representative. |
| **Teacher** | Educational Grade and Group management, Student management, Attendance (all three methods), Homework, Exams, Lessons, Reports, Teacher Staff, Settings. | Teachers using the platform. |
| **Teacher Staff** | Module access within assigned permissions, workspace isolation. | Teacher Staff users with various permission assignments. |
| **Student** | Dashboard, schedule, Homework, Exams, Lessons, Subscriptions (Flow B), Settings, self-registration, account activation. | Students using the platform. |
| **Parent** | Dashboard, Student Switcher, Homework, Attendance, Exams, Teachers, Payments, read-only enforcement. | Parents monitoring linked Students. |

## 16.3 UAT Criteria

UAT must verify:

- All confirmed user flows from `AI_DOCS/05_User_Flows.md` are completable.
- All five roles can perform their confirmed actions.
- No role can perform actions outside their confirmed permissions.
- Teacher Workspace isolation is preserved in all workflows.
- Historical data is preserved through Student transfers and Archive operations.
- Flow A and Flow B are clearly separated in all visible contexts.
- Error messages are helpful without exposing private data.
- The Platform is usable through the Web Application on supported browsers.

## 16.4 UAT Environment

UAT must be performed in a dedicated environment that mirrors the production configuration (cPanel Shared Hosting baseline) without using production data or credentials.

---

# 17. Bug Reporting Workflow

## 17.1 Bug Classification

| Severity | Description | Examples |
|---|---|---|
| **Critical** | Breaks a core business rule, violates Teacher Workspace isolation, exposes private data, or prevents a confirmed workflow from completing. | Cross-Teacher data access, duplicate Student accounts created, Audit Log entries lost, permanent deletion occurs. |
| **High** | Breaks a confirmed feature for one or more roles, produces incorrect results, or violates a confirmed business rule without data exposure. | Billable Student calculation incorrect, Homework video format accepted, Parent can modify records, archived records appear as active. |
| **Medium** | Feature works partially but has unexpected behavior, missing validation, or degraded user experience. | Incorrect error message, missing empty-state handling, filter returns unexpected results, pagination count incorrect. |
| **Low** | Cosmetic issue, minor UX inconvenience, or documentation inconsistency. | Label inconsistency, alignment issue, missing canonical terminology. |

## 17.2 Bug Report Requirements

Every bug report must include:

1. **Severity** — Critical, High, Medium, or Low.
2. **Role context** — Which role was active when the bug occurred.
3. **Module** — Which feature area is affected.
4. **Steps to reproduce** — Exact steps to trigger the bug.
5. **Expected behavior** — What should happen according to confirmed requirements.
6. **Actual behavior** — What actually happened.
7. **Business rule reference** — Which BR-xxx or requirement is violated, if applicable.
8. **Environment** — Testing environment details (PHP version, MySQL version, Laravel version, browser).

## 17.3 Bug Resolution Priority

- Critical bugs must be resolved before any release.
- High bugs must be resolved before the affected feature is released.
- Medium bugs should be resolved before release where feasible.
- Low bugs may be deferred to a future iteration.

---

# 18. Test Data Management

## 18.1 Test Data Principles

1. **No production data in tests.** Tests must never use production databases, production credentials, or production user accounts.
2. **Factories for deterministic data.** Laravel model factories in `database/factories/` must produce controlled test records that represent valid scope contexts.
3. **Seeders for reference data.** Seeders in `database/seeders/` provide deliberate local, testing, or approved reference data only.
4. **Isolation between tests.** Each test must start with a known data state. Tests must not depend on data created by other tests.
5. **Teacher Workspace representation.** Test data must include multiple Teacher Workspaces to verify isolation.
6. **Multi-role representation.** Test data must include users for all five roles.
7. **Historical data representation.** Test data must include Student transfers, archived records, and historical Enrollment periods to verify history preservation.
8. **Financial flow representation.** Test data must include both Flow A Subscription records and Flow B payment-status records to verify separation.

## 18.2 Test Data Categories

| Category | Data needed |
|---|---|
| **Identity** | Super Admin, multiple Teachers, Teacher Staff with various permissions, Students (self-registered and Teacher-created), Parents (with multiple linked Students). |
| **Academic structure** | Educational Grades, Groups with different Pricing Types, Enrollments (active and historical). |
| **Operations** | Attendance records (all three methods), Homework (Text/Image/PDF), Exams (all question types), Lessons, Question Banks. |
| **Financial** | Flow A Subscription records, Flow B payment-status records, Billing Cycles, Billable Student counts. |
| **Governance** | Audit Log entries for all mandatory event types, archived records for all archivable entities. |
| **Files** | Homework attachments (Image/PDF), Lesson video references, Student submission files. |

## 18.3 Test Database

- A dedicated MySQL 8 test database must be used for all automated tests.
- The test database must be reset before each test suite run to ensure isolation.
- Test database configuration must not affect the development or production databases.

---

# 19. Testing Environments

## 19.1 Environment Requirements

| Environment | Purpose | Configuration |
|---|---|---|
| **Local development** | Developer-level testing during feature implementation. | Laravel 12, PHP 8.3, MySQL 8, local cPanel-compatible stack. Uses factories and seeders. |
| **Automated testing (CI)** | Continuous integration test execution for every code change. | Same stack as local. Runs full backend Feature, Unit, and frontend integration test suites. |
| **Staging** | Pre-release validation, UAT, and regression testing. | Mirrors production configuration on cPanel Shared Hosting baseline. Uses dedicated test data (not production data). |
| **Production** | Live environment. No testing is performed in production. | cPanel Shared Hosting with confirmed stack. |

## 19.2 Environment Constraints

- All testing environments must use MySQL 8, not an alternative database engine.
- All testing environments must use the Database Queue, not Redis or external queue services.
- All testing environments must use File Cache, not Redis or Memcached.
- All testing environments must use the Database session driver.
- Testing environments must not require Docker, Kubernetes, S3 Storage, WebSockets, or Microservices.
- Staging environment must mirror the cPanel Shared Hosting deployment configuration.

## 19.3 Environment Data Isolation

- Each environment must have its own database instance.
- Production data must never be copied to testing environments without sanitization.
- Test data must be clearly identifiable and separable from any real user data.
- Environment credentials must be stored in `.env` files that are not committed to version control.

---

# 20. Release Acceptance Criteria

## 20.1 General Release Criteria

A release is ready for deployment when all of the following criteria are met:

| Criterion | Requirement |
|---|---|
| **All automated tests pass** | Backend Feature, Unit, and frontend integration tests must all pass without failures. |
| **No Critical or High bugs open** | All Critical and High severity bugs must be resolved. |
| **Business rule coverage** | Every affected business rule (BR-xxx) has passing automated test coverage. |
| **Authorization matrix verified** | The complete role-based authorization matrix for affected features has been tested. |
| **Teacher Workspace isolation verified** | Cross-tenant isolation tests pass for all affected modules. |
| **Archive behavior verified** | Archive and restore tests pass for all affected archivable entities. |
| **Audit Log coverage verified** | All mandatory Audit Log events for affected features are recorded. |
| **Flow A / Flow B separation verified** | Financial separation tests pass for affected payment-related features. |
| **Historical data preservation verified** | Student transfer history and Archive preservation tests pass for affected features. |
| **Security checklist passes** | The security checklist from `AI_DOCS/23_Security_Standards.md` §21 passes for affected areas. |
| **UAT completed** | User Acceptance Testing is completed for affected features by relevant role representatives. |
| **Staging environment validated** | The release candidate has been validated in the staging environment. |

## 20.2 Feature-Specific Acceptance Criteria

Each feature module has acceptance criteria defined in `AI_DOCS/02_Software_Requirements.md` (Parts 2–5). These acceptance criteria must be verified before the feature is considered complete.

## 20.3 Non-Functional Acceptance Criteria

Non-functional requirements from `AI_DOCS/02_Software_Requirements.md` Part 6 must be verified:

- Performance: Platform operations remain scoped to correct Teacher Workspace.
- Reliability: Confirmed business rules are consistently enforced.
- Security: Authentication, authorization, and data privacy are enforced.
- Audit Logging: All mandatory events are recorded; entries are immutable.
- Data Retention: Historical data is never deleted; Archive replaces deletion.
- Archiving: Archived records are excluded from active searches; included in reports.

## 20.4 Release Rollback Criteria

A release must be rolled back if:

- A Critical bug is discovered after deployment.
- Teacher Workspace isolation is violated.
- Historical data is lost or corrupted.
- Audit Log entries are lost or modified.
- Flow A and Flow B data is conflated.

---

# 21. Test Coverage Guidelines

## 21.1 Business Rule Coverage

Every confirmed business rule (BR-xxx) from `AI_DOCS/00_Project_Context.md` must have at least one automated test that verifies its enforcement. The following table maps critical business rules to their test coverage priority:

| Business Rule | Coverage priority | Test type |
|---|---|---|
| BR-001: One global Student account | Critical | Feature, Unit |
| BR-002: One Group per Student per Teacher | Critical | Feature, Database |
| BR-003: Teacher Workspace isolation | Critical | Feature, Integration |
| BR-004: Parent read-only, linked Students only | Critical | Feature |
| BR-005: Archive replaces deletion | Critical | Feature, Database |
| BR-006: Audit Log records all important actions | Critical | Feature |
| BR-007: Student transfers preserve history | Critical | Feature, Database |
| BR-008: Billable Student = Enrollment > 15 days | Critical | Unit, Feature |
| BR-009: Group Price and Pricing Type | High | Feature |
| BR-010: Attendance three methods | High | Feature |
| BR-011: Question Bank private, four question types | High | Feature |
| BR-012: Exam attempts workspace-scoped | High | Feature |
| BR-013: Teacher Staff assigned permissions | High | Feature |
| BR-014: Historical data never deleted | Critical | Feature, Database |
| BR-015: Pricing owned by Super Admin | High | Feature |
| BR-016: One Teaching Subject per Teacher | High | Feature, Unit |
| BR-017: Web Application only | Medium | Integration |
| BR-018: Lesson videos Teacher-owned and private | High | Feature |
| BR-019: V1 records payment status only | High | Feature |
| BR-020: One Parent per Student | High | Feature, Database |
| BR-021: Homework Text/Image/PDF only | High | Feature, Unit |
| BR-022: Duplicate Student accounts not allowed | Critical | Feature, Database |

## 21.2 Role Coverage

Every confirmed role must have test coverage for:

- Allowed permissions (positive tests).
- Denied permissions (negative tests).
- Scope boundary enforcement (cross-scope access attempts).
- Audit Log recording for sensitive actions.

## 21.3 Module Coverage

Every confirmed module must have test coverage for:

- CRUD operations (create, read, update, Archive/restore).
- Workspace isolation.
- Validation rules.
- Error handling.
- Audit Log recording.

## 21.4 Edge Case Coverage

Edge cases identified in `AI_DOCS/02_Software_Requirements.md` (Parts 2–5) and `AI_DOCS/22_Search_Filtering.md` §18 must have test coverage where they represent confirmed business-rule boundaries.

## 21.5 Coverage Gaps

Any confirmed requirement that lacks automated test coverage must be documented as a coverage gap with a plan to close it before the affected feature is released. Coverage gaps must not be used to relax acceptance criteria.

---

# 22. Future Automation Strategy

## 22.1 Current State

Version 1 testing is structured to support gradual automation expansion. The initial testing approach combines:

- **Automated backend tests** — Laravel Feature and Unit tests using the framework's built-in testing capabilities.
- **Automated frontend integration tests** — React component and feature tests using the approved test tooling.
- **Manual UAT** — Stakeholder-driven acceptance testing for each role.

## 22.2 Future Automation Areas

The following automation enhancements may be considered after Version 1, subject to formal approval:

| Future area | Required future decision |
|---|---|
| **End-to-end browser testing** | Approve browser testing tooling (e.g., Playwright, Cypress) and environment for browser-level role and workflow tests. |
| **Automated security scanning** | Approve automated vulnerability scanning tools, dependency auditing, and penetration testing. |
| **Automated performance testing** | Approve load testing tools, performance baselines, and regression thresholds. |
| **Automated visual regression testing** | Approve visual diff tools and baseline management for UI consistency. |
| **Continuous integration pipeline** | Approve CI/CD pipeline configuration, test execution triggers, and quality gates. |
| **Test data generation automation** | Approve automated factory-based test data generation for staging and UAT environments. |
| **Accessibility testing automation** | Approve automated accessibility scanning tools and conformance targets. |
| **Cross-browser testing** | Approve browser matrix and automated cross-browser testing infrastructure. |
| **API contract testing** | Approve contract testing between frontend and backend to verify API compatibility. |
| **Monitoring and alerting** | Approve production monitoring, error tracking, and alerting infrastructure. |

## 22.3 Automation Constraints

All future automation must:

- Preserve Teacher Workspace isolation in test data and test execution.
- Not require Redis, Docker, Kubernetes, S3 Storage, WebSockets, or Microservices unless separately approved.
- Not introduce notification features as part of test infrastructure.
- Not use production data or production credentials.
- Preserve the cPanel Shared Hosting compatibility baseline.
- Use canonical terminology in test names, descriptions, and assertions.
- Not bypass authorization, Archive policy, or Audit Log requirements.

## 22.4 Automation Prioritization

Future automation should prioritize:

1. **Critical business rule coverage** — automate tests for BR-001, BR-002, BR-003, BR-004, BR-005, BR-006, BR-007, BR-008, BR-014 first.
2. **Authorization regression** — automate the complete role-based authorization matrix.
3. **Tenant isolation regression** — automate cross-Teacher isolation verification.
4. **Security regression** — automate the security checklist from `AI_DOCS/23_Security_Standards.md`.
5. **Performance regression** — automate performance baselines for critical paths.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Project Context alignment | Passed — testing strategy follows the frozen Version 1 rules. All BR references, role definitions, scope boundaries, and confirmed/pending statuses are consistent with `AI_DOCS/00_Project_Context.md`. |
| RBAC alignment | Passed — authorization testing covers all five roles and their boundaries as defined in `AI_DOCS/08_RBAC.md` and `AI_DOCS/09_Permission_Matrix.md`. |
| System Architecture alignment | Passed — testing environments, technology baseline, and infrastructure constraints are consistent with `AI_DOCS/03_System_Architecture.md`. Laravel 12, PHP 8.3, MySQL 8, Sanctum, Custom RBAC, File Cache, Database Queue, Database sessions, Laravel Public Storage, Laravel Scheduler with Cron Jobs, and cPanel Shared Hosting are preserved. |
| Project Structure alignment | Passed — test directory structure is consistent with `AI_DOCS/04_Project_Structure.md` §9. Backend tests use `tests/Feature/`, `tests/Unit/`, and `tests/Support/`. Frontend tests use `frontend/tests/integration/` and `frontend/tests/e2e/`. |
| Backend Architecture alignment | Passed — testing approach is consistent with `AI_DOCS/11_Backend_Architecture.md`. Layer responsibilities, feature organization, request lifecycle, authorization flow, validation strategy, and error handling are aligned. |
| Frontend Architecture alignment | Passed — frontend testing approach is consistent with `AI_DOCS/12_Frontend_Architecture.md`. Role context handling, route guards, form validation, and component testing are aligned. |
| API Design alignment | Passed — API testing covers all endpoint categories defined in `AI_DOCS/10_API_Design.md`. Pagination, filtering, sorting, error responses, and authorization are tested per the API specification. |
| Security Standards alignment | Passed — security testing covers all areas defined in `AI_DOCS/23_Security_Standards.md`. Authentication, authorization, tenant isolation, input validation, SQL injection, XSS, CSRF, rate limiting, session management, and the security checklist are covered. |
| Background Jobs alignment | Passed — background job testing covers idempotency, failure handling, Billing Cycle processing, Exam auto-grading, and workspace scope preservation consistent with `AI_DOCS/21_Background_Jobs.md`. |
| Search & Filtering alignment | Passed — search testing covers scope resolution, cross-Teacher discovery prevention, Archive-aware results, pagination, sorting, and empty result handling consistent with `AI_DOCS/22_Search_Filtering.md`. |
| Subscription/Billing alignment | Passed — Subscription testing covers Billable Student calculation, Billing Cycle rules, Flow A/Flow B separation, and payment-status-only recording consistent with `AI_DOCS/17_Subscription_Billing.md`. |
| QR Attendance alignment | Passed — Attendance testing covers all three methods, Dynamic QR Code scanning, workspace scoping, and history preservation consistent with `AI_DOCS/16_QR_Attendance_System.md`. |
| Exam Engine alignment | Passed — Exam testing covers all four question types, automatic grading, Bubble Sheet, Essay pending state, and workspace-scoped results consistent with `AI_DOCS/15_Exam_Engine.md`. |
| File Storage alignment | Passed — file testing covers upload validation, workspace ownership, cross-Teacher prevention, and format restrictions consistent with `AI_DOCS/20_File_Storage.md`. |
| Reporting alignment | Passed — report testing covers workspace-scoped reports, archived record indication, Flow A/Flow B separation, and role-appropriate visibility consistent with `AI_DOCS/18_Reporting_Analytics.md`. |
| Software Requirements alignment | Passed — test scenarios trace to functional requirements in `AI_DOCS/02_Software_Requirements.md` Parts 2–5 and non-functional requirements in Part 6. |
| User Flows alignment | Passed — UAT covers the user flows defined in `AI_DOCS/05_User_Flows.md`. |
| Database Design alignment | Passed — database testing covers tenant isolation, Archive behavior, historical preservation, and referential integrity consistent with `AI_DOCS/06_Database_Design.md`. |
| Teacher Workspace isolation | Passed — isolation is the highest-priority testing invariant. Cross-Teacher access prevention is tested across all modules, all roles, and all access paths. |
| Student account rules | Passed — one global Student account, duplicate prevention, two registration methods, per-Teacher partitioning, and Group movement history are preserved. |
| Parent access rules | Passed — linked-Student read-only access, one Parent per Student, and Student Switcher are preserved. |
| Archive policy | Passed — no permanent deletion is referenced anywhere. Archive replaces deletion per BR-005. Historical data preservation is tested. |
| Audit Log policy | Passed — all 10 mandatory event types are tested. Immutability, permanent retention, and Teacher Staff attribution are preserved. |
| Payment handling | Passed — Version 1 records payment status only. Flow A and Flow B separation is tested. No payment processing is assumed. |
| Version 1 scope | Passed — no native mobile testing, payment gateway testing, notification testing, marketplace testing, video homework testing, Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices are introduced. |
| PENDING items | Passed — non-payment enforcement (Q-005), Teacher Staff permission granularity (Q-011), Super Admin content visibility (Q-012), pricing model (Q-013), and localization (Q-015) are preserved as PENDING and not silently hardened. |
| Canonical terminology | Passed — Teacher Workspace, Educational Grade, Teaching Subject, Group, Student, Parent, Teacher Staff, Super Admin, Subscription, Flow A, Flow B, Enrollment, Archive, Audit Log, Dynamic QR Code, ID Card, Question Bank, Bubble Sheet, Lesson, Billable Student, Billing Cycle, and Homework are used consistently. |
| cPanel compatibility | Passed — testing environments and constraints are compatible with cPanel Shared Hosting. No Docker, Redis, Kubernetes, S3, WebSockets, or Microservices are required. |
| No source code | Passed — no source code, test scripts, test fixtures, APIs, database tables, UI implementation, or physical configuration is defined. |
