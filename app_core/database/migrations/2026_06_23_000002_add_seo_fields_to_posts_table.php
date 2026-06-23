<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('title');
            }
            if (! Schema::hasColumn('posts', 'meta_description')) {
                $table->string('meta_description', 320)->nullable()->after('excerpt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
            if (Schema::hasColumn('posts', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
        });
    }
};
