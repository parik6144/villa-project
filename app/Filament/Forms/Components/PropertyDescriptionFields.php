<?php

namespace App\Filament\Forms\Components;

use App\Filament\Resources\PropertyResource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\RichEditor;
use Filament\Support\Enums\IconPosition;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Auth;



class PropertyDescriptionFields
{
    private static $tabTitle = 'Description';

    public static function create(): Tab
    {
        return Tabs\Tab::make(self::$tabTitle)
            ->icon(fn(Get $get) => PropertyResource::getTabIcon($get('tab_icon_description')))
            ->iconPosition(IconPosition::After)
            ->schema([
                Textarea::make('brief_description')
                    ->label('Brief description from Property Owner')
                    ->rows(3)
                    // ->live(true)
                    ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                        $set('brief_description', $state ?: null);
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    }),
                TextInput::make('commercial_title')
                    ->label('Commercial title')
                    // ->reactive()
                    // ->live(true)
                    ->disabled(function () {
                        return !Auth::check() || !Auth::user()->hasRole('admin');
                    })
                    ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                        $set('commercial_title', $state ?: null);
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    }),
                TextInput::make('headline')
                    ->label('Headline (in English)')
                    ->required()
                    ->disabled(function () {
                        return !Auth::check() || !Auth::user()->hasRole('admin');
                    })
                    // ->live(true)
                    ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                        $set('headline', $state ?: null);
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    ->extraInputAttributes([
                        'data-required' => 'true',
                        'name' => 'headline (in English)',
                        'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                    ]),
                Textarea::make('short_summary')
                    ->label('Short summary (in English)')
                    ->rows(3)
                    ->required()
                    ->disabled(function () {
                        return !Auth::check() || !Auth::user()->hasRole('admin');
                    })
                    // ->live(true)
                    ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                        $set('short_summary', $state ?: null);
                        // Removed validation calls to improve performance
                        // PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        // $livewire->validateOnly($component->getStatePath());
                    })
                    ->extraInputAttributes([
                        'data-required' => 'true',
                        'name' => 'short summary (in English)',
                        'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                    ]),
                RichEditor::make('description')
                    ->label('Description (in English)')
                    ->disableToolbarButtons([
                        'attachFiles',
                        'codeBlock',
                    ])
                    ->required()
                    ->disabled(function () {
                        return !Auth::check() || !Auth::user()->hasRole('admin');
                    })
                    // ->live(true)
                    ->afterStateUpdated(function (Set $set, $state, $livewire, $component) {
                        $set('description', $state ?: null);
                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                        $livewire->validateOnly($component->getStatePath());
                    })
                    ->extraInputAttributes([
                        'data-required' => 'true',
                        'name' => 'description (in English)',
                        'x-on:focus' => 'clearFilamentValidationOnFocus(event)',
                    ]),
            ]);
    }
}
