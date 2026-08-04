# Phase 44 — Authentication Validation Report

**Result: PASS — 101 assertions, 0 failures.**
**Migrations added: 0. Schema modifications: 0. New tables: 0.**

Executed against a real Laravel 12.64 application booted on PHP 8.3.32, with
foreign keys enforced and every request driven through the full HTTP kernel
including middleware. Nothing below is asserted from reading the code.

| Validation area | Assertions | Result |
|---|---|---|
| 1. API endpoints | 7 | **PASS** |
| 2. Student registration | 13 | **PASS** |
| 3. Login | 9 | **PASS** |
| 4. Archive compatibility | 3 | **PASS** |
| 5. Current user / Sanctum | 6 | **PASS** |
| 6. Logout | 4 | **PASS** |
| 7. Student activation | 12 | **PASS** |
| 8. Audit log generation | 12 | **PASS** |
| 9. Role context / permission compatibility | 8 | **PASS** |
| 10. Session / cookie authentication | 9 | **PASS** |
| 11. Rate limiting | 2 | **PASS** |
| 12. Zero schema change | 16 | **PASS** |
| **Total** | **101** | **PASS** |

---

## 1. API endpoint tests — PASS

Exactly the five endpoints of `10_API_Design.md` §13 are registered, and no
sixth authentication surface exists.

| Endpoint | Registered |
|---|---|
| `POST /api/v1/auth/login` | ✅ |
| `POST /api/v1/auth/logout` | ✅ |
| `GET /api/v1/auth/me` | ✅ |
| `POST /api/v1/auth/students/register` | ✅ |
| `POST /api/v1/auth/students/activate` | ✅ |

- Exactly 5 auth routes exist — nothing invented.
- No impersonation route: "Login as Teacher" is not part of Version 1 (§3 rule 5).

## 2. Student registration — PASS

BR-022 Method 1, the Student registers their own account.

| Check | Result |
|---|---|
| Returns 201 with the success envelope | PASS |
| `created_by_method = self_registration` | PASS |
| Self-registered account is immediately `active` | PASS |
| Secret never echoed in the response | PASS |
| Password stored hashed (verified with `Hash::check`) | PASS |
| Duplicate identity rejected **409** `STUDENT_DUPLICATE_ACCOUNT` | PASS |
| Rejection reveals no Teacher context (AUT-12) | PASS |
| No second Student row created on duplicate | PASS |
| Weak secret rejected 422 (AUT-03) | PASS |
| Missing fields rejected 422 | PASS |

Duplicate detection queries **including archived accounts**: an archived
Student still holds the global identity, so reusing it would create exactly the
duplicate BR-022 forbids.

## 3. Login — PASS

| Check | Result |
|---|---|
| Valid credentials return 200 with identity | PASS |
| Response exposes `role_contexts` and `permitted_scopes` | PASS |
| Response never contains the secret or the word `password` | PASS |
| Wrong secret returns 401 `AUTH_INVALID_CREDENTIALS` | PASS |
| Unknown identifier returns 401 | PASS |
| **Unknown identifier and wrong secret are byte-identical** (AUT-04) | PASS |
| Missing credentials return 422 | PASS |

The two failure responses were compared field by field after stripping
`request_id`, so no account-existence disclosure is possible.

A hash check runs even when no user matches, so a missing account and a wrong
secret take comparable time and cannot be separated by timing.

## 4. Archive compatibility — PASS

| Check | Result |
|---|---|
| An archived account cannot authenticate | PASS |
| The refusal is **identical** to a wrong-credentials refusal | PASS |
| The archived user row is retained, never deleted | PASS |

23 §3.3 requires that a failure not reveal "whether the account is archived",
and that is verified by response comparison rather than by inspection.

## 5. Current user and Sanctum authentication — PASS

| Check | Result |
|---|---|
| Guest receives 401 `AUTH_UNAUTHENTICATED` | PASS |
| Guest receives JSON, never an HTML login redirect | PASS |
| Authenticated user reads their own identity | PASS |
| Student context reported when one exists | PASS |
| Secret never present in the response | PASS |
| Sanctum guard configured and active | PASS |

## 6. Logout — PASS

| Check | Result |
|---|---|
| Guest cannot log out (401) | PASS |
| Authenticated logout returns 200 with confirmation | PASS |
| Session data destroyed and token revoked | PASS |
| **Historical login Audit Log records retained** | PASS |

## 7. Student activation — PASS

BR-022 Method 2, the Teacher creates the account and the Student activates it.

| Check | Result |
|---|---|
| Cannot log in with the new secret before activating | PASS |
| Activation returns 200 and sets `active` | PASS |
| `created_by_method` remains `teacher_created` | PASS |
| **No duplicate Student created** (AUT-13) | PASS |
| Student can log in after activating | PASS |
| Re-activation rejected 409 `STUDENT_ACCOUNT_ALREADY_ACTIVE` | PASS |
| Unknown identity rejected 404 `STUDENT_ACTIVATION_MISMATCH` | PASS |
| A self-registered account cannot be activated | PASS |
| **Unknown and self-registered outcomes are identical** | PASS |

The last row matters: activation cannot be used to probe which identities
exist, because every mismatch produces the same neutral response.

## 8. Audit log generation — PASS

| Check | Result |
|---|---|
| Login events audited (23 §15.2 item 5) | PASS |
| Successful login records actor and role | PASS |
| Success entry carries origin IP | PASS |
| **Failed login audited** with no actor (AUT-06) | PASS |
| Failure records the attempted identifier | PASS |
| **No secret ever written to the Audit Log** (AUT-02) | PASS |
| Registration audited as `create` on `Student` | PASS |
| Activation audited as `update` with before/after snapshot | PASS |
| Audit entries remain immutable | PASS |

Registration and activation write their account change and their audit entry in
one transaction, so an action can never be persisted without its record
(23 §15.4 transactional guarantee).

## 9. Role context and permission compatibility — PASS

| Check | Result |
|---|---|
| Assigned role context reported | PASS |
| Role scope reported | PASS |
| `permitted_scopes` reflects the assignment | PASS |
| Workspace-scoped role carries its `teacher_workspace_id` | PASS |
| Teacher Staff permissions empty while **Q-011 is PENDING** | PASS |
| An assigned permission surfaces **without any code change** | PASS |
| No unauthorized role context leaked | PASS |

The sixth row is the forward-compatibility proof: a permission was inserted
into the frozen `permission_teacher_staff` table and appeared in `/auth/me`
immediately. When the authorization phase seeds the catalog, nothing in the
authentication layer needs to change.

## 10. Session and cookie tests — PASS

| Check | Result |
|---|---|
| Database session driver (D-040) | PASS |
| `sessions` table present | PASS |
| Session cookie `HttpOnly` | PASS |
| Session cookie `SameSite=Lax` | PASS |
| `SESSION_SECURE_COOKIE=true` in the template | PASS |
| Sanctum stateful domains configured | PASS |
| `/sanctum/csrf-cookie` reachable | PASS |
| **`XSRF-TOKEN` cookie issued** | PASS |
| CSRF route registered by Sanctum | PASS |

Session identifiers are regenerated on successful login, which is the documented
session-fixation defence (23 §7.2).

## 11. Rate limiting — PASS

`33_Validation_Rules.md` AUT-05 confirms a limit must exist while stating the
threshold "must not be presented as product values".

| Check | Result |
|---|---|
| An `auth` limiter is configured and applied to the public endpoints | PASS |
| The threshold lives in configuration, not in code | PASS |

The limiter is keyed by identifier **and** IP together, so an attacker cannot
lock a legitimate account out by exhausting its allowance from elsewhere.

## 12. Zero schema change — PASS

| Check | Result |
|---|---|
| Migration count unchanged (15) | PASS |
| `users` still 8 columns | PASS |
| `students` still 8 columns | PASS |
| `roles` still 7 columns | PASS |
| `permissions` still 7 columns | PASS |
| `teachers` still 8 columns | PASS |
| `teacher_workspaces` still 6 columns | PASS |
| `teaching_subjects` still 8 columns | PASS |
| `teacher_staff` still 8 columns | PASS |
| `audit_log_entries` still 13 columns | PASS |
| No `login_attempts`, `activation_tokens`, or `password_resets` table | PASS |

**The Phase 43B prediction held: authentication required zero migrations.**

---

## One real defect found and fixed

**Logout returned HTTP 500.** Laravel's `SessionGuard::logout()` calls
`getRememberToken()`, which faulted with `MissingAttributeException` because
`remember_token` was deliberately removed in Phase 43 (DD-08: no AI_DOCS
document requires persistent login).

Two options existed. Adding the column would have broken the freeze and
introduced storage for a feature Version 1 does not have. Instead, `User` now
declares `protected $rememberTokenName = ''`, which is the framework's own
mechanism for "this model has no remember token" — `getRememberToken()` then
returns null rather than faulting.

**Schema untouched; the defect is covered by a committed test.**

Three further failures during validation were harness artifacts, each confirmed
against a clean application instance before being dismissed: guards legitimately
cache the authenticated user in-process, a shared session made post-login
"guest" calls genuinely authenticated, and the sandbox has no Vite build so the
SPA shell could not render. None was an application defect, and the guest-401
behaviour was proven correct in an isolated process.

---

## Committed regression tests

| Test file | Covers |
|---|---|
| `tests/Feature/Authentication/LoginTest.php` | Credentials, non-disclosure, archived accounts, login auditing |
| `tests/Feature/Authentication/CurrentUserAndLogoutTest.php` | Current user, role contexts, permissions, logout, audit retention |
| `tests/Feature/Authentication/StudentRegistrationAndActivationTest.php` | Both registration methods, duplicate prevention, activation lifecycle |
| `tests/Feature/Authentication/AuthenticationContractTest.php` | Exactly five endpoints, no impersonation, **zero schema change** |

## Scope compliance

| Rule | Status |
|---|---|
| Zero database migrations | ✅ 15 before, 15 after |
| Zero schema modifications | ✅ every frozen column count unchanged |
| Zero new tables | ✅ verified |
| Zero undocumented fields | ✅ no column added anywhere |
| Zero undocumented relationships | ✅ `RoleContextResolver` queries `TeacherStaff` directly rather than adding a `User` relationship |
| Zero model changes beyond authentication need | ✅ one line on `User`: `$rememberTokenName = ''`, required to make logout work without a migration |
| Zero business logic outside authentication | ✅ no Teacher, Group, Attendance, or other domain behaviour |
| Everything uses the frozen contract | ✅ |

## Toolchain

| Gate | Result |
|---|---|
| PHP syntax, all 103 files | 0 errors |
| `declare(strict_types=1)` | all files |
| ESLint | PASS |
| TypeScript strict | PASS |
| Vitest | 13 passed |
| Production build | PASS |
