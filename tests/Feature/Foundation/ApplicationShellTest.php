<?php

declare(strict_types=1);

namespace Tests\Feature\Foundation;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Application shell delivery.
 *
 * Laravel serves one HTML document and React Router owns client-side
 * navigation, so a deep link or a refresh on any client route must still
 * resolve to the shell — while the /api/v1 surface keeps returning JSON
 * (AI_DOCS/04_Project_Structure.md §2; 12_Frontend_Architecture.md §4).
 */
final class ApplicationShellTest extends TestCase
{
    #[Test]
    public function the_root_path_serves_the_application_shell(): void
    {
        $this->get('/')->assertOk()->assertViewIs('app');
    }

    #[Test]
    public function client_side_deep_links_resolve_to_the_shell(): void
    {
        // Refreshing on a nested client route must not 404.
        foreach (['/login', '/teacher-workspace/groups', '/parent/students/5'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertViewIs('app');
        }
    }

    #[Test]
    public function the_shell_never_shadows_the_api_surface(): void
    {
        // An unknown API route must return the JSON envelope, not the HTML shell.
        $response = $this->getJson('/api/v1/nope');

        $response->assertNotFound();
        $this->assertJson($response->getContent() ?: '');
    }

    #[Test]
    public function the_shell_declares_language_and_direction(): void
    {
        // Arabic is the default language and renders right-to-left.
        $this->get('/')
            ->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSee('dir="rtl"', false);
    }

    #[Test]
    public function the_shell_switches_direction_for_english(): void
    {
        $this->withHeader('Accept-Language', 'en')
            ->get('/')
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('dir="ltr"', false);
    }

    #[Test]
    public function the_shell_exposes_no_authorization_state(): void
    {
        // The shell carries no user, role, or permission data; everything is
        // fetched through the authenticated API.
        $content = (string) $this->get('/')->getContent();

        foreach (['role', 'permission', 'teacher_workspace'] as $leak) {
            $this->assertStringNotContainsString($leak, strtolower($content));
        }
    }
}
