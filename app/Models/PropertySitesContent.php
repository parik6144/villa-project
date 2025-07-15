<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertySitesContent extends Model
{
    use HasFactory;
    use LogsActivity;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'property_sites_content';

    protected $fillable = [
        'property_id',
        'property_site_id',
        'content',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    /**
     * Relationship with Property.
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * Relationship with PropertySites.
     */
    public function propertySite()
    {
        return $this->belongsTo(PropertySites::class, 'property_site_id');
    }
}
