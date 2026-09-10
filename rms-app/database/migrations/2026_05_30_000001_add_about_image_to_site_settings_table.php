<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('site_settings', 'about_image')) {
            return;
        }

        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('about_image')->nullable()->after('hero_background_image');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('about_image');
        });
    }
};
