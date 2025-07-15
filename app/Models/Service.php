<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Service extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'service_category_id',
        'title',
        'description',
        'location',
        'price',
        'availability',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'primary_image' => 'string',
        'gallery_images' => 'array'
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(400)
              ->height(300)
              ->sharpen(10)
              ->performOnCollections('primary_image', 'gallery')
              ->nonQueued();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if ($service->user && !$service->user->hasRole('admin')) {
                $service->is_approved = false;
            }
        });
    }

    public function serviceCategories()
    {
        return $this->belongsTo(ServiceCategories::class, 'service_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function getPrimaryImageUrl()
    {
        return $this->primary_image ? url($this->primary_image->image_path) : asset('image/no-image.png');
    }

}
