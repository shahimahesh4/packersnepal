<?php

namespace App\Filament\Resources\Users;

use App\Actions\AssignPageAccess;
use App\Actions\SaveStaff;
use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\Page;
use App\Models\PageGrant;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Users';

    protected static bool $isGloballySearchable = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('password')->password()->revealable()->minLength(12)->required(fn (string $operation) => $operation === 'create')->helperText('Leave blank to keep the existing password.'),
                Toggle::make('is_active')->default(true),
                CheckboxList::make('staff_roles')->options(fn (): array => Role::query()->where('guard_name', 'web')->where('name', '!=', 'Owner')->orderBy('name')->pluck('name', 'name')->all())->required()->columnSpanFull()
                    ->helperText('Website Manager can edit and publish every page. Narrow page grants do not restrict a broad role. Owner accounts cannot be assigned here.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('roles.name')->badge(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('pageGrants.page.title')->label('Assigned pages')->listWithLineBreaks(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->mutateRecordDataUsing(function (array $data, User $record): array {
                    $data['staff_roles'] = $record->getRoleNames()->all();
                    $data['password'] = '';

                    return $data;
                })->using(fn (User $record, array $data) => app(SaveStaff::class)->handle(auth()->user(), $data, $record)),
                Action::make('pageAccess')->label('Assign pages')->authorize('update')->schema([
                    Select::make('page_id')->label('Page')->options(fn () => Page::pluck('title', 'id'))->required()->live()
                        ->afterStateUpdated(function ($state, $set, User $record) {
                            $set('actions', PageGrant::where('user_id', $record->id)->where('page_id', $state)->pluck('action')->all());
                        }),
                    CheckboxList::make('actions')->options(['view' => 'View', 'update' => 'Edit draft', 'publish' => 'Publish'])->default([])
                        ->helperText('The role must also allow the action. Uncheck all actions to revoke this page assignment.'),
                ])->action(fn (User $record, array $data) => app(AssignPageAccess::class)->handle(auth()->user(), $record, $data)),
            ])
            ->toolbarActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
