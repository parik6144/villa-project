<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PlanyoService;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Support\Facades\Log;
use App\Models\PropertySetting;

class SyncPlanyoUsers extends Command
{
    protected $signature = 'sync:planyo-users';
    protected $description = 'Synchronize users from Planyo based on modified_since parameter';

    protected PlanyoService $planyoService;

    public function __construct(PlanyoService $planyoService)
    {
        parent::__construct();
        $this->planyoService = $planyoService;
    }

    public function handle()
    {
        set_time_limit(120);
        $this->info('Starting Planyo user sync...');

        $minutes = PropertySetting::getValue('async_period_minutes', 5);

        // Получаем пользователей из Planyo
        $response = $this->planyoService->getListUsers([
            'modified_since' => now()->subMinutes($minutes)->toIso8601String(), // Получаем пользователей, измененных за последние 5 минут
        ]);

        if (isset($response['data']['users'])) {
            foreach ($response['data']['users'] as $userData) {
                if (empty($userData['email'])) {
                    continue;
                }

                $user = User::updateOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name'      => $userData['first_name'],
                        'last_name' => $userData['last_name'],
                        'email'     => $userData['email'],
                        'password'  => bcrypt('default_password'),
                    ]
                );

                if (!$user->hasRole('user')) {
                    $user->assignRole('user');
                }

                UserMeta::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'country_code'      => $userData['country'],
                        'street_address'    => $userData['address'],
                        'city'              => $userData['city'],
                        'postal_code'       => $userData['zip'],
                        'state_province'    => $userData['state'],
                        'number'            => $userData['phone_country_code'] . $userData['phone_number'],
                        'mobile_number'     => $userData['mobile_country_code'] . $userData['mobile_number'],
                        'user_planyo_id'    => $userData['id'],
                        'language'          => $userData['language'] ?? null,
                        'registration_time' => $userData['registration_time'] ?? now(),
                        'reservation_count' => $userData['reservation_count'] ?? 0,
                        'last_reservation'  => $userData['last_reservation'] ?? null,
                    ]
                );
            }

            $this->info('Planyo user sync completed.');
            Log::info('Planyo users successfully synchronized.');
        } else {
            $this->warn('No users updated.');
            Log::warning('Planyo sync: No users found.');
        }
    }
}
