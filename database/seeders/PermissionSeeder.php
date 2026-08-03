<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

/**
 * The Version 1 permission catalogue — 09_Permission_Matrix.md.
 *
 * All 215 confirmed permissions, frozen by PERMISSION_REGISTRY.md. Names and
 * scopes are immutable: a new permission may be added only through an approved
 * Architecture Change Request after the matrix is amended.
 *
 * Permissions denied to every role are seeded deliberately. Their presence is
 * what makes a refusal explicit and auditable — an attempt against, say,
 * `teacher_workspace.teacher_staff.self_assign_permission` is a recognized
 * security event rather than an unknown capability.
 *
 * Note the naming detail preserved from the registry: permission names use the
 * prefix `parent_linked_student` (singular) while the scope enum value is
 * `parent_linked_students` (plural). Both are correct; neither is normalized.
 *
 * Idempotent, because `(permission_name, permission_scope)` is unique.
 */
final class PermissionSeeder extends Seeder
{
    /**
     * Platform scope — 60 permissions.
     *
     * @var list<string>
     */
    private const PLATFORM = [
        'platform.dashboard.view',
        'platform.dashboard.view_teacher_private_content',
        'platform.student.view_private_workspace_records',
        'platform.attendance.view_report_summary',
        'platform.homework.view_private_content',
        'platform.lesson.view_private_content',
        'platform.lesson.publish_marketplace',
        'platform.question_bank.view_private_content',
        'platform.exam.view_private_definition',
        'platform.report.view',
        'platform.report.view_teacher_aggregate',
        'platform.payment_status.view',
        'platform.payment_status.record',
        'platform.payment_status.update',
        'platform.payment_status.view_history',
        'platform.subscription.view',
        'platform.subscription.calculate_billable_students',
        'platform.subscription.create_cycle',
        'platform.subscription.update_status',
        'platform.subscription.record_payment_status',
        'platform.subscription.view_history',
        'platform.subscription.enforce_non_payment',
        'platform.subscription.process_online_payment',
        'platform.user.view',
        'platform.user.create_platform_staff',
        'platform.settings.view',
        'platform.settings.update',
        'platform.settings.update_pricing',
        'platform.settings.update_localization',
        'platform.settings.configure_payment_gateway',
        'platform.settings.configure_notifications',
        'platform.file.view_private_teacher_file',
        'platform.notification.view',
        'platform.notification.create',
        'platform.notification.update',
        'platform.notification.archive',
        'platform.audit_log.view',
        'platform.audit_log.view_teacher_workspace_events',
        'platform.audit_log.create',
        'platform.audit_log.update',
        'platform.audit_log.archive',
        'platform.audit_log.restore',
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
     * Teacher Workspace scope — 92 permissions.
     *
     * @var list<string>
     */
    private const TEACHER_WORKSPACE = [
        'teacher_workspace.dashboard.view',
        'teacher_workspace.dashboard.view_history',
        'teacher_workspace.educational_grade.view',
        'teacher_workspace.educational_grade.create',
        'teacher_workspace.educational_grade.update',
        'teacher_workspace.educational_grade.archive',
        'teacher_workspace.educational_grade.restore',
        'teacher_workspace.educational_grade.view_history',
        'teacher_workspace.group.view',
        'teacher_workspace.group.create',
        'teacher_workspace.group.update',
        'teacher_workspace.group.archive',
        'teacher_workspace.group.restore',
        'teacher_workspace.group.view_history',
        'teacher_workspace.group.assign_student',
        'teacher_workspace.group.move_student',
        'teacher_workspace.student.view',
        'teacher_workspace.student.search',
        'teacher_workspace.student.create',
        'teacher_workspace.student.assign_existing',
        'teacher_workspace.student.update',
        'teacher_workspace.student.archive',
        'teacher_workspace.student.restore',
        'teacher_workspace.student.view_history',
        'teacher_workspace.attendance.view',
        'teacher_workspace.attendance.record',
        'teacher_workspace.attendance.update',
        'teacher_workspace.attendance.archive',
        'teacher_workspace.attendance.restore',
        'teacher_workspace.attendance.view_history',
        'teacher_workspace.homework.view',
        'teacher_workspace.homework.create',
        'teacher_workspace.homework.update',
        'teacher_workspace.homework.archive',
        'teacher_workspace.homework.restore',
        'teacher_workspace.homework.grade',
        'teacher_workspace.homework.view_submissions',
        'teacher_workspace.homework.view_history',
        'teacher_workspace.homework.submit_video',
        'teacher_workspace.lesson.view',
        'teacher_workspace.lesson.create',
        'teacher_workspace.lesson.update',
        'teacher_workspace.lesson.archive',
        'teacher_workspace.lesson.restore',
        'teacher_workspace.lesson.upload_video',
        'teacher_workspace.lesson.view_history',
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
        'teacher_workspace.report.view',
        'teacher_workspace.report.view_attendance',
        'teacher_workspace.report.view_homework',
        'teacher_workspace.report.view_exam_results',
        'teacher_workspace.report.view_payments',
        'teacher_workspace.report.view_student_performance',
        'teacher_workspace.payment_status.view',
        'teacher_workspace.payment_status.record',
        'teacher_workspace.payment_status.update',
        'teacher_workspace.payment_status.view_history',
        'teacher_workspace.subscription.view_own_status',
        'teacher_workspace.subscription.update_status',
        'teacher_workspace.teacher_staff.view',
        'teacher_workspace.teacher_staff.create',
        'teacher_workspace.teacher_staff.update',
        'teacher_workspace.teacher_staff.archive',
        'teacher_workspace.teacher_staff.restore',
        'teacher_workspace.teacher_staff.assign_permission',
        'teacher_workspace.teacher_staff.view_history',
        'teacher_workspace.teacher_staff.self_assign_permission',
        'teacher_workspace.settings.view',
        'teacher_workspace.settings.update',
        'teacher_workspace.settings.update_teaching_subject',
        'teacher_workspace.file.view',
        'teacher_workspace.file.upload',
        'teacher_workspace.file.update',
        'teacher_workspace.file.archive',
        'teacher_workspace.file.restore',
        'teacher_workspace.file.view_history',
        'teacher_workspace.notification.view',
        'teacher_workspace.notification.create',
        'teacher_workspace.audit_log.view',
    ];

    /**
     * Student Account scope — 34 permissions.
     *
     * @var list<string>
     */
    private const STUDENT_ACCOUNT = [
        'student_account.dashboard.view',
        'student_account.educational_grade.view',
        'student_account.group.view',
        'student_account.group.update',
        'student_account.student.view',
        'student_account.student.update',
        'student_account.student.activate',
        'student_account.student.create_duplicate',
        'student_account.attendance.view',
        'student_account.attendance.scan_dynamic_qr',
        'student_account.attendance.scan_id_card',
        'student_account.attendance.update',
        'student_account.homework.view',
        'student_account.homework.submit',
        'student_account.homework.update_submission',
        'student_account.homework.grade',
        'student_account.lesson.view',
        'student_account.lesson.browse_marketplace',
        'student_account.exam.view',
        'student_account.exam.attempt',
        'student_account.exam.submit',
        'student_account.exam.view_grade',
        'student_account.question_bank.view',
        'student_account.report.view',
        'student_account.payment_status.view',
        'student_account.payment_status.update',
        'student_account.subscription.view',
        'student_account.settings.view',
        'student_account.settings.update',
        'student_account.file.view',
        'student_account.file.upload',
        'student_account.file.upload_video_homework',
        'student_account.notification.view',
        'student_account.audit_log.view',
    ];

    /**
     * Parent Linked Students scope — 29 permissions.
     *
     * @var list<string>
     */
    private const PARENT_LINKED_STUDENTS = [
        'parent_linked_student.dashboard.view',
        'parent_linked_student.educational_grade.view',
        'parent_linked_student.group.view',
        'parent_linked_student.group.update',
        'parent_linked_student.student.view',
        'parent_linked_student.student.update',
        'parent_linked_student.attendance.view',
        'parent_linked_student.attendance.record',
        'parent_linked_student.attendance.update',
        'parent_linked_student.homework.view',
        'parent_linked_student.homework.submit',
        'parent_linked_student.homework.update',
        'parent_linked_student.lesson.view',
        'parent_linked_student.exam.view',
        'parent_linked_student.exam.view_grade',
        'parent_linked_student.exam.attempt',
        'parent_linked_student.exam.update',
        'parent_linked_student.report.view',
        'parent_linked_student.payment_status.view',
        'parent_linked_student.payment_status.update',
        'parent_linked_student.subscription.view',
        'parent_linked_student.parent_account.view',
        'parent_linked_student.parent_account.update',
        'parent_linked_student.settings.view',
        'parent_linked_student.settings.update',
        'parent_linked_student.file.view',
        'parent_linked_student.file.upload',
        'parent_linked_student.notification.view',
        'parent_linked_student.audit_log.view',
    ];

    public function run(): void
    {
        $catalogue = [
            'platform' => self::PLATFORM,
            'teacher_workspace' => self::TEACHER_WORKSPACE,
            'student_account' => self::STUDENT_ACCOUNT,
            'parent_linked_students' => self::PARENT_LINKED_STUDENTS,
        ];

        foreach ($catalogue as $scope => $names) {
            foreach ($names as $name) {
                Permission::query()->updateOrCreate(
                    ['permission_name' => $name, 'permission_scope' => $scope],
                    ['permission_status' => 'active'],
                );
            }
        }
    }
}
