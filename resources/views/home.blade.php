@php
    $bannerImage = $siteSettings?->home_banner_image
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings->home_banner_image)
        : asset('images/packing-team-hero.jpg');
    $bannerPrimaryUrl = url($siteSettings?->home_banner_primary_url ?: '/request-quote');
    $bannerSecondaryUrl = url($siteSettings?->home_banner_secondary_url ?: '/#services');
    $homeContent = array_replace_recursive(\App\Support\HomePageDefaults::content(), $siteSettings?->home_content ?? []);
    $homeVisibility = array_replace(\App\Support\HomePageDefaults::visibility(), $siteSettings?->home_visibility ?? []);
    $homeAboutImage = $homeContent['about_image'] ? \Illuminate\Support\Facades\Storage::disk('public')->url($homeContent['about_image']) : asset('images/packing-team-hero.jpg');
    $homeSchema = $homeVisibility['faq'] ? [[
        '@context' => 'https://schema.org', '@type' => 'FAQPage',
        'mainEntity' => collect($homeContent['faqs'])->map(fn ($faq) => [
            '@type' => 'Question', 'name' => $faq['question'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
        ])->values()->all(),
    ]] : [];
@endphp
<x-layouts.site :seo-image="$bannerImage" :schema="$homeSchema">
    @if($homeVisibility['banner'])
    <section class="relative min-h-[680px] overflow-hidden bg-forest text-white">
        <img src="{{ $bannerImage }}" alt="{{ $siteSettings?->home_banner_image_alt ?? 'Packers Nepal team carefully packing household belongings' }}" width="1536" height="1024" class="absolute inset-0 h-full w-full object-cover" fetchpriority="high">
        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(90deg,rgba(8,47,53,.98)_0%,rgba(8,47,53,.86)_40%,rgba(8,47,53,.16)_78%)]"></div>
        <div class="pointer-events-none absolute -left-32 top-24 h-80 w-80 rounded-full bg-clay/10 blur-3xl"></div>
        <div class="shell relative grid min-h-[680px] gap-12 py-14 lg:grid-cols-[1.02fr_.98fr] lg:items-center lg:py-24">
            <div class="relative z-10">
                <p class="eyebrow !text-white/70">{{ $siteSettings?->home_banner_eyebrow ?? 'Packing specialists · Kathmandu, Nepal' }}</p>
                <h1 class="display mt-7 max-w-3xl text-5xl leading-[.98] sm:text-6xl lg:text-[5.4rem]">{{ $siteSettings?->home_banner_title ?? 'Pack with care.' }}<br><span class="text-clay">{{ $siteSettings?->home_banner_accent ?? 'Move forward with confidence.' }}</span></h1>
                <p class="mt-7 max-w-xl text-lg leading-8 text-white/70">{{ $siteSettings?->home_banner_description ?? 'We wrap, protect, organize, and label your belongings for a safe handover to the transporter you choose.' }}</p>
                <div class="mt-9 flex flex-wrap items-center gap-6"><a href="{{ $bannerPrimaryUrl }}" class="button">{{ $siteSettings?->home_banner_primary_label ?? 'Plan your packing' }} <span aria-hidden="true">↗</span></a><a href="{{ $bannerSecondaryUrl }}" class="inline-flex items-center gap-2 border-b border-white/40 pb-1 text-sm font-bold hover:border-clay hover:text-leaf">{{ $siteSettings?->home_banner_secondary_label ?? 'Explore services' }} <span aria-hidden="true">↓</span></a></div>
                <div class="mt-10 flex flex-wrap gap-x-8 gap-y-3 border-t border-white/15 pt-6 text-xs font-bold uppercase tracking-[.12em] text-white/55"><span>Clear quotations</span><span>Careful handling</span><span>Transportation arranged by you</span></div>
            </div>
            <div class="relative hidden">
                <div class="absolute -right-5 -top-5 h-24 w-24 rounded-full bg-clay/25"></div>
                <div class="relative overflow-hidden rounded-2xl border border-white/15 bg-white/10 p-5 shadow-[0_36px_90px_-48px_rgba(0,0,0,.8)] backdrop-blur sm:p-8">
                    <div class="flex items-center justify-between text-[0.62rem] font-bold uppercase tracking-[.17em] text-white/60"><span>Packed with purpose</span><span>Ready for handover</span></div>
                    <svg class="mx-auto my-5 w-full max-w-lg" viewBox="0 0 500 390" role="img" aria-label="Carefully wrapped and labeled packing boxes">
                        <ellipse cx="255" cy="348" rx="193" ry="22" fill="#8f3030" opacity=".1"/><path d="M80 205 224 148 362 205 218 266Z" fill="#d7b27f"/><path d="m80 205 138 61v85L80 288Z" fill="#b98752"/><path d="m218 266 144-61v83l-144 63Z" fill="#cba26e"/><path d="m141 180 138 60 24-10-138-60Z" fill="#f2dfbd"/><path d="m279 240 24-10v85l-24 10Z" fill="#e7c995"/><path d="m224 74 112-41 103 44-115 46Z" fill="#deb985"/><path d="m224 74 100 49v95l-100-48Z" fill="#c8955f"/><path d="m324 123 115-46v94l-115 47Z" fill="#e5c392"/><path d="m268 58 101 48 24-10-101-47Z" fill="#f6e6c7"/><path d="m369 106 24-10v94l-24 10Z" fill="#ecd3a5"/><rect x="104" y="233" width="65" height="33" rx="3" transform="rotate(24 104 233)" fill="#fffdf7"/><path d="m114 250 34 15m-37-6 22 10" stroke="#8f3030" stroke-width="3"/><path d="m350 256 71-26 48 28-71 29Z" fill="#efb8ad"/><path d="m350 256 48 31v54l-48-31Z" fill="#ad3f3f"/><path d="m398 287 71-29v54l-71 29Z" fill="#d36d66"/><circle cx="100" cy="103" r="35" fill="#fffdf7"/><path d="m85 103 10 10 19-22" stroke="#8f3030" stroke-width="3" fill="none"/>
                    </svg>
                    <div class="flex items-center justify-between rounded-xl bg-white px-5 py-4 text-forest"><div><p class="font-display text-lg">Every layer has a purpose.</p><p class="mt-1 text-xs text-forest/60">Protect · Organize · Prepare</p></div><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-clay text-white" aria-hidden="true">↗</span></div>
                </div>
                <div class="absolute -bottom-6 -left-4 rounded-2xl bg-white px-5 py-4 shadow-xl sm:-left-8"><p class="text-2xl font-bold">Packing only</p><p class="mt-1 text-xs text-forest/55">A clear, specialist service</p></div>
            </div>
        </div>
    </section>
    @endif

    @if($homeVisibility['services'])
    <section id="services" class="shell py-24">
        <div class="grid gap-7 lg:grid-cols-[1fr_.55fr] lg:items-end"><div><p class="eyebrow">{{ $homeContent['services_eyebrow'] }}</p><h2 class="section-heading max-w-2xl">{{ $homeContent['services_title'] }} <span class="text-clay">{{ $homeContent['services_accent'] }}</span></h2></div><p class="max-w-lg text-base leading-7 text-forest/65 lg:justify-self-end">{{ $homeContent['services_description'] }}</p></div>
        @php($serviceImages = ['service-household.jpg', 'service-office.jpg', 'service-fragile.jpg', 'service-business.jpg'])
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($services as $service)
                <article class="surface group flex min-h-[28rem] flex-col overflow-hidden transition duration-300 hover:-translate-y-1 hover:border-clay/35 hover:bg-white">
                    <div class="h-48 overflow-hidden"><img src="{{ asset('images/'.($serviceImages[$loop->index] ?? 'packing-team-hero.jpg')) }}" width="724" height="543" alt="{{ $service->name }} by Packers Nepal" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy"></div>
                    <div class="flex grow flex-col p-6"><h3 class="display text-2xl">{{ $service->name }}</h3><p class="mt-4 grow text-sm leading-6 text-forest/65">{{ $service->description }}</p>
                    <a href="{{ route('services.show', $service) }}" class="mt-8 flex items-center justify-between border-t border-forest/10 pt-5 text-sm font-bold transition group-hover:text-clay" aria-label="View {{ $service->name }} details">View service details <span aria-hidden="true">↗</span></a></div>
                </article>
            @endforeach
        </div>
    </section>
    @endif

    @if($homeVisibility['path'])
    <section class="bg-mist">
        <div class="shell grid gap-12 py-24 lg:grid-cols-[.75fr_1.25fr] lg:items-start">
            <div><p class="eyebrow">{{ $homeContent['path_eyebrow'] }}</p><h2 class="section-heading">{{ $homeContent['path_title'] }}</h2><p class="mt-6 max-w-md leading-7 text-forest/65">{{ $homeContent['path_description'] }}</p><a href="{{ route('page.show', 'how-it-works') }}" class="text-link mt-8">See how it works <span aria-hidden="true">↗</span></a></div>
            <ol class="grid gap-4 sm:grid-cols-2">
                @foreach($homeContent['path_steps'] as $step)
                    <li class="rounded-2xl border border-forest/10 bg-ivory p-6"><span class="text-xs font-bold text-clay">STEP {{ sprintf('%02d', $loop->iteration) }}</span><h3 class="display mt-5 text-2xl">{{ $step['title'] }}</h3><p class="mt-3 text-sm leading-6 text-forest/65">{{ $step['description'] }}</p></li>
                @endforeach
            </ol>
        </div>
    </section>
    @endif

    @if($homeVisibility['primary_cta'])
    <section class="shell py-24"><div class="relative overflow-hidden rounded-[2rem] bg-forest p-8 text-white shadow-[0_30px_70px_-36px_rgba(143,48,48,.7)] sm:p-12 lg:p-16"><div class="absolute -right-20 -top-28 h-80 w-80 rounded-full border-[55px] border-leaf/10"></div><div class="relative flex flex-col justify-between gap-10 lg:flex-row lg:items-end"><div><p class="text-xs font-bold uppercase tracking-[.22em] text-leaf">{{ $homeContent['primary_cta_eyebrow'] }}</p><h2 class="display mt-5 max-w-2xl text-4xl leading-tight sm:text-5xl">{{ $homeContent['primary_cta_title'] }}</h2><p class="mt-5 max-w-xl leading-7 text-white/75">{{ $homeContent['primary_cta_description'] }}</p></div><a href="{{ url($homeContent['primary_cta_url']) }}" class="button button-light shrink-0">{{ $homeContent['primary_cta_button'] }} <span aria-hidden="true">↗</span></a></div></div></section>
    @endif
    @if($homeVisibility['about'])
    <section class="shell py-24"><div class="grid gap-12 lg:grid-cols-2 lg:items-center"><div class="relative min-h-[460px] overflow-hidden rounded-2xl bg-forest"><img src="{{ $homeAboutImage }}" alt="{{ $homeContent['about_image_alt'] }}" class="absolute inset-0 h-full w-full object-cover object-right"><div class="absolute inset-0 bg-gradient-to-t from-forest-deep/75 to-transparent"></div><div class="absolute bottom-6 left-6 rounded-xl bg-clay px-5 py-4 text-white"><strong class="block text-3xl">100%</strong><span class="text-xs font-bold uppercase tracking-wider">Packing focused</span></div></div><div><p class="eyebrow">{{ $homeContent['about_eyebrow'] }}</p><h2 class="section-heading">{{ $homeContent['about_title'] }} <span class="text-clay">{{ $homeContent['about_accent'] }}</span></h2><p class="mt-6 leading-7 text-forest/65">{{ $homeContent['about_description'] }}</p><div class="mt-8 grid grid-cols-2 gap-6 border-y border-forest/10 py-7"><div><strong class="display text-4xl text-clay">{{ $homeContent['about_stat_one_value'] }}</strong><p class="mt-1 text-sm font-bold">{{ $homeContent['about_stat_one_label'] }}</p></div><div><strong class="display text-4xl text-clay">{{ $homeContent['about_stat_two_value'] }}</strong><p class="mt-1 text-sm font-bold">{{ $homeContent['about_stat_two_label'] }}</p></div></div><a href="{{ route('page.show', 'about') }}" class="button mt-8">More about us <span aria-hidden="true">↗</span></a></div></div></section>
    @endif

    @if($homeVisibility['process'])
    <section class="bg-forest py-24 text-white"><div class="shell"><div class="mx-auto max-w-2xl text-center"><p class="eyebrow justify-center !text-white/65">{{ $homeContent['process_eyebrow'] }}</p><h2 class="section-heading">{{ $homeContent['process_title'] }} <span class="text-clay">{{ $homeContent['process_accent'] }}</span></h2></div><div class="mt-14 grid gap-5 md:grid-cols-4">@foreach($homeContent['process_items'] as $item)<article class="rounded-2xl border border-white/15 bg-white/5 p-6 text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-clay text-sm font-extrabold">{{ sprintf('%02d', $loop->iteration) }}</span><h3 class="mt-6 text-lg font-extrabold">{{ $item['title'] }}</h3><p class="mt-3 text-sm leading-6 text-white/60">{{ $item['description'] }}</p></article>@endforeach</div></div></section>
    @endif

    @if($homeVisibility['benefits'])
    <section class="shell py-24"><div class="grid gap-12 lg:grid-cols-[.85fr_1.15fr]"><div><p class="eyebrow">{{ $homeContent['benefits_eyebrow'] }}</p><h2 class="section-heading">{{ $homeContent['benefits_title'] }} <span class="text-clay">{{ $homeContent['benefits_accent'] }}</span></h2><p class="mt-6 leading-7 text-forest/65">{{ $homeContent['benefits_description'] }}</p></div><div class="grid gap-4 sm:grid-cols-2">@foreach($homeContent['benefits'] as $benefit)<div class="surface p-6"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-clay text-white">✓</span><h3 class="mt-5 font-extrabold">{{ $benefit['title'] }}</h3><p class="mt-2 text-sm leading-6 text-forest/60">{{ $benefit['description'] }}</p></div>@endforeach</div></div></section>
    @endif

    @if($homeVisibility['faq'])
    <section class="bg-mist py-24"><div class="shell grid gap-12 lg:grid-cols-2"><div><p class="eyebrow">{{ $homeContent['faq_eyebrow'] }}</p><h2 class="section-heading">{{ $homeContent['faq_title'] }} <span class="text-clay">{{ $homeContent['faq_accent'] }}</span></h2><p class="mt-5 max-w-md leading-7 text-forest/65">{{ $homeContent['faq_description'] }}</p></div><div class="space-y-3">@foreach($homeContent['faqs'] as $faq)<details class="group rounded-xl border border-forest/10 bg-white p-5"><summary class="flex cursor-pointer list-none items-center justify-between gap-5 font-extrabold">{{ $faq['question'] }}<span class="text-clay transition group-open:rotate-45">+</span></summary><p class="mt-4 pr-8 text-sm leading-6 text-forest/65">{{ $faq['answer'] }}</p></details>@endforeach</div></div></section>
    @endif

    @if($homeVisibility['testimonials'])
    <section class="shell py-24"><div class="mx-auto max-w-2xl text-center"><p class="eyebrow justify-center">{{ $homeContent['testimonials_eyebrow'] }}</p><h2 class="section-heading">{{ $homeContent['testimonials_title'] }} <span class="text-clay">{{ $homeContent['testimonials_accent'] }}</span></h2></div><div class="mx-auto mt-12 grid max-w-5xl gap-5 md:grid-cols-2">@foreach($activeTestimonials->take(2) as $testimonial)<blockquote class="surface flex flex-col p-8"><div class="text-sm tracking-[.14em] text-clay" aria-label="{{ $testimonial->rating }} out of 5 stars">{{ str_repeat('★', $testimonial->rating) }}</div><p class="mt-5 grow text-lg leading-8">“{{ $testimonial->review }}”</p><footer class="mt-6 border-t border-forest/10 pt-5"><strong class="block font-extrabold">{{ $testimonial->customer_name }}</strong><span class="mt-1 block text-sm text-forest/55">{{ $testimonial->customer_detail }}</span></footer></blockquote>@endforeach</div><div class="mt-9 text-center"><a href="{{ route('testimonials.index') }}" class="text-link">Read all customer reviews <span aria-hidden="true">↗</span></a></div></section>
    @endif
</x-layouts.site>
