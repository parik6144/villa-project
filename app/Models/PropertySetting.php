<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertySetting extends Model
{
    use HasFactory;
    use LogsActivity;


    protected $table = 'property_settings';

    protected $fillable = [
        'google_map_api_key',
        'async_period_minutes'
    ];

    protected $casts = [];

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
    public static function getValue(string $key, $default = null)
    {
        return self::first()?->{$key} ?? $default;
    }
}
