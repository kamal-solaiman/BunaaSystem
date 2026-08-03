# 1. Document Information

| Field | Value |
|---|---|
| Document | `AI_DOCS/02_Software_Requirements.md` |
| Document Type | Software Requirements Specification (SRS) |
| Standard Alignment | IEEE 29148 — SRS structure; Parts 1–6 |
| Project | Unified Education Platform |
| Version Scope | Version 1 |
| Source of Truth | `AI_DOCS/00_Project_Context.md` and `AI_DOCS/01_Project_Vision.md` |
| Status | Parts 1–6 authored and consistency-reviewed |
| Date | 2026-07-30 |

This document is the Software Requirements Specification for the Unified Education Platform. This file must remain consistent with the official Project Context and Project Vision. If any conflict is discovered between this document and `AI_DOCS/00_Project_Context.md`, the Project Context wins.

This document contains **Part 1**, **Part 2 — Teacher Module**, **Part 3 — Student Module**, **Part 4 — Parent Module**, **Part 5 — Platform Administration Module**, and **Part 6 — Non-Functional Requirements**. Part 1 establishes the requirements context, product boundaries, terminology, high-level product functions, user roles, constraints, assumptions, and dependencies. Parts 2–5 define role/module requirements. Part 6 defines non-functional requirements. This document does not define APIs, database tables, UI implementation details, or source code.

---

# 2. Purpose

The purpose of this SRS Part 1 is to define the foundational software requirements context for Version 1 of the Unified Education Platform. It translates the business vision and confirmed project rules into a structured requirements baseline that can guide later requirements analysis, design, implementation, validation, and acceptance.

This document serves several purposes:

1. Establish a shared understanding of what the Unified Education Platform is intended to support in Version 1.
2. Identify the boundaries of the product without adding unconfirmed features.
3. Define the core roles, terminology, assumptions, constraints, and dependencies that all later requirements must respect.
4. Provide a consistent reference for future SRS parts that will define detailed functional and non-functional requirements.
5. Preserve alignment with the official Project Context, which is the Single Source of Truth for Version 1.

Part 1 intentionally remains at the product and requirements-context level. Parts 2–5 define module requirements, and Part 6 defines non-functional requirements without specifying APIs, database structures, UI implementation rules, or source-code concerns.

The intended outcome is a clear foundation for the rest of the SRS. Future parts may expand into detailed requirements, but they must not contradict the business rules, scope limits, terminology, and role boundaries documented here and in the Project Context.

---

# 3. Scope

The Unified Education Platform is a SaaS educational platform for teacher-based education. Version 1 is delivered as a **Web Application only**. Native mobile applications are outside the Version 1 scope.

The platform exists to solve the fragmentation experienced when one Student studies with several Teachers and each Teacher uses a different platform. The confirmed solution is: **one platform, one account per Student, one account per Parent, and many isolated Teacher Workspaces**.

Version 1 includes the following core scope boundaries:

- A Student has exactly one global account and may study with multiple Teachers.
- A Parent has one account and may monitor multiple linked Students.
- Version 1 supports exactly one Parent account per Student.
- Each Teacher operates one completely isolated Teacher Workspace.
- Teachers cannot see another Teacher's data under any circumstance.
- Each Teacher account represents exactly one Teaching Subject.
- The Teaching Subject is selected during registration and cannot be changed after account creation.
- A Student belongs to only one Group per Teacher at any time.
- Group moves preserve historical attendance, homework, exams, and grades.
- Teacher Workspaces include Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Users, and Settings.
- Attendance supports three methods: Dynamic QR Code generated daily, ID Card scanning, and manual entry.
- Homework supports Text, Image, and PDF only.
- Exams are built from the Teacher's private Question Bank and support Multiple Choice, True/False, Essay, and Bubble Sheet question types.
- Lessons are Teacher-owned and private to the Teacher Workspace.
- The Platform records payment status only; actual payments are handled outside the Platform.
- The business model is based on monthly Subscriptions paid by Teachers to the Platform.
- Flow A and Flow B are separate financial flows and must never be conflated.
- Archive replaces permanent deletion everywhere.
- Important actions are recorded in the Audit Log.

Version 1 explicitly excludes native mobile applications, online payment gateways, notifications, multiple Teaching Subjects per Teacher account, marketplace behavior, video homework, multiple Parent accounts per Student, and in-platform payment transactions.

This SRS defines the overall product scope in Part 1, module requirements in Parts 2–5, and non-functional requirements in Part 6. It does not describe individual pages, user interface implementation, API behavior, database tables, or source code.

---

# 4. Definitions

The following definitions use the canonical terminology from the Project Context and must be used consistently across all requirements documentation.

| Term | Definition |
|---|---|
| Platform | The Unified Education Platform, a SaaS educational platform for Version 1. |
| Teacher Workspace | One Teacher's completely isolated area of the Platform. It is the unit of data isolation for Teacher-owned operations and records. |
| Educational Grade | A Teacher-created education level, such as First Preparatory, Second Preparatory, or First Secondary. Educational Grades contain Groups. |
| Teaching Subject | The single subject a Teacher teaches, such as Mathematics, Physics, Chemistry, Biology, Arabic, or English. It is selected during Teacher registration and cannot be changed after account creation. |
| Group | A cohort inside one Educational Grade, with Name, Schedule, Price, and Pricing Type. |
| Pricing Type | The fee basis of a Group. Confirmed values are Monthly and Per Lesson. |
| Student | A learner with one global account who may study with multiple Teachers. A Student belongs to only one Group per Teacher at any time. |
| Parent | A guardian account that monitors linked Students. Parent access is read-only everywhere. Version 1 supports exactly one Parent account per Student. |
| Teacher Staff | Internal users created by a Teacher, such as Secretary, Assistant, or Accountant. Teacher Staff exist only inside the creating Teacher's Teacher Workspace and hold only Teacher-assigned permissions. |
| Super Admin | The platform owner role. The Super Admin manages Teachers, Subscriptions, pricing, platform settings, and global reports at the platform level. |
| Subscription | The Teacher's monthly Platform Subscription under Flow A. This must not be confused with Student fees owed to Teachers under Flow B. |
| Flow A | Teacher to Platform Subscription. This is the SaaS revenue flow managed at the platform level. |
| Flow B | Student or Parent to Teacher fees. These fees derive from Group Price and Pricing Type and are tracked by the Platform on the Teacher's behalf. |
| Enrollment | The link between a Student and one Group, and therefore one Teacher. A Student may have separate enrollments with different Teachers. |
| Archive | The required alternative to permanent deletion. Archived records are retained for history and reporting according to the Archive Policy. |
| Audit Log | The append-only record of important actions across the Platform. |
| Dynamic QR Code | A QR Code generated daily for attendance, displayed for the class, and scanned by the Student through the web application. |
| ID Card | A printed QR card carried by the Student and scanned by a QR scanner device for attendance. |
| Question Bank | A Teacher-owned private repository of questions used to build Exams. |
| Bubble Sheet | An electronic exam format simulating traditional paper bubble sheets. Students answer by selecting bubbles on screen, and automatic grading is supported. |
| Student Switcher | The Parent capability for switching between linked Students. |
| Lesson | A video uploaded by a Teacher for that Teacher's own Students. Lessons are private to the Teacher Workspace. |
| Billable Student | A Student enrolled in a Teacher's Group for more than 15 calendar days during the Billing Cycle. Attendance and login activity are not used to determine this status. |
| Billing Cycle | A calendar month. It starts on the first day of the month, ends on the last day of the same month, and begins again automatically on the first day of the next month. |
| Homework | An assignment supporting Text, Image, and PDF formats only in Version 1. |

---

# 5. Acronyms

| Acronym | Meaning |
|---|---|
| SRS | Software Requirements Specification |
| IEEE | Institute of Electrical and Electronics Engineers |
| SaaS | Software as a Service |
| V1 | Version 1 |
| QR | Quick Response |
| ID | Identification |
| PDF | Portable Document Format |
| SMS | Short Message Service |
| RTL | Right-to-left |
| BR | Business Rule |
| Q | Open Question |
| D | Decision |

---

# 6. Intended Audience

This document is intended for all stakeholders who need a shared understanding of the Version 1 software requirements context.

**Product Owner**

The Product Owner uses this document to confirm that software requirements remain aligned with the business vision, confirmed scope, and frozen Project Context. The Product Owner is the source of all confirmed product decisions and is responsible for approving changes that affect business rules or scope.

**Super Admin / Platform Ownership Stakeholders**

Platform ownership stakeholders use this document to understand the product boundaries, commercial model, role responsibilities, and constraints that govern the Platform. This includes understanding the separation between Flow A and Flow B, the Teacher Subscription model, and the platform-level management responsibilities of the Super Admin.

**Teachers**

Teachers are the paying customers and primary operational users of the Platform. They use this requirements context to understand the purpose and boundaries of the Teacher Workspace, including the confirmed ability to manage Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Users, and Settings within an isolated workspace.

**Teacher Staff**

Teacher Staff stakeholders use this document to understand that their access exists only within the Teacher Workspace that created them and only according to permissions assigned by the Teacher. Detailed permission granularity is not defined in this Part 1.

**Students**

Students use the product under the principle of one global account and multiple Teacher relationships. This document clarifies that Student data such as attendance, homework, exams, Lessons, and Subscription-related status is partitioned per Teacher.

**Parents**

Parents use the product as read-only monitors of linked Students. This document clarifies that a Parent may monitor multiple Students, sees only linked Students, and that Version 1 supports exactly one Parent account per Student.

**Architecture, Design, and Development Teams**

Architecture, design, and development teams use this document as an input to later technical and functional specifications. They must not treat this Part 1 as permission to invent features, change business rules, or define implementation details that are not consistent with the Project Context.

**Quality Assurance and Testing Teams**

Quality assurance and testing teams use this document to understand the confirmed product scope, user roles, constraints, assumptions, and dependencies that will shape future test planning. Detailed test cases are not part of this document.

**Documentation and Support Teams**

Documentation and support teams use this document to maintain consistent terminology and to understand the intended product boundaries for Version 1.

---

# 7. Product Overview

The Unified Education Platform is a web-based SaaS product designed for teacher-based education. It provides a unified account model for Students and Parents while preserving strict separation between Teacher Workspaces.

The product is built around a confirmed educational reality: one Student may study with several Teachers, and each Teacher may otherwise use a different platform. This creates duplicated accounts, scattered attendance systems, different QR codes, different homework sources, different exams, and no single place for a Student or Parent to understand the complete learning picture. The Unified Education Platform addresses this by centralizing identity and access for Students and Parents while keeping Teacher-owned operations private.

For Students, the Platform provides one global account. A Student may study with multiple Teachers, but each Teacher relationship remains separate in terms of attendance, homework, exams, Lessons, and Subscription-related status. The Student benefits from reduced account duplication while Teachers retain complete workspace separation.

For Parents, the Platform provides one account for monitoring linked Students. A Parent may monitor multiple Students, and the Parent's access is read-only everywhere. The Parent sees only linked Students and does not operate inside Teacher Workspaces.

For Teachers, the Platform provides a private Teacher Workspace. Each Teacher manages their own Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Users, and Settings. Teacher data is completely isolated, and no Teacher can access another Teacher's data.

For the Super Admin, the Platform provides platform-level management of Teachers, Subscriptions, pricing, platform settings, and global reports. The Super Admin role supports the business model but does not operate as a Teacher inside Teacher Workspaces.

The Platform is not an online course marketplace. Teachers do not sell courses through the Platform. There is no course discovery or browsing across Teachers, and there is no mechanism by which one Teacher's content reaches another Teacher's Students.

The Platform's commercial model is based on monthly Teacher Subscriptions. The Subscription amount depends on the number of Billable Students and the applicable price per Student. The Billing Cycle is a calendar month. Student fee tracking for Teachers is separate from Platform Subscription billing and is governed by Group Price and Pricing Type. Version 1 records payment status only and does not process transactions.

---

# 8. Product Perspective

The Unified Education Platform is a new shared platform that replaces the fragmented experience of separate Teacher platforms without merging Teacher data. It should be understood as a common service with isolated Teacher Workspaces rather than as a shared content marketplace.

From the Student and Parent perspective, the Platform provides unified identity and monitoring. A Student should not need multiple accounts for different Teachers. A Parent should not need multiple accounts to monitor linked Students. However, this unified access does not mean unified Teacher ownership. Each Teacher relationship remains separated within the Platform.

From the Teacher perspective, the Platform behaves as a private operational workspace. A Teacher manages educational structure, Students, attendance records, homework, Exams, Lessons, reports, Teacher Staff, and settings within a workspace that is isolated from every other Teacher Workspace. The Teacher receives the benefits of a shared SaaS platform without sharing data with other Teachers.

From the Platform business perspective, the product is a subscription-based SaaS offering paid for by Teachers. The Platform owner, represented by the Super Admin role, manages Teachers, Subscriptions, pricing, platform settings, and global reports at the platform level.

The Platform must preserve the separation between two financial flows:

- **Flow A — Platform Subscription:** Teacher pays the Platform through a monthly Subscription based on Billable Students.
- **Flow B — Student fees:** Student or Parent pays the Teacher according to the Group's Price and Pricing Type.

Both flows are tracked by payment status only in Version 1. Actual payment processing is outside the Platform.

The Platform must also preserve historical integrity. Permanent deletion is not allowed; Archive is used instead. Important actions are recorded in the Audit Log. Student transfers between Groups preserve historical attendance, homework, exams, and grades.

This product perspective establishes the overall system context without defining implementation design, individual pages, APIs, database tables, or UI behavior.

---

# 9. Product Functions (High-Level)

Version 1 includes the following high-level product functions. These are product-level capabilities only and do not describe individual pages, implementation details, APIs, or database structures.

**Identity and Account Management**

The Platform supports one global Student account and one Parent account model. Students may self-register or be created manually by a Teacher. If a Teacher creates the Student account, the Student can later activate and use the same account. Duplicate Student accounts are not allowed.

Parents have one account and may monitor multiple linked Students. Version 1 supports exactly one Parent account per Student.

**Teacher Workspace Management**

The Platform supports isolated Teacher Workspaces. Each Teacher operates only within their own Teacher Workspace. Teacher data is completely isolated, and Teachers cannot see each other's data.

A Teacher Workspace supports Teacher-owned educational operations, including Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Users, and Settings.

**Academic Structure Management**

The Platform supports Educational Grades and Groups within each Teacher Workspace. Educational Grades are Teacher-created education levels. Groups belong to Educational Grades and include Name, Schedule, Price, and Pricing Type. Pricing Type is Monthly or Per Lesson.

Each Teacher account represents exactly one Teaching Subject. The Teaching Subject is selected during Teacher registration and cannot be changed after account creation. Multiple Teaching Subjects under one Teacher account are not supported in Version 1.

**Enrollment Management**

The Platform supports Student Enrollment in Groups. A Student may study with multiple Teachers but belongs to only one Group per Teacher at any time. Moving a Student between Groups preserves historical attendance, homework, exams, and grades.

**Attendance Management**

The Platform supports Attendance through three confirmed methods: Dynamic QR Code generated daily and scanned by the Student through the web application, printed ID Card scanned by a QR scanner device, and manual entry by the Teacher.

**Homework Management**

The Platform supports Homework using Text, Image, and PDF formats only. Video homework is not supported in Version 1.

**Exam Management**

The Platform supports Teacher-owned private Question Banks and Exams. Supported question types are Multiple Choice, True/False, Essay, and Bubble Sheet. Exams, attempts, and grades are scoped to the relevant Teacher Workspace.

**Lesson Management**

The Platform supports Teacher-owned private Lessons. A Teacher may upload lesson videos exclusively for that Teacher's own Students. Cross-Teacher access to Lessons is not supported.

**Reporting**

The Platform supports reports related to attendance, homework, exam results, payments, and Student performance. Historical data remains available for reporting, including archived records where applicable.

**Teacher Staff Management**

The Platform supports Teacher Staff accounts created by Teachers inside their own Teacher Workspaces. Teacher Staff hold only permissions assigned by the Teacher. Detailed permission granularity remains pending and is not defined in this Part 1.

**Subscription and Billing Status Management**

The Platform supports monthly Teacher Subscriptions under Flow A. The Billing Cycle starts on the first day of each calendar month and ends on the last day of the same month. A new Billing Cycle begins automatically on the first day of the next month.

A Student becomes a Billable Student based on Enrollment duration only. If a Student remains enrolled in a Teacher's Group for more than 15 calendar days during the Billing Cycle, the Student is Billable. Attendance and login activity are not used in this calculation.

**Student Fee Status Tracking**

The Platform supports Student fee status tracking under Flow B. Student fee obligations are derived from Group enrollment, Group Price, and Pricing Type. Actual payments are handled outside the Platform.

**Archive and Historical Integrity**

The Platform supports Archive instead of permanent deletion. Archived records remain available for reports and history. Historical relationships are not detached, rewritten, or lost through archival or Student transfers.

**Audit Log**

The Platform records important actions in the Audit Log. Recorded events include create, update, archive, restore, login, permission change, attendance change, exam modification, homework modification, and Subscription change.

---

# 10. User Roles

Version 1 includes five confirmed roles: Super Admin, Teacher, Teacher Staff, Student, and Parent.

**Super Admin**

The Super Admin owns the Platform at the platform level. The Super Admin manages Teachers, Subscriptions under Flow A, pricing, platform settings, and global reports. The Super Admin does not operate inside Teacher Workspaces as a Teacher.

Pricing is owned by the Super Admin. The pricing model detail, including flat price versus volume tiers, remains pending in the Project Context and must not be silently assumed.

**Teacher**

A Teacher operates one completely isolated Teacher Workspace. The Teacher cannot access another Teacher's data under any circumstance.

Within the Teacher Workspace, the Teacher manages Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Users, and Settings. The Teacher teaches exactly one Teaching Subject per account. The Teaching Subject is selected during registration and cannot be changed after account creation.

The Teacher is the paying customer under the Platform's monthly Subscription model.

**Teacher Staff**

Teacher Staff are created by the Teacher and exist only inside that Teacher Workspace. Examples include Secretary, Assistant, and Accountant.

Teacher Staff hold only permissions assigned by the Teacher. The detailed permission model is pending and must be defined in the appropriate RBAC documentation without contradicting the Project Context.

**Student**

A Student has exactly one global account and may study with multiple Teachers. A Student belongs to only one Group per Teacher at any time.

Student data such as attendance, homework, exams, Lessons, and Subscription-related status is separated per Teacher. A Student may register their own account, or a Teacher may create the Student account manually. Duplicate Student accounts are not allowed.

**Parent**

A Parent has one account and may monitor multiple linked Students. Parent access is read-only everywhere. A Parent sees only linked Students.

Version 1 supports exactly one Parent account per Student. A Student cannot have multiple Parent accounts linked simultaneously in Version 1.

---

# 11. General Constraints

The following general constraints apply to Version 1 and to all future SRS parts unless formally changed by an approved decision consistent with the Project Context.

1. The Project Context is the official Single Source of Truth for Version 1.
2. Version 1 is a Web Application only.
3. Native mobile applications are outside Version 1 scope.
4. Online payment gateways are outside Version 1 scope.
5. Push notifications, email notifications, and SMS notifications are outside Version 1 scope.
6. The Platform is not an online course marketplace.
7. Teachers do not sell courses through the Platform.
8. There is no course discovery or browsing across Teachers.
9. Teacher Workspaces are completely isolated.
10. Teachers cannot see another Teacher's data under any circumstance.
11. A Student has exactly one global account.
12. Duplicate Student accounts are not allowed.
13. A Parent has one account and may monitor multiple Students.
14. Version 1 supports exactly one Parent account per Student.
15. Parent access is read-only everywhere.
16. A Student belongs to only one Group per Teacher at any time.
17. Student transfers preserve historical attendance, homework, exams, and grades.
18. Each Teacher account represents exactly one Teaching Subject.
19. The Teaching Subject is selected during registration and cannot be changed after account creation.
20. Homework supports Text, Image, and PDF only.
21. Video homework is outside Version 1 scope.
22. Lessons are Teacher-owned and private.
23. The Question Bank is Teacher-owned and private.
24. Flow A and Flow B must remain separate.
25. Version 1 records payment status only and does not process transactions.
26. Billable Student calculation is based on Enrollment duration only.
27. Attendance and login activity are not used to calculate Billable Students.
28. No permanent deletion is allowed; Archive must be used instead.
29. Historical data is never deleted and must always remain available.
30. Important actions must be recorded in the Audit Log.
31. Pending Project Context questions must not be silently assumed.
32. This document must not introduce page-level behavior, API design, database design, UI implementation, or source code.

---

# 12. Assumptions

The following assumptions apply to Version 1 based on the Project Context and Project Vision.

1. Teachers are willing to pay monthly for a SaaS educational platform that helps manage their educational operations.
2. The core user problem is fragmentation across separate Teacher platforms.
3. Students benefit from one global account even when studying with multiple Teachers.
4. Parents benefit from one account for monitoring linked Students.
5. Teachers require complete isolation of their Teacher Workspace data.
6. A web application is sufficient for Version 1 delivery.
7. Students can use the web application for Dynamic QR Code attendance scanning.
8. Teachers accept one Teaching Subject per Teacher account for Version 1.
9. If a Teacher needs another Teaching Subject, a separate Teacher account is required.
10. Actual payments can be handled outside the Platform during Version 1.
11. Recording payment status is sufficient for Version 1 financial tracking.
12. Text, Image, and PDF are sufficient Homework formats for Version 1.
13. The confirmed Attendance methods are sufficient for Version 1 classroom operations.
14. The confirmed Exam question types are sufficient for Version 1 exam operations.
15. Parent access being read-only is sufficient for Version 1 monitoring needs.
16. One Parent account per Student is sufficient for Version 1.
17. Archive can replace permanent deletion across the Platform.
18. Historical records must remain available for reports and continuity.
19. Pending decisions will be resolved through later documentation or formal Product Owner decisions.
20. Later SRS parts will provide more detailed requirements without contradicting this Part 1 or the Project Context.

---

# 13. Dependencies

Version 1 depends on several business, operational, and documentation conditions.

**Source-of-Truth Dependencies**

All requirements depend on the Project Context as the official Single Source of Truth. The Project Vision provides business direction and value framing. Future requirements documentation must remain consistent with both, with the Project Context taking precedence in any conflict.

**Product Decision Dependencies**

Several topics remain pending in the Project Context and must be resolved before detailed requirements can be finalized in those areas:

- Non-payment enforcement.
- Lesson video hosting/protection.
- Teacher Staff permission granularity.
- Super Admin content visibility.
- Flat price versus volume tiers.
- Arabic (default) and English (fully supported) are confirmed; timezone, currency, and target market/country.

No software requirement may harden an unresolved pending decision as if it were confirmed.

**Business Operation Dependencies**

The monthly Subscription model depends on the Super Admin owning pricing and managing Platform-level Subscription status. The Billable Student calculation depends on Enrollment duration during the calendar-month Billing Cycle.

Flow B payment tracking depends on Group Price, Pricing Type, and Student Enrollment. Because actual payments are handled outside the Platform in Version 1, accurate payment-status records depend on the responsible users or administrators recording the correct status.

**User Adoption Dependencies**

The Platform depends on Teachers adopting the Teacher Workspace as their operational environment. Student and Parent value depends on Students studying with multiple Teachers through one account and Parents monitoring linked Students through one account.

**Attendance Operation Dependencies**

Dynamic QR Code attendance depends on Students accessing the web application. ID Card attendance depends on printed ID Cards and the availability of a QR scanner device where that method is used. Manual attendance depends on authorized Teacher-side entry.

**Historical Integrity Dependencies**

Reporting and continuity depend on Archive replacing permanent deletion and on historical records remaining available. Student transfers depend on preserving prior attendance, homework, exams, and grades without rewriting history.

**Documentation Dependencies**

This Part 1 depends on later SRS parts and other AI_DOCS documents to define detailed requirements, role permission matrices, architecture, project structure, user flows, database design, API design, UI/UX guidelines, development roadmap, coding standards, and deployment planning. Those future documents must not contradict this Part 1 or the Project Context.

---

# PART 2 — Teacher Module

This Part 2 defines the Teacher Module requirements only. It is limited to Teacher Workspace capabilities confirmed in the Project Context and Project Vision. It does not define the Student Module, Parent Module, Super Admin Module, APIs, database tables, UI implementation details, or source code.

The Teacher Module is governed by the following mandatory principles:

- The Teacher operates only inside one completely isolated Teacher Workspace.
- Teacher data is never shared with another Teacher.
- Teacher Staff may act only within the creating Teacher Workspace and only according to permissions assigned by the Teacher.
- Archive replaces permanent deletion everywhere.
- Important actions must be recorded in the Audit Log.
- The canonical term for the second Teacher academic-structure section is **Educational Grade**, and that term is used consistently to remain aligned with the Project Context.

## 1. Dashboard

### Purpose

The Dashboard provides the Teacher with a high-level operational summary of the Teacher Workspace. Its purpose is to help the Teacher understand current workspace activity, identify items requiring attention, and access summarized information related to Students, Groups, Attendance, Homework, Exams, Reports, payment status, and Teacher Workspace operations.

### Description

The Dashboard is a Teacher Workspace summary area. It must present information only from the Teacher's own Teacher Workspace and must not expose any data belonging to another Teacher. It is not a reporting engine replacement; detailed reporting remains under Reports. It is also not a Super Admin overview and must not include platform-wide information.

The Dashboard should summarize confirmed Teacher Workspace concepts, including Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Users, and Settings. It may present operational indicators derived from those modules, provided the indicators do not introduce new business rules or unsupported features.

### Functional Requirements

- **TM-DASH-FR-001:** The system shall provide the Teacher with a summary of Teacher Workspace activity.
- **TM-DASH-FR-002:** The system shall limit all Dashboard information to the current Teacher Workspace.
- **TM-DASH-FR-003:** The system shall summarize Student participation and Group activity using only records that belong to the Teacher Workspace.
- **TM-DASH-FR-004:** The system shall summarize Attendance activity using Attendance records from the Teacher Workspace.
- **TM-DASH-FR-005:** The system shall summarize Homework activity using Homework records from the Teacher Workspace.
- **TM-DASH-FR-006:** The system shall summarize Exam activity using Exam records from the Teacher Workspace.
- **TM-DASH-FR-007:** The system shall summarize payment-status information without processing payments.
- **TM-DASH-FR-008:** The system shall clearly separate Flow A Subscription status from Flow B Student fee status where both are referenced.
- **TM-DASH-FR-009:** The system shall include archived records in historical summaries only where reporting rules require historical visibility, and archived records shall be clearly indicated when included.
- **TM-DASH-FR-010:** The system shall not display data from any other Teacher Workspace.

### Business Rules

- The Teacher can view only the Teacher Workspace owned by that Teacher (BR-003).
- Teacher Staff can view Dashboard information only if the Teacher assigned the required permission (BR-013).
- Flow A and Flow B must never be conflated.
- Version 1 records payment status only and does not process transactions (BR-019).
- Historical data must remain available and must not be permanently deleted (BR-014).
- Archived records must not appear as active records.

### User Permissions

- Teacher: may view Dashboard information for the Teacher's own Teacher Workspace.
- Teacher Staff: may view Dashboard information only when granted the relevant permission by the Teacher.
- No Teacher or Teacher Staff user may view Dashboard information for another Teacher Workspace.

### Inputs

- Current authenticated user context.
- Current Teacher Workspace context.
- Existing Teacher Workspace records related to Students, Groups, Attendance, Homework, Exams, Lessons, Reports, Users, Settings, and payment status.
- Date or period filters where applicable to summarized information.

### Outputs

- Teacher Workspace summary indicators.
- Operational summaries related to Students, Groups, Attendance, Homework, Exams, Lessons, Reports, Users, Settings, and payment status.
- Clear distinction between active and archived information when archived information is included for historical purposes.
- Clear separation between Flow A and Flow B payment-status information.

### Validations

- The user must be authenticated.
- The user must have access to the current Teacher Workspace.
- Teacher Staff must have assigned permission to view the Dashboard.
- Date or period filters, if used, must be valid and within supported reporting ranges.
- Dashboard data must be scoped to the Teacher Workspace.

### Error Handling

- If the user is not authenticated, the system shall deny access.
- If the user does not belong to or have permission for the Teacher Workspace, the system shall deny access.
- If requested summary information cannot be retrieved, the system shall present an appropriate failure state without exposing technical details.
- If filters are invalid, the system shall reject the request and require valid filter values.

### Edge Cases

- A new Teacher Workspace may have no Students, Groups, Attendance, Homework, Exams, Lessons, or payment-status records.
- A Teacher Workspace may contain archived records that should not be counted as active records.
- A Teacher Staff user may have permission to view some operational areas but not others; unresolved permission granularity must not be assumed beyond assigned permissions.
- Payment status may be incomplete because payments are handled outside the Platform.

### Acceptance Criteria

- The Dashboard displays only data from the Teacher's own Teacher Workspace.
- The Dashboard does not expose another Teacher's data under any circumstance.
- Flow A and Flow B information, if both are shown, are clearly separated.
- Archived records are not presented as active records.
- A Teacher Staff user without the required permission cannot access Dashboard information.
- A new Teacher Workspace with no data is handled without error.

## 2. Educational Grades

### Purpose

Educational Grades allow a Teacher to define the education levels used inside the Teacher Workspace. They provide the first level of academic organization for Groups and help the Teacher structure Students according to educational level.

### Description

An Educational Grade is a Teacher-created education level, such as First Preparatory, Second Preparatory, or First Secondary. Educational Grades belong only to the Teacher Workspace that created them. Groups are organized under Educational Grades. Educational Grades are independent from the Teacher's Teaching Subject.

This section uses the canonical term **Educational Grades** because the Project Context requires this terminology for Teacher-created education levels.

### Functional Requirements

- **TM-GRADE-FR-001:** The system shall allow the Teacher to create Educational Grades inside the Teacher Workspace.
- **TM-GRADE-FR-002:** The system shall allow the Teacher to view Educational Grades belonging to the Teacher Workspace.
- **TM-GRADE-FR-003:** The system shall allow the Teacher to update Educational Grade information.
- **TM-GRADE-FR-004:** The system shall allow the Teacher to Archive an Educational Grade instead of permanently deleting it.
- **TM-GRADE-FR-005:** The system shall allow authorized restoration of an archived Educational Grade.
- **TM-GRADE-FR-006:** The system shall prevent Educational Grades from being visible across Teacher Workspaces.
- **TM-GRADE-FR-007:** The system shall ensure active Group assignment options include only active Educational Grades from the same Teacher Workspace.
- **TM-GRADE-FR-008:** The system shall preserve historical relationships when an Educational Grade is archived.
- **TM-GRADE-FR-009:** The system shall record create, update, Archive, and restore actions in the Audit Log.

### Business Rules

- Educational Grades are Teacher-created and exist only inside the Teacher Workspace.
- Teaching Subject is independent from Educational Grades (BR-016).
- No permanent deletion is allowed; Archive must be used instead (BR-005).
- Historical data remains available, including archived records where relevant to reporting (BR-014).
- Teacher Workspace isolation applies to all Educational Grade operations (BR-003).

### User Permissions

- Teacher: may create, view, update, Archive, and restore Educational Grades in the Teacher's own Teacher Workspace.
- Teacher Staff: may perform Educational Grade actions only when granted the relevant permission by the Teacher.
- Teacher Staff actions must be attributed to the Teacher Staff user in the Audit Log, not to the Teacher.

### Inputs

- Educational Grade name.
- Educational Grade status through active or archived state.
- Current Teacher Workspace context.
- Authorized user performing the action.

### Outputs

- Created or updated Educational Grade record.
- List of active Educational Grades for Teacher Workspace operations.
- Archived Educational Grade state when Archive is performed.
- Restored Educational Grade state when restoration is performed.
- Audit Log entries for important actions.

### Validations

- Educational Grade name is required.
- Educational Grade name must be valid according to product data rules defined by later detailed requirements.
- Educational Grade operations must be scoped to the current Teacher Workspace.
- Archived Educational Grades must not appear in active assignment lists.
- Restoration must be performed only by an authorized user.

### Error Handling

- If the Educational Grade name is missing or invalid, the system shall reject the action.
- If the user lacks permission, the system shall deny the action.
- If the Educational Grade belongs to another Teacher Workspace, the system shall deny access.
- If an Educational Grade is already archived, repeated Archive attempts shall not create inconsistent active-state changes.
- If restoration is not allowed for the current user, the system shall deny the restoration.

### Edge Cases

- A Teacher may create the first Educational Grade in a new Teacher Workspace.
- An Educational Grade may have active Groups associated with it.
- An Educational Grade may have historical Groups and Student records associated with it.
- Archived Educational Grades may still appear in historical reports while excluded from active selection lists.
- Multiple Educational Grades may have similar names unless later detailed requirements define stricter uniqueness rules.

### Acceptance Criteria

- A Teacher can manage Educational Grades only within the Teacher's own Teacher Workspace.
- Educational Grades from another Teacher Workspace are never visible.
- Educational Grades can be archived but not permanently deleted.
- Archived Educational Grades do not appear as active selection options.
- Historical relationships remain available after archival.
- Create, update, Archive, and restore actions are recorded in the Audit Log.

## 3. Groups

### Purpose

Groups organize Students inside an Educational Grade and define the operational structure for schedule, pricing, Enrollment, Attendance, Homework, Exams, Reports, and Student fee tracking under Flow B.

### Description

A Group belongs to one Educational Grade inside a Teacher Workspace. Each Group carries Name, Schedule, Price, and Pricing Type. Pricing Type is either Monthly or Per Lesson. A Student may belong to only one Group per Teacher at any time. Group membership and movement must preserve historical attendance, homework, exams, and grades.

Groups are not Teaching Subjects. The Teacher's Teaching Subject is selected during registration and cannot be changed after account creation. Groups belong to Educational Grades and operate under the Teacher's one Teaching Subject.

### Functional Requirements

- **TM-GROUP-FR-001:** The system shall allow the Teacher to create Groups inside an active Educational Grade within the Teacher Workspace.
- **TM-GROUP-FR-002:** The system shall allow the Teacher to view Groups belonging to the Teacher Workspace.
- **TM-GROUP-FR-003:** The system shall allow the Teacher to update Group Name, Schedule, Price, and Pricing Type.
- **TM-GROUP-FR-004:** The system shall support Pricing Type values of Monthly and Per Lesson.
- **TM-GROUP-FR-005:** The system shall allow the Teacher to Archive a Group instead of permanently deleting it.
- **TM-GROUP-FR-006:** The system shall allow authorized restoration of an archived Group.
- **TM-GROUP-FR-007:** The system shall prevent archived Groups from appearing as active assignment options.
- **TM-GROUP-FR-008:** The system shall preserve historical records associated with a Group when the Group is archived.
- **TM-GROUP-FR-009:** The system shall support moving Students between Groups while preserving historical attendance, homework, exams, and grades.
- **TM-GROUP-FR-010:** The system shall record create, update, Archive, restore, and Student movement actions in the Audit Log.

### Business Rules

- Each Group belongs to one Educational Grade.
- Each Group carries Name, Schedule, Price, and Pricing Type.
- Pricing Type is Monthly or Per Lesson (BR-009).
- A Student belongs to only one Group per Teacher at any time (BR-002).
- Group moves preserve historical attendance, homework, exams, and grades (BR-007).
- No permanent deletion is allowed; Archive must be used instead (BR-005).
- Teacher Workspace isolation applies to all Group operations (BR-003).
- Student fee obligations derive from Group Enrollment, Price, and Pricing Type under Flow B (BR-009).

### User Permissions

- Teacher: may create, view, update, Archive, restore, and manage Groups in the Teacher's own Teacher Workspace.
- Teacher Staff: may perform Group actions only when granted the relevant permission by the Teacher.
- Teacher Staff actions must be recorded under the Teacher Staff user in the Audit Log.

### Inputs

- Group Name.
- Group Schedule.
- Group Price.
- Pricing Type: Monthly or Per Lesson.
- Educational Grade association.
- Student movement requests between Groups.
- Current Teacher Workspace context.

### Outputs

- Created or updated Group information.
- Active Group lists for Teacher Workspace operations.
- Group status as active or archived.
- Updated Student Enrollment state when a Student is moved.
- Payment-status basis for Flow B tracking.
- Audit Log entries for important actions.

### Validations

- Group Name is required.
- Group must be assigned to an active Educational Grade in the same Teacher Workspace.
- Price must be valid according to product data rules defined by later detailed requirements.
- Pricing Type must be Monthly or Per Lesson.
- A Student cannot be assigned to more than one active Group for the same Teacher at the same time.
- Archived Groups cannot receive new active Student assignments.
- Group actions must be scoped to the Teacher Workspace.

### Error Handling

- If required Group information is missing, the system shall reject the action.
- If Pricing Type is not Monthly or Per Lesson, the system shall reject the action.
- If the Educational Grade is archived or outside the Teacher Workspace, the system shall reject the action.
- If assigning a Student would violate the one Group per Teacher rule, the system shall reject the assignment.
- If the user lacks permission, the system shall deny the action.

### Edge Cases

- A Group may have no Students yet.
- A Group may have historical Student Enrollments but no current Students.
- A Group may be archived after historical Attendance, Homework, Exams, and payment-status records exist.
- A Student may be moved from one Group to another under the same Teacher, requiring history preservation.
- Price or Pricing Type changes may affect future fee tracking, while historical payment records must remain historically understandable.

### Acceptance Criteria

- A Teacher can manage Groups only within the Teacher's own Teacher Workspace.
- A Group cannot exist without an Educational Grade association.
- Pricing Type is limited to Monthly or Per Lesson.
- A Student cannot be active in more than one Group for the same Teacher.
- Group archival does not remove historical records.
- Student movement preserves historical attendance, homework, exams, and grades.
- Important Group actions are recorded in the Audit Log.

## 4. Students

### Purpose

The Students area allows the Teacher to manage Student participation inside the Teacher Workspace while preserving the Platform rule that each Student has exactly one global account and may study with multiple Teachers.

### Description

A Teacher may register a new Student, assign an existing Student, search Students, and move Students between Groups. Student accounts must not be duplicated. If the Teacher creates the Student account manually, the Student can later activate and use the same account. Student data is partitioned per Teacher, meaning Attendance, Homework, Exams, Lessons, and Subscription-related status are separate for each Teacher relationship.

The Students area must protect Teacher Workspace isolation. The Teacher may manage the relationship between the Teacher Workspace and the Student, but must not gain access to another Teacher's private data for that Student.

### Functional Requirements

- **TM-STUDENT-FR-001:** The system shall allow the Teacher to register a new Student account manually.
- **TM-STUDENT-FR-002:** The system shall allow the Teacher to assign an existing Student to the Teacher Workspace.
- **TM-STUDENT-FR-003:** The system shall prevent duplicate Student accounts.
- **TM-STUDENT-FR-004:** The system shall allow a Teacher-created Student account to be activated and used later by the Student.
- **TM-STUDENT-FR-005:** The system shall allow the Teacher to search Students for Teacher Workspace management purposes.
- **TM-STUDENT-FR-006:** The system shall allow the Teacher to assign a Student to one Group in the Teacher Workspace.
- **TM-STUDENT-FR-007:** The system shall prevent a Student from belonging to more than one Group for the same Teacher at the same time.
- **TM-STUDENT-FR-008:** The system shall allow the Teacher to move a Student between Groups while preserving historical attendance, homework, exams, and grades.
- **TM-STUDENT-FR-009:** The system shall allow Student records or Teacher-Student workspace relationships to be archived according to the Archive Policy, without permanent deletion.
- **TM-STUDENT-FR-010:** The system shall record Student creation, assignment, update, movement, Archive, and restore actions in the Audit Log.
- **TM-STUDENT-FR-011:** The system shall ensure the Teacher sees only Student data relevant to the Teacher Workspace.

### Business Rules

- A Student has exactly one global account and may study with multiple Teachers (BR-001).
- Duplicate Student accounts are not allowed (BR-022).
- Student Registration supports self-registration and Teacher-created accounts (BR-022).
- A Student belongs to only one Group per Teacher at any time (BR-002).
- Student transfers preserve historical attendance, homework, exams, and grades (BR-007).
- Teacher data is completely isolated (BR-003).
- No permanent deletion is allowed; Archive must be used instead (BR-005).

### User Permissions

- Teacher: may register, assign, search, update, move, Archive, and restore Students within the Teacher's own Teacher Workspace.
- Teacher Staff: may perform Student actions only when granted the relevant permission by the Teacher.
- Teacher Staff actions must be attributed to the Teacher Staff user in the Audit Log.
- Teacher and Teacher Staff cannot access another Teacher's private Student records.

### Inputs

- Student identity information required for registration or matching against existing accounts.
- Student account activation state where relevant.
- Group assignment or movement request.
- Current Teacher Workspace context.
- Authorized user performing the action.

### Outputs

- Created Student account where no duplicate exists.
- Assigned existing Student relationship to the Teacher Workspace.
- Student list scoped to the Teacher Workspace.
- Updated Enrollment or Group assignment state.
- Preserved historical Student records for the Teacher Workspace.
- Audit Log entries for important actions.

### Validations

- Student registration inputs must be sufficient to prevent duplicate accounts according to later detailed identity requirements.
- A new Student account must not duplicate an existing Student account.
- Existing Student assignment must not expose another Teacher's private data.
- Group assignment must be to an active Group in the Teacher Workspace.
- A Student must not be assigned to more than one active Group for the same Teacher.
- Archive and restore actions must be authorized.

### Error Handling

- If Student information is incomplete or invalid, the system shall reject the action.
- If a duplicate Student account is detected, the system shall prevent account creation and support assignment of the existing Student where appropriate.
- If the selected Group is archived or outside the Teacher Workspace, the system shall reject the assignment.
- If Group assignment violates the one Group per Teacher rule, the system shall reject the assignment.
- If the user lacks permission, the system shall deny the action.

### Edge Cases

- A Teacher attempts to create a Student who already exists globally.
- A Student already studies with another Teacher and is assigned to this Teacher Workspace.
- A Teacher-created Student account has not yet been activated by the Student.
- A Student moves between Groups after historical records exist.
- A Student has archived historical records that must remain available in reports.
- A Student is active with multiple Teachers but each Teacher sees only their own workspace-scoped data.

### Acceptance Criteria

- The Teacher can register a Student only when doing so does not create a duplicate Student account.
- The Teacher can assign an existing Student without seeing another Teacher's private data.
- A Student can have only one active Group under the same Teacher.
- Student movement preserves historical attendance, homework, exams, and grades.
- Archive is used instead of permanent deletion.
- Student-related important actions are recorded in the Audit Log.

## 5. Attendance

### Purpose

Attendance allows the Teacher to record and manage Student attendance within the Teacher Workspace using the confirmed Version 1 attendance methods.

### Description

Attendance in Version 1 supports three methods: a Dynamic QR Code generated daily and scanned by the Student through the web application, a printed ID Card scanned by a QR scanner device, and manual entry by the Teacher. Attendance records belong to the Teacher Workspace and must remain separate from Attendance records for other Teachers.

Attendance must not be used to calculate Billable Students for Flow A. Billable Student calculation is based on Enrollment duration only.

### Functional Requirements

- **TM-ATT-FR-001:** The system shall support Attendance recording through a Dynamic QR Code generated daily.
- **TM-ATT-FR-002:** The system shall allow Students to scan the daily Dynamic QR Code through the web application for Attendance.
- **TM-ATT-FR-003:** The system shall support Attendance recording through a printed ID Card scanned by a QR scanner device.
- **TM-ATT-FR-004:** The system shall support manual Attendance entry by the Teacher.
- **TM-ATT-FR-005:** The system shall allow the Teacher to view Attendance records for the Teacher Workspace.
- **TM-ATT-FR-006:** The system shall allow authorized correction or modification of Attendance records.
- **TM-ATT-FR-007:** The system shall record Attendance changes in the Audit Log.
- **TM-ATT-FR-008:** The system shall ensure Attendance records are scoped to the Teacher Workspace.
- **TM-ATT-FR-009:** The system shall ensure Attendance is not used for Billable Student calculation.
- **TM-ATT-FR-010:** The system shall preserve Attendance history when a Student moves between Groups.

### Business Rules

- Attendance supports three methods: daily Dynamic QR Code, ID Card scanning, and manual entry (BR-010).
- Attendance is not used to calculate Billable Students (BR-008).
- Student transfers preserve historical Attendance (BR-007).
- Attendance records are Teacher Workspace scoped (BR-003).
- Attendance changes must be recorded in the Audit Log (BR-006).
- No permanent deletion is allowed; Archive applies where Attendance-related records require removal from active use (BR-005).

### User Permissions

- Teacher: may record, view, and modify Attendance in the Teacher's own Teacher Workspace.
- Teacher Staff: may record, view, or modify Attendance only when granted the relevant permission by the Teacher.
- Student participation in Dynamic QR Code scanning is limited to recording that Student's Attendance for the relevant Teacher relationship; this section does not define the Student Module.
- No user may access Attendance records from another Teacher Workspace.

### Inputs

- Attendance method: Dynamic QR Code, ID Card scan, or manual entry.
- Student identity associated with the Attendance event.
- Group or Attendance context within the Teacher Workspace.
- Attendance date or session context as defined by detailed requirements.
- Authorized user or Student scan context.

### Outputs

- Attendance record for the relevant Student and Teacher Workspace.
- Updated Attendance status for the relevant Group or Student context.
- Audit Log entry for Attendance creation or modification.
- Attendance information available for Reports.

### Validations

- Attendance must be recorded for a Student associated with the Teacher Workspace.
- Attendance must be associated with the correct Teacher Workspace.
- Dynamic QR Code Attendance must use the daily generated Dynamic QR Code.
- ID Card Attendance must identify a valid Student relationship for the Teacher Workspace.
- Manual Attendance must be performed by an authorized Teacher or Teacher Staff user.
- Attendance modifications must be authorized and auditable.

### Error Handling

- If a Dynamic QR Code is invalid for the Attendance context, the system shall reject the Attendance scan.
- If an ID Card scan does not identify a valid Student relationship for the Teacher Workspace, the system shall reject the Attendance action.
- If manual entry is attempted by an unauthorized user, the system shall deny the action.
- If Attendance is attempted for a Student outside the Teacher Workspace, the system shall deny the action.
- If duplicate Attendance is attempted for the same Attendance context, the system shall prevent inconsistent duplicate records according to detailed requirements.

### Edge Cases

- A Student attempts to scan a Dynamic QR Code for a Teacher with whom the Student is not enrolled.
- A Student scans the same Dynamic QR Code more than once for the same Attendance context.
- A Teacher records manual Attendance for a Student after the Student moved Groups.
- A Student has historical Attendance under a previous Group after a Group move.
- ID Card scanning is unavailable, requiring manual Attendance entry.
- Attendance records exist for archived Groups and must remain available for reports.

### Acceptance Criteria

- Attendance can be recorded using all three confirmed methods.
- Attendance records are scoped to the Teacher Workspace.
- Attendance is not used to calculate Billable Students.
- Attendance history remains available after Student Group movement.
- Unauthorized Attendance actions are rejected.
- Attendance changes are recorded in the Audit Log.

## 6. Exams

### Purpose

Exams allow the Teacher to assess Students using Exams built from the Teacher's private Question Bank within the Teacher Workspace.

### Description

The Teacher's Question Bank is private and Teacher-owned. Exams are composed only from the owning Teacher's Question Bank. Supported question types are Multiple Choice, True/False, Essay, and Bubble Sheet. Bubble Sheet is an electronic exam format that simulates traditional paper bubble sheets; Students answer by selecting bubbles on screen, and automatic grading is supported.

Exam definitions, attempts, and grades are workspace-scoped. Teachers never see other Teachers' Exam results. Students and Parents may have per-Teacher partitioned visibility in their own modules, but this section defines only Teacher Module requirements.

### Functional Requirements

- **TM-EXAM-FR-001:** The system shall allow the Teacher to manage a private Teacher-owned Question Bank.
- **TM-EXAM-FR-002:** The system shall support Question Bank question types: Multiple Choice, True/False, Essay, and Bubble Sheet.
- **TM-EXAM-FR-003:** The system shall allow the Teacher to create Exams using only questions from the Teacher's own Question Bank.
- **TM-EXAM-FR-004:** The system shall prevent a Teacher from using another Teacher's Question Bank.
- **TM-EXAM-FR-005:** The system shall allow the Teacher to update Exam definitions within the Teacher Workspace according to authorized actions.
- **TM-EXAM-FR-006:** The system shall allow the Teacher to publish or make Exams available according to detailed requirements, without introducing cross-Teacher visibility.
- **TM-EXAM-FR-007:** The system shall support viewing Exam attempts and grades scoped to the Teacher Workspace.
- **TM-EXAM-FR-008:** The system shall support automatic grading for supported objective question behavior, including Bubble Sheet where applicable.
- **TM-EXAM-FR-009:** The system shall allow authorized grading handling for Essay questions according to detailed requirements.
- **TM-EXAM-FR-010:** The system shall allow Exams and questions to be archived instead of permanently deleted.
- **TM-EXAM-FR-011:** The system shall record Exam and Question Bank creation, update, publishing, Archive, grading, and modification actions in the Audit Log.

### Business Rules

- The Question Bank is Teacher-owned and private (BR-011).
- Exams are composed only from the owning Teacher's Question Bank (BR-011).
- Supported question types are Multiple Choice, True/False, Essay, and Bubble Sheet (BR-011).
- Exam definitions, attempts, and grades are workspace-scoped (BR-012).
- Teachers never see other Teachers' Exam results (BR-012 and BR-003).
- Exam modification actions must be recorded in the Audit Log (BR-006).
- No permanent deletion is allowed; Archive must be used instead (BR-005).
- Student transfers preserve historical Exams and grades (BR-007).

### User Permissions

- Teacher: may manage the Teacher-owned Question Bank and Exams in the Teacher's own Teacher Workspace.
- Teacher Staff: may perform Exam or Question Bank actions only when granted the relevant permission by the Teacher.
- Teacher Staff actions must be attributed to the Teacher Staff user in the Audit Log.
- No Teacher or Teacher Staff user may access another Teacher's Question Bank, Exams, attempts, or grades.

### Inputs

- Question content and question type.
- Exam definition information.
- Selection of questions from the Teacher's private Question Bank.
- Exam status or availability action as defined by detailed requirements.
- Student attempt and answer information for Teacher Workspace Exams.
- Grading inputs for Essay questions where applicable.

### Outputs

- Question Bank entries scoped to the Teacher Workspace.
- Exam definitions scoped to the Teacher Workspace.
- Exam attempt records and grades scoped to the Teacher Workspace.
- Automatically graded results where supported.
- Teacher-visible Exam reports and performance information.
- Audit Log entries for Exam and Question Bank actions.

### Validations

- Question type must be one of Multiple Choice, True/False, Essay, or Bubble Sheet.
- Exams may include only questions owned by the same Teacher Workspace.
- Exam actions must be authorized.
- Exam attempts and grades must be associated with the correct Student and Teacher Workspace.
- Archived questions and Exams must not be treated as active unless restored by an authorized user.
- Cross-Teacher Exam or Question Bank references are not allowed.

### Error Handling

- If an unsupported question type is submitted, the system shall reject the question.
- If a Teacher attempts to use another Teacher's question, the system shall deny the action.
- If an Exam action is attempted without permission, the system shall deny the action.
- If grading information is incomplete for a grading action, the system shall reject or defer the action according to detailed requirements.
- If an archived Exam or question is used as active without restoration, the system shall reject the action.

### Edge Cases

- A Teacher creates the first Question Bank entry in a Teacher Workspace.
- A Teacher creates an Exam with no valid active questions selected.
- A Bubble Sheet question requires automatic grading behavior.
- Essay answers require authorized grading handling.
- A Student moves Groups after completing an Exam; historical Exam attempts and grades must remain linked to history.
- An Exam is archived after attempts exist; historical attempts and grades remain available.

### Acceptance Criteria

- Exams can be created only from the Teacher's own private Question Bank.
- Supported question types are limited to Multiple Choice, True/False, Essay, and Bubble Sheet.
- Teachers cannot access another Teacher's Exams, attempts, grades, or Question Bank.
- Exam attempts and grades remain workspace-scoped.
- Exam and Question Bank archival preserves history.
- Exam modification actions are recorded in the Audit Log.

## 7. Reports

### Purpose

Reports allow the Teacher to review Teacher Workspace information related to Attendance, Homework, Exam results, payments, and Student performance.

### Description

Reports support Teacher decision-making and operational review. Reports must be scoped to the Teacher Workspace and must not expose any data from another Teacher Workspace. Reports may include archived records where historical and reporting rules require their visibility, and archived records must be clearly indicated.

Payment-related reports must distinguish between Flow A and Flow B. Teacher-facing payment reports primarily support Student fee status under Flow B, while the Teacher's Platform Subscription under Flow A remains distinct.

### Functional Requirements

- **TM-REPORT-FR-001:** The system shall provide Attendance reports for the Teacher Workspace.
- **TM-REPORT-FR-002:** The system shall provide Homework reports for the Teacher Workspace.
- **TM-REPORT-FR-003:** The system shall provide Exam result reports for the Teacher Workspace.
- **TM-REPORT-FR-004:** The system shall provide payment-status reports relevant to Teacher Workspace operations.
- **TM-REPORT-FR-005:** The system shall provide Student performance reports based on Teacher Workspace records.
- **TM-REPORT-FR-006:** The system shall include historical data in reports according to the historical data rule.
- **TM-REPORT-FR-007:** The system shall clearly indicate archived records when included in reports.
- **TM-REPORT-FR-008:** The system shall ensure Reports are scoped to the Teacher Workspace.
- **TM-REPORT-FR-009:** The system shall not process payments through Reports.
- **TM-REPORT-FR-010:** The system shall maintain separation between Flow A and Flow B in payment-related reporting.

### Business Rules

- Reports include attendance, homework, exam results, payments, and Student performance.
- Teacher Workspace isolation applies to all Reports (BR-003).
- Historical data is never deleted and must remain available (BR-014).
- Archived records remain available in reports and must be clearly indicated.
- Flow A and Flow B must never be conflated.
- Version 1 records payment status only and does not process transactions (BR-019).

### User Permissions

- Teacher: may view Reports for the Teacher's own Teacher Workspace.
- Teacher Staff: may view Reports only when granted the relevant permission by the Teacher.
- Teacher Staff report access must remain limited to the Teacher Workspace that created the Teacher Staff account.
- No Teacher or Teacher Staff user may view Reports from another Teacher Workspace.

### Inputs

- Teacher Workspace context.
- Report type: Attendance, Homework, Exam results, payments, or Student performance.
- Date or period filters.
- Educational Grade, Group, Student, or status filters where applicable.
- Authorized user context.

### Outputs

- Teacher Workspace-scoped report results.
- Historical data where applicable.
- Clear indication of archived records when included.
- Payment-status information separated by applicable financial flow.
- Report information suitable for Teacher operational review.

### Validations

- User must be authorized to view the selected report type.
- Filters must be valid and must reference records in the Teacher Workspace.
- Reports must not include another Teacher Workspace's records.
- Archived records included for historical purposes must be identified as archived.
- Payment-status reports must not imply payment processing.

### Error Handling

- If the user lacks report permission, the system shall deny access.
- If filters reference records outside the Teacher Workspace, the system shall reject the request.
- If report data is unavailable, the system shall return an appropriate empty or unavailable result without exposing technical details.
- If a payment report would mix Flow A and Flow B, the system shall prevent misleading output and preserve separation.

### Edge Cases

- A Teacher Workspace has no data for the selected report period.
- A report includes records from archived Educational Grades or Groups.
- A Student moved between Groups during the reporting period.
- Payment status is incomplete because actual payments occurred outside the Platform.
- Teacher Staff has permission for one report area but not another; unresolved permission granularity must not be assumed beyond assigned permissions.

### Acceptance Criteria

- Reports include only Teacher Workspace data.
- Reports can cover Attendance, Homework, Exam results, payments, and Student performance.
- Historical records remain available in reports.
- Archived records are clearly indicated when included.
- Payment-status reporting does not process payments.
- Flow A and Flow B remain separate in payment-related reporting.

## 8. Users

### Purpose

Users allows the Teacher to manage Teacher Staff accounts inside the Teacher Workspace and assign permissions according to the Teacher's decisions.

### Description

Teacher Staff are internal users created by the Teacher. Examples include Secretary, Assistant, and Accountant. Teacher Staff exist only inside the Teacher Workspace that created them and hold only permissions assigned by the Teacher. Permission-model granularity remains pending in the Project Context and must not be silently assumed in this SRS Part 2.

The Users area does not create Super Admin, Student, or Parent modules. It is limited to Teacher Staff management within the Teacher Workspace.

### Functional Requirements

- **TM-USERS-FR-001:** The system shall allow the Teacher to create Teacher Staff accounts inside the Teacher Workspace.
- **TM-USERS-FR-002:** The system shall allow the Teacher to view Teacher Staff accounts for the Teacher Workspace.
- **TM-USERS-FR-003:** The system shall allow the Teacher to update Teacher Staff account information.
- **TM-USERS-FR-004:** The system shall allow the Teacher to assign permissions to Teacher Staff.
- **TM-USERS-FR-005:** The system shall ensure Teacher Staff permissions apply only inside the creating Teacher Workspace.
- **TM-USERS-FR-006:** The system shall allow the Teacher to Archive Teacher Staff accounts instead of permanently deleting them.
- **TM-USERS-FR-007:** The system shall allow authorized restoration of archived Teacher Staff accounts.
- **TM-USERS-FR-008:** The system shall record Teacher Staff creation, update, Archive, restore, and permission changes in the Audit Log.
- **TM-USERS-FR-009:** The system shall attribute Teacher Staff actions to the Teacher Staff user, not to the Teacher.

### Business Rules

- Teacher Staff are created by the Teacher and exist only inside that Teacher Workspace (BR-013).
- Teacher Staff hold only permissions assigned by the Teacher (BR-013).
- Permission-model granularity is pending and must not be silently assumed (Q-011).
- Permission changes must be recorded in the Audit Log (BR-006).
- No permanent deletion is allowed; Archive must be used instead (BR-005).
- Teacher Workspace isolation applies to all Teacher Staff access (BR-003).

### User Permissions

- Teacher: may create, view, update, assign permissions, Archive, and restore Teacher Staff accounts in the Teacher's own Teacher Workspace.
- Teacher Staff: may not manage permissions unless explicitly granted permission by the Teacher under the future detailed permission model.
- Teacher Staff cannot grant themselves permissions unless explicitly allowed by future confirmed RBAC rules.
- No Teacher Staff user may access another Teacher Workspace.

### Inputs

- Teacher Staff identity and account information.
- Assigned permission selections according to the confirmed permission model when defined.
- Teacher Workspace context.
- Archive or restore action.
- Authorized user performing the action.

### Outputs

- Created or updated Teacher Staff account.
- Assigned permission state for Teacher Staff.
- Active or archived Teacher Staff status.
- Audit Log entries for account and permission actions.

### Validations

- Teacher Staff account information must be sufficient and valid according to later detailed requirements.
- Teacher Staff must be associated with exactly one creating Teacher Workspace.
- Assigned permissions must be valid under the confirmed permission model when defined.
- Permission changes must be performed by an authorized user.
- Archived Teacher Staff accounts must not be treated as active users.

### Error Handling

- If Teacher Staff information is missing or invalid, the system shall reject the action.
- If a user attempts to assign permissions outside the confirmed permission model, the system shall reject the action.
- If a Teacher Staff user attempts an unauthorized action, the system shall deny the action.
- If a Teacher Staff account is archived, the system shall prevent active use unless restored by an authorized user.
- If an action targets another Teacher Workspace, the system shall deny access.

### Edge Cases

- A Teacher creates the first Teacher Staff account in a Teacher Workspace.
- A Teacher Staff account is archived after performing historical actions; historical Audit Log entries remain attributed to that Teacher Staff user.
- A Teacher Staff user has limited permissions and attempts to access an unassigned module.
- Permission granularity remains pending, so detailed permission behavior must wait for the RBAC document.
- A Teacher Staff user belongs to one Teacher Workspace and must not gain access to another.

### Acceptance Criteria

- A Teacher can manage Teacher Staff only inside the Teacher's own Teacher Workspace.
- Teacher Staff cannot access another Teacher Workspace.
- Teacher Staff hold only Teacher-assigned permissions.
- Permission changes are recorded in the Audit Log.
- Teacher Staff actions are attributed to the Teacher Staff user.
- Teacher Staff accounts can be archived but not permanently deleted.

## 9. Settings

### Purpose

Settings allows the Teacher to manage Teacher Workspace profile and center information within the confirmed Version 1 scope.

### Description

Teacher Settings include Teacher profile, center information, phone numbers, and address. Settings belong to the Teacher Workspace and must not expose or modify another Teacher Workspace. Settings must not allow changing the Teacher's Teaching Subject, because the Teaching Subject is selected during registration and cannot be changed after account creation.

Settings is not a platform-level administration area. Platform settings are managed by the Super Admin and are outside this Teacher Module part.

### Functional Requirements

- **TM-SETTINGS-FR-001:** The system shall allow the Teacher to view Teacher Workspace settings.
- **TM-SETTINGS-FR-002:** The system shall allow the Teacher to update Teacher profile information within the Teacher Workspace.
- **TM-SETTINGS-FR-003:** The system shall allow the Teacher to update center information within the Teacher Workspace.
- **TM-SETTINGS-FR-004:** The system shall allow the Teacher to update phone numbers within the Teacher Workspace.
- **TM-SETTINGS-FR-005:** The system shall allow the Teacher to update address information within the Teacher Workspace.
- **TM-SETTINGS-FR-006:** The system shall prevent the Teacher from changing the Teaching Subject after account creation.
- **TM-SETTINGS-FR-007:** The system shall ensure Settings changes apply only to the Teacher's own Teacher Workspace.
- **TM-SETTINGS-FR-008:** The system shall record Settings updates in the Audit Log.

### Business Rules

- Teacher Settings include Teacher profile, center information, phone numbers, and address.
- Each Teacher account represents exactly one Teaching Subject (BR-016).
- The Teaching Subject is selected during registration and cannot be changed after account creation (BR-016).
- Teacher Workspace isolation applies to Settings (BR-003).
- Important updates must be recorded in the Audit Log (BR-006).
- Settings must not include Super Admin platform settings.

### User Permissions

- Teacher: may view and update Settings for the Teacher's own Teacher Workspace, except the Teaching Subject cannot be changed.
- Teacher Staff: may view or update Settings only when granted the relevant permission by the Teacher.
- Teacher Staff cannot change the Teaching Subject.
- No user may view or update Settings for another Teacher Workspace.

### Inputs

- Teacher profile information.
- Center information.
- Phone numbers.
- Address information.
- Current Teacher Workspace context.
- Authorized user performing the update.

### Outputs

- Updated Teacher Workspace settings.
- Unchanged Teaching Subject after account creation.
- Audit Log entries for Settings updates.

### Validations

- Settings inputs must be valid according to later detailed requirements.
- Settings updates must be scoped to the current Teacher Workspace.
- Teaching Subject updates must be rejected after account creation.
- User must be authorized to view or update Settings.
- Updates must not affect platform-level settings or another Teacher Workspace.

### Error Handling

- If Settings inputs are missing or invalid, the system shall reject the update.
- If a user attempts to change the Teaching Subject after account creation, the system shall reject the update.
- If the user lacks permission, the system shall deny the action.
- If an update targets another Teacher Workspace, the system shall deny access.
- If an update fails, the system shall preserve the previous valid Settings information.

### Edge Cases

- A Teacher Workspace has incomplete center information during early setup.
- A Teacher attempts to change the Teaching Subject after registration.
- A Teacher Staff user has permission to view Settings but not update them.
- Phone number or address information changes after Students are already enrolled.
- Settings updates occur after archived records exist; historical records remain preserved.

### Acceptance Criteria

- A Teacher can update Teacher profile, center information, phone numbers, and address for the Teacher's own Teacher Workspace.
- Teaching Subject cannot be changed after account creation.
- Teacher Staff access to Settings depends on Teacher-assigned permissions.
- Settings changes do not affect another Teacher Workspace.
- Settings updates are recorded in the Audit Log.
- Platform-level settings are not managed through Teacher Settings.

---

*End of PART 2 — Teacher Module.*

---

# PART 3 — Student Module

This Part 3 defines the Student Module requirements only. It is limited to Student capabilities confirmed in the Project Context, Project Vision, and earlier SRS context. It does not define the Parent Module, Super Admin Module, APIs, database tables, UI implementation details, or source code.

The Student Module is governed by the following mandatory principles:

- A Student has exactly one global account and may study with multiple Teachers.
- Student data is partitioned per Teacher, including Attendance, Homework, Exams, Lessons, and Subscription-related status.
- A Student belongs to only one Group per Teacher at any time.
- A Student can be created by self-registration or manually by a Teacher, but duplicate Student accounts are not allowed.
- If a Teacher creates the Student account, the Student can later activate and use the same account.
- Lessons are visible only from the Student's own Teachers.
- The Student Module must not expose one Teacher's private Teacher Workspace data to another Teacher or to unrelated Students.
- Version 1 is a Web Application only.
- Version 1 records payment status only and does not process transactions.

## 1. Dashboard

### Purpose

The Student Dashboard provides a high-level summary of the Student's learning activity across the Teachers with whom the Student is enrolled. Its purpose is to reduce fragmentation by giving the Student one account-level starting point while preserving strict per-Teacher data separation.

### Description

The Student Dashboard is a Student-facing summary area. It may summarize the Student's schedule, Homework, Lessons, Exams, and per-Teacher Subscription-related status. It must not combine Teacher Workspace ownership or expose private Teacher data beyond what is assigned to or relevant to the Student.

The Dashboard must reflect the core product vision: one Student account, multiple Teachers, and per-Teacher partitioned learning records. It is not a Teacher Workspace, a Parent monitoring area, or a platform administration area.

### Functional Requirements

- **SM-DASH-FR-001:** The system shall provide the Student with a summary of learning activity associated with the Student's account.
- **SM-DASH-FR-002:** The system shall group or distinguish summarized information by Teacher where the information belongs to a specific Teacher relationship.
- **SM-DASH-FR-003:** The system shall summarize My Schedule information based on the Student's active Group relationships with Teachers.
- **SM-DASH-FR-004:** The system shall summarize Homework assigned to the Student by the Student's Teachers.
- **SM-DASH-FR-005:** The system shall summarize Lessons available to the Student from the Student's own Teachers.
- **SM-DASH-FR-006:** The system shall summarize Exams available to or completed by the Student within each relevant Teacher relationship.
- **SM-DASH-FR-007:** The system shall summarize per-Teacher Flow B payment status without enabling payment processing.
- **SM-DASH-FR-008:** The system shall not display content, Homework, Lessons, Exams, or status from Teachers with whom the Student is not enrolled.
- **SM-DASH-FR-009:** The system shall preserve separation between each Teacher's data in all Dashboard summaries.
- **SM-DASH-FR-010:** The system shall support Student access through the web application only for Version 1.

### Business Rules

- A Student has exactly one global account and may study with multiple Teachers (BR-001).
- Student records are separated per Teacher (BR-001, BR-003, BR-012).
- Lessons are Teacher-owned and private to the Teacher's own Students (BR-018).
- Version 1 records payment status only and does not process transactions (BR-019).
- Flow B Student fee status must not be confused with Flow A Teacher Platform Subscription.
- Archived records must not appear as active records, while historical data remains available where applicable (BR-005, BR-014).

### User Permissions

- Student: may view Dashboard information associated with the Student's own account and Teacher relationships.
- Student: may not view another Student's Dashboard information.
- Student: may not view Teacher Workspace data except the content and records assigned or relevant to that Student.
- Teacher, Teacher Staff, Parent, and Super Admin access is outside this Student Module section.

### Inputs

- Authenticated Student account context.
- Student's active Teacher relationships.
- Student's Group associations per Teacher.
- Existing schedule, Homework, Lesson, Exam, and Flow B payment-status records relevant to the Student.
- Optional Teacher or period filters where supported by later detailed requirements.

### Outputs

- Student learning summary scoped to the Student account.
- Per-Teacher summary of relevant learning activity.
- Schedule, Homework, Lesson, Exam, and Flow B status indicators.
- Clear separation between information from different Teachers.
- Empty-state output where the Student has no current Teacher relationships or assigned activity.

### Validations

- The user must be authenticated as the Student or otherwise operating under the Student's own account context.
- Dashboard information must belong to the Student account.
- Teacher-specific information must be associated with a Teacher relationship for that Student.
- Archived records must not be presented as active records.
- Payment-status information must be shown as status only and must not initiate payment processing.

### Error Handling

- If the user is not authenticated, the system shall deny access.
- If requested Dashboard information does not belong to the Student, the system shall deny access.
- If Teacher-specific information is unavailable or not assigned to the Student, the system shall not expose it.
- If summary data cannot be retrieved, the system shall provide an appropriate unavailable state without exposing technical details.
- If filters are invalid, the system shall reject the request and require valid values.

### Edge Cases

- The Student has a newly created account but no Teacher relationships yet.
- The Student studies with multiple Teachers and has overlapping activity across Teachers.
- One Teacher relationship has archived records while another remains active.
- A Teacher-created Student account has not yet been fully activated by the Student.
- Payment-status records may be incomplete because actual payments are handled outside the Platform.

### Acceptance Criteria

- The Student Dashboard displays only information belonging to the Student account.
- Dashboard summaries clearly preserve per-Teacher separation.
- The Dashboard does not expose another Student's information.
- The Dashboard does not expose Teacher-private content unrelated to the Student.
- Flow B status is displayed only as payment status and not as an online payment action.
- A Student with no current activity is handled without error.

## 2. My Schedule

### Purpose

My Schedule allows the Student to view schedule information across the Teachers with whom the Student is enrolled. Its purpose is to provide one unified schedule experience while maintaining the per-Teacher relationship boundaries required by the Project Context.

### Description

A Group carries a Schedule. Because a Student may study with multiple Teachers and belongs to only one Group per Teacher at any time, My Schedule must present the Student's schedule based on the Student's current Group relationship for each Teacher. Schedule information must remain associated with the correct Teacher, Educational Grade, and Group context.

My Schedule does not allow the Student to change Group assignment, Teacher settings, or Group Schedule. Group management belongs to the Teacher Module.

### Functional Requirements

- **SM-SCHED-FR-001:** The system shall allow the Student to view schedule information for the Student's active Teacher relationships.
- **SM-SCHED-FR-002:** The system shall derive schedule information from the Student's Group under each Teacher.
- **SM-SCHED-FR-003:** The system shall distinguish schedule entries by Teacher.
- **SM-SCHED-FR-004:** The system shall include Group context for schedule entries where applicable.
- **SM-SCHED-FR-005:** The system shall not show schedules from Teachers with whom the Student is not enrolled.
- **SM-SCHED-FR-006:** The system shall preserve historical schedule context for past records where historical reporting requires it.
- **SM-SCHED-FR-007:** The system shall prevent the Student from changing Group Schedule or Group assignment through My Schedule.
- **SM-SCHED-FR-008:** The system shall support schedule access through the web application only in Version 1.

### Business Rules

- A Student may study with multiple Teachers using one account (BR-001).
- A Student belongs to only one Group per Teacher at any time (BR-002).
- Each Group carries a Schedule (Project Context §7.1).
- Student transfers preserve historical attendance, homework, exams, and grades (BR-007).
- Teacher Workspace isolation applies to schedule data ownership (BR-003).
- Archived Groups must not appear as active schedule sources unless historical context is being shown.

### User Permissions

- Student: may view schedule information associated with the Student's own Teacher relationships.
- Student: may not edit Group Schedule.
- Student: may not assign or move themselves between Groups.
- Student: may not view schedules for unrelated Teacher Workspaces.

### Inputs

- Authenticated Student account context.
- Student's current Teacher relationships.
- Student's current Group per Teacher.
- Group Schedule information associated with each current Group.
- Optional period or Teacher filter where supported by later detailed requirements.

### Outputs

- Student schedule entries associated with the Student's Teachers.
- Teacher and Group context for schedule entries.
- Empty schedule state when no schedule exists for current Teacher relationships.
- Historical indication where archived or past schedule context is included for history.

### Validations

- The Student must be authenticated.
- Each displayed schedule entry must be tied to the Student's current or historically relevant relationship with a Teacher.
- The Student must have only one current Group per Teacher.
- Schedule filters must reference Teachers or periods available to the Student.
- Archived Groups must not be presented as active current schedule sources.

### Error Handling

- If the Student is not authenticated, the system shall deny access.
- If a requested schedule entry belongs to an unrelated Teacher, the system shall deny access.
- If schedule data is unavailable, the system shall provide an appropriate empty or unavailable state.
- If a filter references a Teacher relationship not associated with the Student, the system shall reject the request.
- If schedule data appears inconsistent with the one Group per Teacher rule, the system shall prevent misleading output and require correction through authorized Teacher-side operations.

### Edge Cases

- The Student has no active Group under any Teacher.
- The Student studies with multiple Teachers with schedule entries in the same period.
- A Teacher has not defined a Schedule for the Student's Group.
- The Student was moved between Groups and historical records remain associated with the previous Group.
- A Group is archived after historical schedule-related records exist.

### Acceptance Criteria

- My Schedule shows only schedule entries associated with the Student's Teacher relationships.
- Schedule entries remain distinguishable by Teacher.
- A Student cannot edit Group Schedule or Group assignment from My Schedule.
- The one Group per Teacher rule is preserved.
- Archived Groups are not shown as active schedule sources.
- Missing schedule data is handled without exposing unrelated Teacher data.

## 3. Homework

### Purpose

Homework allows the Student to view and respond to Homework assigned by the Student's Teachers within the supported Version 1 formats.

### Description

Homework is assigned within a Teacher Workspace and is visible to the Student only when it is relevant to the Student's Teacher relationship. Homework supports Text, Image, and PDF only. Video homework is not supported in Version 1.

Homework records must remain partitioned per Teacher. A Student studying with multiple Teachers may have Homework from several Teachers in one account, but each Homework item remains owned by and associated with the assigning Teacher Workspace.

### Functional Requirements

- **SM-HW-FR-001:** The system shall allow the Student to view Homework assigned to the Student by the Student's Teachers.
- **SM-HW-FR-002:** The system shall distinguish Homework by assigning Teacher.
- **SM-HW-FR-003:** The system shall support Homework content and submissions using Text, Image, and PDF formats only.
- **SM-HW-FR-004:** The system shall prevent video homework submission or handling in Version 1.
- **SM-HW-FR-005:** The system shall allow the Student to submit Homework responses where the Homework requires Student submission.
- **SM-HW-FR-006:** The system shall allow the Student to view Homework status relevant to the Student.
- **SM-HW-FR-007:** The system shall allow the Student to view grading or feedback status where such Teacher-side grading exists.
- **SM-HW-FR-008:** The system shall not expose Homework assigned by Teachers with whom the Student is not enrolled.
- **SM-HW-FR-009:** The system shall preserve Homework history when a Student moves between Groups under the same Teacher.
- **SM-HW-FR-010:** The system shall record Homework submission or modification actions in the Audit Log where they qualify as important actions under the Audit Log Policy.

### Business Rules

- Homework supports Text, Image, and PDF only (BR-021).
- Video homework is not supported in Version 1 (BR-021).
- Student data is partitioned per Teacher (BR-001, BR-003).
- Student transfers preserve historical Homework (BR-007).
- Homework modification actions are recorded in the Audit Log (BR-006).
- Archived Homework must not appear as active Homework, while historical data remains available where applicable (BR-005, BR-014).

### User Permissions

- Student: may view Homework assigned to the Student through the Student's Teacher relationships.
- Student: may submit Homework responses only for Homework assigned to that Student and only in supported formats.
- Student: may not create Teacher Homework assignments.
- Student: may not grade Homework.
- Student: may not access another Student's Homework.
- Student: may not access Homework from unrelated Teacher Workspaces.

### Inputs

- Authenticated Student account context.
- Selected Homework item assigned to the Student.
- Text response, Image file, or PDF file where submission is required.
- Teacher or status filters where supported by later detailed requirements.

### Outputs

- Homework list scoped to the Student.
- Homework details relevant to the Student.
- Homework submission status.
- Grading or feedback status where available.
- Audit Log entry for applicable Homework submission or modification actions.

### Validations

- The Student must be authenticated.
- Homework must belong to a Teacher relationship associated with the Student.
- Submitted Homework format must be Text, Image, or PDF only.
- Video submissions must be rejected.
- Homework submissions must be associated with the correct Student and Teacher relationship.
- Archived Homework must not be treated as active assigned Homework.

### Error Handling

- If the Student is not authenticated, the system shall deny access.
- If the Homework does not belong to the Student, the system shall deny access.
- If the submitted format is unsupported, the system shall reject the submission.
- If the Homework is archived or no longer active for submission, the system shall prevent active submission unless later detailed requirements allow historical viewing only.
- If submission fails, the system shall provide an appropriate failure state without exposing technical details.

### Edge Cases

- The Student has no Homework assigned.
- The Student has Homework assigned by multiple Teachers.
- A Homework item was assigned before the Student moved Groups under the same Teacher.
- A Homework item is archived after submission exists.
- The Student attempts to submit video homework.
- Grading or feedback is not yet available.

### Acceptance Criteria

- The Student can view only Homework assigned to the Student.
- Homework remains clearly associated with the assigning Teacher.
- Only Text, Image, and PDF Homework formats are supported.
- Video homework is rejected or unavailable in Version 1.
- Homework history remains available after Group movement where applicable.
- The Student cannot access another Student's Homework.

## 4. Lessons

### Purpose

Lessons allows the Student to access private Teacher-owned lesson videos made available by the Student's own Teachers.

### Description

Lessons are video content uploaded by a Teacher exclusively for that Teacher's own Students. Lessons are private to the Teacher Workspace. A Student may study with multiple Teachers and may see Lessons from each of the Student's Teachers, but no cross-Teacher content discovery or marketplace behavior is allowed.

Lessons must not be treated as courses for sale. The Platform is not an online course marketplace, and Teachers do not sell courses through the Platform.

### Functional Requirements

- **SM-LESSON-FR-001:** The system shall allow the Student to view Lessons made available by the Student's own Teachers.
- **SM-LESSON-FR-002:** The system shall distinguish Lessons by Teacher.
- **SM-LESSON-FR-003:** The system shall prevent the Student from accessing Lessons from Teachers with whom the Student is not enrolled.
- **SM-LESSON-FR-004:** The system shall prevent cross-Teacher Lesson discovery or browsing.
- **SM-LESSON-FR-005:** The system shall prevent one Teacher's Lessons from being exposed to another Teacher's Students.
- **SM-LESSON-FR-006:** The system shall not present Lessons as marketplace courses.
- **SM-LESSON-FR-007:** The system shall exclude archived Lessons from active Lesson access while preserving historical records according to the Archive Policy.
- **SM-LESSON-FR-008:** The system shall support Lesson access through the web application only in Version 1.

### Business Rules

- Lesson videos are Teacher-owned and private (BR-018).
- A Teacher may upload Lessons exclusively for the Teacher's own Students (BR-018).
- No cross-Teacher Lesson access exists (BR-018, BR-003).
- The Platform is not an online course marketplace.
- Teachers do not sell courses through the Platform.
- There is no course discovery or browsing across Teachers.
- Archive replaces permanent deletion and archived records must not appear as active records (BR-005).

### User Permissions

- Student: may view Lessons made available by Teachers with whom the Student is enrolled.
- Student: may not upload Lessons.
- Student: may not access another Student's private Lesson relationship data.
- Student: may not access Lessons from unrelated Teachers.
- Student: may not browse Lessons across Teachers as marketplace content.

### Inputs

- Authenticated Student account context.
- Student's Teacher relationships.
- Selected Lesson associated with one of the Student's Teachers.
- Teacher filter where supported by later detailed requirements.

### Outputs

- List of Lessons available to the Student from the Student's own Teachers.
- Lesson access result for an authorized Student relationship.
- Teacher association for each Lesson.
- Empty-state output where no Lessons are available.

### Validations

- The Student must be authenticated.
- The Lesson must belong to a Teacher with whom the Student has a valid relationship.
- The Lesson must not belong to another Teacher Workspace unrelated to the Student.
- The Lesson must not be archived for active access.
- Lesson access must not create cross-Teacher visibility.

### Error Handling

- If the Student is not authenticated, the system shall deny access.
- If the Lesson is not associated with one of the Student's Teachers, the system shall deny access.
- If the Lesson is archived, the system shall prevent active Lesson access according to the Archive Policy.
- If Lesson content is unavailable, the system shall provide an appropriate unavailable state without exposing Teacher-private storage or implementation details.
- If a Teacher filter references an unrelated Teacher, the system shall reject the request.

### Edge Cases

- The Student has no available Lessons.
- The Student studies with multiple Teachers and each Teacher has separate Lessons.
- A Lesson is archived after the Student previously accessed it.
- The Student moves Groups under the same Teacher and Lesson availability depends on the Student's current Teacher relationship as defined by later detailed requirements.
- A Student attempts to access a Lesson through an unrelated Teacher relationship.

### Acceptance Criteria

- The Student can access Lessons only from the Student's own Teachers.
- Lessons remain clearly associated with the owning Teacher.
- Cross-Teacher Lesson discovery is not available.
- Lessons are not presented as marketplace courses.
- Archived Lessons are not available as active Lessons.
- Unauthorized Lesson access is denied.

## 5. Exams

### Purpose

Exams allows the Student to take and review Exams assigned through the Student's Teacher relationships while preserving per-Teacher Exam separation.

### Description

Exams are built from the Teacher's private Question Bank. Supported question types are Multiple Choice, True/False, Essay, and Bubble Sheet. Bubble Sheet is an electronic exam format simulating traditional paper bubble sheets; Students answer by selecting bubbles on screen, and automatic grading is supported.

Exam definitions, attempts, and grades are workspace-scoped. A Student may have Exams from multiple Teachers, but each Exam, attempt, and grade remains associated with the owning Teacher Workspace.

### Functional Requirements

- **SM-EXAM-FR-001:** The system shall allow the Student to view Exams assigned or made available to the Student through the Student's Teacher relationships.
- **SM-EXAM-FR-002:** The system shall distinguish Exams by Teacher.
- **SM-EXAM-FR-003:** The system shall allow the Student to answer supported Exam question types: Multiple Choice, True/False, Essay, and Bubble Sheet.
- **SM-EXAM-FR-004:** The system shall support Bubble Sheet answering through electronic on-screen selection.
- **SM-EXAM-FR-005:** The system shall support automatic grading for question behavior confirmed as automatically gradable, including Bubble Sheet where applicable.
- **SM-EXAM-FR-006:** The system shall allow the Student to view Exam attempt status and grade information where available to the Student.
- **SM-EXAM-FR-007:** The system shall prevent the Student from accessing Exams from Teachers with whom the Student is not enrolled.
- **SM-EXAM-FR-008:** The system shall preserve Exam attempt and grade history when a Student moves between Groups under the same Teacher.
- **SM-EXAM-FR-009:** The system shall prevent the Student from accessing the Teacher's private Question Bank outside the context of assigned or available Exams.
- **SM-EXAM-FR-010:** The system shall record Exam attempt or submission events in the Audit Log where they qualify as important actions under the Audit Log Policy.

### Business Rules

- The Question Bank is Teacher-owned and private (BR-011).
- Exams are composed only from the owning Teacher's Question Bank (BR-011).
- Supported question types are Multiple Choice, True/False, Essay, and Bubble Sheet (BR-011).
- Exam definitions, attempts, and grades are workspace-scoped (BR-012).
- Student transfers preserve historical Exams and grades (BR-007).
- Teacher Workspace isolation applies to Exams and grades (BR-003).
- Exam modification actions are recorded in the Audit Log (BR-006).
- Archived Exams must not appear as active Exams unless historical viewing is applicable.

### User Permissions

- Student: may view and attempt Exams assigned or made available to the Student through the Student's Teacher relationships.
- Student: may view the Student's own Exam attempt status and grades where available.
- Student: may not access another Student's Exam attempts or grades.
- Student: may not access a Teacher's private Question Bank outside assigned or available Exam context.
- Student: may not create, edit, publish, Archive, or grade Exams.

### Inputs

- Authenticated Student account context.
- Selected Exam assigned or available to the Student.
- Student answers for Multiple Choice, True/False, Essay, or Bubble Sheet questions.
- Exam submission action.
- Teacher filter where supported by later detailed requirements.

### Outputs

- Student-visible Exam list.
- Exam attempt state for the Student.
- Submitted answers associated with the Student and Teacher relationship.
- Grade or result information where available.
- Audit Log entry for applicable Exam attempt or submission events.

### Validations

- The Student must be authenticated.
- The Exam must belong to a Teacher relationship associated with the Student.
- Answer input must match the supported question type.
- Bubble Sheet answers must use valid on-screen selections for the applicable Bubble Sheet structure.
- The Student must not access another Student's attempt or grade.
- Archived Exams must not be treated as active Exams.

### Error Handling

- If the Student is not authenticated, the system shall deny access.
- If the Exam is not assigned or available to the Student, the system shall deny access.
- If answer input is invalid for the question type, the system shall reject the answer or submission according to detailed requirements.
- If an Exam is archived or inactive, the system shall prevent active attempts unless historical viewing is allowed.
- If result information is not yet available, the system shall indicate that the result is unavailable rather than inventing a grade.

### Edge Cases

- The Student has no Exams assigned or available.
- The Student has Exams from multiple Teachers.
- The Student moves Groups after completing an Exam; historical attempt and grade information remains preserved.
- An Essay answer requires grading before final result availability.
- A Bubble Sheet Exam uses automatic grading.
- An Exam is archived after the Student completed an attempt.

### Acceptance Criteria

- The Student can access only Exams associated with the Student's Teacher relationships.
- Exams remain clearly separated by Teacher.
- Supported question types are limited to Multiple Choice, True/False, Essay, and Bubble Sheet.
- Bubble Sheet answering uses electronic on-screen selection.
- The Student cannot access the Teacher's private Question Bank outside assigned or available Exams.
- Exam attempt and grade history remains preserved after Group movement.

## 6. Subscriptions

### Purpose

Subscriptions allows the Student to view per-Teacher payment-status information related to Flow B Student fees. Its purpose is to give the Student one place to understand fee status across Teachers while maintaining the required separation between Flow A and Flow B.

### Description

In the Student Module, the Subscriptions section shows per-Teacher Flow B status. Flow B represents Student or Parent fees owed to a Teacher, derived from the Group's Price and Pricing Type. This section must not be confused with Flow A, which is the Teacher's monthly Platform Subscription paid to the Platform.

Version 1 records payment status only. Actual Student fee payments are handled outside the Platform. The Student must not be offered an online payment gateway or in-platform transaction processing in Version 1.

### Functional Requirements

- **SM-SUB-FR-001:** The system shall allow the Student to view per-Teacher Flow B payment status.
- **SM-SUB-FR-002:** The system shall distinguish payment-status information by Teacher.
- **SM-SUB-FR-003:** The system shall derive Student fee status from Group enrollment, Group Price, and Pricing Type where applicable.
- **SM-SUB-FR-004:** The system shall support Pricing Type values of Monthly and Per Lesson as the fee basis for Groups.
- **SM-SUB-FR-005:** The system shall not process payments in Version 1.
- **SM-SUB-FR-006:** The system shall not present online payment gateway actions in Version 1.
- **SM-SUB-FR-007:** The system shall prevent Flow B Student fee status from being presented as Flow A Teacher Platform Subscription status.
- **SM-SUB-FR-008:** The system shall show only payment-status information related to the Student's own Teacher relationships.
- **SM-SUB-FR-009:** The system shall preserve historical payment-status records according to the Archive and historical data rules.

### Business Rules

- Flow A and Flow B are separate financial flows and must never be conflated.
- Flow B is Student or Parent to Teacher fees derived from Group Price and Pricing Type (BR-009).
- Pricing Type is Monthly or Per Lesson (BR-009).
- Version 1 records payment status only and does not process transactions (BR-019).
- Online payment gateways are out of scope for Version 1 (BR-019).
- Student fee status is partitioned per Teacher (BR-001, BR-003).
- Historical data remains available (BR-014).

### User Permissions

- Student: may view the Student's own per-Teacher Flow B payment status.
- Student: may not view another Student's payment status.
- Student: may not manage Teacher Platform Subscription status under Flow A.
- Student: may not process payments through the Platform in Version 1.
- Student: may not modify payment status unless a later confirmed requirement explicitly allows it.

### Inputs

- Authenticated Student account context.
- Student's Teacher relationships.
- Group Price and Pricing Type associated with the Student's Group under each Teacher.
- Recorded Flow B payment-status records.
- Optional Teacher or period filters where supported by later detailed requirements.

### Outputs

- Per-Teacher Flow B payment-status information for the Student.
- Group pricing basis where applicable and available to the Student.
- Clear indication that status is recorded only and not an in-platform transaction.
- Empty-state output where no payment-status records exist.

### Validations

- The Student must be authenticated.
- Payment-status information must belong to the Student.
- Payment-status information must be associated with a Teacher relationship for the Student.
- Pricing Type must be Monthly or Per Lesson.
- Flow A Teacher Platform Subscription data must not be exposed as Student fee status.
- The system must not initiate payment processing.

### Error Handling

- If the Student is not authenticated, the system shall deny access.
- If payment-status information belongs to another Student, the system shall deny access.
- If a Teacher filter references an unrelated Teacher, the system shall reject the request.
- If payment-status data is unavailable, the system shall provide an appropriate unavailable or empty state.
- If a payment action is attempted, the system shall reject it because Version 1 records status only.

### Edge Cases

- The Student has no recorded Flow B payment-status records.
- The Student studies with multiple Teachers using different Group Pricing Types.
- Payment status is outdated because the actual payment occurred outside the Platform.
- A Student moved Groups and historical payment-status records must remain available.
- A Teacher's Group Price or Pricing Type changed after historical payment-status records existed.

### Acceptance Criteria

- The Student can view only the Student's own per-Teacher Flow B status.
- Flow B status is clearly separated from Flow A Teacher Platform Subscription.
- The system does not process payments.
- The system does not present online payment gateway behavior.
- Payment-status information remains associated with the correct Teacher.
- Historical payment-status records remain available according to the historical data rules.

## 7. Settings

### Purpose

Settings allows the Student to manage the Student's own account-related information and activation state within the confirmed Version 1 boundaries.

### Description

The Student has one global account. The Student may create that account through self-registration, or a Teacher may create the Student account manually. If the Teacher creates the account, the Student can later activate and use the same account. Settings must support the Student's own account context without creating duplicate accounts or exposing Teacher Workspace administration.

Student Settings must not allow the Student to manage Teacher Workspace settings, Group assignment, Teacher Staff permissions, Parent Module behavior, Super Admin settings, or another Student's account.

### Functional Requirements

- **SM-SETTINGS-FR-001:** The system shall allow the Student to view the Student's own account settings.
- **SM-SETTINGS-FR-002:** The system shall allow the Student to update the Student's own account information according to later detailed requirements.
- **SM-SETTINGS-FR-003:** The system shall support activation of a Teacher-created Student account by the Student.
- **SM-SETTINGS-FR-004:** The system shall prevent Student Settings actions from creating duplicate Student accounts.
- **SM-SETTINGS-FR-005:** The system shall prevent the Student from changing Teacher Workspace data through Student Settings.
- **SM-SETTINGS-FR-006:** The system shall prevent the Student from assigning themselves to a Group or moving themselves between Groups through Student Settings.
- **SM-SETTINGS-FR-007:** The system shall prevent the Student from accessing or updating another Student's account settings.
- **SM-SETTINGS-FR-008:** The system shall record important account updates, activation, and login events in the Audit Log according to the Audit Log Policy.

### Business Rules

- A Student has exactly one global account (BR-001).
- Student Registration supports self-registration and Teacher-created accounts (BR-022).
- If a Teacher creates the Student account, the Student can later activate and use the same account (BR-022).
- Duplicate Student accounts are not allowed (BR-022).
- A Student belongs to only one Group per Teacher at any time, and Group movement is managed through authorized Teacher-side operations (BR-002, BR-007).
- Important actions are recorded in the Audit Log (BR-006).
- Version 1 is delivered as a Web Application only (BR-017).

### User Permissions

- Student: may view and update the Student's own account settings according to confirmed detailed requirements.
- Student: may activate the Student's own Teacher-created account.
- Student: may not access another Student's settings.
- Student: may not manage Teacher Workspace settings.
- Student: may not manage Group assignment or Teacher relationships through Settings unless a later confirmed requirement explicitly defines such behavior.
- Student: may not manage Parent account access from this section unless a later confirmed requirement explicitly defines it.

### Inputs

- Authenticated Student account context.
- Student account information updates according to later detailed requirements.
- Account activation information for Teacher-created accounts.
- Current account state.

### Outputs

- Updated Student account settings where authorized and valid.
- Activated Student account state where applicable.
- Rejection of duplicate-account creation attempts.
- Audit Log entries for important account updates, activation, and login events.

### Validations

- The Student must be authenticated for account-setting access, except for activation flows defined by later detailed requirements.
- Account updates must apply only to the Student's own account.
- Account activation must apply only to the matching Teacher-created Student account.
- Account changes must not create duplicate Student accounts.
- Student Settings must not alter Teacher Workspace settings or Group assignment.
- Account inputs must meet later detailed identity and validation requirements.

### Error Handling

- If the Student is not authenticated where authentication is required, the system shall deny access.
- If account update information is invalid, the system shall reject the update.
- If an activation attempt does not match a Teacher-created Student account, the system shall reject activation.
- If an update would create a duplicate Student account, the system shall reject the update.
- If the Student attempts to change Group assignment or Teacher Workspace settings, the system shall deny the action.
- If an update fails, the system shall preserve the previous valid account state.

### Edge Cases

- The Student account was created manually by a Teacher and has not yet been activated by the Student.
- The Student attempts to self-register when an account already exists.
- The Student studies with multiple Teachers and account settings must remain global, while learning records remain per Teacher.
- The Student attempts to update information in a way that conflicts with duplicate-account prevention.
- The Student has historical records under one or more Teachers and account updates must not rewrite history.

### Acceptance Criteria

- The Student can access only the Student's own Settings.
- A Teacher-created Student account can later be activated by the Student.
- Student Settings do not create duplicate Student accounts.
- Student Settings do not change Teacher Workspace settings.
- Student Settings do not allow self-directed Group movement.
- Important account actions are recorded in the Audit Log according to the Audit Log Policy.

---

*End of PART 3 — Student Module.*

---

# PART 4 — Parent Module

This Part 4 defines the Parent Module requirements only. It is limited to Parent capabilities confirmed in the Project Context, Project Vision, and earlier SRS context. It does not define the Super Admin Module, APIs, database tables, UI implementation details, or source code.

The Parent Module is governed by the following mandatory principles:

- A Parent has one account and may monitor multiple linked Students.
- Version 1 supports exactly one Parent account per Student.
- A Parent sees only linked Students.
- Parent access is read-only everywhere.
- A Parent cannot modify Attendance.
- A Parent cannot modify grades.
- A Parent cannot modify Homework.
- A Parent can switch between linked Students through the Student Switcher.
- A Parent can view payment status only.
- Parent payment visibility relates to Flow B records for linked Students.
- Version 1 records payment status only and does not process payments.
- Parent access must preserve Teacher Workspace isolation and per-Teacher partitioning of Student records.

## 1. Dashboard

### Purpose

The Parent Dashboard provides a read-only summary of the linked Students that the Parent is allowed to monitor. Its purpose is to give the Parent a consolidated view of relevant Student information without allowing the Parent to modify educational records or Teacher Workspace data.

### Description

The Parent Dashboard is a monitoring area for linked Students only. It may summarize Homework, Attendance, Exams, Teachers, and Payments information for the selected linked Student or for multiple linked Students where appropriate. All information must remain read-only and must be separated by Student and Teacher relationship.

The Dashboard must not expose unlinked Students, unrelated Teacher Workspace data, platform-level administration data, or any modification capability. The Parent's role is observational and limited to the Students linked to the Parent account.

### Functional Requirements

- **PM-DASH-FR-001:** The system shall allow the Parent to view a Dashboard summary for linked Students only.
- **PM-DASH-FR-002:** The system shall prevent the Parent from viewing Dashboard information for unlinked Students.
- **PM-DASH-FR-003:** The system shall present Student-related summary information in a read-only manner.
- **PM-DASH-FR-004:** The system shall summarize Homework information for linked Students where available.
- **PM-DASH-FR-005:** The system shall summarize Attendance information for linked Students where available.
- **PM-DASH-FR-006:** The system shall summarize Exam information and grades for linked Students where available.
- **PM-DASH-FR-007:** The system shall summarize Teachers associated with linked Students where available.
- **PM-DASH-FR-008:** The system shall summarize Flow B payment-status information for linked Students where available.
- **PM-DASH-FR-009:** The system shall preserve per-Teacher separation when showing linked Student information.
- **PM-DASH-FR-010:** The system shall prevent Dashboard actions that modify Attendance, grades, Homework, Exams, payment status, Student data, or Teacher Workspace data.

### Business Rules

- One Parent account can be linked to multiple Students (BR-020).
- One Student can have only one Parent account in Version 1 (BR-020).
- A Parent sees only linked Students (BR-004).
- Parent access is read-only everywhere (BR-004).
- Student data is partitioned per Teacher (BR-001).
- Teacher Workspace isolation must be preserved (BR-003).
- Version 1 records payment status only and does not process transactions (BR-019).

### User Permissions

- Parent: may view Dashboard information for linked Students only.
- Parent: may not view information for unlinked Students.
- Parent: may not modify Attendance, grades, Homework, Exams, payment status, Student records, Teacher records, or Teacher Workspace data.
- Parent: may not operate as a Teacher, Teacher Staff, Student, or Super Admin through the Parent Dashboard.

### Inputs

- Authenticated Parent account context.
- Linked Student relationships.
- Selected Student context where applicable.
- Existing Homework, Attendance, Exam, Teacher, and payment-status records for linked Students.
- Optional Student, Teacher, or period filters where supported by later detailed requirements.

### Outputs

- Read-only Dashboard summary for linked Students.
- Student-specific summary information.
- Teacher-partitioned Homework, Attendance, Exam, Teacher, and payment-status indicators.
- Empty-state output when no linked Students or no relevant records exist.

### Validations

- The Parent must be authenticated.
- Each displayed Student must be linked to the Parent account.
- Each displayed record must belong to a linked Student.
- Teacher-specific data must be shown only where it is part of a linked Student relationship.
- All Dashboard information must be read-only.
- Payment information must be status-only and must not initiate payment processing.

### Error Handling

- If the Parent is not authenticated, the system shall deny access.
- If the Parent requests data for an unlinked Student, the system shall deny access.
- If requested Teacher-specific data is not associated with a linked Student, the system shall deny access.
- If Dashboard data is unavailable, the system shall provide an appropriate unavailable or empty state without exposing technical details.
- If the Parent attempts a modification action, the system shall reject the action.

### Edge Cases

- The Parent account has no linked Students.
- The Parent has multiple linked Students and must choose or switch between them.
- A linked Student studies with multiple Teachers.
- A linked Student has archived historical records that may appear in historical contexts but not as active records.
- Payment status may be incomplete because actual payments are handled outside the Platform.

### Acceptance Criteria

- The Parent Dashboard displays only linked Student information.
- The Dashboard is read-only.
- The Dashboard preserves Student and Teacher separation.
- The Parent cannot modify Attendance, grades, Homework, Exams, payment status, or Teacher Workspace data.
- Unlinked Student information is never shown.
- Empty linked-Student or no-data states are handled without error.

## 2. Student Switcher

### Purpose

The Student Switcher allows the Parent to switch between linked Students under one Parent account. Its purpose is to support the confirmed rule that one Parent account may monitor multiple Students while ensuring that the Parent sees only linked Students.

### Description

The Student Switcher is the Parent's navigation mechanism for selecting which linked Student's information is currently being viewed. Version 1 supports exactly one Parent account per Student, but one Parent account may be linked to multiple Students. The Student Switcher must never display unlinked Students.

Switching Students changes the Parent's current monitoring context only. It does not modify Student records, Teacher records, Attendance, Homework, Exams, grades, or payment status.

### Functional Requirements

- **PM-SWITCH-FR-001:** The system shall allow the Parent to view the list of Students linked to the Parent account.
- **PM-SWITCH-FR-002:** The system shall allow the Parent to select one linked Student as the current Student context.
- **PM-SWITCH-FR-003:** The system shall update Parent Module views to reflect the selected linked Student context.
- **PM-SWITCH-FR-004:** The system shall prevent unlinked Students from appearing in the Student Switcher.
- **PM-SWITCH-FR-005:** The system shall prevent the Parent from linking additional Students through the Student Switcher unless a later confirmed requirement defines such behavior.
- **PM-SWITCH-FR-006:** The system shall prevent switching actions from modifying Student records.
- **PM-SWITCH-FR-007:** The system shall preserve read-only access after switching between linked Students.
- **PM-SWITCH-FR-008:** The system shall enforce the rule that one Student can have only one Parent account in Version 1.

### Business Rules

- One Parent account can be linked to multiple Students (BR-020).
- One Student can have only one Parent account in Version 1 (BR-020).
- Parent sees only linked Students (BR-004).
- Parent access is read-only everywhere (BR-004).
- The Parent Panel includes a Student Switcher for navigation between linked Students (BR-020).

### User Permissions

- Parent: may view and switch between Students linked to the Parent account.
- Parent: may not view or select unlinked Students.
- Parent: may not use the Student Switcher to create, update, Archive, restore, or unlink Student records unless a later confirmed requirement explicitly defines such behavior.
- Parent: may not use the Student Switcher to change Teacher or Group relationships.

### Inputs

- Authenticated Parent account context.
- Existing linked Student relationships.
- Selected linked Student identifier or context.

### Outputs

- List of linked Students available for switching.
- Current selected Student context.
- Parent Module views scoped to the selected linked Student.
- Empty-state output if the Parent has no linked Students.

### Validations

- The Parent must be authenticated.
- The selected Student must be linked to the Parent account.
- A Student must not be linked to multiple Parent accounts in Version 1.
- The switch action must not modify Student or Teacher Workspace records.
- The selected Student context must be applied only to read-only Parent views.

### Error Handling

- If the Parent is not authenticated, the system shall deny access.
- If the selected Student is not linked to the Parent, the system shall deny the switch.
- If linked Student data cannot be retrieved, the system shall provide an appropriate unavailable state.
- If a Parent attempts to select an unlinked Student, the system shall reject the action.
- If a switching action attempts to modify records, the system shall reject the modification.

### Edge Cases

- The Parent has exactly one linked Student; switching is not necessary but the selected context must still be valid.
- The Parent has multiple linked Students.
- A linked Student studies with multiple Teachers.
- A linked Student relationship is unavailable or inactive according to later detailed requirements.
- The Parent attempts to access a Student identifier outside the linked Student list.

### Acceptance Criteria

- The Parent can switch only between linked Students.
- Unlinked Students never appear in the Student Switcher.
- Switching changes the current read-only viewing context only.
- Switching does not modify Student, Teacher, Group, Attendance, Homework, Exam, grade, or payment records.
- The one Parent account per Student rule is preserved.

## 3. Homework

### Purpose

Homework allows the Parent to view Homework information for linked Students in read-only mode. Its purpose is to help the Parent monitor Student obligations without modifying Homework records or Teacher Workspace data.

### Description

A Parent may view Homework for linked Students only. Homework remains owned by the assigning Teacher Workspace and is partitioned per Teacher. The Parent can see Homework information relevant to the selected linked Student, but cannot create, submit, edit, grade, Archive, restore, or otherwise modify Homework.

Homework in Version 1 supports Text, Image, and PDF only. Video homework is not supported in Version 1.

### Functional Requirements

- **PM-HW-FR-001:** The system shall allow the Parent to view Homework assigned to linked Students.
- **PM-HW-FR-002:** The system shall distinguish Homework by linked Student.
- **PM-HW-FR-003:** The system shall distinguish Homework by assigning Teacher where applicable.
- **PM-HW-FR-004:** The system shall prevent the Parent from viewing Homework for unlinked Students.
- **PM-HW-FR-005:** The system shall present Homework information in read-only mode.
- **PM-HW-FR-006:** The system shall prevent the Parent from creating Homework.
- **PM-HW-FR-007:** The system shall prevent the Parent from submitting or modifying Homework on behalf of a Student.
- **PM-HW-FR-008:** The system shall prevent the Parent from grading Homework.
- **PM-HW-FR-009:** The system shall prevent the Parent from archiving or restoring Homework.
- **PM-HW-FR-010:** The system shall preserve Teacher Workspace isolation when showing Homework information.

### Business Rules

- Parent access is read-only everywhere (BR-004).
- Parent sees only linked Students (BR-004).
- One Parent account may be linked to multiple Students (BR-020).
- Homework supports Text, Image, and PDF only (BR-021).
- Video homework is not supported in Version 1 (BR-021).
- Student data, including Homework, is partitioned per Teacher (BR-001, BR-003).
- Historical Homework remains available according to historical data rules (BR-007, BR-014).

### User Permissions

- Parent: may view Homework for linked Students only.
- Parent: may not create, submit, update, grade, Archive, restore, or modify Homework.
- Parent: may not access Homework for unlinked Students.
- Parent: may not access Teacher-private Homework data beyond what is relevant to linked Students.

### Inputs

- Authenticated Parent account context.
- Selected linked Student context.
- Homework records associated with the linked Student.
- Teacher or status filters where supported by later detailed requirements.

### Outputs

- Read-only Homework list for the selected linked Student.
- Homework details relevant to the linked Student.
- Teacher association for each Homework item where applicable.
- Homework status or grading status where available for monitoring.
- Empty-state output when no Homework exists.

### Validations

- The Parent must be authenticated.
- The selected Student must be linked to the Parent account.
- Homework must belong to the selected linked Student.
- Homework must be shown as read-only.
- Teacher-specific Homework information must remain scoped to the linked Student relationship.

### Error Handling

- If the Parent is not authenticated, the system shall deny access.
- If the selected Student is not linked to the Parent, the system shall deny access.
- If Homework belongs to an unlinked Student, the system shall deny access.
- If the Parent attempts to modify Homework, the system shall reject the action.
- If Homework data is unavailable, the system shall provide an appropriate unavailable or empty state.

### Edge Cases

- The linked Student has no Homework assigned.
- The linked Student has Homework from multiple Teachers.
- Homework has been submitted by the Student but not yet graded.
- Homework is archived after historical records exist.
- The Parent has multiple linked Students and switches between them.

### Acceptance Criteria

- The Parent can view Homework only for linked Students.
- Homework is read-only for the Parent.
- The Parent cannot create, submit, edit, grade, Archive, or restore Homework.
- Homework remains separated by Teacher where applicable.
- Unlinked Student Homework is never exposed.
- Homework no-data states are handled without error.

## 4. Attendance

### Purpose

Attendance allows the Parent to monitor Attendance records for linked Students in read-only mode. Its purpose is to provide visibility into Student attendance without allowing the Parent to record or modify Attendance.

### Description

Attendance is recorded within Teacher Workspaces using confirmed attendance methods. The Parent can view Attendance information for linked Students only. Attendance remains partitioned per Teacher and must preserve Teacher Workspace isolation.

The Parent cannot scan Attendance, enter Attendance manually, correct Attendance, modify Attendance status, or perform any Attendance change. Attendance changes are Teacher-side actions or Student-side scan events where applicable, not Parent actions.

### Functional Requirements

- **PM-ATT-FR-001:** The system shall allow the Parent to view Attendance records for linked Students.
- **PM-ATT-FR-002:** The system shall distinguish Attendance records by linked Student.
- **PM-ATT-FR-003:** The system shall distinguish Attendance records by Teacher where applicable.
- **PM-ATT-FR-004:** The system shall prevent the Parent from viewing Attendance for unlinked Students.
- **PM-ATT-FR-005:** The system shall present Attendance information in read-only mode.
- **PM-ATT-FR-006:** The system shall prevent the Parent from recording Attendance.
- **PM-ATT-FR-007:** The system shall prevent the Parent from modifying Attendance.
- **PM-ATT-FR-008:** The system shall prevent the Parent from correcting Attendance records.
- **PM-ATT-FR-009:** The system shall preserve historical Attendance visibility according to historical data rules.
- **PM-ATT-FR-010:** The system shall ensure Attendance is not used to calculate Billable Students in any Parent-visible context.

### Business Rules

- Parent access is read-only everywhere (BR-004).
- Parent sees only linked Students (BR-004).
- Parent cannot modify Attendance.
- Attendance supports Dynamic QR Code, ID Card scanning, and manual entry, but these are not Parent modification permissions (BR-010).
- Attendance is not used for Billable Student calculation (BR-008).
- Student transfers preserve historical Attendance (BR-007).
- Attendance records are partitioned per Teacher Workspace (BR-003).

### User Permissions

- Parent: may view Attendance records for linked Students only.
- Parent: may not record, scan, manually enter, correct, update, Archive, restore, or modify Attendance.
- Parent: may not access Attendance records for unlinked Students.
- Parent: may not access unrelated Teacher Workspace Attendance records.

### Inputs

- Authenticated Parent account context.
- Selected linked Student context.
- Attendance records associated with the linked Student.
- Teacher or period filters where supported by later detailed requirements.

### Outputs

- Read-only Attendance records for the selected linked Student.
- Teacher association for Attendance records where applicable.
- Historical Attendance information where applicable.
- Empty-state output when no Attendance records exist.

### Validations

- The Parent must be authenticated.
- The selected Student must be linked to the Parent account.
- Attendance records must belong to the selected linked Student.
- Attendance records must remain read-only.
- Filters must not reference unlinked Students or unrelated Teacher Workspaces.

### Error Handling

- If the Parent is not authenticated, the system shall deny access.
- If the selected Student is not linked to the Parent, the system shall deny access.
- If Attendance data belongs to an unlinked Student, the system shall deny access.
- If the Parent attempts to modify Attendance, the system shall reject the action.
- If Attendance data is unavailable, the system shall provide an appropriate unavailable or empty state.

### Edge Cases

- The linked Student has no Attendance records.
- The linked Student studies with multiple Teachers and has Attendance records under each Teacher.
- The linked Student moved between Groups and historical Attendance remains available.
- Attendance records exist under an archived Group.
- The Parent attempts to use a Student Attendance action, such as scanning a Dynamic QR Code, from the Parent context.

### Acceptance Criteria

- The Parent can view Attendance only for linked Students.
- Attendance information is read-only for the Parent.
- The Parent cannot record, correct, or modify Attendance.
- Attendance remains separated by Teacher where applicable.
- Historical Attendance remains visible where applicable.
- Attendance for unlinked Students is never exposed.

## 5. Exams

### Purpose

Exams allows the Parent to monitor Exam information, attempts, results, and grades for linked Students in read-only mode. Its purpose is to support Parent visibility without allowing the Parent to modify grades, Exams, attempts, or Teacher-owned Question Bank content.

### Description

Exams are built from each Teacher's private Question Bank and are workspace-scoped. The Parent may view Exam information and grades for linked Students only, according to the Student's Teacher relationships. The Parent cannot take Exams for the Student, modify answers, modify grades, edit Exams, or access the Teacher's private Question Bank outside the linked Student's visible Exam context.

Exam information must remain separated by Teacher. A linked Student may have Exams from multiple Teachers, but each Exam, attempt, and grade remains associated with the owning Teacher Workspace.

### Functional Requirements

- **PM-EXAM-FR-001:** The system shall allow the Parent to view Exam information for linked Students.
- **PM-EXAM-FR-002:** The system shall allow the Parent to view Exam attempt status and grades for linked Students where available.
- **PM-EXAM-FR-003:** The system shall distinguish Exam information by linked Student.
- **PM-EXAM-FR-004:** The system shall distinguish Exam information by Teacher where applicable.
- **PM-EXAM-FR-005:** The system shall prevent the Parent from viewing Exam information for unlinked Students.
- **PM-EXAM-FR-006:** The system shall present Exam information and grades in read-only mode.
- **PM-EXAM-FR-007:** The system shall prevent the Parent from modifying grades.
- **PM-EXAM-FR-008:** The system shall prevent the Parent from taking Exams or submitting answers on behalf of a Student.
- **PM-EXAM-FR-009:** The system shall prevent the Parent from creating, editing, publishing, archiving, restoring, or grading Exams.
- **PM-EXAM-FR-010:** The system shall prevent the Parent from accessing Teacher-owned private Question Bank content outside linked Student Exam visibility.

### Business Rules

- Parent access is read-only everywhere (BR-004).
- Parent sees only linked Students (BR-004).
- Parent cannot modify grades.
- Exam definitions, attempts, and grades are workspace-scoped (BR-012).
- The Question Bank is Teacher-owned and private (BR-011).
- Supported question types are Multiple Choice, True/False, Essay, and Bubble Sheet (BR-011).
- Student transfers preserve historical Exams and grades (BR-007).
- Teacher Workspace isolation applies to Exam data (BR-003).

### User Permissions

- Parent: may view Exam information and grades for linked Students only.
- Parent: may not modify grades.
- Parent: may not take or submit Exams for a Student.
- Parent: may not create, edit, publish, Archive, restore, or grade Exams.
- Parent: may not access another Student's Exam attempts or grades.
- Parent: may not access Teacher-owned private Question Bank content beyond linked Student Exam visibility.

### Inputs

- Authenticated Parent account context.
- Selected linked Student context.
- Exam, attempt, and grade records associated with the linked Student.
- Teacher or status filters where supported by later detailed requirements.

### Outputs

- Read-only Exam list for the selected linked Student.
- Read-only Exam attempt status and grade information where available.
- Teacher association for Exam records where applicable.
- Empty-state output when no Exam records exist.

### Validations

- The Parent must be authenticated.
- The selected Student must be linked to the Parent account.
- Exam records must belong to the selected linked Student.
- Grades must be shown as read-only.
- Teacher Question Bank content must not be exposed outside linked Student Exam visibility.
- Filters must not reference unlinked Students or unrelated Teacher Workspaces.

### Error Handling

- If the Parent is not authenticated, the system shall deny access.
- If the selected Student is not linked to the Parent, the system shall deny access.
- If Exam data belongs to an unlinked Student, the system shall deny access.
- If the Parent attempts to modify grades or Exam information, the system shall reject the action.
- If Exam results are not yet available, the system shall indicate that results are unavailable rather than inventing a grade.

### Edge Cases

- The linked Student has no Exams assigned or completed.
- The linked Student has Exams from multiple Teachers.
- Essay grading is not yet complete, so final grade may not be available.
- A Bubble Sheet Exam is automatically graded.
- The linked Student moved Groups after completing an Exam, and historical Exam grades remain available.
- An Exam is archived after attempts exist.

### Acceptance Criteria

- The Parent can view Exam information only for linked Students.
- Exam information and grades are read-only for the Parent.
- The Parent cannot modify grades.
- The Parent cannot take Exams or submit answers for a Student.
- Teacher-owned Question Bank content remains private.
- Exam information remains separated by Teacher where applicable.

## 6. Teachers

### Purpose

Teachers allows the Parent to view the Teachers associated with linked Students. Its purpose is to help the Parent understand which Teachers are connected to each linked Student without exposing Teacher Workspace operations or unrelated Teacher data.

### Description

The Parent may view Teachers of linked Students only. A linked Student may study with multiple Teachers, and each Teacher relationship is separate. The Parent's Teachers view must be read-only and must not allow the Parent to browse Teachers as a marketplace, discover unrelated Teachers, access Teacher-private content, or manage Teacher relationships.

This section is not a Teacher management module and does not provide Super Admin capabilities. It only provides Parent visibility into the Teachers of linked Students.

### Functional Requirements

- **PM-TEACHERS-FR-001:** The system shall allow the Parent to view Teachers associated with linked Students.
- **PM-TEACHERS-FR-002:** The system shall distinguish Teachers by linked Student where applicable.
- **PM-TEACHERS-FR-003:** The system shall prevent the Parent from viewing Teachers unrelated to linked Students.
- **PM-TEACHERS-FR-004:** The system shall present Teacher relationship information in read-only mode.
- **PM-TEACHERS-FR-005:** The system shall prevent the Parent from discovering or browsing Teachers outside linked Student relationships.
- **PM-TEACHERS-FR-006:** The system shall prevent the Parent from accessing Teacher Workspace operations.
- **PM-TEACHERS-FR-007:** The system shall prevent the Parent from modifying Teacher, Group, Educational Grade, Student Enrollment, Homework, Attendance, Exam, Lesson, or payment-status records from Teachers.
- **PM-TEACHERS-FR-008:** The system shall preserve Teacher Workspace isolation when showing Teacher information.

### Business Rules

- Parent sees only linked Students (BR-004).
- Parent access is read-only everywhere (BR-004).
- The Teachers view shows the Teachers of linked Students (Project Context §7.3).
- The Platform is not an online course marketplace.
- There is no course discovery or browsing across Teachers.
- Teacher Workspace data is completely isolated (BR-003).
- Lessons and Question Banks are Teacher-owned and private (BR-018, BR-011).

### User Permissions

- Parent: may view Teachers associated with linked Students only.
- Parent: may not discover, browse, or search unrelated Teachers as marketplace content.
- Parent: may not access Teacher Workspace management functions.
- Parent: may not modify Teacher relationships, Student Enrollment, Groups, Attendance, Homework, Exams, Lessons, or payment status.
- Parent: may not access Teacher-private Lesson or Question Bank content unless it is visible through the linked Student's permitted views.

### Inputs

- Authenticated Parent account context.
- Selected linked Student context.
- Teacher relationships associated with the linked Student.
- Optional linked Student filter where supported by later detailed requirements.

### Outputs

- Read-only list of Teachers associated with the selected linked Student.
- Teacher relationship context relevant to the linked Student.
- Empty-state output when the linked Student has no Teacher relationships.

### Validations

- The Parent must be authenticated.
- The selected Student must be linked to the Parent account.
- Teacher information must be associated with the selected linked Student.
- Unrelated Teacher Workspace data must not be exposed.
- Teacher listing must not behave as marketplace discovery.

### Error Handling

- If the Parent is not authenticated, the system shall deny access.
- If the selected Student is not linked to the Parent, the system shall deny access.
- If Teacher information is unrelated to the linked Student, the system shall deny access.
- If the Parent attempts to modify Teacher or Student relationship records, the system shall reject the action.
- If Teacher relationship data is unavailable, the system shall provide an appropriate unavailable or empty state.

### Edge Cases

- A linked Student has no active Teacher relationships.
- A linked Student studies with multiple Teachers.
- Multiple linked Students share one or more Teachers.
- A Teacher relationship includes archived historical records.
- The Parent attempts to browse unrelated Teachers.

### Acceptance Criteria

- The Parent can view only Teachers associated with linked Students.
- Teacher information is read-only.
- The Parent cannot browse unrelated Teachers.
- The Parent cannot access Teacher Workspace operations.
- Teacher-private content remains protected.
- Teacher information remains scoped to linked Student relationships.

## 7. Payments

### Purpose

Payments allows the Parent to view payment-status information for linked Students under Flow B. Its purpose is to provide visibility into Student fees owed to Teachers without processing payments or modifying payment records.

### Description

Flow B represents Student or Parent fees owed to a Teacher, derived from the Group's Price and Pricing Type. The Parent Payments view shows Flow B records for linked Students only. It must not be confused with Flow A, which is the Teacher's monthly Platform Subscription paid to the Platform.

Version 1 records payment status only. Actual Student fee payments are handled outside the Platform. The Parent can view payment status only and cannot process payments through the Platform in Version 1.

### Functional Requirements

- **PM-PAY-FR-001:** The system shall allow the Parent to view Flow B payment-status records for linked Students.
- **PM-PAY-FR-002:** The system shall distinguish payment-status records by linked Student.
- **PM-PAY-FR-003:** The system shall distinguish payment-status records by Teacher where applicable.
- **PM-PAY-FR-004:** The system shall derive Student fee status from Group Price and Pricing Type where applicable.
- **PM-PAY-FR-005:** The system shall support Pricing Type values of Monthly and Per Lesson as the fee basis for Groups.
- **PM-PAY-FR-006:** The system shall prevent the Parent from viewing payment-status records for unlinked Students.
- **PM-PAY-FR-007:** The system shall present payment information as status-only.
- **PM-PAY-FR-008:** The system shall prevent the Parent from modifying payment status.
- **PM-PAY-FR-009:** The system shall prevent online payment processing in Version 1.
- **PM-PAY-FR-010:** The system shall prevent Flow B Student fee status from being presented as Flow A Teacher Platform Subscription status.

### Business Rules

- Parent can view payment status only.
- Parent sees only linked Students (BR-004).
- Parent access is read-only everywhere (BR-004).
- Flow A and Flow B are separate financial flows and must never be conflated.
- Flow B is Student or Parent to Teacher fees derived from Group Price and Pricing Type (BR-009).
- Version 1 records payment status only and does not process transactions (BR-019).
- Online payment gateways are out of scope for Version 1 (BR-019).
- Historical payment-status records remain available according to historical data rules (BR-014).

### User Permissions

- Parent: may view Flow B payment status for linked Students only.
- Parent: may not view payment status for unlinked Students.
- Parent: may not modify payment status.
- Parent: may not process payments through the Platform.
- Parent: may not access or manage Flow A Teacher Platform Subscription records.

### Inputs

- Authenticated Parent account context.
- Selected linked Student context.
- Flow B payment-status records associated with the linked Student.
- Group Price and Pricing Type where applicable.
- Teacher or period filters where supported by later detailed requirements.

### Outputs

- Read-only Flow B payment-status information for linked Students.
- Teacher association for payment-status records where applicable.
- Group pricing basis where available and applicable.
- Clear status-only payment information.
- Empty-state output when no payment-status records exist.

### Validations

- The Parent must be authenticated.
- The selected Student must be linked to the Parent account.
- Payment-status records must belong to the selected linked Student.
- Pricing Type must be Monthly or Per Lesson where displayed.
- Flow A records must not be exposed as Parent Payments.
- No payment processing action may be initiated.

### Error Handling

- If the Parent is not authenticated, the system shall deny access.
- If the selected Student is not linked to the Parent, the system shall deny access.
- If payment-status data belongs to an unlinked Student, the system shall deny access.
- If the Parent attempts to modify payment status, the system shall reject the action.
- If the Parent attempts to initiate payment processing, the system shall reject the action.
- If payment-status data is unavailable, the system shall provide an appropriate unavailable or empty state.

### Edge Cases

- The linked Student has no payment-status records.
- The Parent monitors multiple Students with different Teacher payment statuses.
- A linked Student studies with multiple Teachers using different Pricing Types.
- Payment status may be outdated because actual payments are handled outside the Platform.
- A linked Student moved Groups and historical payment-status records remain available.

### Acceptance Criteria

- The Parent can view payment status only for linked Students.
- Payment information is read-only.
- The Parent cannot modify payment status.
- The Platform does not process payments.
- Flow B is clearly separated from Flow A.
- Payment-status records for unlinked Students are never exposed.

## 8. Settings

### Purpose

Settings defines the Parent account boundary for Version 1. Its purpose is to ensure any Parent account-related access remains limited to the Parent's own account context and does not allow modification of linked Student educational records or Teacher Workspace data.

### Description

The Project Context confirms that a Parent has one account, may monitor multiple Students, has read-only access everywhere, and sees only linked Students. The confirmed Parent Panel navigation includes Dashboard, Student Switcher, Homework, Attendance, Exams, Teachers, and Payments. Because detailed Parent account settings behavior is not confirmed in the Project Context, this Settings section defines only the required access boundaries and restrictions for any Parent account settings area that may be specified later.

Parent Settings must not become a way to modify Student records, Attendance, grades, Homework, Exams, Teacher relationships, payment status, or Teacher Workspace data. Any future detailed account-setting fields must remain consistent with the Project Context and must not violate read-only access to linked Student data.

### Functional Requirements

- **PM-SETTINGS-FR-001:** The system shall ensure Parent account access remains associated only with the authenticated Parent account.
- **PM-SETTINGS-FR-002:** The system shall prevent the Parent from accessing another Parent account through Settings.
- **PM-SETTINGS-FR-003:** The system shall prevent the Parent from modifying linked Student records through Settings.
- **PM-SETTINGS-FR-004:** The system shall prevent the Parent from modifying Attendance through Settings.
- **PM-SETTINGS-FR-005:** The system shall prevent the Parent from modifying grades through Settings.
- **PM-SETTINGS-FR-006:** The system shall prevent the Parent from modifying Homework through Settings.
- **PM-SETTINGS-FR-007:** The system shall prevent the Parent from modifying payment status through Settings.
- **PM-SETTINGS-FR-008:** The system shall prevent the Parent from changing Teacher, Group, Educational Grade, Enrollment, Exam, Lesson, or Teacher Workspace data through Settings.
- **PM-SETTINGS-FR-009:** The system shall preserve the rule that one Student can have only one Parent account in Version 1.
- **PM-SETTINGS-FR-010:** The system shall record important Parent account login or account-update events in the Audit Log where applicable under the Audit Log Policy.

### Business Rules

- One Parent account can be linked to multiple Students (BR-020).
- One Student can have only one Parent account in Version 1 (BR-020).
- Parent sees only linked Students (BR-004).
- Parent access is read-only everywhere (BR-004).
- Parent cannot modify Attendance.
- Parent cannot modify grades.
- Parent cannot modify Homework.
- Parent can view payment status only.
- Important login and update actions are recorded in the Audit Log where applicable (BR-006).

### User Permissions

- Parent: may access only the Parent's own account context according to later detailed requirements.
- Parent: may not access another Parent account.
- Parent: may not modify linked Student educational records.
- Parent: may not modify Attendance, grades, Homework, Exams, payment status, Teacher relationships, Group assignment, Educational Grades, Lessons, or Teacher Workspace data.
- Parent: may not use Settings to perform Teacher, Teacher Staff, Student, or Super Admin actions.

### Inputs

- Authenticated Parent account context.
- Parent account context data as defined by later detailed requirements.
- Linked Student relationship context for validation only.
- Account action request where applicable under later detailed requirements.

### Outputs

- Parent account context available only to the authenticated Parent where applicable.
- Rejection of unauthorized access to other Parent accounts.
- Rejection of attempts to modify linked Student or Teacher Workspace data.
- Audit Log entries for important Parent login or account-update events where applicable.

### Validations

- The Parent must be authenticated for Settings access.
- Settings access must apply only to the authenticated Parent account.
- Linked Student relationships must remain read-only.
- One Student must not be linked to multiple Parent accounts in Version 1.
- Any account-setting behavior defined later must not alter Attendance, grades, Homework, Exams, payment status, Teacher relationships, Group assignment, Educational Grades, Lessons, or Teacher Workspace data.

### Error Handling

- If the Parent is not authenticated, the system shall deny access.
- If the Parent attempts to access another Parent account, the system shall deny access.
- If the Parent attempts to modify linked Student records, the system shall reject the action.
- If the Parent attempts to modify Attendance, grades, Homework, Exams, or payment status, the system shall reject the action.
- If the Parent attempts to change Teacher Workspace data, the system shall reject the action.
- If an account-context action fails, the system shall preserve the previous valid account state where applicable.

### Edge Cases

- The Parent has no linked Students.
- The Parent has multiple linked Students.
- A linked Student already has the one allowed Parent account.
- The Parent attempts to use Settings to alter a linked Student's Homework, Attendance, grades, or payment status.
- Detailed Parent account-setting fields are not yet confirmed and must not be silently assumed.

### Acceptance Criteria

- Parent Settings access, if specified later, is limited to the authenticated Parent account context.
- Settings cannot be used to modify linked Student educational records.
- Settings cannot be used to modify Attendance, grades, Homework, Exams, or payment status.
- Settings cannot be used to modify Teacher Workspace data.
- The one Parent account per Student rule remains enforced.
- Parent access remains read-only with respect to all linked Student educational data.

---

*End of PART 4 — Parent Module.*

---

# PART 5 — Platform Administration (Super Admin) Module

This Part 5 defines the Platform Administration module for the Super Admin role only. It is limited to Platform-level capabilities confirmed in the Project Context, Project Vision, and earlier SRS context. It does not define the Student Module, Parent Module, APIs, database tables, UI implementation details, or source code.

The Platform Administration module is governed by the following mandatory principles:

- The Super Admin owns the Platform at the platform level.
- The Super Admin manages Teachers, Subscriptions under Flow A, pricing, platform settings, and global reports.
- The Super Admin does not operate inside Teacher Workspaces as a Teacher.
- Teacher Workspace data remains completely isolated.
- The Super Admin content-visibility boundary is PENDING; no requirement may silently assume unrestricted browsing of Teacher-private content.
- Subscription pricing is owned by the Super Admin and configured at the Platform level.
- Flat price versus volume tiers remains PENDING and must not be silently assumed.
- Version 1 records payment status only and does not process payments.
- Online payment gateways are out of scope for Version 1.
- Platform staff accounts such as Support, Sales, and Accountant are not included in Version 1 because only five roles are confirmed: Super Admin, Teacher, Teacher Staff, Student, and Parent.
- Any requested capability for “Login as Teacher” is not treated as a confirmed Version 1 functional requirement in this SRS because it is not confirmed in the Project Context and may conflict with the confirmed boundary that the Super Admin does not operate inside Teacher Workspaces.
- Any requested “Teacher suspension” rule is represented only through confirmed Archive and Subscription status concepts; non-payment enforcement remains PENDING and must not be hardened as a confirmed rule.

## 1. Dashboard

### Purpose

The Platform Administration Dashboard provides the Super Admin with a Platform-level overview of the Unified Education Platform. Its purpose is to support Platform ownership, commercial monitoring, operational oversight, and high-level visibility into Teachers, Subscriptions, pricing, payment status, reports, platform settings, and Audit Logs without violating Teacher Workspace isolation.

### Description

The Dashboard is a Super Admin overview area. It must operate at Platform scope and must not behave like a Teacher Workspace Dashboard. It may summarize Platform-level Teacher activity, Subscription status under Flow A, pricing configuration status, payment-status recording activity, global reports, and Audit Log activity according to the confirmed Super Admin role.

Because Super Admin content visibility is PENDING, the Dashboard must not assume unrestricted access to Teacher-private content such as Lessons, Question Banks, Homework content, Exams, or Student-level workspace records beyond confirmed or later-approved visibility boundaries.

### Functional Requirements

- **PA-DASH-FR-001:** The system shall provide the Super Admin with a Platform-level Dashboard.
- **PA-DASH-FR-002:** The system shall summarize Teachers at Platform level without exposing unrelated Teacher-private content beyond confirmed visibility boundaries.
- **PA-DASH-FR-003:** The system shall summarize Flow A Subscription status at Platform level.
- **PA-DASH-FR-004:** The system shall summarize pricing configuration status at Platform level.
- **PA-DASH-FR-005:** The system shall summarize payment-status records without processing payments.
- **PA-DASH-FR-006:** The system shall summarize global reports according to the confirmed Super Admin scope and pending content-visibility boundaries.
- **PA-DASH-FR-007:** The system shall summarize Audit Log activity at Platform level.
- **PA-DASH-FR-008:** The system shall clearly separate Flow A Platform Subscription information from Flow B Student fee information.
- **PA-DASH-FR-009:** The system shall not provide marketplace discovery, course browsing, or cross-Teacher content exposure.
- **PA-DASH-FR-010:** The system shall not provide “Login as Teacher” access from the Dashboard as a confirmed Version 1 capability unless the Project Context is formally updated.

### Business Rules

- The Super Admin owns the Platform at Platform-level scope.
- The Super Admin manages Teachers, Subscriptions, pricing, platform settings, and global reports.
- The Super Admin does not operate inside Teacher Workspaces as a Teacher.
- Teacher Workspace data is completely isolated (BR-003).
- Super Admin content visibility is PENDING (Q-012).
- Flow A and Flow B must never be conflated.
- Version 1 records payment status only and does not process transactions (BR-019).
- Online payment gateways are out of scope for Version 1 (BR-019).

### User Permissions

- Super Admin: may view Platform-level Dashboard information within confirmed Platform administration scope.
- Super Admin: may not access Teacher-private workspace content beyond confirmed or formally approved visibility boundaries.
- Super Admin: may not operate as a Teacher inside a Teacher Workspace as a confirmed Version 1 capability.
- Teacher, Teacher Staff, Student, and Parent users may not access the Platform Administration Dashboard.

### Inputs

- Authenticated Super Admin account context.
- Platform-level Teacher records.
- Flow A Subscription status records.
- Platform pricing configuration.
- Payment-status records.
- Global report indicators.
- Audit Log indicators.
- Date or period filters where applicable.

### Outputs

- Platform-level Dashboard summary.
- Teacher administration indicators.
- Subscription and payment-status indicators.
- Pricing configuration indicators.
- Report and Audit Log summary information.
- Clear separation between Flow A and Flow B information.

### Validations

- The user must be authenticated as Super Admin.
- Dashboard data must be Platform-scoped.
- Teacher Workspace isolation must be preserved.
- Pending content-visibility boundaries must not be bypassed.
- Payment information must be status-only and must not initiate payment processing.
- Filters must be valid and Platform-scoped.

### Error Handling

- If the user is not authenticated as Super Admin, the system shall deny access.
- If a request attempts to access Teacher-private content outside confirmed visibility boundaries, the system shall deny access.
- If Dashboard data cannot be retrieved, the system shall provide an appropriate unavailable state without exposing technical details.
- If filters are invalid, the system shall reject the request.
- If payment processing is attempted, the system shall reject the action because Version 1 records status only.

### Edge Cases

- The Platform has no registered Teachers yet.
- No Subscription status records exist yet.
- Pricing configuration is pending detailed pricing decisions.
- Teacher Workspace content exists but Super Admin visibility boundaries remain pending.
- Payment status may be incomplete because actual payments are handled outside the Platform.

### Acceptance Criteria

- The Super Admin can view Platform-level summary information.
- The Dashboard preserves Teacher Workspace isolation.
- Flow A and Flow B are clearly separated.
- The Dashboard does not process payments.
- The Dashboard does not expose Teacher-private content beyond confirmed boundaries.
- Non-Super Admin users cannot access the Platform Administration Dashboard.

## 2. Teachers

### Purpose

Teachers allows the Super Admin to manage Teacher accounts at the Platform level. Its purpose is to support Platform administration of Teachers while preserving Teacher Workspace isolation and respecting the confirmed rules for Archive, Audit Log, Teaching Subject, and Subscription handling.

### Description

The Super Admin manages Teachers as Platform-level accounts and Subscription customers. Each Teacher operates one completely isolated Teacher Workspace and teaches exactly one Teaching Subject selected during registration. The Teaching Subject cannot be changed after account creation.

The Super Admin may manage Teacher account records at Platform level, but must not operate inside a Teacher Workspace as a Teacher. Any requested “Login as Teacher” capability is not included as a confirmed Version 1 requirement because the Project Context states that the Super Admin does not operate inside Teacher Workspaces and because content visibility remains pending.

Teacher records must not be permanently deleted. Archive is used instead, and historical data remains available. Restoration of archived records is permitted only for authorized users and must be audited.

### Functional Requirements

- **PA-TEACHER-FR-001:** The system shall allow the Super Admin to create Teacher accounts at Platform level.
- **PA-TEACHER-FR-002:** The system shall allow the Super Admin to view Teacher account records at Platform level.
- **PA-TEACHER-FR-003:** The system shall allow the Super Admin to update Teacher account information that is within Platform administration scope.
- **PA-TEACHER-FR-004:** The system shall enforce that each Teacher account represents exactly one Teaching Subject.
- **PA-TEACHER-FR-005:** The system shall prevent changing a Teacher's Teaching Subject after account creation.
- **PA-TEACHER-FR-006:** The system shall allow the Super Admin to Archive Teacher accounts instead of permanently deleting them.
- **PA-TEACHER-FR-007:** The system shall allow authorized restoration of archived Teacher accounts.
- **PA-TEACHER-FR-008:** The system shall preserve historical Teacher Workspace relationships when a Teacher account is archived.
- **PA-TEACHER-FR-009:** The system shall record Teacher account creation, update, Archive, and restore actions in the Audit Log.
- **PA-TEACHER-FR-010:** The system shall not provide “Login as Teacher” as a confirmed Version 1 Teacher management function unless the Project Context is formally updated.

### Business Rules

- Super Admin manages Teachers at Platform-level scope.
- Each Teacher operates one completely isolated Teacher Workspace (BR-003).
- Each Teacher account represents exactly one Teaching Subject (BR-016).
- The Teaching Subject is selected during registration and cannot be changed after account creation (BR-016).
- No permanent deletion is allowed; Archive must be used instead (BR-005).
- Historical data is never deleted and must remain available (BR-014).
- Important actions must be recorded in the Audit Log (BR-006).
- Platform staff accounts such as Support, Sales, and Accountant are out of scope for Version 1 because they are not among the five confirmed roles.

### User Permissions

- Super Admin: may create, view, update, Archive, and restore Teacher accounts at Platform level.
- Super Admin: may not change a Teacher's Teaching Subject after account creation.
- Super Admin: may not operate inside Teacher Workspaces as a Teacher as a confirmed Version 1 capability.
- Teacher, Teacher Staff, Student, and Parent users may not access Platform-level Teacher administration.

### Inputs

- Teacher account information.
- Teaching Subject selected during Teacher registration or account creation.
- Teacher account status action such as Archive or restore.
- Authenticated Super Admin account context.
- Platform-level Teacher filters where applicable.

### Outputs

- Created or updated Teacher account record.
- Teacher account list at Platform level.
- Archived or restored Teacher account state.
- Preserved historical Teacher account and Teacher Workspace relationships.
- Audit Log entries for Teacher account actions.

### Validations

- The user must be authenticated as Super Admin.
- Teacher account information must be valid according to later detailed requirements.
- Teaching Subject is required at account creation.
- Teaching Subject must not be changed after account creation.
- Archive must be used instead of permanent deletion.
- Teacher account actions must be recorded in the Audit Log.

### Error Handling

- If the user is not authenticated as Super Admin, the system shall deny access.
- If Teacher account information is missing or invalid, the system shall reject the action.
- If a Teaching Subject change is attempted after account creation, the system shall reject the action.
- If permanent deletion is attempted, the system shall reject the action and require Archive.
- If the Super Admin attempts to operate inside a Teacher Workspace as a Teacher, the system shall deny the action unless a future confirmed decision changes this boundary.

### Edge Cases

- The first Teacher account is created on the Platform.
- A Teacher account is archived after Students, Groups, Attendance, Homework, Exams, Lessons, Reports, and payment-status records exist.
- A Teacher wants to teach another Teaching Subject and must create a separate Teacher account.
- A Teacher account restoration is requested after archival.
- Troubleshooting requires support, but “Login as Teacher” is not confirmed in the Project Context.

### Acceptance Criteria

- The Super Admin can manage Teacher accounts at Platform level.
- Each Teacher account has exactly one Teaching Subject.
- Teaching Subject cannot be changed after account creation.
- Teacher accounts can be archived but not permanently deleted.
- Teacher account history remains available after Archive.
- Teacher account actions are recorded in the Audit Log.
- “Login as Teacher” is not implemented as a confirmed Version 1 requirement in this SRS.

## 3. Subscriptions

### Purpose

Subscriptions allows the Super Admin to manage Flow A Platform Subscription status for Teachers. Its purpose is to support the Platform's monthly SaaS business model based on Billable Students while preserving the confirmed billing rules and payment-status-only handling for Version 1.

### Description

Flow A is the Teacher to Platform Subscription. Teachers pay monthly, and the Subscription price depends on the number of Billable Students multiplied by the applicable price per Student. The Billing Cycle starts on the first day of every calendar month and ends on the last day of the same month. A new Billing Cycle begins automatically on the first day of the next month.

A Student becomes a Billable Student based on Enrollment duration only. If the Student remains enrolled in a Teacher's Group for more than 15 calendar days during the Billing Cycle, the Student is Billable. Attendance and login activity are not used for this calculation.

Version 1 records Subscription payment status only. Actual Subscription payments are handled outside the Platform. Non-payment enforcement remains PENDING and must not be silently assumed.

### Functional Requirements

- **PA-SUB-FR-001:** The system shall allow the Super Admin to view Flow A Subscription records for Teachers.
- **PA-SUB-FR-002:** The system shall support calendar-month Billing Cycles for Teacher Subscriptions.
- **PA-SUB-FR-003:** The system shall calculate Billable Students based on Enrollment duration only.
- **PA-SUB-FR-004:** The system shall count a Student as Billable only when enrolled in a Teacher's Group for more than 15 calendar days during the Billing Cycle.
- **PA-SUB-FR-005:** The system shall exclude Students enrolled for 15 calendar days or less during the Billing Cycle from Billable Student count.
- **PA-SUB-FR-006:** The system shall not use Attendance or login activity in Billable Student calculation.
- **PA-SUB-FR-007:** The system shall apply Platform-level Subscription pricing configured by the Super Admin.
- **PA-SUB-FR-008:** The system shall allow the Super Admin to record Subscription payment status.
- **PA-SUB-FR-009:** The system shall prevent online payment processing in Version 1.
- **PA-SUB-FR-010:** The system shall record Subscription changes in the Audit Log.

### Business Rules

- Teachers pay monthly Subscriptions to the Platform.
- Monthly Subscription equals Billable Students multiplied by Price Per Student (BR-008).
- Billable Student calculation is based on Enrollment duration only (BR-008).
- Attendance and login activity are not used for Billable Student calculation (BR-008).
- Billing Cycle is a calendar month and begins automatically each month.
- Pricing is owned by the Super Admin (BR-015).
- Flat price versus volume tiers remains PENDING (Q-013).
- Version 1 records payment status only and does not process transactions (BR-019).
- Online payment gateways are out of scope for Version 1 (BR-019).
- Subscription changes must be recorded in the Audit Log (BR-006).

### User Permissions

- Super Admin: may view Teacher Subscription records under Flow A.
- Super Admin: may record Subscription payment status.
- Super Admin: may manage Platform-level pricing configuration according to confirmed pricing rules.
- Super Admin: may not process online payments through the Platform.
- Teacher, Teacher Staff, Student, and Parent users may not manage Flow A Platform Subscriptions.

### Inputs

- Teacher account reference.
- Billing Cycle period.
- Enrollment duration data for Student enrollments under each Teacher.
- Platform-level price per Student.
- Recorded Subscription payment status.
- Authenticated Super Admin account context.

### Outputs

- Billable Student count per Teacher for the Billing Cycle.
- Subscription amount based on confirmed formula.
- Subscription payment-status record.
- Subscription history and status overview.
- Audit Log entries for Subscription changes.

### Validations

- The user must be authenticated as Super Admin.
- Billing Cycle must align with the calendar month rule.
- Billable Student calculation must use Enrollment duration only.
- Attendance and login activity must be excluded from calculation.
- Price configuration must come from Platform-level pricing owned by the Super Admin.
- Payment handling must remain status-only.

### Error Handling

- If the user is not authenticated as Super Admin, the system shall deny access.
- If Billing Cycle data is invalid, the system shall reject the calculation or status action.
- If Enrollment duration data is unavailable, the system shall prevent misleading Subscription calculation output.
- If payment processing is attempted, the system shall reject the action.
- If non-payment enforcement behavior is requested, the system shall not apply unconfirmed enforcement rules until Q-005 is resolved.

### Edge Cases

- A Teacher has no Billable Students in a Billing Cycle.
- A Student is enrolled for exactly 15 calendar days and is not Billable.
- A Student is enrolled for more than 15 calendar days and is Billable.
- A Teacher has Students moved between Groups while Enrollment history must remain accurate.
- Pricing model details are pending between flat price and volume tiers.
- A Subscription payment is made outside the Platform and status must be recorded manually.

### Acceptance Criteria

- The Super Admin can view and manage Teacher Subscription status under Flow A.
- Billable Student calculation follows the more-than-15-calendar-days rule.
- Attendance and login activity are not used in Subscription calculation.
- Billing Cycle follows the calendar month rule.
- Payment processing is not available in Version 1.
- Subscription changes are recorded in the Audit Log.

## 4. Payments

### Purpose

Payments allows the Super Admin to record and monitor payment status related to Platform administration while preserving the confirmed separation between Flow A and Flow B and the Version 1 rule that payments are handled outside the Platform.

### Description

Version 1 contains two distinct money flows. Flow A is the Teacher's Platform Subscription paid to the Platform and managed by the Super Admin. Flow B is Student or Parent fees owed to a Teacher and tracked by the Platform on the Teacher's behalf. These flows must never be conflated.

The Super Admin Payments area must focus on Platform-level payment-status administration and must not process transactions. Online payment gateways are out of scope for Version 1. Where Flow B appears in Platform-level reports or summaries, it must remain clearly identified as Student/Parent to Teacher fee status, not Platform revenue.

### Functional Requirements

- **PA-PAY-FR-001:** The system shall allow the Super Admin to view Flow A payment-status records.
- **PA-PAY-FR-002:** The system shall allow the Super Admin to record Flow A payment status.
- **PA-PAY-FR-003:** The system shall distinguish Flow A Platform Subscription status from Flow B Student fee status.
- **PA-PAY-FR-004:** The system shall prevent Flow B Student fee status from being presented as Platform revenue.
- **PA-PAY-FR-005:** The system shall support status-only payment recording in Version 1.
- **PA-PAY-FR-006:** The system shall prevent online payment processing.
- **PA-PAY-FR-007:** The system shall prevent payment gateway configuration or transaction handling in Version 1.
- **PA-PAY-FR-008:** The system shall preserve historical payment-status records.
- **PA-PAY-FR-009:** The system shall record important payment-status changes in the Audit Log where applicable.
- **PA-PAY-FR-010:** The system shall not create Platform staff payment roles such as Support, Sales, or Accountant in Version 1.

### Business Rules

- Flow A and Flow B are distinct and must never be conflated.
- Flow A is Teacher to Platform Subscription.
- Flow B is Student or Parent to Teacher fees.
- Version 1 records payment status only and does not process transactions (BR-019).
- Online payment gateways are out of scope for Version 1 (BR-019).
- Pricing is owned by the Super Admin (BR-015).
- Historical records remain available (BR-014).
- Important Subscription changes are recorded in the Audit Log (BR-006).

### User Permissions

- Super Admin: may view and record Flow A payment status.
- Super Admin: may view Platform-level payment-status summaries according to confirmed visibility boundaries.
- Super Admin: may not process payments through the Platform.
- Super Admin: may not treat Flow B as Platform revenue.
- Teacher, Teacher Staff, Student, and Parent users may not manage Platform-level payment administration.

### Inputs

- Teacher account reference for Flow A.
- Billing Cycle period.
- Payment status value according to later detailed requirements.
- Payment-status note or reference where defined by later detailed requirements.
- Authenticated Super Admin account context.

### Outputs

- Recorded Flow A payment status.
- Payment-status history.
- Clear separation between Flow A and Flow B payment-status information.
- Audit Log entries for important payment-status changes where applicable.

### Validations

- The user must be authenticated as Super Admin.
- Payment status must relate to a valid Teacher Subscription record for Flow A.
- Payment-status action must not initiate a transaction.
- Flow A and Flow B must remain separated in all outputs.
- Payment gateway data must not be required or processed in Version 1.

### Error Handling

- If the user is not authenticated as Super Admin, the system shall deny access.
- If the payment-status target is invalid, the system shall reject the action.
- If a payment transaction is attempted, the system shall reject the action.
- If payment gateway configuration is attempted, the system shall reject it as out of scope.
- If Flow A and Flow B would be mixed in a payment output, the system shall prevent misleading output.

### Edge Cases

- A Teacher pays outside the Platform and the Super Admin records status later.
- A Teacher has no Billable Students for a Billing Cycle.
- Payment status is disputed or unknown because actual payment handling occurred outside the Platform.
- Flow B payment status appears in reporting but must not be treated as Platform revenue.
- Historical payment-status records exist for archived Teacher accounts.

### Acceptance Criteria

- The Super Admin can record Flow A payment status.
- The Platform does not process payments.
- Online payment gateways are not included.
- Flow A and Flow B remain clearly separated.
- Flow B is not treated as Platform revenue.
- Important payment-status changes are auditable where applicable.

## 5. Reports

### Purpose

Reports allows the Super Admin to view global reports at Platform level according to confirmed visibility boundaries. Its purpose is to support Platform administration, Subscription monitoring, Teacher management, and business oversight without violating Teacher Workspace isolation.

### Description

The Super Admin views global reports. The exact content-visibility boundary remains PENDING in the Project Context, with a proposed default of aggregates, finances, and metadata only. Therefore, this section must not assume unrestricted access to Teacher-private content such as Lesson videos, Question Bank content, Homework content, individual Exam definitions, or workspace-private Student records.

Reports must preserve Flow A and Flow B separation. Flow A reports support Platform Subscription administration. Flow B records may be included only as status-tracking information where permitted by confirmed visibility boundaries and must not be treated as Platform revenue.

### Functional Requirements

- **PA-REPORT-FR-001:** The system shall allow the Super Admin to view global reports at Platform level.
- **PA-REPORT-FR-002:** The system shall support Teacher-related reporting at Platform level.
- **PA-REPORT-FR-003:** The system shall support Flow A Subscription reporting.
- **PA-REPORT-FR-004:** The system shall support pricing and payment-status reporting according to confirmed Platform-level scope.
- **PA-REPORT-FR-005:** The system shall maintain separation between Flow A and Flow B in reports.
- **PA-REPORT-FR-006:** The system shall include historical data according to the historical data rule.
- **PA-REPORT-FR-007:** The system shall clearly indicate archived records when included in reports.
- **PA-REPORT-FR-008:** The system shall enforce Teacher Workspace isolation in report outputs.
- **PA-REPORT-FR-009:** The system shall not expose Teacher-private content unless a later confirmed visibility decision explicitly permits it.
- **PA-REPORT-FR-010:** The system shall not process payments through reports.

### Business Rules

- Super Admin views global reports at Platform level.
- Super Admin content visibility is PENDING (Q-012).
- Teacher Workspace data is completely isolated (BR-003).
- Historical data is never deleted and must remain available (BR-014).
- Archived records remain available in reports and must be clearly indicated.
- Flow A and Flow B must never be conflated.
- Version 1 records payment status only and does not process transactions (BR-019).

### User Permissions

- Super Admin: may view global reports at Platform level according to confirmed visibility boundaries.
- Super Admin: may not access Teacher-private content beyond confirmed or later-approved visibility boundaries.
- Teacher, Teacher Staff, Student, and Parent users may not access Platform Administration global reports.

### Inputs

- Authenticated Super Admin account context.
- Report type.
- Date or Billing Cycle filters.
- Teacher, Subscription, payment-status, pricing, or Archive filters where supported by later detailed requirements.
- Platform-level report criteria.

### Outputs

- Platform-level report results.
- Teacher administration report summaries.
- Flow A Subscription reports.
- Payment-status reports with Flow A and Flow B separation.
- Historical and archived-record indicators where applicable.

### Validations

- The user must be authenticated as Super Admin.
- Report criteria must be Platform-scoped.
- Teacher Workspace isolation must be enforced.
- Pending content-visibility boundaries must not be exceeded.
- Flow A and Flow B must remain separated.
- Payment processing must not occur from reports.

### Error Handling

- If the user is not authenticated as Super Admin, the system shall deny access.
- If report criteria are invalid, the system shall reject the request.
- If a report would expose Teacher-private content outside confirmed visibility boundaries, the system shall deny or restrict the output.
- If report data is unavailable, the system shall provide an appropriate unavailable or empty state.
- If a report would mix Flow A and Flow B, the system shall prevent misleading output.

### Edge Cases

- No Teachers exist yet.
- Reports include archived Teacher accounts.
- Subscription records exist without completed outside-platform payment confirmation.
- Flow B status exists but cannot be treated as Platform revenue.
- Super Admin content visibility remains unresolved and must default to non-invasive reporting boundaries.

### Acceptance Criteria

- The Super Admin can view Platform-level reports.
- Reports preserve Teacher Workspace isolation.
- Reports do not expose Teacher-private content beyond confirmed boundaries.
- Flow A and Flow B remain clearly separated.
- Archived records are clearly indicated when included.
- Reports do not process payments.

## 6. Platform Settings

### Purpose

Platform Settings allows the Super Admin to manage confirmed Platform-level settings, especially pricing and other platform-wide configuration that belongs to the Super Admin role. Its purpose is to centralize Platform ownership controls without extending into Teacher Workspace settings or unconfirmed platform features.

### Description

The Project Context confirms that the Super Admin manages platform settings and owns pricing. Subscription pricing is globally configured for Teachers by the Super Admin. The exact pricing model, flat price versus volume tiers, remains PENDING. Platform Settings must therefore support pricing ownership without silently hardening an unresolved pricing model.

Platform Settings must not include online payment gateway setup in Version 1, because payment gateways are out of scope. Platform Settings must not create Platform staff accounts such as Support, Sales, or Accountant because those roles are not part of the five confirmed roles for Version 1.

### Functional Requirements

- **PA-SETTINGS-FR-001:** The system shall allow the Super Admin to view Platform Settings.
- **PA-SETTINGS-FR-002:** The system shall allow the Super Admin to manage confirmed Platform-level settings.
- **PA-SETTINGS-FR-003:** The system shall allow the Super Admin to configure Subscription pricing at Platform level according to confirmed pricing rules.
- **PA-SETTINGS-FR-004:** The system shall preserve unresolved pricing model decisions without hardening flat price or volume tiers until Q-013 is resolved.
- **PA-SETTINGS-FR-005:** The system shall prevent Teachers from managing Platform-level pricing.
- **PA-SETTINGS-FR-006:** The system shall prevent Platform Settings from changing Teacher Workspace settings.
- **PA-SETTINGS-FR-007:** The system shall prevent online payment gateway configuration in Version 1.
- **PA-SETTINGS-FR-008:** The system shall prevent creation of Platform staff accounts such as Support, Sales, or Accountant in Version 1.
- **PA-SETTINGS-FR-009:** The system shall record important Platform Settings updates in the Audit Log.
- **PA-SETTINGS-FR-010:** The system shall preserve confirmed localization and regional settings as PENDING until formally resolved.

### Business Rules

- Super Admin manages platform settings.
- Pricing is owned by the Super Admin (BR-015).
- Subscription pricing is globally configured at Platform level for Teachers.
- Flat price versus volume tiers remains PENDING (Q-013).
- Online payment gateways are out of scope for Version 1 (BR-019).
- Platform staff accounts such as Support, Sales, and Accountant are out of scope for Version 1.
- Localization and regional settings remain PENDING (Q-015).
- Important updates must be recorded in the Audit Log (BR-006).

### User Permissions

- Super Admin: may view and update confirmed Platform Settings.
- Super Admin: may manage pricing according to confirmed pricing rules.
- Super Admin: may not configure payment gateways in Version 1.
- Super Admin: may not create unconfirmed Platform staff roles in Version 1.
- Teacher, Teacher Staff, Student, and Parent users may not manage Platform Settings.

### Inputs

- Authenticated Super Admin account context.
- Platform-level setting values.
- Pricing configuration values according to confirmed pricing model.
- Setting update action.
- Platform-level configuration filters where applicable.

### Outputs

- Updated Platform Settings where valid and confirmed.
- Pricing configuration status.
- Rejection of unconfirmed or out-of-scope settings.
- Audit Log entries for important Platform Settings updates.

### Validations

- The user must be authenticated as Super Admin.
- Setting must be a confirmed Platform-level setting.
- Pricing updates must follow confirmed pricing rules.
- Pricing model assumptions must not harden unresolved Q-013.
- Payment gateway settings must be rejected as out of scope.
- Platform staff account creation must be rejected as out of scope.

### Error Handling

- If the user is not authenticated as Super Admin, the system shall deny access.
- If a setting value is invalid, the system shall reject the update.
- If an unconfirmed setting is submitted, the system shall reject or defer it until confirmed.
- If payment gateway configuration is attempted, the system shall reject it.
- If Platform staff account creation is attempted, the system shall reject it.
- If a Platform Settings update fails, the system shall preserve the previous valid setting state.

### Edge Cases

- Pricing model remains unresolved when Platform Settings are being documented.
- A Super Admin attempts to configure online payment gateways before future approval.
- A Super Admin attempts to create Support, Sales, or Accountant Platform staff accounts.
- Localization, timezone, currency, or target market settings remain pending.
- Historical invoices must preserve pricing as of their period once pricing records exist.

### Acceptance Criteria

- The Super Admin can manage confirmed Platform Settings.
- The Super Admin owns pricing configuration.
- Unresolved pricing model details are not silently assumed.
- Online payment gateway configuration is not available in Version 1.
- Platform staff accounts are not created in Version 1.
- Important Platform Settings updates are recorded in the Audit Log.

## 7. Audit Logs

### Purpose

Audit Logs allows the Super Admin to review Platform-level important actions according to the confirmed Audit Log Policy. Its purpose is to support accountability, traceability, and operational oversight while preserving Teacher Workspace isolation and pending Super Admin content-visibility boundaries.

### Description

The Audit Log is a first-class, platform-wide subsystem. Every important action must be recorded, including create, update, Archive, restore, login, permission change, Attendance change, Exam modification, Homework modification, and Subscription change. Audit Log entries are append-only and immutable, and retention is permanent.

The Super Admin can view Platform-scope events such as Subscription changes, logins, and administration according to the proposed scoped visibility model. Teacher Workspace Audit Log visibility remains subject to confirmed visibility boundaries; the Super Admin content-visibility boundary is PENDING and must not be silently expanded.

Any requested “Login as Teacher” action is not included as a confirmed Version 1 functional capability in this SRS. If such a capability is approved in a future Project Context update, every such action must be recorded in the Audit Log with actor, target Teacher account, reason, timestamp, and context according to the future approved policy.

### Functional Requirements

- **PA-AUDIT-FR-001:** The system shall record important actions in the Audit Log.
- **PA-AUDIT-FR-002:** The system shall allow the Super Admin to view Platform-scope Audit Log entries according to confirmed visibility boundaries.
- **PA-AUDIT-FR-003:** The system shall record create, update, Archive, and restore actions.
- **PA-AUDIT-FR-004:** The system shall record successful and failed login events.
- **PA-AUDIT-FR-005:** The system shall record permission changes.
- **PA-AUDIT-FR-006:** The system shall record Attendance changes.
- **PA-AUDIT-FR-007:** The system shall record Exam modifications.
- **PA-AUDIT-FR-008:** The system shall record Homework modifications.
- **PA-AUDIT-FR-009:** The system shall record Subscription changes.
- **PA-AUDIT-FR-010:** The system shall keep Audit Log entries append-only and immutable.
- **PA-AUDIT-FR-011:** The system shall retain Audit Log entries permanently.
- **PA-AUDIT-FR-012:** The system shall not implement “Login as Teacher” Audit Log events as a confirmed Version 1 feature unless “Login as Teacher” itself is formally confirmed.

### Business Rules

- Every important action is recorded in the Audit Log (BR-006).
- Audit Log entries are append-only and immutable.
- Audit Log retention is permanent.
- Audit Log events include create, update, Archive, restore, login, permission change, Attendance change, Exam modification, Homework modification, and Subscription change.
- Super Admin scoped visibility is proposed for platform-scope events; content visibility remains PENDING (Q-012).
- Teacher Staff actions are attributed to the Teacher Staff user, not to the Teacher.
- Subscription changes must be recorded in the Audit Log.
- “Login as Teacher” is not confirmed in the Project Context and therefore is not included as a confirmed Version 1 functional requirement.

### User Permissions

- Super Admin: may view Platform-scope Audit Log entries according to confirmed visibility boundaries.
- Super Admin: may not edit or delete Audit Log entries.
- Super Admin: may not use Audit Logs to bypass Teacher Workspace isolation.
- Teacher, Teacher Staff, Student, and Parent Audit Log visibility is outside this Platform Administration section unless defined in their own confirmed modules.

### Inputs

- Actor identity and role.
- Event type.
- Affected entity type and reference.
- Platform scope or Teacher Workspace context where applicable.
- Timestamp and origin information according to the Audit Log Policy.
- Filter criteria such as event type, actor, role, date, or scope where supported by later detailed requirements.

### Outputs

- Append-only Audit Log entries.
- Platform-scope Audit Log views for the Super Admin.
- Filtered Audit Log results where supported.
- Clear event attribution to the actual actor.
- Permanent historical record of important actions.

### Validations

- Important actions must create Audit Log entries.
- Audit Log entries must not be edited or deleted.
- Audit Log visibility must respect confirmed scope boundaries.
- Actor attribution must identify the actual actor and role.
- Teacher Staff actions must not be attributed to the Teacher.
- Subscription changes must be auditable.

### Error Handling

- If the user is not authenticated as Super Admin, the system shall deny Platform Audit Log access.
- If Audit Log filters are invalid, the system shall reject the request.
- If an action requires auditing but cannot be audited, the system shall not silently proceed in a way that loses required auditability.
- If a user attempts to edit or delete Audit Log entries, the system shall reject the action.
- If a request would expose Teacher Workspace information beyond confirmed visibility boundaries, the system shall deny or restrict the output.

### Edge Cases

- The Platform has no Audit Log entries yet.
- A failed login event must still be recorded.
- A Teacher Staff permission change must identify the Teacher Staff user and the permission change.
- A Subscription status change occurs after an outside-platform payment and must be auditable.
- Archived records remain referenced by historical Audit Log entries.
- A future request introduces “Login as Teacher,” but it remains unconfirmed until the Project Context is formally updated.

### Acceptance Criteria

- Important actions are recorded in the Audit Log.
- Audit Log entries are append-only, immutable, and permanently retained.
- The Super Admin can view Platform-scope Audit Logs according to confirmed visibility boundaries.
- Audit Log entries cannot be edited or deleted.
- Subscription changes are auditable.
- Teacher Workspace isolation is preserved in Audit Log visibility.
- “Login as Teacher” is not treated as a confirmed Version 1 capability in this SRS unless the Project Context is formally updated.

---

*End of PART 5 — Platform Administration (Super Admin) Module.*

---

# PART 6 — Non-Functional Requirements

This Part 6 defines non-functional requirements for Version 1 of the Unified Education Platform. It follows the IEEE 29148 SRS intent by specifying quality attributes, constraints, and acceptance criteria that govern the product as a whole. These requirements use only confirmed Project Context decisions. Where the Project Context marks a topic as PENDING, this section preserves that status and does not silently assume an implementation choice, target market, language, currency, timezone, tool, metric, or operating threshold.

## 1. Performance

### Purpose

Performance requirements ensure that the Platform supports the confirmed Version 1 product scope as a Web Application while preserving Teacher Workspace isolation, per-Teacher partitioning of Student records, and reliable access for Teachers, Teacher Staff, Students, Parents, and the Super Admin.

### Requirements

- **NFR-PERF-001:** The Platform shall support normal Version 1 operations for the confirmed roles: Super Admin, Teacher, Teacher Staff, Student, and Parent.
- **NFR-PERF-002:** The Platform shall preserve Teacher Workspace isolation during all operations, including summary views, reports, Attendance, Homework, Exams, Lessons, and payment-status views.
- **NFR-PERF-003:** The Platform shall not use Attendance or login activity in Billable Student calculation; the calculation shall depend on Enrollment duration only.
- **NFR-PERF-004:** The Platform shall support the confirmed Attendance methods in the Web Application, including daily Dynamic QR Code scanning by Students.
- **NFR-PERF-005:** The Platform shall handle archived records without treating them as active records in normal searches or active assignment lists.
- **NFR-PERF-006:** Quantified response-time, throughput, and concurrency targets are not confirmed in the Project Context and shall not be invented in this SRS.

### Constraints

- Version 1 is a Web Application only.
- No native mobile application performance requirements apply to Version 1.
- No implementation-specific performance mechanism is specified in this SRS.
- Performance behavior must not weaken authorization, Teacher Workspace isolation, Archive, or Audit Log requirements.

### Acceptance Criteria

- Platform operations remain scoped to the correct Teacher Workspace or user context.
- Performance requirements do not introduce unconfirmed numeric targets.
- Dynamic QR Code Attendance remains within Web Application scope.
- Billable Student calculation remains based on Enrollment duration only.
- Archived records are not treated as active records to improve performance or simplify queries.

## 2. Scalability

### Purpose

Scalability requirements ensure that the Platform can grow across multiple Teacher Workspaces while preserving the product model of one Student account, one Parent account, and many isolated Teacher Workspaces.

### Requirements

- **NFR-SCAL-001:** The Platform shall support multiple isolated Teacher Workspaces.
- **NFR-SCAL-002:** A Student account shall support studying with multiple Teachers while preserving separate per-Teacher records.
- **NFR-SCAL-003:** A Parent account shall support monitoring multiple linked Students while preserving the rule that one Student has only one Parent account in Version 1.
- **NFR-SCAL-004:** Scaling the number of Teachers shall not allow cross-Teacher data visibility.
- **NFR-SCAL-005:** Scaling the number of Students shall not create duplicate Student accounts.
- **NFR-SCAL-006:** Scaling reports and historical views shall preserve archived records and historical data availability.
- **NFR-SCAL-007:** Quantified scaling limits are not confirmed in the Project Context and shall not be invented in this SRS.

### Constraints

- The Platform is Multi-Tenant, with each Teacher Workspace isolated.
- Teacher Workspace isolation is mandatory and cannot be relaxed for scale.
- One Student belongs to only one Group per Teacher at any time.
- Version 1 supports exactly one Teaching Subject per Teacher account.
- Version 1 supports exactly one Parent account per Student.

### Acceptance Criteria

- Multiple Teacher Workspaces can exist without cross-Teacher data exposure.
- A Student can be associated with multiple Teachers through one global account.
- A Parent can monitor multiple linked Students through one account.
- Duplicate Student accounts are not introduced as a scaling workaround.
- No unconfirmed capacity targets are stated as approved requirements.

## 3. Availability

### Purpose

Availability requirements define the expected access posture for Version 1 without inventing unconfirmed uptime metrics, service levels, infrastructure commitments, or deployment details.

### Requirements

- **NFR-AVAIL-001:** The Platform shall be available as a Web Application for the confirmed Version 1 user roles.
- **NFR-AVAIL-002:** Availability design shall preserve access boundaries for Super Admin, Teacher, Teacher Staff, Student, and Parent roles.
- **NFR-AVAIL-003:** The Platform shall not rely on native mobile applications for Version 1 availability.
- **NFR-AVAIL-004:** Payment processing availability is not required because Version 1 records payment status only and actual payments are handled outside the Platform.
- **NFR-AVAIL-005:** Notifications availability is not required because push notifications, email notifications, and SMS notifications are out of scope for Version 1.
- **NFR-AVAIL-006:** Quantified uptime or service-level targets are not confirmed in the Project Context and shall not be invented in this SRS.

### Constraints

- Version 1 is Web Application only.
- Online payment gateways are out of scope.
- Notifications are out of scope.
- Availability mechanisms must not bypass authentication, authorization, Audit Log, Archive, or Teacher Workspace isolation.

### Acceptance Criteria

- The SRS does not define unconfirmed uptime percentages or service-level guarantees.
- Web Application availability is treated as the Version 1 access model.
- No native mobile, payment gateway, or notification availability requirements are introduced.
- Role boundaries remain enforced during all availability states.

## 4. Reliability

### Purpose

Reliability requirements ensure that confirmed business rules remain consistently enforced over time and across repeated operations.

### Requirements

- **NFR-REL-001:** The Platform shall reliably enforce one global Student account and prevent duplicate Student accounts.
- **NFR-REL-002:** The Platform shall reliably enforce one Group per Student per Teacher at any time.
- **NFR-REL-003:** The Platform shall reliably preserve historical Attendance, Homework, Exams, and grades when a Student moves between Groups.
- **NFR-REL-004:** The Platform shall reliably separate Teacher Workspace data.
- **NFR-REL-005:** The Platform shall reliably record important actions in the Audit Log.
- **NFR-REL-006:** The Platform shall reliably use Archive instead of permanent deletion.
- **NFR-REL-007:** The Platform shall reliably separate Flow A and Flow B.

### Constraints

- Historical data must never be deleted.
- Archive must replace permanent deletion everywhere.
- Teacher Workspace isolation is mandatory.
- Flow A and Flow B must never be conflated.
- Payment status recording must not become payment processing.

### Acceptance Criteria

- Repeated Student registration attempts do not create duplicate Student accounts.
- Group movement preserves historical records.
- Audit Log entries exist for important actions.
- Archived records remain available for historical reporting.
- Flow A and Flow B remain separate in all reliability-sensitive operations.

## 5. Security

### Purpose

Security requirements protect the Platform, users, Teacher Workspace data, private Teacher-owned content, and historical records from unauthorized access or unauthorized modification.

### Requirements

- **NFR-SEC-001:** The Platform shall enforce authentication for protected role-based access.
- **NFR-SEC-002:** The Platform shall enforce authorization for every role and context.
- **NFR-SEC-003:** Teacher Workspace data shall be completely isolated from other Teacher Workspaces.
- **NFR-SEC-004:** Lessons shall remain Teacher-owned and private to the Teacher's own Students.
- **NFR-SEC-005:** Question Banks shall remain Teacher-owned and private.
- **NFR-SEC-006:** Parent access shall be read-only and limited to linked Students.
- **NFR-SEC-007:** Teacher Staff access shall be limited to permissions assigned by the Teacher.
- **NFR-SEC-008:** The Platform shall prevent marketplace-style access to Teacher content.
- **NFR-SEC-009:** The Platform shall not include online payment gateway security requirements in Version 1 because online payment gateways are out of scope.

### Constraints

- Security requirements must not introduce unconfirmed features.
- Super Admin content visibility remains PENDING and must not be silently expanded.
- Teacher Staff permission granularity remains PENDING and must not be silently assumed.
- Lesson video hosting/protection details remain PENDING and must not be hardened in this SRS.

### Acceptance Criteria

- Users cannot access records outside their authorized role and context.
- Teachers cannot access another Teacher's data.
- Parents cannot access unlinked Students.
- Students cannot access another Student's records.
- Teacher-owned Lessons and Question Banks remain private.
- No payment gateway security scope is introduced for Version 1.

## 6. Authentication

### Purpose

Authentication requirements define the confirmed identity-access boundary for users of the Platform while avoiding unconfirmed login-flow details.

### Requirements

- **NFR-AUTHN-001:** The Platform shall authenticate users before allowing protected access.
- **NFR-AUTHN-002:** Authentication shall support the confirmed roles: Super Admin, Teacher, Teacher Staff, Student, and Parent.
- **NFR-AUTHN-003:** Successful and failed login events shall be recorded in the Audit Log.
- **NFR-AUTHN-004:** A Student shall have exactly one global account.
- **NFR-AUTHN-005:** A Parent shall have one account and may monitor multiple linked Students.
- **NFR-AUTHN-006:** Student accounts may originate from Student self-registration or Teacher-created Student accounts, without creating duplicates.
- **NFR-AUTHN-007:** Laravel Sanctum is the confirmed authentication technology for Version 1.

### Constraints

- Duplicate Student accounts are not allowed.
- Version 1 supports exactly one Parent account per Student.
- Authentication details beyond the confirmed technology and business rules are not specified in this SRS.
- “Login as Teacher” is not confirmed in the Project Context and is not an approved Version 1 authentication requirement in this SRS.

### Acceptance Criteria

- Protected access requires authenticated user context.
- Login events are auditable.
- Student duplicate-account prevention is preserved.
- Parent account boundaries are preserved.
- Authentication requirements do not add unconfirmed login methods or impersonation behavior.

## 7. Authorization

### Purpose

Authorization requirements ensure that authenticated users can access only the data and actions permitted by their role, Teacher Workspace, linked Student relationship, or account context.

### Requirements

- **NFR-AUTHZ-001:** The Platform shall enforce role-based access for Super Admin, Teacher, Teacher Staff, Student, and Parent.
- **NFR-AUTHZ-002:** Teachers shall access only their own Teacher Workspace.
- **NFR-AUTHZ-003:** Teacher Staff shall access only the creating Teacher Workspace and only according to Teacher-assigned permissions.
- **NFR-AUTHZ-004:** Students shall access only their own account and per-Teacher Student records.
- **NFR-AUTHZ-005:** Parents shall access only linked Students and shall have read-only access everywhere.
- **NFR-AUTHZ-006:** Super Admin authorization shall be limited to Platform-level administration and confirmed visibility boundaries.
- **NFR-AUTHZ-007:** Authorization shall prevent unauthorized modification of Attendance, Homework, Exams, grades, payment status, Teacher Workspace settings, and Platform Settings.

### Constraints

- Teacher Workspace isolation is mandatory.
- Parent access is read-only.
- Super Admin content visibility is PENDING.
- Teacher Staff permission granularity is PENDING.
- No authorization rule may silently assume unresolved PENDING decisions.

### Acceptance Criteria

- Teacher access never crosses into another Teacher Workspace.
- Teacher Staff access is limited to assigned permissions.
- Parent access is limited to linked Students and read-only operations.
- Student access is limited to the Student's own records.
- Super Admin access does not bypass pending Teacher-private content boundaries.

## 8. Audit Logging

### Purpose

Audit Logging requirements ensure that important actions are traceable, immutable, and permanently retained according to the confirmed Audit Log Policy.

### Requirements

- **NFR-AUDIT-001:** The Platform shall record create actions in the Audit Log.
- **NFR-AUDIT-002:** The Platform shall record update actions in the Audit Log.
- **NFR-AUDIT-003:** The Platform shall record Archive actions in the Audit Log.
- **NFR-AUDIT-004:** The Platform shall record restore actions in the Audit Log.
- **NFR-AUDIT-005:** The Platform shall record successful and failed login events in the Audit Log.
- **NFR-AUDIT-006:** The Platform shall record permission changes in the Audit Log.
- **NFR-AUDIT-007:** The Platform shall record Attendance changes in the Audit Log.
- **NFR-AUDIT-008:** The Platform shall record Exam modifications in the Audit Log.
- **NFR-AUDIT-009:** The Platform shall record Homework modifications in the Audit Log.
- **NFR-AUDIT-010:** The Platform shall record Subscription changes in the Audit Log.
- **NFR-AUDIT-011:** Audit Log entries shall be append-only and immutable.
- **NFR-AUDIT-012:** Audit Log retention shall be permanent.

### Constraints

- Audit Log entries must not be edited or deleted.
- Teacher Staff actions must be attributed to the Teacher Staff user, not to the Teacher.
- Audit Log visibility must preserve authorization boundaries.
- The proposed record shape and transactional mechanics remain outside confirmed business-rule detail unless approved in later architecture documentation.

### Acceptance Criteria

- Each confirmed important action type is auditable.
- Audit Log entries cannot be modified or removed.
- Login events are recorded whether successful or failed.
- Permission changes are auditable.
- Subscription changes are auditable.
- Teacher Staff action attribution is preserved.

## 9. Data Privacy

### Purpose

Data Privacy requirements protect private Teacher Workspace data, Student data, Parent-linked Student visibility, Teacher-owned Lessons, and Teacher-owned Question Banks.

### Requirements

- **NFR-PRIV-001:** Teacher Workspace data shall be completely isolated.
- **NFR-PRIV-002:** Teachers shall not see another Teacher's data.
- **NFR-PRIV-003:** Students shall see per-Teacher partitioned content only for their own Teacher relationships.
- **NFR-PRIV-004:** Parents shall see only linked Students.
- **NFR-PRIV-005:** Parent access shall remain read-only.
- **NFR-PRIV-006:** Lesson videos shall be Teacher-owned and private to the Teacher's own Students.
- **NFR-PRIV-007:** Question Banks shall be Teacher-owned and private.
- **NFR-PRIV-008:** The Platform shall not provide course discovery or cross-Teacher browsing.
- **NFR-PRIV-009:** Super Admin visibility into Teacher-private content shall not be expanded while content visibility remains PENDING.

### Constraints

- Teacher Workspace isolation cannot be relaxed.
- The Platform is not an online course marketplace.
- Teachers do not sell courses through the Platform.
- Super Admin content visibility is PENDING.
- Future privacy decisions must not contradict the Project Context.

### Acceptance Criteria

- A Teacher cannot access another Teacher's Students, Lessons, Question Bank, Exams, Reports, or payment-status records.
- A Parent cannot access an unlinked Student.
- A Student cannot access another Student's records.
- Teacher-owned Lessons are not exposed across Teachers.
- Teacher-owned Question Banks are not exposed across Teachers.
- Marketplace-style discovery is unavailable.

## 10. Backup and Recovery

### Purpose

Backup and Recovery requirements protect continuity of historical data, Audit Log records, archived records, and confirmed business records without inventing unconfirmed recovery targets or infrastructure details.

### Requirements

- **NFR-BACKUP-001:** Backup and recovery planning shall preserve historical data.
- **NFR-BACKUP-002:** Backup and recovery planning shall preserve Audit Log entries.
- **NFR-BACKUP-003:** Backup and recovery planning shall preserve archived records and their historical relationships.
- **NFR-BACKUP-004:** Backup and recovery planning shall preserve Student transfer history, including historical Attendance, Homework, Exams, and grades.
- **NFR-BACKUP-005:** Backup and recovery planning shall preserve Flow A and Flow B separation.
- **NFR-BACKUP-006:** Quantified recovery objectives are not confirmed in the Project Context and shall not be invented in this SRS.

### Constraints

- Historical data is never deleted.
- Audit Log retention is permanent.
- Archive replaces permanent deletion.
- Backup and recovery mechanisms are not specified in this SRS.
- Recovery must not merge Teacher Workspace data.

### Acceptance Criteria

- Backup and recovery requirements preserve historical data and Audit Log records.
- Archived records remain recoverable as archived records with historical relationships intact.
- Student transfer history remains intact after recovery.
- Teacher Workspace isolation is preserved after recovery.
- No unconfirmed recovery time or recovery point targets are introduced.

## 11. Monitoring

### Purpose

Monitoring requirements support operational awareness of confirmed Platform behavior without introducing unconfirmed monitoring tools, dashboards, infrastructure, or external notification features.

### Requirements

- **NFR-MON-001:** Monitoring shall support awareness of Platform availability at a high level.
- **NFR-MON-002:** Monitoring shall support awareness of authentication activity through Audit Log login events.
- **NFR-MON-003:** Monitoring shall support awareness of important actions through Audit Log records.
- **NFR-MON-004:** Monitoring shall not expose Teacher Workspace data across Teacher boundaries.
- **NFR-MON-005:** Monitoring shall not depend on push notifications, email notifications, or SMS notifications in Version 1.
- **NFR-MON-006:** Monitoring tooling, metrics, alert thresholds, and operational dashboards are not confirmed in the Project Context and shall not be invented in this SRS.

### Constraints

- Notifications are out of scope for Version 1.
- Teacher Workspace isolation must be preserved.
- Super Admin content visibility remains PENDING.
- Monitoring must not become a workaround for unauthorized data access.

### Acceptance Criteria

- Monitoring requirements do not introduce notification features.
- Monitoring requirements do not expose Teacher-private content.
- Audit Log remains the confirmed accountability mechanism for important actions.
- No unconfirmed tool, metric, or alert threshold is presented as approved.

## 12. Error Handling

### Purpose

Error Handling requirements ensure that invalid, unauthorized, incomplete, or out-of-scope actions are rejected consistently while preserving security, privacy, historical integrity, and user-role boundaries.

### Requirements

- **NFR-ERR-001:** The Platform shall reject unauthorized access attempts.
- **NFR-ERR-002:** The Platform shall reject actions that violate Teacher Workspace isolation.
- **NFR-ERR-003:** The Platform shall reject duplicate Student account creation.
- **NFR-ERR-004:** The Platform shall reject attempts to assign a Student to more than one Group for the same Teacher at the same time.
- **NFR-ERR-005:** The Platform shall reject permanent deletion attempts and require Archive instead.
- **NFR-ERR-006:** The Platform shall reject online payment processing attempts in Version 1.
- **NFR-ERR-007:** The Platform shall reject unsupported Homework formats outside Text, Image, and PDF.
- **NFR-ERR-008:** The Platform shall reject attempts to change a Teacher's Teaching Subject after account creation.
- **NFR-ERR-009:** The Platform shall reject Parent attempts to modify Attendance, grades, Homework, Exams, or payment status.
- **NFR-ERR-010:** Error responses shall not expose Teacher-private data or unrelated user data.

### Constraints

- Error handling must not reveal unauthorized data.
- Error handling must preserve Audit Log requirements where important actions or failed logins are involved.
- Error handling must not introduce unconfirmed notification behavior.
- Error handling wording and presentation details are not specified in this SRS.

### Acceptance Criteria

- Unauthorized access is denied.
- Business rule violations are rejected.
- Out-of-scope payment gateway behavior is rejected.
- Unsupported Homework formats are rejected.
- Error handling does not reveal private Teacher Workspace data.
- Failed login events are auditable.

## 13. Maintainability

### Purpose

Maintainability requirements ensure the requirements baseline remains consistent with the frozen Project Context and supports gradual, documentation-first development.

### Requirements

- **NFR-MAINT-001:** Requirements shall remain consistent with the Project Context.
- **NFR-MAINT-002:** Future documents shall not contradict the Project Context.
- **NFR-MAINT-003:** Ambiguities shall remain PENDING and shall not be silently assumed.
- **NFR-MAINT-004:** Canonical terminology shall be used consistently.
- **NFR-MAINT-005:** Architecture and documentation shall come before code.
- **NFR-MAINT-006:** Future implementation shall trace to the Project Context and canonical document set.
- **NFR-MAINT-007:** No unnecessary features shall be invented.

### Constraints

- The Project Context is frozen for Version 1.
- Product decisions belong to the Product Owner.
- Technical documents must not override confirmed business rules.
- PENDING items must remain unresolved until formally decided.

### Acceptance Criteria

- The SRS uses canonical terminology.
- The SRS does not introduce unconfirmed Version 1 features.
- The SRS preserves PENDING topics without hardening them.
- Future documentation can trace requirements to the Project Context.
- No source-code or implementation-specific requirements are introduced in this section.

## 14. Localization

### Purpose

Localization requirements preserve the confirmed Arabic-default, English-supported automatic RTL/LTR decision while leaving timezone, currency, and target market decisions PENDING.

### Requirements

- **NFR-LOC-001:** The Platform shall not assume final language requirements until localization decisions are confirmed.
- **NFR-LOC-002:** The Platform shall not assume final timezone requirements until localization decisions are confirmed.
- **NFR-LOC-003:** The Platform shall not assume final currency requirements until localization decisions are confirmed.
- **NFR-LOC-004:** The Platform shall not assume final target market or country until confirmed.
- **NFR-LOC-005:** Localization and regional requirements shall remain consistent with Q-015 until formally resolved.

### Constraints

- Arabic (default) and English (fully supported) are confirmed; timezone, currency, and target market/country are PENDING.
- Proposed defaults are not confirmed requirements.
- No UI language, translation, formatting, currency, or timezone implementation behavior is specified in this SRS.

### Acceptance Criteria

- The SRS does not state Arabic, English, any currency, any timezone, or any target country as confirmed Version 1 requirements.
- Localization decisions remain PENDING.
- Future localization requirements must not contradict the Project Context.
- No implementation details for localization are introduced.

## 15. Accessibility

### Purpose

Accessibility requirements acknowledge that the Platform is a Web Application for multiple roles while avoiding unconfirmed accessibility standards or implementation details.

### Requirements

- **NFR-ACCESS-001:** The Platform shall not define native mobile accessibility requirements for Version 1.
- **NFR-ACCESS-002:** Accessibility requirements shall apply to the Web Application scope when formally specified.
- **NFR-ACCESS-003:** Accessibility requirements shall not alter confirmed business rules, role permissions, Teacher Workspace isolation, Parent read-only access, or Archive policy.
- **NFR-ACCESS-004:** Specific accessibility standards, conformance levels, and detailed criteria are not confirmed in the Project Context and shall not be invented in this SRS.

### Constraints

- Version 1 is Web Application only.
- No accessibility conformance level is confirmed in the Project Context.
- Accessibility must not create unauthorized access or unauthorized modification pathways.
- No UI implementation details are specified in this SRS.

### Acceptance Criteria

- Accessibility requirements remain within Web Application scope.
- No native mobile accessibility requirements are introduced.
- No unconfirmed accessibility standard or conformance level is asserted.
- Accessibility does not weaken authorization or data privacy boundaries.

## 16. Browser Compatibility

### Purpose

Browser Compatibility requirements acknowledge the confirmed Web Application delivery model while avoiding unconfirmed browser support matrices, versions, devices, or implementation details.

### Requirements

- **NFR-BROWSER-001:** Version 1 shall be delivered as a Web Application.
- **NFR-BROWSER-002:** Browser access shall support the confirmed Version 1 role surfaces through the Web Application.
- **NFR-BROWSER-003:** Browser access shall support Student Dynamic QR Code scanning within the Web Application scope.
- **NFR-BROWSER-004:** Native mobile application compatibility is out of scope for Version 1.
- **NFR-BROWSER-005:** Specific browser names, versions, device categories, and compatibility matrices are not confirmed in the Project Context and shall not be invented in this SRS.

### Constraints

- Version 1 is Web Application only.
- No native mobile application compatibility applies to Version 1.
- Browser compatibility must not change business rules or role boundaries.
- No UI implementation details are specified in this SRS.

### Acceptance Criteria

- Browser compatibility requirements remain aligned with Web Application scope.
- Dynamic QR Code scanning remains within Web Application scope.
- No unconfirmed browser matrix is asserted.
- No native mobile compatibility requirement is introduced.

## 17. Data Retention

### Purpose

Data Retention requirements ensure that historical records, Audit Log entries, archived records, and Student transfer history remain available according to confirmed business rules.

### Requirements

- **NFR-RET-001:** Historical data shall never be deleted.
- **NFR-RET-002:** Historical data shall always remain available.
- **NFR-RET-003:** Reports and history queries shall include archived records where applicable and clearly indicated.
- **NFR-RET-004:** Student transfers shall preserve historical Attendance, Homework, Exams, and grades.
- **NFR-RET-005:** Audit Log retention shall be permanent.
- **NFR-RET-006:** Historical invoices shall keep the price as of their period.
- **NFR-RET-007:** Archived records shall retain historical relationships.

### Constraints

- Permanent deletion is not allowed.
- Archive replaces deletion everywhere.
- Historical relationships must not be detached, rewritten, or repointed by archival.
- Data retention requirements must preserve Teacher Workspace isolation.

### Acceptance Criteria

- Historical records remain available after structural changes.
- Audit Log records are permanently retained.
- Archived records remain available in reports where applicable.
- Student transfer history is preserved.
- Historical pricing remains tied to the applicable period.
- Data retention does not expose data across Teacher Workspaces.

## 18. Archiving Policy

### Purpose

Archiving Policy requirements define the non-functional behavior of Archive as the required replacement for permanent deletion throughout Version 1.

### Requirements

- **NFR-ARCH-001:** The Platform shall use Archive instead of permanent deletion everywhere.
- **NFR-ARCH-002:** Archived records shall never appear in normal searches.
- **NFR-ARCH-003:** Archived records shall never appear in active dropdown lists, pickers, selectors, or assignment lists.
- **NFR-ARCH-004:** Archived records shall remain available in reports and historical queries.
- **NFR-ARCH-005:** Archived records shall be clearly indicated when included in reports or historical views.
- **NFR-ARCH-006:** Authorized users shall be able to restore archived records where restoration is allowed.
- **NFR-ARCH-007:** Restore actions shall be recorded in the Audit Log.
- **NFR-ARCH-008:** Archive shall never detach, rewrite, or repoint historical relationships.
- **NFR-ARCH-009:** No hard delete shall exist anywhere in the system.

### Constraints

- Archive applies to all records, by all actors, everywhere.
- Archiving a container must not archive historical records unless later detailed entity rules confirm behavior without contradicting the Project Context.
- Archived records must remain available for reports.
- Archive and restore actions must be auditable.

### Acceptance Criteria

- No permanent deletion behavior is specified or accepted.
- Archived records are excluded from normal active searches and active selection lists.
- Archived records remain visible in historical reports with clear indication.
- Restore actions are auditable.
- Historical relationships remain intact after Archive and restore operations.

---

*End of PART 6 — Non-Functional Requirements.*


## Confirmed Audit Clarifications

The following confirmed clarifications are owned by `00_Project_Context.md` §9.9 (BR-023 through BR-025). They do not alter Flow A, Archive, Audit Log, or the external-payment boundary.

- **Parent workflow:** a Parent creates an account through Parent registration and submits a Parent–Student link request. The Teacher responsible for the Student’s active Enrollment approves or rejects it. Approval is allowed only when the Student has no other linked Parent; it creates read-only access. The Parent may request unlinking, and that Teacher approves the unlink. Request, approval, rejection, and unlinking are Audit Log events; historical records remain preserved.
- **Per Lesson Flow B:** a billable lesson is a Lesson completed for a Group while the Student has an active Enrollment in that Group. The obligation is recorded on completion at the Group’s recorded Price. Drafting, scheduling, publishing, viewing, Attendance, or assignment alone is not billable; one Student has at most one obligation for the same completed lesson. The Platform records payment status only, and payment remains outside the Platform.
- **Group transfer:** Flow A enrollment duration is accumulated across Groups of the same Teacher in the same Billing Cycle; a transfer does not reset the more-than-15-calendar-days test. Flow B obligations remain tied to the Group and completed Lesson that created them.
