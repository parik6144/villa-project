<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'payment_id',
        'planyo_reservation_id',
        'amount',
        'currency',
        'payment_status',
        'payment_time',
        'payment_mode',
        'comment',
        'transaction_id',
        'extra_info',
        'uid'
    ];

    protected $casts = [
        'payment_time' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'planyo_reservation_id', 'planyo_reservation_id');
    }
}
