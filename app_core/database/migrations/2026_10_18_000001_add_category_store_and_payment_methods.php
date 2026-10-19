<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Production already has these (created by hand); guarded so fresh DBs and CI match it.
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('category_store')) {
            Schema::create('category_store', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->foreignId('store_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['category_id', 'store_id']);
            });
        }
        if (! Schema::hasColumn('listings', 'payment_methods')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->json('payment_methods')->nullable();
            });
        }
    }

    public function down(): void
    {
    }
};
