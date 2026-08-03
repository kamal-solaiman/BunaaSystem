<?php

declare(strict_types=1);

namespace App\Support\Audit;

/**
 * The ten mandatory audit events.
 *
 * Confirmed closed set: 23_Security_Standards.md §15.2, 33_Validation_Rules.md
 * §3.3, and 00_Project_Context.md §10. The values match the `event_type` enum
 * frozen in the database contract, so no new event can be introduced without a
 * documentation change first.
 */
enum AuditEvent: string
{
    case Create = 'create';
    case Update = 'update';
    case Archive = 'archive';
    case Restore = 'restore';
    case Login = 'login';
    case PermissionChange = 'permission_change';
    case AttendanceChange = 'attendance_change';
    case ExamModification = 'exam_modification';
    case HomeworkModification = 'homework_modification';
    case SubscriptionChange = 'subscription_change';
}
