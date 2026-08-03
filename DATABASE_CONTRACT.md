# Database Contract — Phase 43

**Status: IMMUTABLE for the tables it covers.**
**Scope: the foundational identity, access, tenancy, and governance tables built in Phase 43.**

This document is the permanent contract for the tables listed below. Once
frozen, the structure of an `IMMUTABLE` table does not change; only rows are
added.

Every fact here was read from the built schema, not from the migration source.
The schema was created by running all 14 migrations against a live database and
inspecting the result.

> **Important scope note.** This contract does **not** declare the whole
> database complete. Phase 43 built the foundational entities only. Nine of the
> thirty-four logical entities in `07_Data_Dictionary.md` are implemented; the
> rest belong to later phases and will require their own migrations. See §4,
> which reports one blocking finding for Phase 44.

---

# 1. Classification summary

| Table | Classification | Future phases may modify? |
|---|---|---|
| `users` | **EXTENDABLE** | Nullable columns only, if AI_DOCS requires |
| `roles` | **IMMUTABLE** | No — rows only |
| `permissions` | **IMMUTABLE** | No — rows only |
| `role_user` | **IMMUTABLE** | No — rows only |
| `permission_teacher_staff` | **IMMUTABLE** | No — rows only |
| `teachers` | **EXTENDABLE** | One documented column pending (§4.4) |
| `teacher_workspaces` | **IMMUTABLE** | No — rows only |
| `teaching_subjects` | **IMMUTABLE** | No — rows only |
| `teacher_staff` | **IMMUTABLE** | No — rows only |
| `audit_log_entries` | **IMMUTABLE** | No — rows only, and rows are never changed |
| `migrations` | **SYSTEM** | Framework-managed |
| `sessions` | **SYSTEM** | Framework-managed |
| `password_reset_tokens` | **SYSTEM** | Framework-managed |
| `cache` *(not created)* | **SYSTEM** | n/a — File Cache is the confirmed driver |
| `jobs` | **SYSTEM** | Framework-managed |
| `job_batches` | **SYSTEM** | Framework-managed |
| `failed_jobs` | **SYSTEM** | Framework-managed |
| `personal_access_tokens` | **SYSTEM** | Sanctum-managed |

Definitions, as specified:

- **IMMUTABLE** — structure cannot change after Phase 43; only new rows may be added.
- **EXTENDABLE** — future **nullable** columns may be added **only** where AI_DOCS explicitly requires them.
- **SYSTEM** — framework tables; owned by Laravel or Sanctum, not by this contract.

---

# 2. Domain tables

## 2.1 `users` — EXTENDABLE

**Purpose.** The global authenticated identity used to access the Platform, and
the single identity foundation behind all five role contexts.

| Property | Value |
|---|---|
| Primary key | `id` (auto-increment) |
| Foreign keys | None — identity is global and references nothing |
| Unique constraints | `email` |
| Check constraints | None |
| Indexes | `email` (unique), `archived_at` |
| Archive behavior | `archived_at`. Archived users vanish from active queries, remain in history. **No hard delete exists** (07 §1). |
| Audit behavior | Referenced by `audit_log_entries.actor_user_id`. Login success and failure are mandatory audit events. |
| AI_DOCS | `07_Data_Dictionary.md` §1; `06_Database_Design.md` §12; `28_Coding_Standards.md` §12 |

**Columns:** `id`, `name`, `email`, `password`, `account_status` (default
`active`), `created_at`, `updated_at`, `archived_at`.

**Why EXTENDABLE.** `07 §1` documents a `Status` attribute whose complete value
set is deferred, and later role phases attach contexts to this identity. If a
future phase must record a documented identity attribute, it may add a
**nullable** column. It may not alter or drop an existing column.

## 2.2 `roles` — IMMUTABLE

**Purpose.** The five confirmed Version 1 roles.

| Property | Value |
|---|---|
| Primary key | `id` |
| Foreign keys | None |
| Unique constraints | `role_name` |
| Check constraints | `role_name` ∈ {super_admin, teacher, teacher_staff, student, parent}; `role_scope` ∈ {platform, teacher_workspace, student_account, parent_linked_students} — native `ENUM` on MySQL 8, `CHECK` on SQLite |
| Indexes | `role_name` (unique), `archived_at` |
| Archive behavior | `archived_at` |
| Audit behavior | Role assignment changes are audited as Permission Change (23 §15.2 item 6) |
| AI_DOCS | `07 §2`; `33 §3.3`; `08_RBAC.md` |

**Why IMMUTABLE.** Both value sets are confirmed closed. `07 §2` states Version
1 has exactly five roles, and `33 §3.3` fixes both enumerations. Adding a role
or scope is a documentation change first, not a migration.

**Currently 0 rows.** Roles are seeded by the authorization phase, not by a
migration.

## 2.3 `permissions` — IMMUTABLE (catalog empty, Q-011)

**Purpose.** A capability that can be granted, especially to Teacher Staff.

| Property | Value |
|---|---|
| Primary key | `id` |
| Foreign keys | None |
| Unique constraints | `(permission_name, permission_scope)` |
| Check constraints | `permission_scope` ∈ the four role scopes |
| Indexes | `(permission_name, permission_scope)` (unique), `archived_at` |
| Archive behavior | `archived_at` |
| Audit behavior | Permission changes are a mandatory audit event |
| AI_DOCS | `07 §3`; `06 §4`; `08_RBAC.md`; **Q-011 PENDING** |

**Why IMMUTABLE.** The structure holds any capability the RBAC catalog later
defines — a name, a scope, and a status. Resolving Q-011 adds **rows**, not
columns.

**Currently 0 rows, deliberately.** `07 §3` and `06 §4` both state granularity
"remains PENDING and must not be silently assumed."

## 2.4 `role_user` — IMMUTABLE

**Purpose.** Assigns a Role to a User in a specific context. Required because
"A User may have one or more Role contexts" (07 §1) is many-to-many.

| Property | Value |
|---|---|
| Primary key | `id` |
| Foreign keys | `user_id`→`users.id` RESTRICT; `role_id`→`roles.id` RESTRICT; `teacher_workspace_id`→`teacher_workspaces.id` RESTRICT |
| Unique constraints | `(user_id, role_id, teacher_workspace_id)` |
| Check constraints | None |
| Indexes | the unique triple; `(user_id, teacher_workspace_id)` |
| Archive behavior | None. A relationship record; `07 §2` declares no Archive State for the assignment. Revocation removes the assignment row; the change is audited. |
| Audit behavior | Assignment and revocation are Permission Change events |
| AI_DOCS | `07 §1`, `§2`; `06 §10`; `PHYSICAL_SCHEMA_DECISIONS.md` DD-03 |

`teacher_workspace_id` is nullable because Platform, Student Account, and
Parent Linked Students scopes have no owning workspace.

**Why IMMUTABLE.** It expresses exactly the documented cardinality — user, role,
context. Nothing further is needed to grant any V1 role.

## 2.5 `permission_teacher_staff` — IMMUTABLE

**Purpose.** Teacher Staff Permission Assignment: "permissions assigned by a
Teacher to Teacher Staff within a Teacher Workspace" (06 §4).

| Property | Value |
|---|---|
| Primary key | `id` |
| Foreign keys | `teacher_staff_id`→`teacher_staff.id` RESTRICT; `permission_id`→`permissions.id` RESTRICT; `teacher_workspace_id`→`teacher_workspaces.id` RESTRICT |
| Unique constraints | `(teacher_staff_id, permission_id)` |
| Check constraints | None |
| Indexes | the unique pair; `teacher_workspace_id` |
| Archive behavior | None — a relationship record. Revoking removes the row and audits the change. |
| Audit behavior | Every assignment change is a Permission Change event |
| AI_DOCS | `06 §4`; `07 §3`, `§30`; `23 §15.2` |

**Why IMMUTABLE.** Whatever granularity Q-011 confirms, an assignment is a
staff member, a permission, and the owning workspace.

## 2.6 `teachers` — EXTENDABLE

**Purpose.** The paying customer who operates one isolated Teacher Workspace.

| Property | Value |
|---|---|
| Primary key | `id` |
| Foreign keys | `user_id`→`users.id` RESTRICT; `teacher_workspace_id`→`teacher_workspaces.id` RESTRICT; `teaching_subject_id`→`teaching_subjects.id` RESTRICT |
| Unique constraints | `user_id`; `teacher_workspace_id` |
| Check constraints | None |
| Indexes | both uniques; `archived_at` |
| Archive behavior | `archived_at`. Archiving preserves all workspace history (06 §14). |
| Audit behavior | Create, update, archive, restore are mandatory audit events |
| AI_DOCS | `07 §4`; `06 §12` |

**Why EXTENDABLE — and this is a known, documented gap.** `07 §4` lists
"Subscription Status Reference | Reference | **Optional** | Must relate to
Flow A only." It was not created, because the `subscriptions` table does not
exist yet. The Subscription phase will add **one nullable foreign key**. This is
the single pre-authorized structural change to this table; see §4.4.

## 2.7 `teacher_workspaces` — IMMUTABLE

**Purpose.** The tenant boundary. One Teacher's completely isolated area.

| Property | Value |
|---|---|
| Primary key | `id` |
| Foreign keys | `teacher_id`→`teachers.id` RESTRICT |
| Unique constraints | `teacher_id` |
| Check constraints | None |
| Indexes | `teacher_id` (unique), `archived_at` |
| Archive behavior | `archived_at`. Archiving a workspace never erases its children (06 §14) — verified by test. |
| Audit behavior | The scope container: `audit_log_entries.teacher_workspace_id` |
| AI_DOCS | `07 §5`; `06 §6`, `§12`; `28 §12.5` (never `tenants`) |

`teacher_id` is nullable at the database level only, to break the documented
creation cycle; the application enforces the Required rule in one transaction
(DD-02).

**Why IMMUTABLE.** Every workspace-owned table in every later phase points *at*
this table. Its own structure carries nothing but identity, status, and archive
state, so growth happens in the referring tables.

## 2.8 `teaching_subjects` — IMMUTABLE

**Purpose.** The single subject associated with a Teacher account, selected once
and immutable thereafter.

| Property | Value |
|---|---|
| Primary key | `id` |
| Foreign keys | `teacher_id`→`teachers.id` RESTRICT; `teacher_workspace_id`→`teacher_workspaces.id` RESTRICT |
| Unique constraints | `teacher_id` — enforces exactly one subject per Teacher |
| Check constraints | None |
| Indexes | `teacher_id` (unique), `teacher_workspace_id`, `archived_at` |
| Archive behavior | `archived_at` |
| Audit behavior | Create and archive are audited; the subject cannot be updated after creation (BR-016) |
| AI_DOCS | `07 §31`; `06 §12` |

**Why IMMUTABLE.** `07 §31` closes the entity: multiple subjects per account are
explicitly unsupported in Version 1.

## 2.9 `teacher_staff` — IMMUTABLE

**Purpose.** An internal user created by a Teacher inside a Teacher Workspace.
Never called sub-teacher.

| Property | Value |
|---|---|
| Primary key | `id` |
| Foreign keys | `user_id`→`users.id` RESTRICT; `teacher_workspace_id`→`teacher_workspaces.id` RESTRICT |
| Unique constraints | `(user_id, teacher_workspace_id)` |
| Check constraints | None |
| Indexes | the unique pair; `(teacher_workspace_id, archived_at)` |
| Archive behavior | `archived_at`. "Archived staff cannot act actively", but Audit Log attribution survives (06 §14). |
| Audit behavior | Actions are attributed to the **staff user**, never the Teacher (23 §15.4) |
| AI_DOCS | `07 §30`; `06 §12` |

`staff_type_label` is free text, not an enum: `07 §30` calls Secretary,
Assistant, and Accountant *examples*, and `33 §3.3` confirms no closed set.

**Why IMMUTABLE.** Permission granularity lands in `permission_teacher_staff`
rows, not in new columns here.

## 2.10 `audit_log_entries` — IMMUTABLE (strictest)

**Purpose.** The append-only, permanent record of every important action.

| Property | Value |
|---|---|
| Primary key | `id` |
| Foreign keys | `actor_user_id`→`users.id` RESTRICT (nullable); `teacher_workspace_id`→`teacher_workspaces.id` RESTRICT (nullable) |
| Unique constraints | None — repeated identical events are legitimate history |
| Check constraints | `actor_role` ∈ the five roles; `scope_context` ∈ {platform, teacher_workspace}; `event_type` ∈ the ten mandatory events of 23 §15.2 |
| Indexes | `(actor_user_id, occurred_at)`, `(event_type, occurred_at)`, `(scope_context, occurred_at)`, `(teacher_workspace_id, occurred_at)`, `(affected_entity_name, affected_entity_id)` |
| Archive behavior | **None, by specification.** No `archived_at` column. `07 §27`: "Audit Log records are not archived or deleted." |
| Audit behavior | This *is* the audit subsystem. Rows are never updated or deleted — the model throws on both, so no actor including Super Admin can rewrite history (23 §15.4). |
| AI_DOCS | `07 §27`; `06 §8`; `23 §15.2`–`§15.4` |

`affected_entity_id` deliberately carries **no** foreign key: it references any
logical entity Platform-wide and must stay valid after the target is archived
(DD-05).

**Why IMMUTABLE — and permanently so.** The event-type enum already covers all
ten mandatory events for every future phase: Attendance Change, Exam
Modification, Homework Modification, and Subscription Change are present even
though those features do not exist yet. No later phase needs a new event type.

---

# 3. System tables

Framework-owned. Not covered by the IMMUTABLE guarantee, because Laravel and
Sanctum own their upgrade path.

| Table | Owner | Purpose | AI_DOCS |
|---|---|---|---|
| `migrations` | Laravel | Migration ledger | — |
| `sessions` | Laravel | Database session driver | D-040; `23 §7` |
| `password_reset_tokens` | Laravel | Password reset flow | `23 §6.2` |
| `jobs`, `job_batches`, `failed_jobs` | Laravel | Database Queue | D-042; `21_Background_Jobs.md` |
| `personal_access_tokens` | Sanctum | Token authentication | `10 §3`; D-001 |

**`cache` table intentionally absent.** Cache is **File** (D-041), so the
skeleton's cache migration was removed — the table would never be read.

---

# 4. Future-phase migration analysis

The instruction was: verify that no future authentication, RBAC, or Teacher
Workspace work requires changing the schema — and **stop and report** if any
does.

I checked each area against the documents rather than assuming. Two areas are
clear. One is not, and I am reporting it before Phase 44 rather than
discovering it mid-implementation.

## 4.1 Authentication — ⚠️ **REQUIRES NEW TABLES**

**Finding: Phase 44 cannot be completed without new migrations.**

`10_API_Design.md` §13 defines exactly five authentication endpoints. Three are
fully supported by the current schema:

| Endpoint | Schema support |
|---|---|
| `POST /auth/login` | ✅ `users`, `sessions`, `personal_access_tokens`, `audit_log_entries` |
| `POST /auth/logout` | ✅ `sessions` |
| `GET /auth/me` | ✅ `users`, `roles`, `role_user` |

The remaining two do not:

| Endpoint | Blocker |
|---|---|
| `POST /auth/students/register` | ❌ No `students` table |
| `POST /auth/students/activate` | ❌ No `students` table |

`07_Data_Dictionary.md` §6 defines the Student entity with attributes that have
nowhere to live today — in particular **Activation Status** ("Active or Pending
Activation") and **Created By Method** ("Self-Registration or Teacher-Created").
Activation is the entire subject of `/auth/students/activate`, and validation
rule `AUT-13` requires matching "exactly one pending-activation Teacher-created
account."

Storing Activation Status on `users.account_status` would be wrong: `07 §6`
places it on the Student entity, and `07 §1` gives User a *separate* Account
Status. Conflating them would invent a design the documents do not describe.

**This does not invalidate Phase 43.** The foundational tables are correct and
complete for what they cover. Phase 43's approved scope was schema + Archive +
Audit Log for the *foundational* entities; the Student entity was never in it.

**Recommended resolution — your decision:**

| Option | Effect |
|---|---|
| **A. Narrow Phase 44** to `login`, `logout`, `me` | No migration needed. Student registration and activation move to a later phase alongside the `students` table. Keeps this contract frozen exactly as written. |
| **B. Add the `students` table first** | A short Phase 43b creating `students` from `07 §6`, with the same traceability discipline, then Phase 44 delivers all five endpoints. |

I recommend **B** if you want the authentication surface complete in one piece,
and **A** if you prefer the contract to stay untouched and the phase to stay
small. Either way the new table would be added under a new contract entry, not
by modifying an IMMUTABLE table.

## 4.2 RBAC — ✅ **NO SCHEMA CHANGE REQUIRED**

`08_RBAC.md` §1 states it "does not define physical persistence structures."
Its stored elements are role, permission, and Teacher Staff assignment. All
five supporting tables exist:

`roles`, `permissions`, `role_user`, `permission_teacher_staff`,
`teacher_staff`.

Resolving **Q-011** inserts permission rows and assignment rows. The
`permission_scope` enum already spans all four role scopes, so any confirmed
capability fits without a structural change.

**Verified: RBAC work requires rows, not migrations.**

## 4.3 Teacher Workspace (tenancy mechanism) — ✅ **NO SCHEMA CHANGE REQUIRED**

The tenancy mechanism itself is complete: `teacher_workspaces` is the boundary,
`teachers` owns it one-to-one, `teacher_staff` is scoped to it, and
`audit_log_entries` records workspace scope.

Every later workspace-owned table will carry its own `teacher_workspace_id`
foreign key pointing at this table. That adds **new** tables; it does not modify
`teacher_workspaces`.

**Verified: the tenancy mechanism needs no migration.**

> Distinguish this from roadmap **Phase 3 features** — Educational Grades,
> Groups, Student movement, Teacher Workspace Settings. Those are new entities
> in `07_Data_Dictionary.md` §9, §10, §11, §12 and will each need their own
> table. That is expected growth, not a change to a frozen table.

## 4.4 One pre-authorized change

`teachers.subscription_status_id` — a **nullable** foreign key documented as
Optional in `07 §4`, deferred only because `subscriptions` does not exist yet.
`teachers` is classified **EXTENDABLE** for exactly this reason. No other
structural change to any table in §2 is pre-authorized.

---

# 5. Change control

For tables in §2, the rules are:

1. **IMMUTABLE tables may not change structure.** No column may be added,
   altered, renamed, or dropped. No index or constraint may be added or
   removed. Only rows are added.
2. **EXTENDABLE tables may gain nullable columns only**, and only where AI_DOCS
   explicitly documents the attribute. Existing columns are still immutable.
3. **SYSTEM tables** follow their framework's upgrade path.
4. **New tables are always allowed.** Later phases add their own entities; that
   is how the database grows.
5. **Any exception requires an Architecture Change Request**
   (`PROJECT_CONSTRAINTS.md` §4), and the documentation change lands before the
   code.

Permanent guarantees, regardless of phase:

- No hard delete. `archived_at` is the only removal mechanism, and every
  foreign key is `RESTRICT` so no cascade can bypass it.
- `audit_log_entries` is append-only, immutable, permanently retained, and
  never archived.
- Teacher Workspace isolation: every workspace-owned record carries its
  `teacher_workspace_id`.
- Canonical names only: `teacher_workspaces` never `tenants`; `archived_at`
  never `deleted_at`.

---

# 6. Verification

Every statement above was checked against a live database built by running all
14 migrations.

| Check | Result |
|---|---|
| Tables, columns, types, nullability, defaults | Read from the built schema |
| Foreign keys and delete behavior | 16 verified, all `RESTRICT` |
| Unique constraints | 10 verified, and violation attempts rejected |
| Check constraints (enums) | Native `ENUM` on MySQL 8, `CHECK` on SQLite |
| Indexes | 15 verified against `06 §10` priorities |
| Archive behavior | 11 assertions |
| Audit immutability | 12 assertions; update and delete both blocked |
| Q-011 preserved | `permissions` and `role_user` both empty |
| Phase 43 validation total | **169 assertions, 0 failures** |

---

# 7. Freeze status

**The tables in §2 are frozen as classified**, and the classifications above are
binding.

**A blanket "DATABASE FOUNDATION FROZEN" declaration is withheld**, because §4.1
found that Phase 44 cannot deliver two of its five documented endpoints without
a `students` table. Declaring the whole database frozen now would either be
untrue, or would force the Student entity into `users` in a way `07 §6` does not
describe.

Awaiting your decision between **Option A** (narrow Phase 44) and **Option B**
(add `students` first). Once chosen, the foundation can be declared frozen with
that decision recorded.
