<?php

namespace App\Filament\Resources\PropertyTypeResource\Pages;

use App\Filament\Resources\PropertyTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Property;
use App\Models\PropertyType;
use Filament\Notifications\Notification;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Actions\Action;

class EditPropertyType extends EditRecord
{
    protected static string $resource = PropertyTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->before(function (PropertyType $record, Actions\DeleteAction $action) {
                    
                    if ($record && Property::where('property_type_id', $record->id)->exists()) {
                        Notification::make()
                            ->title('Error')
                            ->danger()
                            ->body('Cannot delete. This Property Type is linked to a Property.')
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }
}
