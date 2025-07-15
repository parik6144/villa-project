<?php

namespace App\Filament\Resources\ExtendedRoleResource\Pages;

use App\Filament\Resources\ExtendedRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExtendedRole extends EditRecord
{
    protected static string $resource = ExtendedRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
