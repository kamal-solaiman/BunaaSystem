<?php

declare(strict_types=1);

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Student self-registration payload — 33_Validation_Rules.md AUT-11, AUT-03.
 *
 * The detailed identity-field catalog is deferred by the owning requirements
 * (AUT-11: "this catalog adds no field"), so only the fields the frozen schema
 * and the confirmed rules require are validated here. Nothing is invented.
 *
 * Duplicate prevention is a business rule enforced server-side in the service
 * and answered with 409, not 422 (AUT-12; 34_Error_Codes.md §7 note).
 */
final class RegisterStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Public endpoint (10_API_Design.md §13).
        return true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            // 07_Data_Dictionary.md §1 Display Name; bounded per GEN-04.
            'name' => ['required', 'string', 'max:190'],
            // §1 Login Identifier.
            'identifier' => ['required', 'string', 'max:190'],
            // AUT-03: minimum 8 characters with at least one uppercase, one
            // lowercase, and one digit. Special characters are recommended,
            // not mandatory, so none is required here.
            'secret' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()],
        ];
    }
}
