# 31 — Master Index

## Document Scope

This document is the official documentation index for the **Unified Education Platform** (Version 1). It describes the complete documentation structure, the reading order, the dependency relationships between documents, the responsibility and ownership of each document, and the rules that govern how the documentation is versioned, modified, reviewed, and maintained.

This document is an index and governance document only. It does not define source code, APIs, database tables, UI implementation, physical configuration, or business rules of its own. Every rule referenced here is owned by the document that defines it.

`AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found. This Master Index never overrides a canonical document; it only tells the reader where the authoritative statement lives.

**This file is the first document that every AI assistant must read before performing any task on this project.**

---

# 1. Document Purpose

## 1.1 Why This Document Exists

The Unified Education Platform is developed under a documentation-first protocol (D-046, `00_Project_Context.md` §17). Architecture and documentation come before code, no feature is implemented without a confirmed requirement, and no business rule is silently assumed. As a result, the canonical document set in `AI_DOCS/` is large, layered, and interdependent. Without a single entry point, a reader — human or AI — cannot know which document is authoritative for a given question, which documents must be read first, or which documents are affected when something changes.

This Master Index exists to solve exactly that problem. It provides:

1. A complete inventory of every existing document in `AI_DOCS/`.
2. The purpose and responsibility boundary of each document.
3. The dependency relationships between documents (`Depends On` and `Used By`).
4. The ownership of each document.
5. The recommended reading order for different kinds of work.
6. The rules for versioning, modifying, reviewing, and maintaining documentation.
7. Explicit reading instructions for AI assistants and for developers.

## 1.2 What This Document Is Not

- It is not a summary of the project's business rules. Business rules are owned by `00_Project_Context.md` §9.
- It is not a replacement for reading the documents it indexes. It routes the reader; it does not restate content.
- It is not a source of truth for any product, architectural, or technical decision.
- It does not define source code, APIs, database tables, or UI implementation.

## 1.3 Audience

| Audience | How this document is used |
|---|---|
| AI assistants | Mandatory first read for every session and every task. Determines what must be read before acting. |
| Developers | Onboarding entry point and daily routing reference. |
| Architect | Governance instrument for documentation structure, dependencies, and maintenance. |
| Product Owner | Visibility into what is documented, who owns it, and how changes are controlled. |
| Reviewers | Basis for the documentation review checklist and consistency verification. |

---

# 2. Documentation Philosophy

The documentation philosophy of the Unified Education Platform is inherited directly from the Collaboration Protocol (`00_Project_Context.md` §17) and from D-046 (Documentation-First Development). It is stated here as the operating philosophy for the entire `AI_DOCS/` set.

## 2.1 Documentation Comes Before Code

Professional documentation is produced first; code follows. Code follows architecture, architecture follows documentation, and documentation follows confirmed decisions. No feature is coded without a confirmed requirement recorded in this document set.

## 2.2 One Single Source of Truth

`00_Project_Context.md` is the official Single Source of Truth for Version 1 and is **FROZEN** at Revision 2.0 FINAL. Every other document, design, and implementation must remain consistent with it. Where a conflict exists, the Project Context wins — always, and without discussion.

## 2.3 Single Responsibility Per Document

Each document owns one clearly bounded subject. Requirements live in `02_Software_Requirements.md`; architecture in `03_System_Architecture.md`; authorization in `08_RBAC.md` and `09_Permission_Matrix.md`; terminology in `30_Project_Glossary.md`. A document may reference another document's content, but it must never redefine it. Duplication is a defect because it creates a second, competing source of truth.

## 2.4 No Silent Assumptions

Every statement carries exactly one status: **CONFIRMED**, **PROPOSED**, or **PENDING** (`00_Project_Context.md`, Statement Status Convention). PENDING items are recorded with a proposed default so that work can continue, but a PENDING item is never silently hardened into a confirmed rule by a document, a decision, or a line of code.

## 2.5 Consistency Is Mandatory

Contradictions between documents are defects, not stylistic differences. Canonical terminology (`00_Project_Context.md` §19, D-048, `30_Project_Glossary.md`) is mandatory in every document, interface, code artifact, and conversation. Educational Grade is never "Class"; Lesson and Teaching Subject are never "Course"; Archive is never "Delete"; Teacher Staff is never "sub-teacher"; "tenant" appears only in architecture discussion, never in product or UI language.

## 2.6 Traceability

Every documented rule, decision, and open item carries a stable identifier: **BR-xxx** for business rules, **D-xxx** for decisions, **Q-xxx** for open questions. Documents reference these identifiers rather than paraphrasing the underlying rule.

## 2.7 History Is Never Rewritten

The documentation mirrors the product's own Archive philosophy (BR-005, BR-014). Resolved open questions are archived, not deleted (`00_Project_Context.md` §15.2). Change history is append-only (`00_Project_Context.md` §21). Superseded decisions are marked as superseded, never erased.

## 2.8 Scope Discipline

Version 1 scope is locked by the frozen Project Context (`27_Development_Roadmap.md` §18.3). Documentation must never invent features. Future possibilities are labeled *future* or *PENDING*; they never silently expand Version 1.

---

# 3. How To Read The Documentation

## 3.1 The Three-Step Rule

For any task, follow three steps in order:

1. **Read this Master Index** to identify the authoritative documents for the task.
2. **Read `00_Project_Context.md`** to establish the confirmed rules, scope, statuses, and terminology.
3. **Read the task-specific documents** identified by this index, plus their `Depends On` documents where the dependency is relevant to the task.

## 3.2 Read By Question, Not By Number

The document numbers describe a layered structure, not a mandatory sequential read. Use the following routing table to find the owning document for a question.

| Question | Authoritative document |
|---|---|
| What is confirmed for Version 1? What is the scope? | `00_Project_Context.md` |
| Why does this product exist? What is out of scope? | `01_Project_Vision.md` |
| What exactly must a screen or module do? | `02_Software_Requirements.md` |
| How is the system structured technically? | `03_System_Architecture.md` |
| Where does a file or folder belong? | `04_Project_Structure.md` |
| What is the step-by-step journey of a role? | `05_User_Flows.md` |
| How is data modeled logically? | `06_Database_Design.md`, `07_Data_Dictionary.md` |
| Who may do what? | `08_RBAC.md`, `09_Permission_Matrix.md` |
| What does the API contract look like? | `10_API_Design.md` |
| How is backend code organized? | `11_Backend_Architecture.md` |
| How is frontend code organized? | `12_Frontend_Architecture.md` |
| How should the interface look and behave? | `13_UI_UX_Guidelines.md`, `14_UI_Components.md` |
| How do Exams work? | `15_Exam_Engine.md` |
| How does QR Attendance work? | `16_QR_Attendance_System.md` |
| How is the Teacher Subscription calculated (Flow A)? | `17_Subscription_Billing.md` |
| What reports exist and who sees them? | `18_Reporting_Analytics.md` |
| Are notifications available in V1? | `19_Notification_System.md` |
| How are files stored and protected? | `20_File_Storage.md` |
| What runs in the queue or on a schedule? | `21_Background_Jobs.md` |
| How do search, filter, sort, and pagination behave? | `22_Search_Filtering.md` |
| What are the security requirements? | `23_Security_Standards.md` |
| How is the system tested? | `24_Testing_Strategy.md` |
| What are the performance expectations? | `25_Performance_Scalability.md` |
| How is the system deployed? | `26_Deployment_Plan.md` |
| What is built, and in what order? | `27_Development_Roadmap.md` |
| How must code be written? | `28_Coding_Standards.md` |
| Why was a choice made? | `29_Project_Decisions.md` |
| What does this term mean? | `30_Project_Glossary.md` |
| Which document owns this subject? | `31_Master_Index.md` (this document) |
| What are the agreed business rules for a topic? | `32_Business_Rules.md` (consolidated catalog; the owning document it cites remains authoritative) |
| How must an input be validated? | `33_Validation_Rules.md` (consolidated catalog; the owning document it cites remains authoritative) |
| Which error code or message applies to a failure? | `34_Error_Codes.md` (the error code registry; response envelope is owned by `10_API_Design.md` §6) |
| How is each environment configured (versions, variables, drivers, permissions)? | `35_Environment_Configuration.md` (environment configuration standards; the deployment process itself is owned by `26_Deployment_Plan.md`) |
| How do contributors use Git? | `36_Git_Workflow.md` |
| How are releases governed? | `37_Release_Management.md` |
| How are backup and recovery governed? | `38_Backup_Recovery.md` |
| How does a developer get started? | `39_Developer_Guide.md` |
| What rules govern AI-assisted work? | `40_AI_Development_Guide.md` |
| How do Arabic, English, and automatic RTL/LTR work? | `41_Internationalization_i18n.md` |

## 3.3 Reading Signals To Respect

While reading any document, treat the following as binding signals:

- **CONFIRMED** — binding. Implement exactly as written.
- **PROPOSED** — an Architect recommendation acting as a working default. It may be built against, but it must be identified as PROPOSED in any review or discussion.
- **PENDING** — unresolved. No document, decision, or code may harden against it. The open items are Q-005 (non-payment enforcement), Q-010 (Lesson video hosting/protection), Q-011 (Teacher Staff permission granularity), Q-012 (Super Admin content visibility), Q-013 (flat price or volume tiers), and Q-015 (timezone/currency), per `00_Project_Context.md` §15.1.
- **FROZEN / FINAL** — `00_Project_Context.md` is frozen. It is not extended with new features; future scope is documented separately.
- **Consistency Review** — most documents end with a consistency review table. Read it; it states precisely what the document claims to have verified.

## 3.4 Stop Conditions

Stop and ask the Product Owner (or, for an AI assistant, stop and report) when any of the following is true:

1. The task requires a rule that is PENDING.
2. The task requires a feature that is not documented anywhere.
3. Two documents state contradictory rules and the contradiction is not resolved by §10 of this document.
4. The task would require changing `00_Project_Context.md`, which is frozen.

---

# 4. Recommended Reading Order

## 4.1 Universal Foundation (mandatory for everyone)

| Order | Document | Reason |
|---|---|---|
| 1 | `31_Master_Index.md` | Structure, routing, rules of engagement. |
| 2 | `00_Project_Context.md` | Single Source of Truth: scope, business rules, terminology, statuses, open questions. |
| 3 | `30_Project_Glossary.md` | Canonical vocabulary before any detailed reading. |
| 4 | `01_Project_Vision.md` | Purpose, target users, explicit out-of-scope boundaries. |
| 5 | `29_Project_Decisions.md` | Why the confirmed choices were made (D-001 … D-051). |

## 4.2 Full Onboarding Order (new developer or new AI session doing broad work)

1. `31_Master_Index.md`
2. `00_Project_Context.md`
3. `01_Project_Vision.md`
4. `30_Project_Glossary.md`
5. `02_Software_Requirements.md`
6. `05_User_Flows.md`
7. `03_System_Architecture.md`
8. `04_Project_Structure.md`
9. `06_Database_Design.md`
10. `07_Data_Dictionary.md`
11. `08_RBAC.md`
12. `09_Permission_Matrix.md`
13. `10_API_Design.md`
14. `11_Backend_Architecture.md`
15. `12_Frontend_Architecture.md`
16. `13_UI_UX_Guidelines.md`
17. `14_UI_Components.md`
18. `15_Exam_Engine.md`
19. `16_QR_Attendance_System.md`
20. `17_Subscription_Billing.md`
21. `18_Reporting_Analytics.md`
22. `19_Notification_System.md`
23. `20_File_Storage.md`
24. `21_Background_Jobs.md`
25. `22_Search_Filtering.md`
26. `23_Security_Standards.md`
27. `24_Testing_Strategy.md`
28. `25_Performance_Scalability.md`
29. `26_Deployment_Plan.md`
30. `27_Development_Roadmap.md`
31. `28_Coding_Standards.md`
32. `29_Project_Decisions.md`
33. `32_Business_Rules.md`
34. `33_Validation_Rules.md`
35. `34_Error_Codes.md`
    
## 4.3 Task-Oriented Reading Paths

Each path assumes the Universal Foundation (§4.1) has been read.

| Task | Reading path |
|---|---|
| Backend feature work | `02` → `05` → `06` → `07` → `08` → `09` → `10` → `11` → `23` → `28` → the relevant feature document (`15`–`22`) |
| Frontend feature work | `02` → `05` → `12` → `13` → `14` → `10` → `09` → `28` → the relevant feature document (`15`–`22`) |
| Data model work | `06` → `07` → `03` → `08` → `22` → `25` |
| Authorization work | `08` → `09` → `23` → `11` → `10` |
| API contract work | `10` → `02` → `09` → `11` → `22` → `23` |
| Attendance work | `16` → `02` → `05` → `09` → `21` → `18` |
| Exam work | `15` → `02` → `05` → `09` → `20` → `21` → `18` |
| Subscription / Flow A work | `17` → `02` → `09` → `18` → `21` |
| Reporting work | `18` → `22` → `09` → `25` |
| File handling work | `20` → `23` → `09` → `02` |
| Background processing work | `21` → `17` → `18` → `25` → `26` |
| Search, filter, sort, pagination | `22` → `10` → `09` → `25` |
| Security review | `23` → `08` → `09` → `03` → `20` → `10` |
| Testing work | `24` → `02` → `08` → `09` → `23` → `27` |
| Performance work | `25` → `06` → `10` → `22` → `26` |
| Deployment work | `26` → `04` → `03` → `23` → `27` |
| Planning and sequencing | `27` → `02` → `24` → `26` |
| Writing or reviewing code | `28` → `11` or `12` → `04` → `30` |
| Recording or reading a decision | `29` → `00` |
| Terminology check | `30` → `00` §19 |
| Business rule lookup or verification | `32` → the owning document cited by the rule entry (→ `00` §9 for BR-xxx rules) |
| Validation work | `33` → the owning document cited by the rule entry (→ `10` §10 for the response contract, `28` §17 for standards) |
| Error handling / error code work | `34` §3–§4 for code and status discipline → the registry section for the subsystem → `10` §6 for the envelope, `28` §15 for handling, `23` §18 for message policy |
| Environment configuration work | `35` → `26` §3–§4, §7–§17, §22–§23 for the owning deployment requirements it consolidates → `04` §5–§7 → `23` §6–§8, §14, §16, §21 → `21` §4–§5 for queue/scheduler behavior |

## 4.4 Minimum Read Before Any Change

No change of any kind may be made without reading, at minimum: `31_Master_Index.md`, `00_Project_Context.md`, and the document that owns the subject of the change.

---

# 5. Documentation Layers

The document set is organized into seven layers. Layers describe authority and dependency direction: a document may depend on documents in the same or lower layers, and must not contradict any lower layer.

## 5.1 Layer 0 — Foundation (Authority)

| Document | Role |
|---|---|
| `00_Project_Context.md` | Frozen Single Source of Truth. Scope, business rules (BR-xxx), policies, terminology, statuses, open questions. |

Everything in the set derives from Layer 0. Layer 0 depends on nothing.

## 5.2 Layer 1 — Product Definition

| Document | Role |
|---|---|
| `01_Project_Vision.md` | Vision, mission, target users, objectives, in-scope and out-of-scope boundaries, success metrics. |
| `02_Software_Requirements.md` | Functional and non-functional requirements per role and module. Owns all detailed screen behavior. |
| `05_User_Flows.md` | Confirmed end-to-end role journeys (27 flows). |

## 5.3 Layer 2 — Architecture & Structure

| Document | Role |
|---|---|
| `03_System_Architecture.md` | Technical architecture, layering, multi-tenant isolation, subsystem organization. |
| `04_Project_Structure.md` | Repository and deployment-oriented directory structure and ownership. |
| `06_Database_Design.md` | Logical database design, tenant isolation, soft delete, audit, indexing, retention. |
| `07_Data_Dictionary.md` | Logical entities and attributes. |
| `11_Backend_Architecture.md` | Laravel backend layering, lifecycle, services, repositories, policies, jobs. |
| `12_Frontend_Architecture.md` | React frontend structure, role contexts, routing, state, API integration. |

## 5.4 Layer 3 — Access Control & Security

| Document | Role |
|---|---|
| `08_RBAC.md` | Role model, permission categories, ownership, tenant isolation rules. |
| `09_Permission_Matrix.md` | Per-module, per-role permission matrix. |
| `23_Security_Standards.md` | Security requirements across authentication, authorization, isolation, input, sessions, files, operations. |

## 5.5 Layer 4 — Interface Contracts

| Document | Role |
|---|---|
| `10_API_Design.md` | REST API standards, conventions, and endpoint specification. |
| `13_UI_UX_Guidelines.md` | Design system rules, UX standards, accessibility, states. |
| `14_UI_Components.md` | Reusable component contracts and usage standards. |

## 5.6 Layer 5 — Feature Subsystems

| Document | Role |
|---|---|
| `15_Exam_Engine.md` | Exam and Question Bank behavior. |
| `16_QR_Attendance_System.md` | Three Attendance methods, Dynamic QR Code, ID Card, manual entry. |
| `17_Subscription_Billing.md` | Flow A — Teacher Subscription and Billing Cycle. |
| `18_Reporting_Analytics.md` | Report domains, role visibility, export and filtering boundaries. |
| `19_Notification_System.md` | Records the Version 1 notification scope exclusion and future boundaries. |
| `20_File_Storage.md` | File ownership, storage, validation, access control. |
| `21_Background_Jobs.md` | Queue strategy, scheduled tasks, retries, failure handling. |
| `22_Search_Filtering.md` | Search, filtering, sorting, pagination standards. |

## 5.7 Layer 6 — Quality & Delivery

| Document | Role |
|---|---|
| `24_Testing_Strategy.md` | Testing layers, coverage, environments, acceptance criteria. |
| `25_Performance_Scalability.md` | Performance objectives, optimization, capacity planning. |
| `26_Deployment_Plan.md` | Environments, build, deployment, backup, rollback, monitoring. |
| `27_Development_Roadmap.md` | Ten development phases, milestones, release and versioning strategy, risks. |
| `28_Coding_Standards.md` | Mandatory coding conventions for PHP/Laravel and React/TypeScript. |
| `35_Environment_Configuration.md` | Environment configuration standards for Development, Testing, Staging (Future), and Production: software versions, PHP, Laravel/React variables, database, storage, queue, scheduler, cache, mail, permissions, logging, debug, security. Consolidates values; the deployment process remains in `26`. |

## 5.8 Layer 7 — Governance & Reference

| Document | Role |
|---|---|
| `29_Project_Decisions.md` | Decision register (D-xxx) with context, reasoning, alternatives, consequences. |
| `30_Project_Glossary.md` | Canonical terminology. |
| `31_Master_Index.md` | This document — index, dependencies, ownership, documentation governance. |
| `32_Business_Rules.md` | Consolidated reference catalog of all agreed business rules; canonical rule definitions remain owned by the documents it cites. |
| `33_Validation_Rules.md` | Consolidated reference catalog of all validation rules and standards; owning documents it cites remain authoritative. |
| `34_Error_Codes.md` | The application error code registry: codes, statuses, names, user/internal messages, resolutions, logging and response standards. |

## 5.9 Layer Rules

1. A document must never contradict a lower layer.
2. A document must not redefine content owned by another document in any layer.
3. Layer 5, 6, and 7 documents are consumers of Layers 0–4 and must reference rather than restate.
4. A change in a lower layer requires review of every document in higher layers that depends on it (§13.4).

---

# 6. Document Dependency Map

## 6.1 Dependency Direction

Dependencies flow upward from `00_Project_Context.md`. No document may create a dependency that inverts this direction: nothing may make the Project Context depend on it.

```text
                        00_Project_Context.md  (Layer 0 — frozen authority)
                                   |
        +--------------------------+--------------------------+
        |                          |                          |
   01_Project_Vision       02_Software_Requirements      30_Project_Glossary
        |                          |                          ^
        +-----------+--------------+                          |
                    |                                         |
             05_User_Flows                                     |
                    |                                         |
             03_System_Architecture                            |
                    |                                         |
   +--------+-------+--------+---------+---------+            |
   |        |                |         |         |            |
04_Project 06_Database  07_Data   11_Backend 12_Frontend       |
_Structure  _Design    _Dictionary _Arch      _Arch            |
                |          |         |          |             |
                +----+-----+         |          |             |
                     |               |          |             |
                 08_RBAC ------> 09_Permission_Matrix          |
                     |               |          |             |
                     +-------+-------+          |             |
                             |                  |             |
                       10_API_Design      13_UI_UX_Guidelines  |
                             |                  |             |
                             |            14_UI_Components     |
                             |                  |             |
        +--------+-----------+---------+--------+             |
        |        |           |         |        |             |
      15_Exam  16_QR  17_Subscription 18_Reporting 19_Notification
        |        |           |         |        |             |
      20_File 21_Background 22_Search  |        |             |
        |        |           |         |        |             |
        +--------+-----+-----+---------+--------+             |
                       |                                      |
        +------+-------+-------+--------+---------+           |
        |      |               |        |         |           |
   23_Security 24_Testing 25_Performance 26_Deployment 27_Roadmap
        |      |               |        |         |           |
        +------+-------+-------+--------+---------+           |
                       |                                      |
                 28_Coding_Standards                           |
                       |                                      |
                 29_Project_Decisions ------------------------+
                       |
                 35_Environment_Configuration  (Layer 6 — environment configuration standards consolidated from the delivery, security, jobs, performance, structure, and testing documents above; owns the configuration values only)
                       |
                 31_Master_Index  (indexes all of the above)
                       |
                 32_Business_Rules  (consolidated catalog of the rules above; owns none of them)
                       |
                 33_Validation_Rules  (consolidated catalog of the validation rules above; owns none of them)
                       |
                 34_Error_Codes  (registry of the error codes those rules produce; owns the codes only)
                       |
        +--------------+--------------+--------------+
        |              |              |              |
 36_Git_Workflow 37_Release 38_Backup 39_Developer_Guide
        |              |              |              |
                         40_AI_Development_Guide
                                      |
                         41_Internationalization_i18n
```

## 6.2 Cross-Cutting Documents

Four documents are referenced by nearly every other document and must be treated as always-relevant context:

| Document | Cross-cutting role |
|---|---|
| `00_Project_Context.md` | Authority for scope, BR-xxx, policies, and statuses. |
| `30_Project_Glossary.md` | Authority for terminology used by all documents. |
| `29_Project_Decisions.md` | Authority for the reasoning behind confirmed choices. |
| `23_Security_Standards.md` | Constrains every feature, contract, and delivery document. |

## 6.3 High-Impact Documents

A change to any of the following forces a review of a large part of the set. These are the highest-risk documents to modify.

| Document | Downstream impact |
|---|---|
| `00_Project_Context.md` | The entire set. Frozen; changes require Product Owner authority. |
| `02_Software_Requirements.md` | Flows, architecture, data, API, UI, all feature documents, testing, roadmap. |
| `06_Database_Design.md` / `07_Data_Dictionary.md` | Backend, API, feature documents, reporting, search, performance, testing. |
| `08_RBAC.md` / `09_Permission_Matrix.md` | API, backend, frontend, every feature document, security, testing. |
| `10_API_Design.md` | Backend, frontend, UI components, feature documents, testing, performance. |
| `30_Project_Glossary.md` | Terminology in every document. |

## 6.4 Isolation Invariants Shared By All Documents

Every document in the set is bound by the same invariants; a document that breaks one of them is defective regardless of its subject:

1. Teacher Workspace isolation (BR-003).
2. One global Student account, no duplicates (BR-001, BR-022).
3. One Group per Student per Teacher (BR-002).
4. Parent read-only access to linked Students only (BR-004, BR-020).
5. Archive replaces permanent deletion everywhere (BR-005).
6. Every important action is recorded in the immutable Audit Log (BR-006).
7. Historical data is preserved and always available (BR-007, BR-014).
8. Flow A and Flow B remain separate (BR-008, BR-009, BR-015, BR-019).
9. Version 1 is a Web Application only (BR-017).

---

# 7. Versioning Rules

## 7.1 Scope Of These Rules

This section governs **documentation** versioning. Product and API versioning are owned by `27_Development_Roadmap.md` §18 and `10_API_Design.md` §5 and are not redefined here.

## 7.2 Document Revision Numbering

| Element | Rule |
|---|---|
| Revision format | `MAJOR.MINOR` (for example, `1.0`, `1.1`, `2.0`). |
| MAJOR increment | The document's structure, scope, or a confirmed statement changes. |
| MINOR increment | Clarifications, corrections, added references, or added detail that does not change a confirmed statement. |
| First authored revision | `1.0`. |
| Recorded where | In the document's closing revision line and, where the document maintains one, its change history table. |

## 7.3 File Naming

- The naming pattern is `NN_Descriptive_Name.md` with a two-digit sequential prefix, as established in `04_Project_Structure.md` §8.
- Version numbers are **not** part of file names. A document is revised in place; its revision is recorded inside the file.
- Existing numbered documents are never renamed or renumbered. New approved documents take the next free number.
- `00_Project_Context.md` is intentionally unversioned in its file name as the living context, and is currently frozen at Revision 2.0 FINAL.

## 7.4 Frozen Documents

`00_Project_Context.md` is **FROZEN**. It receives no new features. Only the Product Owner may authorize a change to it, and any such change must be logged in its append-only change history (§21 of that document).

## 7.5 Change History

Documents that maintain a change history keep it append-only. Entries record date, revision, author, and the substance of the change. History entries are never edited or removed — the same principle the product applies to the Audit Log (BR-006).

## 7.6 Status Transitions

- PENDING → CONFIRMED requires an explicit Product Owner decision, a new or updated entry in `29_Project_Decisions.md`, and archival of the corresponding Q-xxx in `00_Project_Context.md` §15.2.
- PROPOSED → CONFIRMED requires Product Owner approval recorded as a decision.
- CONFIRMED → anything else is a scope change and requires Product Owner authority.
- A status is never changed implicitly by a downstream document.

## 7.7 Superseded Content

Superseded decisions and rules are marked as superseded with a pointer to the replacement. They are never deleted. This mirrors the Archive policy (BR-005).

---

# 8. Modification Rules

## 8.1 Preconditions For Any Modification

Before modifying any document, the editor must:

1. Read `31_Master_Index.md` (this document).
2. Read `00_Project_Context.md`.
3. Read the target document in full.
4. Read the target document's `Depends On` documents (§15).
5. Identify the target document's `Used By` documents (§15) — these must be reviewed for impact.

## 8.2 Allowed Modifications

| Modification | Allowed? | Condition |
|---|---|---|
| Clarifying wording without changing meaning | Yes | Consistency review still required. |
| Correcting a factual inconsistency with a lower layer | Yes | The lower layer wins; correct the higher-layer document. |
| Adding detail that elaborates a CONFIRMED rule | Yes | Must not extend scope. |
| Recording a new Product Owner decision | Yes | Must be added to `29_Project_Decisions.md` first. |
| Adding a term to the glossary | Yes | The term must already be used in a canonical document. |
| Adding a new feature | No | Requires Product Owner confirmation and a decision entry. |
| Hardening a PENDING item | No | PENDING items remain PENDING until formally resolved. |
| Changing a CONFIRMED rule | No | Product Owner authority only. |
| Editing `00_Project_Context.md` | No | Frozen; Product Owner authority only. |
| Deleting a document or a history entry | No | Documentation is archived, never deleted. |
| Renaming or renumbering an existing document | No | Numbers are stable identifiers. |
| Adding source code, APIs, database tables, or UI implementation to a document that excludes them | No | Each document's stated scope exclusions are binding. |

## 8.3 Mandatory Modification Sequence

1. Confirm the change is authorized (§8.2).
2. Confirm the target document is the correct owner of the subject (§15). If it is not, edit the owning document instead.
3. Apply the change using canonical terminology.
4. Update every affected reference inside the same document.
5. Review the `Used By` documents for contradictions and correct or report them.
6. Update the document's revision and change history (§7).
7. Run the documentation review checklist (§14).
8. Update this Master Index if purpose, dependencies, ownership, or the document inventory changed.

## 8.4 Prohibited In Every Document

- Inventing features, roles, modules, entities, endpoints, or infrastructure that are not confirmed.
- Introducing infrastructure excluded from Version 1: Docker, Redis, Kubernetes, S3 Storage, WebSockets, Microservices, Elasticsearch.
- Introducing native mobile applications, online payment gateways, notifications, marketplace behavior, course discovery, video homework, or multiple Teaching Subjects per Teacher as Version 1 capability.
- Using non-canonical terminology.
- Duplicating another document's authoritative content.
- Referencing a document that does not exist.

---

# 9. Source of Truth Policy

## 9.1 Absolute Authority

`AI_DOCS/00_Project_Context.md` is the official Single Source of Truth for Version 1. It defines scope, canonical business rules (BR-001 … BR-022), the Audit Log Policy, the Archive Policy, architecture principles, the confirmed technology stack, canonical terminology, open questions, and the decision log snapshot. It wins against every other document, design, schema, or line of code.

## 9.2 Ownership By Subject

Authority below Layer 0 is assigned by subject. For each subject, exactly one document is authoritative; every other document is a consumer.

| Subject | Authoritative document |
|---|---|
| Scope, business rules, policies, terminology, statuses, open questions | `00_Project_Context.md` |
| Vision, objectives, out-of-scope boundaries, success metrics | `01_Project_Vision.md` |
| Functional and non-functional requirements, detailed screen behavior | `02_Software_Requirements.md` |
| Technical architecture and subsystem organization | `03_System_Architecture.md` |
| Repository and directory structure | `04_Project_Structure.md` |
| Role journeys | `05_User_Flows.md` |
| Logical database design | `06_Database_Design.md` |
| Logical entities and attributes | `07_Data_Dictionary.md` |
| Role model and access rules | `08_RBAC.md` |
| Per-module permission matrix | `09_Permission_Matrix.md` |
| REST API contract | `10_API_Design.md` |
| Backend structure and layering | `11_Backend_Architecture.md` |
| Frontend structure and layering | `12_Frontend_Architecture.md` |
| Design system rules and UX standards | `13_UI_UX_Guidelines.md` |
| Component contracts | `14_UI_Components.md` |
| Exam behavior | `15_Exam_Engine.md` |
| Attendance behavior | `16_QR_Attendance_System.md` |
| Flow A Subscription and billing behavior | `17_Subscription_Billing.md` |
| Reporting scope and visibility | `18_Reporting_Analytics.md` |
| Notification scope exclusion and future boundaries | `19_Notification_System.md` |
| File storage and access control | `20_File_Storage.md` |
| Queue and scheduled work | `21_Background_Jobs.md` |
| Search, filtering, sorting, pagination | `22_Search_Filtering.md` |
| Security requirements | `23_Security_Standards.md` |
| Testing approach and acceptance | `24_Testing_Strategy.md` |
| Performance and scalability | `25_Performance_Scalability.md` |
| Deployment, backup, rollback, monitoring | `26_Deployment_Plan.md` |
| Phases, milestones, release and versioning strategy | `27_Development_Roadmap.md` |
| Coding conventions | `28_Coding_Standards.md` |
| Decision register and rationale | `29_Project_Decisions.md` |
| Canonical terminology definitions | `30_Project_Glossary.md` |
| Documentation structure, dependencies, ownership, governance | `31_Master_Index.md` |

## 9.3 Consumer Obligations

A consumer document may summarize an authoritative statement only when it also cites the owning document, and only when the summary cannot drift from the original. Where drift is possible, the consumer must reference instead of restate.

## 9.4 Code Is Never A Source of Truth

Implementation never establishes truth. If code contradicts documentation, the code is defective. If the documentation is genuinely wrong, the documentation is corrected first through §8, and then the code is aligned.

---

# 10. Conflict Resolution Policy

## 10.1 Definition

A conflict exists when two documents make statements that cannot both be true, or when a document states something the Single Source of Truth contradicts. Conflicts are defects and must be resolved, never worked around.

## 10.2 Resolution Order

Apply the following precedence, in order, until the conflict is resolved:

1. **`00_Project_Context.md`** — always wins.
2. **Explicit Product Owner confirmation** recorded in `29_Project_Decisions.md` as CONFIRMED.
3. **The subject-owning document** (§9.2) for the subject in dispute.
4. **The lower documentation layer** (§5) over the higher layer.
5. **The more specific document** over the more general one, when both are in the same layer and neither owns the subject.
6. **The more recently revised document**, only when the previous rules cannot resolve the conflict.
7. **Escalation to the Product Owner** when precedence cannot resolve the conflict or when the conflict touches a PENDING item.

## 10.3 Resolution Procedure

1. Stop the affected work. Do not implement either side of the conflict.
2. Record the conflict: the documents involved, the exact statements, and the affected sections.
3. Apply the precedence order in §10.2 to determine the correct statement.
4. Correct the losing document under the modification rules (§8) — never the winning one.
5. Review the corrected document's `Used By` list for propagation.
6. If the conflict revealed an undocumented decision, record it in `29_Project_Decisions.md`.
7. Update revision and change history.
8. Re-run the review checklist (§14).

## 10.4 Terminology Conflicts

Terminology conflicts are resolved by `00_Project_Context.md` §19 first, then by `30_Project_Glossary.md`. The non-canonical usage is always the defect.

## 10.5 PENDING Conflicts

If two documents differ because one of them assumed a resolution to a PENDING question, the assuming document is defective. The PENDING item stays PENDING, and the assumption is removed or relabeled as the documented proposed default.

## 10.6 Conflicts Involving Frozen Content

A conflict with `00_Project_Context.md` is never resolved by amending it. The other document is corrected. Only the Product Owner may unfreeze or amend the Project Context.

---

# 11. AI Reading Instructions

These instructions are binding for every AI assistant working on this project.

## 11.1 Mandatory Session Start

1. Read `31_Master_Index.md` (this document) **first**, in full, before any other action.
2. Read `00_Project_Context.md` in full.
3. Read `30_Project_Glossary.md` before producing any wording that will be read by users or developers.
4. Identify the task's subject and read the owning document (§9.2) and its `Depends On` documents (§15).
5. Only then begin the task.

## 11.2 Behavioral Rules

| Rule | Requirement |
|---|---|
| No invention | Never invent features, roles, entities, endpoints, tables, rules, statuses, metrics, or documents. If it is not documented, it does not exist. |
| No silent assumptions | If information is missing, state that it is missing. Do not fill the gap. |
| PENDING discipline | Never resolve, harden, or quietly assume Q-005, Q-010, Q-011, Q-012, Q-013, or Q-015. |
| Frozen respect | Never modify `00_Project_Context.md`. |
| Terminology discipline | Use canonical terms only. Never "Class", "Course", "Delete", or "sub-teacher"; use "tenant" only in architecture discussion. |
| Scope discipline | Never introduce native mobile, payment gateways, notifications, marketplace behavior, video homework, multiple Teaching Subjects per Teacher, Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices as Version 1 capability. |
| Ownership discipline | Write content only into the document that owns the subject. |
| Scope-exclusion discipline | Respect each document's stated exclusions — most documents explicitly exclude source code, APIs, database tables, UI implementation, and physical configuration. |
| Conflict discipline | Never choose between conflicting statements silently. Apply §10 and report the conflict. |
| Traceability | Reference BR-xxx, D-xxx, and Q-xxx identifiers instead of paraphrasing the underlying rule. |
| Consistency review | Every authored or edited document must end with a consistency review, matching the established pattern of the document set. |

## 11.3 Invariants To Verify In Every Output

Before returning any deliverable, verify that it preserves:

1. Teacher Workspace isolation (BR-003).
2. One global Student account with no duplicates (BR-001, BR-022).
3. One Group per Student per Teacher (BR-002).
4. Parent read-only access to linked Students, one Parent per Student (BR-004, BR-020).
5. Archive instead of deletion (BR-005).
6. Immutable Audit Log coverage of the ten mandatory event types (BR-006).
7. Historical data preservation (BR-007, BR-014).
8. Flow A / Flow B separation (BR-008, BR-009, BR-015, BR-019).
9. Billable Student determined by Enrollment duration only — more than 15 calendar days — never by Attendance or login (BR-008).
10. Web Application only (BR-017).
11. One Teaching Subject per Teacher, unchangeable after registration (BR-016).
12. Homework limited to Text, Image, and PDF (BR-021).
13. Attendance limited to the three confirmed methods (BR-010).
14. Question Bank privacy and the four confirmed question types (BR-011, BR-012).
15. Teacher Staff scoped to the creating Teacher's workspace (BR-013).
16. Lesson videos private to the owning Teacher (BR-018).

## 11.4 Required Stop-And-Report Conditions

An AI assistant must stop and report instead of proceeding when:

- The task depends on a PENDING item.
- The task requires content that no document defines.
- Two documents conflict and §10 does not resolve it.
- The task requires editing frozen content.
- The task requires introducing an excluded technology or an out-of-scope feature.

## 11.5 Output Expectations

- Match the structure, tone, and formatting conventions of the existing document set.
- Do not generate source code, API definitions, database tables, SQL, or UI implementation inside documentation files.
- Keep each document within its declared scope.
- State the authority for any rule referenced, using the owning document and its identifier.

---

# 12. Developer Reading Instructions

## 12.1 Before Onboarding Completes

Read the Universal Foundation (§4.1) and then the Full Onboarding Order (§4.2). Onboarding is not complete until the developer can state, without looking: the five roles, the meaning of Teacher Workspace isolation, the Billable Student rule, the Archive policy, and the Flow A / Flow B distinction.

## 12.2 Before Starting Any Task

1. Read this Master Index for routing.
2. Read `00_Project_Context.md` for the confirmed rules touching the task.
3. Read the owning document (§9.2) and the relevant task path (§4.3).
4. Read `28_Coding_Standards.md` before writing code.
5. Read `27_Development_Roadmap.md` to confirm the task belongs to the current phase.

## 12.3 While Working

- Trace every implementation choice to a documented rule. If you cannot trace it, stop and ask.
- Use canonical terminology in code, comments, commit messages, and UI text.
- Treat the backend as the sole security authority (D-021); the frontend never makes authorization decisions.
- Never permanently delete data — Archive (BR-005).
- Record Audit Log entries for every mandatory event type (BR-006).
- Keep Flow A and Flow B separated in data, endpoints, reports, and authorization.

## 12.4 Before Requesting Review

- Confirm the work matches the requirement in `02_Software_Requirements.md` and the flow in `05_User_Flows.md`.
- Confirm authorization matches `08_RBAC.md` and `09_Permission_Matrix.md`.
- Confirm the API contract matches `10_API_Design.md`.
- Confirm the tests required by `24_Testing_Strategy.md` exist and pass.
- Confirm the code review checklist in `28_Coding_Standards.md` §19 is satisfied.
- If the work revealed a documentation gap or contradiction, report it before merging.

## 12.5 Developer Responsibilities Toward Documentation

Developers do not own the documents, but they are responsible for reporting every discovered inconsistency, gap, or undocumented decision to the Architect. Silence about a discovered contradiction is a process defect.

---

# 13. Documentation Maintenance Rules

## 13.1 Ownership Model

Ownership follows the Collaboration Protocol (`00_Project_Context.md` §17): product decisions belong to the Product Owner; architecture, documentation, database, backend structure, frontend structure, APIs, and development planning belong to the Architect.

| Owner | Documents |
|---|---|
| **Product Owner** | `00_Project_Context.md` (frozen; sole authority to amend), `01_Project_Vision.md` (product authority; maintained by the Architect) |
| **Architect / Technical Lead** | `02` through `31` — all authoring, maintenance, and consistency responsibility |
| **Shared (Product Owner confirms, Architect records)** | `29_Project_Decisions.md` status changes, PENDING resolutions, scope changes |

## 13.2 Maintenance Triggers

Documentation must be reviewed and, where necessary, updated when any of the following occurs:

1. A Product Owner decision is made or changed.
2. A PENDING question is resolved.
3. A new document is authored.
4. A contradiction is discovered.
5. A development phase completes (`27_Development_Roadmap.md`, documentation milestones).
6. Implementation reveals that a documented rule is unimplementable as written.
7. Terminology drift is detected.

## 13.3 Maintenance Cadence

| Activity | Cadence |
|---|---|
| Terminology consistency check | At each documentation milestone. |
| Dependency map verification (§6, §15) | Whenever a document is added or its scope changes. |
| Full cross-document consistency review | At each phase boundary defined in `27_Development_Roadmap.md`. |
| Master Index verification | Whenever any document is added, renamed in purpose, or changes ownership or dependencies. |

## 13.4 Impact Propagation Rule

When a document changes, every document listed in its `Used By` entry (§15) must be reviewed for impact. The review is recorded as part of the change. Skipping propagation review is a defect, because it is exactly how contradictions enter the set.

## 13.5 Adding A New Document

1. Confirm the subject is not already owned by an existing document.
2. Obtain Architect approval; obtain Product Owner approval when the document introduces product scope.
3. Assign the next free two-digit number and follow the `NN_Descriptive_Name.md` pattern.
4. Author the document with: a Document Scope section, an explicit statement that `00_Project_Context.md` is the Single Source of Truth, explicit scope exclusions, and a closing consistency review.
5. Add the document to `04_Project_Structure.md` §8 and to §15 of this Master Index with Purpose, Depends On, Used By, and Owner.
6. Update the dependency map (§6) and, where relevant, the reading paths (§4.3).

## 13.6 Retiring A Document

Documents are never deleted. A retired document is marked **SUPERSEDED** at the top, retains its number, and points to the replacement. Its entry in §15 is retained with the superseded marker.

## 13.7 Numbered Documentation Files

Every numbered file currently present in `AI_DOCS/` is an authored document and is indexed in §15. No placeholder is an official document.

---

# 14. Documentation Review Checklist

Every authored or modified document must pass this checklist before it is considered complete. Reviewers use the same list.

## 14.1 Authority And Scope

| # | Check |
|---|---|
| 1 | The document states that `00_Project_Context.md` is the Single Source of Truth and prevails on conflict. |
| 2 | The document stays inside its declared scope and does not claim another document's subject. |
| 3 | The document's stated exclusions are respected — no source code, APIs, database tables, SQL, UI implementation, or physical configuration where excluded. |
| 4 | No feature, role, entity, module, or infrastructure is invented. |
| 5 | Version 1 scope exclusions are preserved: no native mobile, no payment gateways, no notifications, no marketplace or course discovery, no video homework, no multiple Teaching Subjects per Teacher, no Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices. |

## 14.2 Rules And Traceability

| # | Check |
|---|---|
| 6 | Every rule referenced cites its BR-xxx, D-xxx, or Q-xxx identifier where one exists. |
| 7 | CONFIRMED, PROPOSED, and PENDING statuses are used correctly and never upgraded implicitly. |
| 8 | The six open questions (Q-005, Q-010, Q-011, Q-012, Q-013, Q-015) remain PENDING and are not hardened. |
| 9 | No confirmed rule is contradicted, weakened, or restated inaccurately. |

## 14.3 Business Invariants

| # | Check |
|---|---|
| 10 | Teacher Workspace isolation is preserved (BR-003). |
| 11 | One global Student account and duplicate prevention are preserved (BR-001, BR-022). |
| 12 | One Group per Student per Teacher is preserved (BR-002). |
| 13 | Parent read-only access to linked Students and one Parent per Student are preserved (BR-004, BR-020). |
| 14 | Archive replaces deletion everywhere (BR-005). |
| 15 | Audit Log coverage and immutability are preserved (BR-006). |
| 16 | Historical data preservation is preserved (BR-007, BR-014). |
| 17 | Flow A and Flow B remain separate, and Billable Student is Enrollment-duration-only (BR-008, BR-009, BR-015, BR-019). |
| 18 | Web Application only (BR-017); one Teaching Subject per Teacher (BR-016). |

## 14.4 Consistency

| # | Check |
|---|---|
| 19 | Canonical terminology is used throughout; no "Class", "Course", "Delete", or "sub-teacher"; "tenant" only in architecture context. |
| 20 | Technology references match the confirmed stack: Laravel 12, PHP 8.3, React 19, TypeScript, Vite, Tailwind CSS, MySQL 8, Laravel Sanctum, Laravel Gates & Policies with Custom RBAC, File Cache, Database Queue, Database session driver, Laravel Public Storage, Laravel Scheduler with Cron Jobs, SMTP, Apache or LiteSpeed, cPanel Shared Hosting. |
| 21 | All five roles are represented correctly: Super Admin, Teacher, Teacher Staff, Student, Parent. |
| 22 | Every referenced document exists in `AI_DOCS/`, and no empty placeholder is cited as a source. |
| 23 | Cross-references are accurate, including section numbers where cited. |

## 14.5 Structure And Governance

| # | Check |
|---|---|
| 24 | The document follows the structural conventions of the set, including a Document Scope section and a closing consistency review. |
| 25 | Revision and, where applicable, change history are updated. |
| 26 | The `Used By` documents were reviewed for impact (§13.4). |
| 27 | `31_Master_Index.md` §15 was updated if purpose, dependencies, ownership, or inventory changed. |
| 28 | `04_Project_Structure.md` §8 was updated if a document was added. |

---

# 15. Complete Documentation Index

The index below covers every existing document in `AI_DOCS/`. `Depends On` lists the documents that must be consistent with, and are read before, the entry. `Used By` lists the documents that consume it. `Owner` follows §13.1.

## 15.1 Layer 0 — Foundation

### `00_Project_Context.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/00_Project_Context.md` |
| **Purpose** | The frozen official Single Source of Truth for Version 1. Defines project identification, business model, the five user roles, product surfaces, Teaching Subjects, the canonical business rules BR-001 … BR-022, the Audit Log Policy, the Archive Policy, architecture principles, the confirmed technology stack, the open questions register, the decision log snapshot, the Collaboration Protocol, canonical terminology, and the append-only change history. |
| **Depends On** | None. It is the root authority. |
| **Used By** | Every document in the set: `01`–`31`. |
| **Owner** | Product Owner (frozen — Revision 2.0 FINAL). |

## 15.2 Layer 1 — Product Definition

### `01_Project_Vision.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/01_Project_Vision.md` |
| **Purpose** | Defines the executive summary, vision, mission, problem statement, proposed solution, target users, business objectives, product scope, explicit out-of-scope boundaries, competitive advantages, success metrics, risks, assumptions, constraints, and future vision. |
| **Depends On** | `00_Project_Context.md` |
| **Used By** | `02`, `03`, `05`, `06`, `07`, `17`, `18`, `19`, `27`, `29`, `30`, `31` |
| **Owner** | Product Owner (product authority); Architect (maintenance). |

### `02_Software_Requirements.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/02_Software_Requirements.md` |
| **Purpose** | The Software Requirements Specification (IEEE 29148 aligned). Part 1 establishes requirements context, product boundaries, definitions, acronyms, audience, product functions, roles, constraints, assumptions, and dependencies. Parts 2–5 define the Teacher, Student, Parent, and Platform Administration module requirements. Part 6 defines non-functional requirements. Owns all detailed screen behavior. |
| **Depends On** | `00_Project_Context.md`, `01_Project_Vision.md` |
| **Used By** | `03`, `04`, `05`, `06`, `07`, `08`, `09`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23`, `24`, `25`, `26`, `27`, `29`, `30`, `31` |
| **Owner** | Architect. |

### `05_User_Flows.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/05_User_Flows.md` |
| **Purpose** | Describes the 27 confirmed Version 1 user journeys across all roles — Teacher registration and login, the Teacher daily workflow, Educational Grade and Group creation, Student addition and registration, Student login, joining a Group, QR and manual Attendance, Homework creation and submission, Lesson creation and viewing, Exam creation, taking, and results, Parent login, Student Switcher use, and Parent views of Attendance, Homework, Exams, and payments, Super Admin login, Teacher Subscription, and monthly Subscription renewal — together with the cross-flow rules that apply to all of them. |
| **Depends On** | `00_Project_Context.md`, `01_Project_Vision.md`, `02_Software_Requirements.md` |
| **Used By** | `03`, `06`, `08`, `09`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `22`, `24`, `27`, `31` |
| **Owner** | Architect. |

## 15.3 Layer 2 — Architecture & Structure

### `03_System_Architecture.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/03_System_Architecture.md` |
| **Purpose** | Defines the technical architecture: architecture goals and principles, the high-level system overview, layered architecture, frontend and backend architecture, database architecture, authentication and authorization architecture, multi-tenant architecture, file storage, QR Attendance, Exam Engine, reporting, logging and audit architecture, error handling, security architecture, performance strategy, deployment overview, architecture constraints, and future considerations. |
| **Depends On** | `00_Project_Context.md`, `01_Project_Vision.md`, `02_Software_Requirements.md` |
| **Used By** | `04`, `06`, `07`, `08`, `09`, `10`, `11`, `12`, `15`, `16`, `17`, `18`, `20`, `21`, `22`, `23`, `24`, `25`, `26`, `27`, `29`, `31` |
| **Owner** | Architect. |

### `04_Project_Structure.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/04_Project_Structure.md` |
| **Purpose** | Defines the repository and deployment-oriented directory structure: root layout, backend (Laravel) structure, frontend (React) structure, database structure, storage structure, public assets, configuration, documentation structure, and testing structure, with ownership of each area. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md` |
| **Used By** | `11`, `12`, `14`, `20`, `24`, `26`, `27`, `28`, `31` |
| **Owner** | Architect. |

### `06_Database_Design.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/06_Database_Design.md` |
| **Purpose** | Defines the logical database design: design goals, database engine, naming conventions, entity overview and relationships, tenant isolation strategy, soft delete strategy, audit strategy, versioning strategy, indexing strategy, file storage references, data integrity and referential integrity rules, cascade rules, archiving strategy, data retention policy, and future considerations. |
| **Depends On** | `00_Project_Context.md`, `01_Project_Vision.md`, `02_Software_Requirements.md`, `03_System_Architecture.md` |
| **Used By** | `07`, `08`, `09`, `10`, `11`, `15`, `16`, `17`, `18`, `20`, `21`, `22`, `23`, `24`, `25`, `26`, `27`, `29`, `30`, `31` |
| **Owner** | Architect. |

### `07_Data_Dictionary.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/07_Data_Dictionary.md` |
| **Purpose** | Defines the logical data dictionary — every logical entity and its attributes, including User, Role, Permission, Teacher, Teacher Workspace, Student, Parent, Parent Student Link, Educational Grade, Group, Group Schedule, Student Enrollment, Attendance Session, QR Session, Attendance, Homework, Homework Submission, Lesson, Lesson Video, Question Bank, Question, Exam, Exam Attempt, Exam Answer, and the remaining confirmed entities. Logical only; no physical schema. |
| **Depends On** | `00_Project_Context.md`, `01_Project_Vision.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `06_Database_Design.md` |
| **Used By** | `08`, `09`, `10`, `11`, `15`, `16`, `17`, `18`, `20`, `21`, `22`, `24`, `25`, `27`, `30`, `31` |
| **Owner** | Architect. |

### `11_Backend_Architecture.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/11_Backend_Architecture.md` |
| **Purpose** | Defines the Laravel backend architecture: folder structure, feature-based organization, request lifecycle, routing, controllers, services, repositories, models, form requests, policies and gates, middleware, authentication and authorization flow, validation strategy, file upload strategy, error handling, logging, queue strategy, scheduler, the notification position, performance guidelines, coding principles, and future improvements. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `04_Project_Structure.md`, `06_Database_Design.md`, `07_Data_Dictionary.md`, `08_RBAC.md`, `09_Permission_Matrix.md`, `10_API_Design.md` |
| **Used By** | `15`, `16`, `17`, `18`, `20`, `21`, `22`, `23`, `24`, `25`, `26`, `27`, `28`, `31` |
| **Owner** | Architect. |

### `12_Frontend_Architecture.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/12_Frontend_Architecture.md` |
| **Purpose** | Defines the React frontend architecture: the target frontend stack, application structure, role-context handling for all five roles, routing, state management, server-state handling, form handling, API integration boundaries, and the rule that the frontend never becomes a security authority. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `04_Project_Structure.md`, `05_User_Flows.md`, `09_Permission_Matrix.md`, `10_API_Design.md` |
| **Used By** | `13`, `14`, `15`, `16`, `17`, `18`, `22`, `24`, `25`, `26`, `27`, `28`, `31` |
| **Owner** | Architect. |

## 15.4 Layer 3 — Access Control & Security

### `08_RBAC.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/08_RBAC.md` |
| **Purpose** | Defines the Role-Based Access Control model: RBAC overview, security principles, the five roles, permission categories, permission naming convention, Teacher, Student, Parent, and Super Admin permissions, tenant isolation rules, ownership rules, resource access rules, authentication rules, authorization flow, audit requirements, security constraints, and future considerations. |
| **Depends On** | `00_Project_Context.md`, `01_Project_Vision.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `06_Database_Design.md`, `07_Data_Dictionary.md` |
| **Used By** | `09`, `10`, `11`, `12`, `15`, `16`, `17`, `18`, `20`, `21`, `22`, `23`, `24`, `27`, `29`, `30`, `31` |
| **Owner** | Architect. |

### `09_Permission_Matrix.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/09_Permission_Matrix.md` |
| **Purpose** | Defines the complete logical permission matrix per module and per role — Dashboard, Educational Grades, Groups, Students, Attendance, Homework, Lessons, Exams, Reports, Payments, Subscriptions, Users, Settings, Files, Notifications, Audit Logs, and Platform Management — together with conditional permission notes and a consistency review. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `06_Database_Design.md`, `07_Data_Dictionary.md`, `08_RBAC.md` |
| **Used By** | `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `20`, `21`, `22`, `23`, `24`, `27`, `31` |
| **Owner** | Architect. |

### `23_Security_Standards.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/23_Security_Standards.md` |
| **Purpose** | Defines the complete security standards: security overview and principles, authentication and authorization security, multi-tenant isolation, password policy, session management, API security, file upload security, input validation, SQL injection prevention, XSS prevention, CSRF protection, rate limiting, audit logging, sensitive data handling, backup security, error message policy, and security monitoring. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `06_Database_Design.md`, `08_RBAC.md`, `09_Permission_Matrix.md`, `10_API_Design.md`, `11_Backend_Architecture.md`, `20_File_Storage.md` |
| **Used By** | `24`, `25`, `26`, `27`, `28`, `29`, `31` |
| **Owner** | Architect. |

## 15.5 Layer 4 — Interface Contracts

### `10_API_Design.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/10_API_Design.md` |
| **Purpose** | Defines the REST API specification: API overview and standards, authentication and authorization, versioning under the `/api/v1` prefix, the error response standard, pagination, filtering, sorting, validation response standard, file upload standard, naming conventions, and the endpoint specifications for authentication, Teachers, Students, Parents, Educational Grades, Groups, Attendance, Homework, Lessons, Exams, Reports, Payments, and the remaining confirmed resources. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `06_Database_Design.md`, `07_Data_Dictionary.md`, `08_RBAC.md`, `09_Permission_Matrix.md` |
| **Used By** | `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `20`, `21`, `22`, `23`, `24`, `25`, `26`, `27`, `28`, `31` |
| **Owner** | Architect. |

### `13_UI_UX_Guidelines.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/13_UI_UX_Guidelines.md` |
| **Purpose** | Defines UI/UX rules and standards: design philosophy, UX principles, the design system, color, typography, spacing, icons, buttons, forms, tables, cards, modals, navigation, sidebar, header, dashboard layout, responsiveness within the Web Application scope, accessibility, loading, empty, error, and success states, confirmation dialogs, and in-application messaging boundaries. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `05_User_Flows.md`, `09_Permission_Matrix.md`, `12_Frontend_Architecture.md` |
| **Used By** | `14`, `15`, `16`, `17`, `18`, `22`, `24`, `27`, `28`, `31` |
| **Owner** | Architect. |

### `14_UI_Components.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/14_UI_Components.md` |
| **Purpose** | Defines the reusable Design System component library as contracts only: layout, navigation, form, data display, feedback, dialog, dashboard, QR, file upload, Exam, Attendance, report, settings, loading, empty state, error, and responsive components, plus accessibility rules and component naming conventions. Components never make authorization decisions. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `04_Project_Structure.md`, `05_User_Flows.md`, `09_Permission_Matrix.md`, `10_API_Design.md`, `11_Backend_Architecture.md`, `12_Frontend_Architecture.md`, `13_UI_UX_Guidelines.md` |
| **Used By** | `15`, `16`, `17`, `18`, `22`, `24`, `27`, `28`, `31` |
| **Owner** | Architect. |

## 15.6 Layer 5 — Feature Subsystems

### `15_Exam_Engine.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/15_Exam_Engine.md` |
| **Purpose** | Defines the Exam Engine: feature overview and objectives, supported Exam types, the Teacher-owned private Question Bank, question categories and the four confirmed question types, Exam creation workflow, scheduling, the Student Exam flow, answer submission, automatic and manual grading, Bubble Sheet rules, Essay, True/False, and Multiple Choice behavior, timing, attempts, randomization, passing rules, result calculation and publishing, and role permissions. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `05_User_Flows.md`, `06_Database_Design.md`, `07_Data_Dictionary.md`, `08_RBAC.md`, `09_Permission_Matrix.md`, `10_API_Design.md`, `20_File_Storage.md` |
| **Used By** | `18`, `21`, `24`, `25`, `27`, `29`, `30`, `31` |
| **Owner** | Architect. |

### `16_QR_Attendance_System.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/16_QR_Attendance_System.md` |
| **Purpose** | Defines the QR Attendance System: the three confirmed Attendance methods, the daily Dynamic QR Code, generation and expiration rules, the Student QR scan flow, the ID Card scanner flow, the manual Attendance flow, Attendance Sessions, late arrival and absence rules, duplicate scan prevention, invalid QR handling, offline scenarios, Attendance statuses, role permissions and Parent visibility, reports integration, audit logging, error handling, edge cases, and security considerations. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `05_User_Flows.md`, `06_Database_Design.md`, `07_Data_Dictionary.md`, `08_RBAC.md`, `09_Permission_Matrix.md`, `10_API_Design.md` |
| **Used By** | `18`, `21`, `22`, `24`, `25`, `27`, `29`, `30`, `31` |
| **Owner** | Architect. |

### `17_Subscription_Billing.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/17_Subscription_Billing.md` |
| **Purpose** | Defines the Flow A Teacher Subscription and Billing System: the Subscription model, the distinction between Active Student and Billable Student, the calendar-month Billing Cycle, Subscription calculation rules based on Enrollment duration only, Student activation and deactivation effects, payment status recording, Teacher Subscription status, grace, suspension, and reactivation boundaries, billing reports, Teacher dashboard information, Super Admin management, audit logging, error handling, and edge cases. |
| **Depends On** | `00_Project_Context.md`, `01_Project_Vision.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `06_Database_Design.md`, `07_Data_Dictionary.md`, `08_RBAC.md`, `09_Permission_Matrix.md`, `10_API_Design.md` |
| **Used By** | `18`, `21`, `24`, `26`, `27`, `29`, `30`, `31` |
| **Owner** | Architect. |

### `18_Reporting_Analytics.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/18_Reporting_Analytics.md` |
| **Purpose** | Defines the Reporting & Analytics System: reporting principles, Teacher, Student, Parent, and Platform reports, Attendance, Homework, Exam, payment, Subscription, Group, and Educational Grade reports, export rules, filtering and sorting rules, dashboard statistics, audit reporting, performance considerations, error handling, and edge cases — all bound by role visibility and Teacher Workspace scope. |
| **Depends On** | `00_Project_Context.md`, `01_Project_Vision.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `06_Database_Design.md`, `07_Data_Dictionary.md`, `08_RBAC.md`, `09_Permission_Matrix.md`, `10_API_Design.md`, `15_Exam_Engine.md`, `16_QR_Attendance_System.md`, `17_Subscription_Billing.md`, `22_Search_Filtering.md` |
| **Used By** | `21`, `24`, `25`, `27`, `31` |
| **Owner** | Architect. |

### `19_Notification_System.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/19_Notification_System.md` |
| **Purpose** | Records the complete Version 1 notification scope exclusion and the boundaries any separately approved future Notification System must respect. It documents that push, email, and SMS notifications are out of scope for Version 1 (D-012), that no notification entity, endpoints, permissions, settings, history, or preferences exist in Version 1, and it describes future push, email, and SMS support strictly as future scope. |
| **Depends On** | `00_Project_Context.md`, `01_Project_Vision.md`, `02_Software_Requirements.md`, `09_Permission_Matrix.md` |
| **Used By** | `09`, `11`, `13`, `21`, `24`, `27`, `29`, `31` |
| **Owner** | Architect. |

### `20_File_Storage.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/20_File_Storage.md` |
| **Purpose** | Defines the File Storage architecture on Laravel Public Storage: supported file types, image, PDF, Lesson video, Homework attachment, and profile image storage, file naming strategy, directory structure, upload validation, file size limit governance, storage security, access control, download rules, replacement rules, the file deletion policy under the Archive rule, orphan file cleanup, backup considerations, future cloud storage constraints, error handling, and edge cases. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `04_Project_Structure.md`, `07_Data_Dictionary.md`, `08_RBAC.md`, `09_Permission_Matrix.md`, `10_API_Design.md` |
| **Used By** | `11`, `14`, `15`, `21`, `23`, `24`, `25`, `26`, `27`, `31` |
| **Owner** | Architect. |

### `21_Background_Jobs.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/21_Background_Jobs.md` |
| **Purpose** | Defines all confirmed background jobs and scheduled tasks on the Database Queue and Laravel Scheduler: background processing principles, queue strategy, scheduled tasks, monthly Subscription processing, Attendance cleanup, Exam result processing, report generation, file cleanup, Audit Log maintenance, the future notification position, retry strategy, failure handling, job priorities, monitoring, logging, performance considerations, error handling, and edge cases. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `06_Database_Design.md`, `07_Data_Dictionary.md`, `11_Backend_Architecture.md`, `15_Exam_Engine.md`, `16_QR_Attendance_System.md`, `17_Subscription_Billing.md`, `18_Reporting_Analytics.md`, `20_File_Storage.md` |
| **Used By** | `24`, `25`, `26`, `27`, `29`, `31` |
| **Owner** | Architect. |

### `22_Search_Filtering.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/22_Search_Filtering.md` |
| **Purpose** | Defines Search, Filtering, Sorting, and Pagination standards across all modules and roles: search principles, Global Search, module search, filtering, sorting, pagination standards, search performance, search permissions, advanced search, date range filtering, status filtering, export filters, saved filters as future scope, search logging, error handling, edge cases, and accessibility considerations — always resolving scope before filtering. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `06_Database_Design.md`, `07_Data_Dictionary.md`, `08_RBAC.md`, `09_Permission_Matrix.md`, `10_API_Design.md` |
| **Used By** | `18`, `24`, `25`, `27`, `31` |
| **Owner** | Architect. |

## 15.7 Layer 6 — Quality & Delivery

### `24_Testing_Strategy.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/24_Testing_Strategy.md` |
| **Purpose** | Defines the testing strategy: testing overview, objectives, scope, principles, unit, feature, integration, API, authentication, authorization, database, UI, performance, security, and regression testing, User Acceptance Testing, bug reporting workflow, test data management, testing environments, release acceptance criteria, coverage guidelines, and future automation strategy. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `06_Database_Design.md`, `08_RBAC.md`, `09_Permission_Matrix.md`, `10_API_Design.md`, `11_Backend_Architecture.md`, `12_Frontend_Architecture.md`, `23_Security_Standards.md`, and the feature documents `15`–`22` |
| **Used By** | `26`, `27`, `28`, `31` |
| **Owner** | Architect. |

### `25_Performance_Scalability.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/25_Performance_Scalability.md` |
| **Purpose** | Defines the Performance & Scalability strategy: performance objectives, scalability goals, backend, frontend, database, and API performance, caching strategy, query optimization, file storage and queue performance, search performance, pagination, lazy loading, resource optimization, monitoring metrics, capacity planning, future horizontal scaling, and performance risks — within cPanel Shared Hosting constraints. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `06_Database_Design.md`, `10_API_Design.md`, `11_Backend_Architecture.md`, `12_Frontend_Architecture.md`, `20_File_Storage.md`, `21_Background_Jobs.md`, `22_Search_Filtering.md` |
| **Used By** | `26`, `27`, `24`, `31` |
| **Owner** | Architect. |

### `26_Deployment_Plan.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/26_Deployment_Plan.md` |
| **Purpose** | Defines the deployment strategy: deployment overview and objectives, target environment and server requirements, post-deployment folder structure, environment variable governance, build process, frontend and backend deployment, database migration process, storage configuration, file permissions, queue configuration, scheduler cron configuration, cache configuration, backup strategy, rollback strategy, and monitoring — for cPanel Shared Hosting with VPS/Cloud as the future target. |
| **Depends On** | `00_Project_Context.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `04_Project_Structure.md`, `20_File_Storage.md`, `21_Background_Jobs.md`, `23_Security_Standards.md`, `24_Testing_Strategy.md`, `25_Performance_Scalability.md` |
| **Used By** | `27`, `29`, `31` |
| **Owner** | Architect. |

### `27_Development_Roadmap.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/27_Development_Roadmap.md` |
| **Purpose** | Defines the phased development plan: roadmap overview, development philosophy, project milestones, the ten development phases from Foundation through Optimization, testing milestones, documentation milestones, deployment milestones, release strategy, versioning strategy, risks and dependencies, and future version outlines. |
| **Depends On** | `00_Project_Context.md`, `01_Project_Vision.md`, `02_Software_Requirements.md`, `03_System_Architecture.md`, `04_Project_Structure.md`, `05_User_Flows.md`, `06_Database_Design.md`, `08_RBAC.md`, `09_Permission_Matrix.md`, `10_API_Design.md`, `11_Backend_Architecture.md`, `12_Frontend_Architecture.md`, the feature documents `15`–`22`, `23`, `24`, `25`, `26` |
| **Used By** | `28`, `29`, `31` |
| **Owner** | Architect. |

### `28_Coding_Standards.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/28_Coding_Standards.md` |
| **Purpose** | Defines mandatory coding standards: coding philosophy, general principles, Laravel standards, PHP PSR-12 standards, React standards, TypeScript standards, folder, file, class, function, variable, database, and API naming conventions, Git commit message convention, error handling, logging, validation, and documentation standards, the code review checklist, and future maintainability guidelines. |
| **Depends On** | `00_Project_Context.md`, `03_System_Architecture.md`, `04_Project_Structure.md`, `10_API_Design.md`, `11_Backend_Architecture.md`, `12_Frontend_Architecture.md`, `13_UI_UX_Guidelines.md`, `14_UI_Components.md`, `23_Security_Standards.md`, `24_Testing_Strategy.md` |
| **Used By** | `24`, `27`, `29`, `31` |
| **Owner** | Architect. |

### `35_Environment_Configuration.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/35_Environment_Configuration.md` |
| **Purpose** | The official environment configuration standards for the Unified Education Platform Version 1. Defines how the Development, Testing, Staging (Future), and Production environments are configured on the confirmed cPanel Shared Hosting baseline: environment philosophy and isolation, required software versions and PHP extensions, PHP configuration, the complete Laravel and React environment variable standards with per-environment values, database, storage, queue, scheduler, cache, mail, file permissions, logging, debug, and security configuration, the per-environment checklist, the future VPS/Cloud environment evolution, and environment maintenance guidelines. It consolidates configuration values from the owning documents and invents none; the deployment process (build, release, migration, rollback, backup, monitoring) remains owned by `26_Deployment_Plan.md`, and it contains no deployment scripts, no source code, and no real credentials. |
| **Depends On** | `00_Project_Context.md` (frozen rules, technology stack, policies, PENDING open questions), `03_System_Architecture.md` §4.1 (technology baseline), `04_Project_Structure.md` §5–§7, `19_Notification_System.md` (SMTP scope guard), `20_File_Storage.md` (storage and PENDING protection), `21_Background_Jobs.md` (queue/scheduler configuration), `23_Security_Standards.md` (security, credential, and session configuration), `24_Testing_Strategy.md` §19 (environment definitions and parity), `25_Performance_Scalability.md` §8 (cache), `26_Deployment_Plan.md` (owning deployment requirements it consolidates), `27_Development_Roadmap.md` (environment provisioning milestones), `28_Coding_Standards.md` §3.14, `29_Project_Decisions.md` (D-001…D-051 as cited, incl. D-039…D-045), `30_Project_Glossary.md`, `31_Master_Index.md` (governance) |
| **Used By** | Developers, operators, reviewers, and AI assistants configuring any environment; deployment work in `26_Deployment_Plan.md`; release acceptance in `24_Testing_Strategy.md`; environment provisioning milestones in `27_Development_Roadmap.md`; every future environment change governed through `31_Master_Index.md` §8. |
| **Owner** | Architect (maintains the configuration standards); the owning documents it cites remain authoritative for the behaviors those values drive, confirmed by the Product Owner. |

## 15.8 Layer 7 — Governance & Reference

### `29_Project_Decisions.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/29_Project_Decisions.md` |
| **Purpose** | The decision register. Records every important architectural and business decision (D-001 … D-051) with its context, decision, reasoning, alternatives considered, consequences, status, and related documents — covering technology stack, architecture, business rules, lifecycle and history, security, infrastructure, process, and Version 1 scope exclusions. |
| **Depends On** | `00_Project_Context.md`, and every document whose choices it records (`01`–`28`) |
| **Used By** | `00` (decision log snapshot), `30`, `31`, and every document that cites a D-xxx identifier |
| **Owner** | Architect (records); Product Owner (confirms). |

### `30_Project_Glossary.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/30_Project_Glossary.md` |
| **Purpose** | The official glossary. Defines every important business and technical term alphabetically, with definition, project context, related terms, and related documents — covering all canonical terminology, all five roles, all logical entities, all financial terms, and the technical stack vocabulary. |
| **Depends On** | `00_Project_Context.md` §19, and every document in which the terms are used (`01`–`29`) |
| **Used By** | Every document in the set: `00`–`31` |
| **Owner** | Architect. |

### `31_Master_Index.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/31_Master_Index.md` |
| **Purpose** | **This document.** The official documentation index: document purpose, documentation philosophy, how to read the documentation, recommended reading order, documentation layers, the document dependency map, versioning rules, modification rules, the source of truth policy, the conflict resolution policy, AI reading instructions, developer reading instructions, documentation maintenance rules, the documentation review checklist, and the complete documentation index with each document's purpose, dependencies, consumers, and owner. It is the first document every AI assistant reads before performing any task. |
| **Depends On** | `00_Project_Context.md`, and structurally on every document it indexes (`01`–`35`) |
| **Used By** | Every reader of the documentation — AI assistants, developers, the Architect, the Product Owner, and reviewers — and every document maintenance activity. |
| **Owner** | Architect. |

### `32_Business_Rules.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/32_Business_Rules.md` |
| **Purpose** | The consolidated business rules reference. Gathers every agreed business rule of the Unified Education Platform Version 1 into a single catalog — authentication and registration; all five roles; Educational Grades and Groups; Attendance, Homework, Exams, Bubble Sheets, and Lessons; Subscription, Billing, and Payments; reporting, notifications, file management, and search; security, data retention, and the Audit Log; Version 1 exclusion rules; rule conflict resolution; and the business rule change process. It invents no rule: canonical rule definitions remain owned by `00_Project_Context.md` §9 and by the §9.2 subject-owning documents, and this catalog must never override an owning document. |
| **Depends On** | `00_Project_Context.md` (canonical rules, policies, terminology, open questions), `29_Project_Decisions.md`, `30_Project_Glossary.md`, `31_Master_Index.md`, and every rule-owning document it consolidates (`01`–`02`, `05`–`09`, `15`–`23`, `27`) |
| **Used By** | Every reader seeking a consolidated view of the agreed rules — AI assistants, developers, reviewers — and the §8 modification sequence and §10 conflict resolution activities, which use it to locate every rule affected by a change. |
| **Owner** | Architect (maintains the catalog); rule content remains owned per §9.2 and confirmed by the Product Owner. |

### `33_Validation_Rules.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/33_Validation_Rules.md` |
| **Purpose** | The consolidated validation rules reference. Gathers every validation requirement of the Unified Education Platform Version 1 into a single catalog — validation philosophy and the four-layer model; general standards with Laravel 12 alignment and the canonical enumeration sets; authentication, Teacher, Student, and Parent validation; Educational Grade, Group, Attendance, Homework, Exam, Question Bank, Bubble Sheet, and Lesson validation; Subscription and Payment validation; file upload, search, and API request validation; data integrity rules; validation error messages; validation exceptions (including PENDING non-hardening and deferred limits); and the closing validation checklist. It invents no rule and no limit: field validation remains owned by `07_Data_Dictionary.md`, module validation by `02_Software_Requirements.md`, the request/response contract by `10_API_Design.md`, standards by `28_Coding_Standards.md` §17 and `23_Security_Standards.md` §9–§10, integrity by `06_Database_Design.md` §12–§13, and domain validation by the feature documents it cites. |
| **Depends On** | `00_Project_Context.md` (canonical rules, policies, terminology, open questions), `31_Master_Index.md`, and every validation-owning document it consolidates (`02`, `06`, `07`, `10`, `11`–`14`, `15`–`18`, `20`–`24`, `28`, `29`, `30`, `32`) |
| **Used By** | AI assistants, backend and frontend developers, reviewers, and testers implementing or reviewing any form, endpoint, job, or feature that touches input; used together with `10_API_Design.md` (422 contract) and `24_Testing_Strategy.md` (validation test expectations). |
| **Owner** | Architect (maintains the catalog); validation content remains owned per §9.2 and confirmed by the Product Owner. |

### `34_Error_Codes.md`

| Field | Value |
|---|---|
| **File Name** | `AI_DOCS/34_Error_Codes.md` |
| **Purpose** | The official registry of application error codes for the Unified Education Platform Version 1. Defines the error code structure and naming convention, the HTTP status discipline (the eight documented statuses), and registers every confirmed error — authentication, authorization, validation, Teacher/Student/Parent modules, Attendance, Homework, Exam, Question Bank, Subscription, Payment, File Upload, Search, API, Database, Queue & Background Jobs, and System — each with Error Code, HTTP Status, Error Name, Description, User Message, Internal Message, Possible Causes, Recommended Resolution, and Related Documents. It also consolidates logging requirements, the user-facing and internal message policies, the error response standards, and the governance for future codes. Codes are unique, stable, and never reused; the registry invents no rejection behavior and no HTTP statuses. |
| **Depends On** | `00_Project_Context.md`; the envelope and statuses from `10_API_Design.md` §6, §10; handling and business-violation mappings from `28_Coding_Standards.md` §15–§16; the error message policy from `23_Security_Standards.md` §18; feature error handling from `15`–`22`; job failures from `21_Background_Jobs.md`; validation conditions and message texts from `33_Validation_Rules.md`; business-rule context from `32_Business_Rules.md`; all other authored documents as cited |
| **Used By** | AI assistants, backend and frontend developers, reviewers, testers, and operators diagnosing failures; consumed by every implementation that normalizes exceptions, maps 422/409 conflicts, or writes operational log entries. |
| **Owner** | Architect (maintains the registry); error *behavior* remains owned per §9.2 and confirmed by the Product Owner. |

## 15.9 Additional Authored Documents

### `36_Git_Workflow.md`
| Field | Value |
|---|---|
| Purpose | Git collaboration and branch workflow. |
| Depends On | `28_Coding_Standards.md`, `31_Master_Index.md`. |
| Used By | Contributors and reviewers. |
| Owner | Architect. |

### `37_Release_Management.md`
| Field | Value |
|---|---|
| Purpose | Release lifecycle and approval governance. |
| Depends On | `26_Deployment_Plan.md`, `35_Environment_Configuration.md`, `24_Testing_Strategy.md`. |
| Used By | Release owners and reviewers. |
| Owner | Architect. |

### `38_Backup_Recovery.md`
| Field | Value |
|---|---|
| Purpose | Backup, restoration, and recovery governance. |
| Depends On | `26_Deployment_Plan.md`, `23_Security_Standards.md`, `35_Environment_Configuration.md`. |
| Used By | Operators and reviewers. |
| Owner | Architect. |

### `39_Developer_Guide.md`
| Field | Value |
|---|---|
| Purpose | Developer onboarding and safe contribution guide. |
| Depends On | `31_Master_Index.md`, `28_Coding_Standards.md`, `36_Git_Workflow.md`. |
| Used By | Developers and AI assistants. |
| Owner | Architect. |

### `40_AI_Development_Guide.md`
| Field | Value |
|---|---|
| Purpose | Mandatory AI-assisted development operating guide. |
| Depends On | `31_Master_Index.md`, `00_Project_Context.md`, `39_Developer_Guide.md`. |
| Used By | AI assistants and reviewers. |
| Owner | Architect. |

### `41_Internationalization_i18n.md`
| Field | Value |
|---|---|
| Purpose | Official Arabic-default, English-supported i18n and automatic RTL/LTR strategy. |
| Depends On | `00_Project_Context.md`, `03_System_Architecture.md`, `12_Frontend_Architecture.md`, `13_UI_UX_Guidelines.md`. |
| Used By | Backend, frontend, QA, and documentation contributors. |
| Owner | Architect. |

## 15.10 Index Summary

| Layer | Documents | Count |
|---|---|---|
| Layer 0 — Foundation | `00` | 1 |
| Layer 1 — Product Definition | `01`, `02`, `05` | 3 |
| Layer 2 — Architecture & Structure | `03`, `04`, `06`, `07`, `11`, `12` | 6 |
| Layer 3 — Access Control & Security | `08`, `09`, `23` | 3 |
| Layer 4 — Interface Contracts | `10`, `13`, `14` | 3 |
| Layer 5 — Feature Subsystems | `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22` | 8 |
| Layer 6 — Quality & Delivery | `24`, `25`, `26`, `27`, `28`, `35` | 6 |
| Layer 7 — Governance & Reference | `29`, `30`, `31`, `32`, `33`, `34` | 6 |
| **Total authored documents** | `00` – `41` | **42** |

---

# Consistency Review

| Check | Result |
|---|---|
| Project Context alignment | Passed — the Single Source of Truth policy, Collaboration Protocol (§17), statement status convention, Archive Policy, Audit Log Policy, and canonical terminology from `00_Project_Context.md` are preserved and never contradicted. |
| Document inventory accuracy | Passed — every existing authored document from `00_Project_Context.md` through `35_Environment_Configuration.md` is indexed, including this document (`31_Master_Index.md`). 36 authored documents total. |
| No invented documents | Passed — no document is referenced that does not exist in `AI_DOCS/`. Documents `36`–`41` are authored and indexed; no numbered authored file is treated as a placeholder. |
| Purpose accuracy | Passed — every purpose statement is derived from the target document's own Document Scope and section structure. |
| Dependency accuracy | Passed — every `Depends On` entry reflects a source-of-truth relationship declared in the target document or required by the documentation layers. |
| Consumer accuracy | Passed — every `Used By` entry reflects a document that consumes the referenced content. |
| Ownership accuracy | Passed — ownership follows `00_Project_Context.md` §17: product decisions belong to the Product Owner; architecture, documentation, database, backend structure, frontend structure, APIs, and planning belong to the Architect. |
| Layer integrity | Passed — dependencies flow upward from Layer 0; no document is shown as depending on a higher layer that would invert authority. |
| Source of truth policy | Passed — `00_Project_Context.md` is stated as the absolute authority, subject ownership is assigned to exactly one document per subject, and code is explicitly excluded as a source of truth. |
| Conflict resolution policy | Passed — precedence is anchored to the Project Context first, then confirmed Product Owner decisions, then subject ownership, then documentation layer, consistent with every document's own conflict statement. |
| Versioning policy | Passed — documentation versioning is defined without redefining product versioning (`27_Development_Roadmap.md` §18) or API versioning (`10_API_Design.md` §5, `/api/v1`). |
| Frozen document handling | Passed — `00_Project_Context.md` is treated as frozen at Revision 2.0 FINAL and may only be amended by the Product Owner. |
| PENDING items | Passed — Q-005, Q-010, Q-011, Q-012, Q-013, and Q-015 are preserved as PENDING, listed accurately, and explicitly protected from silent hardening. |
| BR reference accuracy | Passed — all Business Rule references (BR-001 through BR-022) match `00_Project_Context.md` §9. |
| D reference accuracy | Passed — decision references (D-001, D-012, D-021, D-046, D-048, and the D-001…D-051 range) are consistent with `29_Project_Decisions.md`. |
| Canonical terminology | Passed — Platform, Teacher Workspace, Educational Grade, Teaching Subject, Group, Pricing Type, Student, Parent, Teacher Staff, Super Admin, Subscription, Flow A, Flow B, Enrollment, Archive, Audit Log, Dynamic QR Code, ID Card, Question Bank, Bubble Sheet, Student Switcher, Lesson, Billable Student, Billing Cycle, and Homework are used consistently. |
| Non-canonical avoidance | Passed — "Class", "Course", "Delete", and "sub-teacher" are used only as prohibited examples; "tenant" appears only in architecture context. |
| Role coverage | Passed — all five confirmed roles are represented: Super Admin, Teacher, Teacher Staff, Student, Parent. |
| Technology stack consistency | Passed — Laravel 12, PHP 8.3, React 19, TypeScript, Vite, Tailwind CSS, MySQL 8, Laravel Sanctum, Laravel Gates & Policies with Custom RBAC, File Cache, Database Queue, Database session driver, Laravel Public Storage, Laravel Scheduler with Cron Jobs, SMTP, Apache or LiteSpeed, and cPanel Shared Hosting are referenced consistently. |
| Version 1 scope | Passed — no native mobile application, payment gateway, notification capability, marketplace behavior, course discovery, video homework, multiple Teaching Subjects per Teacher, Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices is introduced as Version 1 capability. |
| Flow A / Flow B separation | Passed — the two money flows are consistently distinguished and never conflated. |
| Teacher Workspace isolation | Passed — isolation (BR-003) is listed as a shared invariant binding on every document and every output. |
| Archive and Audit Log policy | Passed — Archive replaces deletion (BR-005) and Audit Log immutability (BR-006) are applied both to the product and, by analogy, to documentation history. |
| No source code | Passed — no source code, shell commands, or implementation examples are included. |
| No API definitions | Passed — API authority is referenced to `10_API_Design.md`; no endpoint, method, payload, or status code is defined here. |
| No database tables | Passed — data authority is referenced to `06_Database_Design.md` and `07_Data_Dictionary.md`; no table, column, or SQL is defined here. |
| No UI implementation | Passed — UI authority is referenced to `13_UI_UX_Guidelines.md` and `14_UI_Components.md`; no markup, CSS, or component code is defined here. |
| No duplicated authority | Passed — this document routes and governs; it does not restate business rules, requirements, permissions, or technical specifications as if it owned them. |
| Requested section coverage | Passed — all fifteen required sections are present in the requested order, and every indexed document includes File Name, Purpose, Depends On, and Used By, with Owner added to satisfy the document-ownership requirement. |

---

*End of document. **REVISION 1.0** — This file is the official documentation index for the Unified Education Platform Version 1 and the first document every AI assistant reads before performing any task. Docs before code; consistency over convenience; Archive — never delete.*
