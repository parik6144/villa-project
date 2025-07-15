<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Season extends Model
{
    use HasFactory;
    use LogsActivity;


    protected $fillable = ['date_from', 'date_to', 'season_title'];

    public function scopeAllSeasons(Builder $query)
    {
        return $query->get();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function scopeNewSeasons(Builder $query)
    {
        return $query->where('date_to', '>=', now());
    }
}
