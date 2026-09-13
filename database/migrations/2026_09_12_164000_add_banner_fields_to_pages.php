<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('banner_eyebrow')->nullable();
            $table->string('banner_title')->nullable();
            $table->string('banner_accent')->nullable();
            $table->text('banner_description')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('banner_image_alt')->nullable();
            $table->string('published_banner_eyebrow')->nullable();
            $table->string('published_banner_title')->nullable();
            $table->string('published_banner_accent')->nullable();
            $table->text('published_banner_description')->nullable();
            $table->string('published_banner_image')->nullable();
            $table->string('published_banner_image_alt')->nullable();
        });

        $banner = [
            'banner_eyebrow' => 'Meet Packers Nepal',
            'banner_title' => 'Packing expertise.',
            'banner_accent' => 'Care in every layer.',
            'banner_description' => 'Professional packing for homes and businesses in Kathmandu Valley and across Nepal, prepared carefully for the transporter you choose.',
            'banner_image_alt' => 'Packers Nepal packing specialists working carefully together',
        ];

        DB::table('pages')->where('slug', 'about')->update($banner + [
            'published_banner_eyebrow' => $banner['banner_eyebrow'],
            'published_banner_title' => $banner['banner_title'],
            'published_banner_accent' => $banner['banner_accent'],
            'published_banner_description' => $banner['banner_description'],
            'published_banner_image_alt' => $banner['banner_image_alt'],
        ]);
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn([
                'banner_eyebrow', 'banner_title', 'banner_accent', 'banner_description',
                'banner_image', 'banner_image_alt', 'published_banner_eyebrow',
                'published_banner_title', 'published_banner_accent',
                'published_banner_description', 'published_banner_image',
                'published_banner_image_alt',
            ]);
        });
    }
};
