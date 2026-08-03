<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Teaching Subject — 07_Data_Dictionary.md §31.
 *
 * The single subject associated with a Teacher account. Selected once during
 * registration and immutable afterwards (BR-016).
 *
 * Created before `teachers` because the Teacher entity holds a required
 * Teaching Subject Reference (§4).
 *
 * The logical entity also declares Teacher Reference and Teacher Workspace
 * Reference. Those are added by a later migration once `teachers` and
 * `teacher_workspaces` exist; see PHYSICAL_SCHEMA_DECISIONS.md DD-02 for why
 * the mutual references are resolved in that order rather than invented away.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teaching_subjects', function (Blueprint $table): void {
            // "Teaching Subject Identifier | Identifier | Required | Generated
            // by system | Must be unique." (§31)
            $table->id();

            // "Subject Name | Text | Required" (§31). Length is a bounded
            // maximum chosen at physical design time, expressly permitted by
            // 33_Validation_Rules.md GEN-04.
            $table->string('subject_name', 190);

            // "Selected At | DateTime | Required | Account creation time |
            // Must be set once." (§31)
            $table->timestamp('selected_at');

            // "Created At | DateTime | Required" is common to the dictionary
            // entities; Laravel's pair also satisfies "Updated At" where the
            // entity declares it (§1).
            $table->timestamps();

            // "Archived State | Archive State | Required | Active | Must follow
            // Archive policy." (§31) Column name mandated by
            // 28_Coding_Standards.md §12.5.
            $table->timestamp('archived_at')->nullable();

            // 06_Database_Design.md §10: "Archive state filtering for active
            // versus archived records."
            $table->index('archived_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching_subjects');
    }
};
