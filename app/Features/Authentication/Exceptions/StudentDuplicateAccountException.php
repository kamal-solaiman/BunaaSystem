<?php

declare(strict_types=1);

namespace App\Features\Authentication\Exceptions;

use RuntimeException;

/**
 * A Student identity already has a global account.
 *
 * Maps to STUDENT_DUPLICATE_ACCOUNT (409) — 34_Error_Codes.md §9; BR-001,
 * BR-022; 33_Validation_Rules.md AUT-12.
 *
 * The rejection must not expose where, or with which Teacher, the existing
 * account studies.
 */
final class StudentDuplicateAccountException extends RuntimeException
{
}
