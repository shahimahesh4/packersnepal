<?php

namespace App\Filament\Resources\Pages;

use App\Actions\PublishPage;
use App\Filament\Resources\Pages\Pages\ManagePages;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
use Illuminate\Database\Eloquent\Builder;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static bool $isGloballySearchable = false;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->visibleTo(auth()->user());
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Website Pages';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Page controls')->tabs([
                    Tab::make('Page Content')->icon(Heroicon::OutlinedDocumentText)->schema([
                        TextInput::make('title')->required()->maxLength(255),
                        TextInput::make('slug')->required()->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->maxLength(120)->unique(ignoreRecord: true)->disabledOn('edit'),
                        Textarea::make('content')->label('Draft content')->rows(16)->required()->maxLength(30000)->columnSpanFull()->helperText('Saving updates the draft. Use Publish to make it public. Plain text is rendered safely.'),
                    ])->columns(2),
                    Tab::make('Search & Sharing')->icon(Heroicon::OutlinedMagnifyingGlass)->schema([
                        TextInput::make('seo_title')->label('SEO title')->maxLength(70)->helperText('Aim for 50–60 characters.'),
                        Textarea::make('meta_description')->label('Meta description')->rows(4)->maxLength(170)->columnSpanFull()->helperText('Aim for 140–160 characters.'),
                        TextInput::make('canonical_url')->label('Canonical URL')->url()->maxLength(255)->helperText('Leave empty to use this page’s normal public URL.'),
                        Toggle::make('robots_index')->label('Allow search indexing')->default(true),
                        Toggle::make('robots_follow')->label('Allow search engines to follow links')->default(true),
                        TextInput::make('social_title')->label('Social sharing title')->maxLength(100)->helperText('Leave empty to use the SEO title.'),
                        Textarea::make('social_description')->label('Social sharing description')->rows(3)->maxLength(200)->columnSpanFull()->helperText('Leave empty to use the meta description.'),
                        FileUpload::make('social_image')->label('Social sharing image')->image()->maxSize(3072)->imageResizeMode('contain')->imageResizeTargetWidth('1920')->imageResizeTargetHeight('1080')->disk('public')->directory('social')->visibility('public')->imageEditor()->columnSpanFull()->helperText('Use a clear 1200 × 630 image when possible.'),
                    ])->columns(2),
                    Tab::make('Page Banner')->icon(Heroicon::OutlinedPhoto)->visible(fn (?Page $record): bool => $record?->slug === 'about')->schema([
                        TextInput::make('banner_eyebrow')->label('Small heading')->maxLength(150)->columnSpanFull(),
                        TextInput::make('banner_title')->label('Main heading')->required()->maxLength(150),
                        TextInput::make('banner_accent')->label('Highlighted heading')->maxLength(150),
                        Textarea::make('banner_description')->label('Banner description')->required()->rows(4)->maxLength(600)->columnSpanFull(),
                        FileUpload::make('banner_image')->label('Background image')->image()->maxSize(3072)->imageResizeMode('contain')->imageResizeTargetWidth('1920')->imageResizeTargetHeight('1920')->disk('public')->directory('page-banners')->visibility('public')->imageEditor()->columnSpanFull(),
                        TextInput::make('banner_image_alt')->label('Image description')->maxLength(180)->columnSpanFull()->helperText('Describe the image for accessibility and search engines.'),
                    ])->columns(2),
                    Tab::make('About Sections')->icon(Heroicon::OutlinedPencilSquare)->visible(fn (?Page $record): bool => $record?->slug === 'about')->schema([
                        Section::make('Company story')->description('Controls the heading, image, statistics, and published page content section.')->schema([
                            TextInput::make('about_content.company_eyebrow')->label('Small heading')->required()->maxLength(120),
                            TextInput::make('about_content.company_title')->label('Main heading')->required()->maxLength(150),
                            TextInput::make('about_content.company_accent')->label('Highlighted heading')->maxLength(150),
                            FileUpload::make('about_content.company_image')->label('Section image')->image()->maxSize(3072)->imageResizeMode('contain')->imageResizeTargetWidth('1920')->imageResizeTargetHeight('1920')->disk('public')->directory('about')->visibility('public')->imageEditor()->columnSpanFull(),
                            TextInput::make('about_content.company_image_alt')->label('Image description')->maxLength(180)->columnSpanFull(),
                            TextInput::make('about_content.stat_one_value')->label('First statistic value')->maxLength(20),
                            TextInput::make('about_content.stat_one_label')->label('First statistic label')->maxLength(80),
                            TextInput::make('about_content.stat_two_value')->label('Second statistic value')->maxLength(20),
                            TextInput::make('about_content.stat_two_label')->label('Second statistic label')->maxLength(80),
                        ])->columns(2)->collapsible(),
                        Section::make('Professional standards')->schema([
                            TextInput::make('about_content.standards_eyebrow')->label('Small heading')->required()->maxLength(120),
                            TextInput::make('about_content.standards_title')->label('Main heading')->required()->maxLength(180),
                            TextInput::make('about_content.standards_accent')->label('Highlighted heading')->maxLength(120),
                            Repeater::make('about_content.standards_cards')->label('Standards cards')->schema([
                                TextInput::make('title')->required()->maxLength(100),
                                Textarea::make('description')->required()->rows(3)->maxLength(350),
                            ])->minItems(1)->maxItems(6)->defaultItems(3)->columns(2)->columnSpanFull(),
                        ])->columns(2)->collapsible(),
                        Section::make('Packing distinction')->schema([
                            TextInput::make('about_content.distinction_eyebrow')->label('Small heading')->required()->maxLength(120),
                            TextInput::make('about_content.distinction_title')->label('Main heading')->required()->maxLength(150),
                            TextInput::make('about_content.distinction_accent')->label('Highlighted heading')->maxLength(150),
                            Textarea::make('about_content.distinction_description')->label('Description')->required()->rows(4)->maxLength(800)->columnSpanFull(),
                            FileUpload::make('about_content.distinction_image')->label('Section image')->image()->maxSize(3072)->imageResizeMode('contain')->imageResizeTargetWidth('1920')->imageResizeTargetHeight('1920')->disk('public')->directory('about')->visibility('public')->imageEditor()->columnSpanFull(),
                            TextInput::make('about_content.distinction_image_alt')->label('Image description')->maxLength(180)->columnSpanFull(),
                            TextInput::make('about_content.distinction_link_label')->label('Link text')->maxLength(80),
                            TextInput::make('about_content.distinction_link_url')->label('Link URL')->maxLength(255),
                        ])->columns(2)->collapsible(),
                        Section::make('Final call to action')->schema([
                            TextInput::make('about_content.cta_eyebrow')->label('Small heading')->required()->maxLength(120),
                            TextInput::make('about_content.cta_title')->label('Main heading')->required()->maxLength(180),
                            Textarea::make('about_content.cta_description')->label('Description')->required()->rows(3)->maxLength(500)->columnSpanFull(),
                            TextInput::make('about_content.cta_button_label')->label('Button text')->required()->maxLength(80),
                            TextInput::make('about_content.cta_button_url')->label('Button URL')->required()->maxLength(255),
                        ])->columns(2)->collapsible(),
                    ]),
                    Tab::make('Section Visibility')->icon(Heroicon::OutlinedEye)->visible(fn (?Page $record): bool => $record?->slug === 'about')->schema([
                        Toggle::make('show_banner')->label('Show header banner')->helperText('The main image banner at the top of the page.'),
                        Toggle::make('show_company_story')->label('Show company story')->helperText('The About our company content and supporting image.'),
                        Toggle::make('show_standards')->label('Show professional standards')->helperText('The three cards explaining what guides the team.'),
                        Toggle::make('show_distinction')->label('Show service distinction')->helperText('The section explaining that transportation is arranged separately.'),
                        Toggle::make('show_cta')->label('Show final call to action')->helperText('The final request-a-quote panel.'),
                    ])->columns(2),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('slug'),
                TextColumn::make('published_at')->dateTime()->placeholder('Unpublished'),
                TextColumn::make('updated_at')->since(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label('Preview draft'),
                EditAction::make(),
                Action::make('publish')->label('Publish draft')->authorize('publish')->requiresConfirmation()
                    ->action(fn (Page $record) => app(PublishPage::class)->handle(auth()->user(), $record)),
            ])
            ->toolbarActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePages::route('/'),
        ];
    }
}
