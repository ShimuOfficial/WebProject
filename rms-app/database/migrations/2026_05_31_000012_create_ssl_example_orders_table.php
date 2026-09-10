<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ssl_example_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('main_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->text('address')->nullable();
            $table->string('status', 30)->nullable();
            $table->string('transaction_id')->nullable()->unique();
            $table->string('currency', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ssl_example_orders');
    }
};
