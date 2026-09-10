<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // price_alerts — used by UserActionController & console.php
        if (!Schema::hasTable('price_alerts')) {
            Schema::create('price_alerts', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->foreignId('listing_id')->constrained()->cascadeOnDelete();
                $t->decimal('price_when_set', 12, 2)->nullable();
                $t->timestamps();
                $t->unique(['user_id', 'listing_id']);
            });
        }

        // saved_searches — fix missing columns used by console.php + controller
        if (Schema::hasTable('saved_searches')) {
            if (!Schema::hasColumn('saved_searches', 'params')) {
                Schema::table('saved_searches', function (Blueprint $t) {
                    $t->text('params')->nullable()->after('user_id');
                });
            }
            if (!Schema::hasColumn('saved_searches', 'last_sent_at')) {
                Schema::table('saved_searches', function (Blueprint $t) {
                    $t->timestamp('last_sent_at')->nullable()->after('last_notified_at');
                });
            }
        } elseif (!Schema::hasTable('saved_searches')) {
            Schema::create('saved_searches', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->text('params')->nullable();
                $t->string('query')->nullable();
                $t->json('filters')->nullable();
                $t->timestamp('last_notified_at')->nullable();
                $t->timestamp('last_sent_at')->nullable();
                $t->timestamps();
            });
        }

        // push_subscriptions
        if (!Schema::hasTable('push_subscriptions')) {
            Schema::create('push_subscriptions', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->string('endpoint', 500)->unique();
                $t->string('p256dh', 200)->nullable();
                $t->string('auth_token', 50)->nullable();
                $t->timestamps();
            });
        }

        // push_notifications
        if (!Schema::hasTable('push_notifications')) {
            Schema::create('push_notifications', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $t->string('title');
                $t->text('body')->nullable();
                $t->string('url')->nullable();
                $t->timestamp('shown_at')->nullable();
                $t->timestamps();
            });
        }

        // users — add phone_verified_at if missing
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'phone_verified_at')) {
            Schema::table('users', function (Blueprint $t) {
                $t->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            });
        }

        // users — add is_verified for verified seller badge
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_verified')) {
            Schema::table('users', function (Blueprint $t) {
                $t->boolean('is_verified')->default(false)->after('account_type');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('price_alerts');
    }
};
