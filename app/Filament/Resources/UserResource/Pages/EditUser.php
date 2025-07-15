<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Http\Middleware\VerifyUserEditPermission;
use App\Models\UserMeta;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;


    public static function getRouteMiddleware(\Filament\Panel $panel): array
    {
        return array_merge(parent::getRouteMiddleware($panel), [
            'auth',
            VerifyUserEditPermission::class,
        ]);
    }
    protected function getHeaderActions(): array
    {
        if (Auth::user()->hasRole('admin')) {
            return [
                Actions\DeleteAction::make(),

                Actions\Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->action(function () {
                        $userMeta = UserMeta::where('user_id', $this->record->id)->first();
                        if ($userMeta) {
                            $userMeta->approval_status = 'approved';
                            $userMeta->approved_at = now();
                            $userMeta->save();
                            $this->record->save();
                        }

                        Notification::make()
                            ->title('User approved!')
                            ->success()
                            ->send();
                    })
                    ->visible(function () {
                        $userMeta = UserMeta::where('user_id', $this->record->id)->first();
                        $userRoles = $this->record->roles->pluck('name')->toArray();

                        $relevantRoles = ['property_owner', 'agent'];

                        return (
                            in_array('property_owner', $userRoles) || in_array('agent', $userRoles)
                        ) && (!$userMeta || ($userMeta->approval_status !== 'approved'));
                    }),
                Actions\Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->action(function () {
                        $userMeta = UserMeta::where('user_id', $this->record->id)->first();
                        if ($userMeta) {
                            $userMeta->approval_status = 'declined';
                            $userMeta->save();
                            $this->record->save();
                        }

                        Notification::make()
                            ->title('User declined!')
                            ->warning()
                            ->send();
                    })
                    ->visible(function () {
                        $userMeta = UserMeta::where('user_id', $this->record->id)->first();
                        $userRoles = $this->record->roles->pluck('name')->toArray();

                        $relevantRoles = ['property_owner', 'agent'];

                        return (
                            in_array('property_owner', $userRoles) || in_array('agent', $userRoles)
                        ) && (!$userMeta || ($userMeta->approval_status !== 'declined' && $userMeta->approval_status !== 'approved'));
                    }),

            ];
        } else {
            return [];
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Если переключатель включён, устанавливаем дату, иначе сбрасываем в null:
        $data['email_verified_at'] = !empty($data['temp_is_email_verified']) ? now() : null;

        // Удаляем виртуальное поле, чтобы оно не пыталось сохраниться в базу,
        // поскольку его там не существует.
        unset($data['temp_is_email_verified']);

        return $data;
    }
}
