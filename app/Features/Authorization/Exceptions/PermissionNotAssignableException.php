<?php

declare(strict_types=1);

namespace App\Features\Authorization\Exceptions;

use RuntimeException;

/**
 * A permission cannot be delegated to Teacher Staff.
 *
 * Raised when the capability is one a Teacher does not hold, or is archived or
 * inactive. Teacher Staff access is an intersection of what the Teacher
 * assigned and what the Teacher holds (09_Permission_Matrix.md §5), so a
 * Teacher can never delegate more than their own access.
 *
 * Maps to AUTHZ_STAFF_PERMISSION_MISSING (403) — 34_Error_Codes.md AUTHZ-04.
 */
final class PermissionNotAssignableException extends RuntimeException
{
}
