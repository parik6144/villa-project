<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyHotelsResource\Pages;
use App\Filament\Resources\PropertyHotelsResource\RelationManagers;
use App\Http\Middleware\VerifyIsAdmin;
use App\Models\PropertyHotels;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyHotelsResource extends Resource
{
    protected static ?string $model = PropertyHotels::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('property_id')
                    ->relationship('property', 'title')
                    ->required(),
                Select::make('hotel_id')
                    ->relationship('hotel', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.title')->label('Property')->searchable(),
                TextColumn::make('hotel.name')->label('Hotel')->searchable(),
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
            'index' => Pages\ListPropertyHotels::route('/'),
            'create' => Pages\CreatePropertyHotels::route('/create'),
            'edit' => Pages\EditPropertyHotels::route('/{record}/edit'),
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
