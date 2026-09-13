<?php

namespace App\Providers;

use App\Models\Service;
use App\Models\Testimonial;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['components.layouts.site', 'livewire.contact-request', 'home'], function ($view): void {
            $request = request();
            $settings = $request->attributes->get('site-settings.model');
            if (! $request->attributes->has('site-settings.loaded')) {
                $settings = WebsiteSetting::query()->first();
                $request->attributes->set('site-settings.model', $settings);
                $request->attributes->set('site-settings.loaded', true);
            }

            $services = $request->attributes->get('site-services');
            if ($services === null) {
                $services = Service::query()->where('is_active', true)->orderBy('sort_order')->get();
                $request->attributes->set('site-services', $services);
            }

            $view->with('siteSettings', $settings);
            $view->with('activeServices', $services);
        });

        View::composer('home', function ($view): void {
            $services = request()->attributes->get('site-services');
            if ($services === null) {
                $services = Service::query()->where('is_active', true)->orderBy('sort_order')->get();
                request()->attributes->set('site-services', $services);
            }

            $testimonials = request()->attributes->get('site-testimonials');
            if ($testimonials === null) {
                $testimonials = Testimonial::query()->where('is_active', true)->orderBy('sort_order')->get();
                request()->attributes->set('site-testimonials', $testimonials);
            }

            $view->with('services', $services);
            $view->with('activeTestimonials', $testimonials);
        });
    }
}
