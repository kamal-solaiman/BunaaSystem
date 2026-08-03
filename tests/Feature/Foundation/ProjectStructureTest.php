<?php

declare(strict_types=1);

namespace Tests\Feature\Foundation;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Structural guarantees.
 *
 * Locks in the single-application layout and the confirmed driver decisions so
 * a later phase cannot quietly reintroduce a separate frontend project or swap
 * an infrastructure choice that shared hosting cannot run
 * (AI_DOCS/04_Project_Structure.md; 35_Environment_Configuration.md §10.4).
 */
final class ProjectStructureTest extends TestCase
{
    #[Test]
    public function the_project_is_a_single_laravel_application(): void
    {
        $forbidden = ['frontend', 'backend', 'laravel_app', 'deployment'];

        foreach ($forbidden as $directory) {
            $this->assertDirectoryDoesNotExist(
                base_path($directory),
                "The project must not contain a separate {$directory}/ directory."
            );
        }
    }

    #[Test]
    public function react_lives_inside_laravel_resources(): void
    {
        $this->assertDirectoryExists(resource_path('js'));
        $this->assertDirectoryExists(resource_path('js/features'));
        $this->assertFileExists(resource_path('js/app/main.tsx'));
    }

    #[Test]
    public function backend_features_use_canonical_names(): void
    {
        // Canonical terminology is mandatory: Educational Grade (never Class),
        // Lesson (never Course), Subscription for Flow A, payment status for
        // Flow B (04_Project_Structure.md §11).
        $expected = [
            'Authentication', 'Authorization', 'PlatformAdministration', 'TeacherWorkspace',
            'EducationalGrades', 'Groups', 'Students', 'Parents', 'Attendance', 'Homework',
            'Lessons', 'Exams', 'Reports', 'Payments', 'Subscriptions', 'Users', 'Settings',
            'Files', 'Archive', 'AuditLog',
        ];

        foreach ($expected as $feature) {
            $this->assertDirectoryExists(
                app_path("Features/{$feature}"),
                "Missing backend feature directory: {$feature}."
            );
        }

        foreach (['Classes', 'Courses', 'Tenants'] as $nonCanonical) {
            $this->assertDirectoryDoesNotExist(app_path("Features/{$nonCanonical}"));
        }
    }

    #[Test]
    public function frontend_features_use_canonical_kebab_case_names(): void
    {
        $expected = [
            'authentication', 'platform-administration', 'teacher-workspace',
            'educational-grades', 'groups', 'students', 'parents', 'attendance',
            'homework', 'lessons', 'exams', 'reports', 'payments', 'subscriptions',
            'users', 'settings', 'files', 'archive', 'audit-log',
        ];

        foreach ($expected as $feature) {
            $this->assertDirectoryExists(
                resource_path("js/features/{$feature}"),
                "Missing frontend feature directory: {$feature}."
            );
        }
    }

    #[Test]
    public function confirmed_infrastructure_drivers_are_the_committed_defaults(): void
    {
        // Fixed by decisions D-040…D-043; all are cPanel-compatible and require
        // no daemon or external service.
        //
        // The test environment deliberately overrides these for isolation, so
        // the assertion targets the committed defaults in the environment
        // template rather than the resolved runtime value.
        $template = (string) file_get_contents(base_path('.env.example'));

        $expected = [
            'SESSION_DRIVER' => 'database',
            'CACHE_STORE' => 'file',
            'QUEUE_CONNECTION' => 'database',
            'FILESYSTEM_DISK' => 'public',
            'DB_CONNECTION' => 'mysql',
            'MAIL_MAILER' => 'smtp',
        ];

        foreach ($expected as $variable => $value) {
            $this->assertStringContainsString(
                "{$variable}={$value}",
                $template,
                "The environment template must declare {$variable}={$value}."
            );
        }
    }

    #[Test]
    public function out_of_scope_infrastructure_is_not_configured(): void
    {
        // Version 1 must not require Docker, Redis, S3, or WebSockets
        // (03_System_Architecture.md §4.1).
        $template = (string) file_get_contents(base_path('.env.example'));

        foreach (['REDIS_', 'AWS_', 'PUSHER_', 'DOCKER'] as $forbidden) {
            $this->assertStringNotContainsString($forbidden, $template);
        }

        $this->assertFileDoesNotExist(base_path('Dockerfile'));
        $this->assertFileDoesNotExist(base_path('docker-compose.yml'));
    }

    #[Test]
    public function mysql_uses_innodb_with_utf8mb4(): void
    {
        $this->assertSame('InnoDB', config('database.connections.mysql.engine'));
        $this->assertSame('utf8mb4', config('database.connections.mysql.charset'));
        $this->assertSame('utf8mb4_unicode_ci', config('database.connections.mysql.collation'));
    }
}
