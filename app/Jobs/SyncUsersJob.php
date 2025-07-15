<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\PlanyoService;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserMeta;

class SyncUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $planyoService = app(PlanyoService::class);

        // Получаем список пользователей из Planyo
        $response = $planyoService->getListUsers();
        // dd($response);
        if (isset($response['data']['users'])) {
            foreach ($response['data']['users'] as $userData) {
                // Если email отсутствует или пустой, пропускаем этого пользователя
                if (empty($userData['email'])) {
                    continue;
                }

                $user = User::updateOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name'      => $userData['first_name'],
                        'last_name' => $userData['last_name'],
                        'email'     => $userData['email'],
                        'password'  => bcrypt('default_password'), // Поменяй, если нужно
                    ]
                );

                // Присваиваем роль "user", если её нет
                if (!$user->hasRole('user')) {
                    $user->assignRole('user');
                }

                // Обновляем или создаём данные в user_meta
                UserMeta::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'country_code'          => $userData['country'],
                        'street_address'        => $userData['address'],
                        'city'                  => $userData['city'],
                        'postal_code'           => $userData['zip'],
                        'state_province'        => $userData['state'],
                        'number'                => $userData['phone_country_code'] . $userData['phone_number'],
                        'mobile_number'         => $userData['mobile_country_code'] . $userData['mobile_number'],
                        'user_planyo_id'        => $userData['id'],
                        'language'              => $userData['language'],
                        'registration_time'     => $userData['registration_time'] ?? now(),
                        'reservation_count'     => $userData['reservation_count'] ?? 0,
                        'last_reservation'      => $userData['last_reservation'] ?? null,
                    ]
                );
            }
        }
    }
}
