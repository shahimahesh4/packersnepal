<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages\ManageTestimonials;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationLabel = 'Testimonials';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('customer_name')->label('Customer name')->required()->maxLength(120),
            TextInput::make('customer_detail')->label('Role or location')->maxLength(180)->helperText('Example: Household packing client · Kathmandu'),
            Textarea::make('review')->required()->rows(6)->minLength(20)->maxLength(1200)->columnSpanFull(),
            Select::make('rating')->options([5 => '5 stars', 4 => '4 stars', 3 => '3 stars', 2 => '2 stars', 1 => '1 star'])->default(5)->required(),
            TextInput::make('sort_order')->numeric()->minValue(0)->default(0)->required(),
            FileUpload::make('photo')->image()->maxSize(3072)->imageResizeMode('contain')->imageResizeTargetWidth('800')->imageResizeTargetHeight('800')->disk('public')->directory('testimonials')->visibility('public')->imageEditor()->avatar(),
            Toggle::make('is_active')->label('Show on website')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('customer_name')->label('Customer')->searchable(),
            TextColumn::make('customer_detail')->label('Role / location')->limit(35),
            TextColumn::make('rating')->formatStateUsing(fn (int $state): string => str_repeat('★', $state))->color('warning'),
            IconColumn::make('is_active')->label('Visible')->boolean(),
            TextColumn::make('sort_order')->label('Order')->sortable(),
            TextColumn::make('updated_at')->since(),
        ])->defaultSort('sort_order')->filters([
            TernaryFilter::make('is_active')->label('Website visibility'),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageTestimonials::route('/')];
    }
}
