<?php

namespace App\Filament\Forms\Components;

use App\Filament\Resources\PropertyResource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\IconPosition;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;
use Illuminate\Support\HtmlString;



class PropertyHouseRulesFields
{
    private static $tabTitle = 'House rules';

    public static function create(): Tab
    {
        return Tabs\Tab::make(self::$tabTitle)
            ->icon(fn(Get $get) => PropertyResource::getTabIcon($get('tab_icon_house_rules')))
            ->iconPosition(IconPosition::After)
            ->visible(
                fn(Get $get): bool =>
                is_array($get('deal_type')) &&
                    (in_array('deal_type_rent', $get('deal_type')) || in_array('deal_type_monthly_rent', $get('deal_type')))
            )
            ->schema([
                AlertBox::make()
                    ->helperText('Please note: The House Rules page allows you to specify the guidelines and expectations guests must follow during their stay. Examples may include rules about noise levels, smoking, pets, events, or maximum occupancy. Clear and concise rules help ensure a positive experience for both you and your guests while protecting your property. Make sure your house rules comply with local regulations and are easy to understand.')
                    ->success()
                    ->resolveIconUsing(name: 'heroicon-o-information-circle')
                    ->extraAttributes(['class' => 'custom-background-helper-text']),
                Select::make('suitable_for_kids')
                    ->label('Suitable for Kids')
                    ->options([
                        'welcome' => 'Children Welcome',
                        'great' => 'Great for Children',
                        'not_suitable' => 'Not Suitable for Children',
                    ])
                    // ->selectablePlaceholder(false)
                    // ->live(true)
                    ->afterStateUpdated(function (?string $state, $set, $livewire, $component) {
                        $set('suitable_for_kids', $state ?: null);
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    })
                    ->required()
                    ->extraInputAttributes([
                        'data-required' => 'true',
                        'name' => 'suitable for Kids',
                        'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                    ]),

                Toggle::make('events_allowed')
                    ->label('Events or Parties Allowed')
                    ->default(false)
                    // ->live(true)
                    ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    }),

                Toggle::make('pets')
                    ->label('Pets Allowed')
                    ->reactive()
                    ->live(true)
                    ->afterStateUpdated(function ($state, callable $set, $livewire, $component) {
                        if ($state) {
                            $set('max_pets', 0);
                        }
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    })
                    ->default(false),

                TextInput::make('max_pets')
                    ->label('Maximum Pets Allowed')
                    ->numeric()
                    ->mask('999')
                    ->default(0)
                    ->minValue(0)
                    ->extraInputAttributes(['min' => '0'])
                    ->required(fn($get) => $get('pets'))
                    ->hidden(fn($get) => !$get('pets'))
                    ->live(true)
                    ->afterStateUpdated(function ($state, callable $set, $livewire, $component) {
                        if ($state === null) {
                            $set('max_pets', 0);
                        }
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    }),

                Toggle::make('pets_fee')
                    ->label('Fee for Pets')
                    ->default(false)
                    ->hidden(fn($get) => !$get('pets'))
                    // ->live(true)
                    ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    }),

                Toggle::make('wheelchair_access')
                    ->label('Wheelchair Access')
                    ->default(false)
                    // ->live(true)
                    ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    }),

                Select::make('smoking_allowed')
                    ->label('Smoking Allowed')
                    ->options([
                        'no_smoking' => 'No Smoking',
                        'allowed' => 'Smoking Allowed',
                        'outside' => 'Smoking Outside',
                    ])
                    // ->selectablePlaceholder(false)
                    // ->live(true)
                    ->afterStateUpdated(function (?string $state, $set, $livewire, $component) {
                        $set('smoking_allowed', $state ?: null);
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    })
                    ->required()
                    ->extraInputAttributes([
                        'data-required' => 'true',
                        'name' => 'smoking Allowed',
                        'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                    ]),

                Select::make('camera')
                    ->label('Camera Present')
                    ->options([
                        'inside' => 'Inside the Property',
                        'no' => 'No',
                        'outside' => 'Outside the Property',
                    ])
                    // ->live(true)
                    ->afterStateUpdated(function (?string $state, $set, $livewire, $component) {
                        $set('camera', $state ?: null);
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    })
                    ->nullable(),
                Toggle::make('noise_monitor')
                    ->label(function () {
                        $label = "Noise Monitors Present";
                        $tooltip = view('custom-label-help', [
                            'icon' => 'heroicon-o-question-mark-circle',
                            'tooltip' => 'Please note: The Noise Monitors Present switch indicates whether noise monitoring devices are installed at the property. If selected, it’s important to disclose this to guests as part of transparency and to comply with privacy regulations. Noise monitors are typically used to ensure house rules regarding noise levels are followed, especially during quiet hours. These devices do not record conversations or audio but monitor noise levels only.',
                        ])->render();
                        return new HtmlString($label . $tooltip);
                    })
                    ->validationAttribute('Noise Monitors Present')
                    ->default(false)
                    // ->live(true)
                    ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    }),

                RichEditor::make('house_rules')
                    ->label('House Rules')
                    ->disableToolbarButtons([
                        'attachFiles',
                        'codeBlock',
                    ])
                    ->nullable()
                    // ->live(true)
                    ->afterStateUpdated(function ($state, $set, $livewire, $component) {
                        $set('house_rules', $state ?: null);
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    }),
            ]);
    }
}
