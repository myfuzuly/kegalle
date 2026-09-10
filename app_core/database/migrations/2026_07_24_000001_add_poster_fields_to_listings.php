<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            // Allow admin-posted classifieds without a user account
            $table->string('poster_name', 120)->nullable()->after('user_id');
            $table->string('poster_phone', 30)->nullable()->after('poster_name');

            // Make user_id nullable so no-account classifieds can be stored
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['poster_name', 'poster_phone']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
