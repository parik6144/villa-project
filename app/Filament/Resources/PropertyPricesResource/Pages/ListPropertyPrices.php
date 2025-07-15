<?php

namespace App\Filament\Resources\PropertyPricesResource\Pages;

use App\Filament\Resources\PropertyPricesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyPrices extends ListRecords
{
    protected static string $resource = PropertyPricesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
