<?php

namespace App\Filament\Forms\Components;

use App\Filament\Resources\PropertyResource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\IconPosition;
use App\Models\PropertySites;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\HtmlString;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;
use App\Services\PlanyoService;
use Filament\Forms;
use Filament\Notifications\Notification;
use App\Models\PropertySync;

use App\Models\PropertyCalendarSynchronization;


class PropertySynchonizationFields
{
    private static $tabTitle = 'Synchronisation';

    public static function create(): Tab
    {
        return Tabs\Tab::make(self::$tabTitle)
            ->icon(fn(Get $get) => PropertyResource::getTabIcon($get('tab_icon_synchronisation')))
            ->iconPosition(IconPosition::After)
            ->schema([

                Section::make("Planyo")
                    ->dehydrated(false)
                    ->statePath('planyo_sync')
                    ->schema([
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('Sync with Planyo')
                                ->action(function (Set $set, Get $get, ?\App\Models\Property $record) {

                                    if (!isset($record->id)) {
                                        Notification::make()
                                            ->title('Error')
                                            ->danger()
                                            ->body('You should save the property before syncing.')
                                            ->send();

                                        return;
                                    }

                                    $planyoService = new PlanyoService();
                                    $sync = $record->synchronisation()->first();
                                    $planyoResourceId = null;
                                    if ($sync) {
                                        $planyoResourceId = $sync->getSiteIdBySiteName('Planyo');
                                    }


                                    $basePropertyId = 0;
                                    $propertySite = PropertySites::where('site', 'Planyo')->first();
                                    if ($propertySite) {
                                        $basePropertyId = $propertySite->default_property_id;
                                    }

                                    $args = [
                                        'base_resource_id' => $basePropertyId,
                                        'name' => $get('title') ?? "Noname " . $record->id,
                                        'quantity' => 1,
                                        'description' => $get('description'),
                                        'min_rental_time' => $get('min_stay_nights'),
                                        'max_rental_time ' => $get('max_stay_nights'),
                                        'language' => 'EN',

                                        'prop_res_number_of_persons' => $get('max_guests'),
                                        'prop_res_bedrooms' => $get('bedroom_count'),
                                        'prop_res_location' => implode(', ', array_filter([
                                            $get('city'),
                                            $get('state_or_region'),
                                            $get('country')
                                        ])),
                                        'prop_res_swimming_pool' => $record->hasAttributeValue('Amenities', ['swimming_pool_private', 'swimming_pool_shared', 'swimming_pool_heating']) ? 'yes' : 'no',
                                        'prop_res_pets_allow' => $get('pets') ? 'yes' : 'no',
                                        'prop_res_gps_coords' => sprintf('%s, %s', $get('latitude'), $get('longitude')),
                                        'prop_res_url' => '',
                                        'prop_res_distance_to_the_beach__maximum_meters_' => $record->getAttributeValueByName('Beach distance'),
                                        'prop_res_distance_to_infrastructions__maximum_km_' => $record->getAttributeValueByName('Infrastructure distance'),
                                        'prop_res_detail_page' => '',
                                        'prop_res_property_owner' => '',
                                        'prop_res_url_hoscape_com' => '',
                                        'prop_res_url_proposedproperty_info' => '',
                                        'prop_res_url_vacaysphere_com' => '',
                                        'prop_res_address' => implode(', ', array_filter([
                                            $get('country'),
                                            $get('state_or_region'),
                                            $get('city'),
                                            $get('street'),
                                            $get('address'),
                                        ])),
                                        //'prop_res_description' => $get('short_summary'),

                                        //TODO: check sunc
                                        // 'prop_res_hidden_flex_admin_duration' => 1,
                                        // 'prop_res_hidden_login_required' => 1,
                                        // 'prop_res_hidden_special_url_access' => 1,
                                        // 'prop_res_hidden_waitlist' => 1,
                                        // 'prop_res_hidden_waitlist_max' => 1,
                                        // 'prop_res_incompatible_product' => 25549,

                                        'prop_res_parking' => $record->hasAttributeValue('Amenities', ['free_parking', 'free_parking_secured']) ? 'yes' : 'no',
                                        'prop_res_wifi' => $record->hasAttributeValue('Amenities', ['free_wifi', 'high_speed_wifi']) ? 'yes' : 'no',


                                        // 'time_unit' => 1440,
                                        // 'sharing_mode' => 0,
                                        // 'start_hour' => 0,
                                        // 'end_hour' => 20,
                                        // 'start_quarters' => 0,
                                        // 'unit_price' => 100,
                                        // 'price_type' => 0,
                                        // 'is_overnight_stay' => 0,
                                        // 'max_quantity_per_rental' => 1,
                                        'is_published' => true,
                                        'is_listed' => true,
                                        // 'start_times' => '',
                                        // 'max_days_to_rental' => 366,
                                        // 'event_dates' => '',
                                        // 'resource_admin_id' => '',
                                        // 'resource_admin_name' => '',
                                        // 'resource_admin_email' => '',
                                        'min_time_between_rentals' => 11,
                                        // 'prepayment_amount' => '40%',
                                        // 'prepayment_amount_valid_until' => 0,
                                        // 'images' => ''
                                    ];

                                    $advanceNoticeMapping = [
                                        '1_day' => 24,
                                        '2_days' => 48,
                                        '3_days' => 72,
                                        '5_days' => 120,
                                        '7_days' => 168,
                                    ];

                                    $minHoursToRental = $get('advance_booking_notice') && isset($advanceNoticeMapping[$get('advance_booking_notice')])
                                        ? $advanceNoticeMapping[$get('advance_booking_notice')]
                                        : null;

                                    if ($minHoursToRental !== null) {
                                        $args['min_hours_to_rental'] = $minHoursToRental;
                                    }

                                    if ($planyoResourceId) {
                                        $args['resource_id'] = $planyoResourceId;
                                        $response = $planyoService->modifyResource($args);
                                    } else {
                                        $response = $planyoService->addResource($args);
                                    }

                                    if ($response['response_code'] === 3) {
                                        Notification::make()
                                            ->title('Error')
                                            ->danger()
                                            ->body($response['response_message'])
                                            ->send();

                                        $set('sync_result', $response['response_message']);

                                        return;
                                    }

                                    if ($response['response_code'] === 0) {

                                        if (isset($response['data']['new_resource_id'])) {
                                            $planyoResourceId = $response['data']['new_resource_id'];
                                            //$record->update(['planyo_resource_id' => $planyoResourceId]);

                                            $newState = array_map(function ($site) use ($planyoResourceId) {
                                                return [
                                                    'synchronization_id' => $site['synchronization_id'],
                                                    'site' => $site['site'],
                                                    'url' => $site['url'],
                                                    'site_id' => $site['site'] === 'Planyo' ? $planyoResourceId : $site['site_id'],
                                                ];
                                            }, $get('synchronisation'));

                                            $set('synchronisation', $newState);
                                        }

                                        if (isset($response['data'])) {
                                            $set('sync_result', print_r($response['data'], true));
                                        }
                                    }


                                    //add/update resource images
                                    if ($planyoResourceId) {
                                        $imagesToDelete = [];
                                        $imagesToAdd = [];

                                        //Get planyo photos list
                                        $response = $planyoService->getResourceInfo($planyoResourceId);

                                        $planyoImages = isset($response['data']['photos']) ? $response['data']['photos'] : [];

                                        $planyoImageIds = array_map('intval', array_keys($planyoImages));

                                        $localImages  = [];

                                        $primaryImage = $record->getMedia('properties')->first();

                                        if ($primaryImage) {
                                            $localImages[] = intval($primaryImage->getCustomProperty('planyo_id'));
                                        }

                                        $mediaItems = $record->getMedia('properties-gallery');
                                        foreach ($mediaItems as $item) {
                                            $localImages[] = intval($item->getCustomProperty('planyo_id'));
                                        }

                                        //Images to delete
                                        $imagesToDelete = array_diff($planyoImageIds, $localImages);

                                        //Images to add
                                        if ($primaryImage) {
                                            $primaryPlanyoId = intval($primaryImage->getCustomProperty('planyo_id'));
                                            if (!$primaryPlanyoId || !in_array($primaryPlanyoId, $planyoImageIds, true)) {
                                                $imagesToAdd[] = $primaryImage;
                                            }
                                        }

                                        foreach ($mediaItems as $item) {
                                            $planyoId = $item->getCustomProperty('planyo_id');
                                            if (!$planyoId || !in_array(intval($planyoId), $planyoImageIds, true)) {
                                                $imagesToAdd[] = $item;
                                            }
                                        }


                                        foreach ($imagesToDelete as $imageId) {
                                            $planyoService->deleteResourceImage($imageId);
                                        }

                                        //Add image
                                        foreach ($imagesToAdd as $item) {
                                            $response = $planyoService->addResourceImage($planyoResourceId, $item->getUrl());
                                            if ($response && $response['response_code'] === 0 && isset($response['data']['id'])) {
                                                $item->setCustomProperty('planyo_id', $response['data']['id']);
                                                $item->save();
                                            }
                                        }
                                    }

                                    //add/update Ical feed url
                                    if ($planyoResourceId) {
                                        $params = ['resource_id' => $planyoResourceId];
                                        $response = $planyoService->getIcal($params);

                                        if ($response['response_code'] === 0 && isset($response['data']['url'])) {
                                            $icalUrl = $response['data']['url'];

                                            $record->update(['export_ical_url' => $icalUrl]);

                                            $set('export_ical_url', $icalUrl);
                                            //$set('sync_result', print_r($response['data'], true));
                                        }
                                    }

                                    // if ($planyoResourceId) {
                                    //     $response = $planyoService->getSeasons(0);

                                    //     dd($response);

                                    //     if ($response['response_code'] === 0 && isset($response['data'])) {
                                    //         $set('sync_result', print_r($response['data'], true));
                                    //     }

                                    // }

                                }),

                            Forms\Components\Actions\Action::make('Get properties list')
                                ->action(function (Set $set, Get $get) {
                                    $planyoService = new PlanyoService();
                                    $response = $planyoService->getListResources();
                                    dd($response);
                                    if ($response['response_code'] === 0) {
                                        $set('sync_result', print_r($response['data'], true));
                                    }
                                }),

                            Forms\Components\Actions\Action::make('Get Data')
                                ->action(function (Set $set, Get $get, ?\App\Models\Property $record) {

                                    if (!isset($record->id)) {
                                        Notification::make()
                                            ->title('Error')
                                            ->danger()
                                            ->body('You should save the property before syncing.')
                                            ->send();

                                        return;
                                    }

                                    $planyoService = new PlanyoService();
                                    $planyoResourceId = $record->synchronisation()->first()->getSiteIdBySiteName('Planyo');
                                    dd($planyoService);
                                    $response = $planyoService->getResourceInfo($planyoResourceId);

                                    // dd($response);
                                    if ($response['response_code'] === 0) {
                                        $set('sync_result', print_r($response['data'], true));
                                    }
                                }),

                        ]),

                        \Filament\Forms\Components\Textarea::make('sync_result')
                            ->dehydrated(false)
                            ->label('Sync Result')
                            ->disabled(),

                    ]),

                TableRepeater::make('synchronisation')
                    // ->relationship('synchronisation')
                    ->headers([
                        Header::make('site')->label('Site')->width('100px'),
                        Header::make('url')->label('URL')->width('200px'),
                        Header::make('source_id')->label('Source ID')->width('100px'),
                    ])
                    ->deletable(false)
                    ->addable(false)
                    ->live(true)
                    ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    })
                    ->schema([
                        Hidden::make('synchronization_id'),

                        TextInput::make('site')
                            ->label('Site')
                            ->disabled(),

Placeholder::make('url_display')
    ->label(false)
    ->dehydrated(false)
    ->content(function (callable $get) {
        $url = $get('url');
        return new HtmlString('
            <div x-data="{ copied: false }" wire:ignore>
                <div class="relative">
                    <input
                        type="text"
                        value="' . e($url) . '"
                        readonly
                        x-ref="urlInput"
                        class="w-full rounded-lg border-gray-300 shadow-sm pr-10"
                    />
                    <button
                        type="button"
                        class="absolute top-1/2 right-2 -translate-y-1/2 text-orange-500 hover:text-orange-600"
                        x-on:click="
                            navigator.clipboard.writeText($refs.urlInput.value);
                            copied = true;
                            setTimeout(() => copied = false, 1500);
                        "
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16h8m-4-4h4m-4-4h4m2 8a2 2 0 01-2 2H8a2 2 0 01-2-2V6a2 2 0 012-2h6l4 4v10z"/>
                        </svg>
                    </button>
                </div>
                <div x-show="copied" x-transition class="mt-1 text-sm text-green-600">Copied!</div>
            </div>
        ');
    }),


                        TextInput::make('site_id')
                    ])
                    ->columnSpan('full')
                    ->afterStateHydrated(function (Set $set, $state, $component) {
                        $sites = PropertySites::all();
                        $record = $component->getRecord();
                        $existingSync = $record && $record->id
                            ? PropertySync::where('property_id', $record->id)
                            ->get()
                            ->keyBy('synchronization_id')
                            : collect();
                        $newState = $sites->map(function ($site) use ($existingSync, $record) {
                            return [
                                'synchronization_id' => $site->id,
                                'site' => $site->site,
                                'url' => $record && $record->id ? ($existingSync[$site->id]->url ?? null) : null,
                                'site_id' => $record && $record->id ? ($existingSync[$site->id]->site_id ?? null) : null,
                            ];
                        })->toArray();
                        
                        $set('synchronisation', $newState);
                    }),

                // TextInput::make('url_for_site_presentation')
                //     ->label('URL for site presentation')
                //     ->url()
                //     ->readonly()
                //     ->suffixAction(
                //         Action::make('copy')
                //             ->icon('heroicon-s-clipboard-document-check')
                //             ->action(function ($livewire, $state) {
                //                 $livewire->js(
                //                     'window.navigator.clipboard.writeText(document.getElementById("data.url_for_site_presentation").value);
                //                         $tooltip("' . __('Copied to clipboard') . '", { timeout: 1500 });'
                //                 );
                //             })
                //     ),

                Section::make("Import Calendars")
                    ->schema([
                        AlertBox::make()
                            ->helperText('Please note: Having external calendars will disable the channel to avoid double bookings')
                            ->extraAttributes([
                                'class' => 'bg-transparent border !border-gray-800 dark:!border-gray-100 !text-gray-800 dark:!text-gray-100',
                            ])
                            ->resolveIconUsing(name: 'heroicon-o-information-circle'),

                        TableRepeater::make('calendar')
                            ->label(false)
                            ->formatStateUsing(function ($state, $component) {
                                $record = $component->getRecord();

                                if (!$record || !$record->id) {
                                    return [];
                                }
                                $calendar = PropertyCalendarSynchronization::where('property_id', $record->id)->get();

                                if ($calendar->isNotEmpty()) {
                                    return $state ?: $calendar->toArray();
                                }
                                return $state ?: [];
                            })
                            ->reorderable(false)
                            ->headers([
                                Header::make('source')->label('Calendar source'),
                                Header::make('url')->label('Calendar URL'),
                                Header::make('name')->label('Calendar name'),
                            ])
                            ->schema([
                                Placeholder::make('source_placeholder')
                                    ->label(false)
                                    ->content(fn(Get $get) => PropertySites::getAllSites()->get($get('calendar_source'))),
                                Placeholder::make('url_placeholder')
                                    ->label(false)
                                    ->content(fn(Get $get) => $get('calendar_url')),
                                Placeholder::make('name_placeholder')
                                    ->label(false)
                                    ->content(fn(Get $get) => $get('calendar_name'))
                            ])
                            ->live(true)
                            ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                            ->extraItemActions([
                                Action::make('Edit')
                                    ->icon('heroicon-s-pencil')
                                    ->modalHeading('Edit calendar')
                                    ->fillForm(function (array $arguments, Repeater $component): array {
                                        $allItems = $component->getState();
                                        $currentKey = $arguments['item'];
                                        return $allItems[$currentKey] ?? [];
                                    })
                                    ->form([

                                        AlertBox::make()
                                            ->helperText('Choose the source of your calendar, then paste the unique URL (often ending .ICS)')
                                            ->extraAttributes([
                                                'class' => 'bg-transparent border !border-gray-800 dark:!border-gray-100 !text-gray-800 dark:!text-gray-100',
                                            ])
                                            ->resolveIconUsing(name: 'heroicon-o-information-circle'),

                                        Select::make('calendar_source')
                                            ->label('Calendar source')
                                            ->options(function ($state, $component, Get $get) {
                                                $sites = PropertySites::getAllSites();
                                                $data = $get('../../data.calendar');
                                                $stateCalendarSources = collect($data)->pluck('calendar_source')->toArray();

                                                $filteredCalendarSources = collect($stateCalendarSources)->filter(fn($CalendarSources) => $CalendarSources !== $state)->toArray();

                                                return collect($sites)
                                                    ->except($filteredCalendarSources)
                                                    ->toArray();
                                            })
                                            ->required(),

                                        TextInput::make('calendar_url')
                                            ->label('Calendar URL')
                                            ->required()
                                            ->type('url'),
                                        TextInput::make('calendar_name')
                                            ->label('Calendar name')
                                            ->required()
                                            ->type('text'),

                                    ])
                                    ->action(function (array $arguments, array $data, $component, Set $set, Get $get): void {
                                        $mainState = $component->getState();
                                        $key = $arguments['item'];
                                        $mainState[$key] = $data;

                                        $component->state($mainState);
                                    })
                            ])
                            ->addActionLabel('Add Import calendar')
                            ->addAction(function ($action) {
                                return $action->form([
                                    AlertBox::make()
                                        ->helperText('Choose the source of your calendar, then paste the unique URL (often ending .ICS)')
                                        ->extraAttributes([
                                            'class' => 'bg-transparent border !border-gray-800 dark:!border-gray-100 !text-gray-800 dark:!text-gray-100',
                                        ])
                                        ->resolveIconUsing(name: 'heroicon-o-information-circle'),

                                    Select::make('calendar_source')
                                        ->label('Calendar source')
                                        ->options(function ($state, $component, Get $get) {
                                            $sites = PropertySites::getAllSites();
                                            $data = $get('../../data.calendar');
                                            $stateCalendarSources = collect($data)->pluck('calendar_source')->toArray();

                                            $filteredCalendarSources = collect($stateCalendarSources)->filter(fn($CalendarSources) => $CalendarSources !== $state)->toArray();

                                            return collect($sites)
                                                ->except($filteredCalendarSources)
                                                ->toArray();
                                        })
                                        ->required(),

                                    TextInput::make('calendar_url')
                                        ->label('Calendar URL')
                                        ->required()
                                        ->type('url'),
                                    TextInput::make('calendar_name')
                                        ->label('Calendar name')
                                        ->required()
                                        ->type('text'),

                                ])
                                    ->action(function ($data, Set $set, Get $get) {
                                        $currentState = $get('calendar') ?? [];
                                        $result = array_merge($currentState, [$data]);
                                        $set('calendar', $result);
                                    });
                            })
                            ->deleteAction(
                                fn(Action $action) => $action->requiresConfirmation(),
                            )
                    ]),
                Section::make("Export Calendar")
                    ->statePath('export_ical')
                    ->schema([
                        AlertBox::make()
                            ->helperText('Please note: Export your calendar to sync on another calendar or booking website. Copy the URL below and paste the link into other services you use.')
                            ->extraAttributes([
                                'class' => 'bg-transparent border !border-gray-800 dark:!border-gray-100 !text-gray-800 dark:!text-gray-100',
                            ])
                            ->resolveIconUsing(name: 'heroicon-o-information-circle'),

                        Toggle::make('allow_external_ical')
                            ->default(false)
                            ->label(function () {
                                $label = "Allow external iCal events to be exported";
                                $tooltip = view('custom-label-help', [
                                    'tooltip' => 'Warning: this feature can cause duplicate events to be imported which may lead to your calendar not functioning correctly in some cases. If you enable this, it is advised to monitor your calendar on a daily basis and report any issues you experience',
                                ])->render();
                                return new HtmlString($label . $tooltip);
                            })
                            ->validationAttribute('Title'),

                       TextInput::make('export_ical_url')
    ->label('Calendar URL (iCal)')
    ->dehydrated(false)
    ->extraAttributes([
        'x-data' => '{ copied: false }',
        'x-init' => '
            let input = $el.querySelector("input");
            if (input) {
                input.setAttribute("x-ref", "icalInput");
                input.setAttribute("readonly", true);
                input.setAttribute("wire:ignore", "");
            }
        ',
    ])
    ->suffix(
        fn () => new HtmlString('
            <button
                type="button"
                class="ml-2 text-orange-500 hover:text-orange-600 transition"
                x-on:click="
                    const input = $el.closest(\'[x-data]\').querySelector(\'[x-ref=icalInput]\');
                    if (input) {
                        navigator.clipboard.writeText(input.value);
                        copied = true;
                        $tooltip(\'Copied to clipboard\', { timeout: 1500 });
                        setTimeout(() => copied = false, 1500);
                    }
                "
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16h8m-4-4h4m-4-4h4m2 8a2 2 0 01-2 2H8a2 2 0 01-2-2V6a2 2 0 012-2h6l4 4v10z"/>
                </svg>
            </button>
            <div x-show="copied" x-transition class="mt-1 text-sm text-green-600">Copied!</div>
        ')
    ),
                    ]),

            ]);
    }
}
