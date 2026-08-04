<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Database\Archivable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Teacher Staff — 07_Data_Dictionary.md §30.
 *
 * Exists only inside the creating Teacher Workspace, and holds only the
 * permissions the Teacher assigns. Actions are attributed to the Teacher Staff
 * user in the Audit Log, never to the Teacher (§30; 23 §15.4).
 *
 * @property int $id
 * @property int $user_id
 * @property int $teacher_workspace_id
 * @property string|null $staff_type_label
 * @property string $account_status
 */
final class TeacherStaff extends Model
{
    use Archivable;

    /**
     * The dictionary entity is "Teacher Staff"; the plural of the canonical
     * term is the same, so the table is not the framework's inflected guess.
     */
    protected $table = 'teacher_staff';

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'teacher_workspace_id',
        'staff_type_label',
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
     * @return BelongsTo<TeacherWorkspace, $this>
     */
    public function teacherWorkspace(): BelongsTo
    {
        return $this->belongsTo(TeacherWorkspace::class);
    }

    /**
     * "Teacher Staff hold only permissions assigned by the Teacher." (§30)
     *
     * @return BelongsToMany<Permission, $this>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_teacher_staff')
            ->withPivot('teacher_workspace_id')
            ->withTimestamps();
    }
}
