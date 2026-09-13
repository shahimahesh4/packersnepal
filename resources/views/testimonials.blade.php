<x-layouts.site title="Customer Reviews of Our Packing Services" meta-description="Read customer reviews of Packers Nepal household, office, fragile-item and business packing services in Kathmandu Valley and across Nepal.">
    <section class="relative overflow-hidden bg-forest text-white">
        <div class="absolute inset-0 opacity-20 [background-image:radial-gradient(circle_at_1px_1px,white_1px,transparent_0)] [background-size:28px_28px]"></div>
        <div class="shell relative grid gap-10 py-20 lg:grid-cols-[1fr_.7fr] lg:items-end lg:py-28">
            <div><p class="eyebrow !text-white/65">Customer experiences</p><h1 class="display mt-6 max-w-4xl text-5xl leading-tight sm:text-6xl lg:text-7xl">Care people can <span class="text-clay">feel and trust.</span></h1><p class="mt-7 max-w-2xl text-lg leading-8 text-white/70">Read what customers value about our careful preparation, organized labels, clear scope, and professional handover.</p></div>
            <div class="rounded-2xl border border-white/15 bg-white/5 p-7 backdrop-blur"><div class="flex text-xl tracking-[.18em] text-clay" aria-label="Five-star service">★★★★★</div><p class="mt-4 text-sm leading-7 text-white/65">Every published review is managed by authorized Packers Nepal staff through the website backend.</p></div>
        </div>
    </section>

    <section class="shell py-20 lg:py-28">
        <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end"><div><p class="eyebrow">Words from our customers</p><h2 class="section-heading max-w-3xl">Packing service built around <span class="text-clay">care and clarity.</span></h2></div><p class="max-w-md text-sm leading-7 text-forest/60">Experiences from household, office, fragile-item, and business packing customers.</p></div>

        @if($testimonials->isEmpty())
            <div class="mt-12 rounded-2xl border border-forest/10 bg-mist p-10 text-center"><h2 class="display text-2xl">Customer stories are coming soon.</h2><p class="mt-3 text-forest/60">Please check back as we add verified packing experiences.</p></div>
        @else
            <div class="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($testimonials as $testimonial)
                    <article class="surface group flex h-full flex-col p-7 transition duration-300 hover:-translate-y-1 hover:border-clay/30 hover:shadow-xl">
                        <div class="flex items-center justify-between gap-5"><div class="flex text-sm tracking-[.14em] text-clay" aria-label="{{ $testimonial->rating }} out of 5 stars">{{ str_repeat('★', $testimonial->rating) }}<span class="text-forest/15">{{ str_repeat('★', 5 - $testimonial->rating) }}</span></div><svg class="text-clay/30" width="36" height="28" viewBox="0 0 36 28" fill="currentColor" aria-hidden="true"><path d="M0 28V16.8C0 5.6 5.1 0 15.2 0v6.2c-4.6.4-7 2.8-7.3 7.2h7.3V28H0Zm20.8 0V16.8C20.8 5.6 25.9 0 36 0v6.2c-4.6.4-7 2.8-7.3 7.2H36V28H20.8Z"/></svg></div>
                        <blockquote class="mt-7 grow text-lg leading-8 text-forest/75">“{{ $testimonial->review }}”</blockquote>
                        <footer class="mt-8 flex items-center gap-4 border-t border-forest/10 pt-6">
                            @if($testimonial->photo)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($testimonial->photo) }}" alt="{{ $testimonial->customer_name }}" class="h-12 w-12 rounded-full object-cover" loading="lazy">@else<span class="flex h-12 w-12 items-center justify-center rounded-full bg-clay font-extrabold text-white">{{ str($testimonial->customer_name)->substr(0, 1)->upper() }}</span>@endif
                            <span><strong class="block font-extrabold">{{ $testimonial->customer_name }}</strong>@if($testimonial->customer_detail)<span class="mt-1 block text-xs text-forest/50">{{ $testimonial->customer_detail }}</span>@endif</span>
                        </footer>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="shell pb-24"><div class="relative overflow-hidden rounded-2xl bg-clay p-8 text-white sm:p-12"><div class="absolute -right-12 -top-20 h-64 w-64 rounded-full border-[45px] border-white/10"></div><div class="relative flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between"><div><p class="text-xs font-bold uppercase tracking-[.22em] text-white/70">Ready when you are</p><h2 class="display mt-4 max-w-2xl text-4xl sm:text-5xl">Experience careful packing for yourself.</h2><p class="mt-4 max-w-xl leading-7 text-white/80">Tell us what needs protection and our team will review the scope with you.</p></div><a href="{{ route('quote.request') }}" class="button button-light shrink-0">Request a packing quote <span aria-hidden="true">↗</span></a></div></div></section>
</x-layouts.site>
