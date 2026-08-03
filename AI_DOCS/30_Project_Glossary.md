# 30 — Project Glossary

## Document Scope

This document is the official glossary for Version 1 of the Unified Education Platform. It defines every important business and technical term used throughout the canonical document set.

This glossary does not define source code, APIs, database tables, UI implementation, or physical configuration. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found.

Terms are sorted alphabetically. Each entry includes the term, its definition, its context within the project, related terms, and the documents where the term is primarily defined or used.

---

# A

---

## Account

**Definition:** A registered user identity on the Platform used to authenticate and access role-appropriate capabilities.

**Context:** Every user of the Platform — Super Admin, Teacher, Teacher Staff, Student, and Parent — has an Account. A Student has exactly one global Account and may study with multiple Teachers through that single Account. A Parent has one Account and may monitor multiple linked Students. Duplicate Student Accounts are not allowed.

**Related Terms:** Student, Parent, Teacher, Teacher Staff, Super Admin, Authentication, Role

**Related Documents:** `00_Project_Context.md` §6, `02_Software_Requirements.md`, `07_Data_Dictionary.md` §1

---

## Active Student

**Definition:** A Student who has an active account and is currently engaged with the Platform. The term is used as a product-level engagement metric.

**Context:** Active Student status is distinct from Billable Student status. Account existence, account activation, Attendance, login activity, Homework, Exam, or Lesson activity do not make a Student Billable. The authoritative billing term is Billable Student, determined solely by Enrollment duration. Active Student must not be used as an ambiguous substitute for Billable Student in Subscription calculations, reports, labels, or audit context.

**Related Terms:** Billable Student, Student, Enrollment, Subscription, Billing Cycle

**Related Documents:** `17_Subscription_Billing.md` §4

---

## API

**Definition:** Application Programming Interface — the RESTful HTTP interface through which the React frontend communicates with the Laravel backend.

**Context:** All Version 1 API endpoints use the `/api/v1` prefix. The API uses JSON for request and response bodies, follows RESTful resource-oriented conventions, and uses Laravel Sanctum for authentication. The API does not support notifications, payment gateway transactions, or marketplace discovery in Version 1. Breaking changes require a future API version.

**Related Terms:** REST API, Laravel Sanctum, Authentication, Endpoint

**Related Documents:** `10_API_Design.md`, `03_System_Architecture.md` §4.1

---

## Archive

**Definition:** The required alternative to permanent deletion throughout the Platform. Archived records are removed from active use but retained for historical purposes.

**Context:** Archive replaces permanent deletion for all records, by all actors, everywhere (BR-005). Archived records never appear in normal searches or active dropdown lists. They remain available in reports and historical queries, clearly indicated. Archived records can be restored by authorized users. Archive and restore actions are recorded in the Audit Log. No hard delete exists anywhere in the system. The canonical term is Archive — never "Delete."

**Related Terms:** Restore, Audit Log, Historical Data, Soft Delete, BR-005, BR-014

**Related Documents:** `00_Project_Context.md` §11, `06_Database_Design.md` §7, §15, `23_Security_Standards.md` §2.1

---

## Attendance

**Definition:** The record of a Student's presence or participation status at a Teacher Workspace session.

**Context:** Version 1 supports three Attendance methods: Dynamic QR Code generated daily and scanned by the Student through the Web Application; printed ID Card scanned by a QR scanner device; and manual entry by the Teacher (BR-010). Attendance records are Teacher Workspace scoped. Attendance changes are recorded in the Audit Log. Attendance is not used to calculate Billable Students; the Flow A calculation uses Enrollment duration only (BR-008). Attendance history is preserved when a Student moves between Groups (BR-007).

**Related Terms:** Dynamic QR Code, ID Card, Attendance Session, Teacher Workspace, Enrollment, Billable Student

**Related Documents:** `16_QR_Attendance_System.md`, `00_Project_Context.md` §7.1, `02_Software_Requirements.md` Part 2 §5

---

## Attendance Session

**Definition:** The Attendance context in which confirmed Attendance methods operate, belonging to a Teacher Workspace and associated with a Group and date context.

**Context:** An Attendance Session belongs to the current Teacher Workspace. Dynamic QR Code generation, ID Card scanning, and Manual Attendance all operate within a valid Attendance Session. Attendance changes for a Session are auditable. The Session title, recurrence, meeting schedule, start/end time, and capacity are not confirmed and must not be inferred.

**Related Terms:** Attendance, Dynamic QR Code, ID Card, Teacher Workspace, Group

**Related Documents:** `16_QR_Attendance_System.md` §10, `07_Data_Dictionary.md` §13

---

## Authentication

**Definition:** The process of verifying a user's identity before granting access to protected Platform resources.

**Context:** Laravel Sanctum is the confirmed authentication technology for Version 1. All protected API endpoints require an authenticated user context. Authentication must be established before any role, scope, or permission resolution occurs. Successful and failed login events are recorded in the Audit Log. Student accounts may be self-registered or Teacher-created without creating duplicates. Duplicate Student accounts are not allowed.

**Related Terms:** Laravel Sanctum, Login, Account, Authorization, RBAC

**Related Documents:** `03_System_Architecture.md` §9, `23_Security_Standards.md` §3

---

## Authorization

**Definition:** The process of determining whether an authenticated user is permitted to perform a specific action on a specific resource within their scope.

**Context:** Authorization uses Laravel Gates & Policies with Custom RBAC based on the logical permission catalog. Every protected request follows a sequence: authentication, role resolution, scope resolution, permission check, ownership/relationship check, Archive state check, and execution or rejection. Authorization must be enforced server-side; frontend visibility or hidden controls are never sufficient security controls. Unauthorized requests are denied without exposing restricted data.

**Related Terms:** RBAC, Permission, Role, Teacher Workspace, Laravel Gates, Policies

**Related Documents:** `08_RBAC.md`, `09_Permission_Matrix.md`, `23_Security_Standards.md` §4

---

# B

---

## Background Job

**Definition:** A deferred, periodic, or scheduled task that does not execute synchronously within the user request lifecycle.

**Context:** Version 1 uses Laravel Database Queue for queued work and Laravel Scheduler with Cron Jobs for scheduled tasks. Background jobs handle monthly Subscription processing, Attendance cleanup, Exam automatic grading, report preparation, file cleanup, and Audit Log maintenance. Jobs must be idempotent, preserve Teacher Workspace isolation, and respect cPanel Shared Hosting resource limits. Background jobs must not introduce notifications, payment processing, or unconfirmed features.

**Related Terms:** Database Queue, Laravel Scheduler, Cron Job, Idempotent

**Related Documents:** `21_Background_Jobs.md`, `03_System_Architecture.md` §4.1

---

## Billable Student

**Definition:** A Student who is counted toward a Teacher's monthly Platform Subscription based on Enrollment duration.

**Context:** A Student is Billable if enrolled in a Teacher's Group for more than 15 calendar days during the Billing Cycle (BR-008). Students enrolled for 15 days or less are not counted. The calculation is based on Enrollment duration only — Attendance and login activity are NOT used. The formula is: Monthly Subscription = Billable Students × Price Per Student. Billable Student is the authoritative billing term and must not be confused with Active Student.

**Related Terms:** Billing Cycle, Subscription, Enrollment, Price Per Student, Flow A, BR-008

**Related Documents:** `00_Project_Context.md` §5.1, `17_Subscription_Billing.md` §6

---

## Billing Cycle

**Definition:** The calendar-month period used for Flow A Subscription billing.

**Context:** The Billing Cycle starts on the first day of every calendar month and ends on the last day of the same month. A new Billing Cycle begins automatically on the first day of the next month (D-006). Billable Students are evaluated within the applicable Billing Cycle. Historical Billing Cycle records are never permanently deleted and retain the pricing as of their period.

**Related Terms:** Billable Student, Subscription, Calendar Month, Flow A

**Related Documents:** `00_Project_Context.md` §5.1, `17_Subscription_Billing.md` §5, `07_Data_Dictionary.md` §32

---

## Bubble Sheet

**Definition:** An electronic Exam format that simulates traditional paper bubble sheets, where Students answer by selecting bubbles on screen and automatic grading is supported.

**Context:** Bubble Sheet is one of four confirmed Question Types supported by the Question Bank (BR-011). It is an electronic on-screen selection format, not a paper scan workflow. Automatic grading is supported for Bubble Sheet where applicable. Bubble Sheet Questions, answers, attempts, and grades remain Teacher Workspace scoped.

**Related Terms:** Question Bank, Exam, Question Type, Automatic Grading, BR-011

**Related Documents:** `15_Exam_Engine.md` §13, `00_Project_Context.md` §9.6

---

## Business Rule

**Definition:** A confirmed, numbered, and binding constraint that governs Platform behavior, referenced as `BR-xxx` from every document and from code.

**Context:** Business Rules are established in `00_Project_Context.md` and are the authoritative behavioral constraints for Version 1. They cover identity and tenancy, academic structure, lifecycle and history, money flows, classroom operations, content ownership, and parent relationships. Business Rules carry CONFIRMED status unless otherwise noted.

**Related Terms:** BR-001 through BR-022, Project Context, CONFIRMED, PENDING

**Related Documents:** `00_Project_Context.md` §9

---

# C

---

## Cache

**Definition:** A temporary data storage mechanism used to improve performance by caching frequently accessed, slowly changing data.

**Context:** Version 1 uses File Cache as the official cache driver, compatible with cPanel Shared Hosting. Cache entries must respect scope boundaries — Teacher Workspace cache entries must be scoped to the specific Teacher Workspace. Cache must be invalidated when underlying data changes. Cache must not be used to store sensitive data such as passwords, tokens, or credentials. Redis is not required for Version 1.

**Related Terms:** File Cache, cPanel Shared Hosting, Performance

**Related Documents:** `25_Performance_Scalability.md` §8, `03_System_Architecture.md` §4.1

---

## cPanel Shared Hosting

**Definition:** The primary deployment target for Version 1, providing web server, database, and hosting capabilities through the cPanel control panel interface.

**Context:** cPanel Shared Hosting is the confirmed primary Version 1 deployment target (D-044). It provides PHP 8.3, MySQL 8, Apache or LiteSpeed web server, Cron Jobs, and SSL. Version 1 must not require Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices. All infrastructure choices (File Cache, Database Queue, Database sessions, Laravel Public Storage) are selected for cPanel compatibility. VPS / Cloud is the future deployment target.

**Related Terms:** Deployment, Apache, LiteSpeed, Cron Job, PHP 8.3, MySQL 8

**Related Documents:** `26_Deployment_Plan.md` §3, `03_System_Architecture.md` §20

---

## Cron Job

**Definition:** A scheduled task executed at a configured interval by the hosting server, used to trigger the Laravel Scheduler.

**Context:** On cPanel Shared Hosting, a Cron Job runs the Laravel Scheduler command at a one-minute interval. The Scheduler itself coordinates all scheduled tasks including Billing Cycle initialization, Billable Student calculation, Subscription snapshot generation, expired QR context cleanup, Exam auto-grading, report processing, and maintenance tasks. Only one Scheduler instance must run at a time.

**Related Terms:** Laravel Scheduler, Background Job, Scheduled Task, cPanel Shared Hosting

**Related Documents:** `21_Background_Jobs.md` §5, `26_Deployment_Plan.md` §16

---

# D

---

## Dashboard

**Definition:** The initial operational summary view presented to an authenticated user, scoped to their role and permissions.

**Context:** Each role has a Dashboard: Super Admin views Platform-level administration and global reporting within confirmed visibility boundaries. Teacher views a Teacher Workspace-scoped operational overview. Teacher Staff sees only sections allowed by explicit Teacher-assigned permissions. Student views only their own per-Teacher-partitioned information. Parent views only linked-Student read-only monitoring information with a clear selected Student context.

**Related Terms:** Teacher Workspace, Role, Teacher Panel, Student Panel, Parent Panel, Super Admin

**Related Documents:** `02_Software_Requirements.md` Parts 2–5, `13_UI_UX_Guidelines.md` §16

---

## Database Queue

**Definition:** The Laravel queue mechanism that stores jobs in the MySQL 8 database for deferred processing.

**Context:** Database Queue is the official Version 1 queue driver, compatible with cPanel Shared Hosting. Jobs are stored in MySQL 8 and processed by a queue worker triggered through the Laravel Scheduler or a cPanel-compatible process. Database Queue does not require Redis, SQS, or Beanstalkd. Successfully processed jobs should be cleaned up periodically.

**Related Terms:** Background Job, Queue, MySQL 8, Laravel, cPanel Shared Hosting

**Related Documents:** `21_Background_Jobs.md` §4, `03_System_Architecture.md` §4.1

---

## Decision (D-xxx)

**Definition:** A documented architectural or business decision with a unique identifier, recorded in the project decisions documentation.

**Context:** Decisions are referenced as D-001 through D-051 in the canonical document set. They cover technology stack, architecture, business rules, security, deployment, and development process. Decisions carry CONFIRMED, PROPOSED, or PENDING status. CONFIRMED decisions are binding and change only through a documented decision update.

**Related Terms:** CONFIRMED, PROPOSED, PENDING, Business Rule, Project Context

**Related Documents:** `29_Project_Decisions.md`, `00_Project_Context.md` §16

---

## Dynamic QR Code

**Definition:** A QR Code generated daily for Attendance, displayed for the class, and scanned by the Student through the Web Application.

**Context:** Dynamic QR Codes are one of three confirmed Attendance methods (BR-010). They are generated daily, not reused across days. The Student scans the code through the Web Application, including a browser on a mobile-capable device where supported. The backend authenticates the Student, verifies the Teacher relationship and Attendance context, and records Attendance. The QR visual value alone never proves Attendance eligibility.

**Related Terms:** Attendance, Attendance Session, ID Card, Web Application, BR-010

**Related Documents:** `16_QR_Attendance_System.md` §4, `00_Project_Context.md` §9.6

---

# E

---

## Educational Grade

**Definition:** A Teacher-created education level inside a Teacher Workspace (e.g., First Preparatory, Second Preparatory, First Secondary).

**Context:** Educational Grades organize Groups within a Teacher Workspace. They belong only to the Teacher Workspace that created them. Educational Grades are independent from Teaching Subjects — a Teaching Subject is not bound to any specific Educational Grade. The canonical term is Educational Grade — never "Class." Archived Educational Grades are removed from active selection lists but remain available for historical reporting.

**Related Terms:** Group, Teacher Workspace, Teaching Subject, BR-016

**Related Documents:** `00_Project_Context.md` §19, `02_Software_Requirements.md` Part 2 §2, `07_Data_Dictionary.md` §9

---

## Enrollment

**Definition:** The time-bounded link between a Student and one Group under one Teacher Workspace.

**Context:** Enrollment represents the Student's current and historical placement in a Teacher's Group. A Student belongs to only one Group per Teacher at any time (BR-002). When a Student moves between Groups, the previous Enrollment is closed logically and a new Enrollment is opened logically. Historical records reference the Enrollment period and structure as of recording time. Enrollment duration is the sole input for Billable Student calculation (BR-008).

**Related Terms:** Student, Group, Teacher Workspace, Billable Student, Student Transfer, BR-002, BR-007, BR-008

**Related Documents:** `07_Data_Dictionary.md` §12, `06_Database_Design.md` §9

---

## Endpoint

**Definition:** A specific URL path in the REST API that accepts HTTP requests for a particular resource or action.

**Context:** All Version 1 endpoints use the `/api/v1` prefix. Endpoints are grouped by scope: Platform (`/api/v1/platform/`), Teacher Workspace (`/api/v1/teacher-workspace/`), Student (`/api/v1/student/`), and Parent (`/api/v1/parent/`). Every protected endpoint performs server-side authorization. Archive and restore use explicit action endpoints; hard deletion endpoints are not provided.

**Related Terms:** API, REST API, Route, URL Path

**Related Documents:** `10_API_Design.md` §2, §12

---

## Exam

**Definition:** An assessment created by a Teacher from the Teacher's private Question Bank, scoped to the Teacher Workspace.

**Context:** Exams are composed only from the owning Teacher's Question Bank (BR-011). Supported Question Types are Multiple Choice, True/False, Essay, and Bubble Sheet. Exam definitions, attempts, and grades are workspace-scoped (BR-012). Teachers never see other Teachers' Exam results. Exams can be archived but not permanently deleted. Exam modifications are recorded in the Audit Log.

**Related Terms:** Question Bank, Question Type, Exam Attempt, Exam Answer, Bubble Sheet, Teacher Workspace, BR-011, BR-012

**Related Documents:** `15_Exam_Engine.md`, `00_Project_Context.md` §7.1

---

## Exam Answer

**Definition:** A Student's response to a specific Question within an Exam Attempt.

**Context:** Exam Answers must match the supported Question Type: Multiple Choice, True/False, Essay, or Bubble Sheet. Bubble Sheet answers use electronic on-screen selection. Automatic grading is supported for applicable types. Essay answers may require Teacher review before a final result is available. Exam Answers are scoped to the Student, Exam, and Teacher Workspace.

**Related Terms:** Exam Attempt, Question, Question Type, Bubble Sheet, Automatic Grading

**Related Documents:** `15_Exam_Engine.md` §10, `07_Data_Dictionary.md` §24

---

## Exam Attempt

**Definition:** A Student's attempt at an Exam, recording the Student's participation and results.

**Context:** An Exam Attempt belongs to the correct Student, Exam, and owning Teacher Workspace. A Student may attempt only Exams assigned or made available through their Teacher relationships. Attempts and grades remain historically available when a Student moves between Groups (BR-007). The Student cannot access another Student's Exam Attempt. The number of attempts, retake eligibility, and resume behavior are not confirmed.

**Related Terms:** Exam, Exam Answer, Student, Teacher Workspace, Grade

**Related Documents:** `15_Exam_Engine.md` §18, `07_Data_Dictionary.md` §23

---

# F

---

## File Attachment

**Definition:** A logical reference to a file stored using Laravel Public Storage, connected to a business record.

**Context:** File Attachments support Teacher-owned Lesson videos, Homework files, and Student Homework submissions. Every file request must pass through backend authorization, Teacher Workspace scope, Student relationship, Parent linked-Student scope, Archive state, and resource ownership checks. Storage paths are not authorization proofs. S3 Storage is not required for Version 1.

**Related Terms:** Laravel Public Storage, Lesson, Homework, Teacher Workspace, Archive

**Related Documents:** `20_File_Storage.md`, `07_Data_Dictionary.md` §28

---

## Flow A

**Definition:** The money flow from Teacher to Platform, representing the monthly Subscription payment.

**Context:** Flow A is the SaaS revenue model. Teachers pay monthly Subscriptions calculated from Billable Students × Price Per Student. The Super Admin manages Flow A Subscriptions, pricing, and payment status at Platform level. Flow A must never be conflated with Flow B. In Version 1, Flow A payments are handled outside the Platform; the Platform records payment status only (BR-019).

**Related Terms:** Subscription, Billable Student, Billing Cycle, Price Per Student, Flow B, Super Admin

**Related Documents:** `00_Project_Context.md` §5.2, `17_Subscription_Billing.md`

---

## Flow B

**Definition:** The money flow from Student (or Parent) to Teacher, representing fees owed based on Group pricing.

**Context:** Flow B is derived from Group Price and Pricing Type (Monthly or Per Lesson) (BR-009). Flow B is tracked by the Platform on the Teacher's behalf. In Version 1, Flow B payments are handled outside the Platform; the Platform records payment status only (BR-019). Flow B must never be conflated with Flow A. Student and Parent views show Flow B as "payment status," never as "Subscription."

**Related Terms:** Pricing Type, Group Price, Payment Status, Flow A, BR-009

**Related Documents:** `00_Project_Context.md` §5.2, `02_Software_Requirements.md`

---

# G

---

## Global Search

**Definition:** A search capability that allows a user to search across multiple data domains within their confirmed scope.

**Context:** Global Search does not mean unrestricted Platform-wide search. It means the user can search across accessible modules from a single entry point, with results limited to their authorized boundary. A Teacher's search returns only Teacher Workspace records. A Student's search returns only their own per-Teacher-partitioned records. A Parent's search returns only linked Student records. Global Search must not reveal the existence of records in inaccessible scopes.

**Related Terms:** Module Search, Search, Filtering, Teacher Workspace, Scope

**Related Documents:** `22_Search_Filtering.md` §4

---

## Grade (Exam)

**Definition:** The result or score of an Exam Attempt, representing the Student's performance.

**Context:** Grades are workspace-scoped (BR-012). Automatic grading is supported for confirmed automatically gradable behavior, including Bubble Sheet. Essay Questions may require Teacher review before a final Grade is available; pending states are presented as unavailable/pending, never as fabricated results. Historical Grades are preserved through Student Group movement (BR-007). Parents may view available Grades for linked Students read-only.

**Related Terms:** Exam Attempt, Automatic Grading, Bubble Sheet, Essay, Teacher Workspace

**Related Documents:** `15_Exam_Engine.md` §11, §21

---

## Group

**Definition:** A cohort inside one Educational Grade, carrying Name, Schedule, Price, and Pricing Type.

**Context:** Groups organize Students within a Teacher Workspace and define the operational structure for scheduling, pricing, Attendance, Homework, Exams, and Flow B fee status. A Group belongs to one Educational Grade. A Student belongs to only one Group per Teacher at any time (BR-002). Pricing Type is either Monthly or Per Lesson (BR-009). Group archival preserves historical Enrollment and Student activity records.

**Related Terms:** Educational Grade, Student, Enrollment, Pricing Type, Schedule, Price, Teacher Workspace

**Related Documents:** `00_Project_Context.md` §7.1, `02_Software_Requirements.md` Part 2 §3, `07_Data_Dictionary.md` §10

---

# H

---

## Historical Data

**Definition:** Data that has been recorded in the past and must remain available for reporting and history queries, regardless of structural changes.

**Context:** Historical data is never deleted and must always remain available (BR-014). Reports and history queries include archived records, clearly indicated. Student transfers preserve historical Attendance, Homework, Exams, and grades (BR-007). Historical invoices keep the price as of their period. The Audit Log is permanently retained.

**Related Terms:** Archive, BR-014, BR-007, Report, Audit Log

**Related Documents:** `00_Project_Context.md` §9.3, `06_Database_Design.md` §16

---

## Homework

**Definition:** An assignment created by a Teacher within a Teacher Workspace, supporting Text, Image, and PDF formats only.

**Context:** Homework supports Text, Image, and PDF only (BR-021). Video homework is NOT supported in Version 1. Student submissions accept Image and PDF only. Homework is Teacher Workspace scoped. Homework modifications are recorded in the Audit Log. Homework history is preserved through Student Group movement. Parent access to Homework is read-only and limited to linked Students.

**Related Terms:** Homework Submission, Teacher Workspace, Student, Parent, BR-021

**Related Documents:** `02_Software_Requirements.md` Part 2 §6, `05_User_Flows.md` §12–§13, `07_Data_Dictionary.md` §16

---

## Homework Submission

**Definition:** A Student's response or status for assigned Homework.

**Context:** Homework Submissions accept Text, Image, and PDF formats only. Video submissions are rejected. A Student may submit only for Homework assigned to that Student through a valid Teacher relationship. Parent cannot submit or modify Homework. Homework Submission history is preserved through Student Group movement. Submissions are scoped to the correct Teacher Workspace and Student relationship.

**Related Terms:** Homework, Student, Teacher Workspace, Image, PDF, BR-021

**Related Documents:** `07_Data_Dictionary.md` §17, `05_User_Flows.md` §13

---

# I

---

## ID Card

**Definition:** A printed QR card carried by the Student and scanned by a QR scanner device for Attendance.

**Context:** ID Card is one of three confirmed Attendance methods (BR-010). The ID Card contains a QR code (not a barcode). A QR scanner device reads the ID Card during a valid Teacher Workspace Attendance Session. The Student does not receive a self-service ID Card scanning permission; ID Card scanning is a Teacher-side/Teacher Workspace operation. The backend resolves Student identity and validates the Teacher Workspace Attendance context before recording Attendance.

**Related Terms:** Attendance, Dynamic QR Code, QR Scanner, Attendance Session, BR-010

**Related Documents:** `16_QR_Attendance_System.md` §8, `00_Project_Context.md` §9.6

---

## Idempotent

**Definition:** A property of a background job or operation meaning it produces the same result whether executed once or multiple times.

**Context:** All background jobs must be idempotent by design. Re-processing must not create duplicate records, duplicate Billing Cycle entries, duplicate Audit Log entries, or inconsistent state. Re-running a Subscription calculation must overwrite with the correct current result, not add to it. Re-grading an Exam attempt must overwrite with the correct grading result.

**Related Terms:** Background Job, Database Queue, Retry

**Related Documents:** `21_Background_Jobs.md` §3

---

# L

---

## Laravel

**Definition:** The PHP framework used for the Platform's backend application.

**Context:** Laravel 12 on PHP 8.3 is the confirmed backend framework (D-001). It provides authentication (Sanctum), authorization (Gates & Policies), ORM (Eloquent), routing, validation, queue management (Database Queue), task scheduling (Laravel Scheduler), file storage (Laravel Public Storage), and caching (File Cache). The backend is a modular monolith, not a microservices system.

**Related Terms:** PHP 8.3, Laravel Sanctum, Laravel Gates, Eloquent, Database Queue, Laravel Scheduler

**Related Documents:** `11_Backend_Architecture.md`, `03_System_Architecture.md` §4.1

---

## Laravel Gates

**Definition:** Laravel's authorization mechanism for defining closure-based permission checks.

**Context:** Laravel Gates are used alongside Policies and Custom RBAC to enforce role boundaries, Teacher Workspace ownership, Teacher Staff assigned permissions, Student self-scope, Parent linked-Student read-only access, Super Admin Platform scope, Archive and restore permissions, and file access ownership. Gate definitions use the permission names from the Permission Matrix as the logical catalog.

**Related Terms:** Authorization, Policies, Custom RBAC, Permission Matrix

**Related Documents:** `11_Backend_Architecture.md` §11, `08_RBAC.md`

---

## Laravel Public Storage

**Definition:** Laravel's file storage mechanism used for storing files on the server's filesystem with a public-accessible symlink.

**Context:** Laravel Public Storage is the Version 1 storage baseline, selected for cPanel Shared Hosting compatibility. Application-level authorization and ownership checks control access to stored files so Teacher-owned content remains private. Storage paths, filenames, and directory structures are not authorization proofs. S3 Storage is not required for Version 1.

**Related Terms:** File Attachment, Storage, cPanel Shared Hosting, Lesson, Homework

**Related Documents:** `20_File_Storage.md`, `04_Project_Structure.md` §5

---

## Laravel Sanctum

**Definition:** Laravel's first-party authentication package for SPAs and API token authentication.

**Context:** Laravel Sanctum is the confirmed authentication technology for Version 1 (D-001). It provides session-based authentication for the Web Application and token-based authentication where applicable. Sanctum's SPA authentication includes CSRF protection through the X-XSRF-TOKEN header. Session cookies have HttpOnly, Secure, and SameSite flags.

**Related Terms:** Authentication, Session, CSRF, SPA

**Related Documents:** `03_System_Architecture.md` §9, `23_Security_Standards.md` §3

---

## Laravel Scheduler

**Definition:** Laravel's task scheduling mechanism that runs scheduled commands at defined intervals.

**Context:** The Laravel Scheduler is triggered by Cron Jobs on cPanel Shared Hosting. It coordinates scheduled tasks including Billing Cycle initialization, Billable Student calculation, Subscription snapshot generation, expired QR context cleanup, Exam auto-grading, report processing, and maintenance tasks. Only one Scheduler instance must run at a time.

**Related Terms:** Cron Job, Background Job, Scheduled Task, cPanel Shared Hosting

**Related Documents:** `21_Background_Jobs.md` §5, `26_Deployment_Plan.md` §16

---

## Lesson

**Definition:** A video uploaded by a Teacher for the Teacher's own Students, private to the Teacher Workspace.

**Context:** Lessons are Teacher-owned and private (BR-018). A Teacher may upload Lesson videos exclusively for their own Students. No cross-Teacher access exists. Lessons are not marketplace courses. The canonical term is Lesson — never "Course." Lesson video hosting and protection details remain PENDING (Q-010). Archived Lessons stop being active but historical references are retained.

**Related Terms:** Lesson Video, Teacher Workspace, Student, BR-018, Q-010

**Related Documents:** `00_Project_Context.md` §19, `02_Software_Requirements.md` Part 2, `07_Data_Dictionary.md` §18

---

## Lesson Video

**Definition:** The video file reference associated with a Lesson, stored using Laravel Public Storage.

**Context:** Lesson Videos are Teacher-owned and private. A Teacher may upload videos exclusively for their own Students. No cross-Teacher access exists. Lesson Videos belong to the owning Teacher Workspace. Version 1 uses Laravel Public Storage; S3 Storage is not required. Archived Lesson Videos remain retained historically.

**Related Terms:** Lesson, Teacher Workspace, Laravel Public Storage, BR-018

**Related Documents:** `07_Data_Dictionary.md` §19, `20_File_Storage.md` §6

---

## Login

**Definition:** The act of a user authenticating into the Platform through the Web Application.

**Context:** Login events are recorded in the Audit Log — both successful and failed attempts. Authentication is established through Laravel Sanctum. Login does not grant cross-Teacher access. "Login as Teacher" is not confirmed for Version 1 and is not implemented. Duplicate Student account prevention is enforced at login/registration.

**Related Terms:** Authentication, Laravel Sanctum, Account, Audit Log

**Related Documents:** `05_User_Flows.md` §2, §8, §19, §25

---

# M

---

## Marketplace

**Definition:** An online course discovery and selling platform — explicitly excluded from the Platform.

**Context:** The Platform is NOT an online course marketplace. Teachers do NOT sell courses on the Platform. There is no course discovery/browsing across Teachers, and no mechanism by which one Teacher's content reaches another Teacher's Students. No marketplace endpoints, database entities, UI components, or discovery behavior exists in Version 1.

**Related Terms:** Course Discovery, Cross-Teacher Browsing, Non-Goal

**Related Documents:** `00_Project_Context.md` §4.1, `01_Project_Vision.md` §4

---

## Module Search

**Definition:** Search within a specific module's data domain, the primary search mechanism for focused data retrieval.

**Context:** Module Search allows a user to search within a specific module (e.g., Students, Homework, Exams) with results limited to their authorized scope. A Teacher's module search returns only Teacher Workspace records. A Student's module search returns only their own per-Teacher records. Question Bank search is available only to the owning Teacher and authorized Teacher Staff.

**Related Terms:** Global Search, Search, Filtering, Teacher Workspace

**Related Documents:** `22_Search_Filtering.md` §5

---

## Multi-Tenant Architecture

**Definition:** The architectural pattern where each Teacher Workspace operates as a completely isolated tenant.

**Context:** Multi-Tenant Architecture is confirmed for the Platform (BR-003). Each Teacher Workspace is a tenant. Every workspace-owned row carries the Teacher's identity; queries are workspace-scoped; no cross-tenant foreign keys exist. Tenant isolation applies to Educational Grades, Groups, Students, Attendance, Homework, Lessons, Question Bank, Exams, Reports, Teacher Staff, Settings, and Flow B payment-status records.

**Related Terms:** Teacher Workspace, Tenant, Teacher Workspace Isolation, BR-003

**Related Documents:** `03_System_Architecture.md` §11, `06_Database_Design.md` §6

---

## MySQL 8

**Definition:** The relational database management system used as the official Version 1 database.

**Context:** MySQL 8 is the confirmed database engine (D-001). It persists all Platform data including user identities, Teacher Workspace records, Enrollment history, Attendance, Homework, Exams, Lessons, Subscriptions, payment status, Archive state, Audit Log entries, session data, and queue jobs. MySQL 8 supports full-text search where needed. The database design must be optimized for cPanel Shared Hosting.

**Related Terms:** Database, Persistence, Database Queue, Session Driver

**Related Documents:** `06_Database_Design.md` §2, `03_System_Architecture.md` §4.1

---

# N

---

## Notification

**Definition:** A message delivered to a user outside of their active Web Application session — explicitly excluded from Version 1.

**Context:** Push notifications, email notifications, and SMS notifications are out of scope for Version 1 (D-012). No Notification entity, API endpoints, permissions, settings, queue jobs, or scheduled tasks exist. SMTP is available as mail-transport availability only and does not create notification features. In-context UI feedback (validation, error, success messages) is not a Notification System.

**Related Terms:** Push Notification, Email Notification, SMS Notification, SMTP

**Related Documents:** `19_Notification_System.md`, `00_Project_Context.md` §4.2

---

# O

---

## Open Question (Q-xxx)

**Definition:** A question that remains unresolved in the Project Context, carrying PENDING status and awaiting Product Owner confirmation.

**Context:** Open Questions are referenced as Q-xxx. Six Open Questions remain unresolved for Version 1: Q-005 (non-payment enforcement), Q-010 (lesson video hosting/protection), Q-011 (Teacher Staff permission granularity), Q-012 (Super Admin content visibility), Q-013 (flat price vs. volume tiers), and Q-015 (timezone/currency). PENDING items must not be silently assumed.

**Related Terms:** PENDING, CONFIRMED, PROPOSED, Project Context

**Related Documents:** `00_Project_Context.md` §15

---

# P

---

## Parent

**Definition:** A guardian account that monitors linked Students with read-only access.

**Context:** A Parent has ONE account and may monitor multiple linked Students (BR-020). Version 1 supports exactly ONE Parent account per Student. Parent access is read-only everywhere (BR-004). The Parent sees only linked Students. The Parent Panel includes Dashboard, Student Switcher, Homework, Attendance, Exams, Teachers, and Payments. The Parent cannot modify Attendance, grades, Homework, Exams, payment status, Student records, Teacher records, or Teacher Workspace data.

**Related Terms:** Student, Student Switcher, Linked Student, Read-Only, BR-004, BR-020

**Related Documents:** `00_Project_Context.md` §6.5, `02_Software_Requirements.md` Part 4, `05_User_Flows.md` §19–§24

---

## Parent Student Link

**Definition:** The relationship between a Parent and a linked Student, determining which Students a Parent can monitor.

**Context:** One Parent can be linked to multiple Students. One Student can have only one Parent account in Version 1. Parent access through this link is read-only. The Student Switcher uses linked Students from this relationship.

**Related Terms:** Parent, Student, Student Switcher, BR-020

**Related Documents:** `07_Data_Dictionary.md` §8, `02_Software_Requirements.md` Part 4 §2

---

## Payment Status

**Definition:** The recorded state of a financial obligation — status only, not an in-platform transaction.

**Context:** Version 1 records payment status only; it does not process transactions (BR-019). Payment Status applies to both Flow A (Teacher Subscription) and Flow B (Student/Parent fees to Teacher). The Platform only records the status value; actual payments are handled outside the Platform. Online payment gateways are out of scope. The term "payment status" is used for Flow B; "Subscription" is used for Flow A.

**Related Terms:** Flow A, Flow B, Subscription, BR-019

**Related Documents:** `00_Project_Context.md` §5.3, `17_Subscription_Billing.md` §9

---

## Pending (PENDING)

**Definition:** A status indicating that a decision or question is unknown, ambiguous, or blocked on a Product Owner answer.

**Context:** PENDING items must not be silently assumed. No code may harden against a PENDING decision. Six items remain PENDING for Version 1: non-payment enforcement, lesson video hosting/protection, Teacher Staff permission granularity, Super Admin content visibility, flat price vs. volume tiers, and timezone/currency. Each PENDING item has a proposed default in the Project Context.

**Related Terms:** CONFIRMED, PROPOSED, Open Question, Project Context

**Related Documents:** `00_Project_Context.md` §15.1

---

## Permission

**Definition:** A capability that can be granted within the Platform, especially for Teacher Staff.

**Context:** Permissions support role-based and custom permission checks without violating Teacher Workspace isolation. Permissions may be assigned to Teacher Staff by the Teacher. Permission changes are recorded in the Audit Log. Teacher Staff hold only permissions assigned by the Teacher (BR-013). Permission granularity remains PENDING (Q-011).

**Related Terms:** RBAC, Role, Teacher Staff, Permission Matrix, BR-013, Q-011

**Related Documents:** `09_Permission_Matrix.md`, `08_RBAC.md` §4, `07_Data_Dictionary.md` §3

---

## Platform

**Definition:** The Unified Education Platform — the SaaS educational platform itself.

**Context:** The Platform is the product. It serves five confirmed roles: Super Admin, Teacher, Teacher Staff, Student, and Parent. The Platform is a Web Application only for Version 1. It is not an online course marketplace. The Platform uses Multi-Tenant Architecture with each Teacher Workspace as an isolated tenant.

**Related Terms:** Unified Education Platform, SaaS, Web Application, Version 1

**Related Documents:** `00_Project_Context.md` §2, `01_Project_Vision.md`

---

## Price Per Student

**Definition:** The amount charged per Billable Student for calculating a Teacher's monthly Subscription under Flow A.

**Context:** Price Per Student is configured by the Super Admin at Platform level (BR-015). The formula is: Monthly Subscription = Billable Students × Price Per Student. Historical invoices keep the price as of their period. Flat price versus volume tiers remains PENDING (Q-013).

**Related Terms:** Billable Student, Subscription, Super Admin, Flow A, BR-015

**Related Documents:** `00_Project_Context.md` §5.1, `17_Subscription_Billing.md` §6

---

## Pricing Type

**Definition:** The fee basis of a Group, determining how Student fees are calculated under Flow B.

**Context:** Pricing Type has two confirmed values: Monthly or Per Lesson (BR-009). Every Group carries a Price and Pricing Type. Student fee obligations derive from Group Enrollment, Price, and Pricing Type. Pricing Type is part of Flow B, not Flow A.

**Related Terms:** Group, Price, Flow B, BR-009

**Related Documents:** `00_Project_Context.md` §19, `02_Software_Requirements.md` Part 2 §3

---

## PROPOSED

**Definition:** A status indicating an architect's recommendation — a working default awaiting Product Owner approval.

**Context:** PROPOSED items represent the architect's recommended approach. They are working defaults intended to keep momentum while awaiting formal confirmation. They must not be treated as CONFIRMED unless formally approved. Examples include Subscription invoicing as immutable monthly snapshots (D-003) and non-payment enforcement ladder (D-004).

**Related Terms:** CONFIRMED, PENDING, Decision, Project Context

**Related Documents:** `00_Project_Context.md` §3

---

# Q

---

## QR Scanner

**Definition:** A hardware device used to read QR codes from printed ID Cards for Attendance recording.

**Context:** QR Scanner devices read the printed QR code on a Student's ID Card during a valid Teacher Workspace Attendance Session. QR Scanner operation is a Teacher-side/Teacher Workspace activity; the Student does not receive a self-service ID Card scanning permission. Barcode scanning is not a confirmed Version 1 Attendance method.

**Related Terms:** ID Card, Attendance, Attendance Session, QR Code

**Related Documents:** `16_QR_Attendance_System.md` §8

---

## Question Bank

**Definition:** A Teacher-owned private repository of questions used to build Exams.

**Context:** The Question Bank is private and Teacher-owned (BR-011). Each Teacher Workspace has its own Question Bank boundary. An Exam may contain only Questions from the owning Teacher Workspace's Question Bank. Teachers cannot see another Teacher's Question Bank. Questions may be archived and restored; permanent deletion is not allowed. Supported Question Types are Multiple Choice, True/False, Essay, and Bubble Sheet.

**Related Terms:** Question, Question Type, Exam, Teacher Workspace, BR-011

**Related Documents:** `15_Exam_Engine.md` §4, `00_Project_Context.md` §9.6

---

## Question Type

**Definition:** The classification of a Question in the Question Bank, determining how Students answer and how grading is handled.

**Context:** Version 1 confirms four Question Types: Multiple Choice, True/False, Essay, and Bubble Sheet (BR-011). Multiple Choice, True/False, and Bubble Sheet support automatic grading. Essay requires authorized Teacher/Teacher Staff grading. Unsupported question types are rejected. No additional Question Types are confirmed for Version 1.

**Related Terms:** Question Bank, Multiple Choice, True/False, Essay, Bubble Sheet, BR-011

**Related Documents:** `15_Exam_Engine.md` §6

---

# R

---

## RBAC

**Definition:** Role-Based Access Control — the authorization model governing who can access Platform data and functions based on roles, scopes, ownership, and permissions.

**Context:** Version 1 has five confirmed roles: Super Admin, Teacher, Teacher Staff, Student, and Parent. RBAC is implemented through Laravel Gates & Policies with Custom RBAC. Access is determined by role, scope (Platform, Teacher Workspace, Student Account, Parent Linked Students), ownership, permission, and Archive state. Deny by default is the security principle. No role receives hard-delete permission.

**Related Terms:** Role, Permission, Authorization, Teacher Workspace, Scope

**Related Documents:** `08_RBAC.md`, `09_Permission_Matrix.md`

---

## React

**Definition:** The JavaScript library used for the Platform's frontend Web Application.

**Context:** React 19 with TypeScript, Vite, and Tailwind CSS is the confirmed frontend stack (D-001). The frontend is a single-page Web Application that communicates exclusively with the Laravel backend through the documented REST API. React Router provides client-side routing. TanStack Query manages server state. React Hook Form with Zod handles forms and validation. The frontend is not a security boundary.

**Related Terms:** TypeScript, Vite, Tailwind CSS, React Router, TanStack Query, SPA

**Related Documents:** `12_Frontend_Architecture.md`, `03_System_Architecture.md` §4.1

---

## Report

**Definition:** Authorized operational and historical information presented to a user within their confirmed scope.

**Context:** Reports include Attendance, Homework, Exam results, payment status, and Student performance for Teachers. Students view their own per-Teacher records. Parents view linked Student records read-only. Super Admin views Platform-level reports within confirmed visibility boundaries. Reports include historical data with archived records clearly indicated. Flow A and Flow B remain separate in all reports. Reports do not process payments.

**Related Terms:** Teacher Workspace, Historical Data, Archive, Flow A, Flow B, Dashboard

**Related Documents:** `18_Reporting_Analytics.md`, `02_Software_Requirements.md` Parts 2–5

---

## Restore

**Definition:** The action of returning an archived record to active status.

**Context:** Restore is the counterpart of Archive. Authorized users may restore archived records where restoration is allowed. Restore actions are recorded in the Audit Log. Restored records return to active status and appear in normal searches and selection lists again. The restoring actor and action are recorded in the Audit Log (§10.1, event 4).

**Related Terms:** Archive, Audit Log, BR-005

**Related Documents:** `00_Project_Context.md` §11

---

## Role

**Definition:** One of the five confirmed user types that determine a user's access context on the Platform.

**Context:** Version 1 has exactly five roles: Super Admin, Teacher, Teacher Staff, Student, and Parent. Each Role has a defined scope: Super Admin is Platform-scoped; Teacher is own Teacher Workspace-scoped; Teacher Staff is creating Teacher Workspace-scoped with Teacher-assigned permissions; Student is own account and per-Teacher records-scoped; Parent is linked Students-scoped and read-only.

**Related Terms:** Super Admin, Teacher, Teacher Staff, Student, Parent, RBAC, Permission

**Related Documents:** `00_Project_Context.md` §6, `08_RBAC.md` §3

---

# S

---

## SaaS

**Definition:** Software as a Service — the commercial model where the Platform is provided as a subscription-based service.

**Context:** The Platform is a SaaS educational platform. Teachers are the paying customers who subscribe monthly. The Subscription price depends on Billable Students × Price Per Student. The SaaS revenue is Flow A. Flow B (Student/Parent fees to Teachers) is separate from SaaS revenue.

**Related Terms:** Platform, Subscription, Flow A, Teacher

**Related Documents:** `00_Project_Context.md` §2, `01_Project_Vision.md`

---

## Schedule

**Definition:** The time and day information associated with a Group.

**Context:** Each Group carries a Schedule. Students view their schedule through My Schedule, derived from their Group under each Teacher. The Student cannot edit Group Schedule. Schedule information remains associated with the correct Teacher, Educational Grade, and Group context. Detailed recurring schedule structure is deferred to later requirements.

**Related Terms:** Group, Student, My Schedule, Teacher Workspace

**Related Documents:** `02_Software_Requirements.md` Part 3 §2, `07_Data_Dictionary.md` §11

---

## Search

**Definition:** The capability to locate records by text query within the authenticated user's authorized scope.

**Context:** Search results respect RBAC permissions and Teacher Workspace isolation. A Teacher's search returns only Teacher Workspace records. A Student's search returns only their own records, partitioned per Teacher. A Parent's search returns only linked Student records. Search must not reveal the existence of records in inaccessible scopes. All search results are paginated.

**Related Terms:** Global Search, Module Search, Filtering, Sorting, Pagination

**Related Documents:** `22_Search_Filtering.md`

---

## Session Driver

**Definition:** The mechanism used to store user session data on the server.

**Context:** Version 1 uses the Database Session Driver. Session data is stored in MySQL 8. Sessions must expire after inactivity and have absolute timeouts. Session cookies have HttpOnly, Secure, and SameSite flags. Sessions are invalidated on logout and password change. Redis is not required for Version 1 session management.

**Related Terms:** Authentication, Session, MySQL 8, Database, Laravel Sanctum

**Related Documents:** `23_Security_Standards.md` §7, `26_Deployment_Plan.md` §8.2

---

## Soft Delete

**Definition:** A data persistence pattern where records are marked as deleted without being physically removed from the database.

**Context:** In this project, the canonical term is Archive — never "Delete" or "Soft Delete" in product-facing contexts. Soft Delete describes the technical persistence strategy behind Archive. Archived records are retained, excluded from active searches and selection lists, and remain available for historical reporting and restoration.

**Related Terms:** Archive, Restore, Historical Data, BR-005

**Related Documents:** `06_Database_Design.md` §7, `00_Project_Context.md` §11

---

## Student

**Definition:** A learner with one global account who may study with multiple Teachers.

**Context:** A Student has exactly one global Account (BR-001) and may study with multiple Teachers. A Student belongs to only one Group per Teacher at any time (BR-002). Student data (Attendance, Homework, Exams, Lessons, Subscription-related status) is partitioned per Teacher. Student Registration supports two methods: self-registration and Teacher-created accounts. Duplicate Student accounts are not allowed (BR-022). Teacher-created accounts can later be activated by the Student.

**Related Terms:** Account, Enrollment, Group, Teacher, Parent, BR-001, BR-002, BR-022

**Related Documents:** `00_Project_Context.md` §6.4, `02_Software_Requirements.md` Part 3, `05_User_Flows.md` §7–§8

---

## Student Fee Status

**Definition:** The recorded state of fees owed by a Student or Parent to a Teacher, derived from Group Price and Pricing Type under Flow B.

**Context:** Student Fee Status represents Flow B obligations. It is derived from Group enrollment, Price, and Pricing Type. Version 1 records payment status only. Student Fee Status must not be confused with Flow A Subscription. The Platform does not process Student fee transactions.

**Related Terms:** Flow B, Pricing Type, Group, Payment Status, BR-009

**Related Documents:** `07_Data_Dictionary.md` §33, `02_Software_Requirements.md` Part 3 §6

---

## Student Switcher

**Definition:** The Parent Panel control for switching between linked Students.

**Context:** The Student Switcher allows a Parent to select which linked Student's information is currently being viewed. Version 1 supports exactly one Parent account per Student, but one Parent account may be linked to multiple Students. The Student Switcher must never display unlinked Students. Switching Students changes the Parent's current monitoring context only; it does not modify Student records.

**Related Terms:** Parent, Student, Parent Student Link, Linked Student, BR-020

**Related Documents:** `00_Project_Context.md` §19, `02_Software_Requirements.md` Part 4 §2

---

## Student Transfer

**Definition:** The movement of a Student between Groups under the same Teacher.

**Context:** When a Student moves between Groups, historical Attendance, Homework, Exams, and grades are preserved (BR-007). History is never moved, deleted, or rewritten by structural changes. The previous Enrollment is closed logically and a new Enrollment is opened logically. Historical records reference the Enrollment period and structure as of recording time.

**Related Terms:** Enrollment, Group, Teacher Workspace, BR-007, Historical Data

**Related Documents:** `00_Project_Context.md` §9.3, `05_User_Flows.md` §9

---

## Subscription

**Definition:** The Teacher's monthly Platform Subscription under Flow A.

**Context:** Subscription refers exclusively to Flow A — the Teacher-to-Platform monthly payment. The canonical term is Subscription for Flow A only. Student and Parent per-Teacher fee status is described as "payment status" (Flow B), never as "Subscription." Monthly Subscription = Billable Students × Price Per Student. The Super Admin manages Subscription status and pricing at Platform level.

**Related Terms:** Flow A, Billable Student, Billing Cycle, Price Per Student, Super Admin

**Related Documents:** `00_Project_Context.md` §19, `17_Subscription_Billing.md`

---

## Super Admin

**Definition:** The platform owner role that manages Platform-level administration.

**Context:** The Super Admin owns the Platform at Platform level only — does not operate inside Teacher Workspaces. The Super Admin manages Teachers, Subscriptions (Flow A), pricing, Platform Settings, and global reports. Content-visibility boundary is PENDING (Q-012). The Super Admin is not a substitute for a Teacher, Teacher Staff, Student, or Parent context. "Login as Teacher" is not confirmed for Version 1.

**Related Terms:** Platform, Subscription, Flow A, Pricing, Platform Settings, Q-012

**Related Documents:** `00_Project_Context.md` §6.1, `02_Software_Requirements.md` Part 5

---

# T

---

## Teacher

**Definition:** The primary paying customer of the Platform who operates one isolated Teacher Workspace.

**Context:** Teachers subscribe monthly to use the Platform. Each Teacher operates one completely isolated Teacher Workspace and cannot access another Teacher's data under any circumstance (BR-003). The Teacher manages Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Users, and Settings within the workspace. Each Teacher account represents exactly one Teaching Subject, selected during registration and immutable after account creation (BR-016).

**Related Terms:** Teacher Workspace, Teaching Subject, Subscription, Educational Grade, Group, BR-003, BR-016

**Related Documents:** `00_Project_Context.md` §6.2, `02_Software_Requirements.md` Part 2

---

## Teacher Staff

**Definition:** Internal users created by a Teacher inside a Teacher Workspace, holding only Teacher-assigned permissions.

**Context:** Teacher Staff are created by the Teacher and exist only inside that Teacher Workspace (BR-013). Examples include Secretary, Assistant, and Accountant. Teacher Staff hold only permissions assigned by the Teacher. Permission-model granularity is PENDING (Q-011). Teacher Staff actions are attributed to the Teacher Staff user in the Audit Log, never to the Teacher. The canonical term is Teacher Staff — never "sub-teacher."

**Related Terms:** Teacher, Teacher Workspace, Permission, BR-013, Q-011

**Related Documents:** `00_Project_Context.md` §6.3, `08_RBAC.md`, `07_Data_Dictionary.md` §30

---

## Teacher Workspace

**Definition:** One Teacher's completely isolated area of the Platform, serving as the unit of data isolation.

**Context:** Teacher Workspace is the tenant boundary in the Multi-Tenant Architecture. Every Teacher Workspace-owned record carries the Teacher's identity. No Teacher can see another Teacher's data under any circumstance (BR-003). The canonical term is Teacher Workspace — "tenant" is used only in architecture discussions, never in product or UI language. Teacher Workspace includes Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Teacher Staff, and Settings.

**Related Terms:** Tenant, Multi-Tenant Architecture, Teacher, Teacher Staff, BR-003

**Related Documents:** `00_Project_Context.md` §19, `03_System_Architecture.md` §11, `07_Data_Dictionary.md` §5

---

## Teaching Subject

**Definition:** The single subject a Teacher teaches (e.g., Mathematics, Physics, Chemistry, Biology, Arabic, English).

**Context:** Each Teacher account represents exactly one Teaching Subject (BR-016). The Teaching Subject is selected during Teacher registration and cannot be changed after account creation. Version 1 does NOT support multiple Teaching Subjects per Teacher account. If a Teacher wants to teach another subject, a separate Teacher account must be created. Teaching Subjects are independent from Educational Grades. The canonical term is Teaching Subject — never "Course."

**Related Terms:** Teacher, Educational Grade, BR-016

**Related Documents:** `00_Project_Context.md` §8, `07_Data_Dictionary.md` §31

---

## Tenant

**Definition:** An architectural term for an isolated data boundary — equivalent to Teacher Workspace in this project.

**Context:** In the Multi-Tenant Architecture, each Teacher Workspace is a tenant. The term "tenant" is used only in architecture discussions; product and UI language uses "Teacher Workspace." Tenant isolation means every workspace-owned record is scoped so no Teacher can see another Teacher's data.

**Related Terms:** Teacher Workspace, Multi-Tenant Architecture, Tenant Isolation, BR-003

**Related Documents:** `03_System_Architecture.md` §11, `06_Database_Design.md` §6

---

## Tenant Isolation

**Definition:** The architectural enforcement ensuring that each Teacher Workspace's data is completely separated from every other Teacher Workspace.

**Context:** Tenant Isolation is mandatory (BR-003). It is enforced at every layer: database queries, API responses, file access, search results, reports, cache entries, and error messages. No cross-tenant foreign keys exist except through approved global identity relationships (e.g., Student identity). Every query to workspace-owned data must include the Teacher Workspace scope. Tenant Isolation is a business rule and a data-design rule.

**Related Terms:** Teacher Workspace, Multi-Tenant Architecture, BR-003, Scope

**Related Documents:** `03_System_Architecture.md` §11, `23_Security_Standards.md` §5

---

## TypeScript

**Definition:** The statically-typed superset of JavaScript used for the Platform's frontend code.

**Context:** TypeScript is used with React 19 for the frontend Web Application. TypeScript provides compile-time type safety. TypeScript strict mode must be enabled. Shared TypeScript contracts are located in `src/types/`. The `any` type must not be used; `unknown` is used where the type is genuinely unknown.

**Related Terms:** React, Frontend, Type Safety

**Related Documents:** `12_Frontend_Architecture.md`, `04_Project_Structure.md` §3

---

# V

---

## Version 1

**Definition:** The initial release of the Unified Education Platform with the confirmed scope defined in the frozen Project Context.

**Context:** Version 1 is a Web Application only. It does not include native mobile applications, online payment gateways, notifications, multiple Teaching Subjects per Teacher, marketplace behavior, video homework, or multiple Parent accounts per Student. Version 1 uses Laravel 12, React 19, MySQL 8, Laravel Sanctum, and cPanel Shared Hosting. The Project Context is frozen for Version 1.

**Related Terms:** Platform, Web Application, Project Context, Frozen

**Related Documents:** `00_Project_Context.md`, `27_Development_Roadmap.md`

---

## Vite

**Definition:** The frontend build tool and development server used for the React 19 Web Application.

**Context:** Vite is the official Version 1 frontend build tool (D-015). It provides fast development server startup, optimized production builds, native TypeScript support, and built-in code splitting. Vite produces fingerprinted static assets for deployment alongside the Laravel backend on cPanel Shared Hosting. Vite environment variables are limited to browser-safe values with the `VITE_` prefix.

**Related Terms:** React, Build Tool, Frontend, TypeScript

**Related Documents:** `12_Frontend_Architecture.md`, `26_Deployment_Plan.md` §9.3

---

# W

---

## Web Application

**Definition:** A browser-based application accessed through a web browser, as opposed to a native mobile application.

**Context:** Version 1 is delivered as a Web Application only (BR-017). All Version 1 capabilities, including daily Dynamic QR Code attendance scanning, are delivered through the Web Application. No native mobile application exists in Version 1. The Web Application uses React 19 with TypeScript, Vite, and Tailwind CSS for the frontend, and communicates with the Laravel 12 backend through the REST API.

**Related Terms:** Platform, Version 1, React, Browser, BR-017

**Related Documents:** `00_Project_Context.md` §2, `02_Software_Requirements.md`

---

## Workspace

**See:** Teacher Workspace

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|-------------|--------|
| Project Context alignment | Passed — all terms and definitions are derived from `00_Project_Context.md` and the canonical document set. No new business terms are invented. |
| Canonical terminology | Passed — all canonical terms from `00_Project_Context.md` §19 are included: Platform, Teacher Workspace, Educational Grade, Teaching Subject, Group, Pricing Type, Student, Parent, Teacher Staff, Super Admin, Subscription, Flow A, Flow B, Enrollment, Archive, Audit Log, Dynamic QR Code, ID Card, Question Bank, Bubble Sheet, Student Switcher, Lesson, Billable Student, Billing Cycle, Homework. |
| Non-canonical avoidance | Passed — "Class" is never used for Educational Grade; "Course" is never used for Lesson; "Delete" is never used for Archive; "Tenant" is used only with architecture context qualifier; "sub-teacher" is never used for Teacher Staff. |
| Alphabetical ordering | Passed — terms are sorted alphabetically within letter groups. |
| Definition accuracy | Passed — every definition matches the usage in the canonical documents. |
| Context accuracy | Passed — every context section reflects the actual confirmed rules and boundaries. |
| Related terms accuracy | Passed — every related term list includes genuinely related terms found in the canonical documents. |
| Related documents accuracy | Passed — every related document list references actual documents from the AI_DOCS set. |
| BR reference accuracy | Passed — all Business Rule references (BR-001 through BR-022) are accurate and consistent with the Project Context. |
| D reference accuracy | Passed — all Decision references (D-001 through D-051) are consistent with `29_Project_Decisions.md`. |
| Q reference accuracy | Passed — all Open Question references (Q-005, Q-010, Q-011, Q-012, Q-013, Q-015) are accurate. |
| Role coverage | Passed — all five roles are defined: Super Admin, Teacher, Teacher Staff, Student, Parent. |
| Financial term separation | Passed — Flow A (Subscription) and Flow B (payment status/Student Fee Status) are clearly separated and never conflated. |
| Version 1 scope | Passed — no terms imply native mobile, payment gateways, notifications, marketplace behavior, video homework, or multiple subjects per Teacher. |
| PENDING items | Passed — PENDING items are documented as PENDING with their proposed defaults clearly labeled. No PENDING item is silently hardened. |
| No source code | Passed — no source code, API definitions, database tables, SQL, or UI implementation is included. |
| No invented terms | Passed — every term in this glossary is used in at least one canonical document. No new terms are created. |
| Term completeness | Passed — all requested terms from the requirements are included, plus all additional important terms found across the canonical documents. |
| CONFIRMED/PROPOSED/PENDING status | Passed — the status convention is defined and used accurately throughout. |
| Technical stack terms | Passed — Laravel, React, TypeScript, Vite, MySQL 8, Laravel Sanctum, Laravel Gates, Database Queue, File Cache, Laravel Public Storage, Laravel Scheduler, Cron Job, cPanel Shared Hosting, API, REST API, SPA are all defined. |
| Data entity terms | Passed — all logical entities from `07_Data_Dictionary.md` are represented: User, Role, Permission, Teacher, Teacher Workspace, Student, Parent, Parent Student Link, Educational Grade, Group, Group Schedule, Student Enrollment, Attendance Session, QR Session, Attendance, Homework, Homework Submission, Lesson, Lesson Video, Question Bank, Question, Exam, Exam Attempt, Exam Answer, Payment, Subscription, Audit Log, File Attachment, Platform Settings, Teacher Staff, Teaching Subject, Billing Cycle, Student Fee Status. |

---

*End of document. **REVISION 1.0** — This file is the official glossary for the Unified Education Platform Version 1. Docs before code; consistency over convenience; Archive — never delete.*


## Confirmed Audit Clarifications

The following confirmed clarifications are owned by `00_Project_Context.md` §9.9 (BR-023 through BR-025). They do not alter Flow A, Archive, Audit Log, or the external-payment boundary.

- **Parent workflow:** a Parent creates an account through Parent registration and submits a Parent–Student link request. The Teacher responsible for the Student’s active Enrollment approves or rejects it. Approval is allowed only when the Student has no other linked Parent; it creates read-only access. The Parent may request unlinking, and that Teacher approves the unlink. Request, approval, rejection, and unlinking are Audit Log events; historical records remain preserved.
- **Per Lesson Flow B:** a billable lesson is a Lesson completed for a Group while the Student has an active Enrollment in that Group. The obligation is recorded on completion at the Group’s recorded Price. Drafting, scheduling, publishing, viewing, Attendance, or assignment alone is not billable; one Student has at most one obligation for the same completed lesson. The Platform records payment status only, and payment remains outside the Platform.
- **Group transfer:** Flow A enrollment duration is accumulated across Groups of the same Teacher in the same Billing Cycle; a transfer does not reset the more-than-15-calendar-days test. Flow B obligations remain tied to the Group and completed Lesson that created them.
