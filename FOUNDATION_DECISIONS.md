# Foundation Decisions

Architectural decisions taken while building the Phase 42 foundation, with the
reasoning, the alternatives rejected, and the consequences each one imposes on
every later phase.

Decisions are numbered **D-F01…D-F16**. The `D-F` prefix keeps them distinct
from the product decisions `D-001…D-050` in `AI_DOCS/29_Project_Decisions.md`,
which these must never contradict.

A decision recorded here is binding. Changing one requires an Architecture
Change Request under `PROJECT_CONSTRAINTS.md` §4.

---

## D-F01 — Single Laravel application

**Decision.** The repository is one Laravel 12 application. `app/`, `bootstrap/`,
`config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`, `tests/`,
and `vendor/` sit at the root. No `frontend/`, `backend/`, `laravel_app/`,
`deployment/`, or `scripts/` directory exists.

**Why.** The deployment target is a single cPanel directory, `public_html/113`.
A two-root repository has to be reassembled before it can be uploaded — built,
copied, merged — and that assembly step is exactly where a shared-hosting
deployment goes wrong: a stale bundle, a path that resolved locally but not on
the server, an `.env` that landed in the wrong root. Collapsing the split
removes the assembly step: the repository *is* the deployable unit.

It also removes a class of drift. With one `composer.json`, one `package.json`,
and one `.env`, configuration cannot disagree with itself.

**A documented divergence.** `AI_DOCS/04_Project_Structure.md` §1 describes a
repository with separate `backend/` and `frontend/` roots. The Phase 42 brief
supersedes it. This is the **only** place the foundation knowingly departs from
that document, and it is recorded here rather than applied silently.
`04_Project_Structure.md` should be amended to match before Phase 43, per
`PROJECT_CONSTRAINTS.md` §3.10.

**Rejected.** A monorepo with two roots and a build step that copies the bundle
into Laravel's `public/` — rejected because it reintroduces the assembly step
without giving anything back on a single-tenant shared host.

**Consequence.** No phase may reintroduce the split (`PROJECT_CONSTRAINTS.md`
§3.1), and `ProjectStructureTest` fails the build if one appears.

---

## D-F02 — React lives in `resources/js`

**Decision.** All React, TypeScript, and styling source lives under
`resources/js`. Vite's inputs are `resources/js/styles/app.css` and
`resources/js/app/main.tsx`.

**Why.** It is where Laravel already expects browser source, so `@vite`, the
Vite plugin, hot reload, and the manifest all work without custom paths.
`AI_DOCS/04_Project_Structure.md` §3 defines the internal shape — `app/`,
`features/`, `components/`, `lib/`, `routes/`, `layouts/`, `types/` — and that
shape is preserved exactly; only its parent directory changed.

**Consequence.** No React source may exist outside `resources/js`. There is no
`index.html`: Laravel renders the shell, because the shell must carry the CSRF
token, the resolved locale and direction, and the base path — none of which a
static HTML entry could supply.

---

## D-F03 — No frontend/backend separation

**Decision.** One repository, one dependency graph, one deployable artifact. The
boundary between server and browser is enforced by **architecture**, not by
directory distance.

**Why.** The separation that matters is not physical. What must hold is that the
browser never becomes an authority: Laravel owns authentication, authorization,
Teacher Workspace isolation, Archive behavior, and Audit Log recording, and the
React application only presents what the API returns
(`AI_DOCS/28_Coding_Standards.md` §1.3). That guarantee comes from the deny-by-default
gate, the single HTTP boundary, and server-side policies — none of which is
strengthened by putting the code in a sibling folder.

Physical separation would, however, cost something real: two installs, two
lockfiles, two `.env` files, and a merge step before every upload.

**Consequence.** Route guards in `resources/js/routes` are **usability**
measures. They are never a security control, and no phase may treat them as one.

---

## D-F04 — Feature-Based Architecture

**Decision.** Both sides are organized by business capability, not by technical
layer. `app/Features/{PascalCase}` and `resources/js/features/{kebab-case}`,
with the same 20 canonical features on the backend and 19 on the frontend.

**Why.** `AI_DOCS/04_Project_Structure.md` §2–§3 and
`11_Backend_Architecture.md` §3 require it, and the reason holds up: this system
is a set of largely independent capabilities — Attendance, Homework, Exams,
Subscriptions — that share identity and isolation rules but little else. Grouping
by layer would scatter one capability across six directories and make it hard to
see whether a rule is enforced. Grouping by capability keeps a feature's
services, requests, and queries together, so its boundary is legible and a
reviewer can check isolation in one place.

The naming is not cosmetic. `EducationalGrades` (never `Classes`), `Lessons`
(never `Courses`), `TeacherWorkspace` (never `Tenant`), `Subscriptions` for
Flow A and `Payments` for Flow B — canonical terminology is mandatory
(`04_Project_Structure.md` §11), and the two money flows must never be conflated.

**Consequence.** Business logic lives in a feature. A feature must not import
another feature's internals; cross-feature reuse goes through
`app/Services`, `app/Support`, or a shared domain-neutral component. All 39
directories are kept in version control by a documented `.gitkeep`, because Git
does not track empty directories — without them the architecture would not
survive a clone.

---

## D-F05 — Deployment targets cPanel shared hosting

**Decision.** cPanel with Apache or LiteSpeed, PHP 8.3, MySQL 8, uploaded to
`public_html/113`.

**Why.** It is the confirmed target (`AI_DOCS/26_Deployment_Plan.md` §4.1;
`03_System_Architecture.md` §4.1). Every infrastructure decision follows from
its constraints rather than from preference: no root access, no daemons, no
process manager, often no SSH, and frequently no symlinks.

**Consequence.** This is why cache is **File**, queue is **Database**, sessions
are **Database**, and scheduling is a single Cron entry calling
`schedule:run` — each is a decision that needs no persistent process
(D-040…D-043). No phase may introduce a component that requires one.

---

## D-F06 — No Docker

**Decision.** No `Dockerfile`, no `docker-compose.yml`, no container tooling.
`laravel/sail` was removed from the dependencies it ships in by default.

**Why.** `AI_DOCS/03_System_Architecture.md` §4.1 states Version 1 must not
require Docker, and shared hosting cannot run a container regardless. Keeping a
container definition would create a second, untested environment: the risk is
that development runs on the container's PHP and extensions while production
runs cPanel's, and the difference surfaces only after deployment.

**Consequence.** Environment parity is maintained by pinning versions —
`"php": "^8.3"` and `platform.php: 8.3.0` in `composer.json` — not by shipping a
runtime.

---

## D-F07 — No symlinks

**Decision.** No symlink anywhere. `config/filesystems.php` declares
`'links' => []`, and `php artisan storage:link` is never part of deployment.

**Why.** Two independent reasons, either sufficient.

Operationally, shared hosts frequently disable `symlink()` or break links when
restoring from backup, so a deployment that depends on one is fragile.

More importantly, it would be **wrong even where it works**.
`AI_DOCS/04_Project_Structure.md` §5 requires that every file request pass
through backend authorization — Teacher Workspace scope, Student relationship,
Parent linked-Student scope, Archive state, ownership — and that *"paths must
not be accepted from the browser as authorization proof."* A public symlink
makes every stored file reachable by URL, bypassing all of it. A Teacher's
private Lesson video would be one guessed path away from exposure.

**Consequence.** Files are delivered through an authorized controller, to be
built in the Files phase. `public/storage` stays in `.gitignore` so an
accidental `storage:link` cannot be committed, and `DeploymentReadinessTest`
asserts no link exists.

---

## D-F08 — No localhost or development assumptions

**Decision.** No development host, port, or absolute URL is compiled into the
application or assumed at runtime.

**Why.** A development default that survives to production fails in the worst
way — not loudly at deploy time, but later, when a request goes to the wrong
origin or a cookie is refused.

**How it is achieved.** Laravel's stock config files contain `localhost`
fallbacks inside `env()` calls; every one is overridden in `.env.example`, so
none can reach production. `VITE_API_BASE_URL` defaults to the **relative**
`/api/v1`, so the bundle carries no origin at all. `DB_HOST=127.0.0.1` is
retained deliberately — on cPanel that is the correct value, not a leftover.

**Consequence.** Verified by inspection at audit: the only remaining textual
matches for "localhost" are prose comments asserting its absence.

---

## D-F09 — `APP_URL` is the single source of the base path

**Decision.** The deployed path lives in `APP_URL`. Laravel renders it into a
`<meta name="app-base">` tag, and React Router reads that tag at runtime.

**Why.** The application must work at `public_html/113` today and at a domain
root, or a different subdirectory, later. The base path is therefore
**deployment data, not source**. Hardcoding `/113` — in `vite.config.ts`, in the
router, or in an asset path — would bake one environment's shape into the
artifact and require a rebuild to move it.

Resolving at runtime from a server-rendered tag also keeps client and server in
agreement: there is one value, set once in `.env`, and both read it.

**Consequence.** The identical build runs in any environment. No phase may
hardcode `/113`, and `basename.ts` is the only place the base path is
interpreted.

---

## D-F10 — All API URLs are environment-driven

**Decision.** The frontend never contains an absolute API URL. `config/env.ts`
reads `VITE_API_BASE_URL`, defaulting to the relative `/api/v1`, and it is the
only place that value is read.

**Why.** A frontend bundle is publicly readable
(`AI_DOCS/35_Environment_Configuration.md` §11.2), so anything compiled into it
is public and immovable. A relative base additionally means the browser calls
the same origin that served the page — no CORS surface to configure, and
Sanctum's cookie authentication works because the request is genuinely
first-party.

**Consequence.** Only browser-safe values may carry the `VITE_` prefix. No
secret, credential, storage path, or authorization decision may ever appear in
the frontend environment.

---

## D-F11 — Error envelope and code registry centralized

**Decision.** `ApiResponse` builds every envelope; `ErrorCode` registers each
code with its documented HTTP status; `ApiExceptionRenderer` normalizes every
exception.

**Why.** `AI_DOCS/34_Error_Codes.md` §2.7 makes a code a permanent public
contract, and §2.8 requires that not-found and not-visible be
indistinguishable. Both properties fail if any endpoint can assemble its own
response. Centralizing means the leak rules — no stack trace, no SQL, no path,
no framework internal — are enforced in one reviewable place.

**Consequence.** Controllers must return through `ApiResponse`. New codes are
added only after they are registered in `34_Error_Codes.md`.

---

## D-F12 — Arabic default, direction derived from language

**Decision.** Arabic is the default and fallback language. Direction is derived
from the language code, never stored separately. The supported set lives in
`config/localization.php` and `resources/js/locales/`.

**Why.** `AI_DOCS/41_Internationalization_i18n.md` §4 makes Arabic the default
and §6 requires direction to follow the language automatically. Deriving
direction removes a state that could disagree with itself — there is no way to
end up with Arabic rendered left-to-right.

Reading the supported set from configuration satisfies §17 and §24: adding an
approved language is a configuration and translation change, not a code change.

**Rejected.** An i18n library — two languages and a documented fallback chain do
not justify a dependency, and the typed `t()` gives a compile-time error for a
missing English key, which a runtime library would not.

**Consequence.** All user-visible text is translatable. No phase may hardcode a
display string.

---

## D-F13 — Deny by default

**Decision.** `AuthServiceProvider` registers a `Gate::after` boundary so an
ability with no explicit gate or policy resolves to denied.

**Why.** `AI_DOCS/23_Security_Standards.md` §2.1 requires deny-by-default. The
failure mode this prevents is silent: a new resource added without a policy
would otherwise be reachable, and nothing would report it.

**Consequence.** Every resource added in a later phase must register a policy or
gate before it can be accessed.

---

## D-F14 — No hard-delete affordance

**Decision.** `resources/js/lib/http.ts` exposes `get`, `post`, `put`, `patch`,
and `upload`. It deliberately provides no `delete`.

**Why.** `AI_DOCS/28_Coding_Standards.md` §2.4 and §1.5 make Archive the only
removal mechanism; a code path that permanently deletes is a defect. Omitting
the verb from the transport layer means a developer cannot express a hard delete
without consciously adding it — the safe path is the default path.

**Consequence.** Archive and restore are actions:
`POST /{resource}/{id}/archive` and `.../restore`.

---

## D-F15 — Quality gates are executable

**Decision.** Four gates run and pass: `composer lint` (Pint, PSR-12 with strict
types), `composer test` (PHPUnit), `npm run lint` (ESLint), `npm run typecheck`
and `npm test`.

**Why.** A documented standard that nothing enforces decays. This was not
hypothetical: `npm run lint` was declared before an `eslint.config.js` existed,
so the command failed outright, and the first successful run immediately found
an unsafe `any` that the TypeScript compiler had accepted. A gate that does not
run is worse than no gate, because it implies coverage that is absent.

**Consequence.** All four must pass before any phase is considered complete.

---

## D-F16 — Framework config kept intact; drivers selected by value

**Decision.** Laravel's published config files keep their documented connection
blocks for Redis, Memcached, DynamoDB, S3, SQS, and Beanstalkd. Compliance is
achieved by **selecting** the confirmed driver and omitting the credentials.

**Why.** `AI_DOCS/03_System_Architecture.md` §4.1 requires that Version 1 must
not *require* that infrastructure. It does not require deleting Laravel's
defaults. Those blocks are inert: no driver is selected, no package is
installed, and `.env.example` contains no `REDIS_`, `AWS_`, or `PUSHER_` key, so
none can be activated by accident.

Deleting them would fork framework files against upstream for no security or
functional gain, and would make future Laravel upgrades noisier.

**Consequence.** Recorded explicitly so a later reviewer does not mistake the
presence of those blocks for a scope violation. Adding a credential for any of
them **would** be a violation (`PROJECT_CONSTRAINTS.md` §3.2).

---

## Traceability

| Decision | Primary AI_DOCS basis |
|---|---|
| D-F01 Single application | Brief; 04 §1 (superseded, see §3.10) |
| D-F02 React in `resources/js` | 04 §3; 12 §2 |
| D-F03 No separation | 28 §1.3; 12 |
| D-F04 Feature-Based Architecture | 04 §2, §3, §11; 11 §3 |
| D-F05 cPanel target | 26 §4.1; 03 §4.1 |
| D-F06 No Docker | 03 §4.1 |
| D-F07 No symlinks | 04 §5; 26 §7 |
| D-F08 No localhost | 35 §10, §11 |
| D-F09 `APP_URL` base path | 26 §7; 35 §10.2 |
| D-F10 Environment-driven API URLs | 35 §11.2; 10 §5 |
| D-F11 Central error envelope | 10 §6; 34 §2, §26.1 |
| D-F12 Arabic default, derived direction | 41 §3, §4, §6, §17 |
| D-F13 Deny by default | 23 §2.1; 08 |
| D-F14 No hard delete | 28 §1.5, §2.4 |
| D-F15 Executable gates | 24; 28 §4, §6 |
| D-F16 Framework config intact | 03 §4.1; 35 §10.4 |
