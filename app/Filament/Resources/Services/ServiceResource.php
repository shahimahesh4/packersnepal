<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Services\Pages\ManageServices;
use App\Models\Service;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Services';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Service controls')->tabs([
                    Tab::make('Service Page')->icon(Heroicon::OutlinedCube)->schema([
                        TextInput::make('name')->required()->maxLength(255),
                        TextInput::make('slug')->required()->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique(ignoreRecord: true)->maxLength(120),
                        Textarea::make('description')->required()->maxLength(2000)->columnSpanFull(),
                        Textarea::make('details')->label('Detailed page content')->rows(16)->maxLength(30000)->columnSpanFull()->helperText('Use short uppercase lines as section headings.'),
                    ])->columns(2),
                    Tab::make('Banner')->icon(Heroicon::OutlinedPhoto)->schema([
                        TextInput::make('page_content.banner_eyebrow')->label('Small heading')->required()->maxLength(120),
                        TextInput::make('page_content.banner_title')->label('Custom main heading')->maxLength(180)->helperText('Leave empty to use the service name.'),
                        Textarea::make('page_content.banner_description')->label('Custom description')->rows(4)->maxLength(700)->columnSpanFull()->helperText('Leave empty to use the service summary.'),
                        FileUpload::make('banner_image')->label('Banner image')->image()->maxSize(3072)->imageResizeMode('contain')->imageResizeTargetWidth('1920')->imageResizeTargetHeight('1920')->disk('public')->directory('service-banners')->visibility('public')->imageEditor()->columnSpanFull(),
                        TextInput::make('page_content.banner_image_alt')->label('Image description')->maxLength(180)->columnSpanFull(),
                        TextInput::make('page_content.banner_button_label')->label('Button text')->required()->maxLength(80),
                        TextInput::make('page_content.banner_button_url')->label('Button URL')->required()->maxLength(255),
                    ])->columns(2),
                    Tab::make('Page Sections')->icon(Heroicon::OutlinedPencilSquare)->schema([
                        Section::make('Detailed content introduction')->schema([
                            TextInput::make('page_content.details_eyebrow')->label('Small heading')->required(), TextInput::make('page_content.details_title')->label('Main heading')->required(), TextInput::make('page_content.details_accent')->label('Highlighted heading'),
                            TextInput::make('page_content.notice_title')->label('Notice heading')->required(), Textarea::make('page_content.notice_text')->label('Notice text')->required()->columnSpanFull(),
                        ])->columns(2)->collapsible(),
                        Section::make('What to expect')->schema([
                            TextInput::make('page_content.expect_eyebrow')->label('Small heading')->required(), TextInput::make('page_content.expect_title')->label('Main heading')->required(), TextInput::make('page_content.expect_accent')->label('Highlighted heading'),
                            Repeater::make('page_content.expect_cards')->label('Expectation cards')->schema([TextInput::make('title')->required(), Textarea::make('description')->required()])->minItems(1)->maxItems(6)->columns(2)->columnSpanFull(),
                        ])->columns(2)->collapsible(),
                        Section::make('Final call to action')->schema([
                            TextInput::make('page_content.cta_eyebrow')->label('Small heading')->helperText('Leave empty to use “Request [service name]”.'), TextInput::make('page_content.cta_title')->label('Main heading')->required(),
                            Textarea::make('page_content.cta_description')->label('Description')->required()->columnSpanFull(), FileUpload::make('cta_image')->label('CTA image')->image()->maxSize(3072)->imageResizeMode('contain')->imageResizeTargetWidth('1920')->imageResizeTargetHeight('1920')->disk('public')->directory('service-cta')->visibility('public')->imageEditor()->columnSpanFull(),
                            TextInput::make('page_content.cta_image_alt')->label('Image description')->columnSpanFull(), TextInput::make('page_content.cta_button_label')->label('Button text')->required(), TextInput::make('page_content.cta_button_url')->label('Button URL')->required(),
                        ])->columns(2)->collapsible(),
                    ]),
                    Tab::make('Search & Sharing')->icon(Heroicon::OutlinedMagnifyingGlass)->schema([
                        TextInput::make('seo_title')->label('SEO title')->maxLength(70)->helperText('Aim for a clear, unique title of about 50–60 characters.'),
                        Textarea::make('meta_description')->label('Meta description')->rows(4)->maxLength(170)->columnSpanFull()->helperText('Summarize the service naturally in about 140–160 characters.'),
                        TextInput::make('canonical_url')->label('Canonical URL')->url()->maxLength(255)->helperText('Leave empty to use this service’s normal public URL.'),
                        Toggle::make('robots_index')->label('Allow search indexing')->default(true),
                        Toggle::make('robots_follow')->label('Allow search engines to follow links')->default(true),
                        TextInput::make('social_title')->label('Social sharing title')->maxLength(100)->helperText('Leave empty to use the SEO title.'),
                        Textarea::make('social_description')->label('Social sharing description')->rows(3)->maxLength(200)->columnSpanFull(),
                        FileUpload::make('social_image')->label('Social sharing image')->image()->maxSize(3072)->imageResizeMode('contain')->imageResizeTargetWidth('1920')->imageResizeTargetHeight('1080')->disk('public')->directory('social')->visibility('public')->imageEditor()->columnSpanFull()->helperText('Use a clear 1200 × 630 image when possible.'),
                    ])->columns(2),
                    Tab::make('Display')->icon(Heroicon::OutlinedEye)->schema([
                        TextInput::make('sort_order')->numeric()->minValue(0)->default(0)->required(),
                        Toggle::make('is_active')->default(true)->helperText('Inactive services are hidden from the public website.'),
                        Toggle::make('section_visibility.banner')->label('Show banner')->default(true),
                        Toggle::make('section_visibility.details')->label('Show detailed content')->default(true),
                        Toggle::make('section_visibility.expectations')->label('Show expectations')->default(true),
                        Toggle::make('section_visibility.cta')->label('Show final call to action')->default(true),
                    ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('sort_order')->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServices::route('/'),
        ];
    }
}
