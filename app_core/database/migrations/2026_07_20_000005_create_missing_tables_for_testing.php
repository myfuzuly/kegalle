<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates tables that exist on the production MySQL server but have no
 * migration file. Required so the SQLite in-memory test database has a
 * complete schema that matches production.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Roles ─────────────────────────────────────────────────────────────
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $t) {
                $t->id();
                $t->string('key')->unique();
                $t->string('name');
                $t->text('description')->nullable();
                $t->boolean('is_admin_level')->default(false);
                $t->boolean('is_super')->default(false);
                $t->boolean('is_protected')->default(false);
                $t->integer('sort_order')->default(0);
                $t->json('permissions')->nullable();
                $t->timestamps();
            });
        }

        // ── Offers ────────────────────────────────────────────────────────────
        if (!Schema::hasTable('offers')) {
            Schema::create('offers', function (Blueprint $t) {
                $t->id();
                $t->foreignId('listing_id')->constrained()->cascadeOnDelete();
                $t->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
                $t->decimal('offered_price', 12, 2)->nullable();
                $t->string('payment_method')->default('cod');
                $t->string('status')->default('pending');
                $t->text('message')->nullable();
                $t->text('seller_note')->nullable();
                $t->timestamp('responded_at')->nullable();
                $t->timestamps();
            });
        }

        // ── Phone OTPs ────────────────────────────────────────────────────────
        if (!Schema::hasTable('phone_otps')) {
            Schema::create('phone_otps', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->string('phone');
                $t->string('code', 255);
                $t->boolean('used')->default(false);
                $t->timestamp('expires_at');
                $t->timestamps();
            });
        }

        // ── Deals ─────────────────────────────────────────────────────────────
        if (!Schema::hasTable('deals')) {
            Schema::create('deals', function (Blueprint $t) {
                $t->id();
                $t->foreignId('listing_id')->nullable()->constrained()->nullOnDelete();
                $t->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->decimal('deal_price', 12, 2);
                $t->decimal('original_price', 12, 2)->nullable();
                $t->decimal('discount_percent', 5, 2)->nullable();
                $t->timestamp('starts_at')->nullable();
                $t->timestamp('ends_at')->nullable();
                $t->string('status')->default('active');
                $t->boolean('is_flash')->default(false);
                $t->boolean('is_featured')->default(false);
                $t->text('admin_note')->nullable();
                $t->integer('sold_count')->default(0);
                $t->integer('stock_qty')->nullable();
                $t->timestamps();
            });
        }

        // ── Events ────────────────────────────────────────────────────────────
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $t) {
                $t->id();
                $t->string('title');
                $t->string('slug')->unique();
                $t->text('description')->nullable();
                $t->date('event_date')->nullable();
                $t->timestamp('starts_at')->nullable();
                $t->timestamp('ends_at')->nullable();
                $t->string('location')->nullable();
                $t->string('venue')->nullable();
                $t->decimal('price', 12, 2)->default(0);
                $t->string('event_type')->default('general');
                $t->integer('capacity')->nullable();
                $t->string('organizer_name')->nullable();
                $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
                $t->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
                $t->string('status')->default('pending');
                $t->text('admin_note')->nullable();
                $t->boolean('is_free')->default(false);
                $t->boolean('is_featured')->default(false);
                $t->boolean('is_active')->default(true);
                $t->string('image')->nullable();
                $t->unsignedInteger('views')->default(0);
                $t->timestamps();
            });
        }

        // ── Government Services ───────────────────────────────────────────────
        if (!Schema::hasTable('government_services')) {
            Schema::create('government_services', function (Blueprint $t) {
                $t->id();
                $t->string('title');
                $t->string('slug')->unique();
                $t->text('description')->nullable();
                $t->string('icon')->nullable();
                $t->string('icon_bg_start')->nullable();
                $t->string('icon_bg_end')->nullable();
                $t->string('image')->nullable();
                $t->text('content')->nullable();
                $t->string('phone')->nullable();
                $t->string('email')->nullable();
                $t->text('address')->nullable();
                $t->string('map_url')->nullable();
                $t->integer('sort_order')->default(0);
                $t->boolean('is_active')->default(true);
                $t->timestamps();
            });
        }

        // ── Government Service Items ───────────────────────────────────────────
        if (!Schema::hasTable('government_service_items')) {
            Schema::create('government_service_items', function (Blueprint $t) {
                $t->id();
                $t->foreignId('government_service_id')->constrained()->cascadeOnDelete();
                $t->string('title');
                $t->text('description')->nullable();
                $t->string('icon')->nullable();
                $t->integer('sort_order')->default(0);
                $t->timestamps();
            });
        }

        // ── Brands ────────────────────────────────────────────────────────────
        if (!Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $t) {
                $t->id();
                $t->string('category_group')->nullable();
                $t->string('name');
                $t->string('slug')->unique();
                $t->integer('sort_order')->default(0);
                $t->boolean('is_active')->default(true);
                $t->string('logo')->nullable();
                $t->timestamps();
            });
        }

        // ── Brand Models ──────────────────────────────────────────────────────
        if (!Schema::hasTable('brand_models')) {
            Schema::create('brand_models', function (Blueprint $t) {
                $t->id();
                $t->foreignId('brand_id')->constrained()->cascadeOnDelete();
                $t->string('name');
                $t->string('slug')->unique();
                $t->boolean('is_active')->default(true);
                $t->timestamps();
            });
        }

        // ── Listing Variants ──────────────────────────────────────────────────
        if (!Schema::hasTable('listing_variants')) {
            Schema::create('listing_variants', function (Blueprint $t) {
                $t->id();
                $t->foreignId('listing_id')->constrained()->cascadeOnDelete();
                $t->string('name');
                $t->decimal('price', 12, 2)->nullable();
                $t->integer('sort_order')->default(0);
                $t->timestamps();
            });
        }

        // ── Explore Sub Items ─────────────────────────────────────────────────
        if (!Schema::hasTable('explore_sub_items')) {
            Schema::create('explore_sub_items', function (Blueprint $t) {
                $t->id();
                $t->foreignId('explore_item_id')->constrained()->cascadeOnDelete();
                $t->string('label');
                $t->string('url')->nullable();
                $t->integer('sort_order')->default(0);
                $t->timestamps();
            });
        }

        // ── Saved Searches ────────────────────────────────────────────────────
        if (!Schema::hasTable('saved_searches')) {
            Schema::create('saved_searches', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->string('query')->nullable();
                $t->json('filters')->nullable();
                $t->timestamp('last_notified_at')->nullable();
                $t->timestamps();
            });
        }

        // ── Store Products ────────────────────────────────────────────────────
        if (!Schema::hasTable('store_products')) {
            Schema::create('store_products', function (Blueprint $t) {
                $t->id();
                $t->foreignId('store_id')->constrained()->cascadeOnDelete();
                $t->string('name');
                $t->string('slug')->unique();
                $t->text('description')->nullable();
                $t->decimal('price', 12, 2)->default(0);
                $t->string('status')->default('active');
                $t->string('image')->nullable();
                $t->integer('stock_qty')->nullable();
                $t->timestamps();
            });
        }

        // ── Price Drop Alerts ─────────────────────────────────────────────────
        if (!Schema::hasTable('price_drop_alerts')) {
            Schema::create('price_drop_alerts', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->foreignId('listing_id')->constrained()->cascadeOnDelete();
                $t->timestamps();
                $t->unique(['user_id', 'listing_id']);
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'price_drop_alerts', 'store_products', 'saved_searches',
            'explore_sub_items', 'listing_variants', 'brand_models',
            'brands', 'government_service_items', 'government_services',
            'events', 'deals', 'phone_otps', 'offers', 'roles',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
