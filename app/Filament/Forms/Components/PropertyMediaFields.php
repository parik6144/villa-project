<?php

namespace App\Filament\Forms\Components;

use App\Filament\Resources\PropertyResource;
use App\Models\Property;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\IconPosition;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Closure;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Spatie\Image\Image;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Support\Enums\Alignment;
use Filament\Facades\Filament;
use App\Models\PropertySites;
use Filament\Forms\Components\Actions\Action;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class PropertyMediaFields
{
    private static $tabTitle = 'Media';

    public static function create($property): Tab
    {
        return Tabs\Tab::make(self::$tabTitle)
            ->icon(fn(Get $get) => PropertyResource::getTabIcon($get('tab_icon_media')))
            ->iconPosition(IconPosition::After)
            ->schema([
                Section::make('Media')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('primary_image')
                            ->disk('r2')
                            ->image()
                            ->collection('primary_image')
                            ->conversion('thumb')
                            ->rules([
                                fn(): Closure => function (string $attribute, $value, Closure $fail) {
                                    $image = Image::load($value->getPathname());

                                    if ($image->getWidth() > $image->getHeight()) {
                                        if ($image->getWidth() < 1024 || $image->getHeight() < 768) {
                                            $fail('The image must be at least 1024x768 pixels for horizontal orientation.');
                                        }
                                    } else {
                                        if ($image->getWidth() < 768 || $image->getHeight() < 1024) {
                                            $fail('The image must be at least 768x1024 pixels for vertical orientation.');
                                        }
                                    }
                                },
                            ])
                            ->live(true)
                            ->afterStateUpdated(function ($livewire, $component, $state) {
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                            ->getUploadedFileUsing(function (Set $set, $record, $state, string $file, $livewire) {
                                $model = $livewire->getRecord();
                                $primary_image = $model->getMedia("primary_image");
                            
                                if ($primary_image->isNotEmpty() && $primary_image[0]) {
                                    return [
                                        'name' => $primary_image[0]->getAttribute('file_name'),
                                        'url' => $primary_image[0]->getFullUrl(),
                                    ];
                                }
                            
                                return [];
                            })
                            ->panelLayout('grid')
                            ->columnSpan(4)
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('gallery_images')
                            ->label('Gallery Images')
                            ->disk('r2')
                            ->collection('gallery')
                            ->conversion('thumb')
                            ->image()
                            ->maxSize(10240)
                            ->multiple()
                            ->panelLayout('grid')
                            ->reorderable()
                            ->appendFiles()
                            ->rules([
                                fn(): Closure => function (string $attribute, $value, Closure $fail) {
                                    $image = Image::load($value->getPathname());

                                    if ($image->getWidth() > $image->getHeight()) {
                                        if ($image->getWidth() < 1024 || $image->getHeight() < 768) {
                                            $fail('The image must be at least 1024x768 pixels for horizontal orientation.');
                                        }
                                    } else {
                                        if ($image->getWidth() < 768 || $image->getHeight() < 1024) {
                                            $fail('The image must be at least 768x1024 pixels for vertical orientation.');
                                        }
                                    }
                                },
                            ])
                            ->live(true)
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
                            ->afterStateUpdated(function ($livewire, $component) {
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                    ]),
                Section::make('Content URLs')
                    ->schema([
                        TableRepeater::make('sitesContent')
                            ->reorderable(false)
                            ->headers([
                                Header::make('Site')->label('Site'),
                                Header::make('Content URL')->label('Content URL'),
                            ])
                            ->deletable(fn() => Filament::auth()->user()->hasRole('admin'))
                            ->schema([
                                Placeholder::make('site_placeholder')
                                    ->label(false)
                                    ->content(fn(Get $get) => PropertySites::find($get('property_site_id'))?->site),
                                Placeholder::make('url_content_placeholder')
                                    ->label(false)
                                    ->content(fn(Get $get) => $get('content')),
                            ])
                            ->live(true)
                            ->afterStateUpdated(function ($livewire, $component) {
                                PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                $livewire->validateOnly($component->getStatePath());
                            })
                            ->formatStateUsing(function ($state, $component) {
                                $record = $component->getRecord();

                                if (!$record || !$record->id) {
                                    return [];
                                }

                                $sitesContent = $record->sitesContent()->get();

                                $formattedContent = $sitesContent->map(function ($content) {
                                    return [
                                        'property_site_id' => $content->property_site_id,
                                        'content' => $content->content,
                                    ];
                                })->toArray();

                                return $state ?: $formattedContent;
                            })
                            ->extraItemActions([
                                Filament::auth()->user()->hasRole('admin') ?

                                    Action::make('Edit')
                                    ->icon('heroicon-s-pencil')
                                    ->modalHeading('Edit Sites Content')
                                    ->fillForm(function (array $arguments, Repeater $component): array {
                                        $allItems = $component->getState();
                                        $currentKey = $arguments['item'];
                                        return $allItems[$currentKey] ?? [];
                                    })
                                    ->form([
                                        Select::make('property_site_id')
                                            ->label('Site')
                                            ->options(function () {
                                                return PropertySites::pluck('site', 'id')->toArray();
                                            })
                                            // ->selectablePlaceholder(false)
                                            ->required()
                                            ->columnSpan(2),
                                        TextInput::make('content')
                                            ->label('Content URL')
                                            ->url()
                                            ->required(),
                                    ])
                                    ->action(function ($livewire, array $arguments, array $data, $component, Set $set, Get $get): void {
                                        $mainState = $component->getState();
                                        $key = $arguments['item'];
                                        $mainState[$key] = $data;

                                        $component->state($mainState);
                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                    })
                                    : null
                            ])
                            ->addActionLabel('Add row')
                            ->addAction(function ($action) {
                                return $action->form([
                                    Select::make('property_site_id')
                                        ->label('Site')
                                        ->options(function () {
                                            return PropertySites::pluck('site', 'id')->toArray();
                                        })
                                        // ->selectablePlaceholder(false)
                                        ->required()
                                        ->columnSpan(2),
                                    TextInput::make('content')
                                        ->label('Content URL')
                                        ->url()
                                        ->required(),
                                ])
                                    ->action(function ($data, Set $set, Get $get, $livewire) {
                                        $currentState = $get('sitesContent') ?? [];
                                        $result = array_merge($currentState, [$data]);
                                        $set('sitesContent', $result);
                                        PropertyResource::validateTabsAction($livewire, self::$tabTitle);
                                    });
                            })
                            ->deleteAction(
                                fn(Action $action) => $action->requiresConfirmation(),
                            )
                    ])
                    ->hidden(
                        fn(Get $get) => (Filament::auth()->user()->hasRole('admin') ? false : count($get('sitesContent')) === 0)
                    ),

            ]);
    }
}
