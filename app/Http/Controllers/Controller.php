<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Base controller.
 *
 * Controllers are thin request coordinators: they receive a validated request,
 * resolve the authenticated role and scope context, call a service, and return
 * a standardized response through App\Support\Api\ApiResponse.
 *
 * Controllers must not contain business rules, run raw queries, decide Teacher
 * Workspace access on their own, calculate Billable Students, or process
 * payments (AI_DOCS/28_Coding_Standards.md §3.2).
 */
abstract class Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;
}
