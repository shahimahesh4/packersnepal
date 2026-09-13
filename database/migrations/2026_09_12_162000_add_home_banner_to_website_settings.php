<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->string('home_banner_eyebrow')->nullable();
            $table->string('home_banner_title')->nullable();
            $table->string('home_banner_accent')->nullable();
            $table->text('home_banner_description')->nullable();
            $table->string('home_banner_image')->nullable();
            $table->string('home_banner_image_alt')->nullable();
            $table->string('home_banner_primary_label')->nullable();
            $table->string('home_banner_primary_url')->nullable();
            $table->string('home_banner_secondary_label')->nullable();
            $table->string('home_banner_secondary_url')->nullable();
        });

        DB::table('website_settings')->update([
            'home_banner_eyebrow' => 'Packing specialists · Kathmandu, Nepal',
            'home_banner_title' => 'Pack with care.',
            'home_banner_accent' => 'Move forward with confidence.',
            'home_banner_description' => 'We wrap, protect, organize, and label your belongings for a safe handover to the transporter you choose.',
            'home_banner_image_alt' => 'Packers Nepal team carefully packing household belongings',
            'home_banner_primary_label' => 'Plan your packing',
            'home_banner_primary_url' => '/request-quote',
            'home_banner_secondary_label' => 'Explore services',
            'home_banner_secondary_url' => '/#services',
        ]);
    }

    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_banner_eyebrow', 'home_banner_title', 'home_banner_accent',
                'home_banner_description', 'home_banner_image', 'home_banner_image_alt',
                'home_banner_primary_label', 'home_banner_primary_url',
                'home_banner_secondary_label', 'home_banner_secondary_url',
            ]);
        });
    }
};
