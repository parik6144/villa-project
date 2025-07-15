<?php

namespace App\Filament\Resources\PropertyPricesResource\Pages;

use App\Filament\Resources\PropertyPricesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyPrices extends EditRecord
{
    protected static string $resource = PropertyPricesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
