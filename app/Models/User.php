<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Authenticatable user.
 *
 * Version 1 recognizes five roles: Super Admin, Teacher, Teacher Staff,
 * Student, and Parent (AI_DOCS/08_RBAC.md). A Student has exactly one global
 * account and may study with multiple Teachers; a Parent has one account and
 * may monitor multiple linked Students.
 *
 * Foundation only: this holds the framework authentication contract. Roles,
 * permissions, Teacher Workspace ownership, relationships, Archive state, and
 * every business attribute arrive with their own phases, driven by
 * 06_Database_Design.md and 07_Data_Dictionary.md.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasApiTokens;
    use Notifiable;

    /**
     * Mass assignable attributes.
     *
     * The allow-list is explicit; the model is never unguarded
     * (28_Coding_Standards.md §3.5).
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Attributes hidden from array and JSON output.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // Hashed on assignment so a plaintext secret can never be persisted.
            'password' => 'hashed',
        ];
    }
}
