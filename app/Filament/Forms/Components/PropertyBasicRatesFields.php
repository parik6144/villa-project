<?php

namespace App\Filament\Forms\Components;

use App\Filament\Resources\PropertyResource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Support\Enums\IconPosition;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Auth;
use App\Models\PropertyAvailability;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use App\Models\BasicRateCommission;
use Filament\Forms\Components\Placeholder;

class PropertyBasicRatesFields
{
    private static $tabTitle = 'Basic rates';

    public static function create(): Tab
    {
        return Tabs\Tab::make(self::$tabTitle)
            ->icon(fn(Get $get) => PropertyResource::getTabIcon($get('tab_icon_basic_rates')))
            ->iconPosition(IconPosition::After)
            ->visible(
                fn(Get $get): bool =>
                is_array($get('deal_type')) &&
                    (in_array('deal_type_rent', $get('deal_type')) || in_array('deal_type_monthly_rent', $get('deal_type')))
            )
            ->schema([
                Section::make('Nightly rates (Payout amount)')
                    ->description('Set your nightly rates by entering the amount you wish to receive in payout.')
                    ->columns(4)
                    ->schema([
                        TextInput::make('basic_night_net')
                            ->label(function () {
                                $label = "Basic night net (EUR)";
                                $tooltip = view('custom-label-help', [
                                    'icon' => 'heroicon-o-question-mark-circle',
                                    'tooltip' => 'Please note: The Basic Night Net Price (EUR) you provide here represents your clear earnings after taxes and any applicable fees. This is the amount you will receive directly for each booked night. Ensure the price accurately reflects your desired net income per night.',
                                    'position' => 'bottom'
                                ])->render();
                                return new HtmlString($label . $tooltip);
                            })
                            ->numeric()
                            ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                            ->live(onBlur: true)
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->step(0.01)
                            ->columnSpan(2)
                            ->required()
                            ->validationAttribute('Basic night net')
                            ->afterStateUpdated(function ($state, Get $get, Set $set, $livewire, $component) {

                                if (!$state) {
                                    $set('basic_night_net', null);
                                    $set('basic_night_gross', null);
                                    return;
                                }

                                if (!$get('basic_rate_commission_id')) {
                                    Notification::make()
                                        ->title('Please choose "Tax Bracket" in "Basic Details" Tab')
                                        ->warning()
                                        ->send();

                                    return;
                                }

                                $basicRateCommissionId = $get('basic_rate_commission_id');
                                $basicNightNet = $get('basic_night_net');
                                $basicRateCommissionData = BasicRateCommission::find($basicRateCommissionId);

                                $basicRateCommission = $basicRateCommissionData->commission_rate;
                                $taxes = $basicRateCommissionData->taxes;
                                $agentCommission = $basicRateCommissionData->agent_commission;
                                $service = $basicRateCommissionData->service;

                                $totalCommission = $basicRateCommission + $taxes + $agentCommission + $service;

                                $basicNightGross = $basicNightNet + ($basicNightNet * $totalCommission / 100);
                                $set('basic_night_gross', round($basicNightGross, 2));
                                $set('basic_night_net', round($get('basic_night_net'), 2));

                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                            ->extraInputAttributes([
                                'data-required' => 'true',
                                'name' => 'applicable tax bracket',
                                'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            ]),

                        TextInput::make('basic_night_gross')
                            ->label(function () {
                                $label = "Basic night gross (EUR)";
                                $tooltip = view('custom-label-help', [
                                    'icon' => 'heroicon-o-question-mark-circle',
                                    'tooltip' => 'Please note: The Basic Night Gross Price (EUR) is the total amount that the guest will pay per night for their stay. This amount includes any applicable taxes, fees, and other charges. Ensure this price aligns with the full cost to the guest.',
                                    'position' => 'bottom'
                                ])->render();
                                return new HtmlString($label . $tooltip);
                            })
                            ->numeric()
                            ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->step(0.01)
                            ->validationAttribute('Basic night gross')
                            ->required()
                            ->disabled(function () {
                                return !Auth::check() || !Auth::user()->hasRole('admin');
                            })
                            ->columnSpan(2)
                            // ->live(true)
                            ->afterStateUpdated(function ($livewire, $component, $state, Set $set) {
                                $set('basic_night_gross', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                             ->extraInputAttributes([
                                'data-required' => 'true',
                                'name' => 'Basic night gross',
                                'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            ]),


                        TextInput::make('weekend_night_net')
                            ->label(function () {
                                $label = "Weekend night net (EUR)";
                                $tooltip = view('custom-label-help', [
                                    'icon' => 'heroicon-o-question-mark-circle',
                                    'tooltip' => 'Please note: The Weekend Night Net Price (EUR) represents your “clear earnings after taxes and fees” for bookings on weekend nights. This is the amount you will receive directly for each weekend night booked. Ensure the price reflects your desired net income for weekends.',
                                ])->render();
                                return new HtmlString($label . $tooltip);
                            })
                            ->numeric()
                            ->required()
                            ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                            ->live(onBlur: true)
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->columnSpan(2)
                            ->validationAttribute('Weekend Night Net')
                            ->afterStateUpdated(function ($state, Get $get, Set $set, $livewire, $component) {
                                if (!$state) {
                                    $set('weekend_night_net', null);
                                    $set('weekend_night_gross', null);
                                    return;
                                }

                                if (!$get('basic_rate_commission_id')) {
                                    Notification::make()
                                        ->title('Please choose "Revenue level" in "Basic Details" Tab')
                                        ->warning()
                                        ->send();

                                    return;
                                }

                                $basicRateCommissionId = $get('basic_rate_commission_id');
                                $WeekendNightNet = $get('weekend_night_net');
                                $basicRateCommissionData = BasicRateCommission::find($basicRateCommissionId);

                                $basicRateCommission = $basicRateCommissionData->commission_rate;
                                $taxes = $basicRateCommissionData->taxes;
                                $agentCommission = $basicRateCommissionData->agent_commission;
                                $service = $basicRateCommissionData->service;

                                $totalCommission = $basicRateCommission + $taxes + $agentCommission + $service;

                                $weekendNightGross = $WeekendNightNet + ($WeekendNightNet * $totalCommission / 100);


                                $set('weekend_night_gross', round($weekendNightGross, 2));
                                $set('weekend_night_net', round($get('weekend_night_net'), 2));

                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                            ->extraInputAttributes([
                                'data-required' => 'true',
                                'name' => 'Weekend Night Net',
                                'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            ]),
                            

                        TextInput::make('weekend_night_gross')
                            ->label(function () {
                                $label = "Weekend night gross (EUR)";
                                $tooltip = view('custom-label-help', [
                                    'icon' => 'heroicon-o-question-mark-circle',
                                    'tooltip' => 'Please note: The Weekend Night Gross Price (EUR) is the total amount that the guest will pay for a weekend night stay. This amount includes any applicable taxes, fees, and other charges. Ensure this price reflects the full cost to the guest for weekend bookings.',
                                ])->render();
                                return new HtmlString($label . $tooltip);
                            })
                            ->numeric()
                            ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->step(0.01)
                            ->validationAttribute('Weekend night gross')
                            ->disabled(function () {
                                return !Auth::check() || !Auth::user()->hasRole('admin');
                            })
                            ->columnSpan(2)
                            // ->live(true)
                            ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                                $set('weekend_night_gross', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            }),
                    ])
                    ->visible(
                        fn(Get $get): bool =>
                        is_array($get('deal_type')) && (in_array('deal_type_rent', $get('deal_type')))
                    ),

                Section::make(' Monthly rates (Payout amount)')
                    ->description('Set your monthly rates by entering the amount you wish to receive in payout.')
                    ->columns(4)
                    ->schema([
                        TextInput::make('monthly_rate')
                            ->label('Monthly rate (EUR)')
                            ->numeric()
                            ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->disabled(function () {
                                return !Auth::check() || !Auth::user()->hasRole('admin');
                            })
                            ->columnSpan(2)
                            ->live(true)
                            ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                                $set('monthly_rate', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            }),
                        TextInput::make('monthly_rate_sqm')
                            ->label('Monthly rate per Sqm (EUR)')
                            ->numeric()
                            ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->disabled(function () {
                                return !Auth::check() || !Auth::user()->hasRole('admin');
                            })
                            ->columnSpan(2)
                            ->live(true)
                            ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                                $set('monthly_rate_sqm', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            }),
                    ])
                    ->visible(
                        fn(Get $get): bool =>
                        is_array($get('deal_type')) && (in_array('deal_type_monthly_rent', $get('deal_type')))
                    ),

                Section::make('Basic rules')
                    ->columns(4)
                    ->schema([
                        TextInput::make('max_guests')
                            ->label(function () {
                                $label = "Maximum number of Guests";
                                $tooltip = view('custom-label-help', [
                                    'icon' => 'heroicon-o-question-mark-circle',
                                    'tooltip' => 'Please note: The Maximum Number of Guests field specifies the maximum capacity of your property. This includes all individuals, regardless of age, who will stay at the property. Ensure the number reflects the actual capacity of your space to provide a comfortable and safe experience for your guests.',
                                ])->render();
                                return new HtmlString($label . $tooltip);
                            })
                            ->numeric()
                            ->extraInputAttributes(['min' => '0'])
                            ->minValue(0)
                            ->validationAttribute('Maximum number of Guests')
                            ->required()
                            ->columnSpanFull()
                            ->live(true)
                            ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                                $set('max_guests', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            }),

                        TextInput::make('min_stay_nights')
                            ->label(function () {
                                $label = "Minimum stay (nights)";
                                $tooltip = view('custom-label-help', [
                                    'icon' => 'heroicon-o-question-mark-circle',
                                    'tooltip' => 'Please note: The Minimum Stay field defines the minimum number of nights a guest must book to reserve your property. This requirement will apply to all bookings and ensures longer stays. Enter the number of nights that aligns with your hosting preferences and rental strategy.',
                                ])->render();
                                return new HtmlString($label . $tooltip);
                            })
                            ->numeric()
                            ->extraInputAttributes(['min' => '1'])
                            ->minValue(1)
                            ->validationAttribute('Minimum stay (nights)')
                            ->required()
                            ->columnSpan(2)
                            ->visible(
                                fn(Get $get): bool =>
                                is_array($get('deal_type')) && (in_array('deal_type_rent', $get('deal_type')))
                            )
                            ->live(true)
                            ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                                $set('min_stay_nights', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            }),

                        TextInput::make('max_stay_nights')
                            ->label(function () {
                                $label = "Maximum stay (nights)";
                                $tooltip = view('custom-label-help', [
                                    'icon' => 'heroicon-o-question-mark-circle',
                                    'tooltip' => 'Please note: The Maximum Stay field sets the maximum number of nights a guest can book your property in a single reservation. Use this field to define the longest allowable stay duration that fits your hosting preferences and availability.',
                                ])->render();
                                return new HtmlString($label . $tooltip);
                            })
                            ->numeric()
                            ->extraInputAttributes(['min' => '1'])
                            ->minValue(1)
                            ->validationAttribute('Maximum stay (nights)')
                            ->required()
                            ->gte('min_stay_nights')
                            ->columnSpan(2)
                            ->visible(
                                fn(Get $get): bool =>
                                is_array($get('deal_type')) && (in_array('deal_type_rent', $get('deal_type')))
                            )
                            ->live(true)
                            ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                                $set('max_stay_nights', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            }),

                        CheckboxList::make('check_in_days')
                            ->label(function () {
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
                            ->columnSpan(2)
                            ->bulkToggleable()
                            ->validationAttribute('Check-in days')
                            ->required()
                            ->formatStateUsing(function ($state, $component) {
                                $record = $component->getRecord();
                                $daysOfWeek = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

                                if (!$record || !$record->id) {
                                    return [];
                                }

                                if ($record->checkin_any) {
                                    return array_map(fn($day) => 'checkin_' . $day, $daysOfWeek);
                                }

                                $values = [];

                                foreach ($daysOfWeek as $day) {
                                    $field = 'checkin_' . $day;
                                    if ($record->{$field}) {
                                        $values[] = $field;
                                    }
                                }

                                return $values;
                            })
                            ->selectAllAction(
                                fn(Action $action) => $action->label('Any day'),
                            )
                            ->visible(
                                fn(Get $get): bool =>
                                is_array($get('deal_type')) && (in_array('deal_type_rent', $get('deal_type')))
                            )->live(true)
                            ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            }),

                        CheckboxList::make('check_out_days')
                            ->label(function () {
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
                            ->columnSpan(2)
                            ->bulkToggleable()
                            ->validationAttribute('Check-out days')
                            ->required()
                            ->formatStateUsing(function ($state, $component) {
                                $record = $component->getRecord();
                                $daysOfWeek = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

                                if (!$record || !$record->id) {
                                    return [];
                                }

                                if ($record->checkout_any) {
                                    return array_map(fn($day) => 'checkout_' . $day, $daysOfWeek);
                                }

                                $values = [];

                                foreach ($daysOfWeek as $day) {
                                    $field = 'checkout_' . $day;
                                    if ($record->{$field}) {
                                        $values[] = $field;
                                    }
                                }

                                return $values;
                            })
                            ->selectAllAction(
                                fn(Action $action) => $action->label('Any day'),
                            )
                            ->visible(
                                fn(Get $get): bool =>
                                is_array($get('deal_type')) && (in_array('deal_type_rent', $get('deal_type')))
                            )
                            ->live(true)
                            ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            }),

                    ]),
            ]);
    }
}
