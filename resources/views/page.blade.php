@php
    $pageSocialImagePath = $page->published_social_image ?: $page->published_banner_image;
    $pageSocialImage = $pageSocialImagePath ? \Illuminate\Support\Facades\Storage::disk('public')->url($pageSocialImagePath) : null;
    $pageSchema = [[
        '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $page->published_title, 'item' => route('page.show', $page->slug)],
        ],
    ]];
@endphp
<x-layouts.site :title="$page->published_seo_title ?: $page->published_title" :meta-description="$page->published_meta_description ?: 'Professional packing services for homes and businesses in Nepal.'" :seo-image="$pageSocialImage" :schema="$pageSchema" :canonical="$page->published_canonical_url" :indexable="$page->published_robots_index" :follow="$page->published_robots_follow" :social-title="$page->published_social_title" :social-description="$page->published_social_description">
    @if($page->slug === 'about')
        @include('pages.about', ['page' => $page])
    @elseif($page->slug === 'how-it-works')
        @include('pages.how-it-works', ['page' => $page])
    @else
        <section class="relative overflow-hidden border-b border-forest/10 bg-mist"><div class="absolute -right-24 -top-32 h-96 w-96 rounded-full border-[65px] border-leaf/20"></div><div class="shell relative py-20 sm:py-28"><p class="eyebrow">Packers Nepal</p><h1 class="display mt-6 max-w-4xl text-5xl leading-tight sm:text-6xl">{{ $page->published_title }}</h1><p class="mt-6 text-sm font-bold uppercase tracking-[.14em] text-forest/45">Professional packing · Clear scope · Careful handover</p></div></section>
        <article class="shell grid gap-12 py-16 lg:grid-cols-[.65fr_1.35fr] lg:py-24"><aside><p class="text-xs font-bold uppercase tracking-[.2em] text-clay">Packing specialists</p><p class="mt-4 max-w-xs text-sm leading-6 text-forest/60">We protect and prepare your goods at the agreed location. Transportation is arranged separately.</p></aside><div><div class="max-w-3xl whitespace-pre-line text-lg leading-9 text-forest/75">{{ $page->published_content }}</div><div class="mt-12 rounded-2xl bg-sage p-7 sm:flex sm:items-center sm:justify-between sm:gap-8"><div><h2 class="display text-2xl">Ready to plan your packing?</h2><p class="mt-2 text-sm text-forest/65">Tell us what needs care and where the work will happen.</p></div><a href="{{ route('quote.request') }}" class="button mt-6 shrink-0 sm:mt-0">Request a quote <span aria-hidden="true">↗</span></a></div></div></article>
    @endif
</x-layouts.site>
