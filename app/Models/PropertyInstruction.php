<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class PropertyInstruction extends Model implements HasMedia
{
    use HasFactory;
    use LogsActivity;
    use InteractsWithMedia;

    protected $fillable = [
        'property_id',
        'check_in',
        'check_out',
        'check_in_contact_person',
        'key_collection_point',
        'telephone_number',
        'check_in_instructions',
        'attached_instructions',
        'closest_airports',
        'directions',
    ];

    protected $casts = [
        'attached_instructions' => 'array',
        'closest_airports' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
