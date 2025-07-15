<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeasonResource\Pages;
use App\Filament\Resources\SeasonResource\RelationManagers;
use App\Models\Season;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\DateColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SeasonResource extends Resource
{
    protected static ?string $model = Season::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\DatePicker::make('date_from')
                ->label('Start Date')
                ->required()
                ->native(false)
                ->prefixIcon('heroicon-o-calendar')
                ->reactive()
                ->disabled(function () {
                    return !Auth::check() || !Auth::user()->hasRole('admin');
                })
                ->before('date_to')
                ->rule(function (callable $get, ?Season $record = null) {
                    return function (string $attribute, $value, $fail) use ($get, $record) {
                        $dateTo = $get('date_to');
                        $existingSeasons = Season::query()
                            ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                            ->where(function ($query) use ($value, $dateTo) {
                                $query->whereBetween('date_from', [$value, $dateTo])
                                    ->orWhereBetween('date_to', [$value, $dateTo])
                                    ->orWhere(function ($query) use ($value, $dateTo) {
                                        $query->where('date_from', '<=', $value)
                                            ->where('date_to', '>=', $dateTo);
                                    });
                            })
                            ->exists();

                        if ($existingSeasons) {
                            $fail('The selected date range overlaps with an existing season.');
                        }
                    };
                })
                ->columnSpan(6),
            Forms\Components\DatePicker::make('date_to')
                ->label('End Date')
                ->required()
                ->native(false)
                ->prefixIcon('heroicon-o-calendar')
                ->after('date_from')
                ->reactive()
                ->disabled(function () {
                    return !Auth::check() || !Auth::user()->hasRole('admin');
                })
                ->rule(function (callable $get, ?Season $record = null) {
                    return function (string $attribute, $value, $fail) use ($get, $record) {
                        $dateFrom = $get('date_from');
                        $existingSeasons = Season::query()
                            ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                            ->where(function ($query) use ($dateFrom, $value) {
                                $query->whereBetween('date_from', [$dateFrom, $value])
                                    ->orWhereBetween('date_to', [$dateFrom, $value])
                                    ->orWhere(function ($query) use ($dateFrom, $value) {
                                        $query->where('date_from', '<=', $dateFrom)
                                            ->where('date_to', '>=', $value);
                                    });
                            })
                            ->exists();

                        if ($existingSeasons) {
                            $fail('The selected date range overlaps with an existing season.');
                        }
                    };
                })
                ->columnSpan(6),
            Forms\Components\TextInput::make('season_title')
                ->label('Season Name')
                ->required()
                ->disabled(function () {
                    return !Auth::check() || !Auth::user()->hasRole('admin');
                })
                ->maxLength(255)
                ->columnSpan(12),
        ])
        ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('season_title')
                    ->label('Season Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('date_from')
                    ->label('Start Date')
                    ->sortable(),

                TextColumn::make('date_to')
                    ->label('End Date')
                    ->sortable()
            ])
            ->defaultSort('date_to','desc')
            ->filters([
                //
            ])
            ->actions([
                ...(Auth::user()->hasRole('admin') ? [
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ] : []),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListSeasons::route('/'),
            'create' => Pages\CreateSeason::route('/create'),
            'edit' => Pages\EditSeason::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Seasons';
    }
}
