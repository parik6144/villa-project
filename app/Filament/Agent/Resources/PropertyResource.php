<?php

namespace App\Filament\Agent\Resources;

use App\Filament\Agent\Resources\PropertyResource\Pages;
use App\Filament\Agent\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Illuminate\Support\Facades\Auth;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('property_type_id')
                    ->relationship('propertyType', 'name')
                    ->required()
                    ->default(Auth::id()),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required(),
                TextInput::make('location')
                    ->required(),
                Toggle::make('availability'),
                TextInput::make('sleeping_places')
                    ->type('number'),
                TextInput::make('square')
                    ->type('number')
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Title')->searchable(),
                TextColumn::make('propertyType.name')->label('Property Type')->searchable(),
                TextColumn::make('location')->label('Location')->searchable(),
                BooleanColumn::make('availability')->label('Available'),
                //TextColumn::make('sleeping_places')->label('Sleeping Places'),
                TextColumn::make('square')->label('Square (m²)'),
                TextColumn::make('created_at')->label('Created At')->dateTime(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}