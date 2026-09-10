<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('phone')->nullable();
            $t->string('password');
            $t->string('role')->default('user');
            $t->string('status')->default('active');
            $t->rememberToken();
            $t->timestamps();
        });

        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('parent_id')->nullable();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('type')->default('both');
            $t->string('icon')->nullable();
            $t->text('description')->nullable();
            $t->integer('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('custom_field_groups', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('custom_fields', function (Blueprint $t) {
            $t->id();
            $t->foreignId('group_id')->nullable()->constrained('custom_field_groups')->nullOnDelete();
            $t->string('label');
            $t->string('name');
            $t->string('type');
            $t->json('options')->nullable();
            $t->string('placeholder')->nullable();
            $t->boolean('is_required')->default(false);
            $t->boolean('is_searchable')->default(false);
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('category_custom_field', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->constrained()->cascadeOnDelete();
            $t->foreignId('custom_field_id')->constrained()->cascadeOnDelete();
            $t->boolean('is_required')->default(false);
            $t->boolean('show_in_filter')->default(false);
            $t->boolean('show_in_list')->default(false);
            $t->integer('sort_order')->default(0);
        });

        Schema::create('membership_plans', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->decimal('price', 12, 2)->default(0);
            $t->integer('duration_days')->default(30);
            $t->integer('ad_limit')->default(0);
            $t->integer('product_limit')->default(0);
            $t->integer('store_limit')->default(1);
            $t->integer('featured_quota')->default(0);
            $t->json('allowed_categories')->nullable();
            $t->json('features')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('stores', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('membership_plan_id')->nullable()->constrained('membership_plans')->nullOnDelete();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('logo')->nullable();
            $t->string('banner')->nullable();
            $t->text('description')->nullable();
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->string('whatsapp')->nullable();
            $t->text('address')->nullable();
            $t->string('city')->nullable();
            $t->json('opening_hours')->nullable();
            $t->string('status')->default('pending');
            $t->boolean('is_featured')->default(false);
            $t->timestamp('membership_expires_at')->nullable();
            $t->timestamps();
        });

        Schema::create('listings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $t->string('ad_type')->default('sell');
            $t->string('type')->default('classified');
            $t->string('title');
            $t->string('slug')->unique();
            $t->longText('description')->nullable();
            $t->decimal('price', 14, 2)->nullable();
            $t->string('currency')->default('LKR');
            $t->string('condition')->nullable();
            $t->string('location')->nullable();
            $t->string('status')->default('pending');
            $t->boolean('is_featured')->default(false);
            $t->boolean('is_top')->default(false);
            $t->boolean('is_urgent')->default(false);
            $t->timestamp('bumped_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->unsignedBigInteger('views')->default(0);
            $t->timestamps();
        });

        Schema::create('listing_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $t->string('path');
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('listing_field_values', function (Blueprint $t) {
            $t->id();
            $t->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $t->foreignId('custom_field_id')->constrained()->cascadeOnDelete();
            $t->text('value')->nullable();
            $t->timestamps();
        });

        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('membership_plan_id')->nullable()->constrained()->nullOnDelete();
            $t->string('gateway')->default('offline');
            $t->string('reference')->nullable();
            $t->decimal('amount', 12, 2);
            $t->string('currency')->default('LKR');
            $t->string('status')->default('pending');
            $t->json('meta')->nullable();
            $t->timestamps();
        });

        if (! Schema::hasTable('chat_threads')) {
            Schema::create('chat_threads', function (Blueprint $t) {
                $t->id();
                $t->foreignId('listing_id')->nullable()->constrained()->nullOnDelete();
                $t->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
                $t->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
                $t->string('status')->default('open');
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $t) {
                $t->id();
                $t->foreignId('thread_id')->constrained('chat_threads')->cascadeOnDelete();
                $t->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $t->text('message')->nullable();
                $t->string('attachment_path')->nullable();
                $t->timestamp('read_at')->nullable();
                $t->timestamps();
            });
        }

        Schema::create('reviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('store_id')->nullable()->constrained()->cascadeOnDelete();
            $t->foreignId('listing_id')->nullable()->constrained()->cascadeOnDelete();
            $t->tinyInteger('rating');
            $t->text('comment')->nullable();
            $t->string('status')->default('pending');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        $tables = [
            'reviews', 'chat_messages', 'chat_threads', 'payments',
            'listing_field_values', 'listing_images', 'listings',
            'stores', 'membership_plans', 'category_custom_field',
            'custom_fields', 'custom_field_groups', 'categories', 'users',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
