<?php

declare(strict_types=1);

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Student activation payload — 33_Validation_Rules.md AUT-13, AUT-03.
 *
 * Activation is the documented authentication exception path: it is reachable
 * without an authenticated context, because the account being activated cannot
 * yet be logged into (02_Software_Requirements.md: account-setting access
 * requires authentication "except for activation flows").
 */
final class ActivateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            // The activation identifier is the account's Login Identifier;
            // 07 §6 defines no separate activation-token attribute, and the
            // frozen schema holds none, so none is invented.
            'identifier' => ['required', 'string', 'max:190'],
            // The Student sets their own secret when taking ownership.
            'secret' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()],
        ];
    }
}
