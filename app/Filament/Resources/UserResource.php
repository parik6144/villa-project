<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\CompanyEmployee;
use App\Models\UserMeta;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = "heroicon-o-rectangle-stack";



    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make("name")
                ->required()
                ->minLength(3)
                ->maxLength(50),
            TextInput::make("last_name")
                ->minLength(3)
                ->maxLength(50),
            TextInput::make("email")
                ->required()
                ->email()
                ->extraInputAttributes(['autocomplete' => 'nope'])
                ->unique("users", "email", ignoreRecord: true),
            TextInput::make("password")
                ->password()
                ->extraInputAttributes(['autocomplete' => 'nope'])
                ->hidden(!Auth::user()->hasRole('admin') && (fn($record) => $record !== null))
                ->required(fn($record) => $record === null),
            Section::make('User roles')
                ->schema([
                    CheckboxList::make('roles')
                        ->reactive()
                        ->relationship('roles', 'name')
                        ->options(
                            fn() => Role::where('name', '!=', 'company')->pluck('name', 'id')->toArray()
                        )
                        ->columns(2)
                        ->label('Roles'),
                ])
                ->visible(
                    fn($record) => $record ? $record->id != Auth::id() : true
                ),

            Toggle::make('temp_is_email_verified')
                ->label('Email Verified')
                // Устанавливаем состояние тумблера при загрузке формы:
                ->afterStateHydrated(function ($component, $state, $record) {
                    $component->state($record && $record->email_verified_at ? true : false);
                })
                // Гарантируем, что значение тумблера попадёт в данные формы
                ->dehydrateStateUsing(fn($state) => $state)
                // Можно явно указать, что поле должно быть включено в де-гидратацию:
                ->dehydrated(true)
                ->visible(fn() => Auth::user()->hasRole('admin')),


            Fieldset::make('User details')
                ->label(false)
                ->relationship('userMeta')
                ->schema([

                    Section::make('User details')
                        ->label('User details')
                        ->schema([

                            Toggle::make('disabled'),
                            Grid::make(3)
                                ->schema([
                                    TextInput::make('number')->label('Phone Number')->mask('+99 999 999-9999')->required(),
                                    TextInput::make('mobile_number')->label('Mobile Number')->mask('+99 999 999-9999'),
                                    TextInput::make('telegram')->label('Telegram')->mask('+99 999 999-9999'),
                                    TextInput::make('viber')->label('Viber')->mask('+99 999 999-9999'),
                                    TextInput::make('whatsapp')->label('WhatsApp')->mask('+99 999 999-9999'),
                                    TextInput::make('facebook')->label('Facebook'),
                                    TextInput::make('instagram')->label('Instagram'),
                                    TextInput::make('tiktok')->label('TikTok'),
                                ]),
                            Select::make('country_code')
                                ->options((new \App\Models\UserMeta())->getCountriesForSelect())
                                ->searchable()
                                ->required()
                                ->columnSpan(2)
                                ->reactive()
                                ->afterStateUpdated(function (callable $set) {
                                    $set('city', null);
                                }),

                            Select::make('city')
                                ->label('City')
                                ->required()
                                ->searchable()
                                ->options(function (callable $get) {
                                    $country = $get('country_code');

                                    if (!$country) {
                                        return [];
                                    }

                                    return (new \App\Models\UserMeta)->getCitiesForCountry($country);
                                })
                                ->reactive()
                                ->columnSpan(2),



                            TextArea::make('street_address')
                                ->label('Address')
                                ->reactive()
                                ->required(
                                    fn(Get $get) =>
                                    !array_intersect([1, 5], (array)$get('../roles')) //accountant and admin
                                )
                                ->columnSpan(2)
                                ->live(true),

                            TextArea::make('street_address_line_2')
                                ->label('Address 2')
                                ->reactive()
                                ->columnSpan(2)
                                ->live(true),

                            TextInput::make('state_province')
                                ->label('State or Province')
                                ->reactive()
                                ->required(
                                    fn(Get $get) =>
                                    !array_intersect([1, 5], (array)$get('../roles')) //accountant and admin
                                )
                                ->columnSpan(2)
                                ->live(true),

                            TextInput::make('postal_code')
                                ->label('Postal/Zip Code')
                                ->maxLength(6)
                                ->reactive()
                                ->required(
                                    fn(Get $get) =>
                                    !array_intersect([1, 5], (array)$get('../roles')) //accountant and admin
                                )
                                ->live(true),
                            Select::make('heard_about_us')
                                ->options([
                                    'partner_recommendation' => 'Partner Recommendation',
                                    'search' => 'Search',
                                    'social_media' => 'Social Media',
                                    'ad' => 'Ad',
                                    'conference' => 'Conference',
                                    'other' => 'Other',
                                ])
                                ->label('How Did You Hear About Us?'),
                            Textarea::make('additional_comments')->label('Additional Comments'),
                        ])
                        ->visible(
                            fn(Get $get, $livewire): bool =>
                            $livewire->record
                                ? (
                                    array_intersect([1, 2, 3, 4, 5, 6], (array)$get('../roles')) ||
                                    $livewire->record->hasRole('agent') ||
                                    in_array(2, (array)$get('../roles')) ||
                                    ($livewire->record->hasRole('property_owner') && in_array(4, (array)$get('../roles'))) ||
                                    ($livewire->record->hasRole('accountant') && in_array(5, (array)$get('../roles'))) ||
                                    ($livewire->record->hasRole('manager') && in_array(6, (array)$get('../roles'))) ||
                                    in_array(2, (array)$get('roles'))
                                )
                                : (
                                    array_intersect([2], (array)$get('../roles')) ||
                                    in_array(1, (array)$get('../roles')) ||
                                    in_array(2, (array)$get('../roles')) ||
                                    in_array(3, (array)$get('../roles')) ||
                                    in_array(4, (array)$get('../roles')) ||
                                    in_array(5, (array)$get('../roles')) ||
                                    in_array(6, (array)$get('../roles'))

                                )
                        ),

                    Section::make('Sync data')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('user_planyo_id')
                                        ->label('user_planyo_id'),
                                    TextInput::make('registration_time')
                                        ->label('registration_time'),
                                    TextInput::make('reservation_count')
                                        ->label('reservation_count')
                                        ->default(0),
                                    TextInput::make('last_reservation')
                                        ->label('last_reservation'),
                                ])
                        ])
                        ->visible(
                            fn(Get $get, $livewire) =>
                            $livewire->record
                                ? (
                                    in_array(3, (array)$get('../roles')) ||
                                    $livewire->record->hasRole('user') && in_array(3, (array)$get('../roles'))
                                )
                                : (
                                    in_array(3, (array)$get('../roles'))
                                )
                        ),

                    Section::make('Financial details')
                        ->schema([
                            TextInput::make('tax_id')
                                ->label('Tax ID')
                                // ->mask('99999999999999999999999999999')
                                ->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
                                ,

                            TextInput::make('iban')
                                ->label('IBAN')
                                ->placeholder('Enter your IBAN')
                                ->mask('aa99 **** 9999 9999 9999 9999 9999 9999 99')
                                ->extraInputAttributes(['onChange' => 'this.value = this.value.toUpperCase()'])
                                ->regex('/^[A-Z]{2}[0-9]{2}(?:[ ]?[A-Z0-9]{1,4}){0,30}$/')
                                ->helperText('Example: DE89 3704 0044 0532 0130 00 (up to 36 characters)'),


                            TextInput::make('beneficiary')
                                ->label('Beneficiary'),

                            Select::make('accountant_id')
                                ->label('Accountant')
                                ->searchable()
                                ->relationship('accountant', 'full_name')
                                ->helperText('Select the accountant responsible for this user.')
                                // ->extraAttributes(['class' => 'dropdown-up'])
                                ->position('top')
                                ->extraAttributes(['data-position' => 'top'])
                                ->getSearchResultsUsing(function (string $query) {
                                    if (strlen($query) < 3) {
                                        return [];
                                    }

                                    return \App\Models\User::whereHas('roles', function ($queryBuilder) {
                                        $queryBuilder->where('name', 'accountant');
                                    })
                                        ->where(function ($queryBuilder) use ($query) {
                                            $queryBuilder->where('name', 'like', '%' . $query . '%')
                                                ->orWhere('last_name', 'like', '%' . $query . '%')
                                                ->orWhere('email', 'like', '%' . $query . '%');
                                        })
                                        ->get()
                                        ->mapWithKeys(function ($user) {
                                            return [
                                                $user->id => "{$user->full_name} ({$user->email})",
                                            ];
                                        });
                                })



                        ])
                        ->visible(
                            fn(Get $get, $livewire) =>
                            $livewire->record
                                ? (
                                    in_array(4, (array)$get('../roles')) ||
                                    $livewire->record->hasRole('agent') && in_array(4, (array)$get('../roles'))
                                )
                                : (
                                    in_array(4, (array)$get('../roles'))
                                )
                        ),


                    Section::make('Permissions')
                        ->schema([
                            Checkbox::make('rent')
                                ->label('Rent'),
                            Checkbox::make('real_estate')
                                ->label('Real Estate'),
                            Checkbox::make('service')
                                ->label('Service'),
                        ])
                        ->visible(
                            fn(Get $get, $livewire) =>
                            $livewire->record
                                ? (
                                    in_array(2, (array)$get('../roles')) ||
                                    $livewire->record->hasRole('agent') && in_array(2, (array)$get('../roles'))
                                )
                                : (
                                    in_array(2, (array)$get('../roles'))
                                )
                        ),

                    Section::make('Company details')
                        // ->relationship('userMeta')
                        ->schema([


                            Radio::make('company_selection')
                                ->label('Company Selection')
                                ->dehydrated(false)
                                ->options([
                                    'select' => 'Company',
                                    'custom' => 'Existing Company details',
                                ])
                                ->afterStateHydrated(function ($component, $state) {
                                    $record = $component->getRecord();
                                    if ($record) {
                                        // Если у пользователя уже есть связанная запись в company_employees,
                                        // устанавливаем состояние 'select'.
                                        $companyEmployee = CompanyEmployee::where('employee_user_id', $record->user_id)->first();
                                        if ($companyEmployee) {
                                            $component->state('select');
                                        } elseif ($record->company_name) {
                                            // Если поле company_name заполнено, значит используется кастовая компания.
                                            $component->state('custom');
                                        }
                                    }
                                })
                                ->reactive()
                                ->inline(),

                        ])
                        ->visible(
                            fn(Get $get, $livewire) =>
                            $livewire->record
                                ? (
                                    in_array(2, (array)$get('../roles')) ||
                                    $livewire->record->hasRole('agent') && in_array(2, (array)$get('../roles')) ||
                                    in_array(6, (array)$get('../roles')) ||
                                    $livewire->record->hasRole('manager') && in_array(6, (array)$get('../roles'))
                                )
                                : (
                                    in_array(2, (array)$get('../roles')) ||
                                    in_array(6, (array)$get('../roles'))
                                )
                        ),

                    Section::make('Add Custom Company')
                        ->schema([
                            TextInput::make('company_name')->label('Company Name'),

                            Select::make('company_type')
                                ->options([
                                    'management' => 'Management',
                                    'agency' => 'Agency',
                                    'broker' => 'Broker',
                                    'other' => 'Other',
                                ])
                                ->label('Company Type'),

                            Select::make('role_in_company')
                                ->options([
                                    'owner' => 'Owner',
                                    'co-owner' => 'Co-Owner',
                                    'manager' => 'Manager',
                                    'operator' => 'Operator',
                                    'other' => 'Other',
                                ])
                                ->label('Role in Company'),

                            TextInput::make('website_link')->label('Website Link')->type('url'),

                            Textarea::make('about_agency')->label('About Agency'),
                        ])
                        ->visible(fn($get) => $get('company_selection') === 'custom'),



                ])
                ->extraAttributes([
                    'class' => '!p-0 !m-0 !border-0 !shadow-none space-y-0',
                ]),

            Section::make('Select Existing Company')
                ->relationship('companyEmployee')
                ->schema([
                    Select::make('company_user_id')
                        ->label('Search Company')
                        ->preload(false)
                        ->extraAttributes(['data-position' => 'top'])
                        ->options(
                            fn() => User::whereHas('roles', fn($query) => $query->where('name', 'company'))
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->required()
                        ->afterStateHydrated(function (callable $set, callable $get, $state, $record) {
                            if ($record && $record->company_user_id) {
                                $set('company_user_id', $record->company_user_id);
                            }
                        }),

                    Hidden::make('role')->dehydrated(),

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
                        ->visible(fn($get) => $get('role_label') === 'Other')
                        ->afterStateUpdated(fn($state, callable $set) => $set('role', $state))
                        ->required(),

                ])
                ->visible(fn($get) => $get('userMeta.company_selection') === 'select'),









        ])
            ->columns('full')
            ->extraAttributes(['class' => 'user-form']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name")
                    ->searchable(),
                TextColumn::make("last_name")
                    ->searchable(),
                TextColumn::make("email")
                    ->searchable(),
                TextColumn::make("roles.name")
                    ->label("Role")
                    ->sortable()
                    ->searchable(),
                TextColumn::make('userMeta.approval_status')
                    ->badge()
                    ->label("Approval Status")
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'primary',
                        'approved' => 'success',
                        'declined' => 'danger'
                    }),
            ])
            ->filters([
                SelectFilter::make("role")
                    ->relationship("roles", "name"),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListUsers::route("/"),
            "create" => Pages\CreateUser::route("/create"),
            "edit" => Pages\EditUser::route("/{record}/edit"),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->whereDoesntHave('roles', function ($query) {
            $query->where('name', 'company');
        });
    }
}
