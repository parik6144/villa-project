<?php

namespace App\Filament\Resources\ServiceCategoriesResource\Pages;

use App\Filament\Resources\ServiceCategoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceCategories extends EditRecord
{
    protected static string $resource = ServiceCategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
