<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Resolves the mutual references the dictionary declares between Teacher,
 * Teacher Workspace, and Teaching Subject.
 *
 * - Teacher Workspace holds "Teacher Reference | Reference | Required | Must
 *   reference the owning Teacher." (07_Data_Dictionary.md §5)
 * - Teaching Subject holds "Teacher Reference" and "Teacher Workspace
 *   Reference", both Required. (§31)
 * - Teacher itself holds Required references to both. (§4)
 *
 * Those cycles cannot all be satisfied by column order inside a single
 * `CREATE TABLE`, so the back-references are added here once every table
 * exists. The columns are nullable at the database level *only* because a row
 * must exist before its counterpart can point at it; the logical "Required"
 * rule is enforced by the application at creation time, inside the same
 * transaction. See PHYSICAL_SCHEMA_DECISIONS.md DD-02.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_workspaces', function (Blueprint $table): void {
            // §5: "Teacher Reference | Reference | Required | Must reference
            // the owning Teacher."
            $table->foreignId('teacher_id')
                ->nullable()
                ->after('id')
                ->constrained('teachers')
                ->restrictOnDelete();

            // "Belongs to one Teacher." (§5 Relationships) — one-to-one.
            $table->unique('teacher_id');
        });

        Schema::table('teaching_subjects', function (Blueprint $table): void {
            // §31: "Teacher Reference | Reference | Required | Must reference
            // one Teacher account."
            $table->foreignId('teacher_id')
                ->nullable()
                ->after('id')
                ->constrained('teachers')
                ->restrictOnDelete();

            // §31: "Teacher Workspace Reference | Reference | Required | Must
            // reference the Teacher Workspace."
            $table->foreignId('teacher_workspace_id')
                ->nullable()
                ->after('teacher_id')
                ->constrained('teacher_workspaces')
                ->restrictOnDelete();

            // "Each Teacher account represents exactly one Teaching Subject."
            // (§31 Business Rules; 06_Database_Design.md §12) — enforcing the
            // one-subject-per-Teacher rule in the schema.
            $table->unique('teacher_id');

            // 06_Database_Design.md §10: "Tenant scope through Teacher
            // Workspace association."
            $table->index('teacher_workspace_id');
        });
    }

    public function down(): void
    {
        Schema::table('teaching_subjects', function (Blueprint $table): void {
            $table->dropForeign(['teacher_id']);
            $table->dropForeign(['teacher_workspace_id']);
            $table->dropUnique(['teacher_id']);
            $table->dropIndex(['teacher_workspace_id']);
            $table->dropColumn(['teacher_id', 'teacher_workspace_id']);
        });

        Schema::table('teacher_workspaces', function (Blueprint $table): void {
            $table->dropForeign(['teacher_id']);
            $table->dropUnique(['teacher_id']);
            $table->dropColumn('teacher_id');
        });
    }
};
