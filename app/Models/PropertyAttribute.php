<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyAttribute extends Model
{
    use LogsActivity;
    protected $fillable = ['property_id', 'attribute_id', 'value'];

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

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
