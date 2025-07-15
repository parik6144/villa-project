<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Placeholder;
use Filament\Support\Enums\IconPosition;
use Filament\Forms\Components\Hidden;
use App\Filament\Resources\PropertyResource;
use Illuminate\Support\Facades\Auth;
use App\Models\LicenceType;
use App\Models\BasicRateCommission;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;
use Illuminate\Support\HtmlString;
use Spatie\Image\Image;
use Closure;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PropertyRealEstateDetailsFields
{
	private static $tabTitle = 'Real estate details';

	public static function create(): Tab
	{
		return
			Tabs\Tab::make(self::$tabTitle)
			->icon(fn(Get $get) => PropertyResource::getTabIcon($get('tab_icon_real_estate_details')))
			->iconPosition(IconPosition::After)
			->columns(4)
			->schema([

				Section::make()
					->label('Floor plan list')
					->schema([
						SpatieMediaLibraryFileUpload::make('floor_plan_list')
							->disk('r2')
							->collection('floor-plan-list-gallery')
							->image()
							->maxSize(10240)
							->maxFiles(20)
							->multiple()
							->panelLayout('grid')
							->reorderable()
							->appendFiles()
							->columnSpan(4)
							->live(true)
							->afterStateUpdated(function ($livewire, $component) {
								// Removed validation calls to improve performance
								// PropertyResource::validateTabsAction($livewire, self::$tabTitle);
								// $livewire->validateOnly($component->getStatePath());
							})
							->getUploadedFileUsing(function (Set $set, $record, $state, string $file, $livewire) {
                                $media = Media::where('uuid', $file)->first();

                                if ($media === null) {
                                    return [];
                                }

                                return [
                                    'name' => $media->getAttribute('file_name'),
                                    'url'  => $media->getFullUrl(),
                                ];
                            })
					]),

				Select::make('year_of_construction')
					->label('Year of Construction')
					->options(array_combine(range(date('Y'), 1800), range(date('Y'), 1800)))
					->searchable()
					->columnSpan(2)
					->nullable()
					// ->live(true)
					// ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
					// 	$set('year_of_construction', $state ?: null);
					// 	PropertyResource::validateTabsAction($livewire, self::$tabTitle);
					// 	$livewire->validateOnly($component->getStatePath());
					// })
					,

				Select::make('year_of_renovation')
					->label('Year of renovation')
					->options(array_combine(range(date('Y'), 1800), range(date('Y'), 1800)))
					->searchable()
					->columnSpan(2)
					->nullable()
					// ->live(true)
					// ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
					// 	$set('year_of_renovation', $state ?: null);
					// 	PropertyResource::validateTabsAction($livewire, self::$tabTitle);
					// 	$livewire->validateOnly($component->getStatePath());
					// })
					,

				TextInput::make('kitchen_area_size')
					->label('Kitchen area size')
					->numeric()
					->inputMode('decimal')
					// ->live(onBlur: true)
					->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none'])
					->minValue(0)
					->maxValue(999.99)
					->step(0.01)
					->columnSpan(1)
					->nullable()
					->rules(['numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/'])
					->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
						$set('kitchen_area_size', $state ?: null);
						if (!preg_match('/^\d{1,3}(\.\d{1,2})?$/', $state)) {
							$set('living_area_size', null);
						}
						// Removed validation calls to improve performance
						// PropertyResource::validateTabsAction($livewire, self::$tabTitle);
						// $livewire->validateOnly($component->getStatePath());
					}),

				Select::make('kitchen_area_units')
					->label('Kitchen area Units')
					->options([
						'm2' => 'm²',
						'ft2' => 'ft²'
					])
					->columnSpan(1)
					->nullable()
					// ->live(true)
					->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
						$set('kitchen_area_units', $state ?: null);
						// Removed validation calls to improve performance
						// PropertyResource::validateTabsAction($livewire, self::$tabTitle);
						// $livewire->validateOnly($component->getStatePath());
					}),

				TextInput::make('living_area_size')
					->label('Living room area size')
					->numeric()
					->inputMode('decimal')
					// ->live(onBlur: true)
					->minValue(0)
					->step(0.01)
					->columnSpan(1)
					->nullable()
					->rules(['numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/'])
					->extraInputAttributes([
						'min' => '0',
						'class' => '[&::-webkit-inner-spin-button]:appearance-none',
					])
					->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
						$set('living_area_size', $state ?: null);
						if (!preg_match('/^\d{1,3}(\.\d{1,2})?$/', $state)) {
							$set('living_area_size', null);
						}
						// Removed validation calls to improve performance
						// PropertyResource::validateTabsAction($livewire, self::$tabTitle);
						// $livewire->validateOnly($component->getStatePath());
					}),

				Select::make('living_area_units')
					->label('Living room area Units')
					->options([
						'm2' => 'm²',
						'ft2' => 'ft²'
					])
					->columnSpan(1)
					->nullable()
					// ->live(true)
					->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
						$set('living_area_units', $state ?: null);
						// Removed validation calls to improve performance
						// PropertyResource::validateTabsAction($livewire, self::$tabTitle);
						// $livewire->validateOnly($component->getStatePath());
					}),

				Fieldset::make('Heating features')
					->columns(4)
					->schema([
						Hidden::make('heating_features')
							->dehydrated(),

						Radio::make('radio_heating_features')
							->label(false)
							->options([
								'deisel' => 'Deisel',
								'gas' => 'Gas',
								'air_conditioner' => 'Air Conditioner',
								'other' => 'Other',
							])
							->columnSpan(4)
							->columns(4)
							->reactive()
							->nullable()
							->afterStateHydrated(function ($state, callable $set, callable $get) {
								$savedType = $get('heating_features');
								if (!$savedType) {
									$set('radio_heating_features', null);
								} elseif (!in_array($savedType, [
									'deisel',
									'gas',
									'air_conditioner',
								])) {
									$set('radio_heating_features', 'other');
									$set('other_heating_features', $savedType);
								} else {
									$set('radio_heating_features', $savedType);
								}
							})
							->afterStateUpdated(function ($state, callable $set, $livewire, $component) {
								if ($state !== 'other') {
									$set('heating_features', $state);
									$set('other_heating_features', null);
								} else {
									$set('heating_features', null);
								}
								// Removed validation calls to improve performance
								// PropertyResource::validateTabsAction($livewire, self::$tabTitle);
								// $livewire->validateOnly($component->getStatePath());
							}),

						TextInput::make('other_heating_features')
							->label('Other heating features')
							->placeholder('Other option')
							->reactive()
							->columnSpan(4)
							->hidden(fn(callable $get) => $get('radio_heating_features') !== 'other')
							->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
								$set('other_heating_features', $state ?: null);
								// Removed validation calls to improve performance
								// PropertyResource::validateTabsAction($livewire, self::$tabTitle);
								// $livewire->validateOnly($component->getStatePath());
							}),
					]),

				Fieldset::make('Aditional features')
					->schema([
						CheckboxList::make('aditional_features')
							->label('')
							->options([
								'new_development' => 'New development',
								'renovated' => 'Renovated',
								'luxurious' => 'Luxurious',
								'unfinished' => 'Unfinished',
								'under_construction' => 'Under construction',
								'neoclassical' => 'Neoclassical',
								'empty' => 'Empty',
							])
							// ->live(true)
							->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
								$set('aditional_features', $state ?: null);
								// Removed validation calls to improve performance
								// PropertyResource::validateTabsAction($livewire, self::$tabTitle);
								// $livewire->validateOnly($component->getStatePath());
							})
							->afterStateHydrated(function (Set $set, $state) {
								if (is_null($state)) {
									$set('aditional_features', []);
								} elseif (!is_array($state)) {
									$set('aditional_features', json_decode($state, true) ?? []);
								}
							})
							->dehydrateStateUsing(fn($state) => $state ? explode(',', $state) : [])
							->columnSpan(4)
							->columns(4)
							->nullable(),
					]),

				Fieldset::make('Suitable for')
					->columns(4)
					->schema([
						CheckboxList::make('suitable_for')
							->label('')
							->reactive()
							->options([
								'holiday_home' => 'Holiday home',
								'investment' => 'Investment',
								'student' => 'Student',
								'professional_use' => 'Professional use',
								'tourist_rental' => 'Tourist rental',
								'other' => 'Other',
							])
							->live(true)
							->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
								$set('suitable_for', $state ?: null);
								PropertyResource::validateTabsAction($livewire, self::$tabTitle);
								$livewire->validateOnly($component->getStatePath());
							})
							->afterStateHydrated(function (Set $set, $state) {
								if (is_null($state)) {
									$set('suitable_for', []);
								} elseif (!is_array($state)) {
									$set('suitable_for', json_decode($state, true) ?? []);
								}
							})
							->columnSpan(4)
							->columns(4)
							->nullable(),

						TextInput::make('suitable_for_custom')
							->label('Custom suitable for')
							->visible(fn(Get $get) => $get('suitable_for') &&  in_array('other', $get('suitable_for')))
							->placeholder('Other option')
							->nullable()
							->columnSpan(4)
							->columns(1)
							// ->reactive()
							// ->live(true)
							->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
								$set('suitable_for_custom', $state ?: null);
								PropertyResource::validateTabsAction($livewire, self::$tabTitle);
								$livewire->validateOnly($component->getStatePath());
							}),
					]),

				TextInput::make('common_expences')
					->label('Common expenses per year (EUR)')
					->numeric()
					// ->live(onBlur: true)
					->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
					->inputMode('decimal')
					->step(0.01)
					->minValue(0)
					->maxValue(999.99)
					->default(0)
					->columnSpan(4)
					->columns(1)
					->nullable()
					->rules(['numeric', 'regex:/^\d{1,3}(\.\d{1,2})?$/'])
					->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
						$set('common_expences', $state ?: null);
						PropertyResource::validateTabsAction($livewire, self::$tabTitle);
						$livewire->validateOnly($component->getStatePath());
					}),

				TextInput::make('price_for_sale_eur')
					->label('Price for sale (EUR)')
					->numeric()
					// ->live(onBlur: true)
					->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
					->inputMode('decimal')
					->step(0.01)
					->minValue(0)
					->default(0)
					->columnSpan(2)
					->nullable()
					->rules(['numeric', 'regex:/^\d{1,8}(\.\d{1,2})?$/'])
					->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
						$set('price_for_sale_eur', $state ?: null);
						PropertyResource::validateTabsAction($livewire, self::$tabTitle);
						$livewire->validateOnly($component->getStatePath());
					}),

				TextInput::make('price_for_sale_per_sq_m')
					->label('Price for sale (per sq m)')
					->numeric()
					// ->live(onBlur: true)
					->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
					->inputMode('decimal')
					->step(0.01)
					->minValue(0)
					->default(0)
					->columnSpan(2)
					->nullable()
					->rules(['numeric', 'regex:/^\d{1,4}(\.\d{1,2})?$/'])
					->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
						$set('price_for_sale_per_sq_m', $state ?: null);
						PropertyResource::validateTabsAction($livewire, self::$tabTitle);
						$livewire->validateOnly($component->getStatePath());
					}),

				TextInput::make('return_on_investment')
					->label('ROI (Return on investment) (%)')
					->numeric()
					// ->live(onBlur: true)
					->extraInputAttributes(['min' => '0', 'class' => '[&::-webkit-inner-spin-button]:appearance-none',])
					->inputMode('decimal')
					->minValue(-999.99)
					->maxValue(999.99)
					->step(0.01)
					->columnSpan(2)
					->nullable()
					->rule('regex:/^-?\d{1,3}(\.\d{1,2})?$/')
					->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
						$set('return_on_investment', $state ?: null);
						PropertyResource::validateTabsAction($livewire, self::$tabTitle);
						$livewire->validateOnly($component->getStatePath());
					}),

			])
			->visible(function ($get) {
				$dealType = (array) $get('deal_type');

				return (in_array('deal_type_sale', $dealType) || in_array('deal_type_monthly_rent', $dealType))
					&& Auth::user()?->hasAnyRole(['admin', 'property_owner', 'manager']);
			});
	}
}
