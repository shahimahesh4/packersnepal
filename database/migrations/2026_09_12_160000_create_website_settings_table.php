<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Packers Nepal');
            $table->string('tagline')->nullable();
            $table->text('business_description')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('email')->nullable();
            $table->string('service_area')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('x_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('default_seo_title')->nullable();
            $table->text('default_meta_description')->nullable();
            $table->timestamps();
        });

        DB::table('website_settings')->insert([
            'site_name' => 'Packers Nepal',
            'tagline' => 'Care in every layer',
            'business_description' => 'Professional packing, wrapping, and labeling for the things that matter. Your goods leave the site prepared for your chosen transporter.',
            'address' => 'New Road, Kathmandu',
            'phone' => '9801010000',
            'email' => 'info@packersnepal.com',
            'service_area' => 'Kathmandu Valley and agreed locations across Nepal',
            'facebook_url' => 'https://facebook.com/packersnepal',
            'instagram_url' => 'https://instagram.com/packersnepal',
            'youtube_url' => 'https://youtube.com/@packersnepal',
            'x_url' => 'https://x.com/packersnepal',
            'tiktok_url' => 'https://tiktok.com/@packersnepal',
            'default_seo_title' => 'Professional Packing Services in Nepal | Packers Nepal',
            'default_meta_description' => 'Professional household, office, fragile-item and business packing services in Kathmandu and across Nepal. Request a clear packing quote.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
