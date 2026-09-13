@php
    $defaultImages = ['household-packing' => 'service-household.jpg', 'office-packing' => 'service-office.jpg', 'fragile-packing' => 'service-fragile.jpg', 'business-packing' => 'service-business.jpg'];
    $pageContent = array_replace_recursive(\App\Support\ServicePageDefaults::content(), $service->page_content ?? []);
    $visibility = array_replace(\App\Support\ServicePageDefaults::visibility(), $service->section_visibility ?? []);
    $fallbackImage = asset('images/'.($defaultImages[$service->slug] ?? 'packing-team-hero.jpg'));
    $bannerImage = $service->banner_image ? \Illuminate\Support\Facades\Storage::disk('public')->url($service->banner_image) : $fallbackImage;
    $ctaImage = $service->cta_image ? \Illuminate\Support\Facades\Storage::disk('public')->url($service->cta_image) : $bannerImage;
    $socialImage = $service->social_image ? \Illuminate\Support\Facades\Storage::disk('public')->url($service->social_image) : $bannerImage;
    
    $serviceSchema = [[
        '@context' => 'https://schema.org', '@type' => 'Service', 'name' => $service->name,
        'description' => $service->meta_description ?: $service->description, 'url' => route('services.show', $service),
        'image' => $bannerImage, 'areaServed' => ['@type' => 'Country', 'name' => 'Nepal'],
        'provider' => ['@id' => url('/').'#business'],
    ], [
        '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $service->name, 'item' => route('services.show', $service)],
        ],
    ]];
@endphp
<x-layouts.site :title="$service->seo_title ?: $service->name" :meta-description="$service->meta_description ?: $service->description" :seo-image="$socialImage" :schema="$serviceSchema" :canonical="$service->canonical_url" :indexable="$service->robots_index" :follow="$service->robots_follow" :social-title="$service->social_title" :social-description="$service->social_description">
    @if($visibility['banner'])
        <section class="relative min-h-[560px] overflow-hidden bg-forest bg-cover bg-center text-white" style="background-image: url('{{ $bannerImage }}')" role="img" aria-label="{{ $pageContent['banner_image_alt'] ?: $service->name.' by Packers Nepal' }}"><div class="absolute inset-0 bg-gradient-to-r from-forest-deep via-forest-deep/90 to-forest/15"></div><div class="shell relative flex min-h-[560px] items-center py-20"><div class="max-w-3xl"><nav aria-label="Breadcrumb" class="text-xs font-bold uppercase tracking-[.18em] text-white/55"><a href="{{ route('home') }}" class="hover:text-white">Home</a><span class="mx-2">/</span><span>Services</span></nav><p class="eyebrow mt-7 !text-white/60">{{ $pageContent['banner_eyebrow'] }}</p><h1 class="display mt-4 text-5xl leading-tight sm:text-6xl lg:text-7xl">{{ $pageContent['banner_title'] ?: $service->name }}</h1><p class="mt-6 max-w-2xl text-lg leading-8 text-white/75">{{ $pageContent['banner_description'] ?: $service->description }}</p><a href="{{ url($pageContent['banner_button_url']) }}" class="button mt-9">{{ $pageContent['banner_button_label'] }} <span aria-hidden="true">↗</span></a></div></div></section>
    @endif

    @if($visibility['details'])
        <section class="shell py-20 lg:py-28"><div class="grid gap-12 lg:grid-cols-[.65fr_1.35fr] lg:items-start"><aside class="lg:sticky lg:top-32"><p class="eyebrow">{{ $pageContent['details_eyebrow'] }}</p><h2 class="section-heading">{{ $pageContent['details_title'] }} <span class="text-clay">{{ $pageContent['details_accent'] }}</span></h2><div class="mt-8 rounded-2xl bg-sage p-6"><p class="text-xs font-bold uppercase tracking-[.18em] text-clay">{{ $pageContent['notice_title'] }}</p><p class="mt-3 text-sm leading-7 text-forest/70">{{ $pageContent['notice_text'] }}</p></div></aside><article class="max-w-3xl">@php($blocks = preg_split('/\R{2,}/', trim($service->details ?: $service->description))) @foreach($blocks as $block) @if(mb_strlen($block) < 80 && mb_strtoupper($block) === $block)<h2 class="display {{ $loop->first ? '' : 'mt-12' }} text-2xl sm:text-3xl">{{ Str::headline(Str::lower($block)) }}</h2>@else<p class="{{ $loop->first ? '' : 'mt-5' }} text-lg leading-9 text-forest/70">{{ $block }}</p>@endif @endforeach</article></div></section>
    @endif

    @if($visibility['expectations'])
        <section class="bg-mist py-20"><div class="shell"><div class="mx-auto max-w-2xl text-center"><p class="eyebrow justify-center">{{ $pageContent['expect_eyebrow'] }}</p><h2 class="section-heading">{{ $pageContent['expect_title'] }} <span class="text-clay">{{ $pageContent['expect_accent'] }}</span></h2></div><div class="mt-12 grid gap-5 md:grid-cols-3">@foreach($pageContent['expect_cards'] as $item)<article class="surface p-7"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-clay text-white">✓</span><h3 class="mt-6 text-xl font-extrabold">{{ $item['title'] }}</h3><p class="mt-3 text-sm leading-7 text-forest/65">{{ $item['description'] }}</p></article>@endforeach</div></div></section>
    @endif

    @if($visibility['cta'])
        <section class="shell py-20 lg:py-28"><div class="grid overflow-hidden rounded-2xl bg-forest text-white lg:grid-cols-[1fr_.85fr]"><div class="p-8 sm:p-12"><p class="eyebrow !text-white/65">{{ $pageContent['cta_eyebrow'] ?: 'Request '.$service->name }}</p><h2 class="display mt-6 max-w-2xl text-4xl leading-tight sm:text-5xl">{{ $pageContent['cta_title'] }}</h2><p class="mt-5 max-w-xl leading-8 text-white/70">{{ $pageContent['cta_description'] }}</p><a href="{{ url($pageContent['cta_button_url']) }}" class="button mt-8">{{ $pageContent['cta_button_label'] }} <span aria-hidden="true">↗</span></a></div><img src="{{ $ctaImage }}" alt="{{ $pageContent['cta_image_alt'] ?: $service->name.' by Packers Nepal' }}" class="h-full min-h-[360px] w-full object-cover" loading="lazy"></div></section>
    @endif
</x-layouts.site>
