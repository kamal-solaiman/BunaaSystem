# 16 — QR Attendance System

## Document Scope

This document defines the confirmed Version 1 QR Attendance System, which is part of the Teacher Workspace Attendance subsystem. It describes business behavior, role boundaries, validation boundaries, and explicitly records requested areas for which no Version 1 rule is confirmed.

This document does not include source code, APIs, database tables, UI implementation, scanner implementation, token formats, or deployment implementation. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and wins if any conflict is found.

Version 1 is a **Web Application only**. Dynamic QR Code Attendance is scanned by the Student through the Web Application, including a browser on a mobile-capable device where supported. This does not introduce a native mobile application. Printed **ID Cards with QR codes** are scanned by a QR scanner device through the Teacher Workspace Attendance operation. Barcode scanning is not a confirmed Version 1 Attendance method and is not defined by this document.

---

# 1. Feature Overview

QR Attendance provides one of three confirmed Attendance methods for a Teacher Workspace:

1. A **Dynamic QR Code** generated daily, displayed for the class, and scanned by the Student through the Web Application.
2. A printed **ID Card** containing a QR code, scanned by a QR scanner device as a Teacher Workspace Attendance operation.
3. **Manual Attendance** entered by the Teacher or authorized Teacher Staff.

Every Attendance record is scoped to the correct Teacher Workspace. Attendance history is preserved when a Student moves Groups. Attendance is not used to calculate Billable Students; Flow A Billable Student calculation uses Enrollment duration only.

---

# 2. Objectives

The QR Attendance System objectives are to:

1. Support the confirmed daily Dynamic QR Code Attendance method.
2. Let an authenticated Student scan a Dynamic QR Code through the Web Application for the Student’s relevant Teacher relationship.
3. Support printed ID Card QR scanning through a QR scanner device in the Teacher Workspace Attendance context.
4. Support manual Attendance by a Teacher or authorized Teacher Staff member.
5. Verify Student relationship and Teacher Workspace context before recording Attendance.
6. Maintain strict Teacher Workspace isolation for all Attendance data and actions.
7. Preserve Attendance history through Student Group movement and Archive-related historical views.
8. Record Attendance changes in the Audit Log.
9. Provide authorized Teacher, Student, Parent, and reporting views without changing their role boundaries.
10. Keep Attendance completely separate from Flow A Billable Student calculation and from Flow B payment status.

---

# 3. Attendance Methods

| Method | Confirmed actor and flow | Scope boundary |
|---|---|---|
| Dynamic QR Code | The Student scans a daily Dynamic QR Code through the Web Application. | The Student must have a valid relationship with the relevant Teacher Workspace. |
| Printed ID Card QR scan | The Student presents a printed ID Card with a QR code; a QR scanner device reads it in the Teacher Workspace Attendance context. | Teacher-side/Teacher Workspace operation; a Student has no self-service ID Card scanning permission. |
| Manual Attendance | The Teacher or authorized Teacher Staff records Attendance manually. | Only inside the current Teacher Workspace and only with required permission. |

No other Attendance method is confirmed. In particular, barcode scanning, biometric Attendance, face recognition, geolocation, GPS check-in, NFC, Bluetooth, SMS, email, and native mobile Attendance are out of scope for this Version 1 definition.

---

# 4. Dynamic QR Code

A Dynamic QR Code is generated daily for Attendance in a Teacher Workspace context and displayed for the class.

The confirmed Dynamic QR Code flow is:

1. An authorized Teacher or Teacher Staff member establishes a valid Teacher Workspace Attendance Session/context.
2. The Platform generates the Dynamic QR Code for the daily Attendance context.
3. The Dynamic QR Code is displayed for the class.
4. The authenticated Student scans the QR Code through the Web Application.
5. The backend authenticates the Student and verifies the Student’s relationship with the relevant Teacher.
6. The backend records Attendance in the correct Teacher Workspace context when validation succeeds.
7. The Attendance change is recorded in the Audit Log.

The QR visual value alone never proves a Student’s eligibility or records Attendance. The backend performs the final authentication, relationship, scope, and duplicate/inconsistent-record checks.

---

# 5. QR Generation Rules

The following QR generation rules are confirmed:

- A Dynamic QR Code is generated **daily**.
- Generation occurs in an Attendance context belonging to the current Teacher Workspace.
- The Dynamic QR Code is displayed for the class.
- A valid Attendance Session/context belongs to the current Teacher Workspace and relevant Group/session context.
- Teacher and authorized Teacher Staff generation/recording actions require the appropriate Teacher Workspace Attendance permission.
- Generated QR Attendance context cannot be used to record Attendance for a Student outside the relevant Teacher relationship.
- The Dynamic QR Code method is separate from printed ID Card QR scanning and Manual Attendance.

The QR payload format, visual encoding configuration, token construction, refresh frequency within a day, rotation behavior, and generation implementation are not confirmed and are not defined here.

---

# 6. QR Expiration Rules

The confirmed lifecycle rule is **daily** Dynamic QR Code generation. The official requirements do not define a specific expiry timestamp, duration, grace interval, scan window, timezone rule, mid-day refresh, automatic invalidation event, or QR rotation interval.

Version 1 therefore requires only that:

- A Dynamic QR Code must be valid for the relevant daily Attendance context when scanned.
- An invalid QR context is rejected.
- An archived, invalid, or otherwise inactive Attendance context is not treated as an active Attendance scan context.
- The Platform must not present an unconfirmed duration or expiry promise to users.

Precise expiration rules remain unconfirmed and must not be invented. Arabic (default) and English (fully supported) with automatic RTL/LTR are confirmed; timezone, country, and regional time formatting remain PENDING.

---

# 7. Student QR Scan Flow

1. The Student authenticates through the Web Application.
2. The Student opens the authorized Dynamic QR Code Attendance scan task.
3. The Student scans the daily Dynamic QR Code displayed for the class through the Web Application. A mobile-capable browser may be used where browser capability permits; no native application is required or implied.
4. The Platform receives the scan in the authenticated Student context.
5. The backend verifies that the Dynamic QR Code context is valid and that the Student is associated with the relevant Teacher relationship.
6. The backend verifies that the Attendance record belongs to the correct Teacher Workspace context and does not create an inconsistent duplicate record.
7. If valid, the backend records the Student’s Attendance.
8. The Platform provides an accurate recorded, rejected, unavailable, or error outcome.
9. The Attendance event is recorded in the Audit Log where required.

The Student can scan only for the Student’s own Attendance. The Student cannot scan an ID Card as a self-service Attendance operation, manually change Attendance, record another Student’s Attendance, or use QR scanning to access another Teacher Workspace.

---

# 8. Student ID Card Scanner Flow

The confirmed ID Card method uses a printed **QR code**, not a barcode.

1. The Student presents a printed ID Card with the Student’s QR code.
2. A QR scanner device reads the ID Card during a valid Teacher Workspace Attendance Session/context.
3. The Platform resolves the Student identity and the relevant Teacher Workspace Attendance context.
4. The backend verifies the Student relationship with that Teacher Workspace and the validity of the Attendance Session/context.
5. If valid, the backend records Attendance.
6. The Platform records the Attendance change in the Audit Log.

The scanner is part of the Teacher Workspace Attendance operation. The Teacher or authorized Teacher Staff operates within the required permission boundary. The Student does not receive a self-service ID Card scan permission. Barcode formats, card printing design, card issuance, lost-card handling, scanner hardware models, and scanner connectivity are not confirmed.

---

# 9. Manual Attendance Flow

1. The Teacher or authorized Teacher Staff authenticates in the current Teacher Workspace.
2. The actor opens the relevant Attendance Session/context and identifies the Student.
3. The actor records or corrects the Attendance status manually.
4. The backend verifies the actor’s permission, Teacher Workspace scope, Student relationship, and valid Attendance context.
5. The backend records or updates the Attendance record.
6. The Platform preserves historical context and records the Attendance change in the Audit Log.

Manual Attendance is the confirmed Teacher-side alternative when Dynamic QR scanning or printed ID Card QR scanning is unavailable. Parent users cannot record or update Attendance. Student users cannot manually modify Attendance.

The permitted Attendance status values and correction-reason requirements are not defined by the confirmed requirements; only the ability to record and correct Attendance status is confirmed.

---

# 10. Attendance Session

An **Attendance Session** is the Attendance context in which the confirmed Attendance methods operate.

Confirmed Session rules:

- A Session belongs to the current Teacher Workspace.
- A Session is associated with a Group/session context and date context as required for Teacher Workspace Attendance.
- A Student Attendance record must be associated with the correct Student and Teacher Workspace Attendance Session/context.
- Dynamic QR Code generation operates in a valid Attendance Session/context.
- Printed ID Card scanning operates in a valid Teacher Workspace Attendance Session/context.
- Manual Attendance uses the valid Teacher Workspace Attendance Session/context.
- Attendance changes for a Session are auditable.

The Session title, recurrence, meeting schedule, start/end time, capacity, lifecycle state, editing policy, and relationship to Group Schedule beyond the confirmed context are not specified and must not be inferred.

---

# 11. Late Arrival Rules

Late Arrival status, late thresholds, grace periods, Teacher overrides, automatic lateness determination, and late-arrival reporting are not confirmed Version 1 requirements.

The system may record or correct Attendance status through authorized Teacher Workspace operations, but this document does not define **Late** as a required status or a rule for determining it. No QR scan time rule may be interpreted as a confirmed late-arrival rule.

Any future late-arrival feature must preserve Teacher Workspace isolation, Student self scope, Parent read-only visibility, Attendance history, and Audit Log requirements.

---

# 12. Absence Rules

Absence status, absence reason, excused/unexcused distinctions, automatic absence marking, absence thresholds, and absence-related enforcement are not confirmed Version 1 requirements.

The confirmed system records and permits authorized correction of Attendance status. It does not define an absence algorithm or status taxonomy. Attendance history remains available after Group movement and within permitted reports/history views.

No absence state may be used for Flow A Billable Student calculation. Billable Students are determined by Enrollment duration only.

---

# 13. Duplicate Scan Prevention

The Platform must prevent inconsistent duplicate Attendance records when duplicate Attendance is attempted for the same Attendance context.

Confirmed behavior:

- A Student scanning the same Dynamic QR Code more than once for the same Attendance context must not create inconsistent duplicate Attendance.
- A manual entry or ID Card scan that conflicts with the same Student and Attendance context must be validated safely.
- The backend determines whether an Attendance record may be recorded or corrected.
- The user receives an accurate outcome rather than a false success state.
- Relevant Attendance changes remain auditable.

The exact deduplication key, retry behavior, update-versus-reject logic, timing window, and conflict-resolution policy are not confirmed and are not defined here.

---

# 14. Invalid QR Handling

An invalid Dynamic QR Code scan is rejected.

The Platform must reject a scan when, for example, the QR context is not valid for the relevant Attendance context or the Student does not have a valid relationship with the relevant Teacher Workspace. The response must:

- Make clear that Attendance was not recorded.
- Avoid exposing the identity, records, or Teacher Workspace details of another Student or Teacher.
- Avoid revealing QR internals, token values, storage details, or implementation details.
- Allow the authorized Teacher/Teacher Staff to use the confirmed Manual Attendance method when appropriate.

A failed scan does not grant the Student access to another Teacher’s Attendance, Group, Student list, or private Teacher Workspace data.

---

# 15. Offline Scenarios

Offline QR scanning, offline Attendance storage, deferred synchronization, local conflict resolution, and offline-first behavior are not confirmed Version 1 requirements.

The confirmed flows require the Web Application to communicate with the backend so the backend can authenticate the user, validate the Student relationship and Teacher Workspace context, record Attendance, apply duplicate/inconsistent-record safeguards, and create required Audit Log entries.

When connectivity or a required browser/scanner capability is unavailable:

- The Platform must not claim that Attendance was recorded when backend confirmation has not occurred.
- The authorized Teacher or Teacher Staff may use the confirmed Manual Attendance method when the Platform is available.
- No local/offline Attendance record, sync queue, or automatic reconciliation behavior is assumed.

---

# 16. Attendance Statuses

Version 1 confirms that Manual Attendance can record an **Attendance status** and that authorized actors can correct Attendance. It does not define the allowed status values, labels, transitions, default status, absence/late meanings, or reporting aggregation rules.

Accordingly:

- The status must be associated with the correct Student, Attendance Session/context, and Teacher Workspace.
- Only the Teacher or authorized Teacher Staff may record or correct status within the Teacher Workspace.
- A Student may view the Student’s own Attendance where available but may not change it.
- A Parent may view linked Student Attendance read-only but may not change it.
- Attendance status must not be used for Billable Student calculation.
- Any future status taxonomy must be formally approved and preserve historical record integrity.

---

# 17. Teacher Permissions

| Actor | Confirmed Attendance permissions | Boundaries |
|---|---|---|
| Teacher | Views, records, corrects/updates, views history, and performs authorized Archive/restore of Attendance-related records in the Teacher’s own Teacher Workspace. Handles Dynamic QR Code context, printed ID Card QR scanning, and Manual Attendance. | Cannot access another Teacher Workspace’s Attendance or Student relationships. |
| Teacher Staff | Performs Attendance view, record, update, history, Dynamic QR Code/ID Card/manual operations only with explicit Teacher-assigned permission. | Exists only in the creating Teacher Workspace. Detailed permission granularity remains PENDING. Actions are attributed to the Teacher Staff user. |
| Super Admin | Has no confirmed Teacher Workspace Attendance operation permission. Platform-level attendance report summary visibility is conditional on confirmed Super Admin reporting boundaries. | Does not gain Teacher-private Attendance browsing or Teacher impersonation. |

Teacher-side operations require backend authorization and Teacher Workspace scope verification. Archive replaces permanent deletion where an Attendance-related record is archivable; historical information remains retained.

---

# 18. Student Permissions

A Student may:

- Scan the daily Dynamic QR Code through the Web Application for the Student’s own Attendance, after backend validation of the relevant Teacher relationship.
- View the Student’s own Attendance where available in the Student’s per-Teacher records.

A Student may not:

- Scan a printed ID Card as a self-service Attendance operation.
- Record, correct, Archive, restore, or manually modify Attendance.
- Scan or view Attendance for another Student.
- Scan a Dynamic QR Code for a Teacher with whom the Student does not have a valid relationship.
- Access Teacher Workspace Attendance lists, private Attendance data, or another Teacher’s Student records.

---

# 19. Parent Visibility

A Parent may view Attendance for linked Students only, in read-only mode.

Parent visibility requirements:

- The Parent is authenticated and selects a linked Student.
- The Platform validates the Parent-Student link before resolving Attendance.
- Attendance is presented only for that linked Student and remains separated by the Student’s Teacher relationships where applicable.
- Historical Attendance may remain available where permitted, including after Student Group movement; archived/historical context is clearly indicated.
- The Parent cannot record, correct, Archive, restore, or otherwise modify Attendance.
- The Parent cannot view Attendance for an unlinked Student or use the view to discover another Teacher Workspace’s data.

Version 1 supports exactly one Parent account per Student, while a Parent account may monitor multiple linked Students.

---

# 20. Reports Integration

Attendance supports Teacher Workspace operational reporting and authorized Student/Parent history views.

| Role context | Confirmed reporting/view boundary |
|---|---|
| Teacher | Views Attendance records and Teacher Workspace reports/history only within the Teacher’s own Teacher Workspace. |
| Teacher Staff | Views Attendance information only with Teacher-assigned permission in the creating Teacher Workspace. |
| Student | Views the Student’s own Attendance where available, partitioned by Teacher. |
| Parent | Views linked Student Attendance read-only, partitioned by Teacher where applicable. |
| Super Admin | Platform attendance report-summary visibility is conditional on confirmed reporting/content-visibility boundaries; unrestricted Teacher-private Attendance access is not granted. |

Reports may include archived Attendance-related records where historical rules require it; archived records must be clearly indicated and never presented as active records. Attendance reports must not be used to calculate Flow A Billable Students or to process Flow B payments.

---

# 21. Audit Logging

The Audit Log is append-only, immutable, and permanently retained. Attendance changes must be recorded in the Audit Log.

Required Attendance-related audit coverage includes, where applicable:

- Dynamic QR Code Attendance recording.
- Printed ID Card QR scan Attendance recording.
- Manual Attendance recording.
- Authorized Attendance correction or modification.
- Archive and restore actions for archivable Attendance-related records.
- Attendance Session or Dynamic QR Code generation actions where they qualify as important Attendance actions.
- Teacher Staff Attendance actions, attributed to the Teacher Staff user rather than the Teacher.
- Failed or denied security-sensitive actions where required by the Audit Log policy.

Audit entries preserve the relevant Teacher Workspace or Platform context and actor attribution. They are not editable, archivable, or deletable. Parent and Student Audit Log views are not confirmed Version 1 product surfaces.

---

# 22. Error Handling

| Condition | Required handling |
|---|---|
| Student not authenticated for Dynamic QR scan | Deny scan; no Attendance is recorded. |
| Dynamic QR context invalid | Reject scan and state safely that Attendance was not recorded. |
| Student has no valid Teacher relationship | Deny the Attendance action without exposing private Teacher Workspace data. |
| ID Card QR cannot resolve a valid Student/Teacher Workspace relationship | Reject the Attendance action. |
| Manual Attendance actor is unauthorized | Deny the action. |
| Attendance request targets another Teacher Workspace | Deny without exposing restricted data. |
| Duplicate/inconsistent Attendance attempt | Prevent inconsistent duplicate record; provide an accurate safe outcome. |
| Archived/inactive Attendance context used as active | Reject active Attendance action unless separately permitted historical viewing applies. |
| Invalid Attendance status input | Reject according to the supported status rules when those are formally defined; do not invent a status value. |
| Connectivity/scanner capability unavailable | Do not claim a record was created; use an appropriate unavailable/error state and the confirmed Manual Attendance path where applicable. |

Error messages must not reveal another Student’s Attendance, unlinked Student data, another Teacher Workspace, QR internals, credentials, technical stack details, or raw backend errors.

---

# 23. Edge Cases

The QR Attendance System must safely handle these confirmed or directly required cases:

1. A new Teacher Workspace has no Students, Groups, Attendance Sessions, or Attendance records.
2. A valid Dynamic QR Code is displayed but a Student is not associated with the relevant Teacher Workspace.
3. A Student scans the same Dynamic QR Code more than once for the same Attendance context.
4. A printed ID Card QR scan does not resolve a valid Student relationship for the current Teacher Workspace.
5. ID Card scanner access is unavailable and the Teacher/authorized Teacher Staff uses Manual Attendance.
6. A Student moves Groups after prior Attendance exists; the historical Attendance remains available.
7. An Attendance-related record or Group is archived after historical Attendance exists; historical reports retain the record and indicate archival state.
8. A Teacher Staff member attempts Attendance work without the required Teacher-assigned permission.
9. A Parent selects an unlinked Student’s Attendance.
10. A Student tries to scan for another Student or a Teacher relationship that is not the Student’s own.
11. An authorized actor corrects Attendance; the change remains auditable.
12. The Web Application lacks required connectivity or scan capability; no unconfirmed offline Attendance behavior is assumed.

Late arrivals, absences, timer/expiration specifics, barcode input, offline synchronization, and Attendance status values remain unconfirmed rather than being inferred from these cases.

---

# 24. Security Considerations

Security is preserved through the confirmed platform architecture and Attendance boundaries:

1. Every protected Attendance action requires authenticated context.
2. The backend makes the final authorization, Student relationship, ownership, Teacher Workspace, record-state, and duplicate/inconsistent-record decisions.
3. Dynamic QR Code scanning validates the authenticated Student against the relevant Teacher relationship before Attendance is recorded.
4. Printed ID Card QR scanning validates the Student identity and relevant Teacher Workspace Attendance context before Attendance is recorded.
5. Teacher Staff access is limited to explicit Teacher-assigned permissions within the creating Teacher Workspace.
6. Parent access is linked-Student scoped and read-only; Student access is self-scoped.
7. Cross-Teacher Attendance access is denied without exposing restricted data.
8. QR values, Student identity, Attendance history, and Teacher Workspace details are not exposed through error messages, browser state, or UI feedback beyond authorized context.
9. Required Attendance changes are recorded in the immutable, permanent Audit Log.
10. Attendance does not alter, calculate, or reveal Flow A Billable Student calculation, which is based on Enrollment duration only.

Specific QR signing, token lifetime, encryption, rate-limit thresholds, scanner hardening, device management, and offline security controls are not confirmed implementation requirements and are not defined here.

---

# 25. Future Improvements

The following are future considerations only and are not Version 1 commitments:

| Future area | Required future decision |
|---|---|
| QR expiration | Define expiry duration, rotation, timezone, refresh, and invalidation rules. |
| Attendance statuses | Define status values, absence/late meanings, correction reasons, and historical reporting rules. |
| Late and absence policy | Define thresholds, excused/unexcused policy, automation, and authorized overrides. |
| Offline behavior | Define offline capture, storage, synchronization, conflict handling, and security before implementation. |
| Card/scanner support | Consider barcode or other card formats only through separate approval; Version 1 confirms QR ID Cards only. |
| Additional methods | Biometric, geolocation, GPS, NFC, Bluetooth, or other methods require separate approval and privacy/security review. |
| Notifications | Push, email, and SMS Attendance notifications remain out of scope until separately approved. |
| Native application | A native Attendance application is outside Version 1 and requires separate scope approval. |
| Reporting | Any advanced Platform-level Attendance visibility must resolve Super Admin Teacher-private content boundaries. |

All future work must preserve Teacher Workspace isolation, one global Student account, Parent linked-Student read-only access, Enrollment-duration-only Billable Student calculation, Archive instead of permanent deletion, historical retention, and permanent immutable Audit Log records.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority. |
| Requested section coverage | Passed — all 25 requested QR Attendance System sections are present. |
| Confirmed Attendance methods | Passed — daily Dynamic QR Code, printed QR ID Card scanner, and Teacher/authorized Teacher Staff Manual Attendance are the only defined methods. |
| Mobile/browser boundary | Passed — Student QR scanning is defined through the Web Application, including supported mobile-capable browsers, without introducing a native mobile application. |
| Barcode constraint | Passed — the requested card scanner flow is documented as the confirmed printed QR ID Card flow; barcode support is explicitly not assumed. |
| Unconfirmed rules | Passed — QR expiry specifics, late/absence policy, offline behavior, status taxonomy, Session lifecycle detail, and scanner implementation are explicitly not invented. |
| Role and tenant boundaries | Passed — Teacher Workspace isolation, Teacher Staff assigned permissions, Student self scan/view scope, Parent linked-Student read-only visibility, and constrained Super Admin reporting are preserved. |
| Attendance business rules | Passed — history survives Group movement, Archive does not delete history, changes are auditable, and Attendance is excluded from Billable Student calculation. |
| Scope | Passed — no source code, APIs, database tables, UI implementation, token formats, or scanner implementation details are included. |
| Version 1 exclusions | Passed — no native application, notification system, payment processing, biometric/geolocation method, barcode assumption, or unconfirmed offline capability is introduced. |
| Terminology | Passed — Teacher Workspace, Attendance, Dynamic QR Code, ID Card, Student, Parent, Archive, Audit Log, Flow A, Flow B, and Billable Student are used consistently. |
