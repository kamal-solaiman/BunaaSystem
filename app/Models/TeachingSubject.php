<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Database\Archivable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Teaching Subject — 07_Data_Dictionary.md §31.
 *
 * "Each Teacher account represents exactly one Teaching Subject" and it
 * "cannot be changed after account creation."
 *
 * @property int $id
 * @property string $subject_name
 * @property int|null $teacher_id
 * @property int|null $teacher_workspace_id
 */
final class TeachingSubject extends Model
{
    use Archivable;

    /** @var list<string> */
    protected $fillable = [
        'subject_name',
        'teacher_id',
        'teacher_workspace_id',
        'selected_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['selected_at' => 'datetime'];
    }

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * @return BelongsTo<TeacherWorkspace, $this>
     */
    public function teacherWorkspace(): BelongsTo
    {
        return $this->belongsTo(TeacherWorkspace::class);
    }
}
