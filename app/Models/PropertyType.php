<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyType extends Model
{
    use HasFactory;
    use LogsActivity;


    protected $fillable = [
        'name',
        'residential',
        'commercial',
        'land'
    ];

    protected $casts = [
        'residential' => 'boolean',
        'commercial' => 'boolean',
        'land' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function getPropertyClassAttribute(): string
    {
        $classes = [];
        if ($this->residential) $classes[] = 'Residential';
        if ($this->commercial) $classes[] = 'Commercial';
        if ($this->land) $classes[] = 'Land';

        return implode(', ', $classes);
    }

    public static function getTypesByClass(string $class)
    {
        if (in_array($class, ['residential', 'commercial', 'land'])) {
            return self::where($class, true)->get();
        }

        return collect();
    }
}
