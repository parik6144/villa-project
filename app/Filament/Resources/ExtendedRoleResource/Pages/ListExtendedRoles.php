<?php

namespace App\Filament\Resources\ExtendedRoleResource\Pages;

use App\Filament\Resources\ExtendedRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExtendedRoles extends ListRecords
{
    protected static string $resource = ExtendedRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
