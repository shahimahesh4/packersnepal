<?php

namespace App\Filament\Resources\Users\Pages;

use App\Actions\SaveStaff;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->using(fn (array $data) => app(SaveStaff::class)->handle(auth()->user(), $data)),
        ];
    }
}
