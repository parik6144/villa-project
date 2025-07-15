<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\User;
use App\Models\UserMeta;
use App\Filament\Resources\UserResource;
use App\Http\Middleware\VerifyIsAdmin;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Services\PlanyoService;
use Filament\Notifications\Notification;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public static function getRouteMiddleware(\Filament\Panel $panel): array
    {
        return array_merge(parent::getRouteMiddleware($panel), [
            'auth',
            VerifyIsAdmin::class,
        ]);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('syncUsers')
                ->label('Sync users from Planyo')
                ->action(function () {
                    dispatch(new \App\Jobs\SyncUsersJob());

                    Notification::make()
                        ->title('Synchronization started in background')
                        ->success()
                        ->send();
                })
        ];
    }
}
