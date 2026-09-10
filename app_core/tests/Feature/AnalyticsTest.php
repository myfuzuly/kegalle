<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Verifies that GA and Meta Pixel scripts are injected into the HTML
 * when their respective env vars are configured, and absent when not.
 */
class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    // ── Google Analytics ─────────────────────────────────────────────────────

    public function test_ga_script_present_when_id_configured(): void
    {
        config(['services.analytics.ga_id' => 'G-TESTID1234']);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('googletagmanager.com', false);
        $response->assertSee('G-TESTID1234', false);
    }

    public function test_ga_script_absent_when_id_not_configured(): void
    {
        config(['services.analytics.ga_id' => '']);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('googletagmanager.com', false);
    }

    public function test_ga_gtag_config_call_present(): void
    {
        config(['services.analytics.ga_id' => 'G-TESTID1234']);

        $html = $this->get('/')->getContent();
        $this->assertStringContainsString("gtag('config', 'G-TESTID1234')", $html);
    }

    public function test_ga_loads_on_all_public_pages(): void
    {
        config(['services.analytics.ga_id' => 'G-TESTID1234']);

        $pages = ['/', '/listings', '/stores', '/blog', '/categories'];
        foreach ($pages as $page) {
            $this->get($page)
                ->assertStatus(200)
                ->assertSee('G-TESTID1234', false);
        }
    }

    // ── Meta Pixel ────────────────────────────────────────────────────────────

    public function test_pixel_script_present_when_id_configured(): void
    {
        config(['services.analytics.pixel_id' => '123456789012345']);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('fbevents.js', false);
        $response->assertSee('123456789012345', false);
    }

    public function test_pixel_script_absent_when_id_not_configured(): void
    {
        config(['services.analytics.pixel_id' => '']);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('fbevents.js', false);
    }

    public function test_pixel_noscript_fallback_present(): void
    {
        config(['services.analytics.pixel_id' => '123456789012345']);

        $html = $this->get('/')->getContent();
        $this->assertStringContainsString('facebook.com/tr', $html);
    }

    // ── CSP allows analytics when configured ─────────────────────────────────

    public function test_csp_includes_ga_domains_when_ga_configured(): void
    {
        config(['services.analytics.ga_id' => 'G-TESTID1234']);

        $csp = $this->get('/')->headers->get('Content-Security-Policy', '');
        $this->assertStringContainsString('googletagmanager.com', $csp);
        $this->assertStringContainsString('google-analytics.com', $csp);
    }

    public function test_csp_includes_pixel_domains_when_pixel_configured(): void
    {
        config(['services.analytics.pixel_id' => '123456789012345']);

        $csp = $this->get('/')->headers->get('Content-Security-Policy', '');
        $this->assertStringContainsString('connect.facebook.net', $csp);
    }
}
