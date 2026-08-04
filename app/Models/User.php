<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Database\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
 * "User identity is global. Role-specific access is controlled separately
 * through Role, Permission, and contextual relationships." (§1 Notes)
 *
 * "No hard delete exists for User records." (§1 Business Rules) — the account
 * is archived, never removed.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Archivable;
    use HasApiTokens;
    use HasFactory;
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
        'account_status',
    ];

    /**
     * The Student context for this identity, when one exists.
     *
     * "Student has one global User identity." (07 §6) A User may instead be a
     * Teacher, Teacher Staff, Parent, or Super Admin; those contexts are added
     * by their own phases.
     *
     * @return HasOne<Student, $this>
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Role contexts held by this identity.
     *
     * "A User may have one or more Role contexts." (§1 Relationships) The
     * pivot carries the Teacher Workspace for workspace-scoped roles, because
     * "A Role is assigned to a User in a specific context." (§2)
     *
     * @return BelongsToMany<Role, $this>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withPivot('teacher_workspace_id')
            ->withTimestamps();
    }

    /**
     * Attributes hidden from array and JSON output.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Disables Laravel's "remember me" token.
     *
     * No AI_DOCS document requires persistent login, so the `remember_token`
     * column was deliberately not created (PHYSICAL_SCHEMA_DECISIONS.md DD-08).
     * Laravel's session guard reads the token during logout, so an empty name
     * tells the framework there is none — `getRememberToken()` then returns
     * null instead of faulting on a missing attribute.
     *
     * This keeps the frozen schema untouched: the alternative would be adding
     * a column for a feature Version 1 does not have.
     *
     * @var string
     */
    protected $rememberTokenName = '';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Hashed on assignment so a plaintext secret can never be persisted.
            'password' => 'hashed',
        ];
    }
}
