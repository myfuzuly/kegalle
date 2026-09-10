<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FULLTEXT index — skip if already exists (may have been added via fix_db script)
        $existing = DB::select("SHOW INDEX FROM listings WHERE Key_name = 'ft_listings_search'");
        if (empty($existing)) {
            DB::statement('ALTER TABLE listings ADD FULLTEXT INDEX ft_listings_search (title, description)');
        }

        // Composite indexes using raw SQL with prefix on varchar columns
        $indexes = collect(DB::select('SHOW INDEX FROM listings'))->pluck('Key_name')->unique()->toArray();
        if (!in_array('idx_listings_status_cat_date', $indexes))
            DB::statement('ALTER TABLE listings ADD INDEX idx_listings_status_cat_date (status(20), category_id, created_at)');
        if (!in_array('idx_listings_status_store_date', $indexes))
            DB::statement('ALTER TABLE listings ADD INDEX idx_listings_status_store_date (status(20), store_id, created_at)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE listings DROP INDEX ft_listings_search');
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex('idx_listings_status_cat_date');
            $table->dropIndex('idx_listings_status_store_date');
            $table->dropIndex('idx_listings_status_town_date');
        });
    }
};
