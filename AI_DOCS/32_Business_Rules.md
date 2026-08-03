# 32 — Business Rules

## Document Scope

This document is the single authoritative **reference** for all business rules of the Unified Education Platform. It consolidates, in one uniform catalog, every business rule already defined and approved across the canonical document set (`AI_DOCS/00`–`31`).

This document **introduces no new business rules**. Every rule recorded here is consolidated from an already-approved source document, and each rule cites its authoritative definition. Where a rule appears in more than one document, the duplication is resolved by referencing the authoritative definition rather than restating it as a competing source.

Authority statement:

- `AI_DOCS/00_Project_Context.md` is the official **Single Source of Truth** for Version 1 and prevails over every other document, including this one, if any conflict is ever found.
- The canonical business rules **BR-001 … BR-022**, the Audit Log Policy, the Archive Policy, scope, statuses, and canonical terminology are owned by `00_Project_Context.md` (§9, §10, §11, §15, §19).
- Subject-level authority below the Project Context follows the ownership table in `31_Master_Index.md` §9.2 (for example: RBAC → `08_RBAC.md`; Flow A billing → `17_Subscription_Billing.md`; Attendance → `16_QR_Attendance_System.md`; canonical terminology → `30_Project_Glossary.md`).

Consolidated catalog identifiers (for example, AUTH-01, PAY-02) assigned in this document are **reference identifiers only**. They exist so that each consolidated rule can be cited from reviews, tests, and conversations. They do not create, replace, or renumber the canonical BR-xxx rules, and they must never be cited as if they were new canonical business rules.

This document uses only canonical terminology (`00_Project_Context.md` §19, `30_Project_Glossary.md`). It does not define source code, APIs, database tables, UI implementation, or physical configuration. Every rule carries the confirmation status of its authoritative source: **CONFIRMED**, **PROPOSED**, or **PENDING**; no status is ever silently upgraded here.

---

# 1. Document Purpose

1. Provide a single authoritative reference in which every confirmed business rule of the Platform can be found, in one uniform format, without searching across more than thirty documents.
2. Consolidate — without modifying — the business rules already approved in the Project Context, the Software Requirements, the User Flows, the RBAC and Permission Matrix, the Data Dictionary, the feature subsystem documents (Exam Engine, QR Attendance, Subscription & Billing, Reporting & Analytics, Notification System, File Storage, Background Jobs, Search & Filtering, Security Standards), the Project Decisions register, and the Master Index governance policies.
3. Resolve duplicated rules by referencing the authoritative definition, so that a reader always knows which document owns each rule.
4. Preserve the exact terminology, confirmation statuses, PENDING open questions, and Version 1 scope exclusions of the source documents.
5. Record the conflict-resolution and rule-change governance that applies whenever a business rule is disputed or must change.

What this document is not:

- It is not a new source of truth above `00_Project_Context.md`. It is a consolidated reference layer over the existing authorities.
- It is not a requirements document (owned by `02_Software_Requirements.md`), a permissions document (owned by `08_RBAC.md` / `09_Permission_Matrix.md`), or a glossary (owned by `30_Project_Glossary.md`).
- It does not define validation field rules, error codes, source code, APIs, database tables, or UI implementation; those subjects remain reserved to their own documents or excluded by scope.

---

# 2. Business Rules Philosophy

The philosophy below is inherited from `00_Project_Context.md` §17 (Collaboration Protocol), `31_Master_Index.md` §2 (Documentation Philosophy), and `30_Project_Glossary.md` ("Business Rule"). It governs how the rules in this catalog must be read and used.

**2.1 One Single Source of Truth.** `00_Project_Context.md` is frozen (Revision 2.0 FINAL) and wins every conflict. This reference never overrides it; it routes to it. (31 §2.2, §9.1.)

**2.2 Scope anchors.** Version 1 is a **Web Application only** (BR-017, D-049); payments are handled outside the Platform with status-only recording (BR-019, D-002); notifications are out of scope (D-012); the Platform is **not** a marketplace and Teachers do **not** sell courses (D-050); each Teacher account has exactly one Teaching Subject (BR-016, D-051); Homework is Text, Image, and PDF only (BR-021, D-011). These anchors are repeated in their domain sections below and are binding on every interpretation of every other rule.

**2.3 No invented rules.** A rule exists only because an approved document defines it (`00_Project_Context.md` §17.7; `31_Master_Index.md` §11.2). If a behavior is not documented anywhere, it is not a rule. Catalog identifiers in this document reference existing rules; they never create new ones.

**2.4 Status discipline.** Every statement carries exactly one status — **CONFIRMED** (binding), **PROPOSED** (Architect's working default), or **PENDING** (unresolved; nothing may harden against it). The six open questions remain PENDING and are flagged inside the affected rules below: **Q-005** non-payment enforcement, **Q-010** Lesson video hosting/protection, **Q-011** Teacher Staff permission granularity, **Q-012** Super Admin content visibility, **Q-013** flat price vs. volume tiers, **Q-015** timezone/currency. (`00_Project_Context.md` Statement Status Convention, §15.1.)

**2.5 Traceability.** Canonical rules are cited by identifier — **BR-xxx** (business rules), **D-xxx** (decisions), **Q-xxx** (open questions) — rather than by paraphrase (`31_Master_Index.md` §2.6). Where a full rule entry in this catalog is itself the canonical BR-xxx rule, its Rule ID is the BR-xxx identifier.

**2.6 Single ownership, no duplication.** Each subject has exactly one authoritative owner (`31_Master_Index.md` §2.3, §9.2). Duplication that could drift is a defect; consumers cite the owner instead of restating. Accordingly, each domain section below begins with its **authoritative sources**, fully defines only the rules that primarily belong to that domain, and **cross-references** rules owned elsewhere instead of redefining them.

**2.7 History is never rewritten.** Rules, decisions, and open questions mirror the product's Archive philosophy: resolved questions are archived not deleted, change history is append-only, superseded content is marked not erased (`31_Master_Index.md` §2.7; BR-005, BR-014).

**2.8 Rule anatomy.** Each consolidated rule in this catalog is recorded with the same eight fields:

| Field | Meaning |
|---|---|
| **Rule ID** | Canonical `BR-xxx` for the 22 canonical rules; otherwise a domain-prefixed catalog identifier (reference only, per Document Scope). |
| **Rule Name** | A short canonical title for the rule. |
| **Description** | What the rule states, using the source documents' own meaning and terminology. |
| **Applies To** | The roles, modules, flows, or records the rule governs. |
| **Trigger** | The event or condition under which the rule is evaluated. |
| **Expected Behavior** | What the Platform must do (or must never do) when the rule applies. |
| **Exceptions** | Documented exceptions, unconfirmed areas (PENDING/PROPOSED), or "None confirmed." |
| **Related Documents** | The authoritative definition and the strongest supporting sources. |

---

# 3. Authentication Rules

**Authoritative sources:** `00_Project_Context.md` §9 (BR-006, BR-017, BR-022), `08_RBAC.md` §13, `23_Security_Standards.md` §3, `03_System_Architecture.md` §9, `02_Software_Requirements.md` Part 6 §6, `05_User_Flows.md` flows 2, 8, 19, 25; decision D-037 (`29_Project_Decisions.md`).

### AUTH-01 — Authenticated Context Required for Protected Access

- **Rule ID:** AUTH-01
- **Rule Name:** Authenticated Context Required
- **Description:** All protected user actions require an authenticated user context. Authentication is established before any role, scope, or permission resolution occurs, and is carried on every request to protected resources.
- **Applies To:** All five roles (Super Admin, Teacher, Teacher Staff, Student, Parent); every protected surface.
- **Trigger:** Any request to a protected resource or action.
- **Expected Behavior:** The backend validates the authenticated context first; unauthenticated access is denied.
- **Exceptions:** Public authentication endpoints (login, registration surfaces) per `23_Security_Standards.md` §8.2.
- **Related Documents:** `23_Security_Standards.md` §3.2; `08_RBAC.md` §13; `02_Software_Requirements.md` Part 6 §6 (NFR-AUTHN-001); `03_System_Architecture.md` §9.

### AUTH-02 — Laravel Sanctum Is the Confirmed Authentication Mechanism

- **Rule ID:** AUTH-02
- **Rule Name:** Sanctum Authentication
- **Description:** Authentication for Version 1 is implemented with Laravel Sanctum, as confirmed in the technology stack and decision D-037; credential validation itself is performed server-side by the Laravel backend.
- **Applies To:** All authenticated access for all roles.
- **Trigger:** Any authentication attempt or authenticated request.
- **Expected Behavior:** The React frontend submits credentials; the Laravel backend validates them through Laravel Sanctum and establishes the authenticated context. The frontend never makes authentication decisions.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §13; `29_Project_Decisions.md` D-037, D-001; `23_Security_Standards.md` §3.1–§3.3; `05_User_Flows.md` flows 2, 8, 19, 25.

### BR-017 — Web Application Only

- **Rule ID:** BR-017 (canonical, CONFIRMED)
- **Rule Name:** Version 1 Is a Web Application Only
- **Description:** Version 1 is delivered as a Web Application only. No native mobile application exists in Version 1; every V1 interaction — including daily Dynamic QR Code Attendance scanning — runs in the web application. A Student may scan through a mobile-capable browser where browser capability permits; this does not create a native application.
- **Applies To:** All roles, all modules, all delivery surfaces; authentication and Attendance scanning explicitly.
- **Trigger:** Any design, authentication, or feature decision that assumes a non-web client.
- **Expected Behavior:** Every capability is delivered through the browser-based Web Application; native-mobile requirements are rejected as out of scope.
- **Exceptions:** Native mobile applications may be considered only as a separately approved future phase (`27_Development_Roadmap.md` §20.2).
- **Related Documents:** `00_Project_Context.md` §4.2, §9.2; `29_Project_Decisions.md` D-049; `16_QR_Attendance_System.md` Document Scope; `03_System_Architecture.md` §2.

### AUTH-03 — Login Events Are Audited (Success and Failure)

- **Rule ID:** AUTH-03
- **Rule Name:** Login Audit Recording
- **Description:** Every successful and every failed authentication is recorded in the Audit Log. This is one of the ten mandatory Audit Log events (Audit Log Policy, event 5).
- **Applies To:** All roles; all login attempts through the Web Application.
- **Trigger:** A login attempt, whether it succeeds or fails.
- **Expected Behavior:** An Audit Log entry is written for the login event; failed logins are recorded without exposing whether the account exists.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §10.1 (event 5), BR-006; `23_Security_Standards.md` §3.3; `05_User_Flows.md` flows 2, 8, 19, 25. See also §25 of this document.

### AUTH-04 — Generic Authentication Failures

- **Rule ID:** AUTH-04
- **Rule Name:** No Account Existence Disclosure
- **Description:** Authentication errors are generic. They must not reveal whether an account exists, whether the password was wrong, or whether the account is archived.
- **Applies To:** All authentication and password-reset interactions.
- **Trigger:** Any failed login or reset attempt.
- **Expected Behavior:** The user receives a safe generic failure (for example, "Invalid credentials"); no protected data or account metadata is revealed; the failure is auditable per AUTH-03.
- **Exceptions:** None confirmed.
- **Related Documents:** `23_Security_Standards.md` §3.3, §18.2–§18.3; `05_User_Flows.md` flow 2 (Error Flows).

### AUTH-05 — No Impersonation ("Login as Teacher" Not Confirmed)

- **Rule ID:** AUTH-05
- **Rule Name:** Impersonation Excluded from Version 1
- **Description:** Unconfirmed impersonation behavior must not be introduced. "Login as Teacher" is not confirmed in the Project Context and is not a Version 1 capability for any role, including the Super Admin.
- **Applies To:** Super Admin; Platform administration; authentication architecture.
- **Trigger:** Any request to enter a Teacher Workspace as a Teacher by another actor.
- **Expected Behavior:** The request is denied; authentication grants only the authenticated account's own confirmed role context.
- **Exceptions:** None confirmed; any future impersonation capability requires a separate Product Owner decision.
- **Related Documents:** `02_Software_Requirements.md` Part 5 §7 (Audit Logs — Business Rules); `09_Permission_Matrix.md` §17 (`platform.teacher.login_as_teacher` — Denied); `08_RBAC.md` §13 (Authentication Constraints); `05_User_Flows.md` flow 25.

### AUTH-06 — Authentication Must Not Violate Identity Rules

- **Rule ID:** AUTH-06
- **Rule Name:** Identity Integrity in Authentication
- **Description:** The authentication architecture must not introduce duplicate Student accounts, multiple Parent accounts for one Student in Version 1, native mobile authentication requirements, or unconfirmed impersonation behavior.
- **Applies To:** Authentication architecture; account creation and activation flows.
- **Trigger:** Any design or change touching authentication, registration, or account activation.
- **Expected Behavior:** Authentication enforces one global Student account (BR-001, BR-022), one Parent account per Student (BR-020), and the Web-Application-only scope (BR-017).
- **Exceptions:** None confirmed.
- **Related Documents:** `08_RBAC.md` §13; `03_System_Architecture.md` §9.4; `00_Project_Context.md` §9 (BR-001, BR-020, BR-022). See also §4 and §6 of this document.

### AUTH-07 — Authentication Establishes Context, Not Authorization

- **Rule ID:** AUTH-07
- **Rule Name:** Authentication Is Not Authorization
- **Description:** Successful authentication establishes the user's identity and available role contexts only. It does not grant cross-Teacher access, Parent modification rights, Student access to Teacher Workspace management, or Super Admin access to Teacher-private content; authorization, scope, and ownership are re-evaluated for every protected action by the backend.
- **Applies To:** All roles; every protected action.
- **Trigger:** Every request after authentication succeeds.
- **Expected Behavior:** The backend resolves role, scope, ownership/relationship, permission, and Archive state before executing the action (authorization flow per `08_RBAC.md` §14).
- **Exceptions:** None confirmed.
- **Related Documents:** `05_User_Flows.md` flow 2 (Business Rules); `08_RBAC.md` §14; `23_Security_Standards.md` §4.2.

---

# 4. User Registration Rules

**Authoritative sources:** `00_Project_Context.md` §6.4, §8, §9.1 (BR-016, BR-020, BR-022), `05_User_Flows.md` flows 1, 6, 7, `02_Software_Requirements.md` Part 1 §10, Part 3 §7, `07_Data_Dictionary.md` §6, `08_RBAC.md` §13; decision D-013 (`29_Project_Decisions.md`).

### BR-022 — Two Student Registration Methods; No Duplicates

- **Rule ID:** BR-022 (canonical, CONFIRMED)
- **Rule Name:** Student Registration Methods and Duplicate Prevention
- **Description:** Student Registration supports exactly two confirmed methods: (1) the Student registers their own account (self-registration), and (2) the Teacher creates the Student account manually. Both methods produce exactly **one** global Student account; **duplicate Student accounts are NOT allowed**. If the Teacher creates the account, the Student can later activate and use the same account.
- **Applies To:** Student registration; Teacher-side Student creation ("Add Student"); account activation.
- **Trigger:** Any Student account creation attempt, by either method.
- **Expected Behavior:** The Platform validates the required information and checks for an existing global Student account. If none exists, one account is created; if a matching account exists, a duplicate creation attempt is rejected and the existing account is used only where the authorized assignment is valid — without exposing another Teacher's private data.
- **Exceptions:** None confirmed. The mechanism that matches existing accounts must still never create duplicates and never leak cross-Teacher data.
- **Related Documents:** `00_Project_Context.md` §9.1 (BR-022), §6.4; `05_User_Flows.md` flows 6–7; `29_Project_Decisions.md` D-013; `02_Software_Requirements.md` Part 2 §4, Part 3 §7; `07_Data_Dictionary.md` §6.

### REG-01 — Teacher-Created Account Activation

- **Rule ID:** REG-01
- **Rule Name:** Activation of Teacher-Created Student Accounts
- **Description:** If a Teacher creates the Student account, the Student can later activate and use that same account. Activation uses the existing account; it never registers a second account.
- **Applies To:** Students whose accounts were created by a Teacher; authentication and activation flows.
- **Trigger:** A Student begins using a Teacher-created account.
- **Expected Behavior:** The activation path verifies that the activation identifier matches a Teacher-created Student account, then enables the Student to authenticate with the same single global account.
- **Exceptions:** No activation-by-notification exists (notifications are out of scope, §20). Activation is not a Billable Student condition (§16, SUB-03).
- **Related Documents:** `00_Project_Context.md` §6.4, BR-022; `05_User_Flows.md` flow 7 (Alternative Flows); `08_RBAC.md` §13; `23_Security_Standards.md` §3.4; `17_Subscription_Billing.md` §7.

### REG-02 — Teacher Registration Selects Exactly One Teaching Subject

- **Rule ID:** REG-02
- **Rule Name:** Single Teaching Subject at Teacher Registration
- **Description:** During Teacher registration, exactly one Teaching Subject is selected and associated with the Teacher account and its Teacher Workspace. An attempt to select multiple Teaching Subjects for one account is rejected, and an attempt to change the Teaching Subject after account creation is rejected.
- **Applies To:** Teacher registration and Teacher account creation.
- **Trigger:** Teacher account creation; any later attempt to modify the Teaching Subject.
- **Expected Behavior:** The Platform creates the Teacher account with one Teacher Workspace and exactly one immutable Teaching Subject. A Teacher who needs another subject creates a separate Teacher account.
- **Exceptions:** The exact self-service versus Platform-managed Teacher account-creation mechanism is **not confirmed** and must not be assumed (see REG-03).
- **Related Documents:** `00_Project_Context.md` §8, BR-016 (authoritative definition in §5 of this document); `05_User_Flows.md` flow 1; `02_Software_Requirements.md` Part 5 §2.

### REG-03 — Teacher Account Creation Mechanism Not Confirmed

- **Rule ID:** REG-03
- **Rule Name:** Unconfirmed Teacher Account-Creation Channel
- **Description:** The exact self-service versus Platform-managed Teacher account-creation mechanism is not confirmed. The single-Teaching-Subject rule and platform-scope Teacher administration are confirmed; the registration channel itself must not be assumed.
- **Applies To:** Teacher onboarding design and documentation.
- **Trigger:** Any work that depends on how a Teacher account comes into existence.
- **Expected Behavior:** No flow, document, or code hardens a specific Teacher self-registration or Platform-managed creation channel until confirmed; both confirmed constraints (one Teaching Subject, Super Admin Platform-level Teacher management) apply regardless of channel.
- **Exceptions:** None beyond the stated unconfirmed channel.
- **Related Documents:** `05_User_Flows.md` flow 1 (Preconditions); `02_Software_Requirements.md` Part 5 §2; `00_Project_Context.md` §6.1.

### REG-04 — Registration Implies No Workspace Membership

- **Rule ID:** REG-04
- **Rule Name:** No Automatic Teacher Relationship from Registration
- **Description:** Student self-registration creates only the global Student account. It does not imply Teacher Workspace membership, Group assignment, or Enrollment; those are created only through an authorized Teacher-side workflow (Add Student / Join Group).
- **Applies To:** Student self-registration.
- **Trigger:** Completion of Student self-registration.
- **Expected Behavior:** The Student can authenticate and access only the Student's own context; a Student with no active Teacher relationship receives an appropriate empty state without access to any Teacher's or Student's records.
- **Exceptions:** None confirmed.
- **Related Documents:** `05_User_Flows.md` flow 7 (Postconditions), flow 8; `09_Permission_Matrix.md` §3–§4.

**Cross-referenced rules (authoritative definitions elsewhere in this catalog):** BR-001 and BR-002 (§6), BR-020 (§7 — registration and linking of Parent accounts must respect one Parent account per Student), BR-017 (§3 — registration is web-only), AUTH-06 (§3 — no identity violations during registration/authentication).

---

# 5. Teacher Rules

**Authoritative sources:** `00_Project_Context.md` §6.2, §7.1, §8, §9.1–§9.8 (BR-003, BR-013, BR-016), `02_Software_Requirements.md` Part 2, `08_RBAC.md` §3, §6, `09_Permission_Matrix.md`, `17_Subscription_Billing.md` §15–§16; decisions D-008, D-050, D-051 (`29_Project_Decisions.md`).

### BR-003 — Teacher Workspace Isolation

- **Rule ID:** BR-003 (canonical, CONFIRMED)
- **Rule Name:** Complete Teacher Data Isolation (Multi-Tenant)
- **Description:** Teacher data is completely isolated using a Multi-Tenant architecture. Each Teacher operates one completely isolated Teacher Workspace and cannot access another Teacher's data **under any circumstance** — no exceptions. Isolation is enforced at the data layer (every Teacher Workspace row carries the Teacher's identity; every query is workspace-scoped; no cross-tenant foreign keys) and at the authorization layer.
- **Applies To:** All Teacher-owned records and operations: Educational Grades, Groups, Student relationships, Enrollments, Attendance, Homework, Exams, Question Bank, Lessons, Reports, Teacher Staff, Teacher Workspace Settings, Flow B payment-status records, and Teacher-owned files.
- **Trigger:** Every access, query, report, search, file operation, or background job that touches Teacher Workspace data.
- **Expected Behavior:** Access is evaluated only within the correct Teacher Workspace context. Cross-Teacher requests are denied without exposing the other workspace's existence or data — including in search results, counts, error messages, and reports.
- **Exceptions:** None. Super Admin Platform-level visibility into Teacher-private content is PENDING (Q-012) and is not an exception to this rule (see §8, ADM-03).
- **Related Documents:** `00_Project_Context.md` §9.1 (BR-003), §12.1; `03_System_Architecture.md` §11; `06_Database_Design.md` §6; `08_RBAC.md` §10; `23_Security_Standards.md` §5; decision D-020.

### BR-016 — One Teaching Subject Per Teacher Account

- **Rule ID:** BR-016 (canonical, CONFIRMED)
- **Rule Name:** Single Immutable Teaching Subject
- **Description:** Each Teacher account represents exactly **one** Teaching Subject (for example: Mathematics, Physics, Chemistry, Biology, Arabic, English). The subject is selected only once, during Teacher registration, and **CANNOT be changed** after account creation. Version 1 does not support multiple Teaching Subjects under one account; a separate Teacher account is required for each additional subject. Teaching Subjects are **independent from Educational Grades**.
- **Applies To:** Teacher registration, Teacher Workspace, Teacher Settings, Platform Teacher administration.
- **Trigger:** Teacher account creation; any attempt to modify the Teaching Subject or to add a second subject.
- **Expected Behavior:** The system rejects subject changes after creation, rejects multiple-subject selection, and treats the selected subject as belonging to the Teacher Workspace. Subject management never appears in Teacher Settings.
- **Exceptions:** None confirmed. (Earlier drafts allowing change via Settings were superseded — the frozen Project Context removed them.)
- **Related Documents:** `00_Project_Context.md` §8, §9.2 (BR-016); `29_Project_Decisions.md` D-008, D-051; `02_Software_Requirements.md` Part 2 §9; `07_Data_Dictionary.md` §31; `09_Permission_Matrix.md` §13 (`…update_teaching_subject` — Denied).

### BR-013 — Teacher Staff Scoped to the Creating Teacher Workspace

- **Rule ID:** BR-013 (canonical, CONFIRMED)
- **Rule Name:** Teacher Staff Existence and Permission Boundary
- **Description:** Teacher Staff (for example: Secretary, Assistant, Accountant) are created by the Teacher, exist **only** inside that Teacher Workspace, and hold **only** the permissions assigned by the Teacher. Teacher Staff can never access another Teacher Workspace. Teacher Staff actions are attributed to the Teacher Staff user, never to the Teacher (see §25, AUD-05).
- **Applies To:** Teacher Staff accounts and all their actions; the Teacher's Users module.
- **Trigger:** Teacher Staff creation; any Teacher Staff action; any permission assignment change.
- **Expected Behavior:** The backend permits a Teacher Staff action only when the specific Teacher-assigned permission exists for the current Teacher Workspace; denied otherwise. Permission changes are recorded in the Audit Log. Teacher Staff cannot grant themselves permissions.
- **Exceptions:** Permission-model **granularity** is PENDING (Q-011; proposed default: fixed capability-flag catalog per module with saveable named presets). No preset or flag catalog is hardened until resolved.
- **Related Documents:** `00_Project_Context.md` §6.3, §9.6 (BR-013), §10.2; `08_RBAC.md` §3, §10; `09_Permission_Matrix.md` §12 (`…self_assign_permission` — Denied); `02_Software_Requirements.md` Part 2 §8; Q-011.

### TCH-01 — Teacher Workspace Module Inventory

- **Rule ID:** TCH-01
- **Rule Name:** Confirmed Teacher Workspace Modules
- **Description:** Within the Teacher Workspace, the Teacher manages exactly the confirmed modules: Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Users (Teacher Staff), Settings — including the explicit Homework module. Teacher Settings cover Teacher profile, center information, phone numbers, and address, and must not include Super Admin platform settings.
- **Applies To:** Teacher Panel; Teacher Workspace navigation and capabilities.
- **Trigger:** Any module, navigation, or capability definition for the Teacher Panel.
- **Expected Behavior:** The Teacher Panel contains the confirmed modules and no others; capability additions enter only through approved decisions.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §6.2, §7.1; `02_Software_Requirements.md` Part 2 (all sections), Part 2 §9 (Settings — Business Rules); `01_Project_Vision.md` §8.

### TCH-02 — Teacher Is the Flow A Paying Customer

- **Rule ID:** TCH-02
- **Rule Name:** Teacher Pays the Monthly Platform Subscription
- **Description:** The Platform's commercial model is a monthly Subscription paid by Teachers to the Platform (Flow A). The Teacher is the paying customer; account creation does not introduce payment processing.
- **Applies To:** Business model; Teacher accounts; Flow A.
- **Trigger:** Teacher account lifecycle; Billing Cycle processing.
- **Expected Behavior:** The Platform calculates and records the Teacher's Flow A Subscription basis/status per Billing Cycle (see §16–§17); actual payment occurs outside the Platform (BR-019, §18).
- **Exceptions:** Non-payment enforcement is PENDING (Q-005) — see §16, SUB-06.
- **Related Documents:** `00_Project_Context.md` §5.1, §6.2; `17_Subscription_Billing.md` §3; `05_User_Flows.md` flow 1.

### TCH-03 — Teacher's Own Flow A Visibility Only

- **Rule ID:** TCH-03
- **Rule Name:** Teacher Subscription Visibility Boundary
- **Description:** A Teacher may view the Teacher's **own** Flow A Subscription information where it is exposed to the Teacher. The Teacher cannot view or manage another Teacher's Subscription, cannot manage Platform-level pricing, and cannot update their own Flow A status.
- **Applies To:** Teacher Dashboard and any Teacher-facing Subscription surface.
- **Trigger:** Teacher access to Subscription information.
- **Expected Behavior:** Only the Teacher's own Flow A status/summary is presented, clearly identified as the Flow A Platform Subscription and kept visually and semantically separate from Flow B Student fee payment status; no price configuration, payment processing, or Platform-level management actions are offered to the Teacher.
- **Exceptions:** Exact dashboard indicators and status labels are not confirmed (must not be invented).
- **Related Documents:** `17_Subscription_Billing.md` §3, §15; `09_Permission_Matrix.md` §11 (`teacher_workspace.subscription.*`); `05_User_Flows.md` flow 26.

### TCH-04 — No Marketplace; Teachers Do Not Sell Courses

- **Rule ID:** TCH-04
- **Rule Name:** Marketplace Exclusion
- **Description:** The Platform is **not** an online course marketplace. Teachers do **not** sell courses through the Platform. There is no course discovery or browsing across Teachers, and no mechanism by which one Teacher's content reaches another Teacher's Students.
- **Applies To:** All Teacher content (Lessons, Question Bank, Exams); all roles; all product surfaces.
- **Trigger:** Any feature, permission, search, or content-publication behavior that resembles discovery, browsing, or selling.
- **Expected Behavior:** Such behavior is denied; no public Teacher content catalog, cross-Teacher content sharing, or marketplace permission exists for any role.
- **Exceptions:** None confirmed; a marketplace would require a separate future Product Owner scope decision (which is not part of any current plan).
- **Related Documents:** `00_Project_Context.md` §4.1; `29_Project_Decisions.md` D-050; `02_Software_Requirements.md` Part 1 §11; `09_Permission_Matrix.md` §7 (`…browse_marketplace` — Denied), §17.

**Cross-referenced rules (authoritative definitions elsewhere in this catalog):** BR-005 and BR-014 (§24 — Archive/retention apply to every Teacher operation), BR-006 (§25 — Teacher actions are audited), BR-007 (§10 — Group moves preserve history), BR-011 (§13 — private Question Bank), BR-018 (§15 — private Lessons), BR-019 (§18 — no payment processing), BR-008 (§16 — Billing inputs).

---

# 6. Student Rules

**Authoritative sources:** `00_Project_Context.md` §6.4, §7.2, §9.1 (BR-001, BR-002, BR-022), `02_Software_Requirements.md` Part 3, `05_User_Flows.md` flows 6–9, 13, 15, 17–18, `08_RBAC.md` §7, `09_Permission_Matrix.md` §3–§11, `07_Data_Dictionary.md` §6.

### BR-001 — One Global Student Account; Per-Teacher Partitioning

- **Rule ID:** BR-001 (canonical, CONFIRMED)
- **Rule Name:** Single Global Student Account with Multiple Teachers
- **Description:** A Student has exactly **one global account** and **may study with multiple Teachers**. Per-Teacher data — Attendance, Homework, Exams, Lessons, and Subscription-related (Flow B) status — exists **separately per Teacher** inside that single account. One login; all content partitioned per Teacher.
- **Applies To:** Student identity; every Student-facing module; scalability and security design.
- **Trigger:** Any Student account creation, access, or data display.
- **Expected Behavior:** The Student authenticates once and sees only the Student's own records, separated per Teacher relationship; scaling or feature work must never create duplicates and never mix per-Teacher data.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §4, §6.4, §9.1 (BR-001); `03_System_Architecture.md` §2, §5.4; `02_Software_Requirements.md` Part 3 §1; `29_Project_Decisions.md` D-030 (context).

### BR-002 — One Group Per Teacher at Any Time

- **Rule ID:** BR-002 (canonical, CONFIRMED)
- **Rule Name:** Single Active Group Membership Per Teacher
- **Description:** A Student belongs to **only ONE Group per Teacher** at any time. Group moves close one Enrollment period and open another (BR-007, §10). A second simultaneous Group assignment for the same Teacher is rejected.
- **Applies To:** Enrollments; Group assignment and movement; scheduling; billing inputs.
- **Trigger:** Any Group assignment or Group move for a Student.
- **Expected Behavior:** The Platform verifies the one-Group-per-Teacher constraint before creating or updating the active Enrollment; the constraint is enforced per-tenant at the data and authorization layers.
- **Exceptions:** None confirmed. A Student may simultaneously belong to Groups of **different** Teachers under BR-001.
- **Related Documents:** `00_Project_Context.md` §9.1 (BR-002); `29_Project_Decisions.md` D-030; `06_Database_Design.md` §12; `05_User_Flows.md` flow 9; `02_Software_Requirements.md` Part 2 §3–§4.

### STU-01 — Student Self Scope

- **Rule ID:** STU-01
- **Rule Name:** Student Access Limited to Own Account and Own Per-Teacher Records
- **Description:** A Student can access only the Student's own account information, own schedule, Homework assigned to them, Lessons from their own Teachers, Exams assigned or available to them, own per-Teacher Attendance/results/grades where available, and own per-Teacher Flow B status. A Student must never access another Student's records, Teacher Workspace management areas, or Teacher-private Question Banks outside an authorized Exam context.
- **Applies To:** Student Panel; all Student-scoped queries and views.
- **Trigger:** Any Student data request.
- **Expected Behavior:** The backend returns only self-scoped, per-Teacher partitioned data; requests for another Student's or a Teacher's private records are denied without disclosure.
- **Exceptions:** None confirmed.
- **Related Documents:** `08_RBAC.md` §3, §7; `05_User_Flows.md` flow 8; `09_Permission_Matrix.md` §4.

### STU-02 — No Student Self-Service Group Moves

- **Rule ID:** STU-02
- **Rule Name:** Group Assignment Is Teacher-Side Only
- **Description:** A Student cannot self-assign to a Group, cannot move themselves between Groups, and cannot modify Group assignment. No Student self-service Group-joining permission is confirmed; all Group membership changes are authorized Teacher-side operations.
- **Applies To:** Student Panel; Group and Enrollment management.
- **Trigger:** Any Student attempt to join, leave, or change a Group.
- **Expected Behavior:** The attempt is denied; Group membership changes only through the authorized Teacher or Teacher Staff workflow (with history preserved per BR-007, §10).
- **Exceptions:** None confirmed.
- **Related Documents:** `05_User_Flows.md` flow 9 (Error Flows); `09_Permission_Matrix.md` §3 (`student_account.group.update` — Denied); `02_Software_Requirements.md` Part 3 §7.

### STU-03 — Student Attendance Participation Boundary

- **Rule ID:** STU-03
- **Rule Name:** What a Student May and May Not Do in Attendance
- **Description:** A Student may scan the daily Dynamic QR Code through the Web Application for the Student's **own** Attendance (after backend validation) and may view the Student's own Attendance where available. A Student may not: scan a printed ID Card as a self-service operation (it is a Teacher-side operation), manually record/correct/modify Attendance, scan for another Student, or scan for a Teacher with whom the Student has no valid relationship.
- **Applies To:** Attendance module; Student role.
- **Trigger:** Any Student Attendance interaction.
- **Expected Behavior:** Only the Student's own scan (Dynamic QR Code) and view actions proceed; every modification attempt is denied.
- **Exceptions:** None confirmed.
- **Related Documents:** `16_QR_Attendance_System.md` §7, §18; `09_Permission_Matrix.md` §5; `05_User_Flows.md` flow 10.

### STU-04 — Student Homework Participation Boundary

- **Rule ID:** STU-04
- **Rule Name:** What a Student May and May Not Do with Homework
- **Description:** A Student may view Homework assigned to the Student and submit the Student's own responses in the supported formats (Text, Image, PDF; binary upload Image or PDF only) for assigned Homework. A Student may not create or edit Teacher Homework, grade Homework, submit for unassigned Homework, or submit video or other unsupported types.
- **Applies To:** Homework module; Student role.
- **Trigger:** Any Student Homework interaction.
- **Expected Behavior:** Valid self-scoped submissions are recorded under the correct Student and Teacher Workspace; invalid or unauthorized actions are rejected.
- **Exceptions:** Modification of a Student's own submission is conditional on later detailed requirements (`09_Permission_Matrix.md` §6 — `…update_submission` Conditional).
- **Related Documents:** `05_User_Flows.md` flow 13; `09_Permission_Matrix.md` §6; `20_File_Storage.md` §3, §7; BR-021 (§12).

### STU-05 — Student Exam Participation Boundary

- **Rule ID:** STU-05
- **Rule Name:** What a Student May and May Not Do in Exams
- **Description:** A Student may view and attempt Exams assigned or made available through the Student's own Teacher relationships, submit the Student's own attempts, and view own attempt status/grades where available. A Student may not view another Student's attempts or grades, attempt an Exam from a Teacher with whom the Student is not enrolled, access the private Question Bank outside the authorized Exam, or create/edit/publish/Archive/restore/grade Exams.
- **Applies To:** Exam Engine; Student role.
- **Trigger:** Any Student Exam interaction.
- **Expected Behavior:** Attempts and results stay scoped to Student × Teacher Workspace; unauthorized access is denied without disclosure.
- **Exceptions:** None confirmed.
- **Related Documents:** `15_Exam_Engine.md` §9, §24; `09_Permission_Matrix.md` §8; BR-012 (§13).

### STU-06 — Student Lesson Access Boundary

- **Rule ID:** STU-06
- **Rule Name:** Lessons Only from the Student's Own Teachers
- **Description:** A Student may view only Lessons authorized/available through the Student's own Teacher relationships. Requests for Lessons of an unrelated Teacher are denied; a direct file path or identifier never bypasses backend authorization. The Student may not create, edit, Archive, restore, or manage Lessons.
- **Applies To:** Lesson module; Student role; file access.
- **Trigger:** Any Student Lesson or Lesson-file request.
- **Expected Behavior:** Only authorized Lesson content for the Student's own Teacher relationships is provided; everything else is denied without exposing private storage details.
- **Exceptions:** None confirmed; Lesson hosting/protection mechanics are PENDING (Q-010, §15 LSN-04).
- **Related Documents:** `05_User_Flows.md` flow 15; `09_Permission_Matrix.md` §7; `20_File_Storage.md` §6; BR-018 (§15).

### STU-07 — Student Subscriptions Surface Means Flow B Only

- **Rule ID:** STU-07
- **Rule Name:** Student-Facing "Subscriptions" Is Flow B Status
- **Description:** The Student Panel's Subscriptions area shows the Student's **per-Teacher Flow B payment status** only. It is not the Flow A Teacher Platform Subscription, and Flow A records are not accessible to Students.
- **Applies To:** Student Panel Subscriptions; payment-status visibility.
- **Trigger:** Student opens Subscriptions / payment-status views.
- **Expected Behavior:** Per-Teacher Flow B status is displayed, partitioned per Teacher; the Student cannot modify payment status and cannot process payments.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §7.2; `09_Permission_Matrix.md` §10–§11 (`student_account.subscription.view` — Denied; it is Flow B); `02_Software_Requirements.md` Part 3 §6.

**Cross-referenced rules (authoritative definitions elsewhere in this catalog):** BR-022 and REG-01 (§4 — registration/activation), BR-007 (§10 — transfer history), BR-010 (§11 — Attendance methods), BR-011/BR-012 (§13 — Exams), BR-018 (§15 — Lessons), BR-019 (§18 — no payment processing), BR-021 (§12 — Homework formats), BR-017 (§3 — web only).

---

# 7. Parent Rules

**Authoritative sources:** `00_Project_Context.md` §6.5, §7.3, §9.8 (BR-004, BR-020), `02_Software_Requirements.md` Part 4, `05_User_Flows.md` flows 19–24, `08_RBAC.md` §8, `09_Permission_Matrix.md`, `07_Data_Dictionary.md` §7–§8; decision D-009 (`29_Project_Decisions.md`).

### BR-004 — Parent Sees Only Linked Students; Read-Only Everywhere

- **Rule ID:** BR-004 (canonical, CONFIRMED)
- **Rule Name:** Parent Linked-Student Read-Only Boundary
- **Description:** A Parent sees **only linked Students** and has **read-only access everywhere**. A Parent cannot modify Attendance, grades, Homework, Exams, Student records, Teacher records, payment status, or any Teacher Workspace data.
- **Applies To:** Parent role; every Parent Panel surface; every Parent data request.
- **Trigger:** Any Parent read or write attempt.
- **Expected Behavior:** Reads succeed only for linked Students; every modification attempt is denied. Parent visibility never becomes a path to alter data.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §6.5, §9.1 (BR-004); `08_RBAC.md` §3, §8; `02_Software_Requirements.md` Part 4 (all sections); `07_Data_Dictionary.md` §7.

### BR-020 — One Parent Account Per Student; A Parent May Link Multiple Students

- **Rule ID:** BR-020 (canonical, CONFIRMED)
- **Rule Name:** Parent–Student Linking Rule and Student Switcher
- **Description:** Version 1 supports exactly **ONE Parent account per Student** — a Student cannot have multiple Parent accounts linked simultaneously. One Parent account **may be linked to multiple Students**. The Parent Panel includes a **Student Switcher** for navigation between linked Students.
- **Applies To:** Parent accounts; Parent Student Link relationships; Parent Panel navigation.
- **Trigger:** Any Parent–Student link creation or Parent context selection.
- **Expected Behavior:** Linking enforces one Parent account per Student; the Parent may monitor multiple linked Students and switches context through the Student Switcher.
- **Exceptions:** None confirmed. The detailed Parent registration/link-establishment mechanism is not separately specified and must not be invented beyond these confirmed constraints.
- **Related Documents:** `00_Project_Context.md` §6.5, §9.8 (BR-020); `29_Project_Decisions.md` D-009; `07_Data_Dictionary.md` §8; `05_User_Flows.md` flow 20.

### PAR-01 — Parent Cannot Modify Any Educational or Financial Record

- **Rule ID:** PAR-01
- **Rule Name:** Explicit Parent Modification Denials
- **Description:** A Parent may not modify Attendance; modify grades; modify or submit Homework for a Student; take Exams or submit Exam answers; modify Exam records; modify payment status; process payments; modify Student records, Teacher records, or Group assignment; or modify Teacher Workspace data. A Parent also cannot record Attendance and cannot upload files.
- **Applies To:** All Parent interactions with educational, financial, and file data.
- **Trigger:** Any Parent write attempt of any kind.
- **Expected Behavior:** The attempt is denied; only read-only viewing for linked Students proceeds.
- **Exceptions:** The Parent's **own account context** (e.g., own account settings where detailed requirements define it) is conditional and never includes linked Students' educational data (`09_Permission_Matrix.md` §12–§13).
- **Related Documents:** `08_RBAC.md` §8 (Denied Permissions); `09_Permission_Matrix.md` §5–§10, §14; `20_File_Storage.md` §14; `02_Software_Requirements.md` Part 4 §8.

### PAR-02 — Parent Teachers View Is Not Marketplace Browsing

- **Rule ID:** PAR-02
- **Rule Name:** Parent Teachers Visibility Boundary
- **Description:** The Parent Panel's Teachers view shows the Teachers of the Parent's linked Students only. It is not Teacher browsing, course discovery, or marketplace behavior, and it exposes no Teacher-private content (Lessons, Question Banks).
- **Applies To:** Parent Panel Teachers view.
- **Trigger:** Parent opens the Teachers view.
- **Expected Behavior:** Only Teachers related to linked Students are shown, read-only; no unrelated Teacher or Teacher-private content is discoverable.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §7.3; `02_Software_Requirements.md` Part 4 §6; `08_RBAC.md` §8.

### PAR-03 — Parent Payments View Is Flow B Only

- **Rule ID:** PAR-03
- **Rule Name:** Parent Payment Visibility Boundary
- **Description:** The Parent Panel's Payments view shows the linked Student's **per-Teacher Flow B payment status** (derived from Group Price and Pricing Type) only. Requests for Flow A Teacher Platform Subscription data are denied; no payment action can be initiated.
- **Applies To:** Parent Panel Payments.
- **Trigger:** Parent opens Payments for a selected linked Student.
- **Expected Behavior:** Recorded Flow B status (and the applicable Group Price/Pricing Type basis where permitted) is shown read-only; no transaction can be initiated because Version 1 records status only.
- **Exceptions:** If no status is recorded, an appropriate empty/unavailable state is shown — never an invented value.
- **Related Documents:** `05_User_Flows.md` flow 24; `02_Software_Requirements.md` Part 4 §7; `09_Permission_Matrix.md` §10–§11; BR-019 (§18).

### PAR-04 — Student Switcher Is a Context Change, Not an Authorization Bypass

- **Rule ID:** PAR-04
- **Rule Name:** Student Switcher Semantics
- **Description:** The Student Switcher changes the Parent's active monitoring context from one linked Student to another. It never alters authorization: selecting an unlinked Student is denied, and the switcher cannot be used to alter Student educational records or payment status. The Parent must never see another Parent's linked Students.
- **Applies To:** Parent Panel Student Switcher.
- **Trigger:** Parent selects a Student in the Student Switcher.
- **Expected Behavior:** The Platform validates the Parent–Student link and presents only that linked Student's authorized read-only information, separated by the Student's Teacher relationships.
- **Exceptions:** A stale or unavailable link produces a safe unavailable state without exposing information.
- **Related Documents:** `05_User_Flows.md` flow 20; `00_Project_Context.md` §7.3, BR-020.

### PAR-05 — Parent Audit Log Visibility Is Not a Confirmed Surface

- **Rule ID:** PAR-05
- **Rule Name:** No Parent Audit Log Surface in Version 1
- **Description:** Parent (and Student) access to the Audit Log is not a confirmed Version 1 product surface. Parent login events are audited, but the Parent has no Audit Log view.
- **Applies To:** Audit Log visibility; Parent Panel.
- **Trigger:** Any Parent request for Audit Log information.
- **Expected Behavior:** The request is denied; Audit visibility stays within the confirmed scopes (see §25, AUD-06).
- **Exceptions:** None confirmed.
- **Related Documents:** `09_Permission_Matrix.md` §16; `03_System_Architecture.md` §16.4; `08_RBAC.md` §8.

**Cross-referenced rules (authoritative definitions elsewhere in this catalog):** BR-001 (§6 — per-Teacher partitioning inside each linked Student's context), BR-003 (§5 — Parent access spans workspaces only via linked Students, read-only), BR-010 (§11 — Attendance), BR-011/BR-012 (§13 — Exams), BR-019 (§18 — status-only payments), BR-021 (§12 — Homework formats), BR-014 (§24 — historical visibility).

---

# 8. Super Admin Rules

**Authoritative sources:** `00_Project_Context.md` §5.1, §6.1, §9.4 (BR-015), `02_Software_Requirements.md` Part 5, `08_RBAC.md` §9, `09_Permission_Matrix.md` §16–§17, `17_Subscription_Billing.md` §16, `18_Reporting_Analytics.md` §7; decisions D-005, D-032 (`29_Project_Decisions.md`).

### ADM-01 — Super Admin Is Platform-Scoped

- **Rule ID:** ADM-01
- **Rule Name:** Platform-Level Administration Only
- **Description:** The Super Admin owns the Platform at **Platform-level scope**. The Super Admin manages Teachers, Flow A Subscriptions, pricing, platform settings, and global reports — and does **not** operate inside Teacher Workspaces as a Teacher.
- **Applies To:** Super Admin role; Platform Administration module.
- **Trigger:** Any Super Admin action.
- **Expected Behavior:** Actions are evaluated at Platform scope; any attempt to act as a Teacher or inside a Teacher Workspace as a Teacher is denied.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §6.1; `08_RBAC.md` §3, §9; `02_Software_Requirements.md` Part 5 §1; `03_System_Architecture.md` §10.2.

### BR-015 — Pricing Is Owned by the Super Admin

- **Rule ID:** BR-015 (canonical, CONFIRMED)
- **Rule Name:** Super Admin Pricing Ownership
- **Description:** Platform Subscription pricing (Price Per Student; tiers if ever adopted) is owned by the Super Admin at Platform level. **Historical invoices keep the price as of their period.** Subscription pricing is globally configured at Platform level for Teachers; Teachers cannot configure Platform pricing.
- **Applies To:** Flow A pricing; Platform Settings; Billing.
- **Trigger:** Any pricing configuration or Billing calculation.
- **Expected Behavior:** Only the Super Admin configures pricing; Billing uses the applicable Price Per Student; historical periods retain their original price context.
- **Exceptions:** Flat price versus volume tiers is **PENDING** (Q-013; proposed default: flat price per Student at launch with a tier-ready engine). No tier behavior is applied until resolved.
- **Related Documents:** `00_Project_Context.md` §5.1, §9.4 (BR-015); `29_Project_Decisions.md` D-032; `17_Subscription_Billing.md` §6, §16; `02_Software_Requirements.md` Part 5 §6; Q-013.

### ADM-02 — No Teacher Workspace Operations by Super Admin

- **Rule ID:** ADM-02
- **Rule Name:** Super Admin Cannot Operate as Teacher
- **Description:** The Super Admin may not operate inside Teacher Workspaces as a Teacher, may not bypass Teacher Workspace isolation, and may not access Teacher-private content beyond confirmed visibility boundaries. No "Login as Teacher" behavior exists (see §3, AUTH-05).
- **Applies To:** Super Admin; all Teacher Workspace areas.
- **Trigger:** Any Super Admin attempt to enter, browse, or act within a Teacher Workspace.
- **Expected Behavior:** The attempt is denied; Platform-level management never implies Teacher-role capabilities.
- **Exceptions:** None confirmed.
- **Related Documents:** `08_RBAC.md` §9; `09_Permission_Matrix.md` §17; `02_Software_Requirements.md` Part 5 §1, §7.

### ADM-03 — Super Admin Content Visibility Is PENDING

- **Rule ID:** ADM-03
- **Rule Name:** Teacher-Private Content Visibility Boundary (Q-012)
- **Description:** Super Admin visibility into Teacher-private content (Lesson videos, Question Banks, Homework content, Exam definitions, workspace-private Student records) is **PENDING** (Q-012). Until resolved, the Platform must not grant unrestricted browsing of Teacher-private content; Platform reporting remains non-invasive.
- **Applies To:** Super Admin reports, dashboards, global search, Audit Log visibility.
- **Trigger:** Any Super Admin request touching Teacher-private content.
- **Expected Behavior:** Only confirmed Platform-level administration and non-invasive reporting are allowed; anything beyond is denied or restricted to confirmed scope.
- **Exceptions:** The documented **proposed default** is aggregates/finances/metadata only, with no browsing of Teacher-private content (D-005, PROPOSED). It remains a working default, not a confirmed rule.
- **Related Documents:** `00_Project_Context.md` §6.1, §15.1 (Q-012); `29_Project_Decisions.md` D-005; `18_Reporting_Analytics.md` §7; `09_Permission_Matrix.md` — `platform.*.view_private*` entries (Conditional).

### ADM-04 — Platform Staff Roles Are Out of Scope

- **Rule ID:** ADM-04
- **Rule Name:** No Support/Sales/Accountant Platform Staff in Version 1
- **Description:** Platform staff accounts such as Support, Sales, and Accountant are out of scope for Version 1 because they are not among the five confirmed roles (Super Admin, Teacher, Teacher Staff, Student, Parent).
- **Applies To:** Platform user administration; RBAC.
- **Trigger:** Any attempt to define or create Platform staff roles.
- **Expected Behavior:** The attempt is denied; exactly five roles exist in Version 1.
- **Exceptions:** Future versions may define additional Platform roles if separately approved (`08_RBAC.md` §17).
- **Related Documents:** `02_Software_Requirements.md` Part 5 §2, §6; `07_Data_Dictionary.md` §2; `09_Permission_Matrix.md` §12 (`platform.user.create_platform_staff` — Denied), §17.

### ADM-05 — Super Admin Manages Flow A Status Manually

- **Rule ID:** ADM-05
- **Rule Name:** Flow A Subscription Status Authority
- **Description:** The Super Admin views/manages Flow A Subscription records and Billing Cycles, manages Platform pricing, and **manually records** Teacher Subscription payment status after the externally handled payment event. The Platform never collects money, initiates transactions, or stores gateway payment details.
- **Applies To:** Platform Subscriptions and Payments administration.
- **Trigger:** An external Teacher Subscription payment event; Super Admin management actions.
- **Expected Behavior:** The Super Admin records the resulting status; every Subscription change is recorded in the Audit Log; Flow A is never presented as Flow B.
- **Exceptions:** Permitted payment-status values, references, reconciliation, refunds, and adjustments are not confirmed (`17_Subscription_Billing.md` §9).
- **Related Documents:** `17_Subscription_Billing.md` §9, §16; `09_Permission_Matrix.md` §10–§11; `05_User_Flows.md` flow 26.

### ADM-06 — Super Admin Financial and Lifecycle Boundaries

- **Rule ID:** ADM-06
- **Rule Name:** What the Super Admin May Never Do
- **Description:** The Super Admin may not: process payments through the Platform; configure online payment gateways; treat Flow B Student/Parent fees as Platform revenue; hard delete any record; apply unconfirmed non-payment enforcement (grace, suspension, reactivation); or gain unrestricted Teacher-private content access.
- **Applies To:** Super Admin; Platform administration.
- **Trigger:** Any matching attempt.
- **Expected Behavior:** The attempt is denied and, where security-sensitive, logged; Flow A and Flow B remain separate in data, labels, and reports.
- **Exceptions:** None confirmed; non-payment enforcement awaits Q-005 (§16, SUB-06).
- **Related Documents:** `08_RBAC.md` §9 (Denied or Constrained Permissions); `09_Permission_Matrix.md` §10–§11, §17; `17_Subscription_Billing.md` §11–§13, §16; BR-005 (§24), BR-019 (§18).

**Cross-referenced rules (authoritative definitions elsewhere in this catalog):** BR-003 (§5 — isolation binds Super Admin too), BR-005/BR-014 (§24), BR-006 (§25 — Super Admin actions audited), BR-008 (§16 — Billing inputs), BR-016 (§5 — Super Admin cannot change a Teaching Subject either), AUTH-05 (§3 — no impersonation).

---

# 9. Educational Grade Rules

**Authoritative sources:** `00_Project_Context.md` §7.1, §19, `02_Software_Requirements.md` Part 2 §2, `05_User_Flows.md` flow 4, `07_Data_Dictionary.md` §9, `18_Reporting_Analytics.md` §14, `09_Permission_Matrix.md` §2.

### GRD-01 — Educational Grades Are Teacher-Created and Workspace-Scoped

- **Rule ID:** GRD-01
- **Rule Name:** Teacher-Created Education Levels
- **Description:** Educational Grades are Teacher-created education levels (for example: First Preparatory, Second Preparatory, First Secondary). They exist only inside the creating Teacher Workspace and must never be visible across Teacher Workspaces.
- **Applies To:** Educational Grade entity; Teacher Panel; all Educational Grade operations.
- **Trigger:** Any Educational Grade creation, view, update, or use.
- **Expected Behavior:** Create/validate/act only within the current Teacher Workspace; cross-Teacher Educational Grade access is denied.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §7.1, §19; `02_Software_Requirements.md` Part 2 §2; `07_Data_Dictionary.md` §9; BR-003 (§5).

### GRD-02 — Educational Grades Are Independent from Teaching Subjects

- **Rule ID:** GRD-02
- **Rule Name:** Subject–Grade Independence
- **Description:** Teaching Subjects are independent from Educational Grades: a Teaching Subject is not bound to any specific Educational Grade, and Educational Grade management never changes the Teaching Subject.
- **Applies To:** Academic structure; Educational Grade and Teaching Subject handling.
- **Trigger:** Any operation relating the two concepts.
- **Expected Behavior:** The two concepts stay separate; an Educational Grade is never treated as, or used to alter, the Teaching Subject.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §8 (point 8), BR-016; `02_Software_Requirements.md` Part 2 §2; `07_Data_Dictionary.md` §9.

### GRD-03 — Educational Grades Contain Groups

- **Rule ID:** GRD-03
- **Rule Name:** Grade-to-Group Structure
- **Description:** Educational Grades contain Groups; each Group belongs to exactly one Educational Grade in the same Teacher Workspace. A newly created Educational Grade may initially have no Groups.
- **Applies To:** Academic structure; Group creation.
- **Trigger:** Group creation or reorganization.
- **Expected Behavior:** A Group is created only under an active Educational Grade of the current Teacher Workspace; selecting an archived or another Teacher's Educational Grade is denied.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §19; `06_Database_Design.md` §5; `05_User_Flows.md` flows 4–5.

### GRD-04 — Educational Grade Archive and Restore Boundaries

- **Rule ID:** GRD-04
- **Rule Name:** Archive Behavior for Educational Grades
- **Description:** Archive — never permanent deletion — applies to Educational Grades. An archived Educational Grade cannot be used in active Group assignment until authorized restoration; archived Educational Grades do not appear as active selection options, while historical reports may include them clearly marked as archived. Archiving an Educational Grade does not remove or archive its historical Groups, Enrollments, or records.
- **Applies To:** Educational Grade lifecycle; assignment lists; reports.
- **Trigger:** Educational Grade Archive/restore; selection-list rendering; historical reporting.
- **Expected Behavior:** Active lists show only active Educational Grades; historical views include archived ones clearly indicated; restore (authorized, audited) returns the Educational Grade to active use with relationships intact.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §11 (Archive Policy); `05_User_Flows.md` flow 4; `18_Reporting_Analytics.md` §14; `06_Database_Design.md` §14; BR-005, BR-014 (§24).

### GRD-05 — Canonical Terminology: Educational Grade, Never "Class"

- **Rule ID:** GRD-05
- **Rule Name:** Mandatory Terminology for Education Levels
- **Description:** The canonical term is **Educational Grade**. The word "Class" must not be used for this concept in any document, interface, or conversation.
- **Applies To:** All documents, UI text, code artifacts, conversations.
- **Trigger:** Any naming decision for the education-level concept.
- **Expected Behavior:** "Educational Grade" is used everywhere; non-canonical usage is treated as a terminology defect.
- **Exceptions:** The non-canonical word may appear only when explicitly cited as a prohibited example (as in this rule).
- **Related Documents:** `00_Project_Context.md` §19; `29_Project_Decisions.md` D-048; `30_Project_Glossary.md` ("Educational Grade"); `09_Permission_Matrix.md` §2 (mapping note).

**Cross-referenced rules:** BR-003 (§5), BR-005 and BR-014 (§24), BR-006 (§25 — create/update/Archive/restore audited).

---

# 10. Group Rules

**Authoritative sources:** `00_Project_Context.md` §7.1, §9.1 (BR-002), §9.3 (BR-007), §9.5 (BR-009), `02_Software_Requirements.md` Part 2 §3, `05_User_Flows.md` flows 5–6, 9, `07_Data_Dictionary.md` §10–§12, `09_Permission_Matrix.md` §3, `06_Database_Design.md` §9, §14; decisions D-030, D-031 (`29_Project_Decisions.md`).

### GRP-01 — Group Required Attributes and Grade Membership

- **Rule ID:** GRP-01
- **Rule Name:** Group Structure
- **Description:** Each Group belongs to one Educational Grade and carries **Name, Schedule, Price, and Pricing Type**. A Group is not a Teaching Subject. A valid Group may initially contain no Students.
- **Applies To:** Group entity; Group creation/update.
- **Trigger:** Group creation or update.
- **Expected Behavior:** All required attributes are validated (including a valid Price and a Pricing Type of Monthly or Per Lesson); the Group is created under an active Educational Grade of the current Teacher Workspace.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §7.1; `02_Software_Requirements.md` Part 2 §3; `05_User_Flows.md` flow 5; `07_Data_Dictionary.md` §10.

### BR-009 — Group Price and Pricing Type Drive Flow B

- **Rule ID:** BR-009 (canonical, CONFIRMED)
- **Rule Name:** Pricing Type and Flow B Derivation
- **Description:** Every Group carries a **Price** and a **Pricing Type** (`Monthly` or `Per Lesson`). Student fee obligations (Flow B) derive from Group Enrollment, Price, and Pricing Type. Version 1 records payment status only — actual payments are handled outside the Platform (BR-019, §18). Group pricing establishes the basis for Flow B fees, never for the Flow A Subscription.
- **Applies To:** Groups; Flow B fee tracking; Parent/Student/Teacher payment-status views.
- **Trigger:** Group pricing configuration; Flow B status derivation and display.
- **Expected Behavior:** Flow B obligations and statuses are computed from the Student's Group context; Pricing Type accepts only `Monthly` or `Per Lesson`; Flow B never masquerades as Flow A.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §9.5 (BR-009), §5.2; `02_Software_Requirements.md` Part 2 §3, Part 3 §6; `07_Data_Dictionary.md` §10, §33; `05_User_Flows.md` flow 5.

### BR-007 — Student Transfers Preserve All History

- **Rule ID:** BR-007 (canonical, CONFIRMED)
- **Rule Name:** Transfer History Preservation
- **Description:** Student transfers between Groups preserve historical Attendance, Homework, Exams, and grades. History is **never moved, deleted, or rewritten** by structural changes; historical records reference the Enrollment period and structure as of recording time.
- **Applies To:** Group moves; Enrollments; all historical Student records; reports.
- **Trigger:** A Student moves from one Group to another under the same Teacher.
- **Expected Behavior:** The previous Enrollment period closes and a new one opens (logically); all prior records remain linked exactly as recorded and remain available in reports and history views.
- **Exceptions:** None confirmed. Archiving related containers also never erases this history (GRP-06).
- **Related Documents:** `00_Project_Context.md` §9.3 (BR-007), §11.5, §12.4; `29_Project_Decisions.md` D-030, D-031; `06_Database_Design.md` §9; `05_User_Flows.md` flow 9.

### GRP-02 — Enrollment Period Mechanics (PROPOSED Mechanics)

- **Rule ID:** GRP-02
- **Rule Name:** Time-Bounded Enrollments
- **Description:** Enrollment is the link between a Student and one Group (and therefore one Teacher), modeled as a time-bounded period. When a Student moves Groups, the previous Enrollment closes logically and a new one opens, preserving the historical association of records to their original Enrollment context. (Mechanics are PROPOSED in the Project Context; the business outcome is CONFIRMED by BR-007.)
- **Applies To:** Enrollment lifecycle; historical reporting; billing inputs.
- **Trigger:** Group assignment and Group moves.
- **Expected Behavior:** One active Enrollment per Student per Teacher at any time (BR-002, §6); history queries resolve the correct period context.
- **Exceptions:** Physical mechanics are PROPOSED (`00_Project_Context.md` §12.4); only the confirmed outcomes are binding.
- **Related Documents:** `00_Project_Context.md` §12.4, §19 (Enrollment); `06_Database_Design.md` §9; `07_Data_Dictionary.md` §12.

### GRP-03 — Archived Group Behavior

- **Rule ID:** GRP-03
- **Rule Name:** Archived Groups Are Not Active Assignment Targets
- **Description:** Archived Groups are removed from active assignment lists and active report-selection contexts, but remain historical and can be restored by authorized users; restoration preserves historical relationships and is audited. Assignment to an archived Group is rejected.
- **Applies To:** Group lifecycle; assignment pickers; reports.
- **Trigger:** Group Archive/restore; any assignment attempt; report filter rendering.
- **Expected Behavior:** Active lists exclude archived Groups; historical/report contexts may include them clearly indicated; restore returns the Group to active use without rewriting history.
- **Exceptions:** None confirmed.
- **Related Documents:** `09_Permission_Matrix.md` §3; `18_Reporting_Analytics.md` §13; `00_Project_Context.md` §11; `05_User_Flows.md` flows 5–6.

### GRP-04 — Container Archival Never Erases Child History

- **Rule ID:** GRP-04
- **Rule Name:** Archive Cascade Protection
- **Description:** Archiving a container (for example, an Educational Grade or a Group) removes it from active use but **never** archives, detaches, rewrites, or deletes its historical child records. Historical Enrollments, Attendance, Homework, Exams, grades, and payment history remain linked exactly as recorded.
- **Applies To:** All container records (Educational Grade, Group, Exam, Homework, Lesson, Teacher account, Teacher Staff account).
- **Trigger:** Any container Archive action.
- **Expected Behavior:** Only the container's active state changes; child history remains intact, reportable, and restorable with the container.
- **Exceptions:** None confirmed. Entity-specific cascade behavior is deferred to physical design but must never contradict this rule.
- **Related Documents:** `00_Project_Context.md` §11 (property 7); `06_Database_Design.md` §14; `29_Project_Decisions.md` D-033.

**Cross-referenced rules:** BR-002 (§6 — one Group per Student per Teacher; assignment enforcement), BR-001 (§6), BR-003 (§5 — Group operations are workspace-scoped), BR-005 and BR-014 (§24), BR-006 (§25 — Group lifecycle events audited), STU-02 (§6 — assignment is Teacher-side only).

---

# 11. Attendance Rules

**Authoritative sources:** `00_Project_Context.md` §9.6 (BR-010), §10.1 (event 7), `16_QR_Attendance_System.md` (whole document), `02_Software_Requirements.md` Part 2 §5, `05_User_Flows.md` flows 10–11, `09_Permission_Matrix.md` §5, `07_Data_Dictionary.md` §13–§15.

### BR-010 — Three Confirmed Attendance Methods

- **Rule ID:** BR-010 (canonical, CONFIRMED)
- **Rule Name:** Dynamic QR Code, ID Card, and Manual Entry Only
- **Description:** Attendance supports exactly three methods: (1) a **Dynamic QR Code generated daily**, displayed for the class and scanned by the Student through the Web Application; (2) a printed static **ID Card** (QR code) scanned by a QR scanner device; and (3) **manual** entry by the Teacher or authorized Teacher Staff. No other Attendance method is confirmed.
- **Applies To:** Attendance subsystem; all roles that participate in Attendance.
- **Trigger:** Any Attendance recording.
- **Expected Behavior:** Recording occurs through one of the three confirmed methods within a valid Teacher Workspace Attendance Session/context; unconfirmed methods are rejected.
- **Exceptions:** Barcode scanning, biometric, face recognition, geolocation/GPS, NFC, Bluetooth, SMS, email, and native mobile Attendance are **out of scope** (not exceptions, but exclusions) — see ATT-10. ID Cards use QR codes, not barcodes.
- **Related Documents:** `00_Project_Context.md` §9.6 (BR-010), §7.1; `16_QR_Attendance_System.md` §1, §3; `02_Software_Requirements.md` Part 2 §5; `29_Project_Decisions.md` (Q-007 resolution).

### ATT-01 — Daily Dynamic QR Code Generation

- **Rule ID:** ATT-01
- **Rule Name:** Daily QR Cadence
- **Description:** A Dynamic QR Code is generated **daily** for Attendance in a Teacher Workspace context and displayed for the class. Generation belongs to the current Teacher Workspace's valid Attendance Session/context and requires appropriate Teacher Workspace Attendance permission.
- **Applies To:** Teacher and authorized Teacher Staff; Attendance Sessions.
- **Trigger:** Establishment of a daily Attendance context.
- **Expected Behavior:** The Platform generates the daily code for the correct workspace Session/context; generated contexts can never record Attendance for Students outside the relevant Teacher relationship.
- **Exceptions:** QR payload format, token construction, refresh frequency within a day, rotation behavior, and precise expiry timestamps are **not confirmed** and must not be invented (`16_QR_Attendance_System.md` §5–§6).
- **Related Documents:** `00_Project_Context.md` §9.6, §19 (Dynamic QR Code); `16_QR_Attendance_System.md` §4–§6; `21_Background_Jobs.md` §7.1.

### ATT-02 — Student Scans Only Through the Web Application

- **Rule ID:** ATT-02
- **Rule Name:** Web-Application-Only Student Scanning
- **Description:** The Student scans the daily Dynamic QR Code through the Web Application (including a supported mobile-capable browser). This never implies a native mobile application. The QR visual value alone never proves eligibility: the backend authenticates the Student and verifies the Teacher relationship and Session/context before recording.
- **Applies To:** Students; Dynamic QR Code scanning.
- **Trigger:** A Student scans a displayed daily Dynamic QR Code.
- **Expected Behavior:** On successful backend validation, the Student's own Attendance is recorded; failures produce safe recorded/rejected/unavailable outcomes without exposing internals.
- **Exceptions:** None confirmed.
- **Related Documents:** `16_QR_Attendance_System.md` §4, §7; BR-017 (§3); `05_User_Flows.md` flow 10.

### ATT-03 — ID Card Scanning Is a Teacher-Side Operation

- **Rule ID:** ATT-03
- **Rule Name:** Printed ID Card (QR) Scanning Boundary
- **Description:** The printed ID Card method uses a **QR code** (not a barcode), read by a QR scanner device during a valid Teacher Workspace Attendance Session/context. It is a Teacher-side/Teacher Workspace operation: **a Student has no self-service ID Card scanning permission**.
- **Applies To:** Teacher and authorized Teacher Staff; ID Card scanning.
- **Trigger:** A Student presents a printed ID Card for scanning.
- **Expected Behavior:** The backend resolves the Student identity and workspace context, validates the relationship, and records Attendance if valid.
- **Exceptions:** Card printing design, issuance, lost-card handling, scanner hardware, and barcode formats are **not confirmed**.
- **Related Documents:** `16_QR_Attendance_System.md` §3, §8; `09_Permission_Matrix.md` §5 (`student_account.attendance.scan_id_card` — Denied); `00_Project_Context.md` §7.1, §19 (ID Card).

### ATT-04 — Manual Attendance Entry Authority

- **Rule ID:** ATT-04
- **Rule Name:** Manual Entry and Correction Boundary
- **Description:** Manual Attendance (recording or correcting status) is performed only by the Teacher or authorized Teacher Staff inside the current Teacher Workspace, for Students with a valid Teacher relationship. It is the confirmed fallback when QR or ID Card scanning is unavailable. Students cannot modify Attendance; Parents cannot record or correct it.
- **Applies To:** Teacher, Teacher Staff; manual Attendance operations.
- **Trigger:** Manual entry or correction of an Attendance record.
- **Expected Behavior:** The backend verifies permission, workspace scope, Student relationship, and valid Session/context, then records or corrects with an Audit Log entry.
- **Exceptions:** Permitted Attendance **status values** and correction-reason requirements are **not defined** by confirmed requirements; only the ability to record and correct is confirmed (`16_QR_Attendance_System.md` §16).
- **Related Documents:** `16_QR_Attendance_System.md` §9, §16; `05_User_Flows.md` flow 11; `09_Permission_Matrix.md` §5.

### ATT-05 — Duplicate and Inconsistent Attendance Prevention

- **Rule ID:** ATT-05
- **Rule Name:** No Inconsistent Duplicate Attendance
- **Description:** The Platform must prevent inconsistent duplicate Attendance records for the same Student and Attendance context — whether by repeated Dynamic QR scans, conflicting manual entries, or conflicting ID Card scans. The user receives an accurate outcome rather than a false success.
- **Applies To:** All three Attendance methods.
- **Trigger:** A duplicate or conflicting Attendance attempt for the same context.
- **Expected Behavior:** The backend determines whether the record may be recorded or corrected and rejects/safely handles duplicates.
- **Exceptions:** The exact deduplication key, retry behavior, and conflict-resolution mechanics are not confirmed (`16_QR_Attendance_System.md` §13).
- **Related Documents:** `16_QR_Attendance_System.md` §13; `05_User_Flows.md` flow 10 (Error Flows).

### ATT-06 — Invalid QR Handling

- **Rule ID:** ATT-06
- **Rule Name:** Safe Rejection of Invalid QR Scans
- **Description:** An invalid, expired, or contextually incorrect Dynamic QR Code scan is rejected. The response makes clear that Attendance was not recorded, without exposing another Student's or Teacher's data, QR internals, or implementation details. The authorized fallback is the confirmed Manual Attendance method.
- **Applies To:** Dynamic QR Code scanning.
- **Trigger:** An invalid scan.
- **Expected Behavior:** Rejection with a safe message; no Attendance record; no data disclosure.
- **Exceptions:** Precise QR expiration rules remain unconfirmed (`16_QR_Attendance_System.md` §6).
- **Related Documents:** `16_QR_Attendance_System.md` §6, §14; `05_User_Flows.md` flow 10.

### ATT-07 — Attendance Is Never a Billing Input

- **Rule ID:** ATT-07
- **Rule Name:** Attendance Excluded from Billable Student Calculation
- **Description:** Attendance must not be used to calculate Billable Students. Flow A Billable Student calculation uses **Enrollment duration only** (BR-008, §16). No Attendance state may influence Flow A, and Attendance is equally separate from Flow B payment status.
- **Applies To:** Subscription & Billing; Attendance reports; any calculation.
- **Trigger:** Billable Student calculation; Attendance reporting.
- **Expected Behavior:** Attendance data is never queried as a billing input; Attendance reports never compute or display Billable eligibility.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` BR-008; `16_QR_Attendance_System.md` §1, §12, §20, §24; `18_Reporting_Analytics.md` §8; `02_Software_Requirements.md` Part 2 §5.

### ATT-08 — Attendance Changes Are Audited

- **Rule ID:** ATT-08
- **Rule Name:** Attendance Audit Coverage
- **Description:** Recording or modifying any Attendance entry — by any of the three methods — is one of the ten mandatory Audit Log events (Audit Log Policy, event 7). Teacher Staff Attendance actions are attributed to the Teacher Staff user.
- **Applies To:** All Attendance recording, correction, Archive/restore of archivable Attendance-related records.
- **Trigger:** Any Attendance change.
- **Expected Behavior:** An Audit Log entry is written with actor, role, workspace context, and change detail.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §10.1 (event 7); `16_QR_Attendance_System.md` §21; `02_Software_Requirements.md` Part 2 §5. See also §25.

### ATT-09 — No Offline or Unconfirmed Attendance Behavior

- **Rule ID:** ATT-09
- **Rule Name:** Backend Confirmation Required
- **Description:** Offline QR scanning, offline Attendance storage, deferred synchronization, and offline-first behavior are **not confirmed**. The Platform must not claim Attendance was recorded without backend confirmation; when capability or connectivity is unavailable, the confirmed fallback is Manual Attendance once the Platform is available.
- **Applies To:** All Attendance methods.
- **Trigger:** Loss of connectivity or scanner/browser capability.
- **Expected Behavior:** No local/offline record is assumed; accurate unavailable/error states are provided.
- **Exceptions:** None confirmed.
- **Related Documents:** `16_QR_Attendance_System.md` §15; `05_User_Flows.md` flow 10 (Alternative Flows).

### ATT-10 — Unconfirmed Attendance Concepts Must Not Be Inferred

- **Rule ID:** ATT-10
- **Rule Name:** No Late/Absence Policy, Status Taxonomy, or Other Methods
- **Description:** Late-arrival rules, absence rules (reasons, excused/unexcused, thresholds), Attendance status value taxonomies, barcode input, and additional methods (biometric, geolocation, GPS, NFC, Bluetooth, SMS, email, native mobile) are **not confirmed** for Version 1 and must not be inferred or introduced.
- **Applies To:** Attendance design, documentation, and implementation.
- **Trigger:** Any requirement or design touching these areas.
- **Expected Behavior:** The work stops at the confirmed boundary; only separately approved future decisions may extend it.
- **Exceptions:** None — these areas await formal approval (`16_QR_Attendance_System.md` §25).
- **Related Documents:** `16_QR_Attendance_System.md` §3, §11, §12, §16, §25; `02_Software_Requirements.md` Part 1 §11.

**Cross-referenced rules:** BR-003 (§5 — Attendance records are workspace-scoped), BR-005 (§24 — Archive applies where Attendance-related records leave active use; history never deleted), BR-006 (§25), BR-007 (§10 — Attendance history survives Group movement), BR-017 (§3 — web only), PAR-01 (§7 — Parents read-only).

---

# 12. Homework Rules

**Authoritative sources:** `00_Project_Context.md` §9.7 (BR-021), §10.1 (event 9), `02_Software_Requirements.md` Part 3 §3, Part 4 §3, `05_User_Flows.md` flows 12–13, 22, `09_Permission_Matrix.md` §6, `07_Data_Dictionary.md` §16–§17, `20_File_Storage.md` §3, §7; decision D-011 (`29_Project_Decisions.md`).

### BR-021 — Homework Formats: Text, Image, and PDF Only

- **Rule ID:** BR-021 (canonical, CONFIRMED)
- **Rule Name:** Confirmed Homework Formats
- **Description:** Homework supports **Text, Image, and PDF only**. **Video homework is NOT supported in Version 1.** The rule applies both to Teacher-provided Homework content and to Student submissions (Student binary upload is limited to Image or PDF for assigned Homework; Text may be logical content).
- **Applies To:** Homework creation, attachments, and submissions; file upload validation.
- **Trigger:** Any Homework content or submission creation.
- **Expected Behavior:** Unsupported formats — video above all — are rejected; accepted content stays within the confirmed formats and contexts.
- **Exceptions:** Exact image formats, PDF version requirements, and MIME catalogs are not confirmed (`20_File_Storage.md` §3); no format is silently expanded.
- **Related Documents:** `00_Project_Context.md` §9.7 (BR-021), §4.2; `29_Project_Decisions.md` D-011; `20_File_Storage.md` §3, §7, §11; `07_Data_Dictionary.md` §16–§17.

### HW-01 — Homework Is Workspace-Scoped and Assignment-Visible

- **Rule ID:** HW-01
- **Rule Name:** Homework Ownership and Visibility
- **Description:** Homework exists only inside the creating Teacher Workspace and is made available only to its authorized assigned Students through the correct Teacher relationship. No other Teacher's Students ever receive or see it. Review and management of submissions are limited to authorized Teacher Workspace users.
- **Applies To:** Homework module; Teacher, Teacher Staff, Student.
- **Trigger:** Homework creation, assignment, listing, or submission review.
- **Expected Behavior:** Permission, workspace scope, and target relationship are validated before creation or display; cross-workspace targets are denied.
- **Exceptions:** None confirmed.
- **Related Documents:** `05_User_Flows.md` flows 12–13; `02_Software_Requirements.md` Part 3 §3; BR-003 (§5).

### HW-02 — Homework Modifications Are Audited

- **Rule ID:** HW-02
- **Rule Name:** Homework Audit Coverage
- **Description:** Creating, editing, grading, or archiving Homework is one of the ten mandatory Audit Log events (Audit Log Policy, event 9). Qualifying Student submission events are audited where they meet the policy bar.
- **Applies To:** All Homework lifecycle actions and qualifying submissions.
- **Trigger:** Any Homework modification or qualifying submission event.
- **Expected Behavior:** An Audit Log entry is written with correct actor attribution (Teacher Staff actions attributed to the staff user).
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §10.1 (event 9); `02_Software_Requirements.md` Part 3 §3; `08_RBAC.md` §7 (audited Student actions). See also §25.

### HW-03 — Homework Lifecycle and History

- **Rule ID:** HW-03
- **Rule Name:** Archive and History for Homework
- **Description:** Archive replaces permanent deletion for Homework. Archived Homework must not appear as active Homework while remaining available as clearly-indicated historical data; Homework history (including submissions) is preserved when a Student moves Groups (BR-007, §10).
- **Applies To:** Homework lifecycle; Student/Parent/Teacher views; reports.
- **Trigger:** Homework Archive/restore; Group moves; historical viewing.
- **Expected Behavior:** Active lists show active Homework only; historical contexts include archived Homework clearly indicated; transfers never lose Homework history.
- **Exceptions:** None confirmed.
- **Related Documents:** `02_Software_Requirements.md` Part 3 §3, Part 4 §3; `00_Project_Context.md` §11; `07_Data_Dictionary.md` §17; BR-005, BR-014 (§24).

**Cross-referenced rules:** BR-001 (§6 — per-Teacher partitioning), BR-003 (§5), BR-004 / PAR-01 / STU-04 (§7/§6 — Parent read-only, Student submission boundary), HW file rules (§21 — FIL-02, FIL-03).

---

# 13. Exam Rules

**Authoritative sources:** `00_Project_Context.md` §9.6 (BR-011, BR-012), §10.1 (event 8), `15_Exam_Engine.md` (whole document), `02_Software_Requirements.md` Part 2 §6, Part 3 §5, `05_User_Flows.md` flows 16–18, `09_Permission_Matrix.md` §8, `07_Data_Dictionary.md` §20–§24; decisions D-009, D-010 (`29_Project_Decisions.md`).

### BR-011 — Private Teacher-Owned Question Bank; Confirmed Question Types

- **Rule ID:** BR-011 (canonical, CONFIRMED)
- **Description:** The **Question Bank is Teacher-owned and private**. Question types are exactly: **Multiple Choice, True/False, Essay, Bubble Sheet**. Exams are composed **only** from the owning Teacher's bank. Bubble Sheet is an electronic exam simulating traditional paper bubble sheets — Students answer by selecting bubbles on screen; automatic grading is supported (see §14).
- **Rule Name:** Question Bank Privacy and Composition
- **Applies To:** Question Bank; Exam composition; all roles.
- **Trigger:** Question management; Exam creation; any question access.
- **Expected Behavior:** No Teacher, Teacher Staff member, Student, Parent, or Super Admin may view or use another Teacher's private Questions; unsupported question types are rejected; a Student sees question content only inside an authorized Exam.
- **Exceptions:** Question categories/tags/difficulty classifications are not defined (only the four types exist); detailed authoring fields and scoring models are not confirmed.
- **Related Documents:** `00_Project_Context.md` §9.6 (BR-011), §19 (Question Bank); `15_Exam_Engine.md` §3–§7; `07_Data_Dictionary.md` §20–§21; `29_Project_Decisions.md` D-010.

### BR-012 — Exams, Attempts, and Grades Are Workspace-Scoped

- **Rule ID:** BR-012 (canonical, CONFIRMED — derived from BR-003)
- **Rule Name:** Exam Scope (Student × Teacher)
- **Description:** Exam definitions, attempts, and grades are **workspace-scoped** (Student × Teacher). Students and Parents see per-Teacher partitioned views; **Teachers never see other Teachers' results**.
- **Applies To:** Exams, Exam Attempts, Exam Answers, Exam Grades; reporting.
- **Trigger:** Any Exam, attempt, answer, grade, or result access.
- **Expected Behavior:** Records resolve only within the owning Teacher Workspace and the correct Student relationship; cross-Teacher and cross-Student access is denied without disclosure.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §9.6 (BR-012); `15_Exam_Engine.md` §14.4 (via `03_System_Architecture.md` §14.4); `02_Software_Requirements.md` Part 2 §6, Part 3 §5.

### EXM-01 — Only the Four Confirmed Question Types

- **Rule ID:** EXM-01
- **Rule Name:** Question Type Enforcement
- **Description:** A Question must be one of the four supported types: Multiple Choice, True/False, Essay, or Bubble Sheet. Unsupported question input — in authoring or in answering — is rejected.
- **Applies To:** Question Bank; Exam composition; answer submission.
- **Trigger:** Question creation; Exam composition; answer input.
- **Expected Behavior:** Type consistency is validated; unsupported types never enter the bank, an Exam, or a submission.
- **Exceptions:** None confirmed. No oral, video, proctoring, practical, public, marketplace, cross-Teacher, or native-mobile Exam types exist in Version 1.
- **Related Documents:** `15_Exam_Engine.md` §3, §6, §26; `02_Software_Requirements.md` Part 2 §6.

### EXM-02 — Question Visibility Only Inside Authorized Exams

- **Rule ID:** EXM-02
- **Rule Name:** No Question Bank Browsing
- **Description:** Students see Question content only within an Exam assigned or made available through the Student's own Teacher relationship. Parents cannot access Question Bank content. Students, Parents, and other Teachers cannot search or browse a Teacher's private Question Bank.
- **Applies To:** Question Bank visibility; Student/Parent Exam contexts; search.
- **Trigger:** Any question-content access or Question Bank search.
- **Expected Behavior:** Access evaluates the authorized Exam context; everything else is denied.
- **Exceptions:** None confirmed.
- **Related Documents:** `15_Exam_Engine.md` §4; `22_Search_Filtering.md` §10.3; `09_Permission_Matrix.md` §8.

### EXM-03 — Automatic Grading Boundary

- **Rule ID:** EXM-03
- **Rule Name:** Confirmed Automatic Grading Only
- **Description:** Automatic grading applies only to behavior confirmed as automatically gradable — including Bubble Sheet where applicable — and automatic results stay scoped to the correct Student, Exam, and Teacher Workspace. Automatic grading must not expose answer keys, another Student's result, or another Teacher's Exam data.
- **Applies To:** Exam Engine grading; background grading jobs.
- **Trigger:** Submission of an attempt containing automatically gradable questions.
- **Expected Behavior:** Objective questions are graded automatically (via the grading queue); results become available to authorized contexts only.
- **Exceptions:** Scoring formulas, points, partial credit, negative marking, weighting, rounding, grade scales, and release timing are **not confirmed** and must not be invented (`15_Exam_Engine.md` §11, §21).
- **Related Documents:** `15_Exam_Engine.md` §11, §21; `21_Background_Jobs.md` §8.1; D-010.

### EXM-04 — Essay Grading and Pending Results

- **Rule ID:** EXM-04
- **Rule Name:** Authorized Essay Grading; Never Fabricate Results
- **Description:** Essay answers may require authorized Teacher (or authorized Teacher Staff) grading before a final result is available. While grading is incomplete, the Platform indicates the result as unavailable/pending and must **never invent a grade**. Result visibility follows role scope once available.
- **Applies To:** Essay questions; grading workflows; result views (Student, Parent, Teacher).
- **Trigger:** Essay answer submission; grading completion.
- **Expected Behavior:** Pending states are presented honestly; grading actions are attributed and audited; final results release only within authorized scope.
- **Exceptions:** Rubrics, regrading, moderation, feedback, and release criteria are not confirmed (`15_Exam_Engine.md` §12).
- **Related Documents:** `15_Exam_Engine.md` §12, §14, §22; `18_Reporting_Analytics.md` §10; `05_User_Flows.md` flow 18.

### EXM-05 — Attempt Scope Rules

- **Rule ID:** EXM-05
- **Rule Name:** Confirmed Attempt Rules
- **Description:** An attempt belongs to the correct Student, Exam, and owning Teacher Workspace. A Student may attempt only Exams assigned or available through the Student's own Teacher relationships; cannot access another Student's attempt or grade; a Parent cannot take an Exam or submit for a Student; attempts and grades remain historically available through Group movement and Exam Archive (historical only).
- **Applies To:** Exam attempts and grades.
- **Trigger:** Any attempt start, submission, or result access.
- **Expected Behavior:** Attempts execute only within the authorized relationship; history is preserved per BR-007 (§10).
- **Exceptions:** Attempt counts, retakes, resume, drafts, submission replacement, and missed-Exam behavior are **not confirmed** (`15_Exam_Engine.md` §18).
- **Related Documents:** `15_Exam_Engine.md` §9, §18, §27; BR-012; PAR-01 (§7); STU-05 (§6).

### EXM-06 — Archived or Inactive Exams Are Not Active Attempts

- **Rule ID:** EXM-06
- **Rule Name:** Archive Boundary for Exams and Questions
- **Description:** Archive replaces permanent deletion for Exams and Questions. An archived or inactive Exam cannot be taken as an active attempt and is not presented as an active Exam; historical attempts and grades remain available where permitted, clearly historical. An archived Question cannot be used in active Exam composition until restored.
- **Applies To:** Exam/Question lifecycle; Student Exam lists; reports.
- **Trigger:** Exam/Question Archive or restore; attempt requests; report rendering.
- **Expected Behavior:** Active lists contain active Exams only; historical views include archived ones clearly indicated.
- **Exceptions:** None confirmed.
- **Related Documents:** `15_Exam_Engine.md` §7, §8, §18, §27; `18_Reporting_Analytics.md` §10; BR-005 (§24).

### EXM-07 — Exam Modifications Are Audited

- **Rule ID:** EXM-07
- **Rule Name:** Exam Audit Coverage
- **Description:** Creating, editing, publishing/making available, or archiving Exams and Questions is one of the ten mandatory Audit Log events (Audit Log Policy, event 8). Authorized grading and qualifying attempt/submission events are also audited per policy, with Teacher Staff actions attributed to the staff user.
- **Applies To:** Question Bank and Exam lifecycle; grading.
- **Trigger:** Any Exam/Question modification, publication/availability change, or grading action.
- **Expected Behavior:** Audit entries are written with correct actor, role, and workspace context.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §10.1 (event 8); `15_Exam_Engine.md` §28; `02_Software_Requirements.md` Part 2 §6. See also §25.

### EXM-08 — Unconfirmed Exam Behaviors Must Not Be Invented

- **Rule ID:** EXM-08
- **Rule Name:** No Assumed Scheduling, Timing, Randomization, Passing, or Publication Rules
- **Description:** Exam scheduling (dates/windows/recurrence), timing (duration/countdown/timeouts), randomization (question or option), passing rules (marks/thresholds/grade bands), result-calculation formulas, and result-publication workflows are **not confirmed** for Version 1. The only confirmed availability rule is that a Teacher may publish or make Exams available within the Teacher Workspace, and the only confirmed result-visibility rule is availability to authorized contexts.
- **Applies To:** Exam Engine design, UI, and documentation.
- **Trigger:** Any requirement touching these areas.
- **Expected Behavior:** The work stops at the confirmed boundary; available/unavailable/pending states are used without inventing rules.
- **Exceptions:** None — these remain future-approval items (`15_Exam_Engine.md` §29).
- **Related Documents:** `15_Exam_Engine.md` §8, §17, §19–§22, §29; `02_Software_Requirements.md` Part 1 §11.

**Cross-referenced rules:** BR-003 (§5 — Question Bank/Exam isolation), BR-005/BR-014 (§24), BR-007 (§10 — transfers preserve attempts and grades), BR-013 (§5 — Teacher Staff Exam permissions), BSH-01…BSH-05 (§14 — Bubble Sheet), PAR-01/STU-05 (§7/§6).

---

# 14. Bubble Sheet Rules

**Authoritative sources:** `00_Project_Context.md` §9.6 (BR-011), §19 (Bubble Sheet), `15_Exam_Engine.md` §6, §13, `07_Data_Dictionary.md` §24, `09_Permission_Matrix.md` §8; decision D-010 (`29_Project_Decisions.md`).

### BSH-01 — Bubble Sheet Definition

- **Rule ID:** BSH-01
- **Rule Name:** Electronic Bubble Sheet Exam Format
- **Description:** A **Bubble Sheet** is an electronic exam format that **simulates traditional paper bubble sheets**. Students answer by selecting bubbles on screen during an authorized Exam attempt. It is one of the four confirmed Question Types (BR-011, §13).
- **Applies To:** Question Bank; Exams; Student attempts.
- **Trigger:** Bubble Sheet authoring or answering.
- **Expected Behavior:** Bubble Sheet content is authored only in the owning Teacher Workspace; Students interact through on-screen electronic selection only.
- **Exceptions:** The applicable Bubble Sheet structure details (options, layout) are not further specified and must not be invented.
- **Related Documents:** `00_Project_Context.md` §9.6, §19; `15_Exam_Engine.md` §13; `29_Project_Decisions.md` D-010.

### BSH-02 — Bubble Sheet Automatic Grading

- **Rule ID:** BSH-02
- **Rule Name:** Automatic Grading for Bubble Sheet
- **Description:** Automatic grading is supported for Bubble Sheet where applicable, applied after electronic on-screen selection; grading does not require Teacher intervention for the Bubble Sheet part. Results remain associated with the correct Student, Exam, and Teacher Workspace and become visible only to authorized contexts after grading completes.
- **Applies To:** Exam Engine grading; grading background jobs.
- **Trigger:** Submission of an attempt containing Bubble Sheet questions.
- **Expected Behavior:** Selections are validated against the applicable Bubble Sheet structure, then automatically graded and stored in the correct scope.
- **Exceptions:** The Bubble Sheet scoring formula is not confirmed (`15_Exam_Engine.md` §13); see EXM-03 (§13).
- **Related Documents:** `15_Exam_Engine.md` §11, §13; `21_Background_Jobs.md` §8.1–§8.2; `07_Data_Dictionary.md` §24.

### BSH-03 — Bubble Sheet Ownership and Scope

- **Rule ID:** BSH-03
- **Rule Name:** Bubble Sheet Stays Inside the Owning Workspace
- **Description:** Bubble Sheet Questions, answers, attempts, and grades are Teacher-owned and remain Teacher Workspace scoped. A Student cannot access a Bubble Sheet Question outside an assigned or available Exam; a Parent can view only permitted read-only linked-Student result information and cannot select answers.
- **Applies To:** All Bubble Sheet artifacts; all roles.
- **Trigger:** Any Bubble Sheet content access.
- **Expected Behavior:** Scope and relationship checks apply exactly as for other Exam content (BR-012, §13).
- **Exceptions:** None confirmed.
- **Related Documents:** `15_Exam_Engine.md` §13; `09_Permission_Matrix.md` §8; BR-011, BR-012 (§13).

### BSH-04 — Invalid Bubble Sheet Input Is Rejected

- **Rule ID:** BSH-04
- **Rule Name:** Selection Validation
- **Description:** The Platform validates that Bubble Sheet selections are valid for the applicable Bubble Sheet structure; invalid selections or submissions are rejected.
- **Applies To:** Student answer submission.
- **Trigger:** Bubble Sheet answer input or submission.
- **Expected Behavior:** Invalid input is rejected per approved detailed requirements; no malformed selection is recorded.
- **Exceptions:** None confirmed.
- **Related Documents:** `15_Exam_Engine.md` §13, §26; `05_User_Flows.md` flow 17.

### BSH-05 — No Paper Workflows in Version 1

- **Rule ID:** BSH-05
- **Rule Name:** Electronic-Only Bubble Sheet
- **Description:** Version 1 does **not** define paper-sheet scanning, optical mark recognition (OMR), camera capture of answer sheets, printing workflows, answer-sheet templates, or a Bubble Sheet scoring formula. Bubble Sheet is electronic on-screen behavior, not a paper scan workflow.
- **Applies To:** Bubble Sheet design and implementation.
- **Trigger:** Any requirement resembling paper-based processing.
- **Expected Behavior:** Such requirements are out of scope and rejected.
- **Exceptions:** None confirmed; any optical/print workflow would need separate approval (`15_Exam_Engine.md` §29).
- **Related Documents:** `15_Exam_Engine.md` §13, §29; D-010.

**Cross-referenced rules:** BR-011/BR-012 (§13), EXM-03 (§13 — automatic grading boundary), BR-006 (§25 — audited), BR-007 (§10 — history preservation).

---

# 15. Lesson Rules

**Authoritative sources:** `00_Project_Context.md` §4, §9.7 (BR-018), §15.1 (Q-010), `05_User_Flows.md` flows 14–15, `20_File_Storage.md` §6, `09_Permission_Matrix.md` §7, `07_Data_Dictionary.md` §18–§19, `02_Software_Requirements.md` Part 3 §4.

### BR-018 — Lesson Videos Are Teacher-Owned and Private

- **Rule ID:** BR-018 (canonical, CONFIRMED)
- **Rule Name:** Private Teacher-Owned Lessons
- **Description:** Lesson videos are **Teacher-owned and private**. A Teacher may upload Lesson videos **exclusively for their own Students**; **no cross-Teacher access exists**. One Teacher's Lesson videos must never become accessible to another Teacher's Students.
- **Applies To:** Lessons, Lesson Videos, and their files; all roles.
- **Trigger:** Any Lesson upload, management, or access.
- **Expected Behavior:** Lesson files are associated with the owning Teacher Workspace; access is granted only to the Teacher's own Students and authorized Teacher-side users.
- **Exceptions:** Lesson video hosting/protection mechanics are PENDING (Q-010) — see LSN-02.
- **Related Documents:** `00_Project_Context.md` §4, §9.7 (BR-018); `20_File_Storage.md` §6; `07_Data_Dictionary.md` §19; `05_User_Flows.md` flow 14.

### LSN-01 — No Marketplace Discovery or Public Catalog

- **Rule ID:** LSN-01
- **Rule Name:** Lesson Anti-Discovery Rule
- **Description:** There is no public catalog, marketplace discovery, cross-Teacher browsing, or public Teacher content surface for Lessons. Any attempt to publish a Lesson for marketplace discovery or cross-Teacher browsing is rejected.
- **Applies To:** Lesson publication and visibility; search.
- **Trigger:** Any Lesson publication, discovery, or browsing attempt.
- **Expected Behavior:** Publication beyond the owning workspace's authorized Students is denied; Lesson search returns only own-workspace Lessons (Teacher) or own-Teachers' Lessons (Student).
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §4.1; `05_User_Flows.md` flows 14–15; `22_Search_Filtering.md` §5.4, §10.3; D-050.

### LSN-02 — Backend-Authorized Access Only; Hosting/Protection PENDING

- **Rule ID:** LSN-02
- **Rule Name:** Lesson Access Enforcement and Pending Protection Decisions
- **Description:** Lesson file access requires backend authorization; a direct file path, identifier, or storage reference never grants access. Lesson video **hosting and protection details are PENDING (Q-010)**: streaming, download, public URLs, signed URLs, video formats, transcoding, quotas, previews, and watermarking must not be assumed or hardened.
- **Applies To:** Lesson playback/access architecture; file security.
- **Trigger:** Any Lesson file access request; any design for video hosting/protection.
- **Expected Behavior:** The backend validates identity, relationship, availability, and ownership before providing access; no unconfirmed protection or delivery mechanism is presented to users.
- **Exceptions:** Documented proposed default (Q-010): private storage, signed short-lived playback URLs, streaming-only, per-Teacher quota — **PROPOSED**, not confirmed.
- **Related Documents:** `00_Project_Context.md` §15.1 (Q-010), §12.7 (PROPOSED); `20_File_Storage.md` §6, §13; `05_User_Flows.md` flow 15; `03_System_Architecture.md` §12.2.

### LSN-03 — Archived Lessons

- **Rule ID:** LSN-03
- **Rule Name:** Archive Behavior for Lessons
- **Description:** Archive replaces permanent deletion for Lessons. Archived Lessons are not active Lesson content, remain retained historically with their file references, and stay private to the owning Teacher Workspace. Per the PROPOSED architecture principle, archived lessons stop playing but are retained.
- **Applies To:** Lesson lifecycle; reports/history; file retention.
- **Trigger:** Lesson Archive/restore; historical viewing.
- **Expected Behavior:** Active Lesson lists show active Lessons only; historical contexts may reference archived Lessons as clearly-indicated history.
- **Exceptions:** The "stop playing but retained" behavior is PROPOSED (`00_Project_Context.md` §12.7); the confirmed rule is Archive-not-delete with historical retention.
- **Related Documents:** `00_Project_Context.md` §11, §12.7; `20_File_Storage.md` §6, §17; `09_Permission_Matrix.md` §7; BR-005, BR-014 (§24).

### LSN-04 — Parent Panel Does Not Include Lessons

- **Rule ID:** LSN-04
- **Rule Name:** No Parent Lesson Surface in Version 1
- **Description:** The confirmed Parent Panel navigation does not include Lessons; Parents have no Lesson viewing permission in Version 1.
- **Applies To:** Parent Panel; Lesson visibility.
- **Trigger:** Any Parent Lesson request.
- **Expected Behavior:** The request is denied.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §7.3 (Parent Panel navigation); `09_Permission_Matrix.md` §7 (`parent_linked_student.lesson.view` — Denied).

**Cross-referenced rules:** BR-003 (§5 — Lesson isolation), STU-06 (§6 — Student Lesson boundary), BR-017 (§3 — web delivery), BR-021's sibling boundary (§12 — video is a Lesson, not Homework), FIL rules (§21).

---

# 16. Subscription Rules

**Section scope:** Flow A (Platform Subscription) only — the rules below define Flow A Subscriptions and pricing ownership, never Flow B Student/Parent fees.

**Authoritative sources:** `00_Project_Context.md` §5.1, §9.4 (BR-008, BR-015), §15.1 (Q-005, Q-013), `17_Subscription_Billing.md` (whole document), `21_Background_Jobs.md` §6, `02_Software_Requirements.md` Part 5 §3, `05_User_Flows.md` flows 26–27; decisions D-006, D-007, D-032 (`29_Project_Decisions.md`).

### BR-008 — Billable Student: Enrollment Duration Only, More Than 15 Days

- **Rule ID:** BR-008 (canonical, CONFIRMED)
- **Description:** A Student becomes **Billable** based on **Enrollment duration only**. If a Student remains enrolled in a Teacher's Group for **more than 15 calendar days** during the Billing Cycle, the Student is Billable. Students enrolled for **15 days or less are NOT counted**. **Attendance is NOT used. Login activity is NOT used.** Formula: `Monthly Subscription = Billable Students × Price Per Student`.
- **Rule Name:** Billable Student Rule
- **Applies To:** Flow A Subscription calculation; Billing processing; Subscription reports.
- **Trigger:** Billable Student evaluation for a Teacher's Billing Cycle.
- **Expected Behavior:** The calculation counts only Students whose Enrollment duration within the calendar-month Billing Cycle exceeds 15 calendar days, multiplied by the applicable Price Per Student.
- **Exceptions:** A Student enrolled for exactly 15 calendar days is **not** Billable. No engagement metric ever substitutes for Enrollment duration.
- **Related Documents:** `00_Project_Context.md` §5.1, §9.4 (BR-008); `29_Project_Decisions.md` D-007; `17_Subscription_Billing.md` §4, §6; `21_Background_Jobs.md` §6.2.

### SUB-01 — Subscription Model Terms

- **Rule ID:** SUB-01
- **Rule Name:** Teacher-to-Platform Monthly Subscription
- **Description:** The Subscription payer is the Teacher; the payee is the Platform; frequency is monthly; the pricing basis is Billable Students × Price Per Student; pricing is owned by the Super Admin; payment handling is outside the Platform with status-only recording. A **Teacher Subscription is not a Student Subscription**: in Student and Parent contexts, per-Teacher Flow B information is described as **payment status**, never as Subscription.
- **Applies To:** Flow A model; billing vocabulary across all surfaces.
- **Trigger:** Any Subscription modeling, labeling, or reporting.
- **Expected Behavior:** Flow A is always identified as the Platform Subscription; vocabulary never conflates it with Flow B.
- **Exceptions:** None confirmed.
- **Related Documents:** `17_Subscription_Billing.md` §3; `00_Project_Context.md` §5.1–§5.2, §19 (Subscription); D-036.

### SUB-02 — Per-Teacher Independent Evaluation

- **Rule ID:** SUB-02
- **Rule Name:** Separate Billable Evaluation per Teacher Workspace
- **Description:** Every Billable Student is evaluated for the correct Teacher relationship. A Student studying with multiple Teachers is evaluated **separately for each Teacher Workspace** — Billable for one Teacher does not imply Billable for another. Subscription processing never exposes one Teacher's Enrollment data to another Teacher's Subscription record.
- **Applies To:** Billing calculation; Subscription processing jobs.
- **Trigger:** Any Billable evaluation involving multi-Teacher Students.
- **Expected Behavior:** Each Teacher Workspace is processed independently with its own Enrollment durations.
- **Exceptions:** None confirmed.
- **Related Documents:** `17_Subscription_Billing.md` §6, §19 (edge case 4); `21_Background_Jobs.md` §6.2, §6.5.

### SUB-03 — Excluded Calculation Inputs

- **Rule ID:** SUB-03
- **Rule Name:** What Never Makes a Student Billable
- **Description:** The following must never be used to calculate Billable Students: Attendance; login activity; account existence or activation state; Homework, Exam, Lesson, or any other engagement activity; Flow B Student fee payment status. The term "Active Student" must **not** be used as an ambiguous substitute for Billable Student in Subscription calculations, reports, labels, or audit context.
- **Applies To:** Billing calculations; Subscription reporting; dashboards.
- **Trigger:** Any calculation, metric, label, or report near billing.
- **Expected Behavior:** Only Enrollment duration determines Billable status; attempts to use excluded inputs are rejected.
- **Exceptions:** "Active Student" exists only as a non-billing product metric (`01_Project_Vision.md` §11); a formal definition beyond that requires separate approval (`17_Subscription_Billing.md` §4).
- **Related Documents:** `17_Subscription_Billing.md` §4, §6–§7; `02_Software_Requirements.md` Part 5 §3; `18_Reporting_Analytics.md` §8, §12.

### SUB-04 — Subscription Lifecycle Events Are Audited

- **Rule ID:** SUB-04
- **Rule Name:** Subscription Audit Coverage
- **Description:** Subscription lifecycle events — invoice issued/snapshot generation, marked paid (status recording), status changes, and Platform pricing changes — are mandatory Audit Log events (Audit Log Policy, event 10). (Grace, read-only enforcement, and reactivation events would be audited **only if** the PENDING enforcement behavior is ever confirmed.)
- **Applies To:** Flow A records; pricing changes; status recording.
- **Trigger:** Any Subscription or pricing change.
- **Expected Behavior:** The change is recorded with the Super Admin actor and Platform context, in the immutable Audit Log.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §10.1 (event 10); `17_Subscription_Billing.md` §17; `21_Background_Jobs.md` §6. See also §25.

### SUB-05 — Non-Payment Enforcement Is PENDING

- **Rule ID:** SUB-05
- **Rule Name:** No Confirmed Grace, Suspension, or Reactivation (Q-005)
- **Description:** Non-payment enforcement is **PENDING** (Q-005). Version 1 does not define or apply: grace-period duration or events; Teacher Workspace read-only/inaccessible states; Student/Parent access changes; suspension; reactivation; or any automatic enforcement process. **No data may be hard-deleted or automatically archived to enforce non-payment.**
- **Applies To:** Subscription status handling; access control; billing automation.
- **Trigger:** A Teacher Subscription becoming unpaid; any enforcement request.
- **Expected Behavior:** The Platform records status only; no enforcement behavior is applied until Q-005 is formally resolved.
- **Exceptions:** Documented proposed default (Q-005): 7-day grace → Teacher Workspace read-only; Students keep read access; nothing auto-archives — **PROPOSED**, not confirmed.
- **Related Documents:** `00_Project_Context.md` §15.1 (Q-005); `17_Subscription_Billing.md` §11–§13, §16, §19–§20; `09_Permission_Matrix.md` §11 (`…enforce_non_payment` — Conditional/PENDING); `05_User_Flows.md` flow 27.

**Cross-referenced rules:** BR-015 (§8 — pricing ownership, Q-013), BR-019 (§18 — status-only payments), D-006 (§17 — Billing Cycle), SUB/TCH-03 (§5 — Teacher own-status visibility), BIL rules (§17).

---

# 17. Billing Rules

**Section scope:** Flow A Billing processing (Billing Cycles, calculation, snapshots, history); Flow B is covered in §18 as status-only and must not be conflated with this section.

**Authoritative sources:** `00_Project_Context.md` §5.1, §12.3, `17_Subscription_Billing.md` §5, §14, `21_Background_Jobs.md` §3, §5–§6, `05_User_Flows.md` flow 27, `18_Reporting_Analytics.md` §12; decisions D-003, D-006 (`29_Project_Decisions.md`).

### BIL-01 — Calendar-Month Billing Cycle

- **Rule ID:** BIL-01
- **Rule Name:** Billing Cycle Definition (D-006)
- **Description:** The Billing Cycle is a calendar month: it **starts on the first day of every calendar month** and **ends on the last day of the same month**; a new Billing Cycle **begins automatically** on the first day of the next month. The cycle end is **not** the first day of the next month — that is the next cycle's beginning.
- **Applies To:** All Flow A billing processing and reporting.
- **Trigger:** The first day of a calendar month; any cycle validation.
- **Expected Behavior:** Cycles are created and validated against calendar-month boundaries; a Billing Cycle outside these boundaries is invalid.
- **Exceptions:** Scheduler timing, timezone, and partial-period policy beyond the Billable Student rule are not confirmed (`17_Subscription_Billing.md` §5); timezone is PENDING (Q-015).
- **Related Documents:** `00_Project_Context.md` §5.1; `29_Project_Decisions.md` D-006; `17_Subscription_Billing.md` §5; `21_Background_Jobs.md` §6.1; `05_User_Flows.md` flow 27.

### BIL-02 — Scheduled Billing Processing

- **Rule ID:** BIL-02
- **Rule Name:** Billing Cycle Initialization, Calculation, and Snapshot Schedule
- **Description:** Billing runs as scheduled background work on the Database Queue with the Laravel Scheduler (Cron Jobs): Billing Cycle initialization on the **first day** of each month; Billable Student calculation after initialization and periodically during the cycle; Subscription snapshot generation on the **last day** of each month.
- **Applies To:** Background jobs; Subscription processing.
- **Trigger:** Scheduler execution per the confirmed schedule.
- **Expected Behavior:** The scheduled tasks create the cycle, maintain current Billable counts and amounts, and close the cycle with a snapshot; historical cycles are preserved.
- **Exceptions:** Exact scheduler timing is not confirmed (`17_Subscription_Billing.md` §5); notification sending is never part of billing (§20).
- **Related Documents:** `21_Background_Jobs.md` §5.1, §6.1–§6.4; `00_Project_Context.md` §5.1; D-006.

### BIL-03 — Immutable Monthly Snapshots (PROPOSED Mechanics)

- **Rule ID:** BIL-03
- **Rule Name:** Subscription Snapshot Immutability
- **Description:** Monthly usage is materialized into Subscription snapshots that **never mutate once generated**; corrections are adjustment records, not mutations. (Mechanics are PROPOSED per D-003 and the Project Context; the confirmed outcomes are historical preservation and price-as-of-period retention.)
- **Applies To:** Subscription records; Billing close processing.
- **Trigger:** Snapshot generation at cycle end; any billing correction.
- **Expected Behavior:** A snapshot records Teacher reference, Billing Cycle period, Billable Student count, Price Per Student, and calculated amount; later corrections add adjustment records without altering history.
- **Exceptions:** Snapshot mechanics are **PROPOSED** (D-003); correction/adjustment/invoice workflows are not confirmed (`17_Subscription_Billing.md` §20).
- **Related Documents:** `00_Project_Context.md` §12.3; `29_Project_Decisions.md` D-003; `21_Background_Jobs.md` §6.4; BR-015 (§8 — historical price retention).

### BIL-04 — Idempotent Billing Processing

- **Rule ID:** BIL-04
- **Rule Name:** No Duplicate Cycles or Accumulating Amounts
- **Description:** Billing jobs are idempotent: re-execution must not create duplicate Billing Cycles, duplicate snapshots, duplicate Audit Log entries, or accumulating Subscription amounts. Re-calculation **overwrites** with the correct current result. If recording the Audit Log entry fails, the business action is not considered complete.
- **Applies To:** All Subscription/Billing background jobs.
- **Trigger:** Any retry or re-run of billing work (including after failures or Scheduler overlaps).
- **Expected Behavior:** Re-runs converge to the same correct state; failures roll back or safely retry.
- **Exceptions:** None confirmed.
- **Related Documents:** `21_Background_Jobs.md` §3 (principles 3, 7), §6.1–§6.4, §20 (edge cases 7, 13, 14).

### BIL-05 — Historical Billing Records Are Preserved

- **Rule ID:** BIL-05
- **Rule Name:** Billing History Retention
- **Description:** Historical Billing Cycle and Subscription records remain available and are never permanently deleted; historical invoices keep the price applicable to their period; Flow A billing reports remain separate from Flow B payment-status reports.
- **Applies To:** Billing records; Subscription reports; Platform retention.
- **Trigger:** Cycle close; historical reporting; any deletion attempt.
- **Expected Behavior:** Prior periods remain queryable with their original context; deletion attempts are rejected.
- **Exceptions:** None confirmed.
- **Related Documents:** `17_Subscription_Billing.md` §5, §14; `18_Reporting_Analytics.md` §12; BR-014 (§24), BR-015 (§8).

### BIL-06 — Zero and Boundary Billing Cases

- **Rule ID:** BIL-06
- **Rule Name:** Confirmed Billing Edge Behavior
- **Description:** A Teacher with no Billable Students in a Billing Cycle has a Subscription basis derived from **zero** Billable Students. A Student who moves between Groups under the same Teacher during a Billing Cycle is evaluated on the **total Enrollment duration across the moves within the same Teacher Workspace** (history preserved). A Teacher-created but not-yet-activated Student account is irrelevant to billing (activation is not a billing input).
- **Applies To:** Billing calculation edge cases.
- **Trigger:** Cycles with zero Billable Students; intra-cycle Group moves; unactivated accounts.
- **Expected Behavior:** The calculation follows exactly these confirmed outcomes and no others.
- **Exceptions:** None confirmed.
- **Related Documents:** `05_User_Flows.md` flow 26; `21_Background_Jobs.md` §20 (edge cases 2–5, 7); `17_Subscription_Billing.md` §7, §19.

### BIL-07 — No Billing Notifications

- **Rule ID:** BIL-07
- **Rule Name:** Silent Billing Processing
- **Description:** No push, email, or SMS notification is sent for Billing Cycle initialization, calculation, snapshot generation, or status recording (notifications are out of scope; §20). Cycle events are auditable; they are not notifiable.
- **Applies To:** All billing processing.
- **Trigger:** Any billing lifecycle event.
- **Expected Behavior:** Audit where required; no notification of any kind.
- **Exceptions:** None confirmed.
- **Related Documents:** `21_Background_Jobs.md` §6.1 (business rules), §12; `19_Notification_System.md` §9; D-012.

**Cross-referenced rules:** BR-008 and SUB rules (§16), BR-015 (§8 — pricing), BR-019 (§18), RET rules (§24), AUD rules (§25).

---

# 18. Payment Rules

**Section scope:** status-only payment recording for **both** flows — Flow A (Platform Subscription) and Flow B (Student/Parent fees). All payments are external.

**Authoritative sources:** `00_Project_Context.md` §5.2–§5.3, §9.4–§9.5 (BR-009, BR-019), `17_Subscription_Billing.md` §9–§10, `09_Permission_Matrix.md` §10, `18_Reporting_Analytics.md` §11, `07_Data_Dictionary.md` §25, §33, `05_User_Flows.md` flows 24, 26; decisions D-002, D-036 (`29_Project_Decisions.md`).

### BR-019 — Payments Outside the Platform; Status Only

- **Rule ID:** BR-019 (canonical, CONFIRMED)
- **Description:** In Version 1, **both** Teacher Subscription payments (Flow A) and Student fee payments (Flow B) are handled **outside the Platform**. The Platform **only records payment status** — it does not process transactions, collect money, store gateway payment details, or provide online payment gateways.
- **Rule Name:** External Payments; Status-Only Recording
- **Applies To:** All payment-related behavior; all roles.
- **Trigger:** Any payment or payment-status action; any gateway/processing attempt.
- **Expected Behavior:** Status is recorded by the authorized actor after the external event; every processing attempt is rejected; payment status is never presented as proof of a Platform-processed transaction.
- **Exceptions:** Future payment-gateway integration may be considered only as a separate decision (`27_Development_Roadmap.md` §20.2).
- **Related Documents:** `00_Project_Context.md` §5.3, §9.4 (BR-019); `29_Project_Decisions.md` D-002; `17_Subscription_Billing.md` §9; `02_Software_Requirements.md` Part 1 §11.

### PAY-01 — Two Money Flows, Never Conflated

- **Rule ID:** PAY-01
- **Rule Name:** Flow A / Flow B Separation (D-036)
- **Description:** **Flow A** is Teacher → Platform Subscription (SaaS revenue, Platform-managed). **Flow B** is Student/Parent → Teacher fees (Group pricing-derived, tracked on the Teacher's behalf). The two flows must never be conflated in data, logic, reporting, authorization, vocabulary, or UI labeling; Flow B is never treated as Platform revenue, and Flow B status is never labeled as a Subscription.
- **Applies To:** All financial records, views, reports, search, and documentation.
- **Trigger:** Any financial modeling, display, filtering, or reporting.
- **Expected Behavior:** Each flow uses its own records, labels, permission scopes, and report sections; mixing is rejected or corrected.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §5.2; `29_Project_Decisions.md` D-036; `18_Reporting_Analytics.md` §11; `22_Search_Filtering.md` §3.7.

### PAY-02 — Flow B Derivation and Tracking

- **Rule ID:** PAY-02
- **Rule Name:** Student Fee Status Derives from Group Pricing
- **Description:** Flow B fee obligations derive from the Student's Group Enrollment and the Group's **Price** and **Pricing Type** (`Monthly` or `Per Lesson`) — the Flow B rule BR-009 (§10). The Platform tracks this status on the Teacher's behalf; derivation never involves Flow A inputs.
- **Applies To:** Flow B status records; Teacher/Student/Parent payment views.
- **Trigger:** Enrollment in a priced Group; Flow B status derivation.
- **Expected Behavior:** Status reflects the Group pricing basis; it is partitioned per Teacher for Students and shown read-only to Parents of linked Students.
- **Exceptions:** Detailed obligation computation (due dates, proration, cycles for Flow B) is not further specified and must not be invented.
- **Related Documents:** `00_Project_Context.md` §9.5 (BR-009); `07_Data_Dictionary.md` §33; `02_Software_Requirements.md` Part 3 §6, Part 4 §7.

### PAY-03 — Who May Record and Modify Payment Status

- **Rule ID:** PAY-03
- **Rule Name:** Payment Status Write Authority
- **Description:** The **Super Admin** records and updates Flow A Subscription payment status (manually, after external payment). The **Teacher** (or authorized Teacher Staff) records and updates Flow B Student fee status within the Teacher's own Workspace. **Students and Parents cannot modify any payment status**, and no one may manage Flow A except the Super Admin.
- **Applies To:** Payment status write operations; both flows.
- **Trigger:** Any payment-status create/update attempt.
- **Expected Behavior:** Only the authorized actor for the correct flow and scope succeeds; all other attempts are denied; status changes are auditable.
- **Exceptions:** None confirmed.
- **Related Documents:** `09_Permission_Matrix.md` §10; `17_Subscription_Billing.md` §9–§10; `08_RBAC.md` §6, §9.

### PAY-04 — Who May View Payment Status

- **Rule ID:** PAY-04
- **Rule Name:** Payment Status Read Authority
- **Description:** Flow A status is visible to the Super Admin (Platform scope) and to the owning Teacher (own status only). Flow B status is visible to the owning Teacher/authorized Teacher Staff (own workspace), to the Student (own per-Teacher status), and to the Parent (linked Students, read-only). Payment-status views never enable payment actions.
- **Applies To:** All payment-status surfaces.
- **Trigger:** Any payment-status view request.
- **Expected Behavior:** Views resolve within the role's authorized scope only; cross-scope requests are denied.
- **Exceptions:** None confirmed.
- **Related Documents:** `09_Permission_Matrix.md` §10–§11; `05_User_Flows.md` flows 24, 26; TCH-03 (§5), STU-07 (§6), PAR-03 (§7).

### PAY-05 — Payment Processing Attempts Are Rejected

- **Rule ID:** PAY-05
- **Rule Name:** No Processing, No Gateways
- **Description:** Any attempt to process a payment online, configure a payment gateway, collect money, or initiate a transaction through the Platform is rejected — for all roles, both flows. Gateway configuration does not exist in Platform Settings.
- **Applies To:** All payment surfaces; Platform Settings; API-level behavior.
- **Trigger:** Any payment-processing or gateway-configuration attempt.
- **Expected Behavior:** The attempt is rejected as out of scope; error handling never implies processing capability.
- **Exceptions:** None confirmed.
- **Related Documents:** `03_System_Architecture.md` §17.3; `09_Permission_Matrix.md` §10 (`payment_status.process_online_payment`, `…configure_gateway` — Denied), §13, §17; `17_Subscription_Billing.md` §18.

### PAY-06 — Unconfirmed Payment Details

- **Rule ID:** PAY-06
- **Rule Name:** No Assumed Status Model or Financial Workflows
- **Description:** Permitted payment-status values, payment reference content, payment date rules, reconciliation workflows, refunds, adjustments, invoice issuance, and accounting integration are **not confirmed** and must not be invented.
- **Applies To:** Payment status modeling; billing/accounting design.
- **Trigger:** Any design needing these details.
- **Expected Behavior:** The work stops at the confirmed boundary (record status, keep flows separate, preserve history) until separate approval.
- **Exceptions:** None confirmed.
- **Related Documents:** `17_Subscription_Billing.md` §9–§10, §20; `18_Reporting_Analytics.md` §11.

### PAY-07 — Historical Payment Status Is Retained

- **Rule ID:** PAY-07
- **Rule Name:** Payment History Retention
- **Description:** Historical payment-status records (both flows) remain available and are never permanently deleted; historical Flow A invoices retain the price of their period (BR-015, §8); reports may show recorded history where authorized.
- **Applies To:** Payment-status records; reports; retention.
- **Trigger:** Historical queries; any deletion attempt.
- **Expected Behavior:** History remains intact, clearly contextualized; deletion is rejected in favor of Archive (§24).
- **Exceptions:** None confirmed.
- **Related Documents:** `18_Reporting_Analytics.md` §11; `09_Permission_Matrix.md` §10 (`…view_history`); BR-014 (§24).

**Cross-referenced rules:** BR-008 (§16 — Flow A inputs), BR-009 (§10 — Flow B basis), BR-015 (§8 — pricing), SUB/BIL rules (§16–§17), D-012 (§20 — no payment notifications).

---

# 19. Reporting Rules

**Authoritative sources:** `00_Project_Context.md` §7.1, §11 (property 3), `18_Reporting_Analytics.md` (whole document), `02_Software_Requirements.md` Part 2 §7, Part 5 §5, `09_Permission_Matrix.md` §9, §16, `22_Search_Filtering.md` §6–§8.

### RPT-01 — Confirmed Report Domains

- **Rule ID:** RPT-01
- **Rule Name:** What Reports Exist
- **Description:** Teacher Workspace reports cover exactly: **Attendance, Homework, Exam results, payments (Flow B status), and Student performance**. Super Admin reports cover Platform administration: Teacher management, Flow A Subscriptions, pricing, and payment-status information. Student and Parent surfaces provide scoped summaries, not Teacher Workspace reports.
- **Applies To:** Reporting subsystem; all roles.
- **Trigger:** Any report definition or request.
- **Expected Behavior:** Reports are produced only within the confirmed domains and scopes; new metrics, trends, forecasts, or analytics are not invented.
- **Exceptions:** Exact report layouts, aggregate metrics, and export formats are not confirmed.
- **Related Documents:** `00_Project_Context.md` §7.1; `18_Reporting_Analytics.md` §4–§7; `02_Software_Requirements.md` Part 2 §7.

### RPT-02 — Scope-Preserving Reporting

- **Rule ID:** RPT-02
- **Rule Name:** Every Report Resolves Role and Scope First
- **Description:** Authorization, role, relationship, and scope resolve **before** any report retrieval, filtering, sorting, aggregation, or presentation. Teacher/Teacher Staff reports include only the creating Teacher Workspace; Student reports only the Student's own per-Teacher records; Parent reports only the selected linked Student's records, read-only; Super Admin reports Platform scope within confirmed visibility boundaries.
- **Applies To:** All reporting paths.
- **Trigger:** Any report request.
- **Expected Behavior:** Reports never cross Teacher Workspace, Student-self, or Parent-link boundaries — including through filters, counts, empty states, or errors.
- **Exceptions:** None confirmed.
- **Related Documents:** `18_Reporting_Analytics.md` §3–§7, §20; `22_Search_Filtering.md` §3; BR-003 (§5).

### RPT-03 — Archived and Historical Records in Reports

- **Rule ID:** RPT-03
- **Rule Name:** Historical Inclusion with Clear Indication
- **Description:** Historical data is never deleted; reports and history queries **include archived records** where historical/reporting rules require them, **clearly indicated** as archived, and never presented as active records. A report with no records for valid criteria returns an empty state, not an error.
- **Applies To:** All reports and history views.
- **Trigger:** Historical reporting; archived record inclusion; valid-but-empty report results.
- **Expected Behavior:** Archived status is unmistakable in output; empty results stay empty and safe.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §11 (property 3), BR-014; `18_Reporting_Analytics.md` §3, §21–§22; `22_Search_Filtering.md` §13.3.

### RPT-04 — Financial Separation and Status-Only Payments in Reports

- **Rule ID:** RPT-04
- **Rule Name:** No Flow Mixing; No Payment Processing from Reports
- **Description:** Reports clearly identify whether content is **Flow A Subscription payment status** (Super Admin, Platform scope) or **Flow B Student fee payment status** (Teacher/workspace, Student, Parent scope). Reports never present Flow B as Platform revenue, never label Flow B as a Teacher Subscription, and never initiate, collect, or process a transaction. Attendance reports must never calculate or display Billable Student eligibility.
- **Applies To:** Payment, Subscription, and Attendance reports.
- **Trigger:** Any financial or Attendance report.
- **Expected Behavior:** Separate sections, labels, filters, and summaries per flow; status-only display; Billable inputs come only from Enrollment duration (BR-008, §16).
- **Exceptions:** None confirmed.
- **Related Documents:** `18_Reporting_Analytics.md` §8, §11–§12; D-036; BR-019 (§18).

### RPT-05 — Super Admin Global Reports Boundary

- **Rule ID:** RPT-05
- **Rule Name:** Non-Invasive Platform Reporting
- **Description:** Super Admin global reports operate at Platform level within confirmed boundaries and the PENDING content-visibility question (Q-012). "Platform-wide" never means unrestricted browsing of Teacher-private content (Lessons, Question Banks, Homework content, individual Exam definitions, workspace-private Student records).
- **Applies To:** Platform reports; Super Admin dashboards.
- **Trigger:** Any Super Admin report request.
- **Expected Behavior:** Output is denied or restricted to confirmed Platform-level information where the PENDING boundary would be exceeded.
- **Exceptions:** Proposed default D-005 (aggregates/finances/metadata only) — PROPOSED, not confirmed.
- **Related Documents:** `18_Reporting_Analytics.md` §7; `09_Permission_Matrix.md` §9, §16; ADM-03 (§8).

### RPT-06 — No Export in Version 1

- **Rule ID:** RPT-06
- **Rule Name:** Export Not Confirmed
- **Description:** No report export format, download, print, scheduled delivery, email delivery, or external reporting integration is confirmed for Version 1. CSV, spreadsheet, PDF, print, email, or automated export behavior must not be assumed.
- **Applies To:** Reporting; search filtering.
- **Trigger:** Any export-related requirement.
- **Expected Behavior:** The capability is out of scope until separately approved; any future export must reapply the same authorization, scope, Archive, and flow-separation checks.
- **Exceptions:** None confirmed.
- **Related Documents:** `18_Reporting_Analytics.md` §15; `22_Search_Filtering.md` §14.

### RPT-07 — Accurate Result Availability

- **Rule ID:** RPT-07
- **Rule Name:** No Fabricated Results in Reports
- **Description:** Where a result is unavailable (for example, pending Essay grading), reports show an accurate unavailable/pending state — never an invented grade, and incomplete payment status is shown as recorded status only because payments occur outside the Platform.
- **Applies To:** Exam and payment reports.
- **Trigger:** Reporting on incomplete or unavailable data.
- **Expected Behavior:** Honest pending/unavailable states; no inferred values.
- **Exceptions:** None confirmed.
- **Related Documents:** `18_Reporting_Analytics.md` §10–§11, §22; EXM-04 (§13).

**Cross-referenced rules:** BR-003 (§5), BR-005/BR-014 (§24 — archived indication and retention), AUD-06 (§25 — Audit reporting visibility), SRC rules (§22 — filtering/sorting/pagination standards used by reports).

---

# 20. Notification Rules

**Authoritative sources:** `00_Project_Context.md` §4.2, `19_Notification_System.md` (whole document), `21_Background_Jobs.md` §12, `09_Permission_Matrix.md` §15; decision D-012 (`29_Project_Decisions.md`).

### NTF-01 — Notifications Are Out of Scope for Version 1

- **Rule ID:** NTF-01
- **Rule Name:** Complete Notification Exclusion (D-012)
- **Description:** Push notifications, email notifications, and SMS notifications are **out of scope for Version 1**, for every role and every product event. There is no active Notification System, no Notification entity, and no notification delivery of any kind.
- **Applies To:** All modules; all roles; all events.
- **Trigger:** Any requirement, design, or request involving notifications.
- **Expected Behavior:** Notification behavior is rejected as out of scope; no notification types, channels, triggers, or preferences are defined.
- **Exceptions:** None in Version 1; future support requires separate approval (NTF-05).
- **Related Documents:** `00_Project_Context.md` §4.2; `29_Project_Decisions.md` D-012; `19_Notification_System.md` §1–§4.

### NTF-02 — No Notification Entities or Surfaces

- **Rule ID:** NTF-02
- **Rule Name:** No Notification Center, Preferences, or History
- **Description:** Version 1 has no notification API endpoints, permissions, settings, notification center, badges, read/unread state, delivery history, queued or scheduled sending, or notification preferences — for any role. Notification permissions are denied for all roles.
- **Applies To:** Product surfaces; RBAC; data entities; background processing.
- **Trigger:** Any attempt to add such a surface, entity, or permission.
- **Expected Behavior:** The attempt is rejected ("Notification is not a Version 1 product entity").
- **Exceptions:** None confirmed.
- **Related Documents:** `19_Notification_System.md` Document Scope, §11, §13–§14, §17; `09_Permission_Matrix.md` §15.

### NTF-03 — SMTP Baseline Is Not Email Approval

- **Rule ID:** NTF-03
- **Rule Name:** Mail Transport ≠ Notifications
- **Description:** SMTP exists in the technical baseline as mail-transport availability only. It does not authorize or create Version 1 email notifications, templates, preferences, or history, and must not be used to send Version 1 emails.
- **Applies To:** Technical configuration; any email-sending consideration.
- **Trigger:** Work touching the mail transport.
- **Expected Behavior:** No Version 1 email notification behavior is built on the SMTP baseline.
- **Exceptions:** None confirmed.
- **Related Documents:** `19_Notification_System.md` §4, §20; `03_System_Architecture.md` §4.1, §7.1.

### NTF-04 — In-Context Feedback Is Not a Notification

- **Rule ID:** NTF-04
- **Rule Name:** UI Feedback Distinction
- **Description:** Local, in-context feedback while a user actively uses the Web Application (validation, loading, error, success, confirmation, pending, unavailable, empty states) is **not** a notification. It must not be stored, delivered later, given a notification center, or treated as push/email/SMS behavior. Semantic states (success/warning/error) are not notification priorities.
- **Applies To:** UI behavior; UX wording.
- **Trigger:** Any presentation of action feedback.
- **Expected Behavior:** Feedback stays in-context and transient; dismissing it creates no record; unread counts and badges do not exist.
- **Exceptions:** None confirmed.
- **Related Documents:** `19_Notification_System.md` §1, §11, §13; `12_Frontend_Architecture.md` (in-context messaging boundary); `13_UI_UX_Guidelines.md`.

### NTF-05 — No Notification Triggers, Jobs, or Queue Usage

- **Rule ID:** NTF-05
- **Rule Name:** Business Events Never Trigger Notifications
- **Description:** No product event (login, Attendance, Homework, Lessons, Exams, grades, payment-status recording, Subscription changes, Archive/restore, Billing Cycles, reports, errors) triggers push, email, SMS, browser, or in-application notification delivery. The Database Queue and Laravel Scheduler must not be used to send Version 1 notifications; no notification background jobs exist.
- **Applies To:** Event handling; background jobs; scheduling.
- **Trigger:** Any product event; any job/queue design.
- **Expected Behavior:** Events produce required Audit Log entries where mandated (§25) — and nothing else. Audit Log history is never presented as notification history.
- **Exceptions:** None confirmed.
- **Related Documents:** `19_Notification_System.md` §9–§10, §12, §14–§15; `21_Background_Jobs.md` §12.

### NTF-06 — Future Notifications Require Separate Approval

- **Rule ID:** NTF-06
- **Rule Name:** Future-Only Notification Consideration
- **Description:** Push, email, and SMS support may be considered **only** in a separately approved future scope, requiring Product Owner decisions on types, triggers, recipients, consent/preferences, privacy, security, delivery, failure/retry, retention, audit behavior, and localization. Future consideration must not retroactively make any current Version 1 event a notification trigger.
- **Applies To:** Future planning; roadmap.
- **Trigger:** Any future-notification proposal.
- **Expected Behavior:** The proposal is documented as future scope and decided separately; Version 1 behavior is unchanged.
- **Exceptions:** None confirmed.
- **Related Documents:** `19_Notification_System.md` §19–§21; `27_Development_Roadmap.md` §20.2; `31_Master_Index.md` §8.2.

**Cross-referenced rules:** BR-017 (§3 — web only), BIL-07 (§17 — silent billing), AUD rules (§25 — auditing instead of notifying).

---

# 21. File Management Rules

**Authoritative sources:** `00_Project_Context.md` §11, §12.7, `20_File_Storage.md` (whole document), `09_Permission_Matrix.md` §14, `07_Data_Dictionary.md` §28, `23_Security_Standards.md` §9; decision D-043 (`29_Project_Decisions.md`).

### FIL-01 — Laravel Public Storage with Backend-Enforced Privacy

- **Rule ID:** FIL-01
- **Rule Name:** Storage Baseline and Access Authority
- **Description:** Version 1 stores file binaries using **Laravel Public Storage** (compatible with cPanel Shared Hosting); logical ownership/reference information is kept separately from file bytes. Because Lessons and Teacher-owned files are private by business rule, **application-level authorization and ownership checks are mandatory** for every file access: a file reference, storage path, or guessed location never grants access. S3 Storage is not required for Version 1.
- **Applies To:** All file storage and access.
- **Trigger:** Any file upload, access, or reference resolution.
- **Expected Behavior:** The backend validates user, role, workspace, ownership/relationship, and Archive state before any file operation; direct-path access never bypasses it.
- **Exceptions:** None confirmed.
- **Related Documents:** `20_File_Storage.md` Document Scope, §1, §13; `29_Project_Decisions.md` D-043; `03_System_Architecture.md` §12.

### FIL-02 — Confirmed File Contexts Only

- **Rule ID:** FIL-02
- **Rule Name:** Supported File Types per Context
- **Description:** Confirmed file contexts are exactly: **Homework** (Text, Image, PDF — Text may be logical content), **Student Homework submissions** (binary upload Image or PDF only, for assigned Homework through a valid Teacher relationship), and **Lesson videos** (Teacher-owned, private). No other file type or context is confirmed.
- **Applies To:** Upload validation; file association.
- **Trigger:** Any file upload or attachment.
- **Expected Behavior:** Files are accepted only within their confirmed context and type; other types/contexts are not introduced without approval.
- **Exceptions:** Exact image formats, PDF versions, video codecs, and MIME catalogs are not confirmed (`20_File_Storage.md` §3); user profile images are **not** a confirmed feature (`20_File_Storage.md` §8) and must not be introduced merely because Image storage exists.
- **Related Documents:** `20_File_Storage.md` §3–§5, §7–§8; `07_Data_Dictionary.md` §28; BR-021 (§12), BR-018 (§15).

### FIL-03 — Video Homework Is Rejected

- **Rule ID:** FIL-03
- **Rule Name:** No Video Homework Uploads
- **Description:** Video homework must not be accepted, attached, or represented as a supported Homework format — for Teachers and Students alike. Video exists on the Platform only as Teacher-owned **Lesson** content.
- **Applies To:** Homework attachments and submissions.
- **Trigger:** Any video upload in a Homework context.
- **Expected Behavior:** The upload is rejected as out of scope.
- **Exceptions:** None confirmed.
- **Related Documents:** `20_File_Storage.md` §3, §7, §11, §21; BR-021 (§12); D-011.

### FIL-04 — Parent Uploads Are Denied

- **Rule ID:** FIL-04
- **Rule Name:** No Parent File Uploads
- **Description:** Parents may not upload any files. Parent file/educational-data visibility through linked Students remains read-only where confirmed.
- **Applies To:** All Parent interactions with files.
- **Trigger:** Any Parent upload attempt.
- **Expected Behavior:** The attempt is denied.
- **Exceptions:** None confirmed.
- **Related Documents:** `20_File_Storage.md` §14; `09_Permission_Matrix.md` §14; PAR-01 (§7).

### FIL-05 — File Ownership and Workspace Isolation

- **Rule ID:** FIL-05
- **Rule Name:** Files Follow Their Owner's Isolation
- **Description:** Lesson file references belong to the owning Teacher Workspace; Homework file references belong to the relevant Teacher Workspace and Homework context; Student submission files stay associated with the Student, the assigned Homework, and the correct Teacher relationship. Files must never be visible across Teacher Workspaces or to unrelated Students/Parents.
- **Applies To:** All stored files and references.
- **Trigger:** Any file association, listing, or access.
- **Expected Behavior:** Ownership and relationship context is preserved and enforced at every operation.
- **Exceptions:** None confirmed; Super Admin file-content visibility is PENDING per ADM-03 (§8).
- **Related Documents:** `20_File_Storage.md` §13–§14; `07_Data_Dictionary.md` §28; BR-003 (§5); `23_Security_Standards.md` §9.3.

### FIL-06 — Upload Validation Chain

- **Rule ID:** FIL-06
- **Rule Name:** Mandatory Pre-Acceptance Validation
- **Description:** Before a file is accepted, the Platform validates: the uploader is authenticated and authorized for the owning resource and scope; the owning resource exists in the authorized context; Teacher Workspace ownership for Teacher-owned files; Student-assignment validity for submissions (Parent uploads denied); and file type matching the owning context. MIME type is verified against actual file content. Archived/inactive owning resources are not active upload targets unless restored/authorized.
- **Applies To:** Every file upload.
- **Trigger:** Any upload request.
- **Expected Behavior:** Any failed check rejects the upload without exposing private details.
- **Exceptions:** File-size limits, content inspection, virus scanning, checksums, and duplicate detection are **not confirmed** (`20_File_Storage.md` §11; `23_Security_Standards.md` §9.4) — no fabricated limits may be presented (FIL-08).
- **Related Documents:** `20_File_Storage.md` §11; `23_Security_Standards.md` §9.1–§9.2.

### FIL-07 — File Archive, Not Deletion

- **Rule ID:** FIL-07
- **Rule Name:** File Lifecycle Under the Archive Rule
- **Description:** No permanent deletion exists for files or file references. Authorized users may Archive and restore within scope and permission; Archive **does not detach** a file reference from historical Homework, Lesson, submission, or report context; archived files/references are never presented as active content; Archive/restore actions are audited where applicable.
- **Applies To:** File lifecycle; storage consistency.
- **Trigger:** File Archive/restore; historical file access; storage integrity checks.
- **Expected Behavior:** References persist for history and reports; active surfaces show active files only.
- **Exceptions:** Physical binary-deletion lifecycle and storage-retention periods are not confirmed (`20_File_Storage.md` §17).
- **Related Documents:** `20_File_Storage.md` §17; `00_Project_Context.md` §11; `06_Database_Design.md` §11; BR-005, BR-014 (§24).

### FIL-08 — Unconfirmed File Behaviors Must Not Be Invented

- **Rule ID:** FIL-08
- **Rule Name:** No Assumed Sizes, Naming, Download, Replacement, or Cleanup Policies
- **Description:** File-size limits, physical naming conventions/path derivation, download/streaming policies, file replacement/versioning policy, orphan-file cleanup/reconciliation, and backup topology for file binaries are **not confirmed**. The Platform must not present fabricated size limits; an automated cleanup that could endanger historical references must not be assumed; integrity jobs flag inconsistencies for review rather than deleting.
- **Applies To:** File management design; storage operations.
- **Trigger:** Any requirement in these areas.
- **Expected Behavior:** Only confirmed behavior is implemented; the rest awaits separate approval and must preserve ownership, privacy, and historical reference validity.
- **Exceptions:** None confirmed.
- **Related Documents:** `20_File_Storage.md` §9, §12, §15–§19; `21_Background_Jobs.md` §10.1; `23_Security_Standards.md` §9.4.

**Cross-referenced rules:** BR-018/LSN rules (§15 — Lesson files), BR-021/HW rules (§12 — Homework files), Q-010 (§15, LSN-02), SEC rules (§23), RET-04 (§24 — reference retention).

---

# 22. Search Rules

**Authoritative sources:** `22_Search_Filtering.md` (whole document), `18_Reporting_Analytics.md` §16–§17, `09_Permission_Matrix.md`, `02_Software_Requirements.md` Part 2 §4 (Student search).

### SRC-01 — Authorization Before Search

- **Rule ID:** SRC-01
- **Rule Name:** No Search Without Authorization
- **Description:** Every search, filter, sort, and pagination request is evaluated for authentication, role, scope, ownership, and permission **before** data retrieval begins. The backend is the final enforcement authority; frontend visibility or hidden controls are not security controls. A user who cannot view records through normal navigation must never discover them through search.
- **Applies To:** All search and list surfaces; all roles.
- **Trigger:** Any search, filter, sort, or pagination request.
- **Expected Behavior:** Unauthorized search attempts are denied without revealing record existence.
- **Exceptions:** None confirmed.
- **Related Documents:** `22_Search_Filtering.md` §3.1, §10; `23_Security_Standards.md` §4.4.

### SRC-02 — Scope Resolution Before Filtering

- **Rule ID:** SRC-02
- **Rule Name:** Scope First, Criteria Second
- **Description:** The user's authorized scope (Teacher Workspace / Student account / Parent linked-Student / Platform) resolves **before** any search term, filter, or sort is applied. Filters must reference records inside the authorized scope; cross-scope filters (another Teacher Workspace, an unlinked Student, another Student) are rejected.
- **Applies To:** All filtering and advanced search.
- **Trigger:** Any filter application.
- **Expected Behavior:** Filters narrow, but never broaden, the authorized result set; invalid or unauthorized filter references are rejected safely.
- **Exceptions:** None confirmed.
- **Related Documents:** `22_Search_Filtering.md` §3.2, §6.1–§6.3, §11.

### SRC-03 — No Cross-Teacher Discovery

- **Rule ID:** SRC-03
- **Rule Name:** Search Respects Isolation Absolutely
- **Description:** A Teacher's or Teacher Staff member's search must never return records from another Teacher Workspace — and must not reveal the **existence, count, names, or metadata** of records in another workspace, even when a search term matches across workspaces. Equivalently, Students cannot discover other Students' records, and Parents cannot discover unlinked Students' records, through search.
- **Applies To:** Global Search and Module Search; all roles.
- **Trigger:** Any search whose terms could match out-of-scope records.
- **Expected Behavior:** Only in-scope matches return; out-of-scope existence is never leaked — including through counts, empty states, or errors.
- **Exceptions:** None confirmed.
- **Related Documents:** `22_Search_Filtering.md` §3.3–§3.5, §3.8, §18; BR-003 (§5), BR-001 (§6), BR-004 (§7).

### SRC-04 — Archive-Aware Search Results

- **Rule ID:** SRC-04
- **Rule Name:** Active Search Returns Active Records
- **Description:** Active searches and list views return **only active records** (default: active-only). Archived records appear only in authorized historical/report contexts, clearly indicated as archived — never in active dropdown lists, selectors, pickers, or assignment lists.
- **Applies To:** All searches, lists, and selectors.
- **Trigger:** Any search or list rendering; any archive-state filter.
- **Expected Behavior:** Archive-state filtering (active only / archived only / all, the latter two only in authorized historical contexts) never bypasses the Archive Policy.
- **Exceptions:** None confirmed.
- **Related Documents:** `22_Search_Filtering.md` §3.6, §13.3; `00_Project_Context.md` §11; BR-005 (§24).

### SRC-05 — Financial Flow Separation in Search

- **Rule ID:** SRC-05
- **Rule Name:** No Ambiguous Mixed Financial Results
- **Description:** Search and filtering across financial domains maintain Flow A / Flow B separation. A single search must not return ambiguous results mixing Subscription (Flow A) and payment-status (Flow B) records; financial filters clearly distinguish the flows.
- **Applies To:** Payment and Subscription search/report contexts.
- **Trigger:** Any financial-domain search or filter.
- **Expected Behavior:** Mixed results are rejected or separated; labels remain unambiguous.
- **Exceptions:** None confirmed.
- **Related Documents:** `22_Search_Filtering.md` §3.7, §6.1, §13.4, §17–§18; D-036; PAY-01 (§18).

### SRC-06 — Pagination for All Lists

- **Rule ID:** SRC-06
- **Rule Name:** No Unbounded Result Sets
- **Description:** All list endpoints and views support pagination (except small fixed sets). Pagination applies after authorization, scope resolution, filtering, and sorting; metadata (total counts, last page) reflects **only the authorized result set**; a page beyond the last page returns an empty data set, not an error.
- **Applies To:** All list/search views.
- **Trigger:** Any list or search request.
- **Expected Behavior:** Paginated, accurate, scope-correct results; count metadata never leaks out-of-scope records.
- **Exceptions:** Cursor-based pagination is a future consideration; offset-based pagination is the Version 1 standard.
- **Related Documents:** `22_Search_Filtering.md` §8; `18_Reporting_Analytics.md` §20.

### SRC-07 — Content-Restricted Search Domains

- **Rule ID:** SRC-07
- **Rule Name:** Special Search Boundaries
- **Description:** Question Bank search is available only to the owning Teacher and authorized Teacher Staff (Students, Parents, other Teachers cannot search a Teacher's private bank). Lesson search for Students returns only their own Teachers' Lessons (no cross-Teacher Lesson discovery). Teacher search by a Teacher is for Teacher Workspace management and must not expose other Teacher-private records. Audit Log search follows Audit visibility scopes (§25). Flow A search is Platform-scoped (Super Admin; Teachers see own status only).
- **Applies To:** Question Bank, Lessons, Students, Audit Logs, Subscriptions search.
- **Trigger:** Any search in these domains.
- **Expected Behavior:** Domain boundaries apply exactly; denied otherwise.
- **Exceptions:** None confirmed.
- **Related Documents:** `22_Search_Filtering.md` §5.4, §10.3; `02_Software_Requirements.md` Part 2 §4 (Students — Business Rules); EXM-02 (§13); `09_Permission_Matrix.md` §8, §16.

### SRC-08 — Safe Empty Results and Consistent Behavior

- **Rule ID:** SRC-08
- **Rule Name:** Empty Is a Valid Outcome
- **Description:** When a valid search/filter yields no records within the authorized scope, the result is an empty result set with appropriate empty-state handling — never an authorization error, never a hint that matches exist in an inaccessible scope, and never a suggestion to broaden into unauthorized data. Filtering, sorting, and pagination behave consistently across modules.
- **Applies To:** All search surfaces.
- **Trigger:** Any zero-result valid search.
- **Expected Behavior:** Honest, safe empty states; consistent cross-module behavior.
- **Exceptions:** None confirmed.
- **Related Documents:** `22_Search_Filtering.md` §3.9–§3.10, §6.4, §18; `18_Reporting_Analytics.md` §21.

### SRC-09 — Search Infrastructure Boundary

- **Rule ID:** SRC-09
- **Rule Name:** MySQL-Only Search; No Marketplace Discovery
- **Description:** Search runs on MySQL 8 within the cPanel Shared Hosting baseline (MySQL full-text may be used where appropriate). External search engines (Elasticsearch, Algolia, Meilisearch), Redis, and other unconfirmed infrastructure are not required. Search never becomes marketplace discovery, cross-Teacher browsing, global unrestricted access, a notification trigger, or a payment mechanism.
- **Applies To:** Search architecture.
- **Trigger:** Any search design or infrastructure decision.
- **Expected Behavior:** The confirmed baseline is respected; scope and authorization are never traded for performance.
- **Exceptions:** None confirmed.
- **Related Documents:** `22_Search_Filtering.md` §1, §9; `03_System_Architecture.md` §21.

### SRC-10 — Search Logging Boundary

- **Rule ID:** SRC-10
- **Rule Name:** Searches Are Not Mandatory Audit Events
- **Description:** Individual search queries are **not** mandatory Audit Log events under the Audit Log Policy. Operational logging of search activity (for performance, troubleshooting, abuse detection, and repeated failed-authorization security review) is permitted within privacy limits; it never replaces the business Audit Log and never contains private content or credentials.
- **Applies To:** Search operations; logging.
- **Trigger:** Search execution and search-related failures.
- **Expected Behavior:** Required audit events are logged; searches themselves are not individually audited; repeated unauthorized search attempts are logged for security review.
- **Exceptions:** None confirmed.
- **Related Documents:** `22_Search_Filtering.md` §16; `23_Security_Standards.md` §15.5; AUD-08 (§25).

**Cross-referenced rules:** RPT rules (§19 — reports use the same filtering/sorting/pagination standards), BR-005 (§24), ADM-03 (§8 — PENDING Super Admin visibility in search).

---

# 23. Security Rules

**Authoritative sources:** `23_Security_Standards.md` (whole document), `08_RBAC.md` §2, §16, `03_System_Architecture.md` §18, `00_Project_Context.md` §12.6 (PROPOSED mechanics); decisions D-021, D-037–D-043 (`29_Project_Decisions.md`).

### SEC-01 — Backend Is the Sole Security Authority; Deny by Default

- **Rule ID:** SEC-01
- **Rule Name:** Server-Side Enforcement Mandatory (D-021)
- **Description:** The backend is the sole authority for authentication, authorization, tenant isolation, and access control; a user can access a resource **only** when authentication, role, scope, ownership, and permission checks explicitly allow it (deny by default). Frontend visibility, hidden controls, disabled buttons, or URL structure are **never** sufficient security controls.
- **Applies To:** Every layer, every role, every request.
- **Trigger:** Every request and every authorization decision.
- **Expected Behavior:** All final decisions are server-side; unauthorized requests are rejected without exposing restricted data.
- **Exceptions:** None confirmed.
- **Related Documents:** `29_Project_Decisions.md` D-021; `23_Security_Standards.md` §2.1, §4.4; `08_RBAC.md` §14.

### SEC-02 — Least Privilege

- **Rule ID:** SEC-02
- **Rule Name:** Minimum Necessary Permissions
- **Description:** Every role, request, and background job operates with the minimum permissions required. Teacher Staff receive only permissions explicitly assigned by the Teacher; no role receives hard-delete, marketplace, or unconfirmed capabilities.
- **Applies To:** RBAC; background jobs; all operations.
- **Trigger:** Any permission assignment or action.
- **Expected Behavior:** Permissions expand only through documented authority (Teacher assignment for staff; Product Owner decisions for roles).
- **Exceptions:** None confirmed.
- **Related Documents:** `23_Security_Standards.md` §2.1; `08_RBAC.md` §2; BR-013 (§5).

### SEC-03 — Password Policy

- **Rule ID:** SEC-03
- **Rule Name:** Credential Standards
- **Description:** Passwords have a minimum length of 8 characters and must contain at least one uppercase letter, one lowercase letter, and one digit (special characters recommended, not mandatory), enforced server-side. Passwords are hashed via bcrypt or Argon2id through Laravel's hashing; they are never stored in plain text, logged, returned in responses, or placed in Audit Log entries. Password reset uses time-limited, single-use tokens, is rate-limited, never reveals account existence, invalidates old sessions, and is auditable. Default/temporary passwords must be changed on first login.
- **Applies To:** All accounts; authentication security.
- **Trigger:** Password creation, change, or reset.
- **Expected Behavior:** All standard behaviors above are enforced; violations are rejected.
- **Exceptions:** Password history (reuse prevention) is **not confirmed** and must not be silently implemented (`23_Security_Standards.md` §6.3).
- **Related Documents:** `23_Security_Standards.md` §6.

### SEC-04 — Session Security

- **Rule ID:** SEC-04
- **Rule Name:** Database Sessions with Safe Lifecycle (D-040)
- **Description:** Sessions use the Database session driver (MySQL 8). Session identifiers are cryptographically random; cookies carry `HttpOnly`, `Secure` (production), and `SameSite=Lax`/`Strict` flags; sessions expire after inactivity and have an absolute maximum lifetime; logout and password change invalidate sessions; concurrent session counts are bounded or configurable; session data is isolated per user and role context (no blending across roles or Teachers).
- **Applies To:** All authenticated sessions.
- **Trigger:** Session creation, use, expiration, and termination.
- **Expected Behavior:** The confirmed session standards hold; expired/invalidated sessions grant nothing.
- **Exceptions:** None confirmed. Session storage must not require Redis (`23_Security_Standards.md` §7.4).
- **Related Documents:** `23_Security_Standards.md` §7; `29_Project_Decisions.md` D-040.

### SEC-05 — Transport Security

- **Rule ID:** SEC-05
- **Rule Name:** HTTPS in Production (D-039)
- **Description:** HTTPS is required in production for all communication; HTTP redirects to HTTPS; passwords/tokens/personal data never travel unencrypted; security headers (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Cache-Control: no-store` for sensitive responses, CSP at the web server) are applied.
- **Applies To:** All Web Application and API traffic.
- **Trigger:** All production communication.
- **Expected Behavior:** Encrypted transport with the confirmed headers; sensitive responses are never cached.
- **Exceptions:** None confirmed.
- **Related Documents:** `23_Security_Standards.md` §8.1, §8.7, §16.3; `29_Project_Decisions.md` D-039.

### SEC-06 — Rate Limiting on Sensitive Endpoints

- **Rule ID:** SEC-06
- **Rule Name:** Brute-Force and Abuse Protection
- **Description:** Rate limiting applies to: login (per IP and per account identifier), Student registration, password reset, QR Code scanning (per Student per Attendance Session), account activation, file upload, and general API usage. Rate-limited requests receive HTTP 429 with `Retry-After` where applicable; thresholds are not revealed; one Teacher's rate limiting must not affect another.
- **Applies To:** Sensitive endpoints; all roles.
- **Trigger:** Excessive request rates.
- **Expected Behavior:** Throttling per Laravel's built-in mechanisms (File Cache-based), tunable without code changes, never requiring Redis or external services.
- **Exceptions:** Specific threshold values are implementation configuration, not business rules; the Project Context lists rate limiting mechanics as part of its PROPOSED security baseline (`00_Project_Context.md` §12.6), while `23_Security_Standards.md` §14 defines the confirmed standard.
- **Related Documents:** `23_Security_Standards.md` §14; `00_Project_Context.md` §12.6; `02_Software_Requirements.md` Part 6 §5.

### SEC-07 — Layered Input Validation

- **Rule ID:** SEC-07
- **Rule Name:** Validation at Every Layer
- **Description:** Input validation combines: request validation (required fields, formats, types, enum values from **confirmed sets only** — e.g., Pricing Type: `Monthly`/`Per Lesson`; Question Types: the four confirmed), authorization validation (role, scope, ownership, permission, relationships), business validation (no duplicate Students, one Group per Teacher, Teaching Subject immutability, Flow A/B separation, Archive policy), and persistence integrity. Validation failures return field-level errors without exposing internals or revealing whether an unauthorized resource exists.
- **Applies To:** All inputs; all modules.
- **Trigger:** Any data submission.
- **Expected Behavior:** Invalid or unauthorized input is rejected safely (HTTP 422 with safe field-level messages where applicable).
- **Exceptions:** Detailed field-level validation rules belong to the reserved `33_Validation_Rules.md` subject and are not redefined here.
- **Related Documents:** `23_Security_Standards.md` §10; `03_System_Architecture.md` §17.

### SEC-08 — Injection, Scripting, and Forgery Defenses

- **Rule ID:** SEC-08
- **Rule Name:** SQLi / XSS / CSRF Protection
- **Description:** SQL injection is prevented through parameterized queries (no raw SQL with unsanitized input; whitelisted sort fields; escaped LIKE wildcards). XSS is prevented through output encoding (React's default escaping; JSON API responses; sanitization of permitted rich text and filenames; CSP). CSRF protection covers all state-changing requests (tokens; `SameSite` cookies; Sanctum SPA token verification); GET requests perform no state changes.
- **Applies To:** Backend and frontend; all data entry and rendering.
- **Trigger:** Any query building, content rendering, or state-changing request.
- **Expected Behavior:** The layered defenses apply; violations are rejected.
- **Exceptions:** None confirmed.
- **Related Documents:** `23_Security_Standards.md` §11–§13.

### SEC-09 — Sensitive Data Handling and Safe Errors

- **Rule ID:** SEC-09
- **Rule Name:** No Sensitive Data in Errors, Logs, or Responses
- **Description:** API responses include only in-scope data; error messages are generic for authentication/authorization/not-found/server errors and never include SQL, stack traces, server paths, credentials, Teacher-private data, or unlinked Student data. Operational logs never contain passwords, tokens, secrets, or unnecessary personal data. Secrets live in environment variables — never in source code or version control; debug mode is off in production; detailed errors go to operational logs only.
- **Applies To:** All errors, logs, responses, configuration.
- **Trigger:** Any error, log write, response build, or secret handling.
- **Expected Behavior:** Safe generic user-facing errors; protected internals; protected credentials.
- **Exceptions:** None confirmed.
- **Related Documents:** `23_Security_Standards.md` §16, §18; `22_Search_Filtering.md` §17.2.

### SEC-10 — Audit Log as a Security Control

- **Rule ID:** SEC-10
- **Rule Name:** Security Traceability
- **Description:** The Audit Log provides accountability and forensic capability. Beyond the ten mandatory events (§25), security-relevant events — repeated failed logins, authorization failures, cross-Teacher access attempts, rate-limit violations, file-upload validation failures, password-reset activity, session lifecycle events — should be logged per the security standard. Incident-response actions that modify data themselves produce Audit Log entries.
- **Applies To:** Security monitoring; incident response.
- **Trigger:** Security-relevant events; incidents.
- **Expected Behavior:** Security events are traceable without exposing Teacher-private data; monitoring introduces no notifications (§20).
- **Exceptions:** Monitoring tools, dashboards, and alert thresholds are not confirmed (`23_Security_Standards.md` §19.4).
- **Related Documents:** `23_Security_Standards.md` §15, §19–§20; `08_RBAC.md` §15.

### SEC-11 — No Hard Delete Permission Exists

- **Rule ID:** SEC-11
- **Rule Name:** Deletion Is Structurally Impossible
- **Description:** RBAC grants no hard-delete permission to **any** role — not Teacher, not Teacher Staff, not Super Admin. Archive and restore are the only lifecycle operations, restricted to authorized users.
- **Applies To:** Permission model; all actors.
- **Trigger:** Any deletion attempt or permission design.
- **Expected Behavior:** Hard delete is denied everywhere; Archive paths are used per §24.
- **Exceptions:** None confirmed.
- **Related Documents:** `08_RBAC.md` §2, §4 (Archive Permissions), §16; `09_Permission_Matrix.md` §17 (`platform.hard_delete` — Denied); BR-005 (§24).

**Cross-referenced rules:** BR-003 (§5 — isolation), BR-004 (§7 — read-only), AUTH rules (§3), FIL rules (§21 — upload/file security), AUD rules (§25).

---

# 24. Data Retention Rules

**Authoritative sources:** `00_Project_Context.md` §9.3 (BR-005, BR-014), §11 (Archive Policy), `06_Database_Design.md` §14–§16, `29_Project_Decisions.md` D-033, D-035, `02_Software_Requirements.md` Part 6 §17–§18, `23_Security_Standards.md` §17, `26_Deployment_Plan.md` §18.

### BR-005 — Archive Replaces Deletion Everywhere

- **Rule ID:** BR-005 (canonical, CONFIRMED)
- **Description:** **No permanent deletion is allowed — Archive must be used instead.** Applies to **all records, by all actors, everywhere**. No hard delete exists anywhere in the system. Governed by the Archive Policy (see RET-01).
- **Rule Name:** Archive-Only Lifecycle
- **Applies To:** Every entity, every role, every module, every background job.
- **Trigger:** Any request to remove data from active use.
- **Expected Behavior:** The operation is an Archive (state change), never a physical removal; deletion attempts are rejected.
- **Exceptions:** None. Cascade archival never erases child history (GRP-04, §10).
- **Related Documents:** `00_Project_Context.md` §9.3 (BR-005), §11; `29_Project_Decisions.md` D-033; `06_Database_Design.md` §7, §15.

### BR-014 — Historical Data Is Never Deleted and Always Available

- **Rule ID:** BR-014 (canonical, CONFIRMED)
- **Rule Name:** Permanent Historical Availability
- **Description:** Historical data is **never deleted** and must **always remain available** — reports and history queries include archived records, clearly indicated. This covers academic history, Enrollment history, payment-status history, billing history, archived records, and file references tied to historical records.
- **Applies To:** All historical records; reporting; retention design.
- **Trigger:** Historical/reporting queries; retention planning.
- **Expected Behavior:** History stays queryable with correct context and indication; retention policies never expire business history.
- **Exceptions:** None confirmed. (Database growth planning is a performance concern, never a deletion justification.)
- **Related Documents:** `00_Project_Context.md` §9.3 (BR-014); `29_Project_Decisions.md` D-035; `06_Database_Design.md` §16; `02_Software_Requirements.md` Part 6 §17.

### RET-01 — Archive Policy Properties

- **Rule ID:** RET-01
- **Rule Name:** The Seven Confirmed Archive Properties
- **Description:** Archived records: (1) never appear in normal searches; (2) never appear in active dropdown lists/pickers/selectors/assignment lists; (3) remain available in reports, clearly indicated; (4) can be restored by authorized users (with the restore audited); (5) never lose historical relationships — archival never detaches, rewrites, or re-points history; (6) coexist with the absence of any hard delete anywhere in the system; and (7) follow container rules — archiving a container never archives its historical records.
- **Applies To:** Every archivable entity.
- **Trigger:** Any Archive, restore, list, search, or report involving archived records.
- **Expected Behavior:** All seven properties hold simultaneously.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §11; `02_Software_Requirements.md` Part 6 §18 (NFR-ARCH-001…009); `06_Database_Design.md` §15.

### RET-02 — Transfer and Enrollment History Retention

- **Rule ID:** RET-02
- **Rule Name:** Movement Never Loses History
- **Description:** Student transfer history is retained: historical Attendance, Homework, Exams, and grades survive Group moves, referencing the original Enrollment period and structure as of recording time (BR-007, §10). Enrollment history likewise supports Billing accuracy across moves (BIL-06, §17).
- **Applies To:** Enrollments; Student academic records; billing inputs.
- **Trigger:** Group moves; historical queries.
- **Expected Behavior:** Records stay linked to their original context; reports resolve correctly across moves.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` BR-007; `06_Database_Design.md` §9, §16; `29_Project_Decisions.md` D-031.

### RET-03 — Financial and Pricing History Retention

- **Rule ID:** RET-03
- **Rule Name:** Money History Stays Intact
- **Description:** Historical Flow A invoices/snapshots keep the price applicable to their period (BR-015, §8); historical Billing Cycles and Subscription records remain available (BIL-05, §17); historical Flow B payment-status records remain available (PAY-07, §18). Financial history is never rewritten — corrections are adjustments (BIL-03, PROPOSED mechanics).
- **Applies To:** Billing and payment-status records.
- **Trigger:** Billing close; corrections; historical financial reporting.
- **Expected Behavior:** Period-correct financial history remains available and unaltered.
- **Exceptions:** None confirmed.
- **Related Documents:** `17_Subscription_Billing.md` §5, §14; `18_Reporting_Analytics.md` §12; BR-015.

### RET-04 — File Reference Retention

- **Rule ID:** RET-04
- **Rule Name:** Files and References Follow Retention Rules
- **Description:** File references tied to historical records are retained; archived file references remain available for authorized historical reports and are never detached from their owning records; physical file-removal policies must not undermine permanent historical retention (FIL-07, FIL-08).
- **Applies To:** File references; storage maintenance.
- **Trigger:** Archive operations; integrity checks; cleanup proposals.
- **Expected Behavior:** Retention of references; no destructive cleanup.
- **Exceptions:** None confirmed.
- **Related Documents:** `06_Database_Design.md` §11, §16; `20_File_Storage.md` §17–§18; `21_Background_Jobs.md` §10.

### RET-05 — Backup and Recovery Preserve Retention and Isolation

- **Rule ID:** RET-05
- **Rule Name:** Retention-Safe Backups
- **Description:** Backup and recovery planning must preserve: historical data, Audit Log entries (immutable), archived records and their relationships, Student transfer history, Flow A/Flow B separation, and Teacher Workspace isolation (a restore never mixes workspaces). Backup artifacts must not be committed to the source repository; access is restricted; integrity is verified periodically.
- **Applies To:** Backup strategy; recovery procedures.
- **Trigger:** Backup creation, storage, verification, or restoration.
- **Expected Behavior:** Backups preserve all retention invariants; recovery yields the same isolation and history guarantees as production.
- **Exceptions:** Quantified recovery objectives (RTO/RPO) and backup topology are not confirmed (`02_Software_Requirements.md` Part 6 §10; `26_Deployment_Plan.md` §18).
- **Related Documents:** `23_Security_Standards.md` §17; `26_Deployment_Plan.md` §18; `02_Software_Requirements.md` Part 6 §10.

**Cross-referenced rules:** BR-006/AUD rules (§25 — Audit retention is permanent), GRP-04 (§10), RET contexts inside every domain section above.

---

# 25. Audit Log Rules

**Authoritative sources:** `00_Project_Context.md` §9.3 (BR-006), §10 (Audit Log Policy), `29_Project_Decisions.md` D-034, `06_Database_Design.md` §8, `09_Permission_Matrix.md` §16, `03_System_Architecture.md` §16, `23_Security_Standards.md` §15, `21_Background_Jobs.md` §3, §11, §17.

### BR-006 — Every Important Action Is Recorded

- **Rule ID:** BR-006 (canonical, CONFIRMED)
- **Rule Name:** Mandatory Audit Coverage
- **Description:** **Every important action is recorded in the Audit Log.** The Audit Log is a first-class, platform-wide subsystem governed by the Audit Log Policy — the requirement is explicit, not aspirational.
- **Applies To:** All roles, all modules, all surfaces, background jobs.
- **Trigger:** Any important action (see AUD-01 catalog).
- **Expected Behavior:** The action produces an Audit Log entry; an action that requires an audit entry cannot be considered complete without it (see AUD-03).
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §9.3 (BR-006), §10; `29_Project_Decisions.md` D-034.

### AUD-01 — The Ten Mandatory Event Types

- **Rule ID:** AUD-01
- **Rule Name:** Confirmed Audit Event Catalog
- **Description:** The following events are recorded **without exception**, across all roles and surfaces: **(1) Create** — creation of any record; **(2) Update** — modification of any record; **(3) Archive** — every archival action; **(4) Restore** — every restoration; **(5) Login** — every successful and failed authentication; **(6) Permission Change** — any change to a Teacher Staff user's granted permissions; **(7) Attendance Change** — recording or modifying any attendance entry, by any method; **(8) Exam Modification** — creating, editing, publishing, or archiving exams and questions; **(9) Homework Modification** — creating, editing, grading, or archiving homework; **(10) Subscription Change** — Subscription lifecycle events (status changes, payment-status recording).
- **Applies To:** All modules and roles.
- **Trigger:** Any of the ten event types.
- **Expected Behavior:** An entry is written per policy, with correct attribution and context.
- **Exceptions:** None confirmed. (Event names are used exactly as defined in the policy.)
- **Related Documents:** `00_Project_Context.md` §10.1; `06_Database_Design.md` §8; `02_Software_Requirements.md` Part 6 §8.

### AUD-02 — Append-Only, Immutable, Permanently Retained

- **Rule ID:** AUD-02
- **Rule Name:** Audit Immutability
- **Description:** Audit Log entries are **append-only and immutable** — never edited or deleted by any actor, including the Super Admin. Retention is **permanent**; entries are never purged, archived, or compacted. The log itself is subject to BR-005 and BR-014 (it cannot be archived or deleted).
- **Applies To:** The Audit Log subsystem.
- **Trigger:** Any attempt to modify, archive, delete, purge, or compact the log.
- **Expected Behavior:** The attempt is rejected; integrity verification jobs report anomalies without modifying entries.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §10.3; `09_Permission_Matrix.md` §16 (`…audit_log.update/archive/restore` — Denied); `21_Background_Jobs.md` §11.

### AUD-03 — Transactional Guarantee (PROPOSED Mechanics)

- **Rule ID:** AUD-03
- **Rule Name:** Action and Entry Succeed Together
- **Description:** The audit entry is written in the **same database transaction** as the action it describes — an action cannot succeed without its audit record. Background jobs follow the same rule: if audit recording fails, the business action is not considered complete (retry or roll back). (Marked PROPOSED mechanics in the Project Context; `21_Background_Jobs.md` confirms it as a processing principle.)
- **Applies To:** All audited actions; all background jobs.
- **Trigger:** Any audited action execution.
- **Expected Behavior:** Atomic action-plus-audit persistence.
- **Exceptions:** Mechanics are PROPOSED (`00_Project_Context.md` §10.3.2); the outcome (no un-audited important action) is required.
- **Related Documents:** `00_Project_Context.md` §10.3.2; `21_Background_Jobs.md` §3 (principle 7), §20 (edge case 14); `29_Project_Decisions.md` D-034.

### AUD-04 — Audit Entry Content

- **Rule ID:** AUD-04
- **Rule Name:** What Each Entry Contains (PROPOSED Record Shape)
- **Description:** Each entry contains: **Actor** (user ID and role); **Context** (Teacher Workspace or Platform scope); **Event** (event type plus affected entity type and ID); **Payload** (before/after snapshot of changed fields); **Origin** (server timestamp, IP address, device/client information). (Record shape is PROPOSED in the Project Context and confirmed in usage by the Security Standards.)
- **Applies To:** Audit Log entries.
- **Trigger:** Entry creation.
- **Expected Behavior:** Entries carry complete traceability information without exposing unnecessary sensitive data.
- **Exceptions:** Shape is PROPOSED (`00_Project_Context.md` §10.2); passwords and secrets never appear in entries (SEC-03, SEC-09).
- **Related Documents:** `00_Project_Context.md` §10.2; `23_Security_Standards.md` §15.3; `08_RBAC.md` §15.

### AUD-05 — Actor Attribution Rules

- **Rule ID:** AUD-05
- **Rule Name:** Correct Attribution
- **Description:** **Teacher Staff actions are attributed to the Teacher Staff user, never to the Teacher.** Super Admin Platform actions are attributed to the Super Admin; Student and Parent actions are attributed to the authenticated account; failed login events are auditable.
- **Applies To:** All Audit Log entries.
- **Trigger:** Any audited action.
- **Expected Behavior:** The acting user — not the account owner — is recorded.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §10.2; `08_RBAC.md` §15; `06_Database_Design.md` §8; BR-013 (§5).

### AUD-06 — Scoped Audit Visibility

- **Rule ID:** AUD-06
- **Rule Name:** Who May View Audit Logs
- **Description:** A Teacher sees their own workspace's Audit Log where permitted by requirements (visibility is conditional and workspace-scoped); the Super Admin sees Platform-scope events (Subscription changes, logins, administration) within confirmed visibility boundaries, with Teacher Workspace event visibility conditional on the Q-012 resolution and never exposing private content by default; **Student and Parent Audit Log visibility is not a confirmed Version 1 surface**. Audit reporting never becomes a route to Teacher-private content.
- **Applies To:** Audit Log viewing and reporting.
- **Trigger:** Any Audit Log view/search request.
- **Expected Behavior:** Visibility follows role and scope; hidden where not confirmed.
- **Exceptions:** Scoped visibility mechanics are PROPOSED (`00_Project_Context.md` §10.3.3); Q-012 remains PENDING.
- **Related Documents:** `00_Project_Context.md` §10.3.3; `09_Permission_Matrix.md` §16; `18_Reporting_Analytics.md` §19; `03_System_Architecture.md` §16.4.

### AUD-07 — Background Job Audit Obligations

- **Rule ID:** AUD-07
- **Rule Name:** System Actions Are Auditable Too
- **Description:** Background jobs that perform important actions (Billing Cycle events, Subscription snapshot generation, Attendance cleanup, Exam grading, report preparation where qualified) record Audit Log entries identifying the system or actor context, event type, affected record, workspace/Platform context, and timestamp — following the same immutability rules.
- **Applies To:** All background jobs and scheduled tasks.
- **Trigger:** Any important action executed by a job.
- **Expected Behavior:** Job-executed actions are as auditable as user-executed ones.
- **Exceptions:** None confirmed.
- **Related Documents:** `21_Background_Jobs.md` §3, §17.2; `00_Project_Context.md` §10.

### AUD-08 — What Is Not a Mandatory Audit Event

- **Rule ID:** AUD-08
- **Rule Name:** Documented Non-Mandatory Events
- **Description:** Individual search queries are not mandatory Audit Log events (operational logging may apply instead — SRC-10). Parent read-only viewing actions are not listed as mandatory Audit Log events unless later requirements define them as important actions. No notification-delivery audit exists because no notification delivery exists (§20).
- **Applies To:** Audit policy interpretation.
- **Trigger:** Search/read-only operations.
- **Expected Behavior:** Mandatory events are always logged; non-mandatory activity follows operational-logging rules without inflating the business log.
- **Exceptions:** Failed logins and security-relevant events remain auditable regardless (AUTH-03, SEC-10).
- **Related Documents:** `22_Search_Filtering.md` §16.1; `08_RBAC.md` §8; `19_Notification_System.md` §18.

**Cross-referenced rules:** BR-005/BR-014 (§24 — the log itself cannot be archived or deleted), SEC-10 (§23), per-domain audit hooks: ATT-08 (§11), HW-02 (§12), EXM-07 (§13), SUB-04 (§16).

---

# 26. Future Features Rules

**Authoritative sources:** `00_Project_Context.md` §4.2, §15, frozen-document header; `27_Development_Roadmap.md` §18, §20; `29_Project_Decisions.md` §8–§9; `31_Master_Index.md` §2.8, §8.

### FUT-01 — Version 1 Scope Exclusions Are Binding

- **Rule ID:** FUT-01
- **Rule Name:** Confirmed Out-of-Scope List
- **Description:** The following are explicitly **out of scope for Version 1** and binding on all work: native mobile applications (D-049); online payment gateways (D-002); push/email/SMS notifications (D-012); multiple Teaching Subjects per Teacher account (D-051); marketplace behavior and course discovery (D-050); video homework (D-011); multiple Parent accounts per Student (D-009); Platform staff roles beyond the five confirmed roles; and in-platform payment transactions of any kind.
- **Applies To:** All planning, documentation, design, and implementation.
- **Trigger:** Any proposal touching an excluded area.
- **Expected Behavior:** The proposal is future scope only; it is never implemented as Version 1 capability.
- **Exceptions:** None confirmed — each item can change only through the change process (§28) with a separate Product Owner decision.
- **Related Documents:** `00_Project_Context.md` §4.2; `01_Project_Vision.md` §9; `29_Project_Decisions.md` §8; `27_Development_Roadmap.md` §18.3.

### FUT-02 — PENDING Register and Proposed Defaults

- **Rule ID:** FUT-02
- **Rule Name:** The Six Protected Open Questions
- **Description:** Exactly six questions are PENDING and must never be silently hardened: **Q-005** non-payment enforcement (proposed: 7-day grace → workspace read-only; Students keep read access; nothing auto-archives); **Q-010** Lesson video hosting/protection (proposed: private storage, signed short-lived URLs, streaming-only, per-Teacher quota); **Q-011** Teacher Staff permission granularity (proposed: capability-flag catalog per module, saveable named presets); **Q-012** Super Admin content visibility (proposed: aggregates/finances/metadata only); **Q-013** flat price vs. volume tiers (proposed: flat at launch, tier-ready engine); **Q-015** timezone/currency (proposed: Arabic-first RTL + English; per-Teacher timezone; platform-level currency). No code or document may harden against a PENDING item; the proposed default keeps momentum without becoming a rule.
- **Applies To:** All rule application, design, and implementation in the affected areas.
- **Trigger:** Any work touching a PENDING area.
- **Expected Behavior:** Work stops at the confirmed boundary (or proceeds explicitly under the labeled proposed default where the source permits) and reports the PENDING dependency.
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` §15.1; `29_Project_Decisions.md` §9; `31_Master_Index.md` §3.3, §11.2.

### FUT-03 — Future Scope Enters Only Through Approval

- **Rule ID:** FUT-03
- **Rule Name:** Controlled Scope Entry
- **Description:** The Project Context is frozen; no new features are added to it. Future scope must be documented in separate files and requires a Product Owner decision recorded in `29_Project_Decisions.md` **before** any document or code adopts it. Scope changes never retroactively alter Version 1; development follows the phased roadmap.
- **Applies To:** Scope management; documentation and planning.
- **Trigger:** Any new feature or capability proposal.
- **Expected Behavior:** Proposal → Product Owner decision (D-xxx) → documentation of the owning document → implementation through the roadmap. Unapproved scope is rejected (see §28, BCP-02).
- **Exceptions:** None confirmed.
- **Related Documents:** `00_Project_Context.md` header, §17; `31_Master_Index.md` §8.2; `27_Development_Roadmap.md` §18.3; `29_Project_Decisions.md` D-046, D-047.

### FUT-04 — Documented Future Outlines Are Not Commitments

- **Rule ID:** FUT-04
- **Rule Name:** v1.1 / v2.0 Outlines
- **Description:** The roadmap records possible future areas **for orientation only**: v1.1 may include resolved-PENDING refinements (Teacher Staff permissions, content visibility, non-payment enforcement, pricing model), enhanced reporting, search enhancements, and background-job refinement; v2.0 may include native mobile, payment gateways, notifications, advanced media protection, localization, advanced analytics, infrastructure upgrades, multiple Teaching Subjects, and advanced RBAC. None of these are commitments; each requires separate approval per FUT-03.
- **Applies To:** Future planning.
- **Trigger:** Any reference to future capabilities.
- **Expected Behavior:** They are labeled future/PENDING and never treated as active rules.
- **Exceptions:** None confirmed.
- **Related Documents:** `27_Development_Roadmap.md` §20.1–§20.2; each feature document's "Future Improvements / Future Considerations" section.

### FUT-05 — Future Work Must Preserve the Invariants

- **Rule ID:** FUT-05
- **Rule Name:** Non-Negotiable Invariants for All Versions
- **Description:** Every future version, feature, or enhancement — no matter how approved — must preserve: Teacher Workspace isolation (BR-003); one global Student account (BR-001, BR-022); one Parent account per Student in V1 with Parent read-only access (BR-004, BR-020); Archive instead of permanent deletion (BR-005); the immutable permanent Audit Log (BR-006); historical data preservation (BR-007, BR-014); Flow A / Flow B separation; private Teacher-owned content; and canonical terminology.
- **Applies To:** All future scope.
- **Trigger:** Any future feature approval.
- **Expected Behavior:** Approvals are evaluated against the invariant list; anything violating an invariant is defective.
- **Exceptions:** None confirmed.
- **Related Documents:** `27_Development_Roadmap.md` §20.3; `31_Master_Index.md` §11.3, §6.4.

---

# 27. Rule Conflict Resolution

**Authoritative sources:** `31_Master_Index.md` §10 (Conflict Resolution Policy), §9 (Source of Truth Policy), §2.3; `00_Project_Context.md` header and Statement Status Convention.

This section records the confirmed governance for resolving conflicts between statements — including between rules consolidated in this catalog. A conflict exists when two documents make statements that cannot both be true, or when a document contradicts the Single Source of Truth. **Conflicts are defects and must be resolved, never worked around.**

### CRR-01 — Precedence Order

- **Rule ID:** CRR-01
- **Rule Name:** Authority Ranking for Conflicts
- **Description:** Conflicts are resolved by applying the following precedence, in order, until resolved: **(1)** `00_Project_Context.md` — always wins; **(2)** an explicit Product Owner confirmation recorded in `29_Project_Decisions.md` as CONFIRMED; **(3)** the subject-owning document per the ownership table (CRR-06); **(4)** the lower documentation layer over the higher layer; **(5)** the more specific document over the more general one, same-layer, when neither owns the subject; **(6)** the more recently revised document, only when the previous rules cannot resolve it; **(7)** escalation to the Product Owner when precedence cannot resolve the conflict or the conflict touches a PENDING item.
- **Applies To:** Any contradiction between documents, or between a document and this catalog.
- **Trigger:** Discovery of a conflict.
- **Expected Behavior:** Work stops on the affected area; precedence is applied; the losing statement is corrected — never the winning one.
- **Exceptions:** None confirmed.
- **Related Documents:** `31_Master_Index.md` §10.1–§10.2.

### CRR-02 — Resolution Procedure

- **Rule ID:** CRR-02
- **Rule Name:** How a Conflict Is Corrected
- **Description:** The confirmed procedure: (1) stop the affected work — implement neither side; (2) record the conflict (documents, exact statements, affected sections); (3) apply precedence (CRR-01); (4) correct the **losing** document under the modification rules (§28) — never the winning one; (5) review the corrected document's consumers for propagation; (6) if the conflict revealed an undocumented decision, record it in `29_Project_Decisions.md`; (7) update revision and change history; (8) re-run the documentation review checklist.
- **Applies To:** All documentation conflicts.
- **Trigger:** After a conflict is recorded.
- **Expected Behavior:** The full eight-step sequence is executed; silent choice between conflicting statements is prohibited.
- **Exceptions:** None confirmed.
- **Related Documents:** `31_Master_Index.md` §10.3, §11.2 (conflict discipline), §13.4, §14.

### CRR-03 — Terminology Conflicts

- **Rule ID:** CRR-03
- **Rule Name:** Terminology Authority
- **Description:** Terminology conflicts are resolved by `00_Project_Context.md` §19 first, then `30_Project_Glossary.md`. The non-canonical usage is always the defect (for example: "Class" instead of Educational Grade; "Course" instead of Lesson/Teaching Subject; "Delete" instead of Archive; "sub-teacher" instead of Teacher Staff).
- **Applies To:** All documents, interfaces, code artifacts, conversations.
- **Trigger:** Any terminology discrepancy.
- **Expected Behavior:** Canonical terminology replaces the non-canonical usage in the defective document.
- **Exceptions:** None confirmed.
- **Related Documents:** `31_Master_Index.md` §10.4; `00_Project_Context.md` §19; D-048.

### CRR-04 — PENDING Conflicts

- **Rule ID:** CRR-04
- **Rule Name:** Assumed Resolutions Are Defective
- **Description:** If two documents differ because one assumed a resolution to a PENDING question (Q-005, Q-010, Q-011, Q-012, Q-013, Q-015), the **assuming document is defective**. The PENDING item stays PENDING, and the assumption is removed or relabeled as the documented proposed default.
- **Applies To:** Any statement touching a PENDING area.
- **Trigger:** Discovery of an assumed PENDING resolution.
- **Expected Behavior:** The assumption is removed; the PENDING status and its proposed default are restored exactly as documented.
- **Exceptions:** None confirmed.
- **Related Documents:** `31_Master_Index.md` §10.5; FUT-02 (§26).

### CRR-05 — Frozen Content Conflicts

- **Rule ID:** CRR-05
- **Rule Name:** The Project Context Is Never Amended by Resolvers
- **Description:** A conflict with `00_Project_Context.md` is **never** resolved by amending it; the other document is corrected. Only the Product Owner may unfreeze or amend the Project Context.
- **Applies To:** Any conflict involving the frozen Project Context.
- **Trigger:** Any such conflict.
- **Expected Behavior:** The non-frozen document changes; the frozen document does not.
- **Exceptions:** None confirmed.
- **Related Documents:** `31_Master_Index.md` §10.6; `00_Project_Context.md` (frozen, Revision 2.0 FINAL).

### CRR-06 — Duplicate Rules and Subject Ownership

- **Rule ID:** CRR-06
- **Rule Name:** One Owner Per Rule; References Over Restatement
- **Description:** Each rule has exactly one authoritative owner. When the same rule appears in multiple documents (including this catalog), the authoritative definition is the owner's, and other appearances are references that must cite the owner rather than restate competitively. The confirmed ownership mapping relevant to business rules is:

| Rule domain | Authoritative document |
|---|---|
| Canonical business rules, policies, scope, statuses, terminology, open questions | `00_Project_Context.md` (§9, §10, §11, §15, §19) |
| Vision and out-of-scope boundaries | `01_Project_Vision.md` |
| Functional/module requirements and screen behavior | `02_Software_Requirements.md` |
| Role journeys | `05_User_Flows.md` |
| Logical entities and integrity rules | `06_Database_Design.md`, `07_Data_Dictionary.md` |
| Role model and access rules | `08_RBAC.md`, `09_Permission_Matrix.md` |
| Exam behavior | `15_Exam_Engine.md` |
| Attendance behavior | `16_QR_Attendance_System.md` |
| Flow A Subscription and billing | `17_Subscription_Billing.md` |
| Reporting scope and visibility | `18_Reporting_Analytics.md` |
| Notification exclusion and future boundaries | `19_Notification_System.md` |
| File storage and access | `20_File_Storage.md` |
| Queue and scheduled work | `21_Background_Jobs.md` |
| Search, filtering, sorting, pagination | `22_Search_Filtering.md` |
| Security requirements | `23_Security_Standards.md` |
| Decision register | `29_Project_Decisions.md` |
| Canonical terminology definitions | `30_Project_Glossary.md` |
| Documentation structure, ownership, governance | `31_Master_Index.md` |

- **Applies To:** This entire catalog and every consumer of business rules.
- **Trigger:** Any discovery of a duplicated or drift-prone rule statement.
- **Expected Behavior:** Consolidation cites the owner (as this document does per rule); drift is corrected to match the owner; this catalog never overrides an owner.
- **Exceptions:** None confirmed.
- **Related Documents:** `31_Master_Index.md` §2.3, §9.2–§9.3.

---

# 28. Business Rule Change Process

**Authoritative sources:** `31_Master_Index.md` §8 (Modification Rules), §13 (Documentation Maintenance), §14 (Review Checklist); `00_Project_Context.md` §17 (Collaboration Protocol), §15 (Open Questions), §21 (Change History pattern); `29_Project_Decisions.md` (decision register).

This section records the confirmed process by which a **business rule** may be proposed, decided, documented, propagated, and retired. No other process may create, change, or remove business rules.

### BCP-01 — Authority to Change Rules

- **Rule ID:** BCP-01
- **Rule Name:** Product Owner Authority over Product Rules
- **Description:** Product decisions — including every CONFIRMED business rule (BR-xxx) and all scope — belong to the **Product Owner**; technical and documentation authorship belongs to the **Architect**. Changing a CONFIRMED rule, adding a new feature or rule, resolving a PENDING question, or amending the frozen Project Context requires Product Owner authority. No document, AI session, developer, or Architect may unilaterally change a CONFIRMED rule.
- **Applies To:** All rule changes.
- **Trigger:** Any proposed rule change.
- **Expected Behavior:** The change is routed to the Product Owner; without a Product Owner decision, the rule stands.
- **Exceptions:** Clarifying wording **without changing meaning**, and correcting a lower-layer inconsistency in a higher-layer document, are allowed without a new decision (consistency review still required) — see BCP-03.
- **Related Documents:** `00_Project_Context.md` §17.8; `31_Master_Index.md` §8.2, §13.1.

### BCP-02 — Decision First, Then Documentation

- **Rule ID:** BCP-02
- **Rule Name:** The Decision Register Precedes All Rule Changes
- **Description:** A new Product Owner decision is recorded in `29_Project_Decisions.md` (as a D-xxx entry with context, decision, reasoning, alternatives, consequences, and status) **before** any document is changed to adopt it. New canonical business rules (BR-xxx) are established only in `00_Project_Context.md` §9 through a Product Owner-confirmed revision of that document; no other document may assign BR numbers.
- **Applies To:** All new rules, rule changes, PENDING resolutions, scope changes.
- **Trigger:** Any approved product decision.
- **Expected Behavior:** Decision recorded → owning document updated → consumers/propagated references updated → this catalog's relevant rule entries updated (as a reference, per CRR-06).
- **Exceptions:** None confirmed.
- **Related Documents:** `31_Master_Index.md` §8.2; `29_Project_Decisions.md`; `00_Project_Context.md` §9, §17.2.

### BCP-03 — Allowed and Prohibited Modifications

- **Rule ID:** BCP-03
- **Rule Name:** What May and May Not Change
- **Description:** **Allowed** (with consistency review): clarifying wording without changing meaning; correcting a factual inconsistency with a lower layer (the lower layer wins); adding detail that elaborates a CONFIRMED rule without extending scope; recording a new Product Owner decision (via BCP-02); adding a glossary term already used in a canonical document. **Prohibited**: adding a new feature without Product Owner confirmation; hardening a PENDING item; changing a CONFIRMED rule without the Product Owner; editing the frozen Project Context; deleting a document or history entry; renaming/renumbering existing documents (numbers are stable identifiers); adding out-of-scope content (source code, APIs, database tables, UI implementation) to documents that exclude them.
- **Applies To:** All documentation edits, including this catalog.
- **Trigger:** Any proposed modification.
- **Expected Behavior:** The change is classified allowed/prohibited first; prohibited changes are refused or escalated.
- **Exceptions:** None confirmed.
- **Related Documents:** `31_Master_Index.md` §8.2, §8.4.

### BCP-04 — Mandatory Modification Sequence

- **Rule ID:** BCP-04
- **Rule Name:** The Eight-Step Change Sequence
- **Description:** Every authorized change follows: (1) confirm the change is authorized (BCP-03); (2) confirm the target document owns the subject — otherwise edit the owner; (3) apply the change in canonical terminology; (4) update every affected reference inside that document; (5) review consumer documents for contradictions and correct or report them; (6) update revision and change history; (7) run the documentation review checklist; (8) update the Master Index (and project-structure registration) if purpose, dependencies, ownership, or inventory changed.
- **Applies To:** Every modification of a canonical document.
- **Trigger:** Any edit.
- **Expected Behavior:** The sequence completes in order; skipped propagation review is a process defect.
- **Exceptions:** None confirmed.
- **Related Documents:** `31_Master_Index.md` §8.3, §13.4–§13.5, §14.

### BCP-05 — Status Lifecycle Discipline

- **Rule ID:** BCP-05
- **Rule Name:** CONFIRMED / PROPOSED / PENDING Transitions
- **Description:** Statuses are never upgraded implicitly. A PENDING item becomes resolved only through a Product Owner decision (BCP-02), after which the open question is **archived** — not deleted — in the register, with its resolution recorded. A PROPOSED mechanic becomes CONFIRMED only by Product Owner confirmation. Superseded decisions are marked superseded, never erased.
- **Applies To:** The open-question register; the decision register; every status-bearing statement.
- **Trigger:** Any status change or resolution.
- **Expected Behavior:** The registry is updated first; consuming documents (including this catalog) then reflect the new status by reference.
- **Exceptions:** None confirmed.
- **Related Documents:** `31_Master_Index.md` §2.7, §14.2; `00_Project_Context.md` §15 (resolved-question archive), Statement Status Convention.

### BCP-06 — History Is Append-Only; Documents Are Never Deleted

- **Rule ID:** BCP-06
- **Rule Name:** Documentation Retention
- **Description:** Change histories are append-only; resolved questions are archived not deleted; retired documents are marked **SUPERSEDED**, retain their number, and point to the replacement — documents are never deleted. Maintenance is triggered by: Product Owner decisions, PENDING resolutions, new documents, discovered contradictions, phase completions, unimplementable rules, and terminology drift.
- **Applies To:** All documents and their histories.
- **Trigger:** Any change, retirement, or maintenance trigger.
- **Expected Behavior:** History accumulates transparently; nothing is silently erased.
- **Exceptions:** None confirmed.
- **Related Documents:** `31_Master_Index.md` §2.7, §13.2, §13.6; `00_Project_Context.md` §21 (change-history pattern).

### BCP-07 — This Catalog's Own Change Behavior

- **Rule ID:** BCP-07
- **Rule Name:** Keeping the Business Rules Reference Consistent
- **Description:** When an authoritative rule changes through BCP-01…BCP-05, this catalog is updated **by reference correction**: the affected rule entries are aligned with the owning document's new definition; no entry here ever leads or contradicts an owner. Newly confirmed rules are added using the same eight-field anatomy; resolved PENDING flags are updated per BCP-05; catalog-specific identifiers are never renumbered after publication (they are stable references, like document numbers).
- **Applies To:** `32_Business_Rules.md` itself.
- **Trigger:** Any authoritative rule change; any newly confirmed rule.
- **Expected Behavior:** The catalog stays a faithful consolidation; its consistency review is re-run per BCP-04.
- **Exceptions:** None confirmed.
- **Related Documents:** `31_Master_Index.md` §8.3, §13.4, §14; §26–§27 of this document.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority; this document is explicitly a consolidation reference and never overrides it or any subject-owning document. |
| All documents read | Passed — every existing document in `AI_DOCS/` (`00`–`31`, including the Master Index, Glossary, and Decisions register) was read in full or in complete relevant part before authoring; `32` was empty prior to this authoring. |
| Consolidation without invention | Passed — every rule entry traces to an existing approved document; catalog identifiers (AUTH-xx … FUT-xx, CRR/BCP) are declared as reference identifiers only and create no new canonical rules; canonical rules keep their BR-xxx identifiers verbatim. |
| Canonical rule fidelity | Passed — BR-001, BR-002, BR-003, BR-004, BR-005, BR-006, BR-007, BR-008, BR-009, BR-010, BR-011, BR-012, BR-013, BR-014, BR-015, BR-016, BR-017, BR-018, BR-019, BR-020, BR-021, BR-022 are consolidated with meanings identical to `00_Project_Context.md` §9 (e.g., Billable = Enrollment duration > 15 calendar days only; Billing Cycle = calendar month; three Attendance methods; four question types; Text/Image/PDF Homework; one Teaching Subject, immutable; one Parent per Student; two Student registration methods). |
| Duplication resolution | Passed — each domain section lists its authoritative sources; rules owned by other domains are cross-referenced (with section pointers) instead of redefined; CRR-06 records the ownership table from `31_Master_Index.md` §9.2. |
| PENDING discipline | Passed — Q-005, Q-010, Q-011, Q-012, Q-013, Q-015 are preserved as PENDING with their documented proposed defaults labeled PROPOSED; nothing is hardened (no grace/suspension, no video-protection mechanics, no staff granularity, no unrestricted Super Admin content access, no tier pricing, no localization assumptions). |
| PROPOSED labeling | Passed — immutable snapshot mechanics (D-003), enrollment-period mechanics, transactional audit guarantee, audit record shape, scoped audit visibility, archived-lesson playback, and security-baseline mechanics (`00` §12) are labeled PROPOSED exactly as their sources mark them. |
| Version 1 scope | Passed — no native mobile, payment gateways, notifications, marketplace/course discovery, video homework, multiple Teaching Subjects, multiple Parents per Student, Platform staff roles, Docker, Redis, Kubernetes, S3 Storage, WebSockets, Microservices, or external search engines are introduced as capability; exclusions appear as binding rules (FUT-01) not features. |
| Flow A / Flow B separation | Passed — Subscription and Billing sections (§16–§17) cover Flow A only; Payment section (§18) enforces non-conflation; Student/Parent surfaces are mapped to Flow B status only. |
| Invariants | Passed — every `31_Master_Index.md` §11.3 invariant is carried in this catalog: isolation (BR-003), global Student account/no duplicates (BR-001/BR-022), one Group per Teacher (BR-002), Parent read-only/one Parent (BR-004/BR-020), Archive (BR-005), immutable Audit Log with all ten events (BR-006), history (BR-007/BR-014), flow separation and Enrollment-duration billing (BR-008/BR-009/BR-015/BR-019), web only (BR-017), one Teaching Subject (BR-016), Homework formats (BR-021), Attendance methods (BR-010), Question Bank privacy/types (BR-011/BR-012), Teacher Staff scoping (BR-013), Lesson privacy (BR-018). |
| Terminology | Passed — canonical terms only (Teacher Workspace, Educational Grade, Teaching Subject, Group, Pricing Type, Enrollment, Archive, Audit Log, Dynamic QR Code, ID Card, Question Bank, Bubble Sheet, Student Switcher, Lesson, Billable Student, Billing Cycle, Homework, Flow A, Flow B); "Class", "Course", "Delete", "sub-teacher" appear only as prohibited examples; "tenant" used only in architecture context. |
| Required sections | Passed — all 28 requested sections are present in the requested order: Document Purpose; Business Rules Philosophy; Authentication; User Registration; Teacher; Student; Parent; Super Admin; Educational Grade; Group; Attendance; Homework; Exam; Bubble Sheet; Lesson; Subscription; Billing; Payment; Reporting; Notification; File Management; Search; Security; Data Retention; Audit Log; Future Features; Rule Conflict Resolution; Business Rule Change Process. |
| Required rule fields | Passed — every consolidated rule includes Rule ID, Rule Name, Description, Applies To, Trigger, Expected Behavior, Exceptions (or "None confirmed"), and Related Documents citing the authoritative sources. |
| Scope exclusions of the document set | Passed — no source code, no API definitions/endpoints, no database tables/SQL, no UI implementation, no physical configuration is included. |
| Technology references | Passed — where technology is mentioned, it matches the confirmed baseline (Laravel 12, PHP 8.3, React 19, TypeScript, Vite, Tailwind CSS, MySQL 8, Laravel Sanctum, Gates & Policies with Custom RBAC, File Cache, Database Queue, Database session driver, Laravel Public Storage, Laravel Scheduler with Cron Jobs, SMTP, Apache/LiteSpeed, cPanel Shared Hosting) and is used only to restate confirmed rules. |
| Governance registration | Passed — this document carries a Document Scope with scope exclusions and this closing consistency review per `31_Master_Index.md` §13.5 and §14; corresponding registrations were applied in `31_Master_Index.md` §15 and `04_Project_Structure.md` §8. |

---

*End of document. **REVISION 1.0** — This file is the consolidated authoritative reference for all business rules of the Unified Education Platform Version 1. It invents nothing and cites everything: `00_Project_Context.md` remains the Single Source of Truth. Docs before code; consistency over convenience; Archive — never delete.*


## Confirmed Audit Clarifications

The following confirmed clarifications are owned by `00_Project_Context.md` §9.9 (BR-023 through BR-025). They do not alter Flow A, Archive, Audit Log, or the external-payment boundary.

- **Parent workflow:** a Parent creates an account through Parent registration and submits a Parent–Student link request. The Teacher responsible for the Student’s active Enrollment approves or rejects it. Approval is allowed only when the Student has no other linked Parent; it creates read-only access. The Parent may request unlinking, and that Teacher approves the unlink. Request, approval, rejection, and unlinking are Audit Log events; historical records remain preserved.
- **Per Lesson Flow B:** a billable lesson is a Lesson completed for a Group while the Student has an active Enrollment in that Group. The obligation is recorded on completion at the Group’s recorded Price. Drafting, scheduling, publishing, viewing, Attendance, or assignment alone is not billable; one Student has at most one obligation for the same completed lesson. The Platform records payment status only, and payment remains outside the Platform.
- **Group transfer:** Flow A enrollment duration is accumulated across Groups of the same Teacher in the same Billing Cycle; a transfer does not reset the more-than-15-calendar-days test. Flow B obligations remain tied to the Group and completed Lesson that created them.
