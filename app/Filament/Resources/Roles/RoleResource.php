<?php

namespace App\Filament\Resources\Roles;

use App\Actions\SaveStaff;
use App\Filament\Resources\Roles\Pages\ManageRoles;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationLabel = 'User Roles';

    protected static ?string $modelLabel = 'user role';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static bool $isGloballySearchable = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(100)->unique(ignoreRecord: true)
                ->disabled(fn (?Role $record): bool => $record?->name === 'Owner')
                ->helperText('Use a clear role name describing the staff responsibility.'),
            Hidden::make('guard_name')->default('web'),
            CheckboxList::make('permissions')->relationship('permissions', 'name')
                ->options(fn (): array => Permission::query()->where('guard_name', 'web')->orderBy('name')->pluck('name', 'id')->all())
                ->columns(2)->searchable()->bulkToggleable()->columnSpanFull()
                ->helperText('The admin.access permission is required before users with this role can sign in.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->badge(),
                TextColumn::make('permissions.name')->label('Permissions')->badge()->limitList(4)->expandableLimitedList(),
                TextColumn::make('users_count')->counts('users')->label('Users')->sortable(),
                TextColumn::make('updated_at')->since(),
            ])
            ->recordActions([
                EditAction::make()->visible(fn (Role $record): bool => ! in_array($record->name, ['Owner', 'Super Admin'], true)),
                DeleteAction::make()->visible(fn (Role $record): bool => static::canDelete($record)),
            ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->permits('roles.manage') ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny() && $record instanceof Role && ! in_array($record->name, ['Owner', 'Super Admin'], true);
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny() && $record instanceof Role && ! in_array($record->name, static::protectedRoles(), true) && ! $record->users()->exists();
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    private static function protectedRoles(): array
    {
        return ['Owner', 'Super Admin', 'Admin', ...SaveStaff::ROLES];
    }

    public static function getPages(): array
    {
        return ['index' => ManageRoles::route('/')];
    }
}
