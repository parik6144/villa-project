<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\CreateAction;
use App\Services\PlanyoService;
use Filament\Notifications\Notification;
use Carbon\Carbon;


class ManageReservations extends ManageRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Стандартное действие создания через модальное окно
            CreateAction::make()
                ->label('Add reservation')
                ->disabled()
                ->modalHeading('Add new reservation'),


            Action::make('syncReservations')
                ->label('Sync Reservations')

                ->action(function () {
                    dispatch(new \App\Jobs\SyncReservationsJob());

                    Notification::make()
                        ->title('Synchronization started in background')
                        ->success()
                        ->send();
                })
        ];
    }
}
