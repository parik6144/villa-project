<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\PlanyoService;
use Carbon\Carbon;
use App\Models\Reservation;

class SyncReservationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $planyoService = app(PlanyoService::class);

        // Преобразуем выбранные даты в нужный формат
        $startTime = Carbon::now()->subMonth()->format('Y-m-d H:i:s');
        $endTime   = Carbon::now()->format('Y-m-d H:i:s');

        // Вызываем метод для получения резерваций из Planyo
        $response = $planyoService->getListReservations([
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ]);
        // dd($response);
        // Пример обработки ответа: проходим по списку и обновляем или создаем записи
        if ($response['data']['results']) {

            $dataToUpdate = [];
            $dataToUpdatePayments = [];

            foreach ($response['data']['results'] as $reservationData) {

                $result = $planyoService->getReservationData([
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

                $resultPayments = $planyoService->getListReservationPayments([
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
            // dd($dataToUpdate);
        }
    }
}
