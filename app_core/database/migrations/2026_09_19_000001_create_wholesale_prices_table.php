<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wholesale_prices', function (Blueprint $table) {
            $table->id();
            $table->string('commodity', 100);
            $table->string('category', 60)->default('General');
            $table->string('unit', 40)->default('per kg');
            $table->decimal('min_price', 10, 2)->nullable();
            $table->decimal('max_price', 10, 2)->nullable();
            $table->decimal('avg_price', 10, 2)->nullable();
            $table->string('market', 80)->default('Kegalle');
            $table->date('price_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['commodity', 'market', 'price_date']);
            $table->index('price_date');
            $table->index(['is_active', 'price_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wholesale_prices');
    }
};
