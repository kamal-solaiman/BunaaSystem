# 1. Executive Summary

The Unified Education Platform is a SaaS educational platform created to reduce the fragmentation experienced by Students, Parents, and Teachers when each Teacher operates on a separate system. In the current learning environment, one Student may study with several Teachers while being forced to manage different accounts, different attendance methods, different homework sources, different exam systems, and different payment-status records. Parents face the same fragmentation when trying to monitor their children across multiple Teachers. Teachers also need a dedicated place to manage their own educational operations without exposing their data or content to other Teachers.

The platform addresses this problem through a single shared web application where Students and Parents use one account, while each Teacher operates inside a completely isolated Teacher Workspace. A Student may study with multiple Teachers from one global account, but attendance, homework, exams, Lessons, and Subscription-related status remain separated per Teacher. A Parent may use one account to monitor multiple linked Students, with read-only access to the information relevant to those Students. Each Teacher uses the platform as an independent workspace for Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Teacher Staff, and Settings.

The business model is based on monthly Subscriptions paid by Teachers to the Platform. The Teacher's monthly Subscription is calculated from Billable Students, where a Student becomes Billable only when enrolled in a Teacher's Group for more than 15 calendar days during the calendar-month billing cycle. Attendance and login activity are not used in this calculation. Student fees owed to Teachers are a separate money flow based on each Group's Price and Pricing Type. Version 1 records payment status only; all actual payments are handled outside the platform.

Version 1 is intentionally focused. It is a web application only, not a native mobile application. It does not process online payments, does not include push, email, or SMS notifications, does not support multiple Teaching Subjects under one Teacher account, and is not a marketplace. Teachers do not sell courses through the platform, and there is no course discovery across Teachers. This focus protects the core value proposition: one account for Students and Parents, many isolated Teacher Workspaces, and a structured operational system for private teacher-led education.

The platform's long-term direction is to become the central operating layer for teacher-based education while preserving the core principles established for Version 1: Teacher Workspace isolation, single Student and Parent accounts, private Teacher-owned content, clear separation between Platform Subscriptions and Student fees, historical integrity, and business-rule consistency.

---

# 2. Vision Statement

The vision of the Unified Education Platform is to become the trusted unified environment where Students, Parents, and Teachers can participate in teacher-based education without the burden of fragmented systems, duplicated identities, and disconnected learning records.

The platform aims to make the educational experience simpler for Students by giving each Student one account that can be used across multiple Teachers. Instead of treating every Teacher relationship as a separate digital world, the platform allows the Student to maintain one identity while still keeping every Teacher's data separate. This creates a more coherent educational experience without compromising Teacher privacy or ownership.

For Parents, the vision is to provide one reliable place to monitor linked Students. A Parent should not need multiple accounts or separate systems to understand homework, attendance, exams, Teachers, and payment status for their children. The platform supports the Parent's role as a read-only observer who can follow progress and obligations without interfering with Teacher operations.

For Teachers, the vision is to offer an independent professional workspace that feels dedicated to the Teacher while still benefiting from a shared platform. Each Teacher Workspace must remain completely isolated. Teachers should be able to manage their educational structure, Students, Attendance, Homework, Exams, Lessons, Reports, Teacher Staff, and Settings without their data being visible to or mixed with another Teacher's data.

At the Platform level, the vision is to create a sustainable SaaS business built around monthly Teacher Subscriptions. The Platform provides operational value to Teachers and convenience to Students and Parents, while maintaining a clear and fair Subscription model based on Billable Students.

The long-term vision is not to become an online course marketplace. The platform is not designed for Teachers to sell courses to unknown Students, and it does not support course discovery across Teachers. Its purpose is to strengthen the private relationship between each Teacher and their own Students while reducing account and platform fragmentation for Students and Parents.

---

# 3. Mission Statement

The mission of the Unified Education Platform is to simplify teacher-based education by providing one web application where Teachers can manage their own isolated Teacher Workspaces, Students can study with multiple Teachers through one account, and Parents can monitor linked Students through one read-only account.

The platform exists to solve practical educational management problems. It brings together attendance, homework, exams, Lessons, Student fee status, schedules, and reporting into a unified experience while respecting the boundaries between Teachers. The mission is not only to digitize isolated tasks, but to organize the relationship between Teacher, Student, and Parent in a way that is easier to use, easier to track, and easier to manage.

For Teachers, the mission is to provide a structured operational system that supports Groups, Educational Grades, Student management, Attendance, Homework, Exams, Lessons, Reports, Teacher Staff, and Settings inside a private Teacher Workspace. The Teacher should be able to run educational operations professionally without building or maintaining a separate platform.

For Students, the mission is to reduce duplicated accounts and scattered educational records. A Student should be able to access different Teachers from one account while keeping each Teacher's attendance, homework, exams, Lessons, and Subscription-related information properly separated.

For Parents, the mission is to create a single monitoring point for linked Students. The Parent should have clear read-only access to the information needed to follow Student progress and obligations across Teachers.

For the Platform owner, the mission is to operate a reliable SaaS product with a clear monthly Subscription model paid by Teachers, while preserving the business rules, boundaries, and terminology defined in the Project Context.

---

# 4. Problem Statement

Teacher-based education often requires a Student to study with several different Teachers at the same time. When each Teacher uses a different platform, the Student's educational life becomes fragmented. This fragmentation creates unnecessary complexity for Students, Parents, and Teachers.

Students are forced to maintain different login credentials across different platforms. They may need to remember separate accounts for each Teacher, each with its own access method, schedule, homework area, exam system, and attendance process. This makes the learning experience harder than it needs to be. The Student's attention is divided between learning and managing platform differences.

Attendance is also fragmented. One Teacher may use one QR code or attendance method, while another Teacher uses a different system. A Student studying with several Teachers may need to follow different attendance procedures and keep track of different QR codes or attendance expectations. This increases confusion and weakens consistency.

Homework is scattered across platforms. A Student may have assignments from multiple Teachers, but those assignments are not visible together through one account. This makes it harder to know what work is due, what has been completed, and which Teacher assigned which task. Parents face the same problem when trying to help their children stay organized.

Exams and exam results are also disconnected. Students may complete exams through different systems, and Parents may need to check multiple platforms to understand performance. This scattered view prevents a clear picture of the Student's learning status across Teachers.

Parents experience a broader version of the same issue. A Parent may need to monitor more than one Student, and each Student may study with more than one Teacher. Without a unified account model, the Parent is forced into a complex network of accounts, platforms, passwords, and inconsistent reporting experiences. This makes parental monitoring time-consuming and unreliable.

Teachers face a different but related challenge. They need a platform that supports their own educational operations, but they must also protect the privacy of their Students, Lessons, Question Bank, attendance records, homework, exams, and financial tracking. A shared platform must not mean shared Teacher data. Teachers need the benefits of a common system without losing the feeling and reality of a private workspace.

The financial picture can also become confused if money flows are not clearly separated. The Platform Subscription paid by Teachers is different from Student fees owed to Teachers. If these flows are mixed, reporting and accountability become unclear. Version 1 must maintain a clear distinction: Flow A is the Teacher's monthly Subscription to the Platform, while Flow B is Student or Parent fees owed to the Teacher based on Group pricing.

These problems need to be solved because fragmented systems reduce trust, increase administrative overhead, and weaken the educational experience. Students need simplicity. Parents need visibility. Teachers need operational control and privacy. The Platform needs a sustainable business model that supports all of these needs without turning the product into a marketplace or expanding beyond confirmed Version 1 scope.

---

# 5. Proposed Solution

The Unified Education Platform solves the fragmentation problem by creating one web application that serves Students, Parents, Teachers, Teacher Staff, and the Super Admin while preserving strict boundaries between Teacher Workspaces.

The central product idea is simple: one account per Student, one account per Parent, and many isolated Teacher Workspaces. A Student can study with multiple Teachers through one global account. The Student's attendance, homework, exams, Lessons, and Subscription-related information are separated per Teacher, so the Student gets one login without mixing Teacher data. This directly addresses the problem of duplicated Student accounts and scattered learning records.

Parents also use one account. A Parent may monitor multiple linked Students, and Version 1 supports exactly one Parent account per Student. The Parent's access is read-only everywhere, and the Parent sees only linked Students. This gives Parents a consolidated monitoring experience without giving them operational control over Teacher or Student records.

Teachers operate inside completely isolated Teacher Workspaces. Each Teacher Workspace represents one Teacher's private operational environment. A Teacher can manage Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Teacher Staff, and Settings inside that workspace. No Teacher can see another Teacher's data under any circumstance. This solves the privacy and ownership problem that would otherwise prevent Teachers from trusting a shared platform.

The platform supports each Teacher's academic structure. Educational Grades represent Teacher-created education levels. Groups belong to Educational Grades and carry Name, Schedule, Price, and Pricing Type. Each Teacher account represents exactly one Teaching Subject, selected during registration and not changeable after account creation. Teaching Subjects are independent from Educational Grades. If a Teacher wants to teach another subject, a separate Teacher account is required.

Attendance is addressed through three confirmed methods. A dynamic QR Code is generated daily and scanned by the Student through the web application. A printed ID Card can be scanned by a QR scanner. Manual attendance entry is also available to the Teacher. These methods support different operational scenarios while keeping attendance records inside the Teacher Workspace.

Homework is supported in Version 1 using Text, Image, and PDF formats only. Video homework is not supported in Version 1. This keeps the Homework scope clear and aligned with the confirmed business rules.

Exams are built from each Teacher's private Question Bank. Supported question types are Multiple Choice, True/False, Essay, and Bubble Sheet. Bubble Sheet is an electronic exam format that simulates traditional paper bubble sheets, with Students selecting bubbles on screen and automatic grading supported. Exams, attempts, and grades are workspace-scoped and remain separated per Teacher.

Lessons are Teacher-owned and private. A Teacher may upload lesson videos exclusively for their own Students. There is no cross-Teacher access to Lessons, and one Teacher's content cannot reach another Teacher's Students through discovery or marketplace behavior.

Financial handling is structured around two separate money flows. Flow A is the Platform Subscription paid by the Teacher to the Platform. It is calculated monthly based on Billable Students and price per Student. Flow B is Student fees owed to the Teacher, derived from the Group's Price and Pricing Type. Version 1 records payment status for both flows but does not process actual transactions. Payments are handled outside the platform.

The solution is intentionally not a marketplace. There is no course browsing across Teachers, no public course discovery, and no mechanism by which Teachers sell courses through the platform. The platform is designed to support existing Teacher-Student relationships and make them easier to manage.

---

# 6. Target Users

## Teachers

Teachers are the primary paying customers of the platform. They subscribe monthly to use the Platform, with pricing based on the number of Billable Students according to the confirmed billing rule. Teachers receive a private Teacher Workspace that enables them to manage their educational operations in one place.

The value delivered to Teachers is operational control. A Teacher can organize Educational Grades and Groups, manage Students, record Attendance, create Homework, build Exams from a private Question Bank, upload private Lessons, review Reports, create Teacher Staff users, and manage workspace Settings. These capabilities support the Teacher's day-to-day educational work without requiring the Teacher to build or operate a separate platform.

Teachers also gain privacy and separation. Each Teacher Workspace is completely isolated from every other Teacher Workspace. This is essential because Teachers own their Student relationships, Lessons, Question Bank, exam records, attendance records, homework, and reports. The platform gives Teachers the benefits of shared infrastructure while preserving the experience of a dedicated workspace.

Teachers benefit from the single Student account model because a Student can be assigned or registered without creating duplicate identities. The Student may study with multiple Teachers, but the Teacher still sees and manages only the records relevant to that Teacher Workspace. This supports better organization while protecting boundaries.

The platform also supports Teacher delegation through Teacher Staff. A Teacher may create internal users such as Secretary, Assistant, or Accountant. These users exist only within that Teacher Workspace and hold only permissions assigned by the Teacher. This helps Teachers distribute administrative responsibilities while maintaining workspace control.

## Students

Students are the central learning participants. The platform delivers value to Students by reducing duplicated accounts and scattered educational access. Each Student has one global account and may study with multiple Teachers through that account.

The Student's experience is unified but not mixed. Attendance, homework, exams, Lessons, and Subscription-related status are separated per Teacher. This means the Student can access multiple Teachers from one account while each Teacher's information remains properly partitioned. The Student no longer needs a separate platform identity for every Teacher.

Students benefit from having their learning responsibilities organized across Teachers. They can access homework, exams, Lessons, schedules, and per-Teacher Subscription-related status from one account context. The result is less confusion and a clearer relationship with each Teacher.

The platform also protects Student history. When a Student moves between Groups under the same Teacher, historical attendance, homework, exams, and grades are preserved. History is never moved, deleted, or rewritten by structural changes. This protects the integrity of the Student's educational record.

Students may enter the platform through two confirmed registration methods. A Student may register their own account, or a Teacher may create the Student account manually. If the Teacher creates the account, the Student can later activate and use the same account. Duplicate Student accounts are not allowed.

## Parents

Parents use the platform to monitor linked Students. The Parent role is valuable because it reduces the burden of checking multiple systems to understand a Student's progress, obligations, and payment status.

A Parent has one account and may monitor multiple Students. Version 1 supports exactly one Parent account per Student, and the Parent can switch between linked Students through the Student Switcher. The Parent's access is read-only everywhere and limited to linked Students only.

The platform provides Parents with visibility into important educational areas such as homework, attendance, exams, Teachers, and payments related to Flow B. This supports the Parent's need to follow Student performance and obligations without interfering with Teacher operations.

Parents benefit from the platform's multi-Teacher Student model. If a Student studies with several Teachers, the Parent can monitor the relevant Teacher-related information through one account rather than maintaining separate access to each Teacher's independent platform.

The Parent role strengthens trust between Teachers and families. Teachers remain in control of their own Teacher Workspace, while Parents receive appropriate visibility into linked Students. This balance supports communication and accountability without changing Teacher ownership.

## Platform Administrator

The Platform Administrator corresponds to the Super Admin role defined in the Project Context. The Super Admin owns the Platform at the platform level and does not operate inside Teacher Workspaces.

The value delivered to the Super Admin is business control. The Super Admin manages Teachers, Platform Subscriptions, pricing, and platform settings. The Super Admin also views global reports within the platform-level scope.

The Super Admin is responsible for the Platform's commercial model. Pricing is owned by the Super Admin, and the monthly Subscription model depends on Billable Students. Historical invoices keep the price as of their period. The open question of flat price versus volume tiers remains outside this Vision document as a pending pricing-detail decision, with pricing ownership remaining with the Super Admin.

The Super Admin also benefits from clear separation between business flows. Flow A is the Teacher's monthly Platform Subscription. Flow B is Student or Parent fees owed to the Teacher. The Platform Administrator must manage the SaaS business without conflating these two flows.

The Platform Administrator's role supports scale while respecting Teacher Workspace isolation. The Super Admin manages the Platform as a business and service, not as an operator inside each Teacher's private workspace.

---

# 7. Business Objectives

The first business objective is to establish the Unified Education Platform as a subscription-based SaaS product for Teachers. Teachers pay monthly for access to the Platform, and the monthly Subscription is calculated using the confirmed Billable Student rule. This objective creates a recurring revenue model connected to actual Teacher usage through enrolled Students.

The second objective is to reduce educational platform fragmentation for Students and Parents. The platform should make it possible for a Student studying with multiple Teachers to use one account, and for a Parent monitoring multiple Students to use one account. This creates practical value beyond individual Teacher operations and strengthens the platform's role as a unified educational layer.

The third objective is to provide Teachers with a complete operational workspace for Version 1. The Teacher Workspace must support the confirmed modules: Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Users, and Settings. The business goal is to make the platform useful enough for Teachers to adopt as their main operational system.

The fourth objective is to preserve trust through strict Teacher Workspace isolation. Teachers will only adopt a shared platform if they trust that their data, Students, Lessons, Question Bank, reports, and financial records are not visible to other Teachers. This privacy boundary is not only a technical principle; it is a core business requirement.

The fifth objective is to support transparent educational monitoring for Parents. Parents should be able to understand homework, attendance, exams, Teachers, and payment status for linked Students through read-only access. This increases the platform's value to families and improves the Teacher-Parent relationship.

The sixth objective is to maintain financial clarity. The Platform must clearly separate Teacher Subscriptions from Student fees owed to Teachers. Version 1 records payment status only and does not process payments. This avoids operational complexity while still giving Teachers, Parents, and the Super Admin useful financial tracking.

The seventh objective is to protect historical integrity. The platform uses Archive instead of permanent deletion, keeps historical data available, preserves Student transfer history, and records important actions in the Audit Log. These rules support accountability, reporting, and trust.

The eighth objective is to keep Version 1 focused. The Platform must not expand into unrelated areas such as native mobile applications, online payment gateways, notifications, or marketplace behavior during Version 1. A controlled scope improves delivery discipline and reduces the risk of building features that contradict the Project Context.

---

# 8. Product Scope

Version 1 includes the core business capabilities required to operate the Unified Education Platform as a web application.

Version 1 includes one global Student account model. A Student has exactly one account and may study with multiple Teachers. The Student's attendance, homework, exams, Lessons, and Subscription-related status are separated per Teacher. The platform supports Student self-registration and Teacher-created Student accounts, while preventing duplicate Student accounts.

Version 1 includes one Parent account model. A Parent has one account and may monitor multiple linked Students. Version 1 supports exactly one Parent account per Student. Parent access is read-only everywhere and limited to linked Students. The Parent Panel includes a Student Switcher for switching between linked Students.

Version 1 includes isolated Teacher Workspaces. Each Teacher operates one completely isolated Teacher Workspace. No Teacher can access another Teacher's data under any circumstance. The Teacher Workspace includes management of Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Users, and Settings.

Version 1 includes Educational Grades and Groups. Teachers create Educational Grades as education levels. Each Group belongs to one Educational Grade and carries Name, Schedule, Price, and Pricing Type. Pricing Type is either Monthly or Per Lesson.

Version 1 includes the confirmed Teaching Subject rule. Each Teacher account represents exactly one Teaching Subject. The Teaching Subject is selected during registration and cannot be changed after account creation. If a Teacher wants to teach another subject, a separate Teacher account must be created. Teaching Subjects are independent from Educational Grades.

Version 1 includes Student enrollment behavior. A Student belongs to only one Group per Teacher at any time. If a Student moves between Groups, historical attendance, homework, exams, and grades are preserved. History is not moved, deleted, or rewritten by structural changes.

Version 1 includes Attendance using three methods: daily generated Dynamic QR Code scanned by the Student through the web application, printed ID Card scanned by a QR scanner, and manual entry by the Teacher.

Version 1 includes Homework with Text, Image, and PDF support only. Video homework is not included.

Version 1 includes Exams built from the Teacher's private Question Bank. Supported question types are Multiple Choice, True/False, Essay, and Bubble Sheet. Bubble Sheet is an electronic on-screen exam format that simulates traditional paper bubble sheets and supports automatic grading.

Version 1 includes private Lessons. Lesson videos are uploaded by a Teacher exclusively for that Teacher's own Students. Lessons are Teacher-owned and private, with no cross-Teacher access.

Version 1 includes Reports for attendance, homework, exam results, payments, and Student performance. These reports support Teacher operations and preserve historical context according to the Archive and history rules.

Version 1 includes Teacher Staff users. Teacher Staff are created by the Teacher, exist only inside that Teacher Workspace, and hold only permissions assigned by the Teacher. Examples include Secretary, Assistant, and Accountant.

Version 1 includes the Super Admin role. The Super Admin manages Teachers, Subscriptions, pricing, platform settings, and global reports at the platform level.

Version 1 includes the monthly Teacher Subscription model. The billing cycle starts on the first day of every calendar month and ends on the last day of the same month. A new billing cycle begins automatically on the first day of the next month. A Student is a Billable Student if enrolled in a Teacher's Group for more than 15 calendar days during the billing cycle. Attendance and login activity are not used for this calculation.

Version 1 includes Student fee tracking based on Group pricing. Student fees owed to a Teacher derive from Group enrollment, Price, and Pricing Type. The platform records payment status only.

Version 1 includes Archive instead of permanent deletion. No hard delete exists anywhere in the system. Archived records remain available in reports and can be restored by authorized users.

Version 1 includes the Audit Log as a first-class subsystem. Important actions such as create, update, archive, restore, login, permission change, attendance change, exam modification, homework modification, and Subscription change must be recorded.

---

# 9. Out of Scope

The following items are intentionally excluded from Version 1 according to the Project Context.

## Native mobile applications

Version 1 is a web application only. No native mobile application is included in Version 1. All Version 1 capabilities, including daily Dynamic QR Code attendance scanning, are delivered through the web application.

## Online payment gateways

Version 1 does not process online payments. Teacher Subscription payments and Student fee payments are handled outside the platform. The platform records payment status only. Future payment gateway integration may be considered separately, but it is not part of Version 1.

## Notifications

Push notifications, email notifications, and SMS notifications are out of scope for Version 1. The platform must not assume notification behavior as part of Version 1 business scope.

## Multiple Teaching Subjects per Teacher account

Version 1 supports exactly one Teaching Subject per Teacher account. The Teaching Subject is selected during Teacher registration and cannot be changed after account creation. A Teacher who wants to teach another subject must create a separate Teacher account.

## Marketplace behavior

The platform is not an online course marketplace. Teachers do not sell courses through the platform. There is no course discovery or browsing across Teachers, and there is no mechanism by which one Teacher's content reaches another Teacher's Students.

## Video homework

Homework in Version 1 supports Text, Image, and PDF only. Video homework is not supported in Version 1.

## Multiple Parent accounts per Student

Version 1 supports exactly one Parent account per Student. One Parent account may monitor multiple Students, but a Student cannot have multiple Parent accounts linked simultaneously in Version 1.

## In-platform payment transactions for Student fees

Student fees owed to Teachers are tracked through payment status records derived from Group pricing, but actual Student or Parent payments to Teachers are handled outside the platform in Version 1.

## In-platform payment transactions for Teacher Subscriptions

Teacher Subscription payments to the Platform are also handled outside the platform in Version 1. The Platform records status but does not process the transaction.

---

# 10. Competitive Advantages

The platform's first competitive advantage is the single account model for Students and Parents. A Student studying with multiple Teachers can use one account instead of separate accounts on different Teacher platforms. A Parent can use one account to monitor multiple linked Students. This directly addresses the confirmed problem of duplicated accounts and scattered records.

The second advantage is the combination of unification and Teacher isolation. Many shared systems create concern that Teacher data may be mixed, exposed, or treated as part of a marketplace. The Unified Education Platform is designed around isolated Teacher Workspaces. Each Teacher experiences the platform as a private operational environment, while Students and Parents benefit from unified identity.

The third advantage is that the platform is built for teacher-based education rather than course selling. It does not position Teachers as sellers in a marketplace and does not expose Teacher content to course discovery. This supports Teachers who want to manage their own Students privately rather than compete in a public marketplace.

The fourth advantage is clear support for day-to-day Teacher operations. Version 1 includes Educational Grades, Groups, Students, Attendance, Homework, Exams, Lessons, Reports, Teacher Staff, and Settings. These areas reflect the actual operational needs described in the Project Context and help Teachers manage education beyond simple content delivery.

The fifth advantage is flexible attendance support within confirmed scope. Teachers can use a daily Dynamic QR Code, printed ID Card scanning, or manual attendance entry. This allows Teachers to handle attendance in different classroom situations without leaving the platform's attendance structure.

The sixth advantage is the private Teacher-owned Question Bank and exam model. Teachers can create Exams from their own private Question Bank using Multiple Choice, True/False, Essay, and Bubble Sheet question types. Bubble Sheet supports an electronic version of a familiar paper-based examination pattern.

The seventh advantage is clear financial separation. The platform distinguishes between the Teacher's Subscription to the Platform and Student fees owed to the Teacher. This helps avoid confusion and supports separate reporting and status tracking for the two money flows.

The eighth advantage is historical integrity. The platform preserves Student transfer history, avoids permanent deletion by using Archive, keeps historical data available for reports, and records important actions in the Audit Log. This supports accountability and continuity.

The ninth advantage is controlled Version 1 scope. By excluding native mobile applications, online payment gateways, notifications, marketplace behavior, and multiple Teaching Subjects per Teacher account from Version 1, the platform can focus on delivering its core value without unnecessary complexity.

These advantages are not based on unsupported marketing claims. They are based on the confirmed business rules and product boundaries in the Project Context.

---

# 11. Success Metrics

Success should be measured through indicators that reflect Teacher adoption, Student usage, Parent visibility, operational activity, and financial clarity.

## Active Teachers

The number of active Teachers is a primary indicator of SaaS adoption. Since Teachers are the paying customers, growth in active Teacher Workspaces shows that the platform is delivering enough operational value for Teachers to subscribe and continue using it.

## Active Students

The number of active Students shows whether Teachers are bringing their Students onto the platform and whether the single Student account model is being used. This metric should distinguish Students from Billable Students because billing depends on enrollment duration, not simply account existence or login activity.

## Billable Students

The number of Billable Students is a key business metric for the Platform Subscription model. A Student is Billable only when enrolled in a Teacher's Group for more than 15 calendar days during the billing cycle. This metric directly connects product usage to Flow A revenue.

## Monthly Teacher Retention

Teacher retention measures whether Teachers continue using and paying for the platform month after month. Retention is important because the business model is Subscription-based and depends on ongoing Teacher value.

## Student Account Consolidation

A meaningful success indicator is the number of Students studying with more than one Teacher through one account. This shows whether the platform is solving the core fragmentation problem rather than simply replacing individual Teacher systems one by one.

## Parent Account Usage

Parent usage can be measured by the number of Parents actively linked to Students and the number of Parents monitoring multiple Students. This indicates whether the platform is delivering value to families and supporting the Parent monitoring use case.

## Attendance Usage

Attendance usage measures how often Teachers record attendance through the platform and how often the confirmed attendance methods are used. High attendance usage indicates that Teachers rely on the platform for regular classroom operations.

## Homework Activity

Homework activity can be measured through the number of homework assignments created, submitted, reviewed, or graded within the supported formats of Text, Image, and PDF. This indicates whether the Homework module is part of ongoing educational practice.

## Exam Completion Rate

Exam completion rate measures the percentage of assigned Exams that Students complete. This shows whether the exam workflow is being used successfully by Teachers and Students.

## Question Bank Usage

Question Bank usage can be measured by the number of private questions created and reused by Teachers. This helps show whether Teachers are investing educational content into the platform.

## Lesson Usage

Lesson usage can be measured through the number of private Lessons uploaded by Teachers and accessed by their own Students. This indicates whether the Lesson capability is valuable within Teacher Workspaces.

## Payment Status Tracking

Payment status tracking can be measured through the number of Teacher Subscription statuses and Student fee statuses recorded. Since Version 1 does not process payments, success is measured by status-tracking adoption rather than transaction volume.

## Report Usage

Report usage measures how often Teachers use attendance, homework, exam result, payment, and Student performance reports. This indicates whether the platform is supporting decision-making and operational review.

## Archive and Audit Log Coverage

Because Archive and Audit Log are core business rules, success should include evidence that important actions are recorded and that historical records remain available. This supports trust, accountability, and business continuity.

---

# 12. Risks

The first business risk is Teacher adoption risk. Teachers are the paying customers, and they must see enough value in the Teacher Workspace to pay a monthly Subscription. If the operational benefits are not clear, Teachers may continue using existing fragmented tools.

The second risk is complexity of multi-Teacher Student relationships. The platform's core value depends on allowing one Student account to study with multiple Teachers while keeping each Teacher's data separated. If this is not clearly understood by users, it may create confusion during adoption.

The third risk is trust around Teacher data isolation. Teachers may be concerned that their Students, Lessons, Question Bank, reports, or financial records could be visible to other Teachers. Any perceived weakness in isolation would damage confidence in the platform.

The fourth risk is scope expansion. Adding features outside Version 1, such as native mobile applications, payment gateways, notifications, marketplace behavior, or multiple Teaching Subjects under one Teacher account, could delay delivery and contradict the confirmed scope.

The fifth risk is confusion between Flow A and Flow B. If Teacher Subscription payments and Student fees owed to Teachers are not clearly separated in product language and business operations, users may misunderstand who pays whom and what the platform is responsible for.

The sixth risk is payment-status dependency. Since Version 1 records payment status but does not process actual payments, the accuracy of payment records depends on users or administrators updating status correctly.

The seventh risk is Parent expectation management. Parents may expect more than read-only monitoring or may expect multiple Parent accounts per Student. Version 1 must communicate clearly that Parent access is read-only and that exactly one Parent account is supported per Student.

The eighth risk is Teacher Staff permission expectations. Teacher Staff permission granularity remains pending in the Project Context. Until it is fully specified in the appropriate document, the business must avoid promising unsupported permission behavior.

The ninth risk is pending pricing structure. Pricing is owned by the Super Admin, but the decision between flat price and volume tiers remains pending. This could affect launch planning, commercial messaging, and billing operations.

The tenth risk is localization and regional configuration uncertainty. Arabic (default) and English (fully supported) are confirmed; timezone and currency remain pending. These decisions may influence business readiness and market fit, but they must not be silently assumed.

The eleventh risk is historical-data discipline. The platform requires Archive instead of permanent deletion and requires historical data to remain available. If users expect deletion or if operations fail to preserve history, trust and reporting accuracy may be affected.

---

# 13. Assumptions

Version 1 assumes that Teachers are willing to pay monthly for a platform that helps them manage their educational operations and improve the Student and Parent experience.

Version 1 assumes that the core business pain is fragmentation across multiple Teacher platforms, especially duplicated Student accounts, duplicated Parent access, scattered attendance systems, scattered homework, scattered exams, and lack of a unified learning picture.

Version 1 assumes that a web application is sufficient for the initial release. All Version 1 capabilities, including daily Dynamic QR Code scanning for attendance, are delivered through the web application.

Version 1 assumes that Teachers accept one Teaching Subject per Teacher account. If a Teacher teaches another subject, the Teacher must create a separate Teacher account.

Version 1 assumes that actual payments can continue outside the platform at launch. The platform records payment status for Teacher Subscriptions and Student fees, but it does not process transactions.

Version 1 assumes that Students and Parents benefit from one-account access even though Teacher data remains separated by Teacher Workspace.

Version 1 assumes that each Student can have exactly one Parent account linked at the same time, while one Parent account may monitor multiple Students.

Version 1 assumes that the supported Homework formats of Text, Image, and PDF are sufficient for launch. Video homework is intentionally excluded.

Version 1 assumes that Teachers value private Lesson videos for their own Students, without marketplace discovery and without cross-Teacher access.

Version 1 assumes that the confirmed Attendance methods are enough for initial classroom operations: daily Dynamic QR Code, printed ID Card scanning, and manual entry.

Version 1 assumes that the Question Bank and Exam capabilities, including Multiple Choice, True/False, Essay, and Bubble Sheet, are sufficient for the first version of exam management.

Version 1 assumes that historical records must remain available and that users can adapt to Archive replacing permanent deletion.

Version 1 assumes that pending topics in the Project Context, such as Teacher Staff permission granularity, pricing model detail, non-payment enforcement, content visibility boundaries, video hosting/protection details, and localization/regional settings, will be resolved in the appropriate future documentation or decisions without contradicting the frozen Project Context.

---

# 14. Constraints

The first constraint is that the Project Context is the official Single Source of Truth for Version 1. All business documents, technical documents, design decisions, and implementation work must remain consistent with it. No document may introduce a feature or rule that contradicts it.

The second constraint is Version 1 scope. The product is a web application only. Native mobile applications are not included in Version 1.

The third constraint is the commercial model. Teachers pay monthly Subscriptions to the Platform. The Subscription is based on Billable Students and price per Student. A Student is Billable only when enrolled in a Teacher's Group for more than 15 calendar days during the billing cycle. Attendance and login activity are not used in this calculation.

The fourth constraint is payment handling. Version 1 records payment status only. Teacher Subscription payments and Student fee payments are handled outside the platform. Online payment gateways are out of scope.

The fifth constraint is Teacher Workspace isolation. Each Teacher Workspace is completely isolated. Teachers cannot see each other's data under any circumstance. This boundary must not be weakened by future documents or product decisions.

The sixth constraint is Student identity. A Student has exactly one global account and may study with multiple Teachers. Duplicate Student accounts are not allowed.

The seventh constraint is Parent relationship scope. Version 1 supports exactly one Parent account per Student, and Parent access is read-only everywhere. A Parent sees only linked Students.

The eighth constraint is academic structure. A Student belongs to only one Group per Teacher at any time. Group moves preserve history. Each Teacher account represents exactly one Teaching Subject, selected during registration and not changeable after account creation.

The ninth constraint is content ownership. Lesson videos are Teacher-owned and private. The Question Bank is Teacher-owned and private. There is no cross-Teacher access to Teacher content.

The tenth constraint is marketplace exclusion. The Platform is not an online course marketplace. Teachers do not sell courses, and there is no course discovery or browsing across Teachers.

The eleventh constraint is the Archive policy. Permanent deletion is not allowed. Archive must be used instead, and historical data must remain available.

The twelfth constraint is the Audit Log policy. Important actions must be recorded, including create, update, archive, restore, login, permission change, attendance change, exam modification, homework modification, and Subscription change.

The thirteenth constraint is confirmed terminology. Terms such as Teacher Workspace, Educational Grade, Teaching Subject, Group, Student, Parent, Teacher Staff, Super Admin, Subscription, Flow A, Flow B, Enrollment, Archive, Audit Log, Dynamic QR Code, ID Card, Question Bank, Bubble Sheet, Student Switcher, Lesson, Billable Student, Billing Cycle, and Homework must be used consistently.

The fourteenth constraint is unresolved decisions. Pending questions must not be silently assumed. Areas such as non-payment enforcement, lesson video hosting/protection, Teacher Staff permission granularity, Super Admin content visibility, flat price versus volume tiers, and localization/regional settings must remain pending until formally resolved.

---

# 15. Future Vision

The long-term direction of the Unified Education Platform is to become the central operating environment for teacher-based education while protecting the principles established in Version 1.

The future vision begins with deeper adoption of the one-account model. Over time, the platform should make it increasingly natural for Students to use one account across all their Teachers and for Parents to monitor linked Students from one place. The platform's success depends on reducing fragmentation without removing the independence of each Teacher.

The platform should continue to strengthen Teacher Workspaces as private professional environments. Teachers should feel that the platform supports their own educational identity, Student relationships, Lessons, Question Bank, Groups, and reports. Future growth must preserve complete isolation between Teacher Workspaces.

Future phases may revisit capabilities that are explicitly out of scope for Version 1, such as native mobile applications, online payment gateway integration, notifications, and other separately approved enhancements. Any such future work must be documented separately and must not retroactively change Version 1 scope.

The future vision may also include more mature business operations around Subscriptions, pricing, reporting, and payment-status management, while maintaining the clear separation between Flow A and Flow B. The Platform Subscription paid by Teachers must remain distinct from Student fees owed to Teachers.

The platform may evolve to improve reporting and analytics around attendance, homework, exams, payment status, and Student performance. These improvements should help Teachers make better decisions and help Parents better understand linked Students, without changing the Parent's read-only boundary or exposing Teacher-private data across workspaces.

Future growth should continue to honor the Archive and Audit Log principles. As the platform becomes more important to educational operations, historical integrity and accountability become even more valuable. Records should remain reliable, traceable, and available according to the confirmed policies.

The future vision is not to become a marketplace. Even as the platform grows, it should not shift into public course discovery or course selling unless a future Product Owner decision explicitly creates a separate scope. The Version 1 identity of the product is a unified platform for existing teacher-led education, not a marketplace for courses.

The long-term product direction is therefore disciplined expansion: more value for Teachers, Students, Parents, and the Super Admin, while preserving the foundational business rules of single Student and Parent accounts, isolated Teacher Workspaces, private Teacher-owned content, clear financial flows, and consistency with the official Project Context.
