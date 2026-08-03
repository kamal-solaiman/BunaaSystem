# 20 — File Storage

## Document Scope

This document defines the confirmed Version 1 File Storage architecture for the Unified Education Platform. It covers Laravel Public Storage, cPanel Shared Hosting compatibility, confirmed file-owning contexts, authorization boundaries, Archive/history behavior, and future cloud-storage constraints.

It does not provide source code, APIs, database tables, UI implementation, storage-driver configuration, file-size values, storage paths as security credentials, streaming mechanics, signed-URL mechanics, backup procedures, or cloud migration implementation. `AI_DOCS/00_Project_Context.md` is the official Single Source of Truth and prevails if a conflict is found.

Laravel Public Storage is the Version 1 storage baseline. The backend is authoritative for file validation, ownership, Teacher Workspace isolation, Student relationship checks, Parent linked-Student scope, Archive state, and authorized file access. A file reference or storage location never grants access by itself.

---

# 1. Feature Overview

File Storage supports controlled access to files used by confirmed Version 1 features:

- Private Teacher-owned Lesson videos for the Teacher’s own Students.
- Homework files in the confirmed Text, Image, and PDF contexts.
- Student Homework submission files in supported Image and PDF formats.
- Logical file references required by business records, reports, Archive, and historical retention.

Version 1 stores file binaries using Laravel Public Storage and stores logical ownership/reference information separately from file bytes. File access must pass through application-level authorization and ownership checks so that Teacher-owned content remains private to the correct Teacher Workspace and authorized relationships.

File Storage is not a public media catalog, marketplace, cross-Teacher sharing system, video homework system, S3 Storage requirement, or direct browser-storage access capability.

---

# 2. Objectives

The confirmed objectives are to:

1. Use Laravel Public Storage in a manner compatible with cPanel Shared Hosting.
2. Store and reference Teacher-owned Lesson videos privately for authorized Students and Teacher-side users.
3. Support Homework-related Text, Image, and PDF file contexts only.
4. Support Student Homework submissions only when assigned and permitted.
5. Preserve Teacher Workspace ownership and relationship-based access control for every file.
6. Keep file references available for reports, historical records, Archive, and restore behavior.
7. Prevent cross-Teacher, unrelated Student, and unlinked Parent file access.
8. Preserve Archive instead of permanent deletion as the product lifecycle policy.
9. Keep Version 1 compatible with shared-hosting limits without requiring S3 Storage, Redis, WebSockets, Docker, Kubernetes, or microservices.
10. Allow future storage changes only when they preserve file ownership, authorization, privacy, and historical references.

---

# 3. Supported File Types

| File context | Confirmed Version 1 rule |
|---|---|
| Homework assignment | Text, Image, and PDF only. Text may be logical content rather than a binary file. |
| Homework submission | Text, Image, and PDF only; student binary upload is limited to Image or PDF for assigned Homework where permitted. |
| Lesson | Teacher-owned private video Lesson for the Teacher’s own Students. |
| Parent upload | Denied. |
| Video homework | Denied. |
| Other file type/context | Not confirmed; must not be introduced without approval. |

The exact image formats, PDF version requirements, video codecs, MIME-type catalog, file extensions, and media-processing behavior are not confirmed. Validation must enforce the owning resource’s confirmed context without silently expanding allowed types.

---

# 4. Image Storage

Image storage is confirmed only within supported Homework and Student Homework submission contexts.

- A Teacher Workspace Homework file may use Image where the Homework context permits it.
- A Student may upload an Image only as a supported submission for Homework assigned to that Student and only through the Student’s valid Teacher relationship.
- An Image file remains associated with its owning Homework, Homework Submission, Student/Teacher relationship where applicable, and Teacher Workspace context.
- Parent image uploads are denied.
- Image files must not be visible across Teacher Workspaces or to unrelated Students/Parents.
- Image references remain available for historical records and Archive behavior where authorized.

User profile images, image galleries, public images, image discovery, image transformation, thumbnail policy, image-size limit, and image-format catalog are not confirmed Version 1 features.

---

# 5. PDF Storage

PDF storage is confirmed only within supported Homework and Student Homework submission contexts.

- A Teacher Workspace Homework file may use PDF where the Homework context permits it.
- A Student may upload a PDF only as a supported submission for assigned Homework and through the Student’s valid Teacher relationship.
- A PDF reference remains associated with its owning resource, Teacher Workspace, and authorized relationship context.
- Parent PDF uploads are denied.
- PDF access must not cross Teacher Workspace, Student self, or Parent linked-Student boundaries.
- Historical PDF references remain available where reports/history require them and are subject to Archive policy.

PDF annotation, conversion, preview generation, OCR, signing, print behavior, PDF version constraints, and size limits are not confirmed and are not defined here.

---

# 6. Lesson Video Storage

Lesson videos are private Teacher-owned files.

Confirmed rules:

- A Teacher may upload Lesson videos exclusively for that Teacher’s own Students.
- A Lesson video belongs to the owning Teacher Workspace.
- Authorized Teacher-side users may access Lesson files only within the current Teacher Workspace and according to permission.
- A Student may access Lesson content only from the Student’s own Teachers and only when the Lesson is authorized/available through that Teacher relationship.
- One Teacher’s Lesson videos must never be accessible to another Teacher’s Students.
- Lesson videos must not appear in marketplace discovery, public course browsing, or public Teacher content catalog behavior.
- Archived Lesson files and references remain retained historically but are not active Lesson content.

Lesson video hosting and protection details remain PENDING. This document does not assume streaming, download, public URLs, signed URLs, video formats, transcoding, quotas, previews, watermarking, or cloud-video hosting. S3 Storage is not required for Version 1.

---

# 7. Homework Attachments

Homework attachments are Teacher Workspace-owned file references associated with Homework or Homework Submissions.

| Attachment context | Confirmed access and type rule |
|---|---|
| Teacher Homework attachment | Must belong to the current Teacher Workspace; supported Homework formats are Text, Image, and PDF. |
| Student Homework submission attachment | Must belong to assigned Homework, the authenticated Student, and the relevant Teacher relationship; binary upload is Image or PDF only. |
| Parent attachment | Parent upload is denied. Parent monitoring remains read-only. |
| Archived attachment | Historical reference is retained and available only where authorized. |

Homework attachments must preserve Student and Teacher relationship context, prevent unrelated access, and remain available for historical records. Video homework must not be accepted, attached, or represented as a supported Homework format.

---

# 8. User Profile Images

User Profile Images are not a confirmed Version 1 file-storage feature.

No profile-image upload, storage, replacement, access rule, file type, image limit, public visibility rule, or profile-image lifecycle is defined by the official source documents. This document must not introduce one merely because Image storage is supported for Homework contexts.

If profile images are considered in a future approved scope, they must define ownership, role-specific visibility, privacy, authorization, Archive/history behavior, and storage validation without weakening Teacher Workspace isolation or Parent/Student boundaries.

---

# 9. File Naming Strategy

No physical file naming convention, extension policy, generated identifier format, path derivation, user-visible filename rule, or collision strategy is confirmed for Version 1.

The confirmed naming/identity requirements are limited to:

- A file reference identifies an owning logical resource.
- File type must match the allowed type for its owning context.
- A storage reference logically identifies the Laravel Public Storage location.
- Ownership and access are determined by authorized application context, not by a filename or storage reference.
- Canonical product terms must be used in user-facing descriptions: **Lesson**, **Homework**, **Archive**, and **Teacher Workspace**.

A future naming strategy must avoid exposing sensitive identifiers or relying on path/name secrecy as an authorization mechanism. It must preserve historical references when files are archived, restored, or storage infrastructure changes.

---

# 10. Directory Structure

The Version 1 project structure reserves Laravel Public Storage for runtime file data. The following is the approved high-level organization from the Project Structure document; it is an ownership-oriented structure, not an access-control mechanism.

```text
backend/
├── storage/
│   ├── app/
│   │   └── public/
│   │       ├── teacher-workspaces/      # Runtime Teacher Workspace-owned file namespace
│   │       │   ├── lessons/             # Private Teacher-owned Lesson video files/references
│   │       │   ├── homework/            # Permitted Teacher-provided Homework attachments
│   │       │   └── files/               # Other authorized Teacher Workspace file resources
│   │       └── student-homework/        # Student Homework Image/PDF submission files
│   ├── framework/                       # Laravel runtime framework data
│   └── logs/                            # Operational logs
└── public/
    └── storage/                         # Public-storage link or hosting mapping; not an authorization boundary
```

Rules for this organization:

- Runtime file storage, logs, framework data, and generated mappings are not source-controlled.
- Directory/namespacing location does not grant file access.
- Application-level authorization and ownership checks remain mandatory even where Laravel Public Storage/public mapping is used.
- The exact physical path pattern, storage file naming, Lesson-video protection mapping, and hosting-server configuration are implementation/deployment concerns and are not defined here.

---

# 11. Upload Validation Rules

Before a file is accepted, the Platform validates the confirmed file context. The backend remains authoritative.

1. The uploader must be authenticated and authorized for the owning resource and scope.
2. The owning resource must exist in the authorized context.
3. Teacher Workspace file ownership must be enforced for Teacher-owned files.
4. Student Homework submission must belong to Homework assigned to the authenticated Student through a valid Teacher relationship.
5. Parent uploads are denied.
6. File type must match the owning resource’s allowed context.
7. Homework supports Text, Image, and PDF only; a binary Student Homework file is Image or PDF only.
8. Video homework is rejected.
9. Lesson videos must remain Teacher-owned and private; hosting/protection details remain PENDING.
10. Archived/inactive owning resources must not be treated as active upload targets unless separately restored/authorized according to Archive rules.
11. File references must preserve ownership and historical context after acceptance.

File-size limits, MIME allowlists, extension handling, content inspection, virus scanning, checksum handling, duplicate-file detection, conversion, and media-processing rules are not confirmed and are not defined here.

---

# 12. File Size Limits

No file-size limit is confirmed for Version 1.

The Platform must not present a fabricated maximum size for Image, PDF, or Lesson video uploads. Any future size limit must be approved with consideration for Laravel Public Storage, cPanel Shared Hosting constraints, the owning file context, upload reliability, storage capacity, and authorized historical retention.

A future size limit must not alter the confirmed type rules, Teacher Workspace isolation, private Lesson ownership, Student Homework submission scope, Parent upload denial, or Archive behavior.

---

# 13. Storage Security

Storage security is achieved through application-level authorization and ownership controls around Laravel Public Storage.

1. The browser/frontend does not directly access the database or file storage as an authorization bypass.
2. The backend validates the authenticated user, role, Teacher Workspace, resource ownership, Student relationship, Parent link, Archive state, and permitted file operation.
3. Teacher-owned Lesson videos and Teacher Workspace files remain private to authorized Teacher-side users and the Teacher’s own Students where applicable.
4. Student Homework files remain limited to the correct Student, Homework, Teacher relationship, and authorized Teacher Workspace reviewers.
5. A Parent sees only permitted linked-Student information in read-only scope and has no upload authority.
6. File storage paths, public mappings, references, and browser-visible identifiers are not authorization proofs.
7. Cross-Teacher file access is denied.
8. Errors must not reveal storage paths, private file details, credentials, signed-access mechanics, or unrelated file existence.
9. Archive and history requirements preserve references without making archived files active or broadly accessible.

Specific encryption, malware scanning, signed URL, streaming, quota, token, public-access, and server-configuration mechanisms are not confirmed and remain outside this document.

---

# 14. Access Control

| Actor | Confirmed file-access boundary |
|---|---|
| Teacher | May manage authorized Teacher Workspace files within the Teacher’s own Teacher Workspace. |
| Teacher Staff | May access/manage files only in the creating Teacher Workspace and only with Teacher-assigned permissions. |
| Student | May submit supported Image/PDF Homework files for assigned Homework where permitted; may access authorized Lessons and own related files through the Student’s Teacher relationships. |
| Parent | May not upload files. Parent file/educational-data visibility is limited to linked Students and remains read-only where confirmed. |
| Super Admin | Platform-level authority only. Teacher-private file/content visibility remains PENDING and must not be expanded. |

Every file operation must pass authentication, role/scope evaluation, ownership/relationship checks, and Archive-state validation. A Teacher Workspace file list, file identifier, logical storage reference, or guessed path never grants access to another Teacher’s files.

---

# 15. File Download Rules

The confirmed rule is **authorized file access**: a file reference may be accessed only when it is visible through the user’s role and authorized relationship.

Version 1 does not define a separate file-download policy, download capability, streaming behavior, public link, export behavior, or offline copy policy. Therefore:

- A request to access a file must pass the same authorization, ownership, Teacher Workspace, Student relationship, Parent link, and Archive checks as other protected file operations.
- A file must not be made available simply because its storage reference is known.
- Lesson video access remains private and subject to the PENDING hosting/protection decision.
- Historical file references remain available only where reports/history and authorization permit; Archive does not create public availability.

Any future download rule requires separate approval and must preserve private Lesson ownership, Homework submission privacy, historical-reference validity, and Teacher Workspace isolation.

---

# 16. File Replacement Rules

No file replacement/versioning policy is confirmed for Version 1.

The Platform may manage file references associated with confirmed owning resources, but this document does not define whether a replacement creates a new reference, changes an existing reference, retains prior binary versions, or affects historical records.

A future approved replacement rule must:

- Preserve historical references required by reports, Homework, Lessons, and archived records.
- Preserve Teacher Workspace ownership and authorized relationships.
- Avoid silently overwriting a file that is required for historical integrity.
- Apply Archive rather than permanent deletion when a prior reference must leave active use.
- Record important replacement-related actions in the Audit Log where policy requires.

---

# 17. File Deletion Policy

No permanent deletion exists in Version 1. **Archive** replaces deletion for file references and owning business records.

- Authorized users may Archive a file reference only within the appropriate Teacher Workspace scope and permission boundary.
- Authorized users may restore an archived file reference where restoration is allowed.
- Archive does not detach a file reference from historical Homework, Lesson, report, or other owning-record context.
- Archived files/references are historical or inactive and must not be presented as active content.
- Historical references remain available where reports and history require them and access is authorized.
- Archive/restore actions are audited where applicable.

The physical binary-deletion lifecycle, storage-retention period, and any eventual safe removal mechanism are not confirmed. They must not be used to undermine the confirmed permanent historical data and Archive rules.

---

# 18. Orphan File Cleanup

No orphan-file detection, cleanup schedule, physical file removal policy, retention duration, or reconciliation process is confirmed for Version 1.

Because historical file references must remain valid and Archive replaces permanent deletion, an automated cleanup process must not be assumed. It could otherwise remove a binary or reference needed by historical Homework, Lesson, Student submission, report, Audit Log context, or archived record.

Any future cleanup capability requires separate approval and must prove that it preserves historical-reference validity, Teacher Workspace ownership, Archive policy, authorized access, backup/recovery requirements, and Audit Log obligations.

---

# 19. Backup Considerations

Version 1 targets cPanel Shared Hosting and Laravel Public Storage. The official documents require historical retention but do not define a file-backup topology, frequency, retention schedule, restore process, disaster-recovery objective, or cPanel backup configuration for file binaries.

The following principles apply:

- File binaries and their logical references must remain consistent enough to preserve authorized historical access.
- Backup/restore planning must preserve Teacher Workspace isolation, private Lesson ownership, Student Homework submission privacy, Archive state, and historical references.
- Backup handling must not make protected files public or expose storage paths/credentials.
- Backup artifacts must not be committed to the source repository.
- Detailed backup and recovery planning belongs to the approved deployment/operations scope and requires separate definition.

---

# 20. Future Cloud Storage Support

S3 Storage and other cloud/object storage are not required for Version 1. Laravel Public Storage remains the Version 1 baseline.

Future cloud storage may be considered only through separate approval. To avoid a product redesign, any future storage provider must preserve these existing architectural contracts:

1. Logical files remain associated with an owning resource and authorized context.
2. Teacher-owned files remain scoped to the owning Teacher Workspace.
3. Lessons remain private to the owning Teacher and authorized Students.
4. Student Homework submissions remain associated with the Student, assigned Homework, and relevant Teacher relationship.
5. Parent upload denial and Parent linked-Student read-only boundaries remain intact.
6. File references remain historically valid through Archive, reports, and restoration.
7. Access remains application-authorized; a storage-provider location must not become the access-control mechanism.
8. Cross-Teacher file access remains denied.
9. Future migration must not require changing canonical product terminology or reinterpreting Flow A/Flow B boundaries.

Cloud provider selection, migration process, object naming, private-link mechanics, replication, cost, quotas, encryption, CDN, streaming, or download behavior are future implementation/operations decisions and are not defined here.

---

# 21. Error Handling

| Condition | Required handling |
|---|---|
| User is not authenticated | Deny protected upload or access. |
| User lacks role/scope permission | Deny without exposing private file details. |
| File belongs to another Teacher Workspace | Deny access, upload association, Archive, restore, replacement, or other operation. |
| Student submits for unassigned Homework | Deny the upload. |
| Parent attempts upload | Deny because Parent uploads are not permitted. |
| Homework file type is unsupported | Reject; Homework supports Text, Image, and PDF only. |
| Student Homework binary is not Image or PDF | Reject the submission file. |
| Video homework is attempted | Reject because video homework is out of scope. |
| Lesson file is requested outside authorized relationship | Deny without exposing Lesson/private storage details. |
| Owning record/file reference is archived or inactive for active use | Reject active operation unless restoration/authorized historical access applies. |
| File data/reference is unavailable | Provide safe unavailable/error result without implying a public fallback. |
| File size is excessive | Apply only an approved future size limit; do not invent a Version 1 limit. |

Errors must not expose raw storage paths, file-system implementation, credentials, internal media protection details, private Lesson information, unlinked Student files, or another Teacher Workspace’s file existence.

---

# 22. Edge Cases

The File Storage architecture must safely handle these confirmed or directly required cases:

1. A new Teacher Workspace has no file references, Lessons, Homework attachments, or Student submissions.
2. A Teacher uploads or manages an authorized Homework Image/PDF attachment in the Teacher’s own Teacher Workspace.
3. A Student submits an Image or PDF for Homework assigned through one Teacher relationship while studying with multiple Teachers; the file remains in the correct Teacher context.
4. A Student attempts to submit a video as Homework; the Platform rejects it.
5. A Parent attempts to upload a file; the Platform denies it.
6. A Student attempts to access a Lesson from a Teacher who is not the Student’s Teacher; the Platform denies it.
7. A Teacher attempts to access another Teacher’s Lesson, Homework file, Student submission, or file reference; the Platform denies it.
8. A Student moves Groups after Homework, Lesson, or submission history exists; historical file references remain associated with the relevant history.
9. A Homework, Lesson, file reference, Group, or related record is archived; it remains historically retained and is not shown as active content.
10. A file reference is requested using a known path or identifier without valid authorization; the Platform denies it.
11. Lesson video hosting/protection details are unresolved; the Platform does not imply public streaming/download behavior.
12. A future storage migration is considered; it must preserve ownership, access control, and historical references rather than redesigning the product boundary.

---

# Consistency Review

A consistency review was performed before saving this document.

| Review Area | Result |
|---|---|
| Official source alignment | Passed — `00_Project_Context.md` is treated as the final Version 1 authority. |
| Requested section coverage | Passed — all 22 requested File Storage sections are present. |
| Storage baseline | Passed — Laravel Public Storage and cPanel Shared Hosting compatibility are preserved; S3 Storage is not required for Version 1. |
| Supported file scope | Passed — Lesson videos, Homework Text/Image/PDF contexts, and Student Image/PDF Homework submission are preserved; video homework and Parent uploads are denied. |
| Unconfirmed file features | Passed — profile images, exact formats, naming, size limits, download, replacement, orphan cleanup, backup mechanics, and Lesson hosting/protection details are not invented. |
| Access control | Passed — backend authorization, Teacher Workspace ownership, Student relationship, Parent linked-Student read-only scope, Archive state, and Super Admin visibility constraints are preserved. |
| Historical integrity | Passed — file references remain tied to historical records; Archive replaces permanent deletion and archived references are not treated as active. |
| Future cloud support | Passed — future storage changes are constrained to preserve logical ownership, private access, Teacher Workspace isolation, and historical references without requiring a product redesign. |
| Scope | Passed — no source code, APIs, database tables, UI implementation, storage configuration, or provider-specific implementation detail is defined. |
| Terminology | Passed — Teacher Workspace, Lesson, Homework, Student, Parent, Archive, Audit Log, Subscription, payment status, Flow A, and Flow B are used consistently. |
