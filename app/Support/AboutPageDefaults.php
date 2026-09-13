<?php

namespace App\Support;

class AboutPageDefaults
{
    public static function content(): array
    {
        return [
            'company_eyebrow' => 'About our company', 'company_title' => 'Focused on packing.', 'company_accent' => 'Built around care.',
            'company_image' => null, 'company_image_alt' => 'Packers Nepal specialist carefully packing household belongings',
            'stat_one_value' => '4', 'stat_one_label' => 'Packing services', 'stat_two_value' => '1', 'stat_two_label' => 'Clear specialty',
            'standards_eyebrow' => 'What guides us', 'standards_title' => 'Professional standards from request to', 'standards_accent' => 'handover.',
            'standards_cards' => [
                ['title' => 'Careful preparation', 'description' => 'We match packing methods and protective materials to the items involved.'],
                ['title' => 'Clear communication', 'description' => 'We review scope, timing, materials, and price before confirming work.'],
                ['title' => 'Organized results', 'description' => 'We wrap and label goods so the final handover is easy to understand.'],
            ],
            'distinction_eyebrow' => 'A clear distinction', 'distinction_title' => 'We pack.', 'distinction_accent' => 'You choose the transporter.',
            'distinction_description' => 'Packers Nepal does not operate as a moving or transportation company. This focused model gives you control over transport arrangements while our specialists concentrate on protecting, organizing, and preparing your goods.',
            'distinction_image' => null, 'distinction_image_alt' => 'Packers Nepal team preparing business goods',
            'distinction_link_label' => 'See how packing works', 'distinction_link_url' => '/pages/how-it-works',
            'cta_eyebrow' => 'Ready when you are', 'cta_title' => 'Let’s plan the right packing service for you.',
            'cta_description' => 'Tell us what needs care, where the work will happen, and your preferred date.',
            'cta_button_label' => 'Request a packing quote', 'cta_button_url' => '/request-quote',
        ];
    }
}
