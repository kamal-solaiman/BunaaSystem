# 15 — Exam Engine

## Document Scope

This document defines the confirmed Version 1 Exam Engine for the Unified Education Platform. It covers the business boundaries, roles, supported assessment behavior, result lifecycle, and constraints for Teacher Workspace Exams.

It does not provide source code, APIs, database tables, UI implementation, grading algorithms, scheduling algorithms, or infrastructure implementation. It uses only confirmed project requirements. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails over this document if a conflict is found.

The Exam Engine is delivered through the Version 1 Web Application using React 19 and communicates with the Laravel 12 backend. The experience is optimized for this boundary: React 19 presents only authorized, Teacher-partitioned Exam tasks and states, while Laravel 12 remains authoritative for authentication, authorization, Teacher Workspace isolation, Question Bank ownership, validation, grading persistence, Archive behavior, and Audit Log creation. The Engine does not require Redis, WebSockets, Docker, Kubernetes, S3 Storage, or microservices for Version 1.

---

# 1. Feature Overview

The Exam Engine enables a Teacher to assess Students through Exams composed from the Teacher’s private, Teacher-owned Question Bank. It supports **Multiple Choice**, **True/False**, **Essay**, and **Bubble Sheet** questions.

An Exam, every attempt, every answer, and every grade belongs to one Teacher Workspace. A Student may have Exams from multiple Teachers, but those records remain partitioned by Teacher. A Parent may monitor available Exam information and grades for linked Students only, in read-only mode.

The Exam Engine is not a marketplace, public assessment catalog, cross-Teacher Question Bank, native mobile feature, payment feature, notification feature, or permanent-deletion workflow.

---

# 2. Objectives

The confirmed objectives are to:

1. Let Teachers maintain private Question Banks inside their own Teacher Workspaces.
2. Let Teachers create and make available workspace-scoped Exams using only their own Questions.
3. Let Students view and attempt Exams assigned or made available through their own Teacher relationships.
4. Support the four confirmed question types without exposing the private Question Bank outside authorized Exam context.
5. Support automatic grading for confirmed automatically gradable behavior, including Bubble Sheet where applicable.
6. Support authorized Teacher/Teacher Staff Essay grading handling.
7. Preserve Exam attempts and grades when a Student moves Groups under the same Teacher.
8. Allow Parents to monitor linked Students’ available Exam status and grades read-only.
9. Preserve historical Exam and Question Bank records through Archive rather than permanent deletion.
10. Record required Exam, Question Bank, grading, publication/availability, and attempt/submission events in the Audit Log.

---

# 3. Supported Exam Types

Version 1 confirms one Teacher Workspace Exam capability with four supported **question types**. The source documents do not define separate named Exam categories beyond those questions.

| Confirmed capability | Version 1 rule |
|---|---|
| Teacher Workspace Exam | Created by a Teacher or authorized Teacher Staff in the current Teacher Workspace using only that Teacher’s private Question Bank. |
| Multiple Choice | Supported Question Bank and Student answer type. |
| True/False | Supported Question Bank and Student answer type. |
| Essay | Supported Question Bank and Student answer type; authorized grading handling is required where applicable. |
| Bubble Sheet | Electronic on-screen answer format that simulates paper bubble sheets; automatic grading is supported where applicable. |

No additional Exam type is confirmed. The Engine must not introduce oral, video, proctoring, practical, public, marketplace, cross-Teacher, or native-mobile Exam types in Version 1.

---

# 4. Question Bank

The **Question Bank** is private and Teacher-owned. Each Teacher Workspace has its own Question Bank boundary.

- A Teacher may manage the Teacher’s own Question Bank.
- Teacher Staff may perform Question Bank actions only when the Teacher explicitly assigned the relevant permission.
- An Exam may contain only Questions from the owning Teacher Workspace’s Question Bank.
- A Teacher, Teacher Staff member, Student, Parent, or Super Admin must not use Question Bank access to view another Teacher’s private Questions.
- A Student can see Question content only within an Exam assigned or made available through that Student’s Teacher relationship.
- A Parent cannot access Teacher-owned Question Bank content outside the linked Student’s permitted Exam visibility.
- Questions may be Archived and restored by authorized actors; permanent deletion is not allowed.
- Question Bank creation, update, Archive, restore, and other important actions are recorded in the Audit Log according to policy.

Question Bank ownership must be evaluated by the backend before retrieval, Exam composition, grading, or reporting. Frontend visibility and route structure do not establish access.

---

# 5. Question Categories

The official Version 1 requirements define **Question Types**, not a separate taxonomy of Question Categories. Therefore, no additional categories, tags, topics, difficulty levels, curriculum classifications, labels, or scoring classifications are defined by this document.

For Version 1:

- The only confirmed classification is the Question Type: Multiple Choice, True/False, Essay, or Bubble Sheet.
- A Teacher’s private Question Bank remains scoped to that Teacher Workspace regardless of any future categorization.
- Any future Question Category must be separately approved and must not expose Questions across Teacher Workspaces, alter Student scope, or weaken Question Bank privacy.

---

# 6. Question Types

| Question Type | Student interaction | Grading rule | Boundary |
|---|---|---|---|
| Multiple Choice | Selects an answer within an authorized Exam attempt. | Automatically gradable behavior is supported where confirmed. | Student never accesses the Question outside the authorized Exam context. |
| True/False | Selects the applicable true/false answer within an authorized Exam attempt. | Automatically gradable behavior is supported where confirmed. | Teacher-owned Question Bank remains private. |
| Essay | Provides an Essay answer within an authorized Exam attempt. | May require authorized Teacher/Teacher Staff grading before a final result is available. | A Parent cannot answer, grade, or modify the answer. |
| Bubble Sheet | Selects bubbles electronically on screen within an authorized Exam attempt. | Automatic grading is supported where applicable. | It is electronic on-screen behavior, not a paper scan workflow. |

A Question must be one of the four supported types. Unsupported question input is rejected. The detailed Question authoring fields, scoring model, answer-key structure, and category structure are not confirmed and are not defined here.

---

# 7. Exam Creation Workflow

1. An authenticated Teacher, or a Teacher Staff member with the required assigned permission, enters the current Teacher Workspace Exam context.
2. The actor manages or selects valid active Questions from the current Teacher Workspace’s private Question Bank.
3. The actor provides the required Exam definition and availability information defined by the approved product workflow.
4. The actor composes the Exam using only Multiple Choice, True/False, Essay, and/or Bubble Sheet Questions owned by the same Teacher Workspace.
5. The backend validates role, Teacher Staff permission where applicable, Teacher Workspace scope, Question ownership, Question Type, active/archived state, and Exam information.
6. The Platform creates or updates the Exam in the Teacher Workspace.
7. The Teacher or authorized Teacher Staff may publish or make the Exam available according to the confirmed availability workflow.
8. The Platform records required Question Bank, Exam creation, update, publication/availability, Archive, grading, and modification actions in the Audit Log.

An Exam with no valid active selected Questions is an edge case that must not be treated as a valid active Exam. The detailed publication criteria, availability fields, scheduling model, and required Exam-definition fields remain unspecified rather than assumed.

---

# 8. Scheduling Rules

The confirmed requirement is that a Teacher may **publish or make Exams available** within the Teacher Workspace. The official documents do not confirm dates, time windows, recurrence, calendars, late submission, automatic opening/closing, or scheduling conflict behavior.

Therefore, Version 1 scheduling rules are limited to:

- An Exam must be associated with the Teacher Workspace that owns it.
- A Student may view or attempt only an Exam assigned or made available through the Student’s Teacher relationship.
- An archived or inactive Exam must not be treated as an active Exam attempt unless historical viewing is permitted.
- The Teacher or authorized Teacher Staff controls availability only within the confirmed Teacher Workspace permission boundary.

No date/time scheduling behavior may be introduced as a confirmed Version 1 rule until separately approved.

---

# 9. Student Exam Flow

1. The Student authenticates into the Student’s own global account.
2. The Platform presents only Exams assigned or made available through the Student’s Teacher relationships.
3. The Student identifies the relevant Teacher context when the Student has Exams from multiple Teachers.
4. The Student selects an available Exam.
5. The backend verifies that the Exam belongs to a Teacher relationship associated with the Student, that the Exam is active for the requested attempt, and that the Student is not requesting another Student’s data.
6. The Student answers supported Multiple Choice, True/False, Essay, and/or Bubble Sheet Questions.
7. The Student submits the Exam attempt.
8. The Platform records the attempt and submitted answers within the correct Student and Teacher Workspace context.
9. The Platform provides attempt status and grade information when it is available to the Student.

The Student must not create, edit, publish, Archive, restore, or grade Exams. The Student must not access a Teacher’s private Question Bank outside an assigned or available Exam context.

---

# 10. Answer Submission

Answer submission is the Student action that completes the permitted answer input for an authorized Exam attempt.

- The Student must be authenticated.
- The Exam must be assigned or made available through a Teacher relationship associated with that Student.
- Each answer must match the supported Question Type.
- Bubble Sheet answers must use valid electronic on-screen selections for the applicable Bubble Sheet structure.
- The Platform records submitted answers only within the correct Student and Teacher Workspace scope.
- The Platform records Exam attempt or submission events in the Audit Log where they qualify as important actions under the Audit Log Policy.
- The Student cannot submit answers for another Student.
- A Parent cannot take an Exam or submit answers on behalf of a Student.
- An archived or inactive Exam cannot receive an active attempt unless a separately permitted historical view applies.

The exact answer-save behavior before final submission, retry rules, attempt-editing rules, and submission deadline behavior are not confirmed.

---

# 11. Automatic Grading

Automatic grading is supported for confirmed automatically gradable question behavior, including Bubble Sheet where applicable.

| Rule | Version 1 boundary |
|---|---|
| Eligibility | Only behavior confirmed as automatically gradable may be automatically graded. |
| Bubble Sheet | Bubble Sheet automatic grading is supported where applicable after electronic on-screen selection. |
| Scope | Automatic results remain associated with the correct Student, Exam, and Teacher Workspace. |
| Availability | The Student and Parent see grade information only where it is available to their authorized context. |
| Privacy | Automatic grading must not expose Question Bank answer details, another Student’s result, or another Teacher’s Exam information. |
| History | Automatic result history is preserved through Student Group movement and Exam Archive. |

The detailed scoring formula, points, partial credit, negative marking, weighting, rounding, grade scale, and release timing are not confirmed. They must not be invented by the Engine or user interface.

---

# 12. Manual Grading

Essay Questions require authorized grading handling where applicable.

- The Teacher may perform authorized grading within the Teacher’s own Teacher Workspace.
- Teacher Staff may participate only with explicit Teacher-assigned Exam/grading permission.
- Teacher Staff actions are attributed to the Teacher Staff user in the Audit Log, not to the Teacher.
- A Student may view grade information only when it is available to the Student.
- A Parent may view available grade information for a linked Student only, in read-only mode.
- If Essay grading is incomplete, the Platform indicates that the final result is unavailable or pending; it must not invent a grade.
- Manual grading does not grant access to another Teacher’s Questions, attempts, grades, or Student records.

The rubric model, score entry rules, regrading process, moderation, feedback, and result-release criteria are not confirmed and are excluded from Version 1 definition here.

---

# 13. Bubble Sheet Rules

A **Bubble Sheet** is an electronic Exam format that simulates the familiar paper bubble-sheet pattern.

1. Bubble Sheet is a supported Question Type in the Teacher’s private Question Bank.
2. The Teacher creates Bubble Sheet content only in the Teacher’s own Teacher Workspace.
3. The Student answers by selecting bubbles on screen during an authorized Exam attempt.
4. The Platform validates that selections are valid for the applicable Bubble Sheet structure.
5. Automatic grading is supported where applicable.
6. Bubble Sheet Questions, answers, attempts, and grades remain Teacher Workspace scoped.
7. A Student cannot access a Bubble Sheet Question outside an assigned or available Exam.
8. A Parent can view only permitted read-only linked-Student Exam status/result information; the Parent cannot select answers.

Version 1 does not define paper-sheet scanning, optical mark recognition, camera capture of answer sheets, printing workflows, answer-sheet templates, or a Bubble Sheet scoring formula.

---

# 14. Essay Questions

An **Essay** is a supported Question Type that accepts a Student’s Essay answer within an authorized Exam attempt.

- Essay Questions are Teacher-owned through the private Question Bank.
- The Student may answer an Essay Question only in an assigned or available Exam from one of the Student’s Teachers.
- Essay answers require authorized grading handling where applicable.
- While grading is incomplete, the result may remain unavailable or pending; no final grade is inferred.
- Teacher or authorized Teacher Staff grading is limited to the current Teacher Workspace.
- A Parent may view available linked-Student Exam information and grades read-only but cannot change an Essay answer or grade.
- Essay attempt and grade history remains preserved if the Student moves Groups under the same Teacher.

Version 1 does not define Essay word limits, rich text behavior, attachments, automatic Essay grading, rubrics, comments, plagiarism detection, or revision/resubmission rules.

---

# 15. True / False Questions

A **True/False** Question is a supported objective Question Type in the Teacher-owned private Question Bank.

- A Student selects the applicable True/False answer only within an authorized Exam attempt.
- The selected answer must match the True/False Question Type.
- Automatically gradable behavior is supported where confirmed.
- The Question, answer, attempt, and grade remain scoped to the owning Teacher Workspace.
- The Student cannot see the Question Bank or answer details outside the assigned/available Exam context.
- The Parent cannot answer or modify the Question for a linked Student.

The detailed answer-key model, score value, partial-credit behavior, and feedback/review behavior are not confirmed.

---

# 16. Multiple Choice Questions

A **Multiple Choice** Question is a supported objective Question Type in the Teacher-owned private Question Bank.

- A Student selects an answer only within an authorized Exam attempt.
- The submitted answer must match the Multiple Choice Question Type.
- Automatically gradable behavior is supported where confirmed.
- The Question, answer, attempt, and grade remain scoped to the owning Teacher Workspace.
- The Student cannot access the Teacher’s private Question Bank outside the authorized Exam.
- The Parent cannot answer, change, or grade Multiple Choice Questions for a linked Student.

The number of options, answer-key model, score value, partial credit, negative marking, ordering, and post-submission answer visibility are not confirmed.

---

# 17. Exam Timing Rules

No duration, countdown, start time, end time, late window, pause/resume, time extension, timezone behavior, or automatic timeout rule is confirmed for Version 1.

The only timing-related confirmed boundaries are:

- An Exam is available only when it is assigned or made available through the Student’s Teacher relationship.
- An archived or inactive Exam is not an active Exam attempt.
- The Platform must not show a final result before it is available, including while Essay grading remains incomplete.
- Arabic (default) and English (fully supported) with automatic RTL/LTR are confirmed; timezone, target market, and regional formatting remain PENDING and must not be assumed in Exam timing behavior.

The Engine must not introduce a timer-based rule as if it were confirmed.

---

# 18. Attempt Rules

The following attempt rules are confirmed:

1. An attempt belongs to the correct Student, Exam, and owning Teacher Workspace.
2. A Student may attempt only Exams assigned or made available through the Student’s Teacher relationships.
3. A Student cannot access another Student’s Exam attempt or grade.
4. A Parent cannot take an Exam or submit an attempt for a Student.
5. Attempts and grades remain historically available when a Student moves Groups under the same Teacher.
6. Attempts on an archived Exam remain historical information where permitted; the archived Exam is not treated as active.

The number of attempts, retake eligibility, resume behavior, saved-draft behavior, submission replacement, answer-change rules, and missed-Exam behavior are not confirmed. No such rule is defined by this document.

---

# 19. Randomization Rules

Question randomization, answer-option randomization, randomized Question selection, random seeds, and different Exam versions are not confirmed Version 1 requirements.

Accordingly:

- The Engine must not claim or imply randomized Exams.
- An Exam is composed from Questions selected by the authorized Teacher/Teacher Staff from the owning Teacher’s Question Bank.
- Any future randomization must preserve Teacher Workspace isolation, authorized Student availability, attempt/grade history, Question Bank privacy, and Audit Log requirements.

---

# 20. Passing Rules

Passing marks, pass/fail status, grading thresholds, grade bands, and completion certificates are not confirmed Version 1 requirements.

The Engine may provide available grade/result information within the authorized Student, Parent, and Teacher contexts, but it must not calculate, label, or imply a passing outcome unless a future approved requirement defines the rule.

---

# 21. Result Calculation

Result calculation is limited to the confirmed grading behavior:

- Automatic grading is supported for confirmed automatically gradable Question behavior, including Bubble Sheet where applicable.
- Essay Questions receive authorized grading handling where applicable.
- Exam attempt status and grade information are available to the Student where available.
- A Parent may view available grades for linked Students only, in read-only mode.
- Results remain associated with the Student, Exam, and owning Teacher Workspace.
- Historical results remain available after Student Group movement and Exam Archive where permitted.

The aggregate formula, score total, weights, grade bands, partial credit, rounding, missing-answer treatment, finalization logic, and pass/fail calculation are not confirmed. The Engine must not invent them.

---

# 22. Result Publishing

The confirmed result-visibility rule is **availability**: Students may view their own Exam attempt status and grade information where available, and Parents may view linked Students’ available Exam information and grades read-only.

| Actor | Permitted result visibility |
|---|---|
| Teacher | Views attempts and grades for the Teacher’s own Teacher Workspace. |
| Authorized Teacher Staff | Views/handles Exam information only with explicit Teacher-assigned permission in the creating Teacher Workspace. |
| Student | Views the Student’s own attempt status and grade information where available, separated by Teacher. |
| Parent | Views available linked-Student Exam information, attempts, and grades read-only, separated by Teacher where applicable. |
| Super Admin | Platform-level visibility only; Teacher-private content visibility remains PENDING and is not expanded by the Exam Engine. |

The exact publish action, release date/time, result withholding, result revision, notification, and publication workflow are not confirmed. The Engine must use available/unavailable/pending states without inventing publication rules.

---

# 23. Teacher Permissions

| Actor | Confirmed permissions | Boundaries |
|---|---|---|
| Teacher | Manages the Teacher-owned Question Bank and Exams; creates, updates, publishes/makes available, views attempts/grades, handles authorized grading, Archives, and restores within the Teacher’s own Teacher Workspace. | Cannot access another Teacher’s Question Bank, Exams, attempts, grades, Students, or private records. |
| Teacher Staff | Performs Exam, Question Bank, grading, or related actions only when the Teacher assigned the relevant permission. | Exists only in the creating Teacher Workspace; detailed permission granularity remains PENDING. Actions are attributed to Teacher Staff in the Audit Log. |
| Super Admin | Has confirmed Platform-level administration only. | Teacher-private Exam/Question Bank content visibility remains PENDING; no “Login as Teacher” behavior is confirmed. |

Teacher and Teacher Staff actions must be authorized server-side for the current Teacher Workspace. Archive replaces permanent deletion for Questions and Exams.

---

# 24. Student Permissions

A Student may:

- View Exams assigned or made available through the Student’s Teacher relationships.
- Answer Multiple Choice, True/False, Essay, and Bubble Sheet Questions in an authorized Exam attempt.
- Submit the Student’s own Exam attempt.
- View the Student’s own Exam attempt status and grade information where available.
- View Exam information separated by Teacher where the Student studies with multiple Teachers.

A Student may not:

- View another Student’s attempts, answers, or grades.
- Access an Exam from a Teacher with whom the Student is not enrolled.
- Access the Teacher’s private Question Bank outside an assigned/available Exam context.
- Create, edit, publish, Archive, restore, or grade Exams.
- Modify Teacher Workspace Exam definitions or grading records.

---

# 25. Parent Visibility

A Parent may view Exam information, attempt status, and grade information for linked Students only, where that information is available.

Parent visibility requirements:

- The Parent selects only a linked Student.
- The Platform distinguishes the linked Student’s Exam information by Teacher where applicable.
- All Exam information is read-only for the Parent.
- The Parent may not take an Exam, submit answers, create, edit, publish, Archive, restore, grade, or modify grades.
- The Parent may not view Exam information, attempts, grades, or Question Bank content for an unlinked Student.
- The Parent may not access the Teacher-owned private Question Bank outside the linked Student’s visible Exam context.
- Pending Essay grading is presented as unavailable/pending rather than as a fabricated final grade.

Version 1 supports exactly one Parent account per Student, while one Parent account may monitor multiple linked Students.

---

# 26. Error Handling

The Exam Engine rejects invalid, unauthorized, out-of-scope, or unsupported actions safely.

| Condition | Required handling |
|---|---|
| Unsupported Question Type | Reject the Question. Only Multiple Choice, True/False, Essay, and Bubble Sheet are supported. |
| Cross-Teacher Question reference | Deny use of another Teacher Workspace’s Question in an Exam. |
| Unauthorized Exam/Question Bank action | Deny without exposing protected data. |
| Student requests unrelated Teacher’s Exam | Deny because the Exam is not assigned or available through the Student’s relationship. |
| Student requests another Student’s attempt/grade | Deny without exposing the record. |
| Parent selects unlinked Student | Deny without exposing that Student’s Exam data. |
| Parent modification/taking attempt | Reject because Parent access is read-only. |
| Invalid answer for Question Type | Reject the answer or submission according to approved detailed requirements. |
| Invalid Bubble Sheet selection | Reject the selection/submission when it does not match the applicable Bubble Sheet structure. |
| Archived Question or Exam used as active | Reject until authorized restoration; historical viewing remains subject to scope. |
| Grade not available | Present unavailable/pending state; do not invent a grade. |
| Incomplete grading input | Reject or defer the grading action according to approved detailed requirements. |

Error messages must provide enough safe guidance to the authorized actor without exposing Teacher-private Questions, another Teacher Workspace, unlinked Student data, another Student’s grades, or internal implementation details.

---

# 27. Edge Cases

The Exam Engine must handle these confirmed or directly required situations safely:

1. A Teacher creates the first Question Bank entry in a new Teacher Workspace.
2. A Teacher tries to create an Exam with no valid active selected Questions.
3. A Teacher or Teacher Staff attempts to use a Question from another Teacher Workspace.
4. A Student has no assigned or available Exams.
5. A Student has Exams from multiple Teachers and must see them separated by Teacher.
6. A Student attempts to access an Exam from a Teacher relationship that does not exist.
7. A Student submits invalid input for a supported Question Type.
8. A Bubble Sheet Question requires electronic selection and automatic grading where applicable.
9. An Essay answer requires grading, so a final result is not yet available.
10. A Student moves Groups after completing an Exam; historical attempts and grades remain available.
11. An Exam or Question is Archived after attempts exist; historical attempts and grades remain available while the Exam/Question is not active.
12. A Parent monitors a linked Student with no Exam records, multiple Teacher contexts, or pending Essay grading.
13. A Parent attempts to modify a grade or take/submit an Exam on behalf of a Student.
14. A Teacher Staff member loses or lacks the required assigned permission.
15. A Super Admin requests Teacher-private Exam content while the visibility boundary remains PENDING.

No unconfirmed response for timing, retry, randomization, passing, or publication is assumed in these situations.

---

# 28. Audit Logging

The Audit Log is append-only, immutable, and permanently retained. Exam Engine actions must follow the mandatory Audit Log policy.

The following Exam-related actions are recorded where required by policy:

- Question Bank creation and update.
- Question creation, update, Archive, and restore.
- Exam creation, update, publication/making available, Archive, and restore.
- Authorized grading and grade modification actions.
- Student Exam attempt and submission events where they qualify as important actions.
- Teacher Staff permission-sensitive Exam actions, attributed to the Teacher Staff user.
- Relevant denied or failed security-sensitive actions where required by Audit Log policy.

Audit entries preserve the correct Platform or Teacher Workspace context and actor attribution. They must not be editable, archived, or deleted. Audit visibility remains role and scope constrained; Student and Parent Audit Log visibility is not a confirmed Version 1 product surface.

---

# 29. Future Improvements

The following are future considerations only. They are not Version 1 commitments and must not be implemented without separate approval:

| Future area | Constraint for future decision |
|---|---|
| Question Categories | Must preserve private Teacher-owned Question Banks and Teacher Workspace isolation. |
| Scheduling and timing | Requires confirmed date, timezone, availability, duration, late, extension, and attempt rules. |
| Attempt policy | Requires confirmed retake, resume, draft, replacement, and missed-Exam behavior. |
| Randomization | Must preserve Exam/attempt traceability, grading integrity, Question Bank privacy, and history. |
| Passing and grade scale | Requires approved score, threshold, weighting, rounding, and pass/fail rules. |
| Manual grading workflow | Requires approved rubrics, feedback, regrading, and result-release rules. |
| Advanced assessment | Proctoring, paper scanning, optical mark recognition, plagiarism detection, oral/practical Exams, and richer Question Types require separate approval. |
| Notifications | Exam notifications remain out of scope until separately approved. |
| Native application | Native mobile Exam behavior is outside Version 1 and requires separate approved scope. |
| Infrastructure | Any advanced infrastructure must preserve the Laravel 12 / React 19 architecture and cannot be assumed to require Redis, WebSockets, S3 Storage, Docker, Kubernetes, or microservices. |

All future improvements must preserve Teacher Workspace isolation, global Student identity, Parent read-only linked-Student scope, Question Bank privacy, Flow A / Flow B separation, Archive instead of permanent deletion, historical retention, and permanent immutable Audit Log records.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority. |
| Requested Exam Engine coverage | Passed — all 29 requested sections are present. |
| Confirmed scope only | Passed — unsupported details for categories, scheduling, timing, attempts, randomization, passing, calculation, and publishing are explicitly identified as unconfirmed rather than invented. |
| Stack alignment | Passed — the Engine is defined for the React 19 Web Application and Laravel 12 backend, with the backend retaining authoritative enforcement. |
| Teacher Workspace isolation | Passed — Questions, Exams, attempts, answers, grades, reports, and permissions remain scoped to the owning Teacher Workspace. |
| Role boundaries | Passed — Teacher/Teacher Staff permissions, Student self scope, Parent linked-Student read-only visibility, and PENDING Super Admin content visibility are preserved. |
| Question and Exam rules | Passed — only Multiple Choice, True/False, Essay, and electronic Bubble Sheet are supported; private Question Bank ownership is preserved. |
| Grading and results | Passed — automatic grading is limited to confirmed applicable behavior; Essay grading may remain pending; results are never fabricated. |
| Archive and Audit Log | Passed — Archive replaces permanent deletion, historical attempts/grades survive Group movement and Archive, and required actions remain auditable. |
| Version 1 exclusions | Passed — no marketplace, public Question Bank, payment processing, notifications, native mobile, video homework, paper scanning, proctoring, or unconfirmed advanced Exam feature is introduced. |
| Terminology | Passed — Teacher Workspace, Question Bank, Exam, Bubble Sheet, Student, Parent, Archive, Audit Log, Subscription, and payment status are used consistently. |
