<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyCalendarSynchronization extends Model
{
    use HasFactory;

    protected $table = 'property_calendar_synchronizations';

    protected $fillable = [
        'property_id', 
        'calendar_source',
        'calendar_url',
        'calendar_name',
    ];


    public function property()
    {
        return $this->belongsTo(Property::class);
    }

}
