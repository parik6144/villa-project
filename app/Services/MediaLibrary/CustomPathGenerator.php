<?php

namespace App\Services\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use App\Models\Property;

class CustomPathGenerator implements PathGenerator
{
    /*
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        if ($media->model_type == 'App\Models\Property') {
            $property = $media->model;

            // if (empty($property->getSlug())) {
            //     $property->generateSlug();
            // }
                
            return "properties/{$property->getSlug()}/{$media->collection_name}/";
        }

        if ($media->model_type == 'App\Models\Service') {
            $modelId = $media->model->id;
            return "services/{$modelId}/{$media->collection_name}/";
        }

        return md5($media->id . config('app.key')) . '/';
    }

    /*
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        if ($media->model_type == 'App\Models\Property') {
            $property = $media->model;
            $slug = $property->getSlug();
            return "properties/{$slug}/{$media->collection_name}/conversions/";
        }

        if ($media->model_type == 'App\Models\Service') {
            $modelId = $media->model->id;
            return "services/{$modelId}/{$media->collection_name}/conversions/";
        }

        return md5($media->id . config('app.key')) . '/conversions/';
    }

    /*
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        if ($media->model_type == 'App\Models\Property') {
            $property = $media->model;
            $slug = $property->getSlug();
            return "properties/{$slug}/{$media->collection_name}/responsive-images/";
        }

        if ($media->model_type == 'App\Models\Service') {
            $modelId = $media->model->id;
            return "services/{$modelId}/{$media->collection_name}/responsive-images/";
        }

        return md5($media->id . config('app.key')) . '/responsive-images/';
    }
}