<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Database\Archivable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Permission — 07_Data_Dictionary.md §3.
 *
 * The catalog is intentionally empty. Teacher Staff permission granularity is
 * PENDING Q-011 and "must not be silently assumed"; the final catalog belongs
 * to RBAC documentation (§3 Notes).
 *
 * @property int $id
 * @property string $permission_name
 * @property string $permission_scope
 * @property string $permission_status
 */
final class Permission extends Model
{
    use Archivable;

    /** @var list<string> */
    protected $fillable = ['permission_name', 'permission_scope', 'permission_status'];

    /**
     * "Permissions may be assigned to Teacher Staff by the Teacher." (§3)
     *
     * @return BelongsToMany<TeacherStaff, $this>
     */
    public function teacherStaff(): BelongsToMany
    {
        return $this->belongsToMany(TeacherStaff::class, 'permission_teacher_staff')
            ->withPivot('teacher_workspace_id')
            ->withTimestamps();
    }
}
