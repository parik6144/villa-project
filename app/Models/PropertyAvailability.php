<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyAvailability extends Model
{
    use LogsActivity;
    protected $table = 'property_availability';

    protected $fillable = ['property_id', 'type', 'date_from', 'date_to', 'date_for_sale', 'available'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public static function checkPeriodOverlap(array $periods)
    {
        foreach ($periods as $index => $period) {
            $dateFrom = new \DateTime($period['date_from']);
            $dateTo = new \DateTime($period['date_to']);
    
            foreach ($periods as $compareIndex => $comparePeriod) {
                // Skip checking the same period
                if ($index === $compareIndex) {
                    continue;
                }
    
                $compareDateFrom = new \DateTime($comparePeriod['date_from']);
                $compareDateTo = new \DateTime($comparePeriod['date_to']);
    
                // Check for overlap, including the case where last night of one period is the same as the first night of the next period
                if (
                    ($dateFrom > $compareDateFrom && $dateFrom < $compareDateTo) || // Start of the period overlaps with the compare period
                    ($dateTo > $compareDateFrom && $dateTo < $compareDateTo) || // End of the period overlaps with the compare period
                    ($dateFrom <= $compareDateFrom && $dateTo >= $compareDateTo) || // The period completely contains the compare period
                    ($dateTo > $compareDateFrom && $dateTo <= $compareDateTo)     // End date of the current period is the same or after the start of the next period (not strict overlap)
                ) {
                    return [
                        'start' => $dateFrom->format('M d, Y'), // Start date of the current period
                        'end' => $dateTo->format('M d, Y'),     // End date of the current period
                        'overlapping_period' => $compareDateFrom->format('M d, Y') . ' - ' . $compareDateTo->format('M d, Y'), // The period that overlaps
                    ];
                }
            }
        }
    
        return false;
    }

    public function scopeForSale($query)
    {
        return $query->where('type', 'sale');
    }

    public function scopeForRent($query)
    {
        return $query->where('type', 'rent');
    }
}
