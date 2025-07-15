<?php
namespace App\Filament\Forms\Components;

use App\Filament\Resources\PropertyResource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Actions\Action;
use App\Models\Season;
use App\Models\PropertySeason;
use App\Models\BasicRateCommission;
use App\Models\PropertyAvailability;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\IconPosition;
use Filament\Notifications\Notification;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;

class PropertySeasonalRatesFields
{
    private static $tabTitle = 'Seasonal rates';

    public static function create(): Tab
    {
        return Tabs\Tab::make(self::$tabTitle)
        ->icon(fn (Get $get) => PropertyResource::getTabIcon($get('tab_icon_seasonal_rates')))
        ->iconPosition(IconPosition::After)
        // ->visible(fn (Get $get): bool => 
        //     $get('deal_type') && in_array('deal_type_rent', $get('deal_type'))
        // )
        ->visible(fn (Get $get): bool => 
            is_array($get('deal_type')) && 
            (in_array('deal_type_rent', $get('deal_type')) || in_array('deal_type_monthly_rent', $get('deal_type')))
        )
        ->schema([
            Group::make()
                ->schema([

                    AlertBox::make()
                        ->helperText('Please note: The Seasonal Rates page allows you to set specific pricing for different seasons, holidays, or peak periods. These rates will override your standard pricing for the selected dates, enabling you to maximize earnings during high-demand periods or offer discounts during low-demand times. Ensure your seasonal rates align with market trends and guest expectations for those periods.')
                        ->success()
                        ->resolveIconUsing(name: 'heroicon-o-information-circle')
			->extraAttributes(['class' => 'custom-background-helper-text']),
                    TableRepeater::make('property_seasons')
                        ->label('Seasonal rates')
                        ->columns(2)
                        ->cloneable()
                        ->collapsible()
                        ->collapsed()
                        ->reorderable(false)
                        ->reactive()
                        ->deleteAction(
                            fn (Action $action) => $action->requiresConfirmation(),
                        )
                        ->rule(function (callable $get, $record = null) {
                            return function (string $attribute, $value, $fail) use ($get, $record) {
                                $property_seasons = $get('property_seasons');

                                // Exclude seasons with discount == true
                                $filtered_seasons = array_filter($property_seasons, function ($season) {
                                    return empty($season['discount']);
                                });

                                // Check overlap with other season periods
                                $overlap = PropertyAvailability::checkPeriodOverlap($filtered_seasons);
                                if ($overlap) {
                                    $fail("The season period {$overlap['start']} - {$overlap['end']} overlaps with another season period {$overlap['overlapping_period']}");
                                }
                            };
                        })
                        ->formatStateUsing(function ($state, $component) {
                            $record = $component->getRecord();

                            if (!$record || !$record->id) {
                                return [];
                            }

                            $propertySeasons = PropertySeason::where('property_id', $record->id)
                                ->where('date_to', '>', now())
                                ->get()
                                ->map(function ($season) {

                                    $daysOfWeek = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

                                    $check_in_days = [];
                                    $check_out_days = [];
                                    
                                    if($season->checkin_any){
                                        $check_in_days = array_map(function($day){
                                            return 'checkin_' . $day;
                                        }, $daysOfWeek);
                                    }else{
                                        foreach ($daysOfWeek as $day) {
                                            $field = 'checkin_' . $day;
                                            if ($season->{$field}) {
                                                $check_in_days[] = $field;
                                            }
                                        }
                                    }

                                    if($season->checkout_any){
                                        $check_out_days = array_map(function($day){
                                            return 'checkout_' . $day;
                                        }, $daysOfWeek);
                                    }else{
                                        foreach ($daysOfWeek as $day) {
                                            $field = 'checkout_' . $day;
                                            if ($season->{$field}) {
                                                $check_out_days[] = $field;
                                            }
                                        }
                                    }

                                    return [
                                        'season_type' => $season->season_id !== null ? 'global' : 'custom',
                                        'season_id' => $season->season_id,
                                        'custom_season_name' => $season->custom_season_name,
                                        'date_from' => $season->date_from,
                                        'date_to' => $season->date_to,
                                        'season_basic_night_net' => round($season->season_basic_night_net, 2),
                                        'season_basic_night_gross' => round($season->season_basic_night_gross, 2),
                                        'season_weekend_night_net' => round($season->season_weekend_night_net, 2),
                                        'season_weekend_night_gross' => round($season->season_weekend_night_gross, 2),
                                        'min_stay_nights' => $season->min_stay_nights,
                                        'max_stay_nights' => $season->max_stay_nights,
                                        'discount' => $season->discount,
                                        'check_in_days' => $check_in_days,
                                        'check_out_days' => $check_out_days
                                    ];
                                })
                                ->toArray();


                            return $state ?: $propertySeasons;
                        })
                        ->addActionLabel('Add season')
                        ->headers( self::tableHeader() )
                        ->streamlined()
                        ->schema( self::tableFields() )
                        ->extraItemActions([
                            Action::make('Edit')
                                ->icon('heroicon-s-pencil')
                                ->modalHeading('Edit Season')
                                ->fillForm(function (array $arguments, Repeater $component, Get $get): array {
                                    $allItems = $component->getState();
                                    $currentKey = $arguments['item'];
                                    $formData  = $allItems[$currentKey] ?? [];
                                    $formData['basic_rate_commission'] = BasicRateCommission::find($get('basic_rate_commission_id'))->commission_rate;
                                    
                                    return $formData;
                                })
                                ->form([

                                    Grid::make(2)
                                        ->schema( self::formFields(false, true) )

                                ])
                                ->action(function (array $arguments, array $data, $component, Set $set, Get $get, $livewire): void {
                                    $mainState = $component->getState();
                                    $key = $arguments['item'];
                                    $mainState[$key] = $data;

                                    $component->state($mainState);

                                    PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                    $livewire->validateOnly($component->getStatePath());
                                })
                        ])
                        ->addAction(function ($action) {
                            return $action->form([

                                Grid::make(2)
                                    ->schema( self::formFields() )
                                ])
                                ->fillForm(function (array $arguments, Repeater $component, Get $get): array {
                                    $formData  = [];
                                    $formData['basic_rate_commission'] = BasicRateCommission::find($get('basic_rate_commission_id'))->commission_rate ?? 1;
                                    $formData['season_type'] = 'global';
                                    
                                    return $formData;
                                })
                                ->action(function ($data, Set $set, Get $get, $component, $livewire) {
                                    $currentState = $get('property_seasons') ?? [];
                                    $result = array_merge($currentState, [$data]);
                                    $set('property_seasons', $result);

                                    PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                    $livewire->validateOnly($component->getStatePath());
                                });
                        })
                ]),

            //previous seasonal rates
            Group::make()
            ->schema([

                TableRepeater::make('property_previous_seasons')
                    ->label('Seasonal rates')
                    ->columns(2)
                    ->addable(false)
                    ->reorderable(false)
                    ->visible(fn ($get) => $get('show_previous'))
                    ->deleteAction(function (Action $action) {
                        $action->visible(false);
                    })
                    ->formatStateUsing(function ($state, $component) {
                        $record = $component->getRecord();

                        if (!$record || !$record->id) {
                            return [];
                        }

                        $propertySeasons = PropertySeason::where('property_id', $record->id)
                            ->where('date_to', '<', now())
                            ->get()
                            ->map(function ($season) {

                                $daysOfWeek = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

                                $check_in_days = [];
                                $check_out_days = [];
                                
                                if($season->checkin_any){
                                    $check_in_days = array_map(function($day){
                                        return 'checkin_' . $day;
                                    }, $daysOfWeek);
                                }else{
                                    foreach ($daysOfWeek as $day) {
                                        $field = 'checkin_' . $day;
                                        if ($season->{$field}) {
                                            $check_in_days[] = $field;
                                        }
                                    }
                                }

                                if($season->checkout_any){
                                    $check_out_days = array_map(function($day){
                                        return 'checkout_' . $day;
                                    }, $daysOfWeek);
                                }else{
                                    foreach ($daysOfWeek as $day) {
                                        $field = 'checkout_' . $day;
                                        if ($season->{$field}) {
                                            $check_out_days[] = $field;
                                        }
                                    }
                                }

                                return [
                                    'season_type' => $season->season_id !== null ? 'global' : 'custom',
                                    'season_id' => $season->season_id,
                                    'custom_season_name' => $season->custom_season_name,
                                    'date_from' => $season->date_from,
                                    'date_to' => $season->date_to,
                                    'season_basic_night_net' => round($season->season_basic_night_net),
                                    'season_basic_night_gross' => round($season->season_basic_night_gross, 2),
                                    'season_weekend_night_net' => round($season->season_weekend_night_net, 2),
                                    'season_weekend_night_gross' => round($season->season_weekend_night_gross, 2),
                                    'min_stay_nights' => $season->min_stay_nights,
                                    'max_stay_nights' => $season->max_stay_nights,
                                    'discount' => $season->discount,
                                    'check_in_days' => $check_in_days,
                                    'check_out_days' => $check_out_days,
                                ];
                            })
                            ->toArray();


                        return $state ?: $propertySeasons;
                    })
                    ->headers( self::tableHeader() )
                    ->streamlined()
                    ->schema( self::tableFields() )
                    ->extraItemActions([
                        Action::make('Detail')
                            ->icon('heroicon-o-eye')
                            ->modalHeading('Edit Season')
                            ->modalSubmitAction(false)
                            ->fillForm(function (array $arguments, Repeater $component, Get $get): array {
                                $allItems = $component->getState();
                                $currentKey = $arguments['item'];
                                $formData  = $allItems[$currentKey] ?? [];
                                $formData['basic_rate_commission'] = BasicRateCommission::find($get('basic_rate_commission_id'))->commission_rate;
                                
                                return $formData;
                            })
                            ->form([
                                Grid::make(2)
                                    ->schema( self::formFields(true) )
                                    ->disabled(true)
                            ])
                    ])
            ]),

            Toggle::make('show_previous')
                ->label('Show previous seasonal rates')
                ->default(false)
                ->reactive()
                ->visible(function ($get, $component) {
                    $record = $component->getRecord();

                    if (!$record || !$record->id) {
                        return [];
                    }

                    if (!$record) {
                        return false;
                    }

                    return PropertySeason::query()
                        ->where('property_id', $record->id)
                        ->where('date_to', '<', now())
                        ->exists();
                }),
            ]);
    }

    private static function tableHeader() : array
    {
        return [
            Header::make('Seasonal name')->width('180px'),
            Header::make('Date From')->width('100px')
                ->align(Alignment::Center),
            Header::make('Date To')->width('100px')
                ->align(Alignment::Center),
            Header::make('Basic Night Net')
                ->align(Alignment::Center),
            Header::make('Basic Night Gross')
                ->align(Alignment::Center),
            Header::make('Weekend Night Net')
                ->align(Alignment::Center),
            Header::make('Weekend Night Gross')
                ->align(Alignment::Center),
            Header::make('Discount')->width('20px')
                ->align(Alignment::Center),
        ];
    }

    private static function tableFields() : array
    {
        return [

            Placeholder::make('season_placeholder')
                ->label(false)
                ->content(fn (Get $get) => $get('season_type') === 'global'
                    ? optional(Season::find($get('season_id')))->season_title
                    : $get('custom_season_name')),

            Placeholder::make('date_from_placeholder')
                ->label(false)
                ->content(fn (Get $get) => $get('date_from')),

            Placeholder::make('date_to_placeholder')
                ->label(false)
                ->content(fn (Get $get) => $get('date_to')),

            Placeholder::make('basic_night_net_placeholder')
                ->label(false)
                ->content(fn (Get $get) => $get('season_basic_night_net') . ' EUR'),

            Placeholder::make('basic_night_gross_placeholder')
                ->label(false)
                ->content(fn (Get $get) => $get('season_basic_night_gross') . ' EUR'),

            Placeholder::make('weekend_night_net_placeholder')
                ->label(false)
                ->content(fn (Get $get) => $get('season_weekend_night_net') . ' EUR'),

            Placeholder::make('weekend_night_gross_placeholder')
                ->label(false)
                ->content(fn (Get $get) => $get('season_weekend_night_gross') . ' EUR'),

            Placeholder::make('discount_placeholder')
                ->label(false)
                ->content(fn (Get $get) => $get('discount') ? 'Yes' : 'No'),

        ];
    }

    private static function formFields($is_previous = false, $is_edit = false) : array
    {
        return [

            Radio::make('season_type')
                ->label('Season Type')
                ->options([
                    'global' => 'Season',
                    'custom' => 'Custom season',
                ])
                ->default('global')
                ->reactive()
                ->inline(),

            Select::make('season_id')
                ->label('Season')
                ->options(
                    Season::when(
                        !$is_previous, 
                        function ($query) {
                            return $query->newSeasons(); 
                        },
                        function ($query) {
                            return $query->allSeasons();
                        }
                    )
                        // ->get()
                        ->pluck('season_title', 'id')
                        ->mapWithKeys(function ($season, $id) {
                            $seasonRecord = Season::find($id);
                            return [
                                $id => "{$seasonRecord->season_title} ({$seasonRecord->date_from} - {$seasonRecord->date_to})"
                            ];
                        })
                )
                ->required()
                ->visible(fn ($get) => $get('season_type') === 'global')
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    if ($state) {
                        $season = Season::find($state);
                        if ($season) {
                            $set('date_from', $season->date_from);
                            $set('date_to', $season->date_to);
                        }
                    }
                })
                ->columnSpanFull(),

            TextInput::make('custom_season_name')
                ->label('Custom season name')
                ->required()
                ->visible(fn ($get) => $get('season_type') === 'custom')
                ->columnSpanFull(),

            Hidden::make('date_from')
                ->visible(fn ($get) => $get('season_type') === 'global'),
            Hidden::make('date_to')
                ->visible(fn ($get) => $get('season_type') === 'global'),

            DatePicker::make('date_from')
                ->label('Date From')
                ->prefixIcon('heroicon-o-calendar')
                ->before('date_to')
                ->visible(fn ($get) => $get('season_type') === 'custom')
                ->required(),

            DatePicker::make('date_to')
                ->label('Date to')
                ->required()
                ->visible(fn ($get) => $get('season_type') === 'custom')
                ->prefixIcon('heroicon-o-calendar')
                ->minDate( $is_edit ? false : now() )
                ->after('date_from'),

            TextInput::make('season_basic_night_net')
                ->label(function(){
                    $label = "Basic night net (EUR)";
                    $tooltip = view('custom-label-help', [
                        'icon' => 'heroicon-o-question-mark-circle',
                        'tooltip' => 'Please note: The Basic Night Net Price (EUR) you provide here represents your clear earnings after taxes and any applicable fees. This is the amount you will receive directly for each booked night. Ensure the price accurately reflects your desired net income per night.',
                    ])->render();
                    return new HtmlString($label . $tooltip);
                })
                ->numeric()
                ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                ->live(onBlur: true)
                ->minValue(0)
                ->inputMode('decimal')
                ->required()
                ->validationAttribute('Basic night net')
                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                    if(!$get('../../data.basic_rate_commission_id')){
                        Notification::make()
                        ->title('Please choose "Tax Bracket" in "Basic Details" Tab')
                        ->warning()
                        ->send();

                        return;
                    }
                    
                    $basicRateCommissionId = $get('../../data.basic_rate_commission_id');
                    $basicNightNet = $state;
                    $basicRateCommissionData = BasicRateCommission::find($basicRateCommissionId);

                    $basicRateCommission = $basicRateCommissionData->commission_rate;
                    $taxes = $basicRateCommissionData->taxes;
                    $agentCommission = $basicRateCommissionData->agent_commission;
                    $service = $basicRateCommissionData->service;

                    $totalCommission = $basicRateCommission + $taxes + $agentCommission + $service;

                    $basicNightGross = $basicNightNet + ($basicNightNet * $totalCommission / 100);


                    $set('season_basic_night_gross', round($basicNightGross, 2));
                    $set('season_basic_night_net', round($state, 2));
                }),

            TextInput::make('season_basic_night_gross')
                ->label(function(){
                    $label = "Basic night gross (EUR)";
                    $tooltip = view('custom-label-help', [
                        'icon' => 'heroicon-o-question-mark-circle',
                        'tooltip' => 'Please note: The Basic Night Gross Price (EUR) is the total amount that the guest will pay per night for their stay. This amount includes any applicable taxes, fees, and other charges. Ensure this price aligns with the full cost to the guest.',
                    ])->render();
                    return new HtmlString($label . $tooltip);
                })
                ->numeric()
                ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                ->minValue(0)
                // ->disabled()
                ->validationAttribute('Basic night gross')
                ->required(),

            TextInput::make('season_weekend_night_net')
                ->label(function(){
                    $label = "Weekend night net (EUR)";
                    $tooltip = view('custom-label-help', [
                        'icon' => 'heroicon-o-question-mark-circle',
                        'tooltip' => 'Please note: The Weekend Night Net Price (EUR) represents your “clear earnings after taxes and fees” for bookings on weekend nights. This is the amount you will receive directly for each weekend night booked. Ensure the price reflects your desired net income for weekends.',
                    ])->render();
                    return new HtmlString($label . $tooltip);
                })
                ->numeric()
                ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                ->live(onBlur: true)
                ->minValue(0)
                ->inputMode('decimal')
                ->step(0.01)
                ->validationAttribute('Weekend Night Net')
                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                    if(!$get('../../data.basic_rate_commission_id')){
                        Notification::make()
                        ->title('Please choose "Tax Bracket" in "Basic Details" Tab')
                        ->warning()
                        ->send();

                        return;
                    }
                    
                    $basicRateCommissionId = $get('../../data.basic_rate_commission_id');
                    $WeekendNightNet = $state;
                    $basicRateCommissionData = BasicRateCommission::find($basicRateCommissionId);

                    $basicRateCommission = $basicRateCommissionData->commission_rate;
                    $taxes = $basicRateCommissionData->taxes;
                    $agentCommission = $basicRateCommissionData->agent_commission;
                    $service = $basicRateCommissionData->service;

                    $totalCommission = $basicRateCommission + $taxes + $agentCommission + $service;

                    $weekendNightGross = $WeekendNightNet + ($WeekendNightNet * $totalCommission / 100);
                    
                    $set('season_weekend_night_gross', round($weekendNightGross, 2));
                    $set('season_weekend_night_net', round($state, 2));
                }),

            TextInput::make('season_weekend_night_gross')
                ->label(function(){
                    $label = "Weekend night gross (EUR)";
                    $tooltip = view('custom-label-help', [
                        'icon' => 'heroicon-o-question-mark-circle',
                        'tooltip' => 'Please note: The Weekend Night Gross Price (EUR) is the total amount that the guest will pay for a weekend night stay. This amount includes any applicable taxes, fees, and other charges. Ensure this price reflects the full cost to the guest for weekend bookings.',
                    ])->render();
                    return new HtmlString($label . $tooltip);
                })
                ->numeric()
                // ->disabled()
                ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                ->minValue(0)
                ->validationAttribute('Weekend night gross')
                ->step(0.01),

            TextInput::make('min_stay_nights')
                ->label(function(){
                    $label = "Minimum stay (nights)";
                    $tooltip = view('custom-label-help', [
                        'icon' => 'heroicon-o-question-mark-circle',
                        'tooltip' => 'Please note: The Minimum Stay field defines the minimum number of nights a guest must book to reserve your property. This requirement will apply to all bookings and ensures longer stays. Enter the number of nights that aligns with your hosting preferences and rental strategy.',
                    ])->render();
                    return new HtmlString($label . $tooltip);
                })
                ->numeric()
                ->validationAttribute('Minimum stay (nights)')
                ->required(),

            TextInput::make('max_stay_nights')
                ->label(function(){
                    $label = "Maximum stay (nights)";
                    $tooltip = view('custom-label-help', [
                        'icon' => 'heroicon-o-question-mark-circle',
                        'tooltip' => 'Please note: The Maximum Stay field sets the maximum number of nights a guest can book your property in a single reservation. Use this field to define the longest allowable stay duration that fits your hosting preferences and availability.',
                    ])->render();
                    return new HtmlString($label . $tooltip);
                })
                ->numeric()
                ->validationAttribute('Maximum stay (nights)')
                ->required()
                ->gte('min_stay_nights'),

            CheckboxList::make('check_in_days')
                ->label(function(){
                    $label = "Check-in days";
                    $tooltip = view('custom-label-help', [
                        'icon' => 'heroicon-o-question-mark-circle',
                        'tooltip' => 'Please note: The Check-in Days field allows you to specify the days of the week when guests are permitted to check into your property. Select the days that align with your availability and hosting schedule. This setting ensures clarity for guests and helps you manage your calendar efficiently.',
                    ])->render();
                    return new HtmlString($label . $tooltip);
                })
                ->options([
                    'checkin_mon' => 'Monday',
                    'checkin_tue' => 'Tuesday',
                    'checkin_wed' => 'Wednesday',
                    'checkin_thu' => 'Thursday',
                    'checkin_fri' => 'Friday',
                    'checkin_sat' => 'Saturday',
                    'checkin_sun' => 'Sunday'
                ])
                ->columns(2)
                ->bulkToggleable()
                ->validationAttribute('Check-in days')
                ->required()
                ->selectAllAction(
                    fn (Action $action) => $action->label('Any Day'),
                ),

            CheckboxList::make('check_out_days')
                ->label(function(){
                    $label = "Check-out days";
                    $tooltip = view('custom-label-help', [
                        'icon' => 'heroicon-o-question-mark-circle',
                        'tooltip' => 'Please note: The Check-out Days field specifies the days of the week when guests are required to check out of your property. Select the days that best fit your hosting schedule and cleaning or preparation routines. This helps ensure smooth transitions between bookings and efficient property management.',
                    ])->render();
                    return new HtmlString($label . $tooltip);
                })
                ->options([
                    'checkout_mon' => 'Monday',
                    'checkout_tue' => 'Tuesday',
                    'checkout_wed' => 'Wednesday',
                    'checkout_thu' => 'Thursday',
                    'checkout_fri' => 'Friday',
                    'checkout_sat' => 'Saturday',
                    'checkout_sun' => 'Sunday'
                ])
                ->columns(2)
                ->bulkToggleable()
                ->validationAttribute('Check-out days')
                ->required()
                ->selectAllAction(
                    fn (Action $action) => $action->label('Any day'),
                ),

            Toggle::make('discount')
                ->label('Discount')
                ->default(false),

            ];
    }
}
