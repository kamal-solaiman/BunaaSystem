<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * User ↔ Role assignment.
 *
 * DERIVED TABLE. The dictionary defines no join entity, but it states:
 *
 * - "A User may have one or more Role contexts." (§1 Relationships)
 * - "A Role is assigned to a User in a specific context." (§2 Relationships)
 * - "Teacher Staff Role is scoped to the creating Teacher Workspace." (§2)
 *
 * A many-to-many relationship cannot be represented without an association
 * table, so this table is mathematically necessary to implement the stated
 * relationship rather than a convenience. See PHYSICAL_SCHEMA_DECISIONS.md
 * DD-03.
 *
 * The workspace column carries the "in a specific context" qualifier: a
 * Teacher Staff role is meaningful only inside the workspace that created it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('role_id')->constrained('roles')->restrictOnDelete();

            // "A Role is assigned to a User in a specific context." (§2)
            // Nullable because Platform, Student Account, and Parent Linked
            // Students scopes have no owning Teacher Workspace; only
            // workspace-scoped roles carry one (§2; 06 §6).
            $table->foreignId('teacher_workspace_id')
                ->nullable()
                ->constrained('teacher_workspaces')
                ->restrictOnDelete();

            $table->timestamps();

            // "Archived State ... Must follow Archive policy" governs the Role
            // itself (§2). The assignment is a relationship record and the
            // dictionary declares no archive state for it, so none is invented.

            // The same role must not be assigned twice to a user in the same
            // context. Enforcing it in the schema prevents duplicate
            // assignments that would make Audit Log attribution ambiguous.
            $table->unique(['user_id', 'role_id', 'teacher_workspace_id'], 'role_user_unique_assignment');

            // 06_Database_Design.md §10: "Role context lookup" and "Tenant
            // scope through Teacher Workspace association."
            $table->index(['user_id', 'teacher_workspace_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
