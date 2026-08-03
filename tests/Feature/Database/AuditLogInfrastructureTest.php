<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use App\Models\AuditLogEntry;
use App\Models\TeacherWorkspace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

/**
 * Audit Log infrastructure.
 *
 * Append-only, immutable, permanently retained
 * (07_Data_Dictionary.md §27; 06_Database_Design.md §8;
 * 23_Security_Standards.md §15.4).
 */
final class AuditLogInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    /** @param array<string, mixed> $overrides */
    private function makeEntry(array $overrides = []): AuditLogEntry
    {
        return AuditLogEntry::create(array_merge([
            'actor_user_id' => User::factory()->create()->id,
            'actor_role' => 'teacher',
            'scope_context' => 'teacher_workspace',
            'teacher_workspace_id' => TeacherWorkspace::create(['workspace_status' => 'active'])->id,
            'event_type' => 'create',
            'affected_entity_name' => 'Teacher Staff',
            'affected_entity_id' => 1,
            'event_details' => ['before' => null, 'after' => ['staff_type_label' => 'Secretary']],
            'ip_address' => '203.0.113.10',
            'user_agent' => 'test-agent',
            'occurred_at' => now(),
        ], $overrides));
    }

    #[Test]
    public function an_entry_records_the_documented_content(): void
    {
        // 23 §15.3: actor, context, event, payload, origin.
        $entry = $this->makeEntry()->fresh();

        $this->assertNotNull($entry->actor_user_id);
        $this->assertSame('teacher', $entry->actor_role);
        $this->assertSame('teacher_workspace', $entry->scope_context);
        $this->assertSame('create', $entry->event_type);
        $this->assertSame('Teacher Staff', $entry->affected_entity_name);
        $this->assertSame('203.0.113.10', $entry->ip_address);
        $this->assertNotNull($entry->occurred_at);
    }

    #[Test]
    public function the_payload_preserves_a_before_and_after_snapshot(): void
    {
        // 23 §15.3: "Payload: before/after snapshot of changed fields."
        $details = $this->makeEntry()->fresh()->event_details;

        $this->assertIsArray($details);
        $this->assertArrayHasKey('before', $details);
        $this->assertSame('Secretary', $details['after']['staff_type_label']);
    }

    #[Test]
    public function an_entry_cannot_be_updated(): void
    {
        // 23 §15.4: no actor, including Super Admin, may edit an entry.
        $entry = $this->makeEntry();

        $this->expectException(RuntimeException::class);
        $entry->update(['event_type' => 'update']);
    }

    #[Test]
    public function an_entry_cannot_be_deleted(): void
    {
        // 23 §15.4: permanent retention; entries are never purged.
        $entry = $this->makeEntry();

        $this->expectException(RuntimeException::class);
        $entry->delete();
    }

    #[Test]
    public function a_tampered_entry_survives_intact(): void
    {
        $entry = $this->makeEntry();

        try {
            $entry->update(['event_type' => 'update']);
        } catch (RuntimeException) {
            // expected
        }

        $this->assertSame('create', AuditLogEntry::find($entry->id)?->event_type);
    }

    #[Test]
    public function entries_are_never_archived(): void
    {
        // §27 Notes: "Audit Log records are not archived or deleted."
        $this->assertFalse(Schema::hasColumn('audit_log_entries', 'archived_at'));
    }

    #[Test]
    public function entries_carry_no_update_timestamp(): void
    {
        // An immutable record is never updated, so an updated_at column could
        // only mislead.
        $this->assertFalse(Schema::hasColumn('audit_log_entries', 'updated_at'));
    }

    #[Test]
    public function an_event_without_a_resolvable_actor_is_recordable(): void
    {
        // A failed login is a mandatory audit event (23 §15.2 item 5) and may
        // carry no resolvable actor — §27 says "where available".
        $entry = $this->makeEntry([
            'actor_user_id' => null,
            'actor_role' => null,
            'scope_context' => 'platform',
            'teacher_workspace_id' => null,
            'event_type' => 'login',
            'affected_entity_name' => 'User',
            'affected_entity_id' => null,
        ]);

        $this->assertTrue($entry->exists);
        $this->assertNull($entry->fresh()->actor_user_id);
    }

    #[Test]
    public function platform_scope_entries_carry_no_workspace(): void
    {
        $entry = $this->makeEntry([
            'scope_context' => 'platform',
            'teacher_workspace_id' => null,
        ]);

        $this->assertNull($entry->fresh()->teacher_workspace_id);
    }
}
