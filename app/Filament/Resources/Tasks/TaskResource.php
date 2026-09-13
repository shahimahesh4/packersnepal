<?php

namespace App\Filament\Resources\Tasks;

use App\Filament\Resources\Tasks\Pages\ManageTasks;
use App\Models\Task;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationLabel = 'Assigned Tasks';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static bool $isGloballySearchable = false;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->visibleTo(auth()->user());
    }

    public static function form(Schema $schema): Schema
    {
        $manager = fn (): bool => auth()->user()?->permits('tasks.manage') ?? false;

        return $schema->components([
            TextInput::make('title')->required()->maxLength(180)->disabled(fn (): bool => ! $manager()),
            Select::make('assigned_to')->label('Assign to user')->relationship('assignedTo', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('is_active', true))->searchable()->preload()->required()->disabled(fn (): bool => ! $manager()),
            Select::make('page_id')->label('Related website page')->relationship('page', 'title')->searchable()->preload()->placeholder('General task')->disabled(fn (): bool => ! $manager()),
            DatePicker::make('due_date')->native(false)->disabled(fn (): bool => ! $manager()),
            Select::make('priority')->options(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'])->default('normal')->required()->disabled(fn (): bool => ! $manager()),
            Select::make('status')->options(['pending' => 'Pending', 'in_progress' => 'In progress', 'on_hold' => 'On hold', 'completed' => 'Completed'])->default('pending')->required(),
            Textarea::make('description')->rows(6)->maxLength(5000)->columnSpanFull()->disabled(fn (): bool => ! $manager()),
            Hidden::make('created_by'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('assignedTo.name')->label('Assigned to')->searchable(),
                TextColumn::make('page.title')->label('Page')->placeholder('General'),
                TextColumn::make('priority')->badge()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('due_date')->date()->sortable()->placeholder('No deadline'),
                TextColumn::make('creator.name')->label('Assigned by'),
            ])
            ->filters([
                SelectFilter::make('status')->options(['pending' => 'Pending', 'in_progress' => 'In progress', 'on_hold' => 'On hold', 'completed' => 'Completed']),
                SelectFilter::make('priority')->options(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent']),
                SelectFilter::make('assigned_to')->label('Assigned user')->relationship('assignedTo', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->visible(fn (): bool => auth()->user()?->permits('tasks.manage') ?? false),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageTasks::route('/')];
    }
}
