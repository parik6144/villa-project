<?php

namespace App\Filament\Resources\ServiceCategoriesResource\Pages;

use App\Filament\Resources\ServiceCategoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceCategories extends ListRecords
{
    protected static string $resource = ServiceCategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
