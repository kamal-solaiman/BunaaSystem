<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Database\Archivable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Student — 07_Data_Dictionary.md §6.
 *
 * "Represents a learner with one global account who may study with multiple
 * Teachers." Identity is global; Teacher-specific academic data is partitioned
 * per Teacher and does not live here (§6 Notes).
 *
 * Phase 43B provides the entity and its relationships only. Registration,
 * activation, duplicate detection, and every other workflow belong to later
 * phases — this model deliberately contains no business logic.
 *
 * @property int $id
 * @property int $user_id
 * @property string $activation_status
 * @property string $account_status
 * @property string $created_by_method
 */
final class Student extends Model
{
    use Archivable;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'activation_status',
        'account_status',
        'created_by_method',
    ];

    /**
     * "Student has one global User identity." (§6 Relationships)
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Whether the account still awaits activation by the Student.
     *
     * A Teacher-created account starts pending and is activated later
     * (§6 Business Rules; 33 STU-02). This is a state reader, not the
     * activation workflow, which Phase 44 owns.
     */
    public function isPendingActivation(): bool
    {
        return $this->activation_status === 'pending_activation';
    }
}
