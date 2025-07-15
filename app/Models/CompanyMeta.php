<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PragmaRX\Countries\Package\Countries;

class CompanyMeta extends Model
{
    protected $table = 'company_meta';

    protected $fillable = [
        'user_id',
        'type',
        'about',
        'phone',
        'country',
        'city',
        'address',
        'address2',
        'state',
        'postal_code',
        'website',
        'telegram',
        'viber',
        'whatsapp',
        'facebook',
        'instagram',
        'tiktok',
        'tax_id',
        'iban',
        'beneficiary',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCountriesForSelect()
    {
        return Countries::all()
            ->pluck('name.common', 'cca2')
            ->toArray();
    }

    public function getCitiesForCountry($countryCode)
    {

        return Countries::where('cca2', $countryCode)
            ->first()
            ->hydrate('cities')
            ->cities
            ->pluck('name', 'name')
            ->toArray();
    }

}
