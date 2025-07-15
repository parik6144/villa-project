<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function handleRecordCreation(array $data): Media
    {
        \Log::info( print_r($data, true) );
        
        $media = Media::create([
            'name' => $data['file']->getClientOriginalName(),
            'file_name' => $data['file']->getClientOriginalName(),
        ]);

        $media->addMedia($data['file'])
              ->toMediaCollection('images');

        return $media;
    }
}
