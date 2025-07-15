<?php

namespace App\Filament\Resources\SeasonResource\Pages;

use App\Filament\Resources\SeasonResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditSeason extends EditRecord
{
    protected static string $resource = SeasonResource::class;

    protected function getHeaderActions(): array
    {
        if (Auth::user()->hasRole('admin')) {
            return [
            Actions\DeleteAction::make(),
        ];
        }
        else{
            return [];
        }
    }



    protected function getFormActions(): array
    {
        if (!Auth::user()->hasRole('admin')) {
            return [];
        }
        else{
            return [
                ...parent::getFormActions(),
            ];
        }

    }
    }
