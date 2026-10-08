<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = [
        'brands' => ['category_id' => 'ubig'],
        'deals' => ['title' => 'string', 'description' => 'text', 'poster_type' => 'string', 'organizer_name' => 'string', 'organizer_phone' => 'phone', 'organizer_email' => 'string'],
        'events' => ['organizer_phone' => 'phone', 'organizer_email' => 'string', 'poster_type' => 'string'],
        'explore_items' => ['slug' => 'string', 'description' => 'text', 'content' => 'longtext'],
        'explore_sub_items' => ['name' => 'string', 'description' => 'text', 'phone' => 'phone', 'email' => 'string', 'address' => 'string', 'map_url' => 'text', 'image' => 'string', 'is_active' => 'bool'],
        'government_service_items' => ['name' => 'string', 'phone' => 'phone', 'email' => 'string', 'address' => 'string', 'map_url' => 'text', 'image' => 'string'],
        'listings' => ['stock' => 'int'],
        'reviews' => ['reply' => 'text', 'replied_at' => 'timestamp'],
        'roles' => ['permissions' => 'text'],
    ];

    public function up(): void
    {
        foreach ($this->columns as $table => $cols) {
            if (!Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($table, $cols) {
                foreach ($cols as $name => $type) {
                    if (Schema::hasColumn($table, $name)) continue;
                    match ($type) {
                        'ubig' => $t->unsignedBigInteger($name)->nullable(),
                        'text' => $t->text($name)->nullable(),
                        'longtext' => $t->longText($name)->nullable(),
                        'phone' => $t->string($name, 30)->nullable(),
                        'int' => $t->integer($name)->nullable(),
                        'bool' => $t->boolean($name)->default(true),
                        'timestamp' => $t->timestamp($name)->nullable(),
                        default => $t->string($name)->nullable(),
                    };
                }
            });
        }
    }

    public function down(): void
    {
        // Additive repair migration; columns may hold data, so no automatic rollback.
    }
};
