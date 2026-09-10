<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_per_dish', 10, 4);
            $table->timestamps();

            $table->unique(['menu_id', 'inventory_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_ingredients');
    }
};
