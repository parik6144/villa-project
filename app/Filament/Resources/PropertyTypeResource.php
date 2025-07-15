<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyTypeResource\Pages;
use App\Filament\Resources\PropertyTypeResource\RelationManagers;
use App\Http\Middleware\VerifyIsAdmin;
use App\Models\PropertyType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class PropertyTypeResource extends Resource
{
    protected static ?string $model = PropertyType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            Select::make('property_class')
                ->label('Property Class')
                ->options([
                    'residential' => 'Residential',
                    'commercial' => 'Commercial',
                    'land' => 'Land',
                ])
                ->multiple()
                ->required()
                ->formatStateUsing(function ($state, $record) {
                    if ($record) {
                        $selected = [];
                        if ($record->residential) $selected[] = 'residential';
                        if ($record->commercial) $selected[] = 'commercial';
                        if ($record->land) $selected[] = 'land';
                        return $selected;
                    }
                    return $state;
                })
                ->dehydrateStateUsing(function ($state, $record) {
                    if ($record) {
                        if (in_array('residential', $state)) $record->residential = true;
                        if (in_array('commercial', $state)) $record->commercial = true;
                        if (in_array('land', $state)) $record->land = true;
                    }
                })
                ->afterStateUpdated(function ($state, $set) {
                    $set('residential', in_array('residential', $state));
                    $set('commercial', in_array('commercial', $state));
                    $set('land', in_array('land', $state));
                }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable(),
                TextColumn::make('property_class')
                    ->label('Property Class')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record) return '';

                        $classes = [];
                        if ($record->residential) $classes[] = 'Residential';
                        if ($record->commercial) $classes[] = 'Commercial';
                        if ($record->land) $classes[] = 'Land';

                        return implode(', ', $classes);
                    }),
                // TextColumn::make('created_at')->label('Created At')->dateTime(),
            ])
            ->filters([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getRouteMiddleware(\Filament\Panel $panel): array
    {
        return array_merge(parent::getRouteMiddleware($panel), [
            'auth',
            VerifyIsAdmin::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPropertyTypes::route('/'),
            'create' => Pages\CreatePropertyType::route('/create'),
            'edit' => Pages\EditPropertyType::route('/{record}/edit'),
        ];
    }
}
