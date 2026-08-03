# 22 — Search & Filtering

## Document Scope

This document defines the complete Search, Filtering, Sorting, and Pagination standards for Version 1 of the Unified Education Platform. It establishes consistent rules across all modules, roles, and data domains.

This document does not define source code, APIs, SQL queries, database tables, UI implementation, or physical index structures. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found.

The Search & Filtering system operates within the confirmed Version 1 technology baseline: **Laravel 12** backend with **MySQL 8**, **Laravel Sanctum** authentication, **Laravel Gates & Policies with Custom RBAC** authorization, **File Cache**, and **cPanel Shared Hosting** compatibility. All search behavior must preserve Teacher Workspace isolation, Student self-scope, Parent linked-Student read-only access, Archive policy, and Flow A / Flow B separation.

---

# 1. Feature Overview

Search, Filtering, Sorting, and Pagination are cross-cutting capabilities that apply to every list, report, dashboard summary, and data-retrieval surface in the Platform. They provide consistent mechanisms for users to locate, refine, order, and navigate authorized data within their confirmed scope.

Version 1 supports:

- **Search** for locating records by text query (name, title, identifier, keyword) within the authenticated user's authorized scope.
- **Filtering** for narrowing result sets by relevant criteria such as status, date range, Educational Grade, Group, Student, Teacher, and domain-specific attributes.
- **Sorting** for ordering results by supported fields in ascending or descending direction.
- **Pagination** for presenting large result sets in manageable, navigable pages.

These capabilities apply across all confirmed modules: Educational Grades, Groups, Students, Attendance, Homework, Lessons, Exams, Question Banks, Reports, Payments (Flow B), Subscriptions (Flow A), Teacher Staff, Audit Logs, Settings, and Platform Administration.

Search and filtering are not marketplace discovery, cross-Teacher browsing, global unrestricted data access, notification triggers, or payment processing mechanisms. They are scoped data-retrieval aids that respect every confirmed authorization boundary.

---

# 2. Objectives

The confirmed objectives of the Search & Filtering system are to:

1. Provide **consistent search, filtering, sorting, and pagination behavior** across all modules and roles.
2. Ensure all search results **respect RBAC permissions** as defined in `AI_DOCS/08_RBAC.md` and `AI_DOCS/09_Permission_Matrix.md`.
3. Guarantee that **Teachers can search only within their own Teacher Workspace** (BR-003).
4. Guarantee that **Students can search only their own accessible records**, partitioned per Teacher relationship (BR-001).
5. Guarantee that **Parents can search only data related to their linked Students**, in read-only mode (BR-004).
6. Guarantee that **Super Admin can perform Platform-level searches** within confirmed content-visibility boundaries (Q-012 PENDING).
7. Ensure **all lists support pagination** to protect against unbounded result sets and shared-hosting resource limits.
8. Ensure **filtering is consistent across all modules** so users experience predictable behavior regardless of the data domain.
9. Ensure **sorting supports ascending and descending order** where applicable.
10. **Optimize for large datasets** within the cPanel Shared Hosting baseline without requiring Redis, external search engines, S3 Storage, or unconfirmed infrastructure.
11. Preserve **Teacher Workspace isolation**, **Archive policy**, **Flow A / Flow B separation**, and **Audit Log obligations** in all search and filtering operations.
12. Prevent search and filtering from exposing Teacher-private content, unlinked Student data, Question Bank content, Lesson content, or another Teacher Workspace's records.

---

# 3. Search Principles

The following principles govern all search, filtering, sorting, and pagination behavior in Version 1:

1. **Authorization before search.** Every search, filter, sort, and pagination request must be evaluated for authentication, role, scope, ownership, and permission before data retrieval begins. The backend is the final enforcement authority. Frontend visibility or hidden controls are not sufficient security controls.

2. **Scope resolution before filtering.** The user's authorized scope (Teacher Workspace, Student account, Parent linked-Student, Platform) must be resolved before any filter, sort, or search term is applied. Search terms and filters must not bypass scope boundaries.

3. **Teacher Workspace isolation is mandatory.** A Teacher's search must never return records from another Teacher Workspace. Search terms that would match records in another workspace must not reveal the existence of those records (BR-003).

4. **Student self-scope is mandatory.** A Student's search must return only the Student's own records, partitioned per Teacher relationship. A Student must not discover another Student's records through search (BR-001).

5. **Parent linked-Student scope is mandatory.** A Parent's search must return only data related to the Parent's linked Students, in read-only mode. A Parent must not discover unlinked Student data through search (BR-004).

6. **Archive-aware results.** Active searches return only active records. Archived records appear only in historical/report contexts where explicitly permitted and clearly indicated as archived. Search must not treat archived records as active (BR-005, §11 Archive Policy).

7. **Flow A / Flow B separation.** Search and filtering across financial domains must maintain the separation between Flow A (Subscription) and Flow B (payment status). A single search must not return ambiguous results that mix the two flows.

8. **No cross-Teacher discovery.** Search must not reveal the existence, count, names, or metadata of records belonging to another Teacher Workspace, even when a search term matches across workspaces.

9. **Consistent behavior.** Filtering, sorting, and pagination must behave consistently across all modules so that users can predict how data retrieval works regardless of the current feature area.

10. **Performance-safe.** All search and filtering must be optimized for MySQL 8 and cPanel Shared Hosting. Complex joins and large result sets must be handled within the confirmed infrastructure baseline.

11. **No unconfirmed features.** Search must not introduce marketplace discovery, cross-Teacher browsing, global content search, notification triggers, payment processing, or native mobile search behavior.

12. **Canonical terminology.** Search labels, filter names, and result displays must use canonical terminology: Educational Grade (never "Class"), Lesson (never "Course"), Archive (never "Delete"), Subscription (Flow A only), payment status (Flow B).

---

# 4. Global Search

## 4.1 Definition

Global Search allows a user to search across multiple data domains within the user's confirmed scope. Global Search does not mean unrestricted Platform-wide search; it means the user can search across accessible modules from a single entry point, with results limited to their authorized boundary.

## 4.2 Scope by Role

| Role | Global Search scope |
|---|---|
| **Super Admin** | Platform-level Teacher accounts, Subscription records, pricing, platform settings, and Platform-scoped reports within confirmed content-visibility boundaries. Does not include Teacher-private content such as Lessons, Question Banks, Homework content, or individual Exam definitions while visibility remains PENDING. |
| **Teacher** | All modules within the Teacher's own Teacher Workspace: Educational Grades, Groups, Students, Attendance, Homework, Lessons, Exams, Question Banks, Reports, Teacher Staff, Settings, and Flow B payment-status records. No results from another Teacher Workspace. |
| **Teacher Staff** | Modules within the creating Teacher Workspace, limited to the Teacher-assigned permissions. No results from another Teacher Workspace. |
| **Student** | The Student's own records across Teachers: schedule, Homework, Lessons, Exams, per-Teacher Attendance, per-Teacher Flow B status. Results are partitioned per Teacher relationship. No results from another Student's records. |
| **Parent** | Data related to linked Students only: Homework, Attendance, Exams, Teachers, and Flow B payment status for linked Students. Read-only. No results for unlinked Students. |

## 4.3 Global Search Behavior

- Global Search accepts a text query and returns matching records from accessible modules within the user's scope.
- Results are grouped or labeled by module for clarity.
- Each result must pass the same authorization, scope, ownership, and Archive-state checks as if the user navigated to that module directly.
- Global Search must not return records the user cannot access through normal module navigation.
- Global Search must not reveal the existence of records the user is not authorized to see.
- Empty results are a valid outcome and must not imply that matching records exist in an inaccessible scope.

## 4.4 Global Search Restrictions

- Global Search must not search across Teacher Workspaces for Teachers or Teacher Staff.
- Global Search must not search other Students' records for Students.
- Global Search must not search unlinked Students' records for Parents.
- Global Search must not expose Teacher-private content to the Super Admin beyond confirmed visibility boundaries.
- Global Search must not return archived records in active search results.
- Global Search must not trigger notifications, process payments, or initiate background operations.
- Global Search must not use unconfirmed search engines, external indexing services, or infrastructure beyond MySQL 8.

---

# 5. Module Search

## 5.1 Definition

Module Search allows a user to search within a specific module's data domain. Module Search is the primary search mechanism for focused data retrieval within a single feature area.

## 5.2 Module Search Domains

| Module | Searchable fields (logical) | Scope boundary |
|---|---|---|
| Educational Grades | Name | Current Teacher Workspace only. |
| Groups | Name, Educational Grade name | Current Teacher Workspace only. |
| Students | Name, identifier, account information | Current Teacher Workspace Student relationships for Teacher/Teacher Staff. Own account for Student. Linked Students for Parent. |
| Attendance | Student name, date, session context, status | Current Teacher Workspace for Teacher/Teacher Staff. Own Attendance for Student. Linked Student Attendance for Parent. |
| Homework | Title, description, status | Current Teacher Workspace for Teacher/Teacher Staff. Assigned Homework for Student. Linked Student Homework for Parent. |
| Lessons | Title, description | Current Teacher Workspace for Teacher. Available Lessons from own Teachers for Student. |
| Exams | Title, status | Current Teacher Workspace for Teacher/Teacher Staff. Available/assigned Exams for Student. Linked Student Exams for Parent. |
| Question Bank | Question content, question type | Current Teacher Workspace only. Student and Parent cannot search Question Bank. |
| Reports | Report type, date, criteria | Current Teacher Workspace for Teacher. Own records for Student. Linked Student records for Parent. Platform scope for Super Admin. |
| Payments (Flow B) | Student name, status, date | Current Teacher Workspace for Teacher. Own per-Teacher status for Student. Linked Student status for Parent. |
| Subscriptions (Flow A) | Teacher name, status, Billing Cycle | Platform scope for Super Admin. Own status for Teacher where authorized. |
| Teacher Staff | Name, role | Current Teacher Workspace only. |
| Audit Logs | Actor, event type, date, scope | Platform scope for Super Admin within confirmed visibility. Own workspace for Teacher where permitted. |
| Files | File name, owning resource | Current Teacher Workspace only. |

## 5.3 Module Search Behavior

- Module Search accepts a text query and optional module-specific filters.
- Results are limited to the user's authorized scope for that module.
- Search terms must not cause results to leak from another Teacher Workspace, Student account, or unlinked Student relationship.
- Module Search results are paginated.
- Module Search supports the same sorting and filtering rules as module list views.

## 5.4 Module Search Restrictions

- Teacher Staff can search only modules for which they have Teacher-assigned permissions.
- Student search within any module returns only the Student's own records, partitioned per Teacher.
- Parent search within any module returns only linked Student records, in read-only mode.
- Question Bank search is available only to the owning Teacher (and authorized Teacher Staff). Students, Parents, and other Teachers cannot search a Teacher's private Question Bank.
- Lesson search for Students returns only Lessons from the Student's own Teachers. No cross-Teacher Lesson discovery.

---

# 6. Filtering Rules

## 6.1 General Filtering Principles

1. **Filters apply after authorization and scope resolution.** A filter must never broaden the result set beyond the user's authorized scope.
2. **Filters must reference records within the authorized scope.** A filter that references an unauthorized record (e.g., another Teacher Workspace, an unlinked Student) must be rejected.
3. **Filters are consistent across modules.** Common filter types (status, date range, Educational Grade, Group, Student) behave the same way in every module where they apply.
4. **Filters must not bypass Archive rules.** Active searches return only active records unless the user explicitly requests historical/archived results in an authorized reporting context.
5. **Filters must not mix Flow A and Flow B.** Financial filters must clearly distinguish between Subscription (Flow A) and payment-status (Flow B) criteria.
6. **Unsupported or invalid filters must be rejected safely** without exposing unauthorized data or implementation details.

## 6.2 Common Filters

The following filters are available across modules where applicable to the module's data domain:

| Filter | Description | Applicable modules |
|---|---|---|
| `status` | Active, archived, pending, submitted, paid, unpaid, or other valid resource-specific status. | All archivable modules, Homework, Exams, Payments, Subscriptions. |
| `educational_grade_id` | Filter by Educational Grade. Must belong to the current Teacher Workspace. | Groups, Students, Attendance, Homework, Exams, Reports. |
| `group_id` | Filter by Group. Must belong to the current Teacher Workspace or visible Student relationship. | Students, Attendance, Homework, Exams, Payments, Reports. |
| `student_id` | Filter by Student. Must be within authorized scope. | Attendance, Homework, Exams, Payments, Reports. |
| `teacher_id` | Filter by Teacher. Allowed only at Platform level, or in Student/Parent contexts for own relationships. | Subscriptions, Platform reports, Student views, Parent views. |
| `teacher_workspace_id` | Filter by Teacher Workspace. Allowed only when the authenticated role is permitted to reference that workspace. | Super Admin Platform-level queries only. |
| `from_date` | Start date for date-range filtering. | Attendance, Homework, Exams, Payments, Subscriptions, Audit Logs, Reports. |
| `to_date` | End date for date-range filtering. | Attendance, Homework, Exams, Payments, Subscriptions, Audit Logs, Reports. |
| `billing_cycle` | Calendar-month Billing Cycle for Flow A Subscription queries. | Subscriptions, Platform reports. |
| `pricing_type` | Monthly or Per Lesson. | Groups, Payments (Flow B). |
| `attendance_method` | Dynamic QR Code, ID Card, or Manual. | Attendance. |
| `question_type` | Multiple Choice, True/False, Essay, Bubble Sheet. | Question Bank, Exams. |
| `exam_id` | Filter by specific Exam. | Exam Attempts, Exam results. |
| `homework_id` | Filter by specific Homework. | Homework Submissions. |
| `archive_state` | Active only, archived only, or all. Available only in authorized historical/report contexts. | All archivable modules. |

## 6.3 Filter Validation

Every filter value must be validated before being applied:

1. The referenced record must exist.
2. The referenced record must be within the user's authorized scope.
3. The filter value must be a valid type (e.g., valid ID, valid date, valid enum value).
4. Cross-Teacher filters must be rejected for Teacher and Teacher Staff roles.
5. Unlinked Student filters must be rejected for Parent roles.
6. Another Student's filter must be rejected for Student roles.
7. Filters that reference Teacher-private content must respect the PENDING Super Admin content-visibility boundary.

## 6.4 Empty Filter Results

When a valid filter yields no records within the user's authorized scope, the result is an empty result set with appropriate empty-state handling. The system must not:

- Return an authorization error for a valid filter with no matches.
- Imply that matching records exist in an inaccessible scope.
- Suggest broadening the search to unauthorized data.

---

# 7. Sorting Rules

## 7.1 General Sorting Principles

1. **Sorting applies after authorization, scope resolution, and filtering.** Sorting must not cause records outside the user's authorized scope to appear.
2. **Sorting must not expose data outside the permitted result set.** Changing sort order must not reveal records that are not authorized for the user.
3. **Sorting supports ascending and descending order.** A leading minus sign (e.g., `-created_at`) indicates descending order; a bare field name indicates ascending.
4. **Sorting must not cause archived records to appear as active.** Archive state filtering takes precedence over sort order.
5. **Sorting must preserve Flow A / Flow B separation.** Financial sorting must not mix Subscription and payment-status records.
6. **Unsupported or invalid sort fields must be rejected or ignored consistently** without broadening the result set.

## 7.2 Common Sort Fields

| Sort field | Description | Applicable modules |
|---|---|---|
| `created_at` | Creation timestamp order. | All modules. |
| `updated_at` | Last update timestamp order. | All modules. |
| `name` | Alphabetical order where the resource has a name field. | Educational Grades, Groups, Students, Lessons, Question Banks, Teacher Staff. |
| `title` | Alphabetical order where the resource has a title field. | Homework, Exams, Lessons. |
| `status` | Status order where meaningful. | Homework, Exams, Payments, Subscriptions. |
| `date` | Date order for date-based records. | Attendance, Homework, Exams, Payments, Subscriptions, Audit Logs. |
| `student_name` | Alphabetical Student name order. | Attendance, Homework, Exams, Payments, Reports. |
| `grade` | Numerical or categorical grade order. | Exam results, Homework grading. |
| `amount` | Numerical amount order. | Payments, Subscriptions. |
| `billing_cycle` | Chronological Billing Cycle order. | Subscriptions. |

## 7.3 Sort Field Validation

- Sort fields must be in the allowed catalog for the module.
- Unsupported sort fields must be rejected or ignored; the system must not apply a default sort that broadens the result set.
- Multi-column sorting (sorting by more than one field simultaneously) is not confirmed for Version 1 and must not be assumed.
- Default sort order should be defined per module where applicable, typically `created_at` descending for list views.

## 7.4 Sorting Constraints

- Sorting must be applied to the authorized, filtered result set before pagination.
- Sorting must not be used to infer the existence of unauthorized records.
- Sorting performance must be compatible with MySQL 8 and cPanel Shared Hosting; sort fields should be indexed in the physical schema.

---

# 8. Pagination Standards

## 8.1 General Pagination Principles

1. **All list endpoints and views support pagination** unless the endpoint explicitly returns a small fixed set (e.g., a Student's list of Teachers, a fixed set of role options, a single detail view).
2. **Pagination applies after authorization, scope resolution, filtering, and sorting.** Hidden or unauthorized records must not affect page counts or navigation.
3. **Page size is configurable within allowed limits.** Default page size should balance usability and performance for cPanel Shared Hosting.
4. **Pagination metadata must be accurate.** Total count, current page, per-page count, and last page must reflect only the user's authorized result set.

## 8.2 Pagination Parameters

| Parameter | Description | Constraints |
|---|---|---|
| `page` | Requested page number (1-based). | Must be a positive integer. Pages beyond the last page return an empty data set, not an error. |
| `per_page` | Number of records per page. | Must be within allowed minimum and maximum limits. Exceeding the maximum returns the maximum. |

## 8.3 Pagination Response Metadata

Paginated responses include:

| Field | Description |
|---|---|
| `meta.current_page` | The page being returned. |
| `meta.per_page` | The number of records on this page. |
| `meta.total` | The total number of records matching the filter within the user's authorized scope. |
| `meta.last_page` | The total number of pages. |

## 8.4 Pagination Behavior

- Requesting a page beyond the last page returns an empty data set, not an error.
- Changing filters or sort order may change the total count and page layout.
- Total count reflects only authorized records; unauthorized records are not counted.
- Pagination must not reveal the existence of records in another Teacher Workspace, Student account, or unlinked Student relationship through count discrepancies.

## 8.5 Pagination Performance

- Pagination queries must be optimized for MySQL 8 and cPanel Shared Hosting.
- Large offset values (e.g., page 10,000) must be handled gracefully without excessive query time.
- Cursor-based pagination may be considered as a future optimization for very large datasets, but offset-based pagination is the Version 1 standard.

---

# 9. Search Performance

## 9.1 Performance Principles

Search and filtering must be optimized for the confirmed Version 1 infrastructure: Laravel 12, PHP 8.3, MySQL 8, File Cache, and cPanel Shared Hosting.

Performance guidelines:

1. **Resolve scope before searching.** The authorized scope must narrow the search space before the search term or filter is applied.
2. **Use indexed fields for search and sort.** Searchable and sortable fields should be indexed in the physical database schema.
3. **Paginate all list results.** Unbounded result sets must not be returned.
4. **Avoid full-table scans.** Search queries must use appropriate indexes and scope constraints.
5. **Limit search term length.** Extremely long search terms must be rejected or truncated to prevent query performance issues.
6. **Cache frequently accessed filter options.** Educational Grade lists, Group lists, and other selector data may be cached using File Cache where appropriate.
7. **Chunk large operations.** Background search operations (e.g., report preparation involving search) must be chunked for shared hosting compatibility.

## 9.2 Performance Constraints

- Search must not require Redis, Elasticsearch, Algolia, Meilisearch, or any external search infrastructure.
- Search must not require S3 Storage, Docker, Kubernetes, WebSockets, or Microservices.
- Search must not require unconfirmed response-time, throughput, concurrency, or capacity targets.
- Search optimization must never bypass authorization, Teacher Workspace isolation, Archive policy, or historical data retention.
- Performance must not be improved by treating archived records as active or by omitting authorization checks.

## 9.3 Full-Text Search

MySQL 8 full-text search may be used where text-based search within a column is needed. Full-text search must:

- Apply only within the user's authorized scope.
- Use appropriate MySQL 8 full-text indexes.
- Not require external search engines.
- Preserve Teacher Workspace isolation in all full-text queries.

---

# 10. Search Permissions

## 10.1 Permission Principles

Search and filtering permissions follow the same RBAC model as all other Platform operations. The permission matrix in `AI_DOCS/09_Permission_Matrix.md` governs what data a user can access; search is a mechanism for locating that data, not a separate permission category.

A user who does not have permission to view a module's records through normal navigation must not be able to discover those records through search.

## 10.2 Permission Enforcement

| Role | Search permission enforcement |
|---|---|
| **Super Admin** | May search Platform-level records within confirmed content-visibility boundaries. Search respects the PENDING Teacher-private content visibility (Q-012). |
| **Teacher** | May search only within the Teacher's own Teacher Workspace. Cross-Teacher search results are denied. |
| **Teacher Staff** | May search only within the creating Teacher Workspace and only for modules covered by Teacher-assigned permissions. |
| **Student** | May search only the Student's own records, partitioned per Teacher relationship. Another Student's records are never returned. |
| **Parent** | May search only linked Student records, in read-only mode. Unlinked Student records are never returned. |

## 10.3 Permission-Specific Search Restrictions

- **Question Bank search:** Only the owning Teacher and authorized Teacher Staff. Students, Parents, and other Teachers cannot search a Teacher's private Question Bank.
- **Lesson search:** Students can search only Lessons from their own Teachers. No cross-Teacher Lesson discovery or marketplace browsing.
- **Audit Log search:** Super Admin at Platform scope within confirmed visibility. Teacher at own workspace scope where permitted. Student and Parent have no confirmed Audit Log search surface.
- **Subscription (Flow A) search:** Super Admin at Platform scope. Teacher may view own status where authorized. Student, Parent, and Teacher Staff cannot search Flow A records.
- **Payment status (Flow B) search:** Teacher within own workspace. Student for own records. Parent for linked Student records. Super Admin for Platform-level summaries.

## 10.4 Search and Teacher Staff Permissions

Teacher Staff search access is always conditional on explicit Teacher-assigned permissions within the creating Teacher Workspace. Detailed permission granularity remains PENDING (Q-011). Search must not grant Teacher Staff access to modules beyond their assigned permissions.

---

# 11. Advanced Search

## 11.1 Definition

Advanced Search allows users to combine multiple filter criteria simultaneously to narrow results more precisely than a single text query.

## 11.2 Advanced Search Behavior

- Advanced Search accepts multiple filter criteria (e.g., Educational Grade + Group + Student + date range + status).
- All criteria are combined with AND logic; all conditions must be satisfied.
- Each criterion must pass the same validation, authorization, and scope checks as individual filters.
- Advanced Search does not bypass any authorization, Teacher Workspace, Student, Parent, or Archive boundary.
- Advanced Search results are paginated, filterable, and sortable according to the same standards as simple search.

## 11.3 Advanced Search Restrictions

- Advanced Search must not support OR logic that would cross Teacher Workspace, Student, or Parent scope boundaries.
- Advanced Search must not support queries that combine Flow A and Flow B criteria in a single ambiguous result set.
- Advanced Search must not support raw query expressions, SQL injection vectors, or unconfirmed query syntax.
- Advanced Search complexity must be bounded to prevent excessive query execution time on cPanel Shared Hosting.

## 11.4 Advanced Search Scope by Role

| Role | Advanced Search scope |
|---|---|
| **Super Admin** | Platform-level criteria within confirmed content-visibility boundaries. |
| **Teacher** | Own Teacher Workspace criteria only. |
| **Teacher Staff** | Creating Teacher Workspace criteria, limited to assigned permissions. |
| **Student** | Own records across own Teacher relationships. |
| **Parent** | Linked Student records only. |

---

# 12. Date Range Filtering

## 12.1 General Date Range Rules

Date range filtering allows users to restrict results to records within a specific time period.

- Date range filters use `from_date` and `to_date` parameters.
- Both dates are inclusive.
- Dates must be valid calendar dates.
- `from_date` must not be after `to_date`.
- Date range filters are applied after scope resolution and authorization.

## 12.2 Date Range by Module

| Module | Date range application |
|---|---|
| Attendance | Attendance date or session date. |
| Homework | Creation date, due date, or submission date where applicable. |
| Lessons | Creation date or availability date. |
| Exams | Creation date, availability date, or attempt date. |
| Exam Attempts | Attempt start or submission date. |
| Payments (Flow B) | Payment status date or period. |
| Subscriptions (Flow A) | Billing Cycle period (calendar month). |
| Audit Logs | Event timestamp. |
| Reports | Reporting period. |

## 12.3 Billing Cycle Date Filtering

Billing Cycle filtering uses calendar-month periods:

- A Billing Cycle is identified by its calendar month (e.g., "2026-08" for August 2026).
- Billing Cycle starts on the first day and ends on the last day of the same month (D-006).
- Billing Cycle filters apply only to Flow A Subscription and related Platform reports.
- Billing Cycle filters must not be used to filter Flow B payment-status records unless the context explicitly supports period-based filtering.

## 12.4 Date Range Edge Cases

- A date range that spans archived and active periods returns only active records unless historical reporting is explicitly authorized.
- A date range with no matching records returns an empty result set.
- A date range that extends beyond the Platform's data history returns only existing records.
- Future dates in date range filters are valid but may return no results.
- A Student moved Groups during the selected date range; historical records from the previous Group remain available in authorized historical contexts.

---

# 13. Status Filtering

## 13.1 General Status Filter Rules

Status filtering allows users to restrict results to records in a specific state.

- Status filter values must be valid for the resource's confirmed status model.
- Invalid status values are rejected.
- Status filters are applied after scope resolution and authorization.

## 13.2 Common Status Values

| Resource | Confirmed status concepts |
|---|---|
| All archivable resources | Active / Archived. |
| Homework | Active, submitted, graded, archived (specific values confirmed in detailed requirements). |
| Exams | Active, published, archived (specific values confirmed in detailed requirements). |
| Exam Attempts | In progress, submitted, graded, pending review (for Essay). |
| Payments (Flow B) | Recorded status values only; not payment processing states. |
| Subscriptions (Flow A) | Recorded Subscription status values; non-payment enforcement statuses remain PENDING. |
| Attendance | Status values confirmed in detailed requirements; not used for Billable Student calculation. |
| Teacher Staff | Active / Archived. |

## 13.3 Archive State Filtering

- **Active only (default):** Returns only non-archived records. This is the default for all normal searches and list views.
- **Archived only:** Returns only archived records. Available only in authorized historical/report contexts.
- **All (active and archived):** Returns both active and archived records. Available only in authorized historical/report contexts, with archived records clearly indicated.

Archive state filtering must not be used to bypass the Archive Policy. Archived records must never appear in active dropdown lists, selectors, pickers, or assignment lists (§11 Archive Policy).

## 13.4 Status Filter Restrictions

- Status filters must not reveal the existence of records in inaccessible scopes.
- Status filters must not mix Flow A and Flow B statuses.
- Non-payment enforcement statuses are not confirmed for Version 1 and must not be used as filter values until Q-005 is resolved.

---

# 14. Export Filters

## 14.1 Export Status

No report export format, download capability, print capability, scheduled delivery, email delivery, or external reporting integration is confirmed for Version 1 (`AI_DOCS/18_Reporting_Analytics.md` §15).

Therefore, Export Filters are not a confirmed Version 1 capability. The Platform must not assume CSV, spreadsheet, PDF, print, email, or automated export behavior.

## 14.2 Future Export Filter Requirements

If a future export capability is approved, the same filters, sorting, pagination, authorization, scope, Archive, and privacy rules that apply to on-screen results must also apply to exported data:

- Exported data must be limited to the user's authorized scope.
- Export must preserve Teacher Workspace isolation.
- Export must preserve Flow A / Flow B separation.
- Export must clearly identify archived/historical records.
- Export must not leak private data through filenames, output content, caching, or delivery channels.
- Export must not create a notification feature or bypass backend authorization.

---

# 15. Saved Filters (Future)

## 15.1 Saved Filter Status

Saved Filters — allowing users to store and reuse frequently used filter combinations — are not confirmed for Version 1. They are a future consideration only.

## 15.2 Future Saved Filter Requirements

If Saved Filters are approved in a future version:

- Saved Filters must store only filter criteria, not cached result data.
- Saved Filters must be re-evaluated against the user's current authorization and scope each time they are applied. A saved filter must not return results the user can no longer access.
- Saved Filters must be scoped to the user's account and role context.
- Saved Filters must not bypass Archive rules, Teacher Workspace isolation, or RBAC boundaries.
- Teacher Staff Saved Filters must respect assigned permissions.
- Saved Filters must not include unconfirmed search syntax, cross-Teacher criteria, or marketplace discovery behavior.
- Saved Filters must be included in the Audit Log where they qualify as important user-preference changes.

---

# 16. Search Logging

## 16.1 Search Logging Scope

Search operations are not individually listed as mandatory Audit Log events in the Project Context Audit Log Policy (§10.1). Therefore, individual search queries are not required to be recorded in the business Audit Log.

However, operational logging of search activity supports:

- Performance monitoring and optimization.
- Abuse detection (e.g., repeated unauthorized search attempts).
- Troubleshooting of search-related issues.

## 16.2 Operational Search Logging

Search-related operational logs may record:

- Search queries that result in authorization failures.
- Search queries that result in repeated empty results (potential abuse indicator).
- Search performance metrics (query duration, result count).
- Filter and sort operations that trigger validation failures.

Operational search logging constraints:

- Logs must not contain the full content of private Teacher Workspace records.
- Logs must not expose Student personal data, Question Bank content, Lesson content, or Homework content.
- Logs must not contain sensitive credentials or application secrets.
- Operational logs do not replace the business Audit Log.
- Logging must not introduce notification features.

## 16.3 Failed Search Authorization Logging

Repeated failed search authorization attempts (e.g., a user repeatedly attempting to access another Teacher Workspace through search) should be logged for security review. Such events may qualify as security-sensitive actions that should be recorded in the Audit Log according to the Audit Log Policy.

---

# 17. Error Handling

## 17.1 Search Error Categories

| Condition | Required handling |
|---|---|
| User is not authenticated | Deny search access. Return authentication-required response (401). |
| User lacks permission for the searched module | Deny search without exposing the existence of records (403). |
| Teacher/Teacher Staff attempts to search another Teacher Workspace | Reject the search. Do not reveal that matching records exist in another workspace. |
| Student attempts to search another Student's records | Reject the search. Do not reveal that matching records exist for another Student. |
| Parent attempts to search unlinked Student records | Reject the search. Do not reveal unlinked Student data. |
| Super Admin search exceeds PENDING content-visibility boundary | Restrict results to confirmed Platform-level information. |
| Search term is too long or contains invalid characters | Reject the search term safely (422). |
| Filter references an invalid or unauthorized record | Reject the filter without exposing the record's existence. |
| Sort field is unsupported | Reject or ignore the sort field; do not broaden the result set. |
| Date range is invalid (from_date after to_date) | Reject the date range with a validation error (422). |
| Status filter value is invalid | Reject the status filter with a validation error (422). |
| Search query exceeds performance limits | Reject or simplify the query to protect shared hosting resources. |
| Search returns no results | Return an empty result set with appropriate empty-state handling. |
| Payment filter mixes Flow A and Flow B | Reject or separate the filter to preserve financial-flow separation. |
| Archived records would appear in active search | Exclude archived records from active results; include only in authorized historical contexts. |

## 17.2 Error Response Constraints

- Error responses must not reveal the existence of records in another Teacher Workspace, Student account, or unlinked Student relationship.
- Error responses must not expose Teacher-private data, Question Bank content, Lesson content, Student personal data, or internal implementation details.
- Error responses must not contain SQL queries, database schema information, or stack traces.
- Validation errors must indicate which filter or sort parameter is invalid without exposing the full query or scope details.
- Error responses must use the standardized error structure defined in `AI_DOCS/10_API_Design.md` §6.

---

# 18. Edge Cases

The Search & Filtering system must safely handle the following confirmed or directly required scenarios:

1. **A new Teacher Workspace has no records.** Search and filtering return empty results with appropriate empty-state handling. No error is generated.

2. **A Student studies with multiple Teachers.** Search results are partitioned per Teacher. The Student can search across their own Teacher relationships, but results remain separated per Teacher.

3. **A Parent monitors multiple Students.** Search results include only the currently selected linked Student's data. The Parent must switch Students using the Student Switcher to search a different Student's data.

4. **A Student has been moved between Groups.** Historical records from the previous Group remain available in authorized historical/report contexts. Active search returns current records; historical search includes prior Group records where permitted.

5. **An Educational Grade or Group is archived.** Archived Educational Grades and Groups do not appear in active filter selectors. They appear only in historical/report contexts where explicitly authorized, clearly indicated as archived.

6. **A search term matches records in multiple Teacher Workspaces.** For Teacher and Teacher Staff roles, only matching records within the current Teacher Workspace are returned. The search must not reveal that matching records exist in other workspaces.

7. **A search term matches an unlinked Student's records.** For Parent and Student roles, only matching records within the authorized scope are returned. The search must not reveal that matching records exist for unlinked Students or other Students.

8. **A search spans archived and active records.** Active search returns only active records. Historical search returns both, with archived records clearly indicated. The default is active-only.

9. **A filter combination yields no results.** The result is an empty result set with appropriate empty-state handling. No error is generated, and no suggestion is made to broaden the search to unauthorized scope.

10. **A very large result set matches a broad search.** Results are paginated to protect shared hosting resources. Total count reflects only authorized records.

11. **A search is performed during Billing Cycle processing.** Background Subscription calculation does not affect search results. Subscription status shown in search reflects the latest recorded state.

12. **Flow A and Flow B data exist for the same Student-Teacher relationship.** Search and filtering must clearly separate Subscription (Flow A) and payment-status (Flow B) results. They must not appear in an ambiguous combined result set.

13. **A search includes records with pending Essay grading.** Exam result search shows the accurate pending state rather than inventing a grade.

14. **A search is performed by a Teacher Staff member with limited permissions.** Search returns results only for modules covered by the Teacher-assigned permissions. Modules without permission are not searchable and do not appear in search scope.

15. **A search term is a substring of a Teacher Workspace name.** The search must not reveal Teacher Workspace names or identifiers that the user is not authorized to see.

16. **A search query references an archived Student relationship.** Archived Student relationships within the Teacher Workspace are excluded from active search results. They appear only in historical/report contexts where authorized.

17. **Pagination metadata changes when filters change.** When a user changes filters or sort order, the total count and page layout may change. The system must not cache stale pagination metadata across filter changes.

18. **A search is performed on Audit Log data.** Audit Log search is immutable and append-only. Search results must not expose entries beyond the user's confirmed Audit Log visibility scope.

---

# 19. Accessibility Considerations

## 19.1 Search Accessibility

Search, filtering, sorting, and pagination must be accessible through the Web Application (BR-017). Version 1 is a Web Application only; no native mobile search behavior is defined.

Accessibility requirements:

1. **Keyboard navigation.** Search inputs, filter controls, sort controls, and pagination navigation must be operable via keyboard.
2. **Screen reader compatibility.** Search results, filter states, sort indicators, and pagination metadata must be announced to screen readers with appropriate ARIA attributes.
3. **Focus management.** When search results update, focus must be managed so that keyboard and screen-reader users can navigate to the new results.
4. **Clear labeling.** Filter controls, sort controls, and search inputs must have clear, descriptive labels that use canonical terminology.
5. **Error communication.** Search errors, invalid filters, and empty results must be communicated accessibly, not through color or visual-only cues.
6. **RTL support.** Search inputs and result displays must support right-to-left layout where Arabic-first RTL is applicable (Q-015 PENDING).

## 19.2 Accessibility Constraints

- Accessibility must not weaken authorization or data privacy boundaries.
- Accessibility must not create alternate paths for unauthorized data access.
- Search accessibility improvements must remain within the confirmed Web Application scope.
- Specific accessibility conformance levels are not confirmed in the Project Context and must not be invented.

---

# 20. Future Improvements

The following are future considerations only and are not Version 1 commitments:

| Future area | Required future decision |
|---|---|
| Full-text search engine | Approve Elasticsearch, Meilisearch, or similar only after infrastructure approval. Version 1 uses MySQL 8 only. |
| Autocomplete / typeahead search | Approve behavior, performance impact, and privacy controls for real-time search suggestions. |
| Saved Filters | Approve storage, sharing, scoping, and RBAC integration for reusable filter presets. |
| Cross-module search | Approve expanded Global Search that spans more modules while preserving all authorization boundaries. |
| Export with filters | Approve export formats, delivery, privacy controls, and filter-parity rules for data export. |
| Advanced query syntax | Approve Boolean operators, wildcards, phrase search, and field-specific search while preventing abuse and scope bypass. |
| Search analytics | Approve search-term analytics, popular-search tracking, and zero-result analysis without exposing private data. |
| Cursor-based pagination | Approve cursor-based pagination for very large datasets while preserving offset-based pagination as default. |
| Multi-column sorting | Approve multi-column sort behavior and UI while preserving single-column sorting as default. |
| Search result highlighting | Approve keyword highlighting in search results while preserving data privacy. |
| Regional search behavior | Resolve language, timezone, currency, and market requirements (Q-015) before regional search formatting is introduced. |
| Mobile search experience | Approve native mobile search behavior only after native mobile application is separately approved. |

All future improvements must preserve Teacher Workspace isolation, one global Student account, Parent linked-Student read-only access, Flow A / Flow B separation, Archive instead of permanent deletion, historical retention, immutable Audit Log records, and cPanel Shared Hosting compatibility (or separately approved infrastructure).

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority. |
| Requested section coverage | Passed — all 20 requested Search & Filtering sections are present. |
| RBAC integration | Passed — search permissions follow the RBAC model and Permission Matrix. Teacher own-Workspace, Student self-scope, Parent linked-Student read-only, Teacher Staff assigned-permission, and Super Admin Platform-scope boundaries are preserved. |
| Teacher Workspace isolation | Passed — all search and filtering operations preserve tenant isolation. Cross-Teacher discovery is prevented. |
| Student scope | Passed — Student search returns only the Student's own records, partitioned per Teacher. Duplicate Student account prevention is not affected by search. |
| Parent scope | Passed — Parent search returns only linked Student records in read-only mode. Unlinked Students are never discoverable. |
| Super Admin scope | Passed — Platform-level search is supported; Teacher-private content visibility remains PENDING. |
| Archive policy | Passed — active search returns only active records. Archived records appear only in authorized historical contexts, clearly indicated. No hard deletion is referenced. |
| Flow A / Flow B separation | Passed — Subscription and payment-status filtering and results remain separate. No ambiguous combined financial results. |
| Filtering consistency | Passed — common filters are defined with consistent behavior across modules. Filter validation, scope enforcement, and empty-result handling are specified. |
| Sorting consistency | Passed — ascending/descending sort is defined. Sort field validation, scope enforcement, and Archive-aware behavior are specified. |
| Pagination consistency | Passed — all lists support pagination. Pagination metadata, performance constraints, and scope-aware counting are specified. Consistent with `AI_DOCS/10_API_Design.md` §7. |
| Performance | Passed — search is optimized for MySQL 8 and cPanel Shared Hosting. No Redis, Elasticsearch, S3, Docker, Kubernetes, WebSockets, or Microservices are required. |
| Date range filtering | Passed — date ranges, Billing Cycle filtering, and edge cases are defined. |
| Status filtering | Passed — status values, Archive state filtering, and non-payment enforcement PENDING status are preserved. |
| Export filters | Passed — no unconfirmed export capability is introduced. Future export must reapply authorization. Consistent with `AI_DOCS/18_Reporting_Analytics.md` §15. |
| Saved filters | Passed — not confirmed for Version 1. Future requirements preserve all authorization boundaries. |
| Search logging | Passed — individual searches are not mandatory Audit Log events. Operational logging and security logging are defined. Consistent with `AI_DOCS/11_Backend_Architecture.md` §18. |
| Error handling | Passed — all error categories are specified with safe handling that does not expose unauthorized data. Consistent with `AI_DOCS/10_API_Design.md` §6. |
| Accessibility | Passed — keyboard, screen-reader, focus, labeling, RTL, and error-communication requirements are specified within confirmed scope. |
| Version 1 exclusions | Passed — no marketplace discovery, cross-Teacher browsing, notification triggers, payment processing, native mobile, Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices are introduced. |
| Terminology | Passed — Teacher Workspace, Educational Grade, Group, Student, Parent, Teacher Staff, Super Admin, Subscription, payment status, Flow A, Flow B, Enrollment, Archive, Audit Log, Billable Student, Billing Cycle, Lesson, Question Bank, Homework, Bubble Sheet, and Dynamic QR Code are used consistently. |
| No source code | Passed — no source code, APIs, SQL, database tables, UI implementation, or physical search-index structures are defined. |
