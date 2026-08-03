# Phase 45 — RBAC Contract Verification Report

**Result: PASS. RBAC CONTRACT READY.**
**No RBAC implementation was written.**

Five verifications were required before the contract could be declared ready.
All five pass. Each was checked against the source documents and the frozen
contracts, not asserted.

| # | Verification | Result |
|---|---|---|
| 1 | Matches AI_DOCS exactly | **PASS** |
| 2 | Uses only frozen database tables | **PASS** |
| 3 | Requires zero migrations | **PASS** |
| 4 | Requires zero authentication changes | **PASS** |
| 5 | Requires zero API contract changes | **PASS** |

---

## 1. Matches AI_DOCS exactly — PASS

### Roles

| Source | Roles |
|---|---|
| `08_RBAC.md` §3 | Super Admin, Teacher, Teacher Staff, Student, Parent |
| `07_Data_Dictionary.md` §2 | Super Admin, Teacher, Teacher Staff, Student, Parent |
| Frozen `roles.role_name` enum | `super_admin`, `teacher`, `teacher_staff`, `student`, `parent` |

Three independent sources agree on exactly five. **No sixth role.**

### Scopes

`33_Validation_Rules.md` §3.3 confirms four Role Scopes, and both frozen enums —
`roles.role_scope` and `permissions.permission_scope` — hold exactly those four.

### Permission catalogue

`09_Permission_Matrix.md` defines **215 distinct permissions**, counted from the
document rather than estimated:

| Name prefix | Count |
|---|---|
| `platform.` | 60 |
| `teacher_workspace.` | 92 |
| `student_account.` | 36 |
| `parent_linked_student.` | 29 |
| **Total** | **215** |

The catalogue fits the frozen schema: the longest name is
`teacher_workspace.teacher_staff.self_assign_permission` at 54 characters,
within `permission_name`'s 190-character bound, and all 215 satisfy the unique
`(permission_name, permission_scope)` constraint.

### Flows

The permission-resolution flow reproduces the nine steps of `08_RBAC.md` §14 in
order. The Role Context flow reproduces `07 §1`–`§2`. Audit attribution
reproduces `08 §15`.

### Three findings worth stating

**Manager is not a role.** The brief asked for "Manager behavior". Searching all
42 AI_DOCS files returns five matches for "manager", every one unrelated — *PHP
Extension Manager* (three) and *release manager* (two). `09 §12` explicitly
denies `platform.user.create_platform_staff` for all four columns because
"Platform staff roles such as Support, Sales, and Accountant are out of scope
for Version 1."

Documenting a Manager role would have violated `PROJECT_CONSTRAINTS.md` §1.7
(no hardcoded roles outside the confirmed five) and §1.9 (no contradiction of
AI_DOCS). The contract records it as a deliberate **non-role** in §7.6 instead.

**Singular versus plural scope naming.** Permission *names* use the prefix
`parent_linked_student.` while the *scope enum* is `parent_linked_students`.
Both are correct in their own place, and §2 of the contract flags this so an
implementer does not "normalize" one into the other and break either the
catalogue or the frozen enum.

**`PARENT_WRITE_DENIED` breaks the prefix pattern.** Registry entry AUTHZ-07
carries the code `PARENT_WRITE_DENIED`, not `AUTHZ_PARENT_WRITE_DENIED`. Codes
are permanent public contracts (`34 §2.7`), so §13 records the exact string
rather than a tidier one.

---

## 2. Uses only frozen database tables — PASS

Every table RBAC needs is already frozen by `DATABASE_CONTRACT.md`.

| Table | Contract classification | Verified present |
|---|---|---|
| `roles` | IMMUTABLE | ✅ |
| `permissions` | IMMUTABLE | ✅ |
| `role_user` | IMMUTABLE | ✅ |
| `permission_teacher_staff` | IMMUTABLE | ✅ |
| `teacher_staff` | IMMUTABLE | ✅ |
| `teacher_workspaces` | IMMUTABLE | ✅ |
| `users`, `students` | EXTENDABLE / IMMUTABLE | ✅ |
| `audit_log_entries` | IMMUTABLE | ✅ |

Seeding the catalogue inserts **rows**, which every IMMUTABLE classification
explicitly permits ("only new rows may be added").

### One scope boundary reported now, not later

The **Parent Student Link** is a distinct logical entity (`06 §4`) and has **no
table**. Of the 29 Parent permissions, 6 are Allowed, 9 Conditional, 14 Denied.

- Registering all 29 in the catalogue needs nothing new.
- Enforcing the **9 Conditional** ones depends on an actual Parent-to-Student
  link, which requires `parent_student_links` — a table belonging to the Parent
  phase.

This is a scope boundary, not a blocker. Phase 45 can register the full
catalogue and enforce role, scope, ownership, and Archive. Link-dependent Parent
checks activate when the Parent phase adds its table. Reported here so it is not
discovered mid-implementation, exactly as the missing `students` table was
reported before Phase 44.

---

## 3. Requires zero migrations — PASS

| Check | Result |
|---|---|
| New table required | **None** |
| New column required | **None** |
| Enum widening required | **None** |
| Migration count | **15 before, 15 after** |

Two specific confirmations:

- **`event_type` needs no new value.** All ten mandatory audit events already
  exist in the frozen enum, including `permission_change`, which is exactly what
  RBAC needs (`23 §15.2` item 6).
- **`permission_scope` needs no new value.** All four scopes the 215 permissions
  require are already in the enum.

---

## 4. Requires zero authentication changes — PASS

The authentication contract froze at Phase 44 as **consume-only**. RBAC consumes
it without modification.

| RBAC needs | Already provided by | Change required |
|---|---|---|
| Authenticated identity | `auth:sanctum` guard | None |
| Role contexts | `RoleContextResolver::contextsFor()` | None |
| Permitted scopes | `RoleContextResolver::permittedScopesFor()` | None |
| Staff capabilities | `RoleContextResolver::assignedPermissionsFor()` | None |
| Actor role for audit | `RoleContextResolver::primaryRoleFor()` | None |
| Deny-by-default boundary | `AuthServiceProvider` `Gate::after` | None |

`RoleContextResolver` already implements all four methods RBAC depends on, and
`GET /auth/me` already returns `role_contexts`, `permitted_scopes`, and
`permissions`. Phase 44 proved forward compatibility by inserting a permission
into `permission_teacher_staff` and observing it appear in `/auth/me` with no
code change.

---

## 5. Requires zero API contract changes — PASS

| Check | Result |
|---|---|
| Authentication endpoints | **5 before, 5 after** |
| Request schemas | Unchanged |
| Response schemas | Unchanged |
| Error envelope | Unchanged |

RBAC adds no endpoint. It adds authorization *enforcement* to endpoints that
later phases will register under the already-reserved scope groups.

The eight additional AUTHZ codes are **registry entries**, not contract changes:
they exist in `34_Error_Codes.md` §6 and will be added to the `ErrorCode` enum
and both translation files. `34 §27` permits exactly this — registered codes may
be implemented; unregistered ones may not be invented.

---

## Contract completeness

`RBAC_CONTRACT.md` covers every element the brief required:

| Required | Location | Note |
|---|---|---|
| Every role | §1 | Five |
| Every scope | §2 | Four |
| Every permission entity | §3 | Five frozen tables, 215 permissions |
| Every permission relationship | §4 | |
| Permission resolution flow | §5 | 08 §14, nine steps |
| Role Context resolution flow | §6 | Already implemented and frozen |
| Teacher Staff resolution | §7.3 | Intersection, not inheritance |
| Super Admin behaviour | §7.1 | Q-012 boundary preserved |
| **Manager behaviour** | §7.6 | **Documented as a non-role** |
| Teacher behaviour | §7.2 | |
| Parent behaviour | §7.5 | Link-table boundary noted |
| Student behaviour | §7.4 | Per-Teacher partitioning |
| Permission inheritance | §8 | **None exists** — stated explicitly |
| Permission precedence | §9 | Derived from deny-by-default |
| Conflict resolution | §10 | No conflict is representable |
| Archive interaction | §11 | |
| Audit interaction | §12 | |

Three of these — inheritance, precedence, and conflict resolution — are **not
explicitly documented in AI_DOCS**. I verified that by searching `08_RBAC.md`
and `09_Permission_Matrix.md` for inherit, precedence, conflict, override, and
supersede: the only hit is an unrelated sentence about terminology.

Rather than inventing rules, the contract derives each from documented
principles — deny by default (`08 §2.1`), the ordered flow (`08 §14`), and
session isolation (`23 §7.3`) — and labels them as derivations. §8 in particular
states an **absence**, because an implementer assuming a conventional role
hierarchy would break tenant isolation immediately.

---

## PENDING decisions preserved

| Item | Status | Treatment |
|---|---|---|
| **Q-011** Teacher Staff granularity | PENDING | Catalogue registered; no preset or default staff grant |
| **Q-012** Super Admin content visibility | PENDING | Conditional-pending treated as **denied** until resolved |
| Non-payment enforcement (Q-005) | PENDING | Not an active permission (`09 §18`) |

Neither pending decision is hardened. `09 §18` states enforcement behaviour
"cannot be treated as an active permission", and the contract honours that.

---

# RBAC CONTRACT READY

The specification is complete and verified against AI_DOCS, the frozen database
contract, and the frozen authentication contract.

**No implementation was written.** Awaiting approval before Phase 45 code.
