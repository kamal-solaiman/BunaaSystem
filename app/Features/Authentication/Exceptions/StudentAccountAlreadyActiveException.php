<?php

declare(strict_types=1);

namespace App\Features\Authentication\Exceptions;

use RuntimeException;

/**
 * The targeted Teacher-created account is already active.
 *
 * Maps to STUDENT_ACCOUNT_ALREADY_ACTIVE (409) — 34_Error_Codes.md §9.
 * Activation is a one-way Pending Activation to Active transition.
 */
final class StudentAccountAlreadyActiveException extends RuntimeException
{
}
