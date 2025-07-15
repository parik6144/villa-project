<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    protected $fillable = [
        'planyo_reservation_id',
        'resource_id',
        'client_id',
        'cart_id',
        'start_time',
        'end_time',
        'status',
        'quantity',
        'wants_share',
        'creation_time',
        'unit_assignment',
        'custom_color',
        'site_id',
        'name',
        'currency',
        'night_reservation',
        'user_notes',
        'admin_notes',
        'email',
        'first_name',
        'last_name',
        'address',
        'city',
        'zip',
        'country',
        'mobile_number',
        'phone_number',
        'ppp_rs',
        'user_text',
        'properties',
        'amount_paid',
        'total_price',
        'original_price',
        'discount',
        'log_events',
        'notifications_sent',
        'creation_website',
        'regular_products',
        'custom_products',
    ];

    protected $casts = [
        'start_time'         => 'datetime',
        'end_time'           => 'datetime',
        'creation_time'      => 'datetime',
        'wants_share'        => 'boolean',
        'night_reservation'  => 'boolean',
        'properties'         => 'array',
        'log_events'         => 'array',
        'notifications_sent' => 'array',
        'regular_products'   => 'array',
        'custom_products'    => 'array',
    ];

    // Отношение к клиенту (при наличии записи в таблице clients)
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'id')->whereHas('userMeta', function ($query) {
            $query->whereNotNull('user_planyo_id');
        });
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'planyo_reservation_id', 'planyo_reservation_id');
    }

    public function userMeta()
    {
        return $this->hasOne(UserMeta::class, 'user_planyo_id', 'client_id');
    }
}
