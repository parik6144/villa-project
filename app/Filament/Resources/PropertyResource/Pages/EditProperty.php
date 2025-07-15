<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use App\Http\Middleware\VerifyPropertyEditPermission;
use App\Models\PropertyBathroom;
use App\Models\PropertyBedroom;
use App\Models\PropertyKitchen;
use App\Models\PropertyOtherRooms;
use Cheesegrits\FilamentGoogleMaps\Concerns\InteractsWithMaps;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Models\PropertyImage;
use App\Models\PropertyAttribute;
use App\Models\Attribute;
use App\Models\PropertyAvailability;
use App\Models\PropertySeason;
use App\Models\PropertySitesContent;
use App\Models\Season;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\Property;
use App\Http\Controllers\PropertyHtmlController;
use Illuminate\Database\Eloquent\Model;

class EditProperty extends EditRecord
{
    use InteractsWithMaps;

    protected static string $resource = PropertyResource::class;


    public static function getRouteMiddleware(\Filament\Panel $panel): array
    {
        return array_merge(parent::getRouteMiddleware($panel), [
            'auth',
            VerifyPropertyEditPermission::class,
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
                    ->visible(fn() => $this->record->active == false),

                Actions\Action::make('deactivate')
                    ->label('Deactivate')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(function () {
                        $this->record->active = false;
                        $this->record->save();

                        Notification::make()
                            ->title('Property deactivated!')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn() => $this->record->active == true),

                Actions\Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->disabled(function () {
                        try {
                            $this->validate();
                
                            return false;
                        } catch (\Throwable) {
                            return true;
                        }
                    })
                    ->action(function ($livewire) {
                        // $this->record->save();
                        // $this->record->fill($livewire->data);

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
		            ->extraAttributes(['class' => 'property-html-controller', 'data-propertyid' => $this->record->id])
                    ->visible(fn() => $this->record->approval_status !== 'approved'),

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
                    ->visible(fn() => $this->record->approval_status !== 'declined'),
            ];
        }
        else{
            return [];
        }
    }

    protected function getFormActions(): array
    {
        return [
            // $this->getSaveFormAction(),
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
            //                 $this->save();
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

    // public function cancelAction(): Action
    // {
    //     return Action::make('cancel')
    //         ->requiresConfirmation()
    //         ->color('gray')
    //         ->action(function () {
    //             return redirect()->route('filament.backend.pages.dashboard');
    //         });
    // }

    protected function afterSave(): void
    {
        $this->record->savePropertyData($this->data);
    }

    public function removeOption($index)
    {
        if (isset($this->data['instructions']['closest_airports'][$index])) {
            unset($this->data['instructions']['closest_airports'][$index]);

            $this->data['instructions']['closest_airports'] = array_values($this->data['instructions']['closest_airports']);

        }
    }
}
