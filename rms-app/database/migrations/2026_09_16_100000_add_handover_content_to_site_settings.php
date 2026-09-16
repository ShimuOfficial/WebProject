<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'about_title')) {
                $table->string('about_title')->nullable()->after('about_us');
            }
            if (! Schema::hasColumn('site_settings', 'hours_json')) {
                $table->json('hours_json')->nullable()->after('opening_hours');
            }
            if (! Schema::hasColumn('site_settings', 'content_json')) {
                $table->json('content_json')->nullable()->after('about_title');
            }
            if (! Schema::hasColumn('site_settings', 'facebook_url')) {
                $table->string('facebook_url')->nullable()->after('content_json');
            }
            if (! Schema::hasColumn('site_settings', 'instagram_url')) {
                $table->string('instagram_url')->nullable()->after('facebook_url');
            }
            if (! Schema::hasColumn('site_settings', 'guest_rating')) {
                $table->string('guest_rating')->nullable()->after('instagram_url');
            }
        });

        if (Schema::hasTable('site_settings')) {
            DB::table('site_settings')->where('id', 1)->update([
                'hours_json' => json_encode(config('restaurant.hours')),
                'about_title' => config('restaurant.about.title'),
                'content_json' => json_encode(config('restaurant.content')),
                'guest_rating' => '4.8',
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            foreach (['about_title', 'hours_json', 'content_json', 'facebook_url', 'instagram_url', 'guest_rating'] as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
