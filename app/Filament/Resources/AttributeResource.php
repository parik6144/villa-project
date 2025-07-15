<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributeResource\Pages;
use App\Http\Middleware\VerifyIsAdmin;
use App\Models\Attribute;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Models\PropertyAttribute;
// use Filament\Resources\Form;
use Filament\Resources\Resource;
// use Filament\Resources\Table;
use Filament\Support\Exceptions\Halt;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $label = 'Attribute';
    protected static ?string $pluralLabel = 'Attributes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Attribute Name'),

                Forms\Components\Select::make('group_id')
                    ->relationship('group', 'name')
                    ->required()
                    ->label('Attribute Group'),

                Forms\Components\Checkbox::make('is_required')
                    ->label('Is Required')
                    ->default(false),

                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'select' => 'Select',
                        'text' => 'Text',
                        'textarea' => 'Textarea',
                        'checkbox' => 'Checkbox',
                        'number' => 'Number',
                        'multi-checkbox' => 'Multi Checkbox',
                    ])
                    ->label('Type')
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if (in_array($state, ['select', 'multi-checkbox'])) {
                        $set('options', '');
                    } else {
                        $set('options', null);
                    }
                }),

                // Forms\Components\Textarea::make('options')
                //     ->label('Options (key:value per line)')
                //     ->placeholder("test1:value1\ntest2:value2\n")
                //     ->helperText('Enter options in key:value format, each on a new line. Use `*` for default (multi-checkbox only, e.g., test1:*value1).')
                //     ->rows(10)
                //     ->visible(fn ($get) => in_array($get('type'), ['select', 'multi-checkbox']))
                //     ->formatStateUsing(function (?Attribute $record, $get) {
                //         if ($record === null || empty($record->options)) {
                //             return '';
                //         }

                //         $options = json_decode($record->options, true);
                //         if (!is_array($options)) {
                //             return '';
                //         }

                //         return implode("\n", array_map(function ($key, $value) use ($get) {
                //             $isMultiCheckbox = $get('type') === 'multi-checkbox';
                //             if (is_string($value)) {
                //                 return "{$key}:{$value}";
                //             }

                //             $label = $value['label'] ?? $value;
                //             $isDefault = $isMultiCheckbox && ($value['default'] ?? false);
                //             return $isDefault ? "{$key}:*{$label}" : "{$key}:{$label}";
                //         }, array_keys($options), $options));
                //     })
                //     ->dehydrateStateUsing(function ($state, $get) {
                //         $lines = explode("\n", trim($state));
                //         $options = [];
                //         $isMultiCheckbox = $get('type') === 'multi-checkbox';

                //         foreach ($lines as $line) {
                //             [$key, $value] = explode(':', $line, 2);
                //             $isDefault = $isMultiCheckbox && str_starts_with($value, '*');
                //             $options[$key] = $isMultiCheckbox
                //                 ? ['label' => ltrim($value, '*'), 'default' => $isDefault]
                //                 : ltrim($value, '*');
                //         }

                //         return json_encode($options);
                //     }),       
                
                Forms\Components\Textarea::make('options')
                    ->label('Options (key:value per line)')
                    ->placeholder("test1:value1\ntest2:value2\n")
                    ->helperText('Enter options in key:value format, each on a new line. Use `*` for default (multi-checkbox only, e.g., test1:*value1).')
                    ->rows(10)
                    ->visible(fn ($get) => in_array($get('type'), ['select', 'multi-checkbox']))
                    ->formatStateUsing(function (?Attribute $record, $get) {
                        if ($record === null || empty($record->options)) {
                            return '';
                        }

                        $options = json_decode($record->options, true);
                        if (!is_array($options)) {
                            return '';
                        }

                        return implode("\n", array_map(function ($key, $value) use ($get) {
                            $isMultiCheckbox = $get('type') === 'multi-checkbox';
                            if (is_string($value)) {
                                return "{$key}:{$value}";
                            }

                            $label = $value['label'] ?? $value;
                            $isDefault = $isMultiCheckbox && ($value['default'] ?? false);
                            return $isDefault ? "{$key}:*{$label}" : "{$key}:{$label}";
                        }, array_keys($options), $options));
                    })
                    ->dehydrateStateUsing(function ($state, $get, $set, ?Attribute $record) {
                        $lines = explode("\n", trim($state));
                        $options = [];
                        $isMultiCheckbox = $get('type') === 'multi-checkbox';
                        $existingKeys = $record ? array_keys(json_decode($record->options ?? '{}', true)) : [];

                        foreach ($lines as $line) {
                            [$key, $value] = explode(':', $line, 2);
                            $isDefault = $isMultiCheckbox && str_starts_with($value, '*');
                            $options[$key] = $isMultiCheckbox
                                ? ['label' => ltrim($value, '*'), 'default' => $isDefault]
                                : ltrim($value, '*');
                        }

                        $removedKeys = array_diff($existingKeys, array_keys($options));

                        if (!empty($removedKeys)) {
                            $usedKeys = PropertyAttribute::whereIn('attribute_id', [$record->id])
                            ->where(function ($query) use ($removedKeys) {
                                foreach ($removedKeys as $key) {
                                    $query->orWhereJsonContains('value', $key);
                                }
                            })
                            ->pluck('value')
                            ->toArray();

                            if (!empty($usedKeys)) {
                                Notification::make()
                                    ->title('Warning')
                                    ->body('The following keys are used in Property and cannot be deleted: ' . implode(', ', $usedKeys))
                                    ->danger()
                                    ->send();

                                throw new Halt();
                            }
                        }

                        return json_encode($options);
                    }),

                Forms\Components\Textarea::make('default')
                    ->label('Default value')
                    ->visible(fn ($get) => in_array($get('type'), ['text', 'textarea', 'number'])),

                Forms\Components\Textarea::make('description')
                    ->label('Description'),

                Forms\Components\Textarea::make('notification')
                    ->label('Notification'),

                Forms\Components\Textarea::make('example')
                    ->label('Example'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Attribute Name'),
                Tables\Columns\TextColumn::make('group.name')->label('Group'),
                Tables\Columns\TextColumn::make('type')->label('Type'),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->date(),
                Tables\Columns\TextColumn::make('updated_at')->label('Updated At')->date(),
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttributes::route('/'),
            'create' => Pages\CreateAttribute::route('/create'),
            'edit' => Pages\EditAttribute::route('/{record}/edit'),
        ];
    }
    public static function getRouteMiddleware(\Filament\Panel $panel): array
    {
        return array_merge(parent::getRouteMiddleware($panel), [
            'auth',
            VerifyIsAdmin::class,
        ]);
    }
}
