<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * The five Version 1 roles — 07_Data_Dictionary.md §2; 08_RBAC.md §3.
 *
 * "Version 1 has exactly five roles: Super Admin, Teacher, Teacher Staff,
 * Student, Parent." Platform staff roles such as Support, Sales, and
 * Accountant are out of scope (07 §2; 09 §12).
 *
 * Each role carries the boundary it operates in, matching the confirmed Role
 * Scope set (33_Validation_Rules.md §3.3).
 *
 * Idempotent: re-running updates nothing and duplicates nothing, because
 * `role_name` is unique in the frozen schema.
 */
final class RoleSeeder extends Seeder
{
    /**
     * @var list<array{role_name: string, role_scope: string, description: string}>
     */
    private const ROLES = [
        [
            'role_name' => 'super_admin',
            'role_scope' => 'platform',
            'description' => 'Owns the Platform at Platform-level scope; does not operate inside a Teacher Workspace as a Teacher.',
        ],
        [
            'role_name' => 'teacher',
            'role_scope' => 'teacher_workspace',
            'description' => 'Operates one completely isolated Teacher Workspace.',
        ],
        [
            'role_name' => 'teacher_staff',
            'role_scope' => 'teacher_workspace',
            'description' => 'Created by a Teacher; holds only permissions assigned by that Teacher inside the creating Teacher Workspace.',
        ],
        [
            'role_name' => 'student',
            'role_scope' => 'student_account',
            'description' => 'Holds one global account; records remain partitioned per Teacher.',
        ],
        [
            'role_name' => 'parent',
            'role_scope' => 'parent_linked_students',
            'description' => 'Monitors linked Students with read-only access everywhere.',
        ],
    ];

    public function run(): void
    {
        foreach (self::ROLES as $role) {
            Role::query()->updateOrCreate(
                ['role_name' => $role['role_name']],
                [
                    'role_scope' => $role['role_scope'],
                    'description' => $role['description'],
                ],
            );
        }
    }
}
