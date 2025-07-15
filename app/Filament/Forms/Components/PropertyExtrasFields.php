<?php

namespace App\Filament\Forms\Components;

use App\Filament\Resources\PropertyResource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Checkbox;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Auth;
use App\Models\PropertyAvailability;
use Filament\Forms\Components\Actions\Action;
use App\Models\Tax;
use App\Models\PropertyExtras;
use Filament\Facades\Filament;
use App\Models\BasicRateCommission;

class PropertyExtrasFields
{
    private static $tabTitle = 'Extras';

    public static function create(): Tab
    {
        return Tabs\Tab::make(self::$tabTitle)
            ->icon(fn(Get $get) => PropertyResource::getTabIcon($get('tab_icon_extras')))
            ->iconPosition(IconPosition::After)
            ->visible(
                fn(Get $get): bool =>
                is_array($get('deal_type')) &&
                    (in_array('deal_type_rent', $get('deal_type')) || in_array('deal_type_monthly_rent', $get('deal_type')))
            )
            ->schema([
                Section::make("Cleaning")
                   ->schema([
                        Checkbox::make('is_cleaning')
                            ->label('Cleaning')
                            // ->live(true)
                            ->afterStateUpdated(function ($livewire, $component) {
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                    ])
                    ,
                Section::make("Taxes")
                    ->schema([
                        TableRepeater::make('taxes')
                            // ->relationship('taxes')
                            ->formatStateUsing(function ($state, $component) {
                                $record = $component->getRecord();

                                if (!$record || !$record->id) {
                                    return [];
                                }

                                $taxes = Tax::where('property_id', $record->id)->get();

                                if ($taxes->isNotEmpty()) {
                                    return $state ?: $taxes->toArray();
                                }

                                return $state ?: [];
                            })
                            ->reorderable(false)
                            ->headers([
                                Header::make('Type')->label('Tax type / name'),
                                Header::make('Fee')->label('Fee basis'),
                                Header::make('Amount')->label('Amount'),
                            ])
                            ->deletable(fn() => Filament::auth()->user()->hasRole('admin'))
                            ->schema([
                                Placeholder::make('type_placeholder')
                                    ->label(false)
                                    ->content(fn(Get $get) => $get('tax_type')),
                                Placeholder::make('fee_placeholder')
                                    ->label(false)
                                    ->content(fn(Get $get) => $get('fee_basis')),
                                Placeholder::make('amount_placeholder')
                                    ->label(false)
                                    ->content(
                                        fn(Get $get) =>
                                        $get('amount') . ' ' . ($get('is_percent') ? '%' : '€')
                                    )
                            ])
                            ->extraItemActions([
                                Filament::auth()->user()->hasRole('admin') ?

                                    Action::make('Edit')
                                    ->icon('heroicon-s-pencil')
                                    ->modalHeading('Edit Taxes')
                                    ->fillForm(function (array $arguments, Repeater $component): array {
                                        $allItems = $component->getState();
                                        $currentKey = $arguments['item'];
                                        return $allItems[$currentKey] ?? [];
                                    })
                                    ->form([
                                        Select::make('tax_type')
                                            ->label('Tax Type')
                                            ->options(function ($state, $component, Get $get) {
                                                $availableTaxTypes = Tax::getTaxTypes();
                                                $data = $get('../../data.taxes');
                                                $stateExtras = collect($data)->pluck('tax_type')->toArray();

                                                $filteredExtras = collect($stateExtras)->filter(fn($extra) => $extra !== $state)->toArray();

                                                return collect($availableTaxTypes)
                                                    ->except($filteredExtras)
                                                    ->toArray();
                                            })
                                            ->required(),

                                        Select::make('fee_basis')
                                            ->label('Fee Basis')
                                            ->options(Tax::getFeeBasisOptions())
                                            // ->selectablePlaceholder(false)
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $set, $state) {
                                                $label = str_contains($state, '%') ? '%' : 'EUR';
                                                $set('amount_label', $label);
                                                $set('is_percent', str_contains($state, '%') ? true : false);
                                            })
                                            ->required(),

                                        Hidden::make('is_percent'),

                                        TextInput::make('amount')
                                            ->label(fn($get) => $get('amount_label') ?? 'Amount')
                                            ->numeric()
                                            ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                                            ->inputMode('decimal')
                                            ->minValue(0)
                                            ->live(onBlur: true)
                                            ->default(0)
                                            ->required()
                                            ->label(
                                                fn($get) =>
                                                $get('fee_basis')
                                                    ? (str_contains($get('fee_basis'), '%') ? 'Amount %' : 'Amount EUR')
                                                    : 'Amount'
                                            ),
                                    ])
                                    ->action(function ($livewire, array $arguments, array $data, $component, Set $set, Get $get): void {
                                        $mainState = $component->getState();
                                        $key = $arguments['item'];
                                        $mainState[$key] = $data;

                                        $component->state($mainState);

                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                    })
                                    : null
                            ])
                            ->addActionLabel('Add tax')
                            ->addAction(function ($action) {
                                return $action->form([
                                    Select::make('tax_type')
                                        ->label('Tax Type')
                                        ->options(function ($state, $component, Get $get) {
                                            $availableTaxTypes = Tax::getTaxTypes();
                                            $data = $get('../../data.taxes');
                                            $stateExtras = collect($data)->pluck('tax_type')->toArray();

                                            return collect($availableTaxTypes)
                                                ->except($stateExtras)
                                                ->toArray();
                                        })
                                        ->required(),

                                    Select::make('fee_basis')
                                        ->label('Fee Basis')
                                        ->options(Tax::getFeeBasisOptions())
                                        ->reactive()
                                        // ->selectablePlaceholder(false)
                                        ->afterStateUpdated(function (callable $set, $state) {
                                            $label = str_contains($state, '%') ? '%' : 'EUR';
                                            $set('amount_label', $label);
                                            $set('is_percent', str_contains($state, '%') ? true : false);
                                        })
                                        ->required(),

                                    Hidden::make('is_percent'),

                                    TextInput::make('amount')
                                        ->label(
                                            fn($get) =>
                                            $get('fee_basis')
                                                ? (str_contains($get('fee_basis'), '%') ? 'Amount %' : 'Amount EUR')
                                                : 'Amount'
                                        )
                                        ->numeric()
                                        ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                                        ->inputMode('decimal')
                                        ->minValue(0)
                                        ->default(0)
                                        ->live(onBlur: true)
                                        ->required()
                                        ->afterStateUpdated(function (Get $get, Set $set) {
                                            if (!$get('amount')) {
                                                return;
                                            }
                                            $set('amount', round($get('amount'), 2));
                                        }),
                                ])
                                    ->action(function ($livewire, $data, Set $set, Get $get) {
                                        $currentState = $get('taxes') ?? [];
                                        $result = array_merge($currentState, [$data]);
                                        $set('taxes', $result);

                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                    });
                            })
                            ->deleteAction(
                                fn(Action $action) => $action->requiresConfirmation(),
                            )
                    ])
                    ->hidden(
                        fn(Get $get) => (Filament::auth()->user()->hasRole('admin') ? false : count($get('taxes')) === 0)
                    ),
                Section::make("Optional extras and services")
                    ->schema([

                        TableRepeater::make('extras')
                            // ->relationship('extras')
                            ->formatStateUsing(function ($state, $component) {
                                $record = $component->getRecord();

                                if (!$record || !$record->id) {
                                    return [];
                                }

                                $taxes = PropertyExtras::where('property_id', $record->id)->get();

                                if ($taxes->isNotEmpty()) {
                                    return $state ?: $taxes->toArray();
                                }

                                return $state ?: [];
                            })
                            ->headers([
                                Header::make('extra_service')->label('Extra / Service')->width('200px'),
                                Header::make('fee_basis')->label('Fee Basis')->width('150px'),
                                Header::make('amount')->label('Amount (EUR)')->width('100px'),
                                Header::make('earliest_order')->label('Earliest Order')->width('150px'),
                                Header::make('latest_order')->label('Latest Order')->width('150px'),
                            ])
                            ->schema([
                                Placeholder::make('extra_service_placeholder')
                                    ->content(fn(Get $get) => ucfirst(str_replace('-', ' ', $get('extra_service'))))
                                    ->label(false),

                                Placeholder::make('fee_basis_placeholder')
                                    ->content(fn(Get $get) => ucfirst(str_replace('-', ' ', $get('fee_basis'))))
                                    ->label(false),

                                Placeholder::make('amount_placeholder')
                                    ->content(fn(Get $get) => ucfirst(str_replace('-', ' ', $get('amount'))))
                                    ->label(false),

                                Placeholder::make('earliest_order_placeholder')
                                    ->content(fn(Get $get) => ucfirst(str_replace('-', ' ', $get('earliest_order'))))
                                    ->label(false),

                                Placeholder::make('latest_order_placeholder')
                                    ->content(fn(Get $get) => ucfirst(str_replace('-', ' ', $get('latest_order'))))
                                    ->label(false),

                            ])
                            ->extraItemActions([
                                Action::make('Edit')
                                    ->icon('heroicon-s-pencil')
                                    ->modalHeading('Edit Extra / Service')
                                    ->fillForm(function (array $arguments, Repeater $component): array {
                                        $allItems = $component->getState();
                                        $currentKey = $arguments['item'];
                                        return $allItems[$currentKey] ?? [];
                                    })
                                    ->form([
                                        Grid::make(2)
                                            ->schema(self::extraServiceFields())
                                    ])
                                    ->action(function ($livewire, array $arguments, array $data, $component, Set $set, Get $get): void {
                                        $mainState = $component->getState();
                                        $key = $arguments['item'];
                                        $mainState[$key] = $data;

                                        $component->state($mainState);

                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                    })
                            ])
                            ->addActionLabel('Add an extra')
                            ->addAction(function ($action) {
                                return $action
                                    ->form([
                                        Grid::make(2)
                                            ->schema(self::extraServiceFields())
                                    ])
                                    ->action(function ($livewire, $data, Set $set, Get $get) {
                                        $currentState = $get('extras') ?? [];
                                        $result = array_merge($currentState, [$data]);
                                        $set('extras', $result);

                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                    });
                            })
                            ->deleteAction(
                                fn(Action $action) => $action->requiresConfirmation(),
                            )
                            ->extraActions([
                                Action::make('Custom Extra')
                                    ->label('Add custom Extra')
                                    ->form([
                                        Grid::make(2)
                                            ->schema(self::extraServiceFields(true))
                                    ])
                                    ->action(function ($livewire, $data, Set $set, Get $get) {
                                        $currentState = $get('extras') ?? [];
                                        $data['is_custom'] = true;
                                        $result = array_merge($currentState, [$data]);
                                        $set('extras', $result);

                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                    })
                            ])
                            ->columnSpan('full')
                    ])
            ]);
    }

    private static function extraServiceFields($is_custom = false)
    {
        $propertyList = Auth::user()->hasRole('admin')
            ? \App\Models\Property::pluck('title', 'id')->toArray()
            : Auth::user()->properties()->pluck('title', 'id')->toArray();

        $propertyList = collect($propertyList)
            ->map(fn($title) => $title ?? 'No title')
            ->toArray();

        return [
            Select::make('extra_service')
                ->label('Extra / Service')
                ->reactive()
                ->options(function ($state, $component, Get $get) {
                    $data = $get('../../data.extras');

                    $stateExtras = collect($data)->pluck('extra_service')->toArray();

                    $filteredExtras = array_filter($stateExtras, fn($extra) => $extra !== $state);

                    // exlude already exisiting
                    return collect([
                        'additional-cleaning' => 'Additional Cleaning',
                        'airport-transfer-car' => 'Airport transfer - Car',
                        'airport-transfer-van' => 'Airport transfer - Van',
                        'baby-cot' => 'Baby cot',
                        'bed-linen' => 'Bed linen',
                        'bike-rental' => 'Bike rental',
                        'boat-berth' => 'Boat berth',
                        'breakfast' => 'Breakfast',
                        'car-rental' => 'Car rental',
                        'early-check-in' => 'Early check-in',
                        'electricity' => 'Electricity',
                        'heated-pool' => 'Heated Pool',
                        'high-chair' => 'High chair',
                        'jacuzzi' => 'Jacuzzi',
                        'kitchen-linen' => 'Kitchen linen',
                        'late-check-out' => 'Late check-out',
                        'laundry-service' => 'Laundry service',
                        'linen-package' => 'Linen package (bed linen + towel)',
                        'meditation-class' => 'Meditation class',
                        'muay-thai-gym' => 'Muay Thai Gym',
                        'parking' => 'Parking',
                        'sunbed' => 'Sunbed',
                        'tennis-class' => 'Tennis class',
                        'tennis-court' => 'Tennis court',
                        'towel' => 'Towel',
                        'transfer' => 'Transfer',
                        'umbrella' => 'Umbrella',
                        'waste-sorting' => 'Waste sorting',
                        'welcome-package' => 'Welcome package',
                        'yoga-class' => 'Yoga class',
                    ])
                        ->except($filteredExtras)->toArray();
                })
                ->required()
                ->visible(fn(Get $get) => !$get('is_custom') && !$is_custom),

            TextInput::make('extra_service')
                ->label('Extra / Service')
                ->required()
                ->visible(fn(Get $get) => $get('is_custom') || $is_custom),

            Select::make('fee_basis')
                ->label('Fee Basis')
                ->options([
                    'free' => 'Free',
                    'per-unit-day' => 'Per unit / day',
                    'per-day' => 'Per day',
                    'per-person-week' => 'Per person / week',
                    'per-unit' => 'Per unit',
                    'per-person-day' => 'Per person / day',
                    'per-week' => 'Per week',
                    'per-person' => 'Per person',
                    'per-unit-week' => 'Per unit / week',
                ])
                // ->selectablePlaceholder(false)
                ->afterStateUpdated(function (?string $state, $set) {
                    $set('fee_basis', $state ?: null);
                    if ($state === 'free') {
                        $set('amount', 0);
                    }
                })
                ->reactive()
                ->required(),

            TextInput::make('amount')
                ->label('Amount (EUR)')
                ->numeric()
                ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                ->live(onBlur: true)
                ->inputMode('decimal')
                ->minValue(0)
                ->default(0)
                ->afterStateUpdated(function (Get $get, Set $set) {})
                ->hidden(fn(Get $get) => $get('fee_basis') === null || $get('fee_basis') === 'free'),

            Placeholder::make('amount_calc')
                ->label('Guest pays (EUR)')
                ->content(
                    function (Get $get) {
                        if ($get('amount') === 0) {
                            return;
                        }

                        $basicRateCommissionId = $get('../../data.basic_rate_commission_id');
                        $basicRateCommission = BasicRateCommission::find($basicRateCommissionId)->commission_rate;
                        $amount_calc = $get('amount') + ($get('amount') * $basicRateCommission / 100);
                        return round($amount_calc, 2);
                    }
                )
                ->hidden(fn(Get $get) => $get('fee_basis') === null || $get('fee_basis') === 'free' || ($get('is_custom') || $is_custom)),

            Select::make('earliest_order')
                ->label('Earliest Order')
                ->options([
                    'at-the-time-of-booking' => 'At the time of booking',
                    '2-days-before-check-in' => '2 days before check-in',
                    '1-day-before-check-in' => '1 day before check-in',
                    '2-days-before-check-out' => '2 days before check-out',
                    '1-day-before-check-out' => '1 day before check-out',
                ])
                ->columnSpan(1)
                ->columnStart(1)
                // ->selectablePlaceholder(false)
                ->afterStateUpdated(function (?string $state, $set) {
                    $set('earliest_order', $state ?: null);
                })
                ->required(),

            Select::make('latest_order')
                ->label('Latest Order')
                ->options([
                    '2-days-before-check-in' => '2 days before check-in',
                    '1-day-before-check-in' => '1 day before check-in',
                    '2-days-before-check-out' => '2 days before check-out',
                    '1-day-before-check-out' => '1 day before check-out',
                    'no-restriction' => 'No restriction',
                ])
                ->columnSpan(1)
                ->columnStart(2)
                // ->selectablePlaceholder(false)
                ->afterStateUpdated(function (?string $state, $set) {
                    $set('latest_order', $state ?: null);
                })
                ->required(),
            TextArea::make('additional_info')
                ->label('Additional Information')
                ->maxLength(500)
                ->columnSpanFull()
                ->live(true)
                ->helperText(fn(Get $get) => sprintf('%d characters allowed', 500 - strlen($get('additional_info') ?? ''))),


            Hidden::make('is_custom')
                ->formatStateUsing(function ($state, $component) use ($is_custom) {
                    return fn(Get $get) => $get('is_custom') || $is_custom;
                }),

            Select::make('copy_to_properties')
                ->label('Copy this Extra to other listings')
                ->columnSpanFull()
                ->multiple()
                ->hintAction(
                    fn(Select $component) => Action::make('select all')
                        ->action(function ($component) use ($propertyList) {
                            $record = $component->getRecord();
                            $currentId = $record->id ?? null;

                            $ids = collect($propertyList)
                                ->keys()
                                ->filter(fn($id) => $id != $currentId)
                                ->values()
                                ->toArray();

                            return $component->state($ids);
                        })
                )
                ->options(function ($state, $component) use ($propertyList) {
                    $record = $component->getRecord();
                    $currentId = $record->id ?? null;
                    //exclude current record
                    return collect($propertyList)->filter(fn($value, $key) => $key != $currentId)->toArray();
                })
        ];
    }
}
