@props(['title' => null, 'metaDescription' => null, 'seoImage' => null, 'schema' => [], 'canonical' => null, 'indexable' => true, 'follow' => true, 'socialTitle' => null, 'socialDescription' => null])
@php
    $siteName = $siteSettings?->site_name ?? 'Packers Nepal';
    $siteTagline = $siteSettings?->tagline ?? 'Care in every layer';
    $sitePhone = $siteSettings?->phone ?? '9801010000';
    $sitePhoneHref = preg_replace('/[^0-9+]/', '', $sitePhone);
    $title = $title ?: ($siteSettings?->default_seo_title ?? 'Professional Packing Services in Nepal | Packers Nepal');
    $metaDescription = $metaDescription ?: ($siteSettings?->default_meta_description ?? 'Packing, wrapping, and labeling for homes and businesses. Tell Packers Nepal what needs protecting and request a packing quote.');
    $seoTitle = str_contains(strtolower($title), strtolower($siteName)) ? $title : $title.' | '.$siteName;
    $canonicalUrl = $canonical ?: url()->current();
    $socialImage = $seoImage ? asset($seoImage) : asset('images/packers-nepal-logo.png');
    $socialTitle = $socialTitle ?: $seoTitle;
    $socialDescription = $socialDescription ?: $metaDescription;
    $robots = ($indexable ? 'index' : 'noindex').', '.($follow ? 'follow' : 'nofollow').', max-image-preview:large';
    $sameAs = array_values(array_filter([$siteSettings?->facebook_url, $siteSettings?->instagram_url, $siteSettings?->youtube_url, $siteSettings?->x_url, $siteSettings?->tiktok_url]));
    $structuredData = array_merge([
        [
            '@context' => 'https://schema.org', '@type' => 'WebSite', '@id' => url('/').'#website',
            'url' => url('/'), 'name' => $siteName, 'description' => $metaDescription, 'inLanguage' => 'en-NP',
        ],
        [
            '@context' => 'https://schema.org', '@type' => ['LocalBusiness', 'ProfessionalService'], '@id' => url('/').'#business',
            'name' => $siteName, 'url' => url('/'), 'logo' => asset('images/packers-nepal-logo.png'), 'image' => $socialImage,
            'description' => $siteSettings?->business_description ?: $metaDescription,
            'telephone' => $sitePhone, 'email' => $siteSettings?->email ?? 'info@packersnepal.com',
            'address' => ['@type' => 'PostalAddress', 'streetAddress' => $siteSettings?->address ?? 'Newroad', 'addressLocality' => 'Kathmandu', 'addressCountry' => 'NP'],
            'areaServed' => $siteSettings?->service_area ?: 'Nepal', 'sameAs' => $sameAs,
        ],
    ], $schema);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="{{ $robots }}">
    <meta name="theme-color" content="#082f35">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" type="image/png" href="{{ asset('images/packers-nepal-logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/packers-nepal-logo.png') }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $socialTitle }}">
    <meta property="og:description" content="{{ $socialDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="en_NP">
    <meta property="og:image" content="{{ $socialImage }}">
    <meta property="og:image:type" content="{{ str_ends_with(parse_url($socialImage, PHP_URL_PATH) ?? '', '.png') ? 'image/png' : 'image/jpeg' }}">
    <meta property="og:image:alt" content="{{ $seoTitle }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $socialTitle }}">
    <meta name="twitter:description" content="{{ $socialDescription }}">
    <meta name="twitter:image" content="{{ $socialImage }}">
    <meta name="twitter:image:alt" content="{{ $seoTitle }}">

    @foreach($structuredData as $schemaItem)
        <script type="application/ld+json">{!! json_encode($schemaItem, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    @endforeach

    @vite(['resources/css/app.css', 'resources/js/app.js']) @livewireStyles
</head>
<body class="min-h-screen">
<a href="#main" class="sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:not-sr-only focus:rounded-lg focus:bg-white focus:px-4 focus:py-3 focus:shadow-xl">Skip to content</a>
<header class="sticky top-0 z-[100] border-b border-white/10 bg-forest text-white"><div class="shell flex min-h-20 items-center justify-between gap-5 py-4">
    <a wire:navigate href="{{ route('home') }}" class="group flex shrink-0 items-center" aria-label="{{ $siteName }} home"><img src="{{ asset('images/packers-nepal-logo.png') }}" width="800" height="267" alt="{{ $siteName }} — {{ $siteTagline }}" class="h-16 w-auto max-w-[230px] object-contain transition duration-300 group-hover:scale-[1.02] sm:h-20 sm:max-w-[320px]"></a>
    <nav aria-label="Main navigation" class="hidden items-center gap-7 text-sm font-bold md:flex">
        <a wire:navigate href="{{ route('home') }}" class="transition hover:text-leaf">Home</a>
        <a wire:navigate href="{{ route('page.show', 'about') }}" class="transition hover:text-leaf">About Us</a>
        <div class="relative" x-data="{ servicesOpen: false }" @keydown.escape.window="servicesOpen = false" @click.outside="servicesOpen = false">
            <button type="button" @click="servicesOpen = ! servicesOpen" @mouseenter="servicesOpen = true" class="flex items-center gap-1.5 py-3 transition hover:text-leaf" :aria-expanded="servicesOpen" aria-haspopup="true" aria-controls="desktop-services-menu">
                Services
                <svg class="transition duration-200" :class="servicesOpen && 'rotate-180'" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <div id="desktop-services-menu" x-cloak x-show="servicesOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-2 opacity-0" @mouseenter="servicesOpen = true" @mouseleave="servicesOpen = false" class="absolute left-1/2 top-full z-50 w-72 -translate-x-1/2 overflow-hidden rounded-2xl border border-forest/10 bg-white p-2 text-forest shadow-2xl" role="menu" aria-label="Services menu">
                <div class="border-b border-forest/10 px-4 py-3"><p class="text-[.65rem] font-extrabold uppercase tracking-[.18em] text-clay">Packing services</p><p class="mt-1 text-xs font-medium leading-5 text-forest/55">Careful preparation for homes and businesses.</p></div>
                <div class="py-2">
                    @foreach($activeServices as $menuService)
                        <a href="{{ route('services.show', $menuService) }}" class="group flex items-center gap-3 rounded-xl px-3 py-3 transition hover:bg-sage" role="menuitem"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sage text-clay transition group-hover:bg-white"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3ZM4 7.5l8 4.5 8-4.5M12 12v9"/></svg></span><span class="font-extrabold">{{ $menuService->name }}</span><span class="ml-auto text-clay" aria-hidden="true">↗</span></a>
                    @endforeach
                </div>
                <a href="{{ route('home') }}#services" class="flex items-center justify-between rounded-xl bg-forest px-4 py-3 text-xs font-extrabold text-white transition hover:bg-clay" role="menuitem"><span>View all services</span><span aria-hidden="true">→</span></a>
            </div>
        </div>
        <a wire:navigate href="{{ route('page.show', 'how-it-works') }}" class="transition hover:text-leaf">How It Works</a>
        <a wire:navigate href="{{ route('contact') }}" class="transition hover:text-leaf">Contact</a>
        <a wire:navigate href="{{ route('quote.request') }}" class="button !min-h-11 !px-5 !py-2.5">Request a quote <span aria-hidden="true">↗</span></a>
    </nav>
    @include('components.mobile-menu')
</div></header>
<main id="main">{{ $slot }}</main>
<footer class="bg-forest-deep text-white">
    <div class="shell grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-[1.2fr_.65fr_.8fr_1fr]">
        <div><img src="{{ asset('images/packers-nepal-logo.png') }}" width="800" height="267" alt="{{ $siteName }}" class="h-16 w-auto max-w-[270px] object-contain" loading="lazy"><p class="mt-5 max-w-sm text-sm leading-7 text-white/65">{{ $siteSettings?->business_description ?? 'Professional packing, wrapping, and labeling for the things that matter. Your goods leave the site prepared for your chosen transporter.' }}</p><div class="mt-6 flex flex-wrap gap-2" aria-label="{{ $siteName }} social media">
            @if($siteSettings?->facebook_url)<a href="{{ $siteSettings->facebook_url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $siteName }} on Facebook" class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/15 text-white/70 transition hover:border-clay hover:bg-clay hover:text-white"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 8h3V4.5c-.5-.1-2.2-.2-4.1-.2-4 0-6.7 2.4-6.7 6.9V15H2v4h4.2v10h5.1V19h4.2l.7-4h-4.9v-3.4C11.3 10.4 11.7 8 14 8Z" transform="scale(.8) translate(3 -3)"/></svg></a>@endif
            @if($siteSettings?->instagram_url)<a href="{{ $siteSettings->instagram_url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $siteName }} on Instagram" class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/15 text-white/70 transition hover:border-clay hover:bg-clay hover:text-white"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></a>@endif
            @if($siteSettings?->youtube_url)<a href="{{ $siteSettings->youtube_url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $siteName }} on YouTube" class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/15 text-white/70 transition hover:border-clay hover:bg-clay hover:text-white"><svg width="19" height="19" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23 7s-.2-1.6-.9-2.3c-.9-.9-1.8-.9-2.3-1C16.6 3.5 12 3.5 12 3.5s-4.6 0-7.8.2c-.5.1-1.4.1-2.3 1C1.2 5.4 1 7 1 7S.8 8.9.8 10.8v1.8c0 1.9.2 3.8.2 3.8s.2 1.6.9 2.3c.9.9 2.1.9 2.6 1 1.9.2 7.5.2 7.5.2s4.6 0 7.8-.2c.5-.1 1.4-.1 2.3-1 .7-.7.9-2.3.9-2.3s.2-1.9.2-3.8v-1.8C23.2 8.9 23 7 23 7ZM9.7 15.2V8.7l6 3.3-6 3.2Z"/></svg></a>@endif
            @if($siteSettings?->x_url)<a href="{{ $siteSettings->x_url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $siteName }} on X" class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/15 text-white/70 transition hover:border-clay hover:bg-clay hover:text-white"><svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.9 2H22l-6.8 7.8L23.2 22H17l-4.9-6.4L6.6 22H3.5l7.2-8.2L3 2h6.4l4.4 5.8L18.9 2Zm-1.1 17.8h1.7L8.5 4.1H6.7l11.1 15.7Z"/></svg></a>@endif
            @if($siteSettings?->tiktok_url)<a href="{{ $siteSettings->tiktok_url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $siteName }} on TikTok" class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/15 text-white/70 transition hover:border-clay hover:bg-clay hover:text-white"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.7 2c.4 2.4 1.8 3.8 4.3 4v3.4c-1.4.1-2.7-.3-4.2-1.2v6.4c0 8.1-8.8 10.6-12.4 4.8-2.3-3.8-.9-10.4 5.6-11v3.6c-.5.1-1 .2-1.5.4-1.4.5-2.2 1.5-2 3.3.4 3.5 6.9 4.6 6.4-2.3V2h3.8Z"/></svg></a>@endif
        </div></div>
        <div><p class="text-xs font-bold uppercase tracking-[.2em] text-leaf">Explore</p><div class="mt-5 flex flex-col gap-3 text-sm text-white/75"><a href="{{ route('home') }}">Home</a><a href="{{ route('home') }}#services">Services</a><a href="{{ route('page.show', 'how-it-works') }}">How it works</a><a href="{{ route('page.show', 'about') }}">About us</a><a href="{{ route('testimonials.index') }}">Testimonials</a></div></div>
        <div><p class="text-xs font-bold uppercase tracking-[.2em] text-leaf">Services</p><div class="mt-5 flex flex-col gap-3 text-sm text-white/75">@foreach($activeServices->take(5) as $footerService)<a href="{{ route('services.show', $footerService) }}" class="hover:text-white">{{ $footerService->name }}</a>@endforeach</div></div>
        <div><p class="text-xs font-bold uppercase tracking-[.2em] text-leaf">Contact us</p><address class="mt-5 flex flex-col gap-4 text-sm not-italic text-white/75">
            <span class="flex items-start gap-3"><svg class="mt-0.5 shrink-0 text-clay" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg><span>{{ $siteSettings?->address ?? 'New Road, Kathmandu' }}</span></span>
            <a href="tel:{{ $sitePhoneHref }}" class="flex items-center gap-3 hover:text-white"><svg class="shrink-0 text-clay" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.8a2 2 0 0 1-.5 2.1L8.1 9.8a16 16 0 0 0 6 6l1.2-1.2a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.8 2.1Z"/></svg><span>{{ $sitePhone }}</span></a>
            <a href="mailto:{{ $siteSettings?->email ?? 'info@packersnepal.com' }}" class="flex items-start gap-3 break-all hover:text-white"><svg class="mt-0.5 shrink-0 text-clay" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg><span>{{ $siteSettings?->email ?? 'info@packersnepal.com' }}</span></a>
            <a href="{{ route('contact') }}" class="flex items-center gap-3 hover:text-white"><svg class="shrink-0 text-clay" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/><path d="M8 9h8M8 13h5"/></svg><span>Send a message</span></a>
        </address></div>
    </div>
    <div class="shell flex flex-col gap-3 border-t border-white/10 py-6 text-xs text-white/45 sm:flex-row sm:justify-between"><p>© {{ now()->year }} {{ $siteName }}</p><p>Packing service only · Transportation arranged separately</p></div>
</footer>
@livewireScripts
</body></html>
