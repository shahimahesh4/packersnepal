<?php

namespace App\Support;

class HomePageDefaults
{
    public static function content(): array
    {
        return [
            'services_eyebrow' => 'Our packing services', 'services_title' => 'Professional packing for every kind of', 'services_accent' => 'space and item.',
            'services_description' => 'Choose the packing support that fits your home, office, fragile belongings, or business requirements.',
            'path_eyebrow' => 'Simple from the start', 'path_title' => 'A clear path from request to packed.',
            'path_description' => 'We focus on preparing your belongings properly at the agreed location while you arrange transportation.',
            'path_steps' => [
                ['title' => 'Tell us what needs care', 'description' => 'Share the packing location, preferred date, and the items or rooms involved.'],
                ['title' => 'Review a clear quote', 'description' => 'We confirm the scope, materials, timing, and price before work is scheduled.'],
                ['title' => 'We pack and label', 'description' => 'The assigned team protects, organizes, and records the agreed goods.'],
                ['title' => 'Ready for handover', 'description' => 'You or your nominated contact checks the work before transportation.'],
            ],
            'primary_cta_eyebrow' => 'One less thing on your list', 'primary_cta_title' => 'Tell us what needs protecting.',
            'primary_cta_description' => 'Share a few details and we will help you plan the packing with a clear scope and no obligation.',
            'primary_cta_button' => 'Request a packing quote', 'primary_cta_url' => '/request-quote',
            'about_eyebrow' => 'Who we are', 'about_title' => 'New standards in the', 'about_accent' => 'packing industry.',
            'about_description' => 'Packers Nepal is a specialist packing business. We prepare homes, offices, fragile belongings, and business goods for safe handover while you remain free to choose your own transporter.',
            'about_image' => null, 'about_image_alt' => 'Packers Nepal specialists preparing household belongings',
            'about_stat_one_value' => '4', 'about_stat_one_label' => 'Specialist services', 'about_stat_two_value' => '1', 'about_stat_two_label' => 'Clear point of contact',
            'process_eyebrow' => 'Our process', 'process_title' => 'Reliable packing built', 'process_accent' => 'around you.',
            'process_items' => [
                ['title' => 'Get a quote', 'description' => 'Tell us what needs packing.'], ['title' => 'Plan the work', 'description' => 'We agree scope and materials.'],
                ['title' => 'Pack securely', 'description' => 'Our team wraps and labels.'], ['title' => 'Check and hand over', 'description' => 'You approve the finished work.'],
            ],
            'benefits_eyebrow' => 'Why choose us', 'benefits_title' => 'Experienced packers with a', 'benefits_accent' => 'passion for protection.',
            'benefits_description' => 'Every job receives a defined scope, suitable materials, clear labeling, and a final handover check.',
            'benefits' => [
                ['title' => 'Careful by default', 'description' => 'Materials and methods matched to each item.'], ['title' => 'Clear before we begin', 'description' => 'Scope, timing, and price reviewed in advance.'],
                ['title' => 'Organized handover', 'description' => 'Labels help identify rooms and contents.'], ['title' => 'Backend controlled', 'description' => 'Authorized staff manage services and inquiries.'],
            ],
            'faq_eyebrow' => 'Frequently asked questions', 'faq_title' => 'Answers to your', 'faq_accent' => 'packing questions.',
            'faq_description' => 'Everything you need to know before requesting a packing quote.',
            'faqs' => [
                ['question' => 'Do you provide transportation?', 'answer' => 'No. We specialize in packing and prepare your goods for the transporter you select.'],
                ['question' => 'Can you pack fragile items?', 'answer' => 'Yes. Tell us what needs special care so we can plan suitable materials and handling.'],
                ['question' => 'Is my preferred date confirmed immediately?', 'answer' => 'No. Your date remains a request until our team reviews availability and confirms it.'],
                ['question' => 'Can businesses request packing?', 'answer' => 'Yes. We support offices, retailers, and other organizations with defined packing needs.'],
            ],
            'testimonials_eyebrow' => 'What clients value', 'testimonials_title' => 'Words of appreciation from', 'testimonials_accent' => 'our customers.',
        ];
    }

    public static function visibility(): array
    {
        return array_fill_keys(['banner', 'services', 'path', 'primary_cta', 'about', 'process', 'benefits', 'faq', 'testimonials'], true);
    }
}
