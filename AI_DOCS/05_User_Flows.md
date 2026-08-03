# 05 — User Flows

## Document Scope

This document describes the confirmed Version 1 user journeys for the Unified Education Platform. It is a business-flow document only. It does not define UI designs, APIs, database tables, source code, or implementation details.

`AI_DOCS/00_Project_Context.md` is the official Single Source of Truth. The flows below must be read with the RBAC, Permission Matrix, API Design, Backend Architecture, Frontend Architecture, Database Design, and Data Dictionary documents. Where any flow conflicts with the Project Context, the Project Context wins.

## Cross-Flow Rules

The following rules apply to every flow unless a flow states a more specific boundary:

- Version 1 is a Web Application only.
- Each Teacher operates one isolated **Teacher Workspace**. No Teacher can access another Teacher’s data.
- A Student has one global account and may study with multiple Teachers. The Student has only one Group per Teacher at a time.
- A Parent has one account, may monitor linked Students, and has read-only access everywhere for linked-Student educational data and payment status. Version 1 supports exactly one Parent account per Student.
- Teacher Staff may act only in the creating Teacher Workspace and only with explicit Teacher-assigned permissions.
- The backend makes the final authentication, authorization, ownership, relationship, and Teacher Workspace scope decisions.
- **Archive** replaces permanent deletion. Historical records remain available where permitted and are not presented as active records.
- Important actions are recorded in the immutable, permanently retained **Audit Log** according to policy.
- **Flow A** is the Teacher-to-Platform monthly **Subscription**. **Flow B** is Student/Parent-to-Teacher fees, represented in Version 1 by payment status only. They must never be conflated.
- Version 1 records payment status but does not process online payments.
- Notifications, marketplace behavior, cross-Teacher browsing, native mobile applications, and video homework are out of scope.

---

# 1. Teacher Registration

**Goal:** Establish a Teacher account, its isolated Teacher Workspace, and the Teacher’s single Teaching Subject.

**Actors:** Prospective Teacher; Platform account-creation authority where applicable; Platform.

**Preconditions:** The account-creation path is available to the prospective Teacher. The required registration information and one Teaching Subject are available. The exact self-service versus Platform-managed Teacher account-creation mechanism is not confirmed and must not be assumed.

**Trigger:** A prospective Teacher begins the approved Teacher registration or account-creation journey.

**Main Flow:**
1. The prospective Teacher provides the required account and Teacher information through the approved journey.
2. The prospective Teacher selects exactly one Teaching Subject.
3. The Platform validates the required information and creates the Teacher account and its Teacher Workspace through the approved account-creation authority.
4. The Platform associates the selected Teaching Subject with the Teacher account.
5. The Teacher can authenticate and enter only the Teacher’s own Teacher Workspace.

**Alternative Flows:**
- If the approved account-creation process is Platform-managed, the authorized Platform actor creates the Teacher account using the same single-Teaching-Subject rule.
- A Teacher who needs to teach another subject creates a separate Teacher account through an approved future registration/account-creation journey; the existing account’s Teaching Subject is not changed.

**Error Flows:**
- Missing or invalid required information is rejected.
- An attempt to select multiple Teaching Subjects for one Teacher account is rejected.
- An attempt to change the Teaching Subject after account creation is rejected.
- An unauthorized actor cannot create or alter a Teacher account.

**Postconditions:** A Teacher account exists with one Teacher Workspace and exactly one immutable Teaching Subject. The account is available only according to its valid authentication and status context.

**Business Rules:** Each Teacher account represents exactly one Teaching Subject, selected during registration and not changeable after account creation (BR-016). Teaching Subjects are independent from Educational Grades. The Teacher Workspace is a tenant boundary (BR-003). The Teacher is the Flow A Subscription customer; account creation does not introduce payment processing.

---

# 2. Teacher Login

**Goal:** Authenticate a Teacher and establish the Teacher’s own Teacher Workspace context.

**Actors:** Teacher; Platform.

**Preconditions:** The Teacher account exists, is eligible to authenticate, and the Teacher has valid credentials.

**Trigger:** The Teacher submits credentials through the Web Application.

**Main Flow:**
1. The Teacher submits credentials.
2. The Platform validates the credentials through Laravel Sanctum.
3. The Platform establishes the authenticated Teacher context.
4. The Platform resolves the Teacher’s own Teacher Workspace.
5. The Teacher enters the Teacher Workspace dashboard and can access only authorized Teacher Workspace capabilities.
6. The login event is recorded in the Audit Log.

**Alternative Flows:**
- A Teacher may begin work in an empty Teacher Workspace; the dashboard presents an appropriate empty operational state.
- The Teacher may subsequently navigate to authorized Educational Grades, Groups, Students, Attendance, Homework, Lessons, Exams, Reports, Users, or Settings workflows.

**Error Flows:**
- Invalid credentials or an ineligible account deny authentication and record the failed login event.
- A request for another Teacher Workspace is denied without exposing its data.
- An unavailable service produces a safe failure without technical details.

**Postconditions:** On success, an authenticated Teacher session/context exists for the Teacher’s own Teacher Workspace only. On failure, no protected Teacher Workspace data is revealed.

**Business Rules:** Authentication does not grant cross-Teacher access. Teacher Workspace scope and authorization are rechecked for protected actions. Flow A Subscription status and Flow B payment status remain separate if referenced on the dashboard.

---

# 3. Teacher Daily Workflow

**Goal:** Enable a Teacher to operate the daily educational activities of the Teacher Workspace.

**Actors:** Teacher; authorized Teacher Staff where explicitly permitted; Platform.

**Preconditions:** The actor is authenticated. A Teacher Workspace is resolved. Teacher Staff, if acting, have the specific Teacher-assigned permissions needed for each action.

**Trigger:** The Teacher or authorized Teacher Staff enters the Teacher Workspace for routine work.

**Main Flow:**
1. The Platform shows a Teacher Workspace-scoped operational summary.
2. The actor reviews current Groups, Students, Attendance, Homework, Lessons, Exams, Reports, Users, Settings, and permitted payment-status information.
3. The actor selects a permitted operational task, such as recording Attendance, managing Students, creating Homework, creating a Lesson, preparing an Exam, or reviewing Reports.
4. The Platform validates role, permission, Teacher Workspace, ownership, and record state for the selected action.
5. The Platform completes the authorized action, preserves history where required, and records required Audit Log events.
6. The actor continues only within the same Teacher Workspace context.

**Alternative Flows:**
- The Teacher Workspace may have no active Students, Groups, or daily work items; the actor may create the prerequisite Educational Grade and Group.
- Teacher Staff may see only the operational areas explicitly assigned by the Teacher.
- Archived records may be visible only in permitted historical or report contexts and are clearly identified as archived.

**Error Flows:**
- A Teacher Staff action without an assigned permission is denied.
- A request for another Teacher’s record is denied without disclosure.
- Invalid or archived prerequisites prevent the requested active operation.
- A failure to retrieve a summary or perform an operation is reported safely; previously valid records are not overwritten.

**Postconditions:** The Teacher Workspace reflects only valid authorized changes. Required actions are auditable; no cross-Teacher data is exposed.

**Business Rules:** Teacher Workspace isolation (BR-003), Teacher Staff explicit permissions (BR-013), Archive instead of deletion (BR-005), historical preservation (BR-014), and Audit Log requirements (BR-006) apply. Flow A Subscription and Flow B payment status remain distinct; no payment transaction is processed.

---

# 4. Create Educational Grade

**Goal:** Create an Educational Grade for organizing Groups in a Teacher Workspace.

**Actors:** Teacher; authorized Teacher Staff; Platform.

**Preconditions:** The actor is authenticated in the current Teacher Workspace and has the required Educational Grade permission.

**Trigger:** The actor chooses to create an Educational Grade.

**Main Flow:**
1. The actor supplies the Educational Grade name.
2. The Platform validates the name and current Teacher Workspace scope.
3. The Platform creates the Educational Grade within that Teacher Workspace.
4. The Platform makes the active Educational Grade available for eligible Group organization.
5. The Platform records the creation in the Audit Log.

**Alternative Flows:**
- An authorized actor may later update, Archive, or restore the Educational Grade through its separate permitted lifecycle action.
- A newly created Educational Grade may initially have no Groups.

**Error Flows:**
- Missing or invalid name is rejected.
- An actor without permission is denied.
- A cross-Teacher Workspace creation request is denied.
- An archived Educational Grade cannot be used in active Group assignment until authorized restoration.

**Postconditions:** An active Educational Grade belongs only to the current Teacher Workspace and can organize Groups. No historical data is removed.

**Business Rules:** Educational Grades are Teacher-created and Teacher Workspace scoped. They are independent from the Teacher’s Teaching Subject. Archive, not permanent deletion, applies; create, update, Archive, and restore actions are auditable.

---

# 5. Create Group

**Goal:** Create a Group under an Educational Grade with its schedule and Flow B pricing basis.

**Actors:** Teacher; authorized Teacher Staff; Platform.

**Preconditions:** The actor is authenticated in the Teacher Workspace, has Group permission, and an active Educational Grade exists in the same Teacher Workspace.

**Trigger:** The actor chooses to create a Group.

**Main Flow:**
1. The actor selects an active Educational Grade in the current Teacher Workspace.
2. The actor provides Group Name, Schedule, Price, and Pricing Type.
3. The actor selects either Monthly or Per Lesson as the Pricing Type.
4. The Platform validates all required information, the Educational Grade relationship, and Teacher Workspace scope.
5. The Platform creates the Group under the selected Educational Grade.
6. The Platform records the creation in the Audit Log.

**Alternative Flows:**
- A valid Group may initially contain no Students.
- An authorized actor may later update, Archive, restore, or manage Student movement without deleting historical records.

**Error Flows:**
- Missing Group information, invalid Price, or a Pricing Type other than Monthly or Per Lesson is rejected.
- Selecting an archived or another Teacher’s Educational Grade is denied.
- An actor without permission is denied.

**Postconditions:** An active Group exists in the current Teacher Workspace under one Educational Grade, with a valid Schedule, Price, and Pricing Type.

**Business Rules:** A Group belongs to one Educational Grade and is not a Teaching Subject. Pricing Type is Monthly or Per Lesson (BR-009) and establishes the basis for Flow B fees, not Flow A Subscription. Archive replaces deletion; Group history and Student history are preserved.

---

# 6. Add Student

**Goal:** Add a new or existing global Student to the current Teacher Workspace and assign the Student to one Group.

**Actors:** Teacher; authorized Teacher Staff; Platform.

**Preconditions:** The actor has Student-management permission in the Teacher Workspace. The target Group is active and belongs to that Teacher Workspace. Required Student identity information is available for duplicate prevention or existing-account matching.

**Trigger:** The actor begins Student registration or assignment.

**Main Flow:**
1. The actor provides Student identity information and selects an active Group.
2. The Platform checks whether a matching global Student account already exists.
3. If no duplicate exists, the Platform creates one Student account; if an existing Student is valid for assignment, the Platform uses that same account without exposing another Teacher’s private data.
4. The Platform establishes the Student’s relationship to the current Teacher Workspace.
5. The Platform assigns the Student to the selected Group.
6. The Platform preserves the assignment/enrollment history and records the action in the Audit Log.
7. A Teacher-created Student account may later be activated and used by that Student.

**Alternative Flows:**
- An existing Student who studies with another Teacher may be assigned to this Teacher Workspace while each Teacher’s records remain separate.
- If the Student already belongs to a Group in this Teacher Workspace, an authorized Group-movement workflow may replace the active Group while preserving historical Attendance, Homework, Exams, and grades.

**Error Flows:**
- Incomplete identity information is rejected.
- A duplicate Student creation attempt is prevented; the existing account is used only where the authorized assignment is valid.
- Assignment to an archived, invalid, or cross-Teacher Group is rejected.
- A second simultaneous Group assignment for the same Teacher is rejected.
- An actor without permission or outside the Teacher Workspace is denied.

**Postconditions:** The Student has one global account and, if active in this Teacher Workspace, one Group for this Teacher. Relevant Teacher Workspace history and required Audit Log entries are preserved.

**Business Rules:** Student self-registration and Teacher-created registration are the only confirmed registration methods (BR-022). Duplicate Student accounts are not allowed. A Student may study with multiple Teachers but belongs to one Group per Teacher at a time (BR-001, BR-002). Teacher Workspace data remains isolated (BR-003); Student movement preserves history (BR-007).

---

# 7. Student Registration

**Goal:** Create one global Student account through the Student self-registration method without creating a duplicate account.

**Actors:** Prospective Student; Platform.

**Preconditions:** The Student can provide the required registration information. The prospective Student does not already have a global Student account, or an existing Teacher-created account is available for activation rather than duplication.

**Trigger:** The prospective Student starts self-registration.

**Main Flow:**
1. The prospective Student submits the required identity and account information.
2. The Platform validates the registration information and checks for an existing global Student account.
3. If no existing Student account is found, the Platform creates one global Student account.
4. The Student authenticates using the created account and can access only the Student’s own context.
5. The Platform records applicable registration and authentication events in the Audit Log.

**Alternative Flows:**
- If the Student has a Teacher-created account, the Student activates and uses that same account rather than registering a second account.
- After authorized Teacher Workspace assignment, the Student can see the Student’s own per-Teacher information.

**Error Flows:**
- Missing or invalid required registration information is rejected.
- A duplicate account attempt is rejected without creating another Student account.
- An attempt to use registration to access another Student’s information is denied.

**Postconditions:** Exactly one global Student account exists for the Student. No Teacher Workspace membership or Group assignment is implied unless separately created through an authorized Teacher workflow.

**Business Rules:** Student registration supports self-registration and Teacher-created accounts only (BR-022). Both methods must result in one global Student account, not duplicates. The Student’s Attendance, Homework, Exams, Lessons, and Flow B status are partitioned per Teacher.

---

# 8. Student Login

**Goal:** Authenticate a Student and establish access to the Student’s own, per-Teacher-partitioned information.

**Actors:** Student; Platform.

**Preconditions:** A Student account exists and is eligible to authenticate. The Student has valid credentials, including after activation of a Teacher-created account where applicable.

**Trigger:** The Student submits credentials through the Web Application.

**Main Flow:**
1. The Student submits credentials.
2. The Platform validates the credentials through Laravel Sanctum.
3. The Platform establishes the authenticated Student context.
4. The Platform loads only the Student’s own dashboard, schedule, Homework, Lessons, Exams, Flow B payment status, and settings context, partitioned by Teacher relationship where applicable.
5. The Platform records the login event in the Audit Log.

**Alternative Flows:**
- A Student studying with multiple Teachers can select or filter the Student’s own records by Teacher relationship where supported.
- A Student with no active Teacher relationship receives an appropriate empty state without access to another Student’s or Teacher’s records.

**Error Flows:**
- Invalid credentials or an ineligible account deny authentication and record the failed login event.
- A request for another Student’s data, another Student’s grades, or a Teacher’s private Question Bank is denied.
- A request for a Teacher relationship not associated with the Student is denied.

**Postconditions:** On success, the Student is authenticated only for the Student’s own account and own per-Teacher records.

**Business Rules:** Student self scope is mandatory. Lessons, Attendance, Homework, Exams, grades, and Flow B status are separate per Teacher. A Student may not modify Teacher Workspace data, access another Student’s records, or access Teacher-private Question Banks outside assigned or available Exams.

---

# 9. Join Group

**Goal:** Establish the Student’s authorized Group membership within a Teacher Workspace.

**Actors:** Teacher; authorized Teacher Staff; Student as the enrolled person; Platform.

**Preconditions:** The Student has a global account or is being created through the authorized Add Student flow. An active Group exists in the Teacher Workspace. The acting Teacher or Teacher Staff has Student/Group permission.

**Trigger:** The Teacher or authorized Teacher Staff assigns the Student to a Group.

**Main Flow:**
1. The authorized actor identifies the Student in the current Teacher Workspace context.
2. The actor selects an active Group belonging to the same Teacher Workspace.
3. The Platform verifies the Student relationship, Group status, permission, and one-Group-per-Teacher rule.
4. The Platform creates or updates the Student’s active Enrollment in that Group.
5. The Platform preserves Enrollment history and records the assignment in the Audit Log.
6. The Student can view the resulting Teacher-partitioned schedule and assigned content after authentication.

**Alternative Flows:**
- A Student who already studies with another Teacher may join a Group in this Teacher Workspace without either Teacher seeing the other’s private records.
- An authorized actor can move the Student from the current Group to another Group under the same Teacher; prior Attendance, Homework, Exams, grades, and Enrollment history are retained.

**Error Flows:**
- A Student cannot self-assign to a Group because no Student self-service Group-joining permission is confirmed.
- Assignment to an archived or cross-Teacher Group is rejected.
- A second active Group assignment under the same Teacher is rejected.
- An unauthorized Teacher Staff member is denied.

**Postconditions:** The Student belongs to one active Group for the relevant Teacher, and history is preserved.

**Business Rules:** The term is **Enrollment** for the Teacher-managed relationship. A Student belongs to only one Group per Teacher at a time (BR-002). Student Group movement preserves historical records (BR-007). Group pricing is Flow B context only and does not grant payment-processing behavior.

---

# 10. QR Attendance

**Goal:** Record a Student’s Attendance using the daily Dynamic QR Code through the Web Application.

**Actors:** Teacher or authorized Teacher Staff; Student; Platform.

**Preconditions:** The Teacher Workspace Attendance context is valid. A daily Dynamic QR Code has been generated for that Attendance context. The Student is authenticated and associated with the relevant Teacher relationship.

**Trigger:** The Student scans the displayed daily Dynamic QR Code.

**Main Flow:**
1. The Teacher or authorized Teacher Staff makes the daily Dynamic QR Code available for the relevant Attendance context.
2. The Student opens the Web Application scanner and scans the code.
3. The Platform authenticates the Student context.
4. The Platform verifies the dynamic code, the Teacher Workspace Attendance context, and the Student’s valid relationship with that Teacher.
5. The Platform records Attendance for the Student in the correct Teacher Workspace.
6. The Platform records the Attendance event in the Audit Log.

**Alternative Flows:**
- For printed ID Card Attendance, a scanner device reads the Student’s printed QR code. The Platform resolves the Student identity and the relevant Teacher Workspace Attendance context, validates the Student relationship, then records Attendance and the required Audit Log event.
- If camera scanning is unavailable, the Teacher or authorized Teacher Staff may use the separate manual Attendance method; the Platform does not create an unconfirmed Student self-entry alternative.

**Error Flows:**
- An invalid, expired, or incorrect daily Dynamic QR Code is rejected.
- A Student not associated with the Teacher Workspace is denied.
- A repeated scan for the same Attendance context is prevented from producing inconsistent duplicate Attendance.
- Authentication or scanner capability failure results in a safe failure without recording Attendance.

**Postconditions:** A valid Attendance record exists only for the Student and the relevant Teacher Workspace, with an Audit Log event where required.

**Business Rules:** Dynamic QR Codes are generated daily (BR-010). Attendance is Teacher Workspace scoped and must not be used to calculate Billable Students; the Flow A calculation uses Enrollment duration only (BR-008). Attendance history survives Group movement (BR-007).

---

# 11. Manual Attendance

**Goal:** Record or correct Attendance manually within a Teacher Workspace.

**Actors:** Teacher; authorized Teacher Staff; Platform.

**Preconditions:** The actor is authenticated in the relevant Teacher Workspace and has Attendance record or modification permission. The Student has a valid relationship with the Teacher Workspace.

**Trigger:** The Teacher or authorized Teacher Staff chooses manual Attendance entry or correction.

**Main Flow:**
1. The actor selects the relevant Group, Student, and Attendance date/session context.
2. The actor records or corrects the Attendance status.
3. The Platform verifies permission, Student relationship, Teacher Workspace scope, and valid Attendance context.
4. The Platform records or updates the Attendance record.
5. The Platform preserves historical context and records the action in the Audit Log.

**Alternative Flows:**
- Manual Attendance can be used when QR or ID Card scanning is unavailable.
- The Student may have moved Groups; the actor records the appropriate Attendance context while prior Group history remains preserved.

**Error Flows:**
- An unauthorized Teacher Staff member is denied.
- Attendance for a Student outside the Teacher Workspace is denied.
- Invalid date/session information or an inconsistent duplicate Attendance attempt is rejected.
- A request to permanently delete Attendance history is rejected; Archive rules apply where removal from active use is required.

**Postconditions:** The valid Teacher Workspace Attendance record is created or corrected and remains historically traceable.

**Business Rules:** Manual entry is one of the three confirmed Attendance methods (BR-010). Attendance changes are auditable (BR-006), Teacher Workspace scoped (BR-003), and are not a Billable Student input (BR-008).

---

# 12. Create Homework

**Goal:** Create Homework in a Teacher Workspace for the appropriate Student context.

**Actors:** Teacher; authorized Teacher Staff; Platform.

**Preconditions:** The actor is authenticated in the Teacher Workspace and has Homework creation permission. The target Student or Group context belongs to the Teacher Workspace.

**Trigger:** The actor chooses to create Homework.

**Main Flow:**
1. The actor provides the Homework title, description, target context, supported format, and optional permitted attachment where applicable.
2. The Platform validates the actor’s permission, Teacher Workspace scope, target relationship, and Homework format.
3. The Platform creates the Homework in the current Teacher Workspace.
4. The Platform makes it available only to its authorized assigned Students through the correct Teacher relationship.
5. The Platform records the creation in the Audit Log where required.

**Alternative Flows:**
- Homework may be text-based or may use supported Image or PDF content/submission context.
- An authorized Teacher or Teacher Staff member may later review or manage the Homework within the same Teacher Workspace.

**Error Flows:**
- Video Homework is rejected.
- A target Student or Group outside the Teacher Workspace is denied.
- Invalid required information or unsupported file format is rejected.
- An actor without the required permission is denied.

**Postconditions:** Homework exists only in the current Teacher Workspace and is visible to the intended authorized Students. No other Teacher’s Students receive it.

**Business Rules:** Homework supports Text, Image, and PDF only (BR-021). Video homework is out of scope. Teacher Workspace isolation, Archive instead of deletion, and Audit Log requirements apply. Parent access, if later viewing the Homework, remains read-only.

---

# 13. Submit Homework

**Goal:** Allow a Student to submit a response for Homework assigned to that Student.

**Actors:** Student; Platform.

**Preconditions:** The Student is authenticated. The Homework is assigned to the Student through a valid Teacher relationship and accepts Student submission. The response is Text, Image, or PDF as supported.

**Trigger:** The Student submits a Homework response.

**Main Flow:**
1. The Student selects Homework from the Student’s own Teacher-partitioned Homework list.
2. The Student provides a text response and/or a permitted Image or PDF submission as applicable.
3. The Platform verifies the Student’s identity, Homework assignment, Teacher relationship, and supported format.
4. The Platform records the Homework submission under the relevant Student and Teacher Workspace context.
5. The Platform makes the submission available only to authorized Teacher Workspace users for review.
6. The Platform records the submission event in the Audit Log where required.

**Alternative Flows:**
- A Student may have Homework from multiple Teachers; each submission remains associated with the correct Teacher relationship.
- If a Homework item does not require a submission, the Student may view it without creating a submission.

**Error Flows:**
- A Student cannot submit for Homework not assigned to that Student.
- Video or another unsupported submission type is rejected.
- An inactive, archived, unavailable, or invalid Homework context prevents active submission according to its state.
- A Parent cannot submit Homework for a Student.

**Postconditions:** A valid submission is associated with the Student, Homework, and correct Teacher Workspace, without exposing it across Teachers.

**Business Rules:** Student submission is self-scoped and supports Text, Image, and PDF only (BR-021). Homework and submissions remain Teacher Workspace and Student relationship scoped. Parent access is read-only; no Parent submission permission exists.

---

# 14. Create Lesson

**Goal:** Create a private Teacher-owned Lesson and make it available only to authorized Students in the Teacher Workspace.

**Actors:** Teacher; authorized Teacher Staff; Platform.

**Preconditions:** The actor is authenticated in the Teacher Workspace and has Lesson creation permission. Required Lesson information and any authorized private video/file reference are available.

**Trigger:** The actor chooses to create a Lesson.

**Main Flow:**
1. The actor provides the Lesson title, description, and availability context.
2. The Platform validates the required information, permission, and Teacher Workspace scope.
3. The Platform creates the Lesson and associates any authorized private file reference.
4. The Platform makes the Lesson available only to Students authorized through the Teacher relationship and Lesson availability context.
5. The Platform records the relevant action in the Audit Log.

**Alternative Flows:**
- A Lesson may initially have no eligible Student audience until the approved availability context is established.
- Authorized Lesson updates, Archive, and restore preserve history and private ownership.

**Error Flows:**
- An actor without Lesson permission is denied.
- A Lesson creation attempt in another Teacher Workspace is denied.
- Invalid required Lesson information is rejected.
- Any attempt to publish the Lesson for marketplace discovery or cross-Teacher browsing is rejected.

**Postconditions:** A private Teacher-owned Lesson exists within the Teacher Workspace and is visible only through authorized relationships.

**Business Rules:** The Platform is not a marketplace. Lessons are private per Teacher and may be uploaded exclusively for that Teacher’s Students. Lesson file access requires backend authorization. Archive replaces permanent deletion; the detailed Lesson video hosting/protection decision remains PENDING and is not resolved by this flow.

---

# 15. Watch Lesson

**Goal:** Allow a Student to view a Lesson from one of the Student’s own Teachers.

**Actors:** Student; Platform.

**Preconditions:** The Student is authenticated. The Lesson belongs to a Teacher relationship associated with the Student and is available to that Student.

**Trigger:** The Student selects an available Lesson.

**Main Flow:**
1. The Student chooses a Teacher relationship where applicable.
2. The Platform presents only Lessons authorized for that Student in that Teacher context.
3. The Student selects a Lesson.
4. The Platform validates Student identity, Teacher relationship, Lesson availability, ownership, and file access authorization.
5. The Platform provides authorized access to the private Lesson content.

**Alternative Flows:**
- A Student studying with multiple Teachers can view each Teacher’s authorized Lessons separately.
- The Student may have no available Lessons for a Teacher and receives an appropriate empty state.

**Error Flows:**
- A request for a Lesson from an unrelated Teacher is denied.
- A request for archived or unavailable Lesson content is denied or shown only as permitted historical information.
- A direct file path or identifier does not bypass backend authorization.

**Postconditions:** The Student has viewed only Lesson content authorized through the Student’s own Teacher relationship. No Teacher-private content is exposed across Teacher Workspaces.

**Business Rules:** Lessons are Teacher-owned and private. There is no public catalog, marketplace discovery, or cross-Teacher content sharing. The Student may not create, edit, Archive, or manage Lessons.

---

# 16. Create Exam

**Goal:** Create a Teacher Workspace Exam using only the owning Teacher’s private Question Bank.

**Actors:** Teacher; authorized Teacher Staff; Platform.

**Preconditions:** The actor is authenticated in the Teacher Workspace and has Exam/Question Bank permission. The selected Questions belong to the current Teacher Workspace’s private Question Bank.

**Trigger:** The actor begins Exam creation.

**Main Flow:**
1. The actor provides the Exam title and availability context.
2. The actor selects Questions from the current Teacher Workspace’s Question Bank.
3. The actor composes the Exam using supported question types: Multiple Choice, True/False, Essay, and Bubble Sheet.
4. The Platform validates permission, Teacher Workspace scope, Question ownership, supported question types, and availability context.
5. The Platform creates the Exam and makes it available only to eligible Students in the Teacher Workspace.
6. The Platform records the relevant creation action in the Audit Log.

**Alternative Flows:**
- A Bubble Sheet Exam uses electronic on-screen selection and is automatically graded where applicable.
- Essay Questions may require Teacher review before a final grade becomes available.
- An Exam may later be updated, Archived, restored, or reviewed by an authorized actor without exposing Questions to another Teacher.

**Error Flows:**
- A Question from another Teacher Workspace is rejected.
- Unsupported question types are rejected.
- An actor without Exam permission is denied.
- A request to create an Exam without valid required information or valid availability context is rejected.

**Postconditions:** A Teacher-owned, Teacher Workspace-scoped Exam exists and can be made available only to authorized Students. The private Question Bank remains protected.

**Business Rules:** Question Banks are Teacher-owned and private (BR-011). Exams use only the owning Teacher’s Question Bank. Exam definitions, attempts, and grades are Teacher Workspace scoped (BR-012). Archive replaces deletion and important Exam actions are auditable.

---

# 17. Take Exam

**Goal:** Allow a Student to complete an assigned or available Exam for the correct Teacher relationship.

**Actors:** Student; Platform.

**Preconditions:** The Student is authenticated. The Exam is assigned or available through the Student’s Teacher relationship, is active for attempting, and is not merely an archived historical record.

**Trigger:** The Student opens an available Exam and starts an attempt.

**Main Flow:**
1. The Student selects an Exam from the Student’s authorized, Teacher-separated Exam list.
2. The Platform verifies that the Exam belongs to a Teacher relationship associated with the Student.
3. The Student answers supported Multiple Choice, True/False, Essay, and/or Bubble Sheet questions.
4. For Bubble Sheet questions, the Student uses electronic on-screen selection.
5. The Student submits the Exam attempt.
6. The Platform validates the attempt context and records answers in the correct Student and Teacher Workspace scope.
7. The Platform performs confirmed automatic grading where applicable and records an Audit Log event when required.

**Alternative Flows:**
- Essay answers remain pending Teacher grading before final results are available.
- A Student can take authorized Exams from multiple Teachers, but each attempt is separate by Teacher.

**Error Flows:**
- The Student is denied access to an Exam not assigned or available through the Student’s Teacher relationship.
- Invalid answer input for a question type is rejected.
- An archived or inactive Exam cannot be taken as an active attempt.
- The Student cannot access the Teacher’s Question Bank outside the assigned/available Exam context or another Student’s attempt.

**Postconditions:** The Student’s Exam attempt is recorded only in the correct Teacher relationship. Grade information is available only when the applicable grading state permits it.

**Business Rules:** Supported question types are Multiple Choice, True/False, Essay, and Bubble Sheet (BR-011). Bubble Sheet is electronic and automatically graded where applicable. Exam attempts and grades are Teacher Workspace scoped; Group movement preserves historical attempts and grades (BR-007).

---

# 18. View Exam Results

**Goal:** Allow a Student to view the Student’s own available Exam result, and allow a Parent to view the linked Student’s available result read-only.

**Actors:** Student; Parent for a linked Student; Platform.

**Preconditions:** The actor is authenticated. The Student result belongs to the Student’s own Teacher relationship, or the Parent has selected a linked Student. A grade/result is available; an Essay result may still be pending review.

**Trigger:** The Student or Parent opens the authorized Exam result view.

**Main Flow:**
1. The Student selects the Student’s own Exam result, or the Parent selects a linked Student and then the Student’s result.
2. The Platform verifies the actor’s Student self scope or Parent linked-Student read-only relationship.
3. The Platform verifies the Exam’s Teacher relationship and result visibility.
4. The Platform displays the available attempt status, grade, and permitted result information separated by Teacher.
5. Historical results remain available where permitted after Group movement or Exam Archive and are clearly historical when applicable.

**Alternative Flows:**
- An automatically gradable Exam, including applicable Bubble Sheet questions, can show available result information after grading.
- An Essay response can show a pending/unavailable result state until Teacher grading is complete.

**Error Flows:**
- A Student cannot view another Student’s attempt or grade.
- A Parent cannot view a result for an unlinked Student.
- A request for an unrelated Teacher’s Exam is denied.
- When a result is not available, the Platform indicates its unavailability rather than inventing a grade.

**Postconditions:** Only authorized Student-self or Parent-linked, read-only result information is displayed. No result is modified by this flow.

**Business Rules:** Parent access is read-only everywhere. Exams, attempts, and grades are Teacher Workspace scoped (BR-012); Teacher-private Question Bank content is not exposed. Historical Exam and grade records survive Student Group movement (BR-007).

---

# 19. Parent Login

**Goal:** Authenticate a Parent and establish the Parent’s linked-Student, read-only monitoring context.

**Actors:** Parent; Platform.

**Preconditions:** A Parent account exists, is eligible to authenticate, has valid credentials, and has one or more valid Student links where monitoring data is expected.

**Trigger:** The Parent submits credentials through the Web Application.

**Main Flow:**
1. The Parent submits credentials.
2. The Platform validates the credentials through Laravel Sanctum.
3. The Platform establishes the authenticated Parent context.
4. The Platform loads only the Parent’s linked Students and Parent account context.
5. The Parent enters a read-only dashboard and can select a linked Student for monitoring.
6. The Platform records the login event in the Audit Log.

**Alternative Flows:**
- A Parent linked to multiple Students can use the Student Switcher.
- A Parent with no currently available linked-Student records receives an appropriate safe empty state.

**Error Flows:**
- Invalid credentials or an ineligible account deny authentication and record the failed login event.
- A request to access an unlinked Student is denied without disclosing that Student’s information.
- A request to modify Attendance, Homework, Exams, grades, payment status, Student records, Teacher records, or Teacher Workspace data is denied.

**Postconditions:** On success, an authenticated Parent context exists with read-only access to linked Students only.

**Business Rules:** A Parent has one account and may monitor multiple Students (BR-020). Version 1 permits exactly one Parent account per Student. Parent access is read-only everywhere (BR-004); parent authentication does not grant a Student or Teacher role.

---

# 20. Parent Switch Between Students

**Goal:** Change the Parent’s active monitoring context from one linked Student to another.

**Actors:** Parent; Platform.

**Preconditions:** The Parent is authenticated and has at least two linked Students.

**Trigger:** The Parent selects a different Student through the Student Switcher.

**Main Flow:**
1. The Platform presents only Students linked to the authenticated Parent.
2. The Parent selects one linked Student.
3. The Platform validates the Parent-Student relationship.
4. The Platform changes the active monitoring context to the selected linked Student.
5. The Platform presents only that Student’s authorized, read-only information, separated by the Student’s Teacher relationships where applicable.

**Alternative Flows:**
- The Parent may switch back to another linked Student at any time.
- If the Parent has only one linked Student, that Student can be the default context and no switch is needed.

**Error Flows:**
- A selection of an unlinked Student is denied.
- A stale or unavailable Student link produces a safe unavailable state without exposing information.
- The Parent cannot use the switcher to alter Student educational records or payment status.

**Postconditions:** The active Parent monitoring context references only a validated linked Student. No Student data is changed.

**Business Rules:** Parent monitoring is linked-Student scoped and read-only. The Parent must not see another Parent’s linked Students or data. The Student Switcher is a context change, not an authorization bypass.

---

# 21. Parent View Attendance

**Goal:** Allow a Parent to view Attendance for a selected linked Student.

**Actors:** Parent; Platform.

**Preconditions:** The Parent is authenticated and has selected a linked Student. Attendance records exist or an empty historical state is valid.

**Trigger:** The Parent opens Attendance for the selected Student.

**Main Flow:**
1. The Parent selects a linked Student through the Student Switcher where necessary.
2. The Parent requests the Student’s Attendance view.
3. The Platform validates the Parent-Student link and resolves only the selected Student’s Teacher-partitioned Attendance records.
4. The Platform presents permitted Attendance history, clearly separated by Teacher and identifying archived/historical context where applicable.
5. The Parent reads the information without modifying it.

**Alternative Flows:**
- The Parent may view Attendance from multiple Teachers for the linked Student while each Teacher’s data remains partitioned.
- The linked Student may have no Attendance records for a Teacher or period; the Platform presents an appropriate empty state.

**Error Flows:**
- An unlinked Student request is denied.
- The Parent cannot record, correct, Archive, restore, or otherwise modify Attendance.
- A request for another Teacher Workspace’s non-linked Student data is denied.

**Postconditions:** The Parent has read only authorized Attendance information; Attendance records are unchanged.

**Business Rules:** Parent access is read-only (BR-004). Attendance remains Teacher Workspace scoped and historical Attendance remains available through Student Group movement (BR-007). Attendance is never used to calculate Billable Students (BR-008).

---

# 22. Parent View Homework

**Goal:** Allow a Parent to view Homework for a selected linked Student.

**Actors:** Parent; Platform.

**Preconditions:** The Parent is authenticated and has selected a linked Student. The Student has authorized Homework information or a valid empty state.

**Trigger:** The Parent opens Homework for the selected Student.

**Main Flow:**
1. The Parent selects a linked Student where necessary.
2. The Parent requests the Student’s Homework view.
3. The Platform validates the Parent-Student link and resolves only Homework associated with that Student’s Teacher relationships.
4. The Platform presents permitted Homework and submission/status information in read-only form, separated by Teacher where applicable.
5. The Parent reads the information without creating, editing, submitting, reviewing, or grading Homework.

**Alternative Flows:**
- The Parent may filter or view Homework by the linked Student’s Teacher relationship where supported.
- Homework may have no submission or may be archived historical information; the Platform presents only permitted status.

**Error Flows:**
- A Parent cannot view Homework for an unlinked Student.
- A Parent cannot submit Homework for the Student.
- A Parent cannot upload files, modify Homework, change a submission, or review/grade it.
- Unauthorized Lesson or file access outside the linked Student relationship is denied.

**Postconditions:** The Parent has viewed only read-only Homework information for a linked Student. No Homework or submission data changes.

**Business Rules:** Parent Homework access is linked-Student scoped and read-only. Homework remains Teacher Workspace scoped. Text, Image, and PDF are the only supported Homework formats; this does not grant Parent upload capability.

---

# 23. Parent View Exams

**Goal:** Allow a Parent to view Exam and available grade information for a selected linked Student.

**Actors:** Parent; Platform.

**Preconditions:** The Parent is authenticated and has selected a linked Student. The Student has authorized Exam attempts/results or a valid empty/pending state.

**Trigger:** The Parent opens Exams for the selected Student.

**Main Flow:**
1. The Parent selects a linked Student where necessary.
2. The Parent requests the Student’s Exams view.
3. The Platform validates the Parent-Student link and resolves only the linked Student’s Exams, attempts, and available grades by Teacher relationship.
4. The Platform presents permitted read-only Exam status and result information.
5. The Parent reads the information without accessing private Questions or changing an Exam, attempt, answer, or grade.

**Alternative Flows:**
- Automatically graded results can be shown when available.
- Essay results can remain pending until Teacher grading is complete.
- Historical attempts and grades remain visible where permitted after Group movement or Exam Archive.

**Error Flows:**
- A Parent cannot view an unlinked Student’s Exams or grades.
- A Parent cannot take an Exam, answer on behalf of a Student, create/edit/archive an Exam, or grade an attempt.
- The Parent cannot access the Teacher’s private Question Bank.
- Unavailable results are shown as unavailable rather than as invented grades.

**Postconditions:** The Parent has viewed only permitted read-only Exam status and result information for the linked Student.

**Business Rules:** Parent read-only access (BR-004) and Teacher Workspace Exam scope (BR-012) apply. Question Banks are Teacher-owned and private (BR-011). Student Group movement preserves historical Exam and grade records (BR-007).

---

# 24. Parent View Payments

**Goal:** Allow a Parent to view a selected linked Student’s Flow B payment status for each Teacher.

**Actors:** Parent; Platform.

**Preconditions:** The Parent is authenticated, has selected a linked Student, and the Student has a Teacher relationship with relevant Flow B payment-status information or a valid empty state.

**Trigger:** The Parent opens Payments for the selected Student.

**Main Flow:**
1. The Parent selects a linked Student where necessary.
2. The Parent requests payment-status information.
3. The Platform validates the Parent-Student link and resolves only the Student’s per-Teacher Flow B payment status.
4. The Platform presents the recorded status and applicable Group Price/Pricing Type basis where permitted.
5. The Parent reads the information; no in-platform transaction is initiated.

**Alternative Flows:**
- The linked Student may study with multiple Teachers using Monthly or Per Lesson Group pricing; each status is shown in its correct Teacher context.
- If no status has been recorded, the Platform shows an appropriate empty or unavailable state.

**Error Flows:**
- A Parent cannot access payment status for an unlinked Student.
- A Parent cannot update payment status or initiate a payment transaction.
- A request for Flow A Teacher Platform Subscription data is denied because it is not the Parent’s Flow B view.
- A payment action is rejected because Version 1 records status only.

**Postconditions:** The Parent has viewed only linked-Student Flow B payment status. No payment status or transaction changes occur.

**Business Rules:** Flow B is Student/Parent-to-Teacher fees derived from Group Price and Pricing Type (BR-009). Flow A Subscription is separate. Version 1 records status only and provides no online payment gateway (BR-019). Parent access is read-only.

---

# 25. Super Admin Login

**Goal:** Authenticate a Super Admin and establish Platform-level administration context.

**Actors:** Super Admin; Platform.

**Preconditions:** A Super Admin account exists, is eligible to authenticate, and has valid credentials.

**Trigger:** The Super Admin submits credentials through the Web Application.

**Main Flow:**
1. The Super Admin submits credentials.
2. The Platform validates the credentials through Laravel Sanctum.
3. The Platform establishes the authenticated Super Admin Platform-level context.
4. The Platform provides authorized access to Teachers, Flow A Subscriptions, pricing, Platform Settings, global reports, and permitted Platform Audit Log views.
5. The Platform records the login event in the Audit Log.

**Alternative Flows:**
- The Platform may have no registered Teachers or Billing Cycle information yet; the Super Admin receives an appropriate empty administrative state.
- The Super Admin may later manage confirmed Platform-level pricing and Subscription status within the separate authorized flows.

**Error Flows:**
- Invalid credentials or an ineligible account deny authentication and record the failed login event.
- A request to enter a Teacher Workspace as a Teacher, including “Login as Teacher,” is denied because it is not confirmed for Version 1.
- Teacher-private content access beyond confirmed visibility is denied; the content-visibility boundary remains PENDING.

**Postconditions:** On success, the Super Admin has authenticated Platform-level access only. No implicit Teacher Workspace role or Teacher-private content access is granted.

**Business Rules:** The Super Admin manages Platform-level Teachers, Flow A Subscriptions, pricing, Platform Settings, and global reports. Super Admin Teacher-private content visibility remains PENDING and must not be expanded. The Super Admin is not a substitute for a Teacher or Parent context.

---

# 26. Teacher Subscription

**Goal:** View and manage the recorded Flow A Subscription status for a Teacher under the Platform’s monthly business model.

**Actors:** Teacher; Super Admin; Platform.

**Preconditions:** The Teacher account exists. The actor is authenticated in the correct role context: the Teacher may view the Teacher’s own Flow A Subscription information; the Super Admin may manage Platform-level Subscription status and pricing within confirmed authority. A relevant Billing Cycle exists or is being prepared according to calendar-month rules.

**Trigger:** The Teacher views Subscription information, or the Super Admin views/manages a Teacher Subscription record.

**Main Flow:**
1. The Platform identifies the relevant Teacher and calendar-month Billing Cycle.
2. The Platform determines Billable Students from that Teacher’s Enrollment duration during the Billing Cycle.
3. The Platform calculates the Subscription basis as Billable Students multiplied by the applicable Price Per Student.
4. The Platform presents the recorded Flow A Subscription status to the authorized Teacher or Super Admin.
5. The Super Admin, where authorized, records or manages Subscription status and Platform pricing according to confirmed rules.
6. The Platform records important Subscription and pricing actions in the Audit Log.

**Alternative Flows:**
- A Teacher with no Billable Students has a Subscription basis derived from zero Billable Students.
- Pricing ownership is with the Super Admin; flat price versus volume tiers remains PENDING and is not assumed by this flow.
- Historical invoices retain the price applicable to their period.

**Error Flows:**
- A Teacher cannot view or manage another Teacher’s Subscription.
- A Student or Parent cannot access Flow A Subscription administration.
- An attempt to use Attendance or login activity to calculate Billable Students is rejected.
- An attempt to process a Subscription payment online is rejected because payment handling is outside the Platform in Version 1.

**Postconditions:** The authorized actor sees or manages only the recorded Flow A Subscription status within confirmed authority. No online transaction is processed.

**Business Rules:** Flow A is Teacher-to-Platform Subscription. Monthly Subscription = Billable Students × Price Per Student. A Student is Billable only after more than 15 calendar days of Enrollment in a Teacher’s Group during the Billing Cycle (BR-008). Flow A must not be conflated with Flow B student fee payment status. Pricing is Super Admin-owned (BR-015); payment processing is out of scope (BR-019).

---

# 27. Monthly Subscription Renewal

**Goal:** Start the next calendar-month Flow A Billing Cycle and maintain the recorded Subscription basis and status for Teachers.

**Actors:** Platform Scheduler; Super Admin where management is required; Teacher as the Subscription customer; Platform.

**Preconditions:** The current calendar-month Billing Cycle is ending or has ended. The Platform has Enrollment history, approved pricing context, and the scheduled Billing Cycle process available through cPanel Cron Jobs/Laravel Scheduler.

**Trigger:** The first day of a new calendar month begins, or an authorized Super Admin performs a permitted Billing Cycle management action.

**Main Flow:**
1. The Platform starts the new Billing Cycle on the first day of the calendar month.
2. The Platform establishes the cycle end as the last day of that same month.
3. For each Teacher, the Platform uses Enrollment duration in the relevant Billing Cycle to determine Billable Students.
4. The Platform applies the applicable Price Per Student and records the Flow A Subscription basis/status for the cycle.
5. The Platform preserves prior Billing Cycle and historical Subscription records.
6. The Platform records required Subscription/Billing Cycle actions in the Audit Log.

**Alternative Flows:**
- The Super Admin may view or manage Billing Cycle records and pricing within confirmed Platform authority.
- A Teacher can view the Teacher’s own recorded Subscription status for the new cycle.
- If a Student is enrolled for 15 days or less during a Billing Cycle, the Student is not Billable for that cycle.

**Error Flows:**
- A Billing Cycle that does not start on the first day or end on the last day of the same month is invalid.
- Attendance or login activity must not be used as an alternative Billable Student input.
- An attempt to enforce a non-payment consequence is not implemented because the enforcement behavior remains PENDING.
- An attempt to collect payment online, send Version 1 payment notifications, or delete historical Billing Cycles is rejected as out of scope.

**Postconditions:** A new calendar-month Billing Cycle exists with recorded Flow A Subscription basis/status derived from valid Enrollment duration rules. Historical cycles remain preserved.

**Business Rules:** Billing Cycles are calendar-month based. The new cycle begins automatically on the first day of the next month. Billable Student calculation is Enrollment-duration-only and requires more than 15 calendar days (BR-008). Attendance and login activity are excluded. Version 1 records status only; online payment gateways and notifications are out of scope. Non-payment enforcement remains PENDING.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority. |
| Required flows | Passed — all 27 requested user flows are present with Goal, Actors, Preconditions, Trigger, Main Flow, Alternative Flows, Error Flows, Postconditions, and Business Rules. |
| Scope | Passed — the document defines user journeys only; no UI designs, APIs, database tables, source code, or implementation details are included. |
| Teacher Workspace isolation | Passed — all Teacher, Teacher Staff, Student, file, Attendance, Lesson, Exam, and report-related paths preserve Teacher Workspace boundaries. |
| Student account rules | Passed — Student self-registration and Teacher-created activation use one global Student account; duplicate accounts are rejected; one Group per Teacher is preserved. |
| Parent rules | Passed — Parent monitoring is linked-Student scoped and read-only, with no Parent submission, grading, payment-status update, or educational-data modification flow. |
| RBAC | Passed — Teacher Staff actions remain conditional on Teacher-assigned permissions; frontend visibility is not treated as authorization; Super Admin private-content visibility remains PENDING. |
| Flow A / Flow B | Passed — Teacher Subscription and monthly renewal are Flow A; Parent payment view is Flow B payment status; no payment processing is introduced. |
| Attendance | Passed — Dynamic QR Code, ID Card alternative, and manual Attendance are preserved; Attendance is excluded from Billable Student calculation. |
| Content and exams | Passed — Lessons and Question Banks remain Teacher-owned and private; supported Homework and Exam types remain within Version 1 rules. |
| Archive and Audit Log | Passed — Archive replaces permanent deletion, history is retained, and required important actions remain auditable. |
| Version 1 exclusions and pending decisions | Passed — no marketplace, native mobile, notifications, payment gateway, video homework, unconfirmed impersonation, non-payment enforcement, or unresolved pricing/RBAC/content-visibility decision is introduced. |
| Terminology | Passed — Teacher Workspace, Educational Grade, Lesson, Attendance, Subscription, payment status, Flow A, Flow B, Archive, and Audit Log are used consistently. |


## Confirmed Audit Clarifications

The following confirmed clarifications are owned by `00_Project_Context.md` §9.9 (BR-023 through BR-025). They do not alter Flow A, Archive, Audit Log, or the external-payment boundary.

- **Parent workflow:** a Parent creates an account through Parent registration and submits a Parent–Student link request. The Teacher responsible for the Student’s active Enrollment approves or rejects it. Approval is allowed only when the Student has no other linked Parent; it creates read-only access. The Parent may request unlinking, and that Teacher approves the unlink. Request, approval, rejection, and unlinking are Audit Log events; historical records remain preserved.
- **Per Lesson Flow B:** a billable lesson is a Lesson completed for a Group while the Student has an active Enrollment in that Group. The obligation is recorded on completion at the Group’s recorded Price. Drafting, scheduling, publishing, viewing, Attendance, or assignment alone is not billable; one Student has at most one obligation for the same completed lesson. The Platform records payment status only, and payment remains outside the Platform.
- **Group transfer:** Flow A enrollment duration is accumulated across Groups of the same Teacher in the same Billing Cycle; a transfer does not reset the more-than-15-calendar-days test. Flow B obligations remain tied to the Group and completed Lesson that created them.
