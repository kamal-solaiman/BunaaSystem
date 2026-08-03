# RBAC Contract — Phase 45

**Status: RBAC CONTRACT READY. Not implemented.**

This document specifies the authorization layer before any of it is built. It
is derived from `08_RBAC.md`, `09_Permission_Matrix.md`, `07_Data_Dictionary.md`,
and `23_Security_Standards.md`, and it consumes the frozen database and
authentication contracts without altering either.

> **One correction to the brief.** It asks for "Manager behavior". **There is no
> Manager role in Version 1.** Searching all 42 AI_DOCS files returns five
> matches for "manager", every one unrelated — *PHP Extension Manager* and
> *release manager*. `08_RBAC.md` §3 and `07_Data_Dictionary.md` §2 define
> exactly five roles, and `09_Permission_Matrix.md` §12 explicitly denies
> `platform.user.create_platform_staff` because "Platform staff roles such as
> Support, Sales, and Accountant are out of scope for Version 1."
>
> Inventing a Manager role would violate `PROJECT_CONSTRAINTS.md` §1.7 and §1.9.
> §7 below documents this as a deliberate non-role instead.

---

# 1. Roles

Exactly five, confirmed by `08_RBAC.md` §3, `07_Data_Dictionary.md` §2, and the
frozen `roles.role_name` enum. No sixth role exists.

| Role | Enum value | Boundary | Source |
|---|---|---|---|
| Super Admin | `super_admin` | Platform | 08 §3 |
| Teacher | `teacher` | One Teacher Workspace | 08 §3 |
| Teacher Staff | `teacher_staff` | The creating Teacher Workspace | 08 §3 |
| Student | `student` | Own global account, partitioned per Teacher | 08 §3 |
| Parent | `parent` | Linked Students, read-only | 08 §3 |

A role never grants access by itself: "Access must also pass contextual
authorization and Permission rules" (`07 §2` Notes).

---

# 2. Scopes

Four, confirmed by `33_Validation_Rules.md` §3.3 and frozen in both
`roles.role_scope` and `permissions.permission_scope`.

| Scope | Enum value | Meaning |
|---|---|---|
| Platform | `platform` | Platform-wide administration |
| Teacher Workspace | `teacher_workspace` | One tenant boundary |
| Student Account | `student_account` | One Student's own records |
| Parent Linked Students | `parent_linked_students` | A Parent's linked Students |

**A naming detail that must not become a bug.** Permission *names* in
`09_Permission_Matrix.md` use the singular prefix `parent_linked_student.…`,
while the *scope enum* is plural `parent_linked_students`. Both are correct in
their own place: the prefix is part of a permission's name string, the enum is
a column value. Implementation must not "fix" one to match the other.

| Name prefix (215 permissions) | Count | Maps to scope enum |
|---|---|---|
| `platform.` | 60 | `platform` |
| `teacher_workspace.` | 92 | `teacher_workspace` |
| `student_account.` | 36 | `student_account` |
| `parent_linked_student.` | 29 | `parent_linked_students` |

---

# 3. Permission entities

All frozen. RBAC adds no table and no column.

| Entity | Table | Purpose | Contract |
|---|---|---|---|
| Role | `roles` | The five roles | IMMUTABLE |
| Permission | `permissions` | The capability catalogue | IMMUTABLE |
| Role assignment | `role_user` | User ↔ Role in a context | IMMUTABLE |
| Staff permission assignment | `permission_teacher_staff` | Teacher-granted capabilities | IMMUTABLE |
| Teacher Staff | `teacher_staff` | Staff context inside a workspace | IMMUTABLE |

**Catalogue sizing verified against the frozen schema.** The matrix defines
**215 distinct permissions**; the longest name is
`teacher_workspace.teacher_staff.self_assign_permission` at 54 characters,
inside `permission_name`'s 190-character bound. The unique constraint
`(permission_name, permission_scope)` accommodates all 215.

Permission naming follows `scope.resource.action` (`08 §5`), using canonical
terms only: `educational_grade` never `class`, `lesson` never `course`,
`archive` never `delete`, `subscription` for Flow A, `payment_status` for
Flow B.

---

# 4. Permission relationships

```
User ──< role_user >── Role
  │        (carries teacher_workspace_id for workspace-scoped roles)
  │
  └──< teacher_staff >── TeacherWorkspace
            │
            └──< permission_teacher_staff >── Permission
                      (carries teacher_workspace_id)
```

| Relationship | Cardinality | Source |
|---|---|---|
| User → Role | many-to-many, context-qualified | 07 §1, §2 |
| User → Teacher Staff | one context per workspace | 07 §30 |
| Teacher Staff → Permission | many-to-many within one workspace | 06 §4; 07 §3 |

`role_user.teacher_workspace_id` is null for Platform, Student Account, and
Parent scopes, which have no owning workspace.

---

# 5. Permission resolution flow

Exactly the nine steps of `08_RBAC.md` §14, in order. No step may be reordered
or skipped.

```
1. Authenticate                      (Phase 44, already frozen)
2. Identify User and active Role context
3. Identify requested resource and action
4. Resolve access scope:
     Super Admin    → Platform
     Teacher, Staff → Teacher Workspace
     Student        → own account + Teacher relationships
     Parent         → linked Students
5. Check resource ownership or relationship
6. Check the action is allowed for the role
7. Check the resource is active or archived
8. Reject without exposing restricted data
9. Audit the action if sensitive or important
```

Two properties are load-bearing:

- **Scope before permission.** Step 4 precedes step 6, so a Teacher holding
  `teacher_workspace.group.update` still cannot touch another workspace's Group.
  Isolation is not a permission that can be granted away.
- **Archive after permission.** Step 7 follows step 6, so an archived record is
  refused even to a user who holds the capability.

Enforcement is server-side. "Frontend visibility or hidden controls are not
sufficient security controls" (`08 §14`).

---

# 6. Role Context resolution flow

```
1. Read the user's assignments from role_user
2. For each: the Role supplies role_name and role_scope;
             the pivot supplies teacher_workspace_id (null when unscoped)
3. A user may hold more than one context (07 §1)
4. Report only assigned contexts — never an unheld one (10 §13)
```

This flow already exists and is **frozen**: `RoleContextResolver` implements it
and `GET /auth/me` returns it. RBAC consumes it unchanged.

**Session isolation.** "Role switching must establish a new session context, not
blend roles" (`23 §7.3`). Contexts are never merged into a single effective set.

---

# 7. Role behaviour

## 7.1 Super Admin — Platform scope

**Manages:** Teachers, Flow A Subscriptions, pricing, Platform Settings, global
reports, Platform-level Audit Log visibility (`08 §3`).

**Cannot:** operate inside a Teacher Workspace as a Teacher.

**PENDING Q-012.** Super Admin visibility into Teacher-private content "remains
PENDING and must not be silently expanded" (`08 §3`). Matrix entries in that
area are **Conditional**, and implementation must treat Conditional-pending as
**denied** until Q-012 resolves. Granting it early would be hardening a pending
decision (`PROJECT_CONSTRAINTS.md` §1.9).

## 7.2 Teacher — one Teacher Workspace

**Manages, strictly within that workspace:** Educational Grades, Groups,
Students, Attendance, Homework, Exams, Lessons, Reports, Teacher Staff,
Settings, Flow B payment status (`08 §3`).

**Cannot:** "access another Teacher's data under any circumstance" (`08 §3`).

The Teacher is the only role that may grant permissions, and only to Teacher
Staff inside their own workspace.

## 7.3 Teacher Staff — derived, never independent

**Not a matrix column.** `09 §5` of the Global Principles states Teacher Staff
"are not shown as a separate matrix column… Teacher Staff access is always
Conditional on Teacher-assigned permissions inside the creating Teacher
Workspace."

Effective access is the **intersection** of three things:

```
(1) a capability explicitly assigned by the Teacher, AND
(2) that capability being within the Teacher's own permission set, AND
(3) the creating Teacher Workspace
```

Never a union, never an inheritance of the Teacher's full set.

**Hard guard.** `teacher_workspace.teacher_staff.self_assign_permission` is
**Denied for every role including Teacher Staff** — "Teacher Staff cannot grant
themselves permissions" (`09 §12`). Privilege escalation is closed by the
catalogue itself.

**PENDING Q-011.** Granularity "remains PENDING and must not be silently
assumed" (`07 §3`). The catalogue is currently empty; staff hold nothing until
it is confirmed.

## 7.4 Student — own account, partitioned per Teacher

**May access only:** own account information, own schedule, Homework assigned to
them, Lessons from their own Teachers, Exams assigned or available to them, own
per-Teacher Flow B status, own per-Teacher Attendance, Homework, Exams, and
grades (`08 §3`).

**Partitioning.** One global account (BR-001), but records stay separated per
Teacher. A Student studying with three Teachers sees three separate sets, never
a merged view.

## 7.5 Parent — linked Students, read-only

**May access:** linked Students only, read-only everywhere (`08 §3`).

**Cannot modify:** Attendance, grades, Homework, Exams, Student records, Teacher
records, payment status, or Teacher Workspace data (`08 §3`).

Version 1 supports exactly one Parent account per Student (BR-020).

**Scope boundary for Phase 45.** The Parent Student Link is a distinct logical
entity (`06 §4`) with **no table yet**. Of the 29 Parent permissions, 6 are
Allowed, 9 Conditional, 14 Denied. Registering all 29 in the catalogue needs
nothing new. **Enforcing** the Conditional ones — which depend on an actual link
— requires the `parent_student_links` table, which belongs to the Parent phase.

This is a scope boundary, not a blocker: RBAC registers the catalogue and
enforces role, scope, ownership, and Archive. Link-dependent Parent checks
activate when the Parent phase lands. Recorded here so it is not discovered
mid-implementation.

## 7.6 Manager — does not exist

No Manager role exists in Version 1. See the note at the top of this document.
`platform.user.create_platform_staff` is **Denied for all four columns**, so
Support, Sales, Accountant, and Manager cannot be created. Any future platform
role requires an Architecture Change Request with the AI_DOCS amendment first.

---

# 8. Permission inheritance

**There is no inheritance.** `08_RBAC.md` and `09_Permission_Matrix.md` define
no inheritance, hierarchy, or role nesting — verified by searching both
documents for inherit, hierarchy, and parent-role language.

| Would-be inheritance | Actual rule |
|---|---|
| Super Admin inherits Teacher | **No.** "does not operate inside Teacher Workspaces as a Teacher" (08 §3) |
| Teacher Staff inherits Teacher | **No.** Only explicitly assigned permissions (08 §2.6) |
| Parent inherits Student | **No.** Read-only, linked-Student scope only (08 §3) |
| Teacher inherits across workspaces | **No.** "No cross-Teacher access" (08 §2.7) |

Every grant is explicit. This is a **derived statement of an absence**, and it
is deliberate: an implementer who assumes the usual role hierarchy would break
tenant isolation on their first commit.

---

# 9. Permission precedence

`08_RBAC.md` states no precedence algorithm, because its principles make one
unnecessary. Precedence follows from **deny by default** (`08 §2.1`) plus the
ordered flow of §14.

| Evaluation order | Effect of failure |
|---|---|
| 1. Authenticated? | 401 |
| 2. Role context held? | 403 |
| 3. Resource within scope? | 403, or 404 where visibility must not be disclosed |
| 4. Ownership or relationship? | 403 / 404 |
| 5. Action allowed for role? | 403 |
| 6. Archive state permits it? | 403 / 404 |

**Any failing check denies. No later check can re-grant.** There is no "allow
overrides deny" rule, because no mechanism produces a competing allow.

`Gate::after` already returns null in `AuthServiceProvider`, so an ability with
no registered policy resolves to denied — the deny-by-default boundary is
already in place and frozen.

---

# 10. Conflict resolution

**No conflict is representable.** Documented for completeness, and because an
implementer will reasonably ask.

| Apparent conflict | Resolution | Why it cannot arise |
|---|---|---|
| Two roles, one allows one denies | **Deny** | Contexts never blend; role switching starts a new session (23 §7.3) |
| Role allows, scope forbids | **Deny** | Scope resolves first (08 §14 step 4) |
| Permission allows, record archived | **Deny for active use** | Archive checked after permission (08 §14 step 7) |
| Teacher Staff assigned more than the Teacher holds | **Deny** | Staff access is an intersection (§7.3) |
| Matrix Conditional with unresolved PENDING | **Deny** | Never harden a pending decision |
| Permission archived while assigned | **Deny** | An archived Permission is not active (07 §3) |

The single rule: **when in doubt, deny.**

---

# 11. Archive interaction

| Concern | Rule | Source |
|---|---|---|
| Hard delete | Never granted by any permission | 08 §2.9 |
| Archived records | Must not appear as active resources | 08 §12 |
| Active selection lists | Archived records excluded | 08 §12 |
| Historical reports | May include archived records, clearly indicated | 08 §12 |
| Restore | Authorized roles only, and audited | 09 §18 |
| Archived Role | Grants nothing | 07 §2 |
| Archived Permission | "Must be active to be assigned" | 07 §3 |
| Archived Teacher Staff | "Archived staff cannot act actively" | 07 §30 |
| Archived Audit Log | Impossible — entries are never archived | 07 §27 |

The last three matter for enforcement: archived rows in `roles`, `permissions`,
and `teacher_staff` are excluded from resolution by the `Archivable` scope, so
archiving a staff account revokes its access without deleting its history.

---

# 12. Audit interaction

RBAC reuses the frozen Audit Log. No new event type is needed — all ten already
exist in the `event_type` enum.

| Audited action | Event type | Source |
|---|---|---|
| Assign or revoke a Teacher Staff permission | `permission_change` | 23 §15.2 item 6 |
| Assign or revoke a role | `permission_change` | 08 §15 |
| Create / update / archive / restore a Teacher Staff account | `create` / `update` / `archive` / `restore` | 08 §15 |

**Attribution** (`08 §15`): Teacher Staff actions are attributed to the **staff
user, never the Teacher**; Super Admin actions to the Super Admin; Student and
Parent actions to the authenticated account.

**Authorization failures** are logged as security-relevant events
(`34_Error_Codes.md` AUTHZ-01), separately from the business Audit Log.

Entries remain append-only, immutable, and permanently retained — already
enforced in the `AuditLogEntry` model.

---

# 13. Error contract

RBAC introduces no new error code. All exist in `34_Error_Codes.md` §6.

The registry defines eight authorization outcomes (`34_Error_Codes.md` §6).

| Code | Status | When | Registry |
|---|---|---|---|
| `AUTHZ_UNAUTHORIZED` | 403 | Role, scope, ownership, or permission check failed | AUTHZ-01 |
| `AUTHZ_CROSS_WORKSPACE_ACCESS` | 403 | Write-style attempt against another workspace | AUTHZ-02 |
| `AUTHZ_WORKSPACE_CONTEXT_MISMATCH` | 403 | Action outside the active workspace context | AUTHZ-03 |
| `AUTHZ_STAFF_PERMISSION_MISSING` | 403 | Teacher Staff lacks the assigned capability | AUTHZ-04 |
| `AUTHZ_FLOW_A_MANAGEMENT_DENIED` | 403 | Flow A attempted outside Platform scope | AUTHZ-05 |
| `AUTHZ_VISIBILITY_EXPANSION_DENIED` | 403 | Attempt to exceed the Q-012 pending boundary | AUTHZ-06 |
| `PARENT_WRITE_DENIED` | 403 | Parent attempted any write | AUTHZ-07 |
| `AUTHZ_CLIENT_AUTHORITY_REJECTED` | 403 or 404 | Client tried to assert its own authority | AUTHZ-08 |
| `RESOURCE_NOT_FOUND` | 404 | Record invisible in the actor's scope | API-03 |

Note that AUTHZ-07 carries the code `PARENT_WRITE_DENIED`, not an `AUTHZ_`
prefix — a registry detail that implementation must reproduce exactly rather
than normalize.

**403 versus 404.** A 403 must not reveal that a resource exists; where
existence itself is private, the answer collapses to 404 so absent and
inaccessible are indistinguishable (`34 §2.8`).

Only `AUTHZ_UNAUTHORIZED` is currently registered in the `ErrorCode` enum. The
other eight are documented codes that the implementation phase will add to the
enum and to both translation files — an addition from the registry, never an
invention.

---

# 14. What Phase 45 will and will not do

**Will:** seed the 215-permission catalogue and the five roles; implement
resolution, scope, ownership, and Archive checks; register Gates and Policies;
audit permission changes.

**Will not:** add a table, column, or migration; change any authentication
endpoint, request, or response; resolve Q-011 or Q-012; create a Manager or any
platform staff role; introduce inheritance; grant hard delete; enforce
link-dependent Parent rules that need `parent_student_links`.

---

# RBAC CONTRACT READY

Specification complete and verified. Awaiting approval before implementation.
