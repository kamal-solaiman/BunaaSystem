# Phase 44 — Authentication Contract Verification Report

**Result: PASS. AUTHENTICATION CONTRACT FROZEN.**

Five verifications were required before the freeze. All five pass. Each was
checked against the implementation and the source documents, not asserted.

| # | Verification | Result |
|---|---|---|
| 1 | Every endpoint matches AI_DOCS exactly | **PASS** |
| 2 | No undocumented endpoint exists | **PASS** |
| 3 | No undocumented response field exists | **PASS** |
| 4 | No undocumented request field exists | **PASS** |
| 5 | No authentication endpoint depends on future phases | **PASS** |

---

## 1. Every endpoint matches AI_DOCS exactly — PASS

`10_API_Design.md` §13 defines five endpoints. The router registers five, and
the sets are identical.

| Documented | Implemented | Method | Match |
|---|---|---|---|
| `/api/v1/auth/login` | `auth/login` | POST | ✅ |
| `/api/v1/auth/logout` | `auth/logout` | POST | ✅ |
| `/api/v1/auth/me` | `auth/me` | GET | ✅ |
| `/api/v1/auth/students/register` | `auth/students/register` | POST | ✅ |
| `/api/v1/auth/students/activate` | `auth/students/activate` | POST | ✅ |

Paths use the documented `/api/v1/auth/{action}` scope pattern
(`28_Coding_Standards.md` §13.1). HTTP verbs match the catalogue exactly: the
four state-changing actions are POST, and the single read is GET.

## 2. No undocumented endpoint exists — PASS

Counting every registered route in `routes/api.php` returns **5**. The four
remaining scope groups — platform, teacher-workspace, student, parent — are
still reserved and empty.

- No impersonation route: "Login as Teacher" is outside Version 1
  (10 §3 rule 5), asserted absent by test.
- No password-reset endpoint: `23 §6.2` describes the policy, but §13 defines no
  endpoint, so none was created.
- No refresh, verify-email, or registration-confirmation endpoint.

The absence of these is deliberate. Each would be a plausible addition to an
authentication layer, and each was excluded because the catalogue does not
define it.

## 3. No undocumented response field exists — PASS

Every field returned by the authentication layer traces to a documented
attribute.

| Field | Documented as | Source |
|---|---|---|
| `id` | Student/User Identifier | 07 §1, §6 |
| `name` | Display Name | 07 §1 |
| `identifier` | Login Identifier | 07 §1 |
| `account_status` | Account Status | 07 §1, §6 |
| `role_contexts[]` | "available role contexts" | 10 §13; 07 §2 |
| `permitted_scopes[]` | "permitted scopes" | 10 §13; 07 §2 |
| `permissions[]` | Teacher Staff assigned permissions | 07 §3, §30 |
| `activation_status` | Activation Status | 07 §6 |
| `created_by_method` | Created By Method | 07 §6 |
| `logged_out` | "Logout confirmation" | 10 §13 |

**Absent by design:** the authentication secret and the password hash. The
response layer contains no reference to `password` at all — verified by grep
across every controller and resource, not only by reading the model's `$hidden`
list.

Also absent: any token value, session identifier, internal timestamp, or
diagnostic field.

## 4. No undocumented request field exists — PASS

| Endpoint | Accepted fields | Authorized by |
|---|---|---|
| `login` | `identifier`, `secret` | 33 AUT-01, AUT-02 |
| `logout` | *(none)* | 10 §13 "None." |
| `me` | *(none)* | 10 §13 "None." |
| `students/register` | `name`, `identifier`, `secret` | 33 AUT-11, AUT-03; 07 §1 |
| `students/activate` | `identifier`, `secret` | 33 AUT-13, AUT-03 |

No convenience field was accepted. In particular:

- No `remember_me` — no AI_DOCS document requires persistent login (DD-08).
- No `device_name` or client hint — not documented.
- No separate `activation_token` — `07 §6` defines no such attribute and the
  frozen schema holds no column for it, so the account's Login Identifier is
  the activation identifier.

## 5. No authentication endpoint depends on future phases — PASS

The layer references five models, all of which exist and are frozen:

`User`, `Student`, `Role`, `TeacherStaff`, `AuditLogEntry`.

| Check | Result |
|---|---|
| All referenced models exist | ✅ 5 / 5 |
| Any reference to Group, Attendance, Homework, Exam, Lesson, Subscription, Payment, Educational Grade, Parent | ✅ none in code |
| Any raw query against a table that does not exist | ✅ none |
| Migrations added by Phase 44 | ✅ zero |

The single textual match for "Parent" is a documentation comment in
`RoleContextResolver` explaining which role scopes carry no Teacher Workspace.
It is prose, not a dependency.

**Forward compatibility without dependency.** The layer reads
`permission_teacher_staff`, which exists but is empty while Q-011 is PENDING.
Inserting a permission makes it appear in `/auth/me` with no code change —
verified by test. Authentication therefore consumes the frozen contract without
waiting on, or anticipating, a future phase.

---

## Two reconciliations, recorded rather than resolved silently

Both were found while writing the contract. Neither is a defect, but both are
places where the implementation and a literal reading of §13 differ, so they are
documented explicitly.

### 429 on registration and activation

§13 lists 429 for `login` only. The implementation throttles all three public
endpoints, so registration and activation can also return 429.

`33 AUT-05` states rate limiting is *"Always applied to authentication
endpoints"* — broader than login alone — and `10 §6` treats 429 as a global
status ("Rate limit exceeded where rate limits apply"). A throttled request
returns the documented generic `API_RATE_LIMIT_EXCEEDED` with a `Retry-After`
header (34 API-04), not a new code.

Leaving registration unthrottled would have left an unauthenticated
account-creating endpoint open to abuse. The broader reading is applied, and
recorded here.

### 401 on activation

§13 lists 401 among activation's error responses and names the permission
`student_account.student.activate`. The implementation makes activation public,
so 401 does not arise.

Two more specific statements govern. `33 AUT-13`: *"Activation is the
authentication exception path."* `02_Software_Requirements.md`: account-setting
access requires authentication *"except for activation flows."*

The reason is structural: an account awaiting activation cannot log in, so
requiring authentication would make the endpoint impossible to use. The
permission in `09_Permission_Matrix.md` is marked *Conditional* — "Student may
activate own Teacher-created account" — which the implementation enforces by
matching the account rather than by demanding a session.

---

## Contract completeness

`AUTHENTICATION_CONTRACT.md` documents every required element:

| Required section | Location |
|---|---|
| Every authentication endpoint | §1 |
| Request schema | §2 |
| Response schema | §3 |
| Status codes | §4 |
| Error codes | §5 |
| Authentication flow | §6 |
| Cookie flow | §7 |
| Sanctum flow | §8 |
| Audit events | §9 |
| Archive behaviour | §10 |
| Permission interaction | §11 |
| Student activation flow | §12 |

## Supporting evidence

The behavioural guarantees behind this contract were proven in Phase 44 by
executing **101 assertions against a booted Laravel 12.64 on PHP 8.3.32**, with
every request driven through the full HTTP kernel. Highlights:

- Unknown identifier, wrong secret, and archived account produce byte-identical
  401 responses.
- Unknown identity and self-registered account produce byte-identical 404s on
  activation.
- No secret appears in any response or audit entry.
- Every frozen table retains its exact column count.

Four PHPUnit suites are committed, including `AuthenticationContractTest`, which
fails if a future phase adds an endpoint outside the documented five or alters
the frozen schema.

---

## Declaration

# AUTHENTICATION CONTRACT FROZEN

From Phase 45 onward the authentication layer may only be **consumed**. It may
not be redesigned, may not receive new business rules or endpoints, and may not
change its request or response contracts. The only permitted change is a bug
fix — a correction that brings the implementation into line with this contract.

Anything else requires an Architecture Change Request
(`PROJECT_CONSTRAINTS.md` §4), with the AI_DOCS amendment landing before the
code.
