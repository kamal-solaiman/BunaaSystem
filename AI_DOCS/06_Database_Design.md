# 1. Database Design Goals

The database design for the Unified Education Platform must support the confirmed Version 1 product scope while preserving all business rules from the Project Context, Project Vision, Software Requirements, and System Architecture.

The primary database design goals are:

1. **Preserve Teacher Workspace isolation**  
   Every Teacher Workspace is a completely isolated tenant. Teacher-owned records must be scoped so that no Teacher can see another Teacher's data under any circumstance.

2. **Support one global Student account**  
   A Student has exactly one global account and may study with multiple Teachers. The database design must allow one Student identity while keeping Attendance, Homework, Exams, Lessons, and Subscription-related status separated per Teacher.

3. **Support one Parent account linked to multiple Students**  
   A Parent has one account and may monitor multiple linked Students. Version 1 supports exactly one Parent account per Student.

4. **Preserve historical data**  
   Historical data is never deleted. Student transfers preserve historical Attendance, Homework, Exams, and grades.

5. **Support Archive instead of permanent deletion**  
   No hard deletion is allowed. Archive is used everywhere. Archived records are excluded from active selection lists and normal active searches but remain available for historical reporting.

6. **Support Audit Log immutability**  
   Important actions must be recorded in the Audit Log. Audit Log entries are append-only, immutable, and permanently retained.

7. **Separate Flow A and Flow B**  
   Flow A is the Teacher's Platform Subscription. Flow B is Student or Parent fees owed to a Teacher. These flows must remain logically separate.

8. **Support cPanel Shared Hosting**  
   The logical design must be compatible with MySQL 8, Database session driver, Database Queue, Laravel Public Storage references, and cPanel Shared Hosting constraints.

This document defines logical database design only. It does not define SQL, database tables, migrations, Laravel models, physical schema, or columns.

---

# 2. Database Engine

The official Version 1 database engine is **MySQL 8**.

MySQL 8 is used to persist:

- Platform-level administration data.
- Global user identities.
- Role relationships.
- Teacher Workspace data.
- Student relationships with Teachers.
- Parent links to Students.
- Educational Grades and Groups.
- Enrollment history.
- Attendance records.
- Homework records.
- Lesson metadata and storage references.
- Question Bank and Exam records.
- Exam attempts and grades.
- Flow A Subscription and payment-status records.
- Flow B Student fee status records.
- Archive state.
- Audit Log entries.
- Database session data.
- Database Queue data.

The database design must remain optimized for cPanel Shared Hosting. Version 1 must not require Redis, S3 Storage, Microservices, WebSockets, Kubernetes, or Docker.

The database is the system of record for business data, historical data, Archive state, Audit Log entries, sessions, and queued work. File binaries are not logically treated as primary business data; files are stored using Laravel Public Storage, while database records maintain logical references to those files.

---

# 3. Naming Conventions

Naming conventions must preserve the canonical terminology defined by the Project Context.

## Canonical Terminology Requirements

The logical database design must use these terms consistently:

- Platform
- Teacher Workspace
- Educational Grade
- Teaching Subject
- Group
- Pricing Type
- Student
- Parent
- Teacher Staff
- Super Admin
- Subscription
- Flow A
- Flow B
- Enrollment
- Archive
- Audit Log
- Dynamic QR Code
- ID Card
- Question Bank
- Bubble Sheet
- Student Switcher
- Lesson
- Billable Student
- Billing Cycle
- Homework

## Naming Rules

1. Logical entity names should use business terminology rather than implementation shortcuts.
2. Teacher Workspace must be used for product and business meaning; tenant may be used only when describing architecture or isolation strategy.
3. Educational Grade must be used instead of non-canonical alternatives.
4. Teaching Subject must not be called Course, because Course implies marketplace behavior.
5. Archive must be used instead of Delete in logical design language.
6. Subscription must refer to Flow A unless explicitly qualified otherwise.
7. Flow B payment status must not be called Subscription.
8. Lesson must refer to Teacher-owned private video content and must not be treated as marketplace content.

This document avoids physical naming rules for tables and columns because physical schema is intentionally out of scope.

---

# 4. Entity Overview

This section describes logical entities only. It does not define database tables, fields, columns, migrations, or models.

## Identity and Access Entities

- **User Identity**  
  Represents a global authenticated identity for users of the Platform.

- **Role Context**  
  Represents the user's role-based context, such as Super Admin, Teacher, Teacher Staff, Student, or Parent.

- **Teacher Staff Permission Assignment**  
  Represents permissions assigned by a Teacher to Teacher Staff within a Teacher Workspace. Permission granularity remains PENDING and must be finalized in RBAC documentation.

## Teacher Workspace Entities

- **Teacher Workspace**  
  Represents one Teacher's isolated workspace and tenant boundary.

- **Teaching Subject**  
  Represents the single subject associated with a Teacher account. It is selected during Teacher registration and cannot be changed after account creation.

- **Educational Grade**  
  Represents a Teacher-created educational level inside a Teacher Workspace.

- **Group**  
  Represents a cohort inside an Educational Grade. A Group carries Schedule, Price, and Pricing Type conceptually, without defining fields in this document.

## Student and Parent Relationship Entities

- **Student Profile**  
  Represents the Student's global account and identity as a learner.

- **Parent Profile**  
  Represents the Parent's global account used to monitor linked Students.

- **Parent Student Link**  
  Represents the relationship between a Parent and linked Students. Version 1 allows one Parent account per Student and one Parent account linked to multiple Students.

- **Teacher Student Relationship**  
  Represents the Student's relationship with a specific Teacher Workspace.

- **Enrollment**  
  Represents the time-bounded relationship between a Student and a Group under a Teacher Workspace.

## Academic Operation Entities

- **Attendance Record**  
  Represents Student Attendance in a Teacher Workspace using Dynamic QR Code, ID Card, or manual entry.

- **Homework**  
  Represents Teacher-assigned Homework. Version 1 supports Text, Image, and PDF only.

- **Homework Submission / Status**  
  Represents Student interaction and status related to Homework where applicable.

- **Lesson**  
  Represents a Teacher-owned private video Lesson for the Teacher's own Students.

- **Question Bank**  
  Represents the Teacher-owned private collection of questions.

- **Question**  
  Represents a question owned by a Teacher Workspace. Supported types are Multiple Choice, True/False, Essay, and Bubble Sheet.

- **Exam**  
  Represents an assessment created from the owning Teacher's Question Bank.

- **Exam Attempt**  
  Represents a Student's attempt at an Exam within a Teacher Workspace.

- **Exam Grade**  
  Represents the result of an Exam attempt, scoped to the Student and Teacher Workspace.

## Money and Reporting Entities

- **Flow A Subscription**  
  Represents the Teacher's monthly Platform Subscription.

- **Flow A Invoice / Billing Snapshot**  
  Represents a billing-period view of Subscription status and Billable Student count. Immutable monthly snapshot mechanics are PROPOSED in the Project Context and must not contradict confirmed billing rules.

- **Flow B Student Fee Status**  
  Represents Student or Parent fee status owed to the Teacher, derived from Group Price and Pricing Type.

- **Report Source Record**  
  Represents logical source data used in reports, not a separate physical reporting table definition.

## Governance Entities

- **Archive State**  
  Represents whether a record is active or archived at the logical level.

- **Audit Log Entry**  
  Represents an append-only record of important actions.

- **File Reference**  
  Represents a logical reference to a file stored in Laravel Public Storage.

---

# 5. Entity Relationships

This section defines logical relationships only.

## Identity Relationships

- A User Identity may have one or more role contexts, depending on confirmed account relationships.
- A Teacher role is associated with one Teacher Workspace.
- Teacher Staff are associated with the Teacher Workspace that created them.
- A Student has one global Student Profile.
- A Parent has one Parent Profile.

## Teacher Workspace Relationships

- One Teacher Workspace belongs to one Teacher account.
- One Teacher Workspace has exactly one Teaching Subject.
- One Teaching Subject belongs to one Teacher Workspace.
- One Teacher Workspace contains many Educational Grades.
- One Educational Grade contains many Groups.
- One Group belongs to one Educational Grade.

## Student Relationships

- One Student may be associated with multiple Teacher Workspaces.
- A Student belongs to only one Group per Teacher at any time.
- A Student may have multiple historical Enrollments under the same Teacher due to Group movement.
- Enrollment history preserves historical Attendance, Homework, Exams, and grades.

## Parent Relationships

- One Parent account may be linked to multiple Students.
- One Student can have only one Parent account linked in Version 1.
- Parent access is read-only and limited to linked Students.

## Academic Relationships

- Attendance belongs to a Student relationship within a Teacher Workspace.
- Homework belongs to a Teacher Workspace and may be associated with Students or Groups according to detailed requirements.
- Lessons belong to a Teacher Workspace and are available only to the Teacher's own Students.
- Question Bank belongs to one Teacher Workspace.
- Questions belong to the owning Teacher Workspace through the Question Bank.
- Exams are composed only from questions owned by the same Teacher Workspace.
- Exam Attempts belong to a Student and Teacher Workspace context.
- Exam Grades belong to Exam Attempts and remain historically available.

## Financial Relationships

- Flow A Subscription belongs to a Teacher account at Platform level.
- Flow A billing uses Billable Students based on Enrollment duration only.
- Flow B Student Fee Status belongs to the Student relationship with a Teacher Workspace.
- Flow B is derived from Group Price and Pricing Type.

## Governance Relationships

- Audit Log Entries reference the actor, role context, action context, and affected logical record.
- Archived records retain all historical relationships.
- File References belong to the owning Teacher Workspace or relevant Teacher-owned content context.

---

# 6. Tenant Isolation Strategy

The Teacher Workspace is the tenant boundary.

Tenant isolation must be enforced logically across all Teacher-owned data, including:

- Educational Grades
- Groups
- Teacher Student Relationships
- Enrollments
- Attendance Records
- Homework
- Lessons
- Question Bank
- Questions
- Exams
- Exam Attempts
- Exam Grades
- Reports
- Teacher Staff
- Teacher Workspace Settings
- Flow B Student Fee Status
- File References

## Isolation Rules

1. Every Teacher Workspace-owned logical entity must be associated with its owning Teacher Workspace.
2. Teacher queries must be scoped to the Teacher's own Teacher Workspace.
3. Teacher Staff queries must be scoped to the Teacher Workspace that created the Teacher Staff user.
4. Student queries may span multiple Teacher Workspaces only for that Student's own Teacher relationships.
5. Parent queries may span Teacher Workspaces only through linked Students and only in read-only form.
6. Super Admin queries remain Platform-level and must respect pending content-visibility boundaries.
7. No cross-Teacher foreign ownership is allowed in logical design.
8. Reports must preserve Teacher Workspace isolation.
9. File References must preserve Teacher Workspace ownership.

Tenant isolation is a business rule and a data-design rule. It must not be treated as only an application-layer concern.

---

# 7. Soft Delete Strategy

The canonical business term is **Archive**, not delete. This section uses “soft delete” only to describe the logical persistence strategy behind Archive.

## Archive Requirements

- No permanent deletion exists anywhere in Version 1.
- Archive applies to all records, by all actors, everywhere.
- Archived records do not appear in normal active searches.
- Archived records do not appear in active dropdown lists, selectors, pickers, or assignment lists.
- Archived records remain available in reports and historical queries.
- Archived records can be restored by authorized users.
- Archive and restore actions are recorded in the Audit Log.
- Archived records never lose historical relationships.

## Logical Soft Delete Behavior

The logical database design must represent active versus archived state for records without physically removing them.

Archived state must support:

- Active filtering.
- Historical inclusion.
- Restoration.
- Auditability.
- Retention of relationships.
- Clear indication in reports.

Physical implementation details for the Archive state are intentionally not defined in this document.

---

# 8. Audit Strategy

The Audit Log is a first-class subsystem and must be supported by the database design.

## Audit Log Requirements

The database design must support Audit Log entries for:

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

## Audit Log Properties

- Audit Log entries are append-only.
- Audit Log entries are immutable.
- Audit Log retention is permanent.
- Audit Log entries must identify the actor and role context.
- Teacher Staff actions must be attributed to the Teacher Staff user, not to the Teacher.
- Audit Log entries must identify the action context, such as Platform scope or Teacher Workspace scope.
- Audit Log entries must support historical traceability even when the affected record is archived.

## Audit Scope

- Teacher-level audit visibility must remain scoped to the Teacher Workspace where permitted.
- Super Admin audit visibility is Platform-level and subject to Super Admin content-visibility boundaries.
- Parent and Student Audit Log visibility is not a confirmed Version 1 surface.

---

# 9. Versioning Strategy

Versioning in this logical database design refers to preserving historical meaning over time, not to source code versioning.

## Historical Versioning Requirements

The database design must preserve historical context for:

- Student Group movement.
- Enrollments over time.
- Attendance history.
- Homework history.
- Exam attempts and grades.
- Flow A billing periods.
- Historical pricing as of the billing period.
- Archived records.
- Audit Log events.

## Enrollment Versioning

Enrollment is a time-bounded relationship between a Student and a Group under a Teacher Workspace. When a Student moves between Groups, the previous Enrollment is closed logically and a new Enrollment is opened logically.

Historical records must remain associated with the correct historical Enrollment context.

## Billing Versioning

Flow A billing must preserve the Billing Cycle and pricing as of the billing period. Immutable monthly Subscription snapshots are PROPOSED in the Project Context and should be considered in future physical design without contradicting confirmed rules.

## Content Versioning

This document does not introduce formal content versioning for Homework, Lessons, Questions, or Exams beyond confirmed Archive, Audit Log, and historical preservation rules.

---

# 10. Indexing Strategy

This section describes logical indexing priorities only. It does not define physical indexes.

The indexing strategy must optimize for MySQL 8 and cPanel Shared Hosting by supporting efficient access patterns without requiring Redis, external search infrastructure, or non-MySQL indexing systems.

## Logical Indexing Priorities

The physical design should later optimize access by:

- Tenant scope through Teacher Workspace association.
- User identity lookup.
- Role context lookup.
- Student global identity uniqueness.
- Parent linked Student relationships.
- Teacher Student Relationships.
- Current Enrollment lookup by Student and Teacher.
- Historical Enrollment lookup.
- Attendance lookup by Student, Group, Teacher Workspace, and date context.
- Homework lookup by Teacher Workspace and Student relationship.
- Lesson lookup by Teacher Workspace and Student availability.
- Exam lookup by Teacher Workspace and Student relationship.
- Flow A Subscription lookup by Teacher and Billing Cycle.
- Flow B payment-status lookup by Student and Teacher relationship.
- Audit Log lookup by actor, event type, scope, and time context.
- Archive state filtering for active versus archived records.

## Indexing Constraints

- Indexing must preserve Teacher Workspace isolation.
- Indexing must not be used to bypass authorization.
- Indexing must support active-record filtering and historical reporting.
- Full physical indexing definitions are intentionally deferred.

---

# 11. File Storage References

Version 1 uses Laravel Public Storage. The database stores logical references to files rather than treating file binaries as business records.

## File Reference Responsibilities

Logical File References must support:

- Teacher-owned Lesson videos.
- Homework files in supported formats: Image and PDF, with Text handled as logical content according to detailed design.
- Historical file references.
- Archived file references.
- Teacher Workspace ownership.
- Student or Parent access through authorized relationships.

## File Ownership Rules

- Lesson file references belong to the owning Teacher Workspace.
- Homework file references belong to the relevant Teacher Workspace and Homework context.
- File references must not be visible across Teacher Workspaces.
- Parent access to files is read-only and limited to linked Students where applicable.
- Student access to files is limited to the Student's own Teacher relationships.

## File Retention Rules

- Archived file references must remain retained for historical reports.
- File references must not be detached from historical records by Archive.
- File storage must not require S3 Storage in Version 1.
- Lesson video hosting/protection remains PENDING and must not be silently hardened.

---

# 12. Data Integrity Rules

The logical database design must enforce confirmed business integrity rules.

## Identity Integrity

- A Student has exactly one global account.
- Duplicate Student accounts are not allowed.
- A Parent has one account.
- One Student can have only one Parent account in Version 1.
- One Parent account may monitor multiple Students.

## Teacher Workspace Integrity

- Each Teacher account has one Teacher Workspace.
- Teacher Workspace-owned records must belong to the correct Teacher Workspace.
- No Teacher Workspace may own another Teacher Workspace's data.
- Teacher Staff exist only inside the creating Teacher Workspace.

## Academic Integrity

- Each Teacher account represents exactly one Teaching Subject.
- Teaching Subject cannot be changed after account creation.
- Educational Grades are independent from Teaching Subjects.
- Each Group belongs to one Educational Grade.
- A Student belongs to only one Group per Teacher at any time.
- Student transfers preserve historical Attendance, Homework, Exams, and grades.

## Content Integrity

- Question Bank is Teacher-owned and private.
- Exams are composed only from the owning Teacher's Question Bank.
- Supported question types are Multiple Choice, True/False, Essay, and Bubble Sheet.
- Lesson videos are Teacher-owned and private.
- Homework supports Text, Image, and PDF only.
- Video homework is not supported in Version 1.

## Financial Integrity

- Flow A and Flow B must remain separate.
- Billable Student calculation is based on Enrollment duration only.
- Attendance and login activity are not used for Billable Student calculation.
- Flow B derives from Group Price and Pricing Type.
- Version 1 records payment status only.

## Governance Integrity

- Archive replaces permanent deletion.
- Historical data is never deleted.
- Audit Log entries are append-only and immutable.

---

# 13. Referential Integrity Rules

Referential integrity must preserve relationships while supporting Archive and historical reporting.

## Logical Referential Rules

- Teacher Workspace-owned records must reference the owning Teacher Workspace logically.
- Groups must reference their Educational Grade logically.
- Student relationships with Teachers must reference both Student and Teacher Workspace logically.
- Enrollments must reference the Student relationship and Group context logically.
- Attendance, Homework, Exams, and grades must preserve the Teacher Workspace and Student context.
- Exam Attempts and grades must preserve their Exam and Student relationship context.
- Flow A Subscription records must preserve Teacher and Billing Cycle context.
- Flow B payment-status records must preserve Student and Teacher relationship context.
- File References must preserve the owning Teacher Workspace and associated logical content context.
- Audit Log entries must preserve actor, role context, event context, and affected record reference.

## Archive-Aware Referential Integrity

Archived records remain valid references for historical reports. Archiving must never break historical references.

## Cross-Tenant Referential Constraints

Teacher Workspace-owned records must not logically reference records owned by another Teacher Workspace except through approved global identity relationships such as Student identity. Even when a Student studies with multiple Teachers, each Teacher-specific relationship remains separate.

---

# 14. Cascade Rules

Cascade rules must preserve historical data and must not hard delete records.

## General Cascade Principles

1. Cascades must never perform permanent deletion.
2. Archiving a parent or container record must not erase historical child records.
3. Historical records remain linked to their original context.
4. Restore behavior must be authorized and audited.
5. Cascade behavior must preserve Teacher Workspace isolation.

## Logical Cascade Examples

- Archiving an Educational Grade removes it from active selection lists but does not remove historical Groups, Enrollments, Attendance, Homework, Exams, or reports.
- Archiving a Group removes it from active assignment lists but preserves historical Enrollment and Student activity records.
- Archiving a Teacher Staff account prevents active use but does not remove historical Audit Log attribution.
- Archiving a Lesson prevents active Lesson access where applicable but preserves historical references.
- Archiving Homework prevents active use while preserving submissions and historical reporting.
- Archiving an Exam prevents active Exam use while preserving attempts and grades.
- Archiving a Teacher account must preserve Teacher Workspace history and records.

Detailed entity-specific cascade behavior is deferred to later physical design, but it must not contradict the Archive Policy.

---

# 15. Archiving Strategy

The Archiving Strategy implements the Project Context rule that Archive replaces deletion everywhere.

## Active Versus Archived State

Each archivable logical entity must support active and archived states. Active records appear in normal operational workflows. Archived records are retained but excluded from active selection and normal active searches.

## Archive Visibility

Archived records:

- Do not appear in normal active searches.
- Do not appear in active dropdown lists, selectors, pickers, or assignment lists.
- Remain available in reports.
- Are clearly indicated when included in historical views.
- Can be restored by authorized users.

## Archive Auditability

Every Archive and restore action must be recorded in the Audit Log. The Audit Log must identify the actor, role context, action context, and affected record reference according to detailed design.

## Historical Preservation

Archive must not detach, rewrite, or repoint historical relationships. Reports and historical queries must continue to work after records are archived.

---

# 16. Data Retention Policy

The Data Retention Policy follows the confirmed Project Context rules.

## Retention Rules

- Historical data is never deleted.
- Audit Log entries are retained permanently.
- Archived records are retained.
- Student transfer history is retained.
- Historical Attendance, Homework, Exams, and grades are retained.
- Historical payment-status records are retained.
- Historical Flow A pricing must remain understandable as of the billing period.
- File References tied to historical records are retained.

## Reporting Retention

Reports and history queries must include archived records where applicable and clearly indicate archived status.

## Audit Retention

Audit Log retention is permanent. Audit Log records are not edited or deleted.

## cPanel Considerations

Because Version 1 targets cPanel Shared Hosting, retention must be designed with MySQL 8 and Laravel Public Storage constraints in mind. Future physical design may define storage management practices, but it must not violate the permanent historical retention and Archive rules.

---

# 17. Future Database Considerations

Future database design may evolve after Version 1 while preserving the confirmed business rules.

Potential future considerations include:

1. **VPS / Cloud migration**  
   Future deployment on VPS / Cloud may allow more advanced database operations, scaling options, backup strategies, and storage options.

2. **Advanced search**  
   Version 1 must remain compatible with MySQL 8 and cPanel Shared Hosting. Any future external search capability must preserve Teacher Workspace isolation.

3. **Advanced file storage**  
   Version 1 uses Laravel Public Storage and must not require S3 Storage. Future storage changes must preserve Teacher-owned file privacy and historical references.

4. **Pricing model refinement**  
   Flat price versus volume tiers remains PENDING. Future physical design may support the confirmed pricing model once resolved.

5. **Non-payment enforcement**  
   Non-payment enforcement remains PENDING. Future database design may support confirmed enforcement behavior only after Product Owner approval.

6. **Super Admin content visibility**  
   Super Admin content visibility remains PENDING. Future database access design must not expose Teacher-private content unless formally confirmed.

7. **Teacher Staff permissions**  
   Teacher Staff permission granularity remains PENDING. Future RBAC and database design may define the final permission model.

8. **Localization and regional settings**  
   Arabic (default) and English (fully supported), with automatic RTL/LTR, are confirmed; timezone, currency, and target market/country remain PENDING. Future database design may support these after confirmation.

9. **Physical schema definition**  
   Future database documentation may define physical tables, columns, indexes, migrations, and Laravel models, but those are intentionally excluded from this logical design document.

All future database considerations must preserve Teacher Workspace isolation, one global Student account, one Parent account per Student, Archive instead of deletion, permanent Audit Log retention, and Flow A / Flow B separation.
