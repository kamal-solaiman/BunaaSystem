<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Physical schema integrity.
 *
 * Verifies that the tables, columns, and naming derived in Phase 43 match the
 * logical specification in AI_DOCS/06_Database_Design.md and
 * AI_DOCS/07_Data_Dictionary.md, and that no undocumented column has been
 * introduced.
 */
final class SchemaIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Every foundational table and its complete documented column set.
     *
     * @return array<string, list<string>>
     */
    private function expectedSchema(): array
    {
        return [
            'users' => ['id', 'name', 'email', 'password', 'account_status', 'created_at', 'updated_at', 'archived_at'],
            'teaching_subjects' => ['id', 'teacher_id', 'teacher_workspace_id', 'subject_name', 'selected_at', 'created_at', 'updated_at', 'archived_at'],
            'teacher_workspaces' => ['id', 'teacher_id', 'workspace_status', 'created_at', 'updated_at', 'archived_at'],
            'teachers' => ['id', 'user_id', 'teacher_workspace_id', 'teaching_subject_id', 'account_status', 'created_at', 'updated_at', 'archived_at'],
            'roles' => ['id', 'role_name', 'role_scope', 'description', 'created_at', 'updated_at', 'archived_at'],
            'permissions' => ['id', 'permission_name', 'permission_scope', 'permission_status', 'created_at', 'updated_at', 'archived_at'],
            'teacher_staff' => ['id', 'user_id', 'teacher_workspace_id', 'staff_type_label', 'account_status', 'created_at', 'updated_at', 'archived_at'],
            'audit_log_entries' => ['id', 'actor_user_id', 'actor_role', 'scope_context', 'teacher_workspace_id', 'event_type', 'affected_entity_name', 'affected_entity_id', 'event_details', 'ip_address', 'user_agent', 'occurred_at', 'created_at'],
        ];
    }

    #[Test]
    public function every_foundational_table_exists(): void
    {
        foreach (array_keys($this->expectedSchema()) as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing table: {$table}.");
        }

        foreach (['role_user', 'permission_teacher_staff'] as $pivot) {
            $this->assertTrue(Schema::hasTable($pivot), "Missing association table: {$pivot}.");
        }
    }

    #[Test]
    public function every_documented_attribute_has_a_column(): void
    {
        foreach ($this->expectedSchema() as $table => $columns) {
            foreach ($columns as $column) {
                $this->assertTrue(
                    Schema::hasColumn($table, $column),
                    "Missing column {$table}.{$column} required by the data dictionary."
                );
            }
        }
    }

    #[Test]
    public function no_undocumented_column_exists(): void
    {
        // Guards the rule that a column exists only because AI_DOCS requires it
        // or because it is mathematically necessary to implement a requirement.
        foreach ($this->expectedSchema() as $table => $columns) {
            $actual = Schema::getColumnListing($table);
            $extra = array_diff($actual, $columns);

            $this->assertSame(
                [],
                array_values($extra),
                "Undocumented column(s) in {$table}: ".implode(', ', $extra)
            );
        }
    }

    #[Test]
    public function archive_state_uses_the_canonical_column_name(): void
    {
        // 28_Coding_Standards.md §12.5 mandates archived_at, never a name that
        // implies permanent deletion.
        foreach (array_keys($this->expectedSchema()) as $table) {
            if ($table === 'audit_log_entries') {
                continue; // Never archived; see the audit test.
            }

            $this->assertTrue(Schema::hasColumn($table, 'archived_at'), "{$table} must carry archived_at.");
            $this->assertFalse(Schema::hasColumn($table, 'deleted_at'), "{$table} must not carry deleted_at.");
        }
    }

    #[Test]
    public function table_names_use_canonical_terminology(): void
    {
        // 28_Coding_Standards.md §12.5 and 06_Database_Design.md §3.
        foreach (['classes', 'courses', 'tenants', 'sub_teachers'] as $nonCanonical) {
            $this->assertFalse(Schema::hasTable($nonCanonical), "Non-canonical table present: {$nonCanonical}.");
        }

        $this->assertTrue(Schema::hasTable('teacher_workspaces'));
        $this->assertTrue(Schema::hasTable('audit_log_entries'));
    }
}
