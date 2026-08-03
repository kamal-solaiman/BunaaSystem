# 00 — Project Context

## Unified Education Platform

> **This document is the official Single Source of Truth for Version 1.**
> Every other document, design, schema, API, and line of code must remain consistent with what is written here. If a conflict is ever discovered, this document wins.
>
> **Status: FINAL** — This document is frozen. No new features will be added. Future scope must be documented in separate files.
>
> **Scope:** Project vision, architecture, business model, business rules, terminology, and high-level concepts. Detailed screen behavior and functional specifications belong to `02_Software_Requirements.md`.

---

### Document Control

| Field | Value |
|-------|-------|
| Document | `AI_DOCS/00_Project_Context.md` |
| Revision | **2.0 FINAL** |
| Type | **FROZEN** — official Single Source of Truth for Version 1 |
| Status | **FINAL — no new features; future scope documented in later files** |
| Author | Senior Software Architect / Technical Lead |
| Product Owner | Project founder (source of all CONFIRMED statements) |
| Established | 2026-07-28 |
| Last revised | 2026-07-28 (Revision 2.0 FINAL — see §21) |

### Statement Status Convention

Every statement in this document carries exactly one status. Nothing is silently assumed:

| Status | Meaning |
|--------|---------|
| **CONFIRMED** | Stated explicitly by the Product Owner. Binding. Changes only through a documented decision. |
| **PROPOSED** | Architect's recommendation. Working default, awaiting Product Owner approval. |
| **PENDING** | Unknown or ambiguous. Blocked on a Product Owner answer. No code may harden against it. |

Traceability IDs used throughout: **BR-xxx** (business rules), **Q-xxx** (open questions), **D-xxx** (architecture decisions).

---

## 1. Purpose of This Document

1. Define the **official Single Source of Truth** for the Unified Education Platform Version 1.
2. Consolidate **all confirmed decisions** about the project in one authoritative place.
3. Serve as the consistency anchor for the canonical document set (`01`–`12`) and for all implementation.
4. Provide onboarding context sufficient for any engineer or AI session to continue the project without loss of prior decisions.

> **DOCUMENT STATUS: FINAL**
> This document is now frozen. No new features will be added here. Future features and post-V1 scope must be documented in separate files.

---

## 2. Project Identification

| Field | Value | Status |
|-------|-------|--------|
| Project name | **Unified Education Platform** (working title) | CONFIRMED |
| Product category | SaaS educational platform | CONFIRMED |
| Commercial model | Monthly Subscription paid by **Teachers** | CONFIRMED |
| **Platform scope — Version 1** | **Web Application only.** A native mobile application is a future phase; every V1 capability (including daily-QR attendance scanning) is delivered through the web application. | CONFIRMED |
| Architecture | **Multi-Tenant architecture** — each Teacher Workspace is a completely isolated tenant | CONFIRMED |
| Roles | 5: Super Admin, Teacher, Teacher Staff, Student, Parent | CONFIRMED |
| **Not** | An online course marketplace; Teachers do **not** sell courses | CONFIRMED |

---

## 3. Background & Problem Statement (CONFIRMED)

Today, one Student typically studies with **several Teachers**, and **each Teacher uses a different educational platform**. From the Student's and Parent's perspective this fragmentation means:

- **Different login credentials** per platform
- **Different QR codes** per platform
- **Different attendance systems** per platform
- **Different homework** per platform
- **Different exams** per platform

The cumulative result is a **poor user experience**: duplicated accounts, scattered records, no unified schedule, and no single place to see a Student's complete learning picture.

---

## 4. Product Vision & Core Solution (CONFIRMED)

**One platform. One account per Student. One account per Parent. Many isolated Teacher Workspaces.**

The Unified Education Platform replaces the per-Teacher platform sprawl with a single system where:

1. **Every Student has ONE account** — under it, they may study with multiple Teachers. Attendance, homework, exams, and Subscription status exist **separately per Teacher** inside that single account.
2. **Every Parent has ONE account** — under it, they monitor multiple Students.
3. **All Teachers share the same platform but NEVER share data.** Each Teacher operates a **completely isolated Teacher Workspace**. No Teacher can see another Teacher's data of any kind.
4. **Lesson videos are private per Teacher.** A Teacher may upload lesson videos **exclusively for their own Students**.

### 4.1 Explicit Non-Goals (CONFIRMED — equally binding as goals)

1. The platform is **NOT an online course marketplace**.
2. Teachers **do NOT sell courses** on the platform.
3. There is **no course discovery/browsing** across Teachers, and no mechanism by which one Teacher's content reaches another Teacher's Students.

### 4.2 Version 1 Scope Exclusions (CONFIRMED)

The following are explicitly **out of scope for Version 1**:

1. **Native mobile application** — V1 is Web Application only (BR-017).
2. **Online payment gateways** — V1 records payment status only; actual payments are handled outside the platform (BR-019).
3. **Notifications** — push notifications, email notifications, and SMS notifications are out of scope for V1.
4. **Multiple Teaching Subjects per Teacher** — V1 supports exactly one Teaching Subject per Teacher account (BR-016).

---

## 5. Business Model

### 5.1 Subscription Mechanics (CONFIRMED)

- The platform is **Subscription-based**.
- **Teachers pay monthly.**
- The Subscription price **depends on the number of Billable Students**:

```
Monthly Subscription = Billable Students × Price Per Student
```

**Billing Period (CONFIRMED):**
- The billing cycle **starts on the first day of every calendar month**.
- The billing cycle **ends on the last day of the same month**.
- A new billing cycle **begins automatically** on the first day of the next month.

**Billable Student Rule (CONFIRMED — BR-008):**
- A Student becomes **Billable** based on **enrollment duration only**.
- If a Student remains enrolled in a Teacher's Group for **more than 15 calendar days** during the billing cycle, the Student is **Billable**.
- Students enrolled for **15 days or less** are **NOT counted**.
- **Attendance is NOT used** for this calculation.
- **Login activity is NOT used** for this calculation.

**Pricing** is owned by the Super Admin (BR-015). Flat price vs. volume tiers: PENDING (Q-013).

### 5.2 The Two Distinct Money Flows (CONFIRMED)

The specification describes **two separate financial flows that must never be conflated**:

| Flow | Payer → Payee | Description | Managed In |
|------|---------------|-------------|------------|
| **Flow A — Platform Subscription** | Teacher → Platform (Super Admin) | Monthly Subscription per §5.1. This is the SaaS revenue. | Subscriptions / Billing (Super Admin) |
| **Flow B — Student fees** | Student (or Parent) → Teacher | Fees owed to a Teacher, derived from the **Group's Price and Pricing Type** (Monthly / Per Lesson). Tracked by the platform on the Teacher's behalf. | Group pricing, Teacher Payments reports, Parent "Payments" view |

### 5.3 Version 1 Payment Handling (CONFIRMED — BR-019)

- **Teacher Subscription payments (Flow A) are handled outside the platform** in Version 1.
- **Student fee payments (Flow B) are handled outside the platform** in Version 1.
- The platform **only records payment status** — it does not process transactions.
- **Online payment gateways are out of scope** for Version 1.
- Future versions may integrate payment gateways; this will be a separate decision.

---

## 6. User Roles (CONFIRMED)

Five roles exist. Detailed permission matrices are owned by `08_RBAC.md`; this section captures confirmed role definitions and boundaries only.

### 6.1 Super Admin

**Owns the platform.** Platform-level scope only — does not operate inside Teacher Workspaces.

- Manages **Teachers**, **Subscriptions** (Flow A), **pricing**, and **platform settings**.
- Views **global reports**. Content-visibility boundary PENDING (Q-012; proposed default: aggregates/finances only, D-005).

### 6.2 Teacher

- Operates **one completely isolated Teacher Workspace** — cannot access another Teacher's data, under any circumstance (BR-003).
- Manages, strictly within that workspace: **Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Users, Settings** (the Homework module is explicit in the role specification — CONFIRMED).
- Teaches **exactly ONE Teaching Subject** per account (§8, BR-016). The subject is selected during registration and **cannot be changed** after account creation.

### 6.3 Teacher Staff

- **Created by the Teacher**; exists only inside that Teacher Workspace.
- Examples: **Secretary, Assistant, Accountant**.
- Holds **only permissions assigned by the Teacher** (BR-013). Permission-model granularity PENDING (Q-011).

### 6.4 Student

- Has **only ONE account** (BR-001) and **may study with multiple Teachers**.
- Belongs to **only ONE Group per Teacher** at any time (BR-002).
- Has **separate attendance, homework, exams, and Subscription status per Teacher**.
- **Student Registration Methods (BR-022):**
  - **Method 1:** The Student registers their own account.
  - **Method 2:** The Teacher creates the Student account manually.
- If the Teacher creates the account, the Student can later activate and use the same account.
- **Duplicate Student accounts are NOT allowed** — both methods create only one Student account.

### 6.5 Parent

- Has **ONE account** and may **monitor multiple Students** (BR-020).
- **Read-only access everywhere**; sees **only linked Students** (BR-004).
- **Version 1 supports exactly ONE Parent account per Student** — a Student cannot have multiple Parent accounts linked simultaneously.
- The **Student Switcher** in the Parent Panel allows switching between linked Students.

---

## 7. Product Surfaces — High-Level (CONFIRMED)

Module inventories only. Detailed screen behavior, workflows, and edge cases are specified in `02_Software_Requirements.md` (to be authored) — they are deliberately **not** duplicated here.

### 7.1 Teacher Panel

Navigation: `Dashboard · Educational Grades · Groups · Students · Attendance · Exams · Homework · Lessons · Reports · Users · Settings`

Confirmed essentials:

- **Educational Grades:** Teacher-created levels (e.g., *First Preparatory, Second Preparatory, First Secondary*).
- **Groups:** each Group belongs to one Educational Grade and carries **Name, Schedule, Price, and Pricing Type (Monthly / Per Lesson)**.
- **Students:** Teacher can **register a new Student**, **assign an existing Student**, **search Students**, and **move Students between Groups** (history preserved — BR-007).
- **Attendance — three methods (BR-010):** (1) **dynamic QR Code, generated daily**, displayed for the class and scanned by the Student through the web application; (2) printed **ID card** scanned by a QR scanner; (3) **manual** entry by the Teacher.
- **Exams:** built from the Teacher's **private Question Bank** — question types: **Multiple Choice, True/False, Essay, Bubble Sheet** (BR-011).
- **Reports:** attendance, homework, exam results, payments, and Student performance.
- **Users:** internal Teacher Staff accounts with assigned permissions.
- **Settings:** Teacher profile, center information, phone numbers, address.

### 7.2 Student Panel

Navigation: `Dashboard · My Schedule · Homework · Exams · Lessons · Subscriptions · Settings`

Boundary: one login; all content **partitioned per Teacher**; Lessons only from the Student's own Teachers; Subscriptions shows per-Teacher Flow B status.

### 7.3 Parent Panel

Navigation: `Dashboard · Student Switcher · Homework · Attendance · Exams · Teachers · Payments`

Boundary: **read-only everywhere**; content only for **linked Students**; Teachers view shows the Teachers of linked Students; Payments shows Flow B records.

---

## 8. Teaching Subjects (CONFIRMED)

1. **Each Teacher account represents exactly ONE Teaching Subject** (BR-016).
2. The Teaching Subject is selected **only once during Teacher registration**.
3. The Teaching Subject **CANNOT be changed** after the Teacher account is created.
4. Examples of Teaching Subjects: **Mathematics, Physics, Chemistry, Biology, Arabic, English**.
5. The selected Teaching Subject **belongs to the Teacher Workspace**.
6. **Version 1 does NOT support multiple Teaching Subjects** under the same Teacher account.
7. If a Teacher wants to teach another subject, a **separate Teacher account must be created**.
8. **Teaching Subjects are independent from Educational Grades** — a Teaching Subject is not bound to any specific Educational Grade; the two are separate academic-structure concepts.

---

## 9. Canonical Business Rules

Numbered and binding. Referenced as `BR-xxx` from every document and from code.

### 9.1 Identity & Tenancy

| ID | Rule | Status |
|----|------|--------|
| **BR-001** | A Student has exactly **one global account** and **may study with multiple Teachers**. Per-Teacher data (attendance, homework, exams, Subscription status) is partitioned per Teacher. | CONFIRMED |
| **BR-002** | A Student belongs to **only ONE Group per Teacher** at any time. Group moves close one enrollment period and open another (BR-007). | CONFIRMED |
| **BR-003** | **Teacher data is completely isolated using a Multi-Tenant architecture.** Teachers cannot see each other's data — no exceptions. Enforced at the data layer (every Teacher Workspace row carries the Teacher's identity; every query is workspace-scoped) and at the authorization layer. | CONFIRMED |
| **BR-004** | A Parent sees **only linked Students** and has **read-only** access everywhere. | CONFIRMED |
| **BR-022** | **Student Registration supports two methods:** (1) Student self-registration, (2) Teacher creates the account manually. If Teacher creates the account, the Student can later activate and use it. **Duplicate accounts are NOT allowed** — both methods create only one Student account. | CONFIRMED |

### 9.2 Academic Structure

| ID | Rule | Status |
|----|------|--------|
| **BR-016** | **Each Teacher account represents exactly ONE Teaching Subject.** The subject is selected during registration and **CANNOT be changed** after account creation. V1 does NOT support multiple subjects per Teacher; a separate account is required for each subject. Teaching Subjects are **independent from Educational Grades**. | CONFIRMED |
| **BR-017** | **Version 1 is delivered as a Web Application only.** No native mobile application in V1; all V1 interactions (including daily-QR attendance scanning) run in the web application. | CONFIRMED |

### 9.3 Lifecycle, History & Audit

| ID | Rule | Status |
|----|------|--------|
| **BR-005** | **No permanent deletion is allowed — Archive must be used instead.** Applies to all records, by all actors, everywhere. Governed by the Archive Policy (§11). | CONFIRMED |
| **BR-006** | **Every important action is recorded in the Audit Log.** Governed by the Audit Log Policy (§10) — explicit event catalog included there. | CONFIRMED |
| **BR-007** | **Student transfers preserve historical attendance, homework, exams, and grades.** History is never moved, deleted, or rewritten by structural changes. | CONFIRMED |
| **BR-014** | **Historical data is never deleted and must always remain available** — reports and history queries include archived records (clearly indicated). | CONFIRMED |

### 9.4 Money — Flow A (Platform Subscription)

| ID | Rule | Status |
|----|------|--------|
| **BR-008** | **Billable Student calculation is based on enrollment duration only.** A Student is Billable if enrolled in a Teacher's Group for **more than 15 calendar days** during the billing cycle. Students enrolled ≤ 15 days are not counted. Attendance and login activity are NOT used. Formula: `Monthly Subscription = Billable Students × Price Per Student`. | CONFIRMED |
| **BR-015** | **Pricing is owned by the Super Admin** (price per Student; tiers if adopted — Q-013). Historical invoices keep the price as of their period. | CONFIRMED |
| **BR-019** | **V1 payments are handled outside the platform.** The platform only records payment status. Online payment gateways are out of scope for V1. | CONFIRMED |

### 9.5 Money — Flow B (Teacher → Student fees)

| ID | Rule | Status |
|----|------|--------|
| **BR-009** | Every Group carries a **Price** and **Pricing Type** (`Monthly` or `Per Lesson`). Student fee obligations derive from Group enrollment. V1 records payment status only — actual payments are handled outside the platform (BR-019). | CONFIRMED |

### 9.6 Classroom Operations

| ID | Rule | Status |
|----|------|--------|
| **BR-010** | Attendance supports **three methods**: (1) **dynamic QR Code generated daily**, scanned by the Student through the web application; (2) printed static **ID card** scanned by a QR scanner; (3) **manual** entry by the Teacher. | CONFIRMED |
| **BR-011** | The **Question Bank is Teacher-owned and private**; question types: **Multiple Choice, True/False, Essay, Bubble Sheet**; Exams are composed only from the owning Teacher's bank. **Bubble Sheet** is an electronic exam simulating traditional paper bubble sheets — Students answer by selecting bubbles on screen; automatic grading is supported. | CONFIRMED |
| **BR-012** | Exam definitions, attempts, and grades are **workspace-scoped** (Student × Teacher). Students and Parents see per-Teacher partitioned views; Teachers never see other Teachers' results. | CONFIRMED (derived from BR-003) |
| **BR-013** | **Teacher Staff** exist only within their creating Teacher's workspace and hold only Teacher-assigned permissions. Granularity PENDING (Q-011). | CONFIRMED |

### 9.7 Content Ownership

| ID | Rule | Status |
|----|------|--------|
| **BR-018** | **Lesson videos are Teacher-owned and private.** A Teacher may upload videos exclusively for their own Students; no cross-Teacher access exists. | CONFIRMED |
| **BR-021** | **Homework supports Text, Image, and PDF only.** Video homework is NOT supported in Version 1. | CONFIRMED |

### 9.8 Parent Relationships

| ID | Rule | Status |
|----|------|--------|
| **BR-020** | **Version 1 supports exactly ONE Parent account per Student.** One Parent account may be linked to multiple Students. The Parent Panel includes a Student Switcher for navigation between linked Students. | CONFIRMED |

---

## 9.9 Confirmed Workflow Clarifications

| ID | Rule | Status |
|----|------|--------|
| **BR-023** | **Parent account workflow:** a Parent creates an account by Parent registration. The Parent submits a Parent–Student link request; the Teacher responsible for the Student’s active Enrollment approves or rejects it. Approval creates the read-only link only when the Student has no other linked Parent. The Parent may request unlinking; the same responsible Teacher approves the unlink. Rejection, approval, and unlinking preserve history and are recorded in the Audit Log. | CONFIRMED |
| **BR-024** | **Flow B — Per Lesson Billing:** a billable lesson is a Lesson completed for a Group while the Student has an active Enrollment in that Group. A Per Lesson fee obligation is recorded when that lesson is completed, at that Group’s recorded Price. A lesson is not billable merely because it is drafted, scheduled, published, viewed, attended, or assigned; each Student may receive at most one obligation for the same completed lesson. The Platform records status only; payment remains outside the Platform. | CONFIRMED |
| **BR-025** | **Same-Teacher Group transfer billing:** for Flow A, Enrollment duration is accumulated across all Groups belonging to the same Teacher during one Billing Cycle; a transfer does not reset the more-than-15-calendar-days test. Flow B obligations remain tied to the Group and lesson that created them; history is preserved. | CONFIRMED |

## 10. Audit Log Policy (CONFIRMED)

The Audit Log is a first-class, platform-wide subsystem. The following is explicit — not aspirational.

### 10.1 Events That MUST Be Recorded

Every one of the following events is recorded, without exception, across all roles and all surfaces:

1. **Create** — creation of any record.
2. **Update** — modification of any record.
3. **Archive** — every archival action (§11).
4. **Restore** — every restoration of an archived record.
5. **Login** — every successful and failed authentication.
6. **Permission Change** — any change to a Teacher Staff user's granted permissions.
7. **Attendance Change** — recording or modifying any attendance entry, by any method.
8. **Exam Modification** — creating, editing, publishing, or archiving exams and questions.
9. **Homework Modification** — creating, editing, grading, or archiving homework.
10. **Subscription Change** — Subscription lifecycle events: invoice issued, marked paid, grace, read-only enforcement, reactivation.

### 10.2 What Each Audit Log Entry Contains (PROPOSED record shape)

- **Actor:** user ID and role (Teacher Staff actions are attributed to the staff user, never to the Teacher).
- **Context:** Teacher Workspace (for workspace-scoped events) or platform scope.
- **Event:** one of the §10.1 event types plus the affected entity type and ID.
- **Payload:** before/after snapshot of changed fields.
- **Origin:** timestamp (server time), IP address and device/client information.

### 10.3 Audit Log Properties

1. **Append-only and immutable** — Audit Log entries are never edited or deleted (BR-005, BR-014 apply to the log itself).
2. **Transactional guarantee (PROPOSED):** the audit entry is written in the same database transaction as the action it describes — an action cannot succeed without its audit record.
3. **Scoped visibility (PROPOSED):** a Teacher sees their own workspace's Audit Log; the Super Admin sees platform-scope events (Subscription changes, logins, administration).
4. **Retention:** permanent.

---

## 11. Archive Policy (CONFIRMED)

Archive replaces delete everywhere (BR-005). Archived records have the following explicit properties:

1. **Never appear in normal searches.**
2. **Never appear in active dropdown lists** (pickers, selectors, assignment lists).
3. **Remain available in reports** — historical and reporting queries include archived records, clearly indicated (BR-014).
4. **Can be restored by authorized users** — the restoring actor and action are recorded in the Audit Log (§10.1, event 4).
5. **Never lose historical relationships** — archival never detaches, rewrites, or re-points a record's history; enrollments, grades, attendance, homework, and payment history remain linked exactly as recorded (BR-007).

Additional properties:

6. **No hard delete exists anywhere in the system** — not for Teachers, not for Teacher Staff, not for the Super Admin.
7. Archival cascades are defined per entity in `06_Database_Design.md` (to be authored); archiving a container (e.g., a Group) never archives its historical records.

---

## 12. Architecture Principles (Summary)

Full definition in `03_System_Architecture.md`. The principles that make the confirmed rules *structural*:

1. **Multi-Tenant Architecture (CONFIRMED).** Teacher data is completely isolated using a Multi-Tenant architecture: each Teacher Workspace is a tenant; every workspace-owned row carries the Teacher's identity; queries are workspace-scoped; no cross-tenant foreign keys; per-tenant constraints enforce rules such as BR-002.
2. **One identity, stacked roles (PROPOSED mechanics).** One global user account per person; Teacher, Teacher Staff, Parent-link, and Student-enrollment roles attach to that account contextually.
3. **Immutable Subscription snapshots (PROPOSED mechanics for BR-008).** Monthly usage is materialized into invoice snapshots that never mutate; corrections are adjustment records.
4. **Transfer-safe enrollment periods (PROPOSED mechanics for BR-007).** Enrollments are time-bounded periods; historical records reference the period and structure as of recording time.
5. **API-first REST (CONFIRMED).** REST API architecture with Laravel Sanctum authentication; versioning and envelope conventions are detailed in `10_API_Design.md`.
6. **Security baseline (PROPOSED).** Server-side authorization on every request; signed short-lived QR tokens; rate limiting on login/scan/submit; signed media URLs; secrets server-side only.
7. **Private media (PROPOSED).** Lesson videos in private storage with signed playback; per-Teacher quota; archived lessons stop playing but are retained (Archive Policy).

---

## 13. Technology Stack (CONFIRMED — D-001 resolved)

| Layer | Technology |
|-------|------------|
| Backend | **Laravel** |
| Frontend | **React** |
| Database | **MySQL** |
| Architecture | **REST API** |
| Authentication | **Laravel Sanctum** |
| V1 platform scope | **Web Application only** (BR-017) |

Environment, tooling, and infrastructure specifics belong to `04_Project_Structure.md` and `26_Deployment_Plan.md` (to be authored).

---

## 14. Localization & Regional Considerations — Language CONFIRMED; Timezone/Currency PENDING (Q-015)

- **CONFIRMED:** Arabic is the default language, English is fully supported, and the application automatically supports RTL/LTR. Future languages are supported. Per-Teacher timezone and platform-level display currency remain PENDING.
- Target market/country — awaiting Product Owner confirmation.

---

## 15. Open Questions Register

Resolved questions are archived in §15.2 — never deleted. Only genuinely unresolved items remain in §15.1.

### 15.1 Open

| ID | Question | Proposed Default |
|----|----------|------------------|
| Q-005 | Non-payment enforcement | 7-day grace → Teacher Workspace read-only; Students keep read access; nothing auto-archives |
| Q-010 | Lesson video hosting/protection | Private storage; signed short-lived playback URLs; streaming-only; per-Teacher quota |
| Q-011 | Teacher Staff permission granularity | Fixed capability-flag catalog per module; saveable named presets |
| Q-012 | Super Admin content visibility | Aggregates/finances/metadata only; no browsing of Teacher-private content |
| Q-013 | Flat price or volume tiers | Flat price per Student at launch; tier-ready engine |
| Q-015 | Timezone/currency | Language is confirmed: Arabic (default), English (fully supported), automatic RTL/LTR, and future languages supported. Per-Teacher timezone and platform-level currency remain PENDING. |

### 15.2 Resolved (archive)

| ID | Resolution |
|----|-----------|
| Q-001 | **Technology stack confirmed:** Laravel, React, MySQL, REST API, Laravel Sanctum (§13, D-001). |
| Q-002 / Q-003 | **Billable Student rule confirmed:** based on **enrollment duration only** — Students enrolled > 15 calendar days are Billable. Attendance and login are NOT used (BR-008). |
| Q-004 | **V1 payment handling confirmed:** payments handled outside the platform; platform records status only; gateways out of scope for V1 (BR-019). |
| Q-006 | **Parent relationship confirmed:** V1 supports exactly ONE Parent per Student; one Parent may link to multiple Students; Student Switcher enables navigation (BR-020). |
| Q-007 | **Student registration confirmed:** two methods — (1) Student self-registration, (2) Teacher creates account manually. Student can later activate Teacher-created account. Duplicate accounts NOT allowed (BR-022). QR cadence also confirmed: dynamic QR Code **generated daily** (BR-010). |
| Q-008 | **Homework format confirmed:** Text, Image, PDF only; video homework NOT supported in V1 (BR-021). |
| Q-009 | **Bubble Sheet confirmed:** electronic exam simulating paper bubble sheets; Students select bubbles on screen; automatic grading supported (BR-011). |
| Q-014 | **Teacher Homework module confirmed** — explicit in role specification (§6.2, §7.1). |
| Q-016 | **Teaching Subject confirmed:** exactly ONE per Teacher account; selected at registration; **cannot be changed** after account creation; separate account required for additional subjects (BR-016). |
| Q-017 | **Billing period confirmed:** starts first day of month, ends last day; new cycle begins automatically (§5.1). |
| Q-018 | **Notifications confirmed:** out of scope for V1 (§4.2). |

---

## 16. Decision Log Snapshot

Authoritative log lives in the project decisions documentation. Current entries:

| ID | Decision | Status |
|----|----------|--------|
| D-001 | **Technology stack: Laravel (backend), React (frontend), MySQL (database), REST API, Laravel Sanctum (authentication); V1 = Web Application only** | **CONFIRMED** |
| D-002 | **V1 payments handled outside platform — record status only; gateways out of scope** | **CONFIRMED** |
| D-003 | Subscription invoicing implemented as immutable monthly snapshots | PROPOSED |
| D-004 | Non-payment enforcement ladder (grace → read-only) | PROPOSED |
| D-005 | Super Admin privacy boundary (aggregates only) | PROPOSED |
| D-006 | **Billing period: calendar month (1st to last day); automatic cycle** | **CONFIRMED** |
| D-007 | **Billable Student = enrollment duration > 15 days; attendance/login NOT used** | **CONFIRMED** |
| D-008 | **One Teaching Subject per Teacher account; selected at registration; cannot be changed; separate account for additional subjects** | **CONFIRMED** |
| D-009 | **One Parent per Student in V1; Parent may link multiple Students** | **CONFIRMED** |
| D-010 | **Bubble Sheet = electronic on-screen selection with auto-grading** | **CONFIRMED** |
| D-011 | **Homework formats: Text, Image, PDF only; no video in V1** | **CONFIRMED** |
| D-012 | **Notifications out of scope for V1** | **CONFIRMED** |
| D-013 | **Student registration: two methods (self-registration or Teacher-created); no duplicates allowed** | **CONFIRMED** |

---

## 17. Collaboration Protocol (CONFIRMED — Product Owner instructions)

1. **Architecture and documentation come before code.** Professional documentation is produced first; code follows.
2. **No random code.** Every artifact traces to this document and the canonical doc set.
3. **No silent assumptions.** Ambiguities are PENDING-tagged (each with a proposed default to keep momentum).
4. **Consistency is mandatory.** Every future document and decision must remain consistent with this document; contradictions are defects.
5. **Project context persists** across sessions; prior decisions remain binding unless formally changed.
6. **The project is built gradually** — a phased development plan (`27_Development_Roadmap.md`, to be authored) governs execution order.
7. **Never invent unnecessary features** — scope enters the documents only from the Product Owner or as flagged PROPOSED items awaiting approval.
8. **Technical leadership accountability:** architecture, documentation, database, backend structure, frontend structure, APIs, and development planning are owned by the Architect; product decisions by the Product Owner.

---

## 18. Canonical Document Set (AI_DOCS/)

The authoritative documentation structure (established 2026-07-28). `00` is intentionally unversioned as the living context.

| # | File | Content Scope | State |
|---|------|---------------|-------|
| 00 | `00_Project_Context.md` | **This document** — single source of truth | **Revision 2.0 FINAL** |
| 01 | `01_Project_Vision.md` | Vision, goals, success metrics | Empty — awaiting instruction |
| 02 | `02_Software_Requirements.md` | Functional & non-functional requirements (owns all detailed screen behavior) | Empty — awaiting instruction |
| 03 | `03_System_Architecture.md` | Multi-Tenant architecture, security, media, billing design | Empty — awaiting instruction |
| 04 | `04_Project_Structure.md` | Repository/module structure | Empty — awaiting instruction |
| 05 | `05_User_Flows.md` | Flows per role | Empty — awaiting instruction |
| 06 | `06_Database_Design.md` | ERD & schema design | Empty — awaiting instruction |
| 07 | `08_RBAC.md` | Roles & permissions matrices | Empty — awaiting instruction |
| 08 | `10_API_Design.md` | REST API specification | Empty — awaiting instruction |
| 09 | `13_UI_UX_Guidelines.md` | Design system & UX rules | Empty — awaiting instruction |
| 10 | `27_Development_Roadmap.md` | Phases & milestones | Empty — awaiting instruction |
| 11 | `28_Coding_Standards.md` | Code conventions | Empty — awaiting instruction |
| 12 | `26_Deployment_Plan.md` | Environments, CI/CD, rollout | Empty — awaiting instruction |

**Note:** Earlier provisional drafts in this folder should be removed to prevent conflicting sources of truth. This document is now the **official and final** Project Context.

---

## 19. Canonical Terminology (CONFIRMED)

The following terms are **mandatory** across every document, interface, and conversation. The right-hand column lists wording to avoid.

| Canonical Term | Definition | Avoid / Notes |
|----------------|------------|---------------|
| **Platform** | The Unified Education Platform (this SaaS product). | — |
| **Teacher Workspace** | One Teacher's completely isolated area of the platform. Unit of data isolation. Architecturally: a **tenant** of the Multi-Tenant architecture. | Use "tenant" only in architecture discussions, never in product/UI language. |
| **Educational Grade** | A Teacher-created education level (e.g., *First Preparatory, Second Preparatory, First Secondary*). Contains Groups. | **Never "Class".** |
| **Teaching Subject** | The single subject a Teacher teaches (e.g., *Mathematics, Physics, Chemistry, Biology, Arabic, English*). Selected at registration; **cannot be changed** after account creation. V1 = one subject per Teacher account (BR-016). | Never "Course" (implies marketplace). |
| **Group** | A cohort inside one Educational Grade, with Name, Schedule, Price, and Pricing Type. | — |
| **Pricing Type** | `Monthly` or `Per Lesson` — the fee basis of a Group (Flow B). | — |
| **Student** | A learner with one global account; may study with multiple Teachers (one Group per Teacher). Created via self-registration or by Teacher; duplicates not allowed (BR-022). | — |
| **Parent** | A guardian account; monitors multiple linked Students; read-only. V1: one Parent per Student (BR-020). | — |
| **Teacher Staff** | Internal users created by a Teacher (Secretary, Assistant, Accountant) with assigned permissions. | Never "sub-teacher". |
| **Super Admin** | The platform owner role (§6.1). | — |
| **Subscription** | The Teacher's monthly platform Subscription (Flow A). | Do not confuse with Flow B fee status. |
| **Flow A / Flow B** | Flow A: Teacher → Platform Subscription. Flow B: Student/Parent → Teacher fees (Group pricing). | — |
| **Enrollment** | The link between a Student and one Group (and therefore one Teacher), modeled as a time-bounded period. | — |
| **Archive** | Soft-deletion replacing all deletion — governed by the Archive Policy (§11). | **Never "Delete".** |
| **Audit Log** | Append-only record of every important action — governed by the Audit Log Policy (§10). | — |
| **Dynamic QR Code** | QR Code **generated daily** for attendance; displayed for the class; scanned by the Student through the web application. | — |
| **ID Card** | Printed QR card carried by the Student; scanned by a QR scanner device. | — |
| **Question Bank** | Teacher-owned private repository of questions (Multiple Choice, True/False, Essay, Bubble Sheet). | — |
| **Bubble Sheet** | Electronic exam simulating traditional paper bubble sheets. Students select bubbles on screen; automatic grading supported. | — |
| **Student Switcher** | Parent-panel control for switching between linked Students. | — |
| **Lesson** | A video uploaded by a Teacher for their own Students; private to the Teacher Workspace (BR-018). | Never "Course". |
| **Billable Student** | A Student enrolled in a Teacher's Group for **more than 15 calendar days** during the billing cycle. Calculation is based on enrollment duration only — attendance and login are NOT used (BR-008). | — |
| **Billing Cycle** | Calendar month: starts on the 1st, ends on the last day; new cycle begins automatically. | — |
| **Homework** | Assignment supporting Text, Image, and PDF formats only. Video homework is NOT supported in V1 (BR-021). | — |

---

## 20. Success Definition (PROPOSED)

The platform succeeds when a Student studying with five Teachers uses **one login, one schedule, one homework list, one exam list, one Subscriptions view** — while each of the five Teachers experiences the platform as if it were built **exclusively for them**, and the Parent sees all of their children without ever needing a second account.

---

## 21. Change History

Append-only. Material changes to this document must be logged here.

| Date | Revision | Author | Change |
|------|----------|--------|--------|
| 2026-07-28 | 1.0 | Architect | Initial authoring — consolidated full project context from Product Owner specification, kickoff analysis, canonical document set establishment, open questions, and decision log snapshot. |
| 2026-07-28 | 1.1 | Architect | Product Owner confirmations applied: stack confirmed (Laravel, React, MySQL, REST API, Laravel Sanctum) and D-001 resolved; V1 scope confirmed as **Web Application only** (BR-017); dynamic QR Code confirmed as **generated daily** (BR-010); 15-day Subscription rule reaffirmed without additions; Multi-Tenant architecture confirmed as the isolation mechanism (BR-003, §12.1). New **Teaching Subjects** section (§8, BR-016). New explicit **Audit Log Policy** (§10) and **Archive Policy** (§11). Terminology unified under canonical terms (§19): Teacher Workspace, Educational Grade, Teaching Subject, Group, Student, Parent, Archive, Audit Log, Subscription. Screen-level detail compressed out of §7 (owned by Software Requirements); duplicated role matrix removed (owned by RBAC document); open questions Q-001, Q-002, Q-003, Q-014 resolved and archived (§15.2). No confirmed requirement removed; no new features invented. |
| 2026-07-28 | 1.2 | Architect | Quality review pass. Added: BR-018 (Lesson video ownership — was stated in §4 but lacked BR number). Added missing glossary entries: Lesson, Active Student, Billable Student. Clarified Architecture Principle #5 (REST API is CONFIRMED, not partially PROPOSED). No contradictions found; no confirmed requirements changed. |
| 2026-07-28 | 1.3 | Architect | Major confirmation pass — 10 Product Owner decisions applied. **Teaching Subject:** BR-016 updated to ONE subject per Teacher; selected at registration; changeable in Settings; separate account for additional subjects. **Billing:** billing cycle confirmed as calendar month (1st to last day); automatic new cycle. **Billable Student:** BR-008 updated — based on enrollment duration ONLY (>15 days); attendance/login NOT used. **Parent:** BR-020 added — V1 supports ONE Parent per Student; Parent may link multiple Students. **Payment:** BR-019 added — V1 payments handled outside platform; record status only; gateways out of scope. **Notifications:** confirmed out of scope for V1 (§4.2). **Homework:** BR-021 added — Text, Image, PDF only; no video in V1. **Bubble Sheet:** BR-011 clarified — electronic on-screen exam with auto-grading. Open questions reduced from 11 to 7; resolved questions archived (Q-004, Q-006, Q-008, Q-009, Q-016–Q-018). Decision log updated with D-002, D-006–D-012 CONFIRMED. Glossary terms updated to remove PENDING references. |
| 2026-07-28 | **2.0 FINAL** | Architect | **DOCUMENT FROZEN.** Final corrections applied: (1) **Teaching Subject** — CANNOT be changed after registration; removed all "changeable in Settings" references. (2) **Student Registration** — BR-022 added: two methods (self-registration or Teacher-created); duplicate accounts NOT allowed; Q-007 resolved. (3) **Homework** — confirmed as Text, Image, PDF only (already in BR-021). (4) **Final cleanup** — removed outdated Q-004 reference from §14; updated glossary; removed provisional drafts note. (5) **Document status set to FINAL** — no new features will be added; future scope in separate files. Open questions reduced to 6. D-013 added (Student registration). This document is now the official Single Source of Truth for Version 1. |

---

*End of document. **REVISION 2.0 FINAL** — This file is the official Single Source of Truth for the Unified Education Platform Version 1. Docs before code; consistency over convenience; Archive — never delete.*
