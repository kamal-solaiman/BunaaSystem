<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Completes the User entity — 07_Data_Dictionary.md §1.
 *
 * The framework's `users` table already carries the Display Name, Login
 * Identifier, and Authentication Secret attributes. Two documented attributes
 * are missing from it:
 *
 * - "Account Status | Status | Required | Active | Must represent an allowed
 *   account state." (§1)
 * - "Archived State | Archive State | Required | Active | Must follow Archive
 *   policy." (§1)
 *
 * They are added here rather than by editing the framework migration, so the
 * baseline table stays recognizable and the addition is traceable.
 *
 * "No hard delete exists for User records." (§1 Business Rules)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            // §1 Account Status. 33_Validation_Rules.md STU-02 confirms
            // "Active" and "Pending Activation" as activation states for
            // Teacher-created Student accounts; the dictionary documents the
            // Active default. Stored as a constrained string rather than an
            // enum because the full per-role state set is not enumerated in
            // one place. See PHYSICAL_SCHEMA_DECISIONS.md DD-04.
            $table->string('account_status', 32)->default('active')->after('password');

            // §1 Archived State. Column name mandated by
            // 28_Coding_Standards.md §12.5.
            $table->timestamp('archived_at')->nullable()->after('updated_at');

            // 06_Database_Design.md §10: "Archive state filtering for active
            // versus archived records."
            $table->index('archived_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['archived_at']);
            $table->dropColumn(['account_status', 'archived_at']);
        });
    }
};
