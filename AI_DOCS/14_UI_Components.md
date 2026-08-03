# 14 — UI Components

## Document Scope

This document defines the Version 1 reusable Design System component library for the Unified Education Platform Web Application. It describes component contracts and usage standards only. It does not provide React code, HTML, CSS, API details, UI mockups, database tables, or implementation details.

The component library follows `13_UI_UX_Guidelines.md` and must remain consistent with the Project Context, Software Requirements, RBAC, Permission Matrix, API Design, Backend Architecture, Frontend Architecture, Project Structure, and User Flows. `00_Project_Context.md` remains the final authority.

A component is a reusable presentation and interaction contract. It does not make authorization decisions, establish Teacher Workspace scope, persist data, record Audit Log entries, or replace backend validation. The backend remains authoritative for all protected actions.

## Component Contract

Every catalog entry below defines these required concerns:

| Concern | Meaning |
|---|---|
| **Purpose** | The bounded user need the component serves. |
| **Props** | Conceptual inputs the consumer supplies; these are not implementation signatures. |
| **States** | Meaningful conditions the component communicates. |
| **Variants** | Approved reusable presentations or task contexts. |
| **Accessibility** | Required keyboard, semantic, focus, and assistive-technology behavior. |
| **Usage Guidelines** | Where and how the component is used. |
| **Validation Rules** | Conditions the component must communicate or require before invoking an action; backend validation remains final. |
| **Notes** | Scope, terminology, privacy, and Version 1 constraints. |

All components must support the shared semantic states where relevant: default, hover, focus, active/selected, disabled, read-only, loading, success, warning, error, unavailable, and archived. A state is not communicated through color alone.

---

# 1. Design System Overview

The library uses reusable, domain-neutral primitives plus domain-aware composite components. Primitives provide consistent controls and feedback; composites assemble them for confirmed educational workflows without duplicating business rules.

| Layer | Responsibility |
|---|---|
| Primitives | Buttons, fields, labels, status indicators, loading indicators, and dialogs. |
| Shared composites | Navigation, tables, cards, feedback, uploads, empty/error states, and context display. |
| Domain composites | QR Attendance, Exams, Attendance, Reports, and Settings components. |
| Feature composition | Features combine components with authorized data and actions; they do not recreate shared component behavior. |

The library must preserve clear role and context boundaries. A Parent receives read-only component variants only for linked Students. Teacher Staff variants are available only when the current context includes the required Teacher-assigned permission. Super Admin components remain Platform scoped; Teacher-private content visibility must not be assumed.

---

# 2. Layout Components

## 2.1 Application Shell

| Concern | Standard |
|---|---|
| Purpose | Provides the persistent application frame around authenticated routes. |
| Props | Active role context, permitted navigation model, header content, sidebar content, main content, and safe contextual status. |
| States | Authenticated, context-loading, navigation-collapsed, navigation-expanded, read-only context, and route-error containment. |
| Variants | Platform, Teacher Workspace, Student, Parent, and public shell. |
| Accessibility | Provides semantic landmarks, a skip-to-content path, logical reading order, visible focus, and no focus loss during layout changes. |
| Usage Guidelines | Use one shell per route context. Do not place feature-specific business actions or private data retrieval in the shell. |
| Validation Rules | Requires a resolved role context before protected content is shown; context labels do not prove authorization. |
| Notes | The Parent shell must visibly identify linked-Student monitoring as read-only. The Teacher Workspace shell must never allow a cross-Teacher context. |

## 2.2 Page Frame

| Concern | Standard |
|---|---|
| Purpose | Establishes a consistent page title, context summary, primary task area, secondary actions, and content region. |
| Props | Title, description, current context, primary action, secondary actions, content, and status/archived indicator. |
| States | Default, loading, empty, error, archived-history, and read-only. |
| Variants | List, detail, create/edit task, history, report, scanner, and exam task page. |
| Accessibility | Uses one clear page heading; action order follows reading order; status is announced in text, not color alone. |
| Usage Guidelines | Use for every principal route. Keep one primary purpose per page and avoid using it as a dashboard card container. |
| Validation Rules | Primary action is shown only when the feature identifies it as permitted; archived resources cannot be presented as active. |
| Notes | Use canonical page titles, including Educational Grades, Lessons, Attendance, and Flow B payment status. |

## 2.3 Context Bar

| Concern | Standard |
|---|---|
| Purpose | Makes the active Teacher Workspace, linked Student, or Teacher relationship visible near scoped content. |
| Props | Context type, display label, optional safe metadata, change action when permitted, and read-only/archived status. |
| States | Resolved, changing, unavailable, read-only, and archived historical context. |
| Variants | Teacher Workspace context, Parent linked Student context, Student Teacher relationship context, and Platform context. |
| Accessibility | Announces context changes and gives context controls explicit labels. |
| Usage Guidelines | Place above scoped lists, detail views, reports, payment-status views, and Student monitoring content. |
| Validation Rules | Only display context returned for the authenticated role; a supplied identifier is never accepted as proof of access. |
| Notes | Context switching must clear stale scoped content. It must not reveal another Teacher’s or unlinked Student’s identity. |

---

# 3. Navigation Components

## 3.1 Sidebar Navigation

| Concern | Standard |
|---|---|
| Purpose | Provides persistent, grouped navigation for the active role context. |
| Props | Navigation groups, current destination, permitted items, collapsed state, and optional context label. |
| States | Expanded, collapsed, active item, unavailable item, and keyboard focus. |
| Variants | Platform, Teacher Workspace, Student, Parent, and public navigation. |
| Accessibility | Uses a labelled navigation landmark; supports keyboard traversal, visible active state, and accessible collapsed control. |
| Usage Guidelines | Group confirmed capabilities logically. Show only items permitted in the active context. |
| Validation Rules | Navigation visibility is not authorization. A route must still handle a backend access denial safely. |
| Notes | Separate **Subscriptions** (Flow A) from **Payments** / payment status (Flow B). Do not include notification, marketplace, payment-gateway, or impersonation destinations. |

## 3.2 Header Bar

| Concern | Standard |
|---|---|
| Purpose | Gives global orientation and access to safe account and context controls. |
| Props | Product identity, active role, optional context control, account menu, logout action, and mobile navigation trigger. |
| States | Default, compact, context-changing, menu-open, and loading. |
| Variants | Authenticated and public header. |
| Accessibility | Has a clear banner landmark, labelled menus, keyboard-accessible controls, and focus restoration after a menu closes. |
| Usage Guidelines | Keep the header concise; do not duplicate the full sidebar or place dense dashboard data in it. |
| Validation Rules | Context options must be limited to valid returned contexts. Logout remains available to authenticated users. |
| Notes | Do not add a notifications bell or payment gateway shortcut in Version 1. |

## 3.3 Context Switcher

| Concern | Standard |
|---|---|
| Purpose | Lets a Parent select a linked Student or a Student select an authorized Teacher relationship where applicable. |
| Props | Context options, selected option, change action, accessible label, and loading/unavailable state. |
| States | Closed, open, selected, changing, empty, unavailable, and error. |
| Variants | Parent Student Switcher and Student Teacher relationship switcher. |
| Accessibility | Is keyboard operable, names the current selection, announces changes, and does not rely only on visual selection. |
| Usage Guidelines | Use only when more than one valid context is available. Preserve selection in the permitted route/application context. |
| Validation Rules | Parent options must be linked Students only; Student options must be the Student’s own Teacher relationships only. |
| Notes | The switcher is never an authorization bypass and must not offer edit behavior to Parents. |

## 3.4 Breadcrumbs

| Concern | Standard |
|---|---|
| Purpose | Shows the current location within a deep, permitted resource path. |
| Props | Ordered safe labels, destination references, current item, and optional context prefix. |
| States | Default, compact, and truncated-safe. |
| Variants | Teacher Workspace management, Student self-service, Parent monitoring, and Platform administration. |
| Accessibility | Uses an accessible navigation label and identifies the current page without making it an unnecessary link. |
| Usage Guidelines | Use for multi-level detail, edit, history, and report paths; omit when it adds no orientation. |
| Validation Rules | Labels must not expose unauthorized resource names or internal identifiers. |
| Notes | Breadcrumbs explain navigation only and do not replace the Context Bar. |

---

# 4. Form Components

## 4.1 Action Button

| Concern | Standard |
|---|---|
| Purpose | Invokes one clear, permitted user action. |
| Props | Action label, intent, icon where useful, pending state, disabled/read-only explanation, and confirmation requirement. |
| States | Default, hover, focus, pressed, pending, disabled, read-only, success, and error-returned. |
| Variants | Primary, secondary, tertiary, consequential, Archive, restore, and submit. |
| Accessibility | Uses a descriptive accessible name, is keyboard operable, exposes pending/disabled state, and retains visible focus. |
| Usage Guidelines | Use action-first labels such as Create Group, Record Attendance, Submit Homework, Archive Lesson, and Restore Group. |
| Validation Rules | Disable duplicate submissions while pending; consequential actions require valid prerequisites and confirmation where specified. |
| Notes | Never use “Delete” as a Version 1 lifecycle action. Parent variants must not expose modifications to linked-Student data. |

## 4.2 Text Field

| Concern | Standard |
|---|---|
| Purpose | Collects a single text, number, or password value in a form. |
| Props | Visible label, value, helper text, required status, field state, input purpose, and error message. |
| States | Empty, filled, focused, required, disabled, read-only, valid, invalid, and pending validation. |
| Variants | Short text, long text, numeric, password, search, and constrained identifier. |
| Accessibility | Has a persistent label, associated instructions/error, keyboard support, and no placeholder-only meaning. |
| Usage Guidelines | Use for names, descriptions, Schedule-related text, search criteria, and supported registration fields. |
| Validation Rules | Communicate required, format, length, and numeric constraints; preserve values after correctable errors. |
| Notes | Never expose Authentication Secrets, raw server errors, or another user’s private data in helper text. |

## 4.3 Select Field

| Concern | Standard |
|---|---|
| Purpose | Lets a user choose from a constrained known set. |
| Props | Label, options, selected value, helper text, required status, placeholder, and field error. |
| States | Unselected, selected, focused, disabled, read-only, invalid, loading options, and empty options. |
| Variants | Single select, searchable select, grouped select, and status select. |
| Accessibility | Exposes label, current selection, option count/availability where appropriate, keyboard operation, and field error association. |
| Usage Guidelines | Use for Educational Grade, Group, Pricing Type, role-permitted filter, and safe context selection. |
| Validation Rules | Options must be valid for current scope; Group assignment uses active Groups only; Pricing Type is only Monthly or Per Lesson. |
| Notes | Do not expose archived active-assignment options, cross-Teacher options, or unconfirmed Teacher Staff permissions. |

## 4.4 Date and Period Field

| Concern | Standard |
|---|---|
| Purpose | Collects or filters a date or supported reporting period. |
| Props | Label, selected date/period, minimum/maximum where supplied, helper text, and error state. |
| States | Empty, selected, focused, invalid, unavailable, and read-only. |
| Variants | Single date, date range, calendar-month Billing Cycle, and report period. |
| Accessibility | Provides typed accessible labels/instructions and does not require pointer-only calendar interaction. |
| Usage Guidelines | Use for Attendance, reports, historical views, and Subscription/Billing Cycle context. |
| Validation Rules | Date ranges must be valid; Billing Cycle presentation follows first-to-last day of the calendar month. |
| Notes | Do not assume timezone, locale, date format, or country-specific calendar behavior; these remain PENDING. |

## 4.5 Form Section

| Concern | Standard |
|---|---|
| Purpose | Groups related fields and explains one bounded data-entry task. |
| Props | Title, description, fields, optional status, and section actions. |
| States | Default, expanded, collapsed where safe, invalid-summary, read-only, and loading. |
| Variants | Registration, Educational Grade, Group, Student, Homework, Lesson, Exam, settings, and filter section. |
| Accessibility | Uses a semantic group heading, logically ordered fields, error summary, and focus movement to the first actionable error after failed submit. |
| Usage Guidelines | Split long forms by task meaning, not database entity or technical field type. |
| Validation Rules | Displays field and non-field validation clearly; final authorization/business validation remains backend-owned. |
| Notes | Teacher registration must make the one immutable Teaching Subject decision clear. Parent linked-Student educational views do not use editable Form Sections. |

---

# 5. Data Display Components

## 5.1 Data Table

| Concern | Standard |
|---|---|
| Purpose | Displays comparable, scoped records for review and permitted management. |
| Props | Title, columns, rows, current scope, filters, sort state, pagination, row actions, loading, empty, and error content. |
| States | Loading, populated, filtered-empty, empty, error, archived-row, read-only, and compact. |
| Variants | Students, Groups, Attendance, Homework, Lessons, Exams, payment status, Subscriptions, Teacher Staff, reports, and Audit Logs. |
| Accessibility | Uses labelled headers, keyboard-accessible row actions, announced sort/filter state, and a responsive readable alternative. |
| Usage Guidelines | Place identity, context, status, and primary permitted action first. Use documented pagination and filters only. |
| Validation Rules | Rows and actions must reflect current role/scope; archived rows are explicitly marked and are not treated as active. |
| Notes | Never render cross-Teacher, unlinked Student, or unauthorized counts/data. Keep Flow A and Flow B tables separate. |

## 5.2 Status Indicator

| Concern | Standard |
|---|---|
| Purpose | Communicates a concise confirmed record, task, or availability state. |
| Props | Status label, semantic intent, optional explanatory text, icon, and archived/read-only state. |
| States | Informational, success, warning, error, pending, unavailable, archived, and read-only. |
| Variants | Account, Enrollment, Attendance, Homework, Exam result, payment status, Subscription, file, and Archive status. |
| Accessibility | Includes text meaning and accessible name; color/icon alone never carries the status. |
| Usage Guidelines | Use close to the affected record/value and avoid status overload. |
| Validation Rules | Displays only status confirmed by the Platform; pending manual grading must not be presented as a final grade. |
| Notes | “Subscription” is Flow A. Flow B uses “payment status,” never an ambiguous Subscription label. |

## 5.3 Detail List

| Concern | Standard |
|---|---|
| Purpose | Presents a small set of labelled record attributes in a readable summary. |
| Props | Label/value pairs, context, optional actions, status indicators, and archived state. |
| States | Default, incomplete, read-only, loading, unavailable, and archived. |
| Variants | Student, Group, Educational Grade, Lesson, Homework, Exam, Teacher, Parent, and settings detail. |
| Accessibility | Associates every value with an explicit label and preserves logical reading order at narrow widths. |
| Usage Guidelines | Use for details, review, and confirmation summaries; do not use as an editable substitute for a form. |
| Validation Rules | Omit values outside the current authorization scope and identify unavailable information neutrally. |
| Notes | Do not display private file paths, authentication data, or another Teacher Workspace’s records. |

## 5.4 Archive Indicator

| Concern | Standard |
|---|---|
| Purpose | Clearly distinguishes a historical archived resource from an active resource. |
| Props | Archived label, archive date/reason where authorized, resource name, and historical-context explanation. |
| States | Archived, restoring, restored, and archive-action error. |
| Variants | Inline indicator, table-row indicator, detail banner, and history indicator. |
| Accessibility | Announces the archived state in text and does not rely on muted color or icon alone. |
| Usage Guidelines | Display whenever archived data appears in reports, history, or detail context. |
| Validation Rules | An archived resource cannot be offered as an active selection/assignment option until valid restoration. |
| Notes | Use **Archive** and **Restore**. Never communicate permanent deletion; historical records remain retained. |

---

# 6. Feedback Components

## 6.1 Inline Feedback

| Concern | Standard |
|---|---|
| Purpose | Communicates contextual help, validation, warning, or task outcome near the affected control or region. |
| Props | Message, semantic intent, optional title, optional safe retry/action, and dismissal behavior. |
| States | Information, success, warning, error, pending, and unavailable. |
| Variants | Field feedback, form summary, section feedback, and scanner/upload feedback. |
| Accessibility | Uses appropriate announcement behavior, clear text, and does not unexpectedly move focus for noncritical updates. |
| Usage Guidelines | Place near the related task. Use concise language explaining what happened or what needs correction. |
| Validation Rules | Messages must be sourced from safe normalized conditions; no raw error payloads or private resource existence details. |
| Notes | In-context feedback is not a Version 1 notification system. |

## 6.2 Success Message

| Concern | Standard |
|---|---|
| Purpose | Confirms a completed, backend-confirmed user action. |
| Props | Completed action label, optional affected safe record label, next-step action, and dismissal timing. |
| States | Visible, announced, dismissed, and unavailable when completion is not confirmed. |
| Variants | Create, update, Archive, restore, submit Homework, record Attendance, and upload complete. |
| Accessibility | Announces meaningful completion without repeatedly interrupting assistive technology users. |
| Usage Guidelines | Use concise past-tense confirmation only after success is confirmed. |
| Validation Rules | Do not show for pending grading, pending processing, failed requests, or unconfirmed local changes. |
| Notes | It does not replace the Audit Log and is not push, email, SMS, or in-app notification delivery. |

---

# 7. Dialog Components

## 7.1 Confirmation Dialog

| Concern | Standard |
|---|---|
| Purpose | Requires explicit confirmation before a consequential action. |
| Props | Title, affected resource/context, consequence text, confirm label, cancel label, intent, pending state, and optional typed confirmation where approved. |
| States | Open, focused, pending confirmation, action error, completed, and cancelled. |
| Variants | Archive, restore, Attendance correction, Exam submission, Homework submission, and other high-impact state change. |
| Accessibility | Moves focus into the dialog, keeps it contained, supports keyboard dismissal where safe, and returns focus to the invoker. |
| Usage Guidelines | Use precise action labels such as Archive Group or Submit Exam. Do not use for ordinary navigation or harmless filtering. |
| Validation Rules | Requires a valid eligible action before opening; prevents repeated confirmation while pending. |
| Notes | Archive dialogs state that history is retained. Do not use a permanent-delete variant. |

## 7.2 Task Dialog

| Concern | Standard |
|---|---|
| Purpose | Supports a short, focused contextual task without leaving the current page. |
| Props | Title, task content, initial focus target, completion action, cancel action, size, and unsaved-change state. |
| States | Open, loading, form-invalid, pending, complete, error, and unsaved changes. |
| Variants | Quick edit, contextual detail, compact filter, and permitted short action. |
| Accessibility | Provides labelled dialog semantics, managed focus, keyboard operation, and an unsaved-change warning before accidental dismissal. |
| Usage Guidelines | Use only for short bounded work. Use a dedicated page for long forms, QR scanning, Exam taking, and complex reports. |
| Validation Rules | Validates contained input before completion and keeps recoverable entered values after failure. |
| Notes | A Task Dialog must not hide role/scope implications or create an alternate authorization path. |

---

# 8. Dashboard Components

## 8.1 Summary Card

| Concern | Standard |
|---|---|
| Purpose | Presents one meaningful, scoped dashboard summary and a permitted path to detail. |
| Props | Title, value/summary, context, status, optional trend/period, detail destination, loading, empty, and error state. |
| States | Loading, populated, zero/empty, unavailable, error, archived-history, and read-only. |
| Variants | Students, Groups, Attendance, Homework, Exams, Lessons, reports, Flow A Subscription, and Flow B payment status. |
| Accessibility | Gives the summary an understandable label, communicates status in text, and makes any destination action explicit. |
| Usage Guidelines | Use a small prioritized set. A dashboard is not a report replacement. |
| Validation Rules | Data must be scope-correct and status-confirmed; historical/archived values must not appear as active counts. |
| Notes | Do not mix Flow A and Flow B in one summary without explicit separate labels. Super Admin cards must respect PENDING content visibility. |

## 8.2 Activity Panel

| Concern | Standard |
|---|---|
| Purpose | Shows a concise list of relevant current or historical operational items. |
| Props | Title, scoped items, item status, period/filter label, empty content, loading, and permitted detail action. |
| States | Loading, populated, empty, filtered-empty, error, and archived-history. |
| Variants | Teacher daily activity, Student assigned work, Parent monitoring summary, and Platform administration summary. |
| Accessibility | Uses a labelled list, exposes item status textually, and maintains keyboard-accessible detail actions. |
| Usage Guidelines | Include only actionable or clearly informative items; link to detailed views rather than duplicating all records. |
| Validation Rules | Items must be authorized for the active context and respect active/archive status. |
| Notes | Do not use as a notification feed; Version 1 has no notifications. |

---

# 9. QR Components

## 9.1 Dynamic QR Display

| Concern | Standard |
|---|---|
| Purpose | Displays the daily Dynamic QR Code for the authorized Teacher Workspace Attendance context. |
| Props | QR visual value, Attendance context label, daily validity explanation, loading/error state, and authorized refresh state. |
| States | Generating, active, expired/unavailable, error, and replaced. |
| Variants | Teacher display and authorized Teacher Staff display. |
| Accessibility | Provides a textual explanation of purpose and availability; it does not require visual QR reading for understanding the surrounding task. |
| Usage Guidelines | Use only in the valid Attendance context. Pair with clear Student scan instructions. |
| Validation Rules | The display must represent the current daily code and correct Teacher Workspace context; the visual code itself does not establish Attendance. |
| Notes | Do not expose QR values in logs, unrelated screens, or to unauthorized users. Attendance does not determine Billable Students. |

## 9.2 QR Scanner Panel

| Concern | Standard |
|---|---|
| Purpose | Guides browser-based Student Dynamic QR Code scanning and scanner-device-assisted ID Card Attendance input. |
| Props | Scan mode, start/cancel actions, camera/scanner availability, context label, normalized result state, and fallback guidance. |
| States | Idle, requesting permission, active scanning, processing, success, invalid, duplicate, permission-denied, unsupported, cancelled, and error. |
| Variants | Student Dynamic QR Code scan and Teacher/Teacher Staff ID Card scanner input. |
| Accessibility | Uses explicit start/stop controls, keyboard-accessible alternatives for supported scanner-device input, text status, and no color-only scan outcome. |
| Usage Guidelines | Request camera access only after deliberate user action. Stop scanning after cancellation, final outcome, or navigation away. |
| Validation Rules | A scan result is submitted for backend validation of identity, Teacher relationship, Teacher Workspace, and Attendance context; it never proves Attendance locally. |
| Notes | If scanning is unavailable, direct an authorized Teacher/Teacher Staff member to Manual Attendance. Do not invent Student self-entry fallback. |

---

# 10. File Upload Components

## 10.1 File Upload Field

| Concern | Standard |
|---|---|
| Purpose | Selects and submits an authorized file for a specific owning resource. |
| Props | Label, owning context, accepted type guidance, selected file metadata, progress, cancel/retry action, required status, and error message. |
| States | Empty, selecting, selected, validating, uploading, complete, cancelled, invalid, and upload-error. |
| Variants | Teacher Workspace file, Lesson file, Homework attachment, and Student Homework submission. |
| Accessibility | Has an explicit label, keyboard file-selection path, text progress/status, and clear supported-format instruction. |
| Usage Guidelines | Identify the owning task before selection and show the selected file name/status without exposing a storage path. |
| Validation Rules | Student Homework submission accepts Image and PDF only. Type/ownership/access validation remains authoritative on the backend. |
| Notes | Do not offer video homework. Parent upload variants do not exist. Private Lesson files remain Teacher-owned and access-controlled. |

## 10.2 File Reference Item

| Concern | Standard |
|---|---|
| Purpose | Displays one authorized file reference and its permitted actions. |
| Props | File display name, type, owner context, status, authorized open/download action, Archive/restore action where permitted, and safe metadata. |
| States | Available, loading, unavailable, archived, restoring, access-denied, and error. |
| Variants | Lesson file, Homework attachment, Student submission, and Teacher Workspace file. |
| Accessibility | Uses descriptive action labels and exposes file state in text. |
| Usage Guidelines | Use in a file list or detail context; do not display raw storage path or signed access mechanism. |
| Validation Rules | Actions appear only for authorized role/relationship and resource state; archived files are not presented as active. |
| Notes | A file reference does not prove access. Parent views are read-only and must not receive upload/Archive/restore actions. |

---

# 11. Exam Components

## 11.1 Exam Question Panel

| Concern | Standard |
|---|---|
| Purpose | Presents one authorized Exam question and captures the permitted Student answer. |
| Props | Question number, question type, prompt, answer options/input, required state, answer state, and attempt status. |
| States | Unanswered, answered, focused, invalid, review, submitted, read-only result, and unavailable. |
| Variants | Multiple Choice, True/False, Essay, and Bubble Sheet question. |
| Accessibility | Provides an understandable question label, keyboard-operable inputs, associated instructions, and clear selected/answered status. |
| Usage Guidelines | Use only within an assigned/available Exam attempt. Keep question controls free from Teacher-private Question Bank management details. |
| Validation Rules | Answer shape must match the supported question type; submitted/archived/inactive Exam state prevents an active answer change where applicable. |
| Notes | Students cannot access Questions outside authorized Exam context. Essay grading may remain pending after submission. |

## 11.2 Bubble Sheet Selector

| Concern | Standard |
|---|---|
| Purpose | Provides electronic on-screen bubble selection for Bubble Sheet questions. |
| Props | Question/item identifiers, available bubble options, selected answers, completion status, and read-only state. |
| States | Unselected, selected, focused, incomplete, complete, submitted, and read-only result. |
| Variants | Single-answer Bubble Sheet and approved multi-item Bubble Sheet layout. |
| Accessibility | Supports keyboard selection, exposes selected state textually, labels each bubble/question, and does not depend on color or spatial position alone. |
| Usage Guidelines | Use as the Bubble Sheet answer control inside an authorized Exam attempt, not as a generic decorative grid. |
| Validation Rules | Only valid on-screen selections for the question structure are accepted; automatic grading applies only after backend-confirmed submission where applicable. |
| Notes | Bubble Sheet is an electronic exam pattern, not a scanned-paper workflow. |

## 11.3 Exam Attempt Summary

| Concern | Standard |
|---|---|
| Purpose | Shows attempt progress, submission readiness, and available result state. |
| Props | Exam title, Teacher context, answered/total summary, attempt state, submission action, result status, and confirmation requirement. |
| States | Not started, in progress, incomplete, ready to submit, submitting, submitted, grading-pending, graded, archived historical, and unavailable. |
| Variants | Student attempt, Student result, and Parent read-only linked-Student result. |
| Accessibility | Announces progress/status in text, provides a clear submit label, and does not trap keyboard focus in question navigation. |
| Usage Guidelines | Place at a stable location during Exam taking and pair final submission with Confirmation Dialog when required. |
| Validation Rules | Submission requires a valid authorized attempt; result is shown only when available. Parent variant is read-only. |
| Notes | Grades are Teacher Workspace scoped. Do not expose private Question Bank content or another Student’s attempt. |

---

# 12. Attendance Components

## 12.1 Attendance Method Selector

| Concern | Standard |
|---|---|
| Purpose | Selects the confirmed Attendance method for an authorized Teacher Workspace task. |
| Props | Available methods, selected method, current Attendance context, permission state, and explanatory text. |
| States | Default, selected, unavailable, disabled with explanation, and error. |
| Variants | Dynamic QR Code display, ID Card scanner, and manual Attendance. |
| Accessibility | Uses labelled, keyboard-operable choices and explains unavailable methods in text. |
| Usage Guidelines | Show only methods allowed for the actor/context. Student flow exposes Dynamic QR Code scanning only; Teacher/authorized Teacher Staff may use manual or ID Card paths when permitted. |
| Validation Rules | The selected method must be one of the three confirmed methods and valid for the Teacher Workspace Attendance context. |
| Notes | Do not add face recognition, GPS, biometric, or other unconfirmed Attendance methods. |

## 12.2 Attendance Register

| Concern | Standard |
|---|---|
| Purpose | Presents a scoped Attendance list and supports authorized manual recording or correction. |
| Props | Attendance context, Student rows, current status, record/correct action, filters, history state, loading, empty, and error content. |
| States | Loading, empty, populated, editing, pending save, saved, invalid, error, and archived historical context. |
| Variants | Teacher manual register, authorized Teacher Staff register, Student self-status, and Parent linked-Student read-only history. |
| Accessibility | Uses labelled controls, clear Student/status association, keyboard editing where editing is permitted, and text confirmation of changes. |
| Usage Guidelines | Keep Group, date/session, and Teacher Workspace context visible. Use the row/action pattern only for authorized actors. |
| Validation Rules | Manual changes require valid Student relationship, Attendance context, and permission; duplicate/inconsistent record conditions are presented safely. |
| Notes | Parent is read-only. Historical Attendance survives Group movement. Attendance must never be presented as a Billable Student input. |

---

# 13. Report Components

## 13.1 Report Filter Bar

| Concern | Standard |
|---|---|
| Purpose | Collects permitted filter, sort, and period criteria for a scoped report. |
| Props | Supported filters, selected values, period field, apply/reset actions, loading, and validation feedback. |
| States | Default, changed, applying, active filters, invalid, empty options, and error. |
| Variants | Teacher Workspace report, Student self report, Parent linked-Student report, and Platform report. |
| Accessibility | Labels every filter, announces active filter state, supports keyboard reset/apply, and does not require color to show active filters. |
| Usage Guidelines | Offer only documented filters and a clear safe reset. Keep scope visible above the filter set. |
| Validation Rules | Filter values must be valid for the active role/scope and supported date range; cross-Teacher or unlinked Student filters are rejected. |
| Notes | The component does not offer unrestricted Super Admin Teacher-private content filters while visibility remains PENDING. |

## 13.2 Report View

| Concern | Standard |
|---|---|
| Purpose | Presents authorized report results, summaries, and historical context. |
| Props | Report title, current scope, criteria summary, result content, pagination, archive markers, loading, empty, unavailable, and error state. |
| States | Loading, populated, empty, filtered-empty, unavailable, error, and archived-history included. |
| Variants | Teacher operational report, Student self view, Parent linked-Student read-only view, and Platform global report. |
| Accessibility | Uses a clear report heading, readable data structure, textual archive/status markers, and accessible table/chart alternatives where applicable. |
| Usage Guidelines | Explain the report period and applied filters. Link to permitted detail only; do not use reports to bypass resource-level boundaries. |
| Validation Rules | Report results must remain scoped and must identify archived records when historical inclusion applies. |
| Notes | Flow A and Flow B are displayed as separate report concerns. No report exposes unconfirmed Teacher-private content. |

---

# 14. Settings Components

## 14.1 Settings Section

| Concern | Standard |
|---|---|
| Purpose | Groups related permitted account, Teacher Workspace, Student, Parent, or Platform settings. |
| Props | Section title, description, fields/details, save action when permitted, read-only state, and validation/error content. |
| States | Default, editing, saved, invalid, pending save, read-only, unavailable, and error. |
| Variants | Platform Settings, Teacher Workspace Settings, Student Settings, and Parent account context. |
| Accessibility | Uses logical groups, labelled fields, error association, and announced save outcome. |
| Usage Guidelines | Separate account settings from Teacher Workspace or Platform settings. Present only settings permitted for the active role. |
| Validation Rules | Teaching Subject updates are rejected after Teacher registration; Parent settings must not modify linked Student educational records. |
| Notes | Do not add notification preferences, payment gateway settings, or unconfirmed settings. |

## 14.2 Permission Assignment Matrix

| Concern | Standard |
|---|---|
| Purpose | Helps a Teacher manage explicitly assignable Teacher Staff permissions within the Teacher Workspace. |
| Props | Teacher Staff identity, confirmed assignable permission list, current assignments, save state, and safe explanatory text. |
| States | Loading, editable, pending save, saved, read-only/unavailable, invalid, and error. |
| Variants | Teacher permission management and authorized read-only review. |
| Accessibility | Uses labelled permission controls, clear selected state, keyboard operation, and a text summary of changed assignments. |
| Usage Guidelines | Use only in the Teacher’s own Teacher Workspace and only for confirmed assignable permissions. |
| Validation Rules | The component cannot assign permissions outside the current Teacher Workspace or beyond the confirmed RBAC model. |
| Notes | Teacher Staff permission granularity remains PENDING; the component must not fabricate a more detailed catalog. Permission changes are auditable. |

---

# 15. Loading Components

## 15.1 Loading Indicator

| Concern | Standard |
|---|---|
| Purpose | Communicates a short in-progress task without implying completion. |
| Props | Accessible loading label, task scope, size/emphasis, optional cancellation, and optional progress detail. |
| States | Initial loading, refreshing, submitting, uploading, scanner-starting, and processing. |
| Variants | Inline, section, page, button-pending, upload progress, and scanner initialization. |
| Accessibility | Announces meaningful loading state without repeated noise and does not hide current context. |
| Usage Guidelines | Use the smallest scope that accurately represents the waiting work. |
| Validation Rules | Must stop on success, failure, cancellation, or context change; it must not mask an authorization failure as indefinite loading. |
| Notes | Do not display data from a prior user, role, Teacher Workspace, linked Student, or Teacher relationship while new scoped data is loading. |

## 15.2 Content Placeholder

| Concern | Standard |
|---|---|
| Purpose | Reserves the structure of content that is expected shortly, reducing visual instability. |
| Props | Expected content shape, accessible loading label, and display scope. |
| States | Visible while loading and removed when content, empty state, or error state resolves. |
| Variants | Card, table row, detail list, dashboard panel, and form section placeholder. |
| Accessibility | Does not present placeholder content as actual data and exposes an understandable loading status. |
| Usage Guidelines | Mirror only meaningful layout structure; do not use for long waits or as a substitute for failure information. |
| Validation Rules | Placeholder is tied to a current scoped request and must clear when context changes. |
| Notes | It contains no fabricated names, counts, grades, payment statuses, or private data. |

---

# 16. Empty State Components

## 16.1 Empty State

| Concern | Standard |
|---|---|
| Purpose | Explains that no data or no available action exists in the current authorized context. |
| Props | Title, concise explanation, context label, optional permitted next action, illustration/icon where approved, and filter-reset action. |
| States | First-use empty, no records, filtered-empty, no permission-safe action, and no available content. |
| Variants | New Teacher Workspace, empty Group, Student activity, Parent monitoring, Lessons, Exams, report, and Flow B payment status. |
| Accessibility | Uses text-first explanation and accessible action labels; illustration is decorative unless it adds meaning. |
| Usage Guidelines | State why the area is empty and provide one next action only if permitted. |
| Validation Rules | Empty output must be scoped correctly and not reveal hidden/private records or unconfirmed features. |
| Notes | Do not use empty states to promote marketplace discovery, online payments, notifications, or a native application. |

---

# 17. Error Components

## 17.1 Error State

| Concern | Standard |
|---|---|
| Purpose | Presents a safe, recoverable task or route failure. |
| Props | Error category, safe message, retry/return action, current task label, and optional request reference where authorized. |
| States | Validation, authentication required, access denied, unavailable/not found, conflict, network/transient, unsupported action, and unexpected failure. |
| Variants | Inline, form summary, section, page, table, file upload, scanner, and route boundary error. |
| Accessibility | Announces the error, gives it a clear heading, supports keyboard access to recovery actions, and moves focus appropriately for submitted form errors. |
| Usage Guidelines | Use human-readable neutral language and provide retry only when retry is safe. |
| Validation Rules | Do not expose raw backend errors, technical internals, credentials, private identifiers, Teacher-private data, or unlinked Student data. |
| Notes | A neutral unavailable state must not reveal whether a protected resource exists. Error UI does not replace Audit Log policy. |

---

# 18. Mobile Components

## 18.1 Mobile Navigation Drawer

| Concern | Standard |
|---|---|
| Purpose | Provides responsive access to the role-aware navigation model in constrained browser viewports. |
| Props | Permitted navigation groups, current destination, open state, active role/context label, and close action. |
| States | Closed, open, active item, focus-contained, and unavailable item. |
| Variants | Platform, Teacher Workspace, Student, Parent, and public drawer. |
| Accessibility | Has a labelled navigation region, keyboard open/close, managed focus while open, and focus return to the trigger. |
| Usage Guidelines | Use as a responsive presentation of Sidebar Navigation, not a second differently authorized navigation model. |
| Validation Rules | Shows only the same scope-appropriate items as expanded navigation. |
| Notes | Responsive browser behavior does not create a native application experience or change role permissions. |

## 18.2 Responsive Action Bar

| Concern | Standard |
|---|---|
| Purpose | Keeps the current task’s primary and safe secondary actions available at constrained widths. |
| Props | Primary action, secondary actions, context label, pending state, and overflow policy. |
| States | Default, compact, pending, read-only, disabled with explanation, and overflow-open. |
| Variants | Form action bar, Attendance task bar, Exam task bar, list action bar, and file action bar. |
| Accessibility | Preserves visible labels or accessible names, keyboard order, focus, and clear overflow-menu labels. |
| Usage Guidelines | Prioritize one primary action; move only lower-priority permitted actions into overflow. |
| Validation Rules | Do not hide an essential confirmation, validation error, or context indicator solely because of narrow width. |
| Notes | Parent variants remain read-only. Do not introduce swipe-only or hover-only required actions. |

---

# 19. Accessibility Rules

All components must comply with these Web Application accessibility rules. No external conformance level is asserted because it is not confirmed by the official source documents.

1. Use semantic structure and native interaction behavior where appropriate.
2. Every interactive component is keyboard operable and has visible focus.
3. Controls have persistent labels or accessible names; icons alone are insufficient for consequential or unfamiliar actions.
4. Error, required, selected, Archive, pending, read-only, and success meaning is available in text or programmatic form, not color alone.
5. Form fields associate labels, instructions, values, and errors.
6. Dialogs manage focus predictably; menus, drawers, and scanner panels expose clear open/close states.
7. Loading, success, error, route, context, and scanner outcome changes are announced at an appropriate level without repetitive disruption.
8. Tables, cards, filters, and responsive components retain readable relationships under zoom and reflow.
9. QR components provide clear camera permission/unsupported guidance and the confirmed manual Attendance path where it is applicable.
10. Accessibility support must not reveal data, alter Teacher Workspace isolation, make Parent views writable, or bypass backend authorization.

---

# 20. Component Naming Convention

Component names use descriptive **PascalCase** nouns or noun phrases. Names identify the reusable user-facing responsibility, not a database table, API endpoint, CSS class, or implementation pattern.

| Category | Naming rule | Examples |
|---|---|---|
| Primitives | Clear control/status noun | `ActionButton`, `TextField`, `SelectField`, `StatusIndicator` |
| Layout/navigation | Structural role noun | `ApplicationShell`, `PageFrame`, `ContextBar`, `SidebarNavigation` |
| Data/feedback | What is displayed or communicated | `DataTable`, `ArchiveIndicator`, `InlineFeedback`, `ErrorState` |
| Domain composites | Canonical domain term plus component responsibility | `AttendanceRegister`, `DynamicQRDisplay`, `BubbleSheetSelector`, `ReportFilterBar` |
| Role-aware variants | Variant/property name, not a duplicate component name when behavior is shared | Parent read-only variant of `ExamAttemptSummary`, not `ParentExamWidget` |

Naming constraints:

- Use **EducationalGrade**, **TeacherWorkspace**, **Lesson**, **Attendance**, **Subscription**, **PaymentStatus**, **Archive**, and **AuditLog** in component names where the domain term is needed.
- Use `Subscription` only for Flow A. Use `PaymentStatus` for Flow B.
- Do not use `Course` for Lesson, `Delete` for Archive actions, or marketplace terminology.
- Do not encode a permission code, raw identifier, endpoint path, or backend implementation concern into a component name.
- Do not create component names that imply notifications, payment processing, native mobile, cross-Teacher discovery, or another excluded Version 1 feature.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority. |
| Requested component coverage | Passed — all 20 requested library areas are documented, and every catalog component includes Purpose, Props, States, Variants, Accessibility, Usage Guidelines, Validation Rules, and Notes. |
| Reusability | Passed — primitives, shared composites, and domain composites have bounded presentation responsibilities and do not own backend enforcement. |
| Scope | Passed — no React code, HTML, CSS, API details, database tables, UI mockups, or implementation details are included. |
| Role and tenant boundaries | Passed — Teacher Workspace isolation, Student self scope, Parent linked-Student read-only access, Teacher Staff assigned permissions, and constrained Super Admin visibility are preserved. |
| Flow A / Flow B | Passed — Subscription components identify Flow A only; Flow B is consistently named payment status; no payment processing is implied. |
| Archive and audit | Passed — Archive/restore language replaces deletion, historical records are distinguishable, and components do not replace Audit Log policy. |
| Files, Attendance, and Exams | Passed — private Lesson/file access, Image/PDF Student Homework, no Parent uploads, three Attendance methods, daily Dynamic QR Code, private Question Bank, and supported Exam types are preserved. |
| Version 1 exclusions | Passed — no notification system, marketplace, payment gateway, video homework, native mobile requirement, unconfirmed impersonation, or unconfirmed Attendance method is introduced. |
| Accessibility and terminology | Passed — Web Application accessibility rules are included without asserting an unconfirmed conformance level; canonical terminology is used consistently. |
