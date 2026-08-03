<?php

declare(strict_types=1);

namespace App\Features\Authentication\Exceptions;

use RuntimeException;

/**
 * The activation payload matched no pending-activation Teacher-created account.
 *
 * Maps to STUDENT_ACTIVATION_MISMATCH (404) — 34_Error_Codes.md §9;
 * 33_Validation_Rules.md AUT-13.
 *
 * Unknown identity, missing Student context, and a self-registered account all
 * produce this same outcome, so activation cannot be used to probe which
 * identities exist.
 */
final class StudentActivationMismatchException extends RuntimeException
{
}
