<?php

namespace App\Filament\Forms\Components;

use App\Filament\Resources\PropertyResource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\IconPosition;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;
use Illuminate\Support\HtmlString;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use App\Forms\Components\CustomSelect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Filament\Forms\Components\BaseFileUpload;

class PropertyInstructionsFields
{
    private static $tabTitle = 'Instructions';

    public static function create($property): Tab
    {
        return Tabs\Tab::make(self::$tabTitle)
            ->icon(fn(Get $get) => PropertyResource::getTabIcon($get('tab_icon_instructions')))
            ->iconPosition(IconPosition::After)
            ->visible(
                fn(Get $get): bool =>
                is_array($get('deal_type')) &&
                    (in_array('deal_type_rent', $get('deal_type')) || in_array('deal_type_monthly_rent', $get('deal_type')))
            )
            ->schema([
                Section::make("instructions")
                    ->label("These are your standard check-in and check-out times. You're able to agree with each guest different times after booking")
                    // ->relationship('instructions')
                    ->schema([
                        TimePicker::make('instructions.check_in')
                            ->label('Check-in')
                            ->minutesStep(15)
                            ->datalist((new \App\Models\Property)->generateTimeSlots())
                            ->required()
                            ->name('check_in')
                            // ->live(true)
                            ->formatStateUsing(function ($record) {
                                return $record?->instructions?->check_in;
                            })
                            ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                $set('check_in', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                            ->extraInputAttributes([
                                'data-required' => 'true',
                                'name' => 'check-in',
                                'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            ]),

                        TimePicker::make('instructions.check_out')
                            ->label('Check-out')
                            ->minutesStep(15)
                            ->datalist((new \App\Models\Property)->generateTimeSlots())
                            ->required()
                            ->name('check_out')
                            // ->live(true)
                            ->formatStateUsing(function ($record) {
                                return $record?->instructions?->check_out;
                            })
                            ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                $set('check_out', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                             ->extraInputAttributes([
                                'data-required' => 'true',
                                'name' => 'check-out',
                                'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            ]),

                        TextInput::make('instructions.check_in_contact_person')
                            ->label('Check-in contact person')
                            ->required()
                            // ->live(true)
                            ->formatStateUsing(function ($record) {
                                return $record?->instructions?->check_in_contact_person;
                            })
                            ->afterStateUpdated(function (?string $state, $set, $livewire, $component) {
                                $set('check_in_contact_person', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                             ->extraInputAttributes([
                                'data-required' => 'true',
                                'name' => 'check-in contact person',
                                'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            ]),
                        Select::make('instructions.key_collection_point')
                            ->label('Key collection point')
                            ->options([
                                'At the property' => 'At the property',
                                'From another location' => 'From another location',
                                'Key code entrance' => 'Key code entrance',
                                'Keys are in a lock box' => 'Keys are in a lock box',
                                'There is a reception' => 'There is a reception',
                            ])
                            // ->selectablePlaceholder(false)
                            // ->live(true)
                            ->formatStateUsing(function ($record) {
                                return $record?->instructions?->key_collection_point;
                            })
                            ->afterStateUpdated(function (?string $state, $set, $livewire, $component) {
                                $set('key_collection_point', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                            ->required()
                             ->extraInputAttributes([
                                'data-required' => 'true',
                                'name' => 'key collection point',
                                'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            ]),

                        PhoneInput::make('instructions.telephone_number')
                            ->label('Telephone number')
                            ->focusNumberFormat(PhoneInputNumberType::E164)
                            ->validateFor(
                                lenient: true,
                            )
                            ->required()
                            // ->live(true)
                            ->formatStateUsing(function ($record) {
                                return $record?->instructions?->telephone_number;
                            })
                            ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                $set('telephone_number', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                            ->extraInputAttributes([
                                'data-required' => 'true',
                                'name' => 'telephone number',
                                'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                            ]),
                    ]),
                Section::make('Write easy to follow check-in instructions and choose when you want them to be shared with the guests.')
                    // ->relationship('instructions')
                    ->schema([
                        Textarea::make('instructions.check_in_instructions')
                            ->label('Instructions')
                            ->maxLength(3000)
                            ->live(true)
                            ->formatStateUsing(function ($record) {
                                return $record?->instructions?->check_in_instructions;
                            })
                            ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                $set('check_in_instructions', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                            ->tooltip("For example: A lockbox code or information about documents guests need to show on arrival."),

                        FileUpload::make('instructions.attached_instructions')
                            ->formatStateUsing(function ($record) {
                                return $record?->instructions?->attached_instructions;
                            })
                            ->getUploadedFileUsing(function ($state, $file, $livewire) {
                                $instructionPath = '';

                                $model = $livewire->getRecord();

                                if ($model && $model->slug) {
                                    $instructionPath = "properties/{$model->slug}/instructions/";
                                } else {
                                    return [];
                                }

                                if ($file) {
                                    return
                                        [
                                            'name' => basename($file),
                                            'url' => Storage::disk('r2')->url($instructionPath . $file)
                                        ];
                                }

                                return [];
                            })
                            ->label('Attach instructions')
                            ->disk('r2')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg'])
                            ->multiple()
                            ->enableReordering()
                            ->live(true)
                            ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            }),
                    ]),
                Section::make('Closest airports')
                    // ->relationship('instructions')
                    ->schema([
                        CustomSelect::make('instructions.closest_airports')
                            ->label('Choose the most common airports that guests can use to travel to your property.')
                            ->searchable()


                            ->reactive()
                            ->getSearchResultsUsing(function (string $query, callable $get) {
                                if (strlen($query) < 3) {
                                    return [];
                                }

                                return (new \App\Models\Property)->searchAirports($query, $get('../latitude'), $get('../longitude'));
                            })
                            ->multiple()
                            ->live(true)
                            ->formatStateUsing(function ($record) {
                                return $record?->instructions?->closest_airports;
                            })
                            ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                $set('closest_airports', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })


                    ]),
                Section::make('Directions')
                    // ->relationship('instructions')
                    ->schema([
                        Textarea::make('instructions.directions')
                            ->tooltip("Provide guests with directions to your property, including suggestions on transportation and other details that may help them arrive easily")
                            ->label('Directions to your property are private and only shared with confirmed guests.')
                            ->maxLength(2000)
                            ->live(true)
                            ->formatStateUsing(function ($record) {
                                return $record?->instructions?->directions;
                            })
                            ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                                $set('directions', $state ?: null);
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            }),
                    ]),
            ]);
    }
}
