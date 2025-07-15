<?php

namespace App\Filament\Forms\Components;

use App\Filament\Resources\PropertyResource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\IconPosition;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Auth;
use App\Models\PropertyAvailability;
use Filament\Forms\Components\Actions\Action;


class PropertyAvailabilityFields
{
    private static $tabTitle = 'Availability';

    public static function create(): Tab
    {
        return Tabs\Tab::make(self::$tabTitle)
            ->icon(fn(Get $get) => PropertyResource::getTabIcon($get('tab_icon_availability')))
            ->iconPosition(IconPosition::After)
            ->visible(fn(Get $get): bool => is_array($get('deal_type')) && ! empty($get('deal_type')))

            ->schema([

                DatePicker::make('date_for_sale')
                    ->label('Available for sale')
                    ->prefixIcon('heroicon-o-calendar')
                    ->native(false)
                    ->required()
                    ->formatStateUsing(function ($state, $component) {
                        $record = $component->getRecord();

                        if (!$record || !$record->id) {
                            return null;
                        }

                        return PropertyAvailability::where('property_id', $record->id)
                            ->where('type', 'sale')
                            ->value('date_for_sale') ?? $state;
                    })
                    ->visible(
                        fn(Get $get): bool =>
                        is_array($get('deal_type')) &&
                            (in_array('deal_type_sale', $get('deal_type')))
                    )
                    ->live(true)
                    ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                        $set('date_for_sale', $state ?: null);
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    }),

                // Available Periods
                Repeater::make('available_periods')
                    ->label('Available for Rent')
                    ->schema([
                        DatePicker::make('date_from')
                            ->label('Date From')
                            ->prefixIcon('heroicon-o-calendar')
                            ->before('date_to')
                            ->native(false)
                            ->required()
                            ->live(true)
                            ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                                $set('date_from', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());

                                $basePath = preg_replace('/\.date_from$/', '', $component->getStatePath());
                                $livewire->validateOnly($basePath . '.date_to');
                            }),
                        DatePicker::make('date_to')
                            ->label('Date To')
                            ->prefixIcon('heroicon-o-calendar')
                            ->after('date_from')
                            ->native(false)
                            ->required()
                            ->live(true)
                            ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                                $set('date_to', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());

                                $basePath = preg_replace('/\.date_to$/', '', $component->getStatePath());
                                $livewire->validateOnly($basePath . '.date_from');
                            }),
                    ])
                    ->rule(function (callable $get, $record = null) {
                        return function (string $attribute, $value, $fail) use ($get, $record) {
                            $availablePeriods = $get('available_periods');
                            // $unavailablePeriods = $get('unavailable_periods');

                            // Check overlap with other available periods
                            $overlap = PropertyAvailability::checkPeriodOverlap($availablePeriods);
                            if ($overlap) {
                                $fail("The available period {$overlap['start']} - {$overlap['end']} overlaps with another available period {$overlap['overlapping_period']}");
                            }
                        };
                    })
                    ->formatStateUsing(function ($state, $component) {
                        $record = $component->getRecord();

                        if (!$record || !$record->id) {
                            return [];
                        }

                        $availablePeriods = PropertyAvailability::where('property_id', $record->id)
                            ->where('available', true)
                            ->where('type', 'rent')
                            ->get(['date_from', 'date_to'])
                            ->toArray();

                        return $state ?: $availablePeriods;
                    })
                    ->live(true)
                    ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    })
                    ->columns(2)
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->deleteAction(
                        fn(Action $action) => $action->requiresConfirmation(),
                    )
                    ->addActionLabel('Add Period')
                    ->visible(
                        fn(Get $get): bool =>
                        is_array($get('deal_type')) &&
                            (in_array('deal_type_rent', $get('deal_type')) || in_array('deal_type_monthly_rent', $get('deal_type')))
                    ),

                // Unavailable Periods
                Repeater::make('unavailable_periods')
                    ->label('Any specific periods when the property will not be available')
                    ->schema([
                        DatePicker::make('date_from')
                            ->label('Date From')
                            ->prefixIcon('heroicon-o-calendar')
                            ->before('date_to')
                            ->native(false)
                            ->required()
                            ->live(true)
                            ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                                $set('date_from', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());

                                $basePath = preg_replace('/\.date_from$/', '', $component->getStatePath());
                                $livewire->validateOnly($basePath . '.date_to');
                            }),
                        DatePicker::make('date_to')
                            ->label('Date To')
                            ->prefixIcon('heroicon-o-calendar')
                            ->after('date_from')
                            ->native(false)
                            ->required()
                            ->live(true)
                            ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                                $set('date_to', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());

                                $basePath = preg_replace('/\.date_to$/', '', $component->getStatePath());
                                $livewire->validateOnly($basePath . '.date_from');
                            }),
                    ])
                    ->rule(function (callable $get, $record = null) {
                        return function (string $attribute, $value, $fail) use ($get, $record) {
                            $unavailablePeriods = $get('unavailable_periods');

                            // Check overlap with other unavailable periods
                            $overlap = PropertyAvailability::checkPeriodOverlap($unavailablePeriods);
                            if ($overlap) {
                                $fail("The unavailable period {$overlap['start']} - {$overlap['end']} overlaps with another unavailable period {$overlap['overlapping_period']}");
                            }
                        };
                    })
                    ->live(true)
                    ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    })
                    ->formatStateUsing(function ($state, $component) {
                        $record = $component->getRecord();

                        if (!$record || !$record->id) {
                            return [];
                        }

                        $unavailablePeriods = PropertyAvailability::where('property_id', $record->id)
                            ->where('available', false)
                            ->where('type', 'rent')
                            ->get(['date_from', 'date_to'])
                            ->toArray();

                        return $state ?: $unavailablePeriods;
                    })
                    ->columns(2)
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->deleteAction(
                        fn(Action $action) => $action->requiresConfirmation(),
                    )
                    ->addActionLabel('Add Period')
                    ->visible(
                        fn(Get $get): bool =>
                        is_array($get('deal_type')) &&
                            (in_array('deal_type_rent', $get('deal_type')) || in_array('deal_type_monthly_rent', $get('deal_type')))
                    ),
            ]);
    }
}
