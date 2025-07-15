<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyHotels extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'property_id',
        'hotel_id',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
    public function hotel(){
        return $this->belongsTo(Hotels::class, 'hotel_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
