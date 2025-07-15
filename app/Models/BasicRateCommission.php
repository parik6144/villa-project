<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicRateCommission extends Model
{
    use HasFactory;

    protected $table = 'basic_rate_commissions';

    protected $fillable = [
        'commission_type',
        'revenue_level',       
        'taxes',
        'agent_commission',
        'service',
        'commission_rate',     
    ];

    protected $casts = [
        'commission_type' => 'string',
        'revenue_level' => 'string',
        'taxes' => 'float',
        'agent_commission' => 'float',
        'service' => 'float',
        'commission_rate' => 'float',
    ];
}
