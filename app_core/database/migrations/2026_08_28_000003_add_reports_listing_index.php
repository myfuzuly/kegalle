<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = collect(DB::select('SHOW INDEX FROM reports'))->pluck('Key_name')->unique()->toArray();
        if (!in_array('idx_reports_listing_id', $indexes)) {
            DB::statement('ALTER TABLE reports ADD INDEX idx_reports_listing_id (listing_id)');
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE reports DROP INDEX IF EXISTS idx_reports_listing_id');
    }
};
