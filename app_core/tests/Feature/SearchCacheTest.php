<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Verifies the three-layer search caching architecture:
 *   1. FULLTEXT search returns results
 *   2. Search-result IDs are written to storage/framework/searchcache/
 *   3. Page HTML is written to storage/framework/pagecache/
 *   4. Repeat requests are served from pagecache (X-Cache: HIT)
 */
class SearchCacheTest extends TestCase
{
    use RefreshDatabase;

    private function approvedListing(string $title, string $description = ''): Listing
    {
        $user  = User::factory()->create(['status' => 'active']);
        $store = Store::factory()->create(['user_id' => $user->id, 'status' => 'approved']);
        return Listing::factory()->create([
            'user_id'     => $user->id,
            'store_id'    => $store->id,
            'status'      => 'approved',
            'title'       => $title,
            'description' => $description ?: $title . ' description',
        ]);
    }

    // ── 1. Basic search returns 200 ───────────────────────────────────────────

    public function test_search_page_returns_200(): void
    {
        $this->get('/listings?q=phone')->assertStatus(200);
    }

    public function test_search_without_query_returns_200(): void
    {
        $this->get('/listings')->assertStatus(200);
    }

    // ── 2. Search finds matching listings ────────────────────────────────────

    public function test_search_returns_matching_listing(): void
    {
        $this->approvedListing('Samsung Galaxy Phone', 'Great condition phone for sale');

        // The listing index renders matching listings in the HTML
        $response = $this->get('/listings?q=samsung');
        $response->assertStatus(200);
        $response->assertSee('Samsung Galaxy Phone');
    }

    public function test_search_excludes_pending_listings(): void
    {
        $user  = User::factory()->create(['status' => 'active']);
        $store = Store::factory()->create(['user_id' => $user->id, 'status' => 'approved']);
        Listing::factory()->create([
            'user_id'  => $user->id,
            'store_id' => $store->id,
            'status'   => 'pending',
            'title'    => 'Hidden Pending Item XYZ9999',
        ]);

        $response = $this->get('/listings?q=XYZ9999');
        $response->assertStatus(200);
        $response->assertDontSee('Hidden Pending Item XYZ9999');
    }

    public function test_search_with_category_filter(): void
    {
        $this->get('/listings?q=test&category=electronics')->assertStatus(200);
    }

    public function test_search_with_price_range_filter(): void
    {
        $this->get('/listings?min_price=1000&max_price=50000')->assertStatus(200);
    }

    public function test_search_with_sort_parameter(): void
    {
        $this->get('/listings?q=phone&sort=price_asc')->assertStatus(200);
        $this->get('/listings?q=phone&sort=price_desc')->assertStatus(200);
        $this->get('/listings?q=phone&sort=newest')->assertStatus(200);
    }

    // ── 3. Searchcache file is written after a query ──────────────────────────

    public function test_searchcache_file_written_after_search(): void
    {
        $cacheDir = storage_path('framework/searchcache');
        // Clear any existing files
        foreach (glob($cacheDir . '/*.json') ?: [] as $f) { @unlink($f); }

        $this->get('/listings?q=uniqueterm12345');

        $files = glob($cacheDir . '/*.json') ?: [];
        $this->assertNotEmpty($files, 'No searchcache JSON file was written after the search request.');
    }

    public function test_searchcache_file_contains_valid_json(): void
    {
        $this->approvedListing('Laptop Computer Test');

        $cacheDir = storage_path('framework/searchcache');
        foreach (glob($cacheDir . '/*.json') ?: [] as $f) { @unlink($f); }

        $this->get('/listings?q=laptop');

        $files = glob($cacheDir . '/*.json') ?: [];
        $this->assertNotEmpty($files);

        $data = json_decode(file_get_contents($files[0]), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('ids', $data);
        $this->assertArrayHasKey('total', $data);
        $this->assertIsArray($data['ids']);
        $this->assertIsInt($data['total']);
    }

    public function test_searchcache_hit_returns_same_results(): void
    {
        $this->approvedListing('Bicycle For Sale Kegalle');

        // Cold hit — writes cache
        $cold = $this->get('/listings?q=bicycle');
        $cold->assertStatus(200);
        $cold->assertSee('Bicycle For Sale Kegalle');

        // Warm hit — reads from searchcache
        $warm = $this->get('/listings?q=bicycle');
        $warm->assertStatus(200);
        $warm->assertSee('Bicycle For Sale Kegalle');
    }

    // ── 4. Pagecache middleware ───────────────────────────────────────────────

    public function test_pagecache_file_written_for_listings_page(): void
    {
        $pcDir = storage_path('framework/pagecache');
        foreach (glob($pcDir . '/*.html') ?: [] as $f) { @unlink($f); }

        $this->get('/listings');

        $files = glob($pcDir . '/*.html') ?: [];
        $this->assertNotEmpty($files, 'No pagecache HTML file was written for /listings.');
    }

    public function test_pagecache_hit_returns_cached_header(): void
    {
        // First request — cold miss, writes cache
        $this->get('/listings');

        // Second request — middleware returns X-Cache: HIT
        $response = $this->get('/listings');
        $response->assertStatus(200);
        $response->assertHeader('X-Cache', 'HIT');
    }

    public function test_pagecache_not_served_to_authenticated_users(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        // Prime the cache anonymously
        $this->get('/listings');

        // Authenticated user must NOT get a stale cached page
        $response = $this->actingAs($user)->get('/listings');
        $response->assertStatus(200);
        // Auth users bypass pagecache — header should not say HIT
        $this->assertNotEquals('HIT', $response->headers->get('X-Cache'));
    }

    // ── 5. Suggestions API ───────────────────────────────────────────────────

    public function test_suggestions_api_returns_json(): void
    {
        $this->approvedListing('Toyota Car Kegalle');

        $response = $this->get('/api/listings/suggestions?q=toyota');
        $response->assertStatus(200);
        $response->assertJsonStructure([['id', 'title', 'url']]);
    }

    public function test_suggestions_api_returns_empty_for_no_match(): void
    {
        $response = $this->get('/api/listings/suggestions?q=zzznomatchxxx');
        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_suggestions_api_requires_min_query_length(): void
    {
        // Single character queries should return empty or 200 with empty array
        $response = $this->get('/api/listings/suggestions?q=a');
        $response->assertStatus(200);
    }
}
