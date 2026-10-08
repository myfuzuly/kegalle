<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('listings', 'poster_whatsapp')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->string('poster_whatsapp', 30)->nullable()->after('poster_phone');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('listings', 'poster_whatsapp')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->dropColumn('poster_whatsapp');
            });
        }
    }
};
