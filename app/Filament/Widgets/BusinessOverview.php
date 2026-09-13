<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Inquiries\InquiryResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Inquiry;
use App\Models\Page;
use App\Models\Service;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Packers Nepal at a glance';

    protected ?string $description = 'Live activity from the website and content system.';

    protected function getStats(): array
    {
        $quotes = Inquiry::query()->where('inquiry_type', 'quote')->count();
        $messages = Inquiry::query()->where('inquiry_type', 'contact')->count();

        return [
            Stat::make('New requests', Inquiry::query()->where('status', 'new')->count())
                ->description("{$quotes} quotes · {$messages} messages")
                ->descriptionIcon(Heroicon::OutlinedInbox)
                ->color('danger')
                ->url(auth()->user()?->permits('inquiries.manage') ? InquiryResource::getUrl() : null),
            Stat::make('All inquiries', Inquiry::query()->count())
                ->description('Customer requests in the database')
                ->descriptionIcon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('info')
                ->url(auth()->user()?->permits('inquiries.manage') ? InquiryResource::getUrl() : null),
            Stat::make('Active services', Service::query()->where('is_active', true)->count())
                ->description('Visible on the public website')
                ->descriptionIcon(Heroicon::OutlinedCube)
                ->color('success')
                ->url(auth()->user()?->permits('services.manage') ? ServiceResource::getUrl() : null),
            Stat::make('Published pages', Page::query()->whereNotNull('published_at')->count())
                ->description('Public website pages')
                ->descriptionIcon(Heroicon::OutlinedDocumentText)
                ->color('warning')
                ->url(auth()->user()?->permits('pages.view') ? PageResource::getUrl() : null),
        ];
    }
}
