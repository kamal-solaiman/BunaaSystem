# Authentication Contract — Phase 44

**Status: AUTHENTICATION CONTRACT FROZEN.**

This document is the permanent contract for the authentication layer. Every
schema below was read from the implementation, not from intent, and every field
is traced to the AI_DOCS section that authorizes it.

From Phase 45 onward authentication may only be **consumed**. See §13.

---

# 1. Endpoint catalogue

Exactly five endpoints exist. `10_API_Design.md` §13 defines five, and the
router registers five — verified by counting every registered route.

| # | Method | Path | Auth required | AI_DOCS |
|---|---|---|---|---|
| 1 | `POST` | `/api/v1/auth/login` | No (public) | 10 §13 |
| 2 | `POST` | `/api/v1/auth/logout` | Yes (`auth:sanctum`) | 10 §13 |
| 3 | `GET` | `/api/v1/auth/me` | Yes (`auth:sanctum`) | 10 §13 |
| 4 | `POST` | `/api/v1/auth/students/register` | No (public) | 10 §13 |
| 5 | `POST` | `/api/v1/auth/students/activate` | No — documented exception path | 10 §13; 33 AUT-13 |

No impersonation endpoint exists. "Login as Teacher" is explicitly outside
Version 1 (10 §3 rule 5) and its absence is asserted by test.

---

# 2. Request schemas

Every request field below is required by a documented rule. No field was added
for convenience.

## 2.1 `POST /auth/login`

| Field | Type | Required | Rule | Source |
|---|---|---|---|---|
| `identifier` | string, max 190 | Yes | Login Identifier; bounded text | 07 §1; 33 AUT-01, GEN-04 |
| `secret` | string | Yes | Authentication Secret; never echoed or length-reported | 07 §1; 33 AUT-02 |

Composition rules are **not** applied at login: AUT-02 states they apply at
set/change time only, and reporting them here would leak information about a
stored secret.

## 2.2 `POST /auth/logout`

No request body. Documented as "None." (10 §13).

## 2.3 `GET /auth/me`

No request body or parameters. Documented as "None." (10 §13).

## 2.4 `POST /auth/students/register`

| Field | Type | Required | Rule | Source |
|---|---|---|---|---|
| `name` | string, max 190 | Yes | Display Name | 07 §1; 33 GEN-04 |
| `identifier` | string, max 190 | Yes | Login Identifier | 07 §1; 33 AUT-11 |
| `secret` | string, min 8, upper + lower + digit | Yes | Secret composition | 33 AUT-03 |

AUT-11 states the detailed identity-field catalogue is deferred and "this
catalog adds no field", so only the attributes the frozen schema and confirmed
rules require are accepted. Special characters are recommended but not
mandatory (AUT-03), so none is required.

## 2.5 `POST /auth/students/activate`

| Field | Type | Required | Rule | Source |
|---|---|---|---|---|
| `identifier` | string, max 190 | Yes | Activation identifier | 33 AUT-13 |
| `secret` | string, min 8, upper + lower + digit | Yes | The Student sets their own secret | 33 AUT-03 |

`07 §6` defines no separate activation-token attribute and the frozen schema
holds none, so the account's Login Identifier is the activation identifier. No
token column was invented.

---

# 3. Response schemas

All responses use the envelope of `10_API_Design.md` §6 and §26.1 of
`34_Error_Codes.md`.

## 3.1 Success envelope

```
{ "success": true, "data": { … } }
```

## 3.2 `POST /auth/login` → 200

Documented as "Authenticated user context and available role contexts."

| Field | Type | Source |
|---|---|---|
| `data.id` | integer | 07 §1 User Identifier |
| `data.name` | string | 07 §1 Display Name |
| `data.identifier` | string | 07 §1 Login Identifier |
| `data.account_status` | string | 07 §1 Account Status |
| `data.role_contexts[]` | array of `{role, scope, teacher_workspace_id}` | 10 §13 "available role contexts"; 07 §2 |
| `data.permitted_scopes[]` | array of string | 10 §13 "permitted scopes"; 07 §2 Role Scope |
| `data.permissions[]` | array of string | 07 §3, §30 Teacher Staff assigned permissions |
| `data.student` | object, **only when a Student context exists** | 07 §6 |

`data.student` carries `id`, `activation_status`, `account_status`,
`created_by_method` — the four Student attributes an authenticated client needs
to know about its own account (07 §6).

**Never present:** the authentication secret, the password hash, or any field
not listed above. `password` is hidden on the model and absent from the
resource (23 §3.6).

## 3.3 `POST /auth/logout` → 200

Documented as "Logout confirmation."

| Field | Type |
|---|---|
| `data.logged_out` | boolean `true` |

## 3.4 `GET /auth/me` → 200

Identical shape to §3.2. Documented as "Current user identity, active role, and
permitted scopes."

## 3.5 `POST /auth/students/register` → 201

Documented as "Created Student account or activation-ready account context."

| Field | Type | Source |
|---|---|---|
| `data.id` | integer | 07 §6 Student Identifier |
| `data.activation_status` | `active` | 07 §6 |
| `data.created_by_method` | `self_registration` | 07 §6; 33 STU-01 |

## 3.6 `POST /auth/students/activate` → 200

Documented as "Activated Student account context."

| Field | Type | Source |
|---|---|---|
| `data.id` | integer | 07 §6 |
| `data.activation_status` | `active` | 07 §6 |
| `data.created_by_method` | `teacher_created` | 07 §6 |

## 3.7 Error envelope

```
{ "success": false, "error": { "code": "…", "message": "…" }, "request_id": "…" }
```

422 responses additionally carry `errors` with field-level messages
(10 §10). Error messages are translatable; codes are stable English identifiers
(41 §10).

---

# 4. Status codes

| Endpoint | Documented | Emitted | Match |
|---|---|---|---|
| `login` | 401, 422, 429 | 200, 401, 422, 429 | ✅ |
| `logout` | 401 | 200, 401 | ✅ |
| `me` | 401 | 200, 401 | ✅ |
| `students/register` | 409, 422 | 201, 409, 422, *429* | ✅ see note |
| `students/activate` | 401, 404, 409, 422 | 200, 404, 409, 422, *429* | ✅ see note |

**429 on registration and activation.** `33 AUT-05` states rate limiting is
"Always applied to authentication endpoints" — broader than login alone — so the
throttle covers all three public endpoints. A throttled request returns the
documented generic `API_RATE_LIMIT_EXCEEDED` with a `Retry-After` header
(34 §19). This is the documented global 429 behaviour ("Rate limit exceeded
where rate limits apply", 10 §6), not a new per-endpoint outcome.

**401 on activation.** §13 lists 401 among activation's error responses, but
`33 AUT-13` and `02_Software_Requirements.md` both state activation is the
authentication **exception** path — an account awaiting activation cannot log
in, so requiring authentication would make the endpoint unusable. The endpoint
is therefore public, and the 401 case does not arise. This is a reconciliation
of two AI_DOCS statements in favour of the more specific rule, recorded here
rather than resolved silently.

---

# 5. Error codes

Every code is registered in `34_Error_Codes.md`. None was invented.

| Code | Status | Endpoint | Meaning | Source |
|---|---|---|---|---|
| `AUTH_INVALID_CREDENTIALS` | 401 | login | Generic credential failure | 34 AUTH-02 |
| `AUTH_UNAUTHENTICATED` | 401 | logout, me | No valid authenticated context | 34 AUTH-01 |
| `VALIDATION_FAILED` | 422 | login, register, activate | Field validation failure | 34 VAL-01 |
| `STUDENT_DUPLICATE_ACCOUNT` | 409 | register | Global Student identity exists | 34 §9 |
| `STUDENT_ACTIVATION_MISMATCH` | 404 | activate | No matching pending account | 34 §9 |
| `STUDENT_ACCOUNT_ALREADY_ACTIVE` | 409 | activate | One-way transition already done | 34 §9 |
| `API_RATE_LIMIT_EXCEEDED` | 429 | public endpoints | Rate limit engaged | 34 API-04 |

All messages exist in Arabic and English (41 §10), verified for every code.

---

# 6. Authentication flow

```
1. Client calls GET /sanctum/csrf-cookie            → XSRF-TOKEN cookie set
2. Client POSTs /auth/login with X-XSRF-TOKEN header
3. Server looks up the identity in the ACTIVE scope  (archived excluded)
4. Server verifies the secret with Laravel hashing
   - a hash check runs even when no user matched, so a missing account and a
     wrong secret take comparable time
5. On failure  → audit Login/failure → 401 AUTH_INVALID_CREDENTIALS
   On success  → establish session, regenerate session id, audit Login/success
6. Client calls GET /auth/me for identity, role contexts, permitted scopes
7. Client POSTs /auth/logout → session destroyed, token revoked, event audited
```

**Non-disclosure.** An unknown identifier, a wrong secret, and an archived
account produce byte-identical 401 responses (23 §3.3; 33 AUT-04). Verified by
comparing the responses field by field, not by inspection.

**Session fixation.** The session identifier is regenerated on successful
authentication (23 §7.2).

---

# 7. Cookie flow

| Concern | Behaviour | Source |
|---|---|---|
| CSRF token | `GET /sanctum/csrf-cookie` issues `XSRF-TOKEN` | 23 §13.3 |
| State-changing requests | Must send `X-XSRF-TOKEN` | 23 §13.2 |
| Session cookie | `HttpOnly` | 23 §7.2 |
| Session cookie | `SameSite=Lax` | 23 §7.2 |
| Session cookie | `Secure` in production (`SESSION_SECURE_COOKIE=true`) | 23 §7.2 |
| Session storage | Database driver | D-040 |
| Remember-me cookie | **None** — no persistent login in Version 1 | DD-08 |

The React client reads `XSRF-TOKEN` and sends `X-XSRF-TOKEN`; the shared HTTP
boundary does this automatically via `withXSRFToken`.

---

# 8. Sanctum flow

| Concern | Behaviour |
|---|---|
| Guard | `sanctum`, applied to `logout` and `me` |
| First-party SPA | Stateful cookie authentication via `statefulApi()` |
| Token authentication | Supported through `personal_access_tokens`; the current token is revoked on logout |
| Stateful domains | `SANCTUM_STATEFUL_DOMAINS`, environment-driven |
| Unauthenticated | JSON 401, never an HTML login redirect |

Sanctum falls back to the `web` guard for first-party session requests, which is
why a cookie-authenticated SPA and a token client both resolve through the same
guard.

---

# 9. Audit events

Every authentication action is audited. Entries are append-only, immutable, and
permanently retained (23 §15.4).

| Action | Event type | Actor | Details recorded |
|---|---|---|---|
| Login success | `login` | the user | `outcome: success` |
| Login failure | `login` | **null** | `outcome: failure`, `attempted_identifier` |
| Logout | `login` | the user | `outcome: logout` |
| Student registration | `create` | the new user | `after.created_by_method` |
| Student activation | `update` | the activating user | `before` / `after` activation status |

Every entry carries origin: server timestamp, IP address, and user agent
(23 §15.3).

**The secret is never recorded** — verified by scanning all persisted audit
details for both correct and incorrect secrets after login attempts (33 AUT-02).

**Failed logins record no actor**, because `07 §27` requires an actor only
"where available" and a failed attempt against an unknown identifier has none.
Recording a fabricated actor would put false data in the one subsystem that must
never lie.

Registration and activation write the account change and the audit entry in one
database transaction (23 §15.4).

---

# 10. Archive behaviour

| Concern | Behaviour | Source |
|---|---|---|
| Archived account login | Denied | 23 §3.3 |
| Disclosure | Indistinguishable from wrong credentials | 23 §3.3 |
| Record retention | The archived user row is retained, never deleted | 06 §7 |
| Duplicate detection | Includes archived accounts | BR-022 |

The last row matters: an archived Student still holds the global identity, so
allowing re-registration would create exactly the duplicate BR-022 forbids.
Registration therefore checks `withTrashed()`.

---

# 11. Permission interaction

Authentication **reports** authorization state; it never grants it.

| Concern | Behaviour |
|---|---|
| Role contexts | Read from `role_user`, including the workspace for scoped roles |
| Permitted scopes | Derived from assigned role scopes |
| Teacher Staff permissions | Read from `permission_teacher_staff` |
| Unassigned contexts | Never reported (10 §13) |
| Authorization decisions | None made here — Gates and Policies remain the authority (08; 23 §4) |

**Q-011 is preserved.** The permission catalogue is empty, so `permissions` is
`[]` today. Inserting a permission into the frozen table makes it appear in
`/auth/me` with **no code change** — verified by test. Resolving Q-011 needs
nothing from this layer.

---

# 12. Student activation flow

The two confirmed registration methods (BR-022):

**Method 1 — self-registration**

```
POST /auth/students/register
  → duplicate check across ALL accounts, archived included
  → create User + Student (activation_status = active,
                           created_by_method = self_registration)
  → audit Create/Student
  → 201
```

**Method 2 — Teacher-created, then activated**

```
(Teacher creates the account in a later phase:
 activation_status = pending_activation, created_by_method = teacher_created)

POST /auth/students/activate
  → resolve exactly one pending-activation Teacher-created account (33 AUT-13)
  → set the Student's own secret
  → activation_status: pending_activation → active   (one-way)
  → audit Update/Student with before + after
  → 200
```

**Guards:**

| Case | Outcome |
|---|---|
| Unknown identity | 404 `STUDENT_ACTIVATION_MISMATCH` |
| Self-registered account | 404 — **identical response**, so activation cannot probe which identities exist |
| Already active | 409 `STUDENT_ACCOUNT_ALREADY_ACTIVE` |
| Duplicate creation | Impossible — activation updates, never creates |

---

# 13. Contract freeze

## AUTHENTICATION CONTRACT FROZEN

From Phase 45 onward, authentication may only be **consumed**:

1. **It may not be redesigned.** The flows in §6–§8 and §12 are fixed.
2. **It may not receive new business rules.** Domain rules belong to their own
   features.
3. **It may not receive new endpoints.** The five in §1 are the complete set.
4. **Request contracts may not change.** No field added, removed, renamed, or
   retyped (§2).
5. **Response contracts may not change.** No field added, removed, renamed, or
   retyped (§3).
6. **Status and error codes may not change** (§4, §5).

**The only permitted future change is a bug fix** — a correction that makes the
implementation match this contract. A change that alters the contract is not a
bug fix and requires an Architecture Change Request
(`PROJECT_CONSTRAINTS.md` §4), with the AI_DOCS amendment landing first.

Permanent guarantees:

- Login failures never disclose whether an account exists, whether the secret
  was wrong, or whether the account is archived.
- The authentication secret is never returned, logged, or audited.
- Every login attempt, success or failure, is audited.
- Archive is respected: archived accounts cannot authenticate and are never
  deleted.
- Authentication makes no authorization decision.
- The database schema stays frozen: authentication added zero migrations.
