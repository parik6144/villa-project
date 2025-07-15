<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'group_id', 
        'name', 
        'type', 
        'options', 
        'is_required',
        'default',
        'description',
        'notification',
        'example'
    ];

    public function group()
    {
        return $this->belongsTo(AttributeGroup::class, 'group_id');
    }
}