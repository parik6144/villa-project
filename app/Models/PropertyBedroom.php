<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyBedroom extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'property_id',
        'name',
        'type',
        'bunk_bed',
        'double_bed',
        'king_sized_bed',
        'queen_sized_bed',
        'single_bed_adult',
        'single_bed_child',
        'sofa_bed_double',
        'sofa_bed_single',
    ];

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
