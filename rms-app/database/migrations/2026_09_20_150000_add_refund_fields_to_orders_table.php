<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('orders', 'cancellation_fee_percent')) {
                $table->unsignedTinyInteger('cancellation_fee_percent')->nullable()->after('cancelled_at');
            }
            if (!Schema::hasColumn('orders', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->nullable()->after('cancellation_fee_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = ['cancelled_at', 'cancellation_fee_percent', 'refund_amount'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
