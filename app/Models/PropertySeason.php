<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BasicRateCommission;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertySeason extends Model
{
    use HasFactory;
    use LogsActivity;


    protected $fillable = [
        'property_id',
        'season_id',
        'custom_season_name',
        'date_from',
        'date_to',
        'season_basic_night_net',
        'season_basic_night_gross',
        'season_weekend_night_net',
        'season_weekend_night_gross',
        'discount',
        'min_stay_nights',
        'max_stay_nights',
        'check_in_days',
        'check_out_days',
        'checkin_mon',
        'checkin_tue',
        'checkin_wed',
        'checkin_thu',
        'checkin_fri',
        'checkin_sat',
        'checkin_sun',
        'checkin_any',
        'checkout_mon',
        'checkout_tue',
        'checkout_wed',
        'checkout_thu',
        'checkout_fri',
        'checkout_sat',
        'checkout_sun',
        'checkout_any',
    ];

    protected $casts = [
        'checkin_mon' => 'boolean',
        'checkin_tue' => 'boolean',
        'checkin_wed' => 'boolean',
        'checkin_thu' => 'boolean',
        'checkin_fri' => 'boolean',
        'checkin_sat' => 'boolean',
        'checkin_sun' => 'boolean',
        'checkin_any' => 'boolean',
        'checkout_mon' => 'boolean',
        'checkout_tue' => 'boolean',
        'checkout_wed' => 'boolean',
        'checkout_thu' => 'boolean',
        'checkout_fri' => 'boolean',
        'checkout_sat' => 'boolean',
        'checkout_sun' => 'boolean',
        'checkout_any' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function getSeasonBasicNightGrossAttribute()
    {
		$net = $this->attributes['season_basic_night_net'] ?? 0;
		
		if (!$this->property || !$this->property->basic_rate_commission_id) {
			return $net;
		}
		$commission = BasicRateCommission::find($this->property->basic_rate_commission_id);
		
		if (!$commission) {
			return $net;
		}
		
		$totalCommission = collect([
			$commission->commission_rate,
			$commission->taxes,
			$commission->agent_commission,
			$commission->service,
		])->sum();
	
		return round($net + ($net * $totalCommission / 100), 2);
    }

	public function getSeasonWeekendNightGrossAttribute(): float
	{
		if (!empty($this->attributes['season_weekend_night_gross'])) {
			return $this->attributes['season_weekend_night_gross'];
		}

		$net = $this->attributes['season_weekend_night_net'] ?? 0;

		if (!$this->property || !$this->property->basic_rate_commission_id) {
			return $net;
		}

		$commission = BasicRateCommission::find($this->property->basic_rate_commission_id);
		if (!$commission) {
			return $net;
		}

		$totalCommission = collect([
			$commission->commission_rate,
			$commission->taxes,
			$commission->agent_commission,
			$commission->service,
		])->sum();

		return round($net + ($net * $totalCommission / 100), 2);
	}
}
