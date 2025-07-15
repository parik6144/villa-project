<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model
{
    protected $fillable = ['name'];

    public function attributes()
    {
        return $this->hasMany(Attribute::class, 'group_id');
    }
}