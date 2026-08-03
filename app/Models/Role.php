<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Database\Archivable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Role — 07_Data_Dictionary.md §2.
 *
 * Version 1 has exactly five roles. "Role does not by itself grant all access.
 * Access must also pass contextual authorization and Permission rules." (§2)
 *
 * @property int $id
 * @property string $role_name
 * @property string $role_scope
 */
final class Role extends Model
{
    use Archivable;

    /** @var list<string> */
    protected $fillable = ['role_name', 'role_scope', 'description'];

    /**
     * "A Role is assigned to a User in a specific context." (§2)
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withPivot('teacher_workspace_id')
            ->withTimestamps();
    }
}
