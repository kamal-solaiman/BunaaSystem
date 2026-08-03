# 12 — Frontend Architecture

## Document Scope

This document defines the frontend architecture for Version 1 of the **Unified Education Platform**. It describes the browser application’s structure, responsibilities, boundaries, and integration approach. It is architecture only: it does not provide React code, UI implementation, CSS, or API implementation.

The frontend is a Web Application for all five role contexts: Super Admin, Teacher, Teacher Staff, Student, and Parent. It must remain consistent with the official source documents, especially `00_Project_Context.md`. If a conflict is found, `00_Project_Context.md` wins.

### Target Frontend Stack

| Concern | Version 1 choice |
|---|---|
| Application runtime | React 19 |
| Language | TypeScript |
| Build tool and development server | Vite |
| Styling system | Tailwind CSS |
| Client-side routing | React Router |
| Server-state management | TanStack Query |
| Form state | React Hook Form |
| Client-side schema validation | Zod |

The frontend communicates only with the Laravel backend through the documented REST API. Laravel Sanctum, backend authorization, Teacher Workspace scoping, business-rule enforcement, Audit Log recording, persistence, and file authorization remain backend responsibilities.

---

# 1. Frontend Overview

The frontend is a React 19 single-page Web Application delivered by Vite. It presents role- and context-appropriate Platform capabilities through the browser, including Web Application access for Dynamic QR Code Attendance scanning.

Its responsibilities are to:

- Establish and preserve the authenticated browser context supplied by Laravel Sanctum.
- Render the permitted application area for the active role context.
- Navigate among permitted routes and preserve safe contextual selections, such as a Parent’s selected linked Student or a Student’s selected Teacher relationship.
- Request, display, filter, paginate, and refresh backend data.
- Collect user input, apply immediate client-side validation, and submit valid requests to the backend.
- Represent loading, empty, archived, success, and error states without exposing private information.
- Provide browser-safe file selection and QR scanning integration.

The frontend is not a security boundary. It must never infer that hiding a control enforces authorization, attempt direct database or storage access, calculate billing, create Audit Log records, or replace backend validation. The backend remains authoritative for authentication, authorization, Teacher Workspace isolation, record ownership, Archive rules, and all business rules.

Version 1 is Web Application only. The frontend must not add native mobile behavior, marketplace discovery, cross-Teacher browsing, online payment processing, notifications, video homework, or support for multiple Teaching Subjects for a Teacher.

---

# 2. Folder Structure

The frontend should use a shallow application shell with feature-owned modules. The exact filenames may evolve, but ownership boundaries must remain clear.

| Area | Responsibility |
|---|---|
| `src/app/` | Application bootstrap, root providers, route composition, global error boundary, and application-wide configuration. |
| `src/routes/` | Route definitions, route guards, route-level lazy loading, and route access metadata. |
| `src/layouts/` | Role-aware layout shells and shared structural regions; layouts contain no domain workflow ownership. |
| `src/features/` | Feature modules organized by Platform capability and role context. Each feature owns its screens, local components, hooks, query definitions, form schemas, and feature-specific types. |
| `src/components/` | Reusable, domain-neutral UI primitives and composite shared components. They must not embed resource-specific authorization or workflow rules. |
| `src/lib/` | Shared technical utilities: HTTP client configuration, response normalization, query-key conventions, date/formatting utilities, and browser capability helpers. |
| `src/auth/` | Authenticated-user bootstrap, session lifecycle coordination, active role context, and authorization helpers. |
| `src/types/` | Shared TypeScript contracts that represent stable API-facing and application concepts. Feature-specific types remain in their feature. |
| `src/config/` | Typed environment configuration, route constants, supported client configuration, and non-secret feature settings. |
| `src/assets/` | Static application assets only. User-uploaded files and private Lesson content do not belong in the application bundle. |
| `src/test/` | Shared test setup and test utilities, if adopted. Feature tests remain near their feature. |

Vite environment variables must be limited to browser-safe configuration such as the public API base URL. No secret, credential, private storage path, authorization decision, or server-only configuration may be included in the frontend build.

---

# 3. Feature-Based Organization

Features are organized around canonical Platform capabilities rather than technical layers alone. A feature may expose route modules, domain components, typed API adapters, TanStack Query hooks, React Hook Form definitions, Zod schemas, and local helpers, while keeping them together.

| Feature area | Architectural responsibility |
|---|---|
| Authentication | Login, logout, authenticated-user bootstrap, session-expiry recovery, and Student account activation flow. |
| Platform Administration | Super Admin dashboard, Teachers, Flow A Subscriptions, pricing, Platform Settings, global reports, and permitted Platform Audit Log views. |
| Teacher Workspace | Teacher and authorized Teacher Staff dashboard, Educational Grades, Groups, Students, Enrollments, Teacher Workspace Settings, and Teacher Staff management. |
| Attendance | Dynamic QR Code display and scan flows, ID Card scanner input handling, manual Attendance, and historical Attendance views. |
| Homework | Workspace Homework management, Student assigned Homework and submission, and Parent read-only Homework monitoring. |
| Lessons | Teacher-owned private Lessons and authorized Student access; no cross-Teacher discovery. |
| Exams | Teacher-owned Question Bank and Exams, Student exam participation, and role-scoped results. |
| Reports | Teacher Workspace, Student, Parent linked-Student, and Platform report views according to returned scope. |
| Payments | Flow B payment-status views and permitted Teacher Workspace updates. It must never be named or combined as Flow A Subscription functionality. |
| Subscriptions | Flow A Subscription views for Teachers and Super Admin only. No transaction processing is introduced. |
| Files | Authorized file upload, reference, access, Archive, and restore workflows. |
| Parent Monitoring | Parent dashboard, Student Switcher, and linked-Student read-only views. |
| Settings | Role-appropriate account, Teacher Workspace, and Platform settings. |

A feature must not import another feature’s internal implementation. Cross-feature reuse belongs in a deliberately exported contract, a shared domain-neutral component, or a common technical utility. This prevents a large role-specific application from becoming a single coupled module.

---

# 4. Routing Strategy

React Router is the authoritative client-side navigation mechanism. Routes are grouped by access boundary, not merely by visual similarity:

- Public authentication routes.
- Authenticated role-context routes for Super Admin, Teacher / Teacher Staff, Student, and Parent.
- Explicit contextual routes where a selected Student, Teacher relationship, or record identifier is required.
- A not-found route and a safe access-denied route.

Route metadata must describe the expected role context and, where appropriate, the required logical permission. A route guard uses the authenticated user and permission information returned by the backend to decide whether navigation should proceed. A guard is a usability measure only; every data request remains subject to backend authorization.

Route design must preserve canonical boundaries:

- Super Admin routes are Platform scoped. Teacher-private content visibility remains PENDING and must not be assumed.
- Teacher and Teacher Staff routes represent only the current Teacher Workspace. Teacher Staff route availability depends on Teacher-assigned permissions.
- Student routes operate on the authenticated Student’s own global account while presenting records partitioned per Teacher.
- Parent routes require a selected linked Student where student-specific data is displayed; they are read-only for educational and payment-status data.

Selected Parent Student and selected Student Teacher relationship may be reflected in validated route parameters or a scoped URL query parameter when it supports refresh, deep linking, and browser navigation. The frontend must revalidate these contexts from backend responses and must clear them when the authenticated context changes. It must not treat a route parameter as proof of access.

Routes should be lazily loaded at feature and layout boundaries. Navigation changes should clear or invalidate context-sensitive data when changing user, role, Teacher Workspace, linked Student, or Teacher relationship.

---

# 5. Layout Architecture

Layouts provide stable structural shells around routed feature content. They centralize navigation regions, authenticated context display, route-level error placement, and responsive structural behavior without owning feature data or business rules.

The architecture uses these layout categories:

| Layout category | Scope |
|---|---|
| Public layout | Authentication and account-activation journeys without authenticated application navigation. |
| Platform layout | Super Admin Platform-level administration only. |
| Teacher Workspace layout | Teacher and Teacher Staff application area for one resolved Teacher Workspace. Navigation is permission-aware. |
| Student layout | Student self-service application area with Teacher-partitioned context. |
| Parent layout | Parent monitoring application area, including the linked Student context. All linked-Student educational views are read-only. |
| Minimal task layout | Focused tasks such as QR scanning or exam participation where a reduced shell improves task completion without changing authorization. |

Layouts consume normalized auth and authorization context; they do not duplicate backend scope resolution. Navigation items are derived from allowed capability metadata so unavailable actions are not presented. A later 403 response is handled safely because permissions can change after initial rendering.

---

# 6. Authentication Flow

Laravel Sanctum is the Version 1 authentication baseline. The frontend coordinates a browser authentication lifecycle; it does not store or manufacture server credentials.

1. The user submits credentials through the authentication feature.
2. The frontend sends the request using the Sanctum-compatible deployment configuration.
3. On success, the frontend obtains or refreshes the authenticated-user context from the backend, including available role contexts and safe authorization metadata.
4. The frontend initializes protected routes only after this bootstrap succeeds.
5. Subsequent requests use the authenticated browser context according to the confirmed Sanctum session or token deployment model.
6. On logout, session expiry, authentication failure, or an invalidated context, the frontend clears in-memory and persisted non-sensitive context, removes protected cached data, and returns to a public route.

The Student activation journey must support a Student account created manually by a Teacher becoming usable by that same Student. It must not create a second Student account. The frontend must never imply support for unconfirmed impersonation, including “Login as Teacher.”

No authentication secret is persisted in application state, query cache, route parameters, logs, analytics payloads, or browser storage. The final Sanctum transport details follow backend deployment configuration; the frontend must keep the transport client isolated so that configuration does not leak into feature modules.

---

# 7. Authorization Handling

Authorization handling has two layers:

1. **Frontend capability handling:** use authenticated role, current context, and safe permission metadata to determine routes, navigation, actions, and read-only presentation.
2. **Backend enforcement:** Laravel Gates, Policies, Custom RBAC, ownership checks, linked-Student checks, Teacher Workspace scoping, and business rules make the final allow/deny decision.

The frontend defaults to deny: a capability is not rendered as available unless it is explicitly known to be available in the current context. It must react safely to authorization failures by removing stale protected data, displaying a generic access-denied state, and avoiding resource existence disclosure.

| Role context | Frontend boundary |
|---|---|
| Super Admin | Platform administration, Flow A Subscriptions, pricing, Platform Settings, and global reports. Teacher-private content visibility is PENDING. |
| Teacher | One Teacher Workspace only. |
| Teacher Staff | The creating Teacher Workspace only and only explicitly Teacher-assigned permissions. Permission granularity remains PENDING. |
| Student | The Student’s own account and own per-Teacher records only. |
| Parent | Linked Students only; read-only everywhere for linked-Student educational data and payment status. |

The frontend must preserve the distinction between **Flow A** (Teacher-to-Platform Subscription) and **Flow B** (Student/Parent-to-Teacher fees represented by payment status). Labels, routes, query keys, cache entries, and feature boundaries must not conflate them.

---

# 8. State Management Strategy

State is classified by ownership and lifetime rather than placed in a single global store.

| State class | Owner and approach |
|---|---|
| Server state | TanStack Query owns data retrieved from the backend: dashboards, lists, record details, reports, permissions, settings, files, and status. Queries use scope-aware keys and stale-time policies appropriate to the resource. |
| Authentication and access context | A small application-level auth boundary owns the normalized current user, active role context, and capability metadata. It is initialized from the backend and cleared on logout or invalid authentication. |
| Route state | React Router owns URL, path parameters, and validated query parameters, including filter, sorting, pagination, Parent selected Student, and Student Teacher relationship when applicable. |
| Form state | React Hook Form owns transient editable field values, field state, and submission state. |
| Local presentation state | Component-local state owns ephemeral concerns such as an open dialog or scanner activation. It must not duplicate server records. |

TanStack Query keys must include every access-defining context necessary to avoid data reuse across boundaries: active role context, Teacher Workspace where applicable, Parent linked Student where applicable, Student Teacher relationship where applicable, resource identity, and normalized list criteria. Cached data must be cleared or invalidated when any of those contexts changes.

Mutations invalidate or update only the affected scoped queries after a successful backend response. Optimistic updates are appropriate only for low-risk, reversible interactions with a clear rollback path. They must not pre-assert authorization, Archive success, Attendance success, exam result, payment status, or other business outcome before the backend confirms it.

---

# 9. API Communication

A shared HTTP boundary is the only frontend path to the Laravel REST API. Feature modules use typed, resource-focused adapters rather than issuing ad hoc requests from layout or presentation components.

The boundary is responsible for:

- Applying the Vite-provided public API base configuration and Sanctum-compatible request behavior.
- Serializing JSON, multipart form data, pagination, filter, and sorting inputs according to `10_API_Design.md`.
- Parsing documented success envelopes, pagination metadata, validation errors, and normalized error responses.
- Mapping HTTP outcomes into a small stable frontend error taxonomy.
- Ensuring requests are cancellable when routes or query inputs change.
- Keeping API contracts, response normalization, and transport concerns separate from React presentation.

The frontend follows the documented REST versioning and resource names. It does not create unconfirmed endpoints, directly access Laravel Public Storage, or treat client types as an alternative to backend validation. Paginated list views preserve backend pagination metadata; filtering and sorting use only documented, permitted parameters.

---

# 10. Form Management

React Hook Form is the standard for interactive forms because it keeps transient form state local and avoids unnecessary React render work. Each feature form owns:

- Default values derived from authorized backend data or explicit safe defaults.
- Its Zod validation schema.
- Field-level interaction state and accessible error association.
- Submission lifecycle and prevention of duplicate submission while a request is pending.
- Success handling that updates only the relevant scoped queries.

Forms are organized by user task, not by backend table or a global form registry. Multi-step tasks retain only the minimum local state needed and validate each step before progression. On a failed submission, valid entered values remain available where safe; server field errors are mapped to their corresponding fields and non-field errors are presented at the form level.

Client-side forms may guide, but never guarantee, a business rule. For example, a client can detect a missing required value, while the backend remains responsible for duplicate Student prevention, one Group per Teacher, Teaching Subject immutability, Archive eligibility, permission checks, and scope validation.

---

# 11. Validation Strategy

Zod provides a single client-side schema source for form input parsing, type inference, and React Hook Form integration. Validation is layered:

1. **Client structural validation:** required fields, input shape, allowed local formats, ranges, and file selection constraints.
2. **Server validation:** authoritative field validation, resource existence, authorization, scope, duplicate prevention, archive state, and business-rule validation.
3. **Response reconciliation:** documented 422 validation responses are mapped back to form fields; conflict and authorization outcomes receive task-level handling.

Client schemas must reflect confirmed rules without hardening PENDING decisions. They must not encode an assumption about Teacher Staff permission granularity, Super Admin Teacher-private content visibility, pricing model, localization, non-payment enforcement, or other unresolved decisions.

Validation messages must explain the input correction needed without exposing private data, internal rules, or unlinked Student information.

---

# 12. Error Handling

Errors are normalized at the shared HTTP boundary and presented by the nearest appropriate scope: field, form, feature, route, or application boundary.

| Error class | Frontend response |
|---|---|
| 401 unauthenticated | Clear protected context and cache, preserve only safe intended navigation if appropriate, then direct to authentication. |
| 403 unauthorized | Present a generic access-denied state, do not reveal whether a protected resource exists, and remove stale capability assumptions. |
| 404 unavailable | Use a neutral unavailable/not-found state; do not distinguish inaccessible private records from absent records. |
| 409 conflict | Preserve form data where safe and explain that current state prevents completion; refresh affected server state. |
| 422 validation | Attach server field messages to the active form and show non-field messages safely. |
| 429 or transient network/server failure | Present a retryable task-level failure with no duplicate mutation attempt. |
| Unexpected client failure | Contain it with route- and application-level error boundaries, report only non-sensitive diagnostic context through an approved operational channel, and offer recovery. |

Error boundaries must not display request headers, credentials, raw backend payloads, stack traces, Teacher Workspace identifiers, file paths, or private record data. Errors do not replace backend Audit Log policy; required audit events remain backend-owned.

---

# 13. Loading Strategy

Loading behavior is deliberate and scoped to the work in progress:

- A bootstrap state is shown while authentication and active context are being resolved.
- Route-level loading is used for lazily loaded feature modules and initial route data.
- Component-level loading is used for independent dashboard panels, lists, and detail regions so unrelated content can remain usable.
- Form and mutation controls indicate pending work and prevent accidental duplicate submissions.
- List views distinguish initial load, background refresh, empty results, filtered empty results, and error states.
- Pagination and filter changes retain the prior valid view where practical while the next scoped query resolves, without presenting stale data as if it belongs to a different context.

Loading states must be accessible, announced appropriately, and must never imply that an action succeeded before backend confirmation. They must not use notification features, which are out of scope for Version 1.

---

# 14. File Upload Handling

File workflows use browser-selected files and multipart form data only through the documented backend endpoints. The frontend never writes directly to Laravel Public Storage and never generates a public storage path as authorization proof.

Before upload, the frontend performs safe early checks for a selected file, local size limits when supplied by confirmed configuration, and resource-specific allowed types. The backend remains authoritative for file type, ownership, resource relationship, access permission, Archive state, and storage acceptance.

Version 1 constraints must be represented clearly:

- Student Homework submission files are limited to **Image** and **PDF**.
- Video homework is rejected; the frontend must not present it as an option.
- Lesson videos are private Teacher-owned content and are available only through authorized backend-controlled access.
- Parent uploads are denied.
- File access, Archive, and restore must preserve Teacher Workspace isolation and historical references.

Upload state is local to the task, with progress where transport support permits, cancellation before completion where safe, retry only after a clear failure, and post-success invalidation of relevant scoped file and owning-resource queries. Selected file metadata and object URLs are released when no longer needed.

---

# 15. QR Scanner Integration

QR Attendance is a browser capability within the Attendance feature, not a separate mobile application. The QR integration is isolated behind a scanner adapter so camera and scanner-library details do not spread through attendance screens.

The adapter must:

- Check browser camera capability and request permission only after an intentional user action.
- Provide a non-camera path for ID Card scanner devices that act as keyboard input, when required by the attendance task.
- Normalize a scanned value and send it to the backend for authentication, Teacher Workspace context, Student relationship validation, and Attendance recording.
- Stop camera streams and release browser resources on success, cancellation, route change, unmount, or error.
- Provide an accessible fallback and a clear unsupported/permission-denied state; no unconfirmed manual substitute is added beyond the confirmed Teacher or authorized Teacher Staff manual Attendance method.

The frontend must not decode a QR value as proof of Attendance eligibility, infer a Teacher Workspace, or mark Attendance locally. The backend verifies the Student relationship and records the Attendance and required Audit Log event. Attendance must never be used by the frontend for Billable Student calculation; billable status is based on Enrollment duration only.

---

# 16. UI Component Organization

The component hierarchy is organized by reuse and domain ownership:

| Component level | Purpose |
|---|---|
| Shared primitives | Accessible, domain-neutral controls and status patterns used throughout the application. |
| Shared composites | Reusable patterns such as paginated data regions, filter controls, confirmation flows, file selector, error state, empty state, and context selector. |
| Layout components | Structural role-aware application shells and navigation regions. |
| Feature components | Domain-specific compositions owned by a feature, such as Attendance scanning, Homework submission, or Parent Student Switcher. |
| Route modules | Compose a layout and feature entry point for a route; they should not become data-access catch-alls. |

Shared components accept explicit state and callbacks rather than reaching into feature queries or authorization internals. Feature components may consume their own query and form hooks. This protects reuse, prevents accidental cross-context data use, and keeps canonical domain terminology close to the relevant feature.

This organization describes component responsibility only. It does not prescribe a visual design or UI implementation.

---

# 17. Theme Strategy

Tailwind CSS is the Version 1 styling system. Theme decisions are expressed as a small semantic design-token layer consumed consistently through Tailwind configuration and shared components, rather than scattered arbitrary visual values.

The theme strategy must support:

- Semantic color, spacing, typography, border, elevation, focus, and status tokens.
- Consistent states for normal, hover, focus, disabled, validation error, loading, Archive, and read-only contexts.
- System-aware color-scheme support only if approved by product design; any persisted preference is non-sensitive local presentation state.
- Sufficient contrast and visible focus indicators under every supported theme.

Theme choice must not alter authorization, data visibility, or role meaning. Theme architecture does not add a new product feature, and this document defines no CSS or UI implementation.

---

# 18. Performance Optimization

The frontend is optimized for React 19 and Vite while preserving correctness and access boundaries.

- Use Vite’s production build pipeline and route/feature-level lazy loading to keep initial JavaScript focused on authentication and the entered role area.
- Use React 19’s rendering model with stable component boundaries; avoid premature memoization and measure before adding it.
- Keep server data in TanStack Query rather than duplicating large lists in global state.
- Use pagination, documented filters, and documented sorting for list resources; do not load unbounded lists into the browser.
- Configure scoped query stale times and invalidation deliberately; never reuse a cache result across user, role, Teacher Workspace, linked Student, or Teacher relationship boundaries.
- Defer noncritical modules such as scanner integration until their feature route is entered.
- Validate and preview selected files efficiently, release local browser resources, and avoid bundling user media.
- Use cancellable requests and avoid state updates after navigation or unmount.
- Keep dependencies minimal and avoid requiring Redis, WebSockets, Docker, S3 Storage, Kubernetes, or microservices for frontend operation.

Performance optimization must never bypass authorization, expose cached protected data, relax Archive behavior, or present an unconfirmed action as complete.

---

# 19. Accessibility Guidelines

The Web Application must be usable with keyboard navigation, assistive technology, zoom, and responsive browser layouts. At minimum, frontend implementation should follow these architectural guidelines:

1. Use semantic document structure and native controls wherever possible.
2. Ensure all interactive controls are keyboard operable with a visible, high-contrast focus indicator.
3. Give inputs programmatic labels and connect validation errors and instructions to the relevant field.
4. Announce route changes, loading status, mutation results, and non-field errors without creating disruptive repeated announcements.
5. Manage focus deliberately after modal dialogs, validation failures, route-level errors, and successful task completion.
6. Do not communicate status solely through color; Archive, read-only, error, and permission states need textual or programmatic meaning.
7. Ensure sufficient contrast, scalable text, responsive reflow, and usable target sizes.
8. Provide accessible alternatives when camera scanning is unavailable or permission is denied, within the confirmed Attendance methods.
9. Keep Parent read-only and disabled controls understandable; use explanatory text where a control is unavailable rather than relying only on a disabled visual state.
10. Test role-specific routes, forms, tables, files, and scanner paths with keyboard and screen-reader workflows.

Accessibility support must not expose actions or records that the role is not permitted to access.

---

# 20. Coding Principles

Frontend work must follow these principles:

1. Preserve canonical terminology: Teacher Workspace, Educational Grade, Lesson, Attendance, Archive, Audit Log, Flow A, Flow B, Subscription, and payment status.
2. Treat `00_Project_Context.md` as the final source of truth and do not convert PENDING decisions into frontend assumptions.
3. Keep the frontend presentation-focused; backend enforcement is mandatory for every protected operation.
4. Deny by default in route and capability presentation.
5. Preserve strict Teacher Workspace isolation in route state, query keys, cache invalidation, and displayed context.
6. Preserve one global Student account and per-Teacher partitioning; never create a duplicate Student account flow.
7. Preserve Parent linked-Student scope and read-only access everywhere for linked-Student data.
8. Preserve Teacher Staff’s explicit Teacher-assigned permission boundary.
9. Keep Flow A Subscription and Flow B payment status separate in feature names, routes, types, cache keys, labels, and reports.
10. Present Archive as the product behavior; do not introduce hard-delete interactions.
11. Do not add payment gateways, notifications, marketplace behavior, cross-Teacher content browsing, native mobile requirements, video homework, or unconfirmed impersonation.
12. Prefer feature-local ownership, typed contracts, accessible reusable components, and small explicit boundaries over a monolithic client store.
13. Never log credentials, private response bodies, private file URLs, or sensitive context to the browser console or client telemetry.
14. Test boundary conditions: session expiry, 403 responses, changed permission assignments, context switching, stale cache prevention, Parent read-only attempts, and cross-Teacher access attempts.

---

# 21. Future Improvements

The following are future considerations only. They do not change Version 1 scope or authorize implementation without a separate approved decision.

| Future area | Consideration |
|---|---|
| Generated API contracts | Generate or validate TypeScript contracts from a confirmed API specification to reduce contract drift. |
| Expanded test automation | Add comprehensive unit, integration, accessibility, and end-to-end coverage for role and context boundaries. |
| Localization | Add language, timezone, currency, and regional formatting only after Product Owner decisions. |
| Offline and installable experiences | Evaluate progressive Web Application capabilities only after explicit scope approval; Version 1 remains a Web Application. |
| Native applications | Consider native mobile clients only as a future phase, with separate authentication and scanner decisions. |
| Advanced file delivery | Revisit private file delivery and object storage only when infrastructure scope changes; Version 1 remains Laravel Public Storage with backend access control. |
| Real-time behavior | Evaluate real-time updates only after an approved infrastructure and product decision; WebSockets are not a Version 1 requirement. |
| Notifications | Consider push, email, or SMS only in separately approved future scope. |
| Payment gateways | Consider online payment processing only in separately approved future scope. |
| RBAC refinement | Incorporate confirmed Teacher Staff permission granularity and Super Admin content-visibility decisions when available. |

Any future improvement must preserve Teacher Workspace isolation, Student and Parent account rules, Parent read-only access, private Teacher-owned Lessons and Question Banks, Flow A / Flow B separation, Archive instead of permanent deletion, and permanent immutable Audit Log retention.

---

# 22. Consistency Review

A consistency review was performed before saving this document.

| Review area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority. |
| Target stack | Passed — React 19, TypeScript, Vite, Tailwind CSS, React Router, TanStack Query, React Hook Form, and Zod are the frontend baseline. |
| Scope | Passed — frontend architecture only; no React code, UI implementation, CSS, API implementation, database design, or backend implementation is defined. |
| Web Application boundary | Passed — all Version 1 capabilities, including Dynamic QR Code scanning, are browser-based; native mobile is not introduced. |
| Authentication | Passed — Laravel Sanctum remains authoritative and no client-side credential persistence or unconfirmed impersonation is introduced. |
| Authorization | Passed — frontend capability handling is explicitly non-authoritative; backend RBAC, policies, ownership, and scope checks remain required. |
| Teacher Workspace isolation | Passed — routing, layout, query keys, caching, files, and features preserve workspace context and reject cross-Teacher assumptions. |
| Student and Parent rules | Passed — one global Student account, per-Teacher partitions, linked-Student Parent scope, and Parent read-only access are preserved. |
| Teacher Staff and Super Admin constraints | Passed — explicit Teacher-assigned permissions and PENDING Super Admin private-content visibility are not expanded. |
| Flow separation | Passed — Flow A Subscription and Flow B payment status are kept separate. |
| Archive and Audit Log | Passed — Archive replaces deletion; Audit Log remains backend-owned, immutable, and permanent. |
| Files and QR Attendance | Passed — backend-controlled private file access, Image/PDF Student Homework submissions, no video homework, and backend-validated QR Attendance are preserved. |
| Excluded Version 1 features | Passed — payment gateways, notifications, marketplace behavior, cross-Teacher browsing, and multiple Teaching Subjects are not introduced. |
| Terminology | Passed — canonical terms are used consistently. |
