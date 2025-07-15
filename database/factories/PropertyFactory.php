<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition()
    {
        $basicNightNet = $this->faker->randomFloat(2, 50, 500);
        $basicNightGross = $basicNightNet * (1 + $this->faker->randomFloat(2, 0.05, 0.25));

        $weekendNightNet = $this->faker->randomFloat(2, $basicNightNet, $basicNightNet * 1.5);
        $weekendNightGross = $weekendNightNet * (1 + $this->faker->randomFloat(2, 0.05, 0.25));

        return [
            'user_id' => \App\Models\User::factory(),
            'property_type_id' => $this->faker->numberBetween(1, 8),
            'basic_rate_commission_id' => $this->faker->numberBetween(1, 3),
            'title' => $this->faker->sentence(),
            'address' => $this->faker->address(),
            'active' => true,
            'deal_type_rent' => true,
            'deal_type_sale' => (bool)rand(0,1),
            'approval_status' => $this->faker->randomElement(['pending', 'approved', 'declined']),
            'floorspace' => $this->faker->randomFloat(2, 50, 500),
            'floorspace_units' => $this->faker->randomElement(['m2', 'ft2']),
            'grounds' => $this->faker->randomFloat(2, 100, 1000),
            'grounds_units' => $this->faker->randomElement(['m2', 'ft2']),
            'floors_in_building' => $this->faker->numberBetween(1, 10),
            'floors_of_property' => $this->faker->numberBetween(1, 5),
            'entrance' => $this->faker->randomElement(['Secured', 'Unsecured', 'Private']),
            'rental_licence_type_id' => $this->faker->numberBetween(1, 4),
            'rental_licence_number' => $this->faker->word(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'country' => $this->faker->countryCode(),
            'city' => $this->faker->city(),
            'state_or_region' => $this->faker->state(),
            'postal_code' => $this->faker->numberBetween(100000, 999999),
            'apartment_floor_building' => $this->faker->optional()->word(),
            'headline' => $this->faker->sentence(),
            'short_summary' => $this->faker->text(),
            'brief_description' => $this->faker->text(),
            'description' => $this->faker->paragraph(),
            'suitable_for_kids' => $this->faker->randomElement(['welcome', 'great', 'not_suitable']),
            'smoking_allowed' => $this->faker->randomElement(['no_smoking', 'allowed', 'outside']),
            'basic_night_net' => $basicNightNet,
            'basic_night_gross' => $basicNightGross,
            'weekend_night_net' => $weekendNightNet,
            'weekend_night_gross' => $weekendNightGross,
            'max_guests' => $this->faker->numberBetween(1, 20),
            'min_stay_nights' => $this->faker->numberBetween(1, 7),
            'max_stay_nights' => $this->faker->numberBetween(7, 30),
            'checkin_any' => true,
            'checkout_any' => true,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (\App\Models\Property $property) {
            $propertyId = $property->id;

            $seasons = collect([]);

            $globalSeasons = \App\Models\Season::all();

            foreach ($globalSeasons as $season) {

                $basicNightNet = $this->faker->randomFloat(2, 50, 500);
                $basicNightGross = $basicNightNet * (1 + $this->faker->randomFloat(2, 0.05, 0.25));

                $weekendNightNet = $this->faker->randomFloat(2, $basicNightNet, $basicNightNet * 1.5);
                $weekendNightGross = $weekendNightNet * (1 + $this->faker->randomFloat(2, 0.05, 0.25));

                $seasons->push([
                    'property_id' => $propertyId,
                    'season_id' => $season->id,
                    'custom_season_name' => null,
                    'date_from' => $season->date_from,
                    'date_to' => $season->date_to,
                    'season_basic_night_net' => $basicNightNet,
                    'season_basic_night_gross' =>  $basicNightGross,
                    'season_weekend_night_net' => $weekendNightNet,
                    'season_weekend_night_gross' => $weekendNightGross,
                    'discount' => $this->faker->boolean(),
                    'min_stay_nights' => $this->faker->numberBetween(1, 7),
                    'max_stay_nights' => $this->faker->numberBetween(7, 30),
                    'checkin_any' => true,
                    'checkout_any' => true,
                ]);
            }

            $seasons->each(fn ($season) => \App\Models\PropertySeason::create($season));
        });
    }
}
