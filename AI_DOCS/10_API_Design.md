# 10 — API Design

## Document Scope

This document defines the REST API specification for Version 1 of the Unified Education Platform.

Source-of-truth documents reviewed before authoring:

- `AI_DOCS/00_Project_Context.md`
- `AI_DOCS/01_Project_Vision.md`
- `AI_DOCS/02_Software_Requirements.md`
- `AI_DOCS/03_System_Architecture.md`
- `AI_DOCS/06_Database_Design.md`
- `AI_DOCS/07_Data_Dictionary.md`
- `AI_DOCS/08_RBAC.md`
- `AI_DOCS/09_Permission_Matrix.md`

This document defines RESTful API contracts at specification level only. It does not define source code, Laravel controllers, database tables, migrations, or implementation internals.

---

# 1. API Overview

The Version 1 API is a REST API used by the React 19 Web Application to communicate with the Laravel 12 backend.

The API supports the confirmed Version 1 roles:

- Super Admin
- Teacher
- Teacher Staff
- Student
- Parent

The API must preserve these core rules:

1. Teacher Workspace isolation is mandatory.
2. A Student has exactly one global account and may study with multiple Teachers.
3. A Parent has one account and may monitor multiple linked Students.
4. Parent access is read-only everywhere.
5. Teacher Staff access is limited to Teacher-assigned permissions inside the creating Teacher Workspace.
6. Flow A and Flow B are separate and must never be conflated.
7. Version 1 records payment status only and does not process payments.
8. Archive replaces permanent deletion everywhere.
9. Important actions are recorded in the Audit Log.
10. Notifications are out of scope for Version 1.

The API is optimized for Laravel 12 and Laravel Sanctum while remaining a technology-neutral REST specification. Laravel-specific implementation classes, controllers, routes files, middleware names, request classes, and persistence details are intentionally excluded.

---

# 2. API Standards

| Standard Area | Standard |
|---|---|
| API style | RESTful resource-oriented HTTP API. |
| Base path | `/api/v1` for all Version 1 endpoints. |
| Data format | JSON for structured request and response bodies. |
| Authentication | Laravel Sanctum authenticated web/API session or token model according to final deployment configuration. |
| Authorization | Laravel Gates & Policies with Custom RBAC based on the logical permission catalog. |
| Transport | HTTPS is required in production. |
| State changes | State-changing operations must be authenticated, authorized, validated, and audited where required. |
| Archive behavior | Archive and restore endpoints use explicit REST actions; hard deletion endpoints are not provided. |
| File handling | File uploads use multipart form data where a binary file is included. Metadata remains JSON-compatible. |
| Payment handling | Payment endpoints record status only; no online payment transaction endpoint exists. |
| Notification handling | No notification endpoints exist in Version 1. |
| Cross-tenant access | Any request attempting cross-Teacher access must be rejected. |

---

# 3. Authentication

The API uses Laravel Sanctum as the confirmed authentication technology for Version 1.

Authentication requirements:

1. Protected endpoints require an authenticated user context.
2. Login success and login failure events must be recorded in the Audit Log.
3. Student self-registration and Teacher-created Student activation must not create duplicate Student accounts.
4. Parent account access must preserve the one Parent account per Student rule.
5. Unconfirmed impersonation behavior such as “Login as Teacher” is not part of Version 1.

Authentication response contents are specified logically. Exact token, session, or cookie mechanics are deployment and implementation concerns and are not defined here.

---

# 4. Authorization

Every protected endpoint must perform server-side authorization.

Authorization uses:

- Role context.
- Scope context.
- Ownership or relationship checks.
- Required permission from `AI_DOCS/09_Permission_Matrix.md`.
- Archive state rules.
- Pending visibility constraints where applicable.

Authorization boundaries:

| Role | API Authorization Boundary |
|---|---|
| Super Admin | Platform-level administration only; Teacher-private content visibility remains PENDING. |
| Teacher | Own Teacher Workspace only. |
| Teacher Staff | Creating Teacher Workspace only and only with Teacher-assigned permissions. |
| Student | Own account and own per-Teacher records only. |
| Parent | Linked Students only and read-only everywhere. |

Unauthorized requests must be denied without exposing restricted data.

---

# 5. Versioning

All Version 1 endpoints use the `/api/v1` prefix.

Versioning rules:

1. Version 1 API behavior must remain consistent with the frozen Project Context.
2. Breaking changes require a future API version.
3. New future-scope features must not be added to Version 1 endpoints unless formally approved in documentation.
4. Version 1 endpoints must not require native mobile applications, online payment gateways, notifications, WebSockets, S3 Storage, Redis, Docker, Kubernetes, or Microservices.

---

# 6. Error Response Standard

All error responses use a consistent logical error structure.

| Field | Purpose |
|---|---|
| `success` | Indicates failure as false. |
| `error.code` | Stable machine-readable error code. |
| `error.message` | Human-readable error message. |
| `error.details` | Optional non-sensitive detail. |
| `request_id` | Optional request reference for support and tracing. |

Common HTTP statuses:

| Status | Meaning |
|---|---|
| 400 | Bad request or invalid operation. |
| 401 | Authentication required or authentication failed. |
| 403 | Authenticated user is not authorized. |
| 404 | Resource not found or not visible to the user. |
| 409 | Conflict with a business rule or current resource state. |
| 422 | Validation failed. |
| 429 | Rate limit exceeded where rate limits apply. |
| 500 | Unexpected server error without exposing internals. |

Error responses must not expose Teacher-private data, unlinked Student data, another Student's records, another Teacher Workspace, or implementation internals.

---

# 7. Pagination Standard

List endpoints support pagination unless the endpoint explicitly returns a small fixed set.

| Parameter | Purpose |
|---|---|
| `page` | Requested page number. |
| `per_page` | Number of records per page within allowed limits. |

Paginated success responses include:

| Field | Purpose |
|---|---|
| `data` | Returned records. |
| `meta.current_page` | Current page. |
| `meta.per_page` | Records per page. |
| `meta.total` | Total visible records. |
| `meta.last_page` | Last page number. |

Pagination must apply after authorization and scope filtering. Hidden or unauthorized records must not affect visible results.

---

# 8. Filtering Standard

List endpoints may support filters appropriate to the resource.

Common filters:

| Filter | Purpose |
|---|---|
| `status` | Active, archived, pending, submitted, paid, unpaid, or other valid resource status. |
| `teacher_workspace_id` | Used only where the authenticated role is allowed to reference that Teacher Workspace. |
| `teacher_id` | Used only in Platform-level contexts or Student/Parent relationship contexts where visible. |
| `student_id` | Used only when the Student is owned, linked, or visible to the authenticated role. |
| `group_id` | Must belong to the permitted Teacher Workspace or visible Student relationship. |
| `educational_grade_id` | Must belong to the permitted Teacher Workspace. |
| `from_date` | Start date for date-range filtering. |
| `to_date` | End date for date-range filtering. |
| `billing_cycle` | Calendar-month billing period for Flow A Subscription queries. |

Filtering rules:

1. Filters must not bypass authorization.
2. Cross-Teacher filters must be rejected unless Platform-level visibility is confirmed.
3. Archived records appear only in historical/report contexts or when explicitly requested by authorized users.
4. Flow A and Flow B filters must remain separate.

---

# 9. Sorting Standard

List endpoints may support sorting using a `sort` query parameter.

Examples of logical sort fields:

| Sort Field | Purpose |
|---|---|
| `created_at` | Creation order. |
| `updated_at` | Last update order. |
| `name` | Alphabetical order where the resource has a name. |
| `status` | Status order where meaningful. |
| `date` | Date order for Attendance, reports, Billing Cycles, and historical records. |

Sorting rules:

1. A leading minus sign indicates descending order where supported.
2. Unsupported sort fields must be rejected or ignored consistently according to API standard behavior.
3. Sorting must occur only on records visible to the authenticated user.

---

# 10. Validation Response Standard

Validation failure responses use HTTP 422.

| Field | Purpose |
|---|---|
| `success` | Indicates failure as false. |
| `error.code` | `VALIDATION_FAILED`. |
| `error.message` | Summary of validation failure. |
| `errors` | Field-level validation messages. |

Validation rules must enforce:

- Required fields.
- Valid enum values.
- Valid dates and date ranges.
- Supported file formats.
- Teacher Workspace ownership.
- Student relationship validity.
- Parent linked-Student validity.
- One Group per Student per Teacher.
- No duplicate Student accounts.
- No Teaching Subject changes after Teacher account creation.
- Flow A and Flow B separation.
- Archive instead of permanent deletion.

---

# 11. File Upload Standard

File upload endpoints use multipart form data when a binary file is included.

File upload rules:

1. Files must be associated with an owning resource and authorized scope.
2. Teacher Workspace file ownership must be enforced.
3. Homework supports Text, Image, and PDF only.
4. Video homework is rejected.
5. Lesson videos are Teacher-owned and private; Lesson video hosting/protection details remain PENDING.
6. Parent file uploads are denied.
7. Student file uploads are allowed only for supported Homework submissions where assigned and permitted.
8. File references must remain available for historical records and archived records.
9. S3 Storage is not required for Version 1.

---

# 12. API Naming Convention

API naming uses canonical product terminology.

| Resource Concept | API Naming Standard |
|---|---|
| Teacher Workspace | `teacher-workspace` in descriptive text and workspace-scoped endpoints. |
| Educational Grade | `educational-grades`. |
| Group | `groups`. |
| Student | `students`. |
| Parent | `parents`. |
| Attendance | `attendance`. |
| Homework | `homework`. |
| Lesson | `lessons`. |
| Question Bank | `question-banks`. |
| Exam | `exams`. |
| Payment Status | `payment-status`. |
| Subscription | `subscriptions` for Flow A only. |
| Audit Log | `audit-logs`. |
| Archive action | `archive`. |
| Restore action | `restore`. |

Naming constraints:

1. Use Educational Grade as the canonical term for the requested Classes module.
2. Use Lesson, not Course.
3. Use Archive, not hard deletion.
4. Use Subscription only for Flow A.
5. Use payment status for Flow B records.
6. Do not create notification endpoint names for Version 1.

---

# 13. Authentication Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| POST | `/api/v1/auth/login` | Authenticate a user. | Public endpoint with authentication validation. | None. | Login identifier and authentication secret. | Authenticated user context and available role contexts. | 401, 422, 429. | Login identifier and secret are required. | Successful and failed login events must be audited. |
| POST | `/api/v1/auth/logout` | End current authenticated session or token context. | Authenticated user. | None. | None. | Logout confirmation. | 401. | User must be authenticated. | Does not remove historical login Audit Log records. |
| GET | `/api/v1/auth/me` | Return current authenticated user and role context. | Authenticated user. | None. | None. | Current user identity, active role, and permitted scopes. | 401. | User must be authenticated. | Response must not expose unauthorized role contexts. |
| POST | `/api/v1/auth/students/register` | Allow Student self-registration. | Public endpoint with registration validation. | None. | Student identity and account information. | Created Student account or activation-ready account context. | 409, 422. | Required identity fields; duplicate Student account prevention. | Student has exactly one global account. |
| POST | `/api/v1/auth/students/activate` | Activate a Teacher-created Student account. | `student_account.student.activate`. | None. | Activation identifier and required account confirmation data. | Activated Student account context. | 401, 404, 409, 422. | Activation data must match Teacher-created Student account. | Activation must not create a duplicate Student account. |

---

# 14. Teachers Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/platform/teachers` | List Teacher accounts at Platform level. | `platform.teacher.view`. | Pagination, filters, sorting. | None. | Paginated Teacher account list. | 401, 403. | Filters must be Platform-scoped. | Super Admin only; Teacher-private content is not included. |
| POST | `/api/v1/platform/teachers` | Create a Teacher account. | `platform.teacher.create`. | None. | Teacher account information and Teaching Subject. | Created Teacher account. | 401, 403, 409, 422. | Teaching Subject is required; account data must be valid. | Each Teacher account has exactly one Teaching Subject. |
| GET | `/api/v1/platform/teachers/{teacher_id}` | View Platform-level Teacher account details. | `platform.teacher.view`. | `teacher_id`. | None. | Teacher account details within Platform scope. | 401, 403, 404. | Teacher must exist and be visible to Super Admin. | Does not grant Teacher Workspace operation. |
| PATCH | `/api/v1/platform/teachers/{teacher_id}` | Update Platform-level Teacher account information. | `platform.teacher.update`. | `teacher_id`. | Allowed Teacher account fields. | Updated Teacher account. | 401, 403, 404, 422. | Teaching Subject cannot be changed. | Important updates are audited. |
| POST | `/api/v1/platform/teachers/{teacher_id}/archive` | Archive Teacher account. | `platform.teacher.archive`. | `teacher_id`. | Archive reason where required. | Archived Teacher account state. | 401, 403, 404, 409, 422. | Teacher must be archivable by authorized Super Admin. | Archive replaces permanent deletion and preserves history. |
| POST | `/api/v1/platform/teachers/{teacher_id}/restore` | Restore archived Teacher account. | `platform.teacher.restore`. | `teacher_id`. | Restore reason where required. | Restored Teacher account state. | 401, 403, 404, 409, 422. | Teacher account must be archived and restorable. | Restore action is audited. |
| GET | `/api/v1/platform/teachers/{teacher_id}/history` | View Teacher account history at Platform level. | `platform.teacher.view_history`. | `teacher_id`, pagination, filters. | None. | Teacher account history. | 401, 403, 404. | History must be Platform-scoped. | Historical data remains available. |

---

# 15. Students Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/teacher-workspace/students` | List Students in the Teacher Workspace. | `teacher_workspace.student.view`. | Pagination, filters, sorting. | None. | Paginated workspace-scoped Student list. | 401, 403. | Filters must reference current Teacher Workspace only. | Teacher sees only own workspace Student relationships. |
| POST | `/api/v1/teacher-workspace/students` | Manually create a Student account. | `teacher_workspace.student.create`. | None. | Student identity and account information. | Created Student account and workspace relationship. | 401, 403, 409, 422. | Required identity fields; duplicate prevention. | Teacher-created Student can later activate same account. |
| POST | `/api/v1/teacher-workspace/students/assign-existing` | Assign existing Student to Teacher Workspace. | `teacher_workspace.student.assign_existing`. | None. | Existing Student reference and Group assignment where applicable. | Student workspace relationship. | 401, 403, 404, 409, 422. | Existing Student must be valid; Group must belong to Teacher Workspace. | Assignment must not expose another Teacher's private data. |
| GET | `/api/v1/teacher-workspace/students/{student_id}` | View Student relationship in Teacher Workspace. | `teacher_workspace.student.view`. | `student_id`. | None. | Workspace-scoped Student detail. | 401, 403, 404. | Student must be associated with current Teacher Workspace. | No cross-Teacher Student records. |
| PATCH | `/api/v1/teacher-workspace/students/{student_id}` | Update Teacher Workspace Student relationship data. | `teacher_workspace.student.update`. | `student_id`. | Allowed workspace-scoped Student fields. | Updated Student relationship. | 401, 403, 404, 422. | Updates must not create duplicate account or cross-scope changes. | Student identity remains global; Teacher data remains partitioned. |
| POST | `/api/v1/teacher-workspace/students/{student_id}/archive` | Archive Student relationship or record within allowed scope. | `teacher_workspace.student.archive`. | `student_id`. | Archive reason where required. | Archived Student relationship state. | 401, 403, 404, 409, 422. | Student must be visible in current Teacher Workspace. | Historical data remains available. |
| POST | `/api/v1/teacher-workspace/students/{student_id}/restore` | Restore archived Student relationship or record. | `teacher_workspace.student.restore`. | `student_id`. | Restore reason where required. | Restored Student relationship state. | 401, 403, 404, 409, 422. | Archived relationship must be restorable. | Restore action is audited. |
| POST | `/api/v1/teacher-workspace/students/{student_id}/move-group` | Move Student between Groups under same Teacher. | `teacher_workspace.group.move_student`. | `student_id`. | Target Group reference and movement date/context. | Updated Enrollment state. | 401, 403, 404, 409, 422. | Target Group must belong to current Teacher Workspace; one active Group per Teacher. | Movement preserves historical Attendance, Homework, Exams, and grades. |
| GET | `/api/v1/student/profile` | View authenticated Student profile. | `student_account.student.view`. | None. | None. | Student profile and account context. | 401, 403. | User must be Student. | Student sees own account only. |
| PATCH | `/api/v1/student/profile` | Update authenticated Student account information. | `student_account.student.update`. | None. | Allowed Student account fields. | Updated Student profile. | 401, 403, 409, 422. | Updates must not create duplicate Student account. | Student cannot change Teacher Workspace data or Group assignment. |

---

# 16. Parents Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/parent/linked-students` | List Students linked to authenticated Parent. | `parent_linked_student.student.view`. | Pagination where applicable. | None. | Linked Student list. | 401, 403. | User must be Parent. | Parent sees only linked Students. |
| GET | `/api/v1/parent/linked-students/{student_id}` | View linked Student overview. | `parent_linked_student.student.view`. | `student_id`. | None. | Read-only linked Student detail. | 401, 403, 404. | Student must be linked to Parent. | Parent access is read-only everywhere. |
| GET | `/api/v1/parent/linked-students/{student_id}/teachers` | View Teachers associated with linked Student. | `parent_linked_student.student.view`. | `student_id`. | None. | Read-only Teacher relationship list. | 401, 403, 404. | Student must be linked to Parent. | No marketplace Teacher browsing. |
| GET | `/api/v1/parent/profile` | View authenticated Parent account context. | `parent_linked_student.parent_account.view`. | None. | None. | Parent account context. | 401, 403. | User must be Parent. | Does not expose unlinked Students. |
| PATCH | `/api/v1/parent/profile` | Update Parent's own account context where defined. | `parent_linked_student.parent_account.update`. | None. | Allowed Parent account fields only. | Updated Parent account context. | 401, 403, 422. | Must not modify linked Student records. | Parent read-only rule applies to all Student educational data. |

---

# 17. Educational Grades Endpoints

This section fulfills the requested Classes module using the canonical term **Educational Grades**.

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/teacher-workspace/educational-grades` | List Educational Grades in Teacher Workspace. | `teacher_workspace.educational_grade.view`. | Pagination, filters, sorting. | None. | Paginated Educational Grade list. | 401, 403. | Scope must be current Teacher Workspace. | Educational Grades are Teacher-created and workspace-scoped. |
| POST | `/api/v1/teacher-workspace/educational-grades` | Create Educational Grade. | `teacher_workspace.educational_grade.create`. | None. | Educational Grade name and allowed metadata. | Created Educational Grade. | 401, 403, 422. | Name is required and valid. | Teaching Subject is independent from Educational Grades. |
| GET | `/api/v1/teacher-workspace/educational-grades/{educational_grade_id}` | View Educational Grade. | `teacher_workspace.educational_grade.view`. | `educational_grade_id`. | None. | Educational Grade detail. | 401, 403, 404. | Must belong to current Teacher Workspace. | No cross-Teacher visibility. |
| PATCH | `/api/v1/teacher-workspace/educational-grades/{educational_grade_id}` | Update Educational Grade. | `teacher_workspace.educational_grade.update`. | `educational_grade_id`. | Allowed Educational Grade fields. | Updated Educational Grade. | 401, 403, 404, 422. | Must belong to current Teacher Workspace. | Update is audited. |
| POST | `/api/v1/teacher-workspace/educational-grades/{educational_grade_id}/archive` | Archive Educational Grade. | `teacher_workspace.educational_grade.archive`. | `educational_grade_id`. | Archive reason where required. | Archived Educational Grade state. | 401, 403, 404, 409. | Active record must be archivable. | Archived records are excluded from active selectors but remain historical. |
| POST | `/api/v1/teacher-workspace/educational-grades/{educational_grade_id}/restore` | Restore Educational Grade. | `teacher_workspace.educational_grade.restore`. | `educational_grade_id`. | Restore reason where required. | Restored Educational Grade state. | 401, 403, 404, 409. | Record must be archived and restorable. | Restore action is audited. |

---

# 18. Groups Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/teacher-workspace/groups` | List Groups in Teacher Workspace. | `teacher_workspace.group.view`. | Pagination, filters, sorting. | None. | Paginated Group list. | 401, 403. | Filters must reference current Teacher Workspace. | Groups are workspace-scoped. |
| POST | `/api/v1/teacher-workspace/groups` | Create Group. | `teacher_workspace.group.create`. | None. | Educational Grade reference, Name, Schedule, Price, Pricing Type. | Created Group. | 401, 403, 404, 422. | Educational Grade must be active and in workspace; Pricing Type must be Monthly or Per Lesson. | Group belongs to one Educational Grade and supports Flow B fee status. |
| GET | `/api/v1/teacher-workspace/groups/{group_id}` | View Group. | `teacher_workspace.group.view`. | `group_id`. | None. | Group detail. | 401, 403, 404. | Group must belong to current Teacher Workspace. | No cross-Teacher visibility. |
| PATCH | `/api/v1/teacher-workspace/groups/{group_id}` | Update Group. | `teacher_workspace.group.update`. | `group_id`. | Allowed Group fields including Schedule, Price, Pricing Type. | Updated Group. | 401, 403, 404, 422. | Pricing Type must be Monthly or Per Lesson. | Historical payment records must remain understandable. |
| POST | `/api/v1/teacher-workspace/groups/{group_id}/archive` | Archive Group. | `teacher_workspace.group.archive`. | `group_id`. | Archive reason where required. | Archived Group state. | 401, 403, 404, 409. | Group must be active and archivable. | Archived Group cannot receive active new assignments; history remains. |
| POST | `/api/v1/teacher-workspace/groups/{group_id}/restore` | Restore Group. | `teacher_workspace.group.restore`. | `group_id`. | Restore reason where required. | Restored Group state. | 401, 403, 404, 409. | Group must be archived and restorable. | Restore is audited. |
| GET | `/api/v1/student/groups` | View Student's current Group contexts by Teacher. | `student_account.group.view`. | Optional Teacher filter. | None. | Student's own Group contexts. | 401, 403. | Returned Groups must relate to Student's own Teacher relationships. | Student cannot change Group assignment. |
| GET | `/api/v1/parent/linked-students/{student_id}/groups` | View linked Student Group contexts. | `parent_linked_student.group.view`. | `student_id`, optional Teacher filter. | None. | Read-only linked Student Group contexts. | 401, 403, 404. | Student must be linked to Parent. | Parent cannot change Group assignment. |

---

# 19. Attendance Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/teacher-workspace/attendance` | List Attendance records. | `teacher_workspace.attendance.view`. | Pagination, filters, sorting. | None. | Paginated Attendance records. | 401, 403. | Filters must reference current Teacher Workspace. | Attendance is Teacher Workspace scoped. |
| POST | `/api/v1/teacher-workspace/attendance/sessions` | Create Attendance Session. | `teacher_workspace.attendance.record`. | None. | Group reference, session date, method context. | Created Attendance Session. | 401, 403, 404, 422. | Group must belong to current Teacher Workspace. | Attendance changes are audited. |
| POST | `/api/v1/teacher-workspace/attendance/sessions/{session_id}/dynamic-qr` | Generate daily Dynamic QR Code context. | `teacher_workspace.attendance.record`. | `session_id`. | QR date/context. | Dynamic QR attendance context. | 401, 403, 404, 409, 422. | Session must belong to current Teacher Workspace and valid date. | Dynamic QR Code is generated daily. |
| POST | `/api/v1/student/attendance/scan-dynamic-qr` | Record Student Attendance by scanning daily Dynamic QR Code. | `student_account.attendance.scan_dynamic_qr`. | None. | QR scan payload and Student context. | Attendance recorded for Student. | 401, 403, 404, 409, 422. | QR context must be valid; Student must be enrolled with Teacher. | Student scans through Web Application; Attendance not used for billing. |
| POST | `/api/v1/teacher-workspace/attendance/id-card-scan` | Record Attendance using printed ID Card scan. | `teacher_workspace.attendance.record`. | None. | ID Card scan reference and session context. | Attendance recorded. | 401, 403, 404, 409, 422. | Student relationship and session must be valid for workspace. | ID Card scan is Teacher Workspace attendance method. |
| POST | `/api/v1/teacher-workspace/attendance/manual` | Record manual Attendance. | `teacher_workspace.attendance.record`. | None. | Student reference, session context, Attendance status. | Attendance recorded. | 401, 403, 404, 409, 422. | Student must belong to Teacher Workspace relationship. | Manual entry is Teacher-side and audited. |
| PATCH | `/api/v1/teacher-workspace/attendance/{attendance_id}` | Correct or update Attendance record. | `teacher_workspace.attendance.update`. | `attendance_id`. | Updated Attendance status and reason. | Updated Attendance record. | 401, 403, 404, 409, 422. | Attendance must belong to current Teacher Workspace. | Attendance changes are audited. |
| GET | `/api/v1/student/attendance` | View Student's own Attendance. | `student_account.attendance.view`. | Pagination, filters. | None. | Student Attendance records by Teacher. | 401, 403. | Records must belong to authenticated Student. | Per-Teacher partitioning required. |
| GET | `/api/v1/parent/linked-students/{student_id}/attendance` | View linked Student Attendance. | `parent_linked_student.attendance.view`. | `student_id`, pagination, filters. | None. | Read-only linked Student Attendance. | 401, 403, 404. | Student must be linked to Parent. | Parent cannot modify Attendance. |

---

# 20. Homework Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/teacher-workspace/homework` | List Homework in Teacher Workspace. | `teacher_workspace.homework.view`. | Pagination, filters, sorting. | None. | Paginated Homework list. | 401, 403. | Scope must be current Teacher Workspace. | Homework is Teacher Workspace scoped. |
| POST | `/api/v1/teacher-workspace/homework` | Create Homework. | `teacher_workspace.homework.create`. | None. | Title, description, target context, supported format, optional files. | Created Homework. | 401, 403, 404, 422. | Format must be Text, Image, or PDF. | Video homework is out of scope. |
| GET | `/api/v1/teacher-workspace/homework/{homework_id}` | View Homework. | `teacher_workspace.homework.view`. | `homework_id`. | None. | Homework detail. | 401, 403, 404. | Homework must belong to current Teacher Workspace. | No cross-Teacher visibility. |
| PATCH | `/api/v1/teacher-workspace/homework/{homework_id}` | Update Homework. | `teacher_workspace.homework.update`. | `homework_id`. | Allowed Homework fields and supported files. | Updated Homework. | 401, 403, 404, 422. | Supported formats only. | Homework modification is audited. |
| POST | `/api/v1/teacher-workspace/homework/{homework_id}/archive` | Archive Homework. | `teacher_workspace.homework.archive`. | `homework_id`. | Archive reason where required. | Archived Homework state. | 401, 403, 404, 409. | Homework must be active and archivable. | Historical submissions remain available. |
| POST | `/api/v1/teacher-workspace/homework/{homework_id}/restore` | Restore Homework. | `teacher_workspace.homework.restore`. | `homework_id`. | Restore reason where required. | Restored Homework state. | 401, 403, 404, 409. | Homework must be archived and restorable. | Restore is audited. |
| GET | `/api/v1/teacher-workspace/homework/{homework_id}/submissions` | View Homework submissions. | `teacher_workspace.homework.view_submissions`. | `homework_id`, pagination, filters. | None. | Paginated submission list. | 401, 403, 404. | Homework must belong to current Teacher Workspace. | Teacher sees own workspace submissions only. |
| PATCH | `/api/v1/teacher-workspace/homework/submissions/{submission_id}/grade` | Grade or review Homework submission. | `teacher_workspace.homework.grade`. | `submission_id`. | Grade/review status and feedback where applicable. | Updated submission review state. | 401, 403, 404, 422. | Submission must belong to current Teacher Workspace. | Homework grading/modification is audited. |
| GET | `/api/v1/student/homework` | List Student's assigned Homework. | `student_account.homework.view`. | Pagination, filters, sorting. | None. | Student Homework list by Teacher. | 401, 403. | Homework must belong to Student's Teacher relationships. | Per-Teacher partitioning required. |
| POST | `/api/v1/student/homework/{homework_id}/submissions` | Submit Homework. | `student_account.homework.submit`. | `homework_id`. | Text answer, Image file, or PDF file. | Created Homework submission. | 401, 403, 404, 409, 422. | Homework must be assigned to Student; format must be supported. | Video homework is rejected. |
| GET | `/api/v1/parent/linked-students/{student_id}/homework` | View linked Student Homework. | `parent_linked_student.homework.view`. | `student_id`, pagination, filters. | None. | Read-only linked Student Homework list. | 401, 403, 404. | Student must be linked to Parent. | Parent cannot submit, update, or grade Homework. |

---

# 21. Lessons Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/teacher-workspace/lessons` | List Lessons in Teacher Workspace. | `teacher_workspace.lesson.view`. | Pagination, filters, sorting. | None. | Paginated Lesson list. | 401, 403. | Scope must be current Teacher Workspace. | Lessons are Teacher-owned and private. |
| POST | `/api/v1/teacher-workspace/lessons` | Create Lesson metadata. | `teacher_workspace.lesson.create`. | None. | Lesson title, description, availability context. | Created Lesson. | 401, 403, 422. | Required Lesson fields must be valid. | Lesson is not marketplace content. |
| GET | `/api/v1/teacher-workspace/lessons/{lesson_id}` | View Lesson. | `teacher_workspace.lesson.view`. | `lesson_id`. | None. | Lesson detail. | 401, 403, 404. | Lesson must belong to current Teacher Workspace. | No cross-Teacher visibility. |
| PATCH | `/api/v1/teacher-workspace/lessons/{lesson_id}` | Update Lesson metadata. | `teacher_workspace.lesson.update`. | `lesson_id`. | Allowed Lesson fields. | Updated Lesson. | 401, 403, 404, 422. | Lesson must belong to current Teacher Workspace. | Teacher-owned privacy preserved. |
| POST | `/api/v1/teacher-workspace/lessons/{lesson_id}/video` | Upload or attach Lesson video. | `teacher_workspace.lesson.upload_video`. | `lesson_id`. | Video file and allowed metadata. | Lesson video reference. | 401, 403, 404, 422. | Lesson must belong to workspace; file must be valid video. | Lesson video hosting/protection details remain PENDING. |
| POST | `/api/v1/teacher-workspace/lessons/{lesson_id}/archive` | Archive Lesson. | `teacher_workspace.lesson.archive`. | `lesson_id`. | Archive reason where required. | Archived Lesson state. | 401, 403, 404, 409. | Lesson must be active and archivable. | Archived Lessons are not active but historical references remain. |
| POST | `/api/v1/teacher-workspace/lessons/{lesson_id}/restore` | Restore Lesson. | `teacher_workspace.lesson.restore`. | `lesson_id`. | Restore reason where required. | Restored Lesson state. | 401, 403, 404, 409. | Lesson must be archived and restorable. | Restore is audited. |
| GET | `/api/v1/student/lessons` | List Lessons available to Student. | `student_account.lesson.view`. | Pagination, filters, sorting. | None. | Student Lesson list by Teacher. | 401, 403. | Lessons must belong to Student's own Teachers. | No cross-Teacher Lesson discovery. |
| GET | `/api/v1/student/lessons/{lesson_id}` | Access Lesson available to Student. | `student_account.lesson.view`. | `lesson_id`. | None. | Authorized Lesson access metadata. | 401, 403, 404. | Lesson must belong to one of Student's Teachers and be active. | Lessons are private to Teacher's own Students. |

---

# 22. Exams Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/teacher-workspace/question-banks` | List Question Banks. | `teacher_workspace.question_bank.view`. | Pagination, filters. | None. | Question Bank list. | 401, 403. | Scope must be current Teacher Workspace. | Question Bank is Teacher-owned and private. |
| POST | `/api/v1/teacher-workspace/question-banks` | Create Question Bank. | `teacher_workspace.question_bank.create`. | None. | Name and allowed metadata. | Created Question Bank. | 401, 403, 422. | Name must be valid. | No cross-Teacher sharing. |
| POST | `/api/v1/teacher-workspace/question-banks/{question_bank_id}/questions` | Create Question. | `teacher_workspace.question_bank.update`. | `question_bank_id`. | Question content, Question Type, answer definition where applicable. | Created Question. | 401, 403, 404, 422. | Type must be Multiple Choice, True/False, Essay, or Bubble Sheet. | Questions are private to Teacher Workspace. |
| PATCH | `/api/v1/teacher-workspace/questions/{question_id}` | Update Question. | `teacher_workspace.question_bank.update`. | `question_id`. | Allowed Question fields. | Updated Question. | 401, 403, 404, 422. | Question must belong to current Teacher Workspace. | Exam modification is audited. |
| POST | `/api/v1/teacher-workspace/questions/{question_id}/archive` | Archive Question. | `teacher_workspace.question_bank.archive`. | `question_id`. | Archive reason where required. | Archived Question state. | 401, 403, 404, 409. | Question must be active and archivable. | Archived Questions cannot be used as active unless restored. |
| GET | `/api/v1/teacher-workspace/exams` | List Exams. | `teacher_workspace.exam.view`. | Pagination, filters, sorting. | None. | Paginated Exam list. | 401, 403. | Scope must be current Teacher Workspace. | Exams are workspace-scoped. |
| POST | `/api/v1/teacher-workspace/exams` | Create Exam. | `teacher_workspace.exam.create`. | None. | Exam title, Question Bank reference, selected Questions, availability context. | Created Exam. | 401, 403, 404, 422. | Questions must belong to same Teacher Workspace. | Exam uses only owning Teacher's Question Bank. |
| GET | `/api/v1/teacher-workspace/exams/{exam_id}` | View Exam. | `teacher_workspace.exam.view`. | `exam_id`. | None. | Exam detail. | 401, 403, 404. | Exam must belong to current Teacher Workspace. | No cross-Teacher Exam visibility. |
| PATCH | `/api/v1/teacher-workspace/exams/{exam_id}` | Update Exam. | `teacher_workspace.exam.update`. | `exam_id`. | Allowed Exam fields. | Updated Exam. | 401, 403, 404, 422. | Exam must belong to current Teacher Workspace. | Exam modification is audited. |
| POST | `/api/v1/teacher-workspace/exams/{exam_id}/publish` | Make Exam available according to detailed rules. | `teacher_workspace.exam.publish`. | `exam_id`. | Availability context. | Published or available Exam state. | 401, 403, 404, 409, 422. | Exam must be valid and workspace-scoped. | No cross-Teacher visibility. |
| POST | `/api/v1/teacher-workspace/exams/{exam_id}/archive` | Archive Exam. | `teacher_workspace.exam.archive`. | `exam_id`. | Archive reason where required. | Archived Exam state. | 401, 403, 404, 409. | Exam must be active and archivable. | Attempts and grades remain historical. |
| POST | `/api/v1/teacher-workspace/exams/{exam_id}/restore` | Restore Exam. | `teacher_workspace.exam.restore`. | `exam_id`. | Restore reason where required. | Restored Exam state. | 401, 403, 404, 409. | Exam must be archived and restorable. | Restore is audited. |
| GET | `/api/v1/teacher-workspace/exams/{exam_id}/attempts` | View Exam attempts. | `teacher_workspace.exam.view_attempts`. | `exam_id`, pagination, filters. | None. | Workspace-scoped Exam attempts. | 401, 403, 404. | Exam must belong to current Teacher Workspace. | Attempts are workspace-scoped. |
| PATCH | `/api/v1/teacher-workspace/exam-attempts/{attempt_id}/grade` | Grade Exam attempt where applicable. | `teacher_workspace.exam.grade`. | `attempt_id`. | Grade data and feedback where applicable. | Updated grade/result. | 401, 403, 404, 422. | Attempt must belong to current Teacher Workspace. | Essay grading and Exam modifications are audited. |
| GET | `/api/v1/student/exams` | List Student Exams. | `student_account.exam.view`. | Pagination, filters, sorting. | None. | Student Exam list by Teacher. | 401, 403. | Exams must be assigned or available to Student. | Per-Teacher partitioning required. |
| POST | `/api/v1/student/exams/{exam_id}/attempts` | Start Exam attempt. | `student_account.exam.attempt`. | `exam_id`. | Attempt start context. | Created Exam attempt. | 401, 403, 404, 409, 422. | Exam must be available to Student. | Student cannot access private Question Bank outside assigned Exam. |
| POST | `/api/v1/student/exam-attempts/{attempt_id}/submit` | Submit Exam answers. | `student_account.exam.submit`. | `attempt_id`. | Answers for supported question types. | Submitted Exam attempt and available result state. | 401, 403, 404, 409, 422. | Answers must match question types. | Bubble Sheet uses electronic on-screen selection. |
| GET | `/api/v1/student/exam-attempts/{attempt_id}/result` | View Student Exam result. | `student_account.exam.view_grade`. | `attempt_id`. | None. | Student-visible result or pending status. | 401, 403, 404. | Attempt must belong to authenticated Student. | Result shown only where available. |
| GET | `/api/v1/parent/linked-students/{student_id}/exams` | View linked Student Exams and results. | `parent_linked_student.exam.view`. | `student_id`, pagination, filters. | None. | Read-only linked Student Exam information. | 401, 403, 404. | Student must be linked to Parent. | Parent cannot take Exams or modify grades. |

---

# 23. Reports Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/teacher-workspace/reports/attendance` | View Teacher Attendance report. | `teacher_workspace.report.view_attendance`. | Date, Group, Student, status filters. | None. | Attendance report data. | 401, 403, 422. | Filters must reference current Teacher Workspace. | Historical records may be included where applicable. |
| GET | `/api/v1/teacher-workspace/reports/homework` | View Teacher Homework report. | `teacher_workspace.report.view_homework`. | Date, Group, Student, status filters. | None. | Homework report data. | 401, 403, 422. | Filters must reference current Teacher Workspace. | Homework history remains available. |
| GET | `/api/v1/teacher-workspace/reports/exam-results` | View Teacher Exam results report. | `teacher_workspace.report.view_exam_results`. | Date, Group, Student, Exam filters. | None. | Exam result report data. | 401, 403, 422. | Filters must reference current Teacher Workspace. | Exam results are workspace-scoped. |
| GET | `/api/v1/teacher-workspace/reports/payments` | View Teacher Flow B payment-status report. | `teacher_workspace.report.view_payments`. | Date, Group, Student, status filters. | None. | Flow B payment-status report. | 401, 403, 422. | Filters must reference current Teacher Workspace. | Flow B must not be presented as Flow A. |
| GET | `/api/v1/teacher-workspace/reports/student-performance` | View Student performance report in Teacher Workspace. | `teacher_workspace.report.view_student_performance`. | Student, Group, date filters. | None. | Student performance report. | 401, 403, 422. | Filters must reference current Teacher Workspace. | No cross-Teacher report data. |
| GET | `/api/v1/student/reports/summary` | View Student's own summary report. | `student_account.report.view`. | Teacher and period filters. | None. | Student summary by Teacher. | 401, 403, 422. | Filters must reference Student's own Teacher relationships. | Student sees own records only. |
| GET | `/api/v1/parent/linked-students/{student_id}/reports/summary` | View linked Student summary report. | `parent_linked_student.report.view`. | `student_id`, Teacher and period filters. | None. | Read-only linked Student summary. | 401, 403, 404, 422. | Student must be linked to Parent. | Parent read-only access. |
| GET | `/api/v1/platform/reports` | View Platform-level reports. | `platform.report.view`. | Report type, date, billing cycle, Teacher filters. | None. | Platform-level report data. | 401, 403, 422. | Filters must be Platform-scoped. | Must respect pending Super Admin content visibility. |

---

# 24. Payments Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/platform/payment-status` | List Flow A payment-status records. | `platform.payment_status.view`. | Pagination, filters, sorting. | None. | Flow A payment-status list. | 401, 403. | Filters must be Platform-scoped. | Flow A is Teacher to Platform Subscription. |
| POST | `/api/v1/platform/payment-status` | Record Flow A payment status. | `platform.payment_status.record`. | None. | Teacher reference, Billing Cycle, status, amount where applicable. | Recorded Flow A status. | 401, 403, 404, 422. | Must relate to valid Flow A Subscription. | Payment is status-only; no transaction processing. |
| PATCH | `/api/v1/platform/payment-status/{payment_id}` | Update Flow A payment status. | `platform.payment_status.update`. | `payment_id`. | Updated status and note/reference where applicable. | Updated Flow A payment status. | 401, 403, 404, 422. | Payment record must be Flow A and Platform-scoped. | Payment-status changes are auditable. |
| GET | `/api/v1/teacher-workspace/payment-status` | List Flow B Student fee status records. | `teacher_workspace.payment_status.view`. | Pagination, filters, sorting. | None. | Flow B payment-status list. | 401, 403. | Filters must reference current Teacher Workspace. | Flow B is Student or Parent fees owed to Teacher. |
| POST | `/api/v1/teacher-workspace/payment-status` | Record Flow B Student fee status. | `teacher_workspace.payment_status.record`. | None. | Student reference, Group context, Pricing Type, amount, status. | Recorded Flow B status. | 401, 403, 404, 422. | Student and Group must belong to Teacher relationship; Pricing Type Monthly or Per Lesson. | Payments are handled outside Platform. |
| PATCH | `/api/v1/teacher-workspace/payment-status/{payment_id}` | Update Flow B Student fee status. | `teacher_workspace.payment_status.update`. | `payment_id`. | Updated status and note/reference where applicable. | Updated Flow B status. | 401, 403, 404, 422. | Payment record must belong to current Teacher Workspace. | Flow B must not be conflated with Flow A. |
| GET | `/api/v1/student/payment-status` | View Student's own Flow B status. | `student_account.payment_status.view`. | Teacher and period filters. | None. | Student Flow B status by Teacher. | 401, 403, 422. | Records must belong to authenticated Student. | Student cannot modify payment status. |
| GET | `/api/v1/parent/linked-students/{student_id}/payment-status` | View linked Student Flow B status. | `parent_linked_student.payment_status.view`. | `student_id`, Teacher and period filters. | None. | Read-only linked Student Flow B status. | 401, 403, 404, 422. | Student must be linked to Parent. | Parent cannot modify payment status. |

---

# 25. Subscriptions Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/platform/subscriptions` | List Flow A Teacher Subscriptions. | `platform.subscription.view`. | Pagination, filters, sorting. | None. | Paginated Subscription list. | 401, 403. | Filters must be Platform-scoped. | Subscription means Flow A only. |
| GET | `/api/v1/platform/subscriptions/{subscription_id}` | View Flow A Subscription detail. | `platform.subscription.view`. | `subscription_id`. | None. | Subscription detail. | 401, 403, 404. | Subscription must be Platform-visible. | Flow A only. |
| POST | `/api/v1/platform/subscriptions/calculate` | Calculate Billable Students and Subscription amount. | `platform.subscription.calculate_billable_students`. | None. | Teacher reference and Billing Cycle. | Billable Student count and amount. | 401, 403, 404, 422. | Billing Cycle must be calendar month; Teacher valid. | Calculation uses Enrollment duration only; Attendance and login are excluded. |
| POST | `/api/v1/platform/subscriptions/{subscription_id}/status` | Update Flow A Subscription status. | `platform.subscription.update_status`. | `subscription_id`. | New status and reason/reference. | Updated Subscription status. | 401, 403, 404, 422. | Subscription must be valid Flow A record. | Subscription changes are audited. |
| GET | `/api/v1/platform/subscriptions/{subscription_id}/history` | View Subscription history. | `platform.subscription.view_history`. | `subscription_id`, pagination. | None. | Subscription history. | 401, 403, 404. | Subscription must be visible to Super Admin. | Historical pricing and billing context remain available. |
| GET | `/api/v1/teacher-workspace/subscription-status` | View Teacher's own Flow A status where exposed. | `teacher_workspace.subscription.view_own_status`. | Billing Cycle filter. | None. | Teacher's own Subscription status. | 401, 403, 422. | Authenticated Teacher context required. | Teacher cannot manage or update Flow A status. |

---

# 26. Users Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/teacher-workspace/users/teacher-staff` | List Teacher Staff. | `teacher_workspace.teacher_staff.view`. | Pagination, filters, sorting. | None. | Paginated Teacher Staff list. | 401, 403. | Scope must be current Teacher Workspace. | Teacher Staff exist only inside creating Teacher Workspace. |
| POST | `/api/v1/teacher-workspace/users/teacher-staff` | Create Teacher Staff account. | `teacher_workspace.teacher_staff.create`. | None. | Teacher Staff user data and optional assigned permissions. | Created Teacher Staff account. | 401, 403, 409, 422. | Required identity fields; valid permission selections where defined. | Teacher Staff hold only Teacher-assigned permissions. |
| GET | `/api/v1/teacher-workspace/users/teacher-staff/{staff_id}` | View Teacher Staff account. | `teacher_workspace.teacher_staff.view`. | `staff_id`. | None. | Teacher Staff detail. | 401, 403, 404. | Staff account must belong to current Teacher Workspace. | No cross-workspace staff access. |
| PATCH | `/api/v1/teacher-workspace/users/teacher-staff/{staff_id}` | Update Teacher Staff account. | `teacher_workspace.teacher_staff.update`. | `staff_id`. | Allowed Teacher Staff fields. | Updated Teacher Staff account. | 401, 403, 404, 422. | Staff account must belong to current Teacher Workspace. | Updates are audited where important. |
| POST | `/api/v1/teacher-workspace/users/teacher-staff/{staff_id}/permissions` | Assign permissions to Teacher Staff. | `teacher_workspace.teacher_staff.assign_permission`. | `staff_id`. | Permission list. | Updated permission assignments. | 401, 403, 404, 422. | Permissions must be valid within confirmed RBAC model. | Permission changes are audited. |
| POST | `/api/v1/teacher-workspace/users/teacher-staff/{staff_id}/archive` | Archive Teacher Staff account. | `teacher_workspace.teacher_staff.archive`. | `staff_id`. | Archive reason where required. | Archived Teacher Staff state. | 401, 403, 404, 409. | Staff account must be active and in current workspace. | Historical Audit Log attribution remains. |
| POST | `/api/v1/teacher-workspace/users/teacher-staff/{staff_id}/restore` | Restore Teacher Staff account. | `teacher_workspace.teacher_staff.restore`. | `staff_id`. | Restore reason where required. | Restored Teacher Staff state. | 401, 403, 404, 409. | Staff account must be archived and restorable. | Restore is audited. |
| GET | `/api/v1/platform/users` | View Platform-level users within confirmed scope. | `platform.user.view`. | Pagination, filters, sorting. | None. | Platform-level user list. | 401, 403. | Super Admin only; visibility boundaries apply. | Platform staff roles beyond Super Admin are out of scope. |

---

# 27. Settings Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/platform/settings` | View Platform Settings. | `platform.settings.view`. | Filters where applicable. | None. | Platform Settings. | 401, 403. | User must be Super Admin. | Confirmed Platform Settings only. |
| PATCH | `/api/v1/platform/settings` | Update confirmed Platform Settings. | `platform.settings.update`. | None. | Confirmed setting values. | Updated Platform Settings. | 401, 403, 422. | Settings must be confirmed and valid. | Online payment gateway and notification settings are rejected. |
| PATCH | `/api/v1/platform/settings/pricing` | Update pricing configuration. | `platform.settings.update_pricing`. | None. | Pricing configuration according to confirmed model. | Updated pricing configuration. | 401, 403, 422. | Must not harden unresolved flat/tier decision beyond confirmed rules. | Pricing is owned by Super Admin. |
| GET | `/api/v1/teacher-workspace/settings` | View Teacher Workspace Settings. | `teacher_workspace.settings.view`. | None. | None. | Teacher Workspace Settings. | 401, 403. | Scope must be current Teacher Workspace. | Teacher can view own settings only. |
| PATCH | `/api/v1/teacher-workspace/settings` | Update Teacher Workspace Settings. | `teacher_workspace.settings.update`. | None. | Teacher profile, center information, phone numbers, address. | Updated Teacher Workspace Settings. | 401, 403, 422. | Teaching Subject updates must be rejected. | Teaching Subject cannot be changed after account creation. |
| GET | `/api/v1/student/settings` | View Student Settings. | `student_account.settings.view`. | None. | None. | Student Settings. | 401, 403. | User must be authenticated Student. | Student sees own account only. |
| PATCH | `/api/v1/student/settings` | Update Student Settings. | `student_account.settings.update`. | None. | Allowed Student account fields. | Updated Student Settings. | 401, 403, 409, 422. | Must not create duplicate Student account. | Student cannot change Group assignment. |
| GET | `/api/v1/parent/settings` | View Parent account context. | `parent_linked_student.settings.view`. | None. | None. | Parent Settings context. | 401, 403. | User must be Parent. | Linked Student educational data remains read-only. |
| PATCH | `/api/v1/parent/settings` | Update Parent account context where defined. | `parent_linked_student.settings.update`. | None. | Allowed Parent account fields only. | Updated Parent Settings. | 401, 403, 422. | Must not modify linked Student educational records. | Parent access remains read-only for linked Student data. |

---

# 28. Files Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/teacher-workspace/files` | List Teacher Workspace file references. | `teacher_workspace.file.view`. | Pagination, filters, sorting. | None. | Paginated file reference list. | 401, 403. | Files must belong to current Teacher Workspace. | File access preserves Teacher Workspace isolation. |
| POST | `/api/v1/teacher-workspace/files` | Upload Teacher Workspace file. | `teacher_workspace.file.upload`. | None. | File binary and owning resource metadata. | Created file reference. | 401, 403, 404, 422. | File type must be valid for owning resource. | Homework files limited to Image and PDF; Lesson videos are private. |
| GET | `/api/v1/files/{file_id}` | Access authorized file reference. | Role-specific file view permission. | `file_id`. | None. | Authorized file access metadata or file response. | 401, 403, 404. | File must be visible through user's role and relationship. | No cross-Teacher file access. |
| POST | `/api/v1/teacher-workspace/files/{file_id}/archive` | Archive file reference. | `teacher_workspace.file.archive`. | `file_id`. | Archive reason where required. | Archived file reference. | 401, 403, 404, 409. | File must belong to current Teacher Workspace. | Historical references remain available. |
| POST | `/api/v1/teacher-workspace/files/{file_id}/restore` | Restore file reference. | `teacher_workspace.file.restore`. | `file_id`. | Restore reason where required. | Restored file reference. | 401, 403, 404, 409. | File must be archived and restorable. | Restore is audited where applicable. |
| POST | `/api/v1/student/homework/{homework_id}/files` | Upload Student Homework submission file. | `student_account.file.upload`. | `homework_id`. | Image or PDF file for assigned Homework. | Created submission file reference. | 401, 403, 404, 422. | Homework must be assigned to Student; file must be Image or PDF. | Video homework is rejected. |

---

# 29. Notifications Endpoints

Notifications are out of scope for Version 1. Therefore, the REST API provides no Version 1 notification endpoints.

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| N/A | N/A | No notification endpoint is provided in Version 1. | `platform.notification.create` and related notification permissions are Denied. | N/A. | N/A. | N/A. | Requests to unsupported notification routes return 404 or 403 according to routing and authorization behavior. | No push, email, or SMS notification payload is accepted. | Notifications are explicitly out of scope for Version 1. |

---

# 30. Platform Administration Endpoints

| Method | URL | Purpose | Required Permission | Request Parameters | Request Body | Success Response | Error Responses | Validation Rules | Business Rules |
|---|---|---|---|---|---|---|---|---|---|
| GET | `/api/v1/platform/dashboard` | View Platform Administration Dashboard. | `platform.dashboard.view`. | Date, billing, status filters where applicable. | None. | Platform Dashboard summary. | 401, 403, 422. | User must be Super Admin. | Must not expose Teacher-private content beyond confirmed boundaries. |
| GET | `/api/v1/platform/audit-logs` | View Platform-scope Audit Logs. | `platform.audit_log.view`. | Pagination, filters, sorting. | None. | Paginated Audit Log entries. | 401, 403, 422. | User must be Super Admin; filters must be Platform-scoped. | Audit Log is append-only, immutable, and permanent. |
| GET | `/api/v1/platform/audit-logs/teacher-workspace-events` | View Teacher Workspace audit events where visibility is confirmed. | `platform.audit_log.view_teacher_workspace_events`. | Pagination, filters, sorting. | None. | Restricted audit event results. | 401, 403, 422. | Must respect pending Super Admin content visibility. | Unrestricted Teacher-private content access is not granted. |
| GET | `/api/v1/platform/billing-cycles` | List Billing Cycles. | `platform.billing_cycle.view`. | Pagination, filters, sorting. | None. | Billing Cycle list. | 401, 403. | Billing Cycles must follow calendar month rule. | New Billing Cycle begins automatically each month. |
| POST | `/api/v1/platform/billing-cycles` | Manage or create Billing Cycle record where required. | `platform.billing_cycle.manage`. | None. | Billing Cycle month context. | Billing Cycle record. | 401, 403, 409, 422. | Start date first day and end date last day of same month. | Calendar-month Billing Cycle is confirmed. |
| GET | `/api/v1/platform/pricing` | View Platform pricing. | `platform.pricing.view`. | None. | None. | Pricing configuration. | 401, 403. | User must be Super Admin. | Pricing is owned by Super Admin. |
| PATCH | `/api/v1/platform/pricing` | Update Platform pricing according to confirmed model. | `platform.pricing.update`. | None. | Price configuration. | Updated pricing configuration. | 401, 403, 422. | Must follow confirmed pricing model; unresolved details remain pending. | Historical invoices keep price as of their period. |
| GET | `/api/v1/platform/reports/global` | View global Platform reports. | `platform.global_report.view`. | Report type, date, billing, Teacher filters. | None. | Global report data. | 401, 403, 422. | User must be Super Admin; visibility boundaries apply. | Flow A and Flow B remain separate. |
| POST | `/api/v1/platform/teachers/{teacher_id}/login-as-teacher` | Not available in Version 1. | `platform.teacher.login_as_teacher` is Denied. | `teacher_id`. | N/A. | N/A. | 403 or 404. | Request is rejected. | “Login as Teacher” is not confirmed in Version 1. |

---

# 31. Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Project Context alignment | Passed — API scope follows the frozen Version 1 rules. |
| RESTful design | Passed — endpoints are resource-oriented and use explicit Archive/restore actions instead of permanent deletion. |
| Laravel 12 optimization | Passed — API is compatible with Laravel 12 and Laravel Sanctum without defining controllers or implementation internals. |
| Authentication | Passed — Laravel Sanctum is used as the confirmed authentication baseline. |
| Authorization | Passed — endpoint permissions reference the logical RBAC and Permission Matrix. |
| Canonical terminology | Passed — Educational Grade is used as the canonical term for the requested Classes module. |
| Teacher Workspace isolation | Passed — Teacher endpoints are scoped to own Teacher Workspace and cross-Teacher access is denied. |
| Student account rule | Passed — duplicate Student accounts are rejected and Student records remain per-Teacher partitioned. |
| Parent access | Passed — Parent endpoints are read-only and linked-Student scoped. |
| Flow A / Flow B separation | Passed — Subscriptions refer to Flow A only; Flow B uses payment-status endpoints. |
| Payment handling | Passed — endpoints record payment status only and do not process payments. |
| Archive policy | Passed — no hard-deletion endpoints are provided. |
| Audit Log policy | Passed — important actions remain auditable and Audit Log mutation is denied. |
| Notifications | Passed — no active notification API exists for Version 1. |
| Out-of-scope features | Passed — no native mobile, online payment gateway, marketplace, WebSocket, S3, Redis, Docker, Kubernetes, or Microservices requirement is introduced. |
| Implementation scope | Passed — no source code, Laravel controllers, database tables, or implementation details are defined. |
