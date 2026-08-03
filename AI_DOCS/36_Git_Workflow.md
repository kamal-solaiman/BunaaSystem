# 36 — Git Workflow

## Document Scope

This document defines the official Git workflow, branching strategy, commit standards, pull request standards, merge process, version control practices, and AI-assisted development integration for Version 1 of the Unified Education Platform.

It is a process and governance document only. It does not define source code, deployment scripts, CI/CD pipeline definitions, infrastructure-as-code, database tables, APIs, or UI implementation. It does not contain executable Git command sequences, shell examples, or configuration file contents.

`AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if any conflict is found. Technology baseline reference: Laravel 12 with PHP 8.3, React 19 with TypeScript/Vite/Tailwind CSS, MySQL 8, Laravel Sanctum, Laravel Gates & Policies with Custom RBAC, File Cache, Database Queue, Database session driver, Laravel Public Storage, Laravel Scheduler with Cron Jobs, SMTP baseline, Apache or LiteSpeed, cPanel Shared Hosting primary target with VPS/Cloud future target.

This workflow is optimized for **solo development with future team expansion** and is explicitly designed for **compatibility with AI-assisted development sessions** that operate on isolated branches.

**Authoritative sources:** `00_Project_Context.md`, `03_System_Architecture.md` §4.1, `04_Project_Structure.md`, `27_Development_Roadmap.md`, `28_Coding_Standards.md` §14, `26_Deployment_Plan.md`, `31_Master_Index.md` §8-§11, `29_Project_Decisions.md` D-044 through D-048.

---

# 1. Document Purpose

The purpose of this document is to provide one authoritative reference for all version control practices of the Unified Education Platform:

- Which branches exist, their purpose, their parent-child relationship, and their protection level.
- How features, hotfixes, and releases are isolated and integrated.
- How branch names are constructed to remain searchable and consistent with canonical terminology.
- How commit messages are structured to provide traceability to business rules, decisions, and modules.
- How pull requests are authored, reviewed, and merged, and which quality gates apply.
- How conflicts are prevented and resolved without losing history or business intent.
- How version tags mark releases, how release workflow maps to deployment milestones, and how rollback is coordinated with `26_Deployment_Plan.md`.
- How ignored files are governed to prevent secrets, generated artifacts, and runtime data from entering history.
- How AI development sessions integrate without colliding with human work and without violating documentation-first discipline.
- How solo developer workflow preserves discipline while remaining lightweight enough for future team growth.

Out of scope: executable Git command examples, shell scripts, CI/CD workflow file contents, source code, API definitions, database schema, UI implementation. Behavioral requirements ownership remains with respective owning documents per `31_Master_Index.md` §9.2.

---

# 2. Git Workflow Philosophy

The Git workflow philosophy inherits the project's core principles:

1. **Documentation before code applies to history.** Every change in history must trace to a confirmed requirement, a decision identifier D-xxx, or a business rule BR-xxx from `00_Project_Context.md`. History that cannot be traced is a defect, mirroring the product's Audit Log principle that every important action must be attributable.

2. **One codebase, environment-specific configuration, never in history.** The same application runs in Development, Testing, Staging (Future), and Production. Secrets, database credentials, application keys, mail credentials live only in deployment-managed environment files, never committed to any branch. The `.env` file never appears in version control, consistent with `35_Environment_Configuration.md` and `23_Security_Standards.md` §16.

3. **Tenant isolation extends to version control.** Code handling Teacher Workspace data must preserve BR-003 isolation in every branch. A feature, fix, or refactor that touches Educational Grades, Groups, Student relationships, Enrollment, Attendance, Homework, Lessons, Question Bank, Exams, Reports, Teacher Staff, Settings, payment-status records, or file references must retain workspace scoping in all commits.

4. **Archive discipline in history.** Deletion in version control is allowed for generated artifacts (vendor, node_modules, build output) that were never intended to be tracked. For product data, the product rule Archive instead of permanent deletion BR-005 remains; for documentation history, resolved open questions are archived not deleted. Git history itself is append-only and immutable for committed work that has been shared.

5. **Small, traceable, reversible increments.** Every commit represents a single logical change. Every branch represents a single objective (feature, fix, release, hotfix). This makes review, bisection, and rollback possible, and aligns with the Audit Log model where each entry contains actor, context, event, and origin.

6. **Solo efficiency with team-ready rigor.** For solo development, the workflow avoids ceremony that would slow a single contributor. Branches remain short-lived, main remains always releasable, and feature integration happens through pull requests even when self-reviewed. These habits scale to a team without rewriting history or changing branch protection.

7. **AI-compatible by design.** AI sessions operate on isolated branches under a distinct namespace. Human and AI work never shares a mutable branch. Integration occurs only through pull requests with the same quality gates as human work. This preserves traceability and prevents history contamination.

8. **No marketplace, no out-of-scope leakage.** History must never introduce native mobile, payment gateways, notifications, marketplace behavior, video homework, multiple Teaching Subjects per Teacher, multiple Parents per Student, Docker, Redis, Kubernetes, S3, WebSockets, or Microservices as Version 1 capability. These exclusions are enforced in review.

9. **Canonical terminology in history.** Branch names, commit messages, pull request titles, tags, and review comments use canonical terms: Platform, Teacher Workspace (never tenant in product contexts), Educational Grade (never Class), Teaching Subject (never Course), Group, Student, Parent, Teacher Staff (never sub-teacher), Super Admin, Subscription (Flow A only), payment status / payment-status (Flow B), Price Per Student, Pricing Type (Monthly / Per Lesson), Enrollment, Archive (never Delete as product behavior), Audit Log, Dynamic QR Code, ID Card, Question Bank, Bubble Sheet, Student Switcher, Lesson (never Course), Billable Student (enrollment duration >15 calendar days), Billing Cycle (calendar month), Homework (Text/Image/PDF only). Non-canonical usage is corrected before merge.

---

# 3. Repository Structure

The repository structure is governed by `04_Project_Structure.md`. Git workflow must preserve that structure in every branch.

**Root layout tracked in version control:**

- `AI_DOCS/` — canonical architecture, requirements, planning documents; numbered `NN_Descriptive_Name.md` pattern per `04_Project_Structure.md` §11; `00_Project_Context.md` frozen.
- `backend/` — Laravel 12 modular monolith; `app/Features/` feature-owned organization, `app/Http/`, `Policies/`, `Jobs/` (Database Queue), `Console/Commands/` (Scheduler), `config/`, `database/migrations/`, `factories/`, `seeders/`, `routes/api.php` with `/api/v1` prefix, `.env.example` non-secret template.
- `frontend/` — React 19 SPA; `src/features/` lower-case kebab case (e.g., `teacher-workspace`, `educational-grades`, `attendance`, `homework`, `lessons`, `exams`, `reports`, `payments` for Flow B, `subscriptions` for Flow A), `src/components/primitives/` and `shared/`, `src/auth/`, `src/routes/`, `src/layouts/`, `src/lib/` HTTP boundary, `vite.config.ts`, `tailwind.config.ts`, `.env.example` with only `VITE_` public variables.
- `deployment/` — non-secret cPanel mapping and Cron reference templates; Apache/LiteSpeed references; no live credentials.
- `scripts/` — optional non-secret repository-level helper scripts; must not duplicate Laravel commands or embed production secrets.
- `.editorconfig`, `.gitattributes`, `.gitignore`, `README.md`, `LICENSE` when selected.

**Never tracked:** `backend/vendor/`, `frontend/node_modules/`, `backend/public/build/` (Vite build artifact), `backend/storage/app/public/*` runtime file binaries, `backend/storage/logs/`, `backend/storage/framework/`, `bootstrap/cache/` generated caches, operational logs, `.env` files containing secrets, backups, database dumps, uploaded Lesson videos or Homework files. These exclusions protect secret hygiene and prevent history bloat. See §18.

Repository is a **modular monolith** not microservices per `29_Project_Decisions.md` D-023. Feature boundaries exist at code organization level, not as separate deployable repositories.

---

# 4. Branching Strategy

The branching strategy is a **trunk-based with structured supporting branches** model adapted for solo development and phased roadmap `27_Development_Roadmap.md`.

Core principles:

- **Main is the single source of deployable truth.** It corresponds to the frozen Project Context and always reflects a releasable state. Production deployments originate only from main or from release branches that are merged to main.
- **Develop is the integration branch for staging.** Integration of completed features that have passed review occurs in develop. Staging environment mirrors develop during active phases DE2-DE9. Develop is the base for feature branches.
- **Feature branches isolate individual objectives.** Short-lived, single-purpose branches for one module or one business rule. They originate from develop and merge back to develop.
- **Release branches stabilize a version.** When develop reaches milestone readiness (e.g., end of Phase), a release branch is created to allow hardening, testing, and documentation finalization without blocking new feature work on develop.
- **Hotfix branches address production issues.** Low-volume, urgent branches originating from main to fix Critical or High bugs discovered post-release. They merge to both main and develop.
- **AI session branches are sandboxed.** AI-assisted development operates under the `arena/` namespace or equivalent isolated namespace, one branch per session/task. These branches are treated as feature branches with identical quality gates and never commit directly to main or develop. Integration occurs via pull request.

This hybrid provides the simplicity of trunk-based development for solo contributor (low overhead, main always green) with the discipline of GitFlow for release management and hotfix handling, scaling smoothly to multiple contributors.

Compatibility: Versioning strategy from `27_Development_Roadmap.md` §18 (semantic MAJOR.MINOR.PATCH) maps directly to tags created from main or release branches.

---

# 5. Main Branches

Two long-lived branches constitute the permanent history backbone.

### Main

- **Purpose:** Production baseline; always releasable; source for production deployment and version tags.
- **Protection:** Fully protected per §21. No direct pushes. All changes arrive via pull request from release branches or hotfix branches, or via fast-forward merges from release branches after release acceptance. History is immutable once published.
- **Stability:** Every commit on main must correspond to a state where all automated tests per `24_Testing_Strategy.md` pass, no Critical/High bugs open, Teacher Workspace isolation verified, Archive behavior verified, Audit Log completeness verified, Flow A / Flow B separation verified.
- **Ownership:** Architect and Product Owner govern inclusion. Merge to main signifies official acceptance per `27_Development_Roadmap.md` §17.2.
- **Relationship to roadmap:** Corresponds to milestone M11 production release and post-release state. Deployment milestones DE9-DE10 target main.

### Develop

- **Purpose:** Integration branch for ongoing Version 1 development; staging environment tracks develop during phases.
- **Origin:** Created from main at project initialization.
- **Stability:** Must remain buildable and testable. It may contain features not yet released to production, but never broken or unreviewed code.
- **Merge target:** All feature branches merge here. Develop is the base for release branch creation.
- **Protection:** Protected but with lighter gates than main to allow rapid solo iteration while preserving review discipline. Direct pushes discouraged; self-reviewed pull requests recommended even for solo work to preserve Audit Log-like traceability of decisions.
- **Relationship to PENDING:** PENDING items Q-005, Q-010, Q-011, Q-012, Q-013, Q-015 remain PENDING; develop must never silently harden them. Proposed defaults may exist as documented placeholders only.

A third category of permanent branches is not introduced; additional long-lived branches such as `staging` are avoided in favor of using develop plus environment-specific configuration in `35_Environment_Configuration.md` and deployment mapping in `26_Deployment_Plan.md`.

---

# 6. Feature Branches

- **Purpose:** Isolate development of a single feature, module, or business rule set defined in `02_Software_Requirements.md` and `27_Development_Roadmap.md`. Examples: Educational Grades CRUD, Groups with Pricing Type Monthly/Per Lesson, Student dual registration methods with duplicate prevention BR-022, Attendance three methods BR-010, Homework Text/Image/PDF only BR-021, Question Bank private BR-011, Bubble Sheet electronic auto-grading, Subscription Billable Student calculation Enrollment duration >15 days BR-008.

- **Origin:** Always originates from the current tip of develop to ensure feature work includes recent integration.

- **Lifespan:** Short-lived ideally, corresponding to a single development phase task or a single user flow from `05_User_Flows.md`. The roadmap's ten phases are decomposed into many feature branches.

- **Scope discipline:** One branch, one objective. Mixing unrelated features (e.g., Attendance and Subscription billing in same branch) is prohibited because it obscures review, violates single-responsibility principle from `28_Coding_Standards.md`, and complicates rollback.

- **Naming:** Follows convention defined in §9. Name contains canonical terms and traceability hint where applicable (module and rule). For example, a branch handling one Group per Student per Teacher BR-002 includes that intent in its descriptive segment.

- **Integration:** Completed feature branches merge to develop via pull request per §11 and §13. Merge strategy is squash to keep develop history linear and focused on feature-level increments while preserving full history inside the pull request discussion.

- **Solo optimization:** For solo developer, feature branch may be iterated with frequent local commits for save points. History hygiene is applied before pull request creation by organizing commits into logical units per commit convention §10.

- **AI compatibility:** AI sessions producing feature work must create a branch under isolated namespace and treat it as a feature branch with same origin and merge target rules.

---

# 7. Hotfix Branches

- **Purpose:** Urgent correction of a Critical or High severity bug discovered in production (main). Examples: Teacher Workspace isolation breach, duplicate Student accounts created violating BR-001/BR-022, Audit Log entries lost, Archive replaced by hard deletion, Flow A/B conflation, payment-status recording failure.

- **Origin:** Originates from main at the tag marking current production release. This ensures hotfix contains only fix without unrelated develop changes.

- **Scope:** Minimal, surgical change that corrects defect without introducing new features or scope expansion. Must preserve frozen Project Context. Must not harden PENDING decisions as part of fix.

- **Dual merge requirement:** After acceptance, merges to both main (with incremented patch tag) and to develop (so fix is present in future releases). If conflict arises during second merge, resolution must preserve hotfix intent, never revert or dilute fix.

- **Version impact:** Patch version increment per semantic versioning `27_Development_Roadmap.md` §18.2 (e.g., 1.0.0 to 1.0.1). Tag created from main after merge.

- **Audit and testing:** Must include test coverage that reproduces defect before fix and verifies correction after fix, per `24_Testing_Strategy.md`. Must include Audit Log verification where applicable.

- **Solo optimization:** Hotfix branches are intentionally rare and short. Solo developer creates hotfix branch, fixes, validates in staging-like local environment, and merges via hotfix pull request with expedited but complete review checklist.

---

# 8. Release Branches

- **Purpose:** Stabilize a release candidate while allowing develop to accept next-phase work. Provides hardening window for comprehensive testing, security checklist verification, documentation consistency review, and staging validation per `27_Development_Roadmap.md` testing milestones T1-T10 and deployment milestones DE2-DE10.

- **Origin:** Created from develop when develop reaches feature completeness for a milestone (e.g., end of Phase 3 Teacher Workspace complete M3, end of Phase 5 Attendance complete M5, end of Phase 9 Subscription complete M9, end of Phase 10 Optimization complete M10).

- **Naming:** Includes major.minor version intent, e.g., `release/1.0` for Version 1 initial release. Minor increments for 1.1 future operations enhancements per roadmap §20.1.

- **Behavior during lifecycle:**
  - Only bug fixes, documentation corrections, and performance/security hardening per Phase 10 allowed on release branch.
  - No new features. Adding a Teaching Subject multi-subject capability, payment gateway, notification, marketplace browsing, video homework support, or multiple Parents per Student would violate freeze and is prohibited.
  - Fixes on release branch are cherry-picked or merged back to develop to keep develop current.

- **Merge outcomes:**
  - Upon release acceptance criteria met per `27_Development_Roadmap.md` §17.2 and `24_Testing_Strategy.md` §20, release branch merges to main. Main receives version tag per §15.
  - Release branch then also merges back to develop to include any hardening fixes.

- **Rollback coupling:** Release branch retention after merge supports diagnosis if rollback per §17 becomes necessary. Release branch tag remains reachable from main history.

- **Solo optimization:** Solo developer maintains one release branch at a time to avoid confusion. Develop remains open for next-phase work immediately after release branch creation, mirroring roadmap's overlapping validation.

---

# 9. Branch Naming Convention

Branch naming provides discoverability, traceability, and automated tooling compatibility without embedding secrets.

**General rules:**

- Lower-case kebab-case throughout, with slash separator for type prefix. Examples pattern: `{type}/{descriptive-kebab}`.
- Use canonical terminology per `30_Project_Glossary.md` and `00_Project_Context.md` §19. Never use non-canonical Class for Educational Grade, Course for Lesson or Teaching Subject, Delete for Archive as product behavior, tenant instead of Teacher Workspace in product contexts, sub-teacher for Teacher Staff.
- Descriptive segment contains module and intent, not person name or date.
- No ticket numbers required for Version 1 solo development; future team may prepend issue identifier after approval.
- No version numbers in feature branches; version numbers appear only in release and hotfix branches and tags.

**Type prefixes:**

| Prefix | Usage | Parent Branch | Merge Target |
|---|---|---|---|
| `feature/` | Single feature, module, or user story | develop | develop |
| `bugfix/` | Non-urgent bug correction discovered in develop/staging | develop | develop |
| `hotfix/` | Urgent production fix Critical/High | main (at tag) | main and develop |
| `release/` | Release stabilization for a planned version | develop | main and develop |
| `docs/` | Documentation-only change in AI_DOCS/ | develop | develop |
| `chore/` | Build, dependency, tooling, gitignore, editorconfig | develop | develop |
| `refactor/` | Restructuring without behavior change | develop | develop |
| `arena/` | Isolated AI-assisted development session branch per task; matches observed sandbox naming | develop or main per task definition | develop or per governance (arena branches for this project target arena namespace and merge via PR) |

**Descriptive examples (conceptual, not command examples):**

- Feature handling Educational Grades CRUD becomes `feature/educational-grades-crud` rooted in Teacher Workspace isolation.
- Feature handling Group pricing Types becomes `feature/groups-pricing-type-monthly-per-lesson`.
- Feature handling Student dual registration with duplicate prevention becomes `feature/students-dual-registration-br022`.
- Feature handling Attendance Dynamic QR Code daily generation becomes `feature/attendance-dynamic-qr-daily`.
- Feature handling Homework Text Image PDF format enforcement becomes `feature/homework-text-image-pdf-enforcement`.
- Feature handling Question Bank private ownership becomes `feature/question-bank-private-br011`.
- Feature handling Bubble Sheet electronic auto-grading becomes `feature/exams-bubble-sheet-auto-grading`.
- Feature handling Subscription Billable Student enrollment duration rule becomes `feature/subscriptions-billable-enrollment-duration-br008`.
- Documentation update becomes `docs/architecture-teacher-workspace-isolation`.
- Hotfix for cross-Teacher data leak becomes `hotfix/teacher-workspace-isolation-leak-br003`.

**Restrictions:**

- No personal identifiers, no secrets, no environment names as sole descriptor.
- No upper-case letters, no underscores in descriptive segment (kebab-case required) consistent with frontend feature folder convention `04_Project_Structure.md` §11.
- No prolonged history in name; name length concise yet sufficient to identify purpose in branch list.

---

# 10. Commit Message Convention

Commit message convention extends `28_Coding_Standards.md` §14 which defines the official format. This section governs its application in Git workflow context; it does not redefine.

**Format (replicated from Coding Standards for governance completeness):**

First line: `{type}({scope}): {short description in imperative mood, <=72 characters}`

Optional second block: body explaining *why* change was made, not *what* (diff shows what), wrapped at 72 characters.

Optional footer: references to related business rules, decisions, breaking change notice with `BREAKING CHANGE:` prefix.

**Types:**

- `feat` — new feature or capability.
- `fix` — bug fix.
- `refactor` — restructuring without behavior change.
- `docs` — documentation-only change.
- `style` — formatting, whitespace, style (no logic change).
- `test` — adding or modifying tests.
- `chore` — build process, dependencies, tooling.
- `perf` — performance improvement.
- `ci` — CI/workflow changes (when future CI added, per governance).
- `revert` — reverting previous commit.

**Scopes mandatory and canonical:**

`auth`, `rbac`, `teacher-workspace`, `educational-grades`, `groups`, `students`, `parents`, `attendance`, `homework`, `lessons`, `exams`, `reports`, `payments` (Flow B payment status only), `subscriptions` (Flow A), `files`, `archive`, `audit-log`, `api`, `frontend`, `backend`, `db`, `config`, `deps`, plus additional scopes as defined in `28_Coding_Standards.md` §14.3.

Scopes must use canonical terms; `educational-grades` not `classes`, `lessons` not `courses`, `teacher-workspace` not `tenant`, `archive` not `delete`.

**Business rule traceability in commit footer:**

Where a commit enforces a specific rule, footer includes rule reference per `28_Coding_Standards.md` §18.2: e.g., reference to BR-003 for Teacher Workspace isolation, BR-022 for duplicate Student prevention, BR-002 for one Group per Student per Teacher, BR-005 for Archive policy, BR-006 for Audit Log, BR-008 for Billable Student enrollment duration, BR-016 for Teaching Subject immutability, BR-019 for payment status only, BR-020 for one Parent per Student, BR-021 for Homework formats.

**Examples of compliant message structure (conceptual descriptions):**

- Type `feat` with scope `attendance` describing addition of Dynamic QR Code daily generation, body explains why three methods required per BR-010, footer references BR-010 and decision D-001 stack choice.
- Type `fix` with scope `students` describing prevention of duplicate Student account creation, body explains dual registration methods Method 1 self-registration Method 2 Teacher-created must converge to one global account, footer references BR-001, BR-022.
- Type `refactor` with scope `payments` describing separation of Flow A and Flow B services, body explains conflation risk and canonical separation, footer references business rule separation.
- Type `docs` with scope generic describing update to architecture diagram for Teacher Workspace isolation, body explains alignment with System Architecture document §11.
- Type `test` with scope `exams` describing Bubble Sheet automatic grading coverage, footer references BR-011.

**Rules:**

- Short description imperative: add, fix, prevent, enforce, separate, archive, restore, calculate, validate, scope.
- No period at end of short description.
- Body explains *why* and *context*, not implementation narration.
- Footer optional but mandatory when breaking change present.
- No secrets, credentials, hostnames, IPs in commit message.
- No co-mingling unrelated changes in single commit. Each commit atomic.

---

# 11. Pull Request Standards

Pull requests are the sole integration mechanism for feature, bugfix, release, hotfix, docs, chore, refactor, and arena branches into develop or main. Even for solo developer, pull request use preserves auditability similar to Audit Log append-only principle.

**Title:** Follows commit message convention first line format: type(scope): short description. Title must use canonical terminology. Must be searchable.

**Description must include:**

- **Purpose and context:** What problem solves, which roadmap phase, which milestone, which user flows from `05_User_Flows.md` affected.
- **Scope and boundaries:** Which modules touched, which Teacher Workspace boundaries preserved, which roles affected (Super Admin, Teacher, Teacher Staff, Student, Parent).
- **Business rules traceability:** List of BR-xxx references enforced or affected, e.g., BR-003 isolation, BR-002 one Group per Student per Teacher, BR-007 transfer history preservation, BR-005 Archive replaces deletion, BR-006 Audit Log mandatory events, BR-008 Billable Student >15 days enrollment only, BR-014 historical data never deleted, BR-016 one Teaching Subject immutable, BR-020 one Parent per Student, BR-021 Homework Text/Image/PDF only.
- **Decisions traceability:** D-xxx references where applicable.
- **Testing evidence:** Which test layers executed per `24_Testing_Strategy.md`: Unit, Feature, Integration, API, Authentication, Authorization, Database, UI. Note that Teacher Workspace isolation tests pass, Student duplicate prevention tested, Parent read-only enforced, Archive/Restore verified, Flow A/B separation verified.
- **Security considerations:** Confirmation that authentication, authorization, tenant isolation, input validation, rate limiting, file upload validation not weakened.
- **Documentation impact:** Whether AI_DOCS updated, which Master Index entry impacted, whether glossary needs term addition.
- **Deployment impact:** Whether database migration included, whether storage namespace change, whether Cron configuration impact per `26_Deployment_Plan.md`.

**Checklist enforcement (mirrors `28_Coding_Standards.md` §19 and security checklist `23_Security_Standards.md` §21):**

- Teacher Workspace isolation for all workspace-owned queries verified.
- No cross-Teacher data access possible.
- No permanent deletion; Archive used.
- Historical data preserved through structural changes, Student transfers.
- One Group per Student per Teacher enforced.
- Duplicate Student prevented.
- Teaching Subject immutability enforced.
- One Parent per Student enforced, Parent read-only.
- Flow A / Flow B never conflated; Subscription refers Flow A only, payment status Flow B only.
- Homework format enforcement Text/Image/PDF, video rejected.
- File upload validation type/size/MIME, Parent upload denied, cross-Teacher denied.
- Error responses standardized, no stack traces, SQL, credentials, private data exposed.
- Canonical terminology used: Educational Grade not Class, Lesson not Course, Archive not Delete, Teacher Workspace not tenant in product contexts.
- No out-of-scope feature introduced: no native mobile, payment gateway, notifications, marketplace, video homework, multiple subjects, Docker, Redis, Kubernetes, S3, WebSockets, Microservices.
- No PENDING hardened: Q-005, Q-010, Q-011, Q-012, Q-013, Q-015 remain PENDING.

**Size guidance for solo + future team:**

- Pull requests kept focused: ideally single feature or single bugfix. Large pull requests spanning many modules discouraged because review quality degrades and rollback becomes all-or-nothing.
- Documentation-only pull requests may be larger when updating index `31_Master_Index.md` §15 but still bounded.

**Draft status:** Early work may exist as draft to gather feedback before final review, but draft must still pass basic build verification.

**AI pull requests:** Arena branches follow same template. Additional metadata includes originating task description, list of AI_DOCS read before authoring per Master Index §11.1, confirmation that no Git commands, CI config, or source code were invented outside scope.

---

# 12. Code Review Process

Code review is mandatory quality gate, even when author and reviewer are same person in solo phase, because review preserves isolation invariants and prevents scope drift. Future team expansion expands reviewer pool without changing process.

**Self-review in solo mode:**

- Author reviews own diff against checklist §11 before marking ready.
- Delay between authoring and self-review recommended to simulate fresh perspective.
- Self-review includes running full relevant test suites locally, verifying no secrets committed, verifying branch naming convention, commit message convention, documentation consistency.

**Peer review in team mode:**

- At least one additional reviewer with domain knowledge required. For Teacher Workspace isolation changes, reviewer must have understanding of multi-tenant architecture `03_System_Architecture.md` §11 and `23_Security_Standards.md` §5.
- Reviewer not embedded in same feature branch; reviewer context fresh.
- Reviewer checks out pull request branch as isolated checkout for manual verification where appropriate, without modifying author history.

**Review focus areas ordered by priority:**

1. **Security and isolation first:** Teacher Workspace isolation BR-003, ownership checks, authorization via Laravel Gates & Policies + Custom RBAC per `08_RBAC.md` and `09_Permission_Matrix.md`, Parent linked-Student read-only BR-004, Student self-scope BR-001, Teacher Staff assigned permissions BR-013.
2. **Business rule correctness:** One global Student account BR-001, one Group per Teacher BR-002, enrollment time-bounded periods BR-007, Billable Student enrollment duration only BR-008, Archive never deletion BR-005, historical retention BR-014, Flow separation, Teaching Subject immutability BR-016, Parent cardinality BR-020, Homework formats BR-021.
3. **Audit Log and Archive integrity:** Mandatory events Create, Update, Archive, Restore, Login success/failure, Permission Change, Attendance Change, Exam Modification, Homework Modification, Subscription Change per Project Context §10. Immutability, attribution, transactional guarantee.
4. **Error handling and message policy per `23_Security_Standards.md` §18:** Generic authentication failure messages, no resource existence leak, no private data in errors, standardized envelope per `10_API_Design.md` §6.
5. **API contract fidelity per `10_API_Design.md`:** Version prefix `/api/v1`, lower-case kebab-case plural resource names, pagination meta, filtering after authorization and scope resolution, sorting whitelist, Archive/restore explicit action endpoints, no hard-delete endpoints.
6. **Naming and terminology:** Folders, files, classes, variables, branch names, commits use canonical terms per §9, `04_Project_Structure.md` §11, `28_Coding_Standards.md` §7-§13.
7. **Test coverage per `24_Testing_Strategy.md`:** Every affected business rule has automated test coverage, isolation regression, authorization matrix, Archive regression, Audit Log regression, Flow separation regression.
8. **Version control hygiene:** Small atomic commits, descriptive messages per §10, no merge commits inside feature branch history before squash, no secrets, no binary bloat, `.gitignore` respected.

**Review comments:**

- Must be specific, actionable, traceable to document section or business rule.
- Categories: blocking defect (must fix before merge), suggestion (improvement), question (clarification needed on PENDING or requirements).
- No personal style preferences overriding canonical coding standards.

**Approval criteria:**

- All checklist items in §11 verified as passed.
- All review comments addressed or explicitly acknowledged as out-of-scope for current phase with tracking note.
- Build verification: backend Feature and Unit suites, frontend integration suites pass; Linting and type checking pass per `28_Coding_Standards.md`.
- Documentation consistency: if code changes behavior, AI_DOCS updated per modification rules `31_Master_Index.md` §8.

**Rejection criteria:** Isolation breach, permanent deletion introduced, duplicate Student allowed, Parent write capability introduced, Flow conflation, out-of-scope feature introduced, PENDING hardened, secrets committed, canonical terminology violated without correction.

---

# 13. Merge Strategy

Merge strategy defines how history is integrated to preserve readability, bisectability, and rollback capability, without quoting executable command sequences.

**Feature branch to develop:**

- **Strategy:** Squash and merge. All commits on feature branch collapsed into single coherent commit on develop with message following §10 convention that summarizes feature. Full history remains in pull request discussion and branch reference before deletion.

- **Rationale:** Solo developer benefits from save-point commits during development; history on develop remains linear and focused on feature-level increments. Future team benefits from clean changelog generation and easy reversion of entire feature via single commit revert.

- **Preservation:** Original branch not immediately deleted from remote until release branch or tag confirms feature stable in staging.

**Bugfix branch to develop:**

- Same as feature: squash and merge.

**Release branch to main and back to develop:**

- **To main:** Merge commit (non-squash) that preserves entire release branch history on main. This preserves Fix commits during release stabilization and makes tag history attributable. Merge commit message indicates release version and roadmap milestone.

- **To develop:** Merge commit that brings hardening fixes back to develop. If release branch received fixes after initial creation, those fixes appear in develop history without duplication.

**Hotfix branch to main and develop:**

- **To main:** Merge commit analogous to release to main, then tag increments patch version.
- **To develop:** Merge commit bringing hotfix to develop. Equivalent of dual merge requirement per §7.

**Documentation and chore branches:**

- Squash and merge to develop similar to feature to keep history concise.

**Conflict handling inside merge strategy per §14:**

- Merge commits must never contain unresolved conflict markers.
- Merge commit must preserve intent of both sides, particularly isolation logic.
- Merge commit must trigger full verification checklist again before merge marked complete.

**History immutability after publication:**

- Once a branch has been merged to main or tags created from main, its history is considered published and must not be rewritten. Any correction introduces new commit or new hotfix branch, never history rewrite that would alter tag ancestry.

**Solo optimization:**

- Squash strategy reduces noise from iterative AI or solo save-point commits while retaining full auditability via pull request.
- Merge commit for releases provides clear demarcation between development phases and production states mapping to roadmap milestones M0-M11.

---

# 14. Conflict Resolution Guidelines

Conflicts are natural in multi-branch workflow and must be resolved with business invariants preservation, not merely textual resolution.

**Prevention:**

- Short-lived branches reduce divergence window.
- Feature decomposition by module reduces overlap: Educational Grades branch unlikely to conflict with Attendance QR branch.
- Pulling latest develop into feature branch frequently (integrating upstream changes) keeps divergence small, but integration must be done via merge from develop into feature branch (not rebase that rewrites shared history) when branch is shared with reviewer.
- Canonical file ownership per `04_Project_Structure.md` reduces contention: backend `app/Features/` owns feature-specific application coordination, `app/Http/` owns HTTP adaptation only.

**Classification of conflict types:**

- **Import/namespace conflict:** Two branches add different imports to same file. Resolution keeps both imports ordered per `28_Coding_Standards.md` §4.7 grouping and alphabetically sorted.
- **Business logic overlap:** Two branches modify same service method (e.g., Student enrollment logic). Resolution requires understanding both intents: one may enforce one Group per Teacher BR-002, other may add Archive check. Result must preserve both checks, not choose one.
- **Authorization conflict:** One branch adds new permission, another modifies policy. Resolution ensures policy uses Permission Matrix catalog and preserves Teacher Workspace ownership, Student self-scope, Parent linked-Student read-only.
- **Schema conflict:** Migration timestamp collision or divergent schema changes. Resolution preserves tenant isolation columns, Archive columns, audit columns, and avoids cross-tenant foreign keys. Migrations ordered chronologically per Laravel conventions.
- **Documentation conflict:** Two branches update same AI_DOCS document index or glossary. Resolution preserves purpose accuracy, dependency accuracy, canonical terminology.

**Resolution principles:**

1. **Isolation first:** If conflict touches Teacher Workspace scoped query, resolution must include workspace scoping from both sides. Never drop `teacher_workspace_id` filter to resolve textual conflict.
2. **No deletion as resolution:** Never resolve conflict by deleting Archive check, Audit Log recording, or validation. Deleting code to make merge compile is defect.
3. **Preserve both intents unless contradictory:** Most conflicts are additive. Combine correctly.
4. **Escalate when contradictory:** If two branches propose contradictory business rule interpretations, stop and resolve per Master Index §10 conflict resolution policy: Project Context wins, then confirmed Product Owner decision in `29_Project_Decisions.md`, then subject-owning document, then lower documentation layer.
5. **Test after resolution:** Resolved branch must pass relevant isolation, authorization, and business rule tests before merge considered complete.
6. **No secrets introduced during resolution:** Merge resolution must not accidentally commit `.env`, credentials, or storage paths from conflict markers.

**Roles during resolution:**

- Author of feature branch primarily responsible for resolving conflict with develop or main.
- Reviewer verifies resolution preserves intents and invariants, not merely that compilation passes.
- For conflicts involving PENDING items, resolution must not harden PENDING; proposed defaults remain proposed defaults.

**AI session conflict resolution:**

- AI branches under `arena/` namespace may diverge quickly. When conflict with develop arises, AI author must incorporate latest develop changes ensuring canonical documents `31_Master_Index.md`, `00_Project_Context.md`, and owning document for task have been re-read before resolution, per AI reading instructions `31_Master_Index.md` §11.

---

# 15. Version Tagging

Version tagging marks releasable states on main and optionally on release branches, aligning with versioning strategy `27_Development_Roadmap.md` §18.

**Scheme:**

- Semantic versioning MAJOR.MINOR.PATCH per roadmap: Major significant product scope change, Minor feature additions within confirmed major scope, Patch bug fixes and minor corrections without scope change.
- Tag format includes leading `v` prefix followed by numbers: e.g., `v1.0.0` for initial production release, `v1.0.1` for hotfix, `v1.1.0` for enhanced operations.
- Annotated tags preferred to store tagger identity, date, and message referencing roadmap milestone and decision log.

**Tag creation rules:**

- Tags created only from main or from release branches that are about to merge to main after acceptance criteria met §17.2.
- No tags from develop or feature branches; develop is integration not release.
- Tag message includes scope summary, milestone identifier M0-M11, and confirmation that release criteria from `24_Testing_Strategy.md` §20 and `27_Development_Roadmap.md` §17.2 met.
- Tag creation is part of release workflow §16; tag creation itself is not a code change.

**Tag immutability:**

- Once published, tag never moved or deleted. If defect discovered after tag, new patch tag created via hotfix branch; existing tag remains for traceability, mirroring Archive philosophy that history is never deleted BR-014.
- Tags provide stable points for rollback reference per §17.

**Pre-release tags (future consideration):**

- For staging validation, pre-release identifiers may be used such as `v1.0.0-rc.1` to mark release candidate tested on staging. Such tags are created from release branch, not main, and are distinct from production tags. Their use requires separate approval as part of release management.

**Mapping to deployment milestones:**

- DE1 provisioning no tag; DE2-DE8 staging updates correspond to integration on develop, not tags; DE9 production preparation corresponds to release candidate tag; DE10 production deployment corresponds to production tag on main.

---

# 16. Release Workflow

Release workflow orchestrates version stabilization, tagging, and deployment hand-off to `26_Deployment_Plan.md`. It maps directly to roadmap milestones `27_Development_Roadmap.md` §16-§17 and testing milestones T1-T10 §14.

**Phases:**

**1. Feature freeze for release scope:** Develop has reached feature completeness for intended release (e.g., Phase 10 complete M10). Product Owner confirms scope lock per `27_Development_Roadmap.md` §18.3.

**2. Release branch creation:** Release branch originates from develop tip. Name reflects target version per §9. Develop immediately reopens for next phase work.

**3. Hardening on release branch:** Only bug fixes, security hardening, performance tuning, documentation consistency review. No new features. Fixes include: query optimization preserving workspace scoping, rate limiting review, file upload validation review, error message audit per `23_Security_Standards.md` §18, cache invalidation review.

**4. Testing and checklists:** Full regression per `24_Testing_Strategy.md` §15 executes. Security checklist per `23_Security_Standards.md` §21 passes. Environment checklist per `35_Environment_Configuration.md` §22 passes: PHP 8.3 extensions, MySQL 8 utf8mb4, File Cache, Database Queue, Database sessions, Laravel Public Storage, permissions 755/775 for storage and bootstrap/cache, .env 600/640, SSL, Cron Scheduler every minute, queue worker, audit log mandatory events.

**5. Staging validation:** Release branch deployed to staging environment mirroring production configuration on cPanel Shared Hosting baseline per `26_Deployment_Plan.md` §3.3. UAT per `24_Testing_Strategy.md` §16 performed by role representatives: Super Admin Teacher management and Subscription management, Teacher Educational Grades/Groups/Students/Attendance/Homework/Exams/Lessons/Reports/Teacher Staff/Settings, Student Dashboard/My Schedule/Homework/Exams/Lessons/Subscriptions/Settings partitioned per Teacher, Parent Dashboard/Student Switcher/Homework/Attendance/Exams/Teachers/Payments read-only, Teacher Staff permission boundaries.

**6. Release acceptance:** Criteria from `27_Development_Roadmap.md` §17.2: all automated tests pass (backend Feature/Unit, frontend integration), no Critical/High bugs open, every affected business rule has passing coverage, complete authorization matrix tested, Teacher Workspace isolation verified across modules, Archive/Restore verified for archivable entities, Audit Log completeness verified for mandatory events, Flow A/B separation verified, historical preservation verified, security checklist passes, UAT completed, staging validated.

**7. Merge to main and tagging:** Upon acceptance, release branch merges to main via merge commit. Tag created on main per §15. Main now represents production baseline.

**8. Merge back to develop:** Release branch merges back to develop to include hardening fixes. Release branch retained for traceability until next major release, then considered historical.

**9. Production deployment hand-off:** Deployment plan `26_Deployment_Plan.md` §11-§12, §22-§23 executes database migration testing, file storage verification, queue configuration, scheduler configuration, cache configuration, SSL, domain configuration, backup before deployment per §26 pre-deployment checklist.

**10. Post-release monitoring:** Per `27_Development_Roadmap.md` §17.4: monitor operational logs and Audit Log for anomalies, verify background job processing Billing Cycle initialization, Billable Student calculation enrollment duration only, Subscription snapshot, QR cleanup, Exam auto-grading, report preparation, verify Cron execution, Database Queue worker processing, confirm no Critical/High bugs in first operational period.

**Solo optimization:** Solo developer may act as release manager, QA, and Product Owner representative, but must still follow checklist discipline and must not bypass staging validation even when working alone.

---

# 17. Rollback Workflow

Rollback workflow defines how to revert production to known-good state when release acceptance fails post-deployment or Critical defect discovered per rollback criteria.

**Rollback triggers per `26_Deployment_Plan.md` §19.1 and `27_Development_Roadmap.md` §17.3:**

- Critical bug after deployment.
- Teacher Workspace isolation violated.
- Historical data lost or corrupted.
- Audit Log entries lost or modified.
- Flow A and Flow B data conflated.
- Authentication or authorization broken.
- Platform unavailable extended period.

**Rollback types:**

**Application code rollback:**

- Identify previous release tag on main that passed full acceptance.
- Restore application code from version control tag or release archive counterpart.
- Reinstall dependencies for previous version per deployment plan; re-cache configuration, routes, views, events per `35_Environment_Configuration.md` §7.3.
- Verify connection to database and storage, Teacher Workspace isolation still enforced.

**Database rollback:**

- If migrations ran as part of failed deployment, evaluate Laravel migration rollback capability for affected migrations without permanently deleting data; Archive replaces deletion BR-005.
- If migration rollback not possible without data loss, restore database from most recent pre-deployment backup taken per pre-deployment checklist. Verify restored database preserves Teacher Workspace isolation, Archive state, Audit Log integrity, historical relationships, Flow A/B separation BR-008/BR-009.
- Verify restored database consistent with restored code version.

**Frontend rollback:**

- Restore previous compiled frontend build artifact from `public/build/` backup. Replace build directory with previous output per deployment plan. Verify frontend loads and communicates with backend via `/api/v1`.

**Storage rollback:**

- If file storage changes during failed deployment, verify file references remain valid, ownership preserved, Teacher Workspace private Lesson ownership BR-018 preserved, Student Homework submission privacy preserved. Restore files from backup if necessary preserving historical references per `20_File_Storage.md`.

**Git-level aspects of rollback without generating command examples:**

- Rollback does not rewrite main history that has been published. Instead, new commit or hotfix branch reverts changes or restores previous tag content as new commit.
- Rollback procedure documented as hotfix branch originating from main at previous good tag, then merging to main as new patch release, and also merging to develop to keep develop consistent.
- Tags for failed release remain in history as immutable record; new tag marks rollback state.

**Constraints per `26_Deployment_Plan.md` §19.3:**

- Rollback must not permanently delete data. Archive replaces deletion.
- Rollback must preserve Teacher Workspace isolation.
- Rollback must preserve Audit Log integrity; rollback actions themselves produce Audit Log entries where applicable.
- Rollback must preserve Flow A/B separation.
- Rollback must not weaken authentication or authorization.

**Checklist per `26_Deployment_Plan.md` §26.3 complements this section:** previous code version identification, pre-deployment database backup availability, pre-deployment file storage backup availability, rollback procedure documentation, post-rollback verification steps.

---

# 18. Git Ignore Standards

Git ignore standards prevent secrets, generated artifacts, dependencies, runtime data, and user-uploaded content from entering version control, consistent with `04_Project_Structure.md` §10 and `35_Environment_Configuration.md` §13.

**Principles:**

- Never track secrets: environment files, keys, credentials, tokens. `.env` never committed; only `.env.example` non-secret templates tracked.
- Never track generated artifacts: dependency directories, compiled frontend build, framework caches, operational logs, storage runtime files.
- Never track user content: Lesson videos, Homework attachments, Student submissions remain in Laravel Public Storage runtime data, not version control.
- Never track backups, database dumps, or storage symlinks that are deployment-specific.

**Must be ignored (conceptual categories, not file listing as command):**

- Backend PHP dependencies directory composed by Composer.
- Frontend JavaScript dependencies directory composed by npm.
- Frontend production build output directory inside backend public area.
- Laravel Public Storage runtime root and its subdirectories teacher-workspaces/lessons, teacher-workspaces/homework, teacher-workspaces/files, student-homework.
- Laravel framework runtime cache, session artifacts, compiled views under storage/framework.
- Laravel operational logs under storage/logs and bootstrap cache generated files.
- Environment configuration files at repository root and backend/frontend containing secrets.
- Operating system generated files and editor temporary files.
- Backup artifacts, database dump files, compressed archives containing data.

**Must be tracked:**

- All source files for backend application code under app/, config/ (without secrets), database/migrations/, factories/, seeders/, routes/, resources/lang/ when localization approved.
- All source files for frontend under src/, public/ static inputs, assets/.
- Documentation under AI_DOCS/ including all numbered documents `00_` through `35_` and this document, plus `04_Project_Structure.md` §8 inventory.
- Deployment non-secret templates under deployment/cpanel/, apache/, litespeed/.
- Scripts that are safe, non-secret helper scripts under scripts/.
- Configuration templates `.env.example` for backend and frontend showing required variable names with non-secret placeholder values.
- Repository governance files `.editorconfig`, `.gitattributes`, `.gitignore`, `README.md`, `LICENSE`.

**Gitignore file governance:**

- Single root ignore file at repository root that governs all subdirectories, with additional local ignore files only where tooling requires and that do not contradict root rules.
- Changes to ignore patterns treated as `chore` type commit with scope `config`, reviewed for impact on secret hygiene.
- No ignore entry may hide a source file that is required for build or that contains canonical business rule enforcement.
- Periodic audit of tracked files ensures no secret leakage via accidental addition.

**Future expansion:**

- When localization approved Q-015, translation resource files become tracked; but sanitized non-production language files tracked, production credentials still ignored.
- When payment gateways approved future, gateway configuration templates tracked as non-secret placeholders, live keys ignored.

---

# 19. AI Development Workflow

AI development workflow ensures AI-assisted sessions integrate safely, preserve documentation-first discipline, and maintain compatibility with solo human work and future team expansion. This section aligns with Master Index AI reading instructions `31_Master_Index.md` §11 and arena branch observation.

**Mandatory session start for AI (binding):**

1. Read `31_Master_Index.md` first in full before any other action.
2. Read `00_Project_Context.md` in full as Single Source of Truth.
3. Read `30_Project_Glossary.md` before producing wording.
4. Identify task subject and read owning document per Master Index §9.2 and its Depends On documents.
5. Only then begin task.

**Branch discipline for AI:**

- Every AI task operates on an isolated branch under `arena/` namespace (observed pattern `arena/<session-id>`). One branch per task, never reused across unrelated tasks.
- Branch creation originates from current tip of develop or main depending on task definition provided by orchestration layer. Orchestration layer governance records this; AI must not switch to another branch name other than its assigned arena branch. Session is fixed to its branch per system policy.
- AI never pushes to any branch other than its assigned arena branch. Never switches to, creates, or pushes to any other branch. Work on any other branch would not be associated with session.
- Integration of AI work to develop or main occurs only via pull request created through GitHub tooling, never via direct merge from AI session.

**Commit discipline for AI:**

- Commits follow §10 commit message convention same as human: type(scope): description with canonical terminology.
- Commits include traceability to BR-xxx, D-xxx where applicable, and reference owning document sections.
- Commits never include secrets, credentials, hostnames, IPs, or generated artifacts that should be ignored per §18.
- AI must not generate deployment scripts or CI configuration as part of commits; prohibited by this document's scope and by task instruction.

**Pull request handling for AI:**

- AI creates pull request from arena branch using GitHub tooling. Pull request title and description follow §11 standards, including purpose, scope, business rules, testing evidence, security considerations, documentation impact.
- AI reads all existing AI_DOCS before starting and treats every existing document as official source of truth per task instruction.
- AI performs complete consistency review before saving per task instruction, matching pattern established in document set consistency review tables.

**Behavioral prohibitions for AI (binding per Master Index §11.2):**

- No invention: never invent features, roles, entities, endpoints, tables, rules, statuses, metrics, or documents. If not documented, it does not exist.
- No silent assumptions: if information missing, state missing, do not fill gap.
- PENDING discipline: never resolve, harden, or quietly assume Q-005 non-payment enforcement, Q-010 Lesson video hosting/protection, Q-011 Teacher Staff permission granularity, Q-012 Super Admin content visibility, Q-013 flat vs tiers, Q-015 timezone/currency.
- Frozen respect: never modify `00_Project_Context.md` which is frozen Revision 2.0 FINAL.
- Terminology discipline: use canonical terms only.
- Scope discipline: never introduce native mobile, payment gateways, notifications, marketplace, video homework, multiple Teaching Subjects per Teacher, Docker, Redis, Kubernetes, S3, WebSockets, Microservices as V1 capability.
- Ownership discipline: write content only into document that owns subject.
- Scope-exclusion discipline: respect each document's stated exclusions — most exclude source code, APIs, database tables, UI implementation, physical configuration, Git commands, CI configuration.

**Collaboration with human developer:**

- Human developer reviews AI pull request using Code Review Process §12 same as human pull request.
- Human may add follow-up commits to same AI branch only if governance allows; otherwise human creates separate branch.
- Arena branch history remains intact for auditability; squash on merge to develop applies per §13.
- Secrets hygiene: AI sessions inherit same secret prohibition; no credential appears in commit diff.

**Future team expansion with AI:**

- Multiple AI sessions may operate concurrently, each on its own arena branch. Conflict prevention via short-lived branches and frequent integration of develop into arena branches via merge (not rebase that rewrites shared history) when governance permits.
- AI Development Guide `40_AI_Development_Guide.md` future will further detail tooling; until authored, this section plus Master Index §11 governs AI behavior.

---

# 20. Developer Workflow

Developer workflow describes daily cycle optimized for solo development while preserving team-ready rigor.

**Solo developer daily cycle:**

**1. Synchronization:** Start by synchronizing local develop and main with remote to ensure latest integration. Verify remote main tip tag corresponds to latest production release per §15. This synchronization step is conceptual and not expressed as executable command sequence here.

**2. Task selection per roadmap:** Select task from current roadmap phase per `27_Development_Roadmap.md` §4-§13. Confirm task belongs to current phase and that its owning document has been read per Master Index §12.2.

**3. Branch creation:** Create feature, bugfix, docs, chore, or refactor branch originating from current develop tip per §6 naming convention §9. One objective per branch.

**4. Local development iterations:** Implement in small atomic commits per §10. Each commit enforces single business rule area and preserves Teacher Workspace isolation, Archive policy, Audit Log, Flow separation. Run relevant test subsets locally per `24_Testing_Strategy.md`: Unit for Billable Student calculation enrollment duration >15 days, Feature for Teacher Workspace isolation, Database for one Group per Student per Teacher, etc.

**5. Self-review:** Before pull request, self-review diff against checklist §11 and coding standards checklist `28_Coding_Standards.md` §19. Verify no secrets, no binary bloat, canonical terminology, PENDING untouched.

**6. Pull request creation:** Create pull request targeting develop (or main for hotfix/release). Title follows commit convention, description includes purpose, business rules BR-xxx, decisions D-xxx, testing evidence, security considerations, documentation impact.

**7. Review:** Even solo, perform self-review after interval simulating fresh perspective. When team grows, peer reviewer assigned. Address review comments, add follow-up commits addressing comments (not amending published history that is already under review).

**8. Merge:** Upon approval and all checks passing (tests, security checklist, environment checklist), merge per strategy §13: squash for feature/bugfix/docs/chore/refactor into develop, merge commit for release/hotfix into main and develop.

**9. Cleanup:** After merge, delete local and remote feature branch to keep branch list clean, unless retention required for traceability until release. Preserve tag history.

**10. Deployment hand-off:** After merge to develop, staging environment updated per deployment milestones DE2-DE9. After merge to main and tag creation, production deployment per DE9-DE10 via `26_Deployment_Plan.md`.

**Context switching:** Solo developer may maintain at most two active branches simultaneously to limit context switching overhead. Additional branches parked with clear naming indicating paused state.

**Documentation coupling:** When code changes alter behavior, relevant AI_DOCS document updated in same pull request or in immediately following docs pull request to prevent divergence per `31_Master_Index.md` §8.

**Tooling compatibility:** Workflow compatible with Laravel 12 artisan commands, Composer dependency management, Vite build tool for React 19, MySQL 8 database, File Cache, Database Queue, Database sessions, all executed via deployment-managed environment files.

---

# 21. Repository Protection Rules

Protection rules enforce quality gates and prevent accidental history loss, solo or team.

**Main branch protection (strictest):**

- No direct pushes allowed. All changes via pull request from release or hotfix branches.
- Require pull request review approval: at least one approved review, even if self-approval in solo mode with documented delay.
- Require status checks to pass before merge: all automated tests per `24_Testing_Strategy.md` (backend Feature/Unit, frontend integration), security checklist, environment configuration validation, build verification (Composer install, Vite build).
- Require branch up to date with base before merge to ensure latest develop changes included.
- Require linear history consideration: main history must remain logically linear via merge commits from release/hotfix, not tangled feature merges.
- Block force pushes and deletion.
- Require signed commits consideration for future team expansion (optional for V1 solo, recommended after team growth).

**Develop branch protection (balanced):**

- Prefer pull requests over direct pushes. Direct pushes discouraged; when allowed for urgent small docs fix, still require local test pass.
- Require review: solo mode allows self-review; team mode requires at least one peer reviewer.
- Require status checks: relevant test subsets must pass; full suite required before release branch creation.
- Block force pushes that would rewrite shared history after branch has been pushed and reviewed.

**Feature/AI branches (lightweight):**

- No protection rules preventing author from pushing iterations.
- Force push discouraged after review started; if history hygiene requires rewrite before review, communication required.
- Arena branches protected by orchestration layer session fixation: session fixed to its assigned branch, cannot switch.

**Tag protection:**

- Tags on main considered immutable per §15. No deletion or movement allowed after publication.
- Tag creation requires main branch protection checks passed.

**Gitignore enforcement:**

- Repository protection tooling should reject commits containing `.env` files with secrets, `vendor/`, `node_modules/`, `public/build/`, runtime storage files, backup dumps, as defined in §18. Prevention via ignore file plus server-side hook conceptually, without detailing hook implementation as source code.

**Future team expansion:**

- Introduce CODEOWNERS concept mapping feature areas to owners: `app/Features/EducationalGrades/` owned by Teacher Workspace domain owner, `app/Features/Attendance/` owned by Attendance domain owner, `AI_DOCS/` owned by Architect. Until CODEOWNERS file authored, ownership per `31_Master_Index.md` §13.1 applies.
- Require second reviewer for security-sensitive areas: authentication, authorization, Teacher Workspace isolation, payment-status, Audit Log.

---

# 22. Best Practices

Best practices are actionable guidelines that have proven to preserve quality in this project's domain.

1. **Documentation first, always.** Author or update AI_DOCS before code when behavior changes. No feature without confirmed requirement. Every commit traces to owning document section and BR-xxx, D-xxx where applicable.

2. **Small, atomic, reversible commits.** One logical change per commit. Mix of unrelated changes obscures bisect and complicates rollback. Commit message explains why per §10.

3. **Branch per objective, short-lived.** Long-lived feature branches increase conflict risk and delay feedback. Roadmap phases decomposed into many short branches integrated frequently to develop.

4. **Never commit secrets.** Environment files, keys, credentials, tokens, storage paths as authorization bypass never enter history. Verification before push includes scanning diff for secret patterns.

5. **Preserve isolation invariants in every commit.** Every query touching Teacher Workspace-owned data includes workspace scoping. No commit weakens Student duplicate prevention BR-022, one Group per Teacher BR-002, Parent read-only BR-004, Archive BR-005, Audit Log BR-006, Flow separation BR-008/BR-009/BR-015/BR-019.

6. **Canonical terminology everywhere.** Branch names, commit messages, pull request titles, review comments, tags use canonical terms. Educational Grade never Class; Lesson never Course; Teaching Subject never Course; Archive never Delete; Teacher Workspace never tenant in product contexts; Teacher Staff never sub-teacher; Subscription Flow A only; payment status Flow B.

7. **Test before push.** Run relevant tests locally before opening pull request. Testing Strategy acceptance criteria per `24_Testing_Strategy.md` §20: business rule coverage, authorization matrix, isolation verified, Archive/Restore verified, Audit Log coverage, Flow separation, historical preservation, security checklist.

8. **Review your own diff.** Self-review catches secrets, typo in terminology, accidental inclusion of generated files, PENDING hardening.

9. **Keep develop green.** Develop must always be buildable, testable, deployable to staging. Feature branch must not merge broken change to develop.

10. **Sync frequently.** Integrate latest develop changes into feature branch regularly to keep divergence small and conflict resolution simple, per §14 prevention.

11. **Respect ignored files.** Verify `.gitignore` standards per §18. Generated artifacts `backend/public/build/`, `vendor/`, `node_modules/`, `storage/` runtime files never tracked.

12. **No out-of-scope leakage.** Verify no native mobile, payment gateway, notification, marketplace browsing, video homework, multiple Teaching Subjects, multiple Parents per Student code introduced in Version 1 commits.

13. **Pending remains pending.** Verify no commit introduces logic that resolves Q-005, Q-010, Q-011, Q-012, Q-013, Q-015. Proposed defaults remain documentation only.

14. **Merge strategy discipline.** Feature branches squash to develop, release and hotfix merge commit to main and develop per §13. History after publication immutable.

15. **Tag hygiene.** Tags created only from main or release branch after acceptance criteria met, annotated, immutable, per §15.

16. **AI collaboration hygiene.** Human reviews AI pull requests with same rigor as human. AI reads all existing AI_DOCS before starting and treats every document as official source of truth.

---

# 23. Common Mistakes to Avoid

List of recurring defects observed in similar projects, prohibited here.

1. **Committing secrets.** Adding `.env` with real database credentials, application key, mail credentials, or storage credentials. Violates `23_Security_Standards.md` §16 and `35_Environment_Configuration.md` secrecy principle. Must never occur.

2. **Committing generated artifacts.** Tracking `vendor/`, `node_modules/`, `public/build/` fingerprinted assets, `storage/framework/` cache, `storage/logs/`, bootstrap cache files bloats history and causes merge conflicts.

3. **Hard deletion instead of Archive.** Introducing hard delete in code path that should archive. Violates BR-005. Archive replaces deletion everywhere, by all actors, including Super Admin.

4. **Cross-Teacher data access.** Forgetting workspace scoping filter `teacher_workspace_id` in query, search, report, file access, cache key, or error response. Violates BR-003 highest-priority invariant.

5. **Duplicate Student accounts.** Allowing self-registration path and Teacher-created path to create separate accounts for same person. Violates BR-001 global account and BR-022 dual methods with duplicate prevention.

6. **Mixing Flow A and Flow B.** Using Subscription term for Student fees owed to Teacher, or payment status term for Teacher Platform Subscription, or conflating pricing models, routes, query keys, labels. Violates Flow separation.

7. **Non-canonical terminology in history.** Branch named `feature/classes-crud` instead of `feature/educational-grades-crud`, commit message containing "Course" for Lesson, "Delete" for Archive, "tenantId" instead of `teacherWorkspaceId` in discussion. Violates `30_Project_Glossary.md` and Master Index §2.5.

8. **Large unstructured commits.** Mixing Educational Grades, Groups, and Attendance changes in single commit with vague message like "various fixes". Breaks traceability to BR-xxx and makes rollback impossible.

9. **Skipping review for solo convenience.** Merging directly to main or develop without pull request, checklist, or test verification, even when solo. Violates Audit Log-like traceability.

10. **Rewriting published history.** Force pushing to main or to a tag, or deleting tag after publication. Violates tag immutability §15 and history preservation analogous to Archive policy.

11. **Introducing out-of-scope features in commit.** Adding notification send logic, payment gateway integration, marketplace browsing endpoint, video homework acceptance, multiple Teaching Subjects per account logic. Violates frozen Project Context scope exclusions §4.2.

12. **Hardening PENDING decisions.** Implementing non-payment enforcement 7-day grace read-only, Lesson video signed URLs streaming-only per-Teacher quota, Teacher Staff fixed capability-flag catalog, Super Admin aggregates only visibility, flat price per Student tyranny, or Arabic RTL localization variables without Product Owner confirmation. Violates PENDING discipline.

13. **Ignoring Gitignore standards.** Adding backup dumps, database backups, file storage binaries, or credentials via accidental force add bypassing ignore.

14. **Merge commits polluting feature history.** Merging main into feature branch repeatedly creating tangled history instead of planned squash at feature completion. Makes review difficult.

15. **Version tag from wrong branch.** Tagging from develop or feature branch instead of main/release branch, creating tag that does not represent production acceptance state.

16. **Rollback that deletes history.** Attempting rollback by deleting commits from main history instead of creating revert commit or hotfix branch restoring previous tag content as new commit. Violates backup and audit principles per `26_Deployment_Plan.md` §19.

17. **AI session branching out of bounds.** AI attempting to switch to, create, or push to branch other than its assigned arena branch, violating session fixation and causing tracking loss.

---

# 24. Future Git Strategy

Future Git strategy anticipates team expansion, infrastructure maturity, VPS/Cloud migration, and potential monorepo evolution while preserving confirmed Version 1 invariants.

**Team expansion:**

- Introduce CODEOWNERS file mapping `backend/app/Features/`, `frontend/src/features/`, `AI_DOCS/` ownership to domain owners per `31_Master_Index.md` §13.1. Ensures review assignment automation.
- Require at least two reviewers for security-sensitive areas: authentication via Sanctum, authorization via Gates & Policies plus Custom RBAC, Teacher Workspace isolation, file access private Lessons, payment-status and Subscription billing.
- Adopt pull request templates per feature type (Teacher Workspace feature, Student/Parent feature, Attendance feature, Exam Engine feature, Reporting feature, Billing feature) to ensure checklist completeness.
- Introduce protected branch rules requiring linear history consideration and signed commits for compliance-sensitive deployments.
- Maintain solo-friendly short-lived branch philosophy; team adds reviewer count, not ceremony overhead.

**CI/CD evolution (conceptual, no configuration file contents defined here):**

- Upon future approval, CI pipeline may enforce automated testing per `24_Testing_Strategy.md`: backend Feature/Unit, frontend integration, security scanning, dependency vulnerability scanning, linting, type checking. CI gates block merge if checks fail, analogous to pre-deployment checklist `26_Deployment_Plan.md` §26.1.
- CD pipeline may automate staging deployment on develop merge and production deployment on main tag creation per deployment milestones DE2-DE10. These pipelines remain compatible with cPanel Shared Hosting baseline initially, VPS/Cloud later.
- CI/CD definitions, workflow file contents, and infrastructure-as-code remain out of scope for this document; they belong to future operational documentation after approval.

**Infrastructure migration impact:**

- VPS/Cloud migration per `26_Deployment_Plan.md` §24-§25 does not change branching strategy. Main remains production baseline, develop remains integration. What changes is deployment target configuration in `35_Environment_Configuration.md` §23, not Git model.
- Object storage S3 future does not introduce separate repository; remains feature behind storage adapter boundary, tracked in same monorepo.

**Monorepo versus polyrepo decision:**

- Current decision D-023 modular monolith confirmed for Version 1. Future consideration of splitting frontend and backend into separate repositories requires explicit decision in `29_Project_Decisions.md` with consequences for history, permissions, and CODEOWNERS. Until approved, monorepo remains.

**Advanced Git features consideration:**

- LFS for large binary Lesson videos: not required for Version 1 because videos stored in Laravel Public Storage runtime, not version control per §18. If future requires tracking large reference assets, LFS evaluation requires separate approval ensuring private Teacher-owned content not publicly exposed via LFS storage.
- Submodules or subtrees for shared libraries: not introduced in Version 1; shared resources remain small explicit per `04_Project_Structure.md` §13.

**Documentation evolution:**

- New documents beyond `36_Git_Workflow.md` (e.g., `37_Release_Management.md`, `38_Backup_Recovery.md`, `39_Developer_Guide.md`, `40_AI_Development_Guide.md`) will extend governance. This document's numbering remains stable; new docs take next free number per `31_Master_Index.md` §7.3.
- Git workflow modifications follow Master Index §8 modification rules: confirm authorized, edit owning document (this document), update references inside same document, review Used By documents (deployment plan, roadmap, coding standards, master index), update revision and change history, run documentation review checklist §14.

**Long-term invariants:**

- All future evolution must preserve Teacher Workspace isolation BR-003, Archive over deletion BR-005, Audit Log immutability BR-006, historical preservation BR-007/BR-014, Flow A/B separation, one global Student account BR-001/BR-022, one Group per Teacher BR-002, Parent read-only BR-004/BR-020, one Teaching Subject immutable BR-016, Homework Text/Image/PDF only BR-021, three Attendance methods BR-010, Question Bank private four types BR-011.
- Canonical terminology remains mandatory across all future history.

---

# Consistency Review

A complete consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Project Context alignment | Passed — every rule preserves frozen V1 rules: BR-001 through BR-022, Audit Log Policy §10 (ten mandatory events), Archive Policy §11, five-role model Super Admin/Teacher/Teacher Staff/Student/Parent, Teaching Subjects single immutable BR-016, Web Application only BR-017, explicit non-goals §4.1 no marketplace, scope exclusions §4.2, PENDING items Q-005/Q-010/Q-011/Q-012/Q-013/Q-015 remain PENDING, canonical terminology §19. Single Source of Truth statement honored. |
| System Architecture alignment | Passed — Git workflow compatible with confirmed stack Laravel 12 PHP 8.3 React 19 TypeScript Vite Tailwind MySQL 8 Sanctum Gates & Policies Custom RBAC File Cache Database Queue Database sessions Laravel Public Storage Scheduler+Cron SMTP Apache/LiteSpeed cPanel primary VPS/Cloud future per `03_System_Architecture.md` §4.1. Modular monolith D-023 preserved, not microservices. |
| Project Structure alignment | Passed — repository structure AI_DOCS/backend/frontend/deployment/scripts/.editorconfig/.gitattributes/.gitignore per `04_Project_Structure.md` §1-§10 preserved. Backend Features organization EducationalGrades/Groups/Students/Parents/Attendance/Homework/Lessons/Exams/Reports/Payments/Subscriptions/Users/Settings/Files/Archive/AuditLog per §2 matches branching scope types. Frontend features lower-case kebab-case teacher-workspace/educational-grades/groups/students/parents/attendance/homework/lessons/exams/reports/payments (Flow B) /subscriptions (Flow A) /users/settings/files/archive/audit-log per §3 preserved in branch naming convention. Build artifact backend/public/build excluded per §6, storage structure per §5 excluded per Git Ignore Standards. |
| Deployment Plan alignment | Passed — branching maps to deployment milestones DE1-DE10 per `26_Deployment_Plan.md` §16, release workflow §16 aligns with §26.1 pre-deployment checklist, rollback workflow §17 aligns with §19 rollback triggers and §19.3 constraints Archive replaces deletion, isolation preserved, Audit Log preserved, Flow separation. Environment config ownership separation between deployment process (26) and config values (35) respected: Git workflow owns branch/tag process only, not variable values. No deployment scripts generated. |
| Development Roadmap alignment | Passed — branching strategy supports ten phases Phase 1 Foundation through Phase 10 Optimization per `27_Development_Roadmap.md` §4-§13, testing milestones T1-T10 §14, documentation milestones §15, deployment milestones §16, release strategy §17 single cohesive V1 release staging updates each phase, versioning strategy §18 semantic MAJOR.MINOR.PATCH mapped to tags §15. Version 1 scope lock §18.3 preserved, no intermediate production releases. |
| Coding Standards alignment | Passed — commit message convention extends `28_Coding_Standards.md` §14 format type(scope): description, types feat/fix/refactor/docs/style/test/chore/perf/ci/revert, scopes auth/rbac/teacher-workspace/educational-grades/groups/students/parents/attendance/homework/lessons/exams/reports/payments/subscriptions/files/archive/audit-log/api/frontend/backend/db/config/deps. Folder naming conventions §7 backend PascalCase, frontend kebab-case applied to branch naming §9. Class naming §9 canonical terms preserved: EducationalGrade not Class, Lesson not Course, TeacherWorkspace not Tenant, Archive not Delete, PaymentStatus Flow B, Subscription Flow A. Variable naming §11 canonical preserved. Import grouping §4.7 preserved conceptually. Code review checklist §19 maps to pull request standards §11 checklist. |
| RBAC and Security alignment | Passed — review process §12 orders security and isolation first, authorization via Gates & Policies + Custom RBAC per `08_RBAC.md`/`09_Permission_Matrix.md`, five roles boundaries Super Admin Platform-only, Teacher own Teacher Workspace isolated, Teacher Staff creating workspace assigned permissions only, Student own account per-Teacher records only, Parent linked-Students read-only only. File upload security per `23_Security_Standards.md` §9 Parent upload denied, video homework denied BR-021. Secrets handling per §7.4 and §21.10 credential never in history, .env outside web root, owner-readable only 600/640. Error message policy per §18 generic messages, no private data. Audit Log mandatory events per §15. |
| Business Rules alignment | Passed — every branch purpose example references correct BR: BR-001 one global Student account, BR-002 one Group per Teacher, BR-003 Teacher Workspace isolation absolute, BR-004 Parent read-only linked only, BR-005 Archive replaces deletion never deletion, BR-006 Audit Log immutable permanent, BR-007 transfer history preserved, BR-008 Billable Student enrollment duration >15 days Attendance/login not used formula, BR-009 Group Price Pricing Type Monthly/Per Lesson Flow B, BR-010 Attendance three methods Dynamic QR Code daily/ID Card printed/Manual, BR-011 Question Bank Teacher-owned private four types Multiple Choice/True/False/Essay/Bubble Sheet, BR-012 exam workspace-scoped, BR-013 Teacher Staff only creating workspace assigned permissions, BR-014 historical never deleted, BR-015 Pricing Super Admin owned, BR-016 one Teaching Subject immutable, BR-017 Web Application only, BR-018 Lesson videos Teacher-owned private, BR-019 V1 payments outside platform status only, BR-020 one Parent per Student Student Switcher, BR-021 Homework Text/Image/PDF only video not supported, BR-022 Student registration two methods duplicate not allowed. Historical per 32_Business_Rules.md catalog consistency. |
| Validation Rules alignment | Passed — Pull request standards §11 and Best Practices §22 enforce validation per `33_Validation_Rules.md` principles: no validation rule invented, no PENDING hardened, file type validation per owning resource context, Pricing Type Monthly/Per Lesson, Question Type enum, date ranges. No numeric file-size limit invented per `20_File_Storage.md` §12. |
| Error Codes alignment | Passed — merge strategy and pull request standards preserve error response envelope per `10_API_Design.md` §6 and `34_Error_Codes.md` standards: standardized code, safe user/internal messages, HTTP status discipline 401/403/404/409/422/429, logging requirements LOG-xx, no stack traces in history. |
| Environment Configuration alignment | Passed — Git Ignore Standards §18 aligns with `35_Environment_Configuration.md` §13 storage layout teacher-workspaces/lessons/homework/files and student-homework, §18 file permissions 755/775 storage/bootstrap/cache and 600/640 .env, §19 operational logs rotation vs Audit Log permanent in MySQL 8. No secrets committed. |
| Testing Strategy alignment | Passed — release workflow §16 requires full regression per `24_Testing_Strategy.md` §15, business rule coverage §21, role coverage, module coverage, authorization matrix, isolation regression, Archive regression, Audit Log regression, Flow separation regression, historical preservation, security checklist passes, UAT completed, staging validated. Release acceptance criteria §17.2 mirrors §20 general release criteria. |
| Feature Docs alignment | Passed — branch examples and pull request scopes reference feature docs: Exam Engine `15_Exam_Engine.md` Bubble Sheet electronic auto-grading, QR Attendance `16_QR_Attendance_System.md` three methods daily QR, Subscription Billing `17_Subscription_Billing.md` calendar month Billing Cycle, Reporting `18_Reporting_Analytics.md` role visibility, Notification exclusion `19_Notification_System.md` D-012 out of scope, File Storage `20_File_Storage.md` Laravel Public Storage private, Background Jobs `21_Background_Jobs.md` Database Queue Scheduler idempotency, Search Filtering `22_Search_Filtering.md` scope before filtering. |
| Decisions alignment | Passed — D-001 stack, D-002 payments status only, D-006 calendar month Billing Cycle, D-007 Billable >15 days enrollment only, D-008 one Teaching Subject, D-009 one Parent per Student, D-010 Bubble Sheet electronic auto-grading, D-011 Homework Text/Image/PDF only, D-012 notifications out of scope, D-013 two registration methods, D-014 PHP 8.3, D-015 Vite, D-016 Tailwind, D-017 React Router, D-018 TanStack Query, D-019 Hook Form+Zod, D-020 Multi-Tenant Teacher Workspace isolation, D-021 Backend-only authorization enforcement, D-022 REST /api/v1, D-023 Modular Monolith not microservices, D-024 Feature-based frontend, D-025 Layered backend thin controllers, D-030 one Group per Student per Teacher, D-031 transfer history preservation, D-032 Super Admin pricing ownership, D-033 Archive replaces deletion, D-034 Immutable Audit Log, D-035 Historical permanent retention, D-036 Flow A/B never conflated, D-037 Sanctum, D-038 Gates & Policies + Custom RBAC, D-039 HTTPS required production, D-040 Database session driver, D-041 File Cache, D-042 Database Queue, D-043 Laravel Public Storage, D-044 cPanel primary, D-045 three environments Local/Staging/Production, D-046 Documentation-first, D-047 ten-phase roadmap, D-048 Mandatory canonical terminology, D-049 Native mobile out of scope, D-050 Not marketplace, D-051 One Subject per Teacher. PENDING Q-005/Q-010/Q-011/Q-012/Q-013/Q-015 preserved PENDING. |
| Master Index alignment | Passed — document follows Master Index reading instructions §11.1: 31 then 00 then 30 then owning documents before authoring. Index update requirement per §13.4 noted in Future Git Strategy when new docs added `37_` through `40_`. Modification rules §8 preconditions followed. Conflict resolution policy §10 precedence Project Context wins. Documentation maintenance rules §13 ownership Architect. No empty placeholder cited as source. |
| AI Workflow alignment | Passed — AI Development Workflow §19 aligns with arena branch model observed, session fixation, documentation-first, no invention, PENDING discipline, frozen respect, terminology discipline, scope discipline, ownership discipline. Compatible with arena/019fb865-bunaasystem current session branch pattern. |
| Prohibited artifacts check | Passed — no Git commands generated, no shell examples, no CI/CD workflow file contents, no YAML, no source code, no API definitions, no database tables, SQL, UI implementation. Process description only. |
| Solo + Team optimization | Passed — workflow lightweight for solo: trunk-based simplicity, short-lived branches, self-review with delay, squash to keep develop linear, main always releasable. Team expansion path: CODEOWNERS, two reviewers for security-sensitive, linear history, signed commits consideration, pull request templates per feature type. Balances both. |
| Canonical terminology | Passed — Platform, Teacher Workspace, Educational Grade, Teaching Subject, Group, Pricing Type, Student, Parent, Teacher Staff, Super Admin, Subscription Flow A, payment status/flow B, Price Per Student, Enrollment, Archive, Audit Log, Dynamic QR Code, ID Card, Question Bank, Bubble Sheet, Student Switcher, Lesson, Billable Student, Billing Cycle, Homework used consistently. Non-canonical Class/Course/Delete/sub-teacher/tenant in product contexts avoided except as prohibited examples. Lower-case kebab-case for feature folders and branch descriptive segments per `04_Project_Structure.md` §11. |
| Requested section coverage | Passed — all 24 requested sections present in order: 1 Document Purpose, 2 Git Workflow Philosophy, 3 Repository Structure, 4 Branching Strategy, 5 Main Branches, 6 Feature Branches, 7 Hotfix Branches, 8 Release Branches, 9 Branch Naming Convention, 10 Commit Message Convention, 11 Pull Request Standards, 12 Code Review Process, 13 Merge Strategy, 14 Conflict Resolution Guidelines, 15 Version Tagging, 16 Release Workflow, 17 Rollback Workflow, 18 Git Ignore Standards, 19 AI Development Workflow, 20 Developer Workflow, 21 Repository Protection Rules, 22 Best Practices, 23 Common Mistakes to Avoid, 24 Future Git Strategy. |

---

*End of document. **REVISION 1.0** — Official Git workflow defining branching, commit, pull request, merge, tagging, release, rollback, ignore standards, AI development integration, developer workflow, protection rules, best practices, mistakes avoidance, and future strategy for Unified Education Platform Version 1. Optimized for Laravel 12, React 19, MySQL 8, cPanel Shared Hosting primary with VPS/Cloud future, solo development with future team expansion, and AI-assisted development compatibility. Docs before code; consistency over convenience; Archive — never delete.*
