# 28 — Coding Standards

## Document Scope

This document defines the complete coding standards for Version 1 of the Unified Education Platform. It establishes mandatory conventions for PHP, Laravel 12, React 19, TypeScript, naming, error handling, logging, validation, documentation, code review, and maintainability across the entire codebase.

This document does not define source code, implementation examples, API definitions, database tables, or UI implementation. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found.

The coding standards are built on the confirmed Version 1 technology baseline: **Laravel 12** backend with **PHP 8.3**, **React 19** frontend with **TypeScript**, **Vite**, **Tailwind CSS**, **MySQL 8** for persistence, **Laravel Sanctum** for authentication, **Laravel Gates & Policies with Custom RBAC** for authorization, **File Cache**, **Database Queue**, **Database session driver**, **Laravel Public Storage**, **Laravel Scheduler with Cron Jobs**, and **cPanel Shared Hosting** as the primary deployment target.

---

# 1. Coding Philosophy

The coding philosophy establishes the foundational beliefs that guide every coding decision in the project. All developers, reviewers, and AI sessions must internalize these principles before writing or reviewing code.

## 1.1 Documentation First

Code follows architecture; architecture follows documentation; documentation follows confirmed decisions. No feature is coded without a confirmed requirement, and no business rule is silently assumed. The canonical document set (`AI_DOCS/`) is the authoritative reference for every line of code.

## 1.2 Correctness Over Cleverness

Code must be correct, readable, and maintainable before it is clever, compact, or performant. A clear implementation that a new team member can understand in one reading is always preferred over a clever shortcut that requires explanation.

## 1.3 Backend Authority

The Laravel backend is the sole authority for authentication, authorization, tenant isolation, business rule enforcement, Audit Log creation, and persistence decisions. The frontend presents authorized data and collects user input but never replaces backend enforcement. No code — frontend or backend — may bypass these boundaries.

## 1.4 Tenant Isolation Is Non-Negotiable

Teacher Workspace isolation is the highest-priority architectural invariant. Every query, every API response, every file access, every search result, and every report must preserve workspace boundaries. No optimization, convenience shortcut, or performance improvement may weaken tenant isolation.

## 1.5 Archive, Never Delete

No code path may permanently delete data. Archive replaces deletion everywhere. Historical records remain available for reports and history queries. This applies to all entities, all actors, and all surfaces.

## 1.6 Audit Everything Important

Every important action produces an Audit Log entry. The Audit Log is append-only, immutable, and permanently retained. Code must not introduce paths that bypass Audit Log requirements.

## 1.7 Canonical Terminology

All code artifacts — variable names, class names, method names, API endpoints, database concepts, comments, and commit messages — must use the canonical terminology defined in `AI_DOCS/00_Project_Context.md` §19. Non-canonical terms must never appear in code.

## 1.8 Version 1 Scope Discipline

Code must not introduce features outside confirmed Version 1 scope. Native mobile applications, online payment gateways, notifications, marketplace behavior, video homework, multiple Teaching Subjects per Teacher, and multiple Parent accounts per Student are out of scope. No code path may implement, partially implement, or prepare for these features.

---

# 2. General Principles

These general principles apply to all code in the project, regardless of language, framework, or layer.

## 2.1 Single Responsibility

Every class, function, component, and module must have one clear responsibility. A Controller coordinates HTTP requests; a Service orchestrates business workflows; a Repository encapsulates query logic; a Model represents a business entity. Responsibilities must not bleed across layers.

## 2.2 Explicit Over Implicit

Code must be explicit about its intent, its data, and its side effects. Magic behavior, implicit global state, hidden dependencies, and undocumented assumptions must be avoided. A function that modifies data must make that modification visible in its name or return value.

## 2.3 Fail Fast and Safely

Invalid input, unauthorized access, missing resources, and business rule violations must be detected and rejected as early as possible. Error responses must provide enough information for the authorized user to understand the failure without revealing private data, implementation details, or unlinked Student information.

## 2.4 No Hard Delete

No code path may permanently delete any record. Archive is the only data removal mechanism. If code appears to delete a record, it is a defect.

## 2.5 No Silent Assumptions

Code must not silently assume PENDING decisions. Non-payment enforcement (Q-005), lesson video hosting (Q-010), Teacher Staff permission granularity (Q-011), Super Admin content visibility (Q-012), pricing model (Q-013), and localization (Q-015) must remain PENDING until formally resolved. Code must not harden these decisions as confirmed behavior.

## 2.6 Separation of Concerns

Backend and frontend have distinct responsibilities. Business logic belongs in the backend. Presentation logic belongs in the frontend. Database logic belongs in the persistence layer. No layer must own another layer's responsibility.

## 2.7 Flow A and Flow B Separation

Code must never conflate Flow A (Teacher → Platform Subscription) and Flow B (Student/Parent → Teacher fees). Separate models, separate services, separate controllers, separate routes, separate frontend features, separate query keys, and separate labels must be maintained.

## 2.8 Testability

Code must be written to be testable. Dependencies must be injectable. Side effects must be controllable. Business logic must be isolated from framework-specific concerns where practical.

## 2.9 No Premature Optimization

Performance optimization must not bypass authorization, tenant isolation, Archive rules, or Audit Log obligations. Optimization decisions must be based on measured bottlenecks, not assumptions.

## 2.10 Consistent Error Handling

All error paths must produce consistent, safe, and informative responses. Error handling must never expose implementation details, stack traces, SQL queries, server paths, credentials, or Teacher-private data.

---

# 3. Laravel Coding Standards

These standards govern all backend code written with Laravel 12 on PHP 8.3.

## 3.1 Application Structure

The backend follows the modular monolith structure defined in `AI_DOCS/04_Project_Structure.md` §2. Feature-based organization under `app/Features/` groups domain-specific code by business capability. Laravel framework directories remain recognizable for maintainability.

## 3.2 Controllers

Controllers act as thin request coordinators. They receive validated requests, resolve the authenticated role and scope context, call the appropriate service layer operation, and return standardized API responses.

Controllers must not:

- Contain core business-rule logic.
- Directly access the database through raw queries.
- Independently decide Teacher Workspace access.
- Calculate Billable Students.
- Process payments.
- Expose Teacher-private content beyond authorization boundaries.
- Implement notification behavior.

Controllers must be grouped by feature and role scope. A single controller must not handle operations from multiple unrelated features.

## 3.3 Services

Services own business workflows and domain orchestration. They coordinate authentication, authorization, tenant scoping, validation, persistence, Archive behavior, Audit Log recording, and response shaping.

Services must be transaction-aware where multiple business changes must succeed together. For auditable actions, the business action and Audit Log entry must be coordinated so that required auditability is not silently lost.

Services must not be injected with HTTP-specific dependencies (Request objects). Input data must be passed as primitive values or dedicated data-transfer objects.

## 3.4 Repositories

Repositories encapsulate complex persistence and query logic. They retrieve records scoped to the correct Teacher Workspace, Student, Parent linked Student, or Platform context.

Repositories must:

- Always scope queries to the resolved context before applying additional filters.
- Apply active-versus-archived filtering consistently at the query level.
- Support MySQL 8-compatible access patterns.

Repositories must not:

- Bypass authorization.
- Expose records outside the caller's resolved scope.
- Hide business-rule violations by returning broader data for filtering later.
- Depend on Redis, external search infrastructure, or non-MySQL storage.

Repositories should be introduced where they reduce duplication or complexity, not as mandatory ceremony for every simple model operation.

## 3.5 Models

Models represent backend business entities and relationships at the Laravel application layer. They must represent confirmed logical entities from the Data Dictionary.

Models must not:

- Be treated as permission boundaries by themselves.
- Expose cross-Teacher relationships without explicit authorization and scope checks.
- Define hard deletion as an application behavior.
- Preserve Teaching Subject immutability after Teacher account creation.
- Create duplicate Student accounts.

Models must use soft delete (Archive) behavior through Laravel's built-in soft delete mechanism or equivalent Archive state representation.

## 3.6 Form Requests

Form Requests define request validation and may perform early authorization checks. They validate required inputs, enum values, date ranges, file rules, and resource references.

Form Requests must not replace Policies, Gates, or Custom RBAC. Validation and authorization work together but remain distinct responsibilities.

Form Request classes must be grouped by feature under `app/Http/Requests/` and named after the resource and action they validate.

## 3.7 Policies and Gates

Policies and Gates are the core Laravel authorization mechanisms. They enforce role boundaries, Teacher Workspace ownership, Teacher Staff assigned permissions, Student self-scope, Parent linked-Student read-only access, Super Admin Platform scope, Archive and restore permissions, file access ownership, and Audit Log visibility boundaries.

Policy classes must be registered for every resource that requires authorization. Gate definitions must use the permission names from the Permission Matrix as the logical catalog.

## 3.8 Middleware

Middleware applies cross-cutting request checks before feature logic executes. Middleware must be lightweight and must not replace feature-specific authorization.

Middleware must not:

- Assume Teacher Staff permission granularity beyond assigned permissions.
- Implement unconfirmed non-payment enforcement.
- Enable notification or payment gateway behavior.

## 3.9 API Resources

API Resources shape response payloads for API endpoints. They must return only the data the frontend needs and avoid returning large nested relationships when the frontend only needs summary data.

API Resources must not expose unauthorized data even if the underlying model contains it. Authorization decisions must be applied before the Resource transformation, not within the Resource itself.

## 3.10 Jobs

Jobs use the Database Queue for deferred backend work. Jobs must respect cPanel Shared Hosting execution time limits. Long-running jobs must be chunked into smaller batches.

Jobs must preserve Teacher Workspace scope and authorization context where relevant. Mandatory business actions must not be considered complete if required persistence or required Audit Log recording failed.

Jobs must not introduce notifications, payment processing, WebSockets, or microservice behavior.

## 3.11 Commands

Console Commands support Laravel Scheduler execution by cPanel Cron Jobs. Commands must not duplicate Laravel artisan functionality, embed production secrets, or introduce unsupported infrastructure.

## 3.12 Exception Handling

All exceptions must be caught and mapped to standardized API error responses. The `app/Exceptions/` directory contains application exception mapping and safe error normalization.

Exception handling must:

- Return standardized error responses consistent with `AI_DOCS/10_API_Design.md`.
- Deny unauthorized access safely.
- Hide restricted data.
- Reject unsupported Version 1 actions.
- Preserve previous valid state when updates fail.
- Record failed login events in the Audit Log.

Exception handling must not expose implementation details, stack traces, SQL details, server paths, secrets, or Teacher-private data.

## 3.13 Service Providers

Service Providers register application bindings, middleware, policies, and authorization rules. They must be organized by scope and feature where that improves clarity.

Authorization registration must cover all five confirmed roles and all resources that require access control.

## 3.14 Configuration

Configuration files must be located in the `config/` directory and must not contain environment-specific values or secrets. All secrets must be referenced through environment variables.

Configuration values must not create out-of-scope notification, payment gateway, or marketplace features.

---

# 4. PHP Standards (PSR-12)

All PHP code must follow PSR-12 (Extended Coding Style) as the baseline coding standard, with the following project-specific additions.

## 4.1 File Structure

PHP files must use the `<?php` declaration, must not use the closing `?>` tag, and must use UTF-8 encoding without BOM.

Each PHP file must contain one class, interface, trait, or enum. The file name must match the class name following PSR-4 autoloading conventions.

## 4.2 Namespace Conventions

Namespaces must follow PSR-4 autoloading. The root namespace for the application is `App\`. Feature-specific code uses `App\Features\{FeatureName}\` namespaces.

Namespace declarations must use PascalCase. Feature directory names use canonical PascalCase feature names, for example `EducationalGrades` and `TeacherWorkspace`.

## 4.3 Class Structure

Class declarations must follow PSR-12 ordering:

1. Namespace declaration.
2. Import statements (grouped by type: PHP core, framework, application).
3. Class declaration.
4. Constants.
5. Properties.
6. Constructor.
7. Methods.

Properties must be declared with visibility (public, protected, private). Typed properties are preferred over untyped properties.

## 4.4 Method Structure

Methods must:

- Declare return types.
- Use type hints for all parameters.
- Follow the single-responsibility principle.
- Be kept short and focused. If a method exceeds a reasonable length, it should be decomposed into smaller methods.

Method names must use camelCase and describe the action performed.

## 4.5 Type Declarations

PHP 8.3 typed properties, parameter types, and return types must be used consistently. Nullable types must use the `?Type` syntax. Union types must be used where the PHP 8.3 type system supports them.

## 4.6 Strict Types

All PHP files must declare `declare(strict_types=1);` at the top of the file. This ensures type safety at runtime and prevents implicit type coercion.

## 4.7 Import Statements

Import statements must be grouped in the following order, with a blank line between each group:

1. PHP core functions and classes.
2. Framework (Laravel) classes.
3. Application classes.

Import statements must use fully qualified class names with the `use` keyword. Aliases must be used only when necessary to resolve naming conflicts.

## 4.8 Formatting

- Code must use 4 spaces for indentation, not tabs.
- Lines must not exceed 120 characters where practical.
- Method chains may be broken across multiple lines for readability.
- Array syntax must use the short array syntax `[]` instead of `array()`.

## 4.9 Comments

- Comments must explain *why*, not *what*.
- PHPDoc blocks must be used for all public methods, describing parameters, return types, and thrown exceptions.
- Inline comments must be used sparingly and only when the intent is not obvious from the code itself.
- Comments must not contain TODO items without an associated tracking reference.

## 4.10 PHP 8.3 Features

PHP 8.3 features may be used where they improve code clarity and type safety, including:

- Typed class constants.
- `#[\Override]` attribute.
- `json_validate()` function.
- Deep cloning of readonly properties.
- Typed enum constants.

PHP features must be used conservatively and only when they serve clarity, not for novelty.

---

# 5. React Standards

These standards govern all frontend code written with React 19, TypeScript, Vite, and Tailwind CSS.

## 5.1 Component Structure

Components must be functional components using React hooks. Class components must not be used.

Each component must:

- Have a single clear responsibility.
- Accept props through a typed interface.
- Return JSX that reflects the component's purpose.
- Handle loading, empty, error, and success states where applicable.

## 5.2 Component Organization

Components are organized by reuse and domain ownership:

- **Shared primitives** (`src/components/primitives/`): Accessible, domain-neutral controls and status patterns.
- **Shared composites** (`src/components/shared/`): Reusable patterns such as paginated data regions, filter controls, and context selectors.
- **Layout components** (`src/layouts/`): Structural role-aware application shells.
- **Feature components** (`src/features/{feature}/components/`): Domain-specific compositions owned by a feature.
- **Route modules** (`src/features/{feature}/pages/`): Compose a layout and feature entry point for a route.

Feature components must not be imported by unrelated features. Cross-feature reuse belongs in shared domain-neutral components or common technical utilities.

## 5.3 Hooks

Custom hooks must use the `use` prefix and must encapsulate reusable stateful logic. Hooks must not embed authorization decisions or business-rule enforcement.

TanStack Query hooks must be scoped by feature and must include every access-defining context in their query keys. Query keys must prevent stale or cross-boundary cache hits.

## 5.4 State Management

State must be classified by ownership and lifetime:

- **Server state**: TanStack Query owns data retrieved from the backend.
- **Authentication and access context**: Application-level auth boundary owns current user, active role, and capability metadata.
- **Route state**: React Router owns URL, path parameters, and validated query parameters.
- **Form state**: React Hook Form owns transient editable field values.
- **Local presentation state**: Component-local state owns ephemeral concerns.

State must not be duplicated across layers. Server data must not be duplicated in global client state.

## 5.5 Forms

React Hook Form is the standard for all interactive forms. Each feature form must own its default values, Zod validation schema, field-level interaction state, submission lifecycle, and success handling.

Forms must prevent duplicate submissions while a request is pending. On failed submission, valid entered values must remain available where safe.

## 5.6 Routing

React Router is the authoritative client-side navigation mechanism. Routes must be grouped by access boundary (Super Admin, Teacher/Teacher Staff, Student, Parent).

Route metadata must describe the expected role context. Route guards must use the authenticated user and permission information returned by the backend. Route guards are usability measures only; backend authorization remains authoritative.

Routes must be lazily loaded at feature and layout boundaries. Navigation changes must clear or invalidate context-sensitive data.

## 5.7 API Communication

A shared HTTP boundary is the only frontend path to the Laravel REST API. Feature modules must use typed, resource-focused adapters rather than issuing ad hoc requests from layout or presentation components.

The HTTP boundary must:

- Apply the public API base configuration and Sanctum-compatible request behavior.
- Serialize JSON, multipart form data, pagination, filter, and sorting inputs according to `AI_DOCS/10_API_Design.md`.
- Parse documented success envelopes, pagination metadata, validation errors, and normalized error responses.
- Map HTTP outcomes into a small stable frontend error taxonomy.
- Ensure requests are cancellable when routes or query inputs change.

Feature modules must not import the HTTP boundary directly from presentation components. They must use their feature-owned query hooks and API adapters.

## 5.8 Error Handling

Errors must be normalized at the shared HTTP boundary and presented by the nearest appropriate scope: field, form, feature, route, or application boundary.

Error handling must:

- Clear protected context on 401 (unauthenticated).
- Present generic access-denied state on 403 (unauthorized).
- Use neutral unavailable state on 404 (not found).
- Preserve form data on 409 (conflict).
- Attach server field messages on 422 (validation failure).
- Present retryable task-level failure on 429 or network errors.

Error boundaries must not display request headers, credentials, raw backend payloads, stack traces, Teacher Workspace identifiers, file paths, or private record data.

## 5.9 Accessibility

The Web Application must be usable with keyboard navigation, assistive technology, zoom, and responsive browser layouts. Components must use semantic document structure, native controls, programmatic labels, and visible focus indicators.

Accessibility must not expose actions or records that the role is not permitted to access.

## 5.10 Performance

Frontend performance must be optimized through route-level lazy loading, code splitting, stable component boundaries, TanStack Query caching, cancellable requests, and efficient file handling.

Performance optimization must never bypass authorization, expose cached protected data, relax Archive behavior, or present an unconfirmed action as complete.

---

# 6. TypeScript Standards

These standards govern all TypeScript code in the frontend.

## 6.1 Strict Mode

TypeScript strict mode must be enabled in `tsconfig.json`. All TypeScript files must pass strict type checking without errors.

## 6.2 Type Definitions

Shared TypeScript contracts must be located in `src/types/`. Feature-specific types must remain in their feature directory under `types/` or co-located with their consuming code.

Types must represent stable API-facing and application concepts. They must not be generated from or duplicated with backend model definitions without a formal contract agreement.

## 6.3 Interfaces vs. Type Aliases

Interfaces must be used for object shapes and class contracts. Type aliases must be used for union types, intersection types, and computed types. Consistency within a file is more important than a global rule, but new files should prefer interfaces for object shapes.

## 6.4 Enum Usage

String literal union types are preferred over TypeScript enums for simple value sets. Enums may be used where runtime iteration or reverse mapping is needed.

## 6.5 Generics

Generics must be used where they improve type safety and reusability without adding unnecessary complexity. Generic type parameters must have descriptive names (e.g., `TResponse`, `TData`, `TError`).

## 6.6 Any and Unknown

The `any` type must not be used. The `unknown` type must be used where the type is genuinely unknown and must be narrowed through type guards before use.

## 6.7 Null and Undefined

Optional properties and nullable types must be explicitly declared. Nullish coalescing (`??`) and optional chaining (`?.`) must be used instead of manual null/undefined checks where they improve readability.

## 6.8 Imports

Import statements must use path aliases configured in `tsconfig.json` where the project uses them. Imports must be grouped:

1. React and framework imports.
2. Third-party library imports.
3. Application imports (absolute paths).
4. Relative imports.

Barrel exports (`index.ts`) may be used within features to simplify imports, but must not re-export internal implementation details of other features.

---

# 7. Folder Naming Conventions

Folder names must be descriptive, canonical, and consistent across the entire project.

## 7.1 Backend Folders

| Area | Convention | Examples |
|------|-----------|----------|
| Laravel PHP namespaces | PascalCase, PSR-4 | `app/Features/EducationalGrades/` |
| Laravel configuration | Lower-case | `config/database.php` |
| Laravel database artifacts | Lower-case with underscores for migrations | `database/migrations/` |
| Storage namespaces | Lower-case kebab case | `storage/app/public/teacher-workspaces/` |

## 7.2 Frontend Folders

| Area | Convention | Examples |
|------|-----------|----------|
| Feature folders | Lower-case kebab case | `src/features/teacher-workspace/` |
| Component folders | Lower-case kebab case | `src/components/primitives/` |
| Layout folders | Lower-case kebab case | `src/layouts/` |
| Utility folders | Lower-case kebab case | `src/lib/` |

## 7.3 Root-Level Folders

| Area | Convention | Examples |
|------|-----------|----------|
| Documentation | Two-digit numeric prefix, Pascal-style with underscores | `AI_DOCS/04_Project_Structure.md` |
| Deployment | Lower-case kebab case | `deployment/cpanel/` |
| Scripts | Lower-case kebab case | `scripts/` |

## 7.4 Canonical Terminology Enforcement

Folder names must use canonical terms:

- `educational-grades` (never `classes`)
- `lessons` (never `courses`)
- `teacher-workspace` (never `tenant`)
- `archive` (never `delete`)
- `payments` for Flow B payment status
- `subscriptions` for Flow A Subscription

---

# 8. File Naming Conventions

File names must be descriptive, canonical, and consistent with the conventions of their language and framework.

## 8.1 PHP Files

| File Type | Convention | Examples |
|-----------|-----------|----------|
| Classes | PascalCase, matching class name | `StoreEducationalGradeRequest.php` |
| Configuration | Lower-case, snake_case | `database.php` |
| Migrations | Laravel chronological convention | `2026_07_28_000001_create_educational_grades_table.php` |
| Seeders | PascalCase with Seeder suffix | `EducationalGradeSeeder.php` |
| Factories | PascalCase with Factory suffix | `EducationalGradeFactory.php` |

## 8.2 TypeScript / React Files

| File Type | Convention | Examples |
|-----------|-----------|----------|
| Components | PascalCase | `EducationalGradeList.tsx` |
| Hooks | camelCase with `use` prefix | `useEducationalGrades.ts` |
| Types | PascalCase | `EducationalGradeTypes.ts` |
| Utilities | camelCase or lower-case kebab case | `formatDate.ts` |
| Configuration | camelCase or lower-case kebab case | `vite.config.ts` |
| Test files | Match source file with test suffix | `EducationalGradeList.test.tsx` |
| Page components | PascalCase with `Page` suffix | `EducationalGradesPage.tsx` |

## 8.3 Documentation Files

| File Type | Convention | Examples |
|-----------|-----------|----------|
| AI_DOCS | Two-digit prefix, Pascal-style with underscores | `28_Coding_Standards.md` |
| README | Uppercase | `README.md` |
| License | Uppercase | `LICENSE` |

## 8.4 Configuration Files

| File Type | Convention | Examples |
|-----------|-----------|----------|
| Environment templates | Lower-case with dot prefix | `.env.example` |
| Editor configuration | Lower-case with dot prefix | `.editorconfig` |
| Git configuration | Lower-case with dot prefix | `.gitignore`, `.gitattributes` |

---

# 9. Class Naming Conventions

Class names must be PascalCase and must describe the entity, service, or responsibility they represent.

## 9.1 Laravel Classes

| Class Type | Naming Pattern | Examples |
|------------|---------------|----------|
| Controllers | `{Resource}Controller` | `EducationalGradeController` |
| Services | `{Domain}Service` or `{Action}Service` | `StudentEnrollmentService` |
| Repositories | `{Resource}Repository` | `StudentRepository` |
| Models | Singular PascalCase (entity name) | `EducationalGrade` |
| Policies | `{Resource}Policy` | `EducationalGradePolicy` |
| Form Requests | `{Action}{Resource}Request` | `StoreEducationalGradeRequest` |
| API Resources | `{Resource}Resource` | `EducationalGradeResource` |
| Jobs | `{Action}{Resource}Job` | `CalculateBillableStudentsJob` |
| Commands | `{Action}{Resource}Command` | `ProcessBillingCycleCommand` |
| Events | `{Resource}{Action}Event` | `AttendanceRecordedEvent` |
| Listeners | `{Event}Listener` | `AttendanceRecordedListener` |
| Middleware | `{Purpose}Middleware` | `TeacherWorkspaceScopeMiddleware` |
| Exceptions | `{Type}Exception` | `TeacherWorkspaceAccessDeniedException` |
| Enums | PascalCase singular | `PricingType`, `QuestionType` |

## 9.2 React / TypeScript Classes and Types

| Entity | Naming Pattern | Examples |
|--------|---------------|----------|
| Components | PascalCase descriptive | `EducationalGradeList` |
| Interfaces | PascalCase descriptive, no `I` prefix | `EducationalGradeData` |
| Type aliases | PascalCase descriptive | `PricingTypeValue` |
| Enums | PascalCase singular | `AttendanceMethod` |

## 9.3 Canonical Terminology in Class Names

Class names must use canonical terms:

- `EducationalGrade` (never `Class` or `Level`)
- `Lesson` (never `Course`)
- `TeacherWorkspace` (never `Tenant`)
- `Archive` (never `Delete`)
- `PaymentStatus` for Flow B (never `Subscription` for Flow B)
- `Subscription` for Flow A only

---

# 10. Function Naming Conventions

Function and method names must be camelCase and must describe the action performed.

## 10.1 Backend Methods

| Method Type | Naming Pattern | Examples |
|-------------|---------------|----------|
| Controller methods | HTTP verb or action | `index`, `store`, `show`, `update`, `archive`, `restore` |
| Service methods | Verb + noun (action description) | `enrollStudent`, `calculateBillableStudents`, `recordAttendance` |
| Repository methods | Find/get/create + resource | `findById`, `getActiveByWorkspace`, `createEnrollment` |
| Model accessors | `get{Attribute}Attribute` | `getFullNameAttribute` |
| Model mutators | `set{Attribute}Attribute` | `setPriceAttribute` |
| Model scopes | Descriptive query scope | `scopeActive`, `scopeByWorkspace` |
| Policy methods | Verb matching permission action | `view`, `create`, `update`, `archive`, `restore` |

## 10.2 Frontend Functions

| Function Type | Naming Pattern | Examples |
|---------------|---------------|----------|
| Components | PascalCase (function component) | `EducationalGradeList` |
| Hooks | `use` + noun or verb | `useEducationalGrades`, `useAuth` |
| Event handlers | `handle` + event | `handleSubmit`, `handleArchive`, `handleStudentSwitch` |
| Utility functions | camelCase verb + noun | `formatDate`, `parseErrorMessage`, `buildQueryString` |
| API adapter functions | verb + resource | `fetchEducationalGrades`, `createEducationalGrade` |
| Query key factories | Resource-based descriptive | `educationalGradesKeys`, `studentsKeys` |

## 10.3 Boolean Functions and Properties

Boolean-returning functions and properties must use prefixes that indicate a true/false return:

- `is` + adjective: `isActive`, `isArchived`, `isBillable`
- `has` + noun: `hasPermission`, `hasLinkedStudents`
- `can` + verb: `canArchive`, `canRestore`
- `should` + verb: `shouldIncludeArchived`

---

# 11. Variable Naming Conventions

Variable names must be descriptive, camelCase, and must clearly communicate their purpose.

## 11.1 Backend Variables

| Variable Type | Convention | Examples |
|---------------|-----------|----------|
| Local variables | camelCase | `teacherWorkspace`, `billableStudentCount` |
| Class properties | camelCase with visibility | `private string $teacherWorkspaceId;` |
| Constants | UPPER_SNAKE_CASE | `MAX_UPLOAD_SIZE`, `BILLABLE_DAYS_THRESHOLD` |
| Enum cases | camelCase or snake_case per Laravel convention | `PricingType::Monthly`, `PricingType::PerLesson` |
| Configuration keys | Lower-case dot notation | `app.name`, `database.default` |
| Environment variables | UPPER_SNAKE_CASE | `DB_HOST`, `APP_URL`, `VITE_API_BASE_URL` |

## 11.2 Frontend Variables

| Variable Type | Convention | Examples |
|---------------|-----------|----------|
| Local variables | camelCase | `teacherWorkspace`, `isLoading` |
| Constants | UPPER_SNAKE_CASE or camelCase for local | `MAX_RETRY_COUNT`, `apiBaseUrl` |
| Component props | camelCase | `studentName`, `onSubmit`, `isReadonly` |
| State variables | camelCase with descriptive prefix | `isOpen`, `hasError`, `selectedStudent` |
| Query keys | camelCase factory pattern | `educationalGradesKeys.list(workspaceId)` |
| CSS class strings | Tailwind utility classes in template literals | `className={\`p-4 \${isError ? 'text-red-500' : ''}\`}` |

## 11.3 Canonical Terminology in Variables

Variable names must use canonical terms:

- `teacherWorkspaceId` (never `tenantId`)
- `educationalGradeId` (never `classId`)
- `lessonId` (never `courseId`)
- `isArchived` (never `isDeleted`)
- `paymentStatus` for Flow B (never `subscriptionStatus` for Flow B)
- `subscription` for Flow A only

---

# 12. Database Naming Conventions

Database naming conventions govern table names, column names, indexes, and migrations. These conventions apply at the logical and physical levels.

## 12.1 Table Names

Table names must be lowercase, plural, and use underscores to separate words.

| Entity | Table Name |
|--------|-----------|
| User Identity | `users` |
| Teacher Workspace | `teacher_workspaces` |
| Educational Grade | `educational_grades` |
| Group | `groups` |
| Student | `students` |
| Parent | `parents` |
| Attendance Record | `attendance_records` |
| Homework | `homework` |
| Lesson | `lessons` |
| Question Bank | `question_banks` |
| Question | `questions` |
| Exam | `exams` |
| Exam Attempt | `exam_attempts` |
| Flow A Subscription | `subscriptions` |
| Flow B Payment Status | `payment_statuses` |
| Audit Log Entry | `audit_log_entries` |
| File Reference | `file_references` |

## 12.2 Column Names

Column names must be lowercase, singular, and use underscores to separate words.

| Column Type | Convention | Examples |
|-------------|-----------|----------|
| Primary key | `id` | `id` |
| Foreign key | `{referenced_table_singular}_id` | `teacher_workspace_id`, `student_id`, `educational_grade_id` |
| Timestamps | Laravel convention | `created_at`, `updated_at` |
| Soft delete (Archive) | Laravel convention or `archived_at` | `deleted_at` or `archived_at` |
| Boolean flags | `is_` prefix or descriptive | `is_active`, `is_archived` |
| Status fields | Descriptive noun | `status`, `payment_status` |
| Name fields | Descriptive noun | `name`, `title`, `description` |
| Price fields | `price`, `amount` | `price`, `amount` |
| Date fields | `{purpose}_date` or `{purpose}_at` | `enrollment_date`, `billing_cycle_start` |

## 12.3 Index Names

Index names must follow the convention: `{table}_{column(s)}_{type}`

| Index Type | Convention | Examples |
|------------|-----------|----------|
| Primary | `{table}_pkey` | `educational_grades_pkey` |
| Foreign key | `{table}_{column}_fkey` | `groups_educational_grade_id_fkey` |
| Unique | `{table}_{column(s)}_unique` | `students_email_unique` |
| Index | `{table}_{column(s)}_index` | `attendance_records_student_id_index` |

## 12.4 Migration Names

Migration names must use Laravel's chronological naming convention and describe the schema intent without leaking data.

| Migration Type | Convention | Examples |
|----------------|-----------|----------|
| Create table | `create_{table}_table` | `create_educational_grades_table` |
| Add column | `add_{column}_to_{table}_table` | `add_archived_at_to_educational_grades_table` |
| Create index | `index_{table}_{columns}` | `index_educational_grades_teacher_workspace_id` |
| Create pivot | `create_{table1}_{table2}_pivot` | `create_student_group_enrollments` |

## 12.5 Canonical Terminology in Database Names

Database names must use canonical terms:

- `educational_grades` (never `classes`)
- `lessons` (never `courses`)
- `teacher_workspaces` (never `tenants`)
- `subscriptions` for Flow A only
- `payment_statuses` for Flow B only
- `archived_at` or `deleted_at` for Archive state (never implying permanent deletion)

---

# 13. API Naming Conventions

API naming conventions govern URL paths, resource names, query parameters, and response structure.

## 13.1 URL Path Structure

All Version 1 endpoints use the `/api/v1` prefix. URL paths must use lowercase, kebab-case, and plural resource names.

| Scope | Path Pattern | Examples |
|-------|-------------|----------|
| Authentication | `/api/v1/auth/{action}` | `/api/v1/auth/login`, `/api/v1/auth/logout` |
| Platform (Super Admin) | `/api/v1/platform/{resource}` | `/api/v1/platform/teachers`, `/api/v1/platform/subscriptions` |
| Teacher Workspace | `/api/v1/teacher-workspace/{resource}` | `/api/v1/teacher-workspace/educational-grades` |
| Student | `/api/v1/student/{resource}` | `/api/v1/student/homework`, `/api/v1/student/exams` |
| Parent | `/api/v1/parent/{resource}` | `/api/v1/parent/linked-students` |

## 13.2 Resource Names

Resource names must use canonical terms and plural forms:

| Resource | API Name |
|----------|---------|
| Educational Grade | `educational-grades` |
| Group | `groups` |
| Student | `students` |
| Attendance | `attendance` |
| Homework | `homework` |
| Lesson | `lessons` |
| Question Bank | `question-banks` |
| Exam | `exams` |
| Payment Status (Flow B) | `payment-status` |
| Subscription (Flow A) | `subscriptions` |
| Audit Log | `audit-logs` |
| Teacher Staff | `teacher-staff` |

## 13.3 Action Endpoints

Non-CRUD actions must use explicit action suffixes:

| Action | Path Pattern | Examples |
|--------|-------------|----------|
| Archive | `POST /{resource}/{id}/archive` | `POST /educational-grades/123/archive` |
| Restore | `POST /{resource}/{id}/restore` | `POST /educational-grades/123/restore` |
| Move | `POST /{resource}/{id}/move-group` | `POST /students/456/move-group` |
| Calculate | `POST /{resource}/calculate` | `POST /subscriptions/calculate` |
| Scan | `POST /{resource}/scan` | `POST /attendance/scan-dynamic-qr` |
| Submit | `POST /{resource}/{id}/submit` | `POST /exams/789/submit` |

## 13.4 Query Parameters

Query parameters must use snake_case:

| Parameter | Convention | Examples |
|-----------|-----------|----------|
| Pagination | `page`, `per_page` | `?page=2&per_page=15` |
| Filtering | `status`, `group_id`, `student_id` | `?status=active&group_id=5` |
| Sorting | `sort`, `-sort` (descending) | `?sort=name&-sort=created_at` |
| Date range | `from_date`, `to_date` | `?from_date=2026-01-01&to_date=2026-01-31` |
| Billing cycle | `billing_cycle` | `?billing_cycle=2026-07` |

## 13.5 Response Envelope

API responses must use the standardized envelope defined in `AI_DOCS/10_API_Design.md`:

- Success responses include `data` and optional `meta`.
- Error responses include `success` (false), `error.code`, `error.message`, and optional `error.details`.
- Validation errors include `errors` with field-level messages.

## 13.6 Canonical Terminology in API Names

API names must use canonical terms:

- `educational-grades` (never `classes`)
- `lessons` (never `courses`)
- `teacher-workspace` (never `tenant`)
- `archive` (never `delete`)
- `payment-status` for Flow B
- `subscriptions` for Flow A only

---

# 14. Git Commit Message Convention

Git commit messages must follow a structured format that communicates intent, scope, and impact clearly.

## 14.1 Commit Message Format

Commit messages must follow this format:

```
{type}({scope}): {short description}

{optional body}

{optional footer}
```

## 14.2 Commit Types

| Type | Purpose |
|------|---------|
| `feat` | New feature or capability. |
| `fix` | Bug fix. |
| `refactor` | Code restructuring without behavior change. |
| `docs` | Documentation changes. |
| `style` | Formatting, whitespace, or style changes (no logic change). |
| `test` | Adding or modifying tests. |
| `chore` | Build process, dependencies, or tooling changes. |
| `perf` | Performance improvement. |
| `ci` | Continuous integration changes. |
| `revert` | Reverting a previous commit. |

## 14.3 Commit Scopes

Scopes must identify the affected feature or area:

| Scope | Meaning |
|-------|---------|
| `auth` | Authentication. |
| `rbac` | Authorization and RBAC. |
| `teacher-workspace` | Teacher Workspace features. |
| `educational-grades` | Educational Grade module. |
| `groups` | Group module. |
| `students` | Student module. |
| `parents` | Parent module. |
| `attendance` | Attendance module. |
| `homework` | Homework module. |
| `lessons` | Lesson module. |
| `exams` | Exam Engine module. |
| `reports` | Reporting module. |
| `payments` | Flow B payment status. |
| `subscriptions` | Flow A Subscription. |
| `files` | File storage. |
| `archive` | Archive and restore. |
| `audit-log` | Audit Log. |
| `api` | API endpoints. |
| `frontend` | Frontend general. |
| `backend` | Backend general. |
| `db` | Database migrations or schema. |
| `config` | Configuration changes. |
| `deps` | Dependency updates. |

## 14.4 Commit Message Rules

- The short description must be in imperative mood (e.g., "add", "fix", "update", "remove").
- The short description must not exceed 72 characters.
- The body must explain *why* the change was made, not *what* was changed (the diff shows what).
- The body must wrap at 72 characters.
- The footer must reference related issues or breaking changes where applicable.
- Breaking changes must include `BREAKING CHANGE:` in the footer.

## 14.5 Commit Message Examples

- `feat(attendance): add Dynamic QR Code daily generation`
- `fix(students): prevent duplicate Student account creation`
- `refactor(payments): separate Flow A and Flow B payment services`
- `docs(api): update endpoint specification for educational grades`
- `test(exams): add Bubble Sheet automatic grading tests`
- `chore(deps): update Laravel to 12.x`

## 14.6 Canonical Terminology in Commit Messages

Commit messages must use canonical terms. "Class" must not be used for Educational Grade. "Course" must not be used for Lesson. "Delete" must not be used for Archive. "Tenant" must not be used for Teacher Workspace.

---

# 15. Error Handling Standards

Error handling must be consistent, safe, and informative across all layers of the application.

## 15.1 Backend Error Handling

### Standardized Error Response Structure

All API error responses must use the structure defined in `AI_DOCS/10_API_Design.md` §6. Error responses must include:

- `success` as false.
- `error.code` as a stable machine-readable error code.
- `error.message` as a human-readable message.
- `error.details` as optional non-sensitive detail.
- `request_id` as optional request reference.

### HTTP Status Code Usage

| Status | Usage |
|--------|-------|
| 400 | Bad request or invalid operation. |
| 401 | Authentication required or failed. |
| 403 | Authenticated user is not authorized. |
| 404 | Resource not found or not visible. |
| 409 | Conflict with business rule or current state. |
| 422 | Validation failed. |
| 429 | Rate limit exceeded. |
| 500 | Unexpected server error. |

### Content Restrictions

Error responses must never include:

- SQL queries or database error details.
- Stack traces or exception chain details.
- Server file paths or directory structures.
- Database credentials or application secrets.
- Teacher-private data, unlinked Student data, or another Teacher Workspace's information.
- Internal API endpoint names or implementation details.
- Framework or library version information.

### Error Logging

Detailed error information (stack traces, SQL context, request details) must be logged to operational logs for troubleshooting. Error responses to users must contain only safe, generic information. `APP_DEBUG` must be false in production.

## 15.2 Frontend Error Handling

### Error Classification

| Error Class | Response |
|-------------|---------|
| 401 Unauthenticated | Clear protected context and cache, direct to authentication. |
| 403 Unauthorized | Present generic access-denied state, do not reveal resource existence. |
| 404 Unavailable | Use neutral unavailable/not-found state. |
| 409 Conflict | Preserve form data, explain current state prevents completion. |
| 422 Validation | Attach server field messages to the active form. |
| 429 / Network | Present retryable task-level failure. |
| Unexpected | Contain with route- and application-level error boundaries. |

### Error Boundaries

Error boundaries must not display request headers, credentials, raw backend payloads, stack traces, Teacher Workspace identifiers, file paths, or private record data.

## 15.3 Business Rule Violations

Business rule violations must be rejected with appropriate HTTP status codes and messages:

| Violation | Status | Handling |
|-----------|--------|---------|
| Duplicate Student account | 409 | Reject creation; support assignment of existing Student. |
| Multiple Groups per Student per Teacher | 409 | Reject assignment; require movement workflow. |
| Teaching Subject change after creation | 422 | Reject update. |
| Parent modification attempt | 403 | Deny action. |
| Cross-Teacher access | 403 | Deny without exposing private data. |
| Payment processing attempt | 422 | Reject as out of scope. |
| Hard delete attempt | 422 | Reject; require Archive. |
| Unsupported file format | 422 | Reject upload. |
| Notification request | 404 | Reject; notifications are out of scope. |

---

# 16. Logging Standards

Logging has two distinct responsibilities: operational logging and the business Audit Log.

## 16.1 Operational Logging

Operational logs support troubleshooting, runtime diagnostics, and hosting support.

### What to Log

- Application errors and exceptions with sufficient context for diagnosis.
- Authentication failures (without revealing whether the account exists).
- Authorization failures (without revealing resource existence).
- Background job start, completion, and failure.
- Scheduler task execution.
- Queue processing status.
- File upload validation failures.

### What Not to Log

- Passwords, tokens, API keys, or credentials.
- Full file content, Question Bank content, or Student personal data.
- Sensitive request or response payloads.
- Database credentials or application secrets.
- Teacher-private content beyond what is needed for troubleshooting.

### Log Levels

| Level | Usage |
|-------|-------|
| `emergency` | System is unusable. |
| `alert` | Action must be taken immediately. |
| `critical` | Critical conditions. |
| `error` | Error conditions that do not require immediate action. |
| `warning` | Warning conditions. |
| `notice` | Normal but significant conditions. |
| `info` | Informational messages. |
| `debug` | Debug-level messages (development only). |

Production logging must use `info` level or higher. `debug` level must not be used in production.

### Log Format

Operational logs must include:

- Timestamp.
- Log level.
- Context (feature, service, or request identifier).
- Message.
- Relevant non-sensitive metadata.

## 16.2 Audit Log

The Audit Log is mandatory, append-only, immutable, and permanently retained.

### Mandatory Events

The following events must be recorded without exception:

1. Create.
2. Update.
3. Archive.
4. Restore.
5. Login (success and failure).
6. Permission Change.
7. Attendance Change.
8. Exam Modification.
9. Homework Modification.
10. Subscription Change.

### Audit Log Entry Content

Each entry must include:

- Actor identity and role.
- Context (Teacher Workspace or Platform scope).
- Event type and affected entity reference.
- Before/after snapshot of changed fields.
- Timestamp (server time), IP address, and device/client information.

### Attribution Rules

- Teacher Staff actions are attributed to the Teacher Staff user, never to the Teacher.
- Super Admin actions are attributed to the Super Admin.
- Student and Parent actions are attributed to the authenticated account.

### Immutability Rules

- Audit Log entries cannot be edited or deleted.
- No actor, including Super Admin, can modify Audit Log entries.
- Audit Log entries are never archived or purged.

### Transactional Guarantee

The Audit Log entry must be written in the same database transaction as the action it describes. An action cannot succeed without its audit record.

## 16.3 Log Rotation

- Operational logs must be rotated to prevent disk space exhaustion on cPanel Shared Hosting.
- Audit Log entries are stored in the MySQL database and are subject to permanent retention rules.
- Log rotation must not delete or modify Audit Log entries.
- Old operational log files should be cleaned up periodically.

---

# 17. Validation Standards

Validation ensures data integrity, security, and business rule compliance across all input paths.

## 17.1 Validation Layers

| Layer | Responsibility |
|-------|---------------|
| Request validation (Form Requests) | Required fields, formats, data types, enum values, date ranges, file rules. |
| Authorization validation | Role, scope, ownership, permission, linked relationship, Teacher Workspace access. |
| Business validation | Confirmed rules: no duplicate Students, one Group per Student per Teacher, Teaching Subject immutability, Flow A/Flow B separation, Archive policy. |
| Persistence integrity | Prevent invalid saved state and preserve logical relationships. |

## 17.2 Backend Validation Rules

### Input Validation

| Concern | Standard |
|---------|----------|
| Required fields | Must be present and non-empty where applicable. |
| Data types | Must match expected types. |
| Enum values | Must be from the confirmed set (e.g., Pricing Type: Monthly or Per Lesson; Question Type: Multiple Choice, True/False, Essay, Bubble Sheet). |
| Date ranges | `from_date` must not be after `to_date`; dates must be valid. |
| String length | Must be bounded to prevent excessive storage. |
| Email format | Must be valid where email is required. |
| Numeric ranges | Must be within valid ranges. |

### File Validation

| Concern | Standard |
|---------|----------|
| File type | Must match owning resource context (Homework: Text/Image/PDF; Lesson: video). |
| File size | Must be within approved limits. |
| MIME type | Must be verified against file content. |
| Filename | Must be sanitized to prevent path traversal. |
| Parent uploads | Denied entirely. |
| Video homework | Denied entirely. |

### Business Rule Validation

| Rule | Validation |
|------|-----------|
| BR-001 | One global Student account; duplicate prevention. |
| BR-002 | One Group per Student per Teacher. |
| BR-016 | Teaching Subject immutable after creation. |
| BR-019 | Payment status only; no transaction processing. |
| BR-021 | Homework: Text, Image, PDF only. |
| BR-022 | Duplicate Student accounts rejected. |

## 17.3 Frontend Validation

Client-side validation uses Zod schemas integrated with React Hook Form.

Frontend validation must:

- Provide immediate feedback for structural validation (required fields, formats, ranges).
- Reflect confirmed business rules without hardening PENDING decisions.
- Never replace backend validation.
- Explain input correction needed without exposing private data.

Frontend validation must not encode assumptions about Teacher Staff permission granularity, Super Admin content visibility, pricing model, localization, non-payment enforcement, or other unresolved decisions.

## 17.4 Validation Error Responses

Validation failure responses must use HTTP 422 with:

- `success` as false.
- `error.code` as `VALIDATION_FAILED`.
- `error.message` as summary of failure.
- `errors` as field-level validation messages.

Error messages must not expose internal implementation details.

---

# 18. Documentation Standards

Documentation must be maintained alongside code to ensure the canonical document set remains accurate and complete.

## 18.1 In-Code Documentation

### PHPDoc Blocks

All public methods must have PHPDoc blocks describing:

- The method purpose.
- `@param` for each parameter with type and description.
- `@return` with return type and description.
- `@throws` for expected exceptions.
- `@see` for related documentation or rules where applicable.

### Inline Comments

Inline comments must explain *why*, not *what*. They must be used sparingly and only when the intent is not obvious from the code.

### TypeScript Documentation

TypeScript code must use JSDoc comments for exported functions, types, and interfaces. Props interfaces for components must document each prop's purpose.

## 18.2 Code-Level Business Rule References

Code that enforces a specific business rule must reference the rule identifier in a comment or PHPDoc `@see` tag:

- `@see BR-001` for Student account rules.
- `@see BR-002` for one Group per Student per Teacher.
- `@see BR-003` for Teacher Workspace isolation.
- `@see BR-005` for Archive policy.
- `@see BR-006` for Audit Log policy.
- `@see BR-008` for Billable Student calculation.
- `@see BR-016` for Teaching Subject immutability.
- `@see BR-019` for payment status only.

This traceability ensures that every critical business rule can be found in the codebase.

## 18.3 README Documentation

The root `README.md` must contain:

- Project name and description.
- Technology stack summary.
- Local development setup instructions (non-secret).
- Link to the canonical document set.
- Contribution guidelines reference.

## 18.4 API Documentation

API documentation is maintained in `AI_DOCS/10_API_Design.md`. Code comments must reference the API documentation for endpoint behavior rather than duplicating it.

## 18.5 Architecture Decision Records

Significant architectural decisions must be documented in `AI_DOCS/29_Project_Decisions.md`. Code must reference the decision ID where applicable.

## 18.6 Changelog

Material changes to the codebase must be reflected in version control commit messages following the Git Commit Message Convention (§14). A separate changelog file may be maintained if approved.

## 18.7 Documentation Update Obligation

When code changes alter behavior, the relevant AI_DOCS document must be updated to remain consistent. Documentation and code must not diverge.

---

# 19. Code Review Checklist

Every code review must verify the following categories. This checklist is mandatory for all pull requests.

## 19.1 Teacher Workspace Isolation

- [ ] All Teacher Workspace-owned queries are scoped to the correct workspace.
- [ ] No cross-Teacher data access is possible through the changed code.
- [ ] Search results do not reveal records from other Teacher Workspaces.
- [ ] Error messages do not expose data from other Teacher Workspaces.
- [ ] File access preserves Teacher Workspace ownership.
- [ ] Reports preserve tenant isolation.
- [ ] Cache entries are scoped to the correct workspace.

## 19.2 Business Rules

- [ ] No permanent deletion exists in the changed code. Archive is used instead.
- [ ] Historical data is preserved through all structural changes.
- [ ] Student transfer history is preserved.
- [ ] One Group per Student per Teacher is enforced.
- [ ] Duplicate Student accounts are prevented.
- [ ] Teaching Subject is immutable after account creation.
- [ ] One Parent per Student is enforced.
- [ ] Parent access is read-only everywhere.
- [ ] Flow A and Flow B are never conflated.
- [ ] Payment status is recorded only; no transaction processing.
- [ ] Homework supports Text, Image, and PDF only; video is rejected.

## 19.3 Authorization and Authentication

- [ ] All protected endpoints perform server-side authorization.
- [ ] Frontend-only checks are not treated as security enforcement.
- [ ] Teacher Staff permissions are limited to assigned permissions.
- [ ] Student access is limited to own account and own per-Teacher records.
- [ ] Parent access is limited to linked Students and read-only.
- [ ] Super Admin operates at Platform level only.
- [ ] No unconfirmed impersonation behavior is introduced.

## 19.4 Audit Log

- [ ] All mandatory Audit Log events are recorded.
- [ ] Audit Log entries are written in the same transaction as the action.
- [ ] Teacher Staff actions are attributed to the Teacher Staff user.
- [ ] Audit Log entries are not editable or deletable.

## 19.5 Error Handling

- [ ] Error responses follow the standardized structure.
- [ ] Error responses do not expose stack traces, SQL, credentials, or private data.
- [ ] Validation errors provide field-level messages.
- [ ] Generic messages are used for authentication and authorization failures.

## 19.6 Canonical Terminology

- [ ] "Educational Grade" is used instead of "Class."
- [ ] "Lesson" is used instead of "Course."
- [ ] "Archive" is used instead of "Delete."
- [ ] "Teacher Workspace" is used instead of "Tenant."
- [ ] "Subscription" refers to Flow A only.
- [ ] "Payment status" refers to Flow B only.

## 19.7 Version 1 Scope

- [ ] No native mobile, payment gateway, notification, marketplace, video homework, or multiple-subject code is introduced.
- [ ] No Docker, Redis, Kubernetes, S3 Storage, WebSocket, or Microservice dependency is added.
- [ ] No PENDING decision is silently hardened.

## 19.8 Code Quality

- [ ] Code follows PSR-12 (PHP) or project TypeScript standards.
- [ ] Functions and methods have single responsibility.
- [ ] Types are explicit and correct.
- [ ] Tests cover new functionality and edge cases.
- [ ] No unused code, dead code, or commented-out code is left behind.
- [ ] No hardcoded secrets, credentials, or environment-specific values.

## 19.9 Performance

- [ ] Database queries are scoped and indexed appropriately.
- [ ] N+1 query problems are avoided through eager loading.
- [ ] List endpoints use pagination.
- [ ] File uploads are validated early.
- [ ] Long-running work is deferred to background jobs.

## 19.10 Accessibility

- [ ] Interactive controls are keyboard operable.
- [ ] Inputs have programmatic labels.
- [ ] Validation errors are associated with their fields.
- [ ] Focus is managed after modal dialogs and validation failures.
- [ ] Status is not communicated solely through color.

---

# 20. Future Maintainability Guidelines

These guidelines ensure the codebase remains maintainable, extensible, and consistent as the project evolves.

## 20.1 Architectural Consistency

Every code change must remain consistent with the canonical document set. If a change reveals an inconsistency between code and documentation, the documentation must be updated or the code must be adjusted. Code and documentation must never diverge.

## 20.2 Refactoring Discipline

Refactoring must not change behavior. Refactoring commits must use the `refactor` type and must not mix behavior changes with structural changes. Every refactoring must pass all existing tests without modification.

## 20.3 Dependency Management

Dependencies must be kept up to date within their major versions. Major version upgrades must be planned, tested, and documented. Dependencies must not introduce unconfirmed infrastructure requirements (Redis, S3, Docker, etc.) for Version 1.

## 20.4 Feature Flagging

If future features require incremental rollout, feature flags must be implemented through configuration, not through commented-out code, dead code paths, or environment-variable-dependent logic that is not documented.

## 20.5 Technical Debt Tracking

Technical debt must be tracked through explicit TODO comments with associated tracking references, issue tracker entries, or approved documentation. TODO comments without tracking references must be resolved or removed.

## 20.6 Test Coverage

Every confirmed business rule must have automated test coverage. New features must include tests that verify both happy-path and error-path behavior. Authorization tests must verify that cross-Teacher access, Parent modification, and unauthorized actions are rejected.

## 20.7 Performance Monitoring

Performance-sensitive code paths (reports, billing calculations, background jobs, search) must be monitored for query count, execution time, and memory usage. Performance regressions must be investigated and resolved.

## 20.8 Security Review

Security-sensitive code (authentication, authorization, file access, payment status, Student account management) must be reviewed for potential vulnerabilities before merging. The security checklist from `AI_DOCS/23_Security_Standards.md` must be consulted for security-sensitive changes.

## 20.9 Future Version Preparation

Code must not prepare for future features by adding unused abstractions, empty interfaces, placeholder methods, or speculative configuration. Future features must be implemented only after formal approval. The modular monolith structure supports adding bounded feature folders when approved.

## 20.10 Canonical Document Set Maintenance

When significant coding patterns emerge that are not yet documented, the relevant AI_DOCS document must be updated. The canonical document set must evolve with the codebase while preserving the frozen Project Context.

## 20.11 Consistent Patterns

Similar problems must be solved with similar patterns across the codebase. If a new pattern is introduced, existing similar code should be updated to use the same pattern to maintain consistency.

## 20.12 Knowledge Transfer

Code must be written so that a new team member can understand its purpose, its boundaries, and its relationship to the canonical document set without requiring tribal knowledge. PHPDoc blocks, JSDoc comments, descriptive naming, and business rule references support this goal.

---

# 21. Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|-------------|--------|
| Project Context alignment | Passed — all standards follow the frozen Version 1 rules. BR references, role definitions, scope boundaries, and confirmed/pending statuses are consistent with `AI_DOCS/00_Project_Context.md`. |
| System Architecture alignment | Passed — technology baseline (Laravel 12, PHP 8.3, React 19, TypeScript, Vite, Tailwind CSS, MySQL 8, Sanctum, Custom RBAC, File Cache, Database Queue, Database sessions, Laravel Public Storage, Laravel Scheduler, Cron Jobs, SMTP, Apache/LiteSpeed, cPanel Shared Hosting) is consistent with `AI_DOCS/03_System_Architecture.md` §4.1. |
| Project Structure alignment | Passed — naming conventions and folder structure references are consistent with `AI_DOCS/04_Project_Structure.md` §11. |
| API Design alignment | Passed — API naming conventions and response structure references are consistent with `AI_DOCS/10_API_Design.md` §12. |
| Backend Architecture alignment | Passed — controller, service, repository, model, policy, middleware, and job standards are consistent with `AI_DOCS/11_Backend_Architecture.md`. |
| Frontend Architecture alignment | Passed — component organization, hooks, state management, routing, and API communication standards are consistent with `AI_DOCS/12_Frontend_Architecture.md`. |
| RBAC alignment | Passed — authorization standards reference the permission model from `AI_DOCS/08_RBAC.md` and `AI_DOCS/09_Permission_Matrix.md`. |
| Database Design alignment | Passed — table and column naming conventions are consistent with `AI_DOCS/06_Database_Design.md` §3. |
| Security Standards alignment | Passed — error handling, validation, logging, and code review standards are consistent with `AI_DOCS/23_Security_Standards.md`. |
| Testing Strategy alignment | Passed — test coverage and code review requirements reference `AI_DOCS/24_Testing_Strategy.md`. |
| Performance & Scalability alignment | Passed — performance guidelines in code review checklist are consistent with `AI_DOCS/25_Performance_Scalability.md`. |
| Deployment Plan alignment | Passed — environment and configuration standards are consistent with `AI_DOCS/26_Deployment_Plan.md`. |
| Development Roadmap alignment | Passed — phased development approach and documentation milestones are consistent with `AI_DOCS/27_Development_Roadmap.md`. |
| Teacher Workspace isolation | Passed — isolation is the highest-priority review checklist item. Every section preserves tenant boundaries. |
| Student account rules | Passed — one global Student account, duplicate prevention, per-Teacher partitioning are referenced throughout. |
| Parent access rules | Passed — linked-Student read-only access is consistently referenced. |
| Archive policy | Passed — no code standard references permanent deletion. Archive replaces deletion per BR-005. |
| Audit Log policy | Passed — logging standards define mandatory events, immutability, permanent retention, and attribution rules consistent with BR-006. |
| Payment handling | Passed — Flow A and Flow B separation is consistently enforced across naming, validation, and code review standards. |
| Version 1 scope | Passed — no native mobile, payment gateway, notification, marketplace, video homework, Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices are introduced. |
| PENDING items | Passed — non-payment enforcement (Q-005), lesson video hosting (Q-010), Teacher Staff permissions (Q-011), Super Admin visibility (Q-012), pricing model (Q-013), and localization (Q-015) are preserved as PENDING and not silently hardened. |
| Canonical terminology | Passed — Teacher Workspace, Educational Grade, Teaching Subject, Group, Student, Parent, Teacher Staff, Super Admin, Subscription, Flow A, Flow B, Enrollment, Archive, Audit Log, Dynamic QR Code, ID Card, Question Bank, Bubble Sheet, Student Switcher, Lesson, Billable Student, Billing Cycle, and Homework are used consistently. |
| No source code | Passed — no source code, implementation examples, API definitions, database tables, or UI implementation is defined. |

---

*End of document. **REVISION 1.0** — This file defines the complete coding standards for the Unified Education Platform Version 1. Docs before code; consistency over convenience; Archive — never delete.*
