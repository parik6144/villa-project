<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Http\Controllers\PropertyHtmlController;
use App\Models\PropertyImage;
use App\Models\PropertyAttribute;
use App\Models\Attribute;
use App\Models\PropertyAvailability;
use App\Models\PropertySeason;
use App\Models\Season;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\VerifyPropertyCreatePermission;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    public static function getRouteMiddleware(\Filament\Panel $panel): array
    {
        return array_merge(parent::getRouteMiddleware($panel), [
            'auth',
            VerifyPropertyCreatePermission::class,
        ]);
    }

    protected function getHeaderActions(): array
    {
        if (Auth::user()->hasRole('admin')) {
            return [
                // Actions\DeleteAction::make(),

                Actions\Action::make('activate')
                    ->label('Activate')
                    ->requiresConfirmation()
                    ->color('success')
                    ->action(function () {
                        $this->record->active = true;
                        $this->record->save();

                        Notification::make()
                            ->title('Property activated!')
                            ->success()
                            ->send();
                    })
                    ->visible(fn() => $this->record && $this->record->active != true ),

                Actions\Action::make('deactivate')
                    ->label('Deactivate')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(function ($livewire) {
                        $this->record->active = false;
                        $this->record->save();

                        Notification::make()
                            ->title('Property deactivated!')
                            ->warning()
                            ->send();
                    })
                    ->disabled(fn()=> !$this->record)
                    ->visible(fn() => !$this->record || ($this->record && $this->record->active == true)),

                Actions\Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->tooltip('Please ensure the form is valid and saved before approving')
                    ->disabled(function () {
                        if(!$this->record){
                            return true;
                        }
                        
                        try {
                            $this->validate();
                
                            return false;
                        } catch (\Throwable) {
                            return true;
                        }
                    })
                    ->action(function ($livewire) {
                        $this->record->saveQuietly();
                        $this->record->savePropertyData($livewire->data);
                        $livewire->form->model($this->record)->saveRelationships();
                        
                        $livewire->record->approval_status = 'approved';
                        $livewire->record->save();
                        $livewire->record->refresh();

                        app(PropertyHtmlController::class)->generateHtml($this->record->id);
                        app(PropertyHtmlController::class)->generateHtmlRealEstate($this->record->id);

                        Notification::make()
                            ->title('Property approved!')
                            ->success()
                            ->send();
                    })
		            ->extraAttributes(function () {
                        $attributes = ['class' => 'property-html-controller'];
                        if ($this->record && $this->record->id) {
                            $attributes['data-propertyid'] = $this->record->id;
                        }
                        return $attributes;
                    })
                    ->visible(fn() => !$this->record || ($this->record && $this->record->approval_status !== 'approved')),

                Actions\Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->action(function () {
                        $this->record->approval_status = 'declined';
                        $this->record->save();

                        Notification::make()
                            ->title('Property rejected!')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn() => $this->record && $this->record->approval_status !== 'declined'),
            ];
        }
        else{
            return [];
        }
    }

    protected function getFormActions(): array
    {
        return [
            // $this->getCreateFormAction(),
            // Action::make('cancel')
            //     ->label('Cancel')
            //     ->color('gray')
            //     ->modalHeading('Are you sure you want to leave?') 
            //     ->modalDescription('There is unsaved data')
            //     ->modalSubmitAction(false)
            //     ->modalCancelAction(function (\Filament\Actions\StaticAction $action) {
            //         return $action->label('Close'); 
            //     })
            //     ->extraModalFooterActions([
            //         Action::make('save_and_leave') 
            //             ->label('Save and Leave')
            //             ->color('success')
            //             ->action(function () {
            //                 $this->create(); 
            //                 return redirect()->route('filament.backend.resources.properties.index');
            //             }),
            //         Action::make('exit') 
            //             ->label('Exit Without Save')
            //             ->color('danger')
            //             ->action(function () {
            //                 return redirect()->route('filament.backend.resources.properties.index');
            //             }),
            //     ]),
                
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['latitude'] = $data['coordinates']['lat'] ?? null;
        $data['longitude'] = $data['coordinates']['lng'] ?? null;

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->savePropertyData($this->data);
    }
}
