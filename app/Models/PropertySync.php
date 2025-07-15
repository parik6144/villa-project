<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertySync extends Model
{
    use HasFactory;
    use LogsActivity;


    protected $table = 'property_sync';

    protected $fillable = [
        'synchronization_id',
        'property_id',
        'url',
        'site_id'
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    /**
     * Связь с PropertySites.
     */
    public function propertySite()
    {
        return $this->belongsTo(PropertySites::class, 'synchronization_id');
    }

    /**
     * Связь с Property.
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * Get site_id by site name
     *
     * @param string $siteName
     * @return mixed
     */
    public function getSiteIdBySiteName($siteName)
    {
        $propertySite = PropertySites::where('site', $siteName)->first();

        return $propertySite
            ? self::where('synchronization_id', $propertySite->id)
                  ->where('property_id', $this->property_id) 
                  ->value('site_id')
            : null;
    }
}
