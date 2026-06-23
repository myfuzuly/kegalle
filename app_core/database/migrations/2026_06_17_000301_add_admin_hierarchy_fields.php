<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('categories') && ! Schema::hasColumn('categories', 'parent_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id')->index();
            });
        }
        if (Schema::hasTable('listings') && ! Schema::hasColumn('listings', 'location_id')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->unsignedBigInteger('location_id')->nullable()->after('category_id')->index();
            });
        }
        if (Schema::hasTable('listings') && ! Schema::hasColumn('listings', 'created_by_admin_id')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by_admin_id')->nullable()->after('user_id')->index();
            });
        }
    }

    public function down(): void {}
};
