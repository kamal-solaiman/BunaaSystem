<?php

declare(strict_types=1);

namespace App\Support\Database;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Archive behavior.
 *
 * Archive replaces permanent deletion everywhere; no record is ever hard
 * deleted (06_Database_Design.md §7, §15; 28_Coding_Standards.md §1.5, §2.4).
 *
 * This builds on Laravel's SoftDeletes so the framework's query scoping is
 * reused, but the column is `archived_at` (28_Coding_Standards.md §12.5) and
 * the vocabulary is Archive and restore, never delete. The delete-flavored
 * verbs stay out of application code so a reviewer cannot mistake one for a
 * hard delete.
 *
 * Archived records:
 * - do not appear in normal active searches or selectors (06 §7);
 * - remain available to reports and historical queries (06 §7);
 * - keep every historical relationship intact (06 §7, §13).
 *
 * @phpstan-require-extends \Illuminate\Database\Eloquent\Model
 */
trait Archivable
{
    use SoftDeletes;

    /**
     * The Archive state column.
     *
     * Overrides the framework default of `deleted_at`.
     */
    public function getDeletedAtColumn(): string
    {
        return 'archived_at';
    }

    /**
     * Archive this record. Nothing is removed from storage.
     */
    public function archive(): bool
    {
        return (bool) $this->delete();
    }

    /**
     * Restore a previously archived record.
     */
    public function unarchive(): bool
    {
        return (bool) $this->restore();
    }

    public function isArchived(): bool
    {
        return $this->{$this->getDeletedAtColumn()} !== null;
    }

    public function archivedAt(): ?Carbon
    {
        $value = $this->{$this->getDeletedAtColumn()};

        return $value instanceof Carbon ? $value : null;
    }

    /**
     * Historical and reporting scope: active records plus archived ones.
     *
     * 06_Database_Design.md §7 requires archived records to remain available
     * for reports and history.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeIncludingArchived(Builder $query): Builder
    {
        /** @phpstan-ignore-next-line provided by SoftDeletes */
        return $query->withTrashed();
    }

    /**
     * Archived records only.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeArchivedOnly(Builder $query): Builder
    {
        /** @phpstan-ignore-next-line provided by SoftDeletes */
        return $query->onlyTrashed();
    }
}
