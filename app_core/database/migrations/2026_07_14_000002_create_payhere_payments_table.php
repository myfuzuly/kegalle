<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payhere_payments')) return;

        Schema::create('payhere_payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 80)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40);              // listing_boost, membership, offer
            $table->unsignedBigInteger('related_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('LKR');
            $table->string('status', 20)->default('pending'); // pending, completed, failed, cancelled
            $table->string('payhere_payment_id')->nullable();
            $table->json('payhere_data')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payhere_payments');
    }
};
