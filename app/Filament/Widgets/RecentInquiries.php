<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentInquiries extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->permits('inquiries.manage') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent quotes and messages')
            ->description('The latest customer requests submitted through the website.')
            ->query(Inquiry::query()->with('service')->latest())
            ->columns([
                TextColumn::make('reference')->label('Reference')->copyable()->searchable(),
                TextColumn::make('inquiry_type')->label('Type')->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('name')->searchable(),
                TextColumn::make('service.name')->label('Service')->placeholder('General message'),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->label('Received')->since()->sortable(),
            ])
            ->defaultPaginationPageOption(5);
    }
}
