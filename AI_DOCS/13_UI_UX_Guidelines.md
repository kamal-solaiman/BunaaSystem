# 13 — UI/UX Guidelines

## Document Scope

This document defines Version 1 UI/UX rules and standards for the Unified Education Platform Web Application. It guides consistent, understandable, accessible, and secure user experiences for Super Admin, Teacher, Teacher Staff, Student, and Parent contexts.

This is a UI/UX standards document only. It does not provide UI mockups, HTML, React code, CSS, API definitions, database tables, or implementation details. `AI_DOCS/00_Project_Context.md` remains the official Single Source of Truth.

All rules in this document apply within the confirmed Web Application scope. Responsive browser use does not introduce a native mobile application requirement. Arabic (default) and English (fully supported), automatic RTL/LTR, timezone, currency, target market, detailed accessibility conformance level, browser matrix, Teacher Staff permission granularity, Super Admin Teacher-private content visibility, non-payment enforcement, and Lesson video hosting/protection remain PENDING where stated in the official documents.

---

# 1. Design Philosophy

The Platform experience must be **clear, calm, trustworthy, and task-focused**. It serves daily educational operations where users need to understand their current context and act without confusion.

1. **Context before action:** Always make the active role and relevant context understandable before presenting data or actions. For Teacher and Teacher Staff, this means the current Teacher Workspace. For Parent, this means the selected linked Student. For Student records, Teacher-partitioned context must be understandable where relevant.
2. **Trust through clarity:** Show only information the current role is entitled to see. Do not imply that a hidden action is the security boundary; backend authorization remains authoritative.
3. **Progressive disclosure:** Start with the information needed for the immediate task and reveal secondary detail when it is relevant. Do not overload dashboards, forms, or tables.
4. **Consistency over novelty:** Similar actions, states, terminology, and feedback use the same patterns across all role contexts.
5. **Historical integrity:** Use **Archive**, never “Delete,” in product language. Distinguish active content from archived historical information.
6. **Respect for educational workflows:** Optimize frequent actions such as Attendance, Student management, Homework, Lessons, Exams, and Parent monitoring for accuracy and low cognitive load.
7. **No invented capabilities:** The experience must not imply payment gateways, notifications, marketplace browsing, cross-Teacher content discovery, native mobile applications, video homework, or other out-of-scope features.

---

# 2. User Experience Principles

| Principle | UI/UX rule |
|---|---|
| Role clarity | Identify the active role context in persistent application structure without exposing roles or data the user does not hold. |
| Scope clarity | Present Teacher Workspace, linked Student, and Teacher relationship context close to the data or action it governs. |
| Least surprise | Use predictable navigation, labels, action placement, confirmation, and feedback. |
| Permission-aware guidance | Show permitted actions clearly. When an action is unavailable, provide an understandable explanation only when doing so does not disclose private information. |
| Read-only distinction | Parent monitoring views are clearly read-only. Read-only status must not be conveyed by color or disabled controls alone. |
| Safe recovery | Preserve valid entered form values after a correctable validation failure. Provide retry or return paths for recoverable failures. |
| Status honesty | Do not show a task as complete until the Platform confirms completion. Do not invent a grade, payment status, Attendance result, or Subscription result. |
| Privacy by default | Do not display cross-Teacher, unlinked Student, or unauthorized file details in navigation, errors, empty states, or search suggestions. |
| Terminology consistency | Use Teacher Workspace, Educational Grade, Lesson, Attendance, Subscription, payment status, Flow A, Flow B, Archive, and Audit Log consistently. |

Role differences are meaningful product boundaries, not merely variations in navigation. A Parent view must never visually imply an ability to change a linked Student’s Attendance, Homework, Exams, grades, or Flow B payment status.

---

# 3. Design System

The design system is a shared set of semantic visual and interaction rules built with the approved frontend styling system, Tailwind CSS. It is organized around reusable semantic tokens and accessible components, not one-off visual values.

## 3.1 Token categories

| Token category | Use |
|---|---|
| Color | Semantic surface, text, border, focus, action, success, warning, error, information, Archive, and read-only meanings. |
| Typography | Roles for page title, section title, body, label, helper text, numeric data, and status text. |
| Spacing | A consistent scale for component padding, gaps, layouts, form groups, and page rhythm. |
| Shape and elevation | Shared rules for borders, radius, separators, layering, and focus visibility. |
| Interaction | Standard states for default, hover, focus, pressed, disabled, loading, selected, read-only, error, and success. |

## 3.2 Component rules

- Shared primitives are domain-neutral and accessible; feature components own domain workflows.
- A component must accept explicit content and state. It must not infer Teacher Workspace, Parent link, authorization, or record ownership from presentation alone.
- Components expose semantic states rather than requiring each feature to create a different visual meaning for error, Archive, read-only, or loading.
- Components must remain understandable at responsive widths and under browser zoom.
- New component variants are added only when a real repeated user need exists; avoid visually similar duplicate controls.

No product brand palette, font family, visual style, or dark-mode requirement is confirmed in the official source documents. Such choices require product design approval and must be implemented through semantic tokens, not hard-coded throughout feature surfaces.

---

# 4. Color Guidelines

Color communicates meaning but never serves as the only carrier of meaning.

| Semantic color role | Permitted meaning |
|---|---|
| Primary action | The principal permitted action in the current task. |
| Neutral surface/text | Structure, hierarchy, default content, and non-actionable information. |
| Success | A completed, backend-confirmed action or valid available result. |
| Warning | A potentially consequential action, incomplete state, or attention-needed condition that is not an error. |
| Error | Validation failure, rejected action, or unrecoverable task failure. |
| Information | Neutral contextual help, pending status, or explanatory content. |
| Archive | Historical/archived state, always paired with the word “Archived” or equivalent text. |
| Read-only | A constrained view, always paired with explicit text or an accessible status. |
| Focus | Keyboard focus. It must remain clearly visible independent of hover or selection styling. |

Rules:

1. Use semantic token names rather than feature-specific colors, for example a status uses the same success meaning in Attendance, Homework, Exams, and Subscriptions.
2. Pair every status color with text, an icon with an accessible name, or both.
3. Maintain sufficient contrast for text, interactive controls, focus treatment, chart/data distinction, and disabled/read-only states.
4. Do not use red to mean only “required,” green to mean only “selected,” or color alone to identify the active Student, Teacher Workspace, or role.
5. Do not imply that Flow A or Flow B is more secure, paid, or finalized merely through color. The displayed backend status and clear label are authoritative.
6. Do not assign color meanings that conflict with Archive, error, or warning semantics.

---

# 5. Typography

Typography must make instructional and operational information easy to scan without creating visual noise.

- Use a small, semantic type scale: page title, section title, component title, body, label, helper text, table data, and status text.
- Use sentence-style labels and concise task-focused headings. Avoid unexplained abbreviations.
- Make page titles describe the current resource or task, such as **Educational Grades**, **Attendance**, **Lessons**, or **Flow B payment status**.
- Use clear text hierarchy before relying on weight, color, or capitalization.
- Reserve strong emphasis for page hierarchy, key status, and important values; do not use excessive all-caps or emphasis for ordinary data.
- Use tabular or otherwise consistently aligned numerals where comparing prices, dates, grades, quantities, or Attendance counts improves comprehension.
- Wrap long names, descriptions, and file names safely rather than truncating essential meaning. If truncation is necessary, provide an accessible way to discover the complete value.
- Support browser text scaling and zoom without overlap, clipped controls, or inaccessible horizontal-only tasks.

A final font family and localization-specific typographic requirements are not confirmed. Typography must therefore remain adaptable and not assume a language, writing direction, currency, or date/time format.

---

# 6. Spacing System

Use one consistent spacing scale for layout rhythm, component density, and touch/keyboard usability.

1. Apply the same base spacing increment across page margins, card padding, table cells, form groups, button groups, and modal sections.
2. Use tighter spacing only for strongly related information, such as a field label and helper text; use larger spacing to separate independent tasks or sections.
3. Keep action groups visually separate from destructive/consequential actions and from unrelated navigation controls.
4. Preserve sufficient target size and separation for interactive controls, particularly Attendance actions, scanner controls, file actions, and table-row menus.
5. Use responsive spacing that reduces wasted space on narrow browser viewports without collapsing hierarchy or making actions difficult to select.
6. Avoid using spacing alone to imply permission, status, or record ownership.

The exact numeric scale is a design-system implementation decision. Every feature must consume the same approved scale rather than define a local one.

---

# 7. Icons

Icons support recognition; labels and accessible names provide the meaning.

- Use one approved icon family consistently across the Web Application.
- Use recognizable icons for common actions such as search, filter, sort, upload, download where authorized, scan, edit, Archive, restore, close, and return.
- Pair unfamiliar, high-impact, or role-sensitive icons with visible text. Icon-only controls are limited to common actions and require an accessible name.
- Do not use an icon alone for Archive, permission denial, payment status, exam grading state, Attendance, or a privacy-sensitive action.
- Use a consistent visual distinction between **Archive** and permanent deletion; Version 1 must never present a permanent-delete icon/action.
- QR scanner controls must use clear wording in addition to any camera or QR icon.
- Icons must remain readable in all semantic states and must not depend on color alone.

---

# 8. Buttons

Buttons represent explicit user intent. Their hierarchy must make the safest expected path obvious.

| Button category | Rule |
|---|---|
| Primary | One principal permitted action per focused task area, such as Save, Submit Homework, Start Scan, or Create Group. |
| Secondary | A permitted supporting action that does not compete with the primary action. |
| Tertiary/text | Low-emphasis navigation or contextual action; it remains keyboard accessible and visibly interactive. |
| Consequential | Archive, restore, submit Exam, record Attendance correction, or other impactful action. It requires clear wording and confirmation where required by risk. |
| Disabled/read-only | Used only when the reason is understandable in context. Disabled appearance is never the sole explanation of Parent read-only access or an unavailable permission. |

Rules:

- Use action-first labels: **Create Educational Grade**, **Record Attendance**, **Submit Homework**, **Archive Group**, **Restore Lesson**.
- Avoid vague labels such as “OK,” “Yes,” or “Click here” for consequential actions.
- Prevent accidental repeated submissions while an action is pending, while retaining clear pending feedback.
- Do not render an unavailable action as if it will succeed. Where it is safe and useful, explain the missing prerequisite or permission without exposing protected data.
- Use **Archive** and **Restore**, never “Delete,” for Version 1 record lifecycle actions.
- A Parent must not receive edit, submit, record, grade, archive, restore, or payment-status update buttons for linked-Student educational data.

---

# 9. Forms

Forms must minimize errors, preserve user input, and make backend validation outcomes understandable.

1. Present fields in the order the user naturally supplies information.
2. Give every input a persistent visible label; placeholder text is not a replacement for a label.
3. Identify required information clearly with text and programmatic meaning, not color alone.
4. Provide concise helper text before input where a format, consequence, or constraint needs explanation.
5. Validate client-side structure early and map authoritative backend validation errors to the relevant fields after submission.
6. Preserve valid entered values when validation fails, a conflict occurs, or a recoverable request fails.
7. Place an error summary at the start of a submitted invalid form and associate each field error with its field.
8. Do not pre-fill or reveal sensitive, unrelated, cross-Teacher, or unlinked Student data.
9. Use safe selection lists: active Groups and Educational Grades only for active assignment; archived records belong in clearly historical contexts.
10. State irreversible-in-practice consequences before submission, such as a Teacher’s Teaching Subject being selected at registration and not changeable afterward.

Form-specific boundaries:

- Student registration must communicate that duplicate Student accounts are prevented and that Teacher-created accounts can later be activated.
- Group forms must make Price and Pricing Type clear; Pricing Type is limited to Monthly or Per Lesson and is the Flow B fee basis.
- Homework forms allow Text, Image, and PDF only. They must not suggest video homework.
- Parent monitoring views are not editable forms for Student educational information or payment status.
- A form must not offer payment gateway fields or transaction actions in Version 1.

---

# 10. Tables

Tables support comparison, review, and management of structured data. They must not become a substitute for a clear task flow on narrow screens.

- Use tables for comparable records such as Students, Groups, Attendance history, Homework, Exams, Teacher Staff, reports, payment-status records, Subscriptions, and Audit Logs where permitted.
- Give each table a clear title, current scope, column headers, and a meaningful empty state.
- Keep high-value columns visible first: identity, relevant Teacher/Group context, status, date, and permitted action.
- Align dates, quantities, grades, and monetary values consistently for scanning; do not assume a currency format until confirmed.
- Support only documented filtering, sorting, and pagination. Explain active filters and provide a clear reset when appropriate.
- Keep row actions contextual and keyboard accessible. Consequential actions need explicit labels rather than an ambiguous icon-only menu.
- Clearly identify archived records and historical rows. Archived data must never look active.
- On narrow screens, preserve each record’s identity, status, and permitted actions through a responsive alternative such as prioritized columns or a record summary; do not require inaccessible horizontal-only reading.
- A table must show only data returned within the active role and scope. Do not expose hidden rows, counts, suggestions, or exports for unauthorized data.

---

# 11. Cards

Cards group a bounded summary, task, or record without replacing page hierarchy.

- Use cards for dashboard summaries, grouped task entry points, contextual record summaries, empty-state guidance, and small independently actionable content areas.
- A card has one clear purpose, descriptive heading, concise supporting content, status where needed, and only relevant permitted actions.
- Avoid nested cards that obscure hierarchy or create unnecessary visual density.
- Use cards to separate Flow A Subscription information from Flow B payment status when both are presented in an authorized context.
- Clearly mark a card as **Archived**, **Read-only**, **Pending**, or **Unavailable** when that state changes the user’s interpretation.
- Do not put sensitive file paths, authentication information, cross-Teacher data, or unlinked Student content into a card summary.

---

# 12. Modals

Modals are reserved for focused decisions or short self-contained tasks that benefit from keeping the user’s current page context.

- Use a modal for confirmation, a narrowly scoped edit, contextual details, or an interruption requiring a decision.
- Do not use a modal for lengthy multi-step creation, complex reporting, Exam taking, QR scanning, or tasks requiring significant navigation; use a dedicated task view instead.
- Give every modal a specific title, concise purpose, clear primary and cancel/close actions, and visible consequences for impactful actions.
- Move keyboard focus into the modal, keep focus within it while open, and return focus to the invoking control on close where possible.
- Closing a modal must not silently discard entered work without an appropriate unsaved-change warning.
- Archive and restore confirmations must name the affected record and explain that historical data is retained; they must not use permanent-delete language.
- Do not place authorization explanations in a modal if doing so would reveal protected record existence or private data.

---

# 13. Navigation

Navigation must be role-aware, scope-aware, predictable, and concise.

1. Group navigation around confirmed product capabilities: Dashboard, Educational Grades, Groups, Students, Attendance, Homework, Lessons, Exams, Reports, Users, Settings, Files where permitted, and role-appropriate Payments/Subscriptions.
2. Present only areas available to the active role context and known permission set. This is usability behavior, not security enforcement.
3. Separate **Subscriptions** (Flow A) from **Payments** or **payment status** (Flow B) in labels, navigation groups, and destination headings.
4. Do not include marketplace/catalog navigation, payment gateway actions, notification center entries, cross-Teacher discovery, or native application prompts.
5. Use a clear active-route treatment that remains visible without color alone.
6. Preserve validated context in navigation: a Parent’s selected linked Student and a Student’s Teacher relationship should not be silently changed by an unrelated route change.
7. Use clear destination labels and avoid exposing internal route names, identifiers, or permission codes.

---

# 14. Sidebar

The sidebar is the primary persistent navigation region in expanded browser layouts.

- Display the active role context and, where applicable, the Teacher Workspace identity or Parent selected linked Student context without exposing unauthorized information.
- Organize destinations into small logical groups, with a clear current-location indicator.
- Keep high-frequency operational areas readily reachable for Teacher workflows: Students, Attendance, Homework, Lessons, and Exams.
- Teacher Staff entries are filtered by Teacher-assigned permissions; unavailable items must not imply a permission assignment.
- Parent navigation exposes only linked-Student, read-only monitoring capabilities.
- Super Admin navigation remains Platform scoped and must not present a route to impersonate or “Login as Teacher.”
- On constrained widths, the sidebar may collapse into an accessible navigation control. The collapsed state must preserve keyboard access, route clarity, and the active context.
- Do not use the sidebar to display sensitive dashboard data, credentials, or private file content.

---

# 15. Header

The header provides global orientation and immediate access to safe, high-frequency contextual controls.

A header may contain:

- Product identity.
- Current role context.
- Current Teacher Workspace context for Teacher/Teacher Staff.
- Parent Student Switcher for validated linked Students.
- Student Teacher relationship context where applicable.
- Account/settings access and logout.

Rules:

- The header must not become a second full navigation system or a dense dashboard.
- Context selectors must clearly show what is changing and revalidate context through the backend.
- A Parent Student Switcher lists only linked Students and does not provide educational-data editing.
- A context label must not be used as proof of authorization; a later access denial is handled safely.
- Do not add a Version 1 notifications bell, payment gateway shortcut, or marketplace search.

---

# 16. Dashboard Layout

Dashboards provide operational orientation, not a replacement for detailed reports.

| Role context | Dashboard standard |
|---|---|
| Teacher | Show a Teacher Workspace-scoped operational overview of Students, Groups, Attendance, Homework, Exams, Lessons, Reports, Users, Settings, and permitted payment-status information. |
| Teacher Staff | Show only Teacher Workspace sections and summaries allowed by explicit Teacher-assigned permissions. |
| Student | Show only the Student’s own information, with Teacher-partitioned context for schedule, Homework, Lessons, Exams, and Flow B status. |
| Parent | Show only linked-Student, read-only monitoring information with a clear selected Student context. |
| Super Admin | Show Platform-level administration and global reporting within confirmed visibility boundaries. |

Dashboard rules:

1. Place current context, urgent actionable work, and a small number of meaningful summaries before secondary detail.
2. Do not make a dashboard a data dump. Use cards and prioritized summaries with paths to detail views.
3. Clearly separate Flow A Subscription status from Flow B payment status.
4. Identify archived information as historical; do not count or show it as active by default.
5. Provide useful empty states for new Teacher Workspaces, Students with no activity, Parents with no available linked-Student records, and Platform contexts with no data.
6. Do not show a Super Admin Teacher-private content summary unless that visibility is confirmed.

---

# 17. Mobile Responsiveness

Version 1 is a Web Application only. Responsive guidelines ensure usable browser layouts across viewport sizes; they do not define or require a native mobile application.

- Start each task with a single-column, readable content order that can expand for larger viewports.
- Preserve task priority: current context, primary action, status, essential information, then secondary information.
- Reflow forms, cards, tables, filters, and button groups without requiring a specific device category or browser matrix, which remain unconfirmed.
- Avoid hover-only information and hover-only actions. All essential interactions need keyboard and touch-compatible equivalents.
- Keep QR scanning available through the browser when supported; provide the confirmed manual Attendance path when camera access is unavailable.
- Keep file selection, upload status, error messages, confirmation dialogs, and context selection usable in constrained viewports.
- Do not remove labels, status meaning, warnings, or authorization context merely to save space.
- Responsive behavior must not change permissions, Teacher Workspace isolation, Parent read-only rules, or active/archive state.

---

# 18. Accessibility

Accessibility guidelines apply to the Version 1 Web Application. No specific external conformance level is asserted because it is not confirmed in the Project Context.

1. Use semantic structure and native controls where possible.
2. Make every interactive control keyboard operable with visible focus.
3. Provide programmatic and visible labels, instructions, and error associations for all form fields.
4. Announce meaningful route changes, loading changes, mutation results, and errors without excessive interruption.
5. Manage focus after opening/closing modals, submitted form errors, route errors, and successful task completion.
6. Do not communicate permission, Archive, read-only, error, selected, or success states by color alone.
7. Support browser zoom, text scaling, readable contrast, reflow, and usable target size.
8. Provide an accessible camera-permission/unsupported-browser explanation and a confirmed alternative path for Attendance where one exists.
9. Explain Parent read-only constraints in text rather than only disabled controls.
10. Test keyboard and assistive-technology journeys for each role, including tables, forms, files, QR scanning, route changes, and error recovery.

Accessibility must never expose additional data, bypass authorization, make a Parent writable, or weaken Teacher Workspace isolation.

---

# 19. Loading States

Loading states show that work is in progress without creating uncertainty or blocking unrelated work unnecessarily.

- Use a page-level loading state only for critical route/context initialization, such as authenticated-user bootstrap or a required role context.
- Use local loading states for independent dashboard panels, tables, record regions, scanner initialization, and file transfers so the rest of the page remains usable.
- Maintain stable layout dimensions where possible to reduce disorientation.
- Mark action controls as pending after submission and prevent duplicate submission without hiding the task outcome.
- Distinguish initial loading, background refresh, form submission, file transfer, scanner initialization, and report preparation states.
- Do not present stale data from a previous user, role, Teacher Workspace, linked Student, or Teacher relationship as current data while the new context loads.
- Loading text must be understandable and accessible; it must not claim success before backend confirmation.

---

# 20. Empty States

Empty states explain that there is currently no data or no available action in the active authorized context. They are not generic errors.

| Context | Empty-state guidance |
|---|---|
| New Teacher Workspace | Explain the next permitted setup step, such as creating an Educational Grade or Group, without assuming data exists. |
| Group | State that no Students are currently assigned and offer an Add Student action only to authorized users. |
| Student | Explain that no current Teacher relationship, schedule item, Homework, Lesson, Exam, or Flow B status is available, as applicable. |
| Parent | State that no permitted record is available for the selected linked Student; do not suggest access to another Student. |
| Reports/history | Explain whether no records match the selected valid filters and provide a safe filter reset. |
| Lessons | State that no authorized Lessons are available; never suggest marketplace discovery. |
| Payments | State that no recorded Flow B payment status is available; do not offer online payment actions. |

Every empty state includes a clear title, a short explanation, and one next action only when the role is permitted to take it. Do not use empty states to reveal unavailable private records, archived active-looking records, or unconfirmed features.

---

# 21. Error States

Error states explain failure safely and direct the user toward recovery where possible.

| Error type | UI/UX standard |
|---|---|
| Validation | Identify the affected field(s), explain the correction, preserve valid inputs, and move focus appropriately after submission. |
| Authentication | Explain that authentication is required or has expired, clear protected context safely, and return to the approved authentication journey. |
| Authorization | Use a generic access-denied message without confirming whether a protected record exists. |
| Not found/unavailable | Use neutral wording that does not distinguish an absent record from an inaccessible private record. |
| Conflict | Explain that the current state prevents completion, preserve safe work, and offer refresh/retry where appropriate. |
| Network/transient failure | Explain that the action could not be completed and provide a non-duplicating retry path. |
| Unsupported action | Explain the confirmed constraint, such as unsupported Homework format or no online payment processing, without suggesting a workaround outside Version 1. |

Error content must not expose request headers, credentials, internal identifiers, Teacher-private data, unlinked Student data, private file paths, technical stack details, or raw server messages. It must not replace required backend Audit Log behavior.

---

# 22. Success Messages

Success feedback is an in-product, transient confirmation of a completed user action. It is not a notification feature.

- Show success feedback only after the Platform confirms the action.
- State what happened in concise task language, such as “Educational Grade created,” “Homework submitted,” “Attendance recorded,” or “Group archived.”
- For a consequential action, pair the message with an immediate, safe next step when appropriate, such as viewing the updated record. Do not provide an undo action that would violate Audit Log, Archive, or business-rule requirements.
- Do not use success feedback to claim an action that is pending manual grading, pending status recording, or requires later backend processing.
- Announce meaningful success feedback accessibly without interrupting the user repeatedly.
- Do not use success feedback as a substitute for the Audit Log or as a push, email, or SMS notification.

---

# 23. Confirmation Dialogs

Confirmation dialogs protect against consequential or easily mistaken actions.

Require confirmation for actions such as:

- Archive or restore of a record.
- Attendance corrections where the action changes a recorded state.
- Submission of an Exam when the user is completing the attempt.
- Submission of Homework when the action is intended as final according to the Homework context.
- Other high-impact state changes where the user needs to confirm intent.

Confirmation rules:

1. Name the action and affected record/context in plain language.
2. Explain the immediate consequence and any known historical effect.
3. Use a precise confirm label, such as **Archive Group** or **Submit Exam**, not “Yes.”
4. Make the safer cancel action easy to find and focus by default when appropriate.
5. Do not require confirmation for ordinary navigation, harmless filtering, or every routine create/save action.
6. Do not confirm an action the user lacks permission to perform; deny access safely instead.
7. Archive confirmation must explain that the record is retained historically and should never describe a permanent deletion.

---

# 24. Notifications

Notifications are explicitly out of scope for Version 1. The UI/UX must not introduce:

- A notification center or bell.
- Push notifications.
- Email notifications.
- SMS notifications.
- Notification preferences.
- Notification badges, unread counts, delivery history, or notification management journeys.

This exclusion does not prevent local, in-context UI feedback such as loading, validation, error, success, or confirmation messages while the user is actively using the Web Application. Such feedback is not a notification subsystem and must not be represented as one.

---

# 25. File Upload Experience

File upload experiences must be transparent about allowed formats, ownership, privacy, and progress.

1. Identify the owning task before file selection, for example Teacher Workspace file, Lesson file, or Student Homework submission.
2. State the permitted file type before selection. Student Homework submissions support Image and PDF only; video homework must not be offered.
3. Show selected file name, permitted type/status, transfer progress where available, completion, and recoverable failure clearly.
4. Allow cancellation before completion where safe, and prevent duplicate uploads while the same transfer is pending.
5. On failure, retain the user’s task context and provide a safe retry path; do not expose storage paths or internal file errors.
6. Explain that access is controlled by the Platform. A browser-visible file reference must not imply broad access.
7. Do not provide Parent upload controls; Parent access is read-only.
8. Lesson video upload/access must communicate private Teacher-owned context. The detailed hosting/protection behavior remains PENDING and must not be implied by UI wording.
9. Archive/restore file actions use the same historical-retention language as other resources.

---

# 26. QR Scanner Experience

The QR scanner experience supports Attendance within the Web Application and must remain focused, permission-aware, and recoverable.

- Clearly identify whether the task is Student Dynamic QR Code scanning or Teacher/Teacher Staff ID Card scanner-assisted Attendance.
- Request camera access only after the user intentionally starts a scan and explain why access is needed.
- Show scanner initialization, active scanning, successful scan, invalid scan, duplicate scan, permission-denied, and unsupported-browser states in clear language.
- Never treat a scanned value as proof that Attendance was recorded. Confirm the outcome only after backend validation of Student identity, Teacher relationship, Teacher Workspace context, and Attendance rules.
- For Student Dynamic QR Code Attendance, make clear that the code is generated daily and is valid only for the relevant Attendance context.
- For printed ID Card scanning, identify the scanner-device input state and confirm the result only after backend validation.
- When camera/scanner access is unavailable, direct the user to the confirmed manual Attendance path for an authorized Teacher or Teacher Staff member. Do not invent a Student self-entry fallback.
- Stop active scanning when the user exits the task, cancels, receives a final result, or encounters a recoverable failure.
- QR scanner feedback must not reveal a Student’s identity, enrollment, or Teacher relationship to an unauthorized viewer.

Attendance UI must never imply that Attendance affects Billable Student calculation. Billable Student calculation uses Enrollment duration only.

---

# 27. Performance Guidelines

UI/UX performance means the experience remains responsive, understandable, and correct while data is loading or changing.

- Prioritize initial rendering of the active role context and the immediate task over nonessential content.
- Load feature areas and heavy browser capabilities, including QR scanner support, only when their authorized route/task needs them.
- Use paginated, filtered, and scoped list experiences rather than rendering unbounded records.
- Keep dashboard panels independently loadable so a delayed report or file status does not block unrelated daily work.
- Preserve contextual correctness over perceived speed: never reuse data between users, roles, Teacher Workspaces, linked Students, or Student Teacher relationships.
- Provide responsive feedback for form submission, Exam submission, Attendance recording, and uploads while preventing accidental duplicate requests.
- Avoid visual instability, unexpected focus movement, and layout shifts that disrupt reading or scanning.
- Optimize media/file presentation without treating private content as public. Private Lesson access continues to require backend authorization.
- Do not require Redis, WebSockets, Docker, Kubernetes, S3 Storage, microservices, or a native application to provide the Version 1 experience.

Performance improvements must never weaken authorization, Archive behavior, Audit Log requirements, Parent read-only access, Teacher Workspace isolation, or Flow A / Flow B separation.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority. |
| Requested UI/UX scope | Passed — all 27 requested standards are included; no mockups, HTML, React code, CSS, APIs, database tables, or implementation details are generated. |
| Web Application boundary | Passed — responsive browser guidance and QR scanning remain within the Web Application; no native mobile requirement is introduced. |
| Role and scope clarity | Passed — Teacher Workspace isolation, Student per-Teacher partitioning, Teacher Staff permissions, Parent linked-Student read-only access, and constrained Super Admin visibility are preserved. |
| Authorization | Passed — UI permission awareness is explicitly non-authoritative; no visual control is treated as backend enforcement. |
| Archive and history | Passed — Archive replaces deletion and historical/archived information is clearly distinguished from active information. |
| Flow A / Flow B | Passed — Subscription and payment-status labels, navigation, cards, dashboards, and feedback keep the two money flows separate; no payment processing is introduced. |
| Version 1 exclusions | Passed — no notification system, payment gateway, marketplace, cross-Teacher discovery, video homework, or unconfirmed impersonation is introduced. |
| Files and QR Attendance | Passed — private Teacher-owned Lessons, Image/PDF Student Homework, Parent upload denial, daily Dynamic QR Code, ID Card scan, and manual Attendance boundaries are preserved. |
| Accessibility and localization | Passed — Web Application accessibility rules are included without asserting an unconfirmed external conformance level; no language, currency, timezone, market, browser matrix, or writing direction is assumed. |
| Terminology | Passed — Teacher Workspace, Educational Grade, Lesson, Attendance, Subscription, payment status, Flow A, Flow B, Archive, and Audit Log are used consistently. |
