<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\CompanyEmployee;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\CompanyMeta;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Actions\Action;

class CompanyResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-office-building';

    public static function form(Form $form): Form
    {
    
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->description('Provide the basic details about the company.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Company Name')
                                    ->required(),
                                
                                Hidden::make('companyMeta.type')
                                    ->dehydrated(),
                                
                                Select::make('companyMeta.type_label')
                                    ->label('Company Type')
                                    ->options([
                                        'Property Management Company' => 'Property Management Company',
                                        'Agency' => 'Agency',
                                        'Tour Operator' => 'Tour Operator',
                                        'Broker' => 'Broker',
                                        'Realtor' => 'Realtor',
                                        'Other' => 'Other',
                                    ])
                                    ->reactive()
                                    ->afterStateHydrated(function ($state, callable $set, callable $get) {
                                        $savedType = $get('companyMeta.type'); 
                                        if (!$savedType) {
                                            $set('companyMeta.type_label', null);
                                        } elseif (!in_array($savedType, [
                                            'Property Management Company',
                                            'Agency',
                                            'Tour Operator',
                                            'Broker',
                                            'Realtor',
                                        ])) {
                                            $set('companyMeta.type_label', 'Other');
                                            $set('companyMeta.type_other', $savedType);
                                        } else {
                                            $set('companyMeta.type_label', $savedType);
                                        }
                                    })
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state !== 'Other') {
                                            $set('companyMeta.type', $state); 
                                            $set('companyMeta.type_other', null); 
                                        } else {
                                            $set('companyMeta.type', null); 
                                        }
                                    })
                                    ->required(),
                                
                                TextInput::make('companyMeta.phone')
                                    ->label('Phone Number')
                                    ->required()
                                    ->tel()
                                    ->mask('+99 999 999-9999'),
                                
                                TextInput::make('companyMeta.type_other')
                                    ->label('Specify Company Type')
                                    ->required()
                                    ->visible(fn ($get) => $get('companyMeta.type_label') === 'Other')
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('companyMeta.type', $state)), 
                                

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->required(),
                            ]),
                        Textarea::make('companyMeta.about')
                            ->label('About Company')
                            ->rows(3)
                            ->columnSpan('full'), 
                    ]),

                Section::make('Address')
                    ->description('Provide the address details of the company.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('companyMeta.country')
                                    ->label('Country')
                                    ->options((new CompanyMeta())->getCountriesForSelect())
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(2)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $set('companyMeta.city', null);
                                    }),

                                Select::make('companyMeta.city')
                                    ->label('City')
                                    ->required()
                                    ->searchable()
                                    ->options(function (callable $get) {
                                        $country = $get('companyMeta.country');

                                        if (!$country) {
                                            return [];
                                        }

                                        return (new CompanyMeta())->getCitiesForCountry($country);
                                    })
                                    ->reactive()
                                    ->columnSpan(2),

                                TextInput::make('companyMeta.address')
                                    ->label('Address')
                                    ->required(),
                                
                                TextInput::make('companyMeta.address2')
                                    ->label('Address 2')
                                    ->required(),
                                
                                TextInput::make('companyMeta.state')
                                    ->label('State or Province')
                                    ->required(),
                                
                                TextInput::make('companyMeta.postal_code')
                                    ->label('Postal/Zip Code')
                                    ->required()
                                    ->maxLength(6), 
                            ]),
                    ]),
                
                Section::make('Tax Information')
                    ->description('Enter the tax and banking information for the company.')
                    ->schema([
                        TextInput::make('companyMeta.tax_id')
                            ->label('Tax ID')
                            ->mask('999999999') 
                            ->nullable(),
                        
                        TextInput::make('companyMeta.beneficiary')
                            ->label('Beneficiary')
                            ->nullable(),

                        TextInput::make('companyMeta.iban')
                            ->label('IBAN')
                            ->nullable()
                            ->regex('/[A-Z]{2}\d{2}[A-Z0-9]{1,30}/')
                    ]),
                
                Section::make('Links')
                    ->description('Add website or social media links for the company.')
                    ->schema([
                        Grid::make(3)
                        ->schema( [
                            TextInput::make('companyMeta.website')
                                    ->label('Website Media Link')
                                    ->type('url')
                                    ->nullable(),
                            TextInput::make('companyMeta.telegram')
                                    ->label('Telegram')
                                    ->nullable(),
                            TextInput::make('companyMeta.viber')
                                    ->label('Viber')
                                    ->mask('+99 999 999-9999')
                                    ->nullable(),
                            TextInput::make('companyMeta.whatsapp')
                                    ->label('WhatsApp')
                                    ->mask('+99 999 999-9999')
                                    ->nullable(),
                            TextInput::make('companyMeta.facebook')
                                    ->label('Facebook')
                                    ->type('url')
                                    ->nullable(),
                            TextInput::make('companyMeta.instagram')
                                    ->label('Instagram')
                                    ->nullable(),
                            TextInput::make('companyMeta.tiktok')
                                    ->label('TikTok')
                                    ->nullable(),
                        ]),
                    ]),
             
                Section::make('Employees')
                    ->description('Company Stuff')
                    ->schema([
                        TableRepeater::make('employees')
                            ->label(false)
                            // ->relationship('employees')
                            ->formatStateUsing(function ($state, $component) {
                                $record = $component->getRecord();
                                if (!$record || !$record->id) {
                                    return [];
                                }
                                $companyEmployees = CompanyEmployee::where('company_user_id', $record->id)->get();
                                return $companyEmployees->toArray();
                            })
                            
                            ->reorderable(false)
                            ->headers([
                                Header::make('Full name')->label('Full name'),
                                Header::make('Role in company')->label('Role in company'),
                            ])
                            ->deletable()
                            
                            ->deleteAction(
                                fn (Action $action) => $action->requiresConfirmation(),
                            )
                            ->schema([
                                Placeholder::make('employee_name')
                                    ->label(false)
                                    ->content(fn (Get $get) => $get('employee_user_id') ? User::find($get('employee_user_id'))->name . ' ' . User::find($get('employee_user_id'))->last_name : 'N/A'),
                
                                Placeholder::make('role')
                                    ->label(false)
                                    ->content(fn (Get $get) => $get('role') ?: 'N/A'),
                            ])
                            ->extraItemActions([
                                Action::make('Edit')
                                    ->icon('heroicon-s-pencil')
                                    ->modalHeading('Edit stuff')
                                    ->modalSubmitActionLabel('Save')
                                    ->fillForm(function (array $arguments, Repeater $component): array {
                                        $allItems = $component->getState();
                                        $currentKey = $arguments['item'];
                                    
                                        if (isset($allItems[$currentKey])) {
                                            $record = $allItems[$currentKey];
                                    
                                            if ($record instanceof CompanyEmployee) {
                                                return $record->toArray();
                                            }
                                    
                                            return $record; 
                                        }
                                    
                                        return [];
                                    })
                                    ->form([
                                        Select::make('employee_user_id')
                                            ->label('Employee')
                                            ->options(function ($state, $component, Get $get) {
                                                $allUsers = User::whereDoesntHave('roles', function ($query) {
                                                    $query->where('name', 'company');
                                                })
                                                ->where(function ($query) {
                                                    $query->where('name', 'like', '%' . request()->input('search') . '%')
                                                        ->orWhere('last_name', 'like', '%' . request()->input('search') . '%')
                                                        ->orWhere('email', 'like', '%' . request()->input('search') . '%');
                                                })
                                                ->get()
                                                ->mapWithKeys(function ($user) {
                                                    return [$user->id => $user->name . ' ' . $user->last_name . ' (' . $user->email . ')'];
                                                });
                                                $data = $get('../../data.employees');
                                                $stateExtras = collect($data)->pluck('employee_user_id')->toArray();
                                               
                                                $filteredExtras = collect($stateExtras)->filter(fn($extra) => $extra !== $state)->toArray();
                                               
                                                return collect($allUsers)
                                                ->except($filteredExtras)
                                                ->toArray();
                                            })
                                            ->searchable()
                                            ->required(),
                
                                        Hidden::make('role')
                                            ->dehydrated(),
                                        
                                        Select::make('role_label')
                                            ->label('Role in Company')
                                            ->options([
                                                'Owner' => 'Owner',
                                                'Co-owner' => 'Co-owner',
                                                'Manager' => 'Manager',
                                                'Agent' => 'Agent',
                                                'Operator' => 'Operator',
                                                'Other' => 'Other',
                                            ])
                                            ->reactive()
                                            ->afterStateHydrated(function ($state, callable $set, callable $get) {
                                                $savedType = $get('role'); 
                                                if (!$savedType) {
                                                    $set('role_label', null);
                                                } elseif (!in_array($savedType, [
                                                    'Owner',
                                                    'Co-owner',
                                                    'Manager',
                                                    'Agent',
                                                    'Operator',
                                                ])) {
                                                    $set('role_label', 'Other');
                                                    $set('role_other', $savedType);
                                                } else {
                                                    $set('role_label', $savedType);
                                                }
                                            })
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state !== 'Other') {
                                                    $set('role', $state); 
                                                    $set('role_other', null); 
                                                } else {
                                                    $set('role', null);
                                                }
                                            })
                                            ->required(),
                                        
                                        TextInput::make('role_other')
                                            ->label('Other Role in Company')
                                            ->visible(fn ($get) => $get('role_label') === 'Other')
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('role', $state))
                                            ->required(),
                                    ])
                                    ->action(function (array $arguments, array $data, $component, Set $set, Get $get): void {
                                        $mainState = $component->getState();
                                        $key = $arguments['item'];
                                        $mainState[$key] = $data;
                                        $component->state($mainState);
                                    })
                            ])
                            ->addActionLabel('Add stuff')
                            ->addAction(function ($action) {
                                return $action
                                ->modalSubmitActionLabel('Save')
                                ->form([
                                    Select::make('employee_user_id')
                                        ->label('Employee')
                                        ->options(function ($state, $component, Get $get) {
                                            $allUsers = User::whereDoesntHave('roles', function ($query) {
                                                $query->where('name', 'company');
                                            })
                                            ->where(function ($query) {
                                                $query->where('name', 'like', '%' . request()->input('search') . '%')
                                                    ->orWhere('last_name', 'like', '%' . request()->input('search') . '%')
                                                    ->orWhere('email', 'like', '%' . request()->input('search') . '%');
                                            })
                                            ->get()
                                            ->mapWithKeys(function ($user) {
                                                return [$user->id => $user->name . ' ' . $user->last_name . ' (' . $user->email . ')'];
                                            });
                                            $data = $get('../../data.employees');
                                            $stateExtras = collect($data)->pluck('employee_user_id')->toArray();
                                           
                                            $filteredExtras = collect($stateExtras)->filter(fn($extra) => $extra !== $state)->toArray();
                                           
                                            return collect($allUsers)
                                            ->except($filteredExtras)
                                            ->toArray();
                                        })
                                        ->searchable()
                                        ->required(),

                
                                    Hidden::make('role')
                                        ->dehydrated(),
                                    
                                    Select::make('role_label')
                                        ->label('Role in Company')
                                        ->options([
                                            'Owner' => 'Owner',
                                            'Co-owner' => 'Co-owner',
                                            'Manager' => 'Manager',
                                            'Agent' => 'Agent',
                                            'Operator' => 'Operator',
                                            'Other' => 'Other',
                                        ])
                                        ->reactive()
                                        ->afterStateHydrated(function ($state, callable $set, callable $get) {
                                            $savedType = $get('role'); 
                                            if (!$savedType) {
                                                $set('role_label', null);
                                            } elseif (!in_array($savedType, [
                                                'Owner',
                                                'Co-owner',
                                                'Manager',
                                                'Agent',
                                                'Operator',
                                            ])) {
                                                $set('role_label', 'Other');
                                                $set('role_other', $savedType);
                                            } else {
                                                $set('role_label', $savedType);
                                            }
                                        })
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            if ($state !== 'Other') {
                                                $set('role', $state); 
                                                $set('role_other', null); 
                                            } else {
                                                $set('role', null);
                                            }
                                        })
                                        ->required(),
                                    
                                    TextInput::make('role_other')
                                        ->label('Other Role in Company')
                                        ->visible(fn ($get) => $get('role_label') === 'Other')
                                        ->afterStateUpdated(fn ($state, callable $set) => $set('role', $state))
                                        ->required(),
                                ])
                                ->action(function ($data, Set $set, Get $get) {
                                    $currentState = $get('employees') ?? [];
                                    $result = array_merge($currentState, [$data]);
                                    $set('employees', $result);
                                });
                            }),
                
                        ]),                    
                ])
                ->columns('full')
                ->extraAttributes(['class' => 'user-form']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable(),
                // TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('companyMeta.type')->label('Type')->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }

    /**
     * Redefine the query to display only users with the "company" role.
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->role('company');
    }

    public static function getPluralModelLabel(): string
    {
        return 'Companies'; 
    }

    public static function getModelLabel(): string
    {
        return 'Company';
    }
    
}
