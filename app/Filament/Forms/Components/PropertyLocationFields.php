<?php

namespace App\Filament\Forms\Components;

use App\Filament\Resources\PropertyResource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Radio;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\IconPosition;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Cheesegrits\FilamentGoogleMaps\Fields\Geocomplete;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use App\Forms\Components\LocationChangedModal;
use App\Services\GeoPlugin;

use App\Services\GeoService;
use Filament\Forms\Components\View;

class PropertyLocationFields
{
    private static $tabTitle = 'Location';

    public static function create($property): Tab
    {
        $geoplugin = new GeoPlugin();
        $geoplugin->locate();

        $default_latitude = (!empty($geoplugin->latitude) && is_numeric($geoplugin->latitude)) ? $geoplugin->latitude : 50;
        $default_longitude = (!empty($geoplugin->longitude) && is_numeric($geoplugin->longitude)) ? $geoplugin->longitude : 30;


        // $latitude = $property->latitude ?? $latitude;
        // $longitude = $property->longitude ?? $longitude;

        return Tabs\Tab::make(self::$tabTitle)
            ->icon(fn(Get $get) => PropertyResource::getTabIcon($get('tab_icon_location')))
            ->iconPosition(IconPosition::After)
            ->columns(4)
            ->schema([

                LocationChangedModal::make('pin_changed_modal')
                    ->heading('Location changed')
                    ->registerActions([
                        Forms\Components\Actions\Action::make('Confirm')
                            ->action(function (Get $get, Set $set, $livewire) {

                                $selectedOption = $get('pin_changed_modal');

                                if ($selectedOption === 'correct') {
                                    $set('old_coordinates', $get('coordinates'));
                                    $set('old_city', $get('city'));
                                    $set('old_street', $get('street'));
                                    $set('old_country', $get('country'));
                                    $set('old_state_or_region', $get('state_or_region'));
                                    $set('old_postal_code', $get('postal_code'));
                                    //continue
                                } elseif ($selectedOption === 'incorrect') {
                                    // dd( $get('old_coordinates') );
                                    $set('search', '');
                                    $set('coordinates', ['lat' => floatval($get('old_coordinates')['lat']), 'lng' => floatval($get('old_coordinates')['lng'])]);
                                    $set('latitude', $get('old_coordinates')['lat']);
                                    $set('longitude', $get('old_coordinates')['lng']);
                                    $set('city', $get('old_city'));
                                    $set('street', $get('old_street'));
                                    $set('country', $get('old_country'));
                                    $set('state_or_region', $get('old_state_or_region'));
                                    $set('postal_code', $get('old_postal_code'));
                                } elseif ($selectedOption === 'save_both') {
                                    $set('search', '');
                                    $set('city', $get('old_city'));
                                    $set('street', $get('old_street'));
                                    $set('country', $get('old_country'));
                                    $set('state_or_region', $get('old_state_or_region'));
                                    $set('postal_code', $get('old_postal_code'));

                                    $set('old_coordinates', $get('coordinates'));
                                }

                                $set('pin_changed_modal', null);
                                $livewire->dispatch('close-modal', id: 'pin_changed_modal');

                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly('data.state_or_region');
                                $livewire->validateOnly('data.street');
                                $livewire->validateOnly('data.postal_code');
                            })
                    ]),

                Hidden::make('region'),
                Hidden::make('old_coordinates')
                    ->formatStateUsing(fn(Get $get) => $get('coordinates')),
                Hidden::make('old_city')
                    ->formatStateUsing(fn(Get $get) => $get('city')),
                Hidden::make('old_street')
                    ->formatStateUsing(fn(Get $get) => $get('street')),
                Hidden::make('old_country')
                    ->formatStateUsing(fn(Get $get) => $get('country')),
                Hidden::make('old_state_or_region')
                    ->formatStateUsing(fn(Get $get) => $get('state_or_region')),
                Hidden::make('old_postal_code')
                    ->formatStateUsing(fn(Get $get) => $get('postal_code')),

                Map::make('coordinates')
                    ->label('Location')
                    ->reactive()
                    ->autocompleteReverse(true)
                    // ->reverseGeocode([
                    //     // 'country' => '%C',
                    //     //'region' => '%A2',
                    //     // 'state_or_region' => '%A1 %A2',
                    //                 // 'city' => '%L',			
                    //     // 'street' => '%S',
                    //     // 'postal_code' => '%z',
                    // ])
                    ->height(fn() => '400px')
                    ->clickable(true)
                    ->geolocate()
                    // ->geolocateOnLoad(true, false)
                    ->defaultLocation(function (Get $get) use ($default_latitude, $default_longitude) {
                        if ($get('latitude') && $get('longitude')) {
                            return [$get('latitude'), $get('longitude')];
                        }

                        return [$default_latitude, $default_longitude];
                    })

                    ->autocomplete('search')
                    ->height(fn() => '400px')
                    ->afterStateUpdated(function ($state, ?array $old, callable $get, callable $set, $livewire) use ($default_latitude, $default_longitude) {

                        if ($old != null) {
                            $set('old_coordinates', $old);
                        } else {
                            $set('old_coordinates', ['lat' => floatval($default_latitude), 'lng' => floatval($default_longitude)]);
                        }

                        $set('latitude', $state['lat']);
                        $set('longitude', $state['lng']);

                        $reverseData = GeoService::reverseGeocode($state['lat'], $state['lng']);

                        if ($reverseData) {
                            $set('country',  $reverseData['country']);
                            $set('city', $reverseData['city']);
                            $set('street', $reverseData['street'] . (!empty($reverseData['address']) ? ', ' . $reverseData['address'] : ''));
                            $set('state_or_region', $reverseData['state_or_region']);
                            $set('postal_code', $reverseData['postal_code']);
                        }

                        //     //$set('coordinates', ['lat' => floatval($state['lat']), 'lng' => floatval($state['lng'])]);
                        //     $set('isMapInteraction', true);
                        // $set('search', '');

                        //     if ($get('city') == null) {
                        //         $set('city', $get('region'));
                        //     }

                        if ($state['lat'] != 0 && $state['lng'] != 0 && $state['lat'] != $default_latitude  &&  $state['lng'] != $default_longitude) {
                            $livewire->dispatch('open-modal', id: 'pin_changed_modal');
                        }
                    })
                    ->columnSpan(4),

                TextInput::make('helper')->label('')->extraAttributes(['style' => 'display: none;'])->dehydrated(false),

                TextInput::make('search')
                    // ->reactive()
                    // ->lazy()
                    ->columnSpan(4),

                TextInput::make('latitude')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set, $livewire, $component) {
                        $set('coordinates', [
                            'lat' => floatVal($state),
                            'lng' => floatVal($get('longitude')),
                        ]);
                        $set('latitude', $state ?: null);
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    ->lazy()
                    ->columnSpan(2)
                    ->live(true),
                TextInput::make('longitude')
                    ->reactive()
                    //->default('22.3')
                    ->afterStateUpdated(function ($state, callable $get, callable $set, $livewire, $component) {
                        $set('coordinates', [
                            'lat' => floatval($get('latitude')),
                            'lng' => floatVal($state),
                        ]);
                        $set('longitude', $state ?: null);
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    ->lazy()
                    ->columnSpan(2)
                    ->live(true),

                Select::make('country')
                    ->options((new \App\Models\Property())->getCountriesForSelect())
                    ->searchable()
                    ->required()
                    ->columnSpan(2)
                    ->reactive()
                    ->afterStateUpdated(function (?string $old, Set $set, $state, $livewire, $component) {
                        $set('search', '');
                        $set('city', '');
                        $set('street', '');
                        $set('state_or_region', '');
                        $set('postal_code', '');

                        $set('old_country', $old);
                        $set('isMapInteraction', false);
                        $set('country', $state ?: null);
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    }),

                Select::make('city')
                    ->label('City')
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(
                        fn(string $search, callable $get) =>
                        array_combine(
                            $cities = \App\Models\Property::getCitiesForCountry($search, $get('country')),
                            $cities
                        )
                    )
                    ->reactive()
                    ->afterStateUpdated(function (?string $old, Set $set, $state, $livewire, $component) {
                        $set('search', '');
                        $set('street', '');
                        $set('state_or_region', '');
                        $set('postal_code', '');

                        $set('old_city', $old);
                        $set('city', $state ?: null);

                        // $coordinates = GeoService::geocode($state);

                        // if ($coordinates instanceof \Geocoder\Model\Coordinates) {
                        //     $latitude = $coordinates->getLatitude();
                        //     $longitude = $coordinates->getLongitude();

                        //     $set('coordinates', ['lat' => floatval($latitude), 'lng' => floatval($longitude)]);
                        //     $set('latitude', $latitude);
                        //     $set('longitude', $longitude);
                        // }

                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    ->columnSpan(2),

                TextInput::make('street')
                    ->label('Street')
                    ->required()
                    ->columns(2)
                    // ->reactive()
                    ->columnSpan(4)
                    // ->live(true)
                    ->afterStateUpdated(function (?string $old, Set $set, $state, $livewire, $component) {
                        $set('old_street', $old);
                        $set('street', $state ?: null);
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    ->extraInputAttributes([
                        'data-required' => 'true',
                        'name' => 'street',
                        'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                    ]),


                TextArea::make('address')
                    ->label('Address')
                    ->nullable()
                    ->columnSpan(2)
                    // ->live(true)
                    ->extraInputAttributes(['autocomplete' => 'nope'])
                    ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                        $set('address', $state ?: null);
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    }),

                TextArea::make('apartment_floor_building')
                    ->label('Apartment, Floor, Building')
                    ->nullable()
                    ->columnSpan(2)
                    // ->live(true)
                    ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                        $set('apartment_floor_building', $state ?: null);
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    }),

                TextInput::make('state_or_region')
                    ->label('State or Region')
                    ->required()
                    // ->reactive()
                    // ->live(true)
                    ->afterStateUpdated(function ($state, ?string $old, callable $set, callable $get, $livewire, $component) {
                        $set('old_state_or_region', $old);
                        $set('state_or_region', $state ?: null);
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    ->columnSpan(2)
                    ->extraInputAttributes([
                        'data-required' => 'true',
                        'name' => 'state or region',
                        'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                    ]),

                TextInput::make('postal_code')
                    ->label('Postal/Zip Code')
                    ->maxLength(6)
                    ->required()
                    ->reactive()
                    ->live(true)
                    ->afterStateUpdated(function ($state, ?string $old, callable $set, callable $get, $livewire, $component) {
                        $set('old_postal_code', $old);
                        $set('postal_code', $state ?: null);
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    })
                    ->columnSpan(2)
                    ->extraInputAttributes([
                        'data-required' => 'true',
                        'name' => 'postal/Zip Code',
                        'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                    ]),

                Radio::make('orientation')
                    ->label('Orientation')
                    ->options([
                        'East' => 'East',
                        'East West' => 'East West',
                        'East meridian' => 'East meridian',
                        'North' => 'North',
                        'North east' => 'North east',
                        'North west' => 'North west',
                        'West' => 'West',
                        'West meridian' => 'West meridian',
                        'Meridian' => 'Meridian',
                        'South' => 'South',
                        'South east' => 'South east',
                        'South west' => 'South west',
                    ])
                    ->columnSpan(4)
                    ->columns(4)
                    ->disabled(fn() => !Auth::user()->hasRole(['admin', 'property_owner']))
                    ->live(true)
                    ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire, $component) {
                        $set('orientation', $state ?: null);
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    }),
                View::make('components.client-side-validation'),
            ]);
    }
}
