<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seed composition root.
 *
 * Seeders provide deliberate local, testing, or approved reference data only.
 * Production seeding must never quietly create accounts, permissions, payment
 * records, or business data (AI_DOCS/04_Project_Structure.md §4).
 *
 * Roles and the permission catalogue are **approved reference data**: both sets
 * are closed and confirmed by 07_Data_Dictionary.md §2 and
 * 09_Permission_Matrix.md, and both are frozen. Seeding them creates no account
 * and grants nothing — a permission exists in the catalogue but is held by
 * nobody until it is assigned.
 */
final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}
