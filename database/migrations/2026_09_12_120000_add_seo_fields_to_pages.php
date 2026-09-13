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
            $table->string('seo_title', 70)->nullable()->after('content');
            $table->string('meta_description', 170)->nullable()->after('seo_title');
            $table->string('published_seo_title', 70)->nullable()->after('published_content');
            $table->string('published_meta_description', 170)->nullable()->after('published_seo_title');
        });

        $title = 'About Our Professional Packing Services in Nepal';
        $description = 'Meet Packers Nepal, Kathmandu Valley’s careful packing specialists for homes, offices, fragile belongings and business goods. Request a clear packing quote.';
        $content = <<<'CONTENT'
Packers Nepal is a professional packing service helping households and businesses protect, organize, and prepare their belongings in Kathmandu Valley and across Nepal. We focus on one important job: packing your goods carefully and preparing them for a smooth handover to the transporter you choose.

PACKING IS OUR SPECIALITY

We are a packing business, not a moving or transportation company. Our team provides household packing, office packing, fragile-item packing, and business packing at the agreed location. By concentrating on packing, we can give proper attention to materials, wrapping, labeling, organization, and handling.

CARE IN EVERY LAYER

Every item has different needs. Everyday household belongings may need strong cartons and organized room labels. Glassware, electronics, artwork, and delicate objects need additional protection. Office equipment, documents, and business stock require a clear system that supports an efficient handover. We review the scope before work begins and select an appropriate packing approach for the items involved.

A CLEAR AND ORGANIZED PROCESS

Our process starts when you tell us what needs packing, where the work will happen, and your preferred date. We review the request, clarify the scope, and prepare a quotation covering the agreed work and materials. A requested date becomes confirmed only after our team checks availability. When packing is complete, you or your nominated contact can review the work before the goods are handed over.

PACKING FOR HOMES AND BUSINESSES

We support families preparing household belongings, offices organizing equipment and documents, retailers packing products, and businesses preparing goods for collection. Whether the requirement involves one delicate group of items or several rooms, our goal is to make the packing stage easier to understand and manage.

WHY CHOOSE PACKERS NEPAL

Customers choose Packers Nepal for careful handling, clear quotations, suitable packing materials, organized labeling, and a defined service scope. Transportation remains separate, giving you the freedom to select the transport provider, schedule, and arrangement that suit your needs.

Tell us what needs protection and where the work will take place. Our team will review your requirements and help you plan a professional packing service with care in every layer.
CONTENT;

        DB::table('pages')->where('slug', 'about')->update([
            'title' => $title,
            'content' => $content,
            'seo_title' => $title,
            'meta_description' => $description,
            'published_title' => $title,
            'published_content' => $content,
            'published_seo_title' => $title,
            'published_meta_description' => $description,
            'published_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'meta_description', 'published_seo_title', 'published_meta_description']);
        });
    }
};
