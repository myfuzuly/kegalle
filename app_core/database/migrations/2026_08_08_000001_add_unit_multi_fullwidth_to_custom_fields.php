<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('custom_fields', function (Blueprint $table) {
            $table->string('unit', 20)->nullable()->after('placeholder');
            $table->boolean('multi')->default(false)->after('unit');
            $table->boolean('full_width')->default(false)->after('multi');
        });
    }

    public function down(): void
    {
        Schema::table('custom_fields', function (Blueprint $table) {
            $table->dropColumn(['unit', 'multi', 'full_width']);
        });
    }
};
