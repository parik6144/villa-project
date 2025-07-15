<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertySites extends Model
{
    use HasFactory;
    use LogsActivity;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'property_sites';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site',
        'url',
        'account_id',
        'api_key',
        'api_url',
        'default_property_id'
    ];

    public function content()
    {
        return $this->hasMany(PropertySitesContent::class, 'property_site_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    /**
     * Get all values ​​of the "site" column from the table property_sites.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getAllSites()
    {
        return self::pluck('site');
    }
}
