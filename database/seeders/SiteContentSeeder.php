<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Service;
use App\Support\AboutPageDefaults;
use App\Support\ServicePageDefaults;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['Household packing', 'household-packing', 'Room-by-room packing, protective wrapping, and clear labels for your household belongings.'],
            ['Office packing', 'office-packing', 'Organized packing for office equipment, documents, and workspaces.'],
            ['Fragile item packing', 'fragile-packing', 'Thoughtful protection for glassware, electronics, and delicate items.'],
            ['Business packing', 'business-packing', 'Packing support for retailers and businesses preparing goods for their chosen transporter.'],
        ] as $index => [$name, $slug, $description]) {
            $service = Service::firstOrCreate(['slug' => $slug], compact('name', 'description') + ['sort_order' => $index]);
            if (blank($service->page_content)) {
                $service->update(['page_content' => ServicePageDefaults::content(), 'section_visibility' => ServicePageDefaults::visibility()]);
            }
        }
        $about = "Packers Nepal is a professional packing service helping households and businesses protect, organize, and prepare their belongings in Kathmandu Valley and across Nepal. We focus on one important job: packing your goods carefully and preparing them for a smooth handover to the transporter you choose.\n\nPACKING IS OUR SPECIALITY\n\nWe are a packing business, not a moving or transportation company. Our team provides household packing, office packing, fragile-item packing, and business packing at the agreed location. By concentrating on packing, we can give proper attention to materials, wrapping, labeling, organization, and handling.\n\nCARE IN EVERY LAYER\n\nEvery item has different needs. Everyday household belongings may need strong cartons and organized room labels. Glassware, electronics, artwork, and delicate objects need additional protection. Office equipment, documents, and business stock require a clear system that supports an efficient handover.\n\nA CLEAR AND ORGANIZED PROCESS\n\nOur process starts when you tell us what needs packing, where the work will happen, and your preferred date. We review the request, clarify the scope, and prepare a quotation covering the agreed work and materials. A requested date becomes confirmed only after our team checks availability.\n\nPACKING FOR HOMES AND BUSINESSES\n\nWe support families, offices, retailers, and businesses preparing goods for collection. Whether the requirement involves one delicate group of items or several rooms, our goal is to make the packing stage easier to understand and manage.\n\nWHY CHOOSE PACKERS NEPAL\n\nCustomers choose Packers Nepal for careful handling, clear quotations, suitable packing materials, organized labeling, and a defined service scope. Transportation remains separate, giving you the freedom to select the provider and arrangement that suit your needs.\n\nTell us what needs protection and where the work will take place. Our team will help you plan a professional packing service with care in every layer.";

        foreach ([
            ['About Our Professional Packing Services in Nepal', 'about', $about, 'About Our Professional Packing Services in Nepal', 'Meet Packers Nepal, Kathmandu Valley’s careful packing specialists for homes, offices, fragile belongings and business goods. Request a clear packing quote.'],
            ['How Our Professional Packing Service Works', 'how-it-works', "Tell us what needs packing, where the work will take place, your preferred date, and anything that requires special care.\n\nOur team reviews the request and may contact you to clarify the number and type of items, access conditions, materials, timing, and handover requirements. We then prepare a quotation for the agreed packing scope.\n\nYour preferred date remains a request until we confirm availability. Once accepted, our packing specialists arrive at the agreed location to protect, wrap, organize, and label the included belongings.\n\nWhen the packing work is complete, you or your nominated contact reviews the result. Your goods are then ready for collection by the transporter you have arranged. Packers Nepal does not provide transportation, vehicle booking, or delivery services.", 'How Our Professional Packing Service Works', 'Learn how Packers Nepal reviews, quotes, schedules, packs and labels belongings for homes and businesses before transporter handover.'],
        ] as [$title, $slug, $content, $seoTitle, $metaDescription]) {
            if (! Page::where('slug', $slug)->exists()) {
                $page = new Page(compact('title', 'slug', 'content') + ['seo_title' => $seoTitle, 'meta_description' => $metaDescription]);
                $page->forceFill(['published_title' => $title, 'published_content' => $content, 'published_seo_title' => $seoTitle, 'published_meta_description' => $metaDescription, 'published_at' => now()])->save();
            }
        }

        $aboutBanner = [
            'banner_eyebrow' => 'Meet Packers Nepal',
            'banner_title' => 'Packing expertise.',
            'banner_accent' => 'Care in every layer.',
            'banner_description' => 'Professional packing for homes and businesses in Kathmandu Valley and across Nepal, prepared carefully for the transporter you choose.',
            'banner_image_alt' => 'Packers Nepal packing specialists working carefully together',
        ];

        $aboutPage = Page::where('slug', 'about')->first();
        if ($aboutPage && blank($aboutPage->banner_title)) {
            $aboutPage->forceFill($aboutBanner + [
                'published_banner_eyebrow' => $aboutBanner['banner_eyebrow'],
                'published_banner_title' => $aboutBanner['banner_title'],
                'published_banner_accent' => $aboutBanner['banner_accent'],
                'published_banner_description' => $aboutBanner['banner_description'],
                'published_banner_image_alt' => $aboutBanner['banner_image_alt'],
            ])->save();
        }

        if ($aboutPage && blank($aboutPage->about_content)) {
            $aboutPage->forceFill([
                'about_content' => AboutPageDefaults::content(),
                'published_about_content' => AboutPageDefaults::content(),
            ])->save();
        }
    }
}
