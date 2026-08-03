# 21 — Background Jobs

## Document Scope

This document defines all confirmed Version 1 background jobs and scheduled tasks for the Unified Education Platform. It describes queue strategy, scheduled tasks, job responsibilities, retry and failure behavior, priorities, monitoring, logging, performance considerations, error handling, and edge cases.

This document does not define source code, APIs, database tables, UI implementation, migration scripts, or physical configuration. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found.

The background processing architecture is built on the confirmed Version 1 technology baseline: **Laravel Database Queue** for queued work and **Laravel Scheduler with Cron Jobs** for scheduled tasks. Both are compatible with **cPanel Shared Hosting** and do not require Redis, Docker, Kubernetes, S3 Storage, WebSockets, or Microservices.

---

# 1. Feature Overview

Background jobs handle deferred, periodic, and scheduled work that does not need to execute synchronously within the user request lifecycle. The background processing system supports:

- **Monthly Subscription processing** under Flow A, including Billing Cycle management and Billable Student calculation.
- **Attendance cleanup** for expired Dynamic QR Code contexts and related maintenance.
- **Exam result processing** for automatic grading of objective question types, including Bubble Sheet.
- **Report preparation** for deferred or aggregated reporting work.
- **File cleanup** for managing storage references and maintaining Laravel Public Storage consistency.
- **Audit Log maintenance** for ensuring permanent retention and operational integrity.
- **Notification processing** as a future placeholder; notifications are out of scope for Version 1.

Background jobs must preserve all confirmed business rules, including Teacher Workspace isolation, one global Student account, Parent linked-Student read-only access, Flow A / Flow B separation, Archive instead of permanent deletion, and permanent Audit Log retention.

---

# 2. Objectives

The confirmed objectives of the background processing system are to:

1. Use **Laravel Database Queue** as the official Version 1 queue mechanism, compatible with cPanel Shared Hosting.
2. Use **Laravel Scheduler with Cron Jobs** for all scheduled tasks, triggered through the cPanel Cron Job configuration.
3. Support monthly Flow A Subscription calculations based on Billable Students and Enrollment duration only.
4. Support automatic grading of objective Exam question types, including Bubble Sheet, without blocking the Student submission request.
5. Support deferred report preparation where report generation is too expensive for synchronous request handling.
6. Support periodic cleanup and maintenance tasks that preserve data integrity without permanent deletion.
7. Ensure all background processing preserves **Teacher Workspace isolation** and authorization context.
8. Ensure jobs are **idempotent** whenever possible so re-execution does not produce duplicate or inconsistent results.
9. Ensure **failed jobs are logged** and available for review without exposing private Teacher Workspace data.
10. Keep Flow A Subscription processing separate from Flow B payment-status tracking.
11. Avoid introducing notifications, payment processing, native mobile behavior, marketplace features, or unconfirmed infrastructure.

---

# 3. Background Processing Principles

The following principles govern all background jobs and scheduled tasks in Version 1:

1. **Database Queue only.** Version 1 uses the Laravel Database Queue driver. Redis, Amazon SQS, and other external queue drivers are not required.
2. **Cron-triggered Scheduler.** The Laravel Scheduler is triggered by cPanel Cron Jobs. There is no daemon or supervisor process assumed on the shared hosting environment.
3. **Idempotent by design.** Jobs must produce the same result whether executed once or multiple times. Re-processing must not create duplicate records, duplicate Billing Cycle entries, duplicate Audit Log entries, or inconsistent state.
4. **Workspace scope preserved.** Every job that operates on Teacher Workspace data must carry and enforce the Teacher Workspace context. A background job must never access another Teacher's data.
5. **Authorization context preserved.** Jobs that operate on behalf of a user must carry the user's authorization context or operate under a well-defined system-level authorization that does not bypass role boundaries.
6. **Archive over deletion.** Background jobs must never permanently delete data. Cleanup tasks use Archive where removal from active use is required.
7. **Audit Log obligations preserved.** If a background job performs an action that qualifies as an important action under the Audit Log Policy, the job must record the Audit Log entry. If the audit recording fails, the business action must not be considered complete.
8. **Flow A / Flow B separation.** Background processing of Subscription data (Flow A) must remain completely separate from Student fee status data (Flow B).
9. **cPanel Shared Hosting compatible.** Jobs must respect shared hosting resource limits, execution time limits, and memory constraints. Long-running tasks must be chunked or batched.
10. **No unconfirmed features.** Background jobs must not introduce notifications, payment gateway processing, marketplace behavior, video homework processing, or native mobile features.

---

# 4. Queue Strategy

## 4.1 Queue Driver

Version 1 uses the **Laravel Database Queue** driver. Jobs are stored in the MySQL 8 database and processed by a queue worker triggered through the Laravel Scheduler or a cPanel-compatible process.

| Concern | Version 1 Standard |
|---|---|
| Queue driver | Database |
| Database | MySQL 8 |
| Worker trigger | Laravel Scheduler / cPanel Cron Jobs |
| External dependencies | None — no Redis, SQS, or Beanstalkd |
| Hosting compatibility | cPanel Shared Hosting |

## 4.2 Queue Names

Logical queue names should separate work by priority and domain to prevent low-priority work from blocking critical tasks:

| Queue name | Purpose | Priority |
|---|---|---|
| `default` | General background work | Medium |
| `billing` | Flow A Subscription and Billing Cycle processing | High |
| `grading` | Exam automatic grading and Bubble Sheet processing | High |
| `reports` | Deferred report preparation | Low |
| `cleanup` | File reference cleanup and maintenance | Low |
| `audit-support` | Non-critical Audit Log enrichment | Medium |

The actual queue name values are implementation decisions; the names above represent logical separation guidance only.

## 4.3 Worker Execution

On cPanel Shared Hosting, the queue worker is triggered through a Cron Job that runs the Laravel queue processing command at a regular interval. The worker processes pending jobs from the database queue table.

Worker constraints:

- The worker must not require a persistent daemon process.
- Execution must respect cPanel's process execution time limits.
- Long-running jobs must be chunked or use Laravel's batchable jobs pattern where appropriate.
- The worker must not consume resources that degrade user-facing request performance.

## 4.4 Job Payload Constraints

- Job payloads must not contain sensitive credentials, raw passwords, or application secrets.
- Job payloads should carry references (IDs) rather than full model objects to reduce database queue table size.
- Job payloads must carry Teacher Workspace context where the job operates on workspace-scoped data.
- Job payloads must not contain Teacher-private content such as Question Bank data, Lesson video content, or Student submission files.

---

# 5. Scheduled Tasks

Scheduled tasks are managed through the Laravel Scheduler and triggered by cPanel Cron Jobs. The Scheduler runs at a configured interval (for example, every minute) and dispatches tasks based on their defined schedule.

## 5.1 Scheduled Task Summary

| Task | Schedule | Description |
|---|---|---|
| Billing Cycle Initialization | First day of each calendar month | Starts a new Billing Cycle and prepares Subscription records. |
| Billable Student Calculation | After Billing Cycle initialization, then periodically during the cycle | Calculates Billable Students per Teacher based on Enrollment duration. |
| Subscription Snapshot Generation | Last day of each calendar month | Generates the immutable Subscription snapshot for the completed Billing Cycle. |
| Expired QR Context Cleanup | Daily | Cleans up expired Dynamic QR Code Attendance contexts. |
| Exam Auto-Grading Queue Processing | Periodic (every 5 minutes) | Processes pending automatic grading jobs for objective question types. |
| Deferred Report Processing | Periodic (every 15 minutes) | Processes queued report preparation jobs. |
| File Reference Integrity Check | Weekly | Verifies that file references in the database point to existing files in Laravel Public Storage. |
| Audit Log Retention Verification | Monthly | Verifies that Audit Log entries remain intact and append-only. |
| Queue Table Maintenance | Weekly | Cleans up successfully processed job records from the database queue table. |

## 5.2 Cron Job Configuration

The cPanel Cron Job configuration must run the Laravel Scheduler command at the required interval. Example Cron entry structure (implementation detail only, not committed configuration):

```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Cron configuration constraints:

- The Cron entry must not contain production credentials or secrets.
- The Cron entry must use the correct project path for the cPanel deployment.
- Only one Scheduler instance must run at a time; overlapping Scheduler runs must be prevented.
- Detailed Cron configuration belongs to deployment documentation, not this document.

---

# 6. Monthly Subscription Processing

Monthly Subscription processing handles the Flow A Billing Cycle lifecycle. It is the most important scheduled background process because it directly supports the Platform's SaaS revenue model.

## 6.1 Billing Cycle Initialization

**Schedule:** First day of each calendar month.

**Responsibilities:**

1. Create or open a new Billing Cycle record for the current calendar month.
2. Set the Billing Cycle start date to the first day of the current month.
3. Set the Billing Cycle end date to the last day of the current month.
4. For each active Teacher Workspace, prepare a Subscription record for the new Billing Cycle.
5. Preserve all historical Billing Cycle and Subscription records from prior periods.
6. Record the Billing Cycle initialization event in the Audit Log.

**Idempotency:** The job must check whether a Billing Cycle already exists for the current month before creating a new one. Re-execution must not create duplicate Billing Cycles.

**Business rules:**

- Billing Cycle follows the calendar-month rule (BR-008, D-006).
- Historical Billing Cycles are never permanently deleted.
- Pricing as of the Billing Period is preserved in historical records.
- Notifications are not sent as part of Billing Cycle initialization.

## 6.2 Billable Student Calculation

**Schedule:** Runs after Billing Cycle initialization and periodically (for example, daily or weekly) during the Billing Cycle to maintain up-to-date Billable Student counts.

**Responsibilities:**

1. For each Teacher Workspace, identify all active Enrollments during the current Billing Cycle.
2. For each Enrollment, calculate the number of calendar days the Student has been enrolled during the Billing Cycle.
3. Count a Student as **Billable** only if enrolled for **more than 15 calendar days** during the Billing Cycle.
4. Students enrolled for 15 calendar days or fewer are not counted.
5. Apply the calculation separately for each Teacher Workspace; a Student studying with multiple Teachers is evaluated independently for each Teacher.
6. Store or update the Billable Student count per Teacher for the current Billing Cycle.
7. Record the calculation event in the Audit Log.

**Idempotency:** Re-running the calculation must overwrite the previous count with the correct current count, not add to it. The result must reflect the current state of Enrollments.

**Business rules:**

- Billable Student calculation is based on Enrollment duration only (BR-008).
- Attendance and login activity must not be used (BR-008).
- Account existence, account activation, Homework, Exam, and Lesson activity must not be used.
- Each Teacher Workspace is evaluated independently.
- Flow B Student fee status is not used in this calculation.

## 6.3 Subscription Amount Calculation

**Schedule:** Runs after Billable Student calculation, or as part of the same job.

**Responsibilities:**

1. For each Teacher, retrieve the Billable Student count for the current Billing Cycle.
2. Retrieve the applicable Price Per Student configured by the Super Admin.
3. Calculate: **Monthly Subscription = Billable Students × Price Per Student**.
4. Store or update the Subscription amount for the Teacher's current Billing Cycle.
5. Preserve the price as of the Billing Period in the Subscription record.

**Idempotency:** Re-calculation must overwrite with the correct amount, not accumulate.

**Business rules:**

- Pricing is owned by the Super Admin (BR-015).
- Flat price versus volume tiers remains PENDING; Version 1 uses the confirmed Price Per Student formula.
- Historical invoices retain the price applicable to their period.
- Flow A Subscription amount must not be derived from or displayed as Flow B Student fee data.

## 6.4 Subscription Snapshot Generation

**Schedule:** Last day of each calendar month, or first day of the next month before Billing Cycle initialization.

**Responsibilities:**

1. Generate an immutable Subscription snapshot for each Teacher for the completed Billing Cycle.
2. The snapshot records: Teacher reference, Billing Cycle period, Billable Student count, Price Per Student, and calculated Subscription amount.
3. The snapshot is immutable once generated; corrections are adjustment records, not mutations.
4. Record the snapshot generation event in the Audit Log.

**Idempotency:** If a snapshot already exists for the Teacher and Billing Cycle, the job must not create a duplicate. Re-execution must verify existing snapshots before generating new ones.

**Business rules:**

- Immutable monthly Subscription snapshots are PROPOSED (D-003) and must not contradict confirmed billing rules.
- Historical snapshots are permanently retained.
- No permanent deletion of snapshot records is allowed.

## 6.5 Teacher Workspace Context

All Subscription processing jobs must resolve the Teacher Workspace context for each Teacher being processed. The jobs operate at Platform scope under the Super Admin's billing authority, but they evaluate each Teacher Workspace independently. Subscription processing must never expose one Teacher's Enrollment or Student data to another Teacher's Subscription record.

---

# 7. Attendance Cleanup Jobs

Attendance cleanup jobs maintain the Attendance subsystem's operational integrity without permanent deletion.

## 7.1 Expired Dynamic QR Code Context Cleanup

**Schedule:** Daily.

**Responsibilities:**

1. Identify Dynamic QR Code Attendance contexts that are no longer active for the current day.
2. Mark expired contexts as inactive or archived according to the Archive policy.
3. Do not permanently delete any Attendance record or QR context.
4. Preserve all historical Attendance data associated with expired contexts.
5. Record the cleanup action in the Audit Log where required.

**Idempotency:** Re-running the cleanup on the same day must not change already-cleaned contexts or create duplicate archive actions.

**Business rules:**

- Dynamic QR Codes are generated daily (BR-010).
- Attendance history is never permanently deleted.
- Archive replaces deletion (BR-005).
- Historical Attendance records remain available for reports (BR-014).

## 7.2 Attendance Session Maintenance

**Schedule:** Daily or as needed.

**Responsibilities:**

1. Verify that Attendance Sessions belong to valid, non-archived Teacher Workspaces.
2. Identify Attendance Sessions that are no longer active.
3. Archive expired Sessions without removing historical Attendance records.
4. Preserve Attendance records associated with archived Sessions for historical reporting.

**Idempotency:** Re-running must not re-archive already-archived Sessions.

---

# 8. Exam Result Processing

Exam result processing handles automatic grading of objective question types that do not require Teacher review.

## 8.1 Automatic Grading Queue

**Trigger:** Dispatched when a Student submits an Exam attempt that contains automatically gradable questions.

**Responsibilities:**

1. Receive a grading job for a specific Exam attempt.
2. Grade all automatically gradable questions: Multiple Choice, True/False, and Bubble Sheet.
3. Calculate the total score for automatically gradable questions.
4. Store the partial or full grade result for the Exam attempt.
5. If the Exam contains Essay questions, mark those as pending Teacher review.
6. Record the grading event in the Audit Log.
7. Preserve the grade result in the correct Student and Teacher Workspace context.

**Idempotency:** Re-grading the same Exam attempt must overwrite the previous auto-graded result with the same correct values, not create duplicate grade records.

**Business rules:**

- Supported question types are Multiple Choice, True/False, Essay, and Bubble Sheet (BR-011).
- Bubble Sheet is electronic on-screen selection with automatic grading support (BR-011, D-010).
- Exam attempts and grades are workspace-scoped (BR-012).
- Student transfers preserve historical Exam grades (BR-007).
- Essay questions may require Teacher grading before final results are available.

## 8.2 Bubble Sheet Processing

Bubble Sheet processing is handled as part of the automatic grading queue. Bubble Sheet questions use electronic on-screen selection, and the grading engine evaluates selected answers against the correct answer set.

Processing rules:

- Bubble Sheet grading is automatic and does not require Teacher intervention.
- The grading result is stored in the correct Teacher Workspace and Student context.
- The result is available to the Student and Parent (read-only for Parent) only after grading is complete.
- Historical Bubble Sheet results are preserved through Group movement and Archive.

## 8.3 Essay Grading Support

Essay questions are not automatically graded. The background system may:

1. Flag Essay answers as pending Teacher review after all objective questions are graded.
2. Notify the Teacher Workspace dashboard of pending Essay reviews through a workspace-scoped indicator (not a push, email, or SMS notification).
3. Store the partial grade (objective questions) while Essay grading is pending.

Essay grading remains a Teacher-side action and is not a background job.

---

# 9. Report Generation Jobs

Report generation jobs handle deferred or aggregated reporting work that is too expensive for synchronous request handling.

## 9.1 Deferred Report Preparation

**Trigger:** Dispatched when a report request requires aggregation across a large dataset or a complex calculation.

**Responsibilities:**

1. Receive a report preparation request with the report type, scope, filters, and requesting user context.
2. Resolve the requesting user's authorization, role, and scope before accessing data.
3. Prepare the report data within the authorized scope only.
4. Store the prepared report result for the requesting user to retrieve.
5. Preserve Teacher Workspace isolation in all report data.
6. Include archived records where historical reporting requires them, clearly indicated.
7. Keep Flow A and Flow B data separate in report preparation.
8. Record the report preparation event in the Audit Log where required.

**Idempotency:** Re-preparing the same report must overwrite the previous result, not create duplicate report outputs.

**Business rules:**

- Teacher reports are scoped to the Teacher's own Teacher Workspace (BR-003).
- Student reports include only the Student's own records, partitioned by Teacher.
- Parent reports include only linked Student records, in read-only mode.
- Super Admin reports operate at Platform scope within confirmed content-visibility boundaries.
- Historical data remains available in reports (BR-014).
- Archived records are clearly indicated when included.

## 9.2 Aggregation Jobs

Aggregation jobs pre-compute summary data for dashboards and frequent report queries.

**Schedule:** Periodic, based on report usage patterns.

**Responsibilities:**

1. Compute summary statistics for each Teacher Workspace, scoped to that workspace only.
2. Compute Platform-level summaries for Super Admin within confirmed visibility boundaries.
3. Store aggregated results in the cache (File Cache) for fast retrieval.
4. Preserve Teacher Workspace isolation in all aggregated data.
5. Keep Flow A and Flow B aggregations separate.

**Idempotency:** Re-aggregation must produce the same result based on current data, overwriting previous cache entries.

---

# 10. File Cleanup Jobs

File cleanup jobs maintain the integrity of file references and Laravel Public Storage without permanent deletion.

## 10.1 File Reference Integrity Check

**Schedule:** Weekly.

**Responsibilities:**

1. Compare file references in the database with files present in Laravel Public Storage.
2. Identify file references that point to missing physical files.
3. Flag orphaned references for review; do not permanently delete references that may be needed by historical records, archived records, or Audit Log context.
4. Identify physical files that have no corresponding database reference.
5. Report inconsistencies to the operational log for review.
6. Do not permanently delete any file or file reference without explicit authorization.

**Idempotency:** Re-running the check must produce the same consistency report for the same state of data.

**Business rules:**

- No permanent deletion exists in Version 1 (BR-005).
- Archived file references must remain retained for historical reports.
- File references must not be detached from historical Homework, Lesson, or submission records.
- Lesson videos are Teacher-owned and private (BR-018).
- Homework supports Text, Image, and PDF only (BR-021).
- Video homework is not supported.

## 10.2 Archived File Maintenance

**Schedule:** Monthly.

**Responsibilities:**

1. Verify that archived file references remain accessible for historical reporting.
2. Verify that archived file references are not presented as active content.
3. Verify that archived Lesson files remain private to the owning Teacher Workspace.
4. Report any integrity issues to the operational log.

**Idempotency:** Re-running must produce the same verification result for the same state of data.

---

# 11. Audit Log Maintenance Jobs

Audit Log maintenance jobs ensure the integrity and availability of the permanent, append-only, immutable Audit Log.

## 11.1 Audit Log Integrity Verification

**Schedule:** Monthly.

**Responsibilities:**

1. Verify that Audit Log entries remain intact and have not been modified or deleted.
2. Verify that Audit Log entries cover all confirmed important action types: create, update, Archive, restore, login, permission change, Attendance change, Exam modification, Homework modification, and Subscription change.
3. Verify that Teacher Staff actions are attributed to the Teacher Staff user, not the Teacher.
4. Report any integrity issues to the operational log.
5. Do not modify, archive, or delete any Audit Log entry.

**Idempotency:** Re-verification must produce the same integrity report for the same state of data.

**Business rules:**

- Audit Log entries are append-only and immutable.
- Audit Log retention is permanent.
- Audit Log entries must not be edited, archived, or deleted.
- Teacher Staff actions must be attributed to the Teacher Staff user (BR-013).

## 11.2 Audit Log Growth Monitoring

**Schedule:** Monthly.

**Responsibilities:**

1. Monitor the growth rate of Audit Log records.
2. Report growth statistics to the operational log for capacity planning.
3. Do not purge, archive, or compact Audit Log entries.

**Idempotency:** Re-running must report current statistics without side effects.

---

# 12. Notification Processing (Future)

Notifications are explicitly **out of scope for Version 1** (BR-017 context, Q-018 resolved, D-012 CONFIRMED). No notification background jobs are implemented in Version 1.

The following are reserved as future considerations only and must not be implemented without formal approval:

| Future notification concern | Required future decision |
|---|---|
| Push notifications | Approve scope, delivery mechanism, and user consent model. |
| Email notifications | Approve scope, content templates, frequency, and opt-in rules. |
| SMS notifications | Approve scope, carrier integration, cost model, and consent rules. |
| Notification queue | Approve queue behavior, prioritization, and delivery guarantees. |
| Notification preferences | Approve per-user, per-role, and per-Teacher-Workspace notification settings. |

Any future notification background processing must:

- Use the Database Queue only (or separately approved queue infrastructure).
- Preserve Teacher Workspace isolation.
- Preserve Parent read-only boundaries.
- Not send notifications for unconfirmed purposes.
- Not bypass the Archive, Audit Log, or authorization requirements.
- Not require Redis, WebSockets, or external notification services unless separately approved.

---

# 13. Retry Strategy

## 13.1 Automatic Retry

Laravel's built-in retry mechanism is used for failed jobs. The retry strategy depends on the job type and failure reason.

| Job category | Retry attempts | Backoff strategy |
|---|---|---|
| Billing Cycle / Subscription | 3 attempts | Exponential backoff (e.g., 60s, 300s, 900s) |
| Automatic Exam grading | 3 attempts | Exponential backoff (e.g., 30s, 120s, 600s) |
| Report preparation | 2 attempts | Linear backoff (e.g., 120s, 300s) |
| File reference integrity | 1 attempt | Fixed delay (e.g., 300s) |
| Audit Log verification | 1 attempt | Fixed delay (e.g., 600s) |
| Attendance cleanup | 2 attempts | Linear backoff (e.g., 60s, 180s) |

## 13.2 Manual Retry

Failed jobs that exhaust automatic retries remain in the failed jobs table. The Super Admin or authorized platform operator may review and manually retry failed jobs through Laravel's built-in failed job management commands.

## 13.3 Retry Constraints

- Retrying a job must not create duplicate records; idempotency is mandatory.
- Retrying a Subscription calculation must overwrite with the correct current result.
- Retrying an Exam grading must overwrite with the correct grading result.
- Retrying a report preparation must overwrite with the current report data.
- Retrying must preserve Teacher Workspace scope and authorization context.

---

# 14. Failure Handling

## 14.1 Failed Job Recording

All failed jobs are recorded in the Laravel failed jobs table. The failure record includes:

- Job class and queue name.
- Job payload (without sensitive data).
- Exception message and stack trace (without exposing Teacher-private data).
- Failure timestamp.
- Number of attempts made.

## 14.2 Failure Categories

| Category | Example | Required handling |
|---|---|---|
| Transient failure | Database connection timeout, temporary resource unavailability | Automatic retry with backoff. |
| Data inconsistency | Missing Enrollment record, orphaned file reference | Log failure, do not retry until data is corrected. |
| Authorization failure | Invalid Teacher Workspace context, expired system context | Log failure, do not retry without corrected context. |
| Business rule violation | Invalid Billing Cycle, non-idempotent state detected | Log failure, alert for manual review. |
| Resource exhaustion | Memory limit exceeded on shared hosting | Chunk the work into smaller batches and retry. |

## 14.3 Failure Notification

Version 1 does not send push, email, or SMS notifications for job failures. Failed job information is available through:

- The Laravel failed jobs table.
- Operational log entries.
- Audit Log entries where the failed action qualifies as an important action.

---

# 15. Job Priorities

Job priority determines processing order when multiple jobs are queued.

| Priority level | Job types | Processing behavior |
|---|---|---|
| **Critical** | Billing Cycle initialization, Subscription snapshot generation | Processed first; delays may affect billing accuracy. |
| **High** | Billable Student calculation, automatic Exam grading (including Bubble Sheet) | Processed promptly; delays may affect user-facing results. |
| **Medium** | Attendance cleanup, Audit Log enrichment, file reference integrity | Processed in normal order; delays are acceptable. |
| **Low** | Deferred report preparation, aggregation jobs, Audit Log growth monitoring | Processed when resources are available; delays are acceptable. |

Priority assignment must not cause low-priority work to starve indefinitely. The queue worker should process all queues in a round-robin or weighted manner where the hosting environment supports it.

---

# 16. Monitoring

## 16.1 Job Monitoring Scope

Background job monitoring supports operational awareness without introducing Version 1 notification features or exposing Teacher-private data.

Monitored indicators:

- Number of pending jobs per queue.
- Number of failed jobs per queue.
- Job processing rate (jobs per minute/hour).
- Job failure rate.
- Longest pending job age.
- Billing Cycle job completion status.
- Exam grading job completion status.

## 16.2 Monitoring Access

| Role | Monitoring visibility |
|---|---|
| Super Admin | Platform-level job status overview within confirmed Platform administration scope. |
| Teacher | No direct access to background job monitoring. Teachers see the results of background processing through their Teacher Workspace (e.g., Exam grades, Subscription status). |
| Teacher Staff | No background job monitoring access. |
| Student | No background job monitoring access. Students see results (e.g., Exam grades) after processing completes. |
| Parent | No background job monitoring access. |

## 16.3 Monitoring Constraints

- Monitoring must not expose Teacher-private data.
- Monitoring must not introduce push, email, or SMS notification features.
- Monitoring tools, dashboards, and alert thresholds are not confirmed and must not be invented.
- Monitoring must not require Redis, external monitoring services, or unconfirmed infrastructure.

---

# 17. Logging

## 17.1 Operational Logging

Background jobs produce operational log entries for:

- Job dispatch (when a job is added to the queue).
- Job processing start and completion.
- Job failure with exception details.
- Job retry attempts.
- Scheduled task execution.

Operational logs are stored using Laravel's logging system (file-based logging compatible with cPanel Shared Hosting).

Operational logging constraints:

- Logs must not contain sensitive credentials, raw passwords, or application secrets.
- Logs must not expose Teacher-private content unnecessarily.
- Logs must not contain Student personal data beyond what is needed for troubleshooting.
- Operational logs do not replace the business Audit Log.

## 17.2 Audit Log Integration

Background jobs that perform important actions must record those actions in the Audit Log. The Audit Log entry must include:

- The system or actor context under which the job executed.
- The event type (e.g., Subscription change, Attendance change, Exam grading).
- The affected record reference and Teacher Workspace or Platform context.
- A timestamp.

Audit Log entries from background jobs follow the same append-only, immutable, and permanent retention rules as all other Audit Log entries.

---

# 18. Performance Considerations

## 18.1 Shared Hosting Constraints

cPanel Shared Hosting imposes limits on process execution time, memory usage, CPU usage, and concurrent processes. Background jobs must be designed with these constraints in mind.

Performance guidelines:

- Long-running jobs must be chunked into smaller batches.
- Jobs that process all Teacher Workspaces must iterate one at a time rather than loading all records into memory.
- Billing Cycle processing must process Teachers sequentially or in small batches.
- Report preparation must use pagination and scoped queries.
- File reference checks must process files in batches.
- Database queue table size must be managed by cleaning up processed jobs.

## 18.2 Database Queue Performance

The Database Queue stores jobs in the MySQL 8 database. Performance considerations:

- The jobs table may grow if the worker cannot keep up with job dispatch rate.
- Successfully processed jobs should be cleaned up periodically.
- Failed jobs are retained in the failed jobs table for review.
- Queue table indexes must support efficient job retrieval (defined in future physical schema).

## 18.3 Scheduler Performance

The Laravel Scheduler runs through a Cron Job. Performance considerations:

- The Scheduler must not run overlapping instances.
- Scheduled tasks that are still running when the next Scheduler trigger occurs must be handled gracefully (e.g., without overlap).
- Multiple scheduled tasks triggered at the same time must be coordinated to avoid resource contention.

---

# 19. Error Handling

## 19.1 Job-Level Error Handling

Every background job must implement:

1. **Exception catching:** Wrap business logic in try-catch blocks to prevent unhandled exceptions from crashing the worker.
2. **Logging:** Log all exceptions with sufficient detail for troubleshooting.
3. **Failure recording:** Mark the job as failed in the Laravel failed jobs table.
4. **Graceful degradation:** If a job cannot complete, ensure no partial state is left that violates business rules.
5. **Rollback:** Use database transactions where the job modifies multiple records; roll back on failure.

## 19.2 Scheduler-Level Error Handling

- If a scheduled task fails, the failure must be logged.
- Subsequent Scheduler runs must not be blocked by a previous failure.
- A failed Billing Cycle initialization must not prevent the next month's cycle from being attempted.
- A failed Billable Student calculation must not prevent other scheduled tasks from running.

## 19.3 Error Response Constraints

- Error messages must not expose Teacher-private data, Student personal data, or internal implementation details.
- Error logs must not contain sensitive credentials or application secrets.
- Failed jobs must be available for review through the failed jobs table and operational logs.

---

# 20. Edge Cases

The background processing system must safely handle the following confirmed or directly required scenarios:

1. **First Billing Cycle on a new Platform.** The Platform may have no Teacher Workspaces yet. Billing Cycle initialization must handle an empty set of Teachers gracefully.

2. **Teacher with no Billable Students.** A Teacher may have zero Billable Students in a Billing Cycle. The Subscription calculation must use zero as the Billable Student count.

3. **Student enrolled for exactly 15 days.** A Student enrolled for exactly 15 calendar days during the Billing Cycle is not Billable. The calculation must correctly apply the more-than-15-days rule.

4. **Student enrolled with multiple Teachers.** Billable Student evaluation is separate for each Teacher Workspace. A Student who is Billable for one Teacher may not be Billable for another.

5. **Student moved Groups during Billing Cycle.** Enrollment history must remain accurate. The Billable Student calculation must use the total enrollment duration across Group moves within the same Teacher Workspace.

6. **Scheduler overlaps.** On cPanel Shared Hosting, the Cron Job may trigger while a previous Scheduler run is still executing. Overlap must be prevented or handled gracefully.

7. **Queue worker not running.** If the queue worker is not running (e.g., Cron misconfiguration), jobs accumulate in the database queue table. When the worker resumes, it must process pending jobs correctly and idempotently.

8. **Exam grading for mixed question types.** An Exam may contain both automatically gradable questions (Multiple Choice, True/False, Bubble Sheet) and Essay questions requiring Teacher review. The grading job must grade objective questions and mark Essay questions as pending.

9. **Large Teacher Workspace with many Students.** Billing Cycle processing for a Teacher Workspace with many Students must not exceed shared hosting memory or execution time limits. Processing must be chunked.

10. **Archived Teacher Workspace during Billing Cycle.** If a Teacher Workspace is archived during a Billing Cycle, the Subscription calculation must handle the archived workspace appropriately without losing historical Billing Cycle data.

11. **File reference points to missing file.** The file reference integrity check must flag the inconsistency for review rather than permanently deleting the reference, because the reference may be needed by historical records or Audit Log context.

12. **Concurrent Exam submissions.** Multiple Students may submit Exam attempts simultaneously. The automatic grading queue must process each attempt independently without interference.

13. **Failed Billing Cycle initialization.** If Billing Cycle initialization fails on the first day of the month, the next Scheduler run must detect the missing cycle and create it. Idempotency ensures no duplicate cycles are created.

14. **Audit Log entry failure during background job.** If recording an Audit Log entry fails during a background job, the business action must not be considered complete. The job must either retry the audit recording or roll back the business action.

---

# 21. Future Improvements

The following are future considerations only and are not Version 1 commitments:

| Future area | Required future decision |
|---|---|
| Advanced queue infrastructure | Redis or other queue drivers may be considered after infrastructure approval. Version 1 uses Database Queue only. |
| Queue monitoring dashboard | Approve a monitoring interface for Super Admin without exposing Teacher-private data. |
| Notification background processing | Approve notification scope, delivery mechanism, and queue behavior. Notifications are out of scope for Version 1. |
| Background report export | Approve export formats, delivery channels, and privacy controls. Export is not confirmed for Version 1. |
| Payment gateway webhook processing | Approve online payment scope and webhook handling. Payment gateways are out of scope for Version 1. |
| Batch operations | Approve bulk operations such as bulk Student import, bulk Attendance recording, and bulk grading. |
| Scheduled backup jobs | Approve backup frequency, retention, storage location, and restore procedures. |
| Non-payment enforcement automation | Resolve Q-005 before implementing any automated enforcement behavior. |
| Advanced Billing Cycle management | Approve correction, adjustment, reconciliation, and invoice behavior without rewriting history. |
| Job prioritization refinement | Approve more granular priority rules and queue routing based on operational experience. |
| WebSockets for real-time job status | Approve real-time infrastructure. WebSockets are not required for Version 1. |

All future improvements must preserve Teacher Workspace isolation, one global Student account, Parent linked-Student read-only access, Flow A / Flow B separation, Archive instead of permanent deletion, historical retention, immutable Audit Log records, and cPanel Shared Hosting compatibility (or separately approved infrastructure).

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority. |
| Requested section coverage | Passed — all 21 requested Background Jobs sections are present. |
| Queue strategy | Passed — Laravel Database Queue is confirmed as the Version 1 mechanism; Redis, SQS, and external drivers are excluded. |
| Scheduler strategy | Passed — Laravel Scheduler with Cron Jobs is confirmed; cPanel Shared Hosting compatibility is preserved. |
| Billing Cycle processing | Passed — calendar-month Billing Cycle, Billable Student calculation based on Enrollment duration only, Attendance/login exclusion, and more-than-15-days rule are preserved. |
| Flow A / Flow B separation | Passed — Subscription processing is completely separate from Student fee status processing. |
| Exam grading | Passed — automatic grading for Multiple Choice, True/False, and Bubble Sheet; Essay pending Teacher review; workspace-scoped grades. |
| Archive policy | Passed — no permanent deletion in any background job; Archive replaces deletion everywhere. |
| Audit Log policy | Passed — important actions from background jobs are recorded in the immutable, permanent Audit Log; Teacher Staff attribution is preserved. |
| Idempotency | Passed — all jobs are designed for idempotent execution; re-processing does not create duplicates or inconsistent state. |
| Failed job handling | Passed — failed jobs are recorded in the failed jobs table; operational logging captures failures; no unconfirmed notification features are introduced. |
| Teacher Workspace isolation | Passed — all jobs that process workspace-scoped data preserve Teacher Workspace boundaries. |
| Notifications | Passed — notifications are explicitly out of scope for Version 1; no notification background jobs are implemented. |
| Version 1 exclusions | Passed — no payment gateway processing, native mobile behavior, marketplace features, video homework processing, Docker, Redis, Kubernetes, S3 Storage, WebSockets, or Microservices are introduced. |
| Terminology | Passed — Teacher Workspace, Educational Grade, Billing Cycle, Billable Student, Subscription, payment status, Flow A, Flow B, Archive, Audit Log, Dynamic QR Code, and Bubble Sheet are used consistently. |
| No source code | Passed — no source code, APIs, database tables, UI implementation, or migration scripts are defined. |
