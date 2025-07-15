<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalLicenceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sale',
        'short_rent',
        'monthly_rent',
        'required',
        'hint',
        'file_attachment',
    ];

    public function property()
    {
        return $this->hasMany(Property::class);
    }

    public function propertyLicences()
    {
        return $this->hasMany(PropertyLicence::class, 'licence_type_id');
    }

    /**
     * Get all values ​​of the "name" column from the table add_licence_types.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getLicenceName()
    {
        return self::pluck('name', 'id');
    }
}
