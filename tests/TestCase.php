<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Base test case.
 *
 * Tests must never bypass server authorization to make assertions easier, and
 * no suite may treat frontend visibility as a substitute for backend access
 * enforcement (AI_DOCS/04_Project_Structure.md §9).
 */
abstract class TestCase extends BaseTestCase
{
    //
}
