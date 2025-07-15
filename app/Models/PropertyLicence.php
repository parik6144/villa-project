<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PropertyLicence extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'property_licence';

    protected $fillable = [
        'property_id',
        'additional_licence_type_id',
        'licence_number',
        'licence_file_name'
    ];

    protected $casts = [
        'licence_file_name' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function additionalLicenceType()
    {
        return $this->belongsTo(AdditionalLicenceType::class, 'additional_licence_type_id');
    }
}
