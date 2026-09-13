<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $title = 'How Our Professional Packing Service Works';
        $description = 'Learn how Packers Nepal reviews, quotes, schedules, packs and labels belongings for homes and businesses before transporter handover.';
        $content = "Tell us what needs packing, where the work will take place, your preferred date, and anything that requires special care.\n\nOur team reviews the request and may contact you to clarify the number and type of items, access conditions, materials, timing, and handover requirements. We then prepare a quotation for the agreed packing scope.\n\nYour preferred date remains a request until we confirm availability. Once accepted, our packing specialists arrive at the agreed location to protect, wrap, organize, and label the included belongings.\n\nWhen the packing work is complete, you or your nominated contact reviews the result. Your goods are then ready for collection by the transporter you have arranged. Packers Nepal does not provide transportation, vehicle booking, or delivery services.";

        DB::table('pages')->where('slug', 'how-it-works')->update([
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
        // Published content is intentionally retained when rolling back presentation changes.
    }
};
