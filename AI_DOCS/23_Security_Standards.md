# 23 — Security Standards

## Document Scope

This document defines the complete security standards for Version 1 of the Unified Education Platform. It establishes security requirements across authentication, authorization, data isolation, input handling, session management, file security, and operational security.

This document does not define source code, APIs, database tables, UI implementation, or physical configuration. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found.

The security architecture is built on the confirmed Version 1 technology baseline: **Laravel 12** backend with **PHP 8.3**, **React 19** frontend with **TypeScript**, **MySQL 8** for persistence, **Laravel Sanctum** for authentication, **Laravel Gates & Policies with Custom RBAC** for authorization, **File Cache**, **Database Queue**, **Database session driver**, **Laravel Public Storage**, and **cPanel Shared Hosting** as the primary deployment target.

---

# 1. Security Overview

Security is a first-class architectural concern across the Unified Education Platform. The Platform handles sensitive educational data including Student records, Teacher-owned private content (Lessons, Question Banks), attendance records, exam results, homework submissions, payment status, and parent-student relationships.

The security architecture protects:

- **Teacher Workspace isolation** — each Teacher's data is completely isolated (BR-003).
- **Student identity** — one global account, no duplicates, per-Teacher data partitioning (BR-001).
- **Parent boundaries** — read-only access to linked Students only (BR-004).
- **Teacher Staff permissions** — only Teacher-assigned permissions within the creating Teacher Workspace (BR-013).
- **Private content** — Lessons and Question Banks are Teacher-owned and private (BR-018, BR-011).
- **Historical integrity** — Archive replaces permanent deletion; Audit Log is immutable (BR-005, BR-006).
- **Financial separation** — Flow A and Flow B are never conflated.
- **Platform scope** — Super Admin operates at Platform level without entering Teacher Workspaces.

The security model follows a **defense-in-depth** strategy: no single layer is solely responsible for security. Authentication, authorization, tenant scoping, input validation, output encoding, and audit logging work together.

---

# 2. Security Principles

## 2.1 Core Security Principles

1. **Deny by default.** A user can access a resource only when authentication, role, scope, ownership, and permission checks explicitly allow it.

2. **Server-side enforcement is mandatory.** The backend is the sole authority for authorization, tenant isolation, and access control. Frontend visibility, hidden controls, or URL structure are never sufficient security controls.

3. **Least privilege.** Every role, every request, and every background job operates with the minimum permissions required. Teacher Staff receive only permissions explicitly assigned by the Teacher.

4. **Teacher Workspace isolation is absolute.** No Teacher can see another Teacher's data under any circumstance. This applies to every layer: database queries, API responses, file access, search results, reports, and error messages (BR-003).

5. **No hard deletion exists anywhere.** Archive replaces permanent deletion for all records, by all actors, everywhere (BR-005).

6. **Audit Log is immutable and permanent.** Every important action produces an Audit Log entry. Entries are append-only, never edited or deleted, and permanently retained (BR-006).

7. **Flow A and Flow B separation.** The Teacher-to-Platform Subscription (Flow A) and Student/Parent-to-Teacher fees (Flow B) are never conflated in data, logic, reporting, or authorization.

8. **No marketplace behavior.** No user role receives permissions for course discovery, cross-Teacher content browsing, or marketplace access.

9. **Sensitive data is never exposed in errors.** Error responses must provide enough information for the authorized user to understand the failure without revealing Teacher-private data, unlinked Student data, implementation details, or credentials.

10. **Version 1 scope is respected.** Security standards do not introduce native mobile security, online payment gateway security, notification security, or infrastructure beyond the confirmed cPanel Shared Hosting baseline.

## 2.2 Security Boundaries

| Boundary | Description |
|---|---|
| **Authentication boundary** | Every protected request must carry a valid authenticated context established through Laravel Sanctum. |
| **Role boundary** | Each request is evaluated against the user's active role context: Super Admin, Teacher, Teacher Staff, Student, or Parent. |
| **Tenant boundary** | Teacher Workspace is the unit of data isolation. All workspace-owned queries must be scoped to the owning Teacher Workspace. |
| **Ownership boundary** | Resources must belong to the user's permitted scope before any operation proceeds. |
| **Archive boundary** | Active operations target only active records. Archived records appear only in authorized historical contexts. |
| **Financial boundary** | Flow A and Flow B data, filters, reports, and authorization never mix. |

---

# 3. Authentication Security

## 3.1 Authentication Mechanism

Laravel Sanctum is the confirmed authentication technology for Version 1. Sanctum provides session-based authentication for the Web Application and token-based authentication where applicable.

## 3.2 Authentication Requirements

- All protected API endpoints require an authenticated user context.
- Authentication must be established before any role, scope, or permission resolution occurs.
- The backend validates credentials; the frontend never makes authentication decisions.
- Authentication context must be carried on every request to protected resources.

## 3.3 Login Security

| Concern | Standard |
|---|---|
| Credential validation | Server-side only; frontend sends credentials, backend validates. |
| Successful login | Recorded in the Audit Log with actor, role, timestamp, IP address, and device/client information. |
| Failed login | Recorded in the Audit Log with the attempted identifier (without exposing whether the account exists), timestamp, IP address, and device/client information. |
| Rate limiting on login | Login endpoints must be rate-limited to prevent brute-force attacks. |
| Generic error messages | Login failures must not reveal whether an account exists, whether the password was wrong, or whether the account is archived. |

## 3.4 Student Account Security

- Student self-registration must not create duplicate Student accounts (BR-022).
- Teacher-created Student accounts must be activatable by the Student later.
- Duplicate account prevention must be enforced server-side.
- Account activation must verify the activation identifier matches a Teacher-created Student account.

## 3.5 Authentication Constraints

Authentication must not introduce:

- Duplicate Student accounts.
- Multiple Parent accounts for one Student in Version 1 (BR-020).
- Unconfirmed impersonation behavior such as "Login as Teacher."
- Native mobile authentication requirements for Version 1 (BR-017).

## 3.6 Credential Storage

- Passwords must be hashed using bcrypt or Argon2id through Laravel's built-in hashing mechanisms.
- Passwords must never be stored in plain text.
- Passwords must never be logged, returned in API responses, or included in error messages.
- Authentication tokens must be stored securely using Laravel Sanctum's token storage.
- Session tokens must be rotated on privilege escalation (e.g., role change).

---

# 4. Authorization Security

## 4.1 Authorization Mechanism

Authorization uses Laravel Gates & Policies with Custom RBAC based on the logical permission catalog defined in `AI_DOCS/08_RBAC.md` and `AI_DOCS/09_Permission_Matrix.md`.

## 4.2 Authorization Flow

Every protected request follows this authorization sequence:

1. **Authentication** — verify the user has a valid authenticated context.
2. **Role resolution** — determine the active role context (Super Admin, Teacher, Teacher Staff, Student, Parent).
3. **Scope resolution** — determine the access scope:
   - Platform scope for Super Admin.
   - Teacher Workspace scope for Teacher and Teacher Staff.
   - Student account and Teacher relationship scope for Student.
   - Linked Student scope for Parent.
4. **Permission check** — verify the role is allowed to perform the requested action per the Permission Matrix.
5. **Ownership/relationship check** — verify the resource belongs to the user's permitted scope.
6. **Archive state check** — verify whether the resource is active or archived.
7. **Execution or rejection** — proceed only when all checks pass; reject without exposing restricted data when any check fails.
8. **Audit** — record the action in the Audit Log if it qualifies as an important action.

## 4.3 Role-Specific Authorization Rules

### Super Admin

- Operates at Platform-level scope only.
- Manages Teachers, Flow A Subscriptions, pricing, platform settings, and global reports.
- Cannot operate inside Teacher Workspaces as a Teacher.
- Cannot access Teacher-private content beyond confirmed visibility boundaries (Q-012 PENDING).
- Cannot hard delete records.

### Teacher

- Operates one completely isolated Teacher Workspace.
- Cannot access another Teacher's data under any circumstance.
- Cannot change Teaching Subject after account creation (BR-016).
- Cannot hard delete records.

### Teacher Staff

- Exists only inside the creating Teacher Workspace.
- Holds only permissions assigned by the Teacher (BR-013).
- Cannot grant self permissions.
- Actions are attributed to the Teacher Staff user, not to the Teacher.
- Permission granularity remains PENDING (Q-011).

### Student

- Accesses only own account and own per-Teacher records (BR-001).
- Cannot access another Student's records.
- Cannot access Teacher Workspace management areas.
- Cannot access Teacher-private Question Bank content outside assigned Exams.
- Cannot modify Attendance manually.

### Parent

- Accesses only linked Students (BR-004).
- Has read-only access everywhere.
- Cannot modify Attendance, grades, Homework, Exams, payment status, Student records, or Teacher records.
- Cannot access unlinked Students.

## 4.4 Frontend Is Not a Security Boundary

- Frontend visibility or hidden controls are never sufficient security controls.
- The frontend may guide user interaction, but the backend makes all final authorization decisions.
- Disabled buttons, hidden menu items, or omitted UI elements are usability aids, not security enforcement.
- Every API request must be independently authorized by the backend regardless of frontend state.

---

# 5. Multi-Tenant Isolation

## 5.1 Tenant Boundary

The Teacher Workspace is the tenant boundary. Every Teacher Workspace-owned record must carry the Teacher's identity and be accessed only within that context.

## 5.2 Tenant-Scoped Resources

All of the following resources are tenant-scoped and must never be accessible across Teacher Workspace boundaries:

- Educational Grades
- Groups
- Student relationships with Teacher
- Student Enrollments
- Attendance records and Attendance Sessions
- Homework and Homework Submissions
- Lessons and Lesson Videos
- Question Bank and Questions
- Exams, Exam Attempts, and Exam Answers
- Reports
- Teacher Staff
- Teacher Workspace Settings
- Flow B payment-status records
- File Attachments owned by the Teacher Workspace

## 5.3 Isolation Enforcement Rules

1. **Database query scoping.** Every query that accesses Teacher Workspace-owned data must include the Teacher Workspace scope. Repositories, services, and models must enforce this at the data access layer.

2. **No cross-tenant foreign keys.** Teacher Workspace-owned records must not reference or be referenced by records owned by another Teacher Workspace, except through approved global identity relationships (e.g., a Student's global account).

3. **Tenant context resolution.** For every Teacher Workspace-owned operation, the backend must resolve the Teacher Workspace context before accessing data. Tenant context is derived from:
   - The authenticated Teacher.
   - The creating Teacher Workspace for Teacher Staff.
   - The Student's relationship with a Teacher.
   - The Parent's linked Student and that Student's Teacher relationship.
   - Platform-level Super Admin operations where allowed.

4. **Report isolation.** Teacher reports include only the Teacher's own Teacher Workspace data. Student views are partitioned per Teacher. Parent views are limited to linked Students. Super Admin global reports respect PENDING content-visibility boundaries.

5. **File isolation.** File access must follow the same ownership and tenant isolation rules as the entity files belong to. Lesson Videos are accessible only to the owning Teacher's Students and authorized Teacher Workspace users.

6. **Search isolation.** Search must never reveal the existence, count, names, or metadata of records belonging to another Teacher Workspace.

7. **Error isolation.** Error responses must not reveal that records exist in another Teacher Workspace, for another Student, or in an unlinked Student relationship.

## 5.4 Student Multi-Tenant Access

A Student has one global account and may study with multiple Teachers (BR-001). The Student's records remain partitioned per Teacher:

- Attendance is separate per Teacher.
- Homework is separate per Teacher.
- Exams and grades are separate per Teacher.
- Lessons are visible only from the Student's own Teachers.
- Flow B payment status is separate per Teacher.

The architecture must support this multi-Teacher access while preserving complete per-Teacher data separation.

## 5.5 Parent Multi-Tenant Access

A Parent accesses only linked Students (BR-004). Parent access spans Teacher Workspaces only through the linked Student relationship, and only in read-only mode. The Parent cannot discover or browse Teachers or Lessons as marketplace content.

---

# 6. Password Policy

## 6.1 Password Requirements

| Concern | Standard |
|---|---|
| Minimum length | 8 characters (enforced server-side). |
| Complexity | Must contain at least one uppercase letter, one lowercase letter, and one digit. Special characters are recommended but not mandatory. |
| Hashing algorithm | bcrypt or Argon2id through Laravel's Hash facade. |
| Storage | Hashed only; plain-text passwords must never be stored, logged, or transmitted in responses. |
| Transmission | HTTPS is required in production; passwords are transmitted over encrypted connections only. |

## 6.2 Password Reset

- Password reset must use a time-limited, single-use token sent through the confirmed communication channel.
- Reset tokens must expire after a defined period.
- Reset requests must be rate-limited.
- Reset must not reveal whether an account exists for the provided identifier.
- Successful password resets must be recorded in the Audit Log.
- Old sessions must be invalidated after password reset.

## 6.3 Password Constraints

- Passwords must not be stored in plain text, reversible encryption, or weak hashing algorithms (e.g., MD5, SHA1).
- Passwords must not appear in API responses, error messages, Audit Log entries, or operational logs.
- Default or temporary passwords must be changed on first login.
- Password history checking (preventing reuse of recent passwords) is not confirmed for Version 1 and must not be silently implemented.

---

# 7. Session Management

## 7.1 Session Driver

Version 1 uses the **Database session driver**. Session data is stored in MySQL 8.

## 7.2 Session Security Standards

| Concern | Standard |
|---|---|
| Session creation | Only after successful authentication. |
| Session identifier | Must be a cryptographically random, sufficiently long value. |
| Session cookie flags | `HttpOnly`, `Secure` (in production), `SameSite=Lax` or `Strict`. |
| Session expiration | Sessions must expire after a defined period of inactivity. |
| Absolute session timeout | Sessions must have a maximum lifetime regardless of activity. |
| Session invalidation on logout | All session data must be destroyed on logout. |
| Session invalidation on password change | Existing sessions must be invalidated when a password is changed. |
| Concurrent sessions | The number of concurrent sessions per user must be bounded or configurable. |

## 7.3 Session Isolation

- Session data must be isolated per user and per role context.
- A Teacher's session must not carry or expose another Teacher's session data.
- A Student's session must reflect only the Student's own account and per-Teacher relationships.
- A Parent's session must reflect only linked Student relationships.
- Role switching (where a user has multiple role contexts) must establish a new session context, not blend roles.

## 7.4 Session Constraints for cPanel Shared Hosting

- Sessions use the Database driver, not file-based storage, to support shared hosting reliability.
- Session table cleanup must be performed periodically to remove expired sessions.
- Session management must not require Redis or external session stores.

---

# 8. API Security

## 8.1 Transport Security

- HTTPS is required in production for all API communication.
- HTTP requests must be redirected to HTTPS in production.
- API responses must include appropriate security headers.

## 8.2 API Authentication

- All protected endpoints require a valid authenticated context established through Laravel Sanctum.
- Authentication must be validated on every request; no endpoint trusts cached or frontend-provided authentication state.
- Login and logout endpoints are the only public authentication endpoints.

## 8.3 API Authorization

- Every protected endpoint performs server-side authorization using Laravel Gates & Policies with Custom RBAC.
- Authorization checks the role, scope, ownership, permission, and Archive state for every request.
- Unauthorized requests are denied without exposing restricted data.

## 8.4 API Versioning

- All Version 1 endpoints use the `/api/v1` prefix.
- Breaking changes require a future API version.
- Version 1 behavior must remain consistent with the frozen Project Context.

## 8.5 API Response Security

- Error responses must use the standardized error structure defined in `AI_DOCS/10_API_Design.md`.
- Error responses must not expose implementation details, stack traces, SQL queries, server paths, credentials, or Teacher-private data.
- Successful responses must not include data outside the user's authorized scope.
- Pagination metadata must reflect only authorized records.

## 8.6 API Rate Limiting

Rate limiting must be applied to sensitive endpoints:

| Endpoint type | Rate limiting purpose |
|---|---|
| Login | Prevent brute-force credential attacks. |
| Student registration | Prevent duplicate account flooding. |
| QR Code scanning | Prevent scan abuse and Attendance manipulation. |
| Password reset | Prevent reset token enumeration. |
| File upload | Prevent storage abuse. |
| General API | Protect against denial-of-service. |

## 8.7 API Security Headers

The following security headers must be set on API responses:

- `X-Content-Type-Options: nosniff` — prevent MIME type sniffing.
- `X-Frame-Options: DENY` or `SAMEORIGIN` — prevent clickjacking.
- `Referrer-Policy` — control referrer information leakage.
- `Cache-Control: no-store` for sensitive responses — prevent caching of private data.
- `Content-Security-Policy` — restrict resource loading (applied at web server level for the frontend).

---

# 9. File Upload Security

## 9.1 Upload Validation

Every file upload must pass through multiple validation layers before acceptance:

1. **Authentication** — the uploader must be authenticated.
2. **Authorization** — the uploader must have permission for the owning resource and scope.
3. **Teacher Workspace ownership** — Teacher-owned files must belong to the current Teacher Workspace.
4. **Student relationship** — Student Homework submissions must belong to assigned Homework through a valid Teacher relationship.
5. **Parent denial** — Parent file uploads are denied.
6. **File type validation** — file type must match the owning resource's allowed context.
7. **MIME type verification** — the actual file content must match the declared type.
8. **File size limits** — applied according to confirmed or approved limits.

## 9.2 Supported File Types

| File context | Allowed types | Denied |
|---|---|---|
| Homework assignment | Text, Image, PDF | Video, executable, archive formats. |
| Homework submission (Student) | Image, PDF | Video, executable, archive formats. |
| Lesson video | Teacher-owned private video | Unconfirmed formats. |
| Parent upload | Denied entirely. | All types. |
| Video homework | Denied entirely. | All types (BR-021). |

## 9.3 File Storage Security

- Files are stored in Laravel Public Storage with application-level authorization.
- Storage paths, filenames, and directory structures are not authorization proofs.
- File access requests must pass through the same authorization, ownership, and scope checks as other protected operations.
- Cross-Teacher file access is denied.
- Archived file references must not be served as active content.

## 9.4 File Upload Constraints for cPanel Shared Hosting

- File storage uses Laravel Public Storage, not S3 or external storage.
- Upload processing must respect shared hosting memory and execution time limits.
- Large files must be validated early to prevent resource exhaustion.
- Virus scanning is not confirmed for Version 1 and must not be silently implemented.

---

# 10. Input Validation

## 10.1 Validation Strategy

Input validation combines multiple layers:

| Layer | Responsibility |
|---|---|
| **Request validation (Form Requests)** | Validate required fields, formats, data types, enum values, date ranges, and file rules. |
| **Authorization validation** | Verify role, scope, ownership, permission, linked relationship, and Teacher Workspace access. |
| **Business validation** | Enforce confirmed rules: no duplicate Students, one Group per Student per Teacher, Teaching Subject immutability, Flow A/Flow B separation, Archive policy. |
| **Persistence integrity** | Prevent invalid saved state and preserve logical relationships at the database level. |

## 10.2 Input Validation Standards

| Concern | Standard |
|---|---|
| Required fields | Must be present and non-empty where applicable. |
| Data types | Must match expected types (string, integer, date, enum, etc.). |
| Enum values | Must be from the confirmed set (e.g., Pricing Type: Monthly or Per Lesson; Question Type: Multiple Choice, True/False, Essay, Bubble Sheet). |
| Date ranges | `from_date` must not be after `to_date`; dates must be valid calendar dates. |
| String length | Must be bounded to prevent excessive data storage and processing. |
| Email format | Must be a valid email format where email is required. |
| Numeric ranges | Must be within valid ranges (e.g., positive integers for IDs, valid prices). |

## 10.3 File Input Validation

- File type must match the owning resource's allowed context.
- File size must be within approved limits.
- MIME type must be verified against file content.
- Filename must be sanitized to prevent path traversal.
- File uploads must be scanned for basic integrity.

## 10.4 Validation Error Handling

- Validation failures return HTTP 422 with field-level error messages.
- Error messages must not expose internal implementation details.
- Validation must not reveal whether an unauthorized resource exists.

---

# 11. SQL Injection Prevention

## 11.1 Prevention Strategy

SQL injection is prevented through the following layered defenses:

1. **Laravel Eloquent ORM** — uses parameterized queries by default; user input is never interpolated directly into SQL strings.
2. **Laravel Query Builder** — uses prepared statements for all database interactions.
3. **Form Request validation** — validates and sanitizes input before it reaches the database layer.
4. **Input type enforcement** — ensures that expected types (integer IDs, date strings, enum values) are validated before database interaction.

## 11.2 SQL Injection Prevention Standards

| Concern | Standard |
|---|---|
| Raw queries | Must not use raw SQL with unsanitized input. Where raw queries are unavoidable, parameterized bindings must be used. |
| String concatenation | Must not be used to build SQL queries from user input. |
| Table and column names | Must not be derived from user input; only validated identifiers from the application schema may be used. |
| LIKE queries | Must escape special characters (`%`, `_`) to prevent LIKE injection. |
| ORDER BY clauses | Must use a whitelist of allowed sort fields, not user-provided column names. |
| IN clauses | Must use parameterized array binding. |

## 11.3 Database Access Constraints

- The application must use a dedicated database user with the minimum required privileges.
- The database user must not have `DROP`, `ALTER`, or `GRANT` privileges in production.
- Database credentials must be stored in environment variables, never in source code or version control.
- Database connections must use encrypted connections where supported by the hosting environment.

---

# 12. XSS Prevention

## 12.1 XSS Prevention Strategy

Cross-Site Scripting (XSS) is prevented through context-appropriate output encoding at both the frontend and backend layers.

## 12.2 Frontend (React 19) XSS Prevention

- React automatically escapes rendered content by default; raw HTML injection requires explicit `dangerouslySetInnerHTML`, which must not be used with user-provided data.
- TypeScript provides compile-time type safety that helps prevent injection of unexpected data types.
- Dynamic content in JSX expressions is auto-escaped by React's rendering engine.

## 12.3 Backend XSS Prevention

- API responses must return JSON, not HTML; the backend does not render user content into HTML templates.
- All user-provided data in API responses must be properly encoded for its output context.
- HTTP response `Content-Type` headers must be set correctly (`application/json` for API responses).

## 12.4 Content Security Policy

- The frontend must implement a Content Security Policy that restricts script sources.
- Inline scripts must be minimized or eliminated.
- The CSP must not allow loading resources from untrusted origins.

## 12.5 XSS Prevention Standards

| Concern | Standard |
|---|---|
| User input in responses | Must be encoded for the output context. |
| Rich text content | Where rich text is permitted (e.g., Homework descriptions), it must be sanitized to remove script tags, event handlers, and dangerous attributes. |
| URL parameters | Must be validated and encoded before use. |
| Error messages | Must not include raw user input without encoding. |
| File names | Must be sanitized before display to prevent XSS through malicious filenames. |

---

# 13. CSRF Protection

## 13.1 CSRF Protection Mechanism

Laravel provides built-in CSRF protection through:

- CSRF tokens on all state-changing requests.
- `SameSite` cookie attribute on session cookies.
- Verification of the `Origin` and `Referer` headers where applicable.

## 13.2 CSRF Protection Standards

| Concern | Standard |
|---|---|
| State-changing requests | Must include a valid CSRF token (POST, PUT, PATCH, DELETE). |
| GET requests | Must not perform state-changing operations; GET must be safe and idempotent. |
| API requests | Sanctum's SPA authentication provides CSRF protection through cookie-based token verification. |
| Token rotation | CSRF tokens must be rotated periodically and on authentication events. |
| Exempt endpoints | Only explicitly public endpoints (e.g., login, registration) may have modified CSRF handling per Laravel Sanctum conventions. |

## 13.3 Double Submit Cookie Pattern

For the React 19 SPA communicating with the Laravel 12 backend:

- Sanctum's cookie-based authentication includes CSRF protection through the `X-XSRF-TOKEN` header.
- The frontend must read the `XSRF-TOKEN` cookie and include it in the `X-XSRF-TOKEN` header for state-changing requests.
- The backend verifies the token on every state-changing request.

---

# 14. Rate Limiting

## 14.1 Rate Limiting Strategy

Rate limiting protects sensitive endpoints against brute-force attacks, enumeration, and abuse. Laravel's built-in rate limiting middleware is used.

## 14.2 Rate Limiting Standards

| Endpoint | Rate limit concern |
|---|---|
| **Login** | Prevent brute-force credential attacks. Limit attempts per IP address and per account identifier. |
| **Student registration** | Prevent mass account creation. Limit registration attempts per IP address. |
| **Password reset** | Prevent reset token enumeration. Limit reset requests per account. |
| **QR Code scanning** | Prevent scan abuse and Attendance manipulation. Limit scans per Student per Attendance Session. |
| **File upload** | Prevent storage abuse. Limit uploads per user per time period. |
| **General API** | Protect against denial-of-service. Apply a general rate limit per authenticated user. |
| **Account activation** | Prevent activation token brute-forcing. Limit activation attempts. |

## 14.3 Rate Limit Response

- Rate-limited requests must receive HTTP 429 (Too Many Requests).
- Rate limit responses must include `Retry-After` header where applicable.
- Rate limit error messages must not reveal internal rate-limit thresholds or implementation details.
- Rate limiting must preserve Teacher Workspace isolation; a Teacher's rate limiting must not affect another Teacher.

## 14.4 Rate Limiting Constraints for cPanel Shared Hosting

- Rate limiting uses Laravel's built-in mechanisms (cache-based throttling with File Cache).
- Rate limiting must not require Redis or external rate-limiting services.
- Rate limiting configuration must be tunable without code changes.

---

# 15. Audit Logging

## 15.1 Audit Log as a Security Control

The Audit Log is not merely a compliance feature; it is a security control that provides traceability, accountability, and forensic capability.

## 15.2 Mandatory Audit Events

The following events must be recorded without exception, across all roles and all surfaces:

1. **Create** — creation of any record.
2. **Update** — modification of any record.
3. **Archive** — every archival action.
4. **Restore** — every restoration of an archived record.
5. **Login** — every successful and failed authentication.
6. **Permission Change** — any change to a Teacher Staff user's granted permissions.
7. **Attendance Change** — recording or modifying any attendance entry, by any method.
8. **Exam Modification** — creating, editing, publishing, or archiving exams and questions.
9. **Homework Modification** — creating, editing, grading, or archiving homework.
10. **Subscription Change** — Subscription lifecycle events: status changes, payment-status recording.

## 15.3 Audit Log Entry Content

Each Audit Log entry must contain:

- **Actor:** user ID and role (Teacher Staff actions are attributed to the staff user, never to the Teacher).
- **Context:** Teacher Workspace (for workspace-scoped events) or Platform scope.
- **Event:** event type plus affected entity type and ID.
- **Payload:** before/after snapshot of changed fields.
- **Origin:** timestamp (server time), IP address, and device/client information.

## 15.4 Audit Log Security Properties

| Property | Standard |
|---|---|
| Append-only | Entries are never modified after creation. |
| Immutable | No actor, including Super Admin, can edit or delete Audit Log entries. |
| Permanent retention | Entries are never purged, archived, or compacted. |
| Transactional guarantee | The Audit Log entry must be written in the same database transaction as the action it describes. |
| Attribution | Teacher Staff actions are attributed to the Teacher Staff user. Super Admin actions are attributed to the Super Admin. Student and Parent actions are attributed to the authenticated account. |

## 15.5 Security-Relevant Audit Events

Beyond the mandatory events, the following security-relevant events should be logged:

- Repeated failed login attempts.
- Authorization failures (attempted access to unauthorized resources).
- Cross-Teacher access attempts.
- Rate limit violations.
- File upload validation failures.
- Password reset requests and completions.
- Session creation and destruction.

---

# 16. Sensitive Data Handling

## 16.1 Sensitive Data Categories

| Category | Examples | Handling standard |
|---|---|---|
| **Authentication credentials** | Passwords, tokens, API keys | Hashed or encrypted; never logged, returned in responses, or stored in plain text. |
| **Personal identification** | Student names, parent relationships, contact information | Visible only within authorized scope; never exposed in errors or unauthorized contexts. |
| **Teacher-private content** | Lesson videos, Question Bank content, Homework content, Exam definitions | Visible only within the owning Teacher Workspace and authorized relationships. |
| **Financial data** | Subscription amounts, payment status, Group pricing | Flow A and Flow B never conflated; visible only within authorized scope. |
| **Session data** | Session identifiers, role context | Stored in Database session driver; session cookies have `HttpOnly`, `Secure`, `SameSite` flags. |
| **File content** | Uploaded files, Lesson videos | Access-controlled through application-level authorization; storage paths are not authorization proofs. |
| **Audit Log data** | Actor identity, action details | Append-only, immutable, permanently retained; never exposed beyond authorized visibility. |

## 16.2 Data Exposure Prevention

- API responses must include only data within the user's authorized scope.
- Error messages must not include sensitive data.
- Operational logs must not contain passwords, tokens, or credentials.
- Debug mode must be disabled in production; stack traces must not be exposed.
- Database credentials, application keys, and mail credentials must be stored in environment variables, never in source code or version control.

## 16.3 Data in Transit

- HTTPS is required in production for all communication.
- Sensitive data (passwords, tokens, personal information) must not be transmitted over unencrypted connections.
- API responses containing sensitive data must include `Cache-Control: no-store` headers.

## 16.4 Data at Rest

- Passwords are stored as bcrypt or Argon2id hashes.
- Database credentials are stored in environment variables.
- Files are stored in Laravel Public Storage with application-level access control.
- Database backups must be encrypted where the hosting environment supports it.
- Sensitive data must not be stored in browser local storage or session storage without encryption.

## 16.5 Data in Logs

- Operational logs must not contain passwords, tokens, API keys, or credentials.
- Operational logs must not contain full file content, Question Bank content, or Student personal data beyond what is needed for troubleshooting.
- Audit Log entries must contain sufficient detail for traceability without exposing unnecessary sensitive data.

---

# 17. Backup Security

## 17.1 Backup Scope

Version 1 targets cPanel Shared Hosting. The official documents require historical retention but do not define a specific backup topology. The following principles apply to any backup strategy.

## 17.2 Backup Security Standards

| Concern | Standard |
|---|---|
| Backup frequency | Must be sufficient to meet operational recovery needs. Exact frequency is an operational decision. |
| Backup content | Must include the MySQL database (all data, sessions, queue jobs, Audit Log entries) and Laravel Public Storage files. |
| Backup encryption | Database and file backups must be encrypted where the hosting environment supports it. |
| Backup storage location | Backups must be stored in a location separate from the production server. Backup artifacts must not be committed to the source repository. |
| Backup access control | Backup access must be restricted to authorized personnel only. Backup credentials must not be stored in source code or version control. |
| Backup integrity | Backup integrity must be verified periodically through test restores. |

## 17.3 Backup Isolation

- Backups must preserve Teacher Workspace isolation; a backup restore must not mix data across Teacher Workspaces.
- Backups must preserve Archive state, Audit Log immutability, and historical data relationships.
- Backups must preserve the separation between Flow A and Flow B data.
- Backup handling must not make protected files public or expose storage paths/credentials.

## 17.4 Recovery Security

- Recovery procedures must preserve the same security properties as the production system.
- Restored data must be verified for integrity before the system is made available.
- Recovery must not weaken authentication, authorization, or tenant isolation.

---

# 18. Error Message Policy

## 18.1 Error Message Principles

Error messages serve two audiences: authorized users who need to understand what went wrong, and attackers who try to extract information. The error message policy balances helpfulness with security.

## 18.2 Error Message Standards

| Concern | Standard |
|---|---|
| **Authentication errors** | Generic "Invalid credentials" message; must not reveal whether the account exists. |
| **Authorization errors** | Generic "Access denied" or "Not authorized" message; must not reveal resource existence. |
| **Validation errors** | Field-level validation messages that explain the expected format without exposing internal constraints. |
| **Not found errors** | Generic "Resource not found" message; must not reveal whether the resource exists but is inaccessible. |
| **Server errors** | Generic "An unexpected error occurred" message; must never include stack traces, SQL queries, or internal paths. |
| **Rate limit errors** | "Too many requests" with `Retry-After` header; must not reveal internal rate-limit thresholds. |

## 18.3 Error Message Content Restrictions

Error messages must never include:

- SQL queries or database error details.
- Stack traces or exception chain details.
- Server file paths or directory structures.
- Database credentials or application secrets.
- Teacher-private data, unlinked Student data, or another Teacher Workspace's information.
- Internal API endpoint names or implementation details.
- Framework or library version information.

## 18.4 Error Logging vs. Error Responses

- Detailed error information (stack traces, SQL context, request details) must be logged to operational logs for troubleshooting.
- Error responses to users must contain only safe, generic information.
- The `APP_DEBUG` setting must be `false` in production to prevent Laravel from exposing stack traces in error responses.

---

# 19. Security Monitoring

## 19.1 Monitoring Scope

Security monitoring supports operational awareness without introducing Version 1 notification features or exposing Teacher-private data.

## 19.2 Monitored Security Indicators

| Indicator | Purpose |
|---|---|
| Failed login attempts | Detect brute-force attacks and credential stuffing. |
| Repeated authorization failures | Detect unauthorized access attempts and potential privilege escalation. |
| Cross-Teacher access attempts | Detect tenant isolation violations or probing. |
| Rate limit violations | Detect abuse patterns. |
| Unusual file upload patterns | Detect potential malware upload or storage abuse. |
| Session anomalies | Detect session hijacking or concurrent session abuse. |
| Audit Log integrity | Verify that Audit Log entries remain intact and immutable. |
| Background job failures | Detect operational issues that may affect security controls. |

## 19.3 Monitoring Access

| Role | Monitoring visibility |
|---|---|
| Super Admin | Platform-level security monitoring within confirmed Platform administration scope. |
| Teacher | No direct access to security monitoring. Teachers see results through their Teacher Workspace. |
| Teacher Staff | No security monitoring access. |
| Student | No security monitoring access. |
| Parent | No security monitoring access. |

## 19.4 Monitoring Constraints

- Monitoring must not expose Teacher-private data.
- Monitoring must not introduce push, email, or SMS notification features (out of scope for V1).
- Monitoring tools, dashboards, and alert thresholds are not confirmed and must not be invented.
- Monitoring must not require Redis, external monitoring services, or unconfirmed infrastructure.
- Monitoring must use File Cache and Database Queue only within the confirmed cPanel Shared Hosting baseline.

---

# 20. Incident Response

## 20.1 Incident Response Principles

While a full incident response plan is an operational concern beyond Version 1 documentation scope, the following security incident handling principles are established.

## 20.2 Incident Categories

| Category | Examples | Initial response |
|---|---|---|
| **Authentication compromise** | Credential leak, session hijacking, mass account takeover | Invalidate affected sessions, force password resets, review Audit Log for unauthorized access. |
| **Authorization bypass** | Cross-Teacher data access, privilege escalation | Identify affected scope, review Audit Log, patch the vulnerability, notify affected Teachers. |
| **Data exposure** | Unauthorized access to Student records, Teacher-private content, or financial data | Identify exposed data scope, review Audit Log, secure the access path, assess impact. |
| **Tenant isolation violation** | Data leakage between Teacher Workspaces | Identify affected workspaces, review Audit Log, patch the isolation gap, notify affected Teachers. |
| **Denial of service** | Rate limit exhaustion, resource abuse | Apply emergency rate limits, identify attack source, restore service availability. |
| **Malware upload** | Malicious file uploaded through Homework or Lesson upload | Quarantine the file, review upload logs, assess impact, patch validation if needed. |

## 20.3 Incident Response Procedures

1. **Detection** — incidents may be detected through monitoring, Audit Log review, user reports, or operational alerts.
2. **Containment** — immediately contain the incident by revoking access, invalidating sessions, or isolating affected systems.
3. **Investigation** — review Audit Log entries, operational logs, and system state to determine the scope and impact.
4. **Remediation** — patch the vulnerability, restore affected data from backups if needed, and verify the fix.
5. **Notification** — notify affected parties according to legal and contractual obligations.
6. **Post-incident review** — document lessons learned and update security standards as needed.

## 20.4 Audit Log as Forensic Evidence

The Audit Log serves as the primary forensic evidence for security incidents:

- Audit Log entries record actor, role, context, event type, affected record, and origin information.
- Audit Log entries are immutable and permanently retained.
- Audit Log entries must not be edited, archived, or deleted, even during incident response.
- Incident response actions that modify data must themselves produce Audit Log entries.

---

# 21. Security Checklist

The following checklist summarizes the security controls that must be verified before Version 1 deployment.

## 21.1 Authentication Checklist

- [ ] Laravel Sanctum is configured and active.
- [ ] All protected endpoints require authentication.
- [ ] Login rate limiting is active.
- [ ] Failed login attempts are recorded in the Audit Log.
- [ ] Passwords are hashed using bcrypt or Argon2id.
- [ ] Password reset uses time-limited, single-use tokens.
- [ ] `APP_DEBUG` is set to `false` in production.
- [ ] Application key is generated and unique.
- [ ] Duplicate Student account prevention is enforced.

## 21.2 Authorization Checklist

- [ ] Laravel Gates & Policies with Custom RBAC are configured for all five roles.
- [ ] Every protected endpoint performs server-side authorization.
- [ ] Teacher Workspace isolation is enforced in all queries and responses.
- [ ] Student self-scope is enforced.
- [ ] Parent linked-Student read-only access is enforced.
- [ ] Teacher Staff permissions are limited to Teacher-assigned permissions.
- [ ] Super Admin does not operate inside Teacher Workspaces.
- [ ] No role has hard-delete permission.
- [ ] Archive and restore permissions are properly scoped.

## 21.3 Multi-Tenant Isolation Checklist

- [ ] All Teacher Workspace-owned queries are workspace-scoped.
- [ ] No cross-Teacher foreign keys exist in the data model.
- [ ] Reports preserve tenant isolation.
- [ ] File access preserves tenant isolation.
- [ ] Search does not reveal cross-Teacher data.
- [ ] Error messages do not expose cross-Teacher data.

## 21.4 Input Validation Checklist

- [ ] Form Requests validate all inputs at the request boundary.
- [ ] Enum values are validated against confirmed sets.
- [ ] Date ranges are validated.
- [ ] File types are validated against owning resource context.
- [ ] File size limits are enforced.
- [ ] MIME types are verified.
- [ ] SQL injection prevention is implemented through parameterized queries.
- [ ] XSS prevention is implemented through output encoding.
- [ ] CSRF protection is active on all state-changing requests.

## 21.5 Session and Transport Checklist

- [ ] Database session driver is configured.
- [ ] Session cookies have `HttpOnly`, `Secure`, and `SameSite` flags.
- [ ] HTTPS is enforced in production.
- [ ] Sessions expire after inactivity and have absolute timeouts.
- [ ] Sessions are invalidated on logout and password change.
- [ ] Security headers are set on responses.

## 21.6 File Security Checklist

- [ ] File uploads are validated for type, size, and MIME.
- [ ] File storage uses Laravel Public Storage with application-level authorization.
- [ ] Cross-Teacher file access is denied.
- [ ] Parent file uploads are denied.
- [ ] Video homework uploads are denied.
- [ ] File paths and storage references are not authorization proofs.

## 21.7 Audit and Monitoring Checklist

- [ ] All mandatory Audit Log events are recorded.
- [ ] Audit Log entries are append-only and immutable.
- [ ] Teacher Staff actions are attributed to the Teacher Staff user.
- [ ] Failed login attempts are logged.
- [ ] Security-relevant events are monitored.
- [ ] Rate limiting is active on sensitive endpoints.

## 21.8 Error Handling Checklist

- [ ] Error responses use the standardized error structure.
- [ ] Error responses do not expose stack traces, SQL, credentials, or private data.
- [ ] Validation errors provide field-level messages without exposing internals.
- [ ] Generic messages are used for authentication and authorization failures.

## 21.9 Backup and Recovery Checklist

- [ ] Database backups are scheduled.
- [ ] File backups are scheduled.
- [ ] Backups are encrypted where supported.
- [ ] Backup access is restricted.
- [ ] Backup integrity is verified periodically.
- [ ] Backup artifacts are not committed to version control.

## 21.10 cPanel Shared Hosting Checklist

- [ ] Laravel Public Storage is properly configured.
- [ ] File Cache is configured.
- [ ] Database Queue is configured.
- [ ] Database session driver is configured.
- [ ] Cron Jobs trigger the Laravel Scheduler.
- [ ] Apache or LiteSpeed security headers are configured.
- [ ] `.env` file is outside the web root and not accessible via HTTP.
- [ ] `storage/` and `bootstrap/cache/` directories have proper write permissions.
- [ ] Sensitive configuration is in environment variables, not source code.

---

# 22. Future Security Enhancements

The following are future considerations only and are not Version 1 commitments. All future security enhancements must preserve the confirmed Version 1 architecture, Teacher Workspace isolation, and business rules.

| Future area | Required future decision |
|---|---|
| **Two-factor authentication (2FA)** | Approve 2FA scope (all roles or selected roles), delivery mechanism (TOTP, SMS, email), and user consent model. |
| **Advanced password policy** | Approve password complexity rules, rotation requirements, account lockout behavior, and password history. |
| **API key management** | Approve API key scope, rotation, revocation, and monitoring for external integrations. |
| **Content Delivery Network (CDN)** | Approve CDN scope for static assets while preserving Teacher Workspace isolation for private content. |
| **Web Application Firewall (WAF)** | Approve WAF scope, rules, and integration with the cPanel hosting environment. |
| **Advanced intrusion detection** | Approve automated threat detection, anomaly detection, and alerting mechanisms. |
| **Security audit automation** | Approve automated security scanning tools, dependency vulnerability scanning, and penetration testing. |
| **Data encryption at rest** | Approve database-level encryption and file-system encryption where the hosting environment supports it. |
| **Advanced file scanning** | Approve virus/malware scanning for uploaded files, content inspection, and quarantine behavior. |
| **Session fingerprinting** | Approve device fingerprinting, geolocation-based session validation, and concurrent session management. |
| **Super Admin audit trail enhancement** | Approve enhanced logging of Super Admin actions with detailed before/after snapshots. |
| **Security incident automation** | Approve automated incident detection, containment, and notification workflows. |
| **Dependency security monitoring** | Approve automated monitoring of Composer and npm dependencies for known vulnerabilities. |
| **Backup automation and testing** | Approve automated backup verification, restore testing, and disaster recovery procedures. |

All future security enhancements must preserve:

- Teacher Workspace isolation (BR-003).
- One global Student account (BR-001).
- Parent linked-Student read-only access (BR-004).
- Archive instead of permanent deletion (BR-005).
- Immutable permanent Audit Log (BR-006).
- Flow A and Flow B separation.
- cPanel Shared Hosting compatibility (or separately approved infrastructure).
- Version 1 Web Application scope (BR-017).

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Project Context alignment | Passed — security standards follow the frozen Version 1 rules. All BR references, role definitions, and scope boundaries are consistent with `AI_DOCS/00_Project_Context.md`. |
| RBAC alignment | Passed — authorization standards are consistent with `AI_DOCS/08_RBAC.md` and `AI_DOCS/09_Permission_Matrix.md`. Role boundaries, permission categories, and tenant isolation rules are preserved. |
| Backend architecture alignment | Passed — security standards are consistent with `AI_DOCS/11_Backend_Architecture.md`. Laravel 12, PHP 8.3, Database Queue, File Cache, Database sessions, Laravel Scheduler, and cPanel Shared Hosting constraints are preserved. |
| API design alignment | Passed — API security standards are consistent with `AI_DOCS/10_API_Design.md`. Error response structure, rate limiting, versioning, and authorization flow are aligned. |
| QR Attendance alignment | Passed — QR Attendance security considerations are consistent with `AI_DOCS/16_QR_Attendance_System.md`. Dynamic QR Code, ID Card scanning, and manual Attendance security boundaries are preserved. |
| Subscription/Billing alignment | Passed — financial security standards are consistent with `AI_DOCS/17_Subscription_Billing.md`. Flow A/Flow B separation, payment status-only recording, and non-payment enforcement PENDING status are preserved. |
| File Storage alignment | Passed — file security standards are consistent with `AI_DOCS/20_File_Storage.md`. Laravel Public Storage, file type restrictions, Teacher Workspace ownership, and Parent upload denial are preserved. |
| Background Jobs alignment | Passed — security standards for background processing are consistent with `AI_DOCS/21_Background_Jobs.md`. Database Queue, idempotency, workspace scope preservation, and Audit Log obligations are preserved. |
| Search & Filtering alignment | Passed — search security standards are consistent with `AI_DOCS/22_Search_Filtering.md`. Scope resolution before filtering, cross-Teacher discovery prevention, and Archive-aware results are preserved. |
| Multi-tenant isolation | Passed — Teacher Workspace isolation is consistently enforced across all security sections. BR-003 is referenced and preserved throughout. |
| Student account rules | Passed — one global Student account, duplicate prevention, per-Teacher partitioning, and two registration methods are preserved. BR-001 and BR-022 are consistently referenced. |
| Parent access rules | Passed — linked-Student read-only access is preserved across all sections. BR-004 and BR-020 are consistently referenced. |
| Archive policy | Passed — no permanent deletion is referenced anywhere. Archive replaces deletion per BR-005 and the Archive Policy (§11). |
| Audit Log policy | Passed — mandatory Audit Log events are consistent with the Audit Log Policy (§10). Immutability, permanent retention, and attribution rules are preserved. |
| Payment handling | Passed — Version 1 records payment status only and does not process transactions. BR-019 is preserved. |
| Canonical terminology | Passed — Teacher Workspace, Educational Grade, Teaching Subject, Group, Student, Parent, Teacher Staff, Super Admin, Subscription, Flow A, Flow B, Enrollment, Archive, Audit Log, Dynamic QR Code, ID Card, Question Bank, Bubble Sheet, Lesson, Billable Student, Billing Cycle, and Homework are used consistently. |
| Version 1 scope | Passed — no native mobile security, online payment gateway security, notification security, marketplace security, Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices are introduced. |
| No source code | Passed — no source code, APIs, database tables, UI implementation, or physical configuration is defined. |
