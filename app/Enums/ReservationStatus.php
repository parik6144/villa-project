<?php

namespace App\Enums;

class ReservationStatus
{
    public const COMPLETED           = 1;
    public const EMAIL_VERIFIED      = 2;
    public const CONFIRMED           = 4;
    public const CANCELLED_BY_ADMIN  = 8;
    public const CANCELLED_BY_USER   = 16;
    public const FRAUDULENT          = 64;
    public const CONFLICT            = 128;
    public const UNAVAILABLE         = 256;
    public const CANCELLED_AUTO      = 512;
    public const QUOTATION           = 2048;
    public const WAITING_LIST        = 8192;
    public const LOCKED              = 16384;
    // Дополнительные статусы для check-in:
    public const CHECKED_IN          = 32;
    public const CHECKED_OUT         = 1024;
    public const NO_SHOW             = 4096;

    /**
     * Декодирует статус бронирования и возвращает список текстовых описаний.
     *
     * @param int $status
     * @return array
     */
    public static function decode(int $status): array
    {
        $mapping = [
            self::COMPLETED           => 'Reservation completed',
            self::EMAIL_VERIFIED      => 'Email verified',
            self::CONFIRMED           => 'Reservation confirmed',
            self::CANCELLED_BY_ADMIN  => 'Cancelled by administrator',
            self::CANCELLED_BY_USER   => 'Cancelled by user',
            self::FRAUDULENT          => 'Fraudulent reservation',
            self::CONFLICT            => 'Conflict',
            self::UNAVAILABLE         => 'Does not affect availability',
            self::CANCELLED_AUTO      => 'Cancelled automatically',
            self::QUOTATION           => 'Quotation',
            self::WAITING_LIST        => 'Waiting list request',
            self::LOCKED              => 'Locked for modifications',
            self::CHECKED_IN          => 'Client checked in',
            self::CHECKED_OUT         => 'Client checked out',
            self::NO_SHOW             => 'No-show',
        ];

        $result = [];
        foreach ($mapping as $bit => $text) {
            if ($status & $bit) {
                $result[] = $text;
            }
        }

        if (empty($result)) {
            $result[] = 'Not completed';
        }

        return $result;
    }
}
