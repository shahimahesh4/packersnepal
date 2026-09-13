<?php

namespace App\Support;

class ServicePageDefaults
{
    public static function content(): array
    {
        return [
            'banner_eyebrow' => 'Professional packing service', 'banner_title' => null, 'banner_description' => null,
            'banner_button_label' => 'Request this service', 'banner_button_url' => '/request-quote', 'banner_image_alt' => null,
            'details_eyebrow' => 'Service details', 'details_title' => 'Professional care for every', 'details_accent' => 'item.',
            'notice_title' => 'Important', 'notice_text' => 'Packers Nepal provides packing, wrapping, organization, and labeling. Transportation, delivery, and vehicle arrangements are handled separately.',
            'expect_eyebrow' => 'What to expect', 'expect_title' => 'A clear service from scope to', 'expect_accent' => 'handover.',
            'expect_cards' => [
                ['title' => 'Scope reviewed', 'description' => 'We clarify the items, location, access, materials, and preferred date.'],
                ['title' => 'Carefully packed', 'description' => 'Our specialists protect, organize, and label the agreed belongings.'],
                ['title' => 'Ready to collect', 'description' => 'You check the work before handover to your selected transporter.'],
            ],
            'cta_eyebrow' => null, 'cta_title' => 'Tell us what needs protecting.',
            'cta_description' => 'Share the packing location, approximate items, access requirements, and preferred date. We will review everything before confirming the scope.',
            'cta_button_label' => 'Get a packing quote', 'cta_button_url' => '/request-quote', 'cta_image_alt' => null,
        ];
    }

    public static function visibility(): array
    {
        return array_fill_keys(['banner', 'details', 'expectations', 'cta'], true);
    }
}
