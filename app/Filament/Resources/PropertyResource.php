<?php

namespace App\Filament\Resources;

use App\Models\Property;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\PropertyAvailability;
use App\Models\AdditionalLicenceType;
use App\Filament\Resources\PropertyResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use App\Filament\Forms\Components\PropertyBasicDetailsFields;
use App\Filament\Forms\Components\PropertyRealEstateDetailsFields;
use App\Filament\Forms\Components\PropertyAttributeFields;
use App\Filament\Forms\Components\PropertyRoomsFields;
use App\Filament\Forms\Components\PropertySeasonalRatesFields;
use App\Filament\Forms\Components\PropertyBookingRulesFields;
use App\Filament\Forms\Components\PropertyAvailabilityFields;
use App\Filament\Forms\Components\PropertyBasicRatesFields;
use App\Filament\Forms\Components\PropertyLocationFields;
use App\Filament\Forms\Components\PropertyMediaFields;
use App\Filament\Forms\Components\PropertyDescriptionFields;
use App\Filament\Forms\Components\PropertyExtrasFields;
use App\Filament\Forms\Components\PropertyHouseRulesFields;
use App\Filament\Forms\Components\PropertyInstructionsFields;
use App\Filament\Forms\Components\PropertySynchonizationFields;
use App\Models\PropertyType;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component as Livewire;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\IconColumn;
use Carbon\Carbon;
use Filament\Forms\Get;
use App\Forms\Components\CustomTabs;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        if ($recordId = request('record')) {
            $property = static::resolveRecordRouteBinding($recordId);
        } else {
            $property = new Property();
        }

        return $form
            ->schema([
                    // Placeholder::make('loading')
                    // ->label(false)
                    // ->content(fn () => new \Illuminate\Support\HtmlString(
                    //     (new LoadingIndicator())->render()
                    // ))
                    // //->extraAttributes(['wire:loading' => 'true'])
                    // ,

                    CustomTabs::make('Property Details')
                        ->tabs([
                        
                        PropertyBasicDetailsFields::create(),
			
			            PropertyRealEstateDetailsFields::create(),

                        PropertyLocationFields::create($property),

                        PropertyRoomsFields::create($property),

                        PropertyMediaFields::create($property),

                        PropertyDescriptionFields::create(),

                        ...PropertyAttributeFields::create($property),

                        PropertyAvailabilityFields::create(),

                        PropertyBasicRatesFields::create(),

                        PropertySeasonalRatesFields::create($property),

                        PropertyExtrasFields::create(),

                        PropertyBookingRulesFields::create($property),

                        PropertyHouseRulesFields::create(),

                        PropertyInstructionsFields::create($property),

                        PropertySynchonizationFields::create(),
                    ])
                    ->afterStateHydrated(function (Livewire $livewire, string $operation) {
                        //validate if is set record
                        //$record = $livewire->getRecord();

                        if($operation !== 'create'){
                            self::validateTabsAction($livewire);
                            $livewire->validate();
                        }
                    })
                    ,
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('Save')
                        ->extraAttributes([
                            'wire:loading.attr' => 'disabled'
                        ])
                        ->action(fn ($livewire, Get $get) => self::handleSave($livewire, $get))
                        ,
                    Forms\Components\Actions\Action::make('cancel')
                        ->label('Cancel')
                        ->color('gray')
                        ->modalHeading('Are you sure you want to leave?')
                        ->modalDescription('There is unsaved data')
                        ->modalSubmitAction(false)
                        ->modalCancelAction(function (\Filament\Actions\StaticAction $action) {
                            return $action->label('Close');
                        })
                        ->extraModalFooterActions(fn (Action $action): array => [
                            $action
                                ->makeModalSubmitAction('saveAndLeaveAction', arguments: ['method' => 'saveAndLeave'])
                                ->label('Save and Leave')
                                ->color('success'),
                            $action
                                ->makeModalSubmitAction('exitAction', arguments: ['method' => 'exit'])
                                ->label('Exit Without Save')
                                ->color('danger')
                        ])
                        ->action(function ($livewire, array $arguments, Get $get){
                            if ($arguments['method'] == 'saveAndLeave') {
                                self::handleSave($livewire, $get, true);
                            }elseif ($arguments['method'] == 'exit') {
                                return redirect()->route('filament.backend.resources.properties.index');
                            }
                        })
                ]),

            ])
            ->columns('full')
            ->extraAttributes([
                'class' => 'property-form',
                'x-on:form-validation-error.window' => "
                    event.preventDefault();
                    event.stopImmediatePropagation();
                "
            ])
            ;
    }

    public static function table(Table $table): Table
    {
        return $table
                ->modifyQueryUsing(function (Builder $query): Builder {
                    if (Auth::check() && !Auth::user()->hasRole('admin')) {
                        if (Auth::user()->hasRole('manager')) {
                            $companyId = \App\Models\CompanyEmployee::where('employee_user_id', Auth::id())
                                ->value('company_user_id');

                                if ($companyId) {
                                    $hasPropertyManagementCompany = \App\Models\CompanyMeta::where('user_id', $companyId)
                                        ->where('type', 'Property Management Company')
                                        ->exists();
                                    if ($hasPropertyManagementCompany) {
                                        $query->where('user_id', $companyId)
                                              ->orWhere('user_id', Auth::id());
                                    } else {
                                        $query->where('user_id', Auth::id());
                                    }
                                } else {
                                    $query->where('user_id', Auth::id());
                                }
                        } else {
                            $query->where('user_id', Auth::id());
                        }
                    }
            
                    return $query;
                })

            ->columns([
                TextColumn::make('title')->label('Title')->searchable(),
                TextColumn::make('propertyType.name')->label('Property Type')->searchable(),
                TextColumn::make('country')->label('Country')->searchable(),
                // IconColumn::make('is_published')
                //     ->label('Published')
                //     ->trueIcon('heroicon-o-check-circle')
                //     ->falseIcon('heroicon-o-x-circle'),
                TextColumn::make('approval_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'primary',
                        'approved' => 'success',
                        'declined' => 'danger'
                    }),
                IconColumn::make('active')
                    ->label('Active')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make("user")
                    ->relationship("user", "name"),
                SelectFilter::make("property_type_id")
                    ->options(
                        PropertyType::pluck('name', 'id')->toArray()
                    )
                    ->multiple()
                    ->label('Property Type'),
                Filter::make('deal_type_rent')
                    ->form([
                        CheckboxList::make('deal_type')
                            ->options([
                                'deal_type_rent' => 'Rent',
                                'deal_type_sale' => 'Sale',
                            ])
                            ->columns(2)
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                in_array('deal_type_rent', $data['deal_type'] ?? []),
                                fn (Builder $query): Builder => $query->where('deal_type_rent', true),
                            )
                            ->when(
                                in_array('deal_type_sale', $data['deal_type'] ?? []),
                                fn (Builder $query): Builder => $query->where('deal_type_sale', true),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (empty($data['deal_type'])) {
                            return null;
                        }

                        $dealTypes = [];

                        if (in_array('deal_type_rent', $data['deal_type'] ?? [])) {
                            $dealTypes[] = 'Rent';
                        }

                        if (in_array('deal_type_sale', $data['deal_type'] ?? [])) {
                            $dealTypes[] = 'Sale';
                        }

                        return 'Deal Type: ' . implode(', ', $dealTypes);
                    }),
                Filter::make('available')
                    ->form([
                        Section::make('Available')
                            ->schema([
                                Forms\Components\DatePicker::make('date_from')
                                    ->native(false),
                                Forms\Components\DatePicker::make('date_to')
                                    ->native(false),
                            ])
                            ->columns(2)
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $dateFrom = $data['date_from'] ?? null;
                        $dateTo = $data['date_to'] ?? null;

                        return $query
                            // If 'date_from' is set, filter availability starting from that date
                            ->when(
                                $dateFrom,
                                fn (Builder $query) => $query->whereHas('availabilities', function (Builder $query) use ($dateFrom, $dateTo) {
                                    $query->whereDate('date_from', '<=', $dateTo ?? now()) // if date_to exists, use it, else use 'now'
                                    ->whereDate('date_to', '>=', $dateFrom) // where date_from is less than or equal to date_to
                                    ->where('available', true);
                                })
                                    ->whereDoesntHave('availabilities', function (Builder $query) use ($dateFrom, $dateTo) {
                                        $query->whereDate('date_from', '<=', $dateTo ?? now())
                                            ->whereDate('date_to', '>=', $dateFrom)
                                            ->where('available', false);
                                    }),
                            )
                            // If 'date_to' is set, filter availability until that date
                            ->when(
                                $dateTo,
                                fn (Builder $query) => $query->whereHas('availabilities', function (Builder $query) use ($dateFrom, $dateTo) {
                                    $query->whereDate('date_from', '<=', $dateTo) // where date_from is less than or equal to date_to
                                    ->whereDate('date_to', '>=', $dateFrom ?? now()) // if date_from exists, use it, else use 'now'
                                    ->where('available', true);
                                })
                                    ->whereDoesntHave('availabilities', function (Builder $query) use ($dateFrom, $dateTo) {
                                        $query->whereDate('date_from', '<=', $dateTo)
                                            ->whereDate('date_to', '>=', $dateFrom ?? now())
                                            ->where('available', false);
                                    }),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (empty($data['date_from']) && empty($data['date_to'])) {
                            return null;
                        }

                        $dateFrom = $data['date_from'] ? Carbon::parse($data['date_from'])->toFormattedDateString() : null;
                        $dateTo = $data['date_to'] ? Carbon::parse($data['date_to'])->toFormattedDateString() : null;

                        if ($dateFrom && $dateTo) {
                            return 'Available from ' . $dateFrom . ' to ' . $dateTo;
                        }

                        if ($dateFrom) {
                            return 'Available from ' . $dateFrom;
                        }

                        if ($dateTo) {
                            return 'Available until ' . $dateTo;
                        }

                        return null;
                    }),
                Filter::make('active_inactive')
                    ->form([
                        CheckboxList::make('active_inactive')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default(['active'])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $filters = $data['active_inactive'] ?? [];

                        if (in_array('active', $filters) && in_array('inactive', $filters)) {
                            return $query;
                        }

                        return $query
                            ->when(
                                in_array('active', $filters),
                                fn (Builder $query): Builder => $query->orWhere('active', 1),
                            )
                            ->when(
                                in_array('inactive', $filters),
                                fn (Builder $query): Builder => $query->orWhere('active', 0),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (empty($data['active_inactive'])) {
                            return null;
                        }

                        $statuses = [];

                        if (in_array('active', $data['active_inactive'] ?? [])) {
                            $statuses[] = 'Active';
                        }

                        if (in_array('inactive', $data['active_inactive'] ?? [])) {
                            $statuses[] = 'Inactive';
                        }

                        return 'Status: ' . implode(', ', $statuses);
                    })
            ])
            ->actions([Tables\Actions\EditAction::make()])
            // ->bulkActions([
            //     BulkAction::make('approve')
            //         ->label('Approve')
            //         ->color('success')
            //         ->icon('heroicon-o-check-circle')
            //         ->action(function ($records) {
            //             foreach ($records as $record) {
            //                 $record->approval_status = 'approved';
            //                 $record->save();
            //             }

            //             Notification::make()
            //                 ->title('Properties approved!')
            //                 ->success()
            //                 ->send();
            //         })
            //         ->requiresConfirmation(),
            // ])
            ;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        if(Auth::check() && Auth::user()->hasAnyRole(['admin', 'property_owner', 'manager']))
        {
            return true;
        }

        return false;
    }

    // public static function validateTabsAction($livewire, $tabName = null)
    // {
    //     $tabs = $livewire->form->getComponents()[0]->getChildComponents();
    //     $tabValidationStates = [];
    //     $isValid = true;

    //     foreach ($tabs as $tab) {
    //         if (!$tab instanceof \Filament\Forms\Components\Tabs\Tab) {
    //             continue;
    //         }

    //         if ($tabName && strtolower(str_replace(' ', '_', $tab->getLabel())) !== strtolower(str_replace(' ', '_', $tabName)) ) {
    //             continue;
    //         }

    //         if( !$tab->isVisible() ){
    //             continue;
    //         }

    //         $tabNameFormatted = strtolower(str_replace(' ', '_', $tab->getLabel()));
    //         $tabFields = $tab->getChildComponents();

    //         $validationResult = self::validateTabFields($livewire, $tabFields);

    //         if($validationResult['isEmpty']) {
    //             $tabValidationStates[$tabNameFormatted] = 'empty';
    //             if((!$validationResult['isValid'])){
    //                 $isValid = false;
    //             }
    //         }elseif ($validationResult['isValid']) {
    //             $tabValidationStates[$tabNameFormatted] = 'success';
    //         }elseif(!$validationResult['isValid']){
    //             $tabValidationStates[$tabNameFormatted] = 'error';
    //             $isValid = false;
    //         }else{
    //             $tabValidationStates[$tabNameFormatted] = 'empty';
    //         }
    //     }

    //     foreach ($tabValidationStates as $tabName => $icon) {
    //         $livewire->data["tab_icon_{$tabName}"] = $icon;
    //     }

    //     // dd($tabValidationStates);
    //     return $isValid;
    // }


    public static function validateTabsAction($livewire, $tabName = null)
    {
        $tabs = $livewire->form->getComponents()[0]->getChildComponents();
        $tabValidationStates = [];
        $isValid = true;

        foreach ($tabs as $tab) {
            if (!$tab instanceof \Filament\Forms\Components\Tabs\Tab) {
                continue;
            }

            if ($tabName && strtolower(str_replace(' ', '_', $tab->getLabel())) !== strtolower(str_replace(' ', '_', $tabName)) ) {
                continue;
            }

            if( !$tab->isVisible() ){
                continue;
            }

            $tabNameFormatted = strtolower(str_replace(' ', '_', $tab->getLabel()));
            // Log::info('TAB NAME: ' . $tabNameFormatted);
            $validationResult = self::validateTabFields($livewire, $livewire->data, $tabNameFormatted);

            if($validationResult['isEmpty']) {
                $tabValidationStates[$tabNameFormatted] = 'empty';
                if((!$validationResult['isValid'])){
                    $isValid = false;
                }
            }elseif ($validationResult['isValid']) {
                $tabValidationStates[$tabNameFormatted] = 'success';
            }elseif(!$validationResult['isValid']){
                $tabValidationStates[$tabNameFormatted] = 'error';
                $isValid = false;
            }else{
                $tabValidationStates[$tabNameFormatted] = 'empty';
            }
        }

        foreach ($tabValidationStates as $tabName => $icon) {
            $livewire->data["tab_icon_{$tabName}"] = $icon;
        }

        // dd($tabValidationStates);
        return $isValid;
    }


    public static function getTabIcon($status)
    {
        switch ($status) {
            case 'empty':
                return 'icon-circle-outlined';
            case 'success':
                return 'icon-check-circle';
            case 'error':
                return 'icon-warning-triangle';
            default:
                return 'icon-circle-outlined';
        }
    }

    // protected static function validateTabFields($livewire, $tabFields)
    // {
    //     $isTabValid = true;
    //     $isTabEmpty = true;

    //     foreach ($tabFields as $field) {
    //         $fieldClass = get_class($field);
            
    //         $fieldName = method_exists($field, 'getStatePath') ? $field->getStatePath() : null;

    //         if ($field instanceof \Filament\Forms\Components\Hidden
    //             || $field instanceof \Filament\Forms\Components\Placeholder
    //             || $field instanceof \Filament\Forms\Components\Toggle
    //             || $field instanceof \KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox
    //             ) {

    //             continue;
    //         }

    //         if ($field->isHidden()) {
    //         continue;
    //         }

    //         if (method_exists($field, 'isDisabled') && $field->isDisabled()) {
    //             continue;
    //         }

    //         $nestedFields = $field->getChildComponents();

    //         if (!empty($nestedFields)) {

    //             if ($field instanceof \Filament\Forms\Components\Repeater) {
    //                 $repeaterState = $field->getState() ?? [];

    //                 try {
    //                     $livewire->validateOnly($field->getStatePath());
    //                 } catch (\Illuminate\Validation\ValidationException $e) {
    //                     $isTabValid = false;
    //                 }

    //                 // Check repeater fields for special validation
    //                 if (
    //                     $fieldName === 'data.synchronisation'
    //                     || $fieldName === 'data.available_periods'
    //                     || $fieldName === 'data.unavailable_periods'
    //                     || $fieldName === 'data.bedrooms'
    //                     || $fieldName === 'data.bathrooms'
    //                     || $fieldName === 'data.kitchens'
    //                     || $fieldName === 'data.other_rooms'
    //                     || $fieldName === 'data.propertyLicences'
    //                     ) {
    //                     foreach ($repeaterState as $rowIndex => $row) {

    //                         foreach ($nestedFields as $nestedField) {
    //                             $nestedFieldName = $nestedField->getStatePath();
    //                             $nestedFieldValue = $row[$nestedField->getName()] ?? null;

    //                             // Skip disabled fields in this specific repeater
    //                             if ($nestedField->isDisabled()) {
    //                                 continue;
    //                             }

    //                             if ($nestedField->isHidden()) {
    //                                 continue;
    //                             }

    //                             if (Str::of($nestedFieldName)->contains('synchronization_id')) {
    //                                 continue;
    //                             }

    //                             // If the nested field has value, set isTabEmpty to false
    //                             if (!empty($nestedFieldValue)) {
    //                                 $isTabEmpty = false;
    //                             }

    //                             try {
    //                                 $livewire->validateOnly($nestedFieldName);
    //                             } catch (\Illuminate\Validation\ValidationException $e) {
    //                                 $isTabValid = false;
    //                             }
    //                         }
    //                     }
    //                 } else {
    //                     // For other repeaters, simply check if there are entries and set isTabEmpty to false
    //                     if (!empty($repeaterState)) {
    //                         $isTabEmpty = false; // Set isEmpty to false immediately when there are entries in the repeater
    //                     }
    //                 }

    //                 continue;
    //             }

    //             // For non-repeater fields, validate nested fields normally
    //             $nestedValidation = self::validateTabFields($livewire, $nestedFields);

    //             if (!$nestedValidation['isValid']) {
    //                 $isTabValid = false;
    //             }

    //             if (!$nestedValidation['isEmpty']) {
    //                 $isTabEmpty = false;
    //             }

    //             continue;
    //         }


    //         if (method_exists($field, 'getStatePath')) {
    //             $fieldValue = $field->getState();

    //             if (
    //                 $fieldName == 'data.latitude'
    //                 || $fieldName == 'data.longitude'
    //                 || $fieldName == 'data.coordinates'
    //                 || $fieldName == 'data.bedroom_count'
    //                 || $fieldName == 'data.bathroom_count'
    //                 || $fieldName == 'data.kitchen_count'
    //                 || $fieldName == 'data.planyo_sync'
    //                 ) {
    //                 continue;
    //             }

    //             if (!is_null($fieldValue) && $fieldValue !== '' && !is_array($fieldValue)) {
    //                 $isTabEmpty = false;
    //             }

    //             if (is_array($fieldValue) && !empty($fieldValue)) {
    //                 $isTabEmpty = false;
    //             }

    //             try {
    //                 $livewire->validateOnly($fieldName);
    //             } catch (\Illuminate\Validation\ValidationException $e) {
    //                 $isTabValid = false;
    //             }
    //         }
    //     }

    //     return ['isValid' => $isTabValid, 'isEmpty' => $isTabEmpty];
    // }

    // protected static function validateTabFields($data, $tabName)
    // {
    //     $isTabValid = true;
    //     $isTabEmpty = true;
    
    //     $allValidationRules = [
    //         // Basic Deatail Tab
    //         'user_id' => ['required', 'exists:users,id'],
    //         'property_class' => ['required', 'in:residential,commercial,land,other'],
    //         'deal_type' => ['required', 'array', 'min:1'],
    //         'deal_type.*' => ['in:deal_type_rent,deal_type_monthly_rent,deal_type_sale'],
    //         'property_type_id' => ['required_unless:property_class,other', 'nullable', 'exists:property_types,id'],
    //         'property_type_custom' => ['required_if:property_class,other', 'nullable', 'string', 'max:255'],
    //         'title' => ['required', 'string', 'max:255'],
    //         'floorspace' => ['required', 'numeric', 'min:0'],
    //         'floorspace_units' => ['required', 'in:m2,ft2'],
    //         'grounds' => ['nullable', 'numeric', 'min:0'],
    //         'grounds_units' => ['nullable', 'in:m2,ft2'],
    //         'floors_in_building' => ['nullable', 'integer', 'min:1'],
    //         'rental_licence_type_id' => ['required'],
    //         'rental_licence_number' => ['required'],
    //         'data.basic_rate_commission_id' => ['required'],

    //         //Real Estate detail tab
    //         'data.floor_plan_list' => ['nullable', 'array', 'max:20'],
    //         'data.floor_plan_list.*' => ['nullable', 'file', 'image', 'max:10240'],
    //         'data.year_of_construction' => ['nullable', 'in:' . implode(',', range(date('Y'), 1800))],
    //         'data.year_of_renovation' => ['nullable', 'in:' . implode(',', range(date('Y'), 1800))],
    //         'data.kitchen_area_size' => ['nullable', 'numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/', 'min:0', 'max:999.99'],
    //         'data.kitchen_area_units' => ['nullable', 'in:m2,ft2'],
    //         'data.living_area_size' => ['nullable', 'numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/', 'min:0'],
    //         'data.living_area_units' => ['nullable', 'in:m2,ft2'],
    //         'data.heating_features' => ['nullable', 'string'],
    //         'data.radio_heating_features' => ['nullable', 'in:deisel,gas,air_conditioner,other'],
    //         'data.other_heating_features' => ['nullable', 'string'],
    //         'data.aditional_features' => ['nullable', 'array'],
    //         'data.aditional_features.*' => ['in:new_development,renovated,luxurious,unfinished,under_construction,neoclassical,empty'],
    //         'data.suitable_for' => ['nullable', 'array'],
    //         'data.suitable_for.*' => ['in:holiday_home,investment,student,professional_use,tourist_rental,other'],
    //         'data.suitable_for_custom' => ['nullable', 'string'],

    //         //Location tab
    //         'latitude' => ['required'],
    //         'longitude' => ['required','numeric'],
    //         'country' => ['required','string','max:100'],
    //         'city' => ['required','string','max:100'],
    //         'street' => ['required','string','max:255'],
    //         'address' => ['nullable','string','max:255'],
    //         'apartment_floor_building' => ['nullable','string','max:100'],
    //         'state_or_region' => ['required','string','max:100'],
    //         'postal_code' => ['required','string','max:6'], 
    //         'orientation' => ['nullable','string','in:East,East West,East meridian,North,North east,North west,West,West meridian,Meridian,South,South east,South west'],

    //         // 'data.email' => 'required|email',
    //         // 'data.phone' => 'required|string|min:10',
    //         // 'data.name' => 'required|string|max:255',
    //         // 'data.address' => 'nullable|string|max:255',
    //         // 'data.postcode' => 'nullable|string|max:10',
    //         // 'data.city' => 'nullable|string|max:255',
    //         // // Repeater nested fields
    //         // 'data.bedrooms.*.name' => 'required|string|max:255',
    //         // 'data.bedrooms.*.size' => 'nullable|numeric|min:0',
    //         // 'data.synchronisation.*.sync_id' => 'nullable|integer',
    //         // 'data.unavailable_periods.*.from' => 'required|date',
    //         // 'data.unavailable_periods.*.to' => 'required|date|after_or_equal:data.unavailable_periods.*.from',
    //     ];
    
    //     $validationData = [];
    //     $validationRules = [];
    
    //     dd($livewire->data);
    //     foreach ($tabFields as $field) {
    //         \Log::info('Field: ' . $field->getStatePath());
    //         $fieldName = method_exists($field, 'getStatePath') ? $field->getStatePath() : null;
    
    //         if($fieldName == 'data'){
    //             continue;
    //         }
    //         if (Str::startsWith($fieldName, 'data.')) {
    //              $fieldName = Str::after($fieldName, 'data.');
    //         }

    //         if (!$fieldName) {
    //             continue;
    //         }
    
    //         if (
    //             $field instanceof \Filament\Forms\Components\Hidden ||
    //             $field instanceof \Filament\Forms\Components\Placeholder ||
    //             $field instanceof \Filament\Forms\Components\Toggle ||
    //             $field instanceof \KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox
    //         ) {
    //             continue;
    //         }
    
    //         // if ($field->isHidden()) {
    //         //     continue;
    //         // }
    
    //         // if (method_exists($field, 'isDisabled') && $field->isDisabled()) {
    //         //     continue;
    //         // }
    
    //         $nestedFields = $field->getChildComponents();
    
    //         // === Repeater ===
    //         // if ($field instanceof \Filament\Forms\Components\Repeater) {
    //         //     $repeaterState = $field->getState() ?? [];
    
    //         //     foreach ($repeaterState as $rowIndex => $row) {
    //         //         foreach ($nestedFields as $nestedField) {
    //         //             $nestedFieldName = $nestedField->getName();
    //         //             $nestedFieldPath = "{$fieldName}.{$rowIndex}.{$nestedFieldName}";
    //         //             $flatFieldPath = "{$fieldName}.*.{$nestedFieldName}";
    
    //         //             $nestedFieldValue = $row[$nestedFieldName] ?? null;
    
    //         //             // if ($nestedField->isDisabled() || $nestedField->isHidden()) {
    //         //             //     continue;
    //         //             // }
    
    //         //             if (!empty($nestedFieldValue)) {
    //         //                 $isTabEmpty = false;
    //         //             }
    
    //         //             $validationData[$nestedFieldPath] = $nestedFieldValue;
    
    //         //             if (array_key_exists($flatFieldPath, $allValidationRules)) {
    //         //                 $validationRules[$nestedFieldPath] = $allValidationRules[$flatFieldPath];
    //         //             } else {
    //         //                 $validationRules[$nestedFieldPath] = 'nullable';
    //         //             }
    //         //         }
    //         //     }
    
    //         //     continue;
    //         // }
    
    //         // === Вложенные поля ===
    //         // if (!empty($nestedFields)) {
    //         //     $nestedValidation = self::validateTabFields($livewire, $nestedFields);
    
    //         //     if (!$nestedValidation['isValid']) {
    //         //         $isTabValid = false;
    //         //     }
    
    //         //     if (!$nestedValidation['isEmpty']) {
    //         //         $isTabEmpty = false;
    //         //     }
    
    //         //     continue;
    //         // }
    
    //         // === Звичайні поля ===
    //         $fieldValue = $field->getState();
    
    //         if (!is_null($fieldValue) && $fieldValue !== '' && !is_array($fieldValue)) {
    //             $isTabEmpty = false;
    //         }
    
    //         if (is_array($fieldValue) && !empty($fieldValue)) {
    //             $isTabEmpty = false;
    //         }
    
    //         $validationData[$fieldName] = $fieldValue;
    
    //         if (array_key_exists($fieldName, $allValidationRules)) {
    //             $validationRules[$fieldName] = $allValidationRules[$fieldName];
    //         } else {
    //             $validationRules[$fieldName] = 'nullable';
    //         }
    //     }
    
    //     // === Bulk-валідація ===
    //     if (!empty($validationData)) {
    //         try {
    //             $start = microtime(true);
    
    //             Log::info( "DATA: " . print_r($validationData, true) );
    //             Log::info( "RULES: " . print_r($validationRules, true) );
    //             $validator = Validator::make($validationData, $validationRules);

    //             if ($validator->fails()) {
    //                 $isTabValid = false;
                
    //                 Log::warning("Validation failed for: " . implode(', ', array_keys($validator->failed())));
                
    //                 foreach ($validator->errors()->messages() as $field => $messages) {
    //                     foreach ($messages as $message) {
    //                         Log::warning("Field: {$field}, Error: {$message}");
    //                     }
    //                 }
                
    //                 foreach ($validator->failed() as $field => $failure) {
    //                     Log::warning("Field: $field, Value: " . var_export($validationData[$field] ?? 'null', true));
    //                     Log::debug("Failure structure: " . json_encode($failure));
    //                 }
    //             }
    
    //             $duration = round((microtime(true) - $start) * 1000, 2);
    //             Log::info("Manual validation took {$duration}ms. Fields: " . implode(', ', array_keys($validationData)));
    //         } catch (\Exception $e) {
    //             $isTabValid = false;
    //             Log::error("Validator exception: " . $e->getMessage());
    //         }
    //     }
    
    //     return ['isValid' => $isTabValid, 'isEmpty' => $isTabEmpty];
    // }

    protected static function getValidationRules($livewire, $data): array
    {
        $attributes = Attribute::all();

        $rules = [
            'basic_details' => [
                'user_id' => ['required', 'exists:users,id'],
                'property_class' => ['required', 'in:residential,commercial,land,other'],
                'deal_type' => ['required', 'array', 'min:1'],
                'deal_type.*' => ['in:deal_type_rent,deal_type_monthly_rent,deal_type_sale'],
                'property_type_id' => ['required_unless:property_class,other', 'nullable', 'exists:property_types,id'],
                'property_type_custom' => ['required_if:property_class,other', 'nullable', 'string', 'max:255'],
                'title' => ['required', 'string', 'max:255'],
                'floorspace' => ['required', 'numeric', 'min:0'],
                'floorspace_units' => ['required', 'in:m2,ft2'],
                'grounds' => ['nullable', 'numeric', 'min:0'],
                'grounds_units' => ['nullable', 'in:m2,ft2'],
                'floors_in_building' => ['nullable', 'integer', 'min:1'],
                'floors_of_property' => ['nullable', 'integer', 'min:1'],
                'entrance' => ['nullable', 'string'],
                'rental_licence_type_id' => [
                    function ($attribute, $value, $fail) use ($data) {
                        $dealType = $data['deal_type'] ?? [];
                        if (in_array('deal_type_rent', $dealType) || in_array('deal_type_monthly_rent', $dealType)) {
                            if (empty($value)) {
                                $fail('The rental licence type is required for rental deals.');
                            }
                        }
                    },
                ],
            
                'rental_licence_number' => [
                    function ($attribute, $value, $fail) use ($data) {
                        $dealType = $data['deal_type'] ?? [];
                        if (in_array('deal_type_rent', $dealType) || in_array('deal_type_monthly_rent', $dealType)) {
                            if (empty($value)) {
                                $fail('The rental licence number is required for rental deals.');
                            }
                        }
                    },
                ],
                'basic_rate_commission_id' => [
                    function ($attribute, $value, $fail) use ($data) {
                        $dealType = $data['deal_type'] ?? [];
                        if (in_array('deal_type_rent', $dealType) || in_array('deal_type_monthly_rent', $dealType)) {
                            if (empty($value)) {
                                $fail('basic_rate_commission_id is required for rental deals.');
                            }
                        }
                    },
                ],
                'propertyLicences' => ['required', 'array'],
                'propertyLicences.*.licence_number' => ['required', 'string'],
                // 'propertyLicences.*.licence_file_name' => ['required'],
                'propertyLicences.*.licence_file_name' => [
                    function ($attribute, $value, $fail) use ($data) {
                        $matches = [];
                        if (preg_match('/propertyLicences\.(\d+)\.licence_file_name/', $attribute, $matches)) {
                            $index = (int) $matches[1];
                            $licences = $data['propertyLicences'] ?? [];

                            if (isset($licences[$index]['additional_licence_type_id'])) {
                                $typeId = $licences[$index]['additional_licence_type_id'];
                                $type = AdditionalLicenceType::find($typeId);

                                if ($type && $type->required && $type->file_attachment && empty($value)) {
                                    $fail("The licence file is required for {$type->name}.");
                                }
                            }
                        }
                    },
                ],
                'otherLicenceType' => ['nullable', 'array'],
            ],
            'real_estate_details' => [
                'floor_plan_list' => [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        if (empty($value)) return;
            
                        if (!is_array($value)) {
                            $fail('The floor_plan_list must be an array of images.');
                            return;
                        }
            
                        foreach ($value as $index => $item) {
                            if ($item instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                // MIME
                                $mime = $item->getMimeType();
                                if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])) {
                                    $fail("The file at index $index must be an image or PDF (jpg, jpeg, png, webp, or pdf).");
                                    continue;
                                }
            
                                // Size
                                if ($item->getSize() > 10240 * 1024) {
                                    $fail("The file at index $index must not be greater than 10MB.");
                                    continue;
                                }
                            } else {
                                // UUID проверка
                                $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('uuid', $item)->first();
            
                                if (!$media) {
                                    $fail("Saved file with UUID $item (index $index) not found.");
                                    continue;
                                }
            
                                // MIME
                                $mime = $media->mime_type;
                                if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])) {
                                    $fail("Saved file at index $index must be an image or PDF (jpg, jpeg, png, webp, or pdf).");
                                    continue;
                                }
            
                                // Size
                                if ($media->size > 10240 * 1024) {
                                    $fail("Saved file at index $index must not be greater than 10MB.");
                                    continue;
                                }
                            }
                        }
                    },
                ],
                'year_of_construction' => ['nullable', 'in:' . implode(',', range(date('Y'), 1800))],
                'year_of_renovation' => ['nullable', 'in:' . implode(',', range(date('Y'), 1800))],
                'kitchen_area_size' => ['nullable', 'numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/', 'min:0', 'max:999.99'],
                'kitchen_area_units' => ['nullable', 'in:m2,ft2'],
                'living_area_size' => ['nullable', 'numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/', 'min:0'],
                'living_area_units' => ['nullable', 'in:m2,ft2'],
                'heating_features' => ['nullable', 'string'],
                'other_heating_features' => ['nullable', 'string'],
                'aditional_features' => ['nullable', 'array'],
                'aditional_features.*' => ['in:new_development,renovated,luxurious,unfinished,under_construction,neoclassical,empty'],
                'suitable_for' => ['nullable', 'array'],
                'suitable_for.*' => ['in:holiday_home,investment,student,professional_use,tourist_rental,other'],
                'suitable_for_custom' => ['nullable', 'string'],
                'common_expences' => ['nullable', 'numeric'],
                'price_for_sale_eur' => ['nullable', 'numeric'],
                'price_for_sale_per_sq_m' => ['nullable', 'numeric'],
                'return_on_investment' => ['nullable', 'numeric', 'min:1', 'max:100'],
            ],
            'location' => [
                'latitude' => ['required'],
                'longitude' => ['required','numeric'],
                'country' => ['required','string','max:100'],
                'city' => ['required','string','max:100'],
                'street' => ['required','string','max:255'],
                'address' => ['nullable','string','max:255'],
                'apartment_floor_building' => ['nullable','string','max:100'],
                'state_or_region' => ['required','string','max:100'],
                'postal_code' => ['required','string','max:6'], 
                'orientation' => ['nullable','string','in:East,East West,East meridian,North,North east,North west,West,West meridian,Meridian,South,South east,South west'],

            ],
            'rooms' => [
                'bedrooms' => ['nullable','array'],
                'bedrooms.*.type' => ['required', Rule::in(['Bedroom', 'Living room', 'Other room'])],
                'bedrooms.*.name' => ['nullable', 'string'],
                'bedrooms.*.bunk_bed' => ['required', 'integer', 'min:0', 'max:999'],
                'bedrooms.*.double_bed' => ['required', 'integer', 'min:0', 'max:999'],
                'bedrooms.*.king_sized_bed' => ['required', 'integer', 'min:0', 'max:999'],
                'bedrooms.*.queen_sized_bed' => ['required', 'integer', 'min:0', 'max:999'],
                'bedrooms.*.single_bed_adult' => ['required', 'integer', 'min:0', 'max:999'],
                'bedrooms.*.single_bed_child' => ['required', 'integer', 'min:0', 'max:999'],
                'bedrooms.*.sofa_bed_double' => ['required', 'integer', 'min:0', 'max:999'],
                'bedrooms.*.sofa_bed_single' => ['required', 'integer', 'min:0', 'max:999'],

                'bathrooms' => ['nullable','array'],
                'bathrooms.*.name' => ['nullable', 'string', 'max:255'],
                'bathrooms.*.private' => ['boolean'],
                'bathrooms.*.bathroom_type' => ['required', Rule::in(['En-suite bathroom', 'Full bathroom', 'WC'])],
                'bathrooms.*.toilet' => ['required', Rule::in(['No toilet', 'Toilet'])],
                'bathrooms.*.shower' => ['required', Rule::in(['No shower', 'Separate shower', 'Shower over bath'])],
                'bathrooms.*.bath' => ['required', Rule::in(['Jacuzzi', 'No bath', 'Standard bath', 'Whirlpool'])],

                'kitchens' => ['nullable','array'],
                'kitchens.*.name' => ['nullable', 'string', 'max:255'],
                'kitchens.*.type' => ['required', Rule::in(['Kitchenette', 'Open plan kitchen', 'Outdoor kitchen', 'Separate kitchen'])],

                'other_rooms' => ['array'],
                'other_rooms.*.common_area' => ['nullable', 'boolean'],
                'other_rooms.*.dining_room' => ['nullable', 'boolean'],
                'other_rooms.*.drying_room' => ['nullable', 'boolean'],
                'other_rooms.*.eating_area' => ['nullable', 'boolean'],
                'other_rooms.*.fitness_room' => ['nullable', 'boolean'],
                'other_rooms.*.games_room' => ['nullable', 'boolean'],
                'other_rooms.*.hall' => ['nullable', 'boolean'],
                'other_rooms.*.laundry' => ['nullable', 'boolean'],
                'other_rooms.*.library' => ['nullable', 'boolean'],
                'other_rooms.*.living_room' => ['nullable', 'boolean'],
                'other_rooms.*.lounge' => ['nullable', 'boolean'],
                'other_rooms.*.office' => ['nullable', 'boolean'],
                'other_rooms.*.pantry' => ['nullable', 'boolean'],
                'other_rooms.*.rumpus_room' => ['nullable', 'boolean'],
                'other_rooms.*.sauna' => ['nullable', 'boolean'],
                'other_rooms.*.studio' => ['nullable', 'boolean'],
                'other_rooms.*.study' => ['nullable', 'boolean'],
                'other_rooms.*.tv_room' => ['nullable', 'boolean'],
                'other_rooms.*.work_studio' => ['nullable', 'boolean'],
            ],
            'media' => [
                'primary_image' => [
                    'required',
                    function ($attribute, $value, $fail) use ($livewire) {
                        $file = is_array($value) ? reset($value) : $value;
                
                        if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            // MIME check
                            $mime = $file->getMimeType();
                            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
                                $fail('The file must be an image of type jpg, jpeg, png, or webp.');
                                return;
                            }
                
                            // Size check (in kilobytes)
                            if ($file->getSize() > 10240 * 1024) {
                                $fail('The image must not be greater than 10MB.');
                                return;
                            }
                
                            // Dimension check using Spatie\Image
                            try {
                                $path = $file->getRealPath();
                                $image = \Spatie\Image\Image::load($path);
                
                                $width = $image->getWidth();
                                $height = $image->getHeight();
                
                                if ($width > $height) {
                                    if ($width < 1024 || $height < 768) {
                                        $fail("The image must be at least 1024x768 pixels for horizontal orientation.");
                                    }
                                } else {
                                    if ($width < 768 || $height < 1024) {
                                        $fail("The image must be at least 768x1024 pixels for vertical orientation.");
                                    }
                                }
                            } catch (\Exception $e) {
                                $fail('Failed to read image dimensions.');
                            }
                
                        } else {
                            // Already uploaded file check
                            $model = $livewire->getRecord();
                            $media = $model->getMedia("primary_image")->first();
                
                            if (!$media) {
                                $fail('Uploaded image not found.');
                                return;
                            }
                        }
                    },
                ],
                'gallery_images' => [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        if (empty($value)) return;
                
                        if (!is_array($value)) {
                            $fail('The gallery must be an array of images.');
                            return;
                        }
                
                        foreach ($value as $index => $uuid) {
                            if ($uuid instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                // MIME
                                $mime = $uuid->getMimeType();
                                if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
                                    $fail("The file at index $index must be an image of type jpg, jpeg, png, or webp.");
                                    continue;
                                }
                
                                // Size
                                if ($uuid->getSize() > 10240 * 1024) {
                                    $fail("The file at index $index must not be greater than 10MB.");
                                    continue;
                                }
                
                                // Dimensions
                                try {
                                    $path = $uuid->getRealPath();
                                    $image = \Spatie\Image\Image::load($path);
                
                                    $width = $image->getWidth();
                                    $height = $image->getHeight();
                
                                    if ($width > $height) {
                                        if ($width < 1024 || $height < 768) {
                                            $fail("Image at index $index must be at least 1024x768 pixels for horizontal orientation.");
                                        }
                                    } else {
                                        if ($width < 768 || $height < 1024) {
                                            $fail("Image at index $index must be at least 768x1024 pixels for vertical orientation.");
                                        }
                                    }
                                } catch (\Exception $e) {
                                    $fail("Could not read image at index $index.");
                                }
                            } else {
                                $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('uuid', $uuid)->first();
                
                                if (!$media) {
                                    $fail("Saved image with UUID $uuid (index $index) not found.");
                                }
                            }
                        }
                    },
                ],
                'sitesContent' => ['nullable', 'array'],
                'sitesContent.*.property_site_id' => ['required', 'integer', 'exists:property_sites,id'],
                'sitesContent.*.content' => ['required', 'url'],
            ],
            'description' => [
                'brief_description' => ['nullable', 'string'],
                'commercial_title' => ['nullable', 'string', 'max:255'],
                'headline' => ['required', 'string', 'max:255'],
                'short_summary' => ['required', 'string'],
                'description' => ['required', 'string'],
            ],
            'availability' => [
                'date_for_sale' => [
                    'nullable',
                    'date',
                    function ($attribute, $value, $fail) use ($data) {
                        $dealType = $data['deal_type'] ?? [];
                    
                        if (is_array($dealType) && in_array('deal_type_sale', $dealType) && empty($value)) {
                            $fail('The Available for sale date is required when sale deal type is selected.');
                        }
                    }
                ],
                'available_periods' => [
                    'array',
                    function ($attribute, $value, $fail) {
                        $overlap = PropertyAvailability::checkPeriodOverlap($value);
                        if ($overlap) {
                            $fail("The available period {$overlap['start']} - {$overlap['end']} overlaps with another period {$overlap['overlapping_period']}.");
                        }
                    },
                ],
                'available_periods.*.date_from' => ['required', 'date'],
                'available_periods.*.date_to' => ['required', 'date', 'after:available_periods.*.date_from'],
            
                'unavailable_periods' => [
                    'array',
                    function ($attribute, $value, $fail) {
                        $overlap = PropertyAvailability::checkPeriodOverlap($value);
                        if ($overlap) {
                            $fail("The unavailable period {$overlap['start']} - {$overlap['end']} overlaps with another period {$overlap['overlapping_period']}.");
                        }
                    },
                ],
                'unavailable_periods.*.date_from' => ['required', 'date'],
                'unavailable_periods.*.date_to' => ['required', 'date', 'after:unavailable_periods.*.date_from'],
            ],
            'basic_rates' => [
                'basic_night_net' => [
                    function ($attribute, $value, $fail) use ($data) {
                        $dealType = $data['deal_type'] ?? [];
                        if (in_array('deal_type_rent', $dealType)) {
                            if (is_null($value)) {
                                return $fail('The basic night net rate is required for rental deals.');
                            }
                            if (!is_numeric($value)) {
                                return $fail('The basic night net rate must be a number.');
                            }
                        }
                    }
                ],
                'basic_night_gross' => [
                    function ($attribute, $value, $fail) use ($data) {
                        $dealType = $data['deal_type'] ?? [];
                        if (in_array('deal_type_rent', $dealType)) {
                            if (is_null($value)) {
                                return $fail('The basic night gross rate is required for rental deals.');
                            }
                            if (!is_numeric($value)) {
                                return $fail('The basic night gross rate must be a number.');
                            }
                        }
                    }
                ],
                'weekend_night_net' => [
                    function ($attribute, $value, $fail) use ($data) {
                        $dealType = $data['deal_type'] ?? [];
                        if (in_array('deal_type_rent', $dealType)) {
                            if (is_null($value)) {
                                return $fail('The weekend night net rate is required for rental deals.');
                            }
                            if (!is_numeric($value)) {
                                return $fail('The weekend night net rate must be a number.');
                            }
                        }
                    }
                ],
                'weekend_night_gross' => [
                    function ($attribute, $value, $fail) {
                        if (!is_null($value) && !is_numeric($value)) {
                            $fail('The weekend night gross rate must be a number.');
                        }
                    }
                ],
                'weekly_discount' => [
                    function ($attribute, $value, $fail) {
                        if (!is_null($value)) {
                            if (!is_numeric($value)) {
                                return $fail('The weekly discount must be a number.');
                            }
                            if ($value < 0 || $value > 100) {
                                return $fail('The weekly discount must be between 0 and 100.');
                            }
                        }
                    }
                ],
                'monthly_discount' => [
                    function ($attribute, $value, $fail) {
                        if (!is_null($value)) {
                            if (!is_numeric($value)) {
                                return $fail('The monthly discount must be a number.');
                            }
                            if ($value < 0 || $value > 100) {
                                return $fail('The monthly discount must be between 0 and 100.');
                            }
                        }
                    }
                ],
                'monthly_rate' => [
                    function ($attribute, $value, $fail) {
                        if (!is_null($value) && (!is_numeric($value) || $value < 0)) {
                            $fail('The monthly rate must be a number greater than or equal to 0.');
                        }
                    }
                ],
                'monthly_rate_sqm' => [
                    function ($attribute, $value, $fail) {
                        if (!is_null($value) && (!is_numeric($value) || $value < 0)) {
                            $fail('The monthly rate per sqm must be a number greater than or equal to 0.');
                        }
                    }
                ],
                'max_guests' => [
                    function ($attribute, $value, $fail) use ($data) {
                        $dealType = $data['deal_type'] ?? [];
                        if (in_array('deal_type_monthly_rent', $dealType)) {
                            if (is_null($value)) {
                                return $fail('The maximum number of guests is required for monthly rent deals.');
                            }
                        }
                        if (!is_null($value)) {
                            if (!is_numeric($value)) {
                                return $fail('The maximum number of guests must be a number.');
                            }
                            if ($value < 0) {
                                return $fail('The maximum number of guests must be at least 0.');
                            }
                        }
                    }
                ],
                'min_stay_nights' => [
                    function ($attribute, $value, $fail) use ($data) {
                        $dealType = $data['deal_type'] ?? [];
                        if (in_array('deal_type_rent', $dealType)) {
                            if (is_null($value)) {
                                return $fail('The minimum stay nights is required for rental deals.');
                            }
                            if (!is_numeric($value) || $value < 1) {
                                return $fail('The minimum stay nights must be a number greater than or equal to 1.');
                            }
                        }
                    }
                ],
                'max_stay_nights' => [
                    function ($attribute, $value, $fail) use ($data) {
                        $dealType = $data['deal_type'] ?? [];
                        if (in_array('deal_type_rent', $dealType)) {
                            if (is_null($value)) {
                                return $fail('The maximum stay nights is required for rental deals.');
                            }
                            if (!is_numeric($value) || $value < 1) {
                                return $fail('The maximum stay nights must be a number greater than or equal to 1.');
                            }
                        }
                    }
                ],
                'check_in_days' => [
                    function ($attribute, $value, $fail) use ($data) {
                        $dealType = $data['deal_type'] ?? [];
                        if (in_array('deal_type_rent', $dealType)) {
                            if (!is_array($value) || count($value) < 1) {
                                return $fail('Check-in days are required and must contain at least 1 item for rental deals.');
                            }
                        }
                    }
                ],
                'check_out_days' => [
                    function ($attribute, $value, $fail) use ($data) {
                        $dealType = $data['deal_type'] ?? [];
                        if (in_array('deal_type_rent', $dealType)) {
                            if (!is_array($value) || count($value) < 1) {
                                return $fail('Check-out days are required and must contain at least 1 item for rental deals.');
                            }
                        }
                    }
                ],
            ],
            'seasonal_rates' => [
                'property_seasons' => [
                    'array',
                    function ($attribute, $value, $fail) {
                        $filteredSeasons = array_filter($value, function ($season) {
                            return empty($season['discount']);
                        });
            
                        $overlap = PropertyAvailability::checkPeriodOverlap($filteredSeasons);
                        if ($overlap) {
                            $fail("The season period {$overlap['start']} - {$overlap['end']} overlaps with another season period {$overlap['overlapping_period']}");
                        }
                    },
                ],
            ],
            'extras' => [
                'is_cleaning' => ['nullable', 'boolean'],
                'taxes' => ['nullable', 'array'],
                'extras' => ['nullable', 'array']
            ],
            'booking_rules' => [
                'cancellation_policy' => ['required'],
                'additional_policy' => [
                    'required_if:cancellation_policy,!=,null',
                ],
                'rates_increase' => [
                    'required_if:additional_policy,!=,null',
                    'nullable',
                    'numeric',
                    'min:0',
                    'exclude_unless:additional_policy,!=,non_refundable'
                ],
                'rates_decrease' => [
                    'required_if:additional_policy,non_refundable',
                    'nullable',
                    'numeric',
                    'min:0',
                    'exclude_unless:additional_policy,non_refundable'
                ],
                'additional_policy_2' => ['nullable'],
                'rates_increase_2' => ['nullable','numeric','min:0'],
                'rates_decrease_2' => ['nullable','numeric','min:0'],
                'booking_rules' => ['nullable', 'array']
            ],
            'house_rules' => [
                'suitable_for_kids'   => ['required', 'string', 'in:welcome,great,not_suitable'],
                'events_allowed'      => ['nullable', 'boolean'],
                'pets'                => ['nullable', 'boolean'],
                'max_pets'            => ['required_if:pets,true', 'numeric', 'min:0'],
                'pets_fee'            => ['nullable', 'boolean'],
                'wheelchair_access'   => ['nullable', 'boolean'],
                'smoking_allowed'     => ['required', 'string', 'in:no_smoking,allowed,outside'],
                'camera'              => ['nullable', 'string', 'in:inside,no,outside'],
                'noise_monitor'       => ['nullable', 'boolean'],
                'house_rules'         => ['nullable', 'string'],
            ],
            'instructions' => [
                'instructions' => ['array'],
                'instructions.check_in' => ['required'],
                'instructions.check_out' => ['required'],
                'instructions.check_in_contact_person' => ['required', 'string', 'max:255'],
                'instructions.key_collection_point' => ['required', 'string'],
                'instructions.telephone_number' => ['required', 'string', 'regex:/^\+?[0-9\s\-().]{7,20}$/'],
                'instructions.check_in_instructions' => ['nullable', 'string'],
                'instructions.attached_instructions' => ['nullable', 'array'],
                'instructions.attached_instructions.*' => ['nullable'],
                'instructions.closest_airports' => ['nullable', 'array'],
                'instructions.closest_airports.*' => ['string'],
                'instructions.directions' => ['nullable', 'string'],
            ],
            'synchronisation' => [
                'synchronisation' => ['nullable', 'array'],
                'synchronisation.*.url' => ['nullable', 'string'],
                'synchronisation.*.site_id' => ['nullable', 'string'],
                'calendar' => ['nullable', 'array'],
                'export_ical_url' => ['nullable', 'string'],
            ]
        ];

        $attrTabsValidationRules = [];

        $attributeGroups = AttributeGroup::with('attributes')->get();

        foreach ($attributeGroups as $group) {
            $groupRules = [];

            foreach ($group->attributes as $attribute) {
                $field = "property_attributes[{$attribute->id}]";

                $attrRules = match ($attribute->type) {
                    'text', 'textarea', 'select' => ['string'],
                    'number' => ['numeric', 'min:0'],
                    'checkbox' => ['boolean'],
                    'multi-checkbox' => ['array'],
                    default => ['nullable'],
                };

                if ($attribute->is_required) {
                    array_unshift($attrRules, 'required');
                } else {
                    array_unshift($attrRules, 'nullable');
                }

                $groupRules[$field] = $attrRules;
            }

            $tabKey = Str::slug($group->name, '_');
            $attrTabsValidationRules[$tabKey] = $groupRules;
        }
        

        // dd($tabsValidationRules);

        return array_merge($rules, $attrTabsValidationRules);
    }

    protected static function validateTabFields($livewire, array $data, string $tabName): array
    {
        $isTabValid = true;
        $isTabEmpty = true;
        // dd($data);

        $allValidationRules = self::getValidationRules($livewire, $data);

        $tabRules = $allValidationRules[$tabName] ?? [];

        if (empty($tabRules)) {
            // Log::warning("No validation rules defined for tab: $tabName");
            return ['isValid' => true, 'isEmpty' => true];
        }

        $validationData = [];

        foreach ($tabRules as $field => $rules) {
            $value = data_get($data, $field);

            if (!is_null($value) && $value !== '' && !is_array($value)) {
                // dd($value);
                $isTabEmpty = false;
            }

            if (is_array($value) && !empty($value)) {
            //    dd("array " . print_r($value, true));
                $isTabEmpty = false;
            }

            $validationData[$field] = $value;
        }

        try {
            // dd($validationData, $tabRules);
            $validator = Validator::make($validationData, $tabRules);

            if ($validator->fails()) {
                $isTabValid = false;

                Log::warning("Validation failed for tab '$tabName':", $validator->errors()->toArray());
            }
        } catch (\Exception $e) {
            $isTabValid = false;
            // Log::error("Validator exception: " . $e->getMessage());
        }

        // dd('is_empty ' . $isTabEmpty);
        return ['isValid' => $isTabValid, 'isEmpty' => $isTabEmpty];
    }
    


    public static function handleSave($livewire, $get, $redirect = false)
    {
		$record = $livewire->getRecord();

        if (!$get('title')) {
            Notification::make()
                ->title('Fill the title')
                ->warning()
                ->send();
            return;
        }

        $isValid = false;

        try {
            $livewire->validate();
            $isValid = true;
        } catch (\Throwable) {
            $isValid = false;
        }

        if ($record) {
			$record->fill($livewire->data)->saveQuietly();
            $record->savePropertyData($livewire->data);
        
            if ($isValid) {
                
                if ($redirect) {
                    redirect()->route('filament.backend.resources.properties.index');
                }

                Notification::make()
                    ->title('Property saved')
                    ->success()
                    ->send();
            } else {
                $isSetToPending = false;
				
                if (isset($livewire->data['approval_status']) && $livewire->data['approval_status'] === 'approved') {
                    // $livewire->data['approval_status'] = 'pending';
					$record->approval_status = 'pending';
                    $record->saveQuietly();
                    $isSetToPending = true;
                }

                if ($redirect) {
                    redirect()->route('filament.backend.resources.properties.index');
                }

                Notification::make()
                    ->title('The property saved as draft')
                    ->warning()
                    ->send();

                if ($isSetToPending) {
                    Notification::make()
                        ->title('The property status has been changed to pending.')
                        ->warning()
                        ->send();
                }
            }

			$livewire->form->model($record)->saveRelationships();
            $livewire->validate();

        } else {
            $livewire->data['approval_status'] = 'pending';
            $modelClass = static::getModel();
            $newRecord = $modelClass::create($livewire->data);
            if ($newRecord->exists) {
                $newRecord->saveQuietly();
                $newRecord->savePropertyData($livewire->data);

                $livewire->record = $newRecord;
                $livewire->form->model($newRecord)->saveRelationships();
                $livewire->record->refresh();

                // $livewire->dispatch('$refresh');
				
				Notification::make()
                    ->title('The property created')
                    ->warning()
                    ->send();

                    if ($redirect) {
                        redirect()->route('filament.backend.resources.properties.index');
                    }

                //return redirect()->route('filament.backend.resources.properties.edit', ['record' => $newRecord->id]);
            }

            if ($redirect) {
                redirect()->route('filament.backend.resources.properties.index');
            }
        }
    }

}
