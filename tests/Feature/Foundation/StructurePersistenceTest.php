<?php

declare(strict_types=1);

namespace Tests\Feature\Foundation;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The documented structure must survive a fresh clone.
 *
 * Git does not track empty directories. Without a tracked placeholder, the
 * Feature-Based Architecture defined by AI_DOCS/04_Project_Structure.md §2–§3
 * would exist only on the machine that created it and would vanish for every
 * other clone and every CI run.
 */
final class StructurePersistenceTest extends TestCase
{
    /** @return list<string> */
    private function backendFeatures(): array
    {
        return [
            'Authentication', 'Authorization', 'PlatformAdministration', 'TeacherWorkspace',
            'EducationalGrades', 'Groups', 'Students', 'Parents', 'Attendance', 'Homework',
            'Lessons', 'Exams', 'Reports', 'Payments', 'Subscriptions', 'Users', 'Settings',
            'Files', 'Archive', 'AuditLog',
        ];
    }

    /** @return list<string> */
    private function frontendFeatures(): array
    {
        return [
            'authentication', 'platform-administration', 'teacher-workspace', 'educational-grades',
            'groups', 'students', 'parents', 'attendance', 'homework', 'lessons', 'exams',
            'reports', 'payments', 'subscriptions', 'users', 'settings', 'files', 'archive',
            'audit-log',
        ];
    }

    #[Test]
    public function every_backend_feature_directory_is_version_controlled(): void
    {
        foreach ($this->backendFeatures() as $feature) {
            $this->assertTrue(
                $this->hasTrackedFile(app_path("Features/{$feature}")),
                "app/Features/{$feature} would not survive a fresh clone."
            );
        }
    }

    #[Test]
    public function every_frontend_feature_directory_is_version_controlled(): void
    {
        foreach ($this->frontendFeatures() as $feature) {
            $this->assertTrue(
                $this->hasTrackedFile(resource_path("js/features/{$feature}")),
                "resources/js/features/{$feature} would not survive a fresh clone."
            );
        }
    }

    #[Test]
    public function shared_frontend_boundaries_are_version_controlled(): void
    {
        foreach (['assets', 'auth', 'components/primitives', 'components/shared', 'layouts'] as $boundary) {
            $this->assertTrue(
                $this->hasTrackedFile(resource_path("js/{$boundary}")),
                "resources/js/{$boundary} would not survive a fresh clone."
            );
        }
    }

    /**
     * A directory survives a clone only if it contains at least one file.
     */
    private function hasTrackedFile(string $directory): bool
    {
        if (! is_dir($directory)) {
            return false;
        }

        foreach ((array) scandir($directory) as $entry) {
            if ($entry !== '.' && $entry !== '..') {
                return true;
            }
        }

        return false;
    }
}
