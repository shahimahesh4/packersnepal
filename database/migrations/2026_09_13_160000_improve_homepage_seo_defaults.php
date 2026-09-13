<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('website_settings')
            ->where('default_seo_title', 'Professional Packing Services in Nepal | Packers Nepal')
            ->update([
                'default_seo_title' => 'Professional Packing Services in Kathmandu | Packers Nepal',
                'default_meta_description' => 'Protect your home, office, fragile items and business goods with professional packing, wrapping and labeling in Kathmandu. Request a clear quote.',
            ]);
    }

    public function down(): void
    {
        DB::table('website_settings')
            ->where('default_seo_title', 'Professional Packing Services in Kathmandu | Packers Nepal')
            ->update([
                'default_seo_title' => 'Professional Packing Services in Nepal | Packers Nepal',
                'default_meta_description' => 'Professional household, office, fragile-item and business packing services in Kathmandu and across Nepal. Request a clear packing quote.',
            ]);
    }
};
