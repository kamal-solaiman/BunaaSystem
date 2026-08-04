<?php

declare(strict_types=1);

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Login payload — 33_Validation_Rules.md AUT-01, AUT-02.
 *
 * Presence only. Credential correctness is never validated here: a wrong
 * secret must produce the same generic 401 as an unknown identifier
 * (AUT-04), so it cannot be reported as a field error.
 */
final class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Public endpoint (10_API_Design.md §13).
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            // AUT-01: required, bounded text (GEN-04). No format is invented
            // beyond what the documents confirm.
            'identifier' => ['required', 'string', 'max:190'],
            // AUT-02: required; never echoed, logged, or length-reported.
            'secret' => ['required', 'string'],
        ];
    }
}
