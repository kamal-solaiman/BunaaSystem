# 17 — Subscription Billing

## Document Scope

This document defines the confirmed Version 1 **Teacher Subscription & Billing System** for **Flow A**: the monthly Subscription paid by a Teacher to the Platform. It does not define Flow B Student/Parent-to-Teacher fee tracking except to preserve its separation from Flow A.

This document uses only confirmed project requirements. It does not provide source code, APIs, database tables, UI implementation, payment-gateway behavior, or an unconfirmed non-payment enforcement workflow. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found.

The business rules supplied with this request are reconciled with the official source as follows:

- Billing is based on **Billable Students**, not a separately defined “Active Student” count. A Billable Student is determined by Enrollment duration only.
- A Student becomes Billable only after more than 15 calendar days of Enrollment in a Teacher’s Group during the Billing Cycle.
- A Billing Cycle starts on the first day and **ends on the last day of the same calendar month**. A new cycle begins automatically on the first day of the next month. It does not end on the first day of the next month.
- Pricing is Platform-level and owned by the Super Admin. Flat price versus volume tiers remains PENDING.
- Teachers pay outside the Platform in Version 1. The Super Admin records Subscription payment status manually; no online transaction is processed.
- Grace Period, suspension, workspace access enforcement, and reactivation behavior for non-payment remain PENDING. The requirement that suspended Teachers cannot access their Workspace until reactivated is therefore not a confirmed Version 1 rule and is not defined as active behavior here.

---

# 1. Feature Overview

The Subscription & Billing System supports the Platform’s monthly SaaS business model. It records and reports the Teacher-to-Platform **Flow A Subscription** based on Billable Students in each calendar-month Billing Cycle.

The core formula is:

> **Monthly Subscription = Billable Students × Price Per Student**

The Platform records Subscription payment status only. Actual Teacher Subscription payments are handled outside the Platform, and online payment gateways are out of scope for Version 1.

Flow A must never be conflated with **Flow B**, which is the Student/Parent-to-Teacher fee obligation derived from Group Price and Pricing Type. Flow B payment status is a separate concern and does not determine, pay, or modify the Teacher’s Flow A Subscription.

---

# 2. Objectives

The confirmed objectives are to:

1. Support monthly Teacher Subscriptions as the Platform’s Flow A SaaS revenue model.
2. Use calendar-month Billing Cycles.
3. Calculate each Teacher’s Subscription from Billable Students and the applicable Price Per Student.
4. Determine Billable Student status using Enrollment duration only.
5. Exclude Attendance and login activity from all Billable Student calculations.
6. Let the Super Admin manage Platform-level pricing and record Flow A Subscription payment status.
7. Preserve Subscription history and historical price context.
8. Keep actual payment handling outside the Platform.
9. Keep Flow A Subscription distinct from Flow B payment status.
10. Record important Subscription and pricing changes in the immutable Audit Log.
11. Preserve Teacher Workspace isolation and Platform-level authorization boundaries.

---

# 3. Subscription Model

| Concern | Confirmed Version 1 rule |
|---|---|
| Subscription payer | Teacher. |
| Subscription payee | Platform. |
| Money flow | Flow A — Teacher-to-Platform Subscription. |
| Frequency | Monthly. |
| Pricing basis | Billable Students × Price Per Student. |
| Pricing ownership | Super Admin at Platform level. |
| Billing period | Calendar month. |
| Payment handling | Outside the Platform; the Platform records status only. |
| Online payment gateway | Out of scope. |
| Teacher access | Teacher may view the Teacher’s own Flow A Subscription information; the Teacher does not manage Platform-level pricing or another Teacher’s Subscription. |
| Super Admin access | Super Admin views/manages Flow A Subscription status and pricing within confirmed Platform authority. |

A Teacher Subscription is not a Student Subscription. In Student and Parent contexts, per-Teacher Flow B fee information is described as **payment status**, not as Flow A Subscription.

---

# 4. Active Student Definition

The official source distinguishes **Active Students** as a product metric from **Billable Students** as the billing input. It does not define a separate “Active Student” eligibility formula for Flow A billing.

For Version 1 billing:

- The authoritative billing term is **Billable Student**.
- A Student is Billable only based on Enrollment duration in a Teacher’s Group during the relevant Billing Cycle.
- A Student’s account existence, account activation state, Attendance, login activity, Homework activity, Exam activity, or Lesson activity does not make the Student Billable.
- A Student enrolled for 15 calendar days or less is not Billable even if the Student is otherwise considered active in a non-billing context.
- A Student may be Billable even though Flow A billing is not based on Attendance or login.

“Active Student” must not be used as an ambiguous substitute for Billable Student in Subscription calculations, reports, labels, or audit context. Any formal Active Student definition beyond the Product Vision metric requires separate approval.

---

# 5. Monthly Billing Cycle

A **Billing Cycle** is a calendar month.

1. The Billing Cycle starts on the first day of every calendar month.
2. The Billing Cycle ends on the last day of that same calendar month.
3. A new Billing Cycle begins automatically on the first day of the next month.
4. Subscription calculation is evaluated within the applicable Billing Cycle.
5. Historical Billing Cycle and Subscription records remain available and are not permanently deleted.

The cycle end is not defined as the first day of the next month. The first day of the next month is the beginning of the next Billing Cycle.

The exact scheduler timing, timezone, partial-period policy beyond the Billable Student rule, correction mechanism, invoice generation mechanics, and invoice format are not confirmed and are not defined here.

---

# 6. Subscription Calculation Rules

The confirmed calculation is:

> **Monthly Subscription = Billable Students × Price Per Student**

A Student is a Billable Student for a Teacher’s Billing Cycle only when all confirmed conditions are satisfied:

1. The Student has an Enrollment in a Group belonging to that Teacher’s Teacher Workspace.
2. The Enrollment duration during the relevant Billing Cycle is **more than 15 calendar days**.
3. The Billable Student is evaluated for the correct Teacher relationship; a Student may study with multiple Teachers and is evaluated separately for each Teacher Workspace.
4. The applicable Price Per Student is the Platform-level price configured by the Super Admin.

The following must not be used to calculate Billable Students:

- Attendance.
- Login activity.
- Account existence alone.
- Homework, Exam, Lesson, or other activity.
- Flow B Student fee payment status.

Pricing is owned by the Super Admin. Flat price versus volume tiers remains PENDING, so the Engine must not assume either model beyond the confirmed applicable Price Per Student rule. Historical invoices retain the price applicable to their period.

---

# 7. Student Activation Rules

Student account activation and Flow A Billable Student calculation are separate concepts.

Confirmed Student account rules:

- A Student may self-register or be created manually by a Teacher.
- A Teacher-created Student account may later be activated and used by that Student.
- Duplicate Student accounts are not allowed.
- A Student has one global account and may study with multiple Teachers.

Confirmed billing rule:

- Student account activation is not a Billable Student condition.
- The only confirmed Billable Student determinant is Enrollment duration in a Teacher’s Group during the Billing Cycle.

The system must not treat account activation, login, Attendance, or any engagement event as an activation rule for billing.

---

# 8. Student Deactivation Rules

No Student deactivation policy, billing deactivation policy, account suspension rule, Enrollment end-date billing treatment beyond the more-than-15-calendar-days rule, or automatic removal rule is confirmed for Version 1.

The confirmed related behavior is:

- A Student may be moved between Groups under the same Teacher while historical Attendance, Homework, Exams, and grades are preserved.
- Archive replaces permanent deletion.
- Historical data remains available.
- Billable Student calculation uses Enrollment duration only.

Therefore, this document does not define a deactivation event as a billing trigger. A future deactivation policy must preserve global Student identity, Teacher Workspace isolation, historical Enrollment/academic records, the Billable Student rule, Archive behavior, and Audit Log requirements.

---

# 9. Payment Status

In Version 1, payments are handled outside the Platform. The Platform records payment status only.

For Flow A Subscription payment status:

- The Super Admin may record a Teacher Subscription payment status manually after the external payment event.
- The Platform does not collect money, initiate a transaction, store gateway payment details, or process an online payment.
- Payment status is a record of status, not proof that the Platform processed a payment.
- Subscription payment status belongs to Flow A and must not be displayed as Flow B Student/Parent-to-Teacher payment status.
- A Student or Parent cannot manage Flow A Subscription payment status.
- Subscription payment-status changes are important actions and must be recorded in the Audit Log.

The permitted payment-status values, payment reference content, payment date rules, reconciliation workflow, refunds, adjustments, and accounting integration are not confirmed and are not defined here.

---

# 10. Teacher Subscription Status

Teacher Subscription status is the recorded Flow A Platform Subscription state for a Teacher and Billing Cycle within confirmed Super Admin authority.

Confirmed behavior:

- The Super Admin can view Teacher Subscription records and record Subscription payment status.
- The Teacher can view the Teacher’s own Flow A Subscription information.
- The Teacher, Teacher Staff, Student, and Parent cannot manage Flow A Platform Subscription status.
- Subscription status is based on the applicable Billable Student count and Price Per Student calculation for the Billing Cycle.
- Subscription status/history remains distinct from Flow B payment status and is preserved historically.
- Important Subscription changes are recorded in the Audit Log.

The set of Subscription statuses, state transitions, unpaid handling, invoice issuance, grace state, read-only state, suspension state, and reactivation state are not confirmed Version 1 behavior.

---

# 11. Grace Period

A Grace Period for Teacher non-payment is **PENDING** under Q-005. The Project Context lists a proposed non-payment enforcement ladder, but it is not a confirmed Version 1 business rule.

Accordingly, Version 1 does not define:

- A grace-period duration.
- Eligibility for grace.
- A grace start or end event.
- Teacher Workspace access during grace.
- Student or Parent access during grace.
- Grace notifications.
- Automatic state transitions at grace expiry.

The Platform may record Flow A Subscription payment status, but it must not silently apply a Grace Period policy until separately confirmed.

---

# 12. Suspension Rules

Teacher Subscription suspension for non-payment is **PENDING** and must not be implemented as confirmed Version 1 behavior.

The requirement that a suspended Teacher cannot access the Teacher Workspace until reactivated conflicts with the current PENDING status of non-payment enforcement and is therefore not adopted as a Version 1 rule in this document.

No confirmed rule defines:

- When or whether a Teacher is suspended for non-payment.
- Whether the Teacher Workspace becomes read-only or inaccessible.
- Whether Teacher Staff, Students, or Parents retain access.
- Whether records are archived, hidden, or changed during non-payment.
- Any automatic suspension process.

No data may be hard-deleted or automatically archived to enforce non-payment. Teacher Workspace isolation, Student/Parent access boundaries, Archive policy, and historical retention remain in force regardless of future enforcement decisions.

---

# 13. Reactivation Rules

Reactivation after non-payment or Subscription suspension is **PENDING** because the underlying enforcement and suspension behavior is not confirmed.

Version 1 does not define a reactivation trigger, actor, authorization, data-restoration behavior, access-restoration behavior, payment verification workflow, or Audit Log event sequence beyond the existing requirement to audit important Subscription changes.

If a future reactivation flow is approved, it must preserve historical Subscription records, Teacher Workspace isolation, Student global identity, Parent linked-Student read-only access, Archive policy, and immutable Audit Log retention. It must not treat payment processing as occurring inside the Platform unless a separate payment-gateway scope is approved.

---

# 14. Billing Reports

Billing reports are Flow A Platform-level reports under the Super Admin’s confirmed authority.

Confirmed report content and behavior:

- The Super Admin may view Flow A Subscription records for Teachers.
- Reports can show Billable Student count per Teacher for a Billing Cycle.
- Reports can show Subscription amount based on the confirmed formula.
- Reports can show recorded Subscription payment status and historical Subscription status overview.
- Pricing history remains understandable because historical invoices retain the price applicable to their period.
- Historical Billing/Subscription records remain available and must not be permanently deleted.
- Flow A reports remain separate from Flow B payment-status reports.
- Super Admin report visibility must remain within confirmed Platform-level boundaries; Teacher-private content visibility remains PENDING.

The report layout, exports, metrics beyond the confirmed records, reconciliation totals, accounting reports, and report delivery/notifications are not confirmed and are not defined here.

---

# 15. Teacher Dashboard Information

A Teacher Dashboard may present authorized summary information from the Teacher’s own Teacher Workspace. Where Flow A information is presented, it must follow these rules:

- Show only the Teacher’s own Flow A Subscription status/summary.
- Identify it clearly as **Subscription** or **Flow A Platform Subscription**.
- Keep it visually and semantically separate from Flow B Student fee payment status.
- Do not show another Teacher’s Subscription information.
- Do not offer price configuration, payment processing, or Platform-level Subscription-management actions to the Teacher.
- Do not infer Billable Student count from Attendance or login activity.
- Clearly identify historical/archived information when it is included.

The exact dashboard indicators, calculations displayed, status labels, or non-payment enforcement messages are not confirmed and must not be invented.

---

# 16. Super Admin Management

The Super Admin manages the Platform’s Flow A Subscription business model at Platform scope.

The Super Admin may:

- View Teacher Flow A Subscription records.
- View/manage calendar-month Billing Cycles within confirmed rules.
- Use Enrollment-duration-only data to determine Billable Student counts.
- Manage Platform-level pricing configuration according to confirmed pricing rules.
- Record Teacher Subscription payment status manually after externally handled payment.
- View Flow A billing reports and Platform-level Audit Log entries within confirmed visibility boundaries.

The Super Admin may not:

- Process online payments through the Platform.
- Conflate Flow A Subscription status with Flow B Student/Parent payment status.
- Use Attendance or login activity in Billing calculations.
- Treat PENDING flat-price/tier details as decided.
- Apply unconfirmed Grace Period, suspension, or reactivation behavior.
- Gain unrestricted Teacher-private content access or impersonate a Teacher.

All Super Admin actions remain Platform scoped and subject to backend authorization and Audit Log policy.

---

# 17. Audit Logging

The Audit Log is append-only, immutable, and permanently retained. Important Subscription actions must be recorded according to the Audit Log Policy.

Subscription/Billing audit coverage includes, where applicable:

- Platform pricing changes by the Super Admin.
- Billing Cycle management or creation actions where required.
- Subscription status changes.
- Manual recording of externally handled Teacher Subscription payment status.
- Other authorized Flow A lifecycle changes that qualify as important actions.
- Relevant denied or failed security-sensitive actions where required by policy.

Audit entries preserve the Super Admin actor and Platform context for Platform actions. They must not be edited, archived, or deleted. Audit Log visibility remains role- and scope-constrained; Student and Parent Audit Log views are not confirmed Version 1 surfaces.

---

# 18. Error Handling

| Condition | Required handling |
|---|---|
| User is not authenticated as Super Admin for a Flow A management action | Deny access. |
| Teacher attempts to manage another Teacher’s Subscription or Platform pricing | Deny access. |
| Student, Parent, or Teacher Staff attempts to manage Flow A Subscription | Deny access. |
| Billing Cycle is not a valid calendar-month period | Reject calculation or status action. |
| Enrollment-duration information is unavailable | Prevent misleading Billable Student calculation output. |
| Calculation uses Attendance or login activity | Reject the calculation because these inputs are excluded. |
| Student is enrolled for 15 days or less | Do not count the Student as Billable. |
| Payment processing is attempted | Reject; Version 1 records status only and payments are external. |
| Flow A/Flow B data is mixed | Reject or correct the presentation/action boundary; the flows must remain separate. |
| Unconfirmed non-payment enforcement is requested | Do not apply Grace Period, suspension, or reactivation behavior until Q-005 is resolved. |
| Historical billing record is targeted for deletion | Reject permanent deletion; preserve historical record under Archive/history rules. |

Error responses and user-facing errors must not expose another Teacher’s Subscription information, Teacher-private workspace content, internal implementation details, or payment credentials.

---

# 19. Edge Cases

The Subscription & Billing System must safely handle the following confirmed or directly required scenarios:

1. A Teacher has no Billable Students in a Billing Cycle; the calculation basis uses zero Billable Students.
2. A Student is enrolled for exactly 15 calendar days during the Billing Cycle and is not Billable.
3. A Student is enrolled for more than 15 calendar days during the Billing Cycle and is Billable.
4. A Student is enrolled with multiple Teachers; Billable evaluation is separate for each Teacher Workspace.
5. A Student moves Groups under the same Teacher; Enrollment history remains accurate for the relevant billing calculation while academic history is preserved.
6. Attendance or login activity exists but must not affect Billable Student calculation.
7. A Teacher-created Student account has not been activated; activation itself is not a billing input.
8. A Subscription payment occurs outside the Platform and the Super Admin records the status manually.
9. Pricing model details remain unresolved between flat price and volume tiers; no unconfirmed tier behavior is applied.
10. A historical Subscription/Billing record exists for a prior period; it remains available and retains the price applicable to that period.
11. A Super Admin attempts to apply non-payment suspension/reaction behavior; it is not applied because enforcement remains PENDING.
12. A Flow B Student fee payment-status record exists; it remains separate from Flow A Teacher Subscription calculation and reporting.

---

# 20. Future Improvements

The following items are future considerations only and are not Version 1 commitments:

| Future area | Required future decision |
|---|---|
| Pricing model | Resolve flat Price Per Student versus volume tiers while preserving historical price context. |
| Non-payment enforcement | Resolve Grace Period, Teacher Workspace read-only/inaccessible behavior, Student/Parent access, suspension, and reactivation under Q-005. |
| Payment gateway | Require separately approved online payment scope; Version 1 remains external-payment status recording only. |
| Billing corrections | Define approved correction, adjustment, reconciliation, and invoice behavior without rewriting history. |
| Active Student metric | Define a formal non-billing Active Student metric without replacing the Billable Student Enrollment-duration rule. |
| Billing reporting | Define advanced reports, exports, accounting integration, and visibility boundaries after approval. |
| Localization | Resolve language, timezone, currency, and market requirements before adding regional billing presentation behavior. |
| Infrastructure | Consider future infrastructure only if it preserves the Laravel 12 / React 19 architecture; Version 1 does not require Redis, WebSockets, S3 Storage, Docker, Kubernetes, or microservices. |

All future improvements must preserve Teacher Workspace isolation, one global Student account, Parent linked-Student read-only scope, Flow A / Flow B separation, Archive instead of permanent deletion, historical retention, and permanent immutable Audit Log records.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority. |
| Requested section coverage | Passed — all 20 requested Subscription & Billing sections are present. |
| Billing terminology | Passed — Billable Student, not an undefined Active Student count, is the sole confirmed Subscription calculation input. |
| Billable Student rule | Passed — Enrollment duration greater than 15 calendar days is required; 15 days or less, Attendance, and login activity are excluded. |
| Billing Cycle correction | Passed — the cycle starts on the first day and ends on the last day of the same month; the next cycle begins automatically on the next month’s first day. |
| Flow separation | Passed — Flow A Teacher Subscription and Flow B Student/Parent payment status are consistently separated. |
| Payment handling | Passed — external payments and Super Admin manual status recording are preserved; no payment gateway or transaction processing is introduced. |
| Pricing and historical records | Passed — Super Admin Platform-level pricing ownership, PENDING flat/tier decision, historical price context, and permanent historical retention are preserved. |
| Non-payment enforcement | Passed — Grace Period, suspension, workspace access restriction, and reactivation are explicitly PENDING and not silently implemented. |
| Roles and access | Passed — Super Admin Platform authority, Teacher own-status visibility, and denial of Flow A management to Teacher Staff, Student, and Parent are preserved. |
| Audit and Archive | Passed — important Subscription changes are auditable; no permanent deletion of historical billing records is included. |
| Scope | Passed — no code, APIs, database tables, UI implementation, payment gateway, or unconfirmed workflow is defined. |
| Terminology | Passed — Teacher Workspace, Student, Enrollment, Billable Student, Billing Cycle, Subscription, payment status, Flow A, Flow B, Archive, and Audit Log are used consistently. |


## Confirmed Audit Clarifications

The following confirmed clarifications are owned by `00_Project_Context.md` §9.9 (BR-023 through BR-025). They do not alter Flow A, Archive, Audit Log, or the external-payment boundary.

- **Parent workflow:** a Parent creates an account through Parent registration and submits a Parent–Student link request. The Teacher responsible for the Student’s active Enrollment approves or rejects it. Approval is allowed only when the Student has no other linked Parent; it creates read-only access. The Parent may request unlinking, and that Teacher approves the unlink. Request, approval, rejection, and unlinking are Audit Log events; historical records remain preserved.
- **Per Lesson Flow B:** a billable lesson is a Lesson completed for a Group while the Student has an active Enrollment in that Group. The obligation is recorded on completion at the Group’s recorded Price. Drafting, scheduling, publishing, viewing, Attendance, or assignment alone is not billable; one Student has at most one obligation for the same completed lesson. The Platform records payment status only, and payment remains outside the Platform.
- **Group transfer:** Flow A enrollment duration is accumulated across Groups of the same Teacher in the same Billing Cycle; a transfer does not reset the more-than-15-calendar-days test. Flow B obligations remain tied to the Group and completed Lesson that created them.
