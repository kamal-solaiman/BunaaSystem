# 33 — Validation Rules

## Document Scope

This document is the **single authoritative reference for all validation rules** used throughout the Unified Education Platform Version 1.

It consolidates validation requirements already defined across the official documentation set — `00_Project_Context.md` (canonical rules, policies, terminology), `02_Software_Requirements.md` (per-module Validations sections), `06_Database_Design.md` (integrity rules), `07_Data_Dictionary.md` (attribute-level validation), `10_API_Design.md` (request and response validation), `12_Frontend_Architecture.md` (client validation strategy), `13_UI_UX_Guidelines.md` and `14_UI_Components.md` (validation presentation), `15`–`23` (feature-domain validation), `28_Coding_Standards.md` (validation standards), and `29_Project_Decisions.md` (D-019, D-020, D-021) — and defines validation standards at the specification level without contradicting any of them.

**Consolidation, not new authority.** This document defines validation standards; it does **not** create new business rules, new fields, new limits, new formats, or new features. Where a limit, format catalog, or mechanic is not confirmed by an owning document (exact string maxima, file-size limits, video codecs, MIME catalogs, attempt limits, availability fields), this document records the requirement as *bounded but deferred* and must never fabricate a value. Canonical rule definitions remain owned by `00_Project_Context.md` §9 and by the subject-owning documents per `31_Master_Index.md` §9.2. `00_Project_Context.md` is the Single Source of Truth and prevails over this document if a conflict is found.

**Validation identifiers (GEN-xx, AUT-xx, TCH-xx, …)** in this document are reference identifiers for traceability inside this catalog and in reviews/tests. They are not BR-xxx business rules and create no new canonical rules.

**Scope exclusions.** This document does not provide:

- Source code of any kind, in any language.
- Form Requests (no Form Request classes, no rule arrays, no class names beyond the documented layer convention).
- APIs (no endpoint definitions; the API contract is owned by `10_API_Design.md`).
- Database tables, columns, migrations, indexes, or SQL.
- UI implementation, component code, or styling.
- New business rules (inventing them is explicitly prohibited; see `32_Business_Rules.md` §1–§2).
- Physical configuration (server, PHP, or framework configuration values).

Where technology is named, it matches the confirmed baseline only: Laravel 12, PHP 8.3, React 19, TypeScript, Vite, Tailwind CSS, MySQL 8, Laravel Sanctum, Gates & Policies with Custom RBAC, File Cache, Database Queue, Database session driver, Laravel Public Storage, Laravel Scheduler with Cron Jobs, SMTP, Apache/LiteSpeed, cPanel Shared Hosting, React Hook Form, and Zod.

---

# 1. Document Purpose

This document exists so that **every input validation decision in the product has exactly one place to be looked up** — by AI assistants, developers, reviewers, and testers — without reading twenty documents or guessing.

Specifically, it:

1. Consolidates all field-level, request-level, and integrity validation requirements already approved in `AI_DOCS/00`–`32`.
2. Defines the validation standards (layers, Laravel 12 alignment, frontend/backend consistency, error-response shape) that apply uniformly to every form and endpoint, restating `28_Coding_Standards.md` §17 and `23_Security_Standards.md` §9–§10.
3. Records, for each rule: **Validation ID, Field Name, Validation Rule, Required / Optional, Allowed Values, Minimum / Maximum Limits, Error Message, and Related Documents.**
4. Records where validation is intentionally *not* hardened — PENDING decisions (Q-005, Q-010, Q-011, Q-012, Q-013, Q-015) and deferred limits — so no validator silently invents product behavior.
5. Provides a closing checklist (§24) used during implementation and review.

This document is a reference for **humans and AI sessions implementing or reviewing validation**. It is not the source of truth for business rules (`32_Business_Rules.md` consolidates those; `00_Project_Context.md` §9 defines the canonical BR-xxx set), for the API contract (`10_API_Design.md`), or for security requirements (`23_Security_Standards.md`). On any conflict, the owning document prevails per `31_Master_Index.md` §10.

**Audience:** AI assistants performing validation work, backend developers, frontend developers, reviewers, and testers.

---

# 2. Validation Philosophy

**2.1 The backend is the only authority.** All validated outcomes — authentication, authorization, Teacher Workspace isolation, ownership, relationships, Archive state, duplicates, and business rules — are decided by the Laravel 12 backend (`11_Backend_Architecture.md`; D-021). The frontend validates to guide, never to guarantee: client-side validation (Zod schemas with React Hook Form, D-019) provides immediate structural feedback but is never a security boundary and never replaces backend validation (`12_Frontend_Architecture.md` §10–§11; `28_Coding_Standards.md` §17.3).

**2.2 Frontend and backend validation are consistent by construction.** Frontend Zod schemas mirror the *confirmed structural* rules in this catalog (required fields, formats, ranges, enum sets, file selection constraints); backend Form Requests enforce them authoritatively together with existence, scope, duplicate, Archive-state, and business-rule checks that the client cannot safely know (`12_Frontend_Architecture.md` §11). A rule that cannot be confirmed from documentation must appear in **neither** layer.

**2.3 Validation is layered (defense in depth).** Every input passes the four documented layers (`23_Security_Standards.md` §10.1; `28_Coding_Standards.md` §17.1):

| Layer | Responsibility |
|---|---|
| Request validation (Form Requests) | Required fields, formats, data types, enum values, date ranges, file rules. |
| Authorization validation | Role, scope, ownership, permission, linked relationship, Teacher Workspace access. |
| Business validation | Confirmed rules: no duplicate Students, one Group per Student per Teacher, Teaching Subject immutability, Flow A/Flow B separation, Archive policy. |
| Persistence integrity | Prevent invalid saved state and preserve logical relationships. |

**2.4 Fail closed, fail honestly.** Invalid input is rejected; the task is never shown as successful before backend confirmation (`13_UI_UX_Guidelines.md`). Conflicts with business rules or current state return HTTP 409; input rule violations return HTTP 422; missing authentication returns 401; missing authorization returns 403; invisible or missing resources return 404 (`10_API_Design.md` §6).

**2.5 Validation protects privacy.** Validation must not reveal whether an unauthorized resource exists, whether an account exists, or anything about another Teacher Workspace, an unlinked Student, or another Student (`23_Security_Standards.md` §10.4). Error messages explain the correction needed — no more (`12_Frontend_Architecture.md` §11).

**2.6 No invented constraints.** A validator may only enforce rules traceable to the official documents. Exact limits the documents deliberately leave open (§3.4, §23) must not be filled in by code, by a UI hint, or by a test (`31_Master_Index.md` §2.4).

**2.7 PENDING is never hardened.** Validators must not encode outcomes for Q-005 (non-payment enforcement), Q-010 (Lesson video hosting/protection), Q-011 (Teacher Staff permission granularity), Q-012 (Super Admin content visibility), Q-013 (flat price or volume tiers), or Q-015 (timezone/currency) (`28_Coding_Standards.md` §17.3; `31_Master_Index.md` §10.5; §23 of this document).

**2.8 Archive-aware validation.** Validation distinguishes active from archived state: archived records are rejected as *active* targets (assignment, upload, composition, attempt) while remaining valid as historical/report data, clearly indicated (`00_Project_Context.md` §11; `32_Business_Rules.md` §24).

---

# 3. General Validation Standards

**Authoritative sources:** `28_Coding_Standards.md` §17; `23_Security_Standards.md` §10; `10_API_Design.md` §6 and §10; `12_Frontend_Architecture.md` §10–§11; `13_UI_UX_Guidelines.md` §9.

## 3.1 Placement And Laravel 12 Alignment

The following standards restate the confirmed coding and architecture conventions (`28_Coding_Standards.md` §3, §17; `11_Backend_Architecture.md`) in validation terms. They describe *where* validation lives and *which Laravel 12 validator capabilities* implement each standard; they do not introduce code.

1. **Request validation lives in Form Requests.** Form Request classes are grouped by feature under `app/Http/Requests/` and named after the resource and action they validate. They validate required inputs, enum values, date ranges, file rules, and resource references.
2. **Form Requests may perform early authorization checks but never replace Policies, Gates, or Custom RBAC.** Validation and authorization are distinct responsibilities that work together.
3. **Controllers receive already-validated requests**; they do not own business validation. Services own business workflows, including business-rule validation, persistence, Archive behavior, and Audit Log recording.
4. **The Laravel 12 built-in validator is the only request-validation mechanism.** The confirmed mapping between the input standards in §3.2 and Laravel validator capabilities is:

| Input standard | Laravel 12 validator capability (reference) |
|---|---|
| Required presence | `required`; presence-with-emptiness variants (`present`, `filled`) where semantics differ; conditional presence via `required_if`-style conditional rules. |
| Optional fields | `nullable` for optional values that are validated when present. |
| Text with bound | `string` with `max`; a lower bound where documented via `min`. |
| Numeric / money | `numeric` or `integer` with `min:0`-style non-negativity where documented. |
| Identifiers | `integer` with positive range. |
| Enum membership | `in` (or the enum-rule equivalent) restricted to confirmed sets (§3.3). |
| Dates | `date` / `date_format`; range ordering via `after_or_equal` / `before_or_equal` style date-comparison rules. |
| Existence in scope | `exists` constrained to the authorized scope (e.g., current Teacher Workspace). |
| Uniqueness in scope | `unique` constrained to the confirmed uniqueness boundary (global for Student identity, login identity). |
| Files | `file` with `mimes` / `mimetypes` restricted to the confirmed owning-context types, size rules only where approved. |
| Prohibited input | `prohibited`-style rejection for inputs a confirmed rule forbids (e.g., notification payloads, payment-gateway fields, Teaching Subject changes). |

5. **Server-side enforcement of every confirmed rule is mandatory even when the frontend validates the same rule.** Where the two layers could diverge, the backend definition in this document is the one that ships (`12_Frontend_Architecture.md` §11.3: documented 422 responses are mapped back to fields).
6. **Eloquent/Query Builder receive only validated input**; expected types (integer IDs, date strings, enum values) are validated before database interaction as part of SQL-injection defense (`23_Security_Standards.md` §11).

## 3.2 Cross-Cutting Input Standards (GEN Rules)

### GEN-01 — Required Field Presence
- **Validation ID:** GEN-01
- **Field Name:** Any field marked "Required" by its owning document
- **Validation Rule:** The field must be present and non-empty. An absent required field, null, or empty string fails validation.
- **Required / Optional:** Required (by definition of the rule)
- **Allowed Values:** Any value satisfying the field's own rule.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The {Field Name} field is required."
- **Related Documents:** `28_Coding_Standards.md` §17.2; `23_Security_Standards.md` §10.2; `07_Data_Dictionary.md` (Required / Optional per attribute).

### GEN-02 — Data Type Conformance
- **Validation ID:** GEN-02
- **Field Name:** All input fields
- **Validation Rule:** Values must match the logical data type in `07_Data_Dictionary.md` (Text, Identifier, Reference, Date, DateTime, Money, Number, Enum, Status, Secret, Structured Data, Storage Reference). Type coercion of invalid shapes is not permitted.
- **Required / Optional:** Applies to every field.
- **Allowed Values:** Type-conformant values only.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The {Field Name} format is invalid."
- **Related Documents:** `07_Data_Dictionary.md` (all entities); `28_Coding_Standards.md` §17.2; `23_Security_Standards.md` §10.2, §11.

### GEN-03 — Enum And Status Membership
- **Validation ID:** GEN-03
- **Field Name:** Any Enum or Status field
- **Validation Rule:** The value must be a member of the confirmed set for that field (§3.3). Values outside the confirmed set are rejected; unconfirmed values must not be accepted "just in case".
- **Required / Optional:** Required where the enum field is required.
- **Allowed Values:** Exactly the confirmed sets in §3.3.
- **Minimum / Maximum Limits:** Exactly one value unless the owning document defines a multi-value field (none confirmed).
- **Error Message:** "The selected {Field Name} is invalid."
- **Related Documents:** `28_Coding_Standards.md` §17.2; `23_Security_Standards.md` §10.2; `07_Data_Dictionary.md` per-entity enums.

### GEN-04 — Bounded Text
- **Validation ID:** GEN-04
- **Field Name:** All Text fields (names, titles, descriptions, schedules)
- **Validation Rule:** Text input must be bounded to prevent excessive storage and processing. Where an owning document defers the exact maximum to later detailed design, the validator enforces a bounded maximum chosen at physical design time and this document does not prescribe the number.
- **Required / Optional:** Applies to all Text fields.
- **Allowed Values:** Human-readable text; no internal identifiers, storage paths, or markup required by no document.
- **Minimum / Maximum Limits:** Minimum per owning document where stated; **maximum bounded but deferred** — do not display a fabricated number to users.
- **Error Message:** "The {Field Name} must not exceed the allowed length." (and "The {Field Name} is required." when empty on a required field)
- **Related Documents:** `23_Security_Standards.md` §10.2; `28_Coding_Standards.md` §17.2; `02_Software_Requirements.md` (fields deferred to "later detailed requirements").

### GEN-05 — Valid Dates
- **Validation ID:** GEN-05
- **Field Name:** All Date / DateTime fields and date filters (`Session Date`, `QR Date`, `Enrollment Start`, `Enrollment End`, `from_date`, `to_date`, Billing Cycle boundaries)
- **Validation Rule:** A date value must be a valid calendar date. System-generated timestamps (Created At, Recorded At, Occurred At, Generated At) are never accepted from client input.
- **Required / Optional:** Per owning document; date-range filters optional.
- **Allowed Values:** Valid calendar dates.
- **Minimum / Maximum Limits:** See GEN-06 for ordering; no confirmed past/future restriction exists unless an owning document states one.
- **Error Message:** "The {Field Name} must be a valid date."
- **Related Documents:** `23_Security_Standards.md` §10.2; `28_Coding_Standards.md` §17.2; `07_Data_Dictionary.md` date attributes.

### GEN-06 — Date Range Ordering
- **Validation ID:** GEN-06
- **Field Name:** Date-range pairs (`from_date`/`to_date`, report Start Date/End Date, `Enrollment Start`/`Enrollment End`, Billing Cycle start/end)
- **Validation Rule:** The range start must not be after the range end. `Enrollment End` must be on or after `Enrollment Start` when present. A Billing Cycle must start on the first day and end on the last day of the same calendar month.
- **Required / Optional:** Both ends optional individually where the owning document allows; the ordering rule applies whenever both are present.
- **Allowed Values:** Valid dates honoring start ≤ end; Billing Cycle = calendar month only.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The start date must not be after the end date."
- **Related Documents:** `23_Security_Standards.md` §10.2; `28_Coding_Standards.md` §17.2; `22_Search_Filtering.md` (range filters); `07_Data_Dictionary.md` §12, §32; `17_Subscription_Billing.md`.

### GEN-07 — Email Format Where Used
- **Validation ID:** GEN-07
- **Field Name:** Any field carrying an email address
- **Validation Rule:** Where email is used, the value must be a valid email format. (The documents confirm the standard but do not enumerate which specific fields carry email; no field is declared "email" by this catalog on its own authority.)
- **Required / Optional:** Per owning document.
- **Allowed Values:** Valid email format.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The {Field Name} must be a valid email address."
- **Related Documents:** `23_Security_Standards.md` §10.2; `28_Coding_Standards.md` §17.2.

### GEN-08 — Numeric Ranges And Money
- **Validation ID:** GEN-08
- **Field Name:** All Number and Money fields (`Price`, `Amount`, `Price Per Student`, `Billable Student Count`, identifiers)
- **Validation Rule:** Values must be within valid ranges: identifiers are positive integers; counts are non-negative integers; money values are valid monetary amounts — non-negative where the owning document requires it (`Price`: "Must be valid non-negative monetary value according to later detailed rules", `07_Data_Dictionary.md` §10).
- **Required / Optional:** Per owning document.
- **Allowed Values:** Positive integers (IDs); non-negative integers (counts); valid monetary amounts.
- **Minimum / Maximum Limits:** Minimum 0 (Price); minimum 1 (positive identifiers); no confirmed upper bounds — none may be fabricated.
- **Error Message:** "The {Field Name} must be a valid amount." / "The {Field Name} must be zero or more."
- **Related Documents:** `23_Security_Standards.md` §10.2; `28_Coding_Standards.md` §17.2; `07_Data_Dictionary.md` §10, §25, §26, §33.

### GEN-09 — Scoped Existence (References)
- **Validation ID:** GEN-09
- **Field Name:** All Reference fields (e.g., `Educational Grade Reference`, `Group Reference`, `Student Reference`, `Question Bank Reference`)
- **Validation Rule:** A reference must identify an existing record **inside the authorized scope** — the current Teacher Workspace for workspace records, linked Students for a Parent, own account for a Student, Platform scope for a Super Admin. A reference to an existing record outside the authorized scope fails validation exactly like a nonexistent reference (no existence disclosure, GEN-12).
- **Required / Optional:** Required unless the owning document marks the reference Optional (e.g., Homework `Group Reference`).
- **Allowed Values:** Existing, in-scope, non-archived-for-active-use records (see GEN-11).
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected {Field Name} is invalid." (neutral — never "exists but belongs to another Teacher")
- **Related Documents:** `10_API_Design.md` §4, §8 (filter scoping); `23_Security_Standards.md` §10.4; `06_Database_Design.md` §12–§13; `32_Business_Rules.md` §5 (BR-003).

### GEN-10 — Confirmed Uniqueness
- **Validation ID:** GEN-10
- **Field Name:** Identity and identifier fields (`Login Identifier`, Student global identity, entity identifiers)
- **Validation Rule:** Uniqueness is enforced exactly at confirmed boundaries: entity identifiers are unique; `Login Identifier` is unique according to authentication rules; a Student has exactly one global account (BR-001, BR-022) enforced server-side. No additional uniqueness (e.g., unique Educational Grade names per workspace) is confirmed — none may be invented.
- **Required / Optional:** Applies to the named fields.
- **Allowed Values:** Values not already taken within the confirmed uniqueness boundary.
- **Minimum / Maximum Limits:** —
- **Error Message:** "An account with this identity already exists." (Student identity/login identity; no disclosure of where it exists)
- **Related Documents:** `07_Data_Dictionary.md` §1, §6; `00_Project_Context.md` §9 (BR-001, BR-022); `32_Business_Rules.md` §4, §6; `23_Security_Standards.md` §3.4.

### GEN-11 — Archive-State Awareness
- **Validation ID:** GEN-11
- **Field Name:** Any reference or target field pointing at an archivable record
- **Validation Rule:** An archived record is not a valid **active** target: archived Educational Grades cannot be used in active Group assignment; archived Groups cannot receive new active assignments; archived Homework/Lesson/Exam/Question cannot be active content, upload targets, attempt targets, or active composition members. Archived records remain valid historical/report subjects, clearly indicated. Restore (not re-validation bypass) returns a record to active validity.
- **Required / Optional:** Applies to all archivable targets.
- **Allowed Values:** Active records for active operations; archived records only in historical/restore contexts.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected {Record} is not available." (for active operations); restore path handles archived records explicitly.
- **Related Documents:** `00_Project_Context.md` §11; `10_API_Design.md` §2, §8; `07_Data_Dictionary.md` (Archived State on every archivable entity); `32_Business_Rules.md` §24.

### GEN-12 — Non-Disclosure On Failure
- **Validation ID:** GEN-12
- **Field Name:** All fields (cross-cutting)
- **Validation Rule:** Validation must not reveal whether an unauthorized resource exists, whether an account exists, or anything about another Teacher Workspace, an unlinked Student, or another Student. Existence/scope failures use neutral messages; unauthorized-resource access and not-found produce indistinguishable outcomes per `10_API_Design.md` §6 (404).
- **Required / Optional:** Applies to every rule.
- **Allowed Values:** Neutral messages only (§22).
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected {Field Name} is invalid." / "The provided credentials are incorrect."
- **Related Documents:** `23_Security_Standards.md` §10.4, §3.3; `10_API_Design.md` §6; `12_Frontend_Architecture.md` §11.

## 3.3 Canonical Enumeration Sets

Every enum/`in`-style validator in the product must use exactly these confirmed sets (and the per-entity Status values documented in `07_Data_Dictionary.md`). Adding a value requires a documentation change first.

| Enumeration | Confirmed Allowed Values | Owning source |
|---|---|---|
| Role | Super Admin, Teacher, Teacher Staff, Student, Parent | `07_Data_Dictionary.md` §2; `00_Project_Context.md` |
| Role Scope | Platform, Teacher Workspace, Student Account, Parent Linked Students | `07_Data_Dictionary.md` §2 |
| Pricing Type | Monthly, Per Lesson | `07_Data_Dictionary.md` §10, §33; BR-009 |
| Question Type | Multiple Choice, True/False, Essay, Bubble Sheet | `07_Data_Dictionary.md` §21; BR-011 |
| Homework Supported Format | Text, Image, PDF | `07_Data_Dictionary.md` §16; BR-021 |
| Homework Submission Format | Text, Image, PDF (binary upload: Image or PDF) | `07_Data_Dictionary.md` §17; `20_File_Storage.md` §3 |
| Attendance Method / Method Context | Dynamic QR Code, ID Card, Manual | `07_Data_Dictionary.md` §13, §15; BR-010 |
| Payment Flow | Flow A, Flow B | `07_Data_Dictionary.md` §25 |
| Student Created By Method | Self-Registration, Teacher-Created | `07_Data_Dictionary.md` §6 |
| Audit Log Scope Context | Platform, Teacher Workspace | `07_Data_Dictionary.md` §27 |
| Audit Log Event Type | Create, Update, Archive, Restore, Login, Permission Change, Attendance Change, Exam Modification, Homework Modification, Subscription Change | `00_Project_Context.md` §10 |
| List `status` filter values | Active, archived, pending, submitted, paid, unpaid, or other valid resource status per endpoint | `10_API_Design.md` §8 |

## 3.4 Confirmed-But-Deferred Limits (Do Not Invent)

The documents deliberately leave these without confirmed values. Validation must keep them *open and bounded where safety requires*, but must not present a fabricated product number anywhere (validator, UI helper text, tests, or docs):

- Exact maximum lengths for names, titles, and descriptions ("later detailed requirements" / physical design).
- File-size limits (`20_File_Storage.md` §12: "No file-size limit is confirmed for Version 1"; apply only an approved future limit; still validate large files early to protect hosting limits, `23_Security_Standards.md` §9.4).
- Exact image formats, PDF versions, video codecs, MIME-type catalog (`20_File_Storage.md` §3).
- Login rate-limit threshold values (rate limiting is confirmed; the numeric threshold is not).
- Pagination `per_page` exact bounds (must be "within allowed limits", `10_API_Design.md` §7; the bounds themselves are documented behavior, not a product number this catalog may fix).
- Schedule description format (`07_Data_Dictionary.md` §11: "valid format defined later").
- Exam availability fields, publication criteria, attempt limits (`15_Exam_Engine.md`).
- Currency, date/number localization (Q-015 PENDING).

**Cross-referenced rules:** SEC rules in `32_Business_Rules.md` §23 (backend sole authority), BR-005/§24 (Archive), and each domain section below; validation-layer placement from `11_Backend_Architecture.md` is restated, not re-owned, here.

---

# 4. Authentication Validation

**Authoritative sources:** `23_Security_Standards.md` §3 (Authentication Security), §6 (Password Policy), §7 (Session Management); `10_API_Design.md` §3, §13 (Authentication Endpoints); `07_Data_Dictionary.md` §1 (User), §6 (Student); `02_Software_Requirements.md` Part 1/Part 3 (registration, activation); `05_User_Flows.md`; `32_Business_Rules.md` §3–§4.

### AUT-01 — Login Identifier
- **Validation ID:** AUT-01
- **Field Name:** Login Identifier
- **Validation Rule:** The login identifier is required and must be valid text according to authentication rules. The backend validates credentials server-side; the frontend sends credentials and never makes authentication decisions. A nonexistent identifier produces the same generic failure as a wrong secret (no account-existence disclosure).
- **Required / Optional:** Required.
- **Allowed Values:** Text conforming to account format; uniqueness per GEN-10.
- **Minimum / Maximum Limits:** Bounded text (GEN-04); confirmed account-format bounds are authentication-baseline (`00_Project_Context.md` §12, PROPOSED mechanics) — no extra format is invented here.
- **Error Message:** "The Login Identifier field is required." (failure of credential check: see AUT-04)
- **Related Documents:** `07_Data_Dictionary.md` §1; `23_Security_Standards.md` §3; `10_API_Design.md` §13; `32_Business_Rules.md` §3.

### AUT-02 — Authentication Secret (At Login)
- **Validation ID:** AUT-02
- **Field Name:** Authentication Secret
- **Validation Rule:** The authentication secret is required. It is validated server-side only; it must never be logged, returned in responses, or included in error messages. No client-side "password correctness" logic exists.
- **Required / Optional:** Required.
- **Allowed Values:** Secret text.
- **Minimum / Maximum Limits:** Composition rules apply at secret set/change time (AUT-03), never reported at login.
- **Error Message:** "The Authentication Secret field is required." (wrong-secret attempts always yield AUT-04's generic failure)
- **Related Documents:** `07_Data_Dictionary.md` §1; `23_Security_Standards.md` §3.6, §6.3; `12_Frontend_Architecture.md` §11.

### AUT-03 — Secret Composition (Set / Change / Reset)
- **Validation ID:** AUT-03
- **Field Name:** Authentication Secret (new value)
- **Validation Rule:** Minimum 8 characters; must contain at least one uppercase letter, one lowercase letter, and one digit; special characters are recommended but not mandatory. Enforced server-side. Hashing uses bcrypt or Argon2id through Laravel's hashing mechanisms; plain-text storage, reversible encryption, and weak hashes (MD5, SHA1) are prohibited.
- **Required / Optional:** Required when a secret is set, changed, or reset.
- **Allowed Values:** Strings satisfying the composition rule.
- **Minimum / Maximum Limits:** Minimum 8 characters (confirmed); no confirmed maximum.
- **Error Message:** "The Authentication Secret must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, and one digit."
- **Related Documents:** `23_Security_Standards.md` §6.1, §6.3; `24_Testing_Strategy.md` (password policy coverage).

### AUT-04 — Credential Check Outcome
- **Validation ID:** AUT-04
- **Field Name:** Login Identifier + Authentication Secret (pair)
- **Validation Rule:** A failed credential check returns a generic failure (HTTP 401) that does not reveal whether the account exists, whether the secret was wrong, or whether the account is archived. No field-level "unknown identifier" or "wrong password" message is ever produced.
- **Required / Optional:** —
- **Allowed Values:** Authenticated context on success; generic 401 on failure.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The provided credentials are incorrect."
- **Related Documents:** `23_Security_Standards.md` §3.3; `10_API_Design.md` §6, §13; `32_Business_Rules.md` §3.

### AUT-05 — Login Rate Limiting
- **Validation ID:** AUT-05
- **Field Name:** Login attempts (endpoint-level)
- **Validation Rule:** Login endpoints must be rate-limited to prevent brute-force attacks; exceeding the limit produces HTTP 429. The numeric threshold and window are **not confirmed** and must not be presented as product values.
- **Required / Optional:** Always applied to authentication endpoints.
- **Allowed Values:** Attempts within the configured limit.
- **Minimum / Maximum Limits:** Limit confirmed to exist; exact threshold deferred (§3.4).
- **Error Message:** "Too many login attempts. Please try again later."
- **Related Documents:** `23_Security_Standards.md` §3.3; `10_API_Design.md` §6, §13.

### AUT-06 — Login Auditing
- **Validation ID:** AUT-06
- **Field Name:** Login event (success and failure)
- **Validation Rule:** Successful and failed login events must be recorded in the Audit Log; failures record the attempted identifier (without exposing whether the account exists), timestamp, IP address, and device/client information. Audit creation never exposes secrets (AUT-02).
- **Required / Optional:** Mandatory side effect of every login attempt.
- **Allowed Values:** Audit event type Login (`00_Project_Context.md` §10).
- **Minimum / Maximum Limits:** —
- **Error Message:** — (not a user-facing rule)
- **Related Documents:** `00_Project_Context.md` §10; `23_Security_Standards.md` §3.3; `10_API_Design.md` §13; `32_Business_Rules.md` §25.

### AUT-07 — Password Reset Request
- **Validation ID:** AUT-07
- **Field Name:** Reset identifier (account reference supplied by the requester)
- **Validation Rule:** Reset requests must not reveal whether an account exists for the provided identifier; requests are rate-limited; the response is identical for existing and non-existing identifiers.
- **Required / Optional:** Required to initiate a reset.
- **Allowed Values:** Any identifier; outcome is non-disclosing.
- **Minimum / Maximum Limits:** Request rate limit applies (values unconfirmed, §3.4).
- **Error Message:** "If an account exists for the provided identifier, reset instructions have been sent."
- **Related Documents:** `23_Security_Standards.md` §6.2; `32_Business_Rules.md` §3.

### AUT-08 — Password Reset Token
- **Validation ID:** AUT-08
- **Field Name:** Reset token
- **Validation Rule:** The reset token must be time-limited and single-use through the confirmed communication channel; expired or reused tokens are rejected. Successful resets are recorded in the Audit Log, and old sessions are invalidated after a reset.
- **Required / Optional:** Required to complete a reset.
- **Allowed Values:** Unexpired, unused, correctly-scoped tokens.
- **Minimum / Maximum Limits:** Token must "expire after a defined period"; the exact period is configuration, not a product value this catalog fixes.
- **Error Message:** "This reset link is invalid or has expired."
- **Related Documents:** `23_Security_Standards.md` §6.2; `00_Project_Context.md` §10.

### AUT-09 — Temporary Secret On First Login
- **Validation ID:** AUT-09
- **Field Name:** Authentication Secret (first login)
- **Validation Rule:** Default or temporary secrets must be changed on first login. Completing the change is a precondition to normal authenticated use of the account.
- **Required / Optional:** Required where a temporary secret was issued.
- **Allowed Values:** New secret satisfying AUT-03.
- **Minimum / Maximum Limits:** As AUT-03.
- **Error Message:** "You must set a new Authentication Secret before continuing."
- **Related Documents:** `23_Security_Standards.md` §6.3.

### AUT-10 — Password History (Non-Rule)
- **Validation ID:** AUT-10
- **Field Name:** Authentication Secret (new value)
- **Validation Rule:** Password history checking (preventing reuse of recent passwords) is **not confirmed** for Version 1 and must not be silently implemented — no validator, UI hint, or test may enforce it.
- **Required / Optional:** Not applicable (prohibited feature).
- **Allowed Values:** Any new secret satisfying AUT-03 (prior secrets are not remembered).
- **Minimum / Maximum Limits:** —
- **Error Message:** —
- **Related Documents:** `23_Security_Standards.md` §6.3.

### AUT-11 — Student Self-Registration Identity Fields
- **Validation ID:** AUT-11
- **Field Name:** Student identity and account information (registration payload)
- **Validation Rule:** Required identity and account fields must be present and valid, and must be **sufficient to prevent duplicate accounts** according to the identity requirements (detailed identity field catalog is deferred to later detailed requirements — `02_Software_Requirements.md`; this catalog adds no field). The public endpoint applies authentication-grade validation plus duplicate prevention.
- **Required / Optional:** Required (all registration identity fields).
- **Allowed Values:** Valid identity values within GEN standards.
- **Minimum / Maximum Limits:** Per GEN-04/GEN-07 as applicable.
- **Error Message:** "The {Field Name} field is required." / GEN-10 duplicate message on conflict.
- **Related Documents:** `02_Software_Requirements.md` Part 3 (Student account validations); `10_API_Design.md` §13 (`/auth/students/register`); `07_Data_Dictionary.md` §6; `32_Business_Rules.md` §4 (BR-022).

### AUT-12 — Student Global Duplicate Prevention
- **Validation ID:** AUT-12
- **Field Name:** Student identity (registration, Teacher-created creation, activation)
- **Validation Rule:** A new Student account must not duplicate an existing global Student account — checked server-side at self-registration, at Teacher-created account creation, and at activation. A duplicate is rejected with HTTP 409 without exposing where or with which Teacher the existing account studies.
- **Required / Optional:** Always enforced.
- **Allowed Values:** Identities with no existing Student account.
- **Minimum / Maximum Limits:** —
- **Error Message:** "An account with this identity already exists." (409)
- **Related Documents:** `00_Project_Context.md` §9 (BR-001, BR-022); `02_Software_Requirements.md` Part 2/Part 3; `10_API_Design.md` §13; `23_Security_Standards.md` §3.4; `32_Business_Rules.md` §6.

### AUT-13 — Teacher-Created Student Activation Data
- **Validation ID:** AUT-13
- **Field Name:** Activation identifier and account confirmation data
- **Validation Rule:** Activation applies only to the matching Teacher-created Student account: the supplied activation data must match that account (404/409 on mismatch), and activation must never create a duplicate Student account. Activation is the authentication exception path (`02_Software_Requirements.md`: account-setting access requires authentication "except for activation flows").
- **Required / Optional:** Required for activation.
- **Allowed Values:** Activation data matching exactly one pending-activation Teacher-created account.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The activation details do not match any account awaiting activation."
- **Related Documents:** `10_API_Design.md` §13 (`/auth/students/activate`); `07_Data_Dictionary.md` §6 (Activation Status: Active or Pending Activation); `02_Software_Requirements.md` Part 3; `32_Business_Rules.md` §4.

### AUT-14 — Session Validity On Protected Actions
- **Validation ID:** AUT-14
- **Field Name:** Authenticated session / token context
- **Validation Rule:** Every protected request must carry a valid authenticated context; missing or invalid context returns HTTP 401. Authentication is validated on every request — no endpoint trusts cached or frontend-provided authentication state. Sessions are destroyed on logout and invalidated on password change; session rotation on privilege escalation applies per session policy.
- **Required / Optional:** Required for all protected endpoints.
- **Allowed Values:** Valid Sanctum-authenticated contexts.
- **Minimum / Maximum Limits:** Idle and absolute session timeouts exist per session policy; exact values are configuration, not product numbers.
- **Error Message:** "Authentication is required." (401)
- **Related Documents:** `23_Security_Standards.md` §3, §7; `10_API_Design.md` §3, §6; `11_Backend_Architecture.md` (request pipeline).

**Cross-referenced rules:** AUT-11…AUT-13 implement the registration rules consolidated in `32_Business_Rules.md` §4 (BR-022 two registration methods; no duplicates). Teacher account creation fields are validated under §5 (TCH-01…TCH-03) because the confirmed creation authority is the Super Admin. Session security details are owned by `23_Security_Standards.md` §7 and restated here only as validation outcomes.

---

# 5. Teacher Validation

**Authoritative sources:** `02_Software_Requirements.md` Part 5 (Teacher Account Management validations) and Part 2 (Settings, Teacher Staff validations); `07_Data_Dictionary.md` §4 (Teacher), §30 (Teacher Staff), §31 (Teaching Subject), §5 (Teacher Workspace); `10_API_Design.md` §14, §26, §27; `32_Business_Rules.md` §5; Q-011 (Teacher Staff permission granularity — PENDING).

### TCH-01 — Teaching Subject Required At Creation
- **Validation ID:** TCH-01
- **Field Name:** Teaching Subject Reference
- **Validation Rule:** Teaching Subject is required at Teacher account creation — selected exactly once through the approved account-creation authority (Super Admin creates Teacher accounts). The account creation payload without a Teaching Subject is invalid.
- **Required / Optional:** Required at creation.
- **Allowed Values:** A valid Teaching Subject (examples: Mathematics, Physics, Chemistry, Biology, Arabic, English).
- **Minimum / Maximum Limits:** Exactly one Teaching Subject per Teacher account.
- **Error Message:** "The Teaching Subject field is required."
- **Related Documents:** `02_Software_Requirements.md` Part 5; `07_Data_Dictionary.md` §4, §31; `10_API_Design.md` §14; `32_Business_Rules.md` §5 (BR-016).

### TCH-02 — Teaching Subject Immutability
- **Validation ID:** TCH-02
- **Field Name:** Teaching Subject Reference
- **Validation Rule:** Any attempt to change the Teaching Subject after account creation must be rejected — at account update, at Teacher Workspace Settings update, and at Platform-level Teacher update alike. The UI states this consequence before submission; the backend rejects it regardless.
- **Required / Optional:** Prohibited after creation.
- **Allowed Values:** Unchangeable value set at creation.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The Teaching Subject cannot be changed after account creation."
- **Related Documents:** `02_Software_Requirements.md` Part 2 (Settings validations), Part 5; `10_API_Design.md` §14, §27; `13_UI_UX_Guidelines.md` §9; `32_Business_Rules.md` §5 (BR-016).

### TCH-03 — Teacher Account Information
- **Validation ID:** TCH-03
- **Field Name:** Teacher account information (creation and update payloads)
- **Validation Rule:** Teacher account information must be valid according to the detailed account requirements; required account fields must be present; updates apply only to allowed Teacher account fields within the actor's scope.
- **Required / Optional:** Required fields per account contract.
- **Allowed Values:** Values passing GEN standards; Teaching Subject excluded from updates (TCH-02).
- **Minimum / Maximum Limits:** GEN-04 bounds; detailed field requirements deferred by the owning requirements.
- **Error Message:** "The {Field Name} field is required." / "The {Field Name} format is invalid."
- **Related Documents:** `02_Software_Requirements.md` Part 5 ("valid according to later detailed requirements"); `10_API_Design.md` §14; `07_Data_Dictionary.md` §4.

### TCH-04 — Teacher Workspace Binding
- **Validation ID:** TCH-04
- **Field Name:** Teacher Workspace Reference
- **Validation Rule:** A Teacher account is associated with exactly one Teacher Workspace; workspace-owned input must reference the Teacher's own Teacher Workspace. Input referencing another Teacher Workspace fails as an invisible resource (GEN-09, GEN-12).
- **Required / Optional:** Required (system-associated at creation).
- **Allowed Values:** The Teacher's own Teacher Workspace.
- **Minimum / Maximum Limits:** Exactly one workspace per Teacher.
- **Error Message:** "The selected {Resource} is invalid."
- **Related Documents:** `07_Data_Dictionary.md` §4, §5; `06_Database_Design.md` §12; `32_Business_Rules.md` §5 (BR-003).

### TCH-05 — Teacher Workspace Settings Fields
- **Validation ID:** TCH-05
- **Field Name:** Teacher profile, center information, phone numbers, address
- **Validation Rule:** Settings inputs must be valid and bounded; updates are scoped to the current Teacher Workspace only and must not affect Platform-level settings or another Teacher Workspace. Teaching Subject updates inside Settings payloads are rejected (TCH-02).
- **Required / Optional:** Per settings contract; each supplied field validated.
- **Allowed Values:** Bounded text values; **no phone-number format is confirmed** — validators must not invent one.
- **Minimum / Maximum Limits:** GEN-04 bounds; no confirmed format patterns.
- **Error Message:** "The {Field Name} format is invalid."
- **Related Documents:** `02_Software_Requirements.md` Part 2 (Settings validations); `10_API_Design.md` §27; `07_Data_Dictionary.md` §5; `32_Business_Rules.md` §5.

### TCH-06 — Teacher Staff Identity Fields
- **Validation ID:** TCH-06
- **Field Name:** Teacher Staff user data (creation payload)
- **Validation Rule:** Teacher Staff account information must be sufficient and valid per the detailed requirements; the account is associated with exactly one creating Teacher Workspace; required identity fields must be present.
- **Required / Optional:** Required identity fields.
- **Allowed Values:** Values passing GEN standards; Staff Type Label is optional text (examples: Secretary, Assistant, Accountant).
- **Minimum / Maximum Limits:** Exactly one creating Teacher Workspace per Teacher Staff account.
- **Error Message:** "The {Field Name} field is required."
- **Related Documents:** `02_Software_Requirements.md` Part 2 (Teacher Staff validations); `07_Data_Dictionary.md` §30; `10_API_Design.md` §26; `32_Business_Rules.md` §5 (BR-013).

### TCH-07 — Teacher Staff Permission Assignment
- **Validation ID:** TCH-07
- **Field Name:** Assigned permissions (permission list)
- **Validation Rule:** Assigned permissions must be valid within the confirmed permission model and must not exceed the owning role scope (Teacher Workspace); permission changes are performed only by an authorized actor and are audited. Teacher Staff permission **granularity is PENDING (Q-011)** — validators must not invent finer or coarser capabilities than the confirmed catalog.
- **Required / Optional:** Optional at creation; validated whenever supplied or changed.
- **Allowed Values:** Confirmed permission identifiers scoped to Teacher Workspace (`08_RBAC.md`, `09_Permission_Matrix.md`).
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected permissions are invalid."
- **Related Documents:** `08_RBAC.md`; `09_Permission_Matrix.md`; `10_API_Design.md` §26; `00_Project_Context.md` §15.1 (Q-011); `32_Business_Rules.md` §5.

### TCH-08 — Archived Teacher Staff Not Active
- **Validation ID:** TCH-08
- **Field Name:** Teacher Staff account state (on any staff action)
- **Validation Rule:** An archived Teacher Staff account must not be treated as an active user: it cannot authenticate into operational use, receive permissions, or perform workspace actions until restored by an authorized user; historical Audit Log attribution to the staff user is preserved.
- **Required / Optional:** Applies to every staff-targeted operation.
- **Allowed Values:** Active accounts for active operations.
- **Minimum / Maximum Limits:** —
- **Error Message:** "This Teacher Staff account is not active." (409 on state conflict)
- **Related Documents:** `02_Software_Requirements.md` Part 2; `07_Data_Dictionary.md` §30; `10_API_Design.md` §26; `32_Business_Rules.md` §5.

### TCH-09 — Teacher Archive / Restore Authorization
- **Validation ID:** TCH-09
- **Field Name:** Archive / restore reason (where required)
- **Validation Rule:** Archive and restore of a Teacher account require the confirmed Platform permission; a reason is supplied where required; permanent deletion input is not a valid operation at all (no hard delete exists). State conflicts (archiving an archived record, restoring an active one) are rejected with 409.
- **Required / Optional:** Reason required where the operation contract requires it.
- **Allowed Values:** Bounded text reason.
- **Minimum / Maximum Limits:** GEN-04.
- **Error Message:** "The archive reason field is required." / "This Teacher account is already archived." (409)
- **Related Documents:** `10_API_Design.md` §14 (archive/restore endpoints); `09_Permission_Matrix.md`; `00_Project_Context.md` §11; `32_Business_Rules.md` §8, §24.

**Cross-referenced rules:** BR-013 staff scoping and BR-016 subject rule are owned by `00_Project_Context.md` §9 and consolidated in `32_Business_Rules.md` §5; Super Admin authority boundaries are consolidated in `32_Business_Rules.md` §8; Q-011 is protected in §23 (EXC-05) of this document.

---

# 6. Student Validation

**Authoritative sources:** `02_Software_Requirements.md` Part 2 (Students validations), Part 3 (Schedule, Settings validations); `07_Data_Dictionary.md` §6 (Student), §12 (Student Enrollment); `10_API_Design.md` §15, §27; `06_Database_Design.md` §12; `32_Business_Rules.md` §6.

### STU-01 — Registration Method (Created By Method)
- **Validation ID:** STU-01
- **Field Name:** Created By Method
- **Validation Rule:** Must be exactly one of **Self-Registration** or **Teacher-Created**; the system records which of the two confirmed registration methods created the account. No third method exists in Version 1.
- **Required / Optional:** Required (system-recorded at creation).
- **Allowed Values:** Self-Registration, Teacher-Created.
- **Minimum / Maximum Limits:** Exactly one value.
- **Error Message:** — (system-managed; invalid values indicate a contract violation)
- **Related Documents:** `07_Data_Dictionary.md` §6; `32_Business_Rules.md` §4 (BR-022).

### STU-02 — Activation Status
- **Validation ID:** STU-02
- **Field Name:** Activation Status
- **Validation Rule:** Must represent a supported activation state — **Active** or **Pending Activation** — so that Teacher-created accounts can later be activated by the Student (AUT-13). Other values are invalid.
- **Required / Optional:** Required.
- **Allowed Values:** Active, Pending Activation.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Activation Status is invalid."
- **Related Documents:** `07_Data_Dictionary.md` §6; `32_Business_Rules.md` §4.

### STU-03 — Teacher-Created Student Identity Fields
- **Validation ID:** STU-03
- **Field Name:** Student identity and account information (Teacher-created payload)
- **Validation Rule:** Required identity fields must be present and sufficient to prevent duplicate accounts (AUT-12 applies); creation also establishes the Student's relationship with the current Teacher Workspace. Duplicate accounts are rejected with 409.
- **Required / Optional:** Required identity fields.
- **Allowed Values:** Valid identity values; no duplicate global identity.
- **Minimum / Maximum Limits:** GEN-04; identity detail catalog deferred by the owning requirements.
- **Error Message:** "The {Field Name} field is required." / "An account with this identity already exists." (409)
- **Related Documents:** `02_Software_Requirements.md` Part 2; `10_API_Design.md` §15; `07_Data_Dictionary.md` §6.

### STU-04 — Existing-Student Assignment
- **Validation ID:** STU-04
- **Field Name:** Existing Student reference (assign-existing payload)
- **Validation Rule:** The referenced Student must be valid (GEN-09); the assignment must not expose another Teacher's private data — failure behaves as if the Student reference were invalid, never revealing another Teacher Workspace.
- **Required / Optional:** Required for assignment.
- **Allowed Values:** Valid global Student identities assigned through the confirmed flow.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Student is invalid." (neutral)
- **Related Documents:** `10_API_Design.md` §15 (`/students/assign-existing`); `02_Software_Requirements.md` Part 2; `06_Database_Design.md` §12; `32_Business_Rules.md` §6.

### STU-05 — Group Assignment Target
- **Validation ID:** STU-05
- **Field Name:** Group Reference (assignment / move-group payloads)
- **Validation Rule:** The target Group must belong to the current Teacher Workspace and be active. Archived Groups cannot receive new active assignments; Groups of another Teacher Workspace are invalid (GEN-09).
- **Required / Optional:** Required when a Group assignment is performed.
- **Allowed Values:** Active Groups in the current Teacher Workspace.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Group is invalid." / "The selected Group is not available." (archived, 409)
- **Related Documents:** `02_Software_Requirements.md` Part 2; `10_API_Design.md` §15, §18; `07_Data_Dictionary.md` §10; `32_Business_Rules.md` §10.

### STU-06 — One Active Group Per Teacher
- **Validation ID:** STU-06
- **Field Name:** Enrollment (Student Reference + Teacher Workspace Reference combination)
- **Validation Rule:** A Student must not be assigned to more than one active Group for the same Teacher at the same time. The check applies at assignment and at Group movement: a move closes one Enrollment period and opens another; overlapping active Enrollments for the same Teacher Workspace are invalid (409).
- **Required / Optional:** Always enforced.
- **Allowed Values:** At most one active Enrollment per Student per Teacher Workspace.
- **Minimum / Maximum Limits:** Maximum 1 active Group per Student per Teacher.
- **Error Message:** "The Student already belongs to an active Group for this Teacher." (409)
- **Related Documents:** `00_Project_Context.md` §9 (BR-002); `07_Data_Dictionary.md` §12; `02_Software_Requirements.md` Part 2; `10_API_Design.md` §15; `32_Business_Rules.md` §10.

### STU-07 — Enrollment Dates
- **Validation ID:** STU-07
- **Field Name:** Enrollment Start; Enrollment End
- **Validation Rule:** `Enrollment Start` is required and must be a valid date; `Enrollment End` is optional and must be on or after `Enrollment Start` when present. Enrollment periods drive Billable Student calculation and must never be rewritten retroactively (history is preserved on movement).
- **Required / Optional:** Start Required; End Optional.
- **Allowed Values:** Valid dates; End ≥ Start.
- **Minimum / Maximum Limits:** End not before Start.
- **Error Message:** "The Enrollment Start field is required." / "The Enrollment End must be on or after the Enrollment Start."
- **Related Documents:** `07_Data_Dictionary.md` §12; `06_Database_Design.md` §13; `17_Subscription_Billing.md`; `32_Business_Rules.md` §10, §16.

### STU-08 — Workspace-Scoped Student Update
- **Validation ID:** STU-08
- **Field Name:** Workspace-scoped Student relationship fields (update payload)
- **Validation Rule:** Updates must not create a duplicate Student account, must not change the Student's global identity into another account, and must not perform cross-scope changes (another Teacher Workspace's data, Platform data). Only allowed workspace-scoped fields are accepted.
- **Required / Optional:** Supplied fields validated.
- **Allowed Values:** Allowed fields only (per endpoint contract).
- **Minimum / Maximum Limits:** GEN-04.
- **Error Message:** "The {Field Name} is not editable here." / "The selected {Field Name} is invalid."
- **Related Documents:** `10_API_Design.md` §15; `32_Business_Rules.md` §6 (BR-001 per-Teacher partitioning).

### STU-09 — Student Own-Account Update
- **Validation ID:** STU-09
- **Field Name:** Student profile / settings fields
- **Validation Rule:** Account updates apply only to the authenticated Student's own account; changes must not create a duplicate Student account; Student Settings must not alter Teacher Workspace settings, Group assignment, or Teacher relationships.
- **Required / Optional:** Supplied fields validated.
- **Allowed Values:** Allowed own-account fields only.
- **Minimum / Maximum Limits:** GEN-04.
- **Error Message:** "The {Field Name} is not editable here." (attempt to alter Teacher-owned data: authorization failure, not validation)
- **Related Documents:** `02_Software_Requirements.md` Part 3 (Settings validations); `10_API_Design.md` §15, §27; `32_Business_Rules.md` §6.

**Cross-referenced rules:** Registration/activation validation lives in §4 (AUT-11…AUT-13); the one-Parent linkage constraint is validated in §7 (PAR-01); the global-account and partitioning rules are BR-001/BR-022/BR-003 consolidated in `32_Business_Rules.md` §6.

---

# 7. Parent Validation

**Authoritative sources:** `02_Software_Requirements.md` Part 4 (Dashboard, Student Switcher, Settings validations); `07_Data_Dictionary.md` §7 (Parent), §8 (Parent Student Link); `10_API_Design.md` §16, §27; `20_File_Storage.md` §10–§14; `32_Business_Rules.md` §7.

### PAR-01 — Parent–Student Link Validity
- **Validation ID:** PAR-01
- **Field Name:** Parent Reference + Student Reference (Parent Student Link)
- **Validation Rule:** Both references must be valid; Version 1 enforces **one Parent account per Student** — a link that would attach a second Parent account to a Student is rejected. One Parent may be linked to multiple Students. Link reads resolve only through active links.
- **Required / Optional:** Required for any linked-Student operation.
- **Allowed Values:** Valid Parent/Student pairs honoring the one-Parent-per-Student rule.
- **Minimum / Maximum Limits:** Maximum 1 Parent account per Student (V1); no limit on Students per Parent is stated.
- **Error Message:** "The selected Student is invalid." (neutral) / "This Student is already linked to a Parent account." (link management, 409)
- **Related Documents:** `07_Data_Dictionary.md` §8; `00_Project_Context.md` §9 (BR-020); `02_Software_Requirements.md` Part 4; `32_Business_Rules.md` §7.

### PAR-02 — Read-Only Enforcement
- **Validation ID:** PAR-02
- **Field Name:** Any write-target field presented by a Parent (Attendance, grades, Homework, Exams, Lessons, Student records, payment status, Group assignment)
- **Validation Rule:** Parent access is read-only everywhere. Any Parent request attempting to create, update, record, submit, grade, archive, or restore educational or payment data is denied (403) — there is no payload that makes it valid.
- **Required / Optional:** Prohibited for Parent actors.
- **Allowed Values:** None (read-only GET operations only).
- **Minimum / Maximum Limits:** —
- **Error Message:** "You do not have permission to perform this action." (403)
- **Related Documents:** `00_Project_Context.md` §9 (BR-004); `02_Software_Requirements.md` Part 4; `09_Permission_Matrix.md`; `32_Business_Rules.md` §7.

### PAR-03 — Student Switcher Selection
- **Validation ID:** PAR-03
- **Field Name:** Selected Student (Student Switcher context)
- **Validation Rule:** The selected Student must be linked to the authenticated Parent; the switch action must not modify any Student or Teacher Workspace record; the selected context applies only to read-only Parent views. An unlinked selection is invalid without disclosure.
- **Required / Optional:** Required for linked-Student views.
- **Allowed Values:** Currently linked Students of the authenticated Parent.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Student is invalid." (neutral)
- **Related Documents:** `02_Software_Requirements.md` Part 4 (Student Switcher validations); `14_UI_Components.md` (Student Switcher contract); `07_Data_Dictionary.md` §8; `32_Business_Rules.md` §7.

### PAR-04 — Parent Own-Account Update
- **Validation ID:** PAR-04
- **Field Name:** Parent account fields (profile/settings payload)
- **Validation Rule:** Updates apply only to the authenticated Parent's allowed own-account fields and must not modify linked Student records or Teacher Workspace data of any kind (Attendance, grades, Homework, Exams, payment status, Teacher relationships, Group assignment, Educational Grades, Lessons).
- **Required / Optional:** Supplied fields validated.
- **Allowed Values:** Allowed own-account fields only.
- **Minimum / Maximum Limits:** GEN-04.
- **Error Message:** "The {Field Name} is not editable here."
- **Related Documents:** `02_Software_Requirements.md` Part 4 (Settings validations); `10_API_Design.md` §16, §27; `32_Business_Rules.md` §7.

### PAR-05 — Parent Upload Denial
- **Validation ID:** PAR-05
- **Field Name:** Any file field in a Parent-originated request
- **Validation Rule:** Parent file uploads are denied entirely — every file type, every context. This is a validation-layer rejection in addition to the authorization denial: no MIME, size, or context check ever succeeds for a Parent upload.
- **Required / Optional:** Prohibited.
- **Allowed Values:** None.
- **Minimum / Maximum Limits:** —
- **Error Message:** "File upload is not available for this account." (denied without inspecting file)
- **Related Documents:** `20_File_Storage.md` §3, §10, §14; `23_Security_Standards.md` §9.1; `10_API_Design.md` §11; `32_Business_Rules.md` §7.

**Cross-referenced rules:** Parent visibility scoping for each domain (Attendance/Homework/Exams/Lessons/payments/reports) is validated by the domain sections (§10–§18) applying GEN-09 with the linked-Student scope; BR-004/BR-020 are consolidated in `32_Business_Rules.md` §7.

---

# 8. Educational Grade Validation

**Authoritative sources:** `02_Software_Requirements.md` Part 2 (Educational Grades validations); `07_Data_Dictionary.md` §9; `10_API_Design.md` §17; `32_Business_Rules.md` §9.

### GRD-01 — Educational Grade Name Required
- **Validation ID:** GRD-01
- **Field Name:** Name
- **Validation Rule:** The Educational Grade name is required on creation.
- **Required / Optional:** Required.
- **Allowed Values:** Valid educational level text.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The Educational Grade name is required."
- **Related Documents:** `02_Software_Requirements.md` Part 2; `07_Data_Dictionary.md` §9; `10_API_Design.md` §17.

### GRD-02 — Educational Grade Name Validity
- **Validation ID:** GRD-02
- **Field Name:** Name
- **Validation Rule:** The name must be valid according to product data rules — bounded, human-readable text. Exact bounds/format are defined by later detailed requirements and physical design; nothing stricter may be assumed.
- **Required / Optional:** Applies whenever Name is present.
- **Allowed Values:** Bounded text (GEN-04).
- **Minimum / Maximum Limits:** Bounded; exact maximum deferred (§3.4).
- **Error Message:** "The Educational Grade name must not exceed the allowed length."
- **Related Documents:** `02_Software_Requirements.md` Part 2 ("later detailed requirements"); `07_Data_Dictionary.md` §9; GEN-04.

### GRD-03 — Workspace Scoping
- **Validation ID:** GRD-03
- **Field Name:** Teacher Workspace Reference
- **Validation Rule:** Every Educational Grade operation must be scoped to the current Teacher Workspace; references to another workspace's Educational Grades are invalid (GEN-09). Educational Grades are Teacher-created and never visible across Teacher Workspaces.
- **Required / Optional:** Required (scoping is mandatory).
- **Allowed Values:** The current Teacher Workspace.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Educational Grade is invalid."
- **Related Documents:** `02_Software_Requirements.md` Part 2; `07_Data_Dictionary.md` §9; `06_Database_Design.md` §12; `32_Business_Rules.md` §9.

### GRD-04 — Active State For Assignment
- **Validation ID:** GRD-04
- **Field Name:** Status / Archived State (when referenced by Group assignment)
- **Validation Rule:** Archived Educational Grades must not appear in active assignment lists and cannot be used as the target of a new active Group assignment until restored by an authorized user (GEN-11). Historical reports may include them, clearly indicated as archived.
- **Required / Optional:** Applies to assignment operations.
- **Allowed Values:** Active Educational Grades for active assignment.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Educational Grade is not available." (409)
- **Related Documents:** `02_Software_Requirements.md` Part 2; `07_Data_Dictionary.md` §9; `00_Project_Context.md` §11; `32_Business_Rules.md` §9, §24.

### GRD-05 — Restore Authorization
- **Validation ID:** GRD-05
- **Field Name:** Restore reason (where required)
- **Validation Rule:** Restoration must be performed only by an authorized user (workspace Educational Grade restore permission); restore of a non-archived record is a 409 state conflict.
- **Required / Optional:** Reason required where the operation contract requires it.
- **Allowed Values:** Bounded text; archived Educational Grade in current workspace.
- **Minimum / Maximum Limits:** GEN-04.
- **Error Message:** "This Educational Grade is not archived." (409)
- **Related Documents:** `10_API_Design.md` §17; `09_Permission_Matrix.md`; `00_Project_Context.md` §11.

**Cross-referenced rules:** Educational Grade creation permission is validated as authorization by `08_RBAC.md`/`09_Permission_Matrix.md`; canonical term "Educational Grade" (never "Class") per `32_Business_Rules.md` §9 (GRD-05 there).

---

# 9. Group Validation

**Authoritative sources:** `02_Software_Requirements.md` Part 2 (Groups validations); `07_Data_Dictionary.md` §10 (Group), §11 (Group Schedule), §12 (Student Enrollment); `10_API_Design.md` §18; `32_Business_Rules.md` §10.

### GRP-01 — Group Name Required
- **Validation ID:** GRP-01
- **Field Name:** Name
- **Validation Rule:** The Group Name is required on creation and must be bounded, valid text.
- **Required / Optional:** Required.
- **Allowed Values:** Valid Group name text.
- **Minimum / Maximum Limits:** Bounded; exact maximum deferred (§3.4).
- **Error Message:** "The Group Name field is required." / "The Group Name must not exceed the allowed length."
- **Related Documents:** `02_Software_Requirements.md` Part 2; `07_Data_Dictionary.md` §10; `10_API_Design.md` §18.

### GRP-02 — Educational Grade Reference
- **Validation ID:** GRP-02
- **Field Name:** Educational Grade Reference
- **Validation Rule:** The Group must be assigned to an **active** Educational Grade in the **same** Teacher Workspace. A missing, archived, or foreign-workspace Educational Grade reference fails (GEN-09, GEN-11; 404/422 as applicable).
- **Required / Optional:** Required.
- **Allowed Values:** Active Educational Grades of the current Teacher Workspace.
- **Minimum / Maximum Limits:** Each Group belongs to exactly one Educational Grade.
- **Error Message:** "The selected Educational Grade is invalid." / "The selected Educational Grade is not available." (archived, 409)
- **Related Documents:** `02_Software_Requirements.md` Part 2; `07_Data_Dictionary.md` §10; `10_API_Design.md` §18; `06_Database_Design.md` §13.

### GRP-03 — Group Price
- **Validation ID:** GRP-03
- **Field Name:** Price
- **Validation Rule:** Price is required and must be a valid, **non-negative** monetary value. The detailed monetary rules (currency, precision) are deferred by the owning documents — no currency symbol, rounding mode, or upper cap may be invented (Q-015 PENDING for currency/localization).
- **Required / Optional:** Required.
- **Allowed Values:** Valid monetary amounts ≥ 0.
- **Minimum / Maximum Limits:** Minimum 0 (confirmed non-negative); no confirmed maximum.
- **Error Message:** "The Price must be a valid monetary amount of zero or more."
- **Related Documents:** `07_Data_Dictionary.md` §10; `02_Software_Requirements.md` Part 2; `32_Business_Rules.md` §10 (BR-009); Q-015.

### GRP-04 — Pricing Type
- **Validation ID:** GRP-04
- **Field Name:** Pricing Type
- **Validation Rule:** Pricing Type is required and must be exactly **Monthly** or **Per Lesson**. This enum drives Flow B fee status and must be validated at creation and on every update.
- **Required / Optional:** Required.
- **Allowed Values:** Monthly, Per Lesson.
- **Minimum / Maximum Limits:** Exactly one value.
- **Error Message:** "The Pricing Type must be Monthly or Per Lesson."
- **Related Documents:** `07_Data_Dictionary.md` §10, §33; `02_Software_Requirements.md` Part 2; `10_API_Design.md` §18; `32_Business_Rules.md` §10 (BR-009).

### GRP-05 — Group Schedule Description
- **Validation ID:** GRP-05
- **Field Name:** Schedule Description
- **Validation Rule:** A Group carries Schedule; the Schedule Description is required and must be valid text. The detailed recurring schedule structure/format is deferred (`07_Data_Dictionary.md` §11: "valid format defined later") — validators must not invent a scheduling syntax.
- **Required / Optional:** Required.
- **Allowed Values:** Bounded text.
- **Minimum / Maximum Limits:** GEN-04; format deferred (§3.4).
- **Error Message:** "The Schedule field is required."
- **Related Documents:** `07_Data_Dictionary.md` §11; `10_API_Design.md` §18; `02_Software_Requirements.md` Part 2.

### GRP-06 — Single Active Group Per Student (Target Check)
- **Validation ID:** GRP-06
- **Field Name:** Group Reference (in assignment and move-group operations)
- **Validation Rule:** A Student cannot be assigned to more than one active Group for the same Teacher at the same time — see STU-06, which is the authoritative check; this rule states it from the Group side for completeness.
- **Required / Optional:** Always enforced.
- **Allowed Values:** Groups for which the Student has no conflicting active Enrollment.
- **Minimum / Maximum Limits:** Max 1 active Enrollment per Student per Teacher.
- **Error Message:** "The Student already belongs to an active Group for this Teacher." (409)
- **Related Documents:** `00_Project_Context.md` §9 (BR-002); STU-06; `10_API_Design.md` §15; `32_Business_Rules.md` §10.

### GRP-07 — Archived Group Assignment Ban
- **Validation ID:** GRP-07
- **Field Name:** Status / Archived State
- **Validation Rule:** Archived Groups cannot receive new active Student assignments (GEN-11); they remain valid as historical/report context, clearly indicated.
- **Required / Optional:** Applies to every assignment operation.
- **Allowed Values:** Active Groups for new assignments.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Group is not available." (409)
- **Related Documents:** `02_Software_Requirements.md` Part 2; `07_Data_Dictionary.md` §10; `00_Project_Context.md` §11; `32_Business_Rules.md` §10, §24.

### GRP-08 — Historical Clarity On Update
- **Validation ID:** GRP-08
- **Field Name:** Price, Pricing Type, Schedule (on update)
- **Validation Rule:** Updates must keep historical payment records understandable: changing Price or Pricing Type must not retroactively rewrite or invalidate recorded Flow B history (business validation at the service layer). This rule validates the *operation*, not the format — format rules are GRP-03/GRP-04.
- **Required / Optional:** Applies to every Group update.
- **Allowed Values:** Valid per GRP-03/GRP-04/GRP-05.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (business conflict yields 409 per `10_API_Design.md` §6)
- **Related Documents:** `10_API_Design.md` §18; `32_Business_Rules.md` §10, §17, §18 (BR-007/BR-014/BR-015 historical preservation).

**Cross-referenced rules:** The one-Group invariant (BR-002) is checked at Enrollment level (STU-06); Educational Grade active-state (GRD-04) gates GRP-02; Flow B derivation rules (BR-009) are consolidated in `32_Business_Rules.md` §10 and §18.

---

# 10. Attendance Validation

**Authoritative sources:** `02_Software_Requirements.md` Part 2 (Attendance validations); `16_QR_Attendance_System.md` (§4–§6, duplicate handling, role boundaries); `07_Data_Dictionary.md` §13 (Attendance Session), §14 (QR Session), §15 (Attendance); `10_API_Design.md` §19; `32_Business_Rules.md` §11.

### ATT-01 — Attendance Session Group And Date
- **Validation ID:** ATT-01
- **Field Name:** Group Reference; Session Date
- **Validation Rule:** The Group must belong to the current Teacher Workspace (GEN-09); the Session Date must be a valid session date. An Attendance Session cannot reference a foreign Group.
- **Required / Optional:** Both required.
- **Allowed Values:** In-workspace Groups; valid calendar dates.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Group is invalid." / "The Session Date must be a valid date."
- **Related Documents:** `10_API_Design.md` §19; `07_Data_Dictionary.md` §13; `32_Business_Rules.md` §11.

### ATT-02 — Attendance Method Context
- **Validation ID:** ATT-02
- **Field Name:** Attendance Method Context / Attendance Method
- **Validation Rule:** The method must be exactly one of **Dynamic QR Code**, **ID Card**, or **Manual**. Barcode scanning and any other method are not confirmed and must be rejected.
- **Required / Optional:** Required.
- **Allowed Values:** Dynamic QR Code, ID Card, Manual.
- **Minimum / Maximum Limits:** Exactly one value.
- **Error Message:** "The selected Attendance method is invalid."
- **Related Documents:** `07_Data_Dictionary.md` §13, §15; `16_QR_Attendance_System.md` §1; `32_Business_Rules.md` §11 (BR-010).

### ATT-03 — Dynamic QR Daily Validity
- **Validation ID:** ATT-03
- **Field Name:** QR scan payload (QR context + date)
- **Validation Rule:** Dynamic QR Code Attendance must use the **daily generated** Dynamic QR Code for the current Teacher Workspace Attendance context. An invalid, expired, wrong-day, or foreign-workspace QR context is rejected; an archived or inactive Attendance context is not an active scan target. The exact expiry timestamp/window is **not confirmed** — validation enforces the daily rule only and must not promise an unconfirmed duration.
- **Required / Optional:** Required for QR scans.
- **Allowed Values:** The valid daily Dynamic QR context of the relevant Teacher Workspace.
- **Minimum / Maximum Limits:** Daily generation cadence (confirmed); exact scan window deferred (§3.4).
- **Error Message:** "This Dynamic QR Code is not valid for today's Attendance."
- **Related Documents:** `16_QR_Attendance_System.md` §5–§6; `02_Software_Requirements.md` Part 2; `10_API_Design.md` §19; `32_Business_Rules.md` §11.

### ATT-04 — QR Scan Student Eligibility
- **Validation ID:** ATT-04
- **Field Name:** Student context (authenticated scanner)
- **Validation Rule:** The Student must be authenticated and must have a valid relationship (Enrollment) with the relevant Teacher Workspace; the QR visual value alone never proves eligibility. A Student cannot scan for a Teacher they have no relationship with, and cannot scan for another Student.
- **Required / Optional:** Required.
- **Allowed Values:** Authenticated Students with a valid Teacher relationship for the session's workspace.
- **Minimum / Maximum Limits:** —
- **Error Message:** "You are not enrolled for this Attendance." (403/422 without revealing workspace internals)
- **Related Documents:** `16_QR_Attendance_System.md` (scan flow; role boundaries); `07_Data_Dictionary.md` §14; `10_API_Design.md` §19; `32_Business_Rules.md` §11.

### ATT-05 — Duplicate Scan / Conflicting Entry Safety
- **Validation ID:** ATT-05
- **Field Name:** Student Reference + Attendance Session context
- **Validation Rule:** Scanning the same Dynamic QR Code more than once for the same Attendance context must not create inconsistent duplicate Attendance; a manual entry or ID Card scan conflicting with the same Student and context must be validated safely — the backend decides record-versus-reject, and the user receives an accurate outcome (never a false success). The exact deduplication key and conflict-resolution policy are **not confirmed** (§23, EXC-05).
- **Required / Optional:** Always applied.
- **Allowed Values:** One consistent Attendance record per Student per Attendance context.
- **Minimum / Maximum Limits:** —
- **Error Message:** "Attendance is already recorded for this Student in this session." (409)
- **Related Documents:** `16_QR_Attendance_System.md` (duplicate/inconsistent-record safeguards); `32_Business_Rules.md` §11.

### ATT-06 — ID Card Scan Validity
- **Validation ID:** ATT-06
- **Field Name:** ID Card scan reference + session context
- **Validation Rule:** ID Card Attendance must resolve a valid printed static QR identity and a valid Student relationship for the current Teacher Workspace, in a valid session context. ID Card scanning is a Teacher-side operation; a Student cannot self-record via ID Card.
- **Required / Optional:** Required for ID Card Attendance.
- **Allowed Values:** Valid printed ID Card codes of Students with a workspace relationship.
- **Minimum / Maximum Limits:** —
- **Error Message:** "This ID Card is not valid for this Attendance session."
- **Related Documents:** `16_QR_Attendance_System.md` §1, role boundaries; `02_Software_Requirements.md` Part 2; `10_API_Design.md` §19; `32_Business_Rules.md` §11.

### ATT-07 — Manual Attendance Authority And Target
- **Validation ID:** ATT-07
- **Field Name:** Student reference; session context; Attendance status
- **Validation Rule:** Manual Attendance must be performed by an authorized Teacher or Teacher Staff user (authorization layer) for a Student associated with the Teacher Workspace through a valid relationship; the session context and status must be valid (ATT-01, ATT-08).
- **Required / Optional:** All three required.
- **Allowed Values:** Workspace-associated Students; valid session contexts; valid Attendance status values.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Student is invalid." / "The selected Attendance status is invalid."
- **Related Documents:** `02_Software_Requirements.md` Part 2; `16_QR_Attendance_System.md` (role boundaries); `10_API_Design.md` §19.

### ATT-08 — Attendance Status Value
- **Validation ID:** ATT-08
- **Field Name:** Attendance Status
- **Validation Rule:** Must be a valid Attendance status value for the confirmed status model (default recorded state: Present or another defined status per `07_Data_Dictionary.md` §15). Unconfirmed status values are rejected.
- **Required / Optional:** Required.
- **Allowed Values:** Confirmed Attendance status set (per data dictionary).
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Attendance status is invalid."
- **Related Documents:** `07_Data_Dictionary.md` §15; `10_API_Design.md` §19.

### ATT-09 — Attendance Correction
- **Validation ID:** ATT-09
- **Field Name:** Attendance record reference; updated status; reason
- **Validation Rule:** Correction applies only to an Attendance record belonging to the current Teacher Workspace; the update carries the corrected status and a reason; Attendance changes are authorized and auditable (Audit event: Attendance Change). Correcting another workspace's record, or a record invisible to the actor, is invalid (GEN-09/12).
- **Required / Optional:** Updated status required; reason required by the correction contract.
- **Allowed Values:** Valid statuses; bounded reason text.
- **Minimum / Maximum Limits:** GEN-04 (reason).
- **Error Message:** "The selected Attendance record is invalid." / "The reason field is required."
- **Related Documents:** `10_API_Design.md` §19 (PATCH attendance); `02_Software_Requirements.md` Part 2; `00_Project_Context.md` §10; `32_Business_Rules.md` §11, §25.

**Cross-referenced rules:** Attendance is never a Billing input (BR-008) — enforced as an integrity rule in §21 (INT-07). Parent/Student read paths for Attendance apply PAR-03 / GEN-09. Account-level scoping of lists appears in §19–§20 (filters and pagination).

---

# 11. Homework Validation

**Authoritative sources:** `02_Software_Requirements.md` Part 2 (Homework creation), Part 3 (Student Homework validations); `07_Data_Dictionary.md` §16 (Homework), §17 (Homework Submission); `10_API_Design.md` §20, §28; `20_File_Storage.md` §3, §7; `32_Business_Rules.md` §12.

### HW-01 — Homework Title
- **Validation ID:** HW-01
- **Field Name:** Title
- **Validation Rule:** The Homework Title is required and must be bounded valid text.
- **Required / Optional:** Required.
- **Allowed Values:** Valid text.
- **Minimum / Maximum Limits:** Bounded; exact maximum deferred (§3.4).
- **Error Message:** "The Homework Title field is required."
- **Related Documents:** `07_Data_Dictionary.md` §16; `10_API_Design.md` §20 (`POST /teacher-workspace/homework`).

### HW-02 — Homework Description
- **Validation ID:** HW-02
- **Field Name:** Description
- **Validation Rule:** Optional; when present it must be valid bounded text.
- **Required / Optional:** Optional.
- **Allowed Values:** Valid text.
- **Minimum / Maximum Limits:** GEN-04.
- **Error Message:** "The Description must not exceed the allowed length."
- **Related Documents:** `07_Data_Dictionary.md` §16.

### HW-03 — Homework Supported Format
- **Validation ID:** HW-03
- **Field Name:** Supported Format
- **Validation Rule:** Must be exactly **Text**, **Image**, or **PDF** (BR-021). Video homework is rejected at validation in every form: as a format value, as an attachment type, and as a represented capability.
- **Required / Optional:** Required.
- **Allowed Values:** Text, Image, PDF.
- **Minimum / Maximum Limits:** Exactly one value.
- **Error Message:** "The Homework format must be Text, Image, or PDF. Video Homework is not supported."
- **Related Documents:** `00_Project_Context.md` §9 (BR-021); `07_Data_Dictionary.md` §16; `10_API_Design.md` §20; `20_File_Storage.md` §3; `32_Business_Rules.md` §12.

### HW-04 — Homework Target Context (Group Reference)
- **Validation ID:** HW-04
- **Field Name:** Group Reference
- **Validation Rule:** Optional; when present, the Group must belong to the same Teacher Workspace and be a valid assignment target (active; GEN-09/GEN-11). Detailed assignment targeting scoping (Group vs Student scope) follows the functional specifications; this catalog adds no targeting mode.
- **Required / Optional:** Optional.
- **Allowed Values:** Active Groups of the current Teacher Workspace.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Group is invalid."
- **Related Documents:** `07_Data_Dictionary.md` §16; `10_API_Design.md` §20; `02_Software_Requirements.md` Part 2.

### HW-05 — Submission Eligibility
- **Validation ID:** HW-05
- **Field Name:** Homework reference + Student context (submission payload)
- **Validation Rule:** The Student may submit only their own assigned Homework: the Homework must be assigned to the authenticated Student through a valid Teacher relationship and must be active (not archived). Submissions to another Student's Homework or to archived Homework are rejected (GEN-11; 404/409/422 as applicable).
- **Required / Optional:** Required for every submission.
- **Allowed Values:** Active Homework assigned to the authenticated Student.
- **Minimum / Maximum Limits:** —
- **Error Message:** "This Homework is not available for submission." (no disclosure of others' Homework)
- **Related Documents:** `02_Software_Requirements.md` Part 3; `07_Data_Dictionary.md` §17; `10_API_Design.md` §20; `20_File_Storage.md` §7.

### HW-06 — Submission Format
- **Validation ID:** HW-06
- **Field Name:** Submission Format / submission file
- **Validation Rule:** Must be Text, Image, or PDF only; a binary Student submission file must be **Image or PDF** only; video submissions are rejected.
- **Required / Optional:** Required.
- **Allowed Values:** Text, Image, PDF (binary: Image, PDF).
- **Minimum / Maximum Limits:** File constraints per §18 (FIL-xx).
- **Error Message:** "The submission must be Text, Image, or PDF. Video submissions are not supported."
- **Related Documents:** `07_Data_Dictionary.md` §17; `10_API_Design.md` §20, §28; `20_File_Storage.md` §3, §7; `02_Software_Requirements.md` Part 3.

### HW-07 — Submission Timestamp Consistency
- **Validation ID:** HW-07
- **Field Name:** Submitted At; Submission Status
- **Validation Rule:** `Submitted At` is required once the submission is submitted (conditional requirement) and is system-recorded — never client-supplied; `Submission Status` must be a valid state (Submitted or Pending per `07_Data_Dictionary.md` §17).
- **Required / Optional:** Conditionally required (on submitted state).
- **Allowed Values:** Valid states; system timestamps.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (system-consistency rule; contract violation)
- **Related Documents:** `07_Data_Dictionary.md` §17.

### HW-08 — Grading Target And Feedback
- **Validation ID:** HW-08
- **Field Name:** Submission reference; grade/review status; feedback
- **Validation Rule:** A submission may be graded/reviewed only if it belongs to the current Teacher Workspace; the grade/review status must be valid and feedback must be valid text where supplied; Homework grading/modification is audited.
- **Required / Optional:** Status required for grading; feedback optional where applicable.
- **Allowed Values:** Valid review states; bounded feedback text.
- **Minimum / Maximum Limits:** GEN-04 (feedback).
- **Error Message:** "The selected submission is invalid." / "The selected review status is invalid."
- **Related Documents:** `10_API_Design.md` §20 (`/submissions/{id}/grade`); `00_Project_Context.md` §10; `32_Business_Rules.md` §12.

**Cross-referenced rules:** File-level checks for attachments and submissions (type, MIME, size, ownership) are §18 (FIL-xx); workspace scoping via GEN-09; BR-021 is consolidated in `32_Business_Rules.md` §12.

---

# 12. Exam Validation

**Authoritative sources:** `02_Software_Requirements.md` Part 2 (Exams & Question Bank validations), Part 3 (Student Exams validations); `15_Exam_Engine.md` (creation flow, attempt flow, Bubble Sheet section, publishing); `07_Data_Dictionary.md` §22 (Exam), §23 (Exam Attempt), §24 (Exam Answer); `10_API_Design.md` §22; `32_Business_Rules.md` §13.

### EXM-01 — Exam Title
- **Validation ID:** EXM-01
- **Field Name:** Title
- **Validation Rule:** The Exam Title is required and must be bounded valid text.
- **Required / Optional:** Required.
- **Allowed Values:** Valid text.
- **Minimum / Maximum Limits:** Bounded; exact maximum deferred (§3.4).
- **Error Message:** "The Exam Title field is required."
- **Related Documents:** `07_Data_Dictionary.md` §22; `10_API_Design.md` §22.

### EXM-02 — Question Bank Reference
- **Validation ID:** EXM-02
- **Field Name:** Question Bank Reference
- **Validation Rule:** Must reference a Question Bank belonging to the same Teacher Workspace as the Exam (GEN-09). Cross-Teacher Question Bank references are invalid by rule (BR-012), not merely filtered.
- **Required / Optional:** Required.
- **Allowed Values:** Question Banks of the current Teacher Workspace.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Question Bank is invalid."
- **Related Documents:** `07_Data_Dictionary.md` §22; `10_API_Design.md` §22 (`POST /teacher-workspace/exams`); `32_Business_Rules.md` §13.

### EXM-03 — Selected Questions Ownership And State
- **Validation ID:** EXM-03
- **Field Name:** Selected Questions (composition list)
- **Validation Rule:** An Exam may include only Questions owned by the same Teacher Workspace and only supported types (QBK-03); archived or inactive Questions cannot be active composition members unless restored (GEN-11).
- **Required / Optional:** Required on composition.
- **Allowed Values:** Active Questions of the current Teacher Workspace's Question Bank.
- **Minimum / Maximum Limits:** —
- **Error Message:** "One or more selected Questions are invalid."
- **Related Documents:** `02_Software_Requirements.md` Part 2; `15_Exam_Engine.md` (creation flow); `10_API_Design.md` §22.

### EXM-04 — Non-Empty Valid Composition
- **Validation ID:** EXM-04
- **Field Name:** Selected Questions (validity aggregate)
- **Validation Rule:** An Exam with no valid active selected Questions must not be treated as a valid active Exam — publication/availability of an empty or fully-invalid composition is rejected. The detailed publication criteria and availability fields remain unspecified by the owning documents; validators must enforce only this confirmed rule without inventing scheduling fields.
- **Required / Optional:** Applies at publish/availability time.
- **Allowed Values:** Compositions with at least one valid active Question.
- **Minimum / Maximum Limits:** Minimum 1 valid active Question for active availability.
- **Error Message:** "The Exam has no valid active Questions."
- **Related Documents:** `15_Exam_Engine.md` (creation/publication flow); `32_Business_Rules.md` §13.

### EXM-05 — Exam Status Lifecycle Value
- **Validation ID:** EXM-05
- **Field Name:** Exam Status / availability context
- **Validation Rule:** Status transitions must follow the confirmed lifecycle states (default Draft or Active per `07_Data_Dictionary.md` §22) and the confirmed availability workflow; unconfirmed availability models (defined scheduling fields, time windows) must not be invented as required inputs.
- **Required / Optional:** Required (system-managed with actor intent).
- **Allowed Values:** Confirmed lifecycle states.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The Exam cannot be moved to the requested state." (409)
- **Related Documents:** `07_Data_Dictionary.md` §22; `15_Exam_Engine.md`; `10_API_Design.md` §22 (`/publish`).

### EXM-06 — Attempt Start Eligibility
- **Validation ID:** EXM-06
- **Field Name:** Exam reference + Student context
- **Validation Rule:** The Exam must be available to the authenticated Student — assigned or available through the Student's Teacher relationship, active (not archived, GEN-11). A Student cannot start attempts on another Student's behalf or on inaccessible Exams; Exam definitions, attempts, and grades are workspace-scoped.
- **Required / Optional:** Required at attempt start.
- **Allowed Values:** Available Exams of the Student's own Teachers.
- **Minimum / Maximum Limits:** Attempt-limit behavior is **not defined** in the current Project Context — no attempt limit validator exists.
- **Error Message:** "This Exam is not available." (409/404 without disclosure)
- **Related Documents:** `02_Software_Requirements.md` Part 3; `07_Data_Dictionary.md` §23; `10_API_Design.md` §22; `32_Business_Rules.md` §13.

### EXM-07 — Answer Type Matching
- **Validation ID:** EXM-07
- **Field Name:** Answer Content (per answer)
- **Validation Rule:** Answer input must match the Question Type: a Multiple Choice answer is a choice selection; a True/False answer is a true/false selection; an Essay answer is text; a Bubble Sheet answer is an on-screen bubble selection valid for the sheet structure (BSH-02). Mismatched answer shapes are rejected.
- **Required / Optional:** Required for every submitted answer.
- **Allowed Values:** Answer shapes matching the referenced Question's type.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The answer format does not match the question type."
- **Related Documents:** `07_Data_Dictionary.md` §24; `10_API_Design.md` §22 (`/attempts/{id}/submit`); `15_Exam_Engine.md`.

### EXM-08 — Attempt Ownership
- **Validation ID:** EXM-08
- **Field Name:** Exam Attempt reference
- **Validation Rule:** Submit, view-result, and grade operations must reference an attempt owned by the correct actor context: Students only their own attempts; Teachers/staff only attempts of Exams in the current Teacher Workspace; Parents read-only on linked Students' attempts where available. Another Student's attempt is invalid-as-invisible (GEN-12).
- **Required / Optional:** Required.
- **Allowed Values:** Attempts within the actor's authorized scope.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Exam attempt is invalid."
- **Related Documents:** `07_Data_Dictionary.md` §23; `10_API_Design.md` §22; `02_Software_Requirements.md` Part 3/Part 4.

### EXM-09 — Essay Grading State Honesty
- **Validation ID:** EXM-09
- **Field Name:** Grade data / grading status (grading payload and result views)
- **Validation Rule:** Essay answers enter a pending-grading state and must not be presented as a final grade until graded by an authorized Teacher-side actor; grading input must be valid for the grading contract and is audited (Exam Modification). Automatic grading applies to supported types only.
- **Required / Optional:** Grade data required when grading.
- **Allowed Values:** Valid grading states (pending → graded) and valid grade values per contract.
- **Minimum / Maximum Limits:** Grading algorithm unowned by this catalog (`15_Exam_Engine.md` scope exclusion).
- **Error Message:** "The grade value is invalid." / "The selected Exam attempt is invalid."
- **Related Documents:** `10_API_Design.md` §22 (`/attempts/{id}/grade`); `14_UI_Components.md` (status honesty — pending grading not a final grade); `15_Exam_Engine.md`; `32_Business_Rules.md` §13.

**Cross-referenced rules:** Question-type and Question Bank privacy rules are §13 (QBK-xx); Bubble Sheet specifics are §14 (BSH-xx); Parent/Student read integrity via GEN-09/PAR-02; BR-011/BR-012 consolidated in `32_Business_Rules.md` §13.

---

# 13. Question Bank Validation

**Authoritative sources:** `07_Data_Dictionary.md` §20 (Question Bank), §21 (Question); `02_Software_Requirements.md` Part 2 (Exams & Question Bank validations); `15_Exam_Engine.md`; `10_API_Design.md` §22; `32_Business_Rules.md` §13.

### QBK-01 — Question Bank Name
- **Validation ID:** QBK-01
- **Field Name:** Name
- **Validation Rule:** The Question Bank Name must be valid text (default or Teacher-defined per `07_Data_Dictionary.md` §20); bounded per GEN-04.
- **Required / Optional:** Required on creation when no default applies.
- **Allowed Values:** Valid text.
- **Minimum / Maximum Limits:** Bounded; exact maximum deferred (§3.4).
- **Error Message:** "The Question Bank Name is required."
- **Related Documents:** `07_Data_Dictionary.md` §20; `10_API_Design.md` §22 (`POST /teacher-workspace/question-banks`).

### QBK-02 — Question Content
- **Validation ID:** QBK-02
- **Field Name:** Question Content
- **Validation Rule:** Question Content is required and must be valid question content for the declared Question Type.
- **Required / Optional:** Required.
- **Allowed Values:** Valid text/structured content matching the Question Type.
- **Minimum / Maximum Limits:** Bounded; exact maximum deferred (§3.4).
- **Error Message:** "The Question Content field is required."
- **Related Documents:** `07_Data_Dictionary.md` §21; `10_API_Design.md` §22.

### QBK-03 — Question Type
- **Validation ID:** QBK-03
- **Field Name:** Question Type
- **Validation Rule:** Must be exactly one of **Multiple Choice**, **True/False**, **Essay**, or **Bubble Sheet** (BR-011). No other question type may be accepted.
- **Required / Optional:** Required.
- **Allowed Values:** Multiple Choice, True/False, Essay, Bubble Sheet.
- **Minimum / Maximum Limits:** Exactly one value.
- **Error Message:** "The Question Type must be Multiple Choice, True/False, Essay, or Bubble Sheet."
- **Related Documents:** `00_Project_Context.md` §9 (BR-011); `07_Data_Dictionary.md` §21; `10_API_Design.md` §22; `32_Business_Rules.md` §13.

### QBK-04 — Answer Definition Presence
- **Validation ID:** QBK-04
- **Field Name:** Answer Definition
- **Validation Rule:** Optional structured data, **required where automatic grading applies** (conditional requirement): automatically graded types (Multiple Choice, True/False, Bubble Sheet) must carry a valid answer definition matching the type; Essay carries no automatic answer definition.
- **Required / Optional:** Conditionally required.
- **Allowed Values:** Structured answer definitions consistent with the Question Type.
- **Minimum / Maximum Limits:** Detailed answer structures are deferred to Exam Engine design — no finer format is invented here.
- **Error Message:** "The Answer Definition is required for automatically graded questions."
- **Related Documents:** `07_Data_Dictionary.md` §21, §24; `15_Exam_Engine.md`; `10_API_Design.md` §22.

### QBK-05 — Question Bank Privacy (Reference Validation)
- **Validation ID:** QBK-05
- **Field Name:** Question Bank Reference; Question Reference (in any cross-record payload)
- **Validation Rule:** Questions and Question Banks are Teacher-owned and private: any payload referencing another Teacher Workspace's Question Bank or Question is invalid (GEN-09), and no sharing/marketplace reference mode exists.
- **Required / Optional:** Applies to all references.
- **Allowed Values:** Own-workspace Question Banks/Questions only.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Question Bank is invalid."
- **Related Documents:** `07_Data_Dictionary.md` §20, §21; `02_Software_Requirements.md` Part 2; `32_Business_Rules.md` §13 (BR-011, BR-012).

### QBK-06 — Archived Question State
- **Validation ID:** QBK-06
- **Field Name:** Question Status
- **Validation Rule:** Archived Questions cannot be used as active (composition, attempts) unless restored by an authorized user (GEN-11); they remain valid historical content.
- **Required / Optional:** Applies to active-use references.
- **Allowed Values:** Active Questions for active use.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Question is not available." (409)
- **Related Documents:** `07_Data_Dictionary.md` §21; `10_API_Design.md` §22; `00_Project_Context.md` §11.

**Cross-referenced rules:** Exam composition membership is §12 (EXM-03); workspace scoping via GEN-09; Bubble Sheet structure checks are §14.

---

# 14. Bubble Sheet Validation

**Authoritative sources:** `15_Exam_Engine.md` (Bubble Sheet section); `02_Software_Requirements.md` Part 3 (Student Exams validations); `07_Data_Dictionary.md` §21, §24; `10_API_Design.md` §22; `32_Business_Rules.md` §14.

### BSH-01 — On-Screen Selection Only
- **Validation ID:** BSH-01
- **Field Name:** Bubble Sheet answer input (interaction mode)
- **Validation Rule:** Bubble Sheet answers are electronic **on-screen selections** during an authorized Exam attempt. Paper-sheet scanning, optical mark recognition, camera capture of answer sheets, printing workflows, and answer-sheet templates are not confirmed and must be rejected as input modes.
- **Required / Optional:** Required mode constraint.
- **Allowed Values:** On-screen bubble selections only.
- **Minimum / Maximum Limits:** —
- **Error Message:** "Bubble Sheet answers must be selected on screen."
- **Related Documents:** `15_Exam_Engine.md` (Bubble Sheet); `10_API_Design.md` §22; `32_Business_Rules.md` §14.

### BSH-02 — Selection Valid For Structure
- **Validation ID:** BSH-02
- **Field Name:** Bubble selection value
- **Validation Rule:** Each selection must be valid for the applicable Bubble Sheet structure — a bubble that exists in that Question's sheet definition. Selections outside the structure are rejected; automatic grading is supported where applicable.
- **Required / Optional:** Required per Bubble Sheet answer.
- **Allowed Values:** Bubbles defined by the applicable Bubble Sheet structure.
- **Minimum / Maximum Limits:** Structure-defined; a scoring formula is **not** defined for V1 and must not be invented.
- **Error Message:** "The selected answer is not valid for this Bubble Sheet."
- **Related Documents:** `15_Exam_Engine.md` (Bubble Sheet); `02_Software_Requirements.md` Part 3; `07_Data_Dictionary.md` §24.

### BSH-03 — Access Only Inside Authorized Exam
- **Validation ID:** BSH-03
- **Field Name:** Bubble Sheet Question reference
- **Validation Rule:** A Student cannot access a Bubble Sheet Question outside an assigned or available Exam; Bubble Sheet content is created only inside the owning Teacher Workspace; attempts, answers, and grades remain workspace-scoped (EXM-06/EXM-08 apply).
- **Required / Optional:** Applies to every Bubble Sheet access.
- **Allowed Values:** Bubble Sheet Questions reached through an authorized Exam attempt.
- **Minimum / Maximum Limits:** —
- **Error Message:** "This Exam content is not available."
- **Related Documents:** `15_Exam_Engine.md` (Bubble Sheet); `32_Business_Rules.md` §13–§14.

**Cross-referenced rules:** BSH-xx are the Bubble Sheet specialization of QBK-03/EXM-07; Parent read-only applies (PAR-02 — a Parent cannot select answers).

---

# 15. Lesson Validation

**Authoritative sources:** `02_Software_Requirements.md` Part 2 (Lessons), Part 3 (Student Lessons validations); `07_Data_Dictionary.md` §18 (Lesson), §19 (Lesson Video); `10_API_Design.md` §21; `20_File_Storage.md` §6; Q-010 (Lesson video hosting/protection — PENDING); `32_Business_Rules.md` §15.

### LSN-01 — Lesson Title
- **Validation ID:** LSN-01
- **Field Name:** Title
- **Validation Rule:** The Lesson Title is required and must be bounded valid text.
- **Required / Optional:** Required.
- **Allowed Values:** Valid text.
- **Minimum / Maximum Limits:** Bounded; exact maximum deferred (§3.4).
- **Error Message:** "The Lesson Title field is required."
- **Related Documents:** `07_Data_Dictionary.md` §18; `10_API_Design.md` §21.

### LSN-02 — Lesson Description And Availability Context
- **Validation ID:** LSN-02
- **Field Name:** Description; availability context
- **Validation Rule:** Description is optional valid text. Availability context input must be valid within the confirmed Lesson model; the documents do not define a detailed availability/scheduling field set — validators must not invent one.
- **Required / Optional:** Description optional; availability context per confirmed contract.
- **Allowed Values:** Valid text; confirmed availability values.
- **Minimum / Maximum Limits:** GEN-04.
- **Error Message:** "The Description must not exceed the allowed length."
- **Related Documents:** `07_Data_Dictionary.md` §18; `10_API_Design.md` §21.

### LSN-03 — Lesson Video Upload Target
- **Validation ID:** LSN-03
- **Field Name:** Lesson reference (video upload)
- **Validation Rule:** The Lesson must belong to the current Teacher Workspace (GEN-09) and be a valid active target (not archived, GEN-11); the uploader must hold workspace Lesson video permission. Lesson videos are Teacher-owned and private at every validation step.
- **Required / Optional:** Required for video upload.
- **Allowed Values:** Own-workspace active Lessons.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Lesson is invalid." / "The selected Lesson is not available." (archived, 409)
- **Related Documents:** `10_API_Design.md` §21 (`/lessons/{id}/video`); `20_File_Storage.md` §6; `32_Business_Rules.md` §15.

### LSN-04 — Lesson Video File Validity
- **Validation ID:** LSN-04
- **Field Name:** Video file
- **Validation Rule:** The file must be a valid video for the Lesson context (file rules per FIL-06…FIL-09). Video formats/codecs, transcoding, streaming, signed URLs, quotas, watermarking, and previews are **PENDING (Q-010)** — no validator may enforce an invented codec/format catalog or protection mechanic; rejection must be context-based (not a fabricated format list).
- **Required / Optional:** Required for video upload.
- **Allowed Values:** Valid video files within confirmed context; format catalog deferred.
- **Minimum / Maximum Limits:** File-size limit not confirmed (FIL-09); hosting mechanics pending.
- **Error Message:** "The file is not a valid Lesson video."
- **Related Documents:** `20_File_Storage.md` §6, §12; `00_Project_Context.md` §15.1 (Q-010); `32_Business_Rules.md` §15.

### LSN-05 — Student Lesson Access Eligibility
- **Validation ID:** LSN-05
- **Field Name:** Lesson reference + Student context (access request)
- **Validation Rule:** The Lesson must belong to a Teacher with whom the Student has a valid relationship, must be active (not archived) for active access, and access must not create cross-Teacher visibility. Lessons of unrelated Teachers are invalid-as-invisible (GEN-12).
- **Required / Optional:** Required for access.
- **Allowed Values:** Active Lessons of the Student's own Teachers.
- **Minimum / Maximum Limits:** —
- **Error Message:** "This Lesson is not available."
- **Related Documents:** `02_Software_Requirements.md` Part 3; `10_API_Design.md` §21; `20_File_Storage.md` §6, §14; `32_Business_Rules.md` §15.

### LSN-06 — No Marketplace Exposure (Rejection Rule)
- **Validation ID:** LSN-06
- **Field Name:** Any Lesson discovery/browse input (public or cross-Teacher)
- **Validation Rule:** Any request shape that would browse, discover, or list Lessons across Teachers (public catalog, marketplace query, cross-Teacher Lesson listing) is invalid — Lesson content has no discovery surface in Version 1.
- **Required / Optional:** Prohibited.
- **Allowed Values:** None.
- **Minimum / Maximum Limits:** —
- **Error Message:** "Not found." (404 — no discovery surface exists to validate against)
- **Related Documents:** `07_Data_Dictionary.md` §18 (Lessons are not marketplace courses); `32_Business_Rules.md` §15 (BR-018), §26.

**Cross-referenced rules:** File-layer video checks are §18; Student/Parent read integrity via GEN-09/12 and PAR-02; archived Lesson behavior per `00_Project_Context.md` §11 (playback details PROPOSED per `00` §12.7 — not hardened).

---

# 16. Subscription Validation

**Authoritative sources:** `02_Software_Requirements.md` Part 5 (Subscription & Billing validations, Flow A payment-status validations); `07_Data_Dictionary.md` §26 (Subscription), §32 (Billing Cycle), §12 (Student Enrollment); `17_Subscription_Billing.md`; `10_API_Design.md` §25, §30; `32_Business_Rules.md` §16–§17; Q-013 (flat vs tiers — PENDING).

### SUB-01 — Billing Cycle Calendar Month
- **Validation ID:** SUB-01
- **Field Name:** Cycle Start Date; Cycle End Date; Billing Cycle reference
- **Validation Rule:** A Billing Cycle must start on the **first day** of a calendar month and end on the **last day** of the same calendar month; the two dates must belong to the same month. Non-calendar-month cycles are rejected (409 on conflicting record creation). A new Billing Cycle begins automatically on the first day of the next month.
- **Required / Optional:** Required wherever a Billing Cycle is specified.
- **Allowed Values:** First day → last day of one calendar month.
- **Minimum / Maximum Limits:** Start = day 1 of month; End = last day of same month.
- **Error Message:** "The Billing Cycle must start on the first day and end on the last day of the same calendar month."
- **Related Documents:** `07_Data_Dictionary.md` §32; `10_API_Design.md` §30 (`/platform/billing-cycles`); `02_Software_Requirements.md` Part 5; `32_Business_Rules.md` §17 (D-006).

### SUB-02 — Billable Student Count Input Basis
- **Validation ID:** SUB-02
- **Field Name:** Billable Student Count (calculation input)
- **Validation Rule:** The count must be calculated from **Enrollment duration only**: an Enrollment is Billable when its duration in the Billing Cycle is **strictly greater than 15 calendar days** (15 days exactly is not Billable; 16 days is). Validation must reject any calculation input derived from Attendance, login activity, Homework, Exam, or Lesson activity.
- **Required / Optional:** System-calculated; required for Subscription calculation.
- **Allowed Values:** Non-negative integers derived from Enrollment duration only.
- **Minimum / Maximum Limits:** Billable threshold: > 15 calendar days (BR-008); count ≥ 0.
- **Error Message:** — (system-calculation integrity; misuse is a 422/operational defect)
- **Related Documents:** `00_Project_Context.md` §9 (BR-008); `02_Software_Requirements.md` Part 5; `24_Testing_Strategy.md` §5.2; `17_Subscription_Billing.md`; `32_Business_Rules.md` §16.

### SUB-03 — Price Per Student
- **Validation ID:** SUB-03
- **Field Name:** Price Per Student
- **Validation Rule:** Must come from Platform-level pricing owned by the Super Admin and must reflect the price applicable to that Billing Cycle — historical Subscriptions keep their period price. The flat-versus-tiers model is **PENDING (Q-013)**: pricing validation must not harden tier structures or reject/assume either model beyond the confirmed ownership rule.
- **Required / Optional:** Required for calculation (Platform-configured default).
- **Allowed Values:** Valid Platform-configured monetary values.
- **Minimum / Maximum Limits:** Valid monetary value; no confirmed cap.
- **Error Message:** "The pricing configuration is invalid."
- **Related Documents:** `07_Data_Dictionary.md` §26, §29; `02_Software_Requirements.md` Part 5; `10_API_Design.md` §30; `00_Project_Context.md` §9 (BR-015), §15.1 (Q-013).

### SUB-04 — Subscription Amount Identity
- **Validation ID:** SUB-04
- **Field Name:** Subscription Amount
- **Validation Rule:** Must equal **Billable Students × Price Per Student** (Monthly Subscription formula). A stored or submitted amount failing this identity is invalid; the amount is calculated, never client-supplied.
- **Required / Optional:** System-calculated.
- **Allowed Values:** Count × Price Per Student of the period.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (calculation integrity; see §21 INT-08)
- **Related Documents:** `00_Project_Context.md` §9 (BR-008); `07_Data_Dictionary.md` §26; `17_Subscription_Billing.md`; `32_Business_Rules.md` §17.

### SUB-05 — Subscription Status Value
- **Validation ID:** SUB-05
- **Field Name:** Subscription Status
- **Validation Rule:** Must represent a valid Flow A status for the Subscription lifecycle (documented defaults: Pending or Unpaid per `07_Data_Dictionary.md` §26); status is recorded only — no payment processing is initiated by any status value.
- **Required / Optional:** Required.
- **Allowed Values:** Confirmed Flow A status set.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Subscription status is invalid."
- **Related Documents:** `07_Data_Dictionary.md` §26; `10_API_Design.md` §25; `32_Business_Rules.md` §16–§17.

### SUB-06 — Calculation Request Validity
- **Validation ID:** SUB-06
- **Field Name:** Teacher reference; Billing Cycle (calculate payload)
- **Validation Rule:** The Teacher must be valid and the Billing Cycle must satisfy SUB-01; calculation requests for a foreign-or-missing Teacher or a malformed cycle are rejected (404/422).
- **Required / Optional:** Both required.
- **Allowed Values:** Valid Teachers; calendar-month cycles.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Teacher is invalid." / SUB-01 message.
- **Related Documents:** `10_API_Design.md` §25 (`/subscriptions/calculate`); `02_Software_Requirements.md` Part 5.

### SUB-07 — Subscription Status Update Payload
- **Validation ID:** SUB-07
- **Field Name:** New status; reason/reference
- **Validation Rule:** Updates require a valid Flow A Subscription record, a valid target status (SUB-05), and a reason/reference per contract; Subscription changes are audited (Subscription Change event). Non-payment enforcement semantics (grace, suspension) are **PENDING (Q-005)**: no status transition may encode enforcement behavior in Version 1.
- **Required / Optional:** Status required; reason/reference per contract.
- **Allowed Values:** Confirmed Flow A statuses; bounded reason text.
- **Minimum / Maximum Limits:** GEN-04 (reason).
- **Error Message:** "The selected Subscription is invalid." / "The reason field is required."
- **Related Documents:** `10_API_Design.md` §25 (`/subscriptions/{id}/status`); `00_Project_Context.md` §10, §15.1 (Q-005); `32_Business_Rules.md` §16, §25.

**Cross-referenced rules:** Payment-status recording for Flow A is §17 (PAY-xx); billing history/snapshot integrity (D-003 snapshots PROPOSED) is §21; Q-013/Q-005 protection is §23 (EXC-05).

---

# 17. Payment Validation

**Authoritative sources:** `02_Software_Requirements.md` Part 5 (Flow A payment-status validations), Part 3 (Student Payments validations), Part 4 (Parent Payments validations); `07_Data_Dictionary.md` §25 (Payment), §33 (Student Fee Status); `10_API_Design.md` §24; `32_Business_Rules.md` §17–§18; D-002.

### PAY-01 — Payment Flow Value
- **Validation ID:** PAY-01
- **Field Name:** Payment Flow
- **Validation Rule:** Every payment-status record must be exactly **Flow A** (Teacher → Platform Subscription) or **Flow B** (Student/Parent fees → Teacher); records and payloads must never conflate the two — Flow B data in a Flow A operation (or vice versa) is invalid.
- **Required / Optional:** Required (context-derived).
- **Allowed Values:** Flow A, Flow B.
- **Minimum / Maximum Limits:** Exactly one value, matching the endpoint's flow.
- **Error Message:** "The payment record is invalid for this context."
- **Related Documents:** `07_Data_Dictionary.md` §25; `10_API_Design.md` §24; `00_Project_Context.md` §9 (BR-008/BR-009/BR-015/BR-019); `32_Business_Rules.md` §18.

### PAY-02 — Flow A Payment-Status Target
- **Validation ID:** PAY-02
- **Field Name:** Teacher reference; Billing Cycle; Related Subscription Reference
- **Validation Rule:** A Flow A payment status must relate to a valid Flow A Subscription (valid Teacher, valid calendar-month Billing Cycle per SUB-01); the record must not require or process payment gateway data.
- **Required / Optional:** Required references.
- **Allowed Values:** Valid Flow A Subscriptions.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Subscription is invalid."
- **Related Documents:** `02_Software_Requirements.md` Part 5; `10_API_Design.md` §24 (`/platform/payment-status`); `07_Data_Dictionary.md` §25.

### PAY-03 — Flow B Payment-Status Target
- **Validation ID:** PAY-03
- **Field Name:** Student reference; Group context; Pricing Type
- **Validation Rule:** The Student and Group must belong to the Teacher relationship/current Teacher Workspace (GEN-09); the Pricing Type must be Monthly or Per Lesson (GRP-04); Flow B derives from Group Price and Pricing Type.
- **Required / Optional:** Required references.
- **Allowed Values:** In-workspace Students/Groups; Monthly, Per Lesson.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected Student is invalid." / "The selected Group is invalid."
- **Related Documents:** `10_API_Design.md` §24 (`/teacher-workspace/payment-status`); `07_Data_Dictionary.md` §33; `32_Business_Rules.md` §18.

### PAY-04 — Payment Amount
- **Validation ID:** PAY-04
- **Field Name:** Amount
- **Validation Rule:** Optional; when recorded it must be a valid monetary amount, and for Flow B it must derive from the Group's pricing context when recorded per `07_Data_Dictionary.md` §33.
- **Required / Optional:** Optional.
- **Allowed Values:** Valid monetary amounts.
- **Minimum / Maximum Limits:** Non-negative per money standards (GEN-08); no confirmed cap.
- **Error Message:** "The Amount must be a valid monetary amount."
- **Related Documents:** `07_Data_Dictionary.md` §25, §33; `10_API_Design.md` §24.

### PAY-05 — Payment Status Value
- **Validation ID:** PAY-05
- **Field Name:** Payment Status
- **Validation Rule:** Must represent a recorded status only (documented defaults include Unpaid or Pending per `07_Data_Dictionary.md` §25, §33); no value may initiate, imply, or represent an in-platform transaction, gateway call, or processed payment (BR-019).
- **Required / Optional:** Required.
- **Allowed Values:** Confirmed status-only values.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected payment status is invalid."
- **Related Documents:** `07_Data_Dictionary.md` §25, §33; `00_Project_Context.md` §9 (BR-019); `32_Business_Rules.md` §18.

### PAY-06 — Payment Processing Rejection
- **Validation ID:** PAY-06
- **Field Name:** Any gateway/transaction field (card data, gateway tokens, transaction identifiers, processor references)
- **Validation Rule:** Payment gateway fields and any payload attempting in-platform payment processing are **rejected** as out of scope — the validator rejects the input category itself, not merely its values.
- **Required / Optional:** Prohibited.
- **Allowed Values:** None.
- **Minimum / Maximum Limits:** —
- **Error Message:** "Online payment processing is not available." (input category rejected)
- **Related Documents:** `10_API_Design.md` §2, §24, §29 analog; `02_Software_Requirements.md` Part 5; D-002; `32_Business_Rules.md` §18, §26.

### PAY-07 — Student / Parent Payment Immutability
- **Validation ID:** PAY-07
- **Field Name:** Any payment-status write field presented by Student or Parent
- **Validation Rule:** Students and Parents cannot record or modify payment status (themselves or others); write attempts are denied (403). Their read inputs (Teacher and period filters) must reference their own relationships only (GEN-09).
- **Required / Optional:** Prohibited for Student/Parent actors.
- **Allowed Values:** Read-only filters within own relationships.
- **Minimum / Maximum Limits:** —
- **Error Message:** "You do not have permission to perform this action." (403)
- **Related Documents:** `02_Software_Requirements.md` Part 3/Part 4; `10_API_Design.md` §24; `09_Permission_Matrix.md`; `32_Business_Rules.md` §6–§7, §18.

**Cross-referenced rules:** Flow A Subscription validity is §16; archive-history of payment records per `00_Project_Context.md` §11 (RET rules, `32_Business_Rules.md` §24); Supervisor authority for recorded-by fields follows `09_Permission_Matrix.md` (`platform.payment_status.record` vs `teacher_workspace.payment_status.record`).

---

# 18. File Upload Validation

**Authoritative sources:** `20_File_Storage.md` §3, §10, §11 (Upload Validation Rules), §12 (File Size Limits), §13 (Storage Security), §14 (Access Control), §21 (Error Handling); `23_Security_Standards.md` §9 (Upload Validation, constraints); `10_API_Design.md` §11, §28; `07_Data_Dictionary.md` §28 (File Attachment); `32_Business_Rules.md` §21.

### FIL-01 — Uploader Authentication
- **Validation ID:** FIL-01
- **Field Name:** Authenticated user context (upload request)
- **Validation Rule:** The uploader must be authenticated; anonymous uploads are denied before any file inspection.
- **Required / Optional:** Required (layer 1 of the documented upload-validation stack).
- **Allowed Values:** Valid authenticated contexts.
- **Minimum / Maximum Limits:** —
- **Error Message:** "Authentication is required." (401)
- **Related Documents:** `23_Security_Standards.md` §9.1; `20_File_Storage.md` §11, §21.

### FIL-02 — Uploader Authorization For Owning Resource
- **Validation ID:** FIL-02
- **Field Name:** Owning resource reference + permission context
- **Validation Rule:** The uploader must be authorized for the owning resource and scope (e.g., workspace file upload permission, Student submission permission); the owning resource must exist in the authorized context.
- **Required / Optional:** Required.
- **Allowed Values:** Authorized owning resources only.
- **Minimum / Maximum Limits:** —
- **Error Message:** "You do not have permission to perform this action." (403)
- **Related Documents:** `23_Security_Standards.md` §9.1; `20_File_Storage.md` §11, §14; `10_API_Design.md` §28.

### FIL-03 — Teacher Workspace File Ownership
- **Validation ID:** FIL-03
- **Field Name:** Teacher Workspace Reference (file reference)
- **Validation Rule:** Teacher-owned files must belong to the current Teacher Workspace; cross-Teacher file association, access, archive, or restore attempts are invalid-as-invisible (GEN-09/12). Directory location never grants access.
- **Required / Optional:** Required for Teacher-owned files.
- **Allowed Values:** The current Teacher Workspace.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected file is invalid."
- **Related Documents:** `20_File_Storage.md` §10, §11, §13, §14; `07_Data_Dictionary.md` §28; `32_Business_Rules.md` §21.

### FIL-04 — Student Submission Ownership Chain
- **Validation ID:** FIL-04
- **Field Name:** Homework reference + Student context (submission file)
- **Validation Rule:** A Student submission file must belong to Homework assigned to the authenticated Student through a valid Teacher relationship; unassigned-Homework uploads are denied (HW-05 restated for the file layer).
- **Required / Optional:** Required for Student upload.
- **Allowed Values:** Assigned active Homework of the authenticated Student.
- **Minimum / Maximum Limits:** —
- **Error Message:** "This Homework is not available for submission."
- **Related Documents:** `20_File_Storage.md` §7, §11, §21; `10_API_Design.md` §28 (`/student/homework/{id}/files`).

### FIL-05 — File Type Per Context
- **Validation ID:** FIL-05
- **Field Name:** File Type
- **Validation Rule:** The file type must match the owning resource's confirmed context: Homework assignment files — **Image or PDF** (Text may be logical content rather than a binary file); Student submission binaries — **Image or PDF**; Lesson — Teacher-owned private **video**. Any other type is rejected for that context.
- **Required / Optional:** Required.
- **Allowed Values:** Per context: Image/PDF (Homework); Image/PDF (submission); video (Lesson).
- **Minimum / Maximum Limits:** Exact image formats, PDF version, and video codec catalog are **not confirmed** (§3.4) — enforcement is context-based, not an invented format list.
- **Error Message:** "This file type is not supported for this content."
- **Related Documents:** `20_File_Storage.md` §3, §7, §11; `23_Security_Standards.md` §9.2; `07_Data_Dictionary.md` §28.

### FIL-06 — MIME Content Verification
- **Validation ID:** FIL-06
- **Field Name:** File binary (content)
- **Validation Rule:** The actual file content must match the declared type — the MIME type is verified against content, not trusted from the filename or client header.
- **Required / Optional:** Required.
- **Allowed Values:** Files whose content matches an allowed type (FIL-05).
- **Minimum / Maximum Limits:** —
- **Error Message:** "The file content does not match its type."
- **Related Documents:** `23_Security_Standards.md` §9.1, §10.3; `28_Coding_Standards.md` §17.2.

### FIL-07 — Filename Sanitization
- **Validation ID:** FIL-07
- **Field Name:** Filename
- **Validation Rule:** Filenames must be sanitized to prevent path traversal; names and storage paths are never authorization proofs and never identify ownership.
- **Required / Optional:** Required (sanitization pass).
- **Allowed Values:** Sanitized names; traversal sequences rejected/neutralized.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The filename is invalid."
- **Related Documents:** `23_Security_Standards.md` §9.1, §10.3; `20_File_Storage.md` §9.

### FIL-08 — Early Size Validation (Without Invented Limit)
- **Validation ID:** FIL-08
- **Field Name:** File size
- **Validation Rule:** Large files must be validated early to prevent resource exhaustion on cPanel Shared Hosting; **no numeric size limit is confirmed** for Version 1 — the Platform must not present a fabricated maximum, and any future approved limit applies without altering FIL-05's type rules.
- **Required / Optional:** Required (early guard); limit value deferred.
- **Allowed Values:** Sizes within approved limits when defined; hosting-feasible sizes meanwhile.
- **Minimum / Maximum Limits:** Limit not confirmed (`20_File_Storage.md` §12; §3.4 of this document).
- **Error Message:** "The file could not be accepted." (no fabricated "exceeds N MB" message)
- **Related Documents:** `20_File_Storage.md` §12, §21; `23_Security_Standards.md` §9.4.

### FIL-09 — Video Homework Rejection
- **Validation ID:** FIL-09
- **Field Name:** File Type (Homework contexts)
- **Validation Rule:** A video file offered as Homework (assignment or submission) is rejected — video Homework is out of scope (BR-021), distinct from FIL-05's generic mismatch.
- **Required / Optional:** Prohibited for Homework contexts.
- **Allowed Values:** None (in Homework contexts).
- **Minimum / Maximum Limits:** —
- **Error Message:** "Video Homework is not supported. Homework supports Text, Image, and PDF only."
- **Related Documents:** `00_Project_Context.md` §9 (BR-021); `20_File_Storage.md` §3, §7, §21; `10_API_Design.md` §11.

### FIL-10 — Active Owning Resource Check
- **Validation ID:** FIL-10
- **Field Name:** Owning resource state
- **Validation Rule:** Archived/inactive owning resources must not be treated as active upload targets unless restored/authorized per Archive rules (GEN-11).
- **Required / Optional:** Applies to every upload.
- **Allowed Values:** Active owning resources.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected resource is not available." (409)
- **Related Documents:** `20_File_Storage.md` §11, §21; `00_Project_Context.md` §11.

### FIL-11 — Executable / Archive Format Rejection
- **Validation ID:** FIL-11
- **Field Name:** File Type (all contexts)
- **Validation Rule:** Executable and archive formats are denied in every confirmed context (they appear on the documented deny list alongside video-in-Homework); no upload context silently widens to accept them.
- **Required / Optional:** Prohibited.
- **Allowed Values:** None.
- **Minimum / Maximum Limits:** —
- **Error Message:** "This file type is not supported."
- **Related Documents:** `23_Security_Standards.md` §9.2 (Denied column); `20_File_Storage.md` §3.

### FIL-12 — Storage Baseline Conformance
- **Validation ID:** FIL-12
- **Field Name:** Storage reference (accepted file)
- **Validation Rule:** Accepted files are stored as references via Laravel Public Storage inside the confirmed organization (workspace namespacing and `student-homework` area); S3/external storage inputs or references are not part of Version 1 validation; a storage reference alone never proves access.
- **Required / Optional:** Structural rule (system-side).
- **Allowed Values:** Laravel Public Storage references per confirmed organization.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (system-consistency rule)
- **Related Documents:** `20_File_Storage.md` §9–§10; `07_Data_Dictionary.md` §28; `32_Business_Rules.md` §21.

**Cross-referenced rules:** Parent upload denial is PAR-05 (denied before file inspection); scanning/malware inspection is not confirmed — EXC-05/§23 protects against silently adding validators; error non-disclosure for files is `20_File_Storage.md` §21 and GEN-12.

---

# 19. Search Validation

**Authoritative sources:** `22_Search_Filtering.md` (search, filtering, sorting, pagination standards); `10_API_Design.md` §7 (Pagination), §8 (Filtering), §9 (Sorting); `12_Frontend_Architecture.md` (route/query state); `32_Business_Rules.md` §22.

### SRC-01 — Query Minimum Length
- **Validation ID:** SRC-01
- **Field Name:** Search query text
- **Validation Rule:** Search minimum length is enforced to guard performance; the documented behavior applies a contextual minimum before backend search work runs. The exact minimum is documented search behavior (owning: `22_Search_Filtering.md`), not a value invented here.
- **Required / Optional:** Applies whenever a query is submitted.
- **Allowed Values:** Queries meeting the documented minimum, or empty handled per documented behavior.
- **Minimum / Maximum Limits:** Contextual minimum per `22_Search_Filtering.md`; not restated as a number.
- **Error Message:** "Please enter a longer search term."
- **Related Documents:** `22_Search_Filtering.md`; `32_Business_Rules.md` §22.

### SRC-02 — Query Maximum Length
- **Validation ID:** SRC-02
- **Field Name:** Search query text
- **Validation Rule:** Query text is bounded; over-limit input is rejected or safely constrained per documented search behavior.
- **Required / Optional:** Applies whenever a query is submitted.
- **Allowed Values:** Bounded text.
- **Minimum / Maximum Limits:** Maximum per `22_Search_Filtering.md` (bounded; value owned there).
- **Error Message:** "The search term is too long."
- **Related Documents:** `22_Search_Filtering.md`; `23_Security_Standards.md` §10.2.

### SRC-03 — Query Content Safety
- **Validation ID:** SRC-03
- **Field Name:** Search query text
- **Validation Rule:** Query content is validated against injection and abuse: input is constrained to documented allowed characters/behavior, never interpolated into queries unsafely, and never used to bypass authorization.
- **Required / Optional:** Always.
- **Allowed Values:** Documented allowed characters/behavior.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The search term contains unsupported characters." (per documented behavior)
- **Related Documents:** `22_Search_Filtering.md`; `23_Security_Standards.md` §10–§11.

### SRC-04 — Empty Query Handling
- **Validation ID:** SRC-04
- **Field Name:** Search query text (empty)
- **Validation Rule:** An empty query follows documented empty-query behavior (owning: `22_Search_Filtering.md`) and must not produce an error state or leaked-results state.
- **Required / Optional:** Applies when the query is empty.
- **Allowed Values:** Documented empty-query outcome.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (empty handled without error per documented behavior)
- **Related Documents:** `22_Search_Filtering.md`.

### SRC-05 — Scope Before Search
- **Validation ID:** SRC-05
- **Field Name:** Search scope context (role/scope implied by the request)
- **Validation Rule:** Search resolves the authorized scope first — results come only from records the user is authorized to see: current Teacher Workspace for Teacher-side, own records for Students, linked Students for Parents, Platform scope for Super Admin. A query can never widen scope.
- **Required / Optional:** Always.
- **Allowed Values:** Authorized scopes only.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (scope is enforced, not reported)
- **Related Documents:** `22_Search_Filtering.md`; `10_API_Design.md` §8; `32_Business_Rules.md` §22; `24_Testing_Strategy.md` §3.1.

### SRC-06 — Filter Reference Validity
- **Validation ID:** SRC-06
- **Field Name:** Filter values (`group_id`, `student_id`, `educational_grade_id`, `teacher_id`, `teacher_workspace_id`, `billing_cycle`, `status`)
- **Validation Rule:** A filter reference must be a valid identifier **and** belong to the authorized scope: `group_id`/`educational_grade_id` must belong to the permitted Teacher Workspace; `student_id` only when the Student is owned, linked, or visible; `teacher_id` only in Platform or visible relationship contexts; cross-Teacher filters are rejected unless Platform-level visibility applies.
- **Required / Optional:** Optional filters; validated when present.
- **Allowed Values:** In-scope valid identifiers; confirmed filter names only.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected filter is invalid." (neutral, non-disclosing)
- **Related Documents:** `10_API_Design.md` §8; `22_Search_Filtering.md`; `32_Business_Rules.md` §22.

### SRC-07 — Date Range Filters
- **Validation ID:** SRC-07
- **Field Name:** `from_date`; `to_date`; report start/end dates
- **Validation Rule:** Both must be valid dates; `from_date` must not be after `to_date` (GEN-06 restated for filters). A Billing Cycle filter follows the calendar-month rule (SUB-01).
- **Required / Optional:** Optional; ordering enforced when both present.
- **Allowed Values:** Valid ordered dates.
- **Minimum / Maximum Limits:** from ≤ to.
- **Error Message:** "The start date must not be after the end date."
- **Related Documents:** `10_API_Design.md` §8; `22_Search_Filtering.md`; `17_Subscription_Billing.md`.

### SRC-08 — Status Filter Values
- **Validation ID:** SRC-08
- **Field Name:** `status` filter
- **Validation Rule:** Must be a valid resource status for the queried resource — e.g., active, archived, pending, submitted, paid, unpaid, or another valid resource status per endpoint; unknown status values are invalid.
- **Required / Optional:** Optional filter.
- **Allowed Values:** Per-resource confirmed status values.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected status is invalid."
- **Related Documents:** `10_API_Design.md` §8; `22_Search_Filtering.md`.

### SRC-09 — Archived Visibility In Results
- **Validation ID:** SRC-09
- **Field Name:** Archived-state handling of results
- **Validation Rule:** Archived records never appear in normal searches; they appear only in historical/report contexts or when explicitly requested by authorized users, clearly indicated as archived. Search input cannot surface archived records as active.
- **Required / Optional:** Always.
- **Allowed Values:** Active records for normal search; archived only via permitted historical paths.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (presentation rule; see `22_Search_Filtering.md`)
- **Related Documents:** `22_Search_Filtering.md`; `10_API_Design.md` §8; `00_Project_Context.md` §11; `32_Business_Rules.md` §22, §24.

### SRC-10 — Sort Whitelist
- **Validation ID:** SRC-10
- **Field Name:** `sort` parameter
- **Validation Rule:** Sorting uses only documented sortable fields for the resource (e.g., `created_at`, `updated_at`, `name`, `status`, `date` where applicable); a leading minus sign indicates descending where supported; unsupported sort fields are rejected or ignored **consistently** per API standard behavior — never silently reinterpreted.
- **Required / Optional:** Optional.
- **Allowed Values:** Whitelisted sort fields per resource, with optional leading `-`.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The requested sort is not supported." (when rejected)
- **Related Documents:** `10_API_Design.md` §9; `22_Search_Filtering.md`.

### SRC-11 — Pagination Bounds
- **Validation ID:** SRC-11
- **Field Name:** `page`; `per_page`
- **Validation Rule:** `page` must be a positive integer; `per_page` must be within allowed limits ("Number of records per page within allowed limits", `10_API_Design.md` §7); pagination applies after authorization and scope filtering so hidden records never affect visible results.
- **Required / Optional:** Optional with documented defaults.
- **Allowed Values:** Positive integers; per_page within allowed limits.
- **Minimum / Maximum Limits:** page ≥ 1; per_page bounds per `10_API_Design.md` §7 / `22_Search_Filtering.md`.
- **Error Message:** "The page number is invalid." / "The per page value is invalid."
- **Related Documents:** `10_API_Design.md` §7; `22_Search_Filtering.md`.

### SRC-12 — Flow Separation In Search And Reports
- **Validation ID:** SRC-12
- **Field Name:** Any filter/query in Subscription or payment-status contexts
- **Validation Rule:** Flow A and Flow B filters remain separate: Subscription queries use Platform-scoped Flow A criteria (including `billing_cycle`); Teacher payment-status queries use workspace Flow B criteria; a query may not join the flows (INT-09 restated for search).
- **Required / Optional:** Always.
- **Allowed Values:** Flow-correct criteria only.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The selected filter is invalid." (neutral)
- **Related Documents:** `10_API_Design.md` §8; `22_Search_Filtering.md`; `32_Business_Rules.md` §17–§18, §22.

**Cross-referenced rules:** Report-visibility rules (who may request which report) are authorization (`09_Permission_Matrix.md`) consolidated in `32_Business_Rules.md` §19; result presentation (empty states, archived marking) per `13_UI_UX_Guidelines.md`/`14_UI_Components.md`.

---

# 20. API Request Validation

**Authoritative sources:** `10_API_Design.md` §2 (Standards), §5 (Versioning), §6 (Error Response Standard), §10 (Validation Response Standard), §11 (File Upload Standard), §12 (Naming), §13–§30 (endpoint tables); `11_Backend_Architecture.md` (request pipeline); `28_Coding_Standards.md` §17; `23_Security_Standards.md` §10.

### API-01 — Request Body Format
- **Validation ID:** API-01
- **Field Name:** Request body (content structure)
- **Validation Rule:** Structured request/response bodies are JSON; file uploads use multipart form data where a binary file is included with JSON-compatible metadata. Malformed bodies are rejected before field validation.
- **Required / Optional:** Required for state-changing operations.
- **Allowed Values:** Valid JSON documents; multipart payloads for file endpoints.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The request could not be understood." (400)
- **Related Documents:** `10_API_Design.md` §2, §11; `28_Coding_Standards.md` §17.4.

### API-02 — Endpoint Existence And Version
- **Validation ID:** API-02
- **Field Name:** Request path / version prefix
- **Validation Rule:** Requests route under `/api/v1`; requests to unsupported routes (including any notification endpoint, which does not exist in Version 1) return 404 or 403 according to routing and authorization behavior — no payload makes them valid.
- **Required / Optional:** Always.
- **Allowed Values:** Documented Version 1 endpoints only.
- **Minimum / Maximum Limits:** —
- **Error Message:** "Not found." (404)
- **Related Documents:** `10_API_Design.md` §5, §29; `32_Business_Rules.md` §20, §26.

### API-03 — Authenticated Context Presence
- **Validation ID:** API-03
- **Field Name:** Authentication context (per request)
- **Validation Rule:** Protected endpoints require an authenticated user context (AUT-14 restated for the API layer); unauthenticated protected requests return 401.
- **Required / Optional:** Required for protected endpoints.
- **Allowed Values:** Valid Sanctum contexts.
- **Minimum / Maximum Limits:** —
- **Error Message:** "Authentication is required." (401)
- **Related Documents:** `10_API_Design.md` §3, §6; `23_Security_Standards.md` §3.

### API-04 — Path Identifier Shape And Visibility
- **Validation ID:** API-04
- **Field Name:** Path identifiers (`{teacher_id}`, `{student_id}`, `{group_id}`, `{exam_id}`, `{file_id}`, etc.)
- **Validation Rule:** Identifiers must be positive integers (`23_Security_Standards.md` §10.2) and must resolve to a record visible in the actor's scope; unresolvable or out-of-scope identifiers return 404 without distinguishing "not found" from "not visible".
- **Required / Optional:** Required where present in route.
- **Allowed Values:** Positive integers resolving to visible records.
- **Minimum / Maximum Limits:** Minimum 1.
- **Error Message:** "Not found." (404)
- **Related Documents:** `10_API_Design.md` §6; `23_Security_Standards.md` §10.2, §10.4; GEN-09/12.

### API-05 — Required Permission Presence
- **Validation ID:** API-05
- **Field Name:** Permission context (per request, against `09_Permission_Matrix.md`)
- **Validation Rule:** The actor must hold the endpoint's documented permission (e.g., `teacher_workspace.attendance.record`, `platform.subscription.calculate_billable_students`); missing permission returns 403 without exposing restricted data. Authorization validation is distinct from input validation and is never skipped.
- **Required / Optional:** Required per endpoint contract.
- **Allowed Values:** Documented permission holders for the endpoint.
- **Minimum / Maximum Limits:** —
- **Error Message:** "You do not have permission to perform this action." (403)
- **Related Documents:** `10_API_Design.md` §4; `08_RBAC.md`; `09_Permission_Matrix.md`.

### API-06 — 422 Validation Failure Shape
- **Validation ID:** API-06
- **Field Name:** Response (validation failure)
- **Validation Rule:** Validation failures use HTTP 422 with `success` = false, `error.code` = `VALIDATION_FAILED`, `error.message` summarizing the failure, and `errors` carrying field-level messages. (Detail in §22.)
- **Required / Optional:** Mandatory response contract.
- **Allowed Values:** The documented shape only.
- **Minimum / Maximum Limits:** —
- **Error Message:** `VALIDATION_FAILED` envelope (see MSG-01).
- **Related Documents:** `10_API_Design.md` §10; `28_Coding_Standards.md` §17.4; `12_Frontend_Architecture.md` §11.

### API-07 — Business-Rule Conflict Shape
- **Validation ID:** API-07
- **Field Name:** Response (state/business conflict)
- **Validation Rule:** Conflicts with a confirmed business rule or current resource state — duplicate Student account, second active Group for the same Teacher, archive/restore state conflicts, non-calendar-month Billing Cycle — return HTTP 409, not 422. Input-format problems are 422; rule/state conflicts are 409.
- **Required / Optional:** Mandatory response discipline.
- **Allowed Values:** 409 for conflicts; 422 for input validation.
- **Minimum / Maximum Limits:** —
- **Error Message:** Conflict summary per §22 (MSG-xx business messages).
- **Related Documents:** `10_API_Design.md` §6; endpoint tables §13–§28 (409 columns).

### API-08 — Prohibited Payload Categories
- **Validation ID:** API-08
- **Field Name:** Payload content (categorical)
- **Validation Rule:** Requests carrying out-of-scope capability payloads are rejected categorically: notification payloads (no endpoint exists), online payment gateway data (PAY-06), notification/payment-gateway settings in Platform Settings, Platform staff account creation, "Login as Teacher" (denied, 403/404), and video Homework formats (FIL-09).
- **Required / Optional:** Prohibited categories.
- **Allowed Values:** None.
- **Minimum / Maximum Limits:** —
- **Error Message:** "Not found." (404) / "You do not have permission to perform this action." (403) / context rejection per the rule section.
- **Related Documents:** `10_API_Design.md` §27, §29, §30; `02_Software_Requirements.md` Part 5; `32_Business_Rules.md` §26.

### API-09 — File Upload Transport
- **Validation ID:** API-09
- **Field Name:** Multipart file payload
- **Validation Rule:** File uploads use multipart form data; the file must be associated with an owning resource and authorized scope; Homework files are limited to Image/PDF, Lesson videos are private; metadata remains JSON-compatible. (Field-level file rules: §18.)
- **Required / Optional:** Required for file endpoints.
- **Allowed Values:** Multipart payloads conforming to §18.
- **Minimum / Maximum Limits:** Per FIL-08.
- **Error Message:** Per FIL-xx messages.
- **Related Documents:** `10_API_Design.md` §11, §28; `20_File_Storage.md` §11.

### API-10 — Transport Security
- **Validation ID:** API-10
- **Field Name:** Transport channel
- **Validation Rule:** HTTPS is required in production; credentials and secrets traverse encrypted connections only. Insecure-channel presentation of secrets is a security violation, not a recoverable input error.
- **Required / Optional:** Required in production.
- **Allowed Values:** HTTPS.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (infrastructure-level)
- **Related Documents:** `10_API_Design.md` §2; `23_Security_Standards.md` §3, §6.1.

### API-11 — Rate-Limit Application
- **Validation ID:** API-11
- **Field Name:** Request rate (per endpoint where limits apply)
- **Validation Rule:** Where rate limits apply (confirmed for login; reset requests), exceeding the limit returns HTTP 429. Thresholds are configuration, not product values (AUT-05).
- **Required / Optional:** Applied where documented.
- **Allowed Values:** Within-limit request rates.
- **Minimum / Maximum Limits:** Values unconfirmed (§3.4).
- **Error Message:** "Too many attempts. Please try again later." (429)
- **Related Documents:** `10_API_Design.md` §6; `23_Security_Standards.md` §3.3, §6.2.

### API-12 — Error Non-Disclosure
- **Validation ID:** API-12
- **Field Name:** Error responses (all)
- **Validation Rule:** Error responses must not expose Teacher-private data, unlinked Student data, another Student's records, another Teacher Workspace, or implementation internals; `error.details` is optional and non-sensitive only.
- **Required / Optional:** Mandatory.
- **Allowed Values:** Non-sensitive details only.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (meta-rule governing all messages; §22)
- **Related Documents:** `10_API_Design.md` §6; `23_Security_Standards.md` §10.4; `28_Coding_Standards.md` §17.4.

**Cross-referenced rules:** Field-level rules for every endpoint's payload live in §4–§19 of this document (endpoint tables in `10_API_Design.md` §13–§30 reference the same confirmed rules); query-string behaviors (page/per_page/sort/filters) are §19; naming of resources follows `10_API_Design.md` §12 canonical naming.

---

# 21. Data Integrity Rules

**Authoritative sources:** `06_Database_Design.md` §12 (Data Integrity Rules), §13 (Referential Integrity Rules); `07_Data_Dictionary.md` (per-entity Business Rules); `24_Testing_Strategy.md` §3, §5.2; `00_Project_Context.md` §9–§11; `32_Business_Rules.md` §21; D-020; D-003 (PROPOSED snapshot mechanics).

These rules bind the **persistence layer** (layer 4 of §2.3): even a perfectly validated API payload must fail if its result would violate an invariant. They are enforced by services/persistence constraints, reconciled with request validation.

### INT-01 — Reference Existence
- **Validation ID:** INT-01
- **Field Name:** All Reference attributes (User Reference, Teacher Reference, Group Reference, etc.)
- **Validation Rule:** Every stored reference must identify an existing valid record of the correct kind; dangling or type-mismatched references cannot be persisted.
- **Required / Optional:** Required (persistence-level).
- **Allowed Values:** Existing records of the referenced entity.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (persistence integrity; surfaced as 422 "The selected {Field Name} is invalid." when triggered by input)
- **Related Documents:** `06_Database_Design.md` §13; `07_Data_Dictionary.md` (all Reference attributes).

### INT-02 — Scope-Match Integrity
- **Validation ID:** INT-02
- **Field Name:** Teacher Workspace Reference on workspace child records (Group Schedule, QR Session, Attendance, Homework Submission, Lesson Video, Question, Exam Attempt, File Attachment, Student Fee Status)
- **Validation Rule:** A child record's Teacher Workspace Reference must **match its parent's**: QR Session matches its Attendance Session's workspace; Attendance matches its Session's; Question matches its Question Bank's; Exam Attempt matches its Exam's; Lesson Video matches its Lesson's; File Attachment matches its owner's; Student Fee Status matches its Student/Group context's. Mismatched persistence is invalid.
- **Required / Optional:** Required.
- **Allowed Values:** Workspace-consistent reference graphs only.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (persistence integrity)
- **Related Documents:** `07_Data_Dictionary.md` §11, §14, §15, §17, §19, §21, §23, §28, §33; `06_Database_Design.md` §12–§13.

### INT-03 — Single Global Student Identity
- **Validation ID:** INT-03
- **Field Name:** Student → User Reference
- **Validation Rule:** A Student has exactly one global account; duplicate Student accounts cannot be persisted, regardless of which surface (self-registration, Teacher-created, activation) submitted the data (AUT-12 enforced at persistence).
- **Required / Optional:** Required.
- **Allowed Values:** One User per Student; one Student account per global identity.
- **Minimum / Maximum Limits:** Exactly 1.
- **Error Message:** "An account with this identity already exists." (409 at the surface)
- **Related Documents:** `06_Database_Design.md` §12; `07_Data_Dictionary.md` §6; `00_Project_Context.md` §9 (BR-001, BR-022).

### INT-04 — One Parent Per Student
- **Validation ID:** INT-04
- **Field Name:** Parent Student Link → Student Reference
- **Validation Rule:** One Student can have only one Parent account in Version 1; a link that would exceed this is invalid at persistence (PAR-01 enforced at integrity).
- **Required / Optional:** Required.
- **Allowed Values:** ≤ 1 Parent link per Student; many Students per Parent allowed.
- **Minimum / Maximum Limits:** Max 1 Parent account per Student.
- **Error Message:** "This Student is already linked to a Parent account." (409)
- **Related Documents:** `06_Database_Design.md` §12; `07_Data_Dictionary.md` §8; `00_Project_Context.md` §9 (BR-020).

### INT-05 — One Active Group Per Student Per Teacher
- **Validation ID:** INT-05
- **Field Name:** Student Enrollment (Student Reference + Teacher Workspace Reference + active window)
- **Validation Rule:** Only one active Group per Student per Teacher at any time; Enrollment periods for the same Teacher Workspace must not overlap actively; Group moves close one period and open another (STU-06/STU-07 enforced at integrity).
- **Required / Optional:** Required.
- **Allowed Values:** Non-overlapping active Enrollments per Student per Teacher.
- **Minimum / Maximum Limits:** Max 1 active Enrollment per Student per Teacher Workspace.
- **Error Message:** "The Student already belongs to an active Group for this Teacher." (409)
- **Related Documents:** `06_Database_Design.md` §12; `07_Data_Dictionary.md` §12; `00_Project_Context.md` §9 (BR-002).

### INT-06 — Teaching Subject Immutability (Persistence)
- **Validation ID:** INT-06
- **Field Name:** Teaching Subject Reference; Selected At
- **Validation Rule:** The Teaching Subject is set once at account creation and cannot be changed thereafter at any layer (TCH-02 enforced at persistence); `Selected At` is set once.
- **Required / Optional:** Required.
- **Allowed Values:** The creation-time value, forever.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The Teaching Subject cannot be changed after account creation."
- **Related Documents:** `07_Data_Dictionary.md` §31; `06_Database_Design.md` §12; `00_Project_Context.md` §9 (BR-016).

### INT-07 — Enrollment-Duration-Only Billing Basis
- **Validation ID:** INT-07
- **Field Name:** Billable Student Count derivation inputs
- **Validation Rule:** Billable Student calculation reads Enrollment duration only; Attendance and login activity are never used in the calculation path (SUB-02 enforced at integrity, including background jobs).
- **Required / Optional:** Required.
- **Allowed Values:** Enrollment-duration derivations only; > 15 calendar days threshold.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (system integrity)
- **Related Documents:** `06_Database_Design.md` §12 (Financial Integrity); `07_Data_Dictionary.md` §12; `00_Project_Context.md` §9 (BR-008); `21_Background_Jobs.md`.

### INT-08 — Subscription Amount Invariant
- **Validation ID:** INT-08
- **Field Name:** Subscription Amount; Billable Student Count; Price Per Student
- **Validation Rule:** Stored Subscription Amount must equal Billable Student Count × Price Per Student for the period (SUB-04 at persistence); pre-approval snapshot immutability mechanics are PROPOSED (D-003) and are not hardened here.
- **Required / Optional:** Required.
- **Allowed Values:** Count × Period price.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (system integrity)
- **Related Documents:** `07_Data_Dictionary.md` §26; `00_Project_Context.md` §9 (BR-008, BR-015); `29_Project_Decisions.md` D-003; `32_Business_Rules.md` §17.

### INT-09 — Flow Separation At Persistence
- **Validation ID:** INT-09
- **Field Name:** Payment Flow; Related Subscription Reference; Related Student Fee Reference
- **Validation Rule:** Flow A records reference Flow A artifacts (Subscription) only; Flow B records reference Flow B artifacts (Student fee context) only; Subscription logic never persists from Flow B data and vice versa; Flow B derives from Group Price and Pricing Type.
- **Required / Optional:** Required.
- **Allowed Values:** Flow-consistent references.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The payment record is invalid for this context."
- **Related Documents:** `06_Database_Design.md` §12; `07_Data_Dictionary.md` §25, §33; D-036.

### INT-10 — Archive-Only Lifecycle At Persistence
- **Validation ID:** INT-10
- **Field Name:** Archived State (all archivable entities)
- **Validation Rule:** No hard delete exists at persistence — records leave active use only via Archive state changes; Archive does not detach historical relationships; restores are explicit and audited. Persistence rejects any operation that would erase a record (BR-005, BR-014).
- **Required / Optional:** Required.
- **Allowed Values:** Active ↔ Archived state transitions only.
- **Minimum / Maximum Limits:** —
- **Error Message:** "This operation is not available." (any deletion attempt)
- **Related Documents:** `00_Project_Context.md` §11; `06_Database_Design.md` §12 (Governance Integrity); `32_Business_Rules.md` §24.

### INT-11 — History Preservation On Structural Change
- **Validation ID:** INT-11
- **Field Name:** Historical Attendance / Homework / Exams / grades (relative to Enrollment movement)
- **Validation Rule:** Student transfers between Groups preserve historical records: history is never moved, deleted, or rewritten by structural changes; historical records keep the Enrollment period and structure as of recording time.
- **Required / Optional:** Required.
- **Allowed Values:** Append-preserved history.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (system integrity)
- **Related Documents:** `06_Database_Design.md` §12; `07_Data_Dictionary.md` §12; `00_Project_Context.md` §9 (BR-007).

### INT-12 — Audit Log Append-Only Immutability
- **Validation ID:** INT-12
- **Field Name:** Audit Log records (all fields)
- **Validation Rule:** Audit Log entries are append-only and immutable, permanently retained, never archived or deleted; any update/delete targeting an Audit Log record is invalid at every layer. Teacher Staff actions are attributed to the Teacher Staff user, never to the Teacher.
- **Required / Optional:** Required.
- **Allowed Values:** Appends only, with the ten confirmed event types (§3.3).
- **Minimum / Maximum Limits:** —
- **Error Message:** — (no user surface accepts Audit Log mutation)
- **Related Documents:** `00_Project_Context.md` §10; `07_Data_Dictionary.md` §27; `32_Business_Rules.md` §25.

### INT-13 — Conditional Timestamp Consistency
- **Validation ID:** INT-13
- **Field Name:** Submitted At, Started At, Recorded At (Exam Attempt, Homework Submission, Student Fee Status, Payment)
- **Validation Rule:** Timestamps that are conditionally required must be present exactly when their condition holds: `Started At` once an attempt starts; `Submitted At` once submitted; `Recorded At` when a status is recorded. All are system-generated, never client-supplied (GEN-05).
- **Required / Optional:** Conditionally required.
- **Allowed Values:** System timestamps consistent with state.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (system integrity)
- **Related Documents:** `07_Data_Dictionary.md` §17, §23, §25, §33.

### INT-14 — No Cross-Workspace Persistence
- **Validation ID:** INT-14
- **Field Name:** Cross-record references (persistence graph)
- **Validation Rule:** No cross-tenant relationships are persisted except through approved global identity relationships (Student identity); reports, search results, file access, and error paths must preserve this isolation end-to-end (D-020).
- **Required / Optional:** Required.
- **Allowed Values:** Workspace-contained graphs plus approved global identity links.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (system integrity; surfaced as GEN-09 failures at the surface)
- **Related Documents:** `29_Project_Decisions.md` D-020; `06_Database_Design.md` §12–§13; `32_Business_Rules.md` §5 (BR-003).

### INT-15 — Valid Status/State At Persistence
- **Validation ID:** INT-15
- **Field Name:** All Status fields (Account Status, Workspace Status, Subscription Status, Payment Status, Fee Status, Cycle Status, Session Status, Link Status, etc.)
- **Validation Rule:** Persisted statuses must represent allowed states for their entity (documented defaults per `07_Data_Dictionary.md`: e.g., Active defaults; Subscription/Fee defaults Pending or Unpaid; Cycle Active or Closed); invalid state values cannot be persisted even by internal jobs.
- **Required / Optional:** Required.
- **Allowed Values:** Per-entity documented states.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (system integrity)
- **Related Documents:** `07_Data_Dictionary.md` (Status attributes all entities); `06_Database_Design.md` §12.

**Cross-referenced rules:** These integrity rules are the persistent twin of the request rules in §4–§20; `24_Testing_Strategy.md` §5.2 enumerates the unit-test expectations that verify several of them (Billing Cycle boundaries, threshold 15-vs-16 days, duplicate prevention, Pricing Type, question types, Homework formats).

---

# 22. Validation Error Messages

**Authoritative sources:** `10_API_Design.md` §6, §10; `28_Coding_Standards.md` §17.4; `23_Security_Standards.md` §3.3, §10.4; `12_Frontend_Architecture.md` §11; `13_UI_UX_Guidelines.md` §9; `14_UI_Components.md`; Q-015 (Arabic/English message localization — CONFIRMED).

### MSG-01 — The 422 Envelope
- **Validation ID:** MSG-01
- **Field Name:** Validation failure response (all fields)
- **Validation Rule:** Validation failures use HTTP 422 with: `success` = false; `error.code` = `VALIDATION_FAILED`; `error.message` = a summary of the validation failure; `errors` = field-level validation messages. No other shape is produced for input-validation failure.
- **Required / Optional:** Mandatory response contract.
- **Allowed Values:** The documented envelope fields only; `error.details` optional and non-sensitive.
- **Minimum / Maximum Limits:** —
- **Error Message:** Summary example: "The given data failed validation."
- **Related Documents:** `10_API_Design.md` §10; `28_Coding_Standards.md` §17.4; `12_Frontend_Architecture.md` §11.

### MSG-02 — Field-Level Message Mapping
- **Validation ID:** MSG-02
- **Field Name:** `errors` payload
- **Validation Rule:** Each failing field receives its own message, mapped to that field, so the frontend can attach server field messages to the active form; non-field failures are presented at the form level.
- **Required / Optional:** Mandatory on every 422.
- **Allowed Values:** Field-keyed messages; form-level messages for non-field failures.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (structural rule)
- **Related Documents:** `12_Frontend_Architecture.md` §11; `28_Coding_Standards.md` (422 handling); `13_UI_UX_Guidelines.md` §9.

### MSG-03 — Correction-Oriented Content
- **Validation ID:** MSG-03
- **Field Name:** All message text
- **Validation Rule:** Messages explain the input correction needed without exposing private data, internal rules, unlinked Student information, or implementation internals; messages state what to fix, not how validation works internally.
- **Required / Optional:** Mandatory.
- **Allowed Values:** Correction-oriented, non-disclosing text using canonical terminology.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (meta-rule)
- **Related Documents:** `12_Frontend_Architecture.md` §11; `23_Security_Standards.md` §10.4, §19 (error message guidance); `28_Coding_Standards.md` §17.4.

### MSG-04 — Non-Disclosure Messages
- **Validation ID:** MSG-04
- **Field Name:** Authentication, existence, and scope failures
- **Validation Rule:** Login failures never reveal whether the account exists, whether the secret was wrong, or whether the account is archived; password-reset requests never reveal account existence; reference/scope failures never confirm existence ("The selected X is invalid.", never "exists but belongs to another Teacher"); 404 covers not-found and not-visible identically.
- **Required / Optional:** Mandatory.
- **Allowed Values:** Neutral texts only.
- **Minimum / Maximum Limits:** —
- **Error Message:** "The provided credentials are incorrect." / "If an account exists for the provided identifier, reset instructions have been sent." / "The selected {Field Name} is invalid." / "Not found."
- **Related Documents:** `23_Security_Standards.md` §3.3, §6.2, §10.4; `10_API_Design.md` §6.

### MSG-05 — Canonical Terminology In Messages
- **Validation ID:** MSG-05
- **Field Name:** All message text
- **Validation Rule:** Messages use canonical product terms — Teacher Workspace, Educational Grade (never "Class"), Group, Lesson (never "Course"), Teaching Subject, Pricing Type, Attendance, Dynamic QR Code, ID Card, Homework, Question Bank, Bubble Sheet, Exam, Enrollment, Archive (never "Delete"), Audit Log, Subscription, payment status, Flow A, Flow B, Billable Student, Billing Cycle, Student Switcher — and never expose internal identifiers, permission codes, or route names.
- **Required / Optional:** Mandatory.
- **Allowed Values:** Canonical terminology only.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (meta-rule)
- **Related Documents:** `00_Project_Context.md` §19; `13_UI_UX_Guidelines.md`; `30_Project_Glossary.md`; `32_Business_Rules.md` §1.

### MSG-06 — Form Presentation Of Errors
- **Validation ID:** MSG-06
- **Field Name:** Submitted invalid form (presentation behavior)
- **Validation Rule:** An error summary appears at the start of a submitted invalid form; each field error is associated with its field; focus is managed after validation failures; valid entered values are preserved after correctable failures.
- **Required / Optional:** Mandatory presentation behavior.
- **Allowed Values:** Summary + field-associated messages; preserved inputs.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (presentation rule)
- **Related Documents:** `13_UI_UX_Guidelines.md` §9; `14_UI_Components.md` (form-field and form-status contracts); `12_Frontend_Architecture.md` §10.

### MSG-07 — Business-Conflict Messages (409)
- **Validation ID:** MSG-07
- **Field Name:** Conflict responses
- **Validation Rule:** A 409 conflict message names the violated rule in user terms without internal detail; the standard set is: "An account with this identity already exists."; "The Student already belongs to an active Group for this Teacher."; "The Teaching Subject cannot be changed after account creation."; "The selected {Record} is not available." (archived/inactive); "This Teacher account is already archived." (state conflict); "The Exam has no valid active Questions."; "Attendance is already recorded for this Student in this session."
- **Required / Optional:** Mandatory for 409 responses.
- **Allowed Values:** Rule-naming, non-disclosing texts.
- **Minimum / Maximum Limits:** —
- **Error Message:** The standard conflict set above.
- **Related Documents:** `10_API_Design.md` §6; domain sections §4–§17 of this document.

### MSG-08 — No Sensitive Detail In Errors
- **Validation ID:** MSG-08
- **Field Name:** `error.details`, `errors`, and all error surfaces
- **Validation Rule:** Errors must not reveal storage paths, file-system implementation, credentials, secrets, signed-access mechanics, private Lesson information, unlinked Student files, another Teacher Workspace's file existence, stack traces, table/column names, or internal identifiers — including in file, search, and report error paths.
- **Required / Optional:** Mandatory.
- **Allowed Values:** Non-sensitive detail only.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (meta-rule; see `20_File_Storage.md` §21 for the file-specific table)
- **Related Documents:** `20_File_Storage.md` §21; `23_Security_Standards.md` §10.4, §13; `10_API_Design.md` §6.

### MSG-09 — Message Language (CONFIRMED)
- **Validation ID:** MSG-09
- **Field Name:** All message text
- **Validation Rule:** Validation messages are translatable. Arabic is the default, English is fully supported, and presentation automatically uses RTL for Arabic and LTR for English. Timezone and currency decisions remain PENDING.
- **Required / Optional:** Mandatory.
- **Allowed Values:** Arabic and English message catalogs; future languages remain supported.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (localized variants required)
- **Related Documents:** `41_Internationalization_i18n.md`; `00_Project_Context.md` §9.9.

### MSG-10 — Standard Field Message Set
- **Validation ID:** MSG-10
- **Field Name:** Common field validations (catalog of standard texts)
- **Validation Rule:** Common failures use the following standard texts (already applied throughout this catalog): "The {Field Name} field is required."; "The {Field Name} format is invalid."; "The {Field Name} must not exceed the allowed length."; "The {Field Name} must be a valid date."; "The start date must not be after the end date."; "The {Field Name} must be a valid email address."; "The {Field Name} must be a valid amount." / "must be zero or more."; "The selected {Field Name} is invalid."; "The Pricing Type must be Monthly or Per Lesson."; "The Question Type must be Multiple Choice, True/False, Essay, or Bubble Sheet."; "The Homework format must be Text, Image, or PDF. Video Homework is not supported."; "The selected Attendance method is invalid."; "This file type is not supported."; "This Dynamic QR Code is not valid for today's Attendance."
- **Required / Optional:** Mandatory for the covered cases; new message families follow MSG-03/04/05.
- **Allowed Values:** The standard set (extend only via documentation change).
- **Minimum / Maximum Limits:** —
- **Error Message:** The standard set above.
- **Related Documents:** All domain sections of this document; `10_API_Design.md` §10.

**Cross-referenced rules:** Frontend error-display contracts are owned by `13_UI_UX_Guidelines.md` §9 and `14_UI_Components.md`; the error-code field (`VALIDATION_FAILED`) and HTTP status table are owned by `10_API_Design.md` §6, §10; this section consolidates their validation-message consequences without redefining them.

---

# 23. Validation Exceptions

**Authoritative sources:** `02_Software_Requirements.md` (module validation exceptions); `20_File_Storage.md` §12; `16_QR_Attendance_System.md` §6; `15_Exam_Engine.md`; `23_Security_Standards.md` §6.3, §9.4; `00_Project_Context.md` §15.1; `31_Master_Index.md` §10.5; `21_Background_Jobs.md`.

"Exception" here means a documented place where the general rules bend, pause, or are deliberately silent — never a loophole. Every exception below is itself a rule.

### EXC-01 — Public Endpoint Exception
- **Validation ID:** EXC-01
- **Field Name:** Authentication requirement (login, Student self-registration)
- **Validation Rule:** The login and Student self-registration endpoints are public: the authenticated-context rule (API-03/AUT-14) does not apply *before* the operation for these endpoints, but every other rule applies in full — input validation, rate limiting (AUT-05/API-11), duplicate prevention (AUT-12), and audit recording (AUT-06).
- **Required / Optional:** Applies only to the documented public endpoints.
- **Allowed Values:** Valid unauthenticated requests to public endpoints only.
- **Minimum / Maximum Limits:** —
- **Error Message:** Per AUT-xx rules.
- **Related Documents:** `10_API_Design.md` §13; `23_Security_Standards.md` §3.

### EXC-02 — Activation Flow Authentication Exception
- **Validation ID:** EXC-02
- **Field Name:** Authentication requirement (Student activation)
- **Validation Rule:** Account-setting access requires authentication **except for activation flows**: a Student activating a Teacher-created account is validated through the activation data match (AUT-13), not through prior authenticated access to that account.
- **Required / Optional:** Applies only to the activation flow.
- **Allowed Values:** Valid activation payloads matching a pending-activation account.
- **Minimum / Maximum Limits:** —
- **Error Message:** Per AUT-13.
- **Related Documents:** `02_Software_Requirements.md` Part 3 (Settings validations); `10_API_Design.md` §13.

### EXC-03 — Text Homework Non-Binary Exception
- **Validation ID:** EXC-03
- **Field Name:** Text-format Homework content
- **Validation Rule:** Text Homework may be **logical content rather than a binary file**; the binary-file rules (FIL-05…FIL-08 transport checks) do not apply to logical text content, while all content, scoping, and format-value rules (HW-01…HW-04) apply unchanged.
- **Required / Optional:** Applies to Text-format Homework.
- **Allowed Values:** Valid bounded text content.
- **Minimum / Maximum Limits:** GEN-04.
- **Error Message:** Per HW-xx.
- **Related Documents:** `20_File_Storage.md` §3; `07_Data_Dictionary.md` §16, §28.

### EXC-04 — Absent Confirmed Limit Is Not Silence To Exploit
- **Validation ID:** EXC-04
- **Field Name:** File size; string lengths; rate-limit thresholds; pagination bounds
- **Validation Rule:** Where no numeric limit is confirmed, the rule is *bounded-but-deferred* (§3.4): early safety validation still applies (large files validated early), but no product-facing limit may be invented, displayed, or tested. A future approved limit slot into FIL-08/GEN-04 without changing this document's confirmed rules.
- **Required / Optional:** Always.
- **Allowed Values:** Deferred values only via documentation change.
- **Minimum / Maximum Limits:** —
- **Error Message:** Per §3.4 rules (no fabricated numbers).
- **Related Documents:** `20_File_Storage.md` §12; `23_Security_Standards.md` §9.4; `31_Master_Index.md` §2.4.

### EXC-05 — PENDING Non-Hardening Exceptions
- **Validation ID:** EXC-05
- **Field Name:** All fields touched by PENDING decisions
- **Validation Rule:** Validation must not encode: non-payment enforcement behavior (Q-005 — no grace/suspension input semantics, yet data-mutation prohibition pending resolution); Lesson video hosting/protection mechanics (Q-010 — no format catalog, signed-URL validation, or quota validation); Teacher Staff permission granularity (Q-011 — no finer-than-catalog permission validation); Super Admin content visibility expansions (Q-012 — no visibility-widening inputs); tiered pricing structures (Q-013 — no tier fields); localization (Q-015 — no locale, timezone, or currency validation). PROPOSED mechanics (D-003 snapshots, attendance deduplication policy, QR expiry window, exam availability fields) likewise must not be validated as confirmed.
- **Required / Optional:** Prohibited until resolution.
- **Allowed Values:** Only the confirmed core rules for each area.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (absence of validators, not a failure mode)
- **Related Documents:** `00_Project_Context.md` §15.1; `31_Master_Index.md` §10.5; `28_Coding_Standards.md` §17.3; `32_Business_Rules.md` §2, §27.

### EXC-06 — Background Job Input Exception
- **Validation ID:** EXC-06
- **Field Name:** Inputs to queued/scheduled work (Billing Cycle initialization, Billable calculation, Subscription snapshot, QR cleanup, Exam auto-grading, report preparation, file reference integrity)
- **Validation Rule:** Internal jobs have no HTTP request layer and no Form Request validation; they still enforce business and integrity validation (§16, §21) and idempotency — a job must not persist anything that would violate INT-xx.
- **Required / Optional:** Applies to all background work.
- **Allowed Values:** Integrity-conformant persisted results only.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (operational logging, not user messages)
- **Related Documents:** `21_Background_Jobs.md`; `11_Backend_Architecture.md`; `24_Testing_Strategy.md` §3.1.

### EXC-07 — Read-Only Request Simplification
- **Validation ID:** EXC-07
- **Field Name:** GET request bodies
- **Validation Rule:** Read-only list/detail requests validate authentication, authorization/scope, and query parameters (§19), but do not run body validation (no body is defined for them in the contract); detail GETs rely on API-04 identifier rules rather than existence-disclosing checks.
- **Required / Optional:** Applies to GET endpoints.
- **Allowed Values:** Valid query strings; no request body.
- **Minimum / Maximum Limits:** —
- **Error Message:** Per §19 and API-04.
- **Related Documents:** `10_API_Design.md` §7–§9, §13–§30.

### EXC-08 — Archived Records As Valid Historical Targets
- **Validation ID:** EXC-08
- **Field Name:** Archived-state check (GEN-11) in historical contexts
- **Validation Rule:** GEN-11's "archived is not an active target" does not block historical/reporting reads: archived records remain valid subjects of reports and history queries where the report rules include them, clearly indicated, and valid targets of authorized restore operations. The exception covers read/report/restore only — never active use.
- **Required / Optional:** Context-dependent.
- **Allowed Values:** Archived records in historical, report, and restore contexts.
- **Minimum / Maximum Limits:** —
- **Error Message:** — (permitted path)
- **Related Documents:** `00_Project_Context.md` §11; `22_Search_Filtering.md`; `32_Business_Rules.md` §19, §24.

### EXC-09 — Rejection-Only Validation (Out-Of-Scope Categories)
- **Validation ID:** EXC-09
- **Field Name:** Notification payloads; payment-gateway fields; Platform staff creation; "Login as Teacher"; video Homework; Parent uploads; marketplace Lesson discovery
- **Validation Rule:** For these categories, the *entire validation rule is rejection*: there is no valid value, format, or flow in Version 1 (API-08, PAY-06, FIL-09, PAR-05, LSN-06). Documents define no acceptance path and none may be created silently.
- **Required / Optional:** Always rejection.
- **Allowed Values:** None.
- **Minimum / Maximum Limits:** —
- **Error Message:** Per the owning rule section (§17, §18, §20; 404/403 where no surface exists).
- **Related Documents:** `10_API_Design.md` §29, §30; `32_Business_Rules.md` §26.

### EXC-10 — Unconfirmed Capability Non-Validation
- **Validation ID:** EXC-10
- **Field Name:** Capabilities documented as "not confirmed" (password history, virus scanning, barcode scanning, orphan-file cleanup, file download policy, file replacement policy, profile images, offline QR sync, attempt limits)
- **Validation Rule:** A capability documented as not confirmed must not silently acquire validators, UI hints, or tests: their absence of acceptance rules means the input either maps to a confirmed rejection (e.g., barcode Attendance → ATT-02 rejection) or simply does not exist as a surface.
- **Required / Optional:** Prohibited until approved.
- **Allowed Values:** Nothing beyond confirmed rules.
- **Minimum / Maximum Limits:** —
- **Error Message:** Context rejection per ATT-02/API-02 as applicable.
- **Related Documents:** `23_Security_Standards.md` §6.3, §9.4; `20_File_Storage.md` §8, §15–§18; `16_QR_Attendance_System.md` §1, §10; `15_Exam_Engine.md`.

**Cross-referenced rules:** Exception policy mirrors the documentation change process: adding an acceptance path for anything in this section requires the documented modification sequence (`31_Master_Index.md` §8; `32_Business_Rules.md` §28 — BCP-xx).

---

# 24. Validation Checklist

**Authoritative sources:** `23_Security_Standards.md` §21.4 (Input Validation Checklist) and §21 supplementary items; `28_Coding_Standards.md` (code-review checklist items); `24_Testing_Strategy.md` §3, §5.2; `13_UI_UX_Guidelines.md` §9; `31_Master_Index.md` §14.

This checklist is the closing review instrument for any form, endpoint, job, or feature that touches input. Every item must pass before validation work is considered complete. Items marked (BE) are backend-anchored; (FE) frontend-anchored; (BOTH) must hold consistently in both layers.

| # | Checklist item | Scope |
|---|---|---|
| CHK-01 | Every input field of the feature appears in this catalog (or an owning document) with Required/Optional, allowed values, and limits defined — nothing untraceable. | BOTH |
| CHK-02 | All inputs are validated at the request boundary via Form Requests (grouped by feature, named after resource and action). *(Described as layer placement — no code.)* | BE |
| CHK-03 | Required fields are enforced as present and non-empty (GEN-01). | BOTH |
| CHK-04 | Data types match the data dictionary logical types (GEN-02). | BOTH |
| CHK-05 | Enum values are validated against the confirmed sets of §3.3 — Pricing Type, Question Type, Homework Formats, Attendance Method, Payment Flow, Created By Method, statuses (GEN-03). | BOTH |
| CHK-06 | Date ranges are validated: valid dates, start ≤ end, Enrollment End ≥ Start, Billing Cycle = calendar month (GEN-05/06, SUB-01, STU-07). | BOTH |
| CHK-07 | Text is bounded; no fabricated maximum is shown to users (GEN-04, §3.4). | BOTH |
| CHK-08 | Numeric rules hold: IDs positive integers; money valid amounts; Price non-negative; counts non-negative (GEN-08, GRP-03). | BOTH |
| CHK-09 | References exist and are in-scope (GEN-09): workspace for Teacher-side, linked Students for Parent, own records for Student, Platform scope for Super Admin. | BE |
| CHK-10 | Uniqueness enforced exactly at confirmed boundaries: global Student identity, Login Identifier, entity identifiers (GEN-10; no invented uniqueness). | BE |
| CHK-11 | Archive-state checks: archived records rejected as active targets for assignment, upload, composition, attempt; restore path validated separately (GEN-11, §4–§18). | BE |
| CHK-12 | Duplicate Student prevention holds in both registration methods and at activation (AUT-11…13, INT-03). | BE |
| CHK-13 | One active Group per Student per Teacher enforced on assign and move (STU-06, GRP-06, INT-05). | BE |
| CHK-14 | One Parent per Student enforced (PAR-01, INT-04). | BE |
| CHK-15 | Teaching Subject required at creation and immutable afterwards (TCH-01/02, INT-06). | BE |
| CHK-16 | Authentication rules hold: backend-only credential validation, generic failure, rate limiting, audit, password policy at set/change only (AUT-01…10). | BE |
| CHK-17 | Teacher Staff validation respects creating-workspace scoping and confirmed-permission validity without hardening granularity (TCH-06…08, Q-011). | BE |
| CHK-18 | Attendance: method enum, daily QR validity, eligibility, duplicate safety, correction reason (ATT-01…09). | BE |
| CHK-19 | Homework: formats Text/Image/PDF, video rejected, submission eligibility, binary Image/PDF (HW-01…08, FIL-05/09). | BOTH |
| CHK-20 | Exams: same-workspace composition, non-empty valid composition for availability, answer-type matching, attempt ownership, pending-grading honesty (EXM-01…09, QBK-xx, BSH-xx). | BE |
| CHK-21 | Lessons: ownership, active state, relationship eligibility, no discovery/monetization inputs; no invented video mechanics (LSN-01…06, Q-010). | BE |
| CHK-22 | Subscription: calendar-month cycle, Enrollment-duration-only count with >15-day threshold, formula identity, Super-Admin-owned pricing without tier hardening (SUB-01…07, INT-07/08, Q-013). | BE |
| CHK-23 | Payments: flow-correct references, status-only values, gateway fields rejected, Student/Parent writes denied (PAY-01…07, D-002). | BE |
| CHK-24 | File uploads pass the full layered stack: auth, permission, ownership, relationship, Parent denial, context type, MIME content check, filename sanitization, early size guard without invented limit (FIL-01…12). | BE |
| CHK-25 | Search: scope before search, scoped filter references, date ordering, status whitelist, archived exclusion, sort whitelist, pagination bounds, flow separation (SRC-01…12). | BE |
| CHK-26 | API: JSON/multipart transport, /api/v1 surface only, positive-integer IDs with 404 invisibility, 401/403/404/409/422/429 discipline, prohibited payload categories rejected (API-01…12). | BE |
| CHK-27 | Integrity: reference existence, scope-match, flow separation, archive-only lifecycle, history preservation, audit append-only, conditional timestamps, no cross-workspace persistence, valid statuses (INT-01…15). | BE |
| CHK-28 | 422 envelope correct: success false, code VALIDATION_FAILED, summary, field-level errors; 409 used for rule/state conflicts (MSG-01/07, API-06/07). | BE |
| CHK-29 | Messages are correction-oriented, canonical, and non-disclosing; no internals, paths, existence leaks, or fabricated limits (MSG-02…05, MSG-08, MSG-10, GEN-12). | BOTH |
| CHK-30 | Form presentation: error summary, field association, focus management, preserved values (MSG-06). | FE |
| CHK-31 | Frontend Zod schemas mirror only confirmed structural rules; backend validation present and authoritative for everything; no client-side "guarantee" of business rules (D-019, D-021; §2.1–2.2). | BOTH |
| CHK-32 | Server field messages are attached to the active form on 422; non-field messages handled at form level (MSG-02). | FE |
| CHK-33 | No validator encodes PENDING items Q-005, Q-010, Q-011, Q-012, Q-013, Q-015, or PROPOSED mechanics (EXC-05). | BOTH |
| CHK-34 | No validator silently adds unconfirmed capabilities (password history, virus scanning, barcode, attempt limits, profile images, cleanup/download/replacement policies) (EXC-10). | BOTH |
| CHK-35 | Parent paths: uploads denied pre-inspection, writes denied, Switcher limited to linked Students, own-account updates only (PAR-01…05). | BE |
| CHK-36 | Background jobs enforce business/integrity validation and idempotency without HTTP layers (EXC-06, INT-07/08/15). | BE |
| CHK-37 | Validation tests exist for the documented expectations: threshold 15-vs-16 days, calendar-month boundaries, duplicate prevention, one-Group rule, subject immutability, format enums, Pricing Type, question types (per `24_Testing_Strategy.md` §5.2). | BE |
| CHK-38 | Terminology check in validators and messages: never "Class", "Course", "Delete", "sub-teacher"; "tenant" only in architecture context (MSG-05). | BOTH |
| CHK-39 | Version 1 exclusion surfaces stay closed: no notification payload acceptance, no gateway data, no Platform staff creation, no "Login as Teacher", no marketplace discovery validation paths (EXC-09, API-08). | BE |
| CHK-40 | Where this catalog says "deferred", no number, format, or mechanic appears in code, UI text, tests, or API docs (§3.4, EXC-04). | BOTH |

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority; this document is a consolidation reference that never overrides it or any subject-owning document per `31_Master_Index.md` §9.2/§10. |
| All documents read | Passed — every authored document in `AI_DOCS/` (`00`–`32`) was read in full or in complete relevant part before authoring; validation-owning documents (`02`, `06`, `07`, `10`, `12`–`14`, `15`, `16`, `20`, `22`, `23`, `28`) were read in full for their validation content. |
| Consolidation without invention | Passed — every rule traces to an existing document: attribute rules to `07_Data_Dictionary.md`, module rules to `02_Software_Requirements.md` Validations sections, request rules to `10_API_Design.md`, integrity rules to `06_Database_Design.md` §12–§13, standards to `28_Coding_Standards.md` §17 and `23_Security_Standards.md` §9–§10, file rules to `20_File_Storage.md`, search rules to `22_Search_Filtering.md`. |
| No invented limits | Passed — deferred limits are explicitly recorded as deferred (§3.4): string maxima, file size, rate thresholds, pagination bounds, formats/codecs, schedules, availability fields, attempt limits, currency/localization; nothing is fabricated. |
| Required per-rule fields | Passed — every rule entry carries Validation ID, Field Name, Validation Rule, Required / Optional, Allowed Values, Minimum / Maximum Limits ("—" where none is confirmed), Error Message, and Related Documents. |
| Required sections | Passed — all 24 requested sections are present in the requested order: Document Purpose; Validation Philosophy; General Validation Standards; Authentication; Teacher; Student; Parent; Educational Grade; Group; Attendance; Homework; Exam; Question Bank; Bubble Sheet; Lesson; Subscription; Payment; File Upload; Search; API Request; Data Integrity; Validation Error Messages; Validation Exceptions; Validation Checklist. |
| Laravel 12 best practices | Passed — §3.1 maps standards to Laravel 12 validator capabilities (Form Requests per feature, built-in rules, scoped exists/unique, conditional requirements, prohibited-input rejection) at specification level without source code, Form Request classes, or rule arrays. |
| Frontend/backend consistency | Passed — D-019/D-021 posture restated throughout: Zod mirrors confirmed structural rules; backend Form Requests/services are authoritative; 422 mapping documented; no layer hardens PENDING items. |
| Canonical rule fidelity | Passed — enum sets match exactly (Pricing Type: Monthly/Per Lesson; Question Type: Multiple Choice, True/False, Essay, Bubble Sheet; Homework: Text/Image/PDF; Attendance: Dynamic QR Code/ID Card/Manual; Flow A/Flow B); Billable threshold strictly > 15 calendar days; Billing Cycle = calendar month; Teaching Subject immutable; one Group per Teacher; one Parent per Student; password policy 8+ with upper/lower/digit; statuses per data dictionary. |
| PENDING/PROPOSED discipline | Passed — Q-005, Q-010, Q-011, Q-012, Q-013, Q-015 are protected from validation hardening (EXC-05, MSG-09, SUB-03/07, LSN-04, TCH-07); PROPOSED mechanics (D-003 snapshots, dedupe policy, QR expiry window) are not validated as confirmed. |
| Terminology | Passed — canonical terms only; "Class", "Course", "Delete", "sub-teacher" appear only as prohibited examples; "tenant" appears only in architecture/D-020 context. |
| Version 1 scope | Passed — notification payloads, payment-gateway fields, Platform staff creation, "Login as Teacher", marketplace discovery, video Homework, barcode Attendance, profile images are rejection-only or nonexistent surfaces (EXC-09/EXC-10, API-08). |
| Scope exclusions | Passed — no source code, no Form Requests, no APIs, no database tables/SQL, no UI implementation, no physical configuration; forbidden categories in the Document Scope hold throughout. |
| Relationship to 32 | Passed — business rules remain consolidated in `32_Business_Rules.md`; this document cross-references its sections (§3–§26) instead of restating rule authority, and adds no new business rules. |
| Governance registration | Passed — this document carries a Document Scope with scope exclusions and this closing consistency review per `31_Master_Index.md` §13.5; corresponding registrations were applied in `31_Master_Index.md` §15 and `04_Project_Structure.md` §8. |

---

*End of document. **REVISION 1.0** — This file is the consolidated authoritative reference for all validation rules of the Unified Education Platform Version 1. The backend is the only authority; the frontend guides but never guarantees; confirmed rules are enforced exactly, and unconfirmed limits are never invented. `00_Project_Context.md` remains the Single Source of Truth.*


## Confirmed Audit Validation Clarifications

- A Parent–Student link request requires an existing Parent account, a Student with an active Enrollment, and no existing linked Parent. Approval and unlink approval require the responsible Teacher; a duplicate link, an already-linked Student, or approval by another actor is rejected.
- A Per Lesson obligation requires Pricing Type `Per Lesson`, a completed Lesson, and active Student Enrollment in that Lesson’s Group. Reject a duplicate obligation for the same Student and completed Lesson. Do not create an obligation for a draft, scheduled, published, viewed, attended, or assigned Lesson.
- For Flow A, validation of Billable Student duration aggregates all Enrollment periods in Groups belonging to the same Teacher within the Billing Cycle; it must not evaluate a same-Teacher transfer as separate duration tests.

**Acceptance criteria:** a valid Parent link becomes read-only only after approval; an unlink takes effect only after approval; a completed Per Lesson Group Lesson creates exactly one obligation for each actively enrolled Student; no listed non-completion state creates one; and a same-Teacher transfer cannot avoid accumulated Flow A duration.
