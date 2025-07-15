<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class PropertyExtras extends Model
{
    protected $table = 'property_extras';

    protected $fillable = [
        'property_id',
        'extra_service',
        'fee_basis',
        'amount',
        'earliest_order',
        'latest_order',
        'additional_info',
        'is_custom'
    ];


    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_property_extra', 'property_extra_id', 'property_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
