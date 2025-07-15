<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\HtmlString;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\IconPosition;
use App\Filament\Resources\PropertyResource;
use Filament\Forms\Components\Hidden;
use App\Forms\Components\RoomsCountChangedModal;
use Filament\Forms\Components\Actions\Action;

class PropertyRoomsFields
{
    private static $tabTitle = 'Rooms';
    
    public static function create(): Tab
    {
        return Tab::make(self::$tabTitle)
            ->schema([
                self::getBedroomsGroup(),
                self::getBathroomsGroup(),
                self::getKitchensGroup(),
                self::getOtherRoomsGroup(),
                RoomsCountChangedModal::make('bedroom_changed_modal')
                    ->heading('Decrease the number of bedrooms')
                    ->description('You are about to decrease the number of bedrooms. This will result in the removal of excess items. Are you sure you want to proceed?')
                    ->registerActions([
                        Action::make('Confirm')
                                ->color('success')
                                ->action(function (Get $get, Set $set, $livewire) {
                                    $bedrooms = $get('bedrooms') ?? [];
                                    $count = (int) ($get('bedroom_count') ?? count($bedrooms));
                            
                                    if (count($bedrooms) > $count) {
                                        $bedrooms = array_slice($bedrooms, 0, $count);
                                        $set('bedrooms', $bedrooms);
                                    }

                                    $livewire->dispatch('close-modal', id: 'bedroom_changed_modal');
                                }),
                        Action::make('Cancel')
                                ->color('gray')
                                ->action(function (Get $get, Set $set, $livewire) {
                                    $previous = $get('bedroom_count_previous');
                                    if ($previous !== null) {
                                        $set('bedroom_count', $previous);
                                    }
                                    $livewire->dispatch('close-modal', id: 'bedroom_changed_modal');
                                }),
                            ]),
                                RoomsCountChangedModal::make('bedroom_changed_modal')
                    ->heading('Decrease the number of bedrooms')
                    ->description('You are about to decrease the number of bedrooms. This will result in the removal of excess items. Are you sure you want to proceed?')
                    ->registerActions([
                        Action::make('Confirm')
                                ->color('success')
                                ->action(function (Get $get, Set $set, $livewire) {
                                    $bedrooms = $get('bedrooms') ?? [];
                                    $count = (int) ($get('bedroom_count') ?? count($bedrooms));
                            
                                    if (count($bedrooms) > $count) {
                                        $bedrooms = array_slice($bedrooms, 0, $count);
                                        $set('bedrooms', $bedrooms);
                                    }

                                    $livewire->dispatch('close-modal', id: 'bedroom_changed_modal');
                                }),
                        Action::make('Cancel')
                                ->color('gray')
                                ->action(function (Get $get, Set $set, $livewire) {
                                    $previous = $get('bedroom_count_previous');
                                    if ($previous !== null) {
                                        $set('bedroom_count', $previous);
                                    }
                                    $livewire->dispatch('close-modal', id: 'bedroom_changed_modal');
                                }),
                            ]),
                RoomsCountChangedModal::make('bathroom_changed_modal')
                            ->heading('Decrease the number of bathrooms')
                            ->description('You are about to decrease the number of bathrooms. This will result in the removal of excess items. Are you sure you want to proceed?')
                            ->registerActions([
                                Action::make('Confirm')
                                        ->color('success')
                                        ->action(function (Get $get, Set $set, $livewire) {
                                            $bathrooms = $get('bathrooms') ?? [];
                                            $count = (int) ($get('bathroom_count') ?? count($bathrooms));
                                    
                                            if (count($bathrooms) > $count) {
                                                $bathrooms = array_slice($bathrooms, 0, $count);
                                                $set('bathrooms', $bathrooms);
                                            }
        
                                            $livewire->dispatch('close-modal', id: 'bathroom_changed_modal');
                                        }),
                                Action::make('Cancel')
                                        ->color('gray')
                                        ->action(function (Get $get, Set $set, $livewire) {
                                            $previous = $get('bathroom_count_previous');
                                            if ($previous !== null) {
                                                $set('bathroom_count', $previous);
                                            }
                                            $livewire->dispatch('close-modal', id: 'bathroom_changed_modal');
                                        }),
                                    ]),
                RoomsCountChangedModal::make('kitchen_changed_modal')
                                    ->heading('Decrease the number of kitchens')
                                    ->description('You are about to decrease the number of kitchens. This will result in the removal of excess items. Are you sure you want to proceed?')
                                    ->registerActions([
                                        Action::make('Confirm')
                                                ->color('success')
                                                ->action(function (Get $get, Set $set, $livewire) {
                                                    $kitchens = $get('kitchens') ?? [];
                                                    $count = (int) ($get('kitchen_count') ?? count($kitchens));
                                            
                                                    if (count($kitchens) > $count) {
                                                        $kitchens = array_slice($kitchens, 0, $count);
                                                        $set('kitchens', $kitchens);
                                                    }
                
                                                    $livewire->dispatch('close-modal', id: 'kitchen_changed_modal');
                                                }),
                                        Action::make('Cancel')
                                                ->color('gray')
                                                ->action(function (Get $get, Set $set, $livewire) {
                                                    $previous = $get('kitchen_count_previous');
                                                    if ($previous !== null) {
                                                        $set('kitchen_count', $previous);
                                                    }
                                                    $livewire->dispatch('close-modal', id: 'kitchen_changed_modal');
                                                }),
                                        ])
                ])
            ->icon(fn(Get $get) => PropertyResource::getTabIcon($get('tab_icon_rooms')))
            ->iconPosition(IconPosition::After);
    }

    protected static function getBedroomsGroup(): Section
    {       
        $itemLabel = fn(array $state): ?string =>
        sprintf(
            '%s%s%s',
            $state['name-label'] ?? 'Bedroom',
            ($state['name'] != '') ? ', ' . $state['name'] : '',
            ($state['type'] != '') ? ', ' . $state['type'] : '',
        );

        // Bedroom Count
        $bedroomCount = Select::make('bedroom_count')
            ->label(function () {
                $label = "Count bedrooms";
                $tooltip = view('custom-label-help', [
                    'tooltip' => 'Specify the total number of bedrooms. Maximum allowed is 20. Each bedroom will be labeled automatically (e.g., Bedroom 1, Bedroom 2).',
                    'position' => 'bottom',
                    'size' => 'large'
                ])->render();
                return new HtmlString($label . $tooltip);
            })
            ->options(collect(range(0, 20))->mapWithKeys(fn ($n) => [$n => $n === 0 ? 'No bedrooms' : $n]))
            ->default(0)
            ->reactive()
            ->live()
            ->dehydrated(false)
            ->afterStateUpdated(function (callable $set, callable $get, $old, $state, $livewire, $component) {
                if (is_numeric($old) && is_numeric($state) && $state < $old ) {
                    $set('bedroom_count_previous', $old); 
                    $livewire->dispatch('open-modal', id: 'bedroom_changed_modal');
                }else{
                    $bedrooms = $get('bedrooms') ?? [];
                    if (empty($state)) {
                        $count = count($bedrooms);
                    } else {
                        $count = min((int) $state, 20);
                    }
    
                    while (count($bedrooms) < $count) {
                        $index = count($bedrooms);
                        $bedrooms[$index] = [
                            'name'             => '',
                            'type'             => '',
                            'bunk_bed'         => 0,
                            'double_bed'       => 0,
                            'king_sized_bed'   => 0,
                            'queen_sized_bed'  => 0,
                            'single_bed_adult' => 0,
                            'single_bed_child' => 0,
                            'sofa_bed_double'  => 0,
                            'sofa_bed_single'  => 0,
                        ];
                    }
    
                    while (count($bedrooms) > $count) {
                        array_pop($bedrooms);
                    }                
    
                    $set('bedrooms', $bedrooms);
                    $set('bedroom_count', $count);
                    
                    // Removed validation calls to improve performance
                    // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                    // $livewire->validateOnly($component->getStatePath());
                }
                
            });

        $bedroomsRepeater = Repeater::make('bedrooms')
            ->label(false)
            // ->relationship('bedrooms')
            ->default([])
            ->schema([
                Hidden::make('name-label')
                    ->default('Bedroom'),
                Hidden::make('id'),

                TextInput::make('name')
                    ->label('Name')
                    ->live(true)
                    ->afterStateUpdated(function ($livewire, $component) {
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    ->dehydrateStateUsing(function($state, Get $get){
                        return !empty($state) ? $state : $get('name-label');
                    }),

                Select::make('type')
                    ->label('Type')
                    ->default('Bedroom')
                    ->live(true)
                    // ->formatStateUsing(fn ($state) => $state ?: 'Bedroom')
                    ->options([
                        'Bedroom' => 'Bedroom',
                        'Living room' => 'Living room',
                        'Other room' => 'Other room',
                    ])
                    ->afterStateUpdated(function ($livewire, $component) {
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    ->required(),

                Select::make('bunk_bed')
                    ->label('Bunk-bed')
                    ->options(collect(range(0, 20))->mapWithKeys(fn ($n) => [$n => $n === 0 ? 'No bunk bed' : $n]))
                    ->required()
                    ->default(0),
                Select::make('double_bed')
                    ->label('Double bed')
                    ->options(collect(range(0, 20))->mapWithKeys(fn ($n) => [$n => $n === 0 ? 'No double bed' : $n]))
                    ->required()
                    ->default(0),

                Select::make('king_sized_bed')
                    ->label('King-sized bed')
                    ->options(collect(range(0, 20))->mapWithKeys(fn ($n) => [$n => $n === 0 ? 'No king-sized bed' : $n]))
                    ->required()
                    ->default(0),

                Select::make('queen_sized_bed')
                    ->label('Queen-sized bed')
                    ->options(collect(range(0, 20))->mapWithKeys(fn ($n) => [$n => $n === 0 ? 'No queen-sized bed' : $n]))
                    ->required()
                    ->default(0),

                Select::make('single_bed_adult')
                    ->label('Single bed (adult)')
                    ->options(collect(range(0, 20))->mapWithKeys(fn ($n) => [$n => $n === 0 ? 'No single bed (adult)' : $n]))
                    ->required()
                    ->default(0),

                Select::make('single_bed_child')
                    ->label('Single bed (child)')
                    ->options(collect(range(0, 20))->mapWithKeys(fn ($n) => [$n => $n === 0 ? 'No single bed (child)' : $n]))
                    ->required()
                    ->default(0),
                Select::make('sofa_bed_double')
                    ->label('Sofa-bed (double)')
                    ->options(collect(range(0, 20))->mapWithKeys(fn ($n) => [$n => $n === 0 ? 'No sofa-bed (single)' : $n]))
                    ->required()
                    ->default(0),

                Select::make('sofa_bed_single')
                    ->label('Sofa-bed (single)')
                    ->options(collect(range(0, 20))->mapWithKeys(fn ($n) => [$n => $n === 0 ? 'No sofa-bed (single)' : $n]))
                    ->required()
                    ->default(0),
            ])
            ->columns(2)
            ->collapsible()
            ->collapsed()
            ->grid(1)
            ->reactive()
            ->formatStateUsing(function($state, $record, Set $set){
                if($record && count($record->bedrooms) > 0){
                    $set('bedroom_count', count($record->bedrooms));
                    return $record->bedrooms;
                }
                return $state;
            })
            ->afterStateUpdated(function (callable $set, $state, $old,  $livewire, $component) {
                if (is_array($state)) {
                    $indexState = 1;
                    foreach ($state as $index => $item) {
                        $state[$index]['name-label'] = 'Bedroom ' . ($indexState);
                        $indexState++;
                    }
                    $set('bedrooms', $state);
                }
                $set('bedroom_count', count($state ?? []));

                // Removed validation calls to improve performance
                // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                // $livewire->validateOnly($component->getStatePath());
            })
            ->itemLabel($itemLabel)
            ->disableItemCreation();

        return Section::make([
            $bedroomCount,
            $bedroomsRepeater
        ])
            ->description('Bedrooms');
    }

    protected static function getBathroomsGroup(): Section
    {
        $itemLabel = fn(array $state): ?string =>
        sprintf(
            '%s%s%s',
            $state['name-label'] ?? 'Bathroom',
            ($state['name'] != '') ? ', ' . $state['name'] : '',
            ($state['bathroom_type'] != '') ? ', ' . $state['bathroom_type'] : '',
        );

        $bathroomCount = Select::make('bathroom_count')
            ->label(function () {
                $label = "Count bathroom";
                $tooltip = view('custom-label-help', [
                    'tooltip' => 'Specify the total number of bathrooms. Maximum allowed is 20. Each bathroom will be labeled automatically (e.g., Bathroom 1, Bathroom 2).',
                    'position' => 'bottom',
                    'size' => 'large'
                ])->render();
                return new HtmlString($label . $tooltip);
            })
            ->options(collect(range(0, 20))->mapWithKeys(fn ($n) => [$n => $n === 0 ? 'No bathrooms' : $n]))
            ->default(0)
            // ->mask('999')
            ->extraInputAttributes(['min' => '0', 'max' => '20'])
            ->reactive()
            ->live()
            ->dehydrated(false)
            ->afterStateUpdated(function (callable $set, callable $get, $old, $state, $livewire, $component) {
                if (is_numeric($old) && is_numeric($state) && $state < $old ) {
                    $set('bathroom_count_previous', $old); 
                    $livewire->dispatch('open-modal', id: 'bathroom_changed_modal');
                }else{
                    $bathrooms = $get('bathrooms') ?? [];
                    if (empty($state)) {
                        $count = count($bathrooms);
                    } else {
                        $count = min((int) $state, 20);
                    }
                    while (count($bathrooms) < $count) {
                        $index = count($bathrooms);
                        $bathrooms[$index] = [
                            'name'             => '',
                            'private'          => false,
                            'bathroom_type'    => '',
                            'toilet'           => '',
                            'shower'           => '',
                            'bath'             => '',
                        ];
                    }
    
                    while (count($bathrooms) > $count) {
                        array_pop($bathrooms);
                    }
    
                    $set('bathrooms', $bathrooms);
                    $set('bathroom_count', $count);
                    
                    PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                    $livewire->validateOnly($component->getStatePath());
                }
            });

        $bathroomsRepeater = Repeater::make('bathrooms')
            ->label(false)
            // ->relationship('bathrooms')
            ->default([])
            ->schema([
                Hidden::make('name-label')
                    ->default('Bathroom'),
                Hidden::make('id'),

                TextInput::make('name')
                    ->label('Name')
                    ->live(true)
                    ->dehydrateStateUsing(function($state, Get $get){
                        return !empty($state) ? $state : $get('name-label');
                    }),

                Toggle::make('private')
                    ->label('Private (Not shared with host or other guests)')
                    ->default(false),

                Select::make('bathroom_type')
                    ->label('Bathroom type')
                    ->options([
                        'En-suite bathroom' => 'En-suite bathroom',
                        'Full bathroom' => 'Full bathroom',
                        'WC' => 'WC',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    // ->default('En-suite bathroom')
                    // ->formatStateUsing(fn ($state) => $state ?: 'En-suite bathroom')
                    ->required(),

                Select::make('toilet')
                    ->label('Toilet')
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    ->options([
                        'No toilet' => 'No toilet',
                        'Toilet' => 'Toilet',
                    ])
                    // ->default('No toilet')
                    // ->formatStateUsing(fn ($state) => $state ?: 'No toilet')
                    ->required(),

                Select::make('shower')
                    ->label('Shower')
                    ->options([
                        'No shower' => 'No shower',
                        'Separate shower' => 'Separate shower',
                        'Shower over bath' => 'Shower over bath',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    // ->default('No shower')
                    // ->formatStateUsing(fn ($state) => $state ?: 'No shower')
                    ->required(),

                Select::make('bath')
                    ->label('Bath')
                    ->options([
                        'Jacuzzi' => 'Jacuzzi',
                        'No bath' => 'No bath',
                        'Standard bath' => 'Standard bath',
                        'Whirlpool' => 'Whirlpool',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    // ->default('Jacuzzi')
                    // ->formatStateUsing(fn ($state) => $state ?: 'Jacuzzi')
                    ->required(),
            ])
            ->columns(2)
            ->collapsible()
            ->collapsed()
            ->grid(1)
            ->reactive()
            ->formatStateUsing(function($state, $record, Set $set){
                if($record && count($record->bathrooms) > 0){
                    $set('bathroom_count', count($record->bathrooms));
                    return $record->bathrooms;
                }
                return $state;
            })
            ->afterStateUpdated(function (callable $set, $state, $livewire, $component) {
                if (is_array($state)) {
                    $indexState = 1;
                    foreach ($state as $index => $item) {
                        $state[$index]['name-label'] = 'Bathroom ' . ($indexState);
                        $indexState++;
                    }
                    $set('bathrooms', $state);
                }
                $set('bathroom_count', count($state ?? []));

                // Removed validation calls to improve performance
                // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                // $livewire->validateOnly($component->getStatePath());
            })
            ->itemLabel($itemLabel)
            ->disableItemCreation();


        return Section::make([
            $bathroomCount,
            $bathroomsRepeater,
        ])
            ->description('Bathrooms');
    }

    protected static function getKitchensGroup(): Section
    {
        $itemLabel = fn(array $state): ?string => 
        sprintf(
            '%s%s%s',
            $state['name-label'] ?? 'Kitchen',
            ($state['name'] != '') ? ', ' . $state['name'] : '',
            ($state['type'] != '') ? ', ' . $state['type'] : '',
        );

        $kitchenCount = Select::make('kitchen_count')
            ->label(function () {
                $label = "Count kitchens";
                $tooltip = view('custom-label-help', [
                    'tooltip' => 'Specify the total number of kitchens. Maximum allowed is 20. Each kitchen will be labeled automatically (e.g., Kitchen 1, Kitchen 2).',
                    'position' => 'bottom',
                    'size' => 'large'
                ])->render();
                return new HtmlString($label . $tooltip);
            })
            ->options(collect(range(0, 20))->mapWithKeys(fn ($n) => [$n => $n === 0 ? 'No kitchen' : $n]))
            ->default(0)
            ->live()
            ->reactive()
            ->extraInputAttributes(['min' => '0', 'max' => '20'])
            ->reactive()
            ->dehydrated(false)
            ->afterStateUpdated(function (callable $set, callable $get, $old, $state, $livewire, $component) {
                if (is_numeric($old) && is_numeric($state) && $state < $old ) {
                    $set('kitchen_count_previous', $old); 
                    $livewire->dispatch('open-modal', id: 'kitchen_changed_modal');
                }else{
                    $kitchens = $get('kitchens') ?? [];
                    if (empty($state)) {
                        $count = count($kitchens);
                    } else {
                        $count = min((int) $state, 5);
                    }
                    while (count($kitchens) < $count) {
                        $index = count($kitchens);
                        $kitchens[$index] = [
                            'name'    => '',
                            'type'    => '',
                        ];
                    }

                    while (count($kitchens) > $count) {
                        array_pop($kitchens);
                    }

                    $set('kitchens', $kitchens);
                    $set('kitchen_count', $count);
                    
                    // Removed validation calls to improve performance
                    // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                    // $livewire->validateOnly($component->getStatePath());
                }
            });

        $kitchensRepeater = Repeater::make('kitchens')
            ->label('')
            // ->relationship('kitchens')
            ->default([])
            ->schema([
                Hidden::make('name-label')
                    ->default('Kitchen'),
                Hidden::make('id'),

                TextInput::make('name')
                    ->label('Name')
                    ->live(true)
                    ->dehydrateStateUsing(function($state, Get $get){
                        return !empty($state) ? $state : $get('name-label');
                    }),

                Select::make('type')
                    ->label('Type')
                    ->options([
                        'Kitchenette' => 'Kitchenette',
                        'Open plan kitchen' => 'Open plan kitchen',
                        'Outdoor kitchen' => 'Outdoor kitchen',
                        'Separate kitchen' => 'Separate kitchen',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    ->default('Kitchenette')
                    ->formatStateUsing(fn ($state) => $state ?: 'Kitchenette')
                    ->required(),
            ])
            ->columns(2)
            ->collapsible()
            ->collapsed()
            ->grid(1)
            ->reactive()
            ->formatStateUsing(function($state, $record, Set $set){
                if($record && count($record->kitchens) > 0){
                    $set('kitchen_count', count($record->kitchens));
                    return $record->kitchens;
                }
                return $state;
            })
            ->afterStateUpdated(function (callable $set, $state, $livewire, $component) {
                if (is_array($state)) {
                    $indexState = 1;
                    foreach ($state as $index => $item) {
                        $state[$index]['name-label'] = 'Kitchen ' . ($indexState);
                        $indexState++;
                    }
                    $set('kitchens', $state);
                }
                $set('kitchen_count', count($state ?? []));

                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
				$livewire->validateOnly($component->getStatePath());
            })
            ->itemLabel($itemLabel)
            ->disableItemCreation();

        return Section::make([
            $kitchenCount,
            $kitchensRepeater,
        ])
            ->description('Kitchens');
    }



    protected static function getOtherRoomsGroup(): Section
    {
        $toggleLabels = [
            'common_area' => 'Common area',
            'dining_room' => 'Dining room',
            'drying_room' => 'Drying room',
            'eating_area' => 'Eating area',
            'fitness_room' => 'Fitness room',
            'games_room' => 'Games room',
            'hall' => 'Hall',
            'laundry' => 'Laundry',
            'library' => 'Library',
            'living_room' => 'Living room',
            'lounge' => 'Lounge',
            'office' => 'Office',
            'pantry' => 'Pantry',
            'rumpus_room' => 'Rumpus room',
            'sauna' => 'Sauna',
            'studio' => 'Studio',
            'study' => 'Study',
            'tv_room' => 'TV room',
            'work_studio' => 'Work studio',
        ];

        $itemLabel = fn(array $state): ?string =>
        count(array_filter($state, fn($value) => $value === true)) > 0
            ? implode(', ', array_map(
                fn($key) => $toggleLabels[$key],
                array_keys(array_filter($state, fn($value) => $value === true))
            ))
            : 'No other rooms';


        $otherRoomsRepeater = Repeater::make('other_rooms')
            ->label('')
            ->relationship('other_rooms')
            ->schema([
                Toggle::make('common_area')->label('Common area'),
                Toggle::make('dining_room')->label('Dining room'),
                Toggle::make('drying_room')->label('Drying room'),
                Toggle::make('eating_area')->label('Eating area'),
                Toggle::make('fitness_room')->label('Fitness room'),
                Toggle::make('games_room')->label('Games room'),
                Toggle::make('hall')->label('Hall'),
                Toggle::make('laundry')->label('Laundry'),
                Toggle::make('library')->label('Library'),
                Toggle::make('living_room')->label('Living room'),
                Toggle::make('lounge')->label('Lounge'),
                Toggle::make('office')->label('Office'),
                Toggle::make('pantry')->label('Pantry'),
                Toggle::make('rumpus_room')->label('Rumpus room'),
                Toggle::make('sauna')->label('Sauna'),
                Toggle::make('studio')->label('Studio'),
                Toggle::make('study')->label('Study'),
                Toggle::make('tv_room')->label('TV room'),
                Toggle::make('work_studio')->label('Work studio'),
            ])
            ->columns(2)
            ->defaultItems(1)
            ->grid(1)
            ->reactive()
            ->itemLabel($itemLabel)
            ->disableItemCreation()
            ->disableItemDeletion()
            ->afterStateUpdated(function (Set $set, $state, $livewire, $component) { 
				PropertyResource::validateTabsAction($livewire, self::$tabTitle);
				$livewire->validateOnly($component->getStatePath());
            })
            ->afterStateHydrated(function (callable $set, $state) {
                if (empty($state)) {
                    $set('other_rooms', [
                        [
                            'common_area'  => false,
                            'dining_room'  => false,
                            'drying_room'  => false,
                            'eating_area'  => false,
                            'fitness_room' => false,
                            'games_room'   => false,
                            'hall'         => false,
                            'laundry'      => false,
                            'library'      => false,
                            'living_room'  => false,
                            'lounge'       => false,
                            'office'       => false,
                            'pantry'       => false,
                            'rumpus_room'  => false,
                            'sauna'        => false,
                            'studio'       => false,
                            'study'        => false,
                            'tv_room'      => false,
                            'work_studio'  => false,
                        ]
                    ]);
                }
            });

        return Section::make([
            $otherRoomsRepeater,
        ])
            ->description('Other Rooms');
    }
}
