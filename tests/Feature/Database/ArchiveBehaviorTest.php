<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use App\Models\TeacherStaff;
use App\Models\TeacherWorkspace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Archive behavior.
 *
 * Archive replaces permanent deletion everywhere (06_Database_Design.md §7,
 * §15; 28_Coding_Standards.md §1.5, §2.4). Archived records disappear from
 * active queries, remain available for history and reporting, and never lose
 * their relationships.
 */
final class ArchiveBehaviorTest extends TestCase
{
    use RefreshDatabase;

    private function makeStaff(): TeacherStaff
    {
        $workspace = TeacherWorkspace::create(['workspace_status' => 'active']);

        return TeacherStaff::create([
            'user_id' => User::factory()->create()->id,
            'teacher_workspace_id' => $workspace->id,
            'staff_type_label' => 'Secretary',
        ]);
    }

    #[Test]
    public function a_record_begins_active(): void
    {
        $this->assertFalse($this->makeStaff()->isArchived());
    }

    #[Test]
    public function archiving_never_removes_the_row(): void
    {
        $staff = $this->makeStaff();
        $staff->archive();

        // The defining property of Archive: the data is still there.
        $this->assertDatabaseHas('teacher_staff', ['id' => $staff->id]);
    }

    #[Test]
    public function archived_records_disappear_from_active_queries(): void
    {
        // 06 §7: archived records must not appear in normal active searches,
        // dropdowns, selectors, pickers, or assignment lists.
        $staff = $this->makeStaff();
        $staff->archive();

        $this->assertNull(TeacherStaff::find($staff->id));
        $this->assertSame(0, TeacherStaff::count());
    }

    #[Test]
    public function archived_records_remain_available_for_history_and_reports(): void
    {
        // 06 §7: archived records remain available in reports and historical
        // queries.
        $staff = $this->makeStaff();
        $staff->archive();

        $this->assertNotNull(TeacherStaff::withTrashed()->find($staff->id));
        $this->assertSame(1, TeacherStaff::onlyTrashed()->count());
    }

    #[Test]
    public function archiving_preserves_historical_relationships(): void
    {
        // 06 §13: "Archiving must never break historical references."
        $staff = $this->makeStaff();
        $workspaceId = $staff->teacher_workspace_id;
        $staff->archive();

        $this->assertSame(
            $workspaceId,
            (int) DB::table('teacher_staff')->where('id', $staff->id)->value('teacher_workspace_id')
        );
    }

    #[Test]
    public function an_archived_record_can_be_restored(): void
    {
        // 06 §7: archived records can be restored by authorized users.
        $staff = $this->makeStaff();
        $staff->archive();

        TeacherStaff::withTrashed()->find($staff->id)->unarchive();

        $this->assertNotNull(TeacherStaff::find($staff->id));
    }

    #[Test]
    public function archiving_writes_the_canonical_column(): void
    {
        $staff = $this->makeStaff();
        $staff->archive();

        $this->assertNotNull(
            DB::table('teacher_staff')->where('id', $staff->id)->value('archived_at')
        );
    }

    #[Test]
    public function archiving_a_container_does_not_erase_its_children(): void
    {
        // 06 §14: "Archiving a parent or container record must not erase
        // historical child records."
        $staff = $this->makeStaff();
        $workspace = TeacherWorkspace::find($staff->teacher_workspace_id);

        $workspace->archive();

        $this->assertDatabaseHas('teacher_staff', ['id' => $staff->id]);
        $this->assertNotNull(TeacherStaff::find($staff->id));
    }
}
