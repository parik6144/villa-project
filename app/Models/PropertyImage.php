<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyImage extends Model
{
    protected $fillable = [
        'property_id',
        'image_path',
        'is_primary'
    ];
    use LogsActivity;

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
