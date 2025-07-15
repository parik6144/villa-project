<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HotelsResource\Pages;
use App\Filament\Resources\HotelsResource\RelationManagers;
use App\Http\Middleware\VerifyIsAdmin;
use App\Models\Hotels;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HotelsResource extends Resource
{
    protected static ?string $model = Hotels::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make("name")
                    ->required(),
                TextInput::make('location')
                    ->required(),
                TextInput::make('rating')
                    ->numeric()
                    ->inputMode('decimal')
                    ->required(),
                Textarea::make('description')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable(),
                TextColumn::make('location')->label('Location')->searchable(),
                TextColumn::make('rating')->label('Rating')->searchable(),
                TextColumn::make('created_at')->label('Created At')->dateTime(),
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
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotels::route('/create'),
            'edit' => Pages\EditHotels::route('/{record}/edit'),
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
