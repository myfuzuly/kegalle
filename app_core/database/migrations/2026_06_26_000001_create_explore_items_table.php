<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('explore_items')) {
            return;
        }

        Schema::create('explore_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->string('gradient_start')->default('#1B5E20');
            $table->string('gradient_end')->default('#388E3C');
            $table->text('items')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_label')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('explore_items');
    }
};
