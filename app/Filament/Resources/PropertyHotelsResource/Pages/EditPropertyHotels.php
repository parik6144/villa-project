<?php

namespace App\Filament\Resources\PropertyHotelsResource\Pages;

use App\Filament\Resources\PropertyHotelsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyHotels extends EditRecord
{
    protected static string $resource = PropertyHotelsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
