<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_admin_level')->default(false);
                $table->boolean('is_super')->default(false);
                $table->boolean('is_protected')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (DB::table('roles')->count() === 0) {
            DB::table('roles')->insert([
                [
                    'key' => 'user',
                    'name' => 'User',
                    'description' => 'Regular buyer/seller account with dashboard access only.',
                    'is_admin_level' => false,
                    'is_super' => false,
                    'is_protected' => true,
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'admin',
                    'name' => 'Admin',
                    'description' => 'Can access the Super Admin panel and manage marketplace content.',
                    'is_admin_level' => true,
                    'is_super' => false,
                    'is_protected' => true,
                    'sort_order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'super_admin',
                    'name' => 'Super Admin',
                    'description' => 'Full control, including managing other admins, roles and site settings.',
                    'is_admin_level' => true,
                    'is_super' => true,
                    'is_protected' => true,
                    'sort_order' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
