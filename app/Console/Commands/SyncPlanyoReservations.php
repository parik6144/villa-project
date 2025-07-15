<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PlanyoService;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\PropertySetting;

class SyncPlanyoReservations extends Command
{
    protected $signature = 'sync:planyo-reservations';
    protected $description = 'Synchronize reservations from Planyo based on modified_since parameter';

    protected PlanyoService $planyoService;

    public function __construct(PlanyoService $planyoService)
    {
        parent::__construct();
        $this->planyoService = $planyoService;
    }

    public function handle()
    {
        set_time_limit(120);
        $this->info('Starting Planyo reservation sync...');

        $minutes = PropertySetting::getValue('async_period_minutes', 5);

        // Получаем измененные резервации за последние 5 минут
        $startTime = Carbon::now()->subMinutes($minutes)->format('Y-m-d H:i:s');
        $endTime   = Carbon::now()->format('Y-m-d H:i:s');

        $response = $this->planyoService->getListReservations([
            'start_time'        => $startTime,
            'end_time'          => $endTime,
            'modified_since'    => now()->subMinutes($minutes)->toIso8601String(), // Только обновленные за последние 5 минут
        ]);

        if (isset($response['data']['results'])) {

            $dataToUpdate = [];
            $dataToUpdatePayments = [];

            foreach ($response['data']['results'] as $reservationData) {

                $result = $this->planyoService->getReservationData([
                    'reservation_id'        => $reservationData['reservation_id'],
                ]);

                // dd($result['data']['resource_id']);

                if ($result['data']) {

                    // dd($result['data']);

                    $dataToUpdate = [
                        // Уникальный идентификатор резервации в системе Planyo
                        'planyo_reservation_id' => $reservationData['reservation_id'] ?? null,
                        // Ключевые поля, которые обязательны
                        'resource_id'           => $result['data']['resource_id'],
                        'client_id'             => $result['data']['user_id'], // или, при наличии, получить локальный id
                        'status'                => $result['data']['status'],
                        'cart_id'               => $result['data']['cart_id'] ?? null,
                        'start_time'            => $result['data']['start_time'],
                        'end_time'              => $result['data']['end_time'],
                        'creation_time'         => $result['data']['creation_time'],
                        'unit_assignment'       => $result['data']['unit_assignment'] ?? null,
                        'custom_color'          => $result['data']['custom_color'] ?? null,
                        'site_id'               => $result['data']['site_id'],
                        'name'                  => $result['data']['name'],
                        'currency'              => $result['data']['currency'],
                        'night_reservation'     => $result['data']['night_reservation'],
                        'user_notes'            => $result['data']['user_notes'] ?? null,
                        'admin_notes'           => $result['data']['admin_notes'] ?? null,
                        'email'                 => $result['data']['email'],
                        'first_name'            => $result['data']['first_name'],
                        'last_name'             => $result['data']['last_name'] ?? null,
                        'address'               => $result['data']['address'] ?? null,
                        'city'                  => $result['data']['city'] ?? null,
                        'zip'                   => $result['data']['zip'] ?? null,
                        'country'               => $result['data']['country'] ?? null,
                        'mobile_number'         => $result['data']['mobile_number'] ?? null,
                        'phone_number'          => $result['data']['phone_number'] ?? null,
                        'ppp_rs'                => $result['data']['ppp_rs'] ?? null,
                        'user_text'             => $result['data']['user_text'] ?? null,
                        'properties'            => $result['data']['properties'] ?? null,
                        'amount_paid'           => $result['data']['amount_paid'] ?? 0,
                        'total_price'           => $result['data']['total_price'],
                        'original_price'        => $result['data']['original_price'],
                        'discount'              => $result['data']['discount'] ?? 0,
                        'log_events'            => $result['data']['log_events'] ?? null,
                        'notifications_sent'    => $result['data']['notifications_sent'] ?? null,
                        'creation_website'      => $result['data']['creation_website'] ?? null,
                        'regular_products'      => $result['data']['regular_products'] ?? null,
                        'custom_products'       => $result['data']['custom_products'] ?? null,
                    ];



                    if (isset($dataToUpdate['planyo_reservation_id'])) {
                        \App\Models\Reservation::updateOrCreate(
                            ['planyo_reservation_id' => $dataToUpdate['planyo_reservation_id']],
                            $dataToUpdate
                        );
                    } else {
                        dd($dataToUpdate);
                    }
                }

                $resultPayments = $this->planyoService->getListReservationPayments([
                    'reservation_id'        => $reservationData['reservation_id'],
                ]);

                if ($resultPayments['data']['results']) {

                    foreach ($resultPayments['data']['results'] as $reservationDataPayment) {
                        // dd($reservationData);
                        $dataToUpdatePayments = [
                            'payment_id'            => $reservationDataPayment['payment_id'] ?? null,
                            'planyo_reservation_id' => $dataToUpdate['planyo_reservation_id'] ?? null,
                            'amount'                => $reservationDataPayment['amount'] ?? null,
                            'currency'              => $reservationDataPayment['currency'] ?? null,
                            'payment_status'        => $reservationDataPayment['payment_status'] ?? null,
                            'payment_time'          => $reservationDataPayment['payment_time'] ?? null,
                            'payment_mode'          => $reservationDataPayment['payment_mode'] ?? null,
                            'comment'               => $reservationDataPayment['comment'] ?? null,
                            'transaction_id'        => $reservationDataPayment['transaction_id'] ?? null,
                            'extra_info'            => $reservationDataPayment['extra_info'] ?? null,
                            'uid'                   => $reservationDataPayment['uid'] ?? null,
                        ];
                        \App\Models\Payment::updateOrCreate(
                            ['payment_id' => $dataToUpdatePayments['payment_id']],
                            $dataToUpdatePayments
                        );
                    }
                }
            }

            $this->info('Planyo reservation sync completed.');
            Log::info('Planyo reservations successfully synchronized.');
        } else {
            $this->warn('No reservations updated.');
            Log::warning('Planyo sync: No reservations found.');
        }
    }
}
