<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyPrices extends Model
{
    use HasFactory;
    use LogsActivity;


    protected $fillable = [
        'price_id',
        'value',
        'property_id',
    ];

    public function priceType()
    {
        return $this->belongsTo(PriceTypes::class, 'price_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

}
