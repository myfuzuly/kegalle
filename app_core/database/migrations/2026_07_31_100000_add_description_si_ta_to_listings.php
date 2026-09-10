<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->longText('description_si')->nullable()->after('description');
            $table->longText('description_ta')->nullable()->after('description_si');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['description_si', 'description_ta']);
        });
    }
};
