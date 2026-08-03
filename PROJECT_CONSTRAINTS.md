# Project Constraints — Permanent Contract

**Status: THE FOUNDATION IS FROZEN.**
**Tag:** `foundation-v1`

This file is the permanent contract for the Bunaa System codebase. It binds
every contributor — human or AI — in every phase from Phase 43 onward.

A constraint here is not a guideline. Violating one is a defect, regardless of
whether the code works, the tests pass, or the reviewer agrees with the
constraint. The route to changing a constraint is §4, never a pull request that
quietly does the opposite.

**Precedence.** `AI_DOCS/00_Project_Context.md` is the Single Source of Truth.
This file governs *how code is written against it*. Where this file and
`AI_DOCS` disagree, `AI_DOCS` wins and this file must be corrected.

---

# 1. The ten prohibitions

Each is absolute. Each states what is forbidden, why, and how it is detected.

## 1.1 Never introduce a frontend/backend split

**Forbidden.** Creating `frontend/`, `backend/`, `laravel_app/`, `deployment/`,
or any second application root. Adding a second `composer.json`,
`package.json`, or `.env`. Moving React out of `resources/js`.

**Why.** The deployment target is one cPanel directory. A split reintroduces an
assembly step before every upload — the exact place a shared-hosting deployment
breaks. See `FOUNDATION_DECISIONS.md` D-F01, D-F02, D-F03.

**Detected by.** `ProjectStructureTest::the_project_is_a_single_laravel_application`
and `::react_lives_inside_laravel_resources`.

## 1.2 Never introduce Docker

**Forbidden.** `Dockerfile`, `docker-compose.yml`, `compose.yaml`, `.devcontainer/`,
`laravel/sail`, or any dependency that assumes a container runtime.

**Why.** `AI_DOCS/03_System_Architecture.md` §4.1 excludes Docker from Version 1,
and shared hosting cannot run a container. A container definition would create a
second, untested runtime that silently diverges from cPanel's PHP. See D-F06.

**Detected by.** `DeploymentReadinessTest::no_container_or_orchestration_files_exist`.

**Extends to.** Kubernetes, Redis, S3, WebSockets, message brokers, and
microservices — anything requiring a persistent process or external service.

## 1.3 Never introduce Nginx-specific code

**Forbidden.** Committing `nginx.conf`, server blocks, virtual hosts, or any
code branching on a specific web server. Depending on an Nginx-only directive
such as `try_files` or `X-Accel-Redirect`.

**Why.** cPanel provides Apache or LiteSpeed (`AI_DOCS/26_Deployment_Plan.md`
§4.1). Web-server configuration is a hosting concern, and `04_Project_Structure.md`
§6 states this repository does not prescribe server configuration contents.

**Permitted.** The two `.htaccess` files, which Apache and LiteSpeed both honor.
Protected file delivery must be implemented in PHP, not delegated to a server
directive.

## 1.4 Never introduce localhost assumptions

**Forbidden.** `localhost`, `127.0.0.1`, `::1`, `http://localhost:8000`,
`:5173`, `:3000`, or any development host or port in application code, frontend
source, or a committed default that reaches production.

**Why.** A development default that survives to production fails late and
quietly. See D-F08.

**Permitted.** `DB_HOST=127.0.0.1` in `.env.example` — on cPanel that is
correct, not a leftover. Laravel's own `env()` fallbacks inside published config
files, because every one is overridden in `.env.example`.

**Detected by.** Grep at audit; the only permitted textual matches are prose
comments asserting absence.

## 1.5 Never introduce hardcoded URLs

**Forbidden.** An absolute URL, domain, origin, or the deployment path `/113`
written into PHP, TypeScript, Blade, or a build config. Reading an API base from
anywhere other than `config/env.ts`. Interpreting the base path anywhere other
than `routes/basename.ts`.

**Why.** The base path is deployment data, not source. Hardcoding it bakes one
environment into the artifact and requires a rebuild to move. See D-F09, D-F10.

**Required instead.** `APP_URL` server-side; `VITE_API_BASE_URL` (relative
`/api/v1`) client-side; `route()` and `@vite` for generated URLs.

## 1.6 Never introduce hardcoded permissions

**Forbidden.** A permission string embedded in a controller, component, route
guard, or conditional. Any authorization decision made by comparing literals.
Any permission not present in `AI_DOCS/09_Permission_Matrix.md`.

**Why.** Custom RBAC (`AI_DOCS/08_RBAC.md`) resolves permissions through Gates
and Policies. A literal check bypasses the deny-by-default boundary and cannot
be audited against the matrix.

**Required instead.** Register a policy or gate; authorize through it. Teacher
Staff permissions are Teacher-assigned at runtime and are never static. Frontend
capability checks are usability only and never a security control.

**Note.** Teacher Staff permission granularity is **PENDING Q-011** and must not
be hardened.

## 1.7 Never introduce hardcoded roles

**Forbidden.** A role name compared as a string literal — `if ($user->role === 'teacher')`
or `role === 'admin'`. Inventing a role outside the confirmed five. Branching on
role where a permission or policy is the correct mechanism.

**Why.** Version 1 has exactly five roles: Super Admin, Teacher, Teacher Staff,
Student, Parent (`AI_DOCS/08_RBAC.md`). Role literals scatter authorization
across the codebase and break Teacher Workspace isolation the moment one is
missed.

**Required instead.** Authorize on capability through a policy or gate, not on
identity through a string.

## 1.8 Never introduce business logic outside Features

**Forbidden.** A business rule, workflow, calculation, or domain invariant in a
controller, middleware, route file, Blade template, React component, or
`app/Support`. A feature importing another feature's internals.

**Why.** Feature-Based Architecture is required by `AI_DOCS/04_Project_Structure.md`
§2–§3 and `11_Backend_Architecture.md` §3. Logic outside a feature cannot be
reviewed for isolation, Archive discipline, or Audit Log coverage. See D-F04.

**Where logic belongs.** `app/Features/{Feature}/` for server workflows;
`resources/js/features/{feature}/` for browser coordination;
`app/Services` or `app/Support` only for genuinely cross-feature, domain-neutral
concerns. Controllers stay thin (28 §3.2).

**Also forbidden.** Conflating Flow A (`Subscriptions`) with Flow B
(`Payments`); using `Classes` for Educational Grade, `Courses` for Lesson,
`Tenant` for Teacher Workspace, or `Delete` for Archive.

## 1.9 Never break AI_DOCS compatibility

**Forbidden.** Implementing behavior that contradicts `AI_DOCS/00`–`41`.
Inventing an API endpoint absent from `10_API_Design.md` §13–§30. Inventing an
error code absent from `34_Error_Codes.md`, or reusing a retired one. Hardening
a PENDING decision — **Q-005, Q-010, Q-011, Q-012, Q-013**, and the
timezone/currency part of **Q-015**. Introducing an out-of-scope feature:
notifications, payment gateways, marketplace behavior, video homework, native
mobile, multiple Teaching Subjects per Teacher, or multiple Parents per Student.

**Why.** `AI_DOCS` is the Single Source of Truth. Code that contradicts it makes
the documentation false, and the documentation is what every later phase and
every AI session reads first.

**Precedent.** Phase 42 invented `/api/v1/session`; the audit removed it because
`10_API_Design.md` §13 defines `auth/me` and nothing else.

**Also permanent.** Archive replaces deletion everywhere — no hard-delete path,
in any layer (D-F14). Important actions produce an append-only Audit Log entry.

## 1.10 Never modify project structure without updating AI_DOCS

**Forbidden.** Adding, removing, renaming, or relocating a top-level directory,
a feature directory, or an architectural boundary without amending `AI_DOCS` in
the same change.

**Why.** Structure and documentation drift apart silently, and every later
contributor then works from a false map.

**Required.** Structural change and documentation update ship together, under
§4. Renaming a feature also requires checking canonical terminology
(`04_Project_Structure.md` §11).

**Resolved.** `AI_DOCS/04_Project_Structure.md` described separate `backend/`
and `frontend/` roots, which the Phase 42 brief superseded (D-F01). It was
amended by explicit approval before Phase 43 in a documentation-only change
recorded in `FOUNDATION_CHANGELOG.md` §7. `AI_DOCS` and the codebase are
synchronized, and there is no outstanding documentation debt.

---

# 2. Invariants that follow from the foundation

Consequences of the frozen foundation. Breaking one breaks a prohibition above.

| # | Invariant | Source |
|---|---|---|
| 2.1 | Backend is the sole authority for authentication, authorization, isolation, Archive, and Audit Log. Frontend guards are usability only. | 28 §1.3; D-F03 |
| 2.2 | Teacher Workspace isolation is absolute: every query, response, file, report, and search result respects it. | 00; 28 §1.4 |
| 2.3 | Archive replaces deletion. No hard-delete path in any layer. | 28 §1.5, §2.4; D-F14 |
| 2.4 | Flow A (`Subscriptions`) and Flow B (`Payments`) stay separate everywhere. | 28 §2.7 |
| 2.5 | Every API response uses the documented envelope, built through `ApiResponse`. | 10 §6; D-F11 |
| 2.6 | No error reveals a stack trace, SQL, path, framework internal, or private data. Not-found and not-visible are indistinguishable. | 34 §2.8; 23 §18 |
| 2.7 | All endpoints live under `/api/v1` in the documented scope groups. | 10 §5; 28 §13.1 |
| 2.8 | Deny by default: a resource without a policy or gate is inaccessible. | 23 §2.1; D-F13 |
| 2.9 | All user-visible text is translatable. Arabic is default; direction derives from language. | 41 §4, §6, §7; D-F12 |
| 2.10 | Confirmed drivers only: Database sessions, File cache, Database queue, Public storage, MySQL 8 InnoDB `utf8mb4`, SMTP transport. | D-040…D-043; D-F05 |
| 2.11 | No symlink. Files are served through an authorized controller. | 04 §5; D-F07 |
| 2.12 | No Node.js on the production server; assets are built locally into `public/build`. | 26; D-F05 |
| 2.13 | Secrets never enter the repository or the frontend bundle. | 35 §10.1, §11.2 |
| 2.14 | All PHP declares `strict_types=1`; TypeScript is strict and `any` is prohibited. | 28 §4.6, §6.1, §6.6 |
| 2.15 | All four quality gates pass before a phase is complete. | D-F15 |

---

# 3. Quality gates

A phase is not complete until all four pass:

```bash
composer lint      # Pint — PSR-12 + declare(strict_types=1)
composer test      # PHPUnit
npm run lint       # ESLint — Rules of Hooks, no any, no unused
npm run typecheck  # TypeScript strict
npm test           # Vitest
npm run build      # Production build
```

Gates are not advisory and must not be weakened to make a change pass. A gate
that cannot run is treated as failing — `npm run lint` was declared before its
config existed, and its first real run immediately found an unsafe `any` the
type-checker had accepted.

---

# 4. Architecture Change Request

From the `foundation-v1` tag onward, **no structural change is permitted without
an approved ACR.** A structural change is anything touching a top-level
directory, an architectural boundary, a constraint in §1, an invariant in §2, a
confirmed driver, the deployment model, or a decision in
`FOUNDATION_DECISIONS.md`.

**Not** a structural change, and needing no ACR: adding a feature's classes
inside its existing directory, adding a documented endpoint from
`10_API_Design.md`, adding a migration for a documented entity, adding tests, or
adding a translation key.

### Procedure

1. **State the problem.** What is blocked, and why the current structure cannot
   accommodate it.
2. **Identify what it touches.** The constraint, invariant, or decision affected.
3. **Check `AI_DOCS` first.** If the change contradicts a document, the document
   must be amended by its own governance
   (`31_Master_Index.md` §8) *before* the code changes. Product-visible changes
   require Product Owner confirmation and a decision entry in
   `29_Project_Decisions.md`.
4. **Give the alternatives.** What was considered, and why it was insufficient.
5. **State the consequences.** Effects on deployment, isolation, Archive, Audit
   Log, Flow A/B separation, and every later phase.
6. **Obtain approval** from the Product Owner.
7. **Record it.** Add a `D-Fxx` entry to `FOUNDATION_DECISIONS.md`, update this
   file, update `AI_DOCS`, and note it in `FOUNDATION_CHANGELOG.md`.
8. **Prove it.** Add or update a test so the new rule is enforced, not merely
   documented.

An ACR is rejected if it would harden a PENDING decision, weaken Teacher
Workspace isolation, introduce a hard-delete path, conflate Flow A with Flow B,
add out-of-scope capability, or require infrastructure that cPanel shared
hosting cannot run.

---

# 5. Freeze statement

The foundation is frozen at tag `foundation-v1`.

At freeze it contains **no business logic**: no domain entity, no business rule,
no calculation, no workflow, and **zero registered API endpoints**. It provides
the structure, transport, error contract, localization, security posture, and
deployment shape that every feature will be built on.

It was verified by execution, not inspection: 120 runtime assertions against a
booted Laravel 12.64 on PHP 8.3.32, plus PHP syntax across all source files,
`strict_types` on 47/47 tracked PHP files, ESLint clean, TypeScript strict
clean, 13 Vitest tests, a passing production build, 0 npm vulnerabilities, and a
fresh-clone check confirming all 39 feature directories survive.

Phase 43 may now begin. It may add features. It may not change the foundation
except through §4.
