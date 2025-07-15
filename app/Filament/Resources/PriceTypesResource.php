<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceTypesResource\Pages;
use App\Filament\Resources\PriceTypesResource\RelationManagers;
use App\Models\PriceTypes;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PriceTypesResource extends Resource
{
    protected static ?string $model = PriceTypes::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPriceTypes::route('/'),
            'create' => Pages\CreatePriceTypes::route('/create'),
            'edit' => Pages\EditPriceTypes::route('/{record}/edit'),
        ];
    }
}
