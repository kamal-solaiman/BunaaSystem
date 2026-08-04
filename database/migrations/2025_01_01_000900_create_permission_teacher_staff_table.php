<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Teacher Staff Permission Assignment — 06_Database_Design.md §4.
 *
 * The Entity Overview names this entity directly: "Represents permissions
 * assigned by a Teacher to Teacher Staff within a Teacher Workspace."
 *
 * Supporting rules:
 * - "Permissions may be assigned to Teacher Staff by the Teacher."
 *   (07_Data_Dictionary.md §3 Relationships)
 * - "Permission Assignment Reference | Reference | Optional" (§30)
 * - "Teacher Staff hold only permissions assigned by the Teacher." (§3, §30)
 *
 * PENDING Q-011 is preserved: the assignment mechanism exists, but because the
 * `permissions` catalog is seeded empty, no granularity is assumed. Both
 * 06 §4 and 07 §3 state the granularity "remains PENDING and must not be
 * silently assumed."
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_teacher_staff', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('teacher_staff_id')
                ->constrained('teacher_staff')
                ->restrictOnDelete();

            $table->foreignId('permission_id')
                ->constrained('permissions')
                ->restrictOnDelete();

            // "assigned by a Teacher to Teacher Staff within a Teacher
            // Workspace" (06 §4). Carrying the workspace on the assignment
            // makes the tenant boundary explicit at the row level, which
            // 06 §6 rule 1 requires of every workspace-owned record.
            $table->foreignId('teacher_workspace_id')
                ->constrained('teacher_workspaces')
                ->restrictOnDelete();

            $table->timestamps();

            // A permission is either assigned or not; assigning it twice is
            // meaningless and would make "Permission Change" audit entries
            // ambiguous (23_Security_Standards.md §15.2 item 6).
            $table->unique(['teacher_staff_id', 'permission_id'], 'permission_staff_unique_assignment');

            // 06_Database_Design.md §10: tenant scope association.
            $table->index('teacher_workspace_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_teacher_staff');
    }
};
