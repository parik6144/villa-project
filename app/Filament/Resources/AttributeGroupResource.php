<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributeGroupResource\Pages;
use App\Http\Middleware\VerifyIsAdmin;
use App\Models\AttributeGroup;
use Filament\Forms;
use Filament\Tables;
// use Filament\Resources\Form;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Filament\Resources\Resource;
// use Filament\Resources\Table;

class AttributeGroupResource extends Resource
{
    protected static ?string $model = AttributeGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $label = 'Attribute Group';
    protected static ?string $pluralLabel = 'Attribute Groups';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Group Name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Group Name'),
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
            'index' => Pages\ListAttributeGroups::route('/'),
            'create' => Pages\CreateAttributeGroup::route('/create'),
            'edit' => Pages\EditAttributeGroup::route('/{record}/edit'),
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
