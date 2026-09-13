<?php

namespace App\Actions;

use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PublishPage
{
    public function handle(User $actor, Page $page): void
    {
        DB::transaction(function () use ($actor, $page) {
            $locked = Page::lockForUpdate()->findOrFail($page->id);
            Gate::forUser($actor)->authorize('publish', $locked);
            DB::table('page_revisions')->insert([
                'page_id' => $locked->id, 'title' => $locked->title, 'content' => $locked->content ?? '',
                'published_by' => $actor->id, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $locked->forceFill([
                'published_title' => $locked->title,
                'published_content' => $locked->content,
                'published_seo_title' => $locked->seo_title,
                'published_meta_description' => $locked->meta_description,
                'published_canonical_url' => $locked->canonical_url,
                'published_robots_index' => $locked->robots_index,
                'published_robots_follow' => $locked->robots_follow,
                'published_social_title' => $locked->social_title,
                'published_social_description' => $locked->social_description,
                'published_social_image' => $locked->social_image,
                'published_banner_eyebrow' => $locked->banner_eyebrow,
                'published_banner_title' => $locked->banner_title,
                'published_banner_accent' => $locked->banner_accent,
                'published_banner_description' => $locked->banner_description,
                'published_banner_image' => $locked->banner_image,
                'published_banner_image_alt' => $locked->banner_image_alt,
                'published_show_banner' => $locked->show_banner,
                'published_show_company_story' => $locked->show_company_story,
                'published_show_standards' => $locked->show_standards,
                'published_show_distinction' => $locked->show_distinction,
                'published_show_cta' => $locked->show_cta,
                'published_about_content' => $locked->about_content,
                'published_at' => now(),
            ])->save();
            DB::table('audit_events')->insert([
                'actor_id' => $actor->id, 'event' => 'page.published', 'subject_type' => 'page', 'subject_id' => $locked->id,
                'metadata' => json_encode(['title' => $locked->title]), 'created_at' => now(),
            ]);
        });
    }
}
