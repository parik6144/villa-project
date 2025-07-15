<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Fieldset;
use Filament\Notifications\Notification;
use App\Models\BasicRateCommission;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;
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
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\IconPosition;

use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use App\Models\BookingRule;

class PropertyBookingRulesFields
{
    private static $tabTitle = 'Booking rules';

    public static function create(): Tab
    {
        return Tabs\Tab::make(self::$tabTitle)
            ->icon(fn(Get $get) => PropertyResource::getTabIcon($get('tab_icon_booking_rules')))
            ->iconPosition(IconPosition::After)
            ->visible(
                fn(Get $get): bool =>
                is_array($get('deal_type')) && in_array('deal_type_rent', $get('deal_type'))
            )
            ->schema([

                Section::make("Advance notice")
                    ->schema([
                        Select::make('advance_booking_notice')
                            ->label('How many days before check-in do you allow bookings?')
                            ->validationAttribute('Advance booking notice')
                            ->options([
                                'no_notice' => 'No advance notice required',
                                '1_day' => 'At least 1 day notice',
                                '2_days' => 'At least 2 days\' notice',
                                '3_days' => 'At least 3 days\' notice',
                                '5_days' => 'At least 5 days\' notice',
                                '7_days' => 'At least 7 days\' notice',
                            ])
                            ->live(true)
                            ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                $set('advance_booking_notice', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                            ->required()
                    ]),
                Section::make('Choose your guest booking options')
                    ->description('Create different booking options for guests by adjusting your rates for different cancellation policies.
                    We’ll use the most suitable options for each channel to maximise your conversion and booking revenue.
                    On channels that support it, we’ll send multiple options to increase your listing appeal.')
                    ->schema([
                        Fieldset::make('preferred_policy_set')
                            ->label(new HtmlString('1. Preferred policy <sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>'))
                            ->schema([
                                Select::make('cancellation_policy')
                                    ->label("Your previously defined rates will be used when guests book using this cancellation policy.")
                                    ->validationAttribute('cancellation policy')
                                    ->reactive()
                                    ->required()
                                    ->options(self::getPreferredPolicyOptions())
                                    ->afterStateUpdated(function (?string $state, Set $set, $livewire, $component) {
                                        $set('cancellation_policy', $state ?: null);
                                        $set('additional_policy', null);
                                        $set('rates_increase', null);
                                        $set('rates_decrease', null);
                                        $set('additional_policy_2', null);
                                        $set('rates_increase_2', null);
                                        $set('rates_decrease_2', null);

                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                        $livewire->validateOnly($component->getStatePath());
                                    }),
                                AlertBox::make()
                                    ->helperText('Basic and Seasonal rates will be used when guests book with this cancellation policy.')
                                    ->success()
                                    ->resolveIconUsing(name: 'heroicon-o-information-circle')
                                    ->extraAttributes(['class' => 'custom-background-helper-text'])
                            ])
                            ->columns(1),

                        Fieldset::make('additional_policy_set')
                            ->label(new HtmlString('2. Additional policy <sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>'))
                            ->schema([
                                Select::make('additional_policy')
                                    ->label('Adjust your rates for guests that book using this cancellation policy.')
                                    ->validationAttribute('Additional Policy')
                                    ->options(fn(Get $get) => self::getAdditionalPolicyOptions($get('cancellation_policy')))
                                    ->reactive()
                                    ->required()
                                    ->afterStateUpdated(function (?string $state, Set $set, $livewire, $component) {
                                        $set('additional_policy', $state ?: null);
                                        $set('additional_policy_2', null);
                                        $set('rates_increase', null);
                                        $set('rates_decrease', null);
                                        $set('rates_increase_2', null);
                                        $set('rates_decrease_2', null);

                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                        $livewire->validateOnly($component->getStatePath());
                                    }),
                                TextInput::make('rates_increase')
                                    ->label(function () {
                                        $label = "Rates increase";
                                        $tooltip = view('custom-label-help', [
                                            'icon' => 'heroicon-o-question-mark-circle',
                                            'tooltip' => 'Increase your rates for more flexible policies, and decrease them for stricter ones. We recommend a maximum change of 30%. You can select decimal values or input 0 to use your usual rates.',
                                        ])->render();
                                        return new HtmlString($label . $tooltip);
                                    })
                                    ->prefix('%')
                                    ->validationAttribute('Rates increase')
                                    ->reactive()
                                    ->required()
                                    ->numeric()
                                    ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                                    ->inputMode('decimal')
                                    ->minValue(0)
                                    ->placeholder('Enter percentage (e.g., 10)')
                                    ->live(true)
                                    ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                        $set('rates_increase', $state ?: null);
                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->visible(fn(Get $get) => $get('additional_policy') !== null && $get('additional_policy') !== 'non_refundable'),
                                AlertBox::make()
                                    ->helperText(function (Get $get) {
                                        return sprintf(
                                            'Basic and Seasonal rates will increase by %s%% when guests book with this cancellation policy.',
                                            $get('rates_increase')
                                        );
                                    })
                                    ->success()
                                    ->resolveIconUsing(name: 'heroicon-o-information-circle')
                                    ->visible(fn(Get $get) => $get('rates_increase')),

                                TextInput::make('rates_decrease')
                                    ->label(function () {
                                        $label = "Rates decrease";
                                        $tooltip = view('custom-label-help', [
                                            'icon' => 'heroicon-o-question-mark-circle',
                                            'tooltip' => 'Increase your rates for more flexible policies, and decrease them for stricter ones. We recommend a maximum change of 30%. You can select decimal values or input 0 to use your usual rates.',
                                        ])->render();
                                        return new HtmlString($label . $tooltip);
                                    })
                                    ->prefix('%')
                                    ->validationAttribute('Rates decrease')
                                    ->reactive()
                                    ->required()
                                    ->numeric()
                                    ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                                    ->inputMode('decimal')
                                    ->minValue(0)
                                    ->placeholder('Enter percentage (e.g., 10)')
                                    ->live(true)
                                    ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                        $set('rates_decrease', $state ?: null);
                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->visible(fn(Get $get) => $get('additional_policy') === 'non_refundable'),

                                AlertBox::make()
                                    ->helperText(function (Get $get) {
                                        return sprintf(
                                            'Basic and Seasonal rates will decrease by %s%% when guests book with this cancellation policy.',
                                            $get('rates_decrease')
                                        );
                                    })
                                    ->success()
                                    ->resolveIconUsing(name: 'heroicon-o-information-circle')
                                    ->visible(fn(Get $get) => $get('rates_decrease')),
                            ])
                            ->visible(fn(Get $get) => $get('cancellation_policy')) // Show only if a "Preferred policy" is selected
                            ->columns(1),

                        Fieldset::make('additional_policy_2_set')
                            ->label('3. Additional policy')
                            ->schema([
                                Select::make('additional_policy_2')
                                    ->label('Adjust your rates for guests that book using this cancellation policy.')
                                    ->validationAttribute('Additional Policy')
                                    ->reactive()
                                    ->afterStateUpdated(function (?string $state, Set $set, $livewire, $component) {
                                        $set('additional_policy_2', $state ?: null);
                                        $set('rates_increase_2', null);
                                        $set('rates_decrease_2', null);

                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->options(fn(Get $get) => self::getFurtherPolicyOptions($get('cancellation_policy'), $get('additional_policy'))),

                                TextInput::make('rates_increase_2')
                                    ->label(function () {
                                        $label = "Rates increase";
                                        $tooltip = view('custom-label-help', [
                                            'icon' => 'heroicon-o-question-mark-circle',
                                            'tooltip' => 'Increase your rates for more flexible policies, and decrease them for stricter ones. We recommend a maximum change of 30%. You can select decimal values or input 0 to use your usual rates.',
                                        ])->render();
                                        return new HtmlString($label . $tooltip);
                                    })
                                    ->prefix('%')
                                    ->validationAttribute('Rates increase')
                                    ->numeric()
                                    ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                                    ->inputMode('decimal')
                                    ->minValue(0)
                                    ->placeholder('Enter percentage (e.g., 10)')
                                    ->live(true)
                                    ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                        $set('rates_increase_2', $state ?: null);
                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->visible(fn(Get $get) => $get('additional_policy_2') !== null && $get('additional_policy_2') !== 'non_refundable'),
                                AlertBox::make()
                                    ->helperText(function (Get $get) {
                                        return sprintf(
                                            'Basic and Seasonal rates will increase by %s%% when guests book with this cancellation policy.',
                                            $get('rates_increase_2')
                                        );
                                    })
                                    ->success()
                                    ->resolveIconUsing(name: 'heroicon-o-information-circle')
                                    ->visible(fn(Get $get) => $get('rates_increase_2')),

                                TextInput::make('rates_decrease_2')
                                    ->label(function () {
                                        $label = "Rates decrease";
                                        $tooltip = view('custom-label-help', [
                                            'icon' => 'heroicon-o-question-mark-circle',
                                            'tooltip' => 'Increase your rates for more flexible policies, and decrease them for stricter ones. We recommend a maximum change of 30%. You can select decimal values or input 0 to use your usual rates.',
                                        ])->render();
                                        return new HtmlString($label . $tooltip);
                                    })
                                    ->prefix('%')
                                    ->validationAttribute('Rates decrease')
                                    ->numeric()
                                    ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                                    ->inputMode('decimal')
                                    ->minValue(0)
                                    ->placeholder('Enter percentage (e.g., 10)')
                                    ->live(true)
                                    ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                        $set('rates_decrease_2', $state ?: null);
                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->visible(fn(Get $get) => $get('additional_policy_2') === 'non_refundable'),

                                AlertBox::make()
                                    ->helperText(function (Get $get) {
                                        return sprintf(
                                            'Basic and Seasonal rates will decrease by %s%% when guests book with this cancellation policy.',
                                            $get('rates_decrease_2')
                                        );
                                    })
                                    ->success()
                                    ->resolveIconUsing(name: 'heroicon-o-information-circle')
                                    ->visible(fn(Get $get) => $get('rates_decrease_2')),
                            ])
                            ->columns(1)
                            ->visible(fn(Get $get) => $get('additional_policy')),
                    ]),

                Section::make("Add specific booking options for Booking.com")
                    ->description('Create additional booking options for guests on Booking.com by configuring your preferred policies. These will replace your policies above for this channel only.')
                    ->schema([
                        Repeater::make('booking_rules')
                            ->label('Cancelation policy (max 7)')
                            ->formatStateUsing(function ($state, $component) {
                                $record = $component->getRecord();

                                if (!$record || !$record->id) {
                                    return [];
                                }

                                $bookingRules = BookingRule::where('property_id', $record->id)->get();

                                if ($bookingRules->isNotEmpty()) {
                                    return $state ?: $bookingRules->toArray();
                                }

                                return $state ?: [];
                            })
                            ->live(true)
                            ->afterStateUpdated(function ($livewire, $component) {
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                            ->maxItems(7)
                            ->extraItemActions([
                                Action::make('Delete')
                                    ->icon('heroicon-o-trash')
                                    ->color('danger')
                                    ->action(function (array $arguments, Repeater $component): void {
                                        $items = $component->getState();
                                        unset($items[$arguments['item']]);
                                        $component->state($items);
                                    })
                                    ->requiresConfirmation()
                                    ->visible(function (Repeater $component, callable $get, array $arguments): bool {
                                        $items = $component->getState();
                                        $keys = array_keys($items);
                                        $currentKey = $arguments['item'] ?? null;
                                        $firstKey = reset($keys);
                                        if (count($keys) === 1) {
                                            return true;
                                        } else {
                                            return $currentKey !== $firstKey;
                                        }
                                    }),
                            ])
                            ->deletable(false)
                            ->reorderable(false)

                            ->itemLabel(function ($state) {
                                static $index = 0;
                                $index++;
                                $someTitle = $index === 1 ? 'Preferred policy' : 'Additional policy:';
                                return "{$index}. {$someTitle}";
                            })

                            ->schema([

                                Placeholder::make('description')
                                    ->label(fn(Get $get) => new HtmlString(
                                        '<strong>' .
                                            ($get('free_cancellation_period')
                                                ? 'Free - ' . $get('free_cancellation_period')
                                                : 'No free cancellation')
                                            . '</strong>'
                                    ))
                                    ->content(fn(Get $get) => new HtmlString(
                                        '* ' .
                                            ($get('is_free_cancellation')
                                                ? 'Guests pay ' . $get('cancellation_fee') . ' if they cancel after ' . $get('free_cancellation_period')
                                                : 'Guests will be charged ' . $get('cancellation_fee') . ' of the booking if they cancel after reservation') .
                                            '<br>* If No-Show Fee: ' .
                                            ($get('no_show_fee') ?? 'N/A') .
                                            '<br>* ' .
                                            ($get('rate_adjustment_value') ? 'Rate ' . $get('rate_adjustment_type') . ' ' . $get('rate_adjustment_value') . '%' : 'No Rate Adjustment')

                                    ))
                            ])
                            ->extraItemActions([
                                Action::make('Edit')
                                    ->icon('heroicon-s-pencil')
                                    ->modalHeading('Edit Booking Rule')
                                    ->fillForm(fn(array $arguments, Repeater $component) => $component->getState()[$arguments['item']] ?? [])
                                    ->form([

                                        // Информация
                                        Placeholder::make('info_placeholder')
                                            ->label(false)
                                            ->content('This policy will replace any other cancellation policies set up in the Builder, but only for Booking.com.'),

                                        // Вопрос о бесплатной отмене
                                        Radio::make('is_free_cancellation')
                                            ->label('Is there a free cancellation period for the guest?')
                                            ->options([
                                                true => 'Yes',
                                                false => 'No',
                                            ])
                                            ->required()
                                            ->boolean()
                                            ->reactive()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                // При изменении "Yes"/"No" скрываем или показываем соответствующие поля
                                                if ($state === true) {
                                                    $set('free_cancellation_period', null);
                                                    $set('cancellation_fee', null);
                                                    $set('no_show_fee', null);
                                                }
                                            }),

                                        // Поля для свободной отмены, если "Yes"
                                        Group::make([
                                            Select::make('free_cancellation_period')
                                                ->label('How long before arrival can the guest cancel free of charge?')
                                                ->options(function ($state, $component, Get $get) {
                                                    $availableFreeCancelationPeriod = BookingRule::getFreeCancellationPeriods();
                                                    $data = $get('../../data.booking_rules');
                                                    $stateBookingRule = collect($data)->pluck('free_cancellation_period')->toArray();

                                                    $filteredBookingRule = collect($stateBookingRule)->filter(fn($bookingRule) => $bookingRule !== $bookingRule)->toArray();

                                                    return collect($availableFreeCancelationPeriod)
                                                        ->except($filteredBookingRule)
                                                        ->mapWithKeys(fn($value, $key) => [$key => "Until {$value}"])
                                                        ->toArray();
                                                })
                                                ->live(true)
                                                ->required()
                                                ->hidden(fn(Get $get) => $get('is_free_cancellation') === null || !$get('is_free_cancellation') === true),

                                            Radio::make('cancellation_fee')
                                                ->label('How much is the guest charged for cancellation after the cancellation deadline?')
                                                ->options(BookingRule::getCancellationFees())
                                                ->required()
                                                ->hidden(fn(Get $get) => $get('free_cancellation_period') === null || !$get('is_free_cancellation') === true)
                                                ->live(true)
                                                ->afterStateUpdated(function (Set $set, $state) {
                                                    if ($state === '100% of the total price') {
                                                        $set('no_show_fee', '100% of the total price');
                                                    }
                                                }),

                                            Placeholder::make('no_show_fee_label')
                                                ->label('No-show fee if the guest dosen\'t show up:')
                                                ->content('100% of the total price')
                                                ->hidden(fn(Get $get) => $get('cancellation_fee') === null || $get('cancellation_fee') !== '100% of the total price'),

                                            Hidden::make('no_show_fee')
                                                ->default(fn(Get $get) => $get('cancellation_fee') === '100% of the total price' ? '100% of the total price' : null)
                                                ->afterStateUpdated(function (Set $set, $state) {
                                                    if ($state === '100% of the total price') {
                                                        $set('no_show_fee', '100% of the total price');
                                                    }
                                                })
                                                ->dehydrated(),

                                            Radio::make('no_show_fee')
                                                ->label('How much is the guest charged for a no show?')
                                                ->options(BookingRule::getNoShowFees())
                                                ->required()
                                                ->hidden(fn(Get $get) => $get('cancellation_fee') === null || $get('cancellation_fee') === '100% of the total price' || $get('is_free_cancellation') === null || !$get('is_free_cancellation') === true),
                                        ])
                                            ->hidden(fn(Get $get) => $get('is_free_cancellation') === null || !$get('is_free_cancellation') === true),

                                        // Поля для случаев, когда "No"
                                        Group::make([
                                            Radio::make('cancellation_fee')
                                                ->label('How much is the guest charged for cancellation?')
                                                ->options(BookingRule::getCancellationFees())
                                                ->required()
                                                ->hidden(fn(Get $get) => $get('is_free_cancellation') === null || !$get('is_free_cancellation') === false)
                                                ->live(true)
                                                ->afterStateUpdated(function (Set $set, $state) {
                                                    if ($state === '100% of the total price') {
                                                        $set('no_show_fee', '100% of the total price');
                                                    }
                                                }),

                                            Placeholder::make('no_show_fee_label')
                                                ->label('No-show fee if the guest dosen\'t show up:')
                                                ->content('100% of the total price')
                                                ->hidden(fn(Get $get) => $get('cancellation_fee') === null || $get('cancellation_fee') !== '100% of the total price'),

                                            Hidden::make('no_show_fee')
                                                ->default(fn(Get $get) => $get('cancellation_fee') === '100% of the total price' ? '100% of the total price' : null)
                                                ->afterStateUpdated(function (Set $set, $state) {
                                                    if ($state === '100% of the total price') {
                                                        $set('no_show_fee', '100% of the total price');
                                                    }
                                                })
                                                ->dehydrated(),

                                            Radio::make('no_show_fee')
                                                ->label('How much is the guest charged for a no show?')
                                                ->options(BookingRule::getNoShowFees())
                                                ->required()
                                                ->hidden(fn(Get $get) => $get('cancellation_fee') === null || $get('cancellation_fee') === '100% of the total price' || $get('is_free_cancellation') === null || !$get('is_free_cancellation') === false),
                                        ])
                                            ->hidden(fn(Get $get) => $get('is_free_cancellation') === null || !$get('is_free_cancellation') === false),

                                        // Изменение ставок в зависимости от политики
                                        Select::make('rate_adjustment_type')
                                            ->label('How does this policy affect your basic rate?')
                                            ->options(BookingRule::getRateAdjustmentTypes())
                                            ->reactive()
                                            ->hidden(fn(Get $get) => $get('cancellation_fee') === null || $get('is_free_cancellation') === null),

                                        TextInput::make('rate_adjustment_value')
                                            ->label('%')
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->required()
                                            ->hidden(fn(Get $get) => !$get('rate_adjustment_type'))
                                            ->extraInputAttributes([
                                                'inputmode' => 'numeric', // Указывает, что поле принимает только числа
                                                'pattern' => '[0-9]*', // Регулярное выражение для блокировки нечисловых символов
                                                'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')", // JavaScript для блокировки ввода символов
                                            ]),
                                    ])
                                    ->action(function (array $arguments, array $data, Repeater $component) {
                                        $state = $component->getState();
                                        $state[$arguments['item']] = $data;
                                        $component->state($state);
                                    }),
                            ])
                            ->addActionLabel('Create new cancellation policy')
                            ->addAction(function ($action) {
                                return $action->form([

                                    // Информация
                                    Placeholder::make('info_placeholder')
                                        ->label(false)
                                        ->content('This policy will replace any other cancellation policies set up in the Builder, but only for Booking.com.'),

                                    // Вопрос о бесплатной отмене
                                    Radio::make('is_free_cancellation')
                                        ->label('Is there a free cancellation period for the guest?')
                                        ->options([
                                            true => 'Yes',
                                            false => 'No',
                                        ])
                                        ->required()
                                        ->boolean()
                                        ->reactive()
                                        ->afterStateUpdated(function (Set $set, $state) {
                                            // При изменении "Yes"/"No" скрываем или показываем соответствующие поля
                                            if ($state === true) {
                                                $set('free_cancellation_period', null);
                                                $set('cancellation_fee', null);
                                                $set('no_show_fee', null);
                                            }
                                        }),

                                    // Поля для свободной отмены, если "Yes"
                                    Group::make([
                                        Select::make('free_cancellation_period')
                                            ->label('How long before arrival can the guest cancel free of charge?')
                                            ->options(function ($state, $component, Get $get) {
                                                $availableFreeCancelationPeriod = BookingRule::getFreeCancellationPeriods();
                                                $data = $get('../../data.booking_rules');
                                                $stateBookingRule = collect($data)->pluck('free_cancellation_period')->toArray();

                                                return collect($availableFreeCancelationPeriod)
                                                    ->except($stateBookingRule)
                                                    ->mapWithKeys(fn($value, $key) => [$key => "Until {$value}"])
                                                    ->toArray();
                                            })
                                            ->live(true)
                                            ->required()
                                            ->hidden(fn(Get $get) => $get('is_free_cancellation') === null || !$get('is_free_cancellation') === true),

                                        Radio::make('cancellation_fee')
                                            ->label('How much is the guest charged for cancellation after the cancellation deadline?')
                                            ->options(BookingRule::getCancellationFees())
                                            ->required()
                                            ->hidden(fn(Get $get) => $get('free_cancellation_period') === null || !$get('is_free_cancellation') === true)
                                            ->live(true)
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                if ($state === '100% of the total price') {
                                                    $set('no_show_fee', '100% of the total price');
                                                }
                                            }),

                                        Placeholder::make('no_show_fee_label')
                                            ->label('No-show fee if the guest dosen\'t show up:')
                                            ->content('100% of the total price')
                                            ->hidden(fn(Get $get) => $get('cancellation_fee') === null || $get('cancellation_fee') !== '100% of the total price'),

                                        Hidden::make('no_show_fee')
                                            ->default(fn(Get $get) => $get('cancellation_fee') === '100% of the total price' ? '100% of the total price' : null)
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                if ($state === '100% of the total price') {
                                                    $set('no_show_fee', '100% of the total price');
                                                }
                                            })
                                            ->dehydrated(),

                                        Radio::make('no_show_fee')
                                            ->label('How much is the guest charged for a no show?')
                                            ->options(BookingRule::getNoShowFees())
                                            ->required()
                                            ->hidden(fn(Get $get) => $get('cancellation_fee') === null || $get('cancellation_fee') === '100% of the total price' || $get('is_free_cancellation') === null || !$get('is_free_cancellation') === true),
                                    ])
                                        ->hidden(fn(Get $get) => $get('is_free_cancellation') === null || !$get('is_free_cancellation') === true),

                                    // Поля для случаев, когда "No"
                                    Group::make([
                                        Radio::make('cancellation_fee')
                                            ->label('How much is the guest charged for cancellation?')
                                            ->options(BookingRule::getCancellationFees())
                                            ->required()
                                            ->hidden(fn(Get $get) => $get('is_free_cancellation') === null || !$get('is_free_cancellation') === false)
                                            ->live(true)
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                if ($state === '100% of the total price') {
                                                    $set('no_show_fee', '100% of the total price');
                                                }
                                            }),

                                        Placeholder::make('no_show_fee_label')
                                            ->label('No-show fee if the guest dosen\'t show up:')
                                            ->content('100% of the total price')
                                            ->hidden(fn(Get $get) => $get('cancellation_fee') === null || $get('cancellation_fee') !== '100% of the total price'),

                                        Hidden::make('no_show_fee')
                                            ->default(fn(Get $get) => $get('cancellation_fee') === '100% of the total price' ? '100% of the total price' : null)
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                if ($state === '100% of the total price') {
                                                    $set('no_show_fee', '100% of the total price');
                                                }
                                            })
                                            ->dehydrated(),

                                        Radio::make('no_show_fee')
                                            ->label('How much is the guest charged for a no show?')
                                            ->options(BookingRule::getNoShowFees())
                                            ->required()
                                            ->hidden(fn(Get $get) => $get('cancellation_fee') === null || $get('cancellation_fee') === '100% of the total price' || $get('is_free_cancellation') === null || !$get('is_free_cancellation') === false),
                                    ])
                                        ->hidden(fn(Get $get) => $get('is_free_cancellation') === null || !$get('is_free_cancellation') === false),

                                    // Изменение ставок в зависимости от политики
                                    Select::make('rate_adjustment_type')
                                        ->label('How does this policy affect your basic rate?')
                                        ->options(BookingRule::getRateAdjustmentTypes())
                                        ->reactive()
                                        ->hidden(fn(Get $get) => $get('cancellation_fee') === null || $get('is_free_cancellation') === null),

                                    TextInput::make('rate_adjustment_value')
                                        ->label('Increase your rates for more flexible policies, and decrease them for stricter ones. (%)')
                                        ->numeric()
                                        ->minValue(0)
                                        ->default(0)
                                        ->required()
                                        ->hidden(fn(Get $get) => !$get('rate_adjustment_type'))
                                        ->extraInputAttributes([
                                            'inputmode' => 'numeric', // Указывает, что поле принимает только числа
                                            'pattern' => '[0-9]*', // Регулярное выражение для блокировки нечисловых символов
                                            'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')", // JavaScript для блокировки ввода символов
                                        ]),



                                ])
                                    ->action(function ($data, Set $set, Get $get) {
                                        $currentState = $get('booking_rules') ?? [];
                                        // // Проверка на количество добавленных записей
                                        // if (count($currentState) >= 7) {
                                        //     // Если записей 7 или больше, не добавляем новую
                                        //     session()->flash('error', 'You can only add up to 7 booking rules.');
                                        //     return;
                                        // }
                                        $currentState[] = $data;
                                        $set('booking_rules', $currentState);
                                    });
                            }),
                    ])
                    ->hidden(
                        fn(Get $get) => (Filament::auth()->user()->hasRole('admin') ? false : count($get('booking_rules')) === 0)
                    ),
            ]);
    }

    protected static function getPreferredPolicyOptions(): array
    {
        return [
            'free_7_days' => 'Free cancellation up to 7 days: 100% refund up to 7 days prior to check-in',
            'free_14_days' => 'Free cancellation up to 14 days: 100% refund up to 14 days prior to check-in',
            'free_30_days' => 'Free cancellation up to 30 days: 100% refund up to 30 days prior to check-in',
            'super_flexible' => 'Super flexible: 50% refund up to check-in, except fees',
            'flexible' => 'Flexible: 50% refund up to 7 days prior to check-in, except fees',
            'moderate' => 'Moderate: 50% refund up to 14 days prior to check-in, except fees',
            'strict' => 'Strict: 50% refund up to 30 days prior to check-in, except fees',
            'non_refundable' => 'Non-refundable: No refund for guest cancellation',
        ];
    }

    protected static function getAdditionalPolicyOptions(?string $preferredPolicy): array
    {
        if (!$preferredPolicy) {
            return [];
        }

        $options = [];

        switch ($preferredPolicy) {
            case 'free_7_days':
            case 'free_14_days':
            case 'free_30_days':
                $options = [
                    'super_flexible' => 'Super flexible: 50% refund up to check-in, except fees',
                    'flexible' => 'Flexible: 50% refund up to 7 days prior to check-in, except fees',
                    'moderate' => 'Moderate: 50% refund up to 14 days prior to check-in, except fees',
                    'strict' => 'Strict: 50% refund up to 30 days prior to check-in, except fees',
                    'non_refundable' => 'Non-refundable: No refund for guest cancellation',
                ];
                break;
            case 'super_flexible':
            case 'flexible':
            case 'moderate':
            case 'strict':
                $options = [
                    'free_7_days' => 'Free cancellation up to 7 days: 100% refund up to 7 days prior to check-in',
                    'free_14_days' => 'Free cancellation up to 14 days: 100% refund up to 14 days prior to check-in',
                    'free_30_days' => 'Free cancellation up to 30 days: 100% refund up to 30 days prior to check-in',
                    'non_refundable' => 'Non-refundable: No refund for guest cancellation',
                ];
                break;
            case 'non_refundable':
                $options = [
                    'free_7_days' => 'Free cancellation up to 7 days: 100% refund up to 7 days prior to check-in',
                    'free_14_days' => 'Free cancellation up to 14 days: 100% refund up to 14 days prior to check-in',
                    'free_30_days' => 'Free cancellation up to 30 days: 100% refund up to 30 days prior to check-in',
                    'super_flexible' => 'Super flexible: 50% refund up to check-in, except fees',
                    'flexible' => 'Flexible: 50% refund up to 7 days prior to check-in, except fees',
                    'moderate' => 'Moderate: 50% refund up to 14 days prior to check-in, except fees',
                    'strict' => 'Strict: 50% refund up to 30 days prior to check-in, except fees',
                ];
                break;
        }

        return $options;
    }

    protected static function getFurtherPolicyOptions(?string $preferredPolicy, ?string $additionalPolicy): array
    {
        if (!$preferredPolicy || !$additionalPolicy) {
            return [];
        }

        $options = [];

        if (
            in_array($preferredPolicy, ['free_7_days', 'free_14_days', 'free_30_days']) &&
            in_array($additionalPolicy, ['super_flexible', 'flexible', 'moderate', 'strict'])
        ) {
            // Case (a)
            $options = [
                'non_refundable' => 'Non-refundable: No refund for guest cancellation',
            ];
        } elseif (
            in_array($preferredPolicy, ['super_flexible', 'flexible', 'moderate', 'strict']) &&
            in_array($additionalPolicy, ['free_7_days', 'free_14_days', 'free_30_days'])
        ) {
            // Case (b)
            $options = [
                'non_refundable' => 'Non-refundable: No refund for guest cancellation',
            ];
        } elseif (
            in_array($preferredPolicy, ['super_flexible', 'flexible', 'moderate', 'strict']) &&
            $additionalPolicy === 'non_refundable'
        ) {
            // Case (c)
            $options = [
                'free_7_days' => 'Free cancellation up to 7 days: 100% refund up to 7 days prior to check-in',
                'free_14_days' => 'Free cancellation up to 14 days: 100% refund up to 14 days prior to check-in',
                'free_30_days' => 'Free cancellation up to 30 days: 100% refund up to 30 days prior to check-in',
            ];
        } elseif (
            $preferredPolicy === 'non_refundable' &&
            in_array($additionalPolicy, ['free_7_days', 'free_14_days', 'free_30_days'])
        ) {
            // Case (d)
            $options = [
                'super_flexible' => 'Super flexible: 50% refund up to check-in, except fees',
                'flexible' => 'Flexible: 50% refund up to 7 days prior to check-in, except fees',
                'moderate' => 'Moderate: 50% refund up to 14 days prior to check-in, except fees',
                'strict' => 'Strict: 50% refund up to 30 days prior to check-in, except fees',
            ];
        } elseif (
            $preferredPolicy === 'non_refundable' &&
            in_array($additionalPolicy, ['super_flexible', 'flexible', 'moderate', 'strict'])
        ) {
            // Case (e)
            $options = [
                'free_7_days' => 'Free cancellation up to 7 days: 100% refund up to 7 days prior to check-in',
                'free_14_days' => 'Free cancellation up to 14 days: 100% refund up to 14 days prior to check-in',
                'free_30_days' => 'Free cancellation up to 30 days: 100% refund up to 30 days prior to check-in',
            ];
        } elseif (
            in_array($preferredPolicy, ['free_7_days', 'free_14_days', 'free_30_days']) &&
            $additionalPolicy === 'non_refundable'
        ) {
            // Case (f)
            $options = [
                'super_flexible' => 'Super flexible: 50% refund up to check-in, except fees',
                'flexible' => 'Flexible: 50% refund up to 7 days prior to check-in, except fees',
                'moderate' => 'Moderate: 50% refund up to 14 days prior to check-in, except fees',
                'strict' => 'Strict: 50% refund up to 30 days prior to check-in, except fees',
            ];
        }

        return $options;
    }
}
