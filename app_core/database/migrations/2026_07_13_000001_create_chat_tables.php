<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('chat_threads')) {
            Schema::create('chat_threads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
                $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
                $table->string('status')->default('open');
                $table->timestamps();

                $table->index(['buyer_id', 'seller_id', 'listing_id']);
            });
        }

        if (! Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('thread_id')->constrained('chat_threads')->cascadeOnDelete();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->text('message');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['thread_id', 'id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_threads');
    }
};
