<?php

namespace App\Filament\Resources\AttributeGroupResource\Pages;

use App\Filament\Resources\AttributeGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttributeGroup extends EditRecord
{
    protected static string $resource = AttributeGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
