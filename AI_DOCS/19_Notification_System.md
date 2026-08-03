# 19 — Notification System

## Document Scope

This document records the complete Version 1 notification scope exclusion and the boundaries that any separately approved future Notification System must respect. It does **not** define an active Version 1 notification feature.

The official source documents confirm that push notifications, email notifications, and SMS notifications are out of scope for Version 1. Accordingly, Version 1 has no Notification entity, notification API endpoints, notification permissions, notification settings, notification center, read/unread state, delivery history, queued notification sending, scheduled notification sending, or notification preferences.

This document does not provide source code, APIs, database tables, UI implementation, delivery-provider configuration, templates, queues, or channel implementation details. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found.

---

# 1. Feature Overview

There is no active Notification System in Version 1.

The Platform must not send push, email, or SMS notifications for Attendance, Homework, Lessons, Exams, payment status, Subscriptions, reports, account events, or any other Product event. SMTP is available as a technical mail-transport baseline only; it does not authorize or create Version 1 email notifications.

Local, in-context Web Application feedback—such as form validation, loading, error, success, confirmation, or unavailable states while a user is actively using the application—is not a Notification System. It must not be stored, delivered later, represented by a notification center, or treated as push, email, or SMS notification behavior.

---

# 2. Objectives

The Version 1 objectives are scope-control objectives:

1. Keep push, email, and SMS notifications out of Version 1.
2. Prevent notification routes, entities, permissions, settings, jobs, schedules, and UI surfaces from being introduced by implication.
3. Keep in-context user feedback distinct from externally or asynchronously delivered notifications.
4. Preserve the ability to consider notifications only in a separately approved future scope.
5. Ensure any future Notification System preserves Teacher Workspace isolation, Student self scope, Parent linked-Student read-only scope, Flow A / Flow B separation, Archive policy, Audit Log policy, and privacy boundaries.

No Version 1 objective includes message delivery, notification management, or notification history.

---

# 3. Notification Types

No Notification Type is defined or supported in Version 1.

The Platform must not define a Version 1 type for Attendance, Homework, Lesson, Exam, result, payment status, Subscription, billing, report, account, system, security, or marketing notifications.

If notification types are considered in a future approved scope, they must be defined through a separate Product Owner decision. Future consideration must not retroactively make any current Version 1 event a notification trigger.

---

# 4. Supported Channels

| Channel | Version 1 status |
|---|---|
| Push notification | Out of scope. |
| Email notification | Out of scope. |
| SMS notification | Out of scope. |
| In-application notification center | Out of scope. |
| Browser notification | Out of scope. |
| Any other delivery channel | Not confirmed. |

SMTP in the technical baseline is mail-transport availability only. It must not be interpreted as supported email notification delivery, templates, preferences, or notification history.

---

# 5. Teacher Notifications

Teacher notifications are not a Version 1 feature.

The Platform must not send Teachers notifications about Students, Attendance, Homework, Lessons, Exams, payment status, Flow A Subscription status, reports, Teacher Staff, settings, or Teacher Workspace events. The Teacher Workspace must not contain a notification center, notification badge, notification preferences, or delivery-management surface.

Teacher in-context feedback after an active action may explain that the action succeeded, failed, is pending, or needs correction. It is not retained or delivered as a notification.

Any future Teacher notification feature must remain scoped to the Teacher’s own Teacher Workspace and must not expose another Teacher’s data.

---

# 6. Student Notifications

Student notifications are not a Version 1 feature.

The Platform must not send Students notifications about Attendance, Homework, Lessons, Exams, grades, Flow B payment status, schedule, account events, or Teacher activity. A Student must not receive a notification center, notification badge, notification preferences, or delayed delivery behavior in Version 1.

The Student may view authorized self-scoped information while using the Web Application. That active-view access is not notification delivery.

Any future Student notification consideration must remain limited to the Student’s own per-Teacher records and must not expose another Student’s data or Teacher-private content.

---

# 7. Parent Notifications

Parent notifications are not a Version 1 feature.

The Platform must not send Parents notifications about linked Students’ Attendance, Homework, Exams, grades, Teachers, or Flow B payment status. A Parent must not receive notification preferences, notification history, or a notification center in Version 1.

A Parent may actively monitor linked Students through the authorized, read-only Web Application experience. That monitoring is not a notification channel and does not authorize Parent modification of linked Student data.

Any future Parent notification consideration must be limited to linked Students and must preserve Parent read-only access everywhere.

---

# 8. Super Admin Notifications

Super Admin notifications are not a Version 1 feature.

The Platform must not send the Super Admin notifications about Teachers, Flow A Subscriptions, pricing, Platform Settings, reports, Audit Logs, account events, or payment status. The Super Admin must not receive a notification center, notification preferences, or delivery history in Version 1.

The Super Admin may actively view authorized Platform-level records and reports. This is not notification delivery and must remain subject to the PENDING Teacher-private content visibility boundary.

Any future Super Admin notification capability must not create unrestricted access to Teacher-private content or become a Teacher impersonation mechanism.

---

# 9. System Notifications

There are no system-generated notifications in Version 1.

The Platform must not generate, schedule, queue, or deliver system notifications for:

- Billing Cycle changes.
- Flow A Subscription status.
- External payment-status recording.
- Attendance events.
- Homework activity.
- Lessons.
- Exams, grades, or results.
- Archive or restore actions.
- Account login or settings changes.
- Report availability.
- Errors or operational monitoring.

Operational logs, Audit Log entries, scheduler activity, queued work, error responses, and local in-context feedback are distinct from notifications and must not be represented as notifications.

---

# 10. Trigger Events

No notification trigger event exists in Version 1.

Version 1 events such as login, Attendance change, Homework modification, Exam modification, Archive, restore, permission change, Subscription change, or externally recorded payment status may require Audit Log entries under the Audit Log Policy. They must not trigger push, email, SMS, browser, or in-application notification delivery.

A future trigger catalog requires separate approval. It must specify event ownership, role/relationship scope, privacy restrictions, delivery eligibility, and Audit Log treatment without changing Version 1 behavior.

---

# 11. Notification Priority

No notification priority, urgency level, severity taxonomy, escalation level, or delivery ordering is defined for Version 1 because no Notification System exists.

UI semantic states such as success, warning, error, unavailable, Archive, and read-only are active in-context feedback states. They are not notification priorities and must not create a deferred or deliverable message.

Any future priority model requires separate approval and must not override role permissions, Teacher Workspace isolation, or Parent read-only boundaries.

---

# 12. Delivery Rules

No notification delivery rule exists in Version 1.

The Platform must not deliver messages asynchronously, on a schedule, via queue, by push, email, SMS, browser channel, or any other notification channel. Laravel Database Queue and Laravel Scheduler may support confirmed non-notification backend work, but they must not be used to send Version 1 notifications.

Any future delivery rule requires separately approved channel, consent/preference, security, retry, retention, role-scope, and privacy decisions.

---

# 13. Read / Unread Status

Read/unread status is not defined for Version 1 because no Notification entity, notification center, or notification history exists.

The Platform must not add unread counts, notification badges, read markers, dismissible notification history, or notification-management workflows. Dismissing an in-context validation, success, error, or confirmation message does not create a read/unread notification record.

A future read/unread model requires separate approval and must define the privacy, retention, audit, and role-scope implications before implementation.

---

# 14. Notification History

No Notification history is retained in Version 1 because Notification is not a Version 1 data entity and no notification delivery occurs.

The requirement to retain notification history can apply only to a separately approved future Notification System. It is not a basis to create a Version 1 Notification entity, history surface, storage mechanism, or Archive rule.

This does not change existing historical-retention requirements:

- Business records remain historically available according to the Archive policy.
- Important actions remain in the immutable, permanent Audit Log.
- Audit Log history is not a Notification history and must not be presented as one.

Any future notification-history retention policy requires separate Product Owner approval and must preserve privacy, Teacher Workspace isolation, Student/Parent scope, and immutable Audit Log distinctions.

---

# 15. Retry Strategy

No notification retry strategy exists in Version 1 because no notification is generated or delivered.

Database Queue retry behavior for other confirmed backend work must not be interpreted as notification-delivery retry support. The Platform must not queue notification messages, retry delivery, create a dead-letter notification process, or report delivery attempts in Version 1.

A future retry strategy requires separately approved decisions about delivery channels, retry limits, failure classification, retention, privacy, user preferences, and audit requirements.

---

# 16. Failure Handling

No notification-delivery failure can occur in Version 1 because no notification is delivered.

Version 1 handling distinguishes:

| Situation | Version 1 behavior |
|---|---|
| User action fails while active in the Web Application | Present safe in-context error feedback; do not create or send a notification. |
| Backend or operational failure | Use confirmed error handling and operational logging; do not notify by push, email, or SMS. |
| Failed login or important business action | Apply required Audit Log policy where applicable; do not send a notification. |
| Unsupported notification request | Reject because notifications are out of scope. |

A future notification failure process requires separate scope approval. It must not reveal private data, notification content, contact details, Teacher Workspace data, or internal delivery-provider details.

---

# 17. User Preferences

No notification preferences are available in Version 1.

The Platform must not provide channel opt-in/opt-out, frequency, quiet hours, recipient selection, topic selection, delivery address/number management, or notification settings. Existing account, Teacher Workspace, Student, Parent, and Platform Settings do not authorize notification preferences.

Future preferences require separate approval and must respect role boundaries, Parent linked-Student read-only access, consent requirements, privacy, and language/timezone decisions, all of which are not defined for notifications in Version 1.

---

# 18. Audit Logging

The Audit Log remains a required Version 1 subsystem, but it is not a Notification System.

- Important actions such as login, Archive, restore, Attendance change, Homework modification, Exam modification, permission change, and Subscription change are audited under the Audit Log Policy.
- The Platform must not create a notification delivery Audit Log because Version 1 has no notification delivery.
- Audit Log entries are append-only, immutable, permanently retained, and scope-aware.
- Audit Log visibility remains constrained: Super Admin Platform-scope visibility follows confirmed boundaries; Teacher Workspace Audit Log visibility is conditional where permitted; Student and Parent Audit Log views are not confirmed Version 1 surfaces.

A future Notification System may require approved notification-event auditing, but it must not alter existing Audit Log retention, actor attribution, or scope rules.

---

# 19. Future Push Notification Support

Push notifications may be considered only in a separately approved future scope. They are not a Version 1 feature and do not imply a native mobile application requirement.

Before future push support can be defined, Product Owner decisions are required for at least:

- Supported client surfaces and whether browser and/or native clients are in scope.
- Notification types and trigger events.
- Recipient scope and consent/preference rules.
- Teacher Workspace, Student, Parent, and Super Admin visibility boundaries.
- Privacy, security, retention, delivery, failure, retry, and Audit Log rules.
- Language, timezone, and market requirements.

Future push support must preserve one global Student account, Parent linked-Student read-only boundaries, Teacher Workspace isolation, and private Teacher-owned content.

---

# 20. Future Email Support

Email notifications may be considered only in a separately approved future scope.

SMTP availability in the Version 1 technical baseline does not constitute email notification approval. It must not be used to send Version 1 emails for Attendance, Homework, Exams, Subscription status, payment status, reports, account events, or marketing.

A future email capability requires separate approved decisions for notification types, recipients, consent/preferences, address ownership, templates, language, privacy, security, history, failures, retry, retention, and Audit Log behavior. Future email design must not expose another Teacher Workspace’s data, an unlinked Student’s data, or Teacher-private content.

---

# 21. Future SMS Support

SMS notifications may be considered only in a separately approved future scope.

No SMS provider, phone-number collection/use, message type, recipient rule, delivery workflow, retry rule, preference, or retention behavior is defined or authorized in Version 1.

Future SMS support requires a separately approved scope covering consent, number ownership, privacy, security, delivery cost/limits, failure/retry, history, Audit Log behavior, localization, and role/Teacher Workspace boundaries.

---

# 22. Security Considerations

Version 1 security requirements for notifications are primarily **non-creation** requirements:

1. Do not create notification records, endpoints, permissions, settings, queues, schedules, or delivery providers.
2. Do not expose contact details, device identifiers, email addresses, phone numbers, notification preferences, or delivery history because no Notification System exists.
3. Do not use SMTP to send Version 1 notification content.
4. Do not convert Audit Log entries, error responses, reports, dashboard data, or in-context feedback into externally delivered messages.
5. Do not let a notification-related request bypass authentication, RBAC, Teacher Workspace isolation, Student self scope, Parent linked-Student scope, Super Admin visibility constraints, Archive rules, or Audit Log policy.
6. Reject unsupported notification requests without exposing internal implementation details.

A future Notification System requires separate security and privacy approval before it can process recipients, content, consent, delivery, or history.

---

# 23. Error Handling

| Condition | Required Version 1 handling |
|---|---|
| Request attempts to create, view, update, Archive, restore, or send a notification | Reject because Notification is not a Version 1 product entity. |
| Request targets a notification endpoint | Return unsupported-route/authorization behavior without exposing internals. |
| Settings request includes notification configuration | Reject because notification settings are out of scope. |
| Queue or scheduler task attempts notification sending | Do not execute notification behavior. |
| User expects a delivery confirmation | Do not claim delivery because no notification delivery occurs. |
| Active user action succeeds/fails | Use in-context success/error feedback only; do not create notification history. |

Errors must not reveal contact details, notification payloads, Teacher-private data, unlinked Student data, secret configuration, internal provider information, or raw backend details.

---

# 24. Edge Cases

The Version 1 scope exclusion applies consistently in these cases:

1. A Teacher records Attendance; the event is audited where required but no Student or Parent notification is sent.
2. A Teacher creates Homework, a Lesson, or an Exam; no Student or Parent notification is sent.
3. An Exam grade becomes available; no Student or Parent notification is sent.
4. A Super Admin records externally handled Flow A Subscription payment status; no Teacher notification is sent.
5. A Student submits Homework or an Exam; no Teacher notification is sent.
6. A Student or Parent changes an account setting; no notification preference is available or updated.
7. A Teacher Workspace record is Archived or restored; no notification is sent.
8. A login succeeds or fails; required Audit Log behavior may apply, but no notification is sent.
9. A scheduled Billing Cycle begins; no notification is sent.
10. SMTP is configured as transport availability; it does not send Version 1 email notifications.
11. An in-context success message is dismissed; it does not become a read notification or history record.
12. A future notification request is proposed; it remains future scope until separately approved.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority. |
| Requested section coverage | Passed — all 24 requested Notification System sections are present. |
| Version 1 scope | Passed — push, email, SMS, browser, and in-application notification features are explicitly excluded. |
| No hidden implementation | Passed — no Notification entity, API, database table, queue, scheduler, UI center, preference, history, or delivery implementation is introduced. |
| In-context feedback distinction | Passed — loading, validation, error, success, and confirmation feedback remain active user-experience states, not notifications. |
| History and audit distinction | Passed — no Version 1 Notification history is created; existing historical retention and immutable Audit Log requirements are preserved and not conflated with notifications. |
| Role and tenant boundaries | Passed — future-only considerations preserve Teacher Workspace isolation, Student self scope, Parent linked-Student read-only access, and constrained Super Admin visibility. |
| Future expansion | Passed — future push, email, and SMS are limited to separately approved scope and require future decisions rather than a Version 1 redesign commitment. |
| Security | Passed — unsupported notification behavior is rejected without exposing private data, credentials, or configuration. |
| Terminology | Passed — Notification, Teacher Workspace, Student, Parent, Subscription, payment status, Flow A, Flow B, Archive, and Audit Log are used consistently. |
