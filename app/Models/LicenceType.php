<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenceType extends Model
{
    protected $fillable = ['name'];
    

    public function property()
    {
        return $this->hasMany(Property::class);
    }
    
}