<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyPricesResource\Pages;
use App\Filament\Resources\PropertyPricesResource\RelationManagers;
use App\Models\PropertyPrices;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyPricesResource extends Resource
{
    protected static ?string $model = PropertyPrices::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('price_id')
                    ->relationship('priceType', 'name')
                    ->required(),
                TextInput::make('value')
                    ->numeric()
                    ->inputMode('decimal')
                    ->required(),
                Select::make('property_id')
                    ->relationship('property', 'title')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('priceType.name')->label('Price Type')->searchable(),
                TextColumn::make('value')->label('Value'),
                TextColumn::make('property.title')->label('Property Name')->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([])
            ->bulkActions([]);
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
            'index' => Pages\ListPropertyPrices::route('/'),
            'create' => Pages\CreatePropertyPrices::route('/create'),
            'edit' => Pages\EditPropertyPrices::route('/{record}/edit'),
        ];
    }
}
