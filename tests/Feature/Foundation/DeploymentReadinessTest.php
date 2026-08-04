<?php

declare(strict_types=1);

namespace Tests\Feature\Foundation;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Shared-hosting deployment guarantees.
 *
 * The application is uploaded directly into `public_html/113`, which places the
 * application root inside the document root. AI_DOCS/26_Deployment_Plan.md §7
 * requires additional rules to deny direct access to sensitive directories in
 * exactly that situation.
 *
 * These checks fail loudly if a later phase weakens the protection or
 * reintroduces infrastructure that shared hosting cannot run.
 */
final class DeploymentReadinessTest extends TestCase
{
    #[Test]
    public function an_application_root_htaccess_protects_sensitive_directories(): void
    {
        $path = base_path('.htaccess');

        $this->assertFileExists($path, 'public_html/113 requires an application-root .htaccess.');

        $rules = (string) file_get_contents($path);

        foreach (['app', 'config', 'database', 'storage', 'vendor'] as $directory) {
            $this->assertStringContainsString(
                $directory,
                $rules,
                "The application-root .htaccess must deny access to {$directory}/."
            );
        }
    }

    #[Test]
    public function the_document_root_htaccess_routes_through_the_front_controller(): void
    {
        $rules = (string) file_get_contents(public_path('.htaccess'));

        $this->assertStringContainsString('index.php', $rules);
        $this->assertStringContainsString('RewriteEngine On', $rules);
    }

    #[Test]
    public function no_container_or_orchestration_files_exist(): void
    {
        foreach (['Dockerfile', 'docker-compose.yml', 'compose.yaml', 'Procfile'] as $file) {
            $this->assertFileDoesNotExist(base_path($file));
        }
    }

    #[Test]
    public function no_separate_frontend_build_lives_outside_laravel(): void
    {
        // Vite writes into Laravel's own public/build; nothing is built or
        // served from a directory outside the application.
        $config = (string) file_get_contents(base_path('vite.config.ts'));

        $this->assertStringContainsString("outDir: 'public/build'", $config);
    }

    #[Test]
    public function the_build_requires_no_node_runtime_on_the_server(): void
    {
        // Assets are compiled locally and uploaded. Nothing in the PHP runtime
        // path shells out to node, npm, or vite.
        foreach (['app', 'bootstrap', 'config', 'routes'] as $directory) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(base_path($directory), \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($files as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $contents = (string) file_get_contents($file->getPathname());

                foreach (['npm run', 'npx ', 'node_modules'] as $needle) {
                    $this->assertStringNotContainsString(
                        $needle,
                        $contents,
                        "Runtime code must not invoke the JavaScript toolchain: {$file->getPathname()}"
                    );
                }
            }
        }
    }

    #[Test]
    public function no_filesystem_symlink_is_required(): void
    {
        // `artisan storage:link` must never be part of deployment: shared
        // hosting often disallows symlinks, and a public link would expose
        // stored files by URL, bypassing the authorization every file request
        // must pass (04_Project_Structure.md §5).
        $this->assertSame([], config('filesystems.links'));
        $this->assertFalse(is_link(public_path('storage')));
    }

    #[Test]
    public function the_private_platform_is_not_offered_for_indexing(): void
    {
        $this->assertStringContainsString('Disallow: /', (string) file_get_contents(public_path('robots.txt')));
    }

    #[Test]
    public function the_environment_template_carries_no_real_secret(): void
    {
        $template = (string) file_get_contents(base_path('.env.example'));

        // Credential keys must be present but empty in the committed template.
        foreach (['APP_KEY=', 'DB_PASSWORD=', 'MAIL_PASSWORD='] as $key) {
            $this->assertMatchesRegularExpression(
                '/^'.preg_quote($key, '/').'\s*$/m',
                $template,
                "{$key} must be present and empty in the committed template."
            );
        }
    }
}
