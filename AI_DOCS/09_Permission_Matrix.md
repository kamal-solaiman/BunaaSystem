# 09 — Permission Matrix

## Document Scope

This document defines the complete logical permission matrix for Version 1 of the Unified Education Platform.

Source-of-truth documents reviewed before authoring:

- `AI_DOCS/00_Project_Context.md`
- `AI_DOCS/01_Project_Vision.md`
- `AI_DOCS/02_Software_Requirements.md`
- `AI_DOCS/03_System_Architecture.md`
- `AI_DOCS/06_Database_Design.md`
- `AI_DOCS/07_Data_Dictionary.md`
- `AI_DOCS/08_RBAC.md`

This document uses Markdown tables only for the permission matrix. It does not define implementation code, APIs, or database tables.

## Matrix Values

| Value | Meaning |
|---|---|
| Allowed | The role may perform the permission within its confirmed scope. |
| Denied | The role may not perform the permission in Version 1. |
| Conditional | The role may perform the permission only when the note condition is satisfied. |

## Global Permission Principles

1. Super Admin permissions are Platform-scoped and do not make the Super Admin a Teacher inside Teacher Workspaces.
2. Teacher permissions are limited to the Teacher's own Teacher Workspace.
3. Student permissions are limited to the Student's own account and the Student's own per-Teacher records.
4. Parent permissions are limited to linked Students and are read-only everywhere.
5. Teacher Staff are not shown as a separate matrix column because the requested columns are Super Admin, Teacher, Student, and Parent. Teacher Staff access is always Conditional on Teacher-assigned permissions inside the creating Teacher Workspace.
6. No permission grants hard deletion. Archive and restore are the only lifecycle permissions where applicable.
7. Notifications are out of scope for Version 1; notification permissions are denied for all roles.
8. Super Admin content visibility into Teacher-private content remains PENDING and is therefore marked Conditional where visibility could exist only within confirmed future boundaries.
9. Teacher Staff permission granularity remains PENDING; this matrix defines the logical system permission catalog without finalizing Teacher Staff presets or detailed staff granularity.

---

# 1. Dashboard Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `platform.dashboard.view` | Allowed | Denied | Denied | Denied | Super Admin may view Platform-level Dashboard only. |
| `teacher_workspace.dashboard.view` | Denied | Allowed | Denied | Denied | Teacher may view only own Teacher Workspace Dashboard. |
| `student_account.dashboard.view` | Denied | Denied | Allowed | Denied | Student may view own Dashboard only. |
| `parent_linked_student.dashboard.view` | Denied | Denied | Denied | Allowed | Parent may view linked-Student Dashboard summaries only. |
| `teacher_workspace.dashboard.view_history` | Denied | Conditional | Denied | Denied | Allowed for Teacher only when historical Dashboard/report context is available in own Teacher Workspace. |
| `platform.dashboard.view_teacher_private_content` | Conditional | Denied | Denied | Denied | Conditional only because Super Admin content visibility is PENDING; unrestricted Teacher-private content access is not granted. |

---

# 2. Educational Grades Permissions

The Product Owner requested a permission group named “Classes”. The canonical Project Context term is **Educational Grade**, and this matrix uses the canonical term to avoid conflicting terminology.

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `teacher_workspace.educational_grade.view` | Denied | Allowed | Denied | Denied | Teacher may view Educational Grades only in own Teacher Workspace. |
| `teacher_workspace.educational_grade.create` | Denied | Allowed | Denied | Denied | Teacher-created academic structure only. |
| `teacher_workspace.educational_grade.update` | Denied | Allowed | Denied | Denied | Teaching Subject remains separate and cannot be changed through Educational Grades. |
| `teacher_workspace.educational_grade.archive` | Denied | Allowed | Denied | Denied | Archive replaces permanent deletion. |
| `teacher_workspace.educational_grade.restore` | Denied | Allowed | Denied | Denied | Restore is audited and scoped to own Teacher Workspace. |
| `teacher_workspace.educational_grade.view_history` | Denied | Allowed | Denied | Denied | Historical records remain available and must preserve workspace scope. |
| `parent_linked_student.educational_grade.view` | Denied | Denied | Denied | Conditional | Parent may see Educational Grade context only when it is part of linked Student read-only information. |
| `student_account.educational_grade.view` | Denied | Denied | Conditional | Denied | Student may see Educational Grade context only where it is part of the Student's own Teacher relationship. |

---

# 3. Groups Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `teacher_workspace.group.view` | Denied | Allowed | Denied | Denied | Teacher may view Groups in own Teacher Workspace only. |
| `teacher_workspace.group.create` | Denied | Allowed | Denied | Denied | Group belongs to one Educational Grade in the Teacher Workspace. |
| `teacher_workspace.group.update` | Denied | Allowed | Denied | Denied | Includes Name, Schedule, Price, and Pricing Type within confirmed rules. |
| `teacher_workspace.group.archive` | Denied | Allowed | Denied | Denied | Archived Groups are removed from active assignment lists but remain historical. |
| `teacher_workspace.group.restore` | Denied | Allowed | Denied | Denied | Restore must preserve historical relationships and be audited. |
| `teacher_workspace.group.view_history` | Denied | Allowed | Denied | Denied | Teacher may view historical Group context in own Teacher Workspace. |
| `teacher_workspace.group.assign_student` | Denied | Allowed | Denied | Denied | Must enforce one Group per Student per Teacher. |
| `teacher_workspace.group.move_student` | Denied | Allowed | Denied | Denied | Must preserve historical Attendance, Homework, Exams, and grades. |
| `student_account.group.view` | Denied | Denied | Conditional | Denied | Student may view own current Group context per Teacher relationship. |
| `student_account.group.update` | Denied | Denied | Denied | Denied | Student cannot move self between Groups. |
| `parent_linked_student.group.view` | Denied | Denied | Denied | Conditional | Parent may view linked Student Group context only in read-only views. |
| `parent_linked_student.group.update` | Denied | Denied | Denied | Denied | Parent cannot change Group assignment. |

---

# 4. Students Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `teacher_workspace.student.view` | Denied | Allowed | Denied | Denied | Teacher may view Student records relevant to own Teacher Workspace only. |
| `teacher_workspace.student.search` | Denied | Allowed | Denied | Denied | Search is for Teacher Workspace management and must not expose other Teacher-private records. |
| `teacher_workspace.student.create` | Denied | Allowed | Denied | Denied | Teacher may manually create Student account only without duplicates. |
| `teacher_workspace.student.assign_existing` | Denied | Allowed | Denied | Denied | Existing Student assignment must not expose another Teacher's private data. |
| `teacher_workspace.student.update` | Denied | Allowed | Denied | Denied | Limited to Teacher Workspace relationship and allowed Student management data. |
| `teacher_workspace.student.archive` | Denied | Allowed | Denied | Denied | Archive applies to the relevant record or relationship; historical data remains. |
| `teacher_workspace.student.restore` | Denied | Allowed | Denied | Denied | Restore is scoped and audited. |
| `teacher_workspace.student.view_history` | Denied | Allowed | Denied | Denied | Teacher may view history for own Teacher Workspace only. |
| `student_account.student.view` | Denied | Denied | Allowed | Denied | Student may view own account and own per-Teacher records. |
| `student_account.student.update` | Denied | Denied | Conditional | Denied | Student may update own account settings only according to confirmed requirements. |
| `student_account.student.activate` | Denied | Denied | Conditional | Denied | Student may activate own Teacher-created account. |
| `student_account.student.create_duplicate` | Denied | Denied | Denied | Denied | Duplicate Student accounts are never allowed. |
| `parent_linked_student.student.view` | Denied | Denied | Denied | Allowed | Parent may view linked Students only. |
| `parent_linked_student.student.update` | Denied | Denied | Denied | Denied | Parent read-only rule. |
| `platform.student.view_private_workspace_records` | Conditional | Denied | Denied | Denied | Conditional only because Super Admin content visibility is PENDING; unrestricted private Student workspace records are not granted. |

---

# 5. Attendance Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `teacher_workspace.attendance.view` | Denied | Allowed | Denied | Denied | Teacher may view Attendance in own Teacher Workspace. |
| `teacher_workspace.attendance.record` | Denied | Allowed | Denied | Denied | Includes Teacher-side manual entry and authorized ID Card scan handling. |
| `teacher_workspace.attendance.update` | Denied | Allowed | Denied | Denied | Attendance changes must be audited. |
| `teacher_workspace.attendance.archive` | Denied | Conditional | Denied | Denied | Allowed only where an Attendance-related record is archivable; history remains. |
| `teacher_workspace.attendance.restore` | Denied | Conditional | Denied | Denied | Allowed only for authorized restoration of archived Attendance-related records. |
| `teacher_workspace.attendance.view_history` | Denied | Allowed | Denied | Denied | Attendance history remains available after Group movement. |
| `student_account.attendance.view` | Denied | Denied | Conditional | Denied | Student may view own Attendance where available in own per-Teacher records. |
| `student_account.attendance.scan_dynamic_qr` | Denied | Denied | Allowed | Denied | Student scans daily Dynamic QR Code through Web Application. |
| `student_account.attendance.scan_id_card` | Denied | Denied | Denied | Denied | ID Card scanning is scanner-side/Teacher Workspace operation, not a Student self-permission. |
| `student_account.attendance.update` | Denied | Denied | Denied | Denied | Student cannot manually modify Attendance records. |
| `parent_linked_student.attendance.view` | Denied | Denied | Denied | Allowed | Parent may view linked Student Attendance read-only. |
| `parent_linked_student.attendance.record` | Denied | Denied | Denied | Denied | Parent cannot record Attendance. |
| `parent_linked_student.attendance.update` | Denied | Denied | Denied | Denied | Parent cannot modify or correct Attendance. |
| `platform.attendance.view_report_summary` | Conditional | Denied | Denied | Denied | Conditional on confirmed Super Admin report visibility boundaries; not Teacher-private browsing. |

---

# 6. Homework Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `teacher_workspace.homework.view` | Denied | Allowed | Denied | Denied | Teacher may view Homework in own Teacher Workspace. |
| `teacher_workspace.homework.create` | Denied | Allowed | Denied | Denied | Homework supports Text, Image, and PDF only. |
| `teacher_workspace.homework.update` | Denied | Allowed | Denied | Denied | Homework modifications must be audited. |
| `teacher_workspace.homework.archive` | Denied | Allowed | Denied | Denied | Archive replaces permanent deletion. |
| `teacher_workspace.homework.restore` | Denied | Allowed | Denied | Denied | Restore is audited. |
| `teacher_workspace.homework.grade` | Denied | Allowed | Denied | Denied | Teacher may review or grade where applicable. |
| `teacher_workspace.homework.view_submissions` | Denied | Allowed | Denied | Denied | Submissions are scoped to Teacher Workspace. |
| `teacher_workspace.homework.view_history` | Denied | Allowed | Denied | Denied | Historical Homework remains available. |
| `student_account.homework.view` | Denied | Denied | Allowed | Denied | Student may view Homework assigned to the Student. |
| `student_account.homework.submit` | Denied | Denied | Conditional | Denied | Allowed only for assigned Homework and supported formats. |
| `student_account.homework.update_submission` | Denied | Denied | Conditional | Denied | Allowed only where later detailed requirements permit modification of own submission. |
| `student_account.homework.grade` | Denied | Denied | Denied | Denied | Student cannot grade Homework. |
| `parent_linked_student.homework.view` | Denied | Denied | Denied | Allowed | Parent may view linked Student Homework read-only. |
| `parent_linked_student.homework.submit` | Denied | Denied | Denied | Denied | Parent cannot submit Homework for Student. |
| `parent_linked_student.homework.update` | Denied | Denied | Denied | Denied | Parent cannot modify Homework. |
| `platform.homework.view_private_content` | Conditional | Denied | Denied | Denied | Conditional only because Super Admin content visibility is PENDING; unrestricted Homework content access is not granted. |
| `teacher_workspace.homework.submit_video` | Denied | Denied | Denied | Denied | Video homework is out of scope for Version 1. |

---

# 7. Lessons Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `teacher_workspace.lesson.view` | Denied | Allowed | Denied | Denied | Teacher may view own Lessons in own Teacher Workspace. |
| `teacher_workspace.lesson.create` | Denied | Allowed | Denied | Denied | Teacher may create private Lesson metadata/content for own Students. |
| `teacher_workspace.lesson.update` | Denied | Allowed | Denied | Denied | Lesson remains Teacher-owned and private. |
| `teacher_workspace.lesson.archive` | Denied | Allowed | Denied | Denied | Archived Lessons are not active but remain historical. |
| `teacher_workspace.lesson.restore` | Denied | Allowed | Denied | Denied | Restore is audited and scoped. |
| `teacher_workspace.lesson.upload_video` | Denied | Allowed | Denied | Denied | Lesson video hosting/protection details remain PENDING. |
| `teacher_workspace.lesson.view_history` | Denied | Allowed | Denied | Denied | Historical Lesson references remain available where applicable. |
| `student_account.lesson.view` | Denied | Denied | Conditional | Denied | Student may view Lessons only from the Student's own Teachers. |
| `student_account.lesson.browse_marketplace` | Denied | Denied | Denied | Denied | Marketplace browsing is out of scope. |
| `parent_linked_student.lesson.view` | Denied | Denied | Denied | Denied | Parent Panel does not include Lessons in confirmed V1 navigation. |
| `platform.lesson.view_private_content` | Conditional | Denied | Denied | Denied | Conditional only because Super Admin content visibility is PENDING; no unrestricted access is granted. |
| `platform.lesson.publish_marketplace` | Denied | Denied | Denied | Denied | The Platform is not a marketplace and Teachers do not sell courses. |

---

# 8. Exams Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `teacher_workspace.question_bank.view` | Denied | Allowed | Denied | Denied | Teacher may view only own private Question Bank. |
| `teacher_workspace.question_bank.create` | Denied | Allowed | Denied | Denied | Question Bank is Teacher-owned and private. |
| `teacher_workspace.question_bank.update` | Denied | Allowed | Denied | Denied | Question and Exam modifications must be audited. |
| `teacher_workspace.question_bank.archive` | Denied | Allowed | Denied | Denied | Archive replaces permanent deletion. |
| `teacher_workspace.question_bank.restore` | Denied | Allowed | Denied | Denied | Restore is audited. |
| `teacher_workspace.exam.view` | Denied | Allowed | Denied | Denied | Teacher may view own Exams. |
| `teacher_workspace.exam.create` | Denied | Allowed | Denied | Denied | Exams use only own Teacher Workspace Question Bank. |
| `teacher_workspace.exam.update` | Denied | Allowed | Denied | Denied | Includes exam definition changes within own workspace. |
| `teacher_workspace.exam.archive` | Denied | Allowed | Denied | Denied | Attempts and grades remain historical. |
| `teacher_workspace.exam.restore` | Denied | Allowed | Denied | Denied | Restore is audited. |
| `teacher_workspace.exam.publish` | Denied | Conditional | Denied | Denied | Conditional on detailed requirements for making Exams available without cross-Teacher visibility. |
| `teacher_workspace.exam.grade` | Denied | Allowed | Denied | Denied | Includes Essay grading where applicable. |
| `teacher_workspace.exam.view_attempts` | Denied | Allowed | Denied | Denied | Teacher may view attempts scoped to own Teacher Workspace. |
| `teacher_workspace.exam.view_history` | Denied | Allowed | Denied | Denied | Historical attempts and grades remain available. |
| `student_account.exam.view` | Denied | Denied | Allowed | Denied | Student may view assigned or available Exams. |
| `student_account.exam.attempt` | Denied | Denied | Conditional | Denied | Allowed only for Exams assigned or available to the Student. |
| `student_account.exam.submit` | Denied | Denied | Conditional | Denied | Allowed only for the Student's own Exam attempt. |
| `student_account.exam.view_grade` | Denied | Denied | Conditional | Denied | Allowed where grade/result is available to the Student. |
| `student_account.question_bank.view` | Denied | Denied | Denied | Denied | Student cannot browse Teacher private Question Bank. |
| `parent_linked_student.exam.view` | Denied | Denied | Denied | Allowed | Parent may view linked Student Exam information read-only. |
| `parent_linked_student.exam.view_grade` | Denied | Denied | Denied | Conditional | Allowed where grades are available for linked Student. |
| `parent_linked_student.exam.attempt` | Denied | Denied | Denied | Denied | Parent cannot take Exams for Student. |
| `parent_linked_student.exam.update` | Denied | Denied | Denied | Denied | Parent cannot modify Exams or grades. |
| `platform.question_bank.view_private_content` | Conditional | Denied | Denied | Denied | Conditional only because Super Admin content visibility is PENDING; private Question Bank browsing is not granted. |
| `platform.exam.view_private_definition` | Conditional | Denied | Denied | Denied | Conditional only within future confirmed Super Admin visibility boundaries. |

---

# 9. Reports Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `platform.report.view` | Allowed | Denied | Denied | Denied | Super Admin may view Platform-level global reports within visibility boundaries. |
| `platform.report.view_teacher_aggregate` | Conditional | Denied | Denied | Denied | Conditional on confirmed content visibility; aggregate/metadata default must not expose private content. |
| `teacher_workspace.report.view` | Denied | Allowed | Denied | Denied | Teacher may view own Teacher Workspace reports. |
| `teacher_workspace.report.view_attendance` | Denied | Allowed | Denied | Denied | Scoped to own Teacher Workspace. |
| `teacher_workspace.report.view_homework` | Denied | Allowed | Denied | Denied | Scoped to own Teacher Workspace. |
| `teacher_workspace.report.view_exam_results` | Denied | Allowed | Denied | Denied | Scoped to own Teacher Workspace. |
| `teacher_workspace.report.view_payments` | Denied | Allowed | Denied | Denied | Primarily Flow B status; Flow A remains separate. |
| `teacher_workspace.report.view_student_performance` | Denied | Allowed | Denied | Denied | Scoped to own Teacher Workspace. |
| `student_account.report.view` | Denied | Denied | Conditional | Denied | Student may view own per-Teacher status, results, or summaries where available. |
| `parent_linked_student.report.view` | Denied | Denied | Denied | Conditional | Parent may view read-only linked Student summaries where available. |
| `report.view_archived_records` | Conditional | Conditional | Conditional | Conditional | Allowed only in permitted historical/report contexts and with archived records clearly indicated. |

---

# 10. Payments Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `platform.payment_status.view` | Allowed | Denied | Denied | Denied | Super Admin may view Flow A payment status at Platform level. |
| `platform.payment_status.record` | Allowed | Denied | Denied | Denied | Super Admin may record Flow A status only. |
| `platform.payment_status.update` | Allowed | Denied | Denied | Denied | Status-only; no transaction processing. |
| `platform.payment_status.view_history` | Allowed | Denied | Denied | Denied | Historical Flow A payment-status records remain available. |
| `teacher_workspace.payment_status.view` | Denied | Allowed | Denied | Denied | Teacher may view Flow B status in own Teacher Workspace. |
| `teacher_workspace.payment_status.record` | Denied | Allowed | Denied | Denied | Teacher may record Flow B Student fee status. |
| `teacher_workspace.payment_status.update` | Denied | Allowed | Denied | Denied | Status-only; no transaction processing. |
| `teacher_workspace.payment_status.view_history` | Denied | Allowed | Denied | Denied | Historical Flow B records remain available. |
| `student_account.payment_status.view` | Denied | Denied | Conditional | Denied | Student may view own per-Teacher Flow B status. |
| `student_account.payment_status.update` | Denied | Denied | Denied | Denied | Student cannot modify payment status unless a future confirmed requirement allows it. |
| `parent_linked_student.payment_status.view` | Denied | Denied | Denied | Allowed | Parent may view linked Student Flow B status read-only. |
| `parent_linked_student.payment_status.update` | Denied | Denied | Denied | Denied | Parent cannot modify payment status. |
| `payment_status.process_online_payment` | Denied | Denied | Denied | Denied | Online payment processing is out of scope for Version 1. |
| `payment_status.configure_gateway` | Denied | Denied | Denied | Denied | Online payment gateways are out of scope for Version 1. |
| `payment_status.conflate_flow_a_flow_b` | Denied | Denied | Denied | Denied | Flow A and Flow B must never be conflated. |

---

# 11. Subscriptions Permissions

In this project, **Subscription** means Flow A: Teacher to Platform monthly Subscription. Student and Parent payment-status visibility belongs to Flow B and is covered under Payments.

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `platform.subscription.view` | Allowed | Denied | Denied | Denied | Super Admin may view Teacher Platform Subscriptions. |
| `platform.subscription.calculate_billable_students` | Allowed | Denied | Denied | Denied | Calculation uses Enrollment duration only, not Attendance or login. |
| `platform.subscription.create_cycle` | Allowed | Denied | Denied | Denied | Billing Cycle is calendar month and begins automatically. |
| `platform.subscription.update_status` | Allowed | Denied | Denied | Denied | Status-only; Subscription changes must be audited. |
| `platform.subscription.record_payment_status` | Allowed | Denied | Denied | Denied | Payment is handled outside Platform in Version 1. |
| `platform.subscription.view_history` | Allowed | Denied | Denied | Denied | Historical Subscription records and pricing context remain available. |
| `teacher_workspace.subscription.view_own_status` | Denied | Conditional | Denied | Denied | Teacher may view own Flow A status where exposed to the Teacher; Teacher cannot manage Platform billing. |
| `teacher_workspace.subscription.update_status` | Denied | Denied | Denied | Denied | Teacher cannot update own Flow A Subscription status. |
| `student_account.subscription.view` | Denied | Denied | Denied | Denied | Student-facing “Subscriptions” area represents Flow B status, not Flow A Subscription. |
| `parent_linked_student.subscription.view` | Denied | Denied | Denied | Denied | Parent Payments represent Flow B status, not Flow A Subscription. |
| `platform.subscription.enforce_non_payment` | Conditional | Denied | Denied | Denied | Non-payment enforcement remains PENDING and must not be silently applied. |
| `platform.subscription.process_online_payment` | Denied | Denied | Denied | Denied | Online payment processing is out of scope. |

---

# 12. Users Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `teacher_workspace.teacher_staff.view` | Denied | Allowed | Denied | Denied | Teacher may view Teacher Staff in own Teacher Workspace. |
| `teacher_workspace.teacher_staff.create` | Denied | Allowed | Denied | Denied | Teacher Staff exist only inside creating Teacher Workspace. |
| `teacher_workspace.teacher_staff.update` | Denied | Allowed | Denied | Denied | Teacher may update staff account information in own workspace. |
| `teacher_workspace.teacher_staff.archive` | Denied | Allowed | Denied | Denied | Archive replaces permanent deletion. |
| `teacher_workspace.teacher_staff.restore` | Denied | Allowed | Denied | Denied | Restore is audited. |
| `teacher_workspace.teacher_staff.assign_permission` | Denied | Allowed | Denied | Denied | Permission changes must be audited. |
| `teacher_workspace.teacher_staff.view_history` | Denied | Allowed | Denied | Denied | Historical staff attribution remains available. |
| `teacher_workspace.teacher_staff.self_assign_permission` | Denied | Denied | Denied | Denied | Teacher Staff cannot grant themselves permissions. |
| `platform.user.view` | Allowed | Denied | Denied | Denied | Super Admin may view Platform-level user administration only within confirmed scope. |
| `platform.user.create_platform_staff` | Denied | Denied | Denied | Denied | Platform staff roles such as Support, Sales, and Accountant are out of scope for Version 1. |
| `student_account.settings.view` | Denied | Denied | Allowed | Denied | Student may view own account settings. |
| `student_account.settings.update` | Denied | Denied | Conditional | Denied | Student may update own account settings according to confirmed requirements. |
| `parent_linked_student.parent_account.view` | Denied | Denied | Denied | Conditional | Parent may access own Parent account context where detailed requirements define it. |
| `parent_linked_student.parent_account.update` | Denied | Denied | Denied | Conditional | Conditional only for own Parent account context; never linked Student educational data. |

---

# 13. Settings Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `platform.settings.view` | Allowed | Denied | Denied | Denied | Super Admin may view confirmed Platform Settings. |
| `platform.settings.update` | Allowed | Denied | Denied | Denied | Super Admin may update confirmed Platform Settings only. |
| `platform.settings.update_pricing` | Conditional | Denied | Denied | Denied | Pricing is owned by Super Admin; flat versus tiers remains PENDING. |
| `platform.settings.update_localization` | Conditional | Denied | Denied | Denied | Arabic (default) and English (fully supported), with automatic RTL/LTR, are confirmed; timezone, currency, and target market remain PENDING. |
| `platform.settings.configure_payment_gateway` | Denied | Denied | Denied | Denied | Payment gateways are out of scope for Version 1. |
| `platform.settings.configure_notifications` | Denied | Denied | Denied | Denied | Notifications are out of scope for Version 1. |
| `teacher_workspace.settings.view` | Denied | Allowed | Denied | Denied | Teacher may view own Teacher Workspace Settings. |
| `teacher_workspace.settings.update` | Denied | Allowed | Denied | Denied | Teacher may update profile, center information, phone numbers, and address. |
| `teacher_workspace.settings.update_teaching_subject` | Denied | Denied | Denied | Denied | Teaching Subject cannot be changed after account creation. |
| `student_account.settings.view` | Denied | Denied | Allowed | Denied | Student may view own Settings. |
| `student_account.settings.update` | Denied | Denied | Conditional | Denied | Conditional on confirmed account-setting fields and duplicate prevention. |
| `parent_linked_student.settings.view` | Denied | Denied | Denied | Conditional | Parent may access own account context if later detailed requirements define it. |
| `parent_linked_student.settings.update` | Denied | Denied | Denied | Conditional | Conditional only for own Parent account context; linked Student records remain read-only. |

---

# 14. Files Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `teacher_workspace.file.view` | Denied | Allowed | Denied | Denied | Teacher may view files belonging to own Teacher Workspace. |
| `teacher_workspace.file.upload` | Denied | Allowed | Denied | Denied | Includes Lesson videos and Homework-supported files within confirmed scope. |
| `teacher_workspace.file.update` | Denied | Allowed | Denied | Denied | File references must preserve ownership and history. |
| `teacher_workspace.file.archive` | Denied | Allowed | Denied | Denied | Archived file references remain available for historical records. |
| `teacher_workspace.file.restore` | Denied | Allowed | Denied | Denied | Restore is audited where applicable. |
| `teacher_workspace.file.view_history` | Denied | Allowed | Denied | Denied | Historical file references remain scoped to Teacher Workspace. |
| `student_account.file.view` | Denied | Denied | Conditional | Denied | Student may view files only through own assigned Homework, submissions, Exams, or Lessons. |
| `student_account.file.upload` | Denied | Denied | Conditional | Denied | Student may upload only supported Homework submission files where allowed. |
| `student_account.file.upload_video_homework` | Denied | Denied | Denied | Denied | Video homework is out of scope. |
| `parent_linked_student.file.view` | Denied | Denied | Denied | Conditional | Parent may view linked Student files only where read-only access is part of linked Student records. |
| `parent_linked_student.file.upload` | Denied | Denied | Denied | Denied | Parent cannot upload Student Homework or Teacher files. |
| `platform.file.view_private_teacher_file` | Conditional | Denied | Denied | Denied | Conditional only because Super Admin content visibility is PENDING; unrestricted private file access is not granted. |
| `file.use_s3_storage` | Denied | Denied | Denied | Denied | S3 Storage is not required for Version 1. |

---

# 15. Notifications Permissions

Notifications are explicitly out of scope for Version 1. SMTP is part of the technical architecture baseline but does not create Version 1 notification features.

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `platform.notification.view` | Denied | Denied | Denied | Denied | Notification is not a Version 1 product entity. |
| `platform.notification.create` | Denied | Denied | Denied | Denied | Push, email, and SMS notifications are out of scope. |
| `platform.notification.update` | Denied | Denied | Denied | Denied | Notification management is out of scope. |
| `platform.notification.archive` | Denied | Denied | Denied | Denied | No Version 1 Notification records exist to archive. |
| `teacher_workspace.notification.view` | Denied | Denied | Denied | Denied | Teacher notifications are out of scope. |
| `teacher_workspace.notification.create` | Denied | Denied | Denied | Denied | Teacher notification sending is out of scope. |
| `student_account.notification.view` | Denied | Denied | Denied | Denied | Student notifications are out of scope. |
| `parent_linked_student.notification.view` | Denied | Denied | Denied | Denied | Parent notifications are out of scope. |
| `notification.send_push` | Denied | Denied | Denied | Denied | Push notifications are out of scope. |
| `notification.send_email` | Denied | Denied | Denied | Denied | Email notifications as product notifications are out of scope. |
| `notification.send_sms` | Denied | Denied | Denied | Denied | SMS notifications are out of scope. |

---

# 16. Audit Logs Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `platform.audit_log.view` | Allowed | Denied | Denied | Denied | Super Admin may view Platform-scope Audit Logs within confirmed visibility boundaries. |
| `platform.audit_log.view_teacher_workspace_events` | Conditional | Denied | Denied | Denied | Conditional on Super Admin content visibility resolution; must not expose private content by default. |
| `platform.audit_log.create` | Conditional | Conditional | Conditional | Conditional | Audit entries are created by the system for important actions by all roles. |
| `platform.audit_log.update` | Denied | Denied | Denied | Denied | Audit Log is immutable. |
| `platform.audit_log.archive` | Denied | Denied | Denied | Denied | Audit Log entries are permanent and not archived. |
| `platform.audit_log.restore` | Denied | Denied | Denied | Denied | Audit Log entries are never archived, so restore does not apply. |
| `teacher_workspace.audit_log.view` | Denied | Conditional | Denied | Denied | Teacher visibility is allowed only for own Teacher Workspace Audit Log where permitted by requirements. |
| `student_account.audit_log.view` | Denied | Denied | Denied | Denied | Student Audit Log visibility is not a confirmed Version 1 surface. |
| `parent_linked_student.audit_log.view` | Denied | Denied | Denied | Denied | Parent Audit Log visibility is not a confirmed Version 1 surface. |
| `audit_log.view_history` | Allowed | Conditional | Denied | Denied | Super Admin Platform history allowed; Teacher workspace history conditional on permitted workspace visibility. |

---

# 17. Platform Management Permissions

| Permission | Super Admin | Teacher | Student | Parent | Notes |
|---|---|---|---|---|---|
| `platform.teacher.view` | Allowed | Denied | Denied | Denied | Super Admin manages Teacher accounts at Platform level. |
| `platform.teacher.create` | Allowed | Denied | Denied | Denied | Teacher account creation is Platform-level administration. |
| `platform.teacher.update` | Allowed | Denied | Denied | Denied | Teaching Subject cannot be changed after account creation. |
| `platform.teacher.archive` | Allowed | Denied | Denied | Denied | Archive replaces permanent deletion. |
| `platform.teacher.restore` | Allowed | Denied | Denied | Denied | Restore is audited. |
| `platform.teacher.view_history` | Allowed | Denied | Denied | Denied | Historical Teacher account records remain available. |
| `platform.teacher.login_as_teacher` | Denied | Denied | Denied | Denied | “Login as Teacher” is not confirmed in Version 1. |
| `platform.teacher.update_teaching_subject` | Denied | Denied | Denied | Denied | Teaching Subject cannot be changed after account creation. |
| `platform.pricing.view` | Allowed | Denied | Denied | Denied | Pricing is owned by Super Admin. |
| `platform.pricing.update` | Conditional | Denied | Denied | Denied | Conditional because flat price versus tiers remains PENDING. |
| `platform.billing_cycle.view` | Allowed | Denied | Denied | Denied | Billing Cycle is calendar month. |
| `platform.billing_cycle.manage` | Allowed | Denied | Denied | Denied | New Billing Cycle begins automatically. |
| `platform.global_report.view` | Allowed | Denied | Denied | Denied | Must respect pending content-visibility boundary. |
| `platform.platform_staff.create` | Denied | Denied | Denied | Denied | Platform staff roles beyond Super Admin are out of scope for Version 1. |
| `platform.marketplace.manage` | Denied | Denied | Denied | Denied | Marketplace behavior is out of scope. |
| `platform.course_discovery.manage` | Denied | Denied | Denied | Denied | Course discovery/browsing across Teachers is out of scope. |
| `platform.payment_gateway.manage` | Denied | Denied | Denied | Denied | Online payment gateways are out of scope. |
| `platform.native_mobile.manage` | Denied | Denied | Denied | Denied | Native mobile applications are out of scope for Version 1. |
| `platform.hard_delete` | Denied | Denied | Denied | Denied | Permanent deletion is not allowed anywhere. |

---

# 18. Conditional Permission Notes

| Area | Conditional Permission Rule |
|---|---|
| Teacher Workspace permissions | A Teacher is allowed only inside the Teacher's own Teacher Workspace. |
| Student permissions | A Student is allowed only for the Student's own account and own per-Teacher records. |
| Parent permissions | A Parent is allowed only for linked Students and read-only access. |
| Super Admin reports and content visibility | Super Admin access to Teacher-private content remains PENDING; until resolved, only confirmed Platform-level administration and non-invasive reporting are allowed. |
| Teacher Staff | Teacher Staff access is always conditional on explicit Teacher-assigned permissions inside the creating Teacher Workspace. |
| Archive and restore | Allowed only for authorized roles and only where the resource supports Archive; hard deletion is never allowed. |
| Historical records | Historical data remains available, but access must preserve role, ownership, and Teacher Workspace boundaries. |
| Flow A and Flow B | Flow A Subscription permissions are Platform-level; Flow B Student fee status permissions are Teacher Workspace, Student, or Parent linked-Student scoped. |
| Notifications | No conditional notification permission exists in Version 1 because notifications are out of scope. |
| Non-payment enforcement | Any enforcement behavior remains PENDING and cannot be treated as an active permission. |

---

# 19. Consistency Review

A consistency review was performed before saving this document against the official source documents.

| Review Area | Result |
|---|---|
| Project Context alignment | Passed — permission boundaries follow the frozen Version 1 rules. |
| Canonical terminology | Passed — Educational Grade is used as the canonical term; the requested Classes group is mapped to Educational Grades. |
| Role coverage | Passed — matrix columns cover Super Admin, Teacher, Student, and Parent as requested; Teacher Staff is documented as conditional Teacher-assigned access. |
| Teacher Workspace isolation | Passed — Teacher permissions are limited to own Teacher Workspace; cross-Teacher access is denied. |
| Student scope | Passed — Student permissions are limited to own account and own per-Teacher records. |
| Parent scope | Passed — Parent permissions are read-only and linked-Student scoped. |
| Super Admin scope | Passed — Super Admin is Platform-scoped and pending content visibility is not silently expanded. |
| Flow A / Flow B separation | Passed — Subscription and payment-status permissions remain separate. |
| Archive policy | Passed — hard deletion is denied everywhere; Archive and restore are used where applicable. |
| Audit Log policy | Passed — important actions remain auditable; Audit Log modification is denied. |
| Notifications | Passed — notification permissions are denied because notifications are out of scope for Version 1. |
| Out-of-scope features | Passed — native mobile, online payment gateways, marketplace behavior, and hard deletion are denied. |
| Implementation scope | Passed — no implementation code, APIs, or database tables are defined. |
