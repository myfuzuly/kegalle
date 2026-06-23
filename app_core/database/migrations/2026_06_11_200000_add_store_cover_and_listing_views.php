<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (! Schema::hasColumn('stores', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('logo');
            }if (! Schema::hasColumn('stores', 'is_verified')) {
                $table->boolean('is_verified')->default(true)->after('status');
            }
        });
        Schema::table('listings', function (Blueprint $table) {
            if (! Schema::hasColumn('listings', 'views')) {
                $table->unsignedBigInteger('views')->default(0)->after('status');
            }
        });
    }

    public function down(): void {}
};
