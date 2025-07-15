<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyOtherRooms extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'property_id',
        'common_area',
        'dining_room',
        'drying_room',
        'eating_area',
        'fitness_room',
        'games_room',
        'hall',
        'laundry',
        'library',
        'living_room',
        'lounge',
        'office',
        'pantry',
        'rumpus_room',
        'sauna',
        'studio',
        'study',
        'tv_room',
        'work_studio',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
