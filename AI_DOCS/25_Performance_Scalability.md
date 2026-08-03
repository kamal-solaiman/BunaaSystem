# 25 — Performance & Scalability

## Document Scope

This document defines the complete Performance & Scalability strategy for Version 1 of the Unified Education Platform. It establishes performance objectives, scalability goals, and optimization guidelines across backend, frontend, database, API, caching, file storage, queue, search, and pagination layers.

This document does not define source code, APIs, SQL queries, database tables, UI implementation, or physical configuration. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found.

The Performance & Scalability strategy is built on the confirmed Version 1 technology baseline: **Laravel 12** backend with **PHP 8.3**, **React 19** frontend with **TypeScript** and **Vite**, **MySQL 8** for persistence, **File Cache**, **Database Queue**, **Database session driver**, **Laravel Public Storage**, and **cPanel Shared Hosting** as the primary deployment target, with **VPS / Cloud** as the future deployment target.

---

# 1. Performance Overview

Performance is a cross-cutting concern that affects every layer of the Unified Education Platform: backend request handling, frontend rendering, database queries, API response times, file storage access, background job processing, search execution, and pagination delivery.

The Platform operates within the confirmed cPanel Shared Hosting baseline. Performance optimization must respect shared hosting resource limits — including process execution time, memory usage, CPU allocation, and concurrent connection constraints — while preserving all confirmed business rules.

Performance work must never:

- Bypass Teacher Workspace isolation (BR-003).
- Weaken authentication or authorization enforcement.
- Treat archived records as active to simplify queries.
- Expose unauthorized data to improve response times.
- Introduce infrastructure that is not confirmed for Version 1 (Redis, Elasticsearch, S3 Storage, Docker, Kubernetes, WebSockets, Microservices).
- Relax Archive policy (BR-005), Audit Log requirements (BR-006), or historical data retention (BR-014).
- Conflate Flow A and Flow B for query convenience.

No confirmed numeric response-time, throughput, concurrency, or capacity targets exist in the Project Context. Therefore, this document defines qualitative performance principles, optimization strategies, and infrastructure-appropriate guidelines without inventing unconfirmed metrics.

---

# 2. Performance Objectives

The confirmed performance objectives are to:

1. **Support all confirmed roles efficiently.** Super Admin, Teacher, Teacher Staff, Student, and Parent operations must complete within acceptable timeframes for interactive web use on the confirmed cPanel Shared Hosting baseline.

2. **Preserve Teacher Workspace isolation under load.** Multi-tenant query scoping must not degrade into full-table scans or cross-tenant data exposure as the number of Teacher Workspaces grows.

3. **Keep list and report endpoints paginated.** All list endpoints and report views must use pagination to prevent unbounded result sets from exhausting shared hosting memory or transfer limits.

4. **Optimize for MySQL 8 on shared hosting.** Queries must use appropriate indexing strategies, scope-first filtering, and MySQL 8-compatible access patterns without requiring external search infrastructure.

5. **Support efficient background job processing.** Database Queue jobs must complete within shared hosting execution time limits. Long-running tasks must be chunked or batched.

6. **Minimize frontend bundle size.** Route-level lazy loading, code splitting, and Vite's production build pipeline must keep the initial JavaScript payload focused on authentication and the entered role area.

7. **Cache where safe and appropriate.** File Cache must be used for frequently accessed, slowly changing reference data within authorized scope boundaries.

8. **Prepare for future VPS / Cloud migration.** Architectural decisions must not prevent future migration to VPS or Cloud infrastructure. However, V1 must not require VPS or Cloud for normal operation.

9. **Never sacrifice correctness for speed.** Performance optimization must never bypass authorization, scope resolution, Archive policy, Audit Log obligations, or historical data retention.

10. **Support growth across multiple dimensions.** The Platform must handle growth in Teacher Workspaces, Students, Groups, Attendance records, Homework submissions, Exam attempts, Lesson references, payment-status records, and Audit Log entries without architectural redesign.

---

# 3. Scalability Goals

## 3.1 Multi-Tenant Scalability

The Platform must scale across an increasing number of isolated Teacher Workspaces without:

- Allowing cross-Teacher data access (BR-003).
- Degrading query performance through unscoped full-table scans.
- Requiring a different database engine or external infrastructure.
- Creating duplicate Student accounts (BR-001, BR-022).

Each Teacher Workspace operates as an independent tenant. Query performance must remain proportional to the size of the individual workspace, not the total Platform data volume.

## 3.2 Student Account Scalability

A Student has one global account and may study with multiple Teachers (BR-001). The architecture must support:

- A Student enrolled with many Teachers simultaneously, with per-Teacher data partitioning maintained efficiently.
- Growth in the total number of Student accounts without degrading login, dashboard, or per-Teacher record retrieval performance.
- Historical data accumulation from Student Group movements (BR-007) without degrading report or history query performance.

## 3.3 Parent Account Scalability

A Parent may monitor multiple linked Students (BR-020). The architecture must support:

- A Parent linked to many Students without degrading the Student Switcher or linked-Student data retrieval.
- Per-Teacher partitioning within each linked Student's context.
- Read-only access performance that does not degrade as linked Student data grows.

## 3.4 Academic Data Scalability

The Platform must scale across:

- Multiple Educational Grades and Groups per Teacher Workspace.
- Growing Attendance records (three methods: Dynamic QR Code, ID Card, manual).
- Growing Homework assignments, submissions, and grading records.
- Growing Exam definitions, Question Bank entries, attempts, and grades (including Bubble Sheet automatic grading).
- Growing Lesson metadata and file references.

## 3.5 Financial Data Scalability

Flow A and Flow B data must remain separate and performant:

- Flow A Subscription records must scale across Billing Cycles and Teachers without mixing with Flow B data.
- Flow B payment-status records must scale across Students, Groups, and Teachers without mixing with Flow A data.
- Historical billing and payment-status records must remain accessible without performance degradation.

## 3.6 Audit Log Scalability

The Audit Log is append-only, immutable, and permanently retained (BR-006). The architecture must support:

- Continuous growth in Audit Log entries without degrading write performance.
- Efficient Audit Log queries scoped by actor, event type, context, and time range.
- Permanent retention without requiring periodic purging or compaction.

## 3.7 File Storage Scalability

File storage must scale across:

- Growing Lesson video references per Teacher Workspace.
- Growing Homework file references (Image, PDF) per Teacher Workspace.
- Growing Student Homework submission files.
- Historical file references that remain valid through Archive and restore operations.

File storage scalability must preserve private Teacher ownership and access control. Lesson video storage details such as hosting, protection, and quota remain PENDING (Q-010) and must be resolved separately.

## 3.8 Report Scalability

Reports must preserve historical availability and Archive indications while maintaining scope boundaries:

- Teacher reports remain workspace-scoped.
- Student views remain self-scoped and partitioned per Teacher.
- Parent views remain linked-Student scoped and read-only.
- Super Admin reports remain Platform-scoped and subject to pending content-visibility boundaries (Q-012).
- Historical reporting must not degrade as archived data accumulates.

## 3.9 Scalability Constraints

Scaling must not introduce:

- Cross-Teacher data access.
- Duplicate Student accounts.
- Multiple Parent accounts for one Student in Version 1.
- Marketplace behavior.
- Payment processing.
- Redis, Elasticsearch, S3 Storage, Docker, Kubernetes, WebSockets, or Microservices as V1 requirements.

---

# 4. Backend Performance

## 4.1 Request Lifecycle Efficiency

The Laravel 12 backend processes every request through authentication, role resolution, scope resolution, middleware, validation, authorization, business logic, persistence, and response serialization. Performance optimization must focus on reducing unnecessary work at each stage without skipping required security or business-rule steps.

| Lifecycle stage | Performance guideline |
|---|---|
| Authentication | Sanctum session/token resolution should be lightweight. Avoid redundant authentication checks within a single request. |
| Role and scope resolution | Resolve role context and Teacher Workspace scope once per request and reuse throughout the lifecycle. |
| Middleware | Keep middleware lightweight. Avoid expensive operations in global middleware; defer complex checks to feature-specific authorization. |
| Validation | Validate early to reject invalid requests before expensive business logic or database queries execute. |
| Authorization | Cache authorization results within a request when the same permission is checked multiple times. |
| Business logic | Avoid redundant database queries within a single service operation. Use eager loading for known relationship access patterns. |
| Persistence | Use transactions only where required for data consistency. Avoid long-running transactions that hold database locks. |
| Response serialization | Return only the data the frontend needs. Avoid over-fetching from the database and serializing unused fields. |

## 4.2 Service Layer Performance

Services own business workflows and should:

- Minimize database round trips by batching related operations.
- Use database transactions where multiple records must change atomically, but keep transactions short.
- Avoid loading entire collections into memory when iterating over large datasets; use chunked processing or cursor-based iteration.
- Delegate long-running work to the Database Queue rather than blocking the request.

## 4.3 Repository Performance

Repositories encapsulate complex query logic and should:

- Always scope queries to the resolved Teacher Workspace, Student, Parent linked-Student, or Platform context before applying additional filters.
- Use eager loading to prevent N+1 query problems for known relationship patterns.
- Apply active-versus-archived filtering consistently at the query level, not in application code after retrieval.
- Support MySQL 8-compatible access patterns without requiring Redis or external search infrastructure.

## 4.4 Middleware Performance

Middleware applies cross-cutting checks and should:

- Avoid database queries in middleware where the same check is performed again by policies or services.
- Use lightweight token and session validation.
- Defer expensive authorization evaluations to the policy/gate layer where context is fully resolved.

## 4.5 Background Job Performance

Background jobs use the Database Queue and must:

- Respect cPanel Shared Hosting execution time limits.
- Chunk long-running tasks into smaller batches.
- Process Teacher Workspaces sequentially or in small batches rather than loading all records into memory.
- Use pagination and scoped queries for report preparation.
- Clean up processed jobs from the queue table periodically.

---

# 5. Frontend Performance

## 5.1 Bundle Optimization

The React 19 frontend built with Vite must minimize the initial JavaScript payload:

- Use route-level lazy loading to load feature modules only when the user navigates to them.
- Use code splitting at feature boundaries so that Teacher, Student, Parent, and Super Admin modules are loaded independently.
- Defer non-critical modules (e.g., QR scanner integration) until their feature route is entered.
- Use Vite's production build pipeline for tree-shaking, minification, and asset fingerprinting.

## 5.2 Rendering Performance

React 19's rendering model should be used efficiently:

- Use stable component boundaries to avoid unnecessary re-renders.
- Avoid premature memoization; measure before adding `React.memo` or `useMemo`.
- Keep server data in TanStack Query rather than duplicating large lists in component state.
- Use virtualized lists for large data sets where pagination alone is insufficient for the visible viewport.

## 5.3 Network Performance

Frontend-to-backend communication should minimize unnecessary requests:

- Use TanStack Query's stale-time and cache-time configuration to avoid redundant refetches.
- Use cancellable requests to abort in-flight API calls when routes or query inputs change.
- Configure scoped query keys that include every access-defining context (role, Teacher Workspace, linked Student, Teacher relationship, resource identity, list criteria) to prevent stale or cross-boundary cache hits.
- Invalidate or update only the affected scoped queries after a successful mutation.

## 5.4 Asset Performance

- Use Vite's asset fingerprinting for cache-friendly static assets.
- Minimize the number and size of static assets imported into the bundle.
- Do not bundle user-uploaded files, Lesson videos, or private content into the frontend build.
- Release local browser resources (object URLs, camera streams) when no longer needed.

## 5.5 Form Performance

React Hook Form keeps transient form state local and avoids unnecessary re-renders:

- Use React Hook Form for all interactive forms.
- Apply Zod schema validation at the client level for immediate feedback without server round trips.
- Prevent duplicate submissions while a request is pending.
- Release file preview resources when form state is cleared.

---

# 6. Database Performance

## 6.1 Query Scoping Strategy

Every query that accesses Teacher Workspace-owned data must include the Teacher Workspace scope. This is both a security requirement (BR-003) and a performance requirement: scoped queries naturally limit the result set to the relevant tenant's data.

| Query context | Scoping requirement |
|---|---|
| Teacher query | Scope to Teacher's own Teacher Workspace. |
| Teacher Staff query | Scope to creating Teacher Workspace. |
| Student query | Scope to Student's own records, partitioned per Teacher. |
| Parent query | Scope to linked Student records only. |
| Super Admin query | Platform-scoped, respecting pending content-visibility boundaries. |
| Background job query | Carry and enforce Teacher Workspace context where applicable. |

## 6.2 Indexing Strategy

Indexing must support the access patterns defined in `AI_DOCS/06_Database_Design.md` §10 and `AI_DOCS/22_Search_Filtering.md` §9:

| Index priority | Access pattern |
|---|---|
| Teacher Workspace scope | Every workspace-owned table must be indexed by the Teacher Workspace association to support efficient tenant-scoped queries. |
| User identity lookup | User identity, role context, and authentication lookups. |
| Student global uniqueness | Student identity uniqueness and duplicate prevention. |
| Enrollment lookup | Current and historical Enrollment lookup by Student and Teacher. |
| Attendance lookup | By Student, Group, Teacher Workspace, and date context. |
| Homework lookup | By Teacher workspace and Student relationship. |
| Exam lookup | By Teacher Workspace and Student relationship. |
| Flow A Subscription | By Teacher and Billing Cycle. |
| Flow B payment status | By Student and Teacher relationship. |
| Audit Log | By actor, event type, scope, and time context. |
| Archive state | Filtering for active versus archived records. |
| Search and filter fields | Fields commonly used in search, filtering, and sorting per `AI_DOCS/22_Search_Filtering.md`. |

Indexing constraints:

- Indexes must preserve Teacher Workspace isolation; they must not be used to bypass authorization.
- Indexes must support both active-record filtering and historical reporting.
- Full physical indexing definitions are deferred to implementation, but logical priorities are established here.

## 6.3 Eager Loading

Eager loading prevents N+1 query problems for known relationship access patterns:

- Use eager loading for relationships that are consistently accessed together (e.g., Group with Educational Grade, Exam with Questions).
- Avoid eager loading large or unnecessary relationships that inflate query result size.
- Use selective eager loading with query scopes to load only the needed related records.

## 6.4 Query Optimization

- Avoid `SELECT *` patterns; retrieve only the columns needed for the operation.
- Use database-level pagination (LIMIT/OFFSET) rather than loading full result sets into application memory.
- Avoid subqueries that scan large portions of the database when a join or a scoped query would suffice.
- Use MySQL 8's query execution plan analysis during development to identify missing indexes or inefficient query patterns.
- Batch related inserts and updates where the database and ORM support it.

## 6.5 Connection Management

On cPanel Shared Hosting:

- Use Laravel's default database connection pooling.
- Avoid long-running database connections that may be terminated by the hosting environment.
- Use short-lived transactions to avoid holding database locks.
- Do not require persistent database connections or connection pools beyond what MySQL 8 and cPanel provide by default.

## 6.6 Archive-Aware Queries

Archive state must be applied at the query level:

- Default queries return only active (non-archived) records.
- Historical/report queries that include archived records must clearly indicate archival state.
- Archived records must not appear in active search, selection lists, or dropdown options.
- Archive filtering must use indexed columns to avoid full-table scans.

---

# 7. API Performance

## 7.1 Response Optimization

API responses must return only the data the frontend needs:

- Use API Resources to shape response payloads, including only authorized and relevant fields.
- Avoid returning large nested relationships when the frontend only needs summary data.
- Use pagination for all list endpoints to prevent unbounded result sets.
- Compress responses where the web server supports it (e.g., gzip via Apache or LiteSpeed).

## 7.2 Request Validation Performance

- Validate requests early to reject invalid inputs before expensive database queries or business logic execute.
- Use Laravel Form Requests for input validation and basic authorization pre-checks.
- Cache validation rules where the framework supports it.

## 7.3 Rate Limiting Performance

Rate limiting protects sensitive endpoints without degrading normal user experience:

- Use Laravel's built-in rate limiting middleware with File Cache as the rate-limiting store.
- Apply rate limiting to sensitive endpoints: login, registration, QR scanning, password reset, file upload.
- Rate limiting must not require Redis or external rate-limiting services.

## 7.4 Authentication Performance

- Sanctum session/token validation should be lightweight and cached within the request lifecycle.
- Avoid redundant authentication checks within a single request.
- Session data stored in the Database session driver must be indexed for efficient lookup.

## 7.5 File Response Performance

- File access must pass through backend authorization and ownership checks.
- Use efficient file streaming where the hosting environment supports it.
- Avoid loading entire file binaries into application memory for authorized access; stream from Laravel Public Storage.
- Validate file access permissions early to reject unauthorized requests before reading file content.

---

# 8. Caching Strategy

## 8.1 Cache Driver

Version 1 uses **File Cache** as the official cache driver, compatible with cPanel Shared Hosting.

| Concern | Version 1 Standard |
|---|---|
| Cache driver | File Cache |
| External dependencies | None — no Redis, Memcached, or external cache service |
| Hosting compatibility | cPanel Shared Hosting |

## 8.2 Cacheable Data

Cache should be used for data that is:

- Frequently accessed.
- Slowly changing (changes infrequently relative to read frequency).
- Not user-specific private data that would require per-user cache keys.
- Safe to serve from a slightly stale version.

| Cacheable concern | Example | Cache invalidation trigger |
|---|---|---|
| Educational Grade lists per Teacher Workspace | Dropdown options, selector data | Create, update, Archive, restore of Educational Grade. |
| Group lists per Teacher Workspace | Dropdown options, selector data | Create, update, Archive, restore of Group. |
| Teaching Subject list | Static reference data | Rarely changes; invalidate on Platform Settings update. |
| Pricing configuration | Super Admin pricing settings | Pricing update by Super Admin. |
| Dashboard summary data | Teacher Workspace operational summaries | Mutation of underlying records. |
| Report aggregation results | Pre-computed summary statistics | Background aggregation job completion. |
| Search filter options | Commonly used filter value lists | Underlying data change. |

## 8.3 Cache Scoping

Cache entries must respect scope boundaries:

- Teacher Workspace cache entries must be scoped to the specific Teacher Workspace. One Teacher's cached data must never be served to another Teacher.
- Student cache entries must be scoped to the Student's own account.
- Parent cache entries must be scoped to the Parent's linked-Student context.
- Platform-level cache entries (e.g., pricing) are shared across authorized Super Admin requests only.

## 8.4 Cache Invalidation

Cache must be invalidated when underlying data changes:

- Use event-driven invalidation where the framework supports it.
- Invalidate scoped cache entries when the relevant records are created, updated, archived, or restored.
- Do not serve stale cache data that would violate business rules (e.g., showing an active Educational Grade that has been archived).
- Background job aggregation results stored in cache must be overwritten when re-aggregated.

## 8.5 Cache Constraints

- File Cache must not be used to store sensitive data such as passwords, tokens, or credentials.
- Cache must not bypass authorization; cached data must still be served only to authorized users.
- Cache must not be used to store unbounded result sets; use pagination for large collections.
- Cache performance on cPanel Shared Hosting depends on file-system I/O; excessive cache fragmentation should be avoided.

## 8.6 Future Cache Migration

When the Platform migrates to VPS or Cloud:

- Redis may be considered as a future cache driver for improved performance and shared cache across multiple application instances.
- Cache scoping, invalidation, and authorization rules must be preserved regardless of the cache driver.
- The caching strategy defined in this document must remain valid after a cache driver change.

---

# 9. Query Optimization

## 9.1 Scope-First Query Pattern

Every query must resolve scope before applying filters, search terms, or sorting:

1. Resolve the user's authorized scope (Teacher Workspace, Student account, Parent linked-Student, Platform).
2. Apply the scope as the base query constraint.
3. Apply filters within the scoped result set.
4. Apply search terms within the scoped and filtered result set.
5. Apply sorting to the scoped, filtered, and searched result set.
6. Apply pagination to the final result set.

This pattern ensures that the query optimizer can use indexes effectively and that the result set never includes unauthorized data.

## 9.2 Avoiding Common Performance Anti-Patterns

| Anti-pattern | Problem | Solution |
|---|---|---|
| N+1 queries | Loading relationships one-by-one in a loop. | Use eager loading for known relationship patterns. |
| Full-table scans | Queries without WHERE clauses or with non-indexed WHERE conditions. | Ensure all frequently filtered columns are indexed. |
| Unbounded result sets | Loading all records without pagination. | Use database-level pagination (LIMIT/OFFSET) for all list queries. |
| In-memory filtering | Loading a large result set and filtering in application code. | Push filters into the database query. |
| Redundant queries | Executing the same query multiple times in a request. | Cache results within the request lifecycle or use eager loading. |
| SELECT * | Retrieving all columns when only a few are needed. | Select only required columns. |
| Large transaction scope | Holding database locks for the duration of a long business operation. | Keep transactions short; use queue for long-running work. |
| Non-indexed sorting | Sorting by columns without indexes. | Index columns used for sorting in frequently accessed queries. |

## 9.3 Full-Text Search Optimization

MySQL 8 full-text search may be used for text-based search within authorized scope:

- Use MySQL 8 full-text indexes where text search is needed.
- Apply full-text search only within the user's authorized scope.
- Do not require external search engines (Elasticsearch, Meilisearch, Algolia) for Version 1.
- Full-text search queries must preserve Teacher Workspace isolation.

## 9.4 Report Query Optimization

Report queries often aggregate across many records:

- Use database-level aggregation (SUM, COUNT, AVG) rather than loading records into application memory.
- Scope report queries to the authorized Teacher Workspace, Student, Parent linked-Student, or Platform context.
- Use background job preparation for reports that require aggregation across large datasets (see `AI_DOCS/21_Background_Jobs.md` §9).
- Cache pre-computed aggregation results using File Cache where appropriate.
- Include archived records in historical reports only when explicitly authorized, using indexed Archive state columns.

---

# 10. File Storage Performance

## 10.1 Storage Access Pattern

Version 1 uses Laravel Public Storage. File access must pass through backend authorization:

1. The user requests access to a file through the API.
2. The backend authenticates the user and resolves role, scope, and ownership.
3. The backend verifies that the file belongs to the user's authorized context (Teacher Workspace, Student relationship, Parent linked-Student).
4. The backend verifies Archive state.
5. The backend delivers the file through an authorized response.

## 10.2 File Upload Performance

- Validate file type, size, and ownership early in the upload process to reject invalid uploads before consuming bandwidth and storage.
- Use chunked upload handling where the framework supports it for large files.
- Process file metadata asynchronously using the Database Queue where post-processing is needed.
- Avoid blocking the request cycle with file conversion, thumbnail generation, or other deferred work.

## 10.3 File Access Performance

- Use efficient file streaming rather than loading entire file binaries into application memory.
- Cache file metadata (ownership, type, Archive state) where appropriate to reduce database queries for repeated access.
- Do not cache file content in application memory; stream from Laravel Public Storage.

## 10.4 File Reference Integrity

- File reference integrity checks (weekly background job) must process files in batches to respect shared hosting limits.
- Orphaned references must be flagged for review rather than automatically removed, preserving historical data integrity.

## 10.5 Future File Storage Migration

When migrating to VPS or Cloud:

- S3 Storage or private object storage may be considered for improved scalability and CDN delivery.
- File ownership, authorization, Teacher Workspace isolation, and historical reference validity must be preserved.
- The storage access pattern defined in this document must remain valid after a storage migration.

---

# 11. Queue Performance

## 11.1 Queue Driver Performance

Version 1 uses the Laravel Database Queue. Performance considerations:

- The queue jobs table is stored in MySQL 8 and shares the same database as business data.
- Successfully processed jobs should be cleaned up periodically (weekly scheduled task) to prevent table growth.
- Failed jobs are retained in the failed jobs table for review.
- Queue table indexes must support efficient job retrieval by queue name, status, and availability timestamp.

## 11.2 Job Processing Performance

- Jobs must respect cPanel Shared Hosting execution time limits.
- Long-running jobs must be chunked into smaller batches (e.g., Billing Cycle processing iterates Teacher Workspaces one at a time).
- High-priority queues (billing, grading) must be processed before low-priority queues (reports, cleanup).
- The queue worker must not consume resources that degrade user-facing request performance.

## 11.3 Scheduler Performance

The Laravel Scheduler runs through Cron Jobs:

- The Scheduler must not run overlapping instances.
- Scheduled tasks that are still running when the next Scheduler trigger occurs must be handled gracefully.
- Multiple scheduled tasks triggered at the same time must be coordinated to avoid resource contention.

## 11.4 Future Queue Migration

When migrating to VPS or Cloud:

- Redis or other external queue drivers may be considered for improved throughput and reliability.
- Queue job priorities, idempotency, and Teacher Workspace scope preservation must be maintained.
- The queue strategy defined in `AI_DOCS/21_Background_Jobs.md` must remain valid after a queue driver change.

---

# 12. Search Performance

## 12.1 Search Optimization Principles

Search and filtering must be optimized for MySQL 8 and cPanel Shared Hosting (per `AI_DOCS/22_Search_Filtering.md` §9):

1. **Resolve scope before searching.** The authorized scope must narrow the search space before the search term or filter is applied.
2. **Use indexed fields for search and sort.** Searchable and sortable fields should be indexed in the physical database schema.
3. **Paginate all list results.** Unbounded result sets must not be returned.
4. **Avoid full-table scans.** Search queries must use appropriate indexes and scope constraints.
5. **Limit search term length.** Extremely long search terms must be rejected or truncated to prevent query performance issues.
6. **Cache frequently accessed filter options.** Educational Grade lists, Group lists, and other selector data may be cached using File Cache where appropriate.
7. **Chunk large operations.** Background search operations (e.g., report preparation involving search) must be chunked for shared hosting compatibility.

## 12.2 Search Performance Constraints

- Search must not require Redis, Elasticsearch, Algolia, Meilisearch, or any external search infrastructure.
- Search must not require S3 Storage, Docker, Kubernetes, WebSockets, or Microservices.
- Search optimization must never bypass authorization, Teacher Workspace isolation, Archive policy, or historical data retention.
- Performance must not be improved by treating archived records as active or by omitting authorization checks.

## 12.3 Full-Text Search

MySQL 8 full-text search may be used where text-based search within a column is needed:

- Apply only within the user's authorized scope.
- Use appropriate MySQL 8 full-text indexes.
- Do not require external search engines.
- Preserve Teacher Workspace isolation in all full-text queries.

## 12.4 Future Search Migration

When migrating to VPS or Cloud:

- External search engines (Elasticsearch, Meilisearch) may be considered for improved full-text search performance.
- Search scope, authorization, Teacher Workspace isolation, and Archive-awareness must be preserved.
- The search optimization strategy defined in `AI_DOCS/22_Search_Filtering.md` must remain valid after infrastructure changes.

---

# 13. Pagination Strategy

## 13.1 Offset-Based Pagination

Version 1 uses offset-based pagination as the standard approach (per `AI_DOCS/22_Search_Filtering.md` §8):

| Parameter | Description | Constraints |
|---|---|---|
| `page` | Requested page number (1-based). | Positive integer. Pages beyond the last page return an empty data set. |
| `per_page` | Number of records per page. | Within allowed minimum and maximum limits. |

## 13.2 Pagination Response Metadata

Paginated responses include:

| Field | Description |
|---|---|
| `meta.current_page` | The page being returned. |
| `meta.per_page` | The number of records on this page. |
| `meta.total` | The total number of records matching the filter within the user's authorized scope. |
| `meta.last_page` | The total number of pages. |

## 13.3 Pagination Performance

- Pagination queries must use database-level LIMIT/OFFSET, not application-level slicing.
- Total count queries must be scoped to the user's authorized result set.
- Large offset values (e.g., page 10,000) must be handled gracefully without excessive query time.
- Changing filters or sort order may change the total count and page layout; the system must not cache stale pagination metadata across filter changes.

## 13.4 Default Page Size

Default page size should balance usability and performance for cPanel Shared Hosting:

- A reasonable default (e.g., 15–25 records per page) balances page load time and user navigation.
- Users may adjust page size within allowed minimum and maximum limits.
- Exceeding the maximum returns the maximum.

## 13.5 Future Pagination Enhancement

Cursor-based pagination may be considered as a future optimization for very large datasets:

- Cursor-based pagination avoids the performance degradation of large offsets.
- It requires a stable sort field (e.g., primary key or created_at).
- Offset-based pagination remains the Version 1 standard.
- Cursor-based pagination, if adopted, must preserve all authorization, scope, and Archive-awareness rules.

---

# 14. Lazy Loading Strategy

## 14.1 Frontend Lazy Loading

Frontend lazy loading defers the loading of feature modules until the user navigates to them:

- Use React Router's lazy loading at feature and layout boundaries.
- Authentication and the entered role area should load eagerly.
- Teacher, Student, Parent, and Super Admin feature modules should load lazily.
- Non-critical modules (e.g., QR scanner integration) should defer until their feature route is entered.
- Shared components and primitives should be bundled with the features that use them or in a small shared chunk.

## 14.2 Backend Lazy Loading

Backend lazy loading applies to relationship loading:

- Use eager loading for known, frequently accessed relationship patterns.
- Use lazy loading for relationships that are rarely accessed or expensive to load.
- Avoid loading full relationship trees when only summary data is needed.
- Use selective column loading to minimize data transfer from MySQL.

## 14.3 Image and File Lazy Loading

- Lesson thumbnails or preview images (if implemented) should use lazy loading in the browser.
- File content should be loaded only when the user explicitly requests access.
- Object URLs for file previews should be released when no longer needed.

## 14.4 Data Lazy Loading

- Dashboard summary data should load asynchronously, with individual panels loading independently.
- List views should use pagination rather than loading all records.
- Detail views should load relationships on demand rather than preloading all related data.
- Background refresh of list data should use TanStack Query's background refetch to avoid blocking the UI.

---

# 15. Resource Optimization

## 15.1 Server Resource Optimization

On cPanel Shared Hosting, server resources are shared and limited:

| Resource | Optimization strategy |
|---|---|
| Memory | Avoid loading large collections into memory. Use chunked processing for background jobs. Use database-level aggregation for reports. |
| CPU | Avoid expensive computations in request handlers. Defer complex calculations to background jobs. |
| Disk I/O | Minimize file-system cache fragmentation. Clean up processed queue jobs periodically. |
| Database connections | Use short-lived connections and transactions. Avoid holding connections during long operations. |
| Process execution time | Respect cPanel's execution time limits. Chunk long-running jobs. Use queue for deferred work. |
| Network bandwidth | Compress API responses. Paginate results. Return only needed fields. |

## 15.2 Database Resource Optimization

- Use connection pooling provided by Laravel and MySQL.
- Keep transactions short to avoid holding database locks.
- Use indexed queries for all frequently accessed access patterns.
- Monitor and manage database table growth, especially for the Audit Log, queue jobs, and session tables.
- Clean up expired sessions from the Database session driver periodically.

## 15.3 File Storage Resource Optimization

- Monitor Laravel Public Storage usage to stay within cPanel disk space limits.
- Process file uploads efficiently to avoid consuming excessive memory.
- Use streaming for file delivery rather than loading entire files into memory.
- Clean up orphaned files where safe to do so, preserving historical references.

## 15.4 Frontend Resource Optimization

- Minimize the number of HTTP requests by batching API calls where the backend supports it.
- Use browser caching for static assets (Vite fingerprinted assets).
- Release browser resources (object URLs, camera streams, event listeners) when components unmount.
- Avoid memory leaks from uncleared intervals, subscriptions, or event handlers.

## 15.5 Background Job Resource Optimization

- Process jobs in small batches to avoid exceeding shared hosting memory limits.
- Use chunked iteration for jobs that process all Teacher Workspaces or large datasets.
- Clean up processed jobs from the queue table to prevent database table bloat.
- Coordinate scheduled tasks to avoid simultaneous resource contention.

---

# 16. Monitoring Metrics

## 16.1 Backend Metrics

| Metric | Purpose |
|---|---|
| Request response time | Identify slow endpoints that may need optimization. |
| Database query count per request | Detect N+1 query problems or redundant queries. |
| Database query execution time | Identify slow queries that may need indexing or optimization. |
| Memory usage per request | Detect memory leaks or excessive data loading. |
| Queue job processing time | Identify jobs that approach shared hosting execution limits. |
| Queue pending job count | Detect job backlog that may indicate processing issues. |
| Failed job count | Detect job failures that need investigation. |
| Cache hit rate | Evaluate cache effectiveness for frequently accessed data. |

## 16.2 Database Metrics

| Metric | Purpose |
|---|---|
| Table row counts | Monitor growth of key tables (Audit Log, queue jobs, sessions, Attendance, Homework). |
| Index usage | Verify that queries use intended indexes. |
| Slow query log | Identify queries that exceed acceptable execution time. |
| Connection count | Monitor database connection usage against shared hosting limits. |
| Disk usage | Monitor database size against cPanel storage limits. |

## 16.3 Frontend Metrics

| Metric | Purpose |
|---|---|
| Bundle size | Monitor JavaScript bundle size to prevent bloat. |
| First contentful paint | Identify initial page load performance. |
| Time to interactive | Measure when the page becomes usable after load. |
| API call count per page | Detect unnecessary or redundant API calls. |
| Cache invalidation rate | Evaluate whether TanStack Query cache is being used efficiently. |

## 16.4 Infrastructure Metrics

| Metric | Purpose |
|---|---|
| Disk usage (database + files) | Monitor total storage consumption against cPanel limits. |
| CPU usage | Identify periods of high CPU utilization. |
| Memory usage | Monitor server memory against shared hosting limits. |
| Cron job execution status | Verify that scheduled tasks are running successfully. |

## 16.5 Monitoring Constraints

- Monitoring must not expose Teacher-private data.
- Monitoring must not introduce push, email, or SMS notification features.
- Monitoring tools, dashboards, and alert thresholds are not confirmed and must not be invented.
- Monitoring must not require Redis, external monitoring services, or unconfirmed infrastructure.
- Quantified response-time, throughput, concurrency, or capacity targets are not confirmed in the Project Context.

---

# 17. Capacity Planning

## 17.1 Growth Dimensions

Capacity planning must consider growth in each of the following dimensions:

| Dimension | Growth driver | Impact area |
|---|---|---|
| Teacher Workspaces | New Teacher registrations. | Database row counts, query scoping, report aggregation. |
| Students per workspace | Teacher enrolling more Students. | Workspace-scoped query result sizes, Attendance records, Homework submissions. |
| Students globally | Platform-wide Student registration. | Global identity lookup, duplicate prevention, per-Teacher partitioning. |
| Groups per workspace | Teacher creating more Groups. | Group-related queries, Enrollment management. |
| Attendance records | Daily Attendance across all methods. | Attendance table growth, report query performance. |
| Homework records | Homework creation, submission, grading. | Homework and submission table growth, file storage. |
| Exam records | Exam creation, attempts, grading. | Exam, attempt, and grade table growth, background grading jobs. |
| Lesson records | Lesson uploads and metadata. | Lesson table growth, file storage. |
| Audit Log entries | Every important action. | Audit Log table growth (permanent retention). |
| File storage | Lesson videos, Homework files, submissions. | Laravel Public Storage disk usage. |
| Queue jobs | Background job dispatch. | Queue table size, processing throughput. |
| Session data | Active user sessions. | Session table size, cleanup requirements. |

## 17.2 Capacity Constraints on cPanel Shared Hosting

| Constraint | Implication |
|---|---|
| Database size | Monitor MySQL database size against cPanel allocation. Historical data is never deleted (BR-014), so growth must be planned. |
| File storage | Monitor Laravel Public Storage usage against cPanel disk allocation. Lesson video storage quota remains PENDING (Q-010). |
| Process execution time | Long-running jobs must be chunked. Background job processing must respect Cron Job execution limits. |
| Memory | Application memory usage must stay within shared hosting limits. Avoid loading large collections into memory. |
| Concurrent connections | MySQL and PHP concurrent connection limits on shared hosting must be respected. |
| CPU | CPU-intensive operations must be deferred to background jobs or optimized for efficiency. |

## 17.3 Capacity Planning Actions

| Action | Frequency | Responsibility |
|---|---|---|
| Review database table growth rates. | Monthly. | Platform operator. |
| Review file storage usage. | Monthly. | Platform operator. |
| Review queue job backlog and failure rates. | Weekly. | Platform operator. |
| Review Audit Log growth. | Monthly (scheduled background job). | Automated via `AI_DOCS/21_Background_Jobs.md` §11. |
| Review session table size. | Monthly. | Platform operator. |
| Optimize slow queries identified in logs. | As needed. | Development team. |
| Clean up processed queue jobs. | Weekly (scheduled background job). | Automated via `AI_DOCS/21_Background_Jobs.md` §5. |
| Verify file reference integrity. | Weekly (scheduled background job). | Automated via `AI_DOCS/21_Background_Jobs.md` §10. |

## 17.4 Capacity Thresholds

Specific numeric thresholds are not confirmed in the Project Context. When capacity approaches cPanel Shared Hosting limits:

- Evaluate whether query optimization, indexing, or caching can reduce resource consumption.
- Evaluate whether background job chunking or scheduling adjustments can reduce peak resource usage.
- Consider migration to VPS or Cloud if shared hosting limits cannot accommodate Platform growth.
- Migration to VPS or Cloud must preserve all confirmed business rules, tenant isolation, and security boundaries.

---

# 18. Future Horizontal Scaling

## 18.1 Current Vertical Scaling Model

Version 1 on cPanel Shared Hosting uses a vertical scaling model:

- Single server hosts the Laravel backend, MySQL database, file storage, queue worker, and scheduler.
- Scaling is achieved by optimizing code, queries, caching, and background job efficiency within the single server's resource limits.

## 18.2 VPS / Cloud Migration Path

When the Platform outgrows cPanel Shared Hosting, migration to VPS or Cloud is the confirmed future deployment target (per `AI_DOCS/03_System_Architecture.md` §4.1).

Migration must preserve:

- Teacher Workspace isolation (BR-003).
- One global Student account (BR-001).
- Parent linked-Student read-only access (BR-004).
- Archive instead of permanent deletion (BR-005).
- Immutable permanent Audit Log (BR-006).
- Flow A / Flow B separation.
- All confirmed business rules.

## 18.3 VPS / Cloud Scaling Opportunities

After migration to VPS or Cloud, the following scaling opportunities may be explored:

| Opportunity | Benefit | Required approval |
|---|---|---|
| Dedicated database server | Separate MySQL from the application server for improved resource allocation. | Infrastructure approval. |
| Redis cache | Replace File Cache with Redis for faster cache access and shared cache across instances. | Infrastructure approval. |
| Redis queue | Replace Database Queue with Redis Queue for improved throughput and reliability. | Infrastructure approval. |
| Object storage (S3) | Move file storage to S3 or equivalent for improved scalability and CDN delivery. | Infrastructure approval + file ownership rules preserved. |
| Load balancing | Distribute requests across multiple application instances. | Infrastructure approval + session sharing strategy. |
| CDN for static assets | Serve frontend static assets through a CDN for improved global performance. | Infrastructure approval. |
| Database read replicas | Use read replicas for report queries and read-heavy operations. | Infrastructure approval + write-through consistency. |
| Horizontal queue workers | Run multiple queue workers for improved background job throughput. | Infrastructure approval. |

## 18.4 Horizontal Scaling Constraints

Any horizontal scaling approach must:

- Preserve Teacher Workspace isolation at every layer.
- Not introduce cross-Teacher data access through shared cache, shared session state, or load balancer routing.
- Preserve Archive policy, Audit Log immutability, and historical data retention.
- Preserve Flow A / Flow B separation.
- Not require Docker, Kubernetes, or Microservices as mandatory V1 components.
- Be compatible with the confirmed technology stack or approved future stack changes.

## 18.5 Database Scaling Strategy

As the database grows:

- Vertical scaling (larger server, more RAM, faster storage) is the first approach after VPS migration.
- Table partitioning by Teacher Workspace or time period may be considered for very large tables (e.g., Attendance, Audit Log).
- Read replicas may be considered for report and analytics queries.
- Write operations must always target the primary database.
- All database scaling must preserve tenant isolation and data integrity.

---

# 19. Performance Risks

## 19.1 Shared Hosting Resource Limits

| Risk | Impact | Mitigation |
|---|---|---|
| Process execution time exceeded | Background jobs fail or are terminated prematurely. | Chunk long-running jobs. Use queue for deferred work. |
| Memory limit exceeded | Request processing fails or is terminated. | Avoid loading large collections. Use pagination. Use chunked processing. |
| Database connection limit | Requests fail during peak usage. | Use short-lived connections and transactions. Optimize query efficiency. |
| Disk space limit | File uploads fail; database growth stalls. | Monitor disk usage. Plan for migration to VPS/Cloud when limits are approached. |
| CPU throttling | Slow response times during peak usage. | Optimize queries and business logic. Defer complex work to background jobs. |
| Cron Job execution limit | Scheduled tasks fail or overlap. | Prevent Scheduler overlap. Chunk scheduled work. |

## 19.2 Multi-Tenant Performance Degradation

| Risk | Impact | Mitigation |
|---|---|---|
| Large Teacher Workspace dominates queries | Queries for one workspace slow down the entire Platform. | Ensure queries are always workspace-scoped. Index workspace association columns. |
| Many small Teacher Workspaces | High overhead from per-workspace query scoping. | Efficient indexing on workspace scope. Batch processing for Platform-level operations. |
| Cross-tenant data scan | Performance degradation from unscoped queries. | Enforce workspace scoping at the repository and service layers. |
| Archive state filtering overhead | Filtering archived records adds query complexity. | Index Archive state columns. Apply Archive filtering at the query level. |

## 19.3 Growth-Related Risks

| Risk | Impact | Mitigation |
|---|---|---|
| Audit Log table growth | Write performance degradation; slow Audit Log queries. | Index Audit Log by actor, event type, scope, and time. Use background verification. Do not purge (permanent retention). |
| Queue table growth | Slow queue processing; database bloat. | Clean up processed jobs weekly. Monitor queue table size. |
| Session table growth | Slow session lookups; database bloat. | Clean up expired sessions periodically. |
| File storage growth | Disk space exhaustion on shared hosting. | Monitor storage usage. Plan for VPS/Cloud migration. |
| Historical data accumulation | Report and history queries slow down. | Use indexed queries with Archive-aware filtering. Use background aggregation for frequent reports. |
| Exam submission spikes | Concurrent Exam submissions overwhelm background grading. | Use queue for grading. Process in batches. Scale queue workers after VPS migration. |

## 19.4 Query Performance Risks

| Risk | Impact | Mitigation |
|---|---|---|
| Missing indexes | Full-table scans for common queries. | Implement logical indexing priorities from `AI_DOCS/06_Database_Design.md` §10. |
| N+1 queries | Excessive database round trips. | Use eager loading for known relationship patterns. |
| Large OFFSET values | Slow pagination for deep pages. | Use reasonable default page sizes. Consider cursor-based pagination in the future. |
| Complex report queries | Long-running aggregation queries. | Use background job preparation. Cache aggregation results. |
| Search across large text fields | Slow LIKE queries without indexes. | Use MySQL 8 full-text indexes where appropriate. |

## 19.5 Frontend Performance Risks

| Risk | Impact | Mitigation |
|---|---|---|
| Bundle size bloat | Slow initial page load. | Use route-level lazy loading. Monitor bundle size. |
| Unnecessary API calls | Increased server load and slow UI. | Use TanStack Query caching and stale-time configuration. |
| Memory leaks | Degrading browser performance over time. | Release resources on unmount. Cancel in-flight requests. |
| Stale cache data | Displaying outdated or cross-boundary data. | Use scoped query keys. Invalidate on mutation. Clear on context change. |

---

# 20. Future Improvements

The following are future considerations only and are not Version 1 commitments:

| Future area | Required future decision |
|---|---|
| **Redis cache** | Approve Redis as a cache driver after VPS/Cloud migration. Version 1 uses File Cache only. |
| **Redis queue** | Approve Redis as a queue driver after VPS/Cloud migration. Version 1 uses Database Queue only. |
| **Elasticsearch / Meilisearch** | Approve external search engine for improved full-text search after infrastructure approval. Version 1 uses MySQL 8 only. |
| **S3 Storage** | Approve S3 or equivalent for file storage after infrastructure approval. Version 1 uses Laravel Public Storage only. |
| **CDN for static assets** | Approve CDN for frontend static asset delivery after infrastructure approval. |
| **Database read replicas** | Approve read replicas for report and analytics queries after VPS/Cloud migration. |
| **Horizontal load balancing** | Approve load balancing across multiple application instances after VPS/Cloud migration. |
| **Cursor-based pagination** | Approve cursor-based pagination for very large datasets while preserving offset-based pagination as default. |
| **Real-time updates** | Approve WebSocket or SSE for real-time data updates after infrastructure approval. WebSockets are not required for Version 1. |
| **Advanced monitoring** | Approve external monitoring tools, APM, and alerting infrastructure. |
| **Database partitioning** | Approve table partitioning by Teacher Workspace or time period for very large tables. |
| **Background job scaling** | Approve multiple queue workers and external queue infrastructure after VPS/Cloud migration. |
| **Performance benchmarking** | Define and approve numeric response-time, throughput, concurrency, and capacity targets. |
| **Content Delivery Network for Lesson videos** | Approve CDN for Lesson video delivery after Lesson hosting/protection details are resolved (Q-010 PENDING). |
| **Localization performance** | Resolve language, timezone, currency, and market requirements (Q-015) before regional performance optimization. |

All future improvements must preserve:

- Teacher Workspace isolation (BR-003).
- One global Student account (BR-001).
- Parent linked-Student read-only access (BR-004).
- Archive instead of permanent deletion (BR-005).
- Immutable permanent Audit Log (BR-006).
- Flow A and Flow B separation.
- Canonical terminology.
- cPanel Shared Hosting compatibility for Version 1 (or separately approved infrastructure for future versions).

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Project Context alignment | Passed — all performance and scalability rules follow the frozen Version 1 source of truth. BR-003 (Teacher Workspace isolation), BR-005 (Archive), BR-006 (Audit Log), BR-014 (historical data), and all other confirmed business rules are preserved. |
| System Architecture alignment | Passed — technology baseline (Laravel 12, PHP 8.3, React 19, Vite, MySQL 8, File Cache, Database Queue, Database sessions, Laravel Public Storage, Laravel Scheduler, cPanel Shared Hosting, VPS/Cloud future target) is consistent with `AI_DOCS/03_System_Architecture.md` §4.1. |
| Backend Architecture alignment | Passed — backend performance guidelines are consistent with `AI_DOCS/11_Backend_Architecture.md` §22. Query scoping, pagination, filtering, sorting, caching, queue usage, and Audit Log obligations are preserved. |
| Frontend Architecture alignment | Passed — frontend performance guidelines are consistent with `AI_DOCS/12_Frontend_Architecture.md` §18. Lazy loading, TanStack Query, React 19 rendering, Vite builds, and authorization boundaries are preserved. |
| Database Design alignment | Passed — indexing strategy is consistent with `AI_DOCS/06_Database_Design.md` §10. Logical indexing priorities, tenant-scoped access patterns, and Archive-aware queries are preserved. |
| Search & Filtering alignment | Passed — search performance guidelines are consistent with `AI_DOCS/22_Search_Filtering.md` §9. Scope-first querying, indexed fields, pagination, full-text search constraints, and external search engine exclusion are preserved. |
| Background Jobs alignment | Passed — queue performance guidelines are consistent with `AI_DOCS/21_Background_Jobs.md` §4 and §18. Database Queue, chunked processing, job prioritization, and cPanel compatibility are preserved. |
| File Storage alignment | Passed — file storage performance guidelines are consistent with `AI_DOCS/20_File_Storage.md`. Laravel Public Storage, application-level authorization, and future cloud storage constraints are preserved. |
| Subscription/Billing alignment | Passed — Billing Cycle and Billable Student calculation performance references are consistent with `AI_DOCS/17_Subscription_Billing.md`. Enrollment-duration-only rule, calendar-month Billing Cycle, and Flow A/Flow B separation are preserved. |
| Security Standards alignment | Passed — performance optimization does not bypass authentication, authorization, or data privacy boundaries defined in `AI_DOCS/23_Security_Standards.md`. |
| Teacher Workspace isolation | Passed — all performance strategies preserve tenant isolation. Multi-tenant scalability, query scoping, cache scoping, and background job scope are defined. |
| Student account rules | Passed — one global Student account, duplicate prevention, per-Teacher partitioning, and Group movement history preservation are referenced. |
| Parent access rules | Passed — linked-Student read-only access and multi-Student monitoring scalability are preserved. |
| Archive policy | Passed — no performance optimization references permanent deletion. Archive-aware queries, Archive state indexing, and historical data retention are defined. |
| Audit Log policy | Passed — Audit Log growth monitoring, permanent retention, append-only/immutable properties, and write performance are addressed. |
| Payment handling | Passed — Version 1 records payment status only. Flow A and Flow B scalability are addressed separately. No payment processing is referenced. |
| Version 1 scope | Passed — no native mobile, payment gateway, notification, marketplace, video homework, Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices are introduced as V1 requirements. |
| cPanel compatibility | Passed — all V1 performance strategies are compatible with cPanel Shared Hosting. File Cache, Database Queue, Database sessions, Laravel Public Storage, Cron Jobs, and Apache/LiteSpeed are preserved. |
| VPS/Cloud migration | Passed — future migration path is defined with preserved business rules, tenant isolation, and security boundaries. |
| PENDING items | Passed — non-payment enforcement (Q-005), Lesson video hosting/protection (Q-010), Teacher Staff permission granularity (Q-011), Super Admin content visibility (Q-012), pricing model (Q-013), and localization (Q-015) are preserved as PENDING and not silently hardened. |
| Canonical terminology | Passed — Teacher Workspace, Educational Grade, Teaching Subject, Group, Student, Parent, Teacher Staff, Super Admin, Subscription, Flow A, Flow B, Enrollment, Archive, Audit Log, Dynamic QR Code, ID Card, Question Bank, Bubble Sheet, Lesson, Billable Student, Billing Cycle, and Homework are used consistently. |
| No source code | Passed — no source code, APIs, SQL queries, database tables, UI implementation, or physical configuration is defined. |
