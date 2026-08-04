<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * Audit Log Entry — 07_Data_Dictionary.md §27.
 *
 * Append-only, immutable, permanently retained
 * (06_Database_Design.md §8; 23_Security_Standards.md §15.4).
 *
 * Immutability is enforced here rather than left to convention: the model
 * refuses to update or delete an existing entry, so no code path — including a
 * Super Admin one — can rewrite history (23 §15.4: "No actor, including Super
 * Admin, can edit or delete Audit Log entries").
 *
 * The entry is never archived. §27 declares no Archived State and its Notes
 * state "Audit Log records are not archived or deleted", so this model
 * deliberately does not use the Archivable trait.
 *
 * @property int $id
 * @property int|null $actor_user_id
 * @property string|null $actor_role
 * @property string $scope_context
 * @property int|null $teacher_workspace_id
 * @property string $event_type
 * @property string $affected_entity_name
 * @property int|null $affected_entity_id
 * @property array<string, mixed>|null $event_details
 */
final class AuditLogEntry extends Model
{
    /**
     * Entries record their own occurrence time and are never updated, so the
     * framework's updated_at column does not apply.
     */
    public const UPDATED_AT = null;

    protected $table = 'audit_log_entries';

    /** @var list<string> */
    protected $fillable = [
        'actor_user_id',
        'actor_role',
        'scope_context',
        'teacher_workspace_id',
        'event_type',
        'affected_entity_name',
        'affected_entity_id',
        'event_details',
        'ip_address',
        'user_agent',
        'occurred_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_details' => 'array',
            'occurred_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Append-only: an existing entry can never be modified.
        static::updating(static function (): never {
            throw new RuntimeException(
                'Audit Log entries are immutable and cannot be updated.'
            );
        });

        // Permanent retention: an entry can never be removed.
        static::deleting(static function (): never {
            throw new RuntimeException(
                'Audit Log entries are permanently retained and cannot be deleted.'
            );
        });
    }

    /**
     * The actor, where one is resolvable.
     *
     * Null for events with no authenticated actor, such as a failed login
     * against an unknown identifier (§27: "where available").
     *
     * @return BelongsTo<User, $this>
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    /**
     * The owning Teacher Workspace for workspace-scoped events; null at
     * Platform scope (§27).
     *
     * @return BelongsTo<TeacherWorkspace, $this>
     */
    public function teacherWorkspace(): BelongsTo
    {
        return $this->belongsTo(TeacherWorkspace::class);
    }
}
