<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class ServiceCategories extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function properties(): HasMany
    {
        return $this->hasMany(ServiceCategories::class);
    }
}
