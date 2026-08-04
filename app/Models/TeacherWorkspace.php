<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Database\Archivable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Teacher Workspace — 07_Data_Dictionary.md §5.
 *
 * The tenant boundary (06_Database_Design.md §6). No Teacher may see another
 * Teacher's data; isolation is absolute and is enforced server-side.
 *
 * @property int $id
 * @property int|null $teacher_id
 * @property string $workspace_status
 */
final class TeacherWorkspace extends Model
{
    use Archivable;

    /** @var list<string> */
    protected $fillable = [
        'teacher_id',
        'workspace_status',
    ];

    /**
     * "Belongs to one Teacher." (§5)
     *
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * "One Teacher Workspace has exactly one Teaching Subject."
     * (06_Database_Design.md §5)
     *
     * @return HasOne<TeachingSubject, $this>
     */
    public function teachingSubject(): HasOne
    {
        return $this->hasOne(TeachingSubject::class);
    }

    /**
     * "Teacher Staff exist only inside the creating Teacher Workspace." (§30)
     *
     * @return HasMany<TeacherStaff, $this>
     */
    public function teacherStaff(): HasMany
    {
        return $this->hasMany(TeacherStaff::class);
    }
}
