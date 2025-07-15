<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\IconPosition;
use App\Filament\Resources\PropertyResource;
use Illuminate\Support\Facades\Auth;
use App\Models\LicenceType;
use App\Models\AdditionalLicenceType;
use App\Models\PropertyLicence;
use App\Models\BasicRateCommission;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\FileUpload;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Closure;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Group;


class PropertyBasicDetailsFields
{
    private static $tabTitle = 'Basic Details';

    public static function create(): Tab
    {
        return //Basic details Tab
            Tabs\Tab::make(self::$tabTitle)
            ->icon(fn(Get $get) => PropertyResource::getTabIcon($get('tab_icon_basic_details')))
            ->iconPosition(IconPosition::After)
            ->columns(4)
            ->schema([
                // Reportable seller
                Group::make([
                    Select::make('user_id')
                        ->relationship('user', 'name', function ($query) {
                            $query->whereHas('roles', function ($q) {
                                $q->where('name', 'property_owner');
                            })->orWhereHas('roles', function ($q) {
                                $q->where('name', 'company');
                            });
                        })
                        ->formatStateUsing(function ($state) {
                            if (Auth::check() && Auth::user()->hasRole('manager')) {
                                $companyId = \App\Models\CompanyEmployee::where('employee_user_id', Auth::id())
                                    ->value('company_user_id');

                                if ($companyId) {
                                    return $companyId;
                                }
                            }

                            return $state;
                        })
                        ->label(function () {
                            $label = "Reportable seller";
                            $tooltip = view('custom-label-help', [
                                'tooltip' => 'Please add the private person or company that ultimately receives the rental income from bookings in this listing. We need their tax information to comply with tax regulations and to pay you for future bookings. If you do not have this information right now, you can add it later in the Taxes information',
                                'position' => 'bottom',
                                'size' => 'large'
                            ])->render();
                            return new HtmlString($label . $tooltip);
                        })
                        ->validationAttribute('Reportable seller')
                        // ->selectablePlaceholder(false)
                        ->required()
                        ->default(fn() => Auth::id())
                        ->columnSpan(2)
                        ->disabled(function () {
                            return !Auth::check() || !Auth::user()->hasRole('admin');
                        })
                        ->extraInputAttributes([
                            'data-required' => 'true',
                            'name' => 'reportable seller',
                            'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            'id' => 'reportable-input',
                        ]),
                    // Deal Type
                    Fieldset::make('Deal Type')
                        ->columnSpan(2)
                        ->schema([
                            CheckboxList::make('deal_type')
                                ->label(false)
                                ->extraAttributes([
                                    'class' => 'grid !grid-cols-[repeat(3,minmax(0,1fr))]'
                                ])
                                ->extraInputAttributes(
                                    [
                                        'class' => 'deal-type-checkbox',
                                        'data-no-loader' => 'true',
                                        'x-data' => '{
                                            handleDealTypeChange() {
                                                const checkboxes = this.$el.querySelectorAll("input[type=checkbox]");
                                                const selectedTypes = [];
                                                checkboxes.forEach(checkbox => {
                                                    if (checkbox.checked) {
                                                        selectedTypes.push(checkbox.value);
                                                    }
                                                });
                                                
                                                // Update Livewire state without triggering reactivity
                                                $wire.set("data.deal_type", selectedTypes, false);
                                                
                                                // Handle dependent data update
                                                this.updateLicenceFields(selectedTypes);
                                            },
                                            async updateLicenceFields(dealTypes) {
                                                if (dealTypes.length === 0) {
                                                    $wire.set("data.propertyLicences", [], false);
                                                    return;
                                                }
                                                
                                                try {
                                                    const response = await fetch("/api/licence-types", {
                                                        method: "POST",
                                                        headers: {
                                                            "Content-Type": "application/json",
                                                            "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
                                                        },
                                                        body: JSON.stringify({ deal_types: dealTypes })
                                                    });
                                                    
                                                    const data = await response.json();
                                                    $wire.set("data.propertyLicences", data, false);
                                                } catch (error) {
                                                    console.error("Error updating licence fields:", error);
                                                }
                                            }
                                        }',
                                        'x-on:change' => 'handleDealTypeChange()'
                                    ]
                                )
                                ->gridDirection('row')
                                ->options([
                                    'deal_type_rent' => 'Short-term rent',
                                    'deal_type_monthly_rent' => 'Monthly rent',
                                    'deal_type_sale' => 'Sale',
                                ])
                                // ->live(true)  // Removed to prevent loader on checkbox click
                                ->allowHtml()
                                ->formatStateUsing(function ($state, $component) {

                                    $record = $component->getRecord();

                                    if (!$record || !$record->id) {
                                        return [];
                                    }

                                    $mapping = [
                                        'deal_type_rent' => $record->deal_type_rent,
                                        'deal_type_sale' => $record->deal_type_sale,
                                        'deal_type_monthly_rent' => $record->deal_type_monthly_rent,
                                    ];

                                    return array_keys(array_filter($mapping));
                                })
                                ->columns(2)
                                ->columnSpan(2)
                                ->required()
                                // ->reactive()  // REMOVED - No more Livewire reactivity
                                // ->afterStateUpdated()  // REMOVED - No more Livewire callbacks
                        ]),
                ])
                    ->columnSpan(2),

                Group::make([
                    // Property class
                    Select::make('property_class')
                        ->required()
                        ->markAsRequired()
                        ->columnSpan(2)
                        ->options([
                            'residential' => 'Residential',
                            'commercial' => 'Commercial',
                            'land' => 'Land',
                            'other' => 'Other'
                        ])
                        ->extraAttributes([
                            'x-model' => 'propertyClass',
                            'x-on:change' => 'fetchTypes()',
                        ])
                        ->extraInputAttributes([
                            'data-required' => 'true',
                            'name' => 'property class',
                            'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            'id' => 'property-class-input',

                        ]),



                    // Custom select rendered with Alpine
                    View::make('components.properties-type')
                        // ->viewData([
                        //      'selectedType' => fn(Get $get) => $get('property_type_id'),
                        // ])
                        ->extraAttributes([
                            'data-required' => 'true',
                            'name' => 'property type'
                        ])
                        ->columnSpan(2),
                ])
                    ->columnSpan(2)
                    ->extraAttributes([
                        'x-data' => '{
                                propertyClass: $wire.get("data.property_class") || "",
                                property_type_id: $wire.get("data.property_type_id") || "",
                                property_type_custom: $wire.get("data.property_type_custom") || "",
                                typeOptions: {},
                                async fetchTypes() {
                                    if (!this.propertyClass || this.propertyClass === "other") {
                                        this.typeOptions = {};
                                        // this.property_type_id = "";
                                        return;
                                    }
                                    const response = await fetch(`/api/property-types/${this.propertyClass}`);
                                    const data = await response.json();
                                    this.typeOptions = data;
                                    this.property_type_id = $wire.get("data.property_type_id") || "";
                                },
                                init() {
                                    this.fetchTypes().then(() => {
                                        // Only set property_type_id AFTER options are loaded
                                        this.property_type_id = String($wire.get("data.property_type_id")) || "";
                                    });

                                    this.$watch("property_type_id", value => {
                                        console.log("property_type_id changed1:", value);
                                        $wire.set("data.property_type_id", value, false);
                                    });

                                    this.$watch("propertyClass", value => {
                                        console.log("propertyClass changed:", value);
                                        // $wire.set("data.property_class", value, false);

                                        this.fetchTypes();
                                    });

                                    this.$watch("property_type_custom", value => {
                                        console.log("property_type_custom changed1:", value);
                                        $wire.set("data.property_type_custom", value, false);
                                    });
                                }
                            }',
                        'x-init' => 'init();fetchTypes();',
                    ]),

                TextInput::make('title')
                    ->required()
                    ->label(function () {
                        $label = "Title from property owner";
                        $tooltip = view('custom-label-help', [
                            'tooltip' => 'Please note: Title will be changed to Commercial marketing title by System administration',
                            'position' => 'bottom'
                        ])->render();
                        return new HtmlString($label . $tooltip);
                    })
                    ->validationAttribute('Title')
                    ->maxLength(255)
                    ->columnSpan(4)
                    // ->live(true)
                    // ->afterStateUpdated(function ($livewire, $component) {
                    //     PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                    //     $livewire->validateOnly($component->getStatePath());
                    // })
                    // ->disabled(function () {
                    //     return !Auth::check() || !Auth::user()->hasRole('admin');
                    // })
                    ->extraInputAttributes(
                        [
                            'data-required' => 'true',
                            'name' => 'title',
                            'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            'id' => 'title-input',
                        ]

                    ),
                Placeholder::make('commercial_title_label')
                    ->label(false)
                    ->columnSpan('full')
                    ->reactive()
                    ->live(true)
                    ->content(function (Get $get) {
                        $label = $get('commercial_title');
                        return new HtmlString('Commercial title: ' . $label . '.');
                    })
                    ->visible(fn(Get $get): bool => !empty($get('commercial_title'))),

                TextInput::make('floorspace')
                    ->required()
                    ->label('Floorspace')
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(1)
                    ->required()
                    ->rules([
                        'numeric',
                        'min:1',
                        'gt:0'
                    ])
                    ->afterStateUpdated(function ($state, callable $set, $livewire, $component) {
                        if (is_numeric($state)) {
                            if ($state < 1) {
                                $component->getStatePath() && $component->state(null);
                                $component->addError('Value must be at least 1');
                            } else {
                                $set('floorspace', round($state, 2));
                            }
                        } else {
                            $set('floorspace', null);
                        }

                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                    })
                    ->dehydrateStateUsing(function ($state) {
                        return empty($state) ? null : round($state, 2);
                    })
                    ->extraInputAttributes([
                        'min' => '1',
                        'class' => '[&::-webkit-inner-spin-button]:appearance-none',
                        'data-required' => 'true',
                        'data-type' => 'number',
                        'name' => 'floorspace',
                        'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                        'id' => 'floorspace-input',
                    ])
                    ->validationMessages([
                        'min' => 'Floorspace must be at least 1',
                    ]),

                Select::make('floorspace_units')
                    ->label('Floorspace Units')
                    ->options([
                        'm2' => 'm²',
                        'ft2' => 'ft²'
                    ])
                    // ->live(true)
                    // ->selectablePlaceholder(false)
                    // ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                    //     $set('floorspace_units', $state ?: null);
                    //     PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                    //     $livewire->validateOnly($component->getStatePath());
                    // })
                    ->required()
                    ->extraInputAttributes(
                        [
                            'data-required' => 'true',
                            'name' => 'floorspace units',
                            'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            'id' => 'floorspace-units-input',
                        ]

                    ),

                TextInput::make('grounds')
                    ->label(' Grounds')
                    ->numeric()
                    ->inputMode('decimal')
                    // ->live(onBlur: true)
                    // ->afterStateUpdated(function ($state, callable $set, $livewire, $component) {
                    //     if (is_numeric($state)) {
                    //         $set('grounds', round($state, 2));
                    //     } else {
                    //         $set('grounds', null);
                    //     }
                    //     PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                    //     $livewire->validateOnly($component->getStatePath());
                    // })
                    ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none'])
                    ->minValue(0)
                    ->step(0.01)
                    ->nullable(),

                Select::make('grounds_units')
                    ->label('Grounds Units')
                    ->options([
                        'm2' => 'm²',
                        'ft2' => 'ft²'
                    ])
                    // ->live(true)
                    // ->afterStateUpdated(function (?string $state, $set, $livewire, $component) {
                    //     $set('grounds_units', $state ?: null);
                    //     PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                    //     $livewire->validateOnly($component->getStatePath());
                    // })
                    ->nullable(),

                TextInput::make('floors_in_building')
                    ->label('Floors in Building')
                    ->numeric()
                    ->minValue(0)
                    ->extraInputAttributes(['min' => '0'])
                    ->nullable()
                // ->live(true)
                // ->afterStateUpdated(function (?string $state, $set, $livewire, $component) {
                //     $set('floors_in_building', $state ?: null);
                //     PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                //     $livewire->validateOnly($component->getStatePath());
                // })
                ,

                TextInput::make('floors_of_property')
                    ->label('Floors of Property')
                    ->numeric()
                    ->minValue(0)
                    ->extraInputAttributes(['min' => '0'])
                    ->nullable()
                // ->live(true)
                // ->afterStateUpdated(function (?string $state, Set $set, $livewire, $component) {
                //     $set('floors_of_property', $state ?: null);
                //     PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                //     $livewire->validateOnly($component->getStatePath());
                // })
                ,

                Select::make('entrance')
                    ->label('Entrance')
                    ->options([
                        'Secured' => 'Common with security',
                        'Unsecured' => 'Common without security',
                        'Private' => 'Private'
                    ])
                    // ->live(true)
                    // ->afterStateUpdated(function (?string $state, $set, $livewire, $component) {
                    //     $set('entrance', $state ?: null);
                    //     PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                    //     $livewire->validateOnly($component->getStatePath());
                    // })
                    ->nullable()
                    ->columnSpan(2),


                Select::make('rental_licence_type_id')
                    ->label('Rental Licence Type')
                    ->options(function () {
                        return LicenceType::pluck('name', 'id')->toArray();
                    })
                    // ->live(true)
                    // // ->selectablePlaceholder(false)
                    // ->afterStateUpdated(function (?string $state, $set, $livewire, $component) {
                    //     $set('rental_licence_type_id', $state ?: null);
                    //     PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                    //     $livewire->validateOnly($component->getStatePath());
                    // })
                    ->required()
                    ->columnSpan(2)
                    ->visible(
                        fn(Get $get): bool =>
                        is_array($get('deal_type')) &&
                            (in_array('deal_type_rent', $get('deal_type')) || in_array('deal_type_monthly_rent', $get('deal_type')))
                    )
                    ->extraInputAttributes(
                        [
                            'data-required' => 'true',
                            'name' => 'rental Licence Type',
                            'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            'id' => 'rental-licence-type-input',
                        ]

                    ),

                TextInput::make('rental_licence_number')
                    ->label('Rental Licence Number')
                    ->columnSpan(4)
                    ->required()
                    ->columnSpan(2)
                    // ->live(true)
                    // ->afterStateUpdated(function (?string $state, $set, $livewire, $component) {
                    //     $set('rental_licence_number', $state ?: null);
                    //     PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                    //     $livewire->validateOnly($component->getStatePath());
                    // })
                    ->visible(
                        fn(Get $get): bool =>
                        is_array($get('deal_type')) &&
                            (in_array('deal_type_rent', $get('deal_type')) || in_array('deal_type_monthly_rent', $get('deal_type')))
                    )
                    ->extraInputAttributes(
                        [
                            'data-required' => 'true',
                            'name' => ' rental Licence Number',
                            'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            'id' => 'rental-licence-number-input',
                        ]

                    ),

                Section::make('Licences')
                    ->schema([

                        TableRepeater::make('propertyLicences')
                            // ->relationship('propertyLicences')
                            ->label('Licences')
                            ->markAsRequired()
                            ->headers([
                                Header::make('licence_type')->label('Licence')->width('30%'),
                                Header::make('licence_number')->label('Number')->width('30%'),
                                Header::make('licence_file')->label('File')->width('40%'),
                            ])
                            ->deletable(false)
                            ->addable(false)
                            ->schema([
                                Placeholder::make('licence_type')
                                    ->label(function (Get $get) {
                                        $label = $get('licence_type');

                                        $tooltip = view('custom-label-help', [
                                            'icon'    => 'heroicon-o-question-mark-circle',
                                            'align'   => 'left',
                                            'size'    => 'large',
                                            'tooltip' => self::getRentalAddLicenceTypeTooltip($get('additional_licence_type_id'))
                                        ])->render();
                                        return new HtmlString($label . $tooltip);
                                    })
                                    ->content(''),

                                TextInput::make('licence_number')
                                    // ->required()
                                    ->label('Licence number')
                                // ->live(true)
                                // ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                //     $set('rental_licence_number', $state ?: null);
                                //     PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                //     $livewire->validateOnly($component->getStatePath());
                                // })
                                ,

                                FileUpload::make('licence_file_name')
                                    ->label('Upload Licence Document')
                                    ->disk('r2')
                                    ->directory('licence')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg'])
                                    ->maxSize(2048)
                                    ->downloadable()
                                    ->previewable(false)
                                    ->openable()
                                    // ->required()
                                    ->live(true)
                                    ->uploadingMessage('Uploading attachment...')
                                    ->visible(fn(callable $get) => optional(AdditionalLicenceType::find($get('additional_licence_type_id')))->file_attachment)
                                    ->afterStateUpdated(function ($livewire, $component) {
                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->getUploadedFileUsing(function ($state, string $file, $livewire) {
                                        $licencePath = '';

                                        $model = $livewire->getRecord();

                                        if ($model && $model->slug) {
                                            $licencePath = "properties/{$model->slug}/licence/";
                                        } else {
                                            return [];
                                        }

                                        if ($file) {
                                            return
                                                [
                                                    'name' => basename($file),
                                                    'url' => Storage::disk('r2')->url($licencePath . $file)
                                                ];
                                        }

                                        return [];
                                    }),
                            ])
                            ->columnSpan('full')
                            ->reactive()
                            ->live(true)
                            ->extraAttributes(fn(callable $get) => [
                                'wire:key' => 'propertyLicences-' . implode('-', (array) $get('deal_type')),
                            ])
                            ->formatStateUsing(function ($state, $component, $record, Get $get) {
                                if (!$record) {
                                    return;
                                }

                                // Загружаем объект недвижимости с лицензиями
                                $property = \App\Models\Property::with('propertyLicences.additionalLicenceType')->find($record->id);
                                $propertyLicences = $property->propertyLicences;

                                $dealType = $get('deal_type');

                                $query = AdditionalLicenceType::where('required', true);

                                $query->where(function ($q) use ($dealType) {
                                    $mapping = [
                                        'deal_type_sale'          => 'sale',
                                        'deal_type_rent'          => 'short_rent',
                                        'deal_type_monthly_rent'  => 'monthly_rent',
                                    ];
                                    $hasCondition = false;
                                    foreach ($mapping as $dealKey => $column) {
                                        if (in_array($dealKey, $dealType, true)) {
                                            $q->orWhere($column, true);
                                            $hasCondition = true;
                                        }
                                    }
                                    if (!$hasCondition) {
                                        $q->whereRaw('0 = 1');
                                    }
                                });

                                $licenceTypes = $query->get();
                                $requiredIds = $licenceTypes->pluck('id')->toArray();

                                // Фильтруем исходный массив состояния, оставляя только элементы с обязательными типами лицензий
                                $existingState = collect($state)->keyBy('additional_licence_type_id');

                                $newState = $licenceTypes->map(function ($licenceType) use ($propertyLicences, $existingState) {
                                    $licenceId = $licenceType->id;
                                    $existingLicence = $propertyLicences->firstWhere('additional_licence_type_id', $licenceId);

                                    return [
                                        'additional_licence_type_id' => $licenceId,
                                        'licence_type'               => $licenceType->name,
                                        'licence_number'             => $existingLicence?->licence_number ?? null,
                                        'licence_file_name'          => $existingLicence?->licence_file_name ? [$existingLicence->licence_file_name[0]] : null,
                                    ];
                                })->toArray();
                                return $newState;
                            }),

                        TableRepeater::make('otherLicenceType')
                            // ->relationship('licenceType')
                            ->formatStateUsing(function ($state, $component) {
                                $record = $component->getRecord();

                                if (!$record || !$record->id) {
                                    return [];
                                }

                                $nonRequiredLicenceTypeIds = AdditionalLicenceType::where('required', false)->pluck('id');

                                $otherLicence = PropertyLicence::where('property_id', $record->id)
                                    ->whereIn('additional_licence_type_id', $nonRequiredLicenceTypeIds)
                                    ->get();

                                if ($otherLicence->isNotEmpty()) {
                                    $slug = $record->slug ?? null;

                                    return $otherLicence->map(function ($licence) use ($slug) {
                                        return [
                                            'other_licence_type_id'    => $licence->additional_licence_type_id,
                                            'other_licence_number'     => $licence->licence_number,
                                            'other_licence_file'    => ($slug && isset($licence->licence_file_name[0]))
                                                ? "properties/{$slug}/licence/{$licence->licence_file_name[0]}"
                                                : null,
                                        ];
                                    })->toArray();
                                }

                                return [];
                            })
                            ->reorderable(false)
                            ->label('Other licences')
                            ->columnSpan('full')
                            ->headers([
                                Header::make('Licence type'),
                                Header::make('Number'),
                            ])
                            ->schema([
                                Placeholder::make('licence_type')
                                    ->label(false)
                                    ->content(fn(Get $get) => AdditionalLicenceType::getLicenceName()->get($get('other_licence_type_id'))),
                                Placeholder::make('licence_number')
                                    ->label(false)
                                    ->content(fn(Get $get) => $get('other_licence_number'))
                            ])
                            ->extraItemActions([

                                Action::make('Edit')
                                    ->icon('heroicon-s-pencil')
                                    ->modalHeading('Edit licence')
                                    ->fillForm(function (array $arguments, Repeater $component): array {
                                        $allItems = $component->getState();
                                        $currentKey = $arguments['item'];
                                        return $allItems[$currentKey] ?? [];
                                    })
                                    ->form([
                                        Select::make('other_licence_type_id')
                                            ->label(function (Get $get) {
                                                $label = "Licence";
                                                $tooltip = view('custom-label-help', [
                                                    'icon' => 'heroicon-o-question-mark-circle',
                                                    'align' => 'left',
                                                    'size' => 'large',
                                                    'tooltip' => self::getRentalAddLicenceTypeTooltip($get('other_licence_type_id'))
                                                ])->render();
                                                return new HtmlString($label . $tooltip);
                                            })
                                            ->options(function ($state, $component, Get $get) {

                                                $query = AdditionalLicenceType::where('required', false);

                                                $dealType = $get('../../data.deal_type');

                                                $query->where(function ($q) use ($dealType) {
                                                    $mapping = [
                                                        'deal_type_sale'          => 'sale',
                                                        'deal_type_rent'          => 'short_rent',
                                                        'deal_type_monthly_rent'  => 'monthly_rent',
                                                    ];
                                                    $hasCondition = false;
                                                    foreach ($mapping as $dealKey => $column) {
                                                        if (in_array($dealKey, $dealType, true)) {
                                                            $q->orWhere($column, true);
                                                            $hasCondition = true;
                                                        }
                                                    }
                                                    if (!$hasCondition) {
                                                        $q->whereRaw('0 = 1');
                                                    }
                                                });

                                                $availableLicenceTypes = $query->pluck('name', 'id');
                                                $data = $get('../../data.otherLicenceType');
                                                $stateLicences = collect($data)->pluck('other_licence_type_id')->toArray();

                                                $filteredLicences = collect($stateLicences)->filter(fn($licence) => $licence !== $state)->toArray();

                                                return collect($availableLicenceTypes)
                                                    ->except($filteredLicences)
                                                    ->toArray();
                                            })
                                            ->afterStateUpdated(function ($state, $set) {
                                                $set('other_licence_type_id', $state ?: null);
                                            })
                                            ->reactive()
                                            ->required(),

                                        TextInput::make('other_licence_number')
                                            ->label('Number')
                                            ->required(),

                                        Hidden::make('file_temp_path'),

                                        FileUpload::make('other_licence_file')
                                            ->label('Upload Licence Document')
                                            ->disk('r2')
                                            ->directory('licence')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg'])
                                            ->maxSize(2048)
                                            ->downloadable()
                                            ->previewable(true)
                                            ->openable()
                                            ->required()
                                            ->visible(fn(callable $get) => optional(AdditionalLicenceType::find($get('other_licence_type_id')))->file_attachment)
                                            ->afterStateUpdated(function ($state, Set $set, $livewire) {
                                                if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                                    $extension = $state->getClientOriginalExtension();
                                                    $newFileName = Str::uuid() . '.' . $extension;
                                                    $tempPath = "tmp-files/{$newFileName}";

                                                    $path = $state->storeAs('tmp-files', $newFileName, 'r2');

                                                    $set('file_temp_path', $tempPath);
                                                }
                                            })

                                    ])
                                    ->action(function (array $arguments, array $data, $component, Set $set, Get $get): void {
                                        $mainState = $component->getState();
                                        $key = $arguments['item'];
                                        $mainState[$key] = $data;
                                        $component->state($mainState);
                                    })
                            ])
                            ->addActionLabel('Add licence')
                            ->addAction(function ($action) {
                                return $action->form([

                                    Select::make('other_licence_type_id')
                                        ->label(function (Get $get) {
                                            $label = "Licence";
                                            $tooltip = view('custom-label-help', [
                                                'icon' => 'heroicon-o-question-mark-circle',
                                                'align' => 'left',
                                                'size' => 'large',
                                                'tooltip' => self::getRentalAddLicenceTypeTooltip($get('other_licence_type_id'))
                                            ])->render();
                                            return new HtmlString($label . $tooltip);
                                        })
                                        ->options(function ($state, $component, Get $get) {

                                            $query = AdditionalLicenceType::where('required', false);

                                            $dealType = $get('../../data.deal_type');
                                            $query->where(function ($q) use ($dealType) {
                                                $mapping = [
                                                    'deal_type_sale'          => 'sale',
                                                    'deal_type_rent'          => 'short_rent',
                                                    'deal_type_monthly_rent'  => 'monthly_rent',
                                                ];
                                                $hasCondition = false;
                                                foreach ($mapping as $dealKey => $column) {
                                                    if (in_array($dealKey, $dealType, true)) {
                                                        $q->orWhere($column, true);
                                                        $hasCondition = true;
                                                    }
                                                }
                                                if (!$hasCondition) {
                                                    $q->whereRaw('0 = 1');
                                                }
                                            });

                                            $availableLicenceTypes = $query->pluck('name', 'id');
                                            $data = $get('../../data.otherLicenceType');
                                            $stateLicences = collect($data)->pluck('other_licence_type_id')->toArray();

                                            $filteredLicences = collect($stateLicences)->filter(fn($licence) => $licence !== $state)->toArray();

                                            return collect($availableLicenceTypes)
                                                ->except($filteredLicences)
                                                ->toArray();
                                        })
                                        ->afterStateUpdated(function ($state, $set) {
                                            $set('other_licence_type_id', $state ?: null);
                                        })
                                        ->reactive()
                                        ->required(),

                                    TextInput::make('other_licence_number')
                                        ->label('Number')
                                    // ->required()
                                    ,

                                    Hidden::make('file_temp_path'),

                                    FileUpload::make('other_licence_file')
                                        ->label('Upload Licence Document')
                                        ->disk('r2')
                                        ->directory('licence')
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg'])
                                        ->maxSize(2048)
                                        ->downloadable()
                                        ->previewable(true)
                                        ->openable()
                                        ->required()
                                        ->visible(fn(callable $get) => optional(AdditionalLicenceType::find($get('other_licence_type_id')))->file_attachment)
                                        ->afterStateUpdated(function ($state, Set $set, $livewire) {
                                            if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                                $extension = $state->getClientOriginalExtension();
                                                $newFileName = Str::uuid() . '.' . $extension;
                                                $tempPath = "tmp-files/{$newFileName}";

                                                $path = $state->storeAs('tmp-files', $newFileName, 'r2');

                                                $set('file_temp_path', $tempPath);
                                            }
                                        }),
                                ])
                                    ->action(function ($data, Set $set, Get $get) {
                                        $currentState = $get('otherLicenceType') ?? [];
                                        $result = array_merge($currentState, [$data]);
                                        $set('otherLicenceType', $result);
                                    });
                            })
                            ->deleteAction(
                                fn(Action $action) => $action->requiresConfirmation(),
                            )

                    ]),

                Select::make('basic_rate_commission_id')
                    ->label(function () {
                        $label = "Select Your Applicable Tax Bracket";
                        $tooltip = view('custom-label-help', [
                            'icon' => 'heroicon-o-question-mark-circle',
                            'align' => 'left',
                            'size' => 'large',
                            'tooltip' => self::getTaxBracketTooltip()
                        ])->render();
                        return new HtmlString($label . $tooltip);
                    })
                    ->validationAttribute('Tax Bracket')
                    ->options(fn() => self::getTaxBracketOptions())
                    // ->relationship('basicRateCommission', 'revenue_level', function ($query) {
                    //     $query->orderBy('id');
                    // })

                    // ->selectablePlaceholder(false)
                    // ->live(true)
                    // ->afterStateUpdated(function (?string $state, $set, $livewire, $component) {
                    //     $set('basic_rate_commission_id', $state ?: null);
                    //     PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                    //     $livewire->validateOnly($component->getStatePath());
                    // })
                    ->required()
                    ->columnSpan(4)
                    ->visible(
                        fn(Get $get): bool =>
                        is_array($get('deal_type')) &&
                            (in_array('deal_type_rent', $get('deal_type')) || in_array('deal_type_monthly_rent', $get('deal_type')))
                    )
                    ->extraInputAttributes([
                        'data-required' => 'true',
                        'name' => 'applicable tax bracket',
                        'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                        'id' => 'basic_rate_commission_id',
                    ]),

                View::make('components.client-side-validation'), // inject your JS here
            ]);
    }

    protected static function getTaxBracketOptions(): array
    {
        if ((Auth::check() && Auth::user()->hasRole('property_owner'))
            || Auth::user()->hasRole('admin')
        ) {
            return BasicRateCommission::where('commission_type', 'Property owner')
                ->orderBy('id')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->id => "{$item->revenue_level} - {$item->taxes}%"
                    ];
                })
                ->toArray();
        }

        if (Auth::check() && Auth::user()->hasRole('manager')) {
            return BasicRateCommission::where('commission_type', 'Management company')
                ->orderBy('id')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->id => "{$item->taxes}%"
                    ];
                })
                ->toArray();
        }

        return [];
    }

    protected static function getTaxBracketTooltip(): string
    {
        if ((Auth::check() && Auth::user()->hasRole('property_owner'))
            || Auth::user()->hasRole('admin')
        ) {

            $taxBrackets = BasicRateCommission::where('commission_type', 'Property owner')
                ->orderBy('revenue_level')
                ->get();

            if ($taxBrackets->isEmpty()) {
                return "Tax bracket information is currently unavailable. Please contact support for assistance.";
            }

            $tooltip = "Please select the tax bracket that corresponds to the income generated from your short-term rental property. The tax brackets are as follows:
            <br/>
            <ul>";

            foreach ($taxBrackets as $bracket) {
                $tooltip .= "<li><strong>{$bracket->taxes}%</strong> for income up to <strong>{$bracket->revenue_level}</strong></li>";
            }

            $tooltip .= "</ul>
            This selection will help us understand the applicable tax category for your property based on the legislation. If you're unsure, please consult your accountant.";

            return $tooltip;
        }

        if (Auth::check() && Auth::user()->hasRole('manager')) {
            return "Please select the tax bracket that corresponds to the income generated from your short-term rental property. 
            This selection will help us understand the applicable tax category for your property based on the legislation. If you're unsure, please consult your accountant.";
        }

        return "";
    }

    protected static function getRentalAddLicenceTypeTooltip(?int $licenceId): string
    {
        if (!$licenceId) {
            return "Select a licence type to see details.";
        }

        return AdditionalLicenceType::where('id', $licenceId)->value('hint') ?? "No additional information available.";
    }
}
