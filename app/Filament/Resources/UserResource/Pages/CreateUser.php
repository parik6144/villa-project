<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Http\Middleware\VerifyIsAdmin;
use App\Models\UserMeta;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Carbon\Carbon;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public static function getRouteMiddleware(\Filament\Panel $panel): array
    {
        return array_merge(parent::getRouteMiddleware($panel), [
            'auth',
            VerifyIsAdmin::class,
        ]);
    }
    protected function afterCreate(): void
    {
        $userRoles = $this->record->roles->pluck('name')->toArray();

        if (in_array('property_owner', $userRoles) || in_array('agent', $userRoles)) {
            UserMeta::where('user_id', $this->record->id)
                ->update([
                    'approval_status' => 'approved',
                    'approved_at' => now(),
                ]);
        }

        if (in_array('admin', $userRoles)) {
            $this->record->forceFill([
                'email_verified_at' => Carbon::now(),
            ])->save();
        }
    }
}
