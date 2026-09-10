<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('phone_otps')) {
            return;
        }

        Schema::table('phone_otps', function (Blueprint $table) {
            $table->string('code', 255)->change();
        });
    }

    public function down(): void
    {
        Schema::table('phone_otps', function (Blueprint $table) {
            $table->string('code', 6)->change();
        });
    }
};
