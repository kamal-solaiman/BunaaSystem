# 34 — Error Codes

## Document Scope

This document is the **official reference and the registry for all application error codes** used throughout the Unified Education Platform Version 1.

It defines the standardized error code system for **business errors, validation errors, authentication errors, authorization errors, API errors, database errors, queue/background job errors, and system errors**, consolidating every error-related requirement already defined across the official documentation set — `00_Project_Context.md` (canonical rules and policies), `10_API_Design.md` §6 and §10 (error response and validation standards), `28_Coding_Standards.md` §15–§16 (error handling, HTTP status usage, business violation mappings, logging), `23_Security_Standards.md` §3, §6, §9, §15–§18 (security error policy, error message policy, logging), `02_Software_Requirements.md` (module validations), `12_Frontend_Architecture.md` (error classification), `13_UI_UX_Guidelines.md` (error presentation), `15`–`22` (feature error handling tables), `21_Background_Jobs.md` §13–§19 (retry, failure, logging), and `33_Validation_Rules.md` (validation conditions and user-message texts).

**Standardization, not invention of behavior.** This document standardizes error *codes, names, and message separation*; it does not create new rejection behavior, new HTTP statuses, new capabilities, or new business rules. Every error in this registry traces to a rejection, denial, failure, or conflict already documented in an owning document. Where documents confirm a rule but not its HTTP status or code name, this document assigns one using the §3–§4 mapping rules and records the assignment — it never contradicts a documented status (notably `28_Coding_Standards.md` §15.3 and `10_API_Design.md` §6). `00_Project_Context.md` is the Single Source of Truth and prevails if a conflict is found.

**Scope exclusions.** This document does not provide source code, exception classes, APIs (no endpoint definitions — endpoints are only cited from `10_API_Design.md`), database tables, SQL, UI implementation, or physical configuration.

**Canonical scope note.** Only the eight documented HTTP statuses are used (§4). Codes are defined at specification level; their Laravel 12 alignment is described in §2.5 and §26 without generating code.

---

# 1. Document Purpose

This document exists so that **every failure a user or operator can encounter has exactly one stable code, one HTTP status, one safe user message, and one diagnosable internal representation**.

Specifically, it:

1. Defines the error code structure and naming convention (§3) and the HTTP status discipline (§4) that every surface follows.
2. Registers every confirmed error of Version 1 — authentication (§5), authorization (§6), validation (§7), the Teacher (§8), Student (§9), and Parent (§10) modules, Attendance (§11), Homework (§12), Exam (§13), Question Bank (§14), Subscription (§15), Payment (§16), File Upload (§17), Search (§18), API-level (§19), Database (§20), Queue & Background Jobs (§21), and System (§22).
3. For every error records: **Error Code, HTTP Status, Error Name, Description, User Message, Internal Message, Possible Causes, Recommended Resolution,** and **Related Documents**.
4. Defines logging requirements (§23), the user-facing message policy (§24), the internal message policy (§25), the error response standards (§26), and the governance for future codes (§27).

**Audience:** AI assistants, backend and frontend developers, reviewers, testers, and operators diagnosing failures.

**Relationship to other documents.** Validation *conditions* (which input is invalid) are consolidated in `33_Validation_Rules.md`; this document owns the error *codes and message policy* those conditions produce. Business rules are consolidated in `32_Business_Rules.md`; error responses must never weaken them. The error response envelope is owned by `10_API_Design.md` §6 and restated in §26 here; this document populates its `error.code` field.

---

# 2. Error Handling Philosophy

**2.1 Consistent, safe, informative — in that order.** All error paths produce consistent responses; no error ever exposes implementation details, stack traces, SQL queries, server paths, credentials, framework or library versions, Teacher-private data, unlinked Student data, or another Teacher Workspace (`28_Coding_Standards.md` §2.10, §15.1; `23_Security_Standards.md` §18.3).

**2.2 Fail fast, fail at the right layer.** Invalid input, unauthorized access, missing resources, and business rule violations are detected and rejected as early as possible (`28_Coding_Standards.md` §2.3): transport shape first, authentication, authorization, input validation, business rules, then persistence integrity. The layer that rejects determines the code family.

**2.3 Two audiences, two messages.** Every error carries a **user message** (safe, generic-or-explanatory within strict limits, canonical terminology) and an **internal message** (diagnostic, logged, never returned). Detailed technical context (stack traces, SQL context, request details) belongs to operational logs only (`23_Security_Standards.md` §18.4; `28_Coding_Standards.md` §15.1).

**2.4 Honest states.** The interface never shows success before backend confirmation; error states explain the failure safely and direct toward recovery where possible (`13_UI_UX_Guidelines.md`). An error must never grant what was denied — a failed scan or rejected action confers no data and no access (`16_QR_Attendance_System.md` §14).

**2.5 Laravel 12 alignment without code.** All exceptions are caught and mapped to standardized API error responses through the application's exception mapping layer (`app/Exceptions/` — `28_Coding_Standards.md` §3.12); the backend emits the §26 envelope; the React 19 frontend normalizes errors at the shared HTTP boundary into the documented classification (`12_Frontend_Architecture.md`; `28_Coding_Standards.md` §15.2). This document defines the codes those mechanisms emit — not the mechanisms.

**2.6 Status discipline.** Eight HTTP statuses carry all Version 1 failure semantics (§4). No status outside the documented set is introduced; conflicts with business rules or state are 409; input-rule violations are 422; the distinction is mandatory (`10_API_Design.md` §6, §10).

**2.7 Codes are forever.** An error code is a public contract: unique, stable, never reused or reassigned. Renaming or repurposing a code would be a breaking change requiring a future API version (`10_API_Design.md` §5); retiring behavior archives the code in this registry rather than reassigning it (§27).

**2.8 Errors never leak scope.** Not-found and not-visible are indistinguishable; authorization failures never confirm resource existence; authentication failures never confirm account existence (`23_Security_Standards.md` §18.2; `10_API_Design.md` §6).

---

# 3. Error Code Structure

**Authoritative sources:** `10_API_Design.md` §6, §10; `28_Coding_Standards.md` §15; this registry (defined here, governing all codes below).

## 3.1 Format

Every error code in Version 1 follows this convention:

1. **Shape:** `PREFIX_DESCRIPTOR` in SCREAMING_SNAKE_CASE — uppercase letters and underscores only; **no digits, hyphens, dots, or version numbers**.
2. **PREFIX** identifies the subsystem category (§3.2). It answers "which part of the platform rejected this?".
3. **DESCRIPTOR** is a short, stable phrase in product vocabulary naming the *condition*, never the internal mechanism (never exception class names, SQL states, or framework terms).
4. **Canonical precedent:** `VALIDATION_FAILED` — already defined by `10_API_Design.md` §10 as the machine code of every 422 envelope — is the registry's anchor and remains exactly as documented.

## 3.2 Category Prefixes

| Prefix | Category | HTTP status(es) | Registry section |
|---|---|---|---|
| `AUTH_` | Authentication failures | 401, 403, 422, 429 | §5 |
| `AUTHZ_` | Authorization failures | 403, 404 | §6 |
| `VALIDATION_` | Input/field validation failures | 422 | §7 |
| `TEACHER_`, `TEACHER_STAFF_`, `GROUP_`, `GRADE_` | Teacher module (workspace) errors | 409, 422 | §8 |
| `STUDENT_` | Student module errors | 403, 404, 409, 422 | §9 |
| `PARENT_` | Parent module errors | 403, 404, 409, 422 | §10 |
| `ATTENDANCE_` | Attendance subsystem errors | 403, 409, 422 | §11 |
| `HOMEWORK_` | Homework subsystem errors | 404, 409, 422 | §12 |
| `EXAM_` | Exam subsystem errors | 404, 409, 422 | §13 |
| `QUESTION_BANK_` | Question Bank errors | 409, 422 | §14 |
| `SUBSCRIPTION_` | Flow A Subscription/billing errors | 409, 422 | §15 |
| `PAYMENT_` | Payment-status errors (both flows) | 403, 422 | §16 |
| `FILE_` | File upload/storage errors | 403, 404, 409, 422 | §17 |
| `SEARCH_` | Search/filter/sort/pagination errors | 403, 422 | §18 |
| `API_` | API contract/transport errors | 400, 404, 429 | §19 |
| `RESOURCE_` | Not-found/not-visible records | 404 | §19 |
| `BUSINESS_` | Cross-cutting capability/business rejections | 404, 409, 422 | §19 |
| `DATABASE_` | Persistence/integrity failures | 500 | §20 |
| `QUEUE_` | Background job/scheduler failures | Internal (no HTTP surface) | §21 |
| `SYSTEM_` | System/unexpected failures | 500 | §22 |

## 3.3 Registry Rules

1. **Uniqueness:** each code appears exactly once in this registry with one canonical definition. Other sections cross-reference it instead of redefining it.
2. **Stability:** codes are never renamed, reused, or reassigned; a retired behavior archives its code (§27).
3. **One status per code:** every code maps to exactly one HTTP status (or "internal" for §21); a status maps to many codes.
4. **Envelope discipline:** for 422 responses, `error.code` remains `VALIDATION_FAILED` exactly as `10_API_Design.md` §10 documents; the specific `VALIDATION_*` (or domain) condition code travels **with the field-level message** inside `errors` (extension defined here; it does not alter the documented envelope). For all other families, the specific code occupies `error.code` directly.
5. **Message pairing:** every code has one user message (§24 policy) and one internal message (§25 policy). Neither may contain the other's audience-inappropriate content.

---

# 4. HTTP Status Code Standards

**Authoritative sources:** `10_API_Design.md` §6; `28_Coding_Standards.md` §15.1, §15.3; `23_Security_Standards.md` §18.2.

Version 1 uses **exactly** these failure statuses. No other status is introduced.

| HTTP Status | Meaning | Used when | Representative codes (non-exhaustive) |
|---|---|---|---|
| 400 | Bad request or invalid operation. | The request itself is malformed or the operation is invalid regardless of field values. | `API_MALFORMED_REQUEST` |
| 401 | Authentication required or authentication failed. | No valid authenticated context, or credentials rejected. | `AUTH_UNAUTHENTICATED`, `AUTH_INVALID_CREDENTIALS`, `AUTH_SESSION_EXPIRED` |
| 403 | Authenticated user is not authorized. | The actor is known but may not perform the action; never reveals whether the target exists. | `AUTHZ_*`, `*_DENIED`, `*_WRITE_DENIED`, `*_UPLOAD_DENIED` |
| 404 | Resource not found **or not visible** to the user. | The record does not exist or is outside the actor's visibility; the two are indistinguishable. Also used for surfaces that do not exist in Version 1. | `RESOURCE_NOT_FOUND`, `API_UNSUPPORTED_ROUTE`, `*_NOT_AVAILABLE`, `BUSINESS_NOTIFICATION_UNSUPPORTED` |
| 409 | Conflict with a business rule or current resource state. | The input is well-formed but conflicts with a confirmed rule or current state (duplicate identity, second active Group, archived-vs-active state, amount/state mismatch). | `*_DUPLICATE_*`, `*_LIMIT_EXCEEDED`, `*_STATE_CONFLICT`, `*_ALREADY_*`, `*_ARCHIVED*` |
| 422 | Validation failed. | A field, format, enum, date, file, or confirmed rule about *input content* fails (including documented out-of-scope content rejections per `28_Coding_Standards.md` §15.3). Envelope code: `VALIDATION_FAILED`. | `VALIDATION_*`, `*_INVALID`, `*_IMMUTABLE`, `*_UNSUPPORTED`, `*_REJECTED` |
| 429 | Rate limit exceeded where rate limits apply. | Confirmed for login and password-reset requests; includes `Retry-After` header; never reveals internal thresholds. | `AUTH_LOGIN_RATE_LIMITED`, `AUTH_RESET_RATE_LIMITED`, `API_RATE_LIMIT_EXCEEDED` |
| 500 | Unexpected server error without exposing internals. | Anything not caused by user input: persistence failures, unexpected exceptions, dependency failures. User message is the generic §24 text; details go to operational logs only. | `DATABASE_*`, `SYSTEM_*` (§21 `QUEUE_*` codes have no HTTP surface) |

Status-assignment rules (used where an owning document confirms a rejection but not its status):

1. Input content wrong → **422**; well-formed input conflicting with a rule or state → **409**.
2. Denied-by-role action → **403**; invisible-to-actor record → **404**.
3. Categories documented as "rejected as out of scope" keep the status `28_Coding_Standards.md` §15.3 assigns (Teaching Subject change and hard-delete attempts: 422; notification requests: 404; payment processing: 422; Parent modification: 403; cross-Teacher access: 403).
4. Background processing has no HTTP status; its codes are internal-only (§21).

---

# 5. Authentication Errors

**Authoritative sources:** `23_Security_Standards.md` §3 (Login Security), §6 (Password Policy), §7 (Session Management), §18.2; `10_API_Design.md` §6, §13; `12_Frontend_Architecture.md` (error classification); `33_Validation_Rules.md` §4.

### AUTH-01 — Unauthenticated
- **Error Code:** `AUTH_UNAUTHENTICATED`
- **HTTP Status:** 401
- **Error Name:** Authentication Required
- **Description:** A protected endpoint was reached without a valid authenticated user context. Authentication is validated on every request; no endpoint trusts cached or frontend-provided authentication state.
- **User Message:** "Authentication is required."
- **Internal Message:** `AUTH_UNAUTHENTICATED: protected endpoint reached without valid session/token context.`
- **Possible Causes:** No session/token presented; session destroyed on logout; session invalidated on password change or reset; token revoked.
- **Recommended Resolution:** Authenticate through the approved login journey; the frontend clears protected context and cache before redirecting (do not retry the protected call first).
- **Related Documents:** `23_Security_Standards.md` §3.2, §7; `10_API_Design.md` §3, §6; `12_Frontend_Architecture.md` (error classification); `33_Validation_Rules.md` AUT-14.

### AUTH-02 — Invalid Credentials
- **Error Code:** `AUTH_INVALID_CREDENTIALS`
- **HTTP Status:** 401
- **Error Name:** Invalid Credentials
- **Description:** A login attempt failed credential validation. The response must not reveal whether the account exists, whether the secret was wrong, or whether the account is archived. Failed logins are recorded in the Audit Log with the attempted identifier (without exposing whether the account exists), timestamp, IP address, and device/client information.
- **User Message:** "The provided credentials are incorrect."
- **Internal Message:** `AUTH_INVALID_CREDENTIALS: login credential check failed for attempted identifier (recorded in Audit Log without confirming account state).`
- **Possible Causes:** Unknown identifier; wrong secret; attempt against an archived account (indistinguishable by design).
- **Recommended Resolution:** Re-enter credentials carefully; use the password-reset flow if the secret is forgotten; no indicator distinguishes the causes — by design.
- **Related Documents:** `23_Security_Standards.md` §3.3, §18.2; `10_API_Design.md` §6, §13; `33_Validation_Rules.md` AUT-04.

### AUTH-03 — Session Expired
- **Error Code:** `AUTH_SESSION_EXPIRED`
- **HTTP Status:** 401
- **Error Name:** Session Expired
- **Description:** A previously authenticated context is no longer valid (idle timeout, absolute session lifetime, or scheduled invalidation per session policy). Responses treat expired sessions exactly like missing authentication.
- **User Message:** "Your session has expired. Please log in again."
- **Internal Message:** `AUTH_SESSION_EXPIRED: request presented an expired/invalidated session context.`
- **Possible Causes:** Idle timeout; absolute timeout reached; session invalidated by password change/reset; session rotation on privilege change.
- **Recommended Resolution:** Re-authenticate; pending unsaved input follows the safe-recovery UI rules (preserve valid form values where safe).
- **Related Documents:** `23_Security_Standards.md` §7.2; `13_UI_UX_Guidelines.md` (authentication error state); `33_Validation_Rules.md` AUT-14.

### AUTH-04 — Login Rate Limited
- **Error Code:** `AUTH_LOGIN_RATE_LIMITED`
- **HTTP Status:** 429
- **Error Name:** Too Many Login Attempts
- **Description:** The login endpoint's brute-force protection rejected the attempt. The response must not reveal internal rate-limit thresholds; a `Retry-After` header communicates the wait.
- **User Message:** "Too many login attempts. Please try again later."
- **Internal Message:** `AUTH_LOGIN_RATE_LIMITED: login rate limit engaged for source (thresholds internal-only); repeated failures monitored per security monitoring rules.`
- **Possible Causes:** Repeated failed logins from a client or against an account; automated guessing.
- **Recommended Resolution:** Wait for the indicated interval; if locked out unexpectedly, use the reset flow after the interval; genuine users succeed normally after limiting clears.
- **Related Documents:** `23_Security_Standards.md` §3.3, §18.2; `10_API_Design.md` §13; `33_Validation_Rules.md` AUT-05, API-11.

### AUTH-05 — Password Policy Violation
- **Error Code:** `AUTH_PASSWORD_POLICY_VIOLATION`
- **HTTP Status:** 422
- **Error Name:** Password Policy Violation
- **Description:** A new Authentication Secret failed the composition policy at set/change/reset time: minimum 8 characters, at least one uppercase letter, one lowercase letter, and one digit (special characters recommended, not mandatory). Password history checking does not exist in Version 1 and must not be enforced. Field-level failure inside the `VALIDATION_FAILED` envelope.
- **User Message:** "The Authentication Secret must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, and one digit."
- **Internal Message:** `AUTH_PASSWORD_POLICY_VIOLATION: new secret failed composition policy (field: password).`
- **Possible Causes:** Too short; missing uppercase/lowercase/digit.
- **Recommended Resolution:** Supply a compliant new secret; the form preserves other valid inputs.
- **Related Documents:** `23_Security_Standards.md` §6.1, §6.3; `33_Validation_Rules.md` AUT-03, AUT-10, MSG-10.

### AUTH-06 — Reset Token Invalid Or Expired
- **Error Code:** `AUTH_RESET_TOKEN_INVALID_OR_EXPIRED`
- **HTTP Status:** 422
- **Error Name:** Reset Token Invalid Or Expired
- **Description:** A password-reset token is malformed, already used, or expired. Reset tokens are time-limited and single-use; reuse or expiry rejects the reset attempt without revealing account state.
- **User Message:** "This reset link is invalid or has expired."
- **Internal Message:** `AUTH_RESET_TOKEN_INVALID_OR_EXPIRED: reset token rejected (invalid/used/expired; single-use enforcement applied).`
- **Possible Causes:** Link older than the token lifetime; link already consumed; tampered token.
- **Recommended Resolution:** Request a fresh reset link; reset requests themselves are rate-limited (AUTH-07).
- **Related Documents:** `23_Security_Standards.md` §6.2; `33_Validation_Rules.md` AUT-07, AUT-08.

### AUTH-07 — Reset Request Rate Limited
- **Error Code:** `AUTH_RESET_RATE_LIMITED`
- **HTTP Status:** 429
- **Error Name:** Too Many Reset Requests
- **Description:** Password reset requests are rate-limited. Responses remain non-disclosing about account existence even while limiting; a `Retry-After` header communicates the wait.
- **User Message:** "Too many requests. Please try again later."
- **Internal Message:** `AUTH_RESET_RATE_LIMITED: reset-request rate limit engaged for source.`
- **Possible Causes:** Repeated reset requests for the same or many identifiers.
- **Recommended Resolution:** Wait for the indicated interval; check the previously received reset message.
- **Related Documents:** `23_Security_Standards.md` §6.2, §18.2.

### AUTH-08 — Mandatory Secret Change Pending
- **Error Code:** `AUTH_FIRST_LOGIN_PASSWORD_CHANGE_REQUIRED`
- **HTTP Status:** 403
- **Error Name:** Mandatory Secret Change Pending
- **Description:** An account operating under a default or temporary secret attempts general authenticated use before setting a new secret. Default/temporary secrets must be changed on first login; general access is withheld until then. (Status assigned per §4 rule 2 — authenticated but not yet authorized for general use.)
- **User Message:** "You must set a new Authentication Secret before continuing."
- **Internal Message:** `AUTH_FIRST_LOGIN_PASSWORD_CHANGE_REQUIRED: temporary-secret account attempted general access.`
- **Possible Causes:** First login after account issuance with a temporary secret.
- **Recommended Resolution:** Complete the mandatory secret change (AUTH-05 policy applies); sessions are invalidated after the change.
- **Related Documents:** `23_Security_Standards.md` §6.3; `33_Validation_Rules.md` AUT-09.

**Cross-referenced codes:** Student registration/activation failures are module errors — see §9 (`STUDENT_DUPLICATE_ACCOUNT`, `STUDENT_ACTIVATION_MISMATCH`, `STUDENT_ACCOUNT_ALREADY_ACTIVE`); session invalidation on logout succeeds silently (no error) per `23_Security_Standards.md` §7.

---

# 6. Authorization Errors

**Authoritative sources:** `08_RBAC.md`; `09_Permission_Matrix.md`; `23_Security_Standards.md` §4–§5, §18.2; `10_API_Design.md` §4, §6; `17_Subscription_Billing.md` §18; Q-012; `32_Business_Rules.md` §23.

### AUTHZ-01 — Unauthorized (Generic)
- **Error Code:** `AUTHZ_UNAUTHORIZED`
- **HTTP Status:** 403
- **Error Name:** Access Denied
- **Description:** The authenticated actor lacks the required role, scope, ownership, relationship, or permission for the action. The response must not reveal whether the target resource exists. Authorization failures are logged as security-relevant events.
- **User Message:** "You do not have permission to perform this action."
- **Internal Message:** `AUTHZ_UNAUTHORIZED: authorization check failed (role/scope/permission/ownership/context) for attempted action.`
- **Possible Causes:** Missing endpoint permission; wrong role context; ownership/relationship check failure.
- **Recommended Resolution:** Do not retry with the same context; use an account/role context that is authorized for the task.
- **Related Documents:** `23_Security_Standards.md` §4.2, §15.5, §18.2; `10_API_Design.md` §4; `08_RBAC.md`; `09_Permission_Matrix.md`.

### AUTHZ-02 — Cross-Workspace Access Attempt
- **Error Code:** `AUTHZ_CROSS_WORKSPACE_ACCESS`
- **HTTP Status:** 403
- **Error Name:** Cross-Workspace Access Denied
- **Description:** An action attempted to operate on another Teacher Workspace's data (write-style attempts are denied with 403 without exposing private data). Record *visibility* outside scope is instead indistinguishable from absence — see `RESOURCE_NOT_FOUND` (§19). Cross-Teacher access attempts are logged as security-relevant events.
- **User Message:** "You do not have permission to perform this action."
- **Internal Message:** `AUTHZ_CROSS_WORKSPACE_ACCESS: cross-Teacher Workspace access attempt blocked (attempted target context logged, never returned).`
- **Possible Causes:** Forged or stale workspace references; scope confusion between multiple Teacher relationships; direct object reference attempts.
- **Recommended Resolution:** Operate strictly within the current authorized context; clear stale context after role/context changes.
- **Related Documents:** `23_Security_Standards.md` §5, §15.5; `28_Coding_Standards.md` §15.3 (cross-Teacher access → 403); `32_Business_Rules.md` §5 (BR-003); `29_Project_Decisions.md` D-020.

### AUTHZ-03 — Workspace Context Mismatch
- **Error Code:** `AUTHZ_WORKSPACE_CONTEXT_MISMATCH`
- **HTTP Status:** 403
- **Error Name:** Workspace Context Mismatch
- **Description:** A request mixes references from different authorized scopes (e.g., a Group from one Teacher Workspace with an Attendance Session from another, or a Question Bank from a foreign workspace in Exam composition). Cross-workspace persistence graphs are prohibited (D-020) and rejected here at the operation level.
- **User Message:** "The selected items cannot be used together."
- **Internal Message:** `AUTHZ_WORKSPACE_CONTEXT_MISMATCH: payload references span multiple Teacher Workspaces; operation rejected.`
- **Possible Causes:** Client assembled cross-scope references; stale cached lists after context change; crafted multi-scope payload.
- **Recommended Resolution:** Re-select items from the active context; refresh scope-dependent lists after switching context.
- **Related Documents:** `29_Project_Decisions.md` D-020; `33_Validation_Rules.md` GEN-09, INT-02, INT-14; `06_Database_Design.md` §12–§13.

### AUTHZ-04 — Missing Staff Permission
- **Error Code:** `AUTHZ_STAFF_PERMISSION_MISSING`
- **HTTP Status:** 403
- **Error Name:** Teacher Staff Permission Missing
- **Description:** A Teacher Staff user attempted an operation requiring a permission the Teacher has not assigned. Teacher Staff hold only Teacher-assigned permissions inside the creating Teacher Workspace; permission beyond that does not exist to check. Permission granularity is PENDING (Q-011) — no finer capability may be assumed by either side of the check.
- **User Message:** "You do not have permission to perform this action."
- **Internal Message:** `AUTHZ_STAFF_PERMISSION_MISSING: staff user lacks assigned endpoint permission for attempted action.`
- **Possible Causes:** Permission never assigned; permission revoked; attempt in wrong workspace context.
- **Recommended Resolution:** Ask the Teacher to assign the required permission if the task is intended; otherwise use the confirmed manual/own-task paths.
- **Related Documents:** `08_RBAC.md`; `09_Permission_Matrix.md`; `00_Project_Context.md` §15.1 (Q-011); `32_Business_Rules.md` §5 (BR-013).

### AUTHZ-05 — Flow A Management Denied
- **Error Code:** `AUTHZ_FLOW_A_MANAGEMENT_DENIED`
- **HTTP Status:** 403
- **Error Name:** Flow A Management Denied
- **Description:** A Teacher, Teacher Staff, Student, or Parent attempted to manage Flow A Subscription — including another Teacher's Subscription or Platform pricing. Flow A management (pricing, calculation, status, records) is Super-Admin-Platform scope; Teachers can only view their own status where exposed.
- **User Message:** "You do not have permission to perform this action."
- **Internal Message:** `AUTHZ_FLOW_A_MANAGEMENT_DENIED: non-Super-Admin actor attempted Flow A management.`
- **Possible Causes:** Misrouted request; role-context confusion; attempted direct pricing/Subscription manipulation.
- **Recommended Resolution:** Flow A concerns route to the Platform administration; Teachers review their own Subscription status view.
- **Related Documents:** `17_Subscription_Billing.md` §18; `09_Permission_Matrix.md`; `32_Business_Rules.md` §16–§17.

### AUTHZ-06 — Visibility Expansion Denied (PENDING Boundary)
- **Error Code:** `AUTHZ_VISIBILITY_EXPANSION_DENIED`
- **HTTP Status:** 403
- **Error Name:** Visibility Expansion Denied
- **Description:** A request attempts visibility beyond confirmed boundaries — notably unrestricted Super Admin access to Teacher-private content (Lesson videos, Question Banks, other Teacher-private content), which is **PENDING (Q-012)**: the confirmed posture grants Platform-level authority with aggregates/operational data only, never unrestricted content access. Audit/tooling surfaces enforce the same boundary.
- **User Message:** "This content is not available."
- **Internal Message:** `AUTHZ_VISIBILITY_EXPANSION_DENIED: attempted Teacher-private content access beyond confirmed visibility boundary (Q-012).`
- **Possible Causes:** Assumed admin omniscience; report/filter combinations targeting private content.
- **Recommended Resolution:** Rely on confirmed Platform-level aggregates and operational views; await a documented Q-012 resolution before any expansion.
- **Related Documents:** `00_Project_Context.md` §15.1 (Q-012); `29_Project_Decisions.md` D-005; `32_Business_Rules.md` §8; `10_API_Design.md` §4, §30.

### AUTHZ-07 — Parent Write Denied
- **Error Code:** `PARENT_WRITE_DENIED`
- **HTTP Status:** 403
- **Error Name:** Parent Modification Denied
- **Description:** A Parent attempted to create, record, update, submit, grade, archive, or restore educational, Student, Teacher Workspace, or payment-status data. Parent access is read-only everywhere; no payload makes a Parent write valid. (Canonical definition here; cross-referenced from §10.)
- **User Message:** "You do not have permission to perform this action."
- **Internal Message:** `PARENT_WRITE_DENIED: Parent actor attempted write operation (read-only role).`
- **Possible Causes:** Replayed write request; UI assumption bypassed; forged client call.
- **Recommended Resolution:** None client-side — behavior is by design; Parent views remain read-only.
- **Related Documents:** `00_Project_Context.md` §9 (BR-004); `28_Coding_Standards.md` §15.3 (Parent modification → 403); `02_Software_Requirements.md` Part 4; `33_Validation_Rules.md` PAR-02.

### AUTHZ-08 — Authorization Bypass Attempt Via Client
- **Error Code:** `AUTHZ_CLIENT_AUTHORITY_REJECTED`
- **HTTP Status:** 403 or 404
- **Error Name:** Client-Asserted Authority Rejected
- **Description:** A request asserts authority through client-controllable data — route parameters, hidden fields, cached capability hints, or context labels. The frontend is not a security boundary; server-side authorization re-evaluates every request, and client-asserted proofs are rejected (403, or 404 when the asserted target must remain invisible).
- **User Message:** "You do not have permission to perform this action." / "Not found."
- **Internal Message:** `AUTHZ_CLIENT_AUTHORITY_REJECTED: client-supplied authority assertion failed server re-evaluation.`
- **Possible Causes:** Manipulated route/query parameters; stale cached permissions; crafted context values.
- **Recommended Resolution:** Refresh the context from backend responses; the frontend must revalidate contexts and treat none as proof.
- **Related Documents:** `23_Security_Standards.md` §4.4; `12_Frontend_Architecture.md` (route state, context revalidation); `14_UI_Components.md` (context labels are not proof).

**Cross-referenced codes:** `PARENT_UPLOAD_DENIED` (§10/§17), `PAYMENT_WRITE_DENIED` (§16), `FILE_ACCESS_DENIED` (§17), `SEARCH_SCOPE_DENIED` (§18), `LOGIN_AS_TEACHER_UNSUPPORTED` (§19), and all 404-visibility cases via `RESOURCE_NOT_FOUND` (§19).

---

# 7. Validation Errors

**Authoritative sources:** `10_API_Design.md` §10; `28_Coding_Standards.md` §17.4; `23_Security_Standards.md` §10.4, §18.2; `33_Validation_Rules.md` §3, §22; `12_Frontend_Architecture.md` §11.

All codes in this section are **422** and travel inside the `VALIDATION_FAILED` envelope: `error.code` is `VALIDATION_FAILED`; the specific condition code below accompanies the field-level message in `errors` (§3.3 rule 4). The generic "Invalid input" family is defined once here; domain-specific invalid-input codes (e.g., `GROUP_PRICE_INVALID`) appear in their module sections and follow the same envelope rule.

### VAL-01 — Validation Failed (Canonical Envelope)
- **Error Code:** `VALIDATION_FAILED`
- **HTTP Status:** 422
- **Error Name:** Validation Failed
- **Description:** The canonical machine code of every validation failure response, exactly as `10_API_Design.md` §10 defines. The response carries `success` false, `error.code` `VALIDATION_FAILED`, a summary `error.message`, and `errors` with field-level messages.
- **User Message:** "The given data could not be validated." (summary; field texts per `33_Validation_Rules.md` §22)
- **Internal Message:** `VALIDATION_FAILED: request failed Form Request validation (failing fields and their rules logged non-sensitively).`
- **Possible Causes:** Any field rule failure in `33_Validation_Rules.md`.
- **Recommended Resolution:** Correct the indicated fields; server field messages attach to the active form; valid entries are preserved.
- **Related Documents:** `10_API_Design.md` §10; `28_Coding_Standards.md` §17.4; `33_Validation_Rules.md` MSG-01/02.

### VAL-02 — Required Field Missing
- **Error Code:** `VALIDATION_REQUIRED`
- **HTTP Status:** 422
- **Error Name:** Required Field Missing
- **Description:** A field its owning document marks Required was absent, null, or empty.
- **User Message:** "The {Field Name} field is required."
- **Internal Message:** `VALIDATION_REQUIRED: required field absent/empty (field logged).`
- **Possible Causes:** Omitted field; empty string submission; disabled control dropped from payload.
- **Recommended Resolution:** Supply the field as the form requires.
- **Related Documents:** `33_Validation_Rules.md` GEN-01, MSG-10; `07_Data_Dictionary.md` (Required/Optional per attribute).

### VAL-03 — Type Mismatch
- **Error Code:** `VALIDATION_TYPE_MISMATCH`
- **HTTP Status:** 422
- **Error Name:** Data Type Mismatch
- **Description:** A value does not match the logical data type (Text, Identifier, Reference, Date, DateTime, Money, Number, Enum, Status, Secret, Structured Data) of its field; invalid shapes are never coerced.
- **User Message:** "The {Field Name} format is invalid."
- **Internal Message:** `VALIDATION_TYPE_MISMATCH: value failed type check (field + expected logical type logged).`
- **Possible Causes:** Wrong JSON type; text for numeric/date; malformed structured input.
- **Recommended Resolution:** Supply the correctly typed value; client schema should catch this before submission.
- **Related Documents:** `33_Validation_Rules.md` GEN-02; `07_Data_Dictionary.md` (all entities); `23_Security_Standards.md` §10.2.

### VAL-04 — Format Invalid
- **Error Code:** `VALIDATION_FORMAT_INVALID`
- **HTTP Status:** 422
- **Error Name:** Format Invalid
- **Description:** A correctly typed value fails a confirmed format rule (e.g., email format where email is used). No unconfirmed format (phone patterns, postal formats, schedule syntax) may be enforced.
- **User Message:** "The {Field Name} format is invalid."
- **Internal Message:** `VALIDATION_FORMAT_INVALID: value failed confirmed format rule (field + rule logged).`
- **Possible Causes:** Malformed email where email is required; malformed documented format.
- **Recommended Resolution:** Correct to the expected format shown by helper text.
- **Related Documents:** `33_Validation_Rules.md` GEN-07, §3.4; `07_Data_Dictionary.md` §11 (format deferred).

### VAL-05 — Length Exceeded
- **Error Code:** `VALIDATION_LENGTH_EXCEEDED`
- **HTTP Status:** 422
- **Error Name:** Length Exceeded
- **Description:** Text input exceeds its bounded allowance. Bounds exist for safety; **no product-facing numeric maximum is presented** because the documents defer exact maxima (UI and messages must not state a fabricated number).
- **User Message:** "The {Field Name} must not exceed the allowed length."
- **Internal Message:** `VALIDATION_LENGTH_EXCEEDED: text exceeded configured bound (field + observed length logged; bounds are configuration, not product numbers).`
- **Possible Causes:** Pasted oversized content; programmatic bulk input.
- **Recommended Resolution:** Shorten the input.
- **Related Documents:** `33_Validation_Rules.md` GEN-04, §3.4; `23_Security_Standards.md` §10.2.

### VAL-06 — Enum Value Invalid
- **Error Code:** `VALIDATION_ENUM_INVALID`
- **HTTP Status:** 422
- **Error Name:** Enum Value Invalid
- **Description:** A value outside the confirmed set for an Enum/Status field (§3.3 of `33_Validation_Rules.md`: Pricing Type Monthly/Per Lesson; Question Type four types; Homework formats; Attendance methods; Flow A/B; Created By Method; documented statuses). Domain-named variants (e.g., `GROUP_PRICING_TYPE_INVALID`) share this condition.
- **User Message:** "The selected {Field Name} is invalid." (or the domain text, e.g., "The Pricing Type must be Monthly or Per Lesson.")
- **Internal Message:** `VALIDATION_ENUM_INVALID: value outside confirmed set (field + rejected value logged; value logged only when non-sensitive).`
- **Possible Causes:** Outdated client enum; crafted value; typo in structured payload.
- **Recommended Resolution:** Choose from the presented options; refresh the page to reload current option sets.
- **Related Documents:** `33_Validation_Rules.md` GEN-03, §3.3; `28_Coding_Standards.md` §17.2.

### VAL-07 — Date Invalid
- **Error Code:** `VALIDATION_DATE_INVALID`
- **HTTP Status:** 422
- **Error Name:** Date Invalid
- **Description:** A Date/DateTime field carries a non-calendar value, or a client attempted to supply a system-generated timestamp (Created At, Recorded At, Occurred At, Generated At, Submitted At, Started At).
- **User Message:** "The {Field Name} must be a valid date."
- **Internal Message:** `VALIDATION_DATE_INVALID: invalid calendar value or client-supplied system timestamp (field logged).`
- **Possible Causes:** Malformed date string; forged system-timestamp field.
- **Recommended Resolution:** Use the date controls provided; do not send system-managed fields.
- **Related Documents:** `33_Validation_Rules.md` GEN-05, INT-13; `07_Data_Dictionary.md` (timestamp attributes).

### VAL-08 — Date Range Invalid
- **Error Code:** `VALIDATION_DATE_RANGE_INVALID`
- **HTTP Status:** 422
- **Error Name:** Date Range Invalid
- **Description:** A range start is after its end (`from_date`/`to_date`, report start/end, Enrollment End before Start). Billing Cycle shape errors use `SUBSCRIPTION_BILLING_CYCLE_INVALID` (§15).
- **User Message:** "The start date must not be after the end date."
- **Internal Message:** `VALIDATION_DATE_RANGE_INVALID: range ordering violated (field pair logged).`
- **Possible Causes:** Swapped range endpoints; stale prefilled filters.
- **Recommended Resolution:** Correct the range order.
- **Related Documents:** `33_Validation_Rules.md` GEN-06, SRC-07, STU-07; `23_Security_Standards.md` §10.2.

### VAL-09 — Numeric Range Invalid
- **Error Code:** `VALIDATION_NUMERIC_RANGE_INVALID`
- **HTTP Status:** 422
- **Error Name:** Numeric Range Invalid
- **Description:** A Number/Money/Identifier value is outside its valid range: identifiers must be positive integers; counts non-negative integers; `Price` non-negative monetary.
- **User Message:** "The {Field Name} must be a valid amount." / "The {Field Name} must be zero or more."
- **Internal Message:** `VALIDATION_NUMERIC_RANGE_INVALID: numeric constraint failed (field + constraint logged).`
- **Possible Causes:** Negative price; zero/negative identifier; non-integer count.
- **Recommended Resolution:** Enter a value within the indicated range.
- **Related Documents:** `33_Validation_Rules.md` GEN-08, GRP-03, API-04; `23_Security_Standards.md` §10.2.

### VAL-10 — Reference Invalid (Scoped Existence)
- **Error Code:** `VALIDATION_REFERENCE_INVALID`
- **HTTP Status:** 422
- **Error Name:** Reference Invalid
- **Description:** A reference field names a record that does not exist **within the authorized scope** — or names one whose existence must not be disclosed; the message is identical in both cases. (For path identifiers the outcome is 404 via `RESOURCE_NOT_FOUND`; for body/filter references it is this 422.)
- **User Message:** "The selected {Field Name} is invalid."
- **Internal Message:** `VALIDATION_REFERENCE_INVALID: scoped existence check failed (field + scope logged; never whether the record exists elsewhere).`
- **Possible Causes:** Stale selection after archive/context switch; references outside the actor's scope; nonexistent records.
- **Recommended Resolution:** Re-select from the refreshed, currently offered options.
- **Related Documents:** `33_Validation_Rules.md` GEN-09, GEN-12, SRC-06; `10_API_Design.md` §6, §8.

### VAL-11 — Prohibited Input
- **Error Code:** `VALIDATION_PROHIBITED_INPUT`
- **HTTP Status:** 422
- **Error Name:** Prohibited Input
- **Description:** The payload contains input a confirmed rule forbids at field level when a specific domain code does not apply (e.g., client-supplied system fields not covered by VAL-07). Domain-named prohibitions (`TEACHER_SUBJECT_IMMUTABLE`, `PAYMENT_GATEWAY_SETTING_REJECTED`, etc.) take precedence in their sections.
- **User Message:** "The {Field Name} is not editable here." / "The request contains unsupported data."
- **Internal Message:** `VALIDATION_PROHIBITED_INPUT: payload carried prohibited field (field logged).`
- **Possible Causes:** Over-posting; crafted payloads; outdated client contract.
- **Recommended Resolution:** Send only the fields the form/contract documents.
- **Related Documents:** `33_Validation_Rules.md` STU-08/09, PAY-06, TCH-02; `10_API_Design.md` endpoint request-body columns.

### VAL-12 — Field-Level Uniqueness Conflict
- **Error Code:** `VALIDATION_UNIQUENESS_CONFLICT`
- **HTTP Status:** 422
- **Error Name:** Value Already In Use
- **Description:** A field with confirmed uniqueness (e.g., Login Identifier on account update) collides with an existing value. The global Student-account rule is a *business* conflict and uses `STUDENT_DUPLICATE_ACCOUNT` (409, §9) instead.
- **User Message:** "An account with this identity already exists."
- **Internal Message:** `VALIDATION_UNIQUENESS_CONFLICT: unique constraint on field collided at validation (field logged; not which record).`
- **Possible Causes:** Identity already registered; concurrent submissions.
- **Recommended Resolution:** Use the existing account (login/reset paths) or a different value as the form indicates.
- **Related Documents:** `33_Validation_Rules.md` GEN-10; `07_Data_Dictionary.md` §1; `28_Coding_Standards.md` §15.3 (contrast with 409 business duplicate).

**Cross-referenced codes:** Domain validation codes appear in §8–§18 (`GROUP_PRICING_TYPE_INVALID`, `ATTENDANCE_STATUS_INVALID`, `HOMEWORK_FORMAT_UNSUPPORTED`, `EXAM_ANSWER_TYPE_MISMATCH`, `QUESTION_BANK_TYPE_UNSUPPORTED`, `SUBSCRIPTION_*`, `PAYMENT_*`, `FILE_*`, `SEARCH_*`); all share the 422 envelope rule (§3.3 rule 4).

---

# 8. Teacher Module Errors

**Authoritative sources:** `02_Software_Requirements.md` Part 2 (Teacher module validations) and Part 5 (Teacher Account Management validations); `28_Coding_Standards.md` §15.3; `10_API_Design.md` §14, §17, §18, §26, §27; `07_Data_Dictionary.md` §4, §9–§12, §30–§31; `33_Validation_Rules.md` §5, §8, §9.

### TCH-01 — Teaching Subject Required
- **Error Code:** `TEACHER_SUBJECT_REQUIRED`
- **HTTP Status:** 422
- **Error Name:** Teaching Subject Required
- **Description:** Teacher account creation without a Teaching Subject. Exactly one Teaching Subject is selected once per Teacher account through the approved account-creation authority.
- **User Message:** "The Teaching Subject field is required."
- **Internal Message:** `TEACHER_SUBJECT_REQUIRED: Teacher creation payload missing Teaching Subject.`
- **Possible Causes:** Omitted subject on creation; empty selection submitted.
- **Recommended Resolution:** Select exactly one Teaching Subject before creating the account.
- **Related Documents:** `02_Software_Requirements.md` Part 5; `07_Data_Dictionary.md` §31; `10_API_Design.md` §14; `33_Validation_Rules.md` TCH-01.

### TCH-02 — Teaching Subject Immutable
- **Error Code:** `TEACHER_SUBJECT_IMMUTABLE`
- **HTTP Status:** 422
- **Error Name:** Teaching Subject Change Rejected
- **Description:** Any attempt to change the Teaching Subject after account creation — at account update, at Teacher Workspace Settings update, or Platform-level Teacher update — is rejected. Documented status: **422** (`28_Coding_Standards.md` §15.3).
- **User Message:** "The Teaching Subject cannot be changed after account creation."
- **Internal Message:** `TEACHER_SUBJECT_IMMUTABLE: update payload attempted Teaching Subject change.`
- **Possible Causes:** Settings payload containing subject; crafted update; stale form state.
- **Recommended Resolution:** Teaching a different Teaching Subject requires a separate Teacher account (`07_Data_Dictionary.md` §4).
- **Related Documents:** `28_Coding_Standards.md` §15.3; `10_API_Design.md` §14, §27; `32_Business_Rules.md` §5 (BR-016); `33_Validation_Rules.md` TCH-02, INT-06.

### TCH-03 — Teacher Account Data Invalid
- **Error Code:** `TEACHER_ACCOUNT_DATA_INVALID`
- **HTTP Status:** 422
- **Error Name:** Teacher Account Data Invalid
- **Description:** Teacher account creation/update payload fails required-field or validity rules per the account contract (required information present and valid; only allowed fields updated).
- **User Message:** "The {Field Name} field is required." / "The {Field Name} format is invalid."
- **Internal Message:** `TEACHER_ACCOUNT_DATA_INVALID: account field validation failed (fields logged).`
- **Possible Causes:** Missing required identity fields; invalid values; non-editable fields supplied.
- **Recommended Resolution:** Correct the indicated fields.
- **Related Documents:** `02_Software_Requirements.md` Part 5; `10_API_Design.md` §14; `33_Validation_Rules.md` TCH-03.

### TCH-04 — Teacher Archive State Conflict
- **Error Code:** `TEACHER_ARCHIVE_STATE_CONFLICT`
- **HTTP Status:** 409
- **Error Name:** Teacher Archive State Conflict
- **Description:** Archiving an already-archived Teacher account, or restoring one that is not archived. Archive/restore is the only lifecycle; state mismatches conflict.
- **User Message:** "This Teacher account is already archived." / "This Teacher account is not archived."
- **Internal Message:** `TEACHER_ARCHIVE_STATE_CONFLICT: archive/restore requested against mismatched current state.`
- **Possible Causes:** Repeated action; stale page state; concurrent administrator actions.
- **Recommended Resolution:** Refresh the record state; no repeat action needed once the desired state holds.
- **Related Documents:** `10_API_Design.md` §14 (archive/restore 409); `00_Project_Context.md` §11; `33_Validation_Rules.md` TCH-09.

### TCH-05 — Teacher Staff Inactive
- **Error Code:** `TEACHER_STAFF_INACTIVE`
- **HTTP Status:** 409
- **Error Name:** Teacher Staff Account Not Active
- **Description:** An archived Teacher Staff account is used as if active — for operational use, permission assignment, or workspace action. Archived staff remain historically attributed but inactive until restored.
- **User Message:** "This Teacher Staff account is not active."
- **Internal Message:** `TEACHER_STAFF_INACTIVE: active-only operation targeted archived staff account.`
- **Possible Causes:** Operation submitted after archival; stale roster UI.
- **Recommended Resolution:** Restore the staff account first if activity is intended (authorized restore only).
- **Related Documents:** `02_Software_Requirements.md` Part 2; `10_API_Design.md` §26; `33_Validation_Rules.md` TCH-08.

### TCH-06 — Teacher Staff Permission Assignment Invalid
- **Error Code:** `TEACHER_STAFF_PERMISSION_INVALID`
- **HTTP Status:** 422
- **Error Name:** Staff Permission Assignment Invalid
- **Description:** A permission-assignment payload contains permissions outside the confirmed catalog, outside the Teacher Workspace role scope, or assumes granularity that is PENDING (Q-011).
- **User Message:** "The selected permissions are invalid."
- **Internal Message:** `TEACHER_STAFF_PERMISSION_INVALID: assignment contained non-catalog/out-of-scope/unconfirmed-granularity permissions.`
- **Possible Causes:** Outdated permission list; crafted payload; assumption of finer granularity.
- **Recommended Resolution:** Assign only permissions offered by the current confirmed catalog; permission changes are audited.
- **Related Documents:** `08_RBAC.md`; `09_Permission_Matrix.md`; `00_Project_Context.md` §15.1 (Q-011); `33_Validation_Rules.md` TCH-07.

### TCH-07 — Workspace Settings Out Of Scope
- **Error Code:** `TEACHER_SETTINGS_OUT_OF_SCOPE`
- **HTTP Status:** 422
- **Error Name:** Settings Update Out Of Scope
- **Description:** A Teacher Workspace Settings update targets Platform-level settings, another Teacher Workspace, non-editable fields, or the Teaching Subject (subject rejection specifically uses TCH-02).
- **User Message:** "The {Field Name} is not editable here."
- **Internal Message:** `TEACHER_SETTINGS_OUT_OF_SCOPE: settings payload exceeded current-workspace editable set.`
- **Possible Causes:** Over-posted form; attempted cross-scope settings write.
- **Recommended Resolution:** Submit only the fields the Settings form offers (profile, center information, phone numbers, address).
- **Related Documents:** `02_Software_Requirements.md` Part 2 (Settings validations); `10_API_Design.md` §27; `33_Validation_Rules.md` TCH-05.

### TCH-08 — Group Pricing Type Invalid
- **Error Code:** `GROUP_PRICING_TYPE_INVALID`
- **HTTP Status:** 422
- **Error Name:** Pricing Type Invalid
- **Description:** A Group Pricing Type other than Monthly or Per Lesson, at creation or update.
- **User Message:** "The Pricing Type must be Monthly or Per Lesson."
- **Internal Message:** `GROUP_PRICING_TYPE_INVALID: Pricing Type outside confirmed enum.`
- **Possible Causes:** Crafted value; outdated client.
- **Recommended Resolution:** Select Monthly or Per Lesson.
- **Related Documents:** `07_Data_Dictionary.md` §10; `10_API_Design.md` §18; `32_Business_Rules.md` §10 (BR-009); `33_Validation_Rules.md` GRP-04.

### TCH-09 — Group Price Invalid
- **Error Code:** `GROUP_PRICE_INVALID`
- **HTTP Status:** 422
- **Error Name:** Group Price Invalid
- **Description:** Missing or invalid Group Price — Price is required and must be a valid non-negative monetary value (currency/precision deferred; nothing stricter confirmed).
- **User Message:** "The Price must be a valid monetary amount of zero or more."
- **Internal Message:** `GROUP_PRICE_INVALID: missing/invalid monetary Price.`
- **Possible Causes:** Empty price; negative or non-numeric value.
- **Recommended Resolution:** Enter a valid non-negative amount.
- **Related Documents:** `07_Data_Dictionary.md` §10; `02_Software_Requirements.md` Part 2; `33_Validation_Rules.md` GRP-03.

### TCH-10 — Group Educational Grade Inactive
- **Error Code:** `GROUP_GRADE_INACTIVE`
- **HTTP Status:** 409
- **Error Name:** Educational Grade Not Available For Group
- **Description:** A Group is created/moved under an archived Educational Grade; archived Grades cannot receive active Group assignment until restored.
- **User Message:** "The selected Educational Grade is not available."
- **Internal Message:** `GROUP_GRADE_INACTIVE: Group referenced archived Educational Grade for active use.`
- **Possible Causes:** Grade archived after the form loaded; stale selection.
- **Recommended Resolution:** Restore the Educational Grade or choose an active one; refresh the selection list.
- **Related Documents:** `02_Software_Requirements.md` Part 2; `07_Data_Dictionary.md` §9–§10; `33_Validation_Rules.md` GRP-02, GRD-04.

### TCH-11 — Group Assignment On Archived Group
- **Error Code:** `GROUP_ASSIGNMENT_ON_ARCHIVED`
- **HTTP Status:** 409
- **Error Name:** Archived Group Cannot Receive Assignments
- **Description:** A Student assignment or Group move targets an archived Group; archived Groups cannot receive new active assignments.
- **User Message:** "The selected Group is not available."
- **Internal Message:** `GROUP_ASSIGNMENT_ON_ARCHIVED: assignment targeted archived Group.`
- **Possible Causes:** Group archived after selection; stale UI.
- **Recommended Resolution:** Restore the Group or select an active one.
- **Related Documents:** `07_Data_Dictionary.md` §10; `10_API_Design.md` §18; `33_Validation_Rules.md` GRP-07, STU-05.

### TCH-12 — Grade Assignment On Archived Grade
- **Error Code:** `GRADE_ASSIGNMENT_ON_ARCHIVED`
- **HTTP Status:** 409
- **Error Name:** Archived Educational Grade Not Assignable
- **Description:** An archived Educational Grade is offered as an active assignment target in any workspace operation beyond Group creation (selector-level rule). Archived Grades stay historical and restore-only.
- **User Message:** "The selected Educational Grade is not available."
- **Internal Message:** `GRADE_ASSIGNMENT_ON_ARCHIVED: archived Grade used as active target.`
- **Possible Causes:** Stale cached selector data.
- **Recommended Resolution:** Restore the Educational Grade first (authorized users only).
- **Related Documents:** `10_API_Design.md` §17; `00_Project_Context.md` §11; `33_Validation_Rules.md` GRD-04/05.

**Cross-referenced codes:** `AUTHZ_STAFF_PERMISSION_MISSING` (§6), `BUSINESS_ARCHIVE_STATE_CONFLICT` generic lifecycle conflicts (§19), report-filter errors (§18), workspace settings 401/403 (AUTH/AUTHZ families).

---

# 9. Student Module Errors

**Authoritative sources:** `02_Software_Requirements.md` Part 2 (Students validations) and Part 3 (Student module validations); `10_API_Design.md` §13, §15, §27; `28_Coding_Standards.md` §15.3; `07_Data_Dictionary.md` §6, §12; `33_Validation_Rules.md` §4, §6; `32_Business_Rules.md` §4, §6.

### STU-01 — Duplicate Student Account
- **Error Code:** `STUDENT_DUPLICATE_ACCOUNT`
- **HTTP Status:** 409
- **Error Name:** Student Account Already Exists
- **Description:** A registration, Teacher-created creation, or activation would duplicate an existing global Student account. One global account per Student identity is a platform rule (BR-001/BR-022); documented status: **409** (`28_Coding_Standards.md` §15.3 — reject creation; support assignment of the existing Student). The response never reveals where the account studies.
- **User Message:** "An account with this identity already exists."
- **Internal Message:** `STUDENT_DUPLICATE_ACCOUNT: global Student identity already exists (duplicate source path logged; no Teacher context returned).`
- **Possible Causes:** Prior self-registration; prior Teacher-created account; activation of an already-existing identity.
- **Recommended Resolution:** Students: log in or use the reset/activation flow. Teachers: use the assign-existing-Student flow.
- **Related Documents:** `28_Coding_Standards.md` §15.3; `10_API_Design.md` §13, §15; `32_Business_Rules.md` §4, §6; `33_Validation_Rules.md` AUT-12, STU-03, INT-03.

### STU-02 — Registration Data Invalid
- **Error Code:** `STUDENT_REGISTRATION_DATA_INVALID`
- **HTTP Status:** 422
- **Error Name:** Registration Data Invalid
- **Description:** Required identity/account fields are missing or invalid at self-registration or Teacher-created creation; inputs must be sufficient for duplicate prevention.
- **User Message:** "The {Field Name} field is required." / "The {Field Name} format is invalid."
- **Internal Message:** `STUDENT_REGISTRATION_DATA_INVALID: registration field validation failed (fields logged).`
- **Possible Causes:** Missing identity fields; malformed values.
- **Recommended Resolution:** Complete the required fields correctly.
- **Related Documents:** `02_Software_Requirements.md` Part 2/Part 3; `10_API_Design.md` §13, §15; `33_Validation_Rules.md` AUT-11, STU-03.

### STU-03 — Activation Data Mismatch
- **Error Code:** `STUDENT_ACTIVATION_MISMATCH`
- **HTTP Status:** 404
- **Error Name:** Activation Data Does Not Match
- **Description:** Activation data does not match any Teacher-created account awaiting activation. The activation flow is the documented authentication exception; its failure must not enumerate pending accounts.
- **User Message:** "The activation details do not match any account awaiting activation."
- **Internal Message:** `STUDENT_ACTIVATION_MISMATCH: activation payload matched no pending-activation Teacher-created account.`
- **Possible Causes:** Typo in activation details; account already activated (see STU-04); details from a different Teacher-created account.
- **Recommended Resolution:** Re-check the activation details with the Teacher; if already activated, log in normally.
- **Related Documents:** `10_API_Design.md` §13 (activate: 404); `33_Validation_Rules.md` AUT-13, EXC-02.

### STU-04 — Account Already Active
- **Error Code:** `STUDENT_ACCOUNT_ALREADY_ACTIVE`
- **HTTP Status:** 409
- **Error Name:** Account Already Activated
- **Description:** An activation attempt targets a Teacher-created account that is already active; activation is a one-way Pending Activation → Active transition.
- **User Message:** "This account is already active. You can log in."
- **Internal Message:** `STUDENT_ACCOUNT_ALREADY_ACTIVE: activation attempted on already-active account.`
- **Possible Causes:** Repeated activation; concurrent activation completion.
- **Recommended Resolution:** Log in with the account credentials (reset if needed).
- **Related Documents:** `10_API_Design.md` §13 (activate: 409); `07_Data_Dictionary.md` §6 (Activation Status); `33_Validation_Rules.md` STU-02.

### STU-05 — One Active Group Per Teacher Exceeded
- **Error Code:** `STUDENT_GROUP_LIMIT_EXCEEDED`
- **HTTP Status:** 409
- **Error Name:** Student Already Has Active Group For Teacher
- **Description:** An assignment would place a Student in more than one active Group for the same Teacher at the same time (BR-002). Documented status: **409** — reject assignment; require the movement workflow (`28_Coding_Standards.md` §15.3).
- **User Message:** "The Student already belongs to an active Group for this Teacher."
- **Internal Message:** `STUDENT_GROUP_LIMIT_EXCEEDED: second active Enrollment in same Teacher Workspace attempted.`
- **Possible Causes:** Assignment without movement; concurrent assignments; stale roster.
- **Recommended Resolution:** Use the move-group operation (closes one Enrollment and opens another, preserving history).
- **Related Documents:** `00_Project_Context.md` §9 (BR-002); `28_Coding_Standards.md` §15.3; `10_API_Design.md` §15; `33_Validation_Rules.md` STU-06, GRP-06, INT-05.

### STU-06 — Enrollment Date Invalid
- **Error Code:** `STUDENT_ENROLLMENT_DATE_INVALID`
- **HTTP Status:** 422
- **Error Name:** Enrollment Date Invalid
- **Description:** Enrollment Start missing/invalid, or Enrollment End earlier than Start; Enrollment periods drive Billable Student calculation and must be chronologically sound.
- **User Message:** "The Enrollment Start field is required." / "The Enrollment End must be on or after the Enrollment Start."
- **Internal Message:** `STUDENT_ENROLLMENT_DATE_INVALID: enrollment date rule violated (field logged).`
- **Possible Causes:** Missing start; reversed dates; malformed date.
- **Recommended Resolution:** Correct the Enrollment dates.
- **Related Documents:** `07_Data_Dictionary.md` §12; `33_Validation_Rules.md` STU-07, GEN-06; `17_Subscription_Billing.md`.

### STU-07 — Assignment Reference Invalid
- **Error Code:** `STUDENT_ASSIGNMENT_REFERENCE_INVALID`
- **HTTP Status:** 404
- **Error Name:** Assignment Reference Not Available
- **Description:** An assign-existing-Student or Group-target reference cannot resolve inside the authorized context. References that would expose another Teacher's private data fail as if nonexistent — citation of the target never reveals its real existence.
- **User Message:** "The selected Student is invalid." / "The selected Group is invalid."
- **Internal Message:** `STUDENT_ASSIGNMENT_REFERENCE_INVALID: assignment reference unresolved in-scope (never disclosing external existence).`
- **Possible Causes:** Nonexistent identity; identity or Group outside the authorized context; archived Group (state cases use TCH-11 409).
- **Recommended Resolution:** Verify the identity with the Student; refresh available Groups.
- **Related Documents:** `10_API_Design.md` §15 (assign-existing: 404); `33_Validation_Rules.md` STU-04/05, GEN-12.

### STU-08 — Student Ownership Denied
- **Error Code:** `STUDENT_OWNERSHIP_DENIED`
- **HTTP Status:** 403
- **Error Name:** Not The Student's Own Record
- **Description:** A Student attempts a write or action on records that are not their own (another Student's submissions, attempts, attendance, payment status, settings) or attempts Teacher-owned management (Group assignment, workspace settings).
- **User Message:** "You do not have permission to perform this action."
- **Internal Message:** `STUDENT_OWNERSHIP_DENIED: Student actor targeted non-own/Teacher-owned resource for write.`
- **Possible Causes:** Swapped identifiers; crafted requests; misunderstanding of role boundary.
- **Recommended Resolution:** Operate only within own account surfaces; Teacher relationships are managed by the Teacher.
- **Related Documents:** `02_Software_Requirements.md` Part 3 (Settings validations); `09_Permission_Matrix.md`; `33_Validation_Rules.md` STU-09, PAY-07.

**Cross-referenced codes:** Scan/submission/attempt failures live in their subsystems (§11 `ATTENDANCE_*`, §12 `HOMEWORK_NOT_AVAILABLE`, §13 `EXAM_*`); Lesson access denial is §15-cross `LSN` surfaced via `RESOURCE_NOT_FOUND`/`AUTHZ` (§6/§19) per `33_Validation_Rules.md` LSN-05/06.

---

# 10. Parent Module Errors

**Authoritative sources:** `02_Software_Requirements.md` Part 4 (Parent module validations); `10_API_Design.md` §16, §27; `28_Coding_Standards.md` §15.3; `07_Data_Dictionary.md` §7–§8; `20_File_Storage.md` §10–§14; `33_Validation_Rules.md` §7; `32_Business_Rules.md` §7.

### PAR-01 — Parent Write Denied (Cross-Reference)
- **Error Code:** `PARENT_WRITE_DENIED` (canonical definition in §6, AUTHZ-07)
- **HTTP Status:** 403
- **Error Name:** Parent Modification Denied
- **Description:** Any Parent attempt to modify Attendance, grades, Homework, Exams, Lessons, Student records, Teacher records, payment status, Group assignments, or Educational Grades. Read-only is absolute (BR-004).
- **User Message:** "You do not have permission to perform this action."
- **Internal Message:** See §6 AUTHZ-07.
- **Possible Causes:** Any Parent-originated write.
- **Recommended Resolution:** By design irreversible; Parent views remain read-only.
- **Related Documents:** §6 AUTHZ-07; `33_Validation_Rules.md` PAR-02.

### PAR-02 — Parent Upload Denied
- **Error Code:** `PARENT_UPLOAD_DENIED`
- **HTTP Status:** 403
- **Error Name:** Parent File Upload Denied
- **Description:** A Parent attempts any file upload in any context. The denial happens before file inspection — no MIME, size, or context check ever succeeds for a Parent upload.
- **User Message:** "File upload is not available for this account."
- **Internal Message:** `PARENT_UPLOAD_DENIED: Parent actor attempted upload; rejected pre-inspection.`
- **Possible Causes:** Forged or mistaken upload call.
- **Recommended Resolution:** None — by design; uploads are Student/Teacher-side only.
- **Related Documents:** `20_File_Storage.md` §3, §10–§14; `23_Security_Standards.md` §9.1; `33_Validation_Rules.md` PAR-05, FIL stack.

### PAR-03 — One Parent Per Student Exceeded
- **Error Code:** `PARENT_LINK_LIMIT_EXCEEDED`
- **HTTP Status:** 409
- **Error Name:** Student Already Linked To A Parent
- **Description:** A link operation would attach a second Parent account to a Student; Version 1 allows exactly one Parent account per Student (BR-020), while one Parent may monitor many Students.
- **User Message:** "This Student is already linked to a Parent account."
- **Internal Message:** `PARENT_LINK_LIMIT_EXCEEDED: second Parent link for same Student attempted.`
- **Possible Causes:** Existing link forgotten; concurrent link operations.
- **Recommended Resolution:** Use the existing Parent account; link changes follow the documented account-management path.
- **Related Documents:** `00_Project_Context.md` §9 (BR-020); `07_Data_Dictionary.md` §8; `02_Software_Requirements.md` Part 4; `33_Validation_Rules.md` PAR-01, INT-04.

### PAR-04 — Unlinked Student Selected
- **Error Code:** `PARENT_UNLINKED_STUDENT`
- **HTTP Status:** 404
- **Error Name:** Student Not Linked To Parent
- **Description:** A Parent addresses a Student not linked to the account (Switcher selection, any linked-Student path). The failure is indistinguishable from a nonexistent Student.
- **User Message:** "The selected Student is invalid."
- **Internal Message:** `PARENT_UNLINKED_STUDENT: unlinked Student reference attempted (no existence disclosure).`
- **Possible Causes:** Stale Switcher state after unlink; crafted identifier.
- **Recommended Resolution:** Refresh the linked-Student list; the Switcher offers exactly the current links.
- **Related Documents:** `10_API_Design.md` §16 (404 on linked-student paths); `33_Validation_Rules.md` PAR-03; `14_UI_Components.md` (Switcher contract).

### PAR-05 — Parent Account Data Invalid
- **Error Code:** `PARENT_ACCOUNT_DATA_INVALID`
- **HTTP Status:** 422
- **Error Name:** Parent Account Data Invalid
- **Description:** A Parent own-account update fails field validation or targets linked Student/Teacher Workspace data (which must never be modifiable through Parent settings).
- **User Message:** "The {Field Name} field is required." / "The {Field Name} is not editable here."
- **Internal Message:** `PARENT_ACCOUNT_DATA_INVALID: parent settings payload invalid or out-of-own-account scope.`
- **Possible Causes:** Invalid own-account fields; payload containing Student/Teacher data keys.
- **Recommended Resolution:** Update only own-account fields offered in Parent Settings.
- **Related Documents:** `02_Software_Requirements.md` Part 4 (Settings validations); `10_API_Design.md` §16, §27; `33_Validation_Rules.md` PAR-04.

**Cross-referenced codes:** All linked-Student read-path visibility failures collapse to `PARENT_UNLINKED_STUDENT`/`RESOURCE_NOT_FOUND`; read-only enforcement on uploads is PAR-02.

---

# 11. Attendance Errors

**Authoritative sources:** `16_QR_Attendance_System.md` §6, §14, §22 (Invalid QR / Error Handling); `02_Software_Requirements.md` Part 2 (Attendance validations); `10_API_Design.md` §19; `07_Data_Dictionary.md` §13–§15; `05_User_Flows.md` (Attendance error flows); `33_Validation_Rules.md` §10.

### ATT-01 — Attendance Method Unsupported
- **Error Code:** `ATTENDANCE_METHOD_UNSUPPORTED`
- **HTTP Status:** 422
- **Error Name:** Attendance Method Not Supported
- **Description:** An Attendance method context/value other than Dynamic QR Code, ID Card, or Manual (BR-010) — e.g., barcode scanning, or offline/local entries posing as a method.
- **User Message:** "The selected Attendance method is invalid."
- **Internal Message:** `ATTENDANCE_METHOD_UNSUPPORTED: method outside confirmed three-method set.`
- **Possible Causes:** Crafted method value; attempt to use unconfirmed scanning modes.
- **Recommended Resolution:** Use one of the three confirmed methods; when scanning capability is unavailable, the authorized Manual method applies.
- **Related Documents:** `07_Data_Dictionary.md` §13, §15; `16_QR_Attendance_System.md` §1; `33_Validation_Rules.md` ATT-02, EXC-10.

### ATT-02 — Dynamic QR Invalid Or Expired
- **Error Code:** `ATTENDANCE_QR_INVALID_OR_EXPIRED`
- **HTTP Status:** 422
- **Error Name:** Dynamic QR Code Not Valid
- **Description:** A scanned Dynamic QR Code is invalid, expired, incorrect for the day/context, or belongs to an inactive/archived Attendance context. The scan is rejected; the response makes clear that Attendance was **not** recorded and reveals no QR internals, other Students, or workspace details (exact expiry mechanics are unconfirmed and remain unhardened).
- **User Message:** "This Dynamic QR Code is not valid for today's Attendance."
- **Internal Message:** `ATTENDANCE_QR_INVALID_OR_EXPIRED: QR context failed daily/scope/active checks (no token internals logged).`
- **Possible Causes:** Scan of a prior/future day's code; screen showing stale code; inactive context; tampered payload.
- **Recommended Resolution:** Scan the code currently displayed for today's session; Teacher/Teacher Staff may use the confirmed Manual method when appropriate.
- **Related Documents:** `16_QR_Attendance_System.md` §6, §14, §22; `05_User_Flows.md` (invalid/expired/incorrect QR rejected); `33_Validation_Rules.md` ATT-03.

### ATT-03 — Attendance Eligibility Denied
- **Error Code:** `ATTENDANCE_ELIGIBILITY_DENIED`
- **HTTP Status:** 403
- **Error Name:** Not Eligible For This Attendance
- **Description:** A Student scans without a valid relationship (Enrollment) with the relevant Teacher Workspace, or attempts to record for another Student; a Student attempts ID Card self-service or manual entry (Teacher-side methods). The QR visual alone never proves eligibility.
- **User Message:** "You are not enrolled for this Attendance."
- **Internal Message:** `ATTENDANCE_ELIGIBILITY_DENIED: scan/manual attempt without valid Student–Teacher relationship or with disallowed method authority.`
- **Possible Causes:** Enrollment ended; wrong Teacher's session; impersonated scan; role confusion.
- **Recommended Resolution:** Verify enrollment with the Teacher; each Student records only own Attendance by the confirmed method.
- **Related Documents:** `16_QR_Attendance_System.md` (scan flow, role boundaries); `10_API_Design.md` §19; `33_Validation_Rules.md` ATT-04, ATT-06/07.

### ATT-04 — Duplicate Attendance Record
- **Error Code:** `ATTENDANCE_DUPLICATE_RECORD`
- **HTTP Status:** 409
- **Error Name:** Attendance Already Recorded
- **Description:** A scan or entry that would create an inconsistent duplicate Attendance for the same Student in the same Attendance context (double scan, or manual/ID-card entry conflicting with an existing record). The backend resolves safely and the user gets an accurate outcome — never a false success. (Exact dedupe mechanics are unconfirmed and remain internal.)
- **User Message:** "Attendance is already recorded for this Student in this session."
- **Internal Message:** `ATTENDANCE_DUPLICATE_RECORD: conflicting second record for same Student+context; resolved per duplicate safeguards.`
- **Possible Causes:** Repeat scan; manual entry duplicating a QR record; retried request.
- **Recommended Resolution:** No action — the record stands; corrections follow the audited manual-correction path (ATT-08).
- **Related Documents:** `16_QR_Attendance_System.md` (duplicate/inconsistent-record safeguards); `10_API_Design.md` §19 (409 on scan endpoints); `33_Validation_Rules.md` ATT-05.

### ATT-05 — ID Card Invalid
- **Error Code:** `ATTENDANCE_ID_CARD_INVALID`
- **HTTP Status:** 422
- **Error Name:** ID Card Not Valid For Session
- **Description:** A printed ID Card scan cannot resolve a valid Student/Teacher Workspace relationship in the session context, or resolves an inactive context.
- **User Message:** "This ID Card is not valid for this Attendance session."
- **Internal Message:** `ATTENDANCE_ID_CARD_INVALID: ID Card QR unresolved to valid workspace Student relationship/session.`
- **Possible Causes:** Card from another Teacher context; damaged/outdated card; wrong session.
- **Recommended Resolution:** Verify the card belongs to the enrolled Student; fall back to Manual Attendance by authorized staff.
- **Related Documents:** `16_QR_Attendance_System.md` §22; `10_API_Design.md` §19; `33_Validation_Rules.md` ATT-06.

### ATT-06 — Attendance Status Invalid
- **Error Code:** `ATTENDANCE_STATUS_INVALID`
- **HTTP Status:** 422
- **Error Name:** Attendance Status Invalid
- **Description:** A status value outside the confirmed Attendance status model (no invented statuses; the data dictionary's documented default is Present, plus formally defined statuses).
- **User Message:** "The selected Attendance status is invalid."
- **Internal Message:** `ATTENDANCE_STATUS_INVALID: status outside confirmed set.`
- **Possible Causes:** Crafted value; outdated client.
- **Recommended Resolution:** Choose from the offered statuses.
- **Related Documents:** `07_Data_Dictionary.md` §15; `16_QR_Attendance_System.md` §22 (invalid status input rejected); `33_Validation_Rules.md` ATT-08.

### ATT-07 — Attendance Session Inactive
- **Error Code:** `ATTENDANCE_SESSION_INACTIVE`
- **HTTP Status:** 409
- **Error Name:** Attendance Context Not Active
- **Description:** An active Attendance action targets an archived/inactive Session or context; archived contexts remain historical only.
- **User Message:** "This Attendance session is not active."
- **Internal Message:** `ATTENDANCE_SESSION_INACTIVE: active Attendance action on archived/inactive context.`
- **Possible Causes:** Session archived after page load; stale display.
- **Recommended Resolution:** Use the current active session; historical views remain available where permitted.
- **Related Documents:** `16_QR_Attendance_System.md` §22; `00_Project_Context.md` §11; `33_Validation_Rules.md` ATT-01, GEN-11.

### ATT-08 — Attendance Correction Invalid
- **Error Code:** `ATTENDANCE_CORRECTION_INVALID`
- **HTTP Status:** 422
- **Error Name:** Attendance Correction Invalid
- **Description:** A correction payload lacks the corrected status or the required reason, or targets a record outside the current Teacher Workspace; corrections are authorized and audited (Attendance Change event).
- **User Message:** "The reason field is required." / "The selected Attendance status is invalid."
- **Internal Message:** `ATTENDANCE_CORRECTION_INVALID: correction missing status/reason or out-of-workspace target.`
- **Possible Causes:** Empty reason; wrong record reference; crafted edit.
- **Recommended Resolution:** Supply a valid status and reason through the correction action.
- **Related Documents:** `10_API_Design.md` §19 (PATCH attendance); `02_Software_Requirements.md` Part 2; `33_Validation_Rules.md` ATT-09.

**Cross-referenced codes:** Filters over Attendance (reports/lists) follow §18; Attendance data can never feed billing (rule, not error — `32_Business_Rules.md` §11; `17_Subscription_Billing.md` §18 rejects such calculations via `SUBSCRIPTION_CALCULATION_BASIS_INVALID`, §15 here).

---

# 12. Homework Errors

**Authoritative sources:** `02_Software_Requirements.md` Part 2/Part 3 (Homework validations); `10_API_Design.md` §20, §28; `07_Data_Dictionary.md` §16–§17; `20_File_Storage.md` §3, §7; `33_Validation_Rules.md` §11; `32_Business_Rules.md` §12.

### HW-01 — Homework Format Unsupported
- **Error Code:** `HOMEWORK_FORMAT_UNSUPPORTED`
- **HTTP Status:** 422
- **Error Name:** Homework Format Unsupported
- **Description:** A Homework Supported Format outside Text, Image, PDF (BR-021) at creation or update — including any video format value.
- **User Message:** "The Homework format must be Text, Image, or PDF. Video Homework is not supported."
- **Internal Message:** `HOMEWORK_FORMAT_UNSUPPORTED: format outside Text/Image/PDF.`
- **Possible Causes:** Crafted format value; attempt to assign video-based Homework.
- **Recommended Resolution:** Choose Text, Image, or PDF; Lesson video is the only video context and is unrelated to Homework.
- **Related Documents:** `00_Project_Context.md` §9 (BR-021); `10_API_Design.md` §20; `20_File_Storage.md` §3; `33_Validation_Rules.md` HW-03.

### HW-02 — Video Homework Rejected
- **Error Code:** `HOMEWORK_VIDEO_REJECTED`
- **HTTP Status:** 422
- **Error Name:** Video Homework Rejected
- **Description:** A video file offered as Homework content — as an assignment attachment, a submission, or any Homework representation. Rejected at validation in every form (context-specific twin of the file-type rule; §17 cross-references this canonical code).
- **User Message:** "Video Homework is not supported. Homework supports Text, Image, and PDF only."
- **Internal Message:** `HOMEWORK_VIDEO_REJECTED: video binary/format presented in Homework context.`
- **Possible Causes:** Video attachment on Homework; video submission file.
- **Recommended Resolution:** Provide Image or PDF (or Text where applicable).
- **Related Documents:** `20_File_Storage.md` §3, §7, §21; `10_API_Design.md` §20, §28; `33_Validation_Rules.md` FIL-09, HW-03.

### HW-03 — Homework Not Available
- **Error Code:** `HOMEWORK_NOT_AVAILABLE`
- **HTTP Status:** 404
- **Error Name:** Homework Not Available
- **Description:** A reference to Homework that does not exist, belongs to another Teacher relationship than the actor's, or is invisible to the actor — submission, view, or grading target alike.
- **User Message:** "This Homework is not available for submission." (submission path) / "Not found." (other paths)
- **Internal Message:** `HOMEWORK_NOT_AVAILABLE: homework reference unresolved in actor scope.`
- **Possible Causes:** Not assigned to this Student; other Teacher's Homework; removed from active use without proper context (archived cases: HW-05).
- **Recommended Resolution:** Refresh the assigned-Homework list; contact the Teacher if an assignment is expected.
- **Related Documents:** `10_API_Design.md` §20; `02_Software_Requirements.md` Part 3; `33_Validation_Rules.md` HW-05, GEN-09/12.

### HW-04 — Submission Format Unsupported
- **Error Code:** `HOMEWORK_SUBMISSION_FORMAT_UNSUPPORTED`
- **HTTP Status:** 422
- **Error Name:** Submission Format Unsupported
- **Description:** A submission whose format is not Text/Image/PDF, or whose binary file is not Image or PDF.
- **User Message:** "The submission must be Text, Image, or PDF. Video submissions are not supported."
- **Internal Message:** `HOMEWORK_SUBMISSION_FORMAT_UNSUPPORTED: submission format/binary outside allowed set.`
- **Possible Causes:** Wrong file kind; crafted submission payload.
- **Recommended Resolution:** Submit a Text answer or an Image/PDF file.
- **Related Documents:** `07_Data_Dictionary.md` §17; `10_API_Design.md` §20, §28; `20_File_Storage.md` §7; `33_Validation_Rules.md` HW-06.

### HW-05 — Archived Homework Target
- **Error Code:** `HOMEWORK_ARCHIVED_TARGET`
- **HTTP Status:** 409
- **Error Name:** Archived Homework Not Active
- **Description:** An active operation (submission, edit, grading) targets archived Homework; archived Homework remains visible only as clearly-indicated history until restored.
- **User Message:** "This Homework is no longer active."
- **Internal Message:** `HOMEWORK_ARCHIVED_TARGET: active operation on archived Homework.`
- **Possible Causes:** Homework archived after assignment list loaded; late submission attempt.
- **Recommended Resolution:** Historical view only; Teachers may restore if intended (authorized restore).
- **Related Documents:** `10_API_Design.md` §20 (409); `00_Project_Context.md` §11; `33_Validation_Rules.md` GEN-11, HW-05.

### HW-06 — Homework Grading Invalid
- **Error Code:** `HOMEWORK_GRADING_INVALID`
- **HTTP Status:** 422
- **Error Name:** Homework Grading Input Invalid
- **Description:** A grading/review payload with an invalid review status or invalid feedback payload, or targeting a submission outside the current Teacher Workspace.
- **User Message:** "The selected review status is invalid." / "The selected submission is invalid."
- **Internal Message:** `HOMEWORK_GRADING_INVALID: grading payload failed status/scope validation.`
- **Possible Causes:** Unknown status value; cross-scope target.
- **Recommended Resolution:** Use offered review states in the workspace submission review.
- **Related Documents:** `10_API_Design.md` §20 ('/submissions/{id}/grade'); `33_Validation_Rules.md` HW-08.

**Cross-referenced codes:** File-layer rejections (type/MIME/name/size-guard/ownership) are §17; Parent submission/grading attempts are `PARENT_WRITE_DENIED` (§10).

---

# 13. Exam Errors

**Authoritative sources:** `15_Exam_Engine.md` (creation/publication; §26 Error Handling); `02_Software_Requirements.md` Part 2/Part 3 (Exam validations); `10_API_Design.md` §22; `07_Data_Dictionary.md` §22–§24; `33_Validation_Rules.md` §12–§14; `32_Business_Rules.md` §13–§14.

### EXM-01 — Empty Exam Composition
- **Error Code:** `EXAM_EMPTY_COMPOSITION`
- **HTTP Status:** 422
- **Error Name:** Exam Has No Valid Questions
- **Description:** Publication/availability of an Exam with no valid active selected Questions; such an Exam must not be treated as a valid active Exam.
- **User Message:** "The Exam has no valid active Questions."
- **Internal Message:** `EXAM_EMPTY_COMPOSITION: availability requested with zero valid active Questions.`
- **Possible Causes:** All selected Questions archived; composition never populated; stale selection after Question removal.
- **Recommended Resolution:** Add valid active Questions from the owning Question Bank before publishing.
- **Related Documents:** `15_Exam_Engine.md` (creation/publication rule); `33_Validation_Rules.md` EXM-04.

### EXM-02 — Exam Unavailable
- **Error Code:** `EXAM_UNAVAILABLE`
- **HTTP Status:** 409
- **Error Name:** Exam Not Available For Attempt
- **Description:** An attempt is started on an Exam not currently available to the Student (unpublished/inactive for the Student's context). Exams invisible to the Student return 404 via `RESOURCE_NOT_FOUND` instead of this code.
- **User Message:** "This Exam is not available."
- **Internal Message:** `EXAM_UNAVAILABLE: attempt start blocked by availability state.`
- **Possible Causes:** Availability window/state not active; Teacher withdrew availability.
- **Recommended Resolution:** Wait for the Teacher to make the Exam available; refresh the Exams list.
- **Related Documents:** `10_API_Design.md` §22 (attempt start 409/404); `15_Exam_Engine.md`; `33_Validation_Rules.md` EXM-06.

### EXM-03 — Archived Exam Active Use
- **Error Code:** `EXAM_ARCHIVED_ACTIVE_USE`
- **HTTP Status:** 409
- **Error Name:** Archived Exam Not Active
- **Description:** Active use of an archived Exam (new attempt, active editing as current Exam); historical attempts/grades remain available where permitted.
- **User Message:** "This Exam is no longer active."
- **Internal Message:** `EXAM_ARCHIVED_ACTIVE_USE: active operation on archived Exam.`
- **Possible Causes:** Stale list; attempt started before archival, submitted after.
- **Recommended Resolution:** Use history views; Teachers may restore where authorized.
- **Related Documents:** `15_Exam_Engine.md` §26 (archived used as active rejected); `10_API_Design.md` §22; `33_Validation_Rules.md` GEN-11.

### EXM-04 — Answer Type Mismatch
- **Error Code:** `EXAM_ANSWER_TYPE_MISMATCH`
- **HTTP Status:** 422
- **Error Name:** Answer Does Not Match Question Type
- **Description:** A submitted answer's shape does not match the Question Type (choice selection, true/false selection, essay text, on-screen bubble selection).
- **User Message:** "The answer format does not match the question type."
- **Internal Message:** `EXAM_ANSWER_TYPE_MISMATCH: answer shape/value mismatched to Question Type.`
- **Possible Causes:** Client submitted stale/wrong payload; crafted answers.
- **Recommended Resolution:** Answer each Question using its on-screen input; resubmit.
- **Related Documents:** `15_Exam_Engine.md` §26 (invalid answer rejected); `07_Data_Dictionary.md` §24; `10_API_Design.md` §22; `33_Validation_Rules.md` EXM-07.

### EXM-05 — Bubble Sheet Selection Invalid
- **Error Code:** `EXAM_BUBBLE_SELECTION_INVALID`
- **HTTP Status:** 422
- **Error Name:** Bubble Sheet Selection Invalid
- **Description:** A Bubble Sheet answer that is not a valid on-screen selection for the applicable sheet structure (including any paper/scan/OMR-mode input, which Version 1 does not support).
- **User Message:** "The selected answer is not valid for this Bubble Sheet."
- **Internal Message:** `EXAM_BUBBLE_SELECTION_INVALID: selection outside applicable Bubble Sheet structure.`
- **Possible Causes:** Out-of-range bubble index; crafted payload; unsupported input mode.
- **Recommended Resolution:** Select bubbles on screen within the sheet shown.
- **Related Documents:** `15_Exam_Engine.md` (Bubble Sheet; §26); `33_Validation_Rules.md` BSH-01/02.

### EXM-06 — Exam Attempt Not Owned
- **Error Code:** `EXAM_ATTEMPT_NOT_OWNED`
- **HTTP Status:** 404
- **Error Name:** Exam Attempt Not Available
- **Description:** A Student addresses another Student's attempt, or any actor addresses an attempt outside their authorized scope (submission, result view); Parent reads remain read-only through linked Students only.
- **User Message:** "The selected Exam attempt is invalid." / "Not found."
- **Internal Message:** `EXAM_ATTEMPT_NOT_OWNED: attempt reference outside authorized scope (no disclosure).`
- **Possible Causes:** Swapped attempt identifiers; forged request.
- **Recommended Resolution:** Use only own attempt links from the Exams surface; Parents use linked-Student views.
- **Related Documents:** `10_API_Design.md` §22 (404 on attempt/result); `07_Data_Dictionary.md` §23; `33_Validation_Rules.md` EXM-08, PAR-02.

### EXM-07 — Exam Grading Invalid
- **Error Code:** `EXAM_GRADING_INVALID`
- **HTTP Status:** 422
- **Error Name:** Exam Grading Input Invalid
- **Description:** Grading input is incomplete or invalid for the grading contract, or targets an attempt outside the current Teacher Workspace. Essay answers remain pending until graded; a pending state must never be presented as a final grade.
- **User Message:** "The grade value is invalid." / "The selected Exam attempt is invalid."
- **Internal Message:** `EXAM_GRADING_INVALID: grading payload failed validity/scope checks.`
- **Possible Causes:** Missing/invalid grade values; cross-scope target.
- **Recommended Resolution:** Complete grading fields correctly through the attempt review.
- **Related Documents:** `15_Exam_Engine.md` §26 (incomplete grading input); `10_API_Design.md` §22 ('/exam-attempts/{id}/grade'); `14_UI_Components.md` (pending ≠ final); `33_Validation_Rules.md` EXM-09.

### EXM-08 — Exam State Transition Conflict
- **Error Code:** `EXAM_STATE_TRANSITION_CONFLICT`
- **HTTP Status:** 409
- **Error Name:** Exam State Transition Conflict
- **Description:** A status transition that the confirmed lifecycle does not allow from the current state (e.g., publishing what is not publishable, acting on an already-archived Exam) within the documented availability workflow.
- **User Message:** "The Exam cannot be moved to the requested state."
- **Internal Message:** `EXAM_STATE_TRANSITION_CONFLICT: lifecycle transition rejected from current state.`
- **Possible Causes:** Repeated publish action; transition on archived Exam; concurrent state changes.
- **Recommended Resolution:** Refresh the Exam state; use the action offered for its current state.
- **Related Documents:** `07_Data_Dictionary.md` §22 (status model); `10_API_Design.md` §22 (publish/archive/restore 409); `33_Validation_Rules.md` EXM-05.

**Cross-referenced codes:** Question/Question Bank rejections are §14; access-scope invisibility collapses to `RESOURCE_NOT_FOUND` (§19); Parent attempt-taking is `PARENT_WRITE_DENIED` (§10); auto-grading job failures are §21 (`QUEUE_*`).

---

# 14. Question Bank Errors

**Authoritative sources:** `07_Data_Dictionary.md` §20–§21; `15_Exam_Engine.md` (§26 Error Handling); `10_API_Design.md` §22; `02_Software_Requirements.md` Part 2 (Exams & Question Bank validations); `33_Validation_Rules.md` §13; `32_Business_Rules.md` §13.

### QBK-01 — Question Type Unsupported
- **Error Code:** `QUESTION_BANK_TYPE_UNSUPPORTED`
- **HTTP Status:** 422
- **Error Name:** Question Type Unsupported
- **Description:** A Question whose type is not Multiple Choice, True/False, Essay, or Bubble Sheet (BR-011); unsupported question input is rejected.
- **User Message:** "The Question Type must be Multiple Choice, True/False, Essay, or Bubble Sheet."
- **Internal Message:** `QUESTION_BANK_TYPE_UNSUPPORTED: question type outside confirmed four-type set.`
- **Possible Causes:** Crafted type value; outdated client.
- **Recommended Resolution:** Choose one of the four supported types.
- **Related Documents:** `00_Project_Context.md` §9 (BR-011); `15_Exam_Engine.md` §26; `10_API_Design.md` §22; `33_Validation_Rules.md` QBK-03.

### QBK-02 — Answer Definition Required
- **Error Code:** `QUESTION_BANK_ANSWER_DEFINITION_REQUIRED`
- **HTTP Status:** 422
- **Error Name:** Answer Definition Required
- **Description:** An automatically graded Question (Multiple Choice, True/False, Bubble Sheet) without a valid answer definition matching its type.
- **User Message:** "The Answer Definition is required for automatically graded questions."
- **Internal Message:** `QUESTION_BANK_ANSWER_DEFINITION_REQUIRED: auto-graded type missing valid answer definition.`
- **Possible Causes:** Incomplete Question authoring payload; definition cleared.
- **Recommended Resolution:** Provide the correct-answer definition for the Question Type; Essay needs none.
- **Related Documents:** `07_Data_Dictionary.md` §21, §24; `15_Exam_Engine.md`; `33_Validation_Rules.md` QBK-04.

### QBK-03 — Question Bank Reference Invalid
- **Error Code:** `QUESTION_BANK_REFERENCE_INVALID`
- **HTTP Status:** 422
- **Error Name:** Question Bank Reference Invalid
- **Description:** An Exam composition or Question operation references a Question Bank/Question that is not valid in the current Teacher Workspace — missing, archived for active use, or foreign (foreign references additionally fail scope checks, never revealing their owner).
- **User Message:** "The selected Question Bank is invalid." / "One or more selected Questions are invalid."
- **Internal Message:** `QUESTION_BANK_REFERENCE_INVALID: reference failed in-workspace validity (no foreign-existence disclosure).`
- **Possible Causes:** Stale composition list; cross-workspace reference attempt; archived members (see QBK-04 for archived state conflict).
- **Recommended Resolution:** Re-select Questions from the current workspace's Question Bank.
- **Related Documents:** `10_API_Design.md` §22; `07_Data_Dictionary.md` §20–§21; `33_Validation_Rules.md` QBK-05, EXM-02/03.

### QBK-04 — Archived Question Active Use
- **Error Code:** `QUESTION_BANK_ARCHIVED_ACTIVE_USE`
- **HTTP Status:** 409
- **Error Name:** Archived Question Not Active
- **Description:** An archived Question is used as active (new composition, attempt content) before authorized restoration.
- **User Message:** "The selected Question is not available."
- **Internal Message:** `QUESTION_BANK_ARCHIVED_ACTIVE_USE: archived Question referenced for active use.`
- **Possible Causes:** Question archived after composition screen loaded.
- **Recommended Resolution:** Restore the Question (authorized) or select active Questions.
- **Related Documents:** `15_Exam_Engine.md` §26 (archived Question rejected); `10_API_Design.md` §22; `33_Validation_Rules.md` QBK-06.

**Cross-referenced codes:** Question Bank privacy (cross-Teacher visibility) collapses to `RESOURCE_NOT_FOUND`/`AUTHZ_CROSS_WORKSPACE_ACCESS` (§19/§6); Exam-level composition conflicts are §13 (`EXAM_EMPTY_COMPOSITION`).

---

# 15. Subscription Errors

**Authoritative sources:** `17_Subscription_Billing.md` §18 (Error Handling); `02_Software_Requirements.md` Part 5 (Subscription & Billing validations); `10_API_Design.md` §25, §30; `07_Data_Dictionary.md` §26, §32; `00_Project_Context.md` §9 (BR-008, BR-015), §15.1 (Q-005, Q-013); `33_Validation_Rules.md` §16–§17.

### SUB-01 — Billing Cycle Invalid
- **Error Code:** `SUBSCRIPTION_BILLING_CYCLE_INVALID`
- **HTTP Status:** 422
- **Error Name:** Billing Cycle Not A Calendar Month
- **Description:** A Billing Cycle that does not start on the first day and end on the last day of the same calendar month, or any action referencing such a cycle. The calendar-month Billing Cycle is confirmed (D-006) and must not be redefined.
- **User Message:** "The Billing Cycle must start on the first day and end on the last day of the same calendar month."
- **Internal Message:** `SUBSCRIPTION_BILLING_CYCLE_INVALID: non-calendar-month cycle rejected.`
- **Possible Causes:** Custom period input; off-by-one month boundaries; crafted cycle.
- **Recommended Resolution:** Use calendar-month cycles only.
- **Related Documents:** `07_Data_Dictionary.md` §32; `10_API_Design.md` §30 ('/platform/billing-cycles' 409/422); `17_Subscription_Billing.md` §18; `33_Validation_Rules.md` SUB-01.

### SUB-02 — Calculation Basis Invalid
- **Error Code:** `SUBSCRIPTION_CALCULATION_BASIS_INVALID`
- **HTTP Status:** 422
- **Error Name:** Invalid Billable Calculation Basis
- **Description:** A Billable Student calculation attempted with disregarded inputs — Attendance, login activity, Homework, Exam, or Lesson activity — or with Enrollment duration logic other than the >15-calendar-days rule (15 days exactly is not Billable; 16 is).
- **User Message:** "Billable Students can only be calculated from Enrollment duration."
- **Internal Message:** `SUBSCRIPTION_CALCULATION_BASIS_INVALID: calculation invoked with excluded activity inputs or wrong threshold logic.`
- **Possible Causes:** Defective calculation call; crafted parameters; misunderstanding of BR-008.
- **Recommended Resolution:** Correct the calculation path to Enrollment-duration-only; this is an implementation defect, not user error.
- **Related Documents:** `00_Project_Context.md` §9 (BR-008); `17_Subscription_Billing.md` §18 (reject Attendance/login-based calculation); `24_Testing_Strategy.md` §5.2; `33_Validation_Rules.md` SUB-02, INT-07.

### SUB-03 — Pricing Configuration Invalid
- **Error Code:** `SUBSCRIPTION_PRICING_CONFIGURATION_INVALID`
- **HTTP Status:** 422
- **Error Name:** Pricing Configuration Invalid
- **Description:** A pricing update that is not a valid Platform-level pricing value, or that hardens an unresolved model — flat price versus volume tiers is **PENDING (Q-013)**: no tier structure may be stored or required, and neither model may be rejected as input principle beyond the confirmed ownership rule.
- **User Message:** "The pricing configuration is invalid."
- **Internal Message:** `SUBSCRIPTION_PRICING_CONFIGURATION_INVALID: invalid pricing value or unconfirmed tier-model input.`
- **Possible Causes:** Non-monetary pricing value; tier payload; assumption of resolved Q-013.
- **Recommended Resolution:** Store only the confirmed Price Per Student configuration; await Q-013 resolution for any model change.
- **Related Documents:** `02_Software_Requirements.md` Part 5 (Settings validations); `10_API_Design.md` §30 ('/platform/pricing' 422); `00_Project_Context.md` §15.1 (Q-013); `33_Validation_Rules.md` SUB-03.

### SUB-04 — Enrollment Data Unavailable For Calculation
- **Error Code:** `SUBSCRIPTION_ENROLLMENT_DATA_UNAVAILABLE`
- **HTTP Status:** 409
- **Error Name:** Billing Calculation Blocked By Missing Data
- **Description:** Enrollment-duration information needed for a truthful Billable Student calculation is unavailable; `17_Subscription_Billing.md` §18 requires preventing misleading calculation output rather than emitting wrong results.
- **User Message:** "The billing calculation cannot be completed right now."
- **Internal Message:** `SUBSCRIPTION_ENROLLMENT_DATA_UNAVAILABLE: required Enrollment-duration inputs missing for Teacher/cycle; calculation withheld.`
- **Possible Causes:** Corrupted/missing Enrollment records; data correction pending.
- **Recommended Resolution:** Restore/complete the Enrollment data, then recalculate; the job-level twin is `QUEUE_DATA_INCONSISTENCY` (§21).
- **Related Documents:** `17_Subscription_Billing.md` §18; `21_Background_Jobs.md` §14.2 (data inconsistency category); `33_Validation_Rules.md` SUB-02.

### SUB-05 — Subscription Status Invalid
- **Error Code:** `SUBSCRIPTION_STATUS_INVALID`
- **HTTP Status:** 422
- **Error Name:** Subscription Status Invalid
- **Description:** A status value outside the confirmed Flow A status model (documented defaults: Pending or Unpaid), or a status transition payload missing its required reason/reference.
- **User Message:** "The selected Subscription status is invalid." / "The reason field is required."
- **Internal Message:** `SUBSCRIPTION_STATUS_INVALID: invalid status value or missing reason/reference on status change.`
- **Possible Causes:** Crafted status; incomplete status payload.
- **Recommended Resolution:** Use offered statuses with the required reason; Subscription changes are audited.
- **Related Documents:** `07_Data_Dictionary.md` §26; `10_API_Design.md` §25 ('/subscriptions/{id}/status'); `33_Validation_Rules.md` SUB-05/07.

### SUB-06 — Unconfirmed Non-Payment Enforcement
- **Error Code:** `SUBSCRIPTION_ENFORCEMENT_UNCONFIRMED`
- **HTTP Status:** 422
- **Error Name:** Non-Payment Enforcement Not Available
- **Description:** Any request or configuration applying non-payment enforcement semantics — grace periods, suspension, read-only enforcement, reactivation behavior — which is **PENDING (Q-005)**: Version 1 does not define or apply enforcement, but never hard-deletes or auto-archives data for non-payment.
- **User Message:** "This action is not available."
- **Internal Message:** `SUBSCRIPTION_ENFORCEMENT_UNCONFIRMED: enforcement-behavior request rejected pending Q-005.`
- **Possible Causes:** Assumed enforcement feature; crafted status/enforcement payload.
- **Recommended Resolution:** No enforcement exists in V1; status remains recorded-only until a documented resolution.
- **Related Documents:** `00_Project_Context.md` §15.1 (Q-005); `17_Subscription_Billing.md` §18; `33_Validation_Rules.md` SUB-07, EXC-05.

**Cross-referenced codes:** Deleting historical billing records → `BUSINESS_HARD_DELETE_REJECTED` (§19) per `17_Subscription_Billing.md` §18; Flow mixing → `PAYMENT_FLOW_MISMATCH` (§16); authorization boundaries for Flow A management → `AUTHZ_FLOW_A_MANAGEMENT_DENIED` (§6); billing job failures → §21.

---

# 16. Payment Errors

**Authoritative sources:** `02_Software_Requirements.md` Part 5 (Flow A payment-status validations), Part 3/Part 4 (Student/Parent Payments validations); `28_Coding_Standards.md` §15.3; `10_API_Design.md` §24; `07_Data_Dictionary.md` §25, §33; `29_Project_Decisions.md` D-002; `33_Validation_Rules.md` §17; `32_Business_Rules.md` §18.

### PAY-01 — Payment Processing Unsupported
- **Error Code:** `PAYMENT_PROCESSING_UNSUPPORTED`
- **HTTP Status:** 422
- **Error Name:** Online Payment Processing Not Available
- **Description:** Any attempt to initiate, execute, or emulate in-platform payment processing (transactions, checkout, gateway calls). Version 1 records payment **status** only; payments happen outside the Platform (BR-019, D-002). Documented status: **422** (`28_Coding_Standards.md` §15.3).
- **User Message:** "Online payment processing is not available."
- **Internal Message:** `PAYMENT_PROCESSING_UNSUPPORTED: in-platform payment transaction attempt rejected.`
- **Possible Causes:** Crafted transaction payload; assumed checkout capability; confusion between status recording and processing.
- **Recommended Resolution:** Record payment status only; handle the actual payment outside the Platform.
- **Related Documents:** `28_Coding_Standards.md` §15.3; `10_API_Design.md` §2, §24; `00_Project_Context.md` §9 (BR-019); `33_Validation_Rules.md` PAY-06.

### PAY-02 — Payment Flow Mismatch
- **Error Code:** `PAYMENT_FLOW_MISMATCH`
- **HTTP Status:** 422
- **Error Name:** Payment Flow Mismatch
- **Description:** A payment-status record or query mixing Flow A and Flow B — Flow B references on a Platform/Subscription record, or Flow A references on a Teacher Workspace fee record.
- **User Message:** "The payment record is invalid for this context."
- **Internal Message:** `PAYMENT_FLOW_MISMATCH: cross-flow payment-status reference attempted.`
- **Possible Causes:** Wrong endpoint for the flow; mixed references; cache cross-contamination between flows.
- **Recommended Resolution:** Use the Platform payment-status surface for Flow A and the workspace payment-status surface for Flow B.
- **Related Documents:** `07_Data_Dictionary.md` §25, §33; `10_API_Design.md` §24; `33_Validation_Rules.md` PAY-01, INT-09.

### PAY-03 — Payment Reference Invalid
- **Error Code:** `PAYMENT_REFERENCE_INVALID`
- **HTTP Status:** 422
- **Error Name:** Payment Reference Invalid
- **Description:** A Flow A record not tied to a valid Flow A Subscription (valid Teacher + calendar-month Billing Cycle), or a Flow B record whose Student/Group/Pricing Type context is invalid for the Teacher relationship.
- **User Message:** "The selected Subscription is invalid." (Flow A) / "The selected Student is invalid." or "The selected Group is invalid." (Flow B)
- **Internal Message:** `PAYMENT_REFERENCE_INVALID: payment-status target reference failed validity.`
- **Possible Causes:** Stale references; wrong flow context; archived targets.
- **Recommended Resolution:** Select from current valid Subscriptions (Flow A) or enrolled Students/Groups (Flow B).
- **Related Documents:** `10_API_Design.md` §24; `07_Data_Dictionary.md` §25, §33; `33_Validation_Rules.md` PAY-02/03.

### PAY-04 — Payment Status Invalid
- **Error Code:** `PAYMENT_STATUS_INVALID`
- **HTTP Status:** 422
- **Error Name:** Payment Status Invalid
- **Description:** A payment status value outside the confirmed status-only set (documented defaults include Unpaid or Pending), or an amount that is not a valid monetary value when recorded.
- **User Message:** "The selected payment status is invalid." / "The Amount must be a valid monetary amount."
- **Internal Message:** `PAYMENT_STATUS_INVALID: status/money validation failed on payment-status record.`
- **Possible Causes:** Crafted status; invalid amount text.
- **Recommended Resolution:** Use the offered status values and valid amounts.
- **Related Documents:** `07_Data_Dictionary.md` §25, §33; `33_Validation_Rules.md` PAY-04/05.

### PAY-05 — Payment Write Denied
- **Error Code:** `PAYMENT_WRITE_DENIED`
- **HTTP Status:** 403
- **Error Name:** Payment Recording Denied For Role
- **Description:** A Student or Parent attempts to record or modify payment status (own or others'); Students/Parents are read-only on Flow B status, and Flow A recording is Platform-scope only.
- **User Message:** "You do not have permission to perform this action."
- **Internal Message:** `PAYMENT_WRITE_DENIED: unauthorized role attempted payment-status write.`
- **Possible Causes:** Crafted write; role confusion.
- **Recommended Resolution:** Read-only views only; recording is done by the Teacher (Flow B) or Super Admin (Flow A).
- **Related Documents:** `09_Permission_Matrix.md`; `02_Software_Requirements.md` Part 3/4; `33_Validation_Rules.md` PAY-07.

### PAY-06 — Payment Gateway Setting Rejected
- **Error Code:** `PAYMENT_GATEWAY_SETTING_REJECTED`
- **HTTP Status:** 422
- **Error Name:** Payment Gateway Settings Out Of Scope
- **Description:** Platform Settings or pricing payloads containing online payment gateway configuration; such settings are rejected as out of scope for Version 1.
- **User Message:** "This setting is not available."
- **Internal Message:** `PAYMENT_GATEWAY_SETTING_REJECTED: gateway configuration input rejected.`
- **Possible Causes:** Assumed gateway feature; over-posted settings payload.
- **Recommended Resolution:** Configure only confirmed Platform Settings.
- **Related Documents:** `02_Software_Requirements.md` Part 5 (Settings validations); `10_API_Design.md` §27 (settings 422); `33_Validation_Rules.md` PAY-06, EXC-09.

**Cross-referenced codes:** Notification-style payment reminders do not exist (§19 `BUSINESS_NOTIFICATION_UNSUPPORTED`); historical payment record deletion → `BUSINESS_HARD_DELETE_REJECTED` (§19); Subscription-side errors are §15.

---

# 17. File Upload Errors

**Authoritative sources:** `20_File_Storage.md` §10–§15, §21 (Error Handling); `23_Security_Standards.md` §9 (Upload Validation), §18.2–18.3; `28_Coding_Standards.md` §15.3; `10_API_Design.md` §11, §28; `33_Validation_Rules.md` §18.

### FIL-01 — File Type Unsupported
- **Error Code:** `FILE_TYPE_UNSUPPORTED`
- **HTTP Status:** 422
- **Error Name:** File Type Not Supported For Context
- **Description:** A file whose type does not match the owning resource's confirmed context (Homework: Image/PDF; Student submission binary: Image/PDF; Lesson: video) — including executable and archive formats, denied in all contexts. Documented status: **422** (`28_Coding_Standards.md` §15.3).
- **User Message:** "This file type is not supported for this content."
- **Internal Message:** `FILE_TYPE_UNSUPPORTED: declared/file type outside owning-context allowance; upload validation failure logged (security-relevant event).`
- **Possible Causes:** Wrong file kind; executable/archive upload attempt; context confusion (video in Homework → `HOMEWORK_VIDEO_REJECTED`, §12).
- **Recommended Resolution:** Upload a supported type for the target content.
- **Related Documents:** `20_File_Storage.md` §3, §11; `23_Security_Standards.md` §9.2; `33_Validation_Rules.md` FIL-05, FIL-11.

### FIL-02 — File MIME Mismatch
- **Error Code:** `FILE_MIME_MISMATCH`
- **HTTP Status:** 422
- **Error Name:** File Content Does Not Match Type
- **Description:** The file's actual content fails MIME verification against its declared type (renamed files, disguised binaries).
- **User Message:** "The file content does not match its type."
- **Internal Message:** `FILE_MIME_MISMATCH: content sniffing disagreed with declared type (upload failure logged).`
- **Possible Causes:** Extension renaming; disguised executable; corrupted file.
- **Recommended Resolution:** Upload a genuine file of an allowed type.
- **Related Documents:** `23_Security_Standards.md` §9.1, §10.3; `28_Coding_Standards.md` §17.2; `33_Validation_Rules.md` FIL-06.

### FIL-03 — Filename Invalid
- **Error Code:** `FILE_NAME_INVALID`
- **HTTP Status:** 422
- **Error Name:** Filename Invalid
- **Description:** A filename failing sanitization (path-traversal sequences, unusable characters); names are never authorization proofs.
- **User Message:** "The filename is invalid."
- **Internal Message:** `FILE_NAME_INVALID: filename failed sanitization/traversal checks.`
- **Possible Causes:** Crafted traversal name; illegal characters.
- **Recommended Resolution:** Rename the file with a normal name.
- **Related Documents:** `23_Security_Standards.md` §10.3; `20_File_Storage.md` §9; `33_Validation_Rules.md` FIL-07.

### FIL-04 — File Size Guard Rejection
- **Error Code:** `FILE_SIZE_GUARD_REJECTED`
- **HTTP Status:** 422
- **Error Name:** File Could Not Be Accepted (Size Guard)
- **Description:** Early size validation rejected the upload to protect shared-hosting limits. **No product size limit is confirmed** — the message must never cite a fabricated maximum; the guard is hosting-protective (`20_File_Storage.md` §12; `23_Security_Standards.md` §9.4).
- **User Message:** "The file could not be accepted."
- **Internal Message:** `FILE_SIZE_GUARD_REJECTED: early hosting size guard rejected upload (guard thresholds are configuration, never product values).`
- **Possible Causes:** Very large file for shared hosting; abusive bulk upload.
- **Recommended Resolution:** Provide a smaller file; no documented product limit exists to quote.
- **Related Documents:** `20_File_Storage.md` §12, §21; `23_Security_Standards.md` §9.4; `33_Validation_Rules.md` FIL-08, EXC-04.

### FIL-05 — File Access Denied
- **Error Code:** `FILE_ACCESS_DENIED`
- **HTTP Status:** 403
- **Error Name:** File Access Denied
- **Description:** A file operation (view, upload association, archive, restore, replacement) without the required role/scope/ownership/relationship — including cross-Workspace file attempts and known-path/identifier guessing. Files invisible to the actor collapse to 404 `RESOURCE_NOT_FOUND`.
- **User Message:** "You do not have permission to perform this action."
- **Internal Message:** `FILE_ACCESS_DENIED: file operation failed authorization/ownership/relationship checks.`
- **Possible Causes:** Guessed file identifiers; cross-Workspace attempt; Parent upload (canonical code `PARENT_UPLOAD_DENIED`, §10).
- **Recommended Resolution:** Access files only through the owning feature surfaces.
- **Related Documents:** `20_File_Storage.md` §13–§15, §21; `10_API_Design.md` §28; `33_Validation_Rules.md` FIL-02/03.

### FIL-06 — File Target Invalid
- **Error Code:** `FILE_TARGET_INVALID`
- **HTTP Status:** 404
- **Error Name:** File Owning Resource Not Available
- **Description:** The owning resource of an upload (Homework, Lesson, workspace context) does not exist in the authorized scope, or a referenced file does not exist.
- **User Message:** "The selected resource is invalid." / "Not found."
- **Internal Message:** `FILE_TARGET_INVALID: owning resource/file reference unresolved in scope.`
- **Possible Causes:** Stale context; wrong resource identifier; guessing.
- **Recommended Resolution:** Refresh the target resource view and retry through it.
- **Related Documents:** `20_File_Storage.md` §11, §21; `10_API_Design.md` §28; `33_Validation_Rules.md` FIL-02/04.

### FIL-07 — File Target Archived
- **Error Code:** `FILE_TARGET_ARCHIVED`
- **HTTP Status:** 409
- **Error Name:** Owning Resource Not Active
- **Description:** An upload or active file operation targets an archived/inactive owning resource or file reference; such targets accept no active content until restored.
- **User Message:** "The selected resource is not available."
- **Internal Message:** `FILE_TARGET_ARCHIVED: active file operation on archived owning resource/reference.`
- **Possible Causes:** Owning Homework/Lesson archived; stale page.
- **Recommended Resolution:** Restore the owning resource (authorized) or upload to an active one.
- **Related Documents:** `20_File_Storage.md` §11, §17, §21; `00_Project_Context.md` §11; `33_Validation_Rules.md` FIL-10.

**Cross-referenced codes:** `HOMEWORK_VIDEO_REJECTED` (§12) covers all video-in-Homework rejections; `PARENT_UPLOAD_DENIED` (§10) covers Parent uploads (pre-inspection denial); Lesson-video mechanics (formats, quotas, URLs) are PENDING Q-010 — no codes exist beyond the §12/§17 context checks (§27).

---

# 18. Search Errors

**Authoritative sources:** `22_Search_Filtering.md` (search/filter/sort/pagination standards); `10_API_Design.md` §7–§9; `18_Reporting_Analytics.md` §21 (report error handling); `33_Validation_Rules.md` §19; `23_Security_Standards.md` §10.

### SRC-01 — Search Query Invalid
- **Error Code:** `SEARCH_QUERY_INVALID`
- **HTTP Status:** 422
- **Error Name:** Search Query Invalid
- **Description:** A query violating documented query rules (below minimum length, beyond maximum length, unsupported characters). Empty queries are **not** errors — they follow documented empty-query behavior.
- **User Message:** "Please enter a longer search term." / "The search term is too long." / "The search term contains unsupported characters."
- **Internal Message:** `SEARCH_QUERY_INVALID: query rule violated (rule logged; query content treated as non-sensitive).`
- **Possible Causes:** Too-short/too-long input; unsupported characters.
- **Recommended Resolution:** Adjust the search text per the guidance.
- **Related Documents:** `22_Search_Filtering.md`; `33_Validation_Rules.md` SRC-01…SRC-04.

### SRC-02 — Search Filter Invalid
- **Error Code:** `SEARCH_FILTER_INVALID`
- **HTTP Status:** 422
- **Error Name:** Search Filter Invalid
- **Description:** A filter that is not documented for the resource, holds a malformed value, or references records outside the authorized scope; cross-Teacher filters are rejected unless Platform-level visibility applies. Non-disclosing by design.
- **User Message:** "The selected filter is invalid."
- **Internal Message:** `SEARCH_FILTER_INVALID: unsupported/malformed/out-of-scope filter (name logged; no external existence disclosure).`
- **Possible Causes:** Crafted query string; stale filter state after context change; cross-Workspace filter attempt.
- **Recommended Resolution:** Use only the filters offered for the current view/role.
- **Related Documents:** `10_API_Design.md` §8; `22_Search_Filtering.md`; `33_Validation_Rules.md` SRC-06.

### SRC-03 — Search Date Range Invalid
- **Error Code:** `SEARCH_DATE_RANGE_INVALID`
- **HTTP Status:** 422
- **Error Name:** Search Date Range Invalid
- **Description:** `from_date` after `to_date`, or invalid dates in a filter/report range (Billing Cycle filters additionally follow the calendar-month rule).
- **User Message:** "The start date must not be after the end date."
- **Internal Message:** `SEARCH_DATE_RANGE_INVALID: filter/report range ordering failed.`
- **Possible Causes:** Swapped endpoints; malformed date filter.
- **Recommended Resolution:** Correct the range.
- **Related Documents:** `10_API_Design.md` §8; `33_Validation_Rules.md` SRC-07, GEN-06.

### SRC-04 — Sort Unsupported
- **Error Code:** `SEARCH_SORT_UNSUPPORTED`
- **HTTP Status:** 422
- **Error Name:** Sort Not Supported
- **Description:** A `sort` value outside the documented sortable fields for the resource; unsupported sorts are rejected or ignored **consistently** per API standard behavior — this code backs the reject behavior.
- **User Message:** "The requested sort is not supported."
- **Internal Message:** `SEARCH_SORT_UNSUPPORTED: non-whitelisted sort field requested.`
- **Possible Causes:** Crafted sort parameter; outdated client.
- **Recommended Resolution:** Use documented sort fields (leading `-` for descending where supported).
- **Related Documents:** `10_API_Design.md` §9; `22_Search_Filtering.md`; `33_Validation_Rules.md` SRC-10.

### SRC-05 — Pagination Invalid
- **Error Code:** `SEARCH_PAGINATION_INVALID`
- **HTTP Status:** 422
- **Error Name:** Pagination Parameters Invalid
- **Description:** A non-positive/non-integer `page`, or a `per_page` outside allowed limits. Pagination always applies after authorization and scope filtering.
- **User Message:** "The page number is invalid." / "The per page value is invalid."
- **Internal Message:** `SEARCH_PAGINATION_INVALID: page/per_page outside allowed bounds.`
- **Possible Causes:** Crafted values; overflow-sized per_page.
- **Recommended Resolution:** Use the pagination controls; defaults apply when omitted.
- **Related Documents:** `10_API_Design.md` §7; `33_Validation_Rules.md` SRC-11.

### SRC-06 — Status Filter Invalid
- **Error Code:** `SEARCH_STATUS_INVALID`
- **HTTP Status:** 422
- **Error Name:** Status Filter Invalid
- **Description:** A `status` filter outside the valid status set of the queried resource (e.g., active, archived, pending, submitted, paid, unpaid where applicable).
- **User Message:** "The selected status is invalid."
- **Internal Message:** `SEARCH_STATUS_INVALID: unknown resource-status filter value.`
- **Possible Causes:** Crafted value; status from another resource.
- **Recommended Resolution:** Pick from the offered statuses.
- **Related Documents:** `10_API_Design.md` §8; `33_Validation_Rules.md` SRC-08.

### SRC-07 — Search Scope Denied
- **Error Code:** `SEARCH_SCOPE_DENIED`
- **HTTP Status:** 403
- **Error Name:** Search Scope Denied
- **Description:** A search/report action beyond the actor's scope (e.g., report type without its permission, Platform-scoped queries by non-Platform roles). Scope is resolved before searching; never widened by a query.
- **User Message:** "You do not have permission to perform this action."
- **Internal Message:** `SEARCH_SCOPE_DENIED: report/search action outside authorized scope.`
- **Possible Causes:** Direct report URL without permission; stale role assumptions.
- **Recommended Resolution:** Use the reports available to the current role.
- **Related Documents:** `18_Reporting_Analytics.md` §21; `09_Permission_Matrix.md`; `33_Validation_Rules.md` SRC-05.

**Cross-referenced rule:** A valid report criterion with no records returns an **empty result, not an error** (`18_Reporting_Analytics.md` §21) — by design, no code is assigned; archived records never appear in normal search results (SRC rules, §19 visibility).

---

# 19. API Errors

**Authoritative sources:** `10_API_Design.md` §2, §5–§6, §10–§12, §29–§30; `28_Coding_Standards.md` §3.12, §15; `23_Security_Standards.md` §8, §18; `19_Notification_System.md` §23; `11_Backend_Architecture.md` (request pipeline).

### API-01 — Malformed Request
- **Error Code:** `API_MALFORMED_REQUEST`
- **HTTP Status:** 400
- **Error Name:** Malformed Request
- **Description:** The request cannot be understood at the transport level: malformed JSON body, wrong content structure for the endpoint (JSON expected; multipart only for file endpoints), or an invalid operation regardless of field values.
- **User Message:** "The request could not be understood."
- **Internal Message:** `API_MALFORMED_REQUEST: JSON parse/transport shape failure (parse diagnostic logged, body not logged).`
- **Possible Causes:** Broken JSON; wrong content type; corrupted client.
- **Recommended Resolution:** Fix the request payload; client contract/schema should prevent recurrence.
- **Related Documents:** `10_API_Design.md` §2, §6, §11; `33_Validation_Rules.md` API-01.

### API-02 — Unsupported Route
- **Error Code:** `API_UNSUPPORTED_ROUTE`
- **HTTP Status:** 404
- **Error Name:** Endpoint Not Found
- **Description:** A request to a route that does not exist in Version 1 — including any notification route (no notification endpoints exist) and 'Login as Teacher' handling (its capability is denied; see BUSINESS codes).
- **User Message:** "Not found."
- **Internal Message:** `API_UNSUPPORTED_ROUTE: request hit undefined/unsupported v1 route.`
- **Possible Causes:** Outdated client; guessed endpoints; scanner traffic.
- **Recommended Resolution:** Use only documented `/api/v1` endpoints.
- **Related Documents:** `10_API_Design.md` §5, §29–§30; `33_Validation_Rules.md` API-02, API-08.

### API-03 — Resource Not Found Or Not Visible
- **Error Code:** `RESOURCE_NOT_FOUND`
- **HTTP Status:** 404
- **Error Name:** Resource Not Found
- **Description:** The canonical not-found/not-visible outcome: the referenced record does not exist **or is outside the actor's visibility**, and the response must never distinguish the two. This is the collapse point for all scoped-record invisibility (cross-Workspace reads, unlinked Students, unavailable Lessons/Homework/Exams/attempts/files, pending-visibility audit/content).
- **User Message:** "Not found."
- **Internal Message:** `RESOURCE_NOT_FOUND: record reference unresolved in actor visibility (actual cause logged; never returned).`
- **Possible Causes:** Nonexistent reference; out-of-scope reference; archived record addressed via an active-only path (state mismatches may instead use 409 codes where the operation contract defines them).
- **Recommended Resolution:** Refresh the current view and navigate from it; never guess identifiers.
- **Related Documents:** `10_API_Design.md` §6; `23_Security_Standards.md` §18.2; `33_Validation_Rules.md` GEN-12, API-04.

### API-04 — Rate Limit Exceeded
- **Error Code:** `API_RATE_LIMIT_EXCEEDED`
- **HTTP Status:** 429
- **Error Name:** Rate Limit Exceeded
- **Description:** Generic rate limiting where documented limits apply; authentication-specific limiting uses `AUTH_LOGIN_RATE_LIMITED`/`AUTH_RESET_RATE_LIMITED` (§5). Responses include a `Retry-After` header and never reveal internal thresholds.
- **User Message:** "Too many requests. Please try again later."
- **Internal Message:** `API_RATE_LIMIT_EXCEEDED: request rate limit engaged for source (thresholds internal-only); violations monitored as security-relevant events.`
- **Possible Causes:** Request bursts; automated traffic.
- **Recommended Resolution:** Back off per Retry-After; clients present a retryable task-level failure without duplicate mutations.
- **Related Documents:** `23_Security_Standards.md` §8.6, §18.2; `10_API_Design.md` §6; `12_Frontend_Architecture.md` (429 handling).

### API-05 — Notification Request Unsupported
- **Error Code:** `BUSINESS_NOTIFICATION_UNSUPPORTED`
- **HTTP Status:** 404
- **Error Name:** Notifications Not Available
- **Description:** Any request to create, view, update, archive, restore, or send a notification — Notification is not a Version 1 product entity. Documented status: **404** (`28_Coding_Standards.md` §15.3). Counters, badges, read markers, and notification-center behavior do not exist.
- **User Message:** "Not found."
- **Internal Message:** `BUSINESS_NOTIFICATION_UNSUPPORTED: notification payload/route rejected (out of scope v1).`
- **Possible Causes:** Third-party widget expectations; crafted payloads; assumed feature.
- **Recommended Resolution:** None — out of scope; in-context feedback states are not notifications.
- **Related Documents:** `19_Notification_System.md` §23; `28_Coding_Standards.md` §15.3; `10_API_Design.md` §29.

### API-06 — Login As Teacher Unsupported
- **Error Code:** `BUSINESS_LOGIN_AS_TEACHER_UNSUPPORTED`
- **HTTP Status:** 403 or 404
- **Error Name:** 'Login As Teacher' Not Available
- **Description:** Any request performing 'Login as Teacher' impersonation; the capability is denied in Version 1 (`platform.teacher.login_as_teacher` is Denied), and the endpoint contract rejects with 403 or 404 per routing/authorization.
- **User Message:** "You do not have permission to perform this action." / "Not found."
- **Internal Message:** `BUSINESS_LOGIN_AS_TEACHER_UNSUPPORTED: impersonation attempt rejected.`
- **Possible Causes:** Assumed admin impersonation feature; crafted request.
- **Recommended Resolution:** None — not a Version 1 capability.
- **Related Documents:** `10_API_Design.md` §30; `09_Permission_Matrix.md`; `33_Validation_Rules.md` API-08.

### API-07 — Platform Staff Creation Unsupported
- **Error Code:** `BUSINESS_PLATFORM_STAFF_UNSUPPORTED`
- **HTTP Status:** 422
- **Error Name:** Platform Staff Roles Out Of Scope
- **Description:** Attempts to create Platform staff roles (Support, Sales, Accountant, or any non-Super-Admin Platform staff); such roles are out of scope and creation must be rejected.
- **User Message:** "This action is not available."
- **Internal Message:** `BUSINESS_PLATFORM_STAFF_UNSUPPORTED: platform staff role creation rejected.`
- **Possible Causes:** Assumed admin team management; crafted role payloads.
- **Recommended Resolution:** None — Version 1 has exactly five roles.
- **Related Documents:** `02_Software_Requirements.md` Part 5 (Settings validations); `07_Data_Dictionary.md` §2; `32_Business_Rules.md` §8, §26.

### API-08 — Hard Delete Rejected
- **Error Code:** `BUSINESS_HARD_DELETE_REJECTED`
- **HTTP Status:** 422
- **Error Name:** Permanent Deletion Not Available
- **Description:** Any permanent-deletion attempt on any record, by any actor, anywhere — no hard delete exists in the system; the operation is Archive or it is rejection. Documented status: **422** (`28_Coding_Standards.md` §15.3 — reject; require Archive).
- **User Message:** "This operation is not available. Records can be archived instead."
- **Internal Message:** `BUSINESS_HARD_DELETE_REJECTED: hard-delete attempt blocked (no delete permission exists for any role).`
- **Possible Causes:** Legacy client expectations; crafted DELETE-style operations; misunderstanding of Archive policy.
- **Recommended Resolution:** Use the Archive action; restore reverses Archive where authorized.
- **Related Documents:** `00_Project_Context.md` §11; `28_Coding_Standards.md` §15.3; `10_API_Design.md` §2 (no hard-deletion endpoints); `32_Business_Rules.md` §24 (BR-005/BR-014).

### API-09 — Archive State Conflict (Generic Lifecycle)
- **Error Code:** `BUSINESS_ARCHIVE_STATE_CONFLICT`
- **HTTP Status:** 409
- **Error Name:** Archive/Restore State Conflict
- **Description:** Generic archive/restore lifecycle conflict for resources without a module-specific code: archiving an already-archived record or restoring a non-archived one. Module-specific twins (e.g., `TEACHER_ARCHIVE_STATE_CONFLICT`) take precedence in their sections.
- **User Message:** "This record is already archived." / "This record is not archived."
- **Internal Message:** `BUSINESS_ARCHIVE_STATE_CONFLICT: lifecycle action mismatched current Archive state.`
- **Possible Causes:** Repeated action; concurrent state changes; stale UI.
- **Recommended Resolution:** Refresh state; no repeat needed once desired state holds.
- **Related Documents:** `10_API_Design.md` §6 (409 semantics) and archive/restore endpoints; `00_Project_Context.md` §11.

**Cross-referenced codes:** 401/403/422/429 families live in §5–§18; the 422 envelope code (`VALIDATION_FAILED`) is §7 VAL-01; transport-security (HTTPS in production) is an infrastructure guarantee, not an application error surface (`10_API_Design.md` §2; `23_Security_Standards.md` §8.1).

---

# 20. Database Errors

**Authoritative sources:** `28_Coding_Standards.md` §15.1, §16.1; `23_Security_Standards.md` §10–§11, §18; `06_Database_Design.md` §12–§13; `33_Validation_Rules.md` §21; `10_API_Design.md` §6.

All codes in this section are **500** with the generic user message; causes appear only in internal messages and operational logs — never in responses. Business-mapped constraint outcomes (e.g., duplicate Student identity) surface as their 409 domain codes, never as raw database errors.

### DB-01 — Integrity Violation (Unmapped)
- **Error Code:** `DATABASE_INTEGRITY_VIOLATION`
- **HTTP Status:** 500
- **Error Name:** Data Integrity Violation
- **Description:** A persistence integrity rule from `33_Validation_Rules.md` §21 (INT-xx) fails in a way not mapped to a documented business code: reference existence, scope-match consistency, status validity, conditional-timestamp consistency at the persistence layer.
- **User Message:** "An unexpected error occurred."
- **Internal Message:** `DATABASE_INTEGRITY_VIOLATION: [INT-xx] failed at persistence (rule id + non-sensitive context logged; SQL context in operational logs only).`
- **Possible Causes:** Bypassed service validation; defective migration/data correction; race between validation and persistence.
- **Recommended Resolution:** Treat as a defect: investigate the logged rule and reconcile data; strengthen the missing request/business validation mapping.
- **Related Documents:** `06_Database_Design.md` §12–§13; `33_Validation_Rules.md` §21; `28_Coding_Standards.md` §15.1.

### DB-02 — Database Unavailable
- **Error Code:** `DATABASE_UNAVAILABLE`
- **HTTP Status:** 500
- **Error Name:** Database Unavailable
- **Description:** The MySQL 8 connection cannot serve the request (connection failure/timeouts at request level). Transient background-job variants retry per §21; request-level failures degrade to a generic error without detail.
- **User Message:** "An unexpected error occurred."
- **Internal Message:** `DATABASE_UNAVAILABLE: connection/query infrastructure failure (driver diagnostic logged server-side only).`
- **Possible Causes:** Hosting database outage; resource exhaustion; maintenance windows.
- **Recommended Resolution:** Retry shortly (frontend offers retryable failure without duplicate mutations); operators follow hosting diagnostics.
- **Related Documents:** `28_Coding_Standards.md` §15.1–§16.1; `25_Performance_Scalability.md` (hosting limits); `12_Frontend_Architecture.md` (transient failure handling).

### DB-03 — Unmapped Uniqueness Violation
- **Error Code:** `DATABASE_UNIQUE_CONSTRAINT_UNMAPPED`
- **HTTP Status:** 500
- **Error Name:** Unmapped Uniqueness Conflict
- **Description:** A uniqueness constraint triggers without a corresponding documented business code (any confirmed uniqueness should map to `STUDENT_DUPLICATE_ACCOUNT` or `VALIDATION_UNIQUENESS_CONFLICT`; an unmapped hit indicates a coverage gap).
- **User Message:** "An unexpected error occurred."
- **Internal Message:** `DATABASE_UNIQUE_CONSTRAINT_UNMAPPED: unique constraint fired outside mapped rules (constraint logged non-sensitively).`
- **Possible Causes:** New persistence constraint without business-code mapping; race producing duplicate between validation and commit.
- **Recommended Resolution:** Map the constraint to a documented code; verify request-level duplicate checks.
- **Related Documents:** `33_Validation_Rules.md` GEN-10, INT-03; `28_Coding_Standards.md` §15.1.

### DB-04 — Scope Mismatch At Persistence
- **Error Code:** `DATABASE_SCOPE_MISMATCH`
- **HTTP Status:** 500
- **Error Name:** Workspace Scope Mismatch (Persistence)
- **Description:** A child record's Teacher Workspace reference would disagree with its parent's at write time (INT-02/INT-14): an integrity breach attempt beyond request validation, treated as a defect, never a data correction by the database layer.
- **User Message:** "An unexpected error occurred."
- **Internal Message:** `DATABASE_SCOPE_MISMATCH: child/parent workspace disagreement at write (references logged non-sensitively).`
- **Possible Causes:** Service-layer bug; cross-workspace assembly that slipped validation.
- **Recommended Resolution:** Defect investigation; service must assemble only scope-consistent graphs (see AUTHZ-03 at request level).
- **Related Documents:** `29_Project_Decisions.md` D-020; `06_Database_Design.md` §12–§13; `33_Validation_Rules.md` INT-02, INT-14.

**Cross-referenced codes:** Any SQL/driver detail lives only in operational logs (content restrictions, §23); business conflicts are 409 module codes; input problems are 422.

---

# 21. Queue & Background Job Errors

**Authoritative sources:** `21_Background_Jobs.md` §3 (principles), §13 (Retry Strategy), §14 (Failure Handling), §17 (Logging), §19 (Error Handling); `28_Coding_Standards.md` §16.1; `23_Security_Standards.md` §16.5.

Background failures have **no HTTP surface** (HTTP Status: "Internal"), produce no user-facing error response, and use the §22 generic user state only when a user-visible task depends on them (e.g., pending results stay honestly pending). Failures are recorded in the Laravel failed jobs table and operational logs; Version 1 sends no push/email/SMS failure notifications.

### QUE-01 — Job Failed
- **Error Code:** `QUEUE_JOB_FAILED`
- **HTTP Status:** Internal (no HTTP surface)
- **Error Name:** Background Job Failed
- **Description:** A queued job threw an unrecoverable exception during execution for the current attempt: exception caught, details logged, job marked failed, no partial state left violating business rules (transactions rolled back).
- **User Message:** — (no user surface; affected user-visible results remain honestly pending)
- **Internal Message:** `QUEUE_JOB_FAILED: job {job/context} threw {exception summary}; attempt n of N; failed-record written without sensitive payload data.`
- **Possible Causes:** Transient resource failure; data inconsistency; authorization context issues; defects.
- **Recommended Resolution:** Automatic retry with the documented backoff for the category; after exhaustion it becomes QUE-02; payloads never contain sensitive data.
- **Related Documents:** `21_Background_Jobs.md` §13.1, §14.1, §19.1.

### QUE-02 — Job Retry Exhausted
- **Error Code:** `QUEUE_JOB_RETRY_EXHAUSTED`
- **HTTP Status:** Internal
- **Error Name:** Background Job Retry Exhausted
- **Description:** A job consumed its documented retry attempts (e.g., Billing/Subscription 3, auto-grading 3, report preparation 2, file integrity 1, audit verification 1, attendance cleanup 2) without success and remains in the failed jobs table awaiting manual review.
- **User Message:** —
- **Internal Message:** `QUEUE_JOB_RETRY_EXHAUSTED: attempts exhausted per category policy; awaiting manual review/retry.`
- **Possible Causes:** Persistent data inconsistency; persistent context invalidity; hosting resource limits; defects.
- **Recommended Resolution:** Super Admin or authorized platform operator reviews and manually retries via Laravel's failed job management; retry preserves idempotency (re-execution must overwrite, never duplicate).
- **Related Documents:** `21_Background_Jobs.md` §13.2–§13.3, §14.1.

### QUE-03 — Job Data Inconsistency
- **Error Code:** `QUEUE_DATA_INCONSISTENCY`
- **HTTP Status:** Internal
- **Error Name:** Job Data Inconsistency
- **Description:** A job encountered missing or inconsistent records required for truthful output (e.g., missing Enrollment records for calculation, orphaned file reference); per the documented failure category: log the failure and do **not** retry until data is corrected.
- **User Message:** —
- **Internal Message:** `QUEUE_DATA_INCONSISTENCY: missing/inconsistent source data (references logged non-sensitively; no retry until corrected).`
- **Possible Causes:** Deleted-by-archive misreads, partial data corrections, integrity drift.
- **Recommended Resolution:** Correct the underlying data, then re-run; the request-level twin is `SUBSCRIPTION_ENROLLMENT_DATA_UNAVAILABLE` (§15).
- **Related Documents:** `21_Background_Jobs.md` §14.2; `33_Validation_Rules.md` EXC-06, INT-xx.

### QUE-04 — Job Context Invalid
- **Error Code:** `QUEUE_CONTEXT_INVALID`
- **HTTP Status:** Internal
- **Error Name:** Job Authorization Context Invalid
- **Description:** A job's carried Teacher Workspace or authorization context is invalid (e.g., expired system context, workspace mismatch): log the failure and do **not** retry without corrected context. Jobs must never access another Teacher Workspace's data.
- **User Message:** —
- **Internal Message:** `QUEUE_CONTEXT_INVALID: invalid/expired workspace or authorization context; no retry until corrected.`
- **Possible Causes:** Archived workspace mid-processing; stale dispatch context; defective dispatch.
- **Recommended Resolution:** Re-dispatch with corrected, authorized context; scope violations are treated as defects and investigated.
- **Related Documents:** `21_Background_Jobs.md` §3 (workspace scope preserved), §14.2; `32_Business_Rules.md` §5 (BR-003).

### QUE-05 — Job Resource Exhausted
- **Error Code:** `QUEUE_RESOURCE_EXHAUSTED`
- **HTTP Status:** Internal
- **Error Name:** Job Resource Exhaustion
- **Description:** A job hit shared-hosting limits (memory, execution time): per the documented category, chunk the work into smaller batches and retry.
- **User Message:** —
- **Internal Message:** `QUEUE_RESOURCE_EXHAUSTED: hosting limit reached (memory/time); batch-size reduction required.`
- **Possible Causes:** Over-large batches; unscoped queries loading big collections.
- **Recommended Resolution:** Chunk/paginate the workload per performance guidance; jobs iterate workspaces one at a time.
- **Related Documents:** `21_Background_Jobs.md` §14.2, §18.1; `25_Performance_Scalability.md` (hosting limits; chunking).

### QUE-06 — Audit Recording Failed
- **Error Code:** `QUEUE_AUDIT_RECORDING_FAILED`
- **HTTP Status:** Internal
- **Error Name:** Audit Recording Failed For Background Action
- **Description:** A background job performing an important action could not record its Audit Log entry; by the documented principle, the business action **must not be considered complete** — rollback/compensate so no unaudited state persists.
- **User Message:** —
- **Internal Message:** `QUEUE_AUDIT_RECORDING_FAILED: audit write failed for job action; action rolled back/incomplete by policy.`
- **Possible Causes:** Database failure during audit write; audit-transaction defect.
- **Recommended Resolution:** Investigate audit persistence; re-execute the job after correction (idempotency guarantees safe re-runs).
- **Related Documents:** `21_Background_Jobs.md` §3 (audit obligations; §19.1 rollback), §17.2; `00_Project_Context.md` §10.

### QUE-07 — Scheduler Task Failed
- **Error Code:** `QUEUE_SCHEDULER_TASK_FAILED`
- **HTTP Status:** Internal
- **Error Name:** Scheduled Task Failed
- **Description:** A scheduled task (via Laravel Scheduler through Cron Jobs) failed; the failure is logged, and subsequent Scheduler runs must not be blocked — a failed Billing Cycle initialization must not prevent next month's cycle from being attempted.
- **User Message:** —
- **Internal Message:** `QUEUE_SCHEDULER_TASK_FAILED: scheduled task failed at scheduler level; next runs unaffected.`
- **Possible Causes:** Cron misconfiguration; task exception; overlapping-instance guard activity.
- **Recommended Resolution:** Check the Cron trigger and task logs; tasks run without overlap; failed work retries per its category.
- **Related Documents:** `21_Background_Jobs.md` §5, §19.2, §18.3.

**Cross-referenced codes:** Idempotency (no duplicate Billing Cycles, snapshots, grades, or archive actions on re-run) is a design constraint, not an error; job-level business violations map to their §8–§16 domain codes when jobs surface state for users.

---

# 22. System Errors

**Authoritative sources:** `10_API_Design.md` §6; `28_Coding_Standards.md` §15.1, §16.1; `23_Security_Standards.md` §18.2–§18.4; `25_Performance_Scalability.md` (hosting limits); `12_Frontend_Architecture.md` (unexpected failure handling); `21_Background_Jobs.md` §19.3.

All codes in this section are **500** with the generic user message "An unexpected error occurred." — never stack traces, SQL, paths, versions, or internals. Details exist only as internal messages and operational log entries.

### SYS-01 — Unexpected Error
- **Error Code:** `SYSTEM_UNEXPECTED`
- **HTTP Status:** 500
- **Error Name:** Unexpected Server Error
- **Description:** The catch-all for any failure not attributable to user input, authorization, validation, business rules, or persistence mappings — the standardized outcome of exception normalization (`28_Coding_Standards.md` §3.12).
- **User Message:** "An unexpected error occurred."
- **Internal Message:** `SYSTEM_UNEXPECTED: unhandled/unnormalized exception normalized to 500 (exception type, message, stack, request context in operational logs only).`
- **Possible Causes:** Defects; unanticipated states; hosting faults.
- **Recommended Resolution:** Users: retry or return via safe paths; `request_id` (when present) supports operator lookup. Operators: trace in operational logs; convert recurring causes into mapped codes.
- **Related Documents:** `10_API_Design.md` §6; `28_Coding_Standards.md` §15.1; `23_Security_Standards.md` §18.2.

### SYS-02 — System Resource Exhaustion
- **Error Code:** `SYSTEM_RESOURCE_EXHAUSTION`
- **HTTP Status:** 500
- **Error Name:** Resource Limits Reached
- **Description:** Request processing hit shared-hosting limits (execution time, memory) and was terminated; the user sees a retryable failure state while sizing fixes (pagination, chunking, query scoping) are applied.
- **User Message:** "An unexpected error occurred." (frontend presents a retryable task-level failure without duplicate mutations)
- **Internal Message:** `SYSTEM_RESOURCE_EXHAUSTION: process time/memory limit reached (endpoint context logged non-sensitively).`
- **Possible Causes:** Oversized result sets; un-chunked heavy work in request path.
- **Recommended Resolution:** Retry; engineers move heavy work to queued/chunked processing per performance guidance.
- **Related Documents:** `25_Performance_Scalability.md` (limits; chunking); `12_Frontend_Architecture.md` (transient handling); `21_Background_Jobs.md` §18.1.

### SYS-03 — Configuration Invalid
- **Error Code:** `SYSTEM_CONFIGURATION_INVALID`
- **HTTP Status:** 500
- **Error Name:** Configuration Invalid
- **Description:** Production runs with an invalid or unsafe configuration state (e.g., `APP_DEBUG` not false, missing critical environment values). Debug exposure in production is prohibited; this failure mode is landing-page-safe and logged, never descriptive.
- **User Message:** "An unexpected error occurred."
- **Internal Message:** `SYSTEM_CONFIGURATION_INVALID: unsafe/invalid runtime configuration detected (key names logged; values never logged).`
- **Possible Causes:** Deploy misconfiguration; missing environment variables; debug enabled in production.
- **Recommended Resolution:** Operators correct configuration per deployment documentation; secrets live in environment variables only.
- **Related Documents:** `28_Coding_Standards.md` §15.1, §16.1; `23_Security_Standards.md` §16.2, §18.4; `26_Deployment_Plan.md`.

### SYS-04 — Dependency Failure
- **Error Code:** `SYSTEM_DEPENDENCY_FAILURE`
- **HTTP Status:** 500
- **Error Name:** Supporting Dependency Failed
- **Description:** A confirmed-baseline supporting service fails during a request (e.g., SMTP during the password-reset-sending operation through the confirmed communication channel, storage write failures). The user-facing operation degrades honestly; no fallback invents notification or alternate-delivery behavior.
- **User Message:** "An unexpected error occurred." (for reset delivery: "If an account exists for the provided identifier, reset instructions have been sent." remains the non-disclosing response where the documented flow requires it)
- **Internal Message:** `SYSTEM_DEPENDENCY_FAILURE: baseline dependency failed during operation (dependency + operation logged; no credentials).`
- **Possible Causes:** Mail transport outage; storage write faults on shared hosting.
- **Recommended Resolution:** Retry later; operators inspect dependency health; corrective ops per deployment scope.
- **Related Documents:** `23_Security_Standards.md` §6.2, §18.4; `28_Coding_Standards.md` §15.1; `33_Validation_Rules.md` MSG-04.

**Cross-referenced codes:** Request-shape/API failures are §19; persistence failures are §20; worker failures are §21; there is intentionally no `503`/maintenance-mode code — only the eight documented statuses exist (§4).

---

# 23. Logging Requirements

**Authoritative sources:** `28_Coding_Standards.md` §16 (Logging Standards); `23_Security_Standards.md` §15.5, §16.5, §18.4; `21_Background_Jobs.md` §14.1, §17; `00_Project_Context.md` §10 (Audit Log Policy); `04_Project_Structure.md` (operational logs location).

This section consolidates the **two-channel rule**: every failure is written to the right channel, once, with the right content.

| # | Requirement | Rule |
|---|---|---|
| LOG-01 | Two channels, never confused | **Operational logs** (troubleshooting, runtime diagnostics) and the business **Audit Log** (accountability) are distinct: operational logs never replace the Audit Log; Audit Log entries never appear inside operational logs. |
| LOG-02 | Operational log content | Application errors/exceptions with sufficient diagnostic context; authentication failures (without revealing whether the account exists); authorization failures (without revealing resource existence); file upload validation failures; background job dispatch/start/completion/failure/retries; scheduler task execution; queue processing status. |
| LOG-03 | Operational log format | Timestamp, log level, context (feature/service/request identifier), message, relevant non-sensitive metadata. Production uses `info` level or higher; `debug` is development-only. |
| LOG-04 | Never logged — anywhere | Passwords and secrets in any form, tokens, API keys, credentials, database credentials or application secrets, full file content, Question Bank content, Student personal data beyond troubleshooting need, sensitive request/response payloads, Teacher-private content beyond troubleshooting need. |
| LOG-05 | Detail placement | Stack traces, SQL context, and request details belong to **operational logs only** — never in error responses (`APP_DEBUG` false in production). |
| LOG-06 | Security-relevant operational events | Repeated failed logins; authorization failures; cross-Teacher access attempts; rate-limit violations; file upload validation failures; password reset requests/completions; session creation and destruction. |
| LOG-07 | Audit Log channel | The ten mandatory events (Create, Update, Archive, Restore, Login success/failure, Permission Change, Attendance Change, Exam Modification, Homework Modification, Subscription Change) are recorded in the Audit Log — append-only, immutable, permanent — with actor identity and role, scope context, event type, affected entity, before/after snapshot of changed fields, timestamp, IP address, device/client information. |
| LOG-08 | Failed login record | Failed logins record the attempted identifier **without exposing whether the account exists**, plus timestamp, IP address, and device/client information. |
| LOG-09 | Job failure records | Failed-job entries carry job class and queue name, payload without sensitive data, exception message and trace without Teacher-private data, failure timestamp, attempts made — in the failed jobs table plus operational logs; plus Audit Log entries where the failed action qualifies as an important action. |
| LOG-10 | Attribution | Teacher Staff actions are attributed to the Teacher Staff user (never the Teacher); Super Admin actions to the Super Admin; Student/Parent actions to the authenticated account; job entries record the system/actor context under which they executed. |
| LOG-11 | No notification channel | Logging is the failure-visibility channel: no push, email, or SMS notification for any failure, user-facing or operational. |
| LOG-12 | Error code correlation | Where an error maps to a registry code, the internal log entry carries that code and, for HTTP responses, the `request_id` (when present) so support can correlate user reports to log entries. |

---

# 24. User-Friendly Error Messages

**Authoritative sources:** `23_Security_Standards.md` §18 (Error Message Policy); `13_UI_UX_Guidelines.md` (error states); `28_Coding_Standards.md` §15.1–§15.3; `12_Frontend_Architecture.md` (error classification); `14_UI_Components.md`; `33_Validation_Rules.md` §22.

## 24.1 Policy

1. Every user message in this registry follows the documented message policy: correction-oriented where the user can fix the input; generic where disclosure would leak (authentication, authorization, not-found, server errors).
2. Messages use canonical terminology (`30_Project_Glossary.md`) and never contain: SQL/driver details, stack traces, server paths, credentials or secrets, internal identifiers, permission codes, route names, framework/versions, Teacher-private data, unlinked Student data, another Teacher Workspace, or fabricated numeric limits.
3. The per-category standard texts (`23_Security_Standards.md` §18.2) are binding:
   - Authentication: "The provided credentials are incorrect." (generic; never reveals account existence).
   - Authorization: "You do not have permission to perform this action." (never reveals resource existence).
   - Validation: the field-level texts of `33_Validation_Rules.md` §22 (explain the expected format without exposing internal constraints).
   - Not found: "Not found." (never distinguishes absent from inaccessible).
   - Server errors: "An unexpected error occurred." (never internals).
   - Rate limits: "Too many requests. Please try again later." with `Retry-After` header (never thresholds).
4. Presentation: validation errors attach to their fields with a form-level summary and managed focus; conflicts preserve safe user input; authorization/not-found use generic states; transient/network failures offer a non-duplicating retry; unsupported actions explain the confirmed constraint without suggesting out-of-scope workarounds (`13_UI_UX_Guidelines.md`; `12_Frontend_Architecture.md`).
5. Messages are translatable: Arabic is the default, English is fully supported, and presentation automatically uses RTL/LTR. Timezone and currency decisions remain PENDING.

## 24.2 User Message Map (Summary)

| Situation | Status | User message |
|---|---|---|
| Not authenticated / session expired | 401 | "Authentication is required." / "Your session has expired. Please log in again." |
| Bad credentials | 401 | "The provided credentials are incorrect." |
| Too many attempts | 429 | "Too many login attempts. Please try again later." (+ Retry-After) |
| Any denial by role/scope/permission | 403 | "You do not have permission to perform this action." |
| Record absent or invisible | 404 | "Not found." |
| Required/format/type/date/enum/reference/length/numeric/prohibited/unique-field failures | 422 | Field-level texts (`33_Validation_Rules.md` MSG-10) attached to fields |
| Business/state conflicts (duplicate account, second active Group, archived-in-use, already-active, duplicate Attendance, transition conflicts) | 409 | The specific conflict texts of §9–§19 |
| Server/dependency/persistence failures | 500 | "An unexpected error occurred." |

---

# 25. Internal Error Messages

**Authoritative sources:** `28_Coding_Standards.md` §15.1 (error logging), §16.1; `23_Security_Standards.md` §16.1, §16.5, §18.4; `21_Background_Jobs.md` §14.1, §19.3.

## 25.1 Policy

1. Internal messages exist for operators and developers only: they live in operational logs and failed-job records, are never returned in responses, and are written in technical-but-safe language.
2. **Shape:** `{ERROR_CODE}: {diagnostic sentence}` — the registry code first, then a compact cause statement with non-sensitive context (feature, job, rule id such as INT-xx, attempt counts, references that do not expose personal/private content).
3. **May contain** (operational logs only): exception types and stack traces, SQL context, request diagnostics, timing data, configuration *key names* (never values), queue/job metadata without sensitive payloads.
4. **Must never contain** (§23 LOG-04 applies everywhere): passwords/secrets/tokens/keys, database credentials, full file content, Question Bank content, Student personal data beyond troubleshooting need, sensitive payloads, Teacher-private content beyond troubleshooting need.
5. Internal messages must, where the policy requires, avoid confirming existence in a way that would leak if logs were over-shared: failed-login and authorization entries as documented (attempted identifier recorded without account-existence confirmation; resource existence not confirmed in authorization logs); the diagnostic codes carry the cause so text stays conservative.
6. For job failures the internal message records the category (transient / data inconsistency / authorization context / business rule / resource exhaustion), the attempts made, and the retry disposition per `21_Background_Jobs.md` §13–§14.

## 25.2 Correlation

When the response includes `request_id`, the internal message includes the same identifier so a user report can be matched to the operational log entry exactly once; log records include timestamp, level, and context per §23 LOG-03.

---

# 26. Error Response Standards

**Authoritative sources:** `10_API_Design.md` §6, §10; `28_Coding_Standards.md` §15.1–§15.2; `12_Frontend_Architecture.md` (error classification); `13_UI_UX_Guidelines.md`; `23_Security_Standards.md` §18.

## 26.1 Backend Envelope

Every error response uses the documented structure:

| Field | Rule |
|---|---|
| `success` | Always `false`. |
| `error.code` | The registry code (§3); for 422 responses exactly `VALIDATION_FAILED`, with specific condition codes carried per-field in `errors` (§3.3 rule 4). |
| `error.message` | The registry user message for the code (§24). |
| `error.details` | Optional; non-sensitive only; never used to smuggle diagnostics. |
| `request_id` | Optional request reference for support and tracing (correlates to §25.2). |
| `errors` | 422 only: field-level validation messages (with their specific condition codes). |

Production never emits debug output; all exceptions are normalized into this envelope via the documented exception-mapping layer.

## 26.2 Frontend Handling Taxonomy

| Error class | Frontend response |
|---|---|
| 401 unauthenticated / session expired | Clear protected context and cache; direct to authentication. |
| 403 unauthorized | Generic access-denied state; do not reveal resource existence; remove stale capability assumptions. |
| 404 unavailable | Neutral unavailable/not-found state; never distinguish inaccessible vs absent. |
| 409 conflict | Preserve form data where safe; explain current state prevents completion; refresh affected server state. |
| 422 validation | Attach server field messages to the active form; show non-field messages safely. |
| 429 / transient network or server failure | Retryable task-level failure with **no duplicate mutation**; honor `Retry-After` where present. |
| Unexpected client failure | Contain with route/application error boundaries; report only non-sensitive diagnostics through the approved operational channel; offer recovery. |

Error boundaries never display request headers, credentials, raw backend payloads, stack traces, Teacher Workspace identifiers, file paths, or private record data. Error handling never replaces required backend Audit Log behavior.

---

# 27. Future Error Codes

**Authoritative sources:** `31_Master_Index.md` §8 (Modification Rules), §9–§10; `10_API_Design.md` §5 (Versioning); `00_Project_Context.md` §15.1 (open questions); `32_Business_Rules.md` §28.

## 27.1 Rules For Adding Codes

1. **Documentation first:** a code may appear in code only after it is registered in this document through the documented modification sequence (Product Owner confirmation for business-visible outcomes; decision recorded where required).
2. **Uniqueness forever:** new codes must not collide with any registered or archived code; the registry (this document) is checked first (`31_Master_Index.md` §4.4 minimum read).
3. **Convention compliance:** `PREFIX_DESCRIPTOR` in SCREAMING_SNAKE_CASE with a §3.2 prefix (or a newly justified prefix added the same way); product vocabulary; stable meaning.
4. **Status discipline:** the new code must map to one of the eight documented statuses; if genuinely new failure *semantics* require a new status, that is a contract-level change needing a future API version, not a quiet addition.
5. **Message pairing:** the user message follows §24 policy; the internal message follows §25 policy; both are registered with the code.
6. **Never reassigned:** when behavior is retired, its code is marked **Archived** in this registry (history preserved; codes are never recycled) — the same Archive-not-delete philosophy as the product.
7. **PENDING-protected:** no code may encode outcomes of Q-005 (enforcement), Q-010 (video hosting/protection), Q-011 (staff granularity), Q-012 (Super Admin content visibility), Q-013 (pricing model), or Q-015 (localization) before their resolutions are documented.

## 27.2 Anticipated Future Families (Not Registered — Placeholders Only)

The following families are *named in advance only to prevent collisions*; they register no codes and grant no capability. Each activates only after its owning question/scope is confirmed in documentation:

| Future area | Blocked prefix | Activation precondition |
|---|---|---|
| Notification delivery (future scope) | `NOTIFICATION_` delivery codes | An approved future notification feature and endpoints (today: `BUSINESS_NOTIFICATION_UNSUPPORTED` only). |
| Online payment integration (future scope) | Payment-integration codes | An approved payment-processing scope (today: `PAYMENT_PROCESSING_UNSUPPORTED` only). |
| Lesson video hosting/protection | `LESSON_VIDEO_*` mechanics codes | Resolution of Q-010. |
| Non-payment enforcement | `SUBSCRIPTION_ENFORCEMENT_*` outcome codes | Resolution of Q-005. |
| Tiered pricing | `PRICING_TIER_*` | Resolution of Q-013. |
| Localization | Localized message variants only | Resolution of Q-015; codes themselves remain language-independent. |

## 27.3 Version 1 Exclusion Guard

Version 1 scope exclusions (native mobile, payment gateways, notifications, marketplace/course discovery, video Homework, multiple Teaching Subjects, multiple Parents per Student, Platform staff roles, Docker, Redis, Kubernetes, S3 Storage, WebSockets, Microservices) must not acquire acceptance path codes; their only registered outcomes are the §12/§16/§19 rejection codes consolidated here.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority; this registry consolidates documented rejections/failures and never contradicts an owning document; `10_API_Design.md` §6/§10, `28_Coding_Standards.md` §15–§16, and `23_Security_Standards.md` §18 remain authoritative for envelope, statuses, and message policy. |
| All documents read | Passed — every authored document in `AI_DOCS/` (`00`–`33`) was read in full or in complete relevant part before authoring; error-owning documents (`10`, `12`–`13`, `15`–`23`, `21`, `28`, `33`) were read in full for error content. |
| Required sections | Passed — all 27 requested sections are present in the requested order: Document Purpose; Error Handling Philosophy; Error Code Structure; HTTP Status Code Standards; Authentication; Authorization; Validation; Teacher Module; Student Module; Parent Module; Attendance; Homework; Exam; Question Bank; Subscription; Payment; File Upload; Search; API; Database; Queue & Background Job; System; Logging Requirements; User-Friendly Error Messages; Internal Error Messages; Error Response Standards; Future Error Codes. |
| Required per-error fields | Passed — every registered error carries Error Code, HTTP Status, Error Name, Description, User Message, Internal Message, Possible Causes, Recommended Resolution, and Related Documents; internal-only errors carry an explicit Internal status explanation instead of an HTTP code. |
| Code uniqueness and naming | Passed — 100+ codes, each SCREAMING_SNAKE_CASE `PREFIX_DESCRIPTOR`, each defined exactly once with cross-references elsewhere; `VALIDATION_FAILED` preserved exactly as documented; uniqueness verified programmatically against the registry. |
| Documented status fidelity | Passed — every documented status mapping is honored: Teaching Subject change 422; Parent modification 403; cross-Teacher 403; payment processing 422; hard delete 422; unsupported file format 422; notification request 404; duplicate Student/second Group 409; login/reset 429; 422 envelope `VALIDATION_FAILED` with field-level `errors`. |
| Message separation | Passed — every code separates a §24-compliant user message from a §25-compliant internal message; no user text contains internals; no internal text contains secrets or private content; 500 family user message is uniformly "An unexpected error occurred." |
| Non-disclosure discipline | Passed — authentication, authorization, not-found, rate-limit, and file/QR/identity paths follow the documented non-disclosure rules (attempted identifier without account confirmation; existence indistinguishability; no thresholds; no QR/storage internals). |
| PENDING/PROPOSED protection | Passed — no code hardens Q-005, Q-010, Q-011, Q-012, Q-013, or Q-015; PROPOSED mechanics (snapshots, dedupe policy, QR expiry mechanics) have rejection-only or no codes; future families are named solely to block collisions. |
| Terminology | Passed — canonical terms only (Teacher Workspace, Educational Grade, Pricing Type, Teaching Subject, Attendance, Dynamic QR Code, ID Card, Homework, Question Bank, Bubble Sheet, Lesson, Exam, Enrollment, Archive, Audit Log, Subscription, payment status, Flow A, Flow B, Billable Student, Billing Cycle, Student Switcher); prohibited terms appear only as prohibited examples. |
| Relationship to 32/33 | Passed — business rules remain consolidated in `32_Business_Rules.md`; validation conditions and field-message texts remain consolidated in `33_Validation_Rules.md`; this document references both and adds no new rules or conditions. |
| Laravel 12 alignment | Passed — codes align with the documented exception-mapping layer, Form Request validation, Gates/Policies/RBAC denials, Database Queue failed-jobs handling, and scheduler behavior, at specification level with no source code. |
| Logging/Audit separation | Passed — operational logging content/format/prohibitions (`28` §16) and Audit Log obligations (`00` §10) are kept distinct; job failure recording and audit obligations for jobs follow `21_Background_Jobs.md`; no failure produces notifications. |
| Version 1 scope | Passed — out-of-scope capabilities have rejection codes only; no acceptance-path codes exist for excluded features; §27.3 states the guard explicitly. |
| Scope exclusions | Passed — no source code, no APIs (endpoint strings only cited), no database tables/SQL, no UI implementation, no physical configuration. |
| Governance registration | Passed — this document carries a Document Scope with scope exclusions and this closing consistency review per `31_Master_Index.md` §13.5; corresponding registrations were applied in `31_Master_Index.md` §15 and `04_Project_Structure.md` §8. |

---

*End of document. **REVISION 1.0** — This file is the official registry of application error codes for the Unified Education Platform Version 1: one stable code per failure, one HTTP status per code, a safe message for users, a diagnosable message for operators — and never a leak of scope, existence, or internals. `00_Project_Context.md` remains the Single Source of Truth.*


## Confirmed Audit Error Conditions

Use the existing validation/conflict response conventions for: a duplicate or already-linked Parent–Student link, an unauthorized link/unlink approval, a link request without active Enrollment, a Per Lesson obligation without a completed Lesson or active Group Enrollment, and a duplicate Student/Lesson obligation. Messages must remain localized in Arabic and English and use canonical terminology.
