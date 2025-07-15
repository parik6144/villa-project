<?php

namespace App\Filament\Resources\PriceTypesResource\Pages;

use App\Filament\Resources\PriceTypesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPriceTypes extends ListRecords
{
    protected static string $resource = PriceTypesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
