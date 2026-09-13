@php
    $aboutBannerImage = $page->published_banner_image ? \Illuminate\Support\Facades\Storage::disk('public')->url($page->published_banner_image) : asset('images/about-team-banner.jpg').'';
    $aboutContent = array_replace_recursive(\App\Support\AboutPageDefaults::content(), $page->published_about_content ?? []);
    $companyImage = $aboutContent['company_image'] ? \Illuminate\Support\Facades\Storage::disk('public')->url($aboutContent['company_image']) : asset('images/service-household.jpg').'';
    $distinctionImage = $aboutContent['distinction_image'] ? \Illuminate\Support\Facades\Storage::disk('public')->url($aboutContent['distinction_image']) : asset('images/service-business.jpg').'';
@endphp
@if($page->published_show_banner)
<section class="relative min-h-[540px] overflow-hidden bg-forest bg-cover bg-top text-white" style="background-image: url('{{ $aboutBannerImage }}')" role="img" aria-label="{{ $page->published_banner_image_alt ?: 'Packers Nepal packing specialists working carefully together' }}">
    <div class="absolute inset-0 bg-gradient-to-r from-forest-deep via-forest-deep/90 to-forest/20"></div>
    <div class="shell relative flex min-h-[540px] items-center py-20">
        <div class="max-w-3xl"><p class="eyebrow !text-white/65">{{ $page->published_banner_eyebrow ?: 'Meet Packers Nepal' }}</p><h1 class="display mt-6 text-5xl leading-[1.02] sm:text-6xl lg:text-7xl">{{ $page->published_banner_title ?: 'Packing expertise.' }}<br><span class="text-clay">{{ $page->published_banner_accent ?: 'Care in every layer.' }}</span></h1><p class="mt-7 max-w-2xl text-lg leading-8 text-white/70">{{ $page->published_banner_description ?: 'Professional packing for homes and businesses in Kathmandu Valley and across Nepal, prepared carefully for the transporter you choose.' }}</p><a href="{{ route('quote.request') }}" class="button mt-9">Plan your packing <span aria-hidden="true">↗</span></a></div>
    </div>
</section>
@endif

@if($page->published_show_company_story)
<section class="shell py-20 lg:py-28">
    <div class="grid gap-12 lg:grid-cols-[.78fr_1.22fr] lg:items-start">
        <aside class="lg:sticky lg:top-32"><p class="eyebrow">{{ $aboutContent['company_eyebrow'] }}</p><h2 class="section-heading">{{ $aboutContent['company_title'] }}<br><span class="text-clay">{{ $aboutContent['company_accent'] }}</span></h2><div class="mt-8 overflow-hidden rounded-2xl"><img src="{{ $companyImage }}" alt="{{ $aboutContent['company_image_alt'] }}" class="aspect-[4/3] w-full object-cover" loading="lazy"></div><div class="mt-5 grid grid-cols-2 gap-3"><div class="rounded-xl bg-forest p-5 text-white"><strong class="display text-3xl text-clay">{{ $aboutContent['stat_one_value'] }}</strong><span class="mt-1 block text-xs font-bold uppercase tracking-wider text-white/60">{{ $aboutContent['stat_one_label'] }}</span></div><div class="rounded-xl bg-sage p-5"><strong class="display text-3xl">{{ $aboutContent['stat_two_value'] }}</strong><span class="mt-1 block text-xs font-bold uppercase tracking-wider text-forest/55">{{ $aboutContent['stat_two_label'] }}</span></div></div></aside>
        <article class="max-w-3xl">
            @php($blocks = preg_split('/\R{2,}/', trim($page->published_content)))
            @foreach($blocks as $block)
                @if(mb_strlen($block) < 80 && mb_strtoupper($block) === $block)
                    <h2 class="display {{ $loop->first ? '' : 'mt-12' }} text-2xl text-forest sm:text-3xl">{{ Str::headline(Str::lower($block)) }}</h2>
                @else
                    <p class="{{ $loop->first ? '' : 'mt-5' }} text-lg leading-9 text-forest/70">{{ $block }}</p>
                @endif
            @endforeach
        </article>
    </div>
</section>
@endif

@if($page->published_show_standards)
<section class="bg-mist py-20"><div class="shell"><div class="mx-auto max-w-2xl text-center"><p class="eyebrow justify-center">{{ $aboutContent['standards_eyebrow'] }}</p><h2 class="section-heading">{{ $aboutContent['standards_title'] }} <span class="text-clay">{{ $aboutContent['standards_accent'] }}</span></h2></div><div class="mt-12 grid gap-5 md:grid-cols-3">@foreach($aboutContent['standards_cards'] as $value)<article class="surface p-7"><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-clay text-xl font-extrabold text-white">✓</span><h3 class="mt-6 text-xl font-extrabold">{{ $value['title'] }}</h3><p class="mt-3 text-sm leading-7 text-forest/65">{{ $value['description'] }}</p></article>@endforeach</div></div></section>
@endif

@if($page->published_show_distinction)
<section class="shell py-20 lg:py-28"><div class="grid overflow-hidden rounded-2xl bg-forest text-white lg:grid-cols-2"><img src="{{ $distinctionImage }}" alt="{{ $aboutContent['distinction_image_alt'] }}" class="h-full min-h-[380px] w-full object-cover" loading="lazy"><div class="flex flex-col justify-center p-8 sm:p-12"><p class="eyebrow !text-white/65">{{ $aboutContent['distinction_eyebrow'] }}</p><h2 class="display mt-6 text-4xl leading-tight">{{ $aboutContent['distinction_title'] }} <span class="text-clay">{{ $aboutContent['distinction_accent'] }}</span></h2><p class="mt-6 leading-8 text-white/70">{{ $aboutContent['distinction_description'] }}</p><a href="{{ url($aboutContent['distinction_link_url']) }}" class="mt-8 inline-flex w-fit items-center gap-2 border-b border-white/40 pb-1 text-sm font-extrabold hover:border-clay hover:text-leaf">{{ $aboutContent['distinction_link_label'] }} <span aria-hidden="true">↗</span></a></div></div></section>
@endif

@if($page->published_show_cta)
<section class="shell pb-24"><div class="relative overflow-hidden rounded-2xl bg-clay p-8 text-white sm:p-12"><div class="absolute -right-12 -top-20 h-64 w-64 rounded-full border-[45px] border-white/10"></div><div class="relative flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between"><div><p class="text-xs font-bold uppercase tracking-[.22em] text-white/70">{{ $aboutContent['cta_eyebrow'] }}</p><h2 class="display mt-4 max-w-2xl text-4xl sm:text-5xl">{{ $aboutContent['cta_title'] }}</h2><p class="mt-4 max-w-xl leading-7 text-white/80">{{ $aboutContent['cta_description'] }}</p></div><a href="{{ url($aboutContent['cta_button_url']) }}" class="button button-light shrink-0">{{ $aboutContent['cta_button_label'] }} <span aria-hidden="true">↗</span></a></div></div></section>
@endif
