<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->string('slug', 120)->unique()->nullable();
                $table->string('category_group', 100)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('brand_models')) {
            Schema::create('brand_models', function (Blueprint $table) {
                $table->id();
                $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
                $table->string('name', 100);
                $table->string('slug', 120)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->unique(['brand_id', 'name']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_models');
        Schema::dropIfExists('brands');
    }
};
