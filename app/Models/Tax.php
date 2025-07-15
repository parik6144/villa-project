<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $table = 'property_taxes';

    const TAX_TYPES = [
        'City tax' => 'City tax',
        'Destination fee' => 'Destination fee',
        'Goods and services tax' => 'Goods and services tax',
        'Government tax' => 'Government tax',
        'Local tax' => 'Local tax',
        'Resort fee' => 'Resort fee',
        'Tax' => 'Tax',
        'Tourism fee' => 'Tourism fee',
        'VAT (Value Added Tax)' => 'VAT (Value Added Tax)',
    ];

    const FEE_BASIS = [
        '% of Rental Amount' => '% of Rental Amount',
        'Per adult / day' => 'Per adult / day',
        'Per adult / stay' => 'Per adult / stay',
        'Per adult / week' => 'Per adult / week',
        'Per day' => 'Per day',
        'Per person / day' => 'Per person / day',
        'Per person / stay' => 'Per person / stay',
        'Per person / week' => 'Per person / week',
        'Per stayPer week' => 'Per stayPer week',
    ];

    protected $fillable = [
        'property_id', 
        'tax_type', 
        'fee_basis', 
        'amount',
        'is_percent'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public static function getTaxTypes()
    {
        return self::TAX_TYPES;
    }

    public static function getFeeBasisOptions()
    {
        return self::FEE_BASIS;
    }
}
