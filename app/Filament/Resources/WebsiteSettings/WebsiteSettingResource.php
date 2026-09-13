<?php

namespace App\Filament\Resources\WebsiteSettings;

use App\Filament\Resources\WebsiteSettings\Pages\ManageWebsiteSettings;
use App\Models\WebsiteSetting;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WebsiteSettingResource extends Resource
{
    protected static ?string $model = WebsiteSetting::class;

    protected static ?string $navigationLabel = 'Website Settings';

    protected static ?string $modelLabel = 'website settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Website settings')->tabs([
                Tab::make('General')->icon(Heroicon::OutlinedBuildingOffice)->schema([
                    TextInput::make('site_name')->required()->maxLength(100),
                    TextInput::make('tagline')->maxLength(150),
                    Textarea::make('business_description')->rows(5)->maxLength(1000)->columnSpanFull(),
                    TextInput::make('service_area')->maxLength(255)->columnSpanFull(),
                ])->columns(2),
                Tab::make('Home Banner')->icon(Heroicon::OutlinedPhoto)->schema([
                    TextInput::make('home_banner_eyebrow')->label('Small heading')->maxLength(150)->columnSpanFull(),
                    TextInput::make('home_banner_title')->label('Main heading')->required()->maxLength(150),
                    TextInput::make('home_banner_accent')->label('Highlighted heading')->maxLength(150),
                    Textarea::make('home_banner_description')->label('Banner description')->required()->rows(4)->maxLength(600)->columnSpanFull(),
                    FileUpload::make('home_banner_image')->label('Background image')->image()->maxSize(3072)->imageResizeMode('contain')->imageResizeTargetWidth('1920')->imageResizeTargetHeight('1920')->disk('public')->directory('banners')->visibility('public')->imageEditor()->columnSpanFull(),
                    TextInput::make('home_banner_image_alt')->label('Image description')->maxLength(180)->columnSpanFull()->helperText('Describe the image for accessibility and search engines.'),
                    TextInput::make('home_banner_primary_label')->label('Primary button text')->maxLength(80),
                    TextInput::make('home_banner_primary_url')->label('Primary button link')->maxLength(255)->helperText('Example: /request-quote'),
                    TextInput::make('home_banner_secondary_label')->label('Secondary button text')->maxLength(80),
                    TextInput::make('home_banner_secondary_url')->label('Secondary button link')->maxLength(255)->helperText('Example: /#services'),
                ])->columns(2),
                Tab::make('Home Sections')->icon(Heroicon::OutlinedPencilSquare)->schema([
                    Section::make('Services introduction')->schema([
                        TextInput::make('home_content.services_eyebrow')->label('Small heading')->required(), TextInput::make('home_content.services_title')->label('Main heading')->required(),
                        TextInput::make('home_content.services_accent')->label('Highlighted heading'), Textarea::make('home_content.services_description')->label('Description')->required()->columnSpanFull(),
                    ])->columns(2)->collapsible(),
                    Section::make('Simple path')->schema([
                        TextInput::make('home_content.path_eyebrow')->label('Small heading')->required(), TextInput::make('home_content.path_title')->label('Main heading')->required(),
                        Textarea::make('home_content.path_description')->label('Description')->required()->columnSpanFull(),
                        Repeater::make('home_content.path_steps')->label('Steps')->schema([TextInput::make('title')->required(), Textarea::make('description')->required()])->minItems(1)->maxItems(6)->columns(2)->columnSpanFull(),
                    ])->columns(2)->collapsible(),
                    Section::make('Primary call to action')->schema([
                        TextInput::make('home_content.primary_cta_eyebrow')->label('Small heading')->required(), TextInput::make('home_content.primary_cta_title')->label('Main heading')->required(),
                        Textarea::make('home_content.primary_cta_description')->label('Description')->required()->columnSpanFull(), TextInput::make('home_content.primary_cta_button')->label('Button text')->required(), TextInput::make('home_content.primary_cta_url')->label('Button URL')->required(),
                    ])->columns(2)->collapsible(),
                    Section::make('About preview')->schema([
                        TextInput::make('home_content.about_eyebrow')->label('Small heading')->required(), TextInput::make('home_content.about_title')->label('Main heading')->required(), TextInput::make('home_content.about_accent')->label('Highlighted heading'),
                        Textarea::make('home_content.about_description')->label('Description')->required()->columnSpanFull(), FileUpload::make('home_content.about_image')->label('Image')->image()->maxSize(3072)->imageResizeMode('contain')->imageResizeTargetWidth('1920')->imageResizeTargetHeight('1920')->disk('public')->directory('home')->visibility('public')->imageEditor()->columnSpanFull(),
                        TextInput::make('home_content.about_image_alt')->label('Image description')->columnSpanFull(), TextInput::make('home_content.about_stat_one_value')->label('First statistic value'), TextInput::make('home_content.about_stat_one_label')->label('First statistic label'), TextInput::make('home_content.about_stat_two_value')->label('Second statistic value'), TextInput::make('home_content.about_stat_two_label')->label('Second statistic label'),
                    ])->columns(2)->collapsible(),
                    Section::make('Process')->schema([
                        TextInput::make('home_content.process_eyebrow')->label('Small heading')->required(), TextInput::make('home_content.process_title')->label('Main heading')->required(), TextInput::make('home_content.process_accent')->label('Highlighted heading'),
                        Repeater::make('home_content.process_items')->label('Process items')->schema([TextInput::make('title')->required(), Textarea::make('description')->required()])->minItems(1)->maxItems(6)->columns(2)->columnSpanFull(),
                    ])->columns(2)->collapsible(),
                    Section::make('Benefits')->schema([
                        TextInput::make('home_content.benefits_eyebrow')->label('Small heading')->required(), TextInput::make('home_content.benefits_title')->label('Main heading')->required(), TextInput::make('home_content.benefits_accent')->label('Highlighted heading'), Textarea::make('home_content.benefits_description')->label('Description')->required()->columnSpanFull(),
                        Repeater::make('home_content.benefits')->label('Benefit cards')->schema([TextInput::make('title')->required(), Textarea::make('description')->required()])->minItems(1)->maxItems(8)->columns(2)->columnSpanFull(),
                    ])->columns(2)->collapsible(),
                    Section::make('Frequently asked questions')->schema([
                        TextInput::make('home_content.faq_eyebrow')->label('Small heading')->required(), TextInput::make('home_content.faq_title')->label('Main heading')->required(), TextInput::make('home_content.faq_accent')->label('Highlighted heading'), Textarea::make('home_content.faq_description')->label('Description')->required()->columnSpanFull(),
                        Repeater::make('home_content.faqs')->label('Questions and answers')->schema([TextInput::make('question')->required(), Textarea::make('answer')->required()])->minItems(1)->maxItems(12)->columnSpanFull(),
                    ])->columns(2)->collapsible(),
                    Section::make('Testimonials introduction')->schema([
                        TextInput::make('home_content.testimonials_eyebrow')->label('Small heading')->required(), TextInput::make('home_content.testimonials_title')->label('Main heading')->required(), TextInput::make('home_content.testimonials_accent')->label('Highlighted heading'),
                    ])->columns(2)->collapsible(),
                ]),
                Tab::make('Home Visibility')->icon(Heroicon::OutlinedEye)->schema([
                    Toggle::make('home_visibility.banner')->label('Show banner'), Toggle::make('home_visibility.services')->label('Show services'),
                    Toggle::make('home_visibility.path')->label('Show simple path'), Toggle::make('home_visibility.primary_cta')->label('Show primary call to action'),
                    Toggle::make('home_visibility.about')->label('Show About preview'), Toggle::make('home_visibility.process')->label('Show process'),
                    Toggle::make('home_visibility.benefits')->label('Show benefits'), Toggle::make('home_visibility.faq')->label('Show FAQs'),
                    Toggle::make('home_visibility.testimonials')->label('Show testimonials'),
                ])->columns(2),
                Tab::make('Contact')->icon(Heroicon::OutlinedPhone)->schema([
                    TextInput::make('address')->required()->maxLength(255),
                    TextInput::make('phone')->required()->tel()->maxLength(40),
                    TextInput::make('email')->required()->email()->maxLength(255),
                ])->columns(2),
                Tab::make('Social Media')->icon(Heroicon::OutlinedShare)->schema([
                    TextInput::make('facebook_url')->label('Facebook URL')->url(),
                    TextInput::make('instagram_url')->label('Instagram URL')->url(),
                    TextInput::make('youtube_url')->label('YouTube URL')->url(),
                    TextInput::make('x_url')->label('X URL')->url(),
                    TextInput::make('tiktok_url')->label('TikTok URL')->url(),
                ])->columns(2),
                Tab::make('SEO Defaults')->icon(Heroicon::OutlinedMagnifyingGlass)->schema([
                    TextInput::make('default_seo_title')->label('Default SEO title')->maxLength(70),
                    Textarea::make('default_meta_description')->label('Default meta description')->rows(4)->maxLength(170)->columnSpanFull(),
                    Textarea::make('default_keywords')->label('Default keywords')->rows(3)->maxLength(500)->columnSpanFull()->helperText('Use a short comma-separated list of relevant search phrases.'),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('site_name')->label('Website')->searchable(),
            TextColumn::make('email'),
            TextColumn::make('phone'),
            TextColumn::make('updated_at')->label('Last updated')->since(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageWebsiteSettings::route('/')];
    }
}
