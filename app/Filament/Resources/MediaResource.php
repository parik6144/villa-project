<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use App\Filament\Resources\MediaResource\RelationManagers;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
// use App\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationLabel = 'Media Library';
    
    protected static ?string $navigationGroup = 'Media';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('file')
                    ->collection('images') 
                    ->label('Upload Image')
                    ->required(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\ImageColumn::make('preview')
                ->label('Preview')
                ->getStateUsing(function ($record) {
                    return $record->getUrl();
                })
                ->size(50),
                Tables\Columns\TextColumn::make('name')->label('Name'),
                Tables\Columns\TextColumn::make('created_at')->label('Created At'),
                Tables\Columns\TextColumn::make('updated_at')->label('Updated At'),
                Tables\Columns\TextColumn::make('model_type')->label('Model Type'),
                Tables\Columns\TextColumn::make('model_id')->label('Model ID'),
            ])
            ->filters([
                
            ])
            ->actions([
                Tables\Actions\Action::make('Delete')
                    ->action(function (Media $media) {
                        $media->delete();
                    })
                    ->requiresConfirmation(),
            ]);
    }
}