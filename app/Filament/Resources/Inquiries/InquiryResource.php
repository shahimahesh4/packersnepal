<?php

namespace App\Filament\Resources\Inquiries;

use App\Filament\Resources\Inquiries\Pages\ManageInquiries;
use App\Models\Inquiry;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static bool $isGloballySearchable = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Quotes & Messages';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reference')->disabled(),
                TextInput::make('name')->disabled(),
                TextInput::make('email')->disabled(),
                TextInput::make('phone')->disabled(),
                TextInput::make('inquiry_type')->label('Type')->disabled(),
                TextInput::make('subject')->disabled(),
                TextInput::make('preferred_date')->disabled(),
                Textarea::make('address')->disabled(),
                Textarea::make('details')->disabled()->columnSpanFull(),
                Select::make('status')->options(['new' => 'New', 'contacted' => 'Contacted', 'assessing' => 'Assessing', 'closed' => 'Closed'])->required(),
                Textarea::make('staff_notes')->maxLength(10000)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->searchable()->copyable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('inquiry_type')->label('Type')->badge(),
                TextColumn::make('subject')->limit(35),
                TextColumn::make('service.name'),
                TextColumn::make('preferred_date')->date(),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(['new' => 'New', 'contacted' => 'Contacted', 'assessing' => 'Assessing', 'closed' => 'Closed']),
                SelectFilter::make('inquiry_type')->label('Type')->options(['quote' => 'Quote request', 'contact' => 'Contact message']),
            ])
            ->recordActions([
                EditAction::make()->label('Review'),
            ])
            ->toolbarActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInquiries::route('/'),
        ];
    }
}
