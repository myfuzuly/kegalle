<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'account_type')) {
                $table->string('account_type')->default('user')->after('status');
            }

            if (! Schema::hasColumn('users', 'social_provider')) {
                $table->string('social_provider')->nullable()->after('account_type');
            }

            if (! Schema::hasColumn('users', 'social_provider_id')) {
                $table->string('social_provider_id')->nullable()->after('social_provider');
            }

            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('social_provider_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['avatar', 'social_provider_id', 'social_provider'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
