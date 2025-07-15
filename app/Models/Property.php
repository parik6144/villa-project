<?php

namespace App\Models;

use Angle\Airports\AirportLibrary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PropertyAttribute;
use App\Models\Attribute;
use PragmaRX\Countries\Package\Countries;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Property extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use LogsActivity;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')->useDisk('r2');
        $this->addMediaCollection('licence')->useDisk('r2');
        $this->addMediaCollection('floor-plan-list-gallery')->useDisk('r2');
    }

    protected $fillable = [
        'user_id',
        'property_type_id',
        'property_type_custom',
        'property_class',
        'basic_rate_commission_id',
        'kitchen_area_size',
        'kitchen_area_units',
        'living_area_size',
        'living_area_units',
        'heating_features',
        'aditional_features',
        'suitable_for',
        'suitable_for_custom',
        'common_expences',
        'year_of_construction',
        'year_of_renovation',
        'price_for_sale_eur',
        'price_for_sale_per_sq_m',
        'return_on_investment',
        'title',
        'slug',
        'active',
        'approval_status',
        'floorspace',
        'floorspace_units',
        'grounds',
        'grounds_units',
        'floors_in_building',
        'floors_of_property',
        'entrance',
        'rental_licence_type_id',
        'rental_licence_number',
        'latitude',
        'longitude',
        'country',
        'street',
        'address',
        'apartment_floor_building',
        'city',
        'state_or_region',
        'postal_code',
        'orientation',
        'commercial_title',
        'headline',
        'short_summary',
        'brief_description',
        'description',
        'suitable_for_kids',
        'events_allowed',
        'pets',
        'max_pets',
        'pets_fee',
        'wheelchair_access',
        'smoking_allowed',
        'camera',
        'noise_monitor',
        'house_rules',
        'basic_night_net',
        'basic_night_gross',
        'weekend_night_net',
        'weekend_night_gross',
        'monthly_rate',
        'monthly_rate_sqm',
        'max_guests',
        'min_stay_nights',
        'max_stay_nights',
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
        'is_cleaning',
        // 'is_published',
        'advance_booking_notice',
        'cancellation_policy',
        'additional_policy',
        'rates_increase',
        'rates_decrease',
        'additional_policy_2',
        'rates_increase_2',
        'rates_decrease_2',
        'allow_external_ical',
        'export_ical_url',
        'url_for_site_presentation',
        'planyo_resource_id',
    ];

    protected $appends = [
        'coordinates',
    ];

    protected $casts = [
        'aditional_features' => 'array',
        'slug' => 'string',
        'suitable_for' => 'array',
        'primary_image' => 'string',
        'gallery_images' => 'array',
        'floor_plan_list' => 'array',
        'deal_type_rent' => 'boolean',
        'deal_type_sale' => 'boolean',
        'deal_type_monthly_rent' => 'boolean',
        'is_cleaning' => 'boolean',
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

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(400)
              ->height(300)
              ->sharpen(10)
              ->performOnCollections('primary_image', 'gallery')
              ->nonQueued();
    }

    public function getCoordinatesAttribute(): array
    {
        return [
            "lat" => (float)$this->latitude,
            "lng" => (float)$this->longitude,
        ];
    }

    public function setCoordinatesAttribute(?array $location): void
    {
        if (is_array($location))
        {
            $this->attributes['latitude'] = $location['lat'];
            $this->attributes['longitude'] = $location['lng'];
            unset($this->attributes['coordinates']);
        }
    }

    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'latitude',
            'lng' => 'longitude',
        ];
    }

    public static function getComputedLocation(): string
    {
        return 'coordinates';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($property) {
            // if ($property->user && !$property->user->hasRole('admin')) {
            //     $property->approval_status = 'approved';
            // }
            if (auth()->check()) {
                $property->user_id = auth()->id();
            }
            $property->generateSlug();
        });

        static::saving(function ($property) {
            if (empty($property->slug)) {
                $property->generateSlug();
            }
        });
    }

    public function generateSlug()
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->title) . '-' . uniqid();
        }
        return $this->slug;
    }

    public function getSlug(): string
    {
        return $this->slug ?? "";
    }

    public function savePropertyData(array $data)
    {
        // \Log::info("savePropertyData called from:", debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5));

        $this->update($data);

        //Save deal type
        if (isset($data['deal_type'])) {
            $deal_type_rent = in_array('deal_type_rent', $data['deal_type'], true);
            $deal_type_sale = in_array('deal_type_sale', $data['deal_type'], true);
            $deal_type_monthly_rent = in_array('deal_type_monthly_rent', $data['deal_type'], true);

            $this->deal_type_rent = $deal_type_rent;
            $this->deal_type_sale = $deal_type_sale;
            $this->deal_type_monthly_rent = $deal_type_monthly_rent;
        }

        //Save attribute value
        $attributes = Attribute::all();

        foreach ($attributes as $attribute) {
            $currentId = $attribute->id;
            $formKey = "property_attributes[$currentId]";
        
            $value = $data[$formKey] ?? null;
        
            if ($attribute->type === 'multi-checkbox' && is_array($value)) {
                $value = json_encode($value);
            }
        
            if (!is_null($value) && $value !== '' && $value !== '[]') {
                PropertyAttribute::updateOrCreate(
                    ['property_id' => $this->id, 'attribute_id' => $currentId],
                    ['value' => $value]
                );
            } else {
                PropertyAttribute::where('property_id', $this->id)
                    ->where('attribute_id', $currentId)
                    ->delete();
            }
        }

        $dateForSale = $data['date_for_sale'] ?? null;

        PropertyAvailability::where('property_id', $this->id)
            ->where('type', 'sale')
            ->delete();
        
        if ($dateForSale !== null && $dateForSale !== '') {
            PropertyAvailability::create([
                'property_id' => $this->id,
                'type' => 'sale',
                'date_for_sale' => $dateForSale,
                'available' => true,
            ]);
        }

        // Save Availability
        if (isset($data['available_periods'])) {
            $availablePeriods = $data['available_periods'];

            // Remove old availaility
            PropertyAvailability::where('property_id', $this->id)
                ->where('type', 'rent')
                ->delete();

            if (!empty($availablePeriods)) {
                foreach ($availablePeriods as $period) {
                    PropertyAvailability::create([
                        'property_id' => $this->id,
                        'type' => 'rent',
                        'date_from' => $period['date_from'],
                        'date_to' => $period['date_to'],
                        'available' => true,
                    ]);
                }
            }
        }

        if (isset($data['unavailable_periods'])) {
            $unavailablePeriods = $data['unavailable_periods'];

            // Remove old availaility
            PropertyAvailability::where('property_id', $this->id)
                ->where('available', false)
                ->delete();

            if (!empty($unavailablePeriods)) {
                foreach ($unavailablePeriods as $period) {
                    PropertyAvailability::create([
                        'property_id' => $this->id,
                        'type' => 'rent',
                        'date_from' => $period['date_from'],
                        'date_to' => $period['date_to'],
                        'available' => false,
                    ]);
                }
            }
        }


        if (isset($data['basic_night_gross'])) {
            $this->basic_night_gross = $data['basic_night_gross'];
        }

        if (isset($data['weekend_night_gross'])) {
            $this->weekend_night_gross = $data['weekend_night_gross'];
        }



        //Save checkin/checkout days
        if (isset($data['check_in_days']) && isset($data['check_out_days'])) {
            $checkInDays = $data['check_in_days'] ?? [];
            $checkOutDays = $data['check_out_days'] ?? [];

            $allCheckInDays = true;
            $allCheckOutDays = true;

            foreach (['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'] as $day) {
                $checkInValue = in_array('checkin_' . $day, $checkInDays, true);
                $this->{'checkin_' . $day} = $checkInValue;

                if (!$checkInValue) {
                    $allCheckInDays = false;
                }
            }

            foreach (['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'] as $day) {
                $checkOutValue = in_array('checkout_' . $day, $checkOutDays, true);
                $this->{'checkout_' . $day} = $checkOutValue;

                if (!$checkOutValue) {
                    $allCheckOutDays = false;
                }
            }

            if ($allCheckInDays) {
                $this->checkin_any = true;
            } else {
                $this->checkin_any = false;
            }

            if ($allCheckOutDays) {
                $this->checkout_any = true;
            } else {
                $this->checkout_any = false;
            }
        }


        //Save Property Season
        if (isset($data['property_seasons'])) {
            $propertySeasons = $data['property_seasons'] ?? [];

            //remove Property Season data > now()
            PropertySeason::where('property_id', $this->id)
                ->where('date_to', '>', now())
                ->delete();

            foreach ($propertySeasons as $propertySeasonData) {
                $seasonType = $propertySeasonData['season_type'];
                $seasonId = $propertySeasonData['season_id'] ?? null;
                $customSeasonName = $propertySeasonData['custom_season_name'] ?? null;

                if ($seasonType === 'global') {
                    $season = Season::find($seasonId);

                    if ($season) {
                        $dateFrom = $season->date_from;
                        $dateTo = $season->date_to;
                    } else {
                        $dateFrom = null;
                        $dateTo = null;
                    }
                } else {
                    $dateFrom = $propertySeasonData['date_from'] ?? null;
                    $dateTo = $propertySeasonData['date_to'] ?? null;
                }

                $basicNightNet = $propertySeasonData['season_basic_night_net'] ?? null;
                $basicNightGross = $propertySeasonData['season_basic_night_gross'] ?? null;
                $weekendNightNet = $propertySeasonData['season_weekend_night_net'] ?? null;
                $weekendNightGross = $propertySeasonData['season_weekend_night_gross'] ?? null;
                $minStayNights = $propertySeasonData['min_stay_nights'] ?? null;
                $maxStayNights = $propertySeasonData['max_stay_nights'] ?? null;
                $discount = $propertySeasonData['discount'] ?? false;

                $checkInDays = $propertySeasonData['check_in_days'] ?? [];
                $checkOutDays = $propertySeasonData['check_out_days'] ?? [];

                $checkinColumns = [];
                $checkoutColumns = [];
                $allCheckInDays = true;
                $allCheckOutDays = true;

                foreach (['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'] as $day) {
                    $checkInValue = in_array('checkin_' . $day, $checkInDays, true);
                    $checkinColumns['checkin_' . $day] = $checkInValue;

                    $checkOutValue = in_array('checkout_' . $day, $checkOutDays, true);
                    $checkoutColumns['checkout_' . $day] = $checkOutValue;

                    if (!$checkInValue) {
                        $allCheckInDays = false;
                    }

                    if (!$checkOutValue) {
                        $allCheckOutDays = false;
                    }
                }

                PropertySeason::create([
                    'property_id' => $this->id,
                    'season_id' => $seasonType === 'global' ? $seasonId : null,
                    'custom_season_name' => $seasonType === 'custom' ? $customSeasonName : null,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'season_basic_night_net' => $basicNightNet,
                    'season_basic_night_gross' => $basicNightGross,
                    'season_weekend_night_net' => $weekendNightNet,
                    'season_weekend_night_gross' => $weekendNightGross,
                    'min_stay_nights' => $minStayNights,
                    'max_stay_nights' => $maxStayNights,
                    'discount' => $discount,
                    'checkin_mon' => $checkinColumns['checkin_mon'],
                    'checkin_tue' => $checkinColumns['checkin_tue'],
                    'checkin_wed' => $checkinColumns['checkin_wed'],
                    'checkin_thu' => $checkinColumns['checkin_thu'],
                    'checkin_fri' => $checkinColumns['checkin_fri'],
                    'checkin_sat' => $checkinColumns['checkin_sat'],
                    'checkin_sun' => $checkinColumns['checkin_sun'],
                    'checkin_any' => $allCheckInDays,
                    'checkout_mon' => $checkoutColumns['checkout_mon'],
                    'checkout_tue' => $checkoutColumns['checkout_tue'],
                    'checkout_wed' => $checkoutColumns['checkout_wed'],
                    'checkout_thu' => $checkoutColumns['checkout_thu'],
                    'checkout_fri' => $checkoutColumns['checkout_fri'],
                    'checkout_sat' => $checkoutColumns['checkout_sat'],
                    'checkout_sun' => $checkoutColumns['checkout_sun'],
                    'checkout_any' => $allCheckOutDays
                ]);
            }
        }



        //save property sites content
        if (isset($data['sitesContent'])) {
            $sitesContentData = $data['sitesContent'] ?? [];

            $this->sitesContent()->delete();

            foreach ($sitesContentData as $siteContent) {
                $this->sitesContent()->create([
                    'property_id' => $this->id,
                    'property_site_id' => $siteContent['property_site_id'],
                    'content' => $siteContent['content'],
                ]);
            }
        }

        //save taxes
        if (isset($data['taxes'])) {
            $taxes = $data['taxes'] ?? [];

            $this->taxes()->delete();

            foreach ($taxes as $tax) {
                $this->taxes()->create([
                    'property_id' => $this->id,
                    'tax_type' => $tax['tax_type'],
                    'fee_basis' => $tax['fee_basis'],
                    'amount' => $tax['amount'],
                    'is_percent' => $tax['is_percent'],
                ]);
            }
        }

        //save extras
        if (isset($data['extras'])) {
            $extras = $data['extras'] ?? [];

            $this->extras()->delete();

            foreach ($extras as $extra) {
                $this->extras()->create([
                    'property_id' => $this->id,
                    'extra_service' => $extra['extra_service'],
                    'fee_basis' => $extra['fee_basis'],
                    'amount' => $extra['amount'] ?? 0,
                    'earliest_order' => $extra['earliest_order'],
                    'latest_order' => $extra['latest_order'],
                    'additional_info' => $extra['additional_info'],
                    'is_custom' => isset($extra['is_custom']) ? $extra['is_custom'] : false,
                ]);

                // Copy extras to other properties
                if (isset($extra['copy_to_properties']) && !empty($extra['copy_to_properties'])) {
                    foreach ($extra['copy_to_properties'] as $copyPropertyId) {
                        PropertyExtras::updateOrCreate(
                            [
                                'property_id' => $copyPropertyId,
                                'extra_service' => $extra['extra_service'],
                            ],
                            [
                                'fee_basis' => $extra['fee_basis'],
                                'amount' => $extra['amount'] ?? 0,
                                'earliest_order' => $extra['earliest_order'],
                                'latest_order' => $extra['latest_order'],
                                'additional_info' => $extra['additional_info'],
                                'is_custom' => $extra['is_custom'] ?? false,
                            ]
                        );
                    }
                }
            }
        }

        //save bookingRules
        if (isset($data['booking_rules'])) {
            $bookingRules = $data['booking_rules'] ?? [];

            // Удаляем старые записи
            $this->bookingRules()->delete();

            foreach ($bookingRules as $bookingRule) {
                // Проверяем наличие параметров и создаем массив для записи
                $recordData = array_filter([
                    'property_id' => $this->id,
                    'is_free_cancellation' => $bookingRule['is_free_cancellation'],
                    'free_cancellation_period' => $bookingRule['free_cancellation_period'] ?? 'No free cancellation',
                    'cancellation_fee' => $bookingRule['cancellation_fee'],
                    'no_show_fee' => $bookingRule['no_show_fee'],
                    'rate_adjustment_type' => $bookingRule['rate_adjustment_type'] ?? null,
                    'rate_adjustment_value' => isset($bookingRule['rate_adjustment_type']) ? $bookingRule['rate_adjustment_value'] : null,
                ], function ($value) {
                    return !is_null($value); // Убираем null значения
                });

                // Проверяем условия is_free_cancellation
                if ($bookingRule['is_free_cancellation'] === 'yes' && empty($bookingRule['free_cancellation_period'])) {
                    throw new \Exception('The free cancellation period must be provided when free cancellation is enabled.');
                }

                // Если есть данные, создаем запись
                if (!empty($recordData)) {
                    $this->bookingRules()->create($recordData);
                }
            }
        }

        //save calendar
        if (isset($data['calendar'])) {
            $calendars = $data['calendar'] ?? [];

            $this->calendars()->delete();

            foreach ($calendars as $calendar) {
                $this->calendars()->create([
                    'property_id' => $this->id,
                    'calendar_source' => $calendar['calendar_source'],
                    'calendar_url' => $calendar['calendar_url'],
                    'calendar_name' => $calendar['calendar_name'],
                ]);
            }
        }

        $attached_instructions = [];

        $currentFiles = $this->instructions && $this->instructions->attached_instructions
            ? $this->instructions->attached_instructions
            : [];
        
        $newFiles = isset($data['instructions']['attached_instructions']) && is_array($data['instructions']['attached_instructions'])
            ? $data['instructions']['attached_instructions']
            : [];
        
        foreach ($newFiles as $file) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $slug = $this->getSlug();
                $instructionPath = "properties/{$slug}/instructions";
                $path = $file->store($instructionPath, 'r2');
        
                $attached_instructions[] = basename($path);
            } else {
                $attached_instructions[] = $file;
            }
        }
        
        $finalFiles = $attached_instructions;

        // dd($this->id, $data['instructions']);
        $this->instructions()->updateOrCreate(
            ['property_id' => $this->id],
            [
                'check_in' => $data['instructions']['check_in'] ?? null,
                'check_out' => $data['instructions']['check_out'] ?? null,
                'check_in_contact_person' => $data['instructions']['check_in_contact_person'] ?? null,
                'key_collection_point' => $data['instructions']['key_collection_point'] ?? null,
                'telephone_number' => $data['instructions']['telephone_number'] ?? null,
                'check_in_instructions' => $data['instructions']['check_in_instructions'] ?? null,
                'attached_instructions' => array_values($finalFiles),
                'closest_airports' => $data['instructions']['closest_airports'] ?? null,
                'directions' => $data['instructions']['directions'] ?? null,
            ]
        );


        // Проверяем, есть ли лицензии в данных
        $propertyLicences = $data['propertyLicences'] ?? [];
        $otherLicenceType = $data['otherLicenceType'] ?? [];


        // Удаляем все предыдущие записи лицензий только один раз
        $this->propertyLicences()->delete();

        // Обрабатываем обязательные лицензии
        if ($propertyLicences) {
            foreach ($propertyLicences as $licence) {
                $licence_filename = $licence['licence_file_name'] ?? null;

                if (is_array($licence_filename)) {
                    $filenames = [];

                    foreach ($licence['licence_file_name'] as $file) {
                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                            $slug = $this->getSlug();
                            $licencePath = "properties/{$slug}/licence";
                            $path = $file->store($licencePath, 'r2');
                            $filenames[] = basename($path);
                        } elseif (is_string($file)) {
                            $filenames[] = $file;
                        }
                    }

                    $licence_filename = $filenames ?: null;
                }

                $this->propertyLicences()->updateOrCreate(
                    [
                        'property_id' => $this->id,
                        'additional_licence_type_id' => $licence['additional_licence_type_id'],
                        'licence_number'    => $licence['licence_number'],
                        'licence_file_name' => (array)$licence_filename,
                    ]
                );
            }
        }

        // Обрабатываем необязательные лицензии
        if ($otherLicenceType) {
			foreach ($otherLicenceType as $licenceType) {
				$fileTempPath = $licenceType['file_temp_path'] ?? null;
				$other_licence_filename = $licenceType['other_licence_file'] ?? null;

				$otherFilenames = [];

				if ($fileTempPath) {
					$slug = $this->getSlug();
					$finalPath = "properties/{$slug}/licence/" . basename($fileTempPath);
					
					if (Storage::disk('r2')->exists($fileTempPath)) {
						$fileContents = Storage::disk('r2')->get($fileTempPath);

						Storage::disk('r2')->put($finalPath, $fileContents);

						Storage::disk('r2')->delete($fileTempPath);
					} 
					
					$otherFilenames[] = basename($finalPath);
				} elseif (is_string($other_licence_filename)) {
					$otherFilenames[] = $other_licence_filename;
				}

				$this->propertyLicences()->updateOrCreate([
					'property_id'                => $this->id,
					'additional_licence_type_id' => $licenceType['other_licence_type_id'],
					'licence_number'             => $licenceType['other_licence_number'],
					'licence_file_name'          => $otherFilenames ?: null,
				]);
			}
		}

        //Bedrooms save
		if (isset($data['bedrooms']) && is_array($data['bedrooms'])) {
			$incomingIds = [];

			foreach ($data['bedrooms'] as $bedroomData) {
				$bedroom = PropertyBedroom::updateOrCreate(
					[
						'id' => $bedroomData['id'] ?? null,
						'property_id' => $this->id,
					],
					[
						'name' => $bedroomData['name'] ?? null,
						'type' => !empty($bedroomData['type']) ? $bedroomData['type'] : null,
						'bunk_bed' => $bedroomData['bunk_bed'],
						'double_bed' => $bedroomData['double_bed'],
						'king_sized_bed' => $bedroomData['king_sized_bed'],
						'queen_sized_bed' => $bedroomData['queen_sized_bed'],
						'single_bed_adult' => $bedroomData['single_bed_adult'],
						'single_bed_child' => $bedroomData['single_bed_child'],
						'sofa_bed_double' => $bedroomData['sofa_bed_double'],
						'sofa_bed_single' => $bedroomData['sofa_bed_single'],
					]
				);

				$incomingIds[] = $bedroom->id;
			}

			PropertyBedroom::where('property_id', $this->id)
				->whereNotIn('id', $incomingIds)
				->delete();
		} else {
			PropertyBedroom::where('property_id', $this->id)->delete();
		}

        //bathrooms save
		if (isset($data['bathrooms']) && is_array($data['bathrooms'])) {
			$incomingIds = [];

			foreach ($data['bathrooms'] as $bathroomData) {
				$bathroom = PropertyBathroom::updateOrCreate(
					[
						'id' => $bathroomData['id'] ?? null,
						'property_id' => $this->id,
					],
					[
						'name' => $bathroomData['name'] ?? null,
						'private' => $bathroomData['private'] ?? false,
						'bathroom_type' => !empty($bathroomData['bathroom_type']) ? $bathroomData['bathroom_type'] : null,
						'toilet' => !empty($bathroomData['toilet']) ? $bathroomData['toilet'] : null,
						'shower' => !empty($bathroomData['shower']) ? $bathroomData['shower'] : null,
						'bath' => !empty($bathroomData['bath']) ? $bathroomData['bath'] : null,
					]
				);

				$incomingIds[] = $bathroom->id;
			}

			PropertyBathroom::where('property_id', $this->id)
				->whereNotIn('id', $incomingIds)
				->delete();
		} else {
			PropertyBathroom::where('property_id', $this->id)->delete();
		}

        //kitchens save
		if (isset($data['kitchens']) && is_array($data['kitchens'])) {
			$incomingIds = [];

			foreach ($data['kitchens'] as $kitchenData) {
				$kitchen = PropertyKitchen::updateOrCreate(
					[
						'id' => $kitchenData['id'] ?? null,
						'property_id' => $this->id,
					],
					[
						'name' => $kitchenData['name'] ?? null,
						'type' => !empty($kitchenData['type']) ? $kitchenData['type'] : null,
					]
				);

				$incomingIds[] = $kitchen->id;
			}

			PropertyKitchen::where('property_id', $this->id)
				->whereNotIn('id', $incomingIds)
				->delete();
		} else {
			PropertyKitchen::where('property_id', $this->id)->delete();
		}

        

        // synchronisation save
        if (isset($data['synchronisation']) && is_array($data['synchronisation'])) {
            $this->synchronisation()->delete();

            // dd($data['synchronisation']);
            foreach ($data['synchronisation'] as $sync) {
                $this->synchronisation()->updateOrCreate([
                    'synchronization_id'    => $sync['synchronization_id'],
                    'property_id'           => $this->id,
                    'url'                   => $sync['url'] ?? null,
                    'site_id'               => isset($sync['site_id']) && !empty($sync['site_id']) ? $sync['site_id'] : null
                ]);
            }
        }

        $this->save();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function property_attributes()
    {
        return $this->hasMany(PropertyAttribute::class);
    }

    public function getAttributeValueByName(string $attributeName): ?string
    {
        return $this->property_attributes()
            ->whereHas('attribute', fn($query) => $query->where('name', $attributeName))
            ->value('value');
    }

    public function hasAttributeValue(string $attributeName, string|array $value): bool
    {
        $values = is_array($value) ? $value : [$value];
    
        return $this->property_attributes()
            ->whereHas('attribute', fn($query) => $query->where('name', $attributeName))
            ->get()
            ->contains(fn($attr) => !empty(array_intersect($values, json_decode($attr->value, true) ?? [])));
    }

    public function availabilities()
    {
        return $this->hasMany(PropertyAvailability::class);
    }

    public function seasons()
    {
        return $this->hasMany(PropertySeason::class);
    }
    public function licenceType()
    {
        return $this->belongsTo(LicenceType::class);
    }
    public function additionalLicenceType()
    {
        return $this->belongsTo(additionalLicenceType::class);
    }
    // public function licenceType()
    // {
    //     return $this->hasMany(LicenceType::class);
    // }

    public function sitesContent()
    {
        return $this->hasMany(PropertySitesContent::class, 'property_id');
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class);
    }

    public function bookingRules()
    {
        return $this->hasMany(BookingRule::class);
    }
    public function calendars()
    {
        return $this->hasMany(PropertyCalendarSynchronization::class);
    }
    public function propertyLicences()
    {
        return $this->hasMany(PropertyLicence::class);
    }
    // public function getBasicNightGrossAttribute()
    // {
    //     if (!empty($this->attributes['basic_night_gross'])) {
    //         return $this->attributes['basic_night_gross'];
    //     }

    //     $basicNightNet = $this->attributes['basic_night_net'] ?? 0;
    //     $basicRateCommission = PropertySetting::first()?->value('basic_rate_commission') ?? 0;

    //     return $basicNightNet + ($basicNightNet * $basicRateCommission / 100);
    // }

    // public function getWeekendNightGrossAttribute()
    // {
    //     if (!empty($this->attributes['weekend_night_gross'])) {
    //         return $this->attributes['weekend_night_gross'];
    //     }

    //     $weekendNightNet = $this->attributes['weekend_night_net'] ?? 0;
    //     $basicRateCommission = PropertySetting::first()?->value('basic_rate_commission') ?? 0;

    //     return $weekendNightNet + ($weekendNightNet * $basicRateCommission / 100);
    // }

    public function bedrooms()
    {
        return $this->hasMany(PropertyBedroom::class);
    }

    public function bathrooms()
    {
        return $this->hasMany(PropertyBathroom::class);
    }

    public function kitchens()
    {
        return $this->hasMany(PropertyKitchen::class);
    }

    public function other_rooms()
    {
        return $this->hasMany(PropertyOtherRooms::class);
    }

    public function synchronisation()
    {
        return $this->hasMany(PropertySync::class, 'property_id');
    }


    public function instructions()
    {
        return $this->hasOne(PropertyInstruction::class);
    }

    public function extras()
    {
        return $this->hasMany(PropertyExtras::class, 'property_id');
    }

    public function getCountriesForSelect()
    {
        return Countries::all()
            ->pluck('name.common', 'cca2')
            ->toArray();
    }

    public function generateTimeSlots(): array
    {
        $timeSlots = [];
        $startTime = strtotime('00:00');
        $endTime = strtotime('23:45');

        for ($time = $startTime; $time <= $endTime; $time += 900) {
            $timeSlots[] = date('H:i', $time);
        }

        return $timeSlots;
    }

    public static function getCitiesForCountry(string $input, ?string $countryCode = null): array
    {

        // return Countries::where('cca2', $countryCode)
        //     ->first()
        //     ->hydrate('cities')
        //     ->cities
        //     ->pluck('name', 'name')
        //     ->toArray();

        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $client = new \GuzzleHttp\Client();

        $params = [
            'input' => $input,
            'types' => '(cities)',
            'key'   => $apiKey,
        ];

        if ($countryCode) {
            $params['components'] = 'country:' . $countryCode;
        }

        $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?' . http_build_query($params);

        try {
            $response = $client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['predictions'])) {
                return [];
            }

            $cities = collect($data['predictions'])->pluck('place_id')->map(function ($placeId) use ($client, $apiKey) {
                try {
                    $detailsUrl = 'https://maps.googleapis.com/maps/api/place/details/json?' . http_build_query([
                        'place_id' => $placeId,
                        'fields'   => 'address_components',
                        'key'      => $apiKey,
                    ]);

                    $detailsResponse = $client->get($detailsUrl);
                    $detailsData = json_decode($detailsResponse->getBody()->getContents(), true);

                    if (!isset($detailsData['result']['address_components'])) {
                        return null;
                    }

                    foreach ($detailsData['result']['address_components'] as $component) {
                        if (in_array('locality', $component['types']) || in_array('administrative_area_level_1', $component['types'])) {
                            return $component['long_name'];
                        }
                    }

                    return null;

                } catch (\Exception $e) {
                    return null;
                }
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

            return $cities;

        } catch (\Exception $e) {
            return [];
        }
    }



    public function searchAirports(string $query, $latitude, $longitude): array
    {
        $airports = AirportLibrary::getFullList();

        $result = [];
        
        // Ensure valid coordinates before processing
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return [];
        }

        foreach ($airports as $airport) {
            if (stripos($airport['name'], $query) !== false || stripos($airport['city'], $query) !== false || stripos($airport['iata'], $query) !== false) {
                
                $distance = $this->calculateDistance(
                    (float)$latitude ?? 0.0,
                    (float)$longitude ?? 0.0,
                    (float)$airport['lat'],
                    (float)$airport['lon']
                );

                $result[$airport['name'] . ', ' . $airport['city'] . ', ' . $airport['iata'] . ', ' . $distance] = "{$airport['name']} , {$airport['city']}, {$airport['iata']}, {$distance}";
            }
        }

        return $result;
    }


    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): string
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        if ($distance < 1) {
            return round($distance * 1000) . ' m';
        }

        return round($distance, 2) . ' km';
    }



    public function basicRateCommission()
    {
        return $this->belongsTo(BasicRateCommission::class);
    }
}
