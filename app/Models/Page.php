<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'seo_title', 'meta_description',
        'canonical_url', 'robots_index', 'robots_follow', 'social_title', 'social_description', 'social_image',
        'banner_eyebrow', 'banner_title', 'banner_accent', 'banner_description',
        'banner_image', 'banner_image_alt',
        'show_banner', 'show_company_story', 'show_standards', 'show_distinction', 'show_cta',
        'about_content',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'published_robots_index' => 'boolean',
            'published_robots_follow' => 'boolean',
            'show_banner' => 'boolean',
            'show_company_story' => 'boolean',
            'show_standards' => 'boolean',
            'show_distinction' => 'boolean',
            'show_cta' => 'boolean',
            'published_show_banner' => 'boolean',
            'published_show_company_story' => 'boolean',
            'published_show_standards' => 'boolean',
            'published_show_distinction' => 'boolean',
            'published_show_cta' => 'boolean',
            'about_content' => 'array',
            'published_about_content' => 'array',
        ];
    }

    public function grants(): HasMany
    {
        return $this->hasMany(PageGrant::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): void
    {
        if (! $user->permits('pages.view') || ! $user->permits('admin.access')) {
            $query->whereRaw('1 = 0');
        } elseif (! $user->permits('pages.scope.all')) {
            $query->whereHas('grants', fn (Builder $grants) => $grants->where('user_id', $user->id)->where('action', 'view'));
        }
    }
}
