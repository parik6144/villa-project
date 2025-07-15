<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class S3Setting extends Model
{
    protected $table = 's3_settings'; 

    protected $fillable = [
        'key', 'secret', 'token', 'region', 'bucket', 'endpoint',
        'use_path_style_endpoint', 'visibility', 'url', 'throw'
    ];

    protected $casts = [
        'use_path_style_endpoint' => 'boolean',
        'throw' => 'boolean',
    ];

    public $timestamps = true;
}
