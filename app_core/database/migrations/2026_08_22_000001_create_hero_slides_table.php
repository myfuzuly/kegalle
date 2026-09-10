<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hero_slides')) return;

        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('title')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed the two existing images
        DB::table('hero_slides')->insert([
            ['image' => '/images/kegalle-town.png',         'title' => 'Kegalle Town',         'sort_order' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['image' => '/images/KEGALLE-TOWER.webp',       'title' => 'Kegalle Clock Tower',  'sort_order' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
