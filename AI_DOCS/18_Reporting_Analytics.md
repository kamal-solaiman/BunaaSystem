# 18 — Reporting & Analytics

## Document Scope

This document defines the confirmed Version 1 Reporting & Analytics System for the Unified Education Platform. It describes report scope, role visibility, reporting domains, historical behavior, filtering/sorting boundaries, and large-dataset principles.

It does not provide source code, APIs, SQL, database tables, UI implementation, export-file implementation, dashboard design, or unconfirmed analytics behavior. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails over this document if a conflict is found.

The Reporting & Analytics System is delivered through the React 19 Web Application and Laravel 12 backend. Laravel remains authoritative for authentication, RBAC, Teacher Workspace scope, Parent linked-Student checks, Student self scope, Archive/history behavior, and data retrieval. Reporting presentation must never become a path around backend authorization.

---

# 1. Feature Overview

Reports provide authorized operational and historical information without weakening Teacher Workspace isolation or privacy boundaries.

Version 1 supports:

- Teacher Workspace reports for Attendance, Homework, Exam results, Flow B payment status, and Student performance.
- Student summary reporting for the Student’s own per-Teacher records.
- Parent linked-Student read-only summary reporting.
- Super Admin Platform-level reports for Teacher administration, Flow A Subscriptions, pricing, payment status, and other confirmed Platform-level information, subject to the PENDING Teacher-private content visibility boundary.
- Historical report visibility for archived data, clearly marked as archived/historical where included.

Reporting is not a payment-processing system, public data catalog, marketplace, cross-Teacher browsing feature, notification feature, or unrestricted analytics surface.

---

# 2. Objectives

The confirmed objectives are to:

1. Support Teacher operational review and decision-making in the Teacher’s own Teacher Workspace.
2. Give Students access to their own report summary, separated by Teacher relationship where applicable.
3. Give Parents read-only reporting for linked Students only.
4. Give the Super Admin Platform-level reporting within confirmed content-visibility boundaries.
5. Include historical data while clearly identifying archived records.
6. Preserve Teacher Workspace isolation, Student self scope, Parent linked-Student scope, and Teacher Staff assigned-permission boundaries.
7. Support valid date/period, Educational Grade, Group, Student, Teacher, status, Exam, and Billing Cycle criteria where applicable to the report domain.
8. Keep Flow A Subscription reporting separate from Flow B payment-status reporting.
9. Support scalable, paginated, filtered, and scoped report access without requiring unconfirmed infrastructure.
10. Avoid exposing private Teacher-owned Lessons, Question Banks, Homework content, individual Exam definitions, or workspace-private Student records outside confirmed access boundaries.

---

# 3. Reporting Principles

1. **Authorization first:** Every report request is authorized for the current user, role, record relationship, and scope.
2. **Teacher Workspace isolation:** Teacher and Teacher Staff reports include only the creating Teacher Workspace’s records.
3. **Relationship scope:** Student reports include only the authenticated Student’s own records; Parent reports include only the selected linked Student’s records.
4. **Platform scope with constraints:** Super Admin reports are Platform level, but Teacher-private content visibility remains PENDING and cannot be silently expanded.
5. **History is retained:** Historical data is never permanently deleted. Reports include archived records where historical/reporting rules require them and clearly mark them as archived.
6. **No money-flow conflation:** Flow A Subscription and Flow B payment status remain separate in report labels, calculations, filters, sections, and summaries.
7. **Status-only payments:** Reports may show recorded payment status but never process payments.
8. **No unauthorized inference:** Counts, suggestions, filters, result rows, empty states, and errors must not reveal another Teacher Workspace, unlinked Student, or protected record.
9. **Safe performance:** Scope is resolved before filtering, sorting, pagination, aggregation, or presentation.
10. **No invented analytics:** Metrics, trends, forecasts, scores, export formats, and other analytics are included only where confirmed.

---

# 4. Teacher Reports

Teachers may view reports only for the Teacher’s own Teacher Workspace. Teacher Staff may view a report only when the Teacher assigned the relevant permission; Teacher Staff report access never extends outside the creating Teacher Workspace.

Confirmed Teacher report domains are:

| Report domain | Confirmed purpose |
|---|---|
| Attendance | Review Teacher Workspace Attendance records and history. |
| Homework | Review Teacher Workspace Homework activity and history. |
| Exam results | Review Teacher Workspace Exam results, attempts, and grades within permitted scope. |
| Payments | Review Flow B Student fee **payment status** relevant to Teacher Workspace operations. |
| Student performance | Review Student performance based on Teacher Workspace records. |

Teacher report criteria may include date/period, Educational Grade, Group, Student, and status where applicable. Exam result reports may use Exam criteria where applicable. All filters and report rows remain Teacher Workspace scoped.

Teacher reports may show the Teacher’s own Flow A Subscription information only where separately authorized, but it must remain clearly distinct from Teacher Workspace Flow B payment-status reporting.

---

# 5. Student Reports

A Student may view a summary report for the Student’s own records only.

Confirmed Student report rules:

- Student report content is limited to the authenticated Student’s own per-Teacher records.
- A Student may use Teacher and period criteria only for the Student’s own Teacher relationships.
- Report information remains separated by Teacher where the Student studies with multiple Teachers.
- The Student may not view another Student’s Attendance, Homework, Exams, grades, payment status, or report summary.
- The Student does not gain access to a Teacher’s private Question Bank, Teacher-private content, or Teacher Workspace reports through reporting.
- Historical information may be available where permitted and must be identified as historical/archived where applicable.

The detailed Student report metric set beyond the confirmed self-summary boundary is not defined and must not be invented.

---

# 6. Parent Reports

A Parent may view a read-only summary report for a selected linked Student only.

Confirmed Parent report rules:

- The Parent must be authenticated and the selected Student must be linked to that Parent.
- Parent report information is limited to the linked Student’s records and is separated by Teacher where applicable.
- Parent reporting is read-only everywhere.
- The Parent cannot modify Attendance, Homework, Exams, grades, payment status, Student records, Teacher records, or Teacher Workspace records from a report.
- The Parent cannot access reports for an unlinked Student or use report criteria to discover another Student’s information.
- Historical linked-Student information may be visible where permitted and is clearly identified as archived/historical where applicable.

The detailed Parent summary metric set is not confirmed beyond the Parent’s linked-Student monitoring capabilities and must not be expanded into a Teacher Workspace report surface.

---

# 7. Platform Reports

The Super Admin may view global reports at Platform level for confirmed Platform administration, Teacher management, Flow A Subscriptions, pricing, and payment-status information.

Platform report rules:

- Super Admin report criteria are Platform scoped, including report type, date, Billing Cycle, Teacher, Subscription, payment-status, pricing, and Archive criteria where applicable.
- Platform reports may include Teacher-related summaries, Flow A Subscription reporting, and pricing/payment-status reporting within confirmed Platform scope.
- Historical and archived records remain available where required and are clearly indicated.
- Flow A and Flow B remain separate. Flow B status is never treated as Platform revenue.
- Teacher Workspace isolation remains mandatory in every output.
- Teacher-private content visibility is PENDING. Platform reporting must not assume unrestricted access to Lesson videos, Question Bank content, Homework content, individual Exam definitions, or workspace-private Student records.

“Platform-wide” does not mean unrestricted Teacher-private content browsing. Until a visibility decision is confirmed, Platform reports remain non-invasive and within the documented boundary.

---

# 8. Attendance Reports

Attendance reports provide authorized Attendance history and operational review.

| Role | Permitted report/view boundary |
|---|---|
| Teacher | Attendance reports for the Teacher’s own Teacher Workspace. |
| Teacher Staff | Attendance reports only with Teacher-assigned permission in the creating Teacher Workspace. |
| Student | The Student’s own Attendance where available, partitioned by Teacher. |
| Parent | Linked Student Attendance read-only, partitioned by Teacher where applicable. |
| Super Admin | Platform attendance report-summary visibility is conditional on confirmed reporting/content-visibility boundaries. |

Attendance report criteria may include date/period, Group, Student, and Attendance status where applicable. Historical Attendance remains available after Student Group movement. Archived Attendance-related records may be included for historical reporting and must be clearly identified.

Attendance reports must not calculate, infer, or display Billable Student eligibility from Attendance. Flow A Billable Student calculation uses Enrollment duration only.

---

# 9. Homework Reports

Homework reports provide authorized visibility into Teacher Workspace Homework activity and history.

- Teachers may review Homework reports only in their own Teacher Workspace.
- Teacher Staff access requires the relevant Teacher-assigned report permission.
- Student self-summary may include only Homework assigned to the Student through the Student’s Teacher relationships.
- Parent summary may include only linked-Student Homework information in read-only form.
- Homework report criteria may include date/period, Group, Student, and status where applicable.
- Archived Homework remains historical and must not appear as active Homework.
- Homework reports must not expose Teacher-private content or Student submissions outside their authorized Teacher Workspace, Student-self, or Parent-linked scope.
- Homework reporting does not add video homework; Version 1 Homework formats remain Text, Image, and PDF only.

The exact Homework report metrics, review scores, submission aggregation, and export behavior are not confirmed.

---

# 10. Exam Reports

Exam reports provide authorized visibility into Teacher Workspace Exam results and Student performance.

- Teachers may review Exam result reports for their own Teacher Workspace only.
- Teacher Staff may view Exam reports only with explicit Teacher-assigned permission.
- Students may view their own attempt status and grade information where available through their own Teacher relationships.
- Parents may view available linked-Student Exam information, attempt status, and grades read-only.
- Exam report criteria may include date/period, Group, Student, and Exam where applicable.
- Exam attempts and grades remain associated with the owning Teacher Workspace and remain historically available after Student Group movement.
- Archived Exams may be available as historical records but must not be represented as active Exams.
- Reports must not expose a Teacher’s private Question Bank content outside authorized Exam visibility.
- Pending Essay grading is reported as unavailable/pending where applicable; no result is fabricated.

Exam reports do not define pass/fail thresholds, aggregate grade formulas, ranking, randomization analytics, or unconfirmed assessment analytics.

---

# 11. Payment Reports

Payment reports describe **recorded payment status only**; they do not process payments.

## Flow B payment-status reports

Teacher Workspace payment reports primarily support Flow B Student/Parent-to-Teacher fee status derived from Group Enrollment, Price, and Pricing Type. They are available only within the relevant Teacher Workspace and may use date/period, Group, Student, and status criteria where applicable.

Students may view their own Flow B payment status by Teacher. Parents may view Flow B payment status for linked Students only, in read-only mode.

## Flow A payment-status reports

Platform-level Flow A payment-status reporting belongs to the Super Admin’s Subscription/Billing scope. It concerns Teachers’ Platform Subscriptions and remains Platform scoped.

Rules for every payment report:

- Clearly identify whether the report is Flow A Subscription payment status or Flow B Student fee payment status.
- Never present Flow B as Platform revenue.
- Never label Flow B Student/Parent fee status as a Teacher Subscription.
- Never initiate, collect, or process a transaction.
- Treat incomplete status as recorded status only because actual payments occur outside the Platform.

---

# 12. Subscription Reports

Subscription reports are Flow A Platform-level reports for the Super Admin and, where separately authorized, own-status information for a Teacher.

Confirmed Subscription report content includes:

- Teacher Subscription records.
- Calendar-month Billing Cycle context.
- Billable Student count per Teacher for the Billing Cycle.
- Subscription amount based on Billable Students × Price Per Student.
- Recorded Subscription payment status.
- Historical Subscription status overview.
- Historical price context, because historical invoices retain the price applicable to their period.

Subscription reporting rules:

- Billable Students are calculated from Enrollment duration only.
- A Student enrolled more than 15 calendar days during the Billing Cycle is Billable; 15 days or less is not counted.
- Attendance and login activity are excluded.
- Pricing is Super Admin-owned; flat price versus volume tiers remains PENDING.
- Flow A remains separate from Flow B.
- Historical Subscription records are retained; no permanent deletion occurs.
- Non-payment enforcement, Grace Period, suspension, and reactivation remain PENDING and must not appear as confirmed report states.

---

# 13. Group Reports

Groups are Teacher Workspace resources organized under Educational Grades. Group reporting is a valid Teacher Workspace reporting dimension where applicable.

Confirmed Group report rules:

- Teachers and authorized Teacher Staff may filter/report within their own Teacher Workspace Groups only.
- Group reports may support Attendance, Homework, Exam results, Flow B payment status, and Student performance where the report domain applies.
- A Group must not be used to expose another Teacher Workspace’s Students or records.
- A Student belongs to only one Group per Teacher at a time.
- Student movement between Groups preserves historical Attendance, Homework, Exams, and grades; reports must preserve the applicable historical context.
- Archived Groups are not active assignment/report-selection options unless historical reporting or authorized restoration applies; archived records are clearly indicated.

Group report layout, aggregate metrics, comparison behavior, and Group ranking are not confirmed.

---

# 14. Grade Reports

In this document, **Grade Reports** means reporting filtered or organized by the canonical **Educational Grade**. It does not introduce a new grading/marking system.

Confirmed Educational Grade report rules:

- Educational Grades are Teacher-created and exist only in the Teacher Workspace that created them.
- Teachers and authorized Teacher Staff may use Educational Grade criteria only within the current Teacher Workspace.
- Educational Grade is applicable as a report filter for Teacher Workspace Attendance, Homework, Exam result, payment-status, and Student performance reporting where relevant.
- Educational Grade is independent from a Teacher’s Teaching Subject.
- Archived Educational Grades do not appear as active selection options; historical reports may include them when clearly marked as archived.
- Educational Grade reporting must not reveal other Teacher Workspaces’ academic structures or records.

This section does not define grade bands, grading scales, pass/fail criteria, or Exam score calculations.

---

# 15. Export Rules

No report export format, download capability, print capability, scheduled delivery, email delivery, or external reporting integration is confirmed for Version 1.

Therefore, Version 1 Reporting & Analytics must not assume CSV, spreadsheet, PDF, print, email, or automated export behavior. If a future export capability is approved, it must:

- Reapply the same role, Teacher Workspace, Student self, Parent linked-Student, Archive, and Super Admin visibility checks as the originating report.
- Preserve Flow A / Flow B separation.
- Clearly identify archived/historical records.
- Avoid private data leakage through filenames, output content, caching, or delivery channels.
- Not create a notification feature or bypass backend authorization.

---

# 16. Filtering Rules

Filtering is applied only after authentication, authorization, and scope resolution.

| Scope | Confirmed applicable filters |
|---|---|
| Teacher Workspace reports | Date/period, Educational Grade, Group, Student, status, and report-specific criteria such as Exam where applicable. |
| Student self-summary | Teacher and period filters limited to the Student’s own Teacher relationships. |
| Parent linked-Student summary | Selected linked Student, Teacher, and period filters limited to that Parent relationship. |
| Platform reports | Report type, date, Billing Cycle, Teacher, Subscription, payment-status, pricing, and Archive criteria where applicable and within confirmed visibility boundaries. |

Filtering rules:

1. Date range/period, Educational Grade, and Group criteria are supported where applicable to the report domain and role scope.
2. Every criterion must reference a record within the authorized scope.
3. Cross-Teacher, unlinked Student, or unrelated Teacher relationship filters are rejected.
4. Archived records may be included only for historical/reporting context and must be clearly identified.
5. Filtering must not make Flow A and Flow B appear in one ambiguous result set.
6. Unsupported or invalid filters are rejected safely without exposing data.

Exact date-range limits, default periods, saved filters, advanced search syntax, and cross-report filter persistence are not confirmed.

---

# 17. Sorting Rules

Reports support sorting only through supported and authorized report fields.

- Sorting is applied after scope resolution, authorization, and filtering.
- Sorting must not expose data outside the role’s permitted result set.
- Supported sorting fields must be relevant to the report domain and its authorized output.
- Sorting must not cause archived records to be presented as active.
- Sorting must preserve Flow A / Flow B separation.
- Large report results are paginated; sorting is applied to the authorized report set before its pages are presented.

The exact sortable field catalog, default order, multi-column sorting behavior, and user-saved sort preference are not confirmed and are not defined here.

---

# 18. Dashboard Statistics

Dashboards may present role-appropriate operational summaries; they do not replace detailed reports.

| Role | Confirmed dashboard-statistic boundary |
|---|---|
| Teacher | Teacher Workspace summaries related to Students, Groups, Attendance, Homework, Exams, Lessons, Reports, Users, Settings, and permitted payment-status information. |
| Teacher Staff | Only the Teacher Workspace summaries covered by explicitly assigned permissions. |
| Student | The Student’s own summaries, including per-Teacher schedule, Homework, Lessons, Exams, and Flow B status where available. |
| Parent | Linked-Student read-only summaries, with clear selected Student context. |
| Super Admin | Platform-level administration and global report summaries within confirmed content-visibility boundaries. |

Dashboard-statistic rules:

- Show only scoped, backend-returned information.
- Clearly distinguish active values from archived/historical values.
- Keep Flow A Subscription information separate from Flow B payment status.
- Do not calculate Billable Students from Attendance, login, or dashboard activity.
- Do not create notification badges, predictions, rankings, or unconfirmed analytics.
- A dashboard must not expose Teacher-private content to the Super Admin while visibility remains PENDING.

---

# 19. Audit Reporting

Audit reporting concerns the immutable, append-only, permanent Audit Log.

| Role | Confirmed Audit Log reporting boundary |
|---|---|
| Super Admin | May view Platform-scope Audit Logs within confirmed visibility boundaries. Teacher Workspace event visibility is conditional and must not expose private content by default. |
| Teacher | Teacher Workspace Audit Log visibility is conditional and limited to the Teacher’s own Teacher Workspace where permitted by requirements. |
| Teacher Staff | No separate confirmed Audit Log report surface; actions remain attributed to the Teacher Staff user. |
| Student | Audit Log visibility is not a confirmed Version 1 product surface. |
| Parent | Audit Log visibility is not a confirmed Version 1 product surface. |

Audit reporting rules:

- Audit entries are never editable, archived, restored, or deleted.
- Audit report filters and output remain role and scope constrained.
- Subscription, Attendance, Exam, Homework, Archive, restore, login, and permission-change events are included only according to the Audit Log policy and authorized visibility.
- Audit reporting must not become a route to Teacher-private content beyond confirmed boundaries.

---

# 20. Performance Considerations

Reporting must support large datasets without weakening correctness, privacy, historical availability, or the shared-hosting Version 1 architecture.

1. Resolve role, authorization, Teacher Workspace, Student self, Parent linked-Student, and Platform scope before report retrieval.
2. Use pagination for list and report results; do not require unbounded record sets to be retrieved or presented at once.
3. Apply supported filtering and sorting only to the authorized scoped result set.
4. Use report-specific criteria to reduce result volume where appropriate.
5. Keep Teacher Workspace reports isolated and avoid cross-tenant retrieval for convenience or performance.
6. Retain historical/archived report availability without treating archived records as active.
7. Keep Flow A and Flow B data logically separate in report retrieval and presentation.
8. Use Laravel 12, MySQL 8, File Cache, Database Queue, and Laravel Scheduler only within the confirmed cPanel Shared Hosting baseline where report preparation is appropriate.
9. Do not require Redis, WebSockets, S3 Storage, Docker, Kubernetes, microservices, or external search infrastructure for Version 1 reporting.
10. Do not define unconfirmed response-time, throughput, concurrency, capacity, or report-size targets.

Performance optimization must never bypass authorization, scope resolution, Archive policy, Audit Log obligations, or historical data retention.

---

# 21. Error Handling

| Condition | Required handling |
|---|---|
| User lacks permission for a report | Deny access without exposing private data. |
| Teacher/Teacher Staff requests another Teacher Workspace report | Reject the request. |
| Student requests another Student’s or unrelated Teacher’s report | Reject the request. |
| Parent selects an unlinked Student | Reject the request without exposing report data. |
| Super Admin report exceeds PENDING content-visibility boundary | Deny or restrict the output to confirmed Platform-level information. |
| Filter is invalid or outside scope | Reject the filter safely. |
| Sort is unsupported or invalid | Reject or ignore it safely without broadening the result set. |
| Report data unavailable | Provide a safe unavailable or empty result without technical details. |
| Report has no records for valid criteria | Provide an empty result, not an error. |
| Payment report mixes Flow A and Flow B | Prevent misleading output and preserve financial-flow separation. |
| Payment processing is requested from a report | Reject because Version 1 records status only. |
| Archived data is treated as active | Correct/reject the output; archival state must be clear. |

Report errors must not expose another Teacher Workspace, unlinked Student, private Question Bank, private Lesson, credentials, internal implementation details, or raw backend errors.

---

# 22. Edge Cases

The Reporting & Analytics System must safely handle these confirmed or directly required cases:

1. A new Teacher Workspace has no Attendance, Homework, Exams, Students, Groups, payment-status records, or reports for the selected period.
2. A Teacher Staff user has permission for one report domain but not another.
3. A Student studies with multiple Teachers; self-summary data remains partitioned by Teacher.
4. A Parent monitors multiple Students; only the selected linked Student’s report data is shown.
5. A Student moves Groups during the reporting period; historical Attendance, Homework, Exams, and grades remain available in their relevant historical context.
6. A report includes archived Educational Grades, Groups, Students, Homework, Exams, or Teacher records; archival state is clearly indicated.
7. An Exam has pending Essay grading; the report shows result availability accurately rather than inventing a grade.
8. A Teacher has Flow B payment-status records while the Super Admin has Flow A Subscription records; the reports remain separate.
9. A payment status is incomplete because actual payment occurred outside the Platform.
10. A Teacher has no Billable Students for a Billing Cycle; the Flow A Subscription report reflects the confirmed calculation basis.
11. A Super Admin requests Teacher-private report content while the content-visibility boundary remains unresolved.
12. A valid report filter yields no records; the result is an empty state, not an authorization error.

---

# 23. Future Improvements

The following are future considerations only and are not Version 1 commitments:

| Future area | Required future decision |
|---|---|
| Exporting | Approve formats, delivery, privacy controls, and report-specific authorization behavior. |
| Advanced analytics | Approve metrics, trends, forecasting, comparisons, ranking, and data-interpretation rules. |
| Dashboards | Approve additional indicators without creating notifications or unauthorized data visibility. |
| Super Admin visibility | Resolve Teacher-private content/report visibility before expanding Platform reports. |
| Search and filtering | Approve advanced search, saved filters, and cross-report criteria while preserving scope. |
| Background preparation | Define permissible report preparation/queue behavior under cPanel Shared Hosting limits. |
| Infrastructure | Consider advanced cache, queue, or search infrastructure only after separate approval; Version 1 does not require Redis, WebSockets, S3 Storage, Docker, Kubernetes, or microservices. |
| Localization | Resolve language, timezone, currency, and market requirements before regional report formatting is introduced. |
| Payment analytics | Consider only after preserving Flow A/Flow B separation and the external-payment status-only Version 1 model. |

All future improvements must preserve Teacher Workspace isolation, global Student identity, Parent linked-Student read-only access, private Teacher-owned Lessons and Question Banks, Archive instead of permanent deletion, historical retention, immutable Audit Log records, and Flow A / Flow B separation.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority. |
| Requested section coverage | Passed — all 23 requested Reporting & Analytics sections are present. |
| Role and RBAC boundaries | Passed — Teacher own-Workspace, Teacher Staff assigned-permission, Student self, Parent linked-Student read-only, and Super Admin Platform-scope boundaries are preserved. |
| Super Admin reporting | Passed — Platform reports are supported but Teacher-private content visibility remains PENDING; “platform-wide” is not interpreted as unrestricted private-content access. |
| Reporting domains | Passed — Teacher Attendance, Homework, Exam results, Flow B payment status, Student performance, Flow A Subscription, Group, Educational Grade, audit, and approved role-summary report boundaries are covered. |
| Filtering and sorting | Passed — date/period, Educational Grade, Group, and other documented criteria are supported where applicable only after scope resolution; pagination protects large datasets. |
| Historical data | Passed — archived/historical records remain reportable where required and are clearly indicated, never treated as active. |
| Financial separation | Passed — Flow A Subscription and Flow B payment-status reports remain distinct; reports never process payments. |
| Performance | Passed — large-dataset guidance uses scoped filtering, sorting, pagination, and cPanel-compatible baseline without unconfirmed targets or infrastructure. |
| Export scope | Passed — no unconfirmed export capability is introduced. |
| Scope | Passed — no source code, APIs, SQL, database tables, UI implementation, or unconfirmed analytics behavior is defined. |
| Terminology | Passed — Teacher Workspace, Educational Grade, Group, Student, Parent, Subscription, payment status, Flow A, Flow B, Archive, and Audit Log are used consistently. |
