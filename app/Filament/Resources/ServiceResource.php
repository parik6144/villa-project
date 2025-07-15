<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Http\Middleware\VerifyIsAdmin;
use App\Models\Service;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Table;


class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $defaultCoordinates = [39.15, 22.3];
        $latitude = $defaultCoordinates[0];
        $longitude = $defaultCoordinates[1];
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('service_category_id')
                    ->relationship('serviceCategories', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required(),
                TextInput::make('location')
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->inputMode('decimal')
                    ->required(),
                Toggle::make('availability'),

                Map::make('coordinates')
                    ->label('Location')
                    ->reactive()
                    ->columnSpanFull()
                    ->defaultLocation([$latitude, $longitude])
                    ->height(fn () => '400px')
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('latitude', $state['lat']);
                        $set('longitude', $state['lng']);
                    }),
                TextInput::make('latitude')
                    ->reactive()
                    ->default('39.15')
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('coordinates', [
                            'lat' => floatVal($state),
                            'lng' => floatVal($get('longitude')),
                        ]);
                    })
                    ->lazy(),
                TextInput::make('longitude')
                    ->reactive()
                    ->default('22.3')
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('coordinates', [
                            'lat' => floatval($get('latitude')),
                            'lng' => floatVal($state),
                        ]);
                    })
                    ->lazy(),

                SpatieMediaLibraryFileUpload::make('primary_image')
                    ->disk('r2')
                    ->collection('service_primary_image')
                    ->conversion('thumb')
                ,

                SpatieMediaLibraryFileUpload::make('gallery_images')
                    ->label('Gallery Images')
                    ->disk('r2')
                    ->collection('service_gallery')
                    ->conversion('thumb')
                    ->image()
                    ->multiple()
                    ->panelLayout('grid')
                    ->columnSpanFull()


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Title')->searchable(),
                TextColumn::make('serviceCategories.name')->label('Service Category')->searchable(),
                TextColumn::make('price')->label('Price'),
                //TextColumn::make('location')->label('Location')->searchable(),
                BooleanColumn::make('is_approved')->label('Approved'),
                BooleanColumn::make('availability')->label('Available'),
                TextColumn::make('created_at')->label('Created At')->dateTime(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([])
            ->bulkActions([
                BulkAction::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->is_approved = true;
                            $record->save();
                        }

                        Notification::make()
                            ->title('Services approved!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
            ]);
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
