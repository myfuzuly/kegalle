<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $existing = fn($table) => collect(DB::select("SHOW INDEX FROM `{$table}`"))->pluck('Key_name')->unique()->toArray();

        if (!in_array('idx_services_user_status', $existing('services'))) {
            DB::statement('ALTER TABLE services ADD INDEX idx_services_user_status (user_id, status)');
        }

        if (!in_array('uidx_listing_field_values', $existing('listing_field_values'))) {
            DB::statement('ALTER TABLE listing_field_values ADD UNIQUE INDEX uidx_listing_field_values (listing_id, custom_field_id)');
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE services DROP INDEX IF EXISTS idx_services_user_status');
        DB::statement('ALTER TABLE listing_field_values DROP INDEX IF EXISTS uidx_listing_field_values');
    }
};
