<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = collect(DB::select('SHOW INDEX FROM chat_messages'))->pluck('Key_name')->unique()->toArray();
        if (!in_array('idx_chat_messages_thread_sender_read', $indexes)) {
            DB::statement('ALTER TABLE chat_messages ADD INDEX idx_chat_messages_thread_sender_read (thread_id, sender_id, read_at)');
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE chat_messages DROP INDEX IF EXISTS idx_chat_messages_thread_sender_read');
    }
};
