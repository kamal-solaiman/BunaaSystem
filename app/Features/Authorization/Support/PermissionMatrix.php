<?php

declare(strict_types=1);

namespace App\Features\Authorization\Support;

/**
 * The confirmed permission matrix — 09_Permission_Matrix.md.
 *
 * A transcription of the matrix, one entry per role-permission grant. Only
 * Allowed and Conditional entries appear: a permission absent from a role's
 * list is Denied, which is the deny-by-default principle (08 §2.1) expressed
 * as data rather than as a rule that could be forgotten.
 *
 * Teacher Staff is deliberately absent. 09 §5 of the Global Principles states
 * Teacher Staff "are not shown as a separate matrix column… Teacher Staff
 * access is always Conditional on Teacher-assigned permissions inside the
 * creating Teacher Workspace." Staff access is resolved as an intersection in
 * PermissionResolver, never as a matrix row.
 *
 * Frozen by PERMISSION_REGISTRY.md: names and scopes are immutable.
 */
final class PermissionMatrix
{
    public const ALLOWED = 'allowed';

    public const CONDITIONAL = 'conditional';

    /**
     * Super Admin — 26 allowed, 15 conditional.
     *
     * @var array<string, string>
     */
    private const SUPER_ADMIN = [
        'platform.dashboard.view' => self::ALLOWED,
        'platform.dashboard.view_teacher_private_content' => self::CONDITIONAL,
        'platform.student.view_private_workspace_records' => self::CONDITIONAL,
        'platform.attendance.view_report_summary' => self::CONDITIONAL,
        'platform.homework.view_private_content' => self::CONDITIONAL,
        'platform.lesson.view_private_content' => self::CONDITIONAL,
        'platform.question_bank.view_private_content' => self::CONDITIONAL,
        'platform.exam.view_private_definition' => self::CONDITIONAL,
        'platform.report.view' => self::ALLOWED,
        'platform.report.view_teacher_aggregate' => self::CONDITIONAL,
        'platform.payment_status.view' => self::ALLOWED,
        'platform.payment_status.record' => self::ALLOWED,
        'platform.payment_status.update' => self::ALLOWED,
        'platform.payment_status.view_history' => self::ALLOWED,
        'platform.subscription.view' => self::ALLOWED,
        'platform.subscription.calculate_billable_students' => self::ALLOWED,
        'platform.subscription.create_cycle' => self::ALLOWED,
        'platform.subscription.update_status' => self::ALLOWED,
        'platform.subscription.record_payment_status' => self::ALLOWED,
        'platform.subscription.view_history' => self::ALLOWED,
        'platform.subscription.enforce_non_payment' => self::CONDITIONAL,
        'platform.user.view' => self::ALLOWED,
        'platform.settings.view' => self::ALLOWED,
        'platform.settings.update' => self::ALLOWED,
        'platform.settings.update_pricing' => self::CONDITIONAL,
        'platform.settings.update_localization' => self::CONDITIONAL,
        'platform.file.view_private_teacher_file' => self::CONDITIONAL,
        'platform.audit_log.view' => self::ALLOWED,
        'platform.audit_log.view_teacher_workspace_events' => self::CONDITIONAL,
        'platform.audit_log.create' => self::CONDITIONAL,
        'platform.teacher.view' => self::ALLOWED,
        'platform.teacher.create' => self::ALLOWED,
        'platform.teacher.update' => self::ALLOWED,
        'platform.teacher.archive' => self::ALLOWED,
        'platform.teacher.restore' => self::ALLOWED,
        'platform.teacher.view_history' => self::ALLOWED,
        'platform.pricing.view' => self::ALLOWED,
        'platform.pricing.update' => self::CONDITIONAL,
        'platform.billing_cycle.view' => self::ALLOWED,
        'platform.billing_cycle.manage' => self::ALLOWED,
        'platform.global_report.view' => self::ALLOWED,
    ];

    /**
     * Teacher — 80 allowed, 7 conditional.
     *
     * @var array<string, string>
     */
    private const TEACHER = [
        'teacher_workspace.dashboard.view' => self::ALLOWED,
        'teacher_workspace.dashboard.view_history' => self::CONDITIONAL,
        'teacher_workspace.educational_grade.view' => self::ALLOWED,
        'teacher_workspace.educational_grade.create' => self::ALLOWED,
        'teacher_workspace.educational_grade.update' => self::ALLOWED,
        'teacher_workspace.educational_grade.archive' => self::ALLOWED,
        'teacher_workspace.educational_grade.restore' => self::ALLOWED,
        'teacher_workspace.educational_grade.view_history' => self::ALLOWED,
        'teacher_workspace.group.view' => self::ALLOWED,
        'teacher_workspace.group.create' => self::ALLOWED,
        'teacher_workspace.group.update' => self::ALLOWED,
        'teacher_workspace.group.archive' => self::ALLOWED,
        'teacher_workspace.group.restore' => self::ALLOWED,
        'teacher_workspace.group.view_history' => self::ALLOWED,
        'teacher_workspace.group.assign_student' => self::ALLOWED,
        'teacher_workspace.group.move_student' => self::ALLOWED,
        'teacher_workspace.student.view' => self::ALLOWED,
        'teacher_workspace.student.search' => self::ALLOWED,
        'teacher_workspace.student.create' => self::ALLOWED,
        'teacher_workspace.student.assign_existing' => self::ALLOWED,
        'teacher_workspace.student.update' => self::ALLOWED,
        'teacher_workspace.student.archive' => self::ALLOWED,
        'teacher_workspace.student.restore' => self::ALLOWED,
        'teacher_workspace.student.view_history' => self::ALLOWED,
        'teacher_workspace.attendance.view' => self::ALLOWED,
        'teacher_workspace.attendance.record' => self::ALLOWED,
        'teacher_workspace.attendance.update' => self::ALLOWED,
        'teacher_workspace.attendance.archive' => self::CONDITIONAL,
        'teacher_workspace.attendance.restore' => self::CONDITIONAL,
        'teacher_workspace.attendance.view_history' => self::ALLOWED,
        'teacher_workspace.homework.view' => self::ALLOWED,
        'teacher_workspace.homework.create' => self::ALLOWED,
        'teacher_workspace.homework.update' => self::ALLOWED,
        'teacher_workspace.homework.archive' => self::ALLOWED,
        'teacher_workspace.homework.restore' => self::ALLOWED,
        'teacher_workspace.homework.grade' => self::ALLOWED,
        'teacher_workspace.homework.view_submissions' => self::ALLOWED,
        'teacher_workspace.homework.view_history' => self::ALLOWED,
        'teacher_workspace.lesson.view' => self::ALLOWED,
        'teacher_workspace.lesson.create' => self::ALLOWED,
        'teacher_workspace.lesson.update' => self::ALLOWED,
        'teacher_workspace.lesson.archive' => self::ALLOWED,
        'teacher_workspace.lesson.restore' => self::ALLOWED,
        'teacher_workspace.lesson.upload_video' => self::ALLOWED,
        'teacher_workspace.lesson.view_history' => self::ALLOWED,
        'teacher_workspace.question_bank.view' => self::ALLOWED,
        'teacher_workspace.question_bank.create' => self::ALLOWED,
        'teacher_workspace.question_bank.update' => self::ALLOWED,
        'teacher_workspace.question_bank.archive' => self::ALLOWED,
        'teacher_workspace.question_bank.restore' => self::ALLOWED,
        'teacher_workspace.exam.view' => self::ALLOWED,
        'teacher_workspace.exam.create' => self::ALLOWED,
        'teacher_workspace.exam.update' => self::ALLOWED,
        'teacher_workspace.exam.archive' => self::ALLOWED,
        'teacher_workspace.exam.restore' => self::ALLOWED,
        'teacher_workspace.exam.publish' => self::CONDITIONAL,
        'teacher_workspace.exam.grade' => self::ALLOWED,
        'teacher_workspace.exam.view_attempts' => self::ALLOWED,
        'teacher_workspace.exam.view_history' => self::ALLOWED,
        'teacher_workspace.report.view' => self::ALLOWED,
        'teacher_workspace.report.view_attendance' => self::ALLOWED,
        'teacher_workspace.report.view_homework' => self::ALLOWED,
        'teacher_workspace.report.view_exam_results' => self::ALLOWED,
        'teacher_workspace.report.view_payments' => self::ALLOWED,
        'teacher_workspace.report.view_student_performance' => self::ALLOWED,
        'teacher_workspace.payment_status.view' => self::ALLOWED,
        'teacher_workspace.payment_status.record' => self::ALLOWED,
        'teacher_workspace.payment_status.update' => self::ALLOWED,
        'teacher_workspace.payment_status.view_history' => self::ALLOWED,
        'teacher_workspace.subscription.view_own_status' => self::CONDITIONAL,
        'teacher_workspace.teacher_staff.view' => self::ALLOWED,
        'teacher_workspace.teacher_staff.create' => self::ALLOWED,
        'teacher_workspace.teacher_staff.update' => self::ALLOWED,
        'teacher_workspace.teacher_staff.archive' => self::ALLOWED,
        'teacher_workspace.teacher_staff.restore' => self::ALLOWED,
        'teacher_workspace.teacher_staff.assign_permission' => self::ALLOWED,
        'teacher_workspace.teacher_staff.view_history' => self::ALLOWED,
        'teacher_workspace.settings.view' => self::ALLOWED,
        'teacher_workspace.settings.update' => self::ALLOWED,
        'teacher_workspace.file.view' => self::ALLOWED,
        'teacher_workspace.file.upload' => self::ALLOWED,
        'teacher_workspace.file.update' => self::ALLOWED,
        'teacher_workspace.file.archive' => self::ALLOWED,
        'teacher_workspace.file.restore' => self::ALLOWED,
        'teacher_workspace.file.view_history' => self::ALLOWED,
        'platform.audit_log.create' => self::CONDITIONAL,
        'teacher_workspace.audit_log.view' => self::CONDITIONAL,
    ];

    /**
     * Student — 6 allowed, 17 conditional.
     *
     * @var array<string, string>
     */
    private const STUDENT = [
        'student_account.dashboard.view' => self::ALLOWED,
        'student_account.educational_grade.view' => self::CONDITIONAL,
        'student_account.group.view' => self::CONDITIONAL,
        'student_account.student.view' => self::ALLOWED,
        'student_account.student.update' => self::CONDITIONAL,
        'student_account.student.activate' => self::CONDITIONAL,
        'student_account.attendance.view' => self::CONDITIONAL,
        'student_account.attendance.scan_dynamic_qr' => self::ALLOWED,
        'student_account.homework.view' => self::ALLOWED,
        'student_account.homework.submit' => self::CONDITIONAL,
        'student_account.homework.update_submission' => self::CONDITIONAL,
        'student_account.lesson.view' => self::CONDITIONAL,
        'student_account.exam.view' => self::ALLOWED,
        'student_account.exam.attempt' => self::CONDITIONAL,
        'student_account.exam.submit' => self::CONDITIONAL,
        'student_account.exam.view_grade' => self::CONDITIONAL,
        'student_account.report.view' => self::CONDITIONAL,
        'student_account.payment_status.view' => self::CONDITIONAL,
        'student_account.settings.view' => self::ALLOWED,
        'student_account.settings.update' => self::CONDITIONAL,
        'student_account.file.view' => self::CONDITIONAL,
        'student_account.file.upload' => self::CONDITIONAL,
        'platform.audit_log.create' => self::CONDITIONAL,
    ];

    /**
     * Parent — 6 allowed, 10 conditional.
     *
     * @var array<string, string>
     */
    private const PARENT = [
        'parent_linked_student.dashboard.view' => self::ALLOWED,
        'parent_linked_student.educational_grade.view' => self::CONDITIONAL,
        'parent_linked_student.group.view' => self::CONDITIONAL,
        'parent_linked_student.student.view' => self::ALLOWED,
        'parent_linked_student.attendance.view' => self::ALLOWED,
        'parent_linked_student.homework.view' => self::ALLOWED,
        'parent_linked_student.exam.view' => self::ALLOWED,
        'parent_linked_student.exam.view_grade' => self::CONDITIONAL,
        'parent_linked_student.report.view' => self::CONDITIONAL,
        'parent_linked_student.payment_status.view' => self::ALLOWED,
        'parent_linked_student.parent_account.view' => self::CONDITIONAL,
        'parent_linked_student.parent_account.update' => self::CONDITIONAL,
        'parent_linked_student.settings.view' => self::CONDITIONAL,
        'parent_linked_student.settings.update' => self::CONDITIONAL,
        'parent_linked_student.file.view' => self::CONDITIONAL,
        'platform.audit_log.create' => self::CONDITIONAL,
    ];

    /**
     * Grants held by a role, keyed by permission name.
     *
     * @return array<string, string>
     */
    public static function forRole(string $role): array
    {
        return match ($role) {
            'super_admin' => self::SUPER_ADMIN,
            'teacher' => self::TEACHER,
            'student' => self::STUDENT,
            'parent' => self::PARENT,
            default => [],
        };
    }

    /**
     * How a role holds a permission: ALLOWED, CONDITIONAL, or null for denied.
     */
    public static function grant(string $role, string $permission): ?string
    {
        return self::forRole($role)[$permission] ?? null;
    }

    /**
     * Every permission the matrix defines — all 215.
     *
     * Listed explicitly rather than derived from the role grants, because
     * 51 permissions are Denied to all four roles and therefore appear in no
     * role's list. Those entries must still exist: their presence is what
     * makes a refusal explicit and auditable rather than an unknown
     * capability (PERMISSION_REGISTRY.md).
     *
     * @var list<string>
     */
    private const ALL = [
        'platform.dashboard.view',
        'teacher_workspace.dashboard.view',
        'student_account.dashboard.view',
        'parent_linked_student.dashboard.view',
        'teacher_workspace.dashboard.view_history',
        'platform.dashboard.view_teacher_private_content',
        'teacher_workspace.educational_grade.view',
        'teacher_workspace.educational_grade.create',
        'teacher_workspace.educational_grade.update',
        'teacher_workspace.educational_grade.archive',
        'teacher_workspace.educational_grade.restore',
        'teacher_workspace.educational_grade.view_history',
        'parent_linked_student.educational_grade.view',
        'student_account.educational_grade.view',
        'teacher_workspace.group.view',
        'teacher_workspace.group.create',
        'teacher_workspace.group.update',
        'teacher_workspace.group.archive',
        'teacher_workspace.group.restore',
        'teacher_workspace.group.view_history',
        'teacher_workspace.group.assign_student',
        'teacher_workspace.group.move_student',
        'student_account.group.view',
        'student_account.group.update',
        'parent_linked_student.group.view',
        'parent_linked_student.group.update',
        'teacher_workspace.student.view',
        'teacher_workspace.student.search',
        'teacher_workspace.student.create',
        'teacher_workspace.student.assign_existing',
        'teacher_workspace.student.update',
        'teacher_workspace.student.archive',
        'teacher_workspace.student.restore',
        'teacher_workspace.student.view_history',
        'student_account.student.view',
        'student_account.student.update',
        'student_account.student.activate',
        'student_account.student.create_duplicate',
        'parent_linked_student.student.view',
        'parent_linked_student.student.update',
        'platform.student.view_private_workspace_records',
        'teacher_workspace.attendance.view',
        'teacher_workspace.attendance.record',
        'teacher_workspace.attendance.update',
        'teacher_workspace.attendance.archive',
        'teacher_workspace.attendance.restore',
        'teacher_workspace.attendance.view_history',
        'student_account.attendance.view',
        'student_account.attendance.scan_dynamic_qr',
        'student_account.attendance.scan_id_card',
        'student_account.attendance.update',
        'parent_linked_student.attendance.view',
        'parent_linked_student.attendance.record',
        'parent_linked_student.attendance.update',
        'platform.attendance.view_report_summary',
        'teacher_workspace.homework.view',
        'teacher_workspace.homework.create',
        'teacher_workspace.homework.update',
        'teacher_workspace.homework.archive',
        'teacher_workspace.homework.restore',
        'teacher_workspace.homework.grade',
        'teacher_workspace.homework.view_submissions',
        'teacher_workspace.homework.view_history',
        'student_account.homework.view',
        'student_account.homework.submit',
        'student_account.homework.update_submission',
        'student_account.homework.grade',
        'parent_linked_student.homework.view',
        'parent_linked_student.homework.submit',
        'parent_linked_student.homework.update',
        'platform.homework.view_private_content',
        'teacher_workspace.homework.submit_video',
        'teacher_workspace.lesson.view',
        'teacher_workspace.lesson.create',
        'teacher_workspace.lesson.update',
        'teacher_workspace.lesson.archive',
        'teacher_workspace.lesson.restore',
        'teacher_workspace.lesson.upload_video',
        'teacher_workspace.lesson.view_history',
        'student_account.lesson.view',
        'student_account.lesson.browse_marketplace',
        'parent_linked_student.lesson.view',
        'platform.lesson.view_private_content',
        'platform.lesson.publish_marketplace',
        'teacher_workspace.question_bank.view',
        'teacher_workspace.question_bank.create',
        'teacher_workspace.question_bank.update',
        'teacher_workspace.question_bank.archive',
        'teacher_workspace.question_bank.restore',
        'teacher_workspace.exam.view',
        'teacher_workspace.exam.create',
        'teacher_workspace.exam.update',
        'teacher_workspace.exam.archive',
        'teacher_workspace.exam.restore',
        'teacher_workspace.exam.publish',
        'teacher_workspace.exam.grade',
        'teacher_workspace.exam.view_attempts',
        'teacher_workspace.exam.view_history',
        'student_account.exam.view',
        'student_account.exam.attempt',
        'student_account.exam.submit',
        'student_account.exam.view_grade',
        'student_account.question_bank.view',
        'parent_linked_student.exam.view',
        'parent_linked_student.exam.view_grade',
        'parent_linked_student.exam.attempt',
        'parent_linked_student.exam.update',
        'platform.question_bank.view_private_content',
        'platform.exam.view_private_definition',
        'platform.report.view',
        'platform.report.view_teacher_aggregate',
        'teacher_workspace.report.view',
        'teacher_workspace.report.view_attendance',
        'teacher_workspace.report.view_homework',
        'teacher_workspace.report.view_exam_results',
        'teacher_workspace.report.view_payments',
        'teacher_workspace.report.view_student_performance',
        'student_account.report.view',
        'parent_linked_student.report.view',
        'platform.payment_status.view',
        'platform.payment_status.record',
        'platform.payment_status.update',
        'platform.payment_status.view_history',
        'teacher_workspace.payment_status.view',
        'teacher_workspace.payment_status.record',
        'teacher_workspace.payment_status.update',
        'teacher_workspace.payment_status.view_history',
        'student_account.payment_status.view',
        'student_account.payment_status.update',
        'parent_linked_student.payment_status.view',
        'parent_linked_student.payment_status.update',
        'platform.subscription.view',
        'platform.subscription.calculate_billable_students',
        'platform.subscription.create_cycle',
        'platform.subscription.update_status',
        'platform.subscription.record_payment_status',
        'platform.subscription.view_history',
        'teacher_workspace.subscription.view_own_status',
        'teacher_workspace.subscription.update_status',
        'student_account.subscription.view',
        'parent_linked_student.subscription.view',
        'platform.subscription.enforce_non_payment',
        'platform.subscription.process_online_payment',
        'teacher_workspace.teacher_staff.view',
        'teacher_workspace.teacher_staff.create',
        'teacher_workspace.teacher_staff.update',
        'teacher_workspace.teacher_staff.archive',
        'teacher_workspace.teacher_staff.restore',
        'teacher_workspace.teacher_staff.assign_permission',
        'teacher_workspace.teacher_staff.view_history',
        'teacher_workspace.teacher_staff.self_assign_permission',
        'platform.user.view',
        'platform.user.create_platform_staff',
        'student_account.settings.view',
        'student_account.settings.update',
        'parent_linked_student.parent_account.view',
        'parent_linked_student.parent_account.update',
        'platform.settings.view',
        'platform.settings.update',
        'platform.settings.update_pricing',
        'platform.settings.update_localization',
        'platform.settings.configure_payment_gateway',
        'platform.settings.configure_notifications',
        'teacher_workspace.settings.view',
        'teacher_workspace.settings.update',
        'teacher_workspace.settings.update_teaching_subject',
        'parent_linked_student.settings.view',
        'parent_linked_student.settings.update',
        'teacher_workspace.file.view',
        'teacher_workspace.file.upload',
        'teacher_workspace.file.update',
        'teacher_workspace.file.archive',
        'teacher_workspace.file.restore',
        'teacher_workspace.file.view_history',
        'student_account.file.view',
        'student_account.file.upload',
        'student_account.file.upload_video_homework',
        'parent_linked_student.file.view',
        'parent_linked_student.file.upload',
        'platform.file.view_private_teacher_file',
        'platform.notification.view',
        'platform.notification.create',
        'platform.notification.update',
        'platform.notification.archive',
        'teacher_workspace.notification.view',
        'teacher_workspace.notification.create',
        'student_account.notification.view',
        'parent_linked_student.notification.view',
        'platform.audit_log.view',
        'platform.audit_log.view_teacher_workspace_events',
        'platform.audit_log.create',
        'platform.audit_log.update',
        'platform.audit_log.archive',
        'platform.audit_log.restore',
        'teacher_workspace.audit_log.view',
        'student_account.audit_log.view',
        'parent_linked_student.audit_log.view',
        'platform.teacher.view',
        'platform.teacher.create',
        'platform.teacher.update',
        'platform.teacher.archive',
        'platform.teacher.restore',
        'platform.teacher.view_history',
        'platform.teacher.login_as_teacher',
        'platform.teacher.update_teaching_subject',
        'platform.pricing.view',
        'platform.pricing.update',
        'platform.billing_cycle.view',
        'platform.billing_cycle.manage',
        'platform.global_report.view',
        'platform.platform_staff.create',
        'platform.marketplace.manage',
        'platform.course_discovery.manage',
        'platform.payment_gateway.manage',
        'platform.native_mobile.manage',
    ];

    /**
     * Every permission name the matrix defines.
     *
     * @return list<string>
     */
    public static function allPermissions(): array
    {
        return self::ALL;
    }

    /**
     * Whether a permission exists in the confirmed catalogue.
     */
    public static function exists(string $permission): bool
    {
        return in_array($permission, self::ALL, true);
    }
}
