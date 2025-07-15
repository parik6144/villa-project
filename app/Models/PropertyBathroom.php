<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyBathroom extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'private',
        'bathroom_type',
        'toilet',
        'shower',
        'bath',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }


}
