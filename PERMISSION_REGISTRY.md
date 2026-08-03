# Permission Registry — Phase 45

**Status: PERMISSION REGISTRY FROZEN.**

Every permission defined by `AI_DOCS/09_Permission_Matrix.md`, extracted
programmatically from the matrix rather than transcribed by hand, so the
registry cannot drift from its source.

**215 permissions. 0 duplicates. 0 conflicts. 0 invented entries.**

---

## How to read this registry

| Column | Meaning |
|---|---|
| **Scope** | The frozen `permissions.permission_scope` enum value. Note the name prefix `parent_linked_student` maps to the enum `parent_linked_students`. |
| **Roles** | Roles with Allowed or Conditional access. "— none" means denied to every role: the permission exists so the capability is explicitly refused. |
| **Condition** | The matrix note, present only where a role is Conditional. |
| **Audit** | The mandatory event from `23_Security_Standards.md` §15.2, or none for reads. |
| **Archive** | Interaction with Archive state (`08_RBAC.md` §12). |
| **Future dependency** | What must exist before the permission is enforceable. |

---

# Dashboard

6 permissions.

### `platform.dashboard.view`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Dashboard |
| Description | Super Admin may view Platform-level Dashboard only. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — enforceable with frozen tables. |

### `teacher_workspace.dashboard.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Dashboard |
| Description | Teacher may view only own Teacher Workspace Dashboard. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — enforceable with frozen tables. |

### `student_account.dashboard.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Dashboard |
| Description | Student may view own Dashboard only. |
| Roles | Student |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — enforceable with frozen tables. |

### `parent_linked_student.dashboard.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Dashboard |
| Description | Parent may view linked-Student Dashboard summaries only. |
| Roles | Parent |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

### `teacher_workspace.dashboard.view_history`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Dashboard |
| Description | Allowed for Teacher only when historical Dashboard/report context is available in own Teacher Workspace. |
| Roles | Teacher (conditional) |
| Condition | Allowed for Teacher only when historical Dashboard/report context is available in own Teacher Workspace. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | None — enforceable with frozen tables. |

### `platform.dashboard.view_teacher_private_content`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Dashboard |
| Description | Conditional only because Super Admin content visibility is PENDING; unrestricted Teacher-private content access is not granted. |
| Roles | Super Admin (conditional) |
| Condition | Conditional only because Super Admin content visibility is PENDING; unrestricted Teacher-private content access is not granted. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Blocked by **Q-012** (Super Admin content visibility) — treat as denied until resolved. |

---

# Educational Grades

8 permissions.

### `teacher_workspace.educational_grade.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Educational Grades |
| Description | Teacher may view Educational Grades only in own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Teacher Workspace phase. |

### `teacher_workspace.educational_grade.create`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Educational Grades |
| Description | Teacher-created academic structure only. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Create — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Teacher Workspace phase. |

### `teacher_workspace.educational_grade.update`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Educational Grades |
| Description | Teaching Subject remains separate and cannot be changed through Educational Grades. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Teacher Workspace phase. |

### `teacher_workspace.educational_grade.archive`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Educational Grades |
| Description | Archive replaces permanent deletion. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Archive — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Archives the record. Never a hard delete (08 §2.9). |
| Future dependency | Teacher Workspace phase. |

### `teacher_workspace.educational_grade.restore`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Educational Grades |
| Description | Restore is audited and scoped to own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Restore — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Restores an archived record; authorized and audited. |
| Future dependency | Teacher Workspace phase. |

### `teacher_workspace.educational_grade.view_history`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Educational Grades |
| Description | Historical records remain available and must preserve workspace scope. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | Teacher Workspace phase. |

### `parent_linked_student.educational_grade.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Educational Grades |
| Description | Parent may see Educational Grade context only when it is part of linked Student read-only information. |
| Roles | Parent (conditional) |
| Condition | Parent may see Educational Grade context only when it is part of linked Student read-only information. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

### `student_account.educational_grade.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Educational Grades |
| Description | Student may see Educational Grade context only where it is part of the Student's own Teacher relationship. |
| Roles | Student (conditional) |
| Condition | Student may see Educational Grade context only where it is part of the Student's own Teacher relationship. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Teacher Workspace phase. |

---

# Groups

12 permissions.

### `teacher_workspace.group.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Groups |
| Description | Teacher may view Groups in own Teacher Workspace only. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Teacher Workspace phase. |

### `teacher_workspace.group.create`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Groups |
| Description | Group belongs to one Educational Grade in the Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Create — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Teacher Workspace phase. |

### `teacher_workspace.group.update`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Groups |
| Description | Includes Name, Schedule, Price, and Pricing Type within confirmed rules. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Teacher Workspace phase. |

### `teacher_workspace.group.archive`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Groups |
| Description | Archived Groups are removed from active assignment lists but remain historical. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Archive — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Archives the record. Never a hard delete (08 §2.9). |
| Future dependency | Teacher Workspace phase. |

### `teacher_workspace.group.restore`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Groups |
| Description | Restore must preserve historical relationships and be audited. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Restore — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Restores an archived record; authorized and audited. |
| Future dependency | Teacher Workspace phase. |

### `teacher_workspace.group.view_history`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Groups |
| Description | Teacher may view historical Group context in own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | Teacher Workspace phase. |

### `teacher_workspace.group.assign_student`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Groups |
| Description | Must enforce one Group per Student per Teacher. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Teacher Workspace phase. |

### `teacher_workspace.group.move_student`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Groups |
| Description | Must preserve historical Attendance, Homework, Exams, and grades. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Teacher Workspace phase. |

### `student_account.group.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Groups |
| Description | Student may view own current Group context per Teacher relationship. |
| Roles | Student (conditional) |
| Condition | Student may view own current Group context per Teacher relationship. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Teacher Workspace phase. |

### `student_account.group.update`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Groups |
| Description | Student cannot move self between Groups. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.group.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Groups |
| Description | Parent may view linked Student Group context only in read-only views. |
| Roles | Parent (conditional) |
| Condition | Parent may view linked Student Group context only in read-only views. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

### `parent_linked_student.group.update`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Groups |
| Description | Parent cannot change Group assignment. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

---

# Students

15 permissions.

### `teacher_workspace.student.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Students |
| Description | Teacher may view Student records relevant to own Teacher Workspace only. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Students phase. |

### `teacher_workspace.student.search`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Students |
| Description | Search is for Teacher Workspace management and must not expose other Teacher-private records. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Students phase. |

### `teacher_workspace.student.create`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Students |
| Description | Teacher may manually create Student account only without duplicates. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Create — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Students phase. |

### `teacher_workspace.student.assign_existing`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Students |
| Description | Existing Student assignment must not expose another Teacher's private data. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Students phase. |

### `teacher_workspace.student.update`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Students |
| Description | Limited to Teacher Workspace relationship and allowed Student management data. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Students phase. |

### `teacher_workspace.student.archive`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Students |
| Description | Archive applies to the relevant record or relationship; historical data remains. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Archive — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Archives the record. Never a hard delete (08 §2.9). |
| Future dependency | Students phase. |

### `teacher_workspace.student.restore`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Students |
| Description | Restore is scoped and audited. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Restore — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Restores an archived record; authorized and audited. |
| Future dependency | Students phase. |

### `teacher_workspace.student.view_history`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Students |
| Description | Teacher may view history for own Teacher Workspace only. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | Students phase. |

### `student_account.student.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Students |
| Description | Student may view own account and own per-Teacher records. |
| Roles | Student |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Students phase. |

### `student_account.student.update`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Students |
| Description | Student may update own account settings only according to confirmed requirements. |
| Roles | Student (conditional) |
| Condition | Student may update own account settings only according to confirmed requirements. |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Students phase. |

### `student_account.student.activate`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Students |
| Description | Student may activate own Teacher-created account. |
| Roles | Student (conditional) |
| Condition | Student may activate own Teacher-created account. |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Students phase. |

### `student_account.student.create_duplicate`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Students |
| Description | Duplicate Student accounts are never allowed. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.student.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Students |
| Description | Parent may view linked Students only. |
| Roles | Parent |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

### `parent_linked_student.student.update`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Students |
| Description | Parent read-only rule. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.student.view_private_workspace_records`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Students |
| Description | Conditional only because Super Admin content visibility is PENDING; unrestricted private Student workspace records are not granted. |
| Roles | Super Admin (conditional) |
| Condition | Conditional only because Super Admin content visibility is PENDING; unrestricted private Student workspace records are not granted. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Blocked by **Q-012** (Super Admin content visibility) — treat as denied until resolved. |

---

# Attendance

14 permissions.

### `teacher_workspace.attendance.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Attendance |
| Description | Teacher may view Attendance in own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Attendance phase. |

### `teacher_workspace.attendance.record`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Attendance |
| Description | Includes Teacher-side manual entry and authorized ID Card scan handling. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Attendance Change — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Attendance phase. |

### `teacher_workspace.attendance.update`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Attendance |
| Description | Attendance changes must be audited. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Attendance phase. |

### `teacher_workspace.attendance.archive`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Attendance |
| Description | Allowed only where an Attendance-related record is archivable; history remains. |
| Roles | Teacher (conditional) |
| Condition | Allowed only where an Attendance-related record is archivable; history remains. |
| Audit impact | Archive — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Archives the record. Never a hard delete (08 §2.9). |
| Future dependency | Attendance phase. |

### `teacher_workspace.attendance.restore`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Attendance |
| Description | Allowed only for authorized restoration of archived Attendance-related records. |
| Roles | Teacher (conditional) |
| Condition | Allowed only for authorized restoration of archived Attendance-related records. |
| Audit impact | Restore — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Restores an archived record; authorized and audited. |
| Future dependency | Attendance phase. |

### `teacher_workspace.attendance.view_history`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Attendance |
| Description | Attendance history remains available after Group movement. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | Attendance phase. |

### `student_account.attendance.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Attendance |
| Description | Student may view own Attendance where available in own per-Teacher records. |
| Roles | Student (conditional) |
| Condition | Student may view own Attendance where available in own per-Teacher records. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Attendance phase. |

### `student_account.attendance.scan_dynamic_qr`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Attendance |
| Description | Student scans daily Dynamic QR Code through Web Application. |
| Roles | Student |
| Condition | — |
| Audit impact | Attendance Change — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Attendance phase. |

### `student_account.attendance.scan_id_card`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Attendance |
| Description | ID Card scanning is scanner-side/Teacher Workspace operation, not a Student self-permission. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `student_account.attendance.update`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Attendance |
| Description | Student cannot manually modify Attendance records. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.attendance.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Attendance |
| Description | Parent may view linked Student Attendance read-only. |
| Roles | Parent |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

### `parent_linked_student.attendance.record`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Attendance |
| Description | Parent cannot record Attendance. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.attendance.update`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Attendance |
| Description | Parent cannot modify or correct Attendance. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.attendance.view_report_summary`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Attendance |
| Description | Conditional on confirmed Super Admin report visibility boundaries; not Teacher-private browsing. |
| Roles | Super Admin (conditional) |
| Condition | Conditional on confirmed Super Admin report visibility boundaries; not Teacher-private browsing. |
| Audit impact | Attendance Change — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Attendance phase. |

---

# Homework

17 permissions.

### `teacher_workspace.homework.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Homework |
| Description | Teacher may view Homework in own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Homework phase. |

### `teacher_workspace.homework.create`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Homework |
| Description | Homework supports Text, Image, and PDF only. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Create — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Homework phase. |

### `teacher_workspace.homework.update`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Homework |
| Description | Homework modifications must be audited. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Homework phase. |

### `teacher_workspace.homework.archive`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Homework |
| Description | Archive replaces permanent deletion. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Archive — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Archives the record. Never a hard delete (08 §2.9). |
| Future dependency | Homework phase. |

### `teacher_workspace.homework.restore`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Homework |
| Description | Restore is audited. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Restore — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Restores an archived record; authorized and audited. |
| Future dependency | Homework phase. |

### `teacher_workspace.homework.grade`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Homework |
| Description | Teacher may review or grade where applicable. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Homework Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Homework phase. |

### `teacher_workspace.homework.view_submissions`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Homework |
| Description | Submissions are scoped to Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Homework Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Homework phase. |

### `teacher_workspace.homework.view_history`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Homework |
| Description | Historical Homework remains available. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | Homework phase. |

### `student_account.homework.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Homework |
| Description | Student may view Homework assigned to the Student. |
| Roles | Student |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Homework phase. |

### `student_account.homework.submit`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Homework |
| Description | Allowed only for assigned Homework and supported formats. |
| Roles | Student (conditional) |
| Condition | Allowed only for assigned Homework and supported formats. |
| Audit impact | Homework Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Homework phase. |

### `student_account.homework.update_submission`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Homework |
| Description | Allowed only where later detailed requirements permit modification of own submission. |
| Roles | Student (conditional) |
| Condition | Allowed only where later detailed requirements permit modification of own submission. |
| Audit impact | Homework Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Homework phase. |

### `student_account.homework.grade`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Homework |
| Description | Student cannot grade Homework. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.homework.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Homework |
| Description | Parent may view linked Student Homework read-only. |
| Roles | Parent |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

### `parent_linked_student.homework.submit`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Homework |
| Description | Parent cannot submit Homework for Student. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.homework.update`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Homework |
| Description | Parent cannot modify Homework. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.homework.view_private_content`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Homework |
| Description | Conditional only because Super Admin content visibility is PENDING; unrestricted Homework content access is not granted. |
| Roles | Super Admin (conditional) |
| Condition | Conditional only because Super Admin content visibility is PENDING; unrestricted Homework content access is not granted. |
| Audit impact | Homework Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Blocked by **Q-012** (Super Admin content visibility) — treat as denied until resolved. |

### `teacher_workspace.homework.submit_video`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Homework |
| Description | Video homework is out of scope for Version 1. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

---

# Lessons

12 permissions.

### `teacher_workspace.lesson.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Lessons |
| Description | Teacher may view own Lessons in own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Lessons phase. |

### `teacher_workspace.lesson.create`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Lessons |
| Description | Teacher may create private Lesson metadata/content for own Students. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Create — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Lessons phase. |

### `teacher_workspace.lesson.update`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Lessons |
| Description | Lesson remains Teacher-owned and private. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Lessons phase. |

### `teacher_workspace.lesson.archive`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Lessons |
| Description | Archived Lessons are not active but remain historical. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Archive — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Archives the record. Never a hard delete (08 §2.9). |
| Future dependency | Lessons phase. |

### `teacher_workspace.lesson.restore`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Lessons |
| Description | Restore is audited and scoped. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Restore — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Restores an archived record; authorized and audited. |
| Future dependency | Lessons phase. |

### `teacher_workspace.lesson.upload_video`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Lessons |
| Description | Lesson video hosting/protection details remain PENDING. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Create — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Blocked by a PENDING decision — treat as denied until resolved. |

### `teacher_workspace.lesson.view_history`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Lessons |
| Description | Historical Lesson references remain available where applicable. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | Lessons phase. |

### `student_account.lesson.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Lessons |
| Description | Student may view Lessons only from the Student's own Teachers. |
| Roles | Student (conditional) |
| Condition | Student may view Lessons only from the Student's own Teachers. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Lessons phase. |

### `student_account.lesson.browse_marketplace`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Lessons |
| Description | Marketplace browsing is out of scope. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.lesson.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Lessons |
| Description | Parent Panel does not include Lessons in confirmed V1 navigation. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — permanently denied in Version 1. |

### `platform.lesson.view_private_content`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Lessons |
| Description | Conditional only because Super Admin content visibility is PENDING; no unrestricted access is granted. |
| Roles | Super Admin (conditional) |
| Condition | Conditional only because Super Admin content visibility is PENDING; no unrestricted access is granted. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Blocked by **Q-012** (Super Admin content visibility) — treat as denied until resolved. |

### `platform.lesson.publish_marketplace`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Lessons |
| Description | The Platform is not a marketplace and Teachers do not sell courses. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

---

# Exams

25 permissions.

### `teacher_workspace.question_bank.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Teacher may view only own private Question Bank. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Exam Engine phase. |

### `teacher_workspace.question_bank.create`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Question Bank is Teacher-owned and private. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Create — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Exam Engine phase. |

### `teacher_workspace.question_bank.update`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Question and Exam modifications must be audited. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Exam Engine phase. |

### `teacher_workspace.question_bank.archive`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Archive replaces permanent deletion. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Archive — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Archives the record. Never a hard delete (08 §2.9). |
| Future dependency | Exam Engine phase. |

### `teacher_workspace.question_bank.restore`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Restore is audited. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Restore — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Restores an archived record; authorized and audited. |
| Future dependency | Exam Engine phase. |

### `teacher_workspace.exam.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Teacher may view own Exams. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Exam Engine phase. |

### `teacher_workspace.exam.create`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Exams use only own Teacher Workspace Question Bank. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Create — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Exam Engine phase. |

### `teacher_workspace.exam.update`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Includes exam definition changes within own workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Exam Engine phase. |

### `teacher_workspace.exam.archive`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Attempts and grades remain historical. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Archive — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Archives the record. Never a hard delete (08 §2.9). |
| Future dependency | Exam Engine phase. |

### `teacher_workspace.exam.restore`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Restore is audited. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Restore — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Restores an archived record; authorized and audited. |
| Future dependency | Exam Engine phase. |

### `teacher_workspace.exam.publish`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Conditional on detailed requirements for making Exams available without cross-Teacher visibility. |
| Roles | Teacher (conditional) |
| Condition | Conditional on detailed requirements for making Exams available without cross-Teacher visibility. |
| Audit impact | Exam Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Exam Engine phase. |

### `teacher_workspace.exam.grade`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Includes Essay grading where applicable. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Homework Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Exam Engine phase. |

### `teacher_workspace.exam.view_attempts`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Teacher may view attempts scoped to own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Exam Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Exam Engine phase. |

### `teacher_workspace.exam.view_history`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Exams |
| Description | Historical attempts and grades remain available. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | Exam Engine phase. |

### `student_account.exam.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Exams |
| Description | Student may view assigned or available Exams. |
| Roles | Student |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Exam Engine phase. |

### `student_account.exam.attempt`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Exams |
| Description | Allowed only for Exams assigned or available to the Student. |
| Roles | Student (conditional) |
| Condition | Allowed only for Exams assigned or available to the Student. |
| Audit impact | Exam Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Exam Engine phase. |

### `student_account.exam.submit`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Exams |
| Description | Allowed only for the Student's own Exam attempt. |
| Roles | Student (conditional) |
| Condition | Allowed only for the Student's own Exam attempt. |
| Audit impact | Homework Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Exam Engine phase. |

### `student_account.exam.view_grade`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Exams |
| Description | Allowed where grade/result is available to the Student. |
| Roles | Student (conditional) |
| Condition | Allowed where grade/result is available to the Student. |
| Audit impact | Exam Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Exam Engine phase. |

### `student_account.question_bank.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Exams |
| Description | Student cannot browse Teacher private Question Bank. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.exam.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Exams |
| Description | Parent may view linked Student Exam information read-only. |
| Roles | Parent |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

### `parent_linked_student.exam.view_grade`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Exams |
| Description | Allowed where grades are available for linked Student. |
| Roles | Parent (conditional) |
| Condition | Allowed where grades are available for linked Student. |
| Audit impact | Exam Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

### `parent_linked_student.exam.attempt`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Exams |
| Description | Parent cannot take Exams for Student. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.exam.update`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Exams |
| Description | Parent cannot modify Exams or grades. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.question_bank.view_private_content`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Exams |
| Description | Conditional only because Super Admin content visibility is PENDING; private Question Bank browsing is not granted. |
| Roles | Super Admin (conditional) |
| Condition | Conditional only because Super Admin content visibility is PENDING; private Question Bank browsing is not granted. |
| Audit impact | Exam Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Blocked by **Q-012** (Super Admin content visibility) — treat as denied until resolved. |

### `platform.exam.view_private_definition`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Exams |
| Description | Conditional only within future confirmed Super Admin visibility boundaries. |
| Roles | Super Admin (conditional) |
| Condition | Conditional only within future confirmed Super Admin visibility boundaries. |
| Audit impact | Exam Modification — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Exam Engine phase. |

---

# Reports

10 permissions.

### `platform.report.view`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Reports |
| Description | Super Admin may view Platform-level global reports within visibility boundaries. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Reporting phase. |

### `platform.report.view_teacher_aggregate`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Reports |
| Description | Conditional on confirmed content visibility; aggregate/metadata default must not expose private content. |
| Roles | Super Admin (conditional) |
| Condition | Conditional on confirmed content visibility; aggregate/metadata default must not expose private content. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Reporting phase. |

### `teacher_workspace.report.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Reports |
| Description | Teacher may view own Teacher Workspace reports. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Reporting phase. |

### `teacher_workspace.report.view_attendance`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Reports |
| Description | Scoped to own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Reporting phase. |

### `teacher_workspace.report.view_homework`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Reports |
| Description | Scoped to own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Reporting phase. |

### `teacher_workspace.report.view_exam_results`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Reports |
| Description | Scoped to own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Reporting phase. |

### `teacher_workspace.report.view_payments`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Reports |
| Description | Primarily Flow B status; Flow A remains separate. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Reporting phase. |

### `teacher_workspace.report.view_student_performance`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Reports |
| Description | Scoped to own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Reporting phase. |

### `student_account.report.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Reports |
| Description | Student may view own per-Teacher status, results, or summaries where available. |
| Roles | Student (conditional) |
| Condition | Student may view own per-Teacher status, results, or summaries where available. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Reporting phase. |

### `parent_linked_student.report.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Reports |
| Description | Parent may view read-only linked Student summaries where available. |
| Roles | Parent (conditional) |
| Condition | Parent may view read-only linked Student summaries where available. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

---

# Payments

12 permissions.

### `platform.payment_status.view`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Payments |
| Description | Super Admin may view Flow A payment status at Platform level. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Payments phase — Flow B tables not yet built. |

### `platform.payment_status.record`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Payments |
| Description | Super Admin may record Flow A status only. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Attendance Change — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Payments phase — Flow B tables not yet built. |

### `platform.payment_status.update`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Payments |
| Description | Status-only; no transaction processing. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Payments phase — Flow B tables not yet built. |

### `platform.payment_status.view_history`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Payments |
| Description | Historical Flow A payment-status records remain available. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | Payments phase — Flow B tables not yet built. |

### `teacher_workspace.payment_status.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Payments |
| Description | Teacher may view Flow B status in own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Payments phase — Flow B tables not yet built. |

### `teacher_workspace.payment_status.record`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Payments |
| Description | Teacher may record Flow B Student fee status. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Attendance Change — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Payments phase — Flow B tables not yet built. |

### `teacher_workspace.payment_status.update`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Payments |
| Description | Status-only; no transaction processing. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Payments phase — Flow B tables not yet built. |

### `teacher_workspace.payment_status.view_history`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Payments |
| Description | Historical Flow B records remain available. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | Payments phase — Flow B tables not yet built. |

### `student_account.payment_status.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Payments |
| Description | Student may view own per-Teacher Flow B status. |
| Roles | Student (conditional) |
| Condition | Student may view own per-Teacher Flow B status. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Payments phase — Flow B tables not yet built. |

### `student_account.payment_status.update`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Payments |
| Description | Student cannot modify payment status unless a future confirmed requirement allows it. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.payment_status.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Payments |
| Description | Parent may view linked Student Flow B status read-only. |
| Roles | Parent |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

### `parent_linked_student.payment_status.update`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Payments |
| Description | Parent cannot modify payment status. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

---

# Subscriptions

12 permissions.

### `platform.subscription.view`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Subscriptions |
| Description | Super Admin may view Teacher Platform Subscriptions. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Subscription phase — Flow A tables not yet built. |

### `platform.subscription.calculate_billable_students`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Subscriptions |
| Description | Calculation uses Enrollment duration only, not Attendance or login. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Subscription Change — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Subscription phase — Flow A tables not yet built. |

### `platform.subscription.create_cycle`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Subscriptions |
| Description | Billing Cycle is calendar month and begins automatically. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Subscription Change — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Subscription phase — Flow A tables not yet built. |

### `platform.subscription.update_status`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Subscriptions |
| Description | Status-only; Subscription changes must be audited. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Subscription phase — Flow A tables not yet built. |

### `platform.subscription.record_payment_status`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Subscriptions |
| Description | Payment is handled outside Platform in Version 1. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Subscription Change — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Subscription phase — Flow A tables not yet built. |

### `platform.subscription.view_history`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Subscriptions |
| Description | Historical Subscription records and pricing context remain available. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | Subscription phase — Flow A tables not yet built. |

### `teacher_workspace.subscription.view_own_status`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Subscriptions |
| Description | Teacher may view own Flow A status where exposed to the Teacher; Teacher cannot manage Platform billing. |
| Roles | Teacher (conditional) |
| Condition | Teacher may view own Flow A status where exposed to the Teacher; Teacher cannot manage Platform billing. |
| Audit impact | Subscription Change — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Subscription phase — Flow A tables not yet built. |

### `teacher_workspace.subscription.update_status`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Subscriptions |
| Description | Teacher cannot update own Flow A Subscription status. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `student_account.subscription.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Subscriptions |
| Description | Student-facing “Subscriptions” area represents Flow B status, not Flow A Subscription. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.subscription.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Subscriptions |
| Description | Parent Payments represent Flow B status, not Flow A Subscription. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — permanently denied in Version 1. |

### `platform.subscription.enforce_non_payment`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Subscriptions |
| Description | Non-payment enforcement remains PENDING and must not be silently applied. |
| Roles | Super Admin (conditional) |
| Condition | Non-payment enforcement remains PENDING and must not be silently applied. |
| Audit impact | Subscription Change — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Blocked by a PENDING decision — treat as denied until resolved. |

### `platform.subscription.process_online_payment`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Subscriptions |
| Description | Online payment processing is out of scope. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

---

# Users

14 permissions.

### `teacher_workspace.teacher_staff.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Users |
| Description | Teacher may view Teacher Staff in own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Granularity **Q-011** PENDING; catalogue entry only, no preset grant. |

### `teacher_workspace.teacher_staff.create`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Users |
| Description | Teacher Staff exist only inside creating Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Create — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Granularity **Q-011** PENDING; catalogue entry only, no preset grant. |

### `teacher_workspace.teacher_staff.update`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Users |
| Description | Teacher may update staff account information in own workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Granularity **Q-011** PENDING; catalogue entry only, no preset grant. |

### `teacher_workspace.teacher_staff.archive`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Users |
| Description | Archive replaces permanent deletion. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Archive — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Archives the record. Never a hard delete (08 §2.9). |
| Future dependency | Granularity **Q-011** PENDING; catalogue entry only, no preset grant. |

### `teacher_workspace.teacher_staff.restore`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Users |
| Description | Restore is audited. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Restore — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Restores an archived record; authorized and audited. |
| Future dependency | Granularity **Q-011** PENDING; catalogue entry only, no preset grant. |

### `teacher_workspace.teacher_staff.assign_permission`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Users |
| Description | Permission changes must be audited. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Permission Change — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Granularity **Q-011** PENDING; catalogue entry only, no preset grant. |

### `teacher_workspace.teacher_staff.view_history`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Users |
| Description | Historical staff attribution remains available. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | Granularity **Q-011** PENDING; catalogue entry only, no preset grant. |

### `teacher_workspace.teacher_staff.self_assign_permission`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Users |
| Description | Teacher Staff cannot grant themselves permissions. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.user.view`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Users |
| Description | Super Admin may view Platform-level user administration only within confirmed scope. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — enforceable with frozen tables. |

### `platform.user.create_platform_staff`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Users |
| Description | Platform staff roles such as Support, Sales, and Accountant are out of scope for Version 1. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `student_account.settings.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Users, Settings  *(cross-listed)* |
| Description | Student may view own account settings. |
| Roles | Student |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — enforceable with frozen tables. |

### `student_account.settings.update`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Users, Settings  *(cross-listed)* |
| Description | Student may update own account settings according to confirmed requirements. |
| Roles | Student (conditional) |
| Condition | Student may update own account settings according to confirmed requirements. |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | None — enforceable with frozen tables. |

### `parent_linked_student.parent_account.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Users |
| Description | Parent may access own Parent account context where detailed requirements define it. |
| Roles | Parent (conditional) |
| Condition | Parent may access own Parent account context where detailed requirements define it. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

### `parent_linked_student.parent_account.update`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Users |
| Description | Conditional only for own Parent account context; never linked Student educational data. |
| Roles | Parent (conditional) |
| Condition | Conditional only for own Parent account context; never linked Student educational data. |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

---

# Settings

11 permissions.

### `platform.settings.view`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Settings |
| Description | Super Admin may view confirmed Platform Settings. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — enforceable with frozen tables. |

### `platform.settings.update`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Settings |
| Description | Super Admin may update confirmed Platform Settings only. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | None — enforceable with frozen tables. |

### `platform.settings.update_pricing`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Settings |
| Description | Pricing is owned by Super Admin; flat versus tiers remains PENDING. |
| Roles | Super Admin (conditional) |
| Condition | Pricing is owned by Super Admin; flat versus tiers remains PENDING. |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Blocked by a PENDING decision — treat as denied until resolved. |

### `platform.settings.update_localization`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Settings |
| Description | Arabic (default) and English (fully supported), with automatic RTL/LTR, are confirmed; timezone, currency, and target market remain PENDING. |
| Roles | Super Admin (conditional) |
| Condition | Arabic (default) and English (fully supported), with automatic RTL/LTR, are confirmed; timezone, currency, and target market remain PENDING. |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Blocked by a PENDING decision — treat as denied until resolved. |

### `platform.settings.configure_payment_gateway`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Settings |
| Description | Payment gateways are out of scope for Version 1. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.settings.configure_notifications`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Settings |
| Description | Notifications are out of scope for Version 1. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `teacher_workspace.settings.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Settings |
| Description | Teacher may view own Teacher Workspace Settings. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — enforceable with frozen tables. |

### `teacher_workspace.settings.update`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Settings |
| Description | Teacher may update profile, center information, phone numbers, and address. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | None — enforceable with frozen tables. |

### `teacher_workspace.settings.update_teaching_subject`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Settings |
| Description | Teaching Subject cannot be changed after account creation. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.settings.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Settings |
| Description | Parent may access own account context if later detailed requirements define it. |
| Roles | Parent (conditional) |
| Condition | Parent may access own account context if later detailed requirements define it. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

### `parent_linked_student.settings.update`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Settings |
| Description | Conditional only for own Parent account context; linked Student records remain read-only. |
| Roles | Parent (conditional) |
| Condition | Conditional only for own Parent account context; linked Student records remain read-only. |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

---

# Files

12 permissions.

### `teacher_workspace.file.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Files |
| Description | Teacher may view files belonging to own Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Files phase. |

### `teacher_workspace.file.upload`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Files |
| Description | Includes Lesson videos and Homework-supported files within confirmed scope. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Create — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Files phase. |

### `teacher_workspace.file.update`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Files |
| Description | File references must preserve ownership and history. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Files phase. |

### `teacher_workspace.file.archive`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Files |
| Description | Archived file references remain available for historical records. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Archive — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Archives the record. Never a hard delete (08 §2.9). |
| Future dependency | Files phase. |

### `teacher_workspace.file.restore`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Files |
| Description | Restore is audited where applicable. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Restore — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Restores an archived record; authorized and audited. |
| Future dependency | Files phase. |

### `teacher_workspace.file.view_history`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Files |
| Description | Historical file references remain scoped to Teacher Workspace. |
| Roles | Teacher |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | Files phase. |

### `student_account.file.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Files |
| Description | Student may view files only through own assigned Homework, submissions, Exams, or Lessons. |
| Roles | Student (conditional) |
| Condition | Student may view files only through own assigned Homework, submissions, Exams, or Lessons. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Files phase. |

### `student_account.file.upload`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Files |
| Description | Student may upload only supported Homework submission files where allowed. |
| Roles | Student (conditional) |
| Condition | Student may upload only supported Homework submission files where allowed. |
| Audit impact | Create — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Files phase. |

### `student_account.file.upload_video_homework`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Files |
| Description | Video homework is out of scope. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.file.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Files |
| Description | Parent may view linked Student files only where read-only access is part of linked Student records. |
| Roles | Parent (conditional) |
| Condition | Parent may view linked Student files only where read-only access is part of linked Student records. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Parent phase — enforcement of Conditional entries needs `parent_student_links` (06 §4). |

### `parent_linked_student.file.upload`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Files |
| Description | Parent cannot upload Student Homework or Teacher files. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.file.view_private_teacher_file`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Files |
| Description | Conditional only because Super Admin content visibility is PENDING; unrestricted private file access is not granted. |
| Roles | Super Admin (conditional) |
| Condition | Conditional only because Super Admin content visibility is PENDING; unrestricted private file access is not granted. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Blocked by **Q-012** (Super Admin content visibility) — treat as denied until resolved. |

---

# Notifications

8 permissions.

### `platform.notification.view`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Notifications |
| Description | Notification is not a Version 1 product entity. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — permanently denied in Version 1. |

### `platform.notification.create`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Notifications |
| Description | Push, email, and SMS notifications are out of scope. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.notification.update`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Notifications |
| Description | Notification management is out of scope. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.notification.archive`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Notifications |
| Description | No Version 1 Notification records exist to archive. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Archives the record. Never a hard delete (08 §2.9). |
| Future dependency | None — permanently denied in Version 1. |

### `teacher_workspace.notification.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Notifications |
| Description | Teacher notifications are out of scope. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — permanently denied in Version 1. |

### `teacher_workspace.notification.create`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Notifications |
| Description | Teacher notification sending is out of scope. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `student_account.notification.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Notifications |
| Description | Student notifications are out of scope. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.notification.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Notifications |
| Description | Parent notifications are out of scope. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — permanently denied in Version 1. |

---

# Audit Logs

9 permissions.

### `platform.audit_log.view`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Audit Logs |
| Description | Super Admin may view Platform-scope Audit Logs within confirmed visibility boundaries. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — enforceable with frozen tables. |

### `platform.audit_log.view_teacher_workspace_events`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Audit Logs |
| Description | Conditional on Super Admin content visibility resolution; must not expose private content by default. |
| Roles | Super Admin (conditional) |
| Condition | Conditional on Super Admin content visibility resolution; must not expose private content by default. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | None — enforceable with frozen tables. |

### `platform.audit_log.create`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Audit Logs |
| Description | Audit entries are created by the system for important actions by all roles. |
| Roles | Super Admin (conditional), Teacher (conditional), Student (conditional), Parent (conditional) |
| Condition | Audit entries are created by the system for important actions by all roles. |
| Audit impact | Create — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | None — enforceable with frozen tables. |

### `platform.audit_log.update`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Audit Logs |
| Description | Audit Log is immutable. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.audit_log.archive`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Audit Logs |
| Description | Audit Log entries are permanent and not archived. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Archives the record. Never a hard delete (08 §2.9). |
| Future dependency | None — permanently denied in Version 1. |

### `platform.audit_log.restore`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Audit Logs |
| Description | Audit Log entries are never archived, so restore does not apply. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Restores an archived record; authorized and audited. |
| Future dependency | None — permanently denied in Version 1. |

### `teacher_workspace.audit_log.view`

| Field | Value |
|---|---|
| Scope | `teacher_workspace` |
| Module | Audit Logs |
| Description | Teacher visibility is allowed only for own Teacher Workspace Audit Log where permitted by requirements. |
| Roles | Teacher (conditional) |
| Condition | Teacher visibility is allowed only for own Teacher Workspace Audit Log where permitted by requirements. |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — enforceable with frozen tables. |

### `student_account.audit_log.view`

| Field | Value |
|---|---|
| Scope | `student_account` |
| Module | Audit Logs |
| Description | Student Audit Log visibility is not a confirmed Version 1 surface. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — permanently denied in Version 1. |

### `parent_linked_student.audit_log.view`

| Field | Value |
|---|---|
| Scope | `parent_linked_students` |
| Module | Audit Logs |
| Description | Parent Audit Log visibility is not a confirmed Version 1 surface. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — permanently denied in Version 1. |

---

# Platform Management

18 permissions.

### `platform.teacher.view`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Super Admin manages Teacher accounts at Platform level. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — enforceable with frozen tables. |

### `platform.teacher.create`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Teacher account creation is Platform-level administration. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Create — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | None — enforceable with frozen tables. |

### `platform.teacher.update`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Teaching Subject cannot be changed after account creation. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | None — enforceable with frozen tables. |

### `platform.teacher.archive`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Archive replaces permanent deletion. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Archive — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Archives the record. Never a hard delete (08 §2.9). |
| Future dependency | None — enforceable with frozen tables. |

### `platform.teacher.restore`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Restore is audited. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Restore — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Restores an archived record; authorized and audited. |
| Future dependency | None — enforceable with frozen tables. |

### `platform.teacher.view_history`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Historical Teacher account records remain available. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Historical view — may include archived records, clearly indicated. |
| Future dependency | None — enforceable with frozen tables. |

### `platform.teacher.login_as_teacher`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | “Login as Teacher” is not confirmed in Version 1. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.teacher.update_teaching_subject`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Teaching Subject cannot be changed after account creation. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.pricing.view`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Pricing is owned by Super Admin. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | None — enforceable with frozen tables. |

### `platform.pricing.update`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Conditional because flat price versus tiers remains PENDING. |
| Roles | Super Admin (conditional) |
| Condition | Conditional because flat price versus tiers remains PENDING. |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Blocked by a PENDING decision — treat as denied until resolved. |

### `platform.billing_cycle.view`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Billing Cycle is calendar month. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Subscription phase — Flow A tables not yet built. |

### `platform.billing_cycle.manage`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | New Billing Cycle begins automatically. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Update — mandatory Audit Log entry (23 §15.2). |
| Archive impact | Applies to active records only; archived records are not actionable. |
| Future dependency | Subscription phase — Flow A tables not yet built. |

### `platform.global_report.view`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Must respect pending content-visibility boundary. |
| Roles | Super Admin |
| Condition | — |
| Audit impact | Read-only — no Audit Log entry required. |
| Archive impact | Active records only; archived excluded from active lists (08 §12). |
| Future dependency | Blocked by a PENDING decision — treat as denied until resolved. |

### `platform.platform_staff.create`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Platform staff roles beyond Super Admin are out of scope for Version 1. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.marketplace.manage`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Marketplace behavior is out of scope. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.course_discovery.manage`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Course discovery/browsing across Teachers is out of scope. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.payment_gateway.manage`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Online payment gateways are out of scope. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

### `platform.native_mobile.manage`

| Field | Value |
|---|---|
| Scope | `platform` |
| Module | Platform Management |
| Description | Native mobile applications are out of scope for Version 1. |
| Roles | — none (denied to all) |
| Condition | — |
| Audit impact | None — denied to every role; attempts logged as security-relevant events. |
| Archive impact | Not applicable — permission is denied. |
| Future dependency | None — permanently denied in Version 1. |

---
# Registry summary

| Metric | Value |
|---|---|
| Distinct permissions | **215** |
| Matrix rows parsed | 217 |
| Cross-listed entries | 2 (`student_account.settings.view`, `student_account.settings.update`) |
| Modules | 17 |
| Duplicates | 0 |
| Conflicting definitions | 0 |
| Invented permissions | 0 |

## Scope distribution

| Scope enum | Name prefix | Permissions |
|---|---|---|
| `platform` | `platform.` | 60 |
| `teacher_workspace` | `teacher_workspace.` | 92 |
| `student_account` | `student_account.` | 34 |
| `parent_linked_students` | `parent_linked_student.` | 29 |
| **Total** | | **215** |

## The two cross-listed permissions

`student_account.settings.view` and `student_account.settings.update` each
appear twice in the matrix — once under **Users** (§12) and once under
**Settings** (§13). The role values are **identical** in both listings, so these
are cross-references, not conflicts.

They are registered **once each**, with both source modules recorded. That is
why 217 matrix rows yield 215 distinct permissions.

---

# PERMISSION REGISTRY FROZEN

From this point:

1. **Permission names are immutable.** No rename, no re-spelling, no
   normalization. In particular the prefix `parent_linked_student` (singular)
   must never be changed to match the scope enum `parent_linked_students`
   (plural) — both are correct in their own place.
2. **Permission scopes are immutable.** Each permission keeps the scope recorded
   here.
3. **Permission identifiers are immutable.** Once a permission row is seeded its
   name and scope form its stable identity; the pair is the natural key behind
   the frozen `unique(permission_name, permission_scope)` constraint.
4. **Only new permissions may be added, and only through an approved
   Architecture Change Request after AI_DOCS changes.** `09_Permission_Matrix.md`
   must be amended first, then the ACR, then the registry, then the code
   (`PROJECT_CONSTRAINTS.md` §4).

A permission may never be removed. Retiring a capability means denying it, not
deleting the entry, so a historical Audit Log reference always resolves.
