<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'details', 'seo_title', 'meta_description', 'canonical_url', 'robots_index', 'robots_follow', 'social_title', 'social_description', 'social_image', 'is_active', 'sort_order', 'banner_image', 'cta_image', 'page_content', 'section_visibility'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'robots_index' => 'boolean', 'robots_follow' => 'boolean', 'page_content' => 'array', 'section_visibility' => 'array'];
    }
}
