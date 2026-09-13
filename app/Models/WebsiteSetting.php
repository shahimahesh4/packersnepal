<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'site_name', 'tagline', 'business_description', 'address', 'phone', 'email',
        'service_area', 'facebook_url', 'instagram_url', 'youtube_url', 'x_url',
        'tiktok_url', 'default_seo_title', 'default_meta_description', 'default_keywords',
        'home_banner_eyebrow', 'home_banner_title', 'home_banner_accent',
        'home_banner_description', 'home_banner_image', 'home_banner_image_alt',
        'home_banner_primary_label', 'home_banner_primary_url',
        'home_banner_secondary_label', 'home_banner_secondary_url',
        'home_content', 'home_visibility',
    ];

    protected function casts(): array
    {
        return ['home_content' => 'array', 'home_visibility' => 'array'];
    }
}
