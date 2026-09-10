<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->json('reserved_requirements')->nullable()->after('inventory_deducted_at');
            $table->timestamp('reserved_at')->nullable()->after('reserved_requirements');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['reserved_requirements', 'reserved_at']);
        });
    }
};
