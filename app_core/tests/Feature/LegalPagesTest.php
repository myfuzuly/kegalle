<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Confirms all legal / policy pages load correctly and contain expected content.
 */
class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    // ── Status codes ──────────────────────────────────────────────────────────

    public function test_privacy_policy_returns_200(): void
    {
        $this->get('/privacy-policy')->assertStatus(200);
    }

    public function test_terms_and_conditions_returns_200(): void
    {
        $this->get('/terms-and-conditions')->assertStatus(200);
    }

    public function test_cookie_policy_returns_200(): void
    {
        $this->get('/cookie-policy')->assertStatus(200);
    }

    public function test_faq_returns_200(): void
    {
        $this->get('/faq')->assertStatus(200);
    }

    public function test_contact_page_returns_200(): void
    {
        $this->get('/contact-us')->assertStatus(200);
    }

    // ── Content spot-checks ───────────────────────────────────────────────────

    public function test_privacy_policy_mentions_kegalle(): void
    {
        $this->get('/privacy-policy')->assertSeeText('Kegalle');
    }

    public function test_cookie_policy_mentions_essential_cookies(): void
    {
        $response = $this->get('/cookie-policy');
        $response->assertStatus(200);
        // Page must explain what cookies are used
        $response->assertSeeText('cookie', false);
    }

    public function test_terms_page_has_content(): void
    {
        $response = $this->get('/terms-and-conditions');
        $response->assertStatus(200);
        $this->assertGreaterThan(500, strlen($response->getContent()), 'T&C page looks too short');
    }

    // ── Footer links (sitemap inclusion) ─────────────────────────────────────

    public function test_cookie_policy_is_in_sitemap(): void
    {
        $sitemap = $this->get('/sitemap-pages.xml');
        $sitemap->assertStatus(200);
        $sitemap->assertSee('cookie-policy');
    }

    public function test_privacy_policy_is_in_sitemap(): void
    {
        $this->get('/sitemap-pages.xml')
            ->assertStatus(200)
            ->assertSee('privacy-policy');
    }

    // ── Sitemap index ─────────────────────────────────────────────────────────

    public function test_sitemap_index_returns_xml(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type');
        $this->assertStringContainsString('xml', $response->headers->get('Content-Type', ''));
    }

    public function test_all_sitemap_sub_indexes_return_200(): void
    {
        $sitemaps = [
            '/sitemap-pages.xml',
            '/sitemap-listings.xml',
            '/sitemap-stores.xml',
            '/sitemap-events.xml',
            '/sitemap-blog.xml',
            '/sitemap-towns.xml',
        ];
        foreach ($sitemaps as $url) {
            $this->get($url)->assertStatus(200, "Sitemap $url failed");
        }
    }

    // ── robots.txt ────────────────────────────────────────────────────────────

    public function test_robots_txt_is_accessible(): void
    {
        $response = $this->get('/robots.txt');
        $response->assertStatus(200);
        $response->assertSee('User-agent');
    }

    public function test_robots_txt_blocks_admin(): void
    {
        $content = $this->get('/robots.txt')->getContent();
        $this->assertStringContainsString('/admin/', $content);
        $this->assertStringContainsString('/dashboard/', $content);
    }

    public function test_robots_txt_points_to_sitemap(): void
    {
        $content = $this->get('/robots.txt')->getContent();
        $this->assertStringContainsString('sitemap.xml', $content);
    }
}
