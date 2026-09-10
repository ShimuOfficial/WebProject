<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('website_name')->default('Restaurant Management System');
            $table->string('website_tagline')->nullable();
            $table->string('logo')->nullable();
            $table->string('hero_badge')->default('Open Today');
            $table->string('hero_title')->default('Fresh Flavors,');
            $table->string('hero_accent')->default('Fired Daily.');
            $table->text('hero_subtitle')->default('Bold dishes crafted from seasonal ingredients. Order online, track your meal, and enjoy a warm dining experience every visit.');
            $table->string('hero_background_image')->nullable();
            $table->string('about_image')->nullable();
            $table->string('primary_color')->default('#FF6B35');
            $table->string('secondary_color')->default('#004E89');
            $table->string('accent_color')->default('#F7C59F');
            $table->string('phone_number')->nullable();
            $table->string('email_address')->nullable();
            $table->string('address')->nullable();
            $table->string('opening_hours')->nullable();
            $table->text('about_us')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
