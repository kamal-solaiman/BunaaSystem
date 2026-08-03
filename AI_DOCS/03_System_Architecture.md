# 1. Document Purpose

This Software Architecture Document defines the technical architecture for Version 1 of the Unified Education Platform.

The purpose of this document is to describe how the Platform is structured technically, how major architectural layers communicate, how Teacher Workspace isolation is enforced, and how confirmed subsystems such as authentication, authorization, file storage, QR Attendance, Exam Engine, Reporting, Logging, Audit Log, and security are organized.

This document is an architecture document only. It does not define source code, database tables, endpoint specifications, UI screens, or detailed implementation tasks. It must remain consistent with:

- `AI_DOCS/00_Project_Context.md`
- `AI_DOCS/01_Project_Vision.md`
- `AI_DOCS/02_Software_Requirements.md`

If any conflict is discovered, `AI_DOCS/00_Project_Context.md` remains the official Single Source of Truth.

---

# 2. Architecture Goals

The architecture of the Unified Education Platform is designed to support the confirmed Version 1 product direction: one Platform, one global Student account, one Parent account, and many isolated Teacher Workspaces.

The main architecture goals are:

1. **Strict Teacher Workspace isolation**
   Each Teacher operates in a completely isolated Teacher Workspace. No Teacher can see another Teacher's data under any circumstance.

2. **Unified Student identity**
   A Student has exactly one global account and may study with multiple Teachers. The architecture must allow one identity while keeping Attendance, Homework, Exams, Lessons, and Subscription-related status partitioned per Teacher.

3. **Unified Parent monitoring**
   A Parent has one account and may monitor multiple linked Students. Parent access must remain read-only and limited to linked Students.

4. **Clear separation of Flow A and Flow B**
   Flow A is the Teacher's Platform Subscription. Flow B is Student or Parent fees owed to a Teacher. The architecture must keep these flows separate in data, logic, reporting, and authorization.

5. **Web Application only for Version 1**
   All Version 1 capabilities, including Dynamic QR Code Attendance scanning, are delivered through the Web Application. Native mobile applications are not part of Version 1.

6. **Private Teacher-owned content**
   Lessons and Question Banks are Teacher-owned and private. The architecture must prevent cross-Teacher content access and marketplace behavior.

7. **Historical integrity**
   Student transfers, Archive, historical records, reports, payment status, and Audit Log entries must preserve history without hard deletion or rewriting.

8. **Auditability**
   Important actions must be recorded in the Audit Log. Audit Log entries are append-only, immutable, and permanently retained.

9. **Controlled scope**
   The architecture must not introduce native mobile applications, online payment gateways, notifications, marketplace behavior, video homework, or multiple Teaching Subjects per Teacher account in Version 1.

---

# 3. Architectural Principles

The Platform architecture follows the confirmed principles and boundaries from the Project Context.

## 3.1 Multi-Tenant Isolation First

The Platform uses a Multi-Tenant architecture where each Teacher Workspace is the tenant boundary. Workspace-owned records are scoped to the owning Teacher Workspace. All access paths must preserve that scope.

Teacher Workspace isolation applies to:

- Educational Grades
- Groups
- Students within the Teacher relationship
- Attendance
- Homework
- Exams
- Question Bank
- Lessons
- Reports
- Teacher Staff
- Settings
- Payment-status records related to that Teacher Workspace

## 3.2 One Identity, Contextual Access

A person may interact with the Platform through a confirmed role context. Version 1 includes five roles:

- Super Admin
- Teacher
- Teacher Staff
- Student
- Parent

The architecture must support a global identity model while applying role-specific and context-specific authorization rules.

## 3.3 Workspace-Scoped Business Logic

Teacher operational logic must run inside the correct Teacher Workspace context. A request that concerns Teacher-owned data must always be evaluated against the active Teacher Workspace.

## 3.4 Read-Only Parent Boundary

Parent access is read-only everywhere. A Parent can view information for linked Students only and cannot modify Attendance, Homework, Exams, grades, payment status, Student records, Teacher records, or Teacher Workspace data.

## 3.5 Archive Instead of Permanent Deletion

No permanent deletion exists anywhere in the system. Archive replaces deletion. Archived records are excluded from active searches and active selection lists but remain available in reports and historical views.

## 3.6 Audit Log as a First-Class Subsystem

The Audit Log is not optional. Important actions across the Platform must produce Audit Log entries. Audit Log entries are append-only, immutable, and permanently retained.

## 3.7 Separation of Money Flows

Flow A and Flow B are separate architectural concerns:

- **Flow A:** Teacher pays Platform for the monthly Subscription.
- **Flow B:** Student or Parent pays Teacher, derived from Group Price and Pricing Type.

Version 1 records payment status only and does not process transactions.

## 3.8 No Marketplace Architecture

The Platform is not an online course marketplace. There is no course discovery, browsing across Teachers, public Teacher content catalog, or mechanism for one Teacher's content to reach another Teacher's Students.

---

# 4. High-Level System Overview

The Unified Education Platform is a Web Application built on the official Version 1 architecture baseline: React 19 with TypeScript, Vite, and Tailwind CSS on the frontend; Laravel 12 with PHP 8.3 on the backend; MySQL 8 for persistence; Laravel Sanctum for authentication; Laravel Gates & Policies with Custom RBAC for authorization; and cPanel Shared Hosting as the primary deployment target.

## 4.1 Official Version 1 Technology Baseline

| Concern | Official Version 1 Baseline |
|---|---|
| Primary Deployment Target | cPanel Shared Hosting |
| Future Deployment Target | VPS / Cloud |
| Backend | Laravel 12, PHP 8.3 |
| Frontend | React 19, TypeScript, Vite, Tailwind CSS |
| Database | MySQL 8 |
| Authentication | Laravel Sanctum |
| Authorization | Laravel Gates & Policies, Custom RBAC based on project requirements |
| Cache | File Cache |
| Queue | Database Queue |
| Session Driver | Database |
| Storage | Laravel Public Storage |
| Scheduler | Laravel Scheduler with Cron Jobs |
| Mail | SMTP |
| Web Server | Apache or LiteSpeed |

Version 1 must not require Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices.

At a high level, the Platform consists of the following major components:

1. **React Web Frontend**
   Provides browser-based access for Super Admin, Teacher, Teacher Staff, Student, and Parent role contexts using React 19, TypeScript, Vite, and Tailwind CSS.

2. **Laravel Backend**
   Owns business logic, authentication integration, authorization, tenant scoping, validation, Audit Log creation, reporting orchestration, billing-status logic, file access control, QR Attendance handling, and Exam Engine operations using Laravel 12 on PHP 8.3.

3. **MySQL 8 Database**
   Persists Platform data, Teacher Workspace data, Student identity, Parent links, Enrollment history, payment status, Archive state, session data, queue jobs, and Audit Log entries.

4. **Laravel Public Storage**
   Stores Teacher-owned files such as Lesson videos and supported Homework files using Laravel Public Storage while requiring application-level ownership checks and access control so Teacher-owned content remains private to the correct Teacher Workspace and authorized users.

5. **Audit Log Subsystem**
   Records important actions and supports permanent traceability.

6. **Reporting Subsystem**
   Produces workspace-scoped and platform-scoped reports while preserving Archive, historical records, and visibility boundaries.

## Layer Communication Summary

The frontend communicates with the backend through HTTP-based REST communication. The backend is the only layer that directly applies business rules, authorization decisions, tenant scoping, and persistence rules. The backend communicates with MySQL for data persistence and with private file storage for controlled media and file access.

The frontend must never bypass backend authorization, tenant scoping, Audit Log requirements, or Archive rules.

---

# 5. Layered Architecture

The Platform follows a layered architecture to separate responsibilities and protect business rules.

## 5.1 Presentation Layer

The Presentation Layer is the React Web Application. It is responsible for:

- Rendering role-specific browser experiences.
- Sending user actions to the backend.
- Displaying data returned by the backend.
- Maintaining client-side navigation state.
- Preserving user context such as selected Student for Parent monitoring or selected Teacher relationship for Student views.

The Presentation Layer is not responsible for final authorization, tenant isolation, billing calculation, Audit Log creation, or persistence decisions.

## 5.2 Application Layer

The Application Layer lives in the Laravel backend. It coordinates user actions and application workflows. It is responsible for:

- Receiving frontend requests.
- Applying authentication context.
- Calling authorization checks.
- Establishing Teacher Workspace context where required.
- Coordinating business services.
- Returning structured responses to the frontend.

## 5.3 Domain / Business Logic Layer

The Domain Layer owns confirmed business rules, including:

- Student has exactly one global account.
- Student belongs to only one Group per Teacher at any time.
- Teacher Workspace isolation.
- Parent read-only access.
- Billable Student calculation based on Enrollment duration only.
- Flow A and Flow B separation.
- Homework formats limited to Text, Image, and PDF.
- One Teaching Subject per Teacher account.
- Archive instead of permanent deletion.
- Audit Log event requirements.

## 5.4 Persistence Layer

The Persistence Layer manages interaction with MySQL. It is responsible for:

- Persisting records.
- Loading records within correct scope.
- Preserving historical relationships.
- Supporting Archive state.
- Supporting reporting queries.
- Supporting Audit Log persistence.

The Persistence Layer must support tenant-scoped access patterns and must not allow cross-Teacher data access.

## 5.5 Storage Layer

The Storage Layer manages private file and media storage. It is responsible for:

- Storing Teacher-owned Lessons.
- Storing supported Homework files where applicable.
- Preserving file ownership and access rules.
- Preventing public cross-Teacher access.
- Retaining archived files according to Archive and historical retention rules.

---

# 6. Frontend Architecture

The frontend is a React 19 Web Application built with TypeScript, Vite, and Tailwind CSS. It provides all Version 1 user interaction through the browser.

## 6.1 Responsibilities

The frontend is responsible for:

- Presenting role-specific application areas for Super Admin, Teacher, Teacher Staff, Student, and Parent.
- Displaying data received from the backend.
- Sending user actions to the backend.
- Handling client-side routing and user context.
- Supporting Web Application access for Dynamic QR Code Attendance scanning.
- Presenting archived records as historical when the backend returns them for reports or history.

## 6.2 Role Context Handling

The frontend must reflect the authenticated role context:

- **Super Admin:** Platform-level administration only.
- **Teacher:** Own Teacher Workspace only.
- **Teacher Staff:** Creating Teacher Workspace only, based on assigned permissions.
- **Student:** Own account, with content partitioned per Teacher.
- **Parent:** Linked Students only, read-only.

The frontend must not treat visible or hidden controls as security enforcement. Security decisions must be made by the backend.

## 6.3 Communication with Backend

The frontend communicates with the Laravel backend using REST-style HTTP communication. The frontend sends authenticated requests and receives data shaped for the authenticated role and context.

The frontend must not directly access the database or file storage. All data and file access must pass through backend authorization and tenant scoping.

## 6.4 Scope Boundaries

The frontend must not introduce:

- Native mobile application behavior.
- Online payment gateway behavior.
- Push, email, or SMS notifications.
- Marketplace discovery.
- Cross-Teacher browsing.
- Multiple Teaching Subjects under one Teacher account.
- Video homework.

---

# 7. Backend Architecture

The backend is built with Laravel 12 on PHP 8.3 and owns the Platform's server-side architecture.

## 7.1 Responsibilities

The backend is responsible for:

- Authentication integration through Laravel Sanctum.
- Authorization enforcement for all roles.
- Teacher Workspace context resolution.
- Business rule enforcement.
- Data validation.
- Coordination of Teacher, Student, Parent, and Super Admin workflows.
- Audit Log creation.
- Archive and restore behavior.
- Billing-status calculation and payment-status recording.
- Reporting orchestration.
- QR Attendance handling.
- Exam Engine coordination.
- File storage access control.
- File Cache for cache responsibilities.
- Database Queue for queued work.
- Database session driver for session persistence.
- Laravel Scheduler with Cron Jobs for scheduled tasks.
- SMTP as the mail transport baseline, without adding Version 1 notification features.

## 7.2 Layer Communication

The backend receives requests from the React frontend, identifies the authenticated user, resolves role and context, applies authorization, executes domain logic, communicates with persistence and storage layers, records Audit Log entries where required, and returns results to the frontend.

## 7.3 Business Rule Enforcement

The backend is the authoritative enforcement point for confirmed rules. The frontend may guide user interaction, but the backend must enforce:

- Teacher Workspace isolation.
- Student duplicate prevention.
- One Group per Student per Teacher.
- Parent read-only access.
- Teacher Staff permission boundaries.
- Teaching Subject immutability.
- Flow A and Flow B separation.
- Archive instead of permanent deletion.
- Audit Log event creation.

## 7.4 Backend Module Responsibilities

At an architectural level, the backend groups responsibilities around confirmed domains:

- Identity and authentication.
- Role and authorization management.
- Teacher Workspace operations.
- Student account and Enrollment context.
- Parent linked-Student monitoring.
- Attendance.
- Homework.
- Lessons.
- Exams and Question Bank.
- Reporting.
- Subscriptions and payment status.
- Archive.
- Audit Log.

These are architectural domains only and do not define database tables or endpoint specifications.

---

# 8. Database Architecture

The database layer uses MySQL 8 as the official Version 1 database.

## 8.1 Responsibilities

The database is responsible for persisting:

- Global user identity records.
- Role context and relationships.
- Teacher Workspace-owned records.
- Student and Parent relationships.
- Enrollment history.
- Attendance records.
- Homework records.
- Exam definitions, attempts, and grades.
- Lesson metadata and file references.
- Subscription and payment-status records.
- Archive state.
- Audit Log entries.
- Reporting source data.

This section does not define database tables, columns, indexes, or physical schema.

## 8.2 Teacher Workspace Scoping

Every Teacher Workspace-owned record must be associated with the owning Teacher Workspace. This association is the data boundary for tenant isolation.

Database access patterns must ensure that:

- Teacher queries are scoped to the Teacher's own Teacher Workspace.
- Teacher Staff queries are scoped to the creating Teacher Workspace.
- Student queries return only the Student's own records partitioned per Teacher.
- Parent queries return only linked Student records.
- Super Admin queries remain Platform-scoped and respect pending content-visibility boundaries.

## 8.3 Historical Data Preservation

The database architecture must preserve historical relationships. Student transfers do not move, delete, or rewrite historical Attendance, Homework, Exams, or grades.

Historical data remains available for reports and history queries. Archived records remain retained and clearly identifiable when included in historical output.

## 8.4 Archive State

Archive state must be represented in a way that supports:

- Exclusion from normal active searches.
- Exclusion from active selection lists.
- Inclusion in historical reports.
- Restoration by authorized users.
- Audit Log recording of Archive and restore actions.

## 8.5 Flow A and Flow B Separation

The database architecture must preserve separate persistence and reporting paths for:

- Flow A: Teacher to Platform Subscription.
- Flow B: Student or Parent to Teacher fees.

The architecture must prevent these flows from being conflated in reporting, billing status, or payment status.

---

# 9. Authentication Architecture

Authentication is based on Laravel Sanctum, as confirmed in the Project Context and the official Version 1 stack.

## 9.1 Responsibilities

Authentication is responsible for:

- Identifying the user account.
- Establishing authenticated session or token context through Laravel Sanctum.
- Supporting authenticated access for the five confirmed roles.
- Recording successful and failed login events in the Audit Log.

## 9.2 Authentication Flow

The high-level authentication flow is:

1. The user submits authentication credentials through the Web Application.
2. The Laravel backend validates the credentials using the confirmed authentication mechanism.
3. On success, the backend establishes an authenticated context for the user.
4. The backend determines available role contexts for the authenticated user.
5. Subsequent requests include authentication context.
6. The backend applies authorization and tenant scoping before executing any action.
7. Successful and failed login attempts are recorded in the Audit Log.

## 9.3 Student Account Activation

A Student account may be created by the Student or manually by a Teacher. If a Teacher creates the Student account, the Student can later activate and use the same account. The authentication architecture must support this without creating duplicate Student accounts.

## 9.4 Authentication Boundaries

The authentication architecture must not introduce:

- Duplicate Student accounts.
- Multiple Parent accounts for one Student in Version 1.
- Unconfirmed impersonation behavior.
- Native mobile authentication requirements for Version 1.

---

# 10. Authorization (RBAC) Architecture

Authorization controls what an authenticated user can access or modify. Version 1 has five confirmed roles: Super Admin, Teacher, Teacher Staff, Student, and Parent. The official authorization baseline is Laravel Gates & Policies with Custom RBAC based on project requirements.

## 10.1 Responsibilities

Authorization is responsible for:

- Enforcing role boundaries.
- Enforcing Teacher Workspace scope.
- Enforcing Parent linked-Student scope.
- Enforcing Student account scope.
- Enforcing Teacher Staff assigned permissions.
- Preventing unauthorized modification.
- Protecting Teacher-owned private content.

## 10.2 Role Boundaries

### Super Admin

The Super Admin operates at Platform-level scope. The Super Admin manages Teachers, Subscriptions under Flow A, pricing, platform settings, and global reports. Super Admin content visibility remains PENDING and must not be silently expanded.

### Teacher

A Teacher operates one completely isolated Teacher Workspace. A Teacher cannot access another Teacher's data under any circumstance.

### Teacher Staff

Teacher Staff are created by a Teacher and exist only inside that Teacher Workspace. Teacher Staff hold only permissions assigned by the Teacher. Permission granularity remains PENDING.

### Student

A Student accesses one global account and may study with multiple Teachers. Student records remain partitioned per Teacher.

### Parent

A Parent accesses only linked Students and has read-only access everywhere.

## 10.3 Authorization Flow

The high-level authorization flow is:

1. Authentication establishes the user identity.
2. The backend determines the active role context.
3. If the action is Teacher Workspace-owned, the backend resolves the Teacher Workspace context.
4. The backend checks whether the role is allowed to perform the action.
5. The backend checks whether the requested record belongs to the allowed scope.
6. If permitted, business logic executes.
7. If denied, the action is rejected without exposing unauthorized data.
8. If the action is important, the Audit Log records the event according to policy.

## 10.4 Authorization Constraints

Authorization must not rely on frontend-only checks. All final authorization decisions must be enforced by the backend.

Authorization must preserve:

- Teacher Workspace isolation.
- Parent read-only access.
- Student account boundaries.
- Teacher Staff permission boundaries.
- Super Admin Platform scope.
- Private Lessons and private Question Banks.

---

# 11. Multi-Tenant Architecture

The Platform uses a Multi-Tenant architecture where each Teacher Workspace is a tenant.

## 11.1 Tenant Boundary

The Teacher Workspace is the tenant boundary. Teacher-owned records must be associated with the owning Teacher Workspace and accessed only within that context.

Tenant-scoped domains include:

- Educational Grades
- Groups
- Student relationships with that Teacher
- Enrollments
- Attendance
- Homework
- Exams
- Question Bank
- Lessons
- Reports
- Teacher Staff
- Teacher Workspace Settings
- Flow B payment-status records

## 11.2 Tenant Isolation Rules

Tenant isolation requires that:

- No Teacher can see another Teacher's data.
- No Teacher Staff can access another Teacher Workspace.
- A Student can access multiple Teachers through one account, but the Student's records remain partitioned per Teacher.
- A Parent can view linked Students, but Teacher data remains visible only through the linked Student relationship and only in read-only form.
- Super Admin Platform-level visibility must respect pending content-visibility boundaries.

## 11.3 Tenant Context Resolution

For every Teacher Workspace-owned operation, the backend must resolve the Teacher Workspace context before accessing data or executing business logic.

Tenant context may be derived from:

- The authenticated Teacher.
- The creating Teacher Workspace for Teacher Staff.
- The Student's relationship with a Teacher.
- The Parent's linked Student and that Student's Teacher relationship.
- Platform-level Super Admin operations where allowed.

## 11.4 Tenant Isolation in Reporting

Reports must preserve tenant isolation. Teacher reports are limited to the Teacher's own Teacher Workspace. Student and Parent views are partitioned by Teacher. Super Admin global reports must not assume access to Teacher-private content while content visibility remains PENDING.

---

# 12. File Storage Architecture

File storage is responsible for controlled access to files used by confirmed Version 1 features. The official Version 1 storage baseline is Laravel Public Storage, selected for compatibility with cPanel Shared Hosting. Because Lessons and Teacher-owned files are private by business rule, application-level authorization and ownership checks must control access to stored files.

## 12.1 Storage Responsibilities

The storage architecture supports Laravel Public Storage on the primary cPanel Shared Hosting deployment target. It supports:

- Lesson videos uploaded by Teachers for their own Students.
- Homework files in supported formats: Text, Image, and PDF.
- File references needed by reports or historical records.
- Retention of archived file references according to Archive and historical policies.

## 12.2 Lesson Storage

Lessons are Teacher-owned private videos. Lesson files must be associated with the owning Teacher Workspace and must be accessible only to the Teacher's own Students and authorized Teacher-side users.

The storage architecture must prevent:

- Cross-Teacher Lesson access.
- Marketplace-style discovery.
- Public course browsing.
- Exposure of one Teacher's Lessons to another Teacher's Students.

Lesson video hosting and protection details remain PENDING and must not be silently hardened beyond the Project Context. S3 Storage is not required for Version 1.

## 12.3 Homework File Storage

Homework supports Text, Image, and PDF only in Version 1. Video homework is not supported.

Homework file storage must:

- Associate Homework files with the owning Teacher Workspace.
- Preserve Student and Teacher relationship context.
- Prevent unauthorized access by unrelated Students, Parents, Teachers, or Teacher Staff.
- Preserve historical access where reports and history require it.

## 12.4 Archive and Retention

Archived Lessons, Homework files, and file references must be retained according to Archive and historical data policies. Archival must not detach file references from historical records.

---

# 13. QR Attendance Architecture

QR Attendance is part of the Attendance subsystem. Version 1 supports three Attendance methods:

1. Dynamic QR Code generated daily and scanned by the Student through the Web Application.
2. Printed ID Card scanned by a QR scanner device.
3. Manual entry by the Teacher.

## 13.1 Responsibilities

The QR Attendance architecture is responsible for:

- Supporting daily Dynamic QR Code Attendance.
- Supporting Student scanning through the Web Application.
- Supporting printed ID Card scanning as an Attendance method.
- Supporting Teacher manual Attendance entry.
- Scoping Attendance records to the correct Teacher Workspace.
- Recording Attendance changes in the Audit Log.
- Preserving Attendance history through Student Group movement.

## 13.2 Dynamic QR Code Flow

The high-level Dynamic QR Code Attendance flow is:

1. A daily Dynamic QR Code is generated for Attendance in a Teacher Workspace context.
2. The QR Code is displayed for the class.
3. The Student scans the QR Code through the Web Application.
4. The backend authenticates the Student context.
5. The backend verifies that the Student is associated with the relevant Teacher relationship.
6. The backend records Attendance in the Teacher Workspace context.
7. The Attendance change is recorded in the Audit Log.

## 13.3 ID Card Attendance Flow

The ID Card Attendance flow is:

1. The Student presents a printed ID Card with a QR code.
2. A QR scanner device reads the ID Card.
3. The backend resolves the Student identity and Teacher Workspace Attendance context.
4. Attendance is recorded if the Student relationship is valid.
5. The Attendance change is recorded in the Audit Log.

## 13.4 Manual Attendance Flow

Manual Attendance is performed by the Teacher or authorized Teacher Staff within the Teacher Workspace. The backend verifies authorization, records Attendance, and records the Attendance change in the Audit Log.

## 13.5 QR Attendance Constraints

Attendance must not be used to calculate Billable Students. Billable Student calculation is based on Enrollment duration only.

---

# 14. Exam Engine Architecture

The Exam Engine supports Teacher-owned Question Banks and workspace-scoped Exams.

## 14.1 Responsibilities

The Exam Engine is responsible for:

- Managing Teacher-owned private Question Banks.
- Supporting confirmed question types: Multiple Choice, True/False, Essay, and Bubble Sheet.
- Building Exams only from the owning Teacher's Question Bank.
- Keeping Exam definitions, attempts, and grades workspace-scoped.
- Supporting automatic grading where confirmed, including Bubble Sheet.
- Preserving Exam attempts and grades when Students move between Groups.
- Recording Exam modifications in the Audit Log.

## 14.2 Question Bank Ownership

Each Question Bank belongs to one Teacher Workspace. The architecture must prevent any Teacher from using, viewing, or modifying another Teacher's Question Bank.

## 14.3 Exam Composition

Exams are composed only from questions owned by the same Teacher Workspace. Cross-Teacher question reuse or discovery is not part of Version 1.

## 14.4 Exam Attempt and Grade Scope

Exam attempts and grades are scoped by Student and Teacher relationship. A Student may have Exam records from multiple Teachers, but those records remain separated per Teacher.

Parents may view Exam information and grades only for linked Students and only in read-only mode.

## 14.5 Bubble Sheet

Bubble Sheet is an electronic exam format simulating traditional paper bubble sheets. Students answer by selecting bubbles on screen. Automatic grading is supported for Bubble Sheet where applicable.

## 14.6 Essay Questions

Essay questions are supported. Essay grading behavior is handled as part of the Exam Engine and remains subject to detailed functional requirements, without altering the confirmed question-type list.

---

# 15. Reporting Architecture

Reporting provides visibility into confirmed data areas while preserving scope, privacy, Archive, and historical rules.

## 15.1 Responsibilities

The Reporting architecture is responsible for:

- Teacher Workspace reports for Attendance, Homework, Exam results, payments, and Student performance.
- Student views of per-Teacher learning records.
- Parent read-only monitoring of linked Students.
- Super Admin global reports within confirmed Platform-level scope and pending content-visibility boundaries.
- Historical reporting that includes archived records where applicable.
- Clear separation between Flow A and Flow B.

## 15.2 Teacher Reports

Teacher reports are scoped to the Teacher's own Teacher Workspace. They must not include another Teacher's data.

Teacher reports may include:

- Attendance
- Homework
- Exam results
- Payments
- Student performance

## 15.3 Student Reporting Views

Student reporting views show only the Student's own records and partition those records per Teacher.

## 15.4 Parent Reporting Views

Parent reporting views are read-only and limited to linked Students. Parent views must preserve per-Teacher separation inside each linked Student context.

## 15.5 Super Admin Reports

Super Admin global reports operate at Platform scope. Content visibility remains PENDING, so the architecture must not assume unrestricted browsing of Teacher-private content.

## 15.6 Archive and Historical Data

Reports must include archived records when historical reporting requires them and must clearly indicate archived status. Historical data is never deleted.

---

# 16. Logging & Audit Architecture

Logging and Audit are separate but related architectural concerns. The Audit Log is the confirmed business-critical record of important actions.

## 16.1 Audit Log Responsibilities

The Audit Log must record:

- Create actions.
- Update actions.
- Archive actions.
- Restore actions.
- Successful and failed login events.
- Permission changes.
- Attendance changes.
- Exam modifications.
- Homework modifications.
- Subscription changes.

Audit Log entries are append-only, immutable, and permanently retained.

## 16.2 Audit Flow

The high-level Audit Log flow is:

1. A user performs an important action.
2. The backend authenticates the user.
3. The backend authorizes the action.
4. The backend executes the business operation.
5. The backend writes an Audit Log entry for the action.
6. The Audit Log records the actor, role, context, event type, affected record reference, and relevant change information according to approved detailed design.

## 16.3 Actor Attribution

Teacher Staff actions must be attributed to the Teacher Staff user, not to the Teacher.

## 16.4 Audit Visibility

Audit visibility must respect authorization boundaries:

- Teacher visibility is limited to the Teacher Workspace scope where permitted.
- Super Admin visibility is Platform scope and subject to pending content-visibility boundaries.
- Parent and Student access to Audit Log is not defined as a confirmed Version 1 product surface.

## 16.5 Operational Logging

Operational logs may support diagnostics and system health, but they do not replace the Audit Log. Operational logging details, tools, and retention rules are not defined in this document.

---

# 17. Error Handling Strategy

The Error Handling Strategy ensures that invalid or unauthorized operations fail safely without exposing private data or weakening business rules.

## 17.1 Responsibilities

Error handling is responsible for:

- Rejecting unauthorized access.
- Rejecting invalid business operations.
- Preventing cross-Teacher data exposure.
- Preventing duplicate Student accounts.
- Preventing permanent deletion.
- Preventing unsupported Homework formats.
- Preventing online payment processing attempts in Version 1.
- Preserving previous valid state when an update fails.

## 17.2 Error Handling Flow

The backend handles errors in the following sequence:

1. Authenticate the user.
2. Resolve role and context.
3. Resolve Teacher Workspace, Student, Parent, or Platform scope.
4. Validate the requested operation.
5. Apply authorization checks.
6. Reject invalid or unauthorized operations safely.
7. Avoid exposing unauthorized data in the response.
8. Record failed login events and other required auditable events in the Audit Log.

## 17.3 Business Rule Violations

The backend must reject operations that violate confirmed rules, including:

- Student duplicate creation.
- Assigning a Student to more than one Group per Teacher.
- Changing a Teacher's Teaching Subject after account creation.
- Parent modification of read-only records.
- Cross-Teacher data access.
- Payment processing in Version 1.
- Permanent deletion.

## 17.4 Error Visibility

Error responses must provide enough information for the authorized user to understand that the action failed, without revealing Teacher-private data, unlinked Student data, or internal implementation details.

---

# 18. Security Architecture

Security architecture protects the Platform, role boundaries, Teacher Workspaces, Student records, Parent links, Teacher-owned files, Question Banks, Lessons, Audit Logs, and payment-status data.

## 18.1 Security Responsibilities

Security architecture is responsible for:

- Authentication.
- Authorization.
- Teacher Workspace isolation.
- Parent read-only boundaries.
- Student account protection.
- Teacher Staff permission enforcement.
- Private file access control.
- Audit Log integrity.
- Archive and historical data protection.
- Flow A and Flow B separation.

## 18.2 Identity Security

Identity security must preserve:

- One global Student account.
- Duplicate Student account prevention.
- One Parent account per Student in Version 1.
- Teacher Staff association with the creating Teacher Workspace.
- Super Admin Platform-level scope.

## 18.3 Data Security

Data security must enforce:

- Teacher Workspace data isolation.
- Student per-Teacher partitioning.
- Parent linked-Student-only access.
- Private Lesson access only for the Teacher's own Students.
- Private Question Bank access only for the owning Teacher Workspace.

## 18.4 Payment Security

Version 1 records payment status only and does not process transactions. Therefore, online payment gateway security is out of scope for Version 1.

## 18.5 Pending Security Decisions

The following security-related topics remain PENDING and must not be silently assumed:

- Super Admin content visibility.
- Teacher Staff permission granularity.
- Lesson video hosting/protection details.
- Non-payment enforcement behavior.

---

# 19. Performance & Scalability Strategy

The Performance and Scalability Strategy supports growth without weakening business rules or tenant isolation.

## 19.1 Performance Strategy

The architecture must support confirmed Version 1 operations through the Web Application. Performance work must preserve:

- Teacher Workspace isolation.
- Authentication and authorization.
- Audit Log creation.
- Archive rules.
- Historical data availability.
- Flow A and Flow B separation.

No confirmed numeric response-time, throughput, concurrency, or capacity targets exist in the Project Context. Therefore, this document does not define such targets.

## 19.2 Scalability Strategy

The Platform must scale across:

- Multiple Teacher Workspaces.
- Students studying with multiple Teachers.
- Parents monitoring multiple linked Students.
- Teacher-owned Lessons and Question Banks.
- Workspace-scoped reports.
- Platform-level Super Admin reports.

Scaling must not introduce:

- Cross-Teacher data access.
- Duplicate Student accounts.
- Multiple Parent accounts for one Student in Version 1.
- Marketplace behavior.
- Payment processing.

## 19.3 Reporting Scalability

Reporting must preserve historical availability and Archive indications while maintaining scope boundaries. Teacher reports remain workspace-scoped. Parent and Student views remain relationship-scoped. Super Admin reports remain Platform-scoped and subject to pending visibility boundaries.

## 19.4 File Scalability

File storage scalability must preserve private Teacher ownership and access control. Lesson video storage details such as hosting, protection, and quota remain PENDING and must be resolved separately.

---

# 20. Deployment Overview

Version 1 is deployed primarily to cPanel Shared Hosting as a Web Application consisting of a React 19 frontend, Laravel 12 backend running on PHP 8.3, MySQL 8 database, and Laravel Public Storage. VPS / Cloud is the future deployment target and must not be required for Version 1.

## 20.1 Deployment Responsibilities

The deployment architecture must support:

- Browser access to the React 19 Web Application.
- Backend application hosting for Laravel 12 on PHP 8.3.
- MySQL 8 persistence.
- Laravel Public Storage with application-level access control.
- Secure authentication through Laravel Sanctum.
- Authorization through Laravel Gates & Policies and Custom RBAC.
- File Cache.
- Database Queue.
- Database session driver.
- Laravel Scheduler executed through Cron Jobs.
- SMTP as the mail transport baseline without introducing Version 1 notification features.
- Apache or LiteSpeed as the Web Server baseline for cPanel Shared Hosting.
- Audit Log retention.
- Archive and historical data preservation.

## 20.2 Deployment Communication

At deployment level:

1. Users access the React 19 Web Application through a browser.
2. The React frontend communicates with the Laravel 12 backend using REST-style HTTP communication.
3. The Laravel backend communicates with MySQL 8 for persistence, sessions, and database-backed queues.
4. The Laravel backend controls access to Laravel Public Storage through application-level authorization.
5. The Laravel backend records Audit Log entries for important actions.
6. Laravel Scheduler runs through Cron Jobs on the hosting environment.
7. SMTP is available as the mail transport baseline without adding push, email, or SMS notification features to Version 1.

## 20.3 Deployment Scope Exclusions

Deployment architecture for Version 1 does not include:

- Native mobile applications.
- Online payment gateways.
- Push notifications, email notifications, or SMS notifications.
- Marketplace infrastructure.
- Video homework infrastructure.
- Docker.
- Redis.
- Kubernetes.
- S3 Storage.
- WebSockets.
- Microservices.

Detailed environment, release, and infrastructure specifications belong to the deployment planning documentation and are not defined in this architecture document.

---

# 21. Architecture Constraints

The architecture is constrained by the confirmed Project Context and must not contradict it.

## 21.1 Confirmed Constraints

1. Version 1 is Web Application only.
2. The official Version 1 technology stack is Laravel 12, PHP 8.3, React 19, TypeScript, Vite, Tailwind CSS, MySQL 8, REST communication style, Laravel Sanctum authentication, Laravel Gates & Policies, Custom RBAC, File Cache, Database Queue, Database session driver, Laravel Public Storage, Laravel Scheduler with Cron Jobs, SMTP, and Apache or LiteSpeed.
3. The architecture is Multi-Tenant with each Teacher Workspace isolated.
4. Student identity is global and unique.
5. Duplicate Student accounts are not allowed.
6. A Student belongs to only one Group per Teacher at any time.
7. A Parent sees only linked Students and has read-only access everywhere.
8. Version 1 supports exactly one Parent account per Student.
9. Each Teacher account represents exactly one Teaching Subject.
10. Teaching Subject cannot be changed after account creation.
11. Lesson videos are Teacher-owned and private.
12. Question Banks are Teacher-owned and private.
13. Homework supports Text, Image, and PDF only.
14. Video homework is out of scope.
15. Online payment gateways are out of scope.
16. Notifications are out of scope.
17. The Platform is not a marketplace.
18. Archive replaces permanent deletion everywhere.
19. Historical data is never deleted.
20. Important actions must be recorded in the Audit Log.
21. Primary deployment target is cPanel Shared Hosting.
22. Future deployment target is VPS / Cloud.
23. Version 1 must not require Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices.

## 21.2 Pending Constraints

The following items are PENDING and must not be hardened without Product Owner confirmation:

- Non-payment enforcement.
- Lesson video hosting/protection.
- Teacher Staff permission granularity.
- Super Admin content visibility.
- Flat price versus volume tiers.
- Arabic (default) and English (fully supported) are confirmed; timezone, currency, and target market/country.

---

# 22. Future Architecture Considerations

Future architecture considerations may be explored after Version 1, but they must not change the confirmed Version 1 scope.

Potential future areas include:

1. **Native mobile applications**
   Native mobile applications are out of scope for Version 1 and may be considered in a future phase.

2. **Online payment gateway integration**
   Version 1 records payment status only. Payment gateway integration may be considered in future versions as a separate decision.

3. **Notifications**
   Push notifications, email notifications, and SMS notifications are out of scope for Version 1 and may be considered separately in the future.

4. **More detailed media protection**
   Lesson video hosting/protection remains PENDING. Future architecture may define hosting, playback protection, quotas, and retention details.

5. **More detailed Teacher Staff permissions**
   Teacher Staff permission granularity remains PENDING and should be resolved in RBAC documentation.

6. **Super Admin content visibility boundary**
   Super Admin visibility into Teacher-private content remains PENDING and must be resolved before implementation hardens any access model.

7. **Localization and regional configuration**
   Arabic (default) and English (fully supported) are confirmed; timezone, currency, and target market/country remain PENDING. Future architecture may define localization and regional infrastructure after confirmation.

All future architecture considerations must preserve the foundational principles of the Platform: Teacher Workspace isolation, one global Student account, one Parent account, private Teacher-owned content, clear Flow A and Flow B separation, Archive instead of deletion, and permanent Audit Log retention.
