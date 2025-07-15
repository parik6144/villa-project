<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRule extends Model
{
    use HasFactory;

    protected $table = 'property_booking_rules';

    const FREE_CANCELLATION_PERIODS = [
        '18:00 on the day of arrival' => '18:00 on the day of arrival',
        '14:00 on the day of arrival' => '14:00 on the day of arrival',
        '1 day before arrival' => '1 day before arrival',
        '2 days before arrival' => '2 days before arrival',
        '3 days before arrival' => '3 days before arrival',
        '5 days before arrival' => '5 days before arrival',
        '7 days before arrival' => '7 days before arrival',
        '14 days before arrival' => '14 days before arrival',
        '21 days before arrival' => '21 days before arrival',
        '28 days before arrival' => '28 days before arrival',
        '30 days before arrival' => '30 days before arrival',
        '42 days before arrival' => '42 days before arrival',
        '60 days before arrival' => '60 days before arrival',
    ];

    const CANCELLATION_FEES = [
        'the cost of the first night' => 'the cost of the first night',
        '50% of the total price' => '50% of the total price',
        '100% of the total price' => '100% of the total price',
    ];

    const NO_SHOW_FEES = [
        'same as cancellation fee' => 'same as cancellation fee',
        '100% of the total price' => '100% of the total price',
    ];

    const RATE_ADJUSTMENT_TYPES = [
        'increase by' => 'increase by',
        'decrease by' => 'decrease by',
    ];

    protected $fillable = [
        'property_id',
        'is_free_cancellation',
        'free_cancellation_period',
        'cancellation_fee',
        'no_show_fee',
        'rate_adjustment_type',
        'rate_adjustment_value',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public static function getFreeCancellationPeriods()
    {
        return self::FREE_CANCELLATION_PERIODS;
    }

    public static function getCancellationFees()
    {
        return self::CANCELLATION_FEES;
    }

    public static function getNoShowFees()
    {
        return self::NO_SHOW_FEES;
    }

    public static function getRateAdjustmentTypes()
    {
        return self::RATE_ADJUSTMENT_TYPES;
    }
}
