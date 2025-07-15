<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use PragmaRX\Countries\Package\Countries;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class UserMeta extends Model
{
    use HasFactory;
    use LogsActivity;




    protected $table = 'user_meta';
    protected $primaryKey = 'user_id';


    protected $fillable = [
        'user_id',
        'company_name',
        'company_type',
        'role_in_company',
        'website_link',
        'country_code',
        'number',
        'mobile_number',
        'telegram',
        'viber',
        'whatsapp',
        'facebook',
        'instagram',
        'tiktok',
        'birthday',
        'address',
        'about_agency',
        'heard_about_us',
        'additional_comments',
        'rent',
        'real_estate',
        'service',
        'approval_status',
        'disabled',
        'parent_id',
        'tax_id',
        'iban',
        'beneficiary',
        'accountant_id',
        'city',
        'postal_code',
        'state_province',
        'street_address',
        'street_address_line_2',
        'user_planyo_id',
        'language',
        'registration_time',
        'reservation_count',
        'last_reservation',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $casts = [
        'rent'        => 'boolean',
        'real_estate' => 'boolean',
        'service'     => 'boolean',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentAgent()
    {
        return $this->belongsTo(User::class, 'parent_id');
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

    public function accountant()
    {
        return $this->belongsTo(User::class, 'accountant_id');
    }

    // В App\Models\UserMeta
    public function companyEmployee()
    {
        return $this->hasOne(CompanyEmployee::class, 'employee_user_id', 'user_id');
    }
}
