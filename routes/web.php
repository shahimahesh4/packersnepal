<?php

use App\Livewire\ContactRequest;
use App\Livewire\QuoteRequest;
use App\Models\Page;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');
Route::get('/request-quote', QuoteRequest::class)->name('quote.request');
Route::get('/contact', ContactRequest::class)->name('contact');
Route::get('/testimonials', fn () => view('testimonials', [
    'testimonials' => Testimonial::query()->where('is_active', true)->orderBy('sort_order')->get(),
]))->name('testimonials.index');
Route::get('/services/{service:slug}', function (Service $service) {
    abort_unless($service->is_active, 404);

    return view('service', compact('service'));
})->name('services.show');
Route::get('/pages/{slug}', function (string $slug) {
    $page = Page::where('slug', $slug)->whereNotNull('published_at')->firstOrFail();

    return view('page', compact('page'));
})->name('page.show');

Route::get('/sitemap.xml', function () {
    $urls = collect([
        ['loc' => route('home'), 'lastmod' => WebsiteSetting::query()->max('updated_at'), 'priority' => '1.0'],
        ['loc' => route('quote.request'), 'lastmod' => null, 'priority' => '0.8'],
        ['loc' => route('contact'), 'lastmod' => null, 'priority' => '0.8'],
        ['loc' => route('testimonials.index'), 'lastmod' => Testimonial::query()->max('updated_at'), 'priority' => '0.7'],
    ])->merge(Service::query()->where('is_active', true)->where('robots_index', true)->get()->map(fn (Service $service) => [
        'loc' => route('services.show', $service), 'lastmod' => $service->updated_at, 'priority' => '0.9',
    ]))->merge(Page::query()->whereNotNull('published_at')->where('published_robots_index', true)->get()->map(fn (Page $page) => [
        'loc' => route('page.show', $page->slug), 'lastmod' => $page->updated_at, 'priority' => '0.8',
    ]));

    return response()->view('sitemap', compact('urls'))->header('Content-Type', 'application/xml');
})->name('sitemap');
