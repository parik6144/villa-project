<?php

namespace App\Filament\Pages;

use App\Http\Middleware\VerifyIsAdmin;
use App\Models\PropertySites;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\PropertySetting;
use App\Models\BasicRateCommission;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Illuminate\Support\Facades\DB;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Get;
use Filament\Forms\Set;
use League\Config\Exception\ValidationException;
use App\Models\Property;
use App\Models\LicenceType;
use App\Models\PropertySitesContent;
use App\Models\PropertySync;
use App\Models\AdditionalLicenceType;
use App\Models\S3Setting;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\HtmlString;

class PropertySettings extends Page implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $data = null;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $slug = 'property-settings';

    protected static string $view = 'filament.pages.property-settings';

    public static function getRouteMiddleware(\Filament\Panel $panel): array
    {
        return array_merge(parent::getRouteMiddleware($panel), [
            'auth',
            VerifyIsAdmin::class,
        ]);
    }

    public function getTitle(): string
    {
        return 'Settings';
    }

    public function mount(): void
    {
        $settings = PropertySetting::first();
        $s3Settings = S3Setting::first();

        $this->data = [];

        $licenceTypes = LicenceType::all()->map(function ($licenceType) {
            return [
                'id' => $licenceType->id,
                'licence_type' => $licenceType->name,
            ];
        })->toArray();

        $addLicenceTypes = AdditionalLicenceType::all()->map(function ($addLicenceType) {
            return [
                'id' => $addLicenceType->id,
                'add_licence_type' => $addLicenceType->name,
                'add_licence_is_required' => array_values(array_filter([
                    $addLicenceType->required ? 'add_licence_required' : null,
                ])),
                'add_licence_hint' => $addLicenceType->hint,
                'add_licence_is_file_attachment' => array_values(array_filter([
                    $addLicenceType->file_attachment ? 'add_licence_file_attachment' : null,
                ])),
                'add_licence_type_operation' => array_values(array_filter([
                    $addLicenceType->sale ? 'add_licence_sale' : null,
                    $addLicenceType->short_rent ? 'add_licence_short_rent' : null,
                    $addLicenceType->monthly_rent ? 'add_licence_month_rent' : null,
                ])),
            ];
        })->toArray();

        $this->data = [
            'basic_rate_commission' => BasicRateCommission::all()->toArray(),
            'licence_types' => $licenceTypes,
            'add_licence_types' => $addLicenceTypes,
            'sites' => PropertySites::all()->toArray(),
        ];

        if ($settings) {
            $this->data['google_map_api_key'] = $settings->google_map_api_key;
        }

        if ($settings) {
            $this->data['async_period_minutes'] = $settings->async_period_minutes;
        }

        if ($s3Settings) {
            $this->data['key'] = $s3Settings->key;
            $this->data['secret'] = $s3Settings->secret;
            $this->data['token'] = $s3Settings->token;
            $this->data['region'] = $s3Settings->region;
            $this->data['bucket'] = $s3Settings->bucket;
            $this->data['endpoint'] = $s3Settings->endpoint;
            $this->data['url'] = $s3Settings->url;
            $this->data['visibility'] = $s3Settings->visibility;
            $this->data['use_path_style_endpoint'] = $s3Settings->use_path_style_endpoint;
            $this->data['throw'] = $s3Settings->throw;
        }
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Section::make('Main')
                ->schema([
                    TableRepeater::make('basic_rate_commission')
                        ->cloneable(false)
                        ->reorderable(false)
                        ->reactive()
                        ->headers([
                            Header::make('Commission Type'),
                            Header::make('Tax obligations type'),
                            Header::make('Taxes (%)'),
                            Header::make('Agent Commission (%)'),
                            Header::make('Service (%)'),
                            Header::make('Basic Rate Commission (%)'),
                        ])
                        ->addActionLabel('Add row')
                        ->schema([
                            Placeholder::make('commission_type_placeholder')
                                ->label(false)
                                ->content(fn(Get $get) => $get('commission_type')),
                            Placeholder::make('revenue_level_placeholder')
                                ->label(false)
                                ->content(fn(Get $get) => $get('revenue_level')),
                            Placeholder::make('taxes_placeholder')
                                ->label(false)
                                ->content(fn(Get $get) => $get('taxes')),
                            Placeholder::make('agent_commission_placeholder')
                                ->label(false)
                                ->content(fn(Get $get) => $get('agent_commission')),
                            Placeholder::make('service_placeholder')
                                ->label(false)
                                ->content(fn(Get $get) => $get('service')),
                            Placeholder::make('commission_rate_placeholder')
                                ->label(false)
                                ->content(fn(Get $get) => $get('commission_rate')),
                        ])
                        ->extraItemActions([
                            Forms\Components\Actions\Action::make('Edit')
                                ->icon('heroicon-s-pencil')
                                ->modalHeading('Edit Rate Commission')
                                ->fillForm(function (array $arguments, Repeater $component): array {
                                    $allItems = $component->getState();
                                    $currentKey = $arguments['item'];
                                    return $allItems[$currentKey] ?? [];
                                })
                                ->form([
                                    Hidden::make('id'),
                                    Select::make('commission_type')
                                        ->options([
                                            'Management company' => 'Management company',
                                            'Property owner' => 'Property owner'
                                        ])
                                        ->required(),
                                    TextInput::make('revenue_level')
                                        ->label('Tax obligations type')
                                        ->placeholder('0-15000')
                                        ->required(),
                                    TextInput::make('taxes')
                                        ->label('Taxes (%)')
                                        ->numeric()
                                        ->inputMode('decimal')
                                        ->extraInputAttributes([
                                            'min' => '0',
                                            'class' => '[&::-webkit-inner-spin-button]:appearance-none',
                                        ])
                                        ->minValue(0)
                                        ->rules(['numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/'])
                                        ->placeholder('0.00')
                                        ->required(),
                                    TextInput::make('agent_commission')
                                        ->label('Agent Commission (%)')
                                        ->numeric()
                                        ->inputMode('decimal')
                                        ->extraInputAttributes([
                                            'min' => '0',
                                            'class' => '[&::-webkit-inner-spin-button]:appearance-none',
                                        ])
                                        ->minValue(0)
                                        ->rules(['numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/'])
                                        ->placeholder('0.00'),
                                    TextInput::make('service')
                                        ->label('Service (%)')
                                        ->numeric()
                                        ->inputMode('decimal')
                                        ->extraInputAttributes([
                                            'min' => '0',
                                            'class' => '[&::-webkit-inner-spin-button]:appearance-none',
                                        ])
                                        ->minValue(0)
                                        ->rules(['numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/'])
                                        ->placeholder('0.00'),
                                    TextInput::make('commission_rate')
                                        ->label('Basic Rate Commission (%)')
                                        ->numeric()
                                        ->inputMode('decimal')
                                        ->extraInputAttributes([
                                            'min' => '0',
                                            'class' => '[&::-webkit-inner-spin-button]:appearance-none',
                                        ])
                                        ->minValue(0)
                                        ->rules(['numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/'])
                                        ->placeholder('0.00')
                                        ->required(),
                                ])
                                ->action(function (array $arguments, array $data, $component, Set $set, Get $get): void {
                                    $mainState = $component->getState();
                                    $key = $arguments['item'];
                                    $mainState[$key] = $data;

                                    $component->state($mainState);
                                })
                        ])
                        ->addAction(function ($action) {
                            return $action->form([
                                Select::make('commission_type')
                                    ->options([
                                        'Management company' => 'Management company',
                                        'Property owner' => 'Property owner'
                                    ])
                                    ->required(),
                                TextInput::make('revenue_level')
                                    ->label('Revenue level (EUR)')
                                    ->placeholder('0-15000')
                                    ->rules(['required', 'regex:/^\d{1,9}-\d{1,9}$/'])
                                    ->required(),
                                TextInput::make('taxes')
                                    ->label('Taxes (%)')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->extraInputAttributes([
                                        'min' => '0',
                                        'class' => '[&::-webkit-inner-spin-button]:appearance-none',
                                    ])
                                    ->minValue(0)
                                    ->rules(['numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/'])
                                    ->placeholder('0.00')
                                    ->required(),
                                TextInput::make('agent_commission')
                                    ->label('Agent Commission (%)')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->extraInputAttributes([
                                        'min' => '0',
                                        'class' => '[&::-webkit-inner-spin-button]:appearance-none',
                                    ])
                                    ->minValue(0)
                                    ->rules(['numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/'])
                                    ->placeholder('0.00'),
                                TextInput::make('service')
                                    ->label('Service (%)')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->extraInputAttributes([
                                        'min' => '0',
                                        'class' => '[&::-webkit-inner-spin-button]:appearance-none',
                                    ])
                                    ->minValue(0)
                                    ->rules(['numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/'])
                                    ->placeholder('0.00'),
                                TextInput::make('commission_rate')
                                    ->label('Basic Rate Commission (%)')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->extraInputAttributes([
                                        'min' => '0',
                                        'class' => '[&::-webkit-inner-spin-button]:appearance-none',
                                    ])
                                    ->minValue(0)
                                    ->rules(['numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/'])
                                    ->placeholder('0.00')
                                    ->required(),
                            ])
                                ->action(function ($data, Set $set, Get $get) {
                                    $currentState = $get('basic_rate_commission') ?? [];
                                    $result = array_merge($currentState, [$data]);
                                    $set('basic_rate_commission', $result);
                                });
                        })
                        ->deleteAction(function ($action) {
                            return $action
                                ->requiresConfirmation()
                                ->before(function (array $arguments, array $data, $component, Set $set, Get $get) use ($action) {
                                    $id = $arguments['item'] ?? null;

                                    $repeaterState = $get('basic_rate_commission');
                                    $itemData = null;

                                    if ($id !== null && isset($repeaterState[$id])) {
                                        $itemData = $repeaterState[$id];
                                    }



                                    if (!isset($itemData['id'])) {
                                        return;
                                    }

                                    if (Property::where('basic_rate_commission_id', $itemData['id'])->exists()) {
                                        Notification::make()
                                            ->title('Error')
                                            ->danger()
                                            ->body('Cannot delete. This rate is linked to a Property record.')
                                            ->send();

                                        $action->cancel();
                                    }
                                });
                        })
                ]),
            Section::make('Directories')
                ->schema([
                    TableRepeater::make('sites')
                        ->headers([
                            Header::make('site')->width('150px'),
                            Header::make('url')->width('160px'),
                            Header::make('Account ID')->width('100px'),
                            Header::make('API Key')->width('100px'),
                            Header::make('API URL')->width('100px'),
                        ])
                        ->reorderable(false)
                        ->schema([
                            Hidden::make('id'),
                            Placeholder::make('Site_placeholder')
                                ->label(false)
                                ->content(fn(Get $get) => $get('site')),
                            Placeholder::make('Url_placeholder')
                                ->label(false)
                                ->content(fn(Get $get) => $get('url')),
                            Placeholder::make('account_id_placeholder')
                                ->label(false)
                                ->content(fn(Get $get) => $get('account_id'))
                                ->extraAttributes(['style' => 'overflow: hidden;']),
                            Placeholder::make('api_key_placeholder')
                                ->label(false)
                                ->content(fn(Get $get) => $get('api_key'))
                                ->extraAttributes(['style' => 'overflow: hidden;']),
                            Placeholder::make('api_url_placeholder')
                                ->label(false)
                                ->content(fn(Get $get) => $get('api_url'))
                                ->extraAttributes(['style' => 'overflow: hidden;']),
                        ])
                        ->extraItemActions([
                            Forms\Components\Actions\Action::make('Edit')
                                ->icon('heroicon-s-pencil')
                                ->modalHeading('Edit Season')
                                ->fillForm(function (array $arguments, Repeater $component): array {
                                    $allItems = $component->getState();
                                    $currentKey = $arguments['item'];
                                    return $allItems[$currentKey] ?? [];
                                })
                                ->form([
                                    Hidden::make('id'),
                                    TextInput::make('site')
                                        ->label('Site')
                                        ->required(),
                                    TextInput::make('url')
                                        ->label('Url')
                                        ->url()
                                        ->required(),
                                    TextInput::make('account_id')
                                        ->label('Account ID'),
                                    TextInput::make('api_key')
                                        ->label('API Key'),
                                    TextInput::make('api_url')
                                        ->label('API URL'),
                                    TextInput::make('default_property_id')
                                        ->label(function () {
                                            $label = "Default Property ID";
                                            $tooltip = view('custom-label-help', [
                                                'icon' => 'heroicon-o-question-mark-circle',
                                                'tooltip' => 'Planyo ID of the resource used as a base for the new resource.',
                                            ])->render();
                                            return new HtmlString($label . $tooltip);
                                        })
                                ])
                                ->action(function (array $arguments, array $data, $component, Set $set, Get $get): void {
                                    $mainState = $component->getState();
                                    $key = $arguments['item'];
                                    $mainState[$key] = $data;

                                    $component->state($mainState);
                                })
                        ])
                        ->addAction(function ($action) {
                            return $action->form([
                                Hidden::make('id'),
                                TextInput::make('site')
                                    ->label('Site')
                                    ->required(),
                                TextInput::make('url')
                                    ->label('Url')
                                    ->url()
                                    ->required(),
                                TextInput::make('account_id')
                                    ->label('Account ID'),
                                TextInput::make('api_key')
                                    ->label('API Key'),
                                TextInput::make('api_url')
                                    ->label('API URL'),
                                TextInput::make('default_property_id'),
                            ])
                                ->action(function ($data, Set $set, Get $get) {
                                    $currentState = $get('sites') ?? [];
                                    $result = array_merge($currentState, [$data]);
                                    $set('sites', $result);
                                });
                        })
                        ->deleteAction(function ($action) {
                            return $action
                                ->requiresConfirmation()
                                ->before(function (array $arguments, array $data, $component, Set $set, Get $get) use ($action) {
                                    $id = $arguments['item'] ?? null;

                                    $repeaterState = $get('sites');
                                    $itemData = null;

                                    if ($id !== null && isset($repeaterState[$id])) {
                                        $itemData = $repeaterState[$id];
                                    }

                                    if (!isset($itemData['id'])) {
                                        return;
                                    }

                                    if (PropertySitesContent::where('property_site_id', $itemData['id'])->exists()) {
                                        Notification::make()
                                            ->title('Error')
                                            ->danger()
                                            ->body('Cannot delete. This rate is linked to a Property record.')
                                            ->send();

                                        $action->cancel();
                                    } elseif (PropertySync::where('synchronization_id', $itemData['id'])->exists()) {
                                        Notification::make()
                                            ->title('Error')
                                            ->danger()
                                            ->body('Cannot delete. This rate is linked to a Property record.')
                                            ->send();

                                        $action->cancel();
                                    }
                                });
                        })
                        ->columnSpan('full'),
                ]),

            Section::make('Rental licence types')
                ->schema([
                    TableRepeater::make('licence_types')
                        ->label(false)
                        ->cloneable(false)
                        ->reorderable(false)
                        ->reactive()
                        ->headers([
                            Header::make('Licence type')
                        ])
                        ->addActionLabel('Add row')
                        ->schema([
                            Placeholder::make('Licence type')
                                ->label(false)
                                ->content(fn(Get $get) => $get('licence_type'))
                        ])
                        ->extraItemActions([
                            Forms\Components\Actions\Action::make('Edit')
                                ->icon('heroicon-s-pencil')
                                ->modalHeading('Edit Season')
                                ->fillForm(function (array $arguments, Repeater $component): array {
                                    $allItems = $component->getState();
                                    $currentKey = $arguments['item'];
                                    return $allItems[$currentKey] ?? [];
                                })
                                ->form([
                                    Hidden::make('id'),

                                    TextInput::make('licence_type')
                                        ->required()
                                ])
                                ->action(function (array $arguments, array $data, $component, Set $set, Get $get): void {
                                    $mainState = $component->getState();
                                    $key = $arguments['item'];
                                    $mainState[$key] = $data;

                                    $component->state($mainState);
                                })
                        ])
                        ->addAction(function ($action) {
                            return $action->form([
                                TextInput::make('licence_type')
                                    ->required(),
                            ])
                                ->action(function ($data, Set $set, Get $get) {
                                    $currentState = $get('licence_types') ?? [];
                                    $result = array_merge($currentState, [$data]);
                                    $set('licence_types', $result);
                                });
                        })

                        ->deleteAction(function ($action) {
                            return $action
                                ->requiresConfirmation()
                                ->before(function (array $arguments, array $data, $component, Set $set, Get $get) use ($action) {
                                    $id = $arguments['item'] ?? null;

                                    $repeaterState = $get('licence_types');
                                    $itemData = null;

                                    if ($id && isset($repeaterState[$id])) {
                                        $itemData = $repeaterState[$id];
                                    }

                                    if (!isset($itemData['id'])) {
                                        return;
                                    }

                                    if (Property::where('rental_licence_type_id', $itemData['id'])->exists()) {
                                        Notification::make()
                                            ->title('Error')
                                            ->danger()
                                            ->body('Cannot delete. This licence type is linked to a Property.')
                                            ->send();

                                        $action->cancel();
                                    }
                                });
                        }),
                ]),
            Section::make('Licences')
                ->schema([
                    TableRepeater::make('add_licence_types')
                        ->label(false)
                        ->cloneable(false)
                        ->reorderable(false)
                        ->reactive()
                        ->headers([
                            Header::make('Licence type'),
                            Header::make('Hint'),
                        ])
                        ->addActionLabel('Add row')
                        ->schema([
                            Placeholder::make('Licence type')
                                ->label(false)
                                ->content(fn(Get $get) => $get('add_licence_type')),
                            Placeholder::make('Hint')
                                ->label(false)
                                ->content(fn(Get $get) => $get('add_licence_hint')),
                        ])
                        ->extraItemActions([
                            Forms\Components\Actions\Action::make('Edit')
                                ->icon('heroicon-s-pencil')
                                ->modalHeading('Edit')
                                ->fillForm(function (array $arguments, Repeater $component): array {
                                    $allItems = $component->getState();
                                    $currentKey = $arguments['item'];
                                    return $allItems[$currentKey] ?? [];
                                })
                                ->form([
                                    Hidden::make('id'),

                                    TextInput::make('add_licence_type')
                                        ->required(),

                                    CheckboxList::make('add_licence_type_operation')
                                        ->label(false)
                                        ->extraAttributes(['class' => 'grid !grid-cols-[repeat(3,minmax(0,1fr))]'])
                                        ->gridDirection('row')
                                        ->options([
                                            'add_licence_short_rent'    => 'Short-term Rent',
                                            'add_licence_month_rent'    => 'Monthly Rent',
                                            'add_licence_sale'          => 'Sale',
                                        ])
                                        ->rules(['required', 'array'])
                                        ->validationMessages([
                                            'required' => 'Please select at least one licence type.',
                                        ])
                                        ->required(),

                                    CheckboxList::make('add_licence_is_required')
                                        ->label(false)
                                        ->extraAttributes(['class' => 'grid !grid-cols-[repeat(3,minmax(0,1fr))]'])
                                        ->gridDirection('row')
                                        ->options([
                                            'add_licence_required' => 'Required',
                                        ]),

                                    TextArea::make('add_licence_hint')
                                        ->label('Hint')
                                        ->maxLength(500)
                                        ->columnSpanFull()
                                        ->live(true)
                                        ->helperText(fn(Get $get) => sprintf('%d characters allowed', 500 - strlen($get('add_licence_hint') ?? '')))
                                        ->required(),

                                    CheckboxList::make('add_licence_is_file_attachment')
                                        ->label(false)
                                        ->extraAttributes(['class' => 'grid !grid-cols-[repeat(3,minmax(0,1fr))]'])
                                        ->gridDirection('row')
                                        ->options([
                                            'add_licence_file_attachment' => 'File attachment',
                                        ]),


                                ])
                                ->action(function (array $arguments, array $data, $component, Set $set, Get $get): void {
                                    $mainState = $component->getState();
                                    $key = $arguments['item'];
                                    $mainState[$key] = $data;

                                    $component->state($mainState);
                                })
                        ])
                        ->addAction(function ($action) {
                            return $action->form([

                                TextInput::make('add_licence_type')
                                    ->required(),

                                CheckboxList::make('add_licence_type_operation')
                                    ->label(false)
                                    ->extraAttributes(['class' => 'grid !grid-cols-[repeat(3,minmax(0,1fr))]'])
                                    ->gridDirection('row')
                                    ->options([
                                        'add_licence_short_rent'    => 'Short-term Rent',
                                        'add_licence_month_rent'    => 'Monthly Rent',
                                        'add_licence_sale'          => 'Sale',
                                    ])
                                    ->rules(['required', 'array'])
                                    ->validationMessages([
                                        'required' => 'Please select at least one licence type.',
                                    ])
                                    ->required(),

                                CheckboxList::make('add_licence_is_required')
                                    ->label(false)
                                    ->extraAttributes(['class' => 'grid !grid-cols-[repeat(3,minmax(0,1fr))]'])
                                    ->gridDirection('row')
                                    ->options([
                                        'add_licence_required' => 'Required',
                                    ]),

                                TextArea::make('add_licence_hint')
                                    ->label('Hint')
                                    ->maxLength(500)
                                    ->columnSpanFull()
                                    ->live(true)
                                    ->helperText(fn(Get $get) => sprintf('%d characters allowed', 500 - strlen($get('add_licence_hint') ?? '')))
                                    ->required(),

                                CheckboxList::make('add_licence_is_file_attachment')
                                    ->label(false)
                                    ->extraAttributes(['class' => 'grid !grid-cols-[repeat(3,minmax(0,1fr))]'])
                                    ->gridDirection('row')
                                    ->options([
                                        'add_licence_file_attachment' => 'File attachment',
                                    ]),

                            ])
                                ->action(function ($data, Set $set, Get $get) {
                                    $currentState = $get('add_licence_types') ?? [];
                                    $result = array_merge($currentState, [$data]);
                                    $set('add_licence_types', $result);
                                });
                        })
                        ->deleteAction(function ($action) {
                            return $action
                                ->requiresConfirmation()
                                ->before(function (array $arguments, array $data, $component, Set $set, Get $get) use ($action) {
                                    $id = $arguments['item'] ?? null;

                                    $repeaterState = $get('add_licence_types');
                                    $itemData = null;

                                    if ($id && isset($repeaterState[$id])) {
                                        $itemData = $repeaterState[$id];
                                    }

                                    if (!isset($itemData['id'])) {
                                        return;
                                    }
                                });
                        })
                ]),

            Section::make('S3 Settings')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('key')
                                ->label('Access Key ID')
                                ->required()
                                ->columns(3),
                            TextInput::make('secret')
                                ->label('Secret Access Key')
                                ->required()
                                ->columns(3),
                            TextInput::make('token')
                                ->label('Token'),
                            TextInput::make('region')
                                ->label('Region')
                                ->default('auto')
                                ->required(),
                            TextInput::make('bucket')
                                ->label('Bucket')
                                ->required(),
                            TextInput::make('endpoint')
                                ->label('Endpoint')
                                ->required(),
                            TextInput::make('url')
                                ->label('Public URL')
                                ->columnSpanFull(),
                            TextInput::make('visibility')
                                ->label('Visibility')
                                ->default('public')
                                ->required(),
                            Checkbox::make('use_path_style_endpoint')
                                ->label('Use Path Style Endpoint')
                                ->inline(false),
                            Checkbox::make('throw')
                                ->label('Throw')
                                ->inline(false),
                        ])
                ]),

            Section::make('Google Map Settings')
                ->schema([
                    TextInput::make('google_map_api_key')
                        ->label('Google Map API Key')
                        ->required()
                ]),

            Section::make('Automatic synchronization period')
                ->schema([
                    TextInput::make('async_period_minutes')
                        ->label('Automatic synchronization period in minutes')
                        ->required()
                ])


        ])
            ->statePath('data')
            ->extraAttributes(['class' => 'settings-form']);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = PropertySetting::firstOrCreate([]);

        $incomingIds = collect($data['basic_rate_commission'])->pluck('id')->filter();

        BasicRateCommission::whereNotIn('id', $incomingIds)->delete();

        foreach ($data['basic_rate_commission'] as $commission) {
            if (!empty($commission['id'])) {
                $existingCommission = BasicRateCommission::find($commission['id']);
                if ($existingCommission) {
                    $existingCommission->update([
                        'commission_type' => $commission['commission_type'],
                        'revenue_level' => $commission['revenue_level'],
                        'taxes' => $commission['taxes'],
                        'agent_commission' => $commission['agent_commission'],
                        'service' => $commission['service'],
                        'commission_rate' => $commission['commission_rate'],
                    ]);
                }
            } else {
                BasicRateCommission::create([
                    'commission_type' => $commission['commission_type'],
                    'revenue_level' => $commission['revenue_level'],
                    'taxes' => $commission['taxes'],
                    'agent_commission' => $commission['agent_commission'],
                    'service' => $commission['service'],
                    'commission_rate' => $commission['commission_rate'],
                ]);
            }
        }

        $incomingLicenceTypeIds = collect($data['licence_types'])->pluck('id')->filter();

        LicenceType::whereNotIn('id', $incomingLicenceTypeIds)->delete();

        foreach ($data['licence_types'] as $licenceTypeData) {

            if (!empty($licenceTypeData['id'])) {
                $licenceType = LicenceType::find($licenceTypeData['id']);
                if ($licenceType) {
                    $licenceType->update([
                        'name' => $licenceTypeData['licence_type'],
                    ]);
                }
            } else {
                LicenceType::create([
                    'name' => $licenceTypeData['licence_type'],
                ]);
            }
        }


        foreach ($data['add_licence_types'] as $addLicenceTypeData) {
            $addLicenceData = [
                'name' => $addLicenceTypeData['add_licence_type'],
                'sale' => in_array('add_licence_sale', $addLicenceTypeData['add_licence_type_operation'] ?? []),
                'short_rent' => in_array('add_licence_short_rent', $addLicenceTypeData['add_licence_type_operation'] ?? []),
                'monthly_rent' => in_array('add_licence_month_rent', $addLicenceTypeData['add_licence_type_operation'] ?? []),
                'required' => in_array('add_licence_required', $addLicenceTypeData['add_licence_is_required'] ?? []),
                'hint' => $addLicenceTypeData['add_licence_hint'],
                'file_attachment' => in_array('add_licence_file_attachment', $addLicenceTypeData['add_licence_is_file_attachment'] ?? []),
            ];

            if (!empty($addLicenceTypeData['id'])) {
                $addLicenceType = AdditionalLicenceType::find($addLicenceTypeData['id']);
                if ($addLicenceType) {
                    $addLicenceType->update($addLicenceData);
                }
            } else {
                AdditionalLicenceType::create($addLicenceData);
            }
        }

        $existingSites = PropertySites::pluck('id')->toArray();
        $newSiteIds = [];
        foreach ($data['sites'] as $site) {
            if (isset($site['id']) && in_array($site['id'], $existingSites)) {
                $propertySite = PropertySites::find($site['id']);
                $propertySite->update([
                    'site' => $site['site'],
                    'url' => $site['url'],
                    'account_id' => $site['account_id'],
                    'api_key' => $site['api_key'],
                    'api_url' => $site['api_url'],
                    'default_property_id' => $site['default_property_id'],
                ]);
                $newSiteIds[] = $site['id'];
            } else {
                $newSite = PropertySites::create([
                    'site' => $site['site'],
                    'url' => $site['url'],
                    'account_id' => $site['account_id'],
                    'api_key' => $site['api_key'],
                    'api_url' => $site['api_url'],
                    'default_property_id' => $site['default_property_id'],
                ]);
                $newSiteIds[] = $newSite->id;
            }
        }
        $sitesToDelete = array_diff($existingSites, $newSiteIds);
        PropertySites::whereIn('id', $sitesToDelete)->delete();

        //Saving S3 settings
        $s3Setting = S3Setting::firstOrNew([]);
        $s3Setting->fill([
            'key' => $data['key'],
            'secret' => $data['secret'],
            'token' => $data['token'],
            'region' => $data['region'],
            'bucket' => $data['bucket'],
            'endpoint' => $data['endpoint'],
            'use_path_style_endpoint' => $data['use_path_style_endpoint']  ?? false,
            'visibility' => $data['visibility'],
            'url' => $data['url'],
            'throw' => $data['throw']  ?? false,
        ]);

        $s3Setting->save();

        $settings->google_map_api_key = $data['google_map_api_key'];
        $settings->async_period_minutes = $data['async_period_minutes'];
        $settings->save();

        Notification::make()
            ->title('Settings Saved')
            ->success()
            ->send();
    }

    public function cancelAction(): Action
    {
        return Action::make('cancel')
            ->requiresConfirmation()
            ->color('gray')
            ->action(function () {
                return redirect()->route('filament.backend.pages.dashboard');
            });
    }

    public function saveAction(): Action
    {
        return Action::make('save')
            ->label('Save Settings')
            ->submit('save');
    }
}
