<?php

namespace App\Filament\Resources\PropertyHotelsResource\Pages;

use App\Filament\Resources\PropertyHotelsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyHotels extends ListRecords
{
    protected static string $resource = PropertyHotelsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
