<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Verifies that every public-facing response carries the full set of
 * security headers added by SecurityHeaders middleware and CSP in index.php.
 */
class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    private function assertSecurityHeaders($response): void
    {
        $response->assertHeader('Content-Security-Policy');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options');
        $response->assertHeader('Referrer-Policy');
    }

    // ── CSP structure ─────────────────────────────────────────────────────────

    public function test_homepage_has_csp_header(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertHeader('Content-Security-Policy');
    }

    public function test_csp_contains_required_directives(): void
    {
        $csp = $this->get('/')->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("base-uri 'self'", $csp);
        $this->assertStringContainsString("form-action 'self'", $csp);
    }

    public function test_csp_allows_google_fonts(): void
    {
        $csp = $this->get('/')->headers->get('Content-Security-Policy');
        $this->assertStringContainsString('fonts.googleapis.com', $csp);
        $this->assertStringContainsString('fonts.gstatic.com', $csp);
    }

    // ── Other security headers ───────────────────────────────────────────────

    public function test_homepage_has_x_content_type_options(): void
    {
        $this->get('/')->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_homepage_has_x_frame_options(): void
    {
        $response = $this->get('/');
        $this->assertNotNull(
            $response->headers->get('X-Frame-Options'),
            'X-Frame-Options header is missing'
        );
    }

    public function test_headers_present_on_listings_page(): void
    {
        $this->assertSecurityHeaders($this->get('/listings'));
    }

    public function test_headers_present_on_legal_pages(): void
    {
        $this->assertSecurityHeaders($this->get('/privacy-policy'));
        $this->assertSecurityHeaders($this->get('/terms-and-conditions'));
        $this->assertSecurityHeaders($this->get('/cookie-policy'));
    }

    public function test_headers_present_on_auth_pages(): void
    {
        $this->assertSecurityHeaders($this->get('/login'));
        $this->assertSecurityHeaders($this->get('/register'));
    }

    // ── CSP does NOT expose GA/Pixel when IDs not configured ─────────────────

    public function test_csp_excludes_ga_when_not_configured(): void
    {
        config(['services.analytics.ga_id' => '']);
        $csp = $this->get('/')->headers->get('Content-Security-Policy');
        // googletagmanager.com must not appear if GA is not configured
        $this->assertStringNotContainsString('googletagmanager.com', $csp ?? '');
    }

    // ── No cache header on auth-gated pages ──────────────────────────────────

    public function test_dashboard_redirects_carry_no_cache_header(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
        // Redirect response should not be pagecached
        $this->assertNotEquals('HIT', $response->headers->get('X-Cache'));
    }
}
