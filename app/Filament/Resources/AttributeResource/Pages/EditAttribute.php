<?php

namespace App\Filament\Resources\AttributeResource\Pages;

use App\Filament\Resources\AttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Models\PropertyAttribute;
use Filament\Support\Exceptions\Halt;

class EditAttribute extends EditRecord
{
    protected static string $resource = AttributeResource::class;

    protected function afterSave(): void
    {
        $this->redirect(AttributeResource::getUrl('index'));
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    $exists = PropertyAttribute::where('attribute_id', $record->id)->exists();
    
                    if ($exists) {
                        Notification::make()
                            ->title('Deletion Blocked')
                            ->body('This attribute is used in properties and cannot be deleted.')
                            ->danger()
                            ->send();
    
                        throw new Halt();
                    }
                }),
        ];
    }
}
