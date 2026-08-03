# 1. RBAC Overview

This document defines the Role-Based Access Control model for Version 1 of the Unified Education Platform.

The RBAC model governs who can access Platform data, Teacher Workspace data, Student data, Parent-linked Student data, and administrative functions. It must remain consistent with the official Project Context, Project Vision, Software Requirements, System Architecture, Database Design, and Data Dictionary.

Version 1 has exactly five confirmed roles:

1. Super Admin
2. Teacher
3. Teacher Staff
4. Student
5. Parent

RBAC in Version 1 is based on the following access dimensions:

- **Role** — the user's confirmed role context.
- **Scope** — Platform scope, Teacher Workspace scope, Student account scope, or Parent linked-Student scope.
- **Ownership** — whether the resource belongs to the user, the user's Teacher Workspace, the user's linked Student, or Platform administration.
- **Permission** — whether the role is allowed to perform the requested action.
- **Archive State** — whether the resource is active or archived.
- **Audit Requirement** — whether the action must be recorded in the Audit Log.

The official authorization architecture uses Laravel Gates & Policies and Custom RBAC based on project requirements. This document defines the logical RBAC model only. It does not define physical persistence structures, HTTP contracts, implementation code, or presentation behavior.

---

# 2. Security Principles

The RBAC model follows these security principles:

1. **Deny by default**  
   A user can access a resource only when the role, scope, ownership, and permission checks allow it.

2. **Teacher Workspace isolation is mandatory**  
   A Teacher can access only their own Teacher Workspace. Teacher Staff can access only the Teacher Workspace that created them.

3. **Student data is self-scoped**  
   A Student can access only their own account and their own per-Teacher records.

4. **Parent data is linked-Student scoped**  
   A Parent can access only linked Students and has read-only access everywhere.

5. **Super Admin is Platform-scoped**  
   The Super Admin has Platform-level administration permissions and does not operate inside Teacher Workspaces as a Teacher.

6. **Teacher Staff permissions are assigned by the Teacher**  
   Teacher Staff hold only the permissions assigned by the Teacher inside that Teacher Workspace.

7. **No cross-Teacher access**  
   No Teacher or Teacher Staff user can view or modify another Teacher Workspace's data.

8. **No marketplace access**  
   No user role receives permissions for course discovery, Teacher browsing as marketplace behavior, or cross-Teacher content access.

9. **Archive replaces deletion**  
   RBAC must never grant hard-delete permissions. Authorized users may Archive or restore where allowed.

10. **Audit Log is mandatory for sensitive actions**  
   Every sensitive or important action must be recorded in the Audit Log according to the Audit Log Policy.

---

# 3. Roles

## Super Admin

The Super Admin owns the Platform at Platform-level scope.

The Super Admin manages:

- Teachers
- Flow A Subscriptions
- Pricing
- Platform Settings
- Global Reports
- Platform-level Audit Log visibility according to confirmed boundaries

The Super Admin does not operate inside Teacher Workspaces as a Teacher. Super Admin visibility into Teacher-private content remains PENDING and must not be silently expanded.

## Teacher

The Teacher operates one completely isolated Teacher Workspace.

The Teacher manages, strictly within that Teacher Workspace:

- Educational Grades
- Groups
- Students
- Attendance
- Homework
- Exams
- Lessons
- Reports
- Users / Teacher Staff
- Settings
- Flow B payment-status records

The Teacher cannot access another Teacher's data under any circumstance.

## Teacher Staff

Teacher Staff are created by a Teacher and exist only inside that Teacher Workspace.

Examples include:

- Secretary
- Assistant
- Accountant

Teacher Staff hold only permissions assigned by the Teacher. Permission granularity remains PENDING and must be finalized without contradicting the Project Context.

## Student

A Student has exactly one global account and may study with multiple Teachers.

A Student can access only:

- Their own account information
- Their own schedule information
- Homework assigned to them
- Lessons from their own Teachers
- Exams assigned or available to them
- Their own per-Teacher Flow B status
- Their own per-Teacher Attendance, Homework, Exams, and grades where available

Student records remain partitioned per Teacher.

## Parent

A Parent has one account and may monitor multiple linked Students.

A Parent can access only linked Students and has read-only access everywhere.

A Parent cannot modify:

- Attendance
- Grades
- Homework
- Exams
- Student records
- Teacher records
- Payment status
- Teacher Workspace data

Version 1 supports exactly one Parent account per Student.

---

# 4. Permission Categories

The RBAC model groups permissions into logical categories. Categories define the type of access but do not define physical persistence structures, HTTP contracts, or implementation code.

## Platform Administration Permissions

Used by Super Admin for Platform-level administration.

Includes logical access to:

- Teacher account management
- Flow A Subscription status
- Pricing configuration
- Platform Settings
- Platform-level reports
- Platform-scope Audit Logs

## Teacher Workspace Management Permissions

Used by Teachers and authorized Teacher Staff inside one Teacher Workspace.

Includes logical access to:

- Educational Grades
- Groups
- Students
- Enrollments
- Attendance
- Homework
- Exams
- Lessons
- Reports
- Users / Teacher Staff
- Teacher Workspace Settings
- Flow B payment status

## Student Self-Service Permissions

Used by Students for their own data only.

Includes logical access to:

- Dashboard summaries
- My Schedule
- Homework
- Lessons
- Exams
- Subscriptions view for Flow B status
- Settings for their own account context

## Parent Monitoring Permissions

Used by Parents for linked Students only.

Includes read-only logical access to:

- Dashboard summaries
- Student Switcher
- Homework
- Attendance
- Exams and grades
- Teachers of linked Students
- Payments / Flow B status
- Parent account context boundaries

## Audit Permissions

Used to determine who can view Audit Log information.

Audit Log visibility must preserve scope boundaries:

- Teacher visibility is Teacher Workspace scoped where permitted.
- Super Admin visibility is Platform scoped and subject to confirmed content-visibility boundaries.
- Student and Parent Audit Log visibility is not confirmed as a Version 1 product surface.

## Archive Permissions

Used to control Archive and restore behavior.

No role receives hard-delete permission.

---

# 5. Permission Naming Convention

Permission names should be logical, consistent, and traceable to canonical terminology.

A recommended logical permission format is:

`scope.resource.action`

Where:

- `scope` identifies the access boundary.
- `resource` identifies the canonical resource name.
- `action` identifies the permitted operation.

Examples of logical scopes:

- `platform`
- `teacher_workspace`
- `student_account`
- `parent_linked_student`

Examples of logical resources:

- `teacher`
- `educational_grade`
- `group`
- `student`
- `attendance`
- `homework`
- `lesson`
- `exam`
- `question_bank`
- `report`
- `payment_status`
- `subscription`
- `settings`
- `audit_log`

Examples of logical actions:

- `view`
- `create`
- `update`
- `archive`
- `restore`
- `submit`
- `grade`
- `record`
- `assign_permission`
- `view_history`

Naming constraints:

- Use `educational_grade`, not class terminology.
- Use `lesson`, not course terminology.
- Use `archive`, not delete terminology.
- Use `subscription` only for Flow A unless explicitly qualified.
- Use `payment_status` for status-only tracking, not transaction processing.

This naming convention is logical only and does not define implementation code.

---

# 6. Teacher Permissions

Teacher permissions apply only within the Teacher's own Teacher Workspace.

## Teacher Allowed Permissions

A Teacher may, within their own Teacher Workspace:

- View Teacher Workspace Dashboard information.
- Create, view, update, Archive, and restore Educational Grades.
- Create, view, update, Archive, and restore Groups.
- Register new Students where no duplicate Student account is created.
- Assign existing Students to the Teacher Workspace.
- Search Students for Teacher Workspace management purposes.
- Assign Students to one Group under the Teacher.
- Move Students between Groups while preserving history.
- Record Attendance through confirmed methods.
- View and modify Attendance where authorized.
- Create, view, update, Archive, restore, and grade Homework where applicable.
- Create, view, update, Archive, restore, and manage Exams.
- Manage the Teacher-owned private Question Bank.
- Upload and manage private Lessons for the Teacher's own Students.
- View Reports for the Teacher Workspace.
- Manage Teacher Staff accounts.
- Assign permissions to Teacher Staff.
- Manage Teacher Workspace Settings except Teaching Subject changes.
- Record and view Flow B payment status for the Teacher Workspace.

## Teacher Denied Permissions

A Teacher may not:

- Access another Teacher Workspace.
- View another Teacher's Students, Groups, Attendance, Homework, Exams, Lessons, Question Bank, Reports, Users, Settings, or payments.
- Change Teaching Subject after account creation.
- Create multiple Teaching Subjects under one Teacher account.
- Hard delete records.
- Process payments through the Platform.
- Use online payment gateways.
- Sell courses through the Platform.
- Publish content to marketplace discovery.

## Teacher Sensitive Actions Requiring Audit Log

The following Teacher actions must be audited:

- Create, update, Archive, or restore records.
- Attendance changes.
- Exam modifications.
- Homework modifications.
- Permission changes for Teacher Staff.
- Subscription-related changes where applicable.
- Login events.

---

# 7. Student Permissions

Student permissions apply only to the Student's own account and per-Teacher records.

## Student Allowed Permissions

A Student may:

- View their own Dashboard summaries.
- View their My Schedule across their own Teacher relationships.
- View Homework assigned to them.
- Submit Homework where required and where the format is supported.
- View Lessons from their own Teachers.
- View Exams assigned or available to them.
- Attempt Exams assigned or available to them.
- View their own Exam attempt status and grades where available.
- View per-Teacher Flow B status in Subscriptions.
- View and update their own account Settings according to confirmed requirements.
- Activate a Teacher-created Student account.
- Scan the daily Dynamic QR Code through the Web Application for Attendance.

## Student Denied Permissions

A Student may not:

- Access another Student's account or records.
- Access Teacher Workspace management areas.
- Access Lessons from Teachers with whom they are not enrolled.
- Access another Teacher's private Question Bank.
- Create or edit Teacher Homework.
- Grade Homework.
- Create, edit, publish, Archive, restore, or grade Exams.
- Modify Attendance records manually.
- Move themselves between Groups.
- Modify payment status.
- Process payments through the Platform.
- Create duplicate Student accounts.

## Student Sensitive Actions Requiring Audit Log

The following Student-related actions must be audited where applicable:

- Login success and failure.
- Homework submission or modification events that qualify under Homework Modification.
- Exam attempt or submission events where they qualify as important actions.
- Attendance scan events where they record Attendance.
- Account activation events where treated as important account actions.

---

# 8. Parent Permissions

Parent permissions are read-only and apply only to linked Students.

## Parent Allowed Permissions

A Parent may:

- View Dashboard summaries for linked Students.
- Use the Student Switcher to switch between linked Students.
- View Homework for linked Students.
- View Attendance for linked Students.
- View Exams and grades for linked Students where available.
- View Teachers associated with linked Students.
- View Flow B payment status for linked Students.
- Access Parent account context boundaries according to confirmed requirements.

## Parent Denied Permissions

A Parent may not:

- Access unlinked Students.
- Modify Attendance.
- Modify grades.
- Modify Homework.
- Submit Homework for a Student.
- Take Exams or submit Exam answers for a Student.
- Modify Exam records.
- Modify payment status.
- Process payments through the Platform.
- Modify Student records.
- Modify Teacher records.
- Modify Group assignment.
- Modify Teacher Workspace data.
- Access Teacher-private Question Bank content.
- Access Teacher Workspace management functions.
- Browse Teachers or Lessons as marketplace content.

## Parent Sensitive Actions Requiring Audit Log

Parent login events must be audited. Parent account updates, if defined in later requirements, must be audited where they qualify as important actions.

Parent read-only viewing actions are not explicitly listed as mandatory Audit Log events in the Project Context unless later requirements define them as important actions.

---

# 9. Super Admin Permissions

Super Admin permissions apply at Platform level.

## Super Admin Allowed Permissions

The Super Admin may:

- Manage Teachers at Platform level.
- Manage Flow A Subscriptions.
- Manage Platform pricing.
- Manage Platform Settings.
- View global reports within confirmed visibility boundaries.
- View Platform-scope Audit Logs according to confirmed visibility boundaries.
- Record Flow A payment status.
- View Platform-level Subscription and payment-status information.

## Super Admin Denied or Constrained Permissions

The Super Admin may not:

- Operate inside Teacher Workspaces as a Teacher in Version 1.
- Bypass Teacher Workspace isolation.
- Access Teacher-private content beyond confirmed visibility boundaries.
- Treat Flow B as Platform revenue.
- Process payments through the Platform.
- Configure online payment gateways for Version 1.
- Create Platform staff roles such as Support, Sales, or Accountant in Version 1.
- Hard delete records.

## Pending Super Admin Visibility

Super Admin content visibility remains PENDING. Until confirmed, the RBAC model must not grant unrestricted access to Teacher-private content such as:

- Lesson videos
- Question Banks
- Homework content
- Exam definitions
- Student workspace-private records

## Super Admin Sensitive Actions Requiring Audit Log

The following Super Admin actions must be audited where applicable:

- Teacher account creation, update, Archive, or restore.
- Subscription status changes.
- Pricing or Platform Settings updates.
- Payment-status changes.
- Login success and failure.
- Archive and restore actions.

---

# 10. Tenant Isolation Rules

Tenant isolation is mandatory and is based on Teacher Workspace.

## Core Tenant Rules

1. Teacher Workspace is the tenant boundary.
2. Teacher-owned records must belong to one Teacher Workspace.
3. Teacher access is limited to the Teacher's own Teacher Workspace.
4. Teacher Staff access is limited to the Teacher Workspace that created them.
5. Student access can span multiple Teacher Workspaces only through the Student's own Teacher relationships.
6. Parent access can span Teacher Workspaces only through linked Students and only in read-only mode.
7. Super Admin access is Platform-level and subject to confirmed visibility boundaries.
8. No Teacher Workspace may expose its data to another Teacher Workspace.
9. Reports must preserve tenant isolation.
10. File access must preserve tenant isolation.

## Tenant-Scoped Resources

Tenant-scoped resources include:

- Educational Grades
- Groups
- Student relationships with Teacher
- Student Enrollments
- Attendance
- Attendance Sessions
- QR Sessions
- Homework
- Homework Submissions
- Lessons
- Lesson Videos
- Question Bank
- Questions
- Exams
- Exam Attempts
- Exam Answers
- Reports
- Teacher Staff
- Teacher Workspace Settings
- Flow B payment status
- File Attachments

---

# 11. Ownership Rules

Ownership rules determine who owns a resource and who may access it.

## Teacher-Owned Resources

The following are owned by the Teacher Workspace:

- Educational Grades
- Groups
- Attendance records
- Homework
- Lessons
- Question Bank
- Questions
- Exams
- Reports
- Teacher Staff
- Teacher Workspace Settings
- Flow B payment-status records
- Teacher-owned File Attachments

Only the owning Teacher and authorized Teacher Staff may manage these resources.

## Student-Owned or Student-Scoped Resources

The Student owns their global account. Academic records are not globally owned in an unrestricted way; they are scoped by Student and Teacher relationship.

Student-scoped resources include:

- My Schedule
- Homework assigned to the Student
- Homework Submissions
- Exam Attempts
- Exam Answers
- Exam grades visible to the Student
- Per-Teacher Flow B status
- Attendance visibility where applicable

## Parent-Scoped Resources

Parent access is scoped by linked Students. The Parent does not own Student academic records and cannot modify them.

## Platform-Owned Resources

The Platform, through Super Admin, owns:

- Teacher account administration
- Flow A Subscription administration
- Pricing
- Platform Settings
- Platform-level reports
- Platform-scope Audit Log visibility

---

# 12. Resource Access Rules

Resource access is evaluated through role, scope, ownership, permission, and Archive state.

## General Access Rules

1. A user must be authenticated before accessing protected resources.
2. A user must have an authorized role context.
3. A resource must belong to the user's permitted scope.
4. Archived records must not appear as active resources.
5. Historical reports may include archived records when clearly indicated.
6. Sensitive actions must be recorded in the Audit Log.
7. Hard delete is never allowed.

## Active Resource Access

Active resource access is allowed only when:

- The user role permits the action.
- The resource belongs to the correct scope.
- The user owns or is linked to the resource context.
- The resource is not archived.

## Archived Resource Access

Archived resource access is allowed only for:

- Historical reports.
- Authorized restoration actions.
- Audit and history views where permitted.

Archived resources must not appear in active selection lists.

## File Access Rules

File Attachments must follow the same ownership and Tenant Isolation Rules as the entity they belong to.

Examples:

- Lesson Videos are accessible only to the owning Teacher's Students and authorized Teacher Workspace users.
- Homework attachments are accessible only within the relevant Teacher Workspace and Student relationship.
- Parent access to files is read-only and only through linked Students.

---

# 13. Authentication Rules

Authentication is required for protected access to the Platform.

## Authentication Requirements

- Laravel Sanctum is the confirmed authentication mechanism.
- All protected user actions require authenticated context.
- Successful and failed login events must be recorded in the Audit Log.
- Student accounts may be self-registered or Teacher-created.
- Teacher-created Student accounts may later be activated by the Student.
- Duplicate Student accounts are not allowed.
- Parent accounts are linked to Students according to Version 1 rules.

## Authentication Constraints

Authentication must not introduce:

- Duplicate Student accounts.
- Multiple Parent accounts for one Student.
- Native mobile authentication requirements for Version 1.
- Unconfirmed impersonation behavior.

---

# 14. Authorization Flow

The authorization flow is:

1. The user authenticates through the Platform.
2. The backend identifies the User and active Role context.
3. The backend determines the requested resource and action.
4. The backend resolves the access scope:
   - Platform scope for Super Admin.
   - Teacher Workspace scope for Teacher and Teacher Staff.
   - Student account and Teacher relationship scope for Student.
   - Linked Student scope for Parent.
5. The backend checks resource ownership or relationship.
6. The backend checks whether the action is allowed for the role.
7. The backend checks whether the resource is active or archived.
8. The backend rejects unauthorized or invalid access without exposing restricted data.
9. If the action proceeds and is sensitive or important, the backend records an Audit Log entry.

Authorization must be enforced server-side. Frontend visibility or hidden controls are not sufficient security controls.

---

# 15. Audit Requirements

The RBAC model must integrate with the Audit Log.

## Mandatory Audit Events

The following events must be recorded:

- Create
- Update
- Archive
- Restore
- Login success and failure
- Permission Change
- Attendance Change
- Exam Modification
- Homework Modification
- Subscription Change

## Audit Data Requirements

Audit Log entries must logically capture:

- Actor identity.
- Actor role.
- Context, such as Platform or Teacher Workspace.
- Event type.
- Affected resource type.
- Affected resource reference where applicable.
- Relevant before/after or change context according to detailed design.
- Timestamp and origin information according to Audit Log policy.

## Audit Attribution Rules

- Teacher Staff actions are attributed to the Teacher Staff user, not to the Teacher.
- Super Admin Platform actions are attributed to the Super Admin.
- Student and Parent actions are attributed to the authenticated Student or Parent account.
- Failed login events must be auditable.

## Audit Immutability

Audit Log entries are append-only, immutable, and permanently retained.

---

# 16. Security Constraints

The RBAC model must respect the following constraints:

1. The Project Context is the Single Source of Truth.
2. Version 1 has exactly five roles.
3. Teacher Workspace isolation is mandatory.
4. Parent access is read-only everywhere.
5. Student access is limited to the Student's own data.
6. Teacher Staff access is limited to assigned permissions inside the creating Teacher Workspace.
7. Super Admin operates at Platform-level scope.
8. Super Admin content visibility remains PENDING.
9. Teacher Staff permission granularity remains PENDING.
10. No hard delete permissions exist.
11. Online payment gateways are out of scope.
12. Notifications are out of scope.
13. Marketplace behavior is out of scope.
14. Multiple Teaching Subjects per Teacher account are out of scope.
15. Multiple Parent accounts per Student are out of scope.
16. Video homework is out of scope.
17. RBAC must not rely on frontend-only enforcement.
18. Sensitive actions must be recorded in the Audit Log.

---

# 17. Future RBAC Considerations

Future RBAC work may refine unresolved areas after Product Owner confirmation.

Potential future RBAC considerations include:

1. **Teacher Staff permission granularity**  
   The detailed Teacher Staff permission model remains PENDING. Future RBAC documentation may define capability flags, presets, or more detailed permission structures.

2. **Super Admin content visibility**  
   Super Admin visibility into Teacher-private content remains PENDING. Future decisions must define whether access is limited to aggregates, finances, metadata, or broader visibility.

3. **Non-payment enforcement**  
   Non-payment enforcement remains PENDING. Future RBAC may define read-only enforcement behavior if confirmed.

4. **Platform staff roles**  
   Support, Sales, and Accountant Platform staff accounts are out of scope for Version 1. Future versions may define additional Platform roles if approved.

5. **Future payment gateway roles**  
   Online payment gateways are out of scope for Version 1. Future payment integrations may require additional payment-related permissions if approved.

6. **Future notification permissions**  
   Notifications are out of scope for Version 1. Future notification features may require permissions if approved.

All future RBAC changes must preserve Teacher Workspace isolation, one global Student account, Parent linked-Student read-only access, Archive instead of hard deletion, and Audit Log requirements.
