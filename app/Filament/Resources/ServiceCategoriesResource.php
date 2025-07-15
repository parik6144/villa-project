<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceCategoriesResource\Pages;
use App\Filament\Resources\ServiceCategoriesResource\RelationManagers;
use App\Http\Middleware\VerifyIsAdmin;
use App\Models\ServiceCategories;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceCategoriesResource extends Resource
{
    protected static ?string $model = ServiceCategories::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            'index' => Pages\ListServiceCategories::route('/'),
            'create' => Pages\CreateServiceCategories::route('/create'),
            'edit' => Pages\EditServiceCategories::route('/{record}/edit'),
        ];
    }
}
