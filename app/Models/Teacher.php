<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Database\Archivable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Teacher — 07_Data_Dictionary.md §4.
 *
 * Owns exactly one Teacher Workspace and exactly one Teaching Subject. The
 * Teaching Subject cannot change after account creation (BR-016).
 *
 * @property int $id
 * @property int $user_id
 * @property int $teacher_workspace_id
 * @property int $teaching_subject_id
 * @property string $account_status
 */
final class Teacher extends Model
{
    use Archivable;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'teacher_workspace_id',
        'teaching_subject_id',
        'account_status',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * "Teacher has one Teacher Workspace." (§4)
     *
     * @return BelongsTo<TeacherWorkspace, $this>
     */
    public function teacherWorkspace(): BelongsTo
    {
        return $this->belongsTo(TeacherWorkspace::class);
    }

    /**
     * "Teacher has exactly one Teaching Subject." (§4)
     *
     * @return BelongsTo<TeachingSubject, $this>
     */
    public function teachingSubject(): BelongsTo
    {
        return $this->belongsTo(TeachingSubject::class);
    }
}
