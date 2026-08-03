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
 * Foundation phase: nothing is seeded. Reference data such as the role and
 * permission catalog arrives with the authorization phase, from the confirmed
 * permission matrix.
 */
final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        //
    }
}
