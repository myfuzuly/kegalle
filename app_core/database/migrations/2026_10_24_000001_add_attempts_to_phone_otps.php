<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('phone_otps') && ! Schema::hasColumn('phone_otps', 'attempts')) {
            Schema::table('phone_otps', function (Blueprint $table) {
                $table->unsignedTinyInteger('attempts')->default(0);
            });
        }
    }

    public function down(): void
    {
    }
};
