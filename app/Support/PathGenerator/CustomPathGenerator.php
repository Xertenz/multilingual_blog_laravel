<?php
namespace App\Support\PathGenerator;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    /*
     * Get the path for the given media, without the root directory or disk name.
     */
    /*
    public function getPath(Media $media): string
    {
        // Customize your path here. For example, using the model type and id:
        //return strtolower($media->model_type) . '/' . $media->model_id . '/' . $media->uuid . '/';
        return 'article/content/' . date('Y') . '/' . date('m') . "/";
    }
    */
    public function getPath(Media $media): string
    {
        $modelType = class_basename($media->model_type); // 'Article'
        $collection = $media->collection_name; // 'featured_imgs' or 'content_imgs'
        $createdAt = $media->created_at;
        
        // تحويل collection name إلى مسار أنظف
        $collectionPath = $this->getCollectionPath($collection);
        
        return sprintf(
            '%s/%s/%d/%02d/%02d/',
            strtolower($modelType),  // 'articles'
            $collectionPath,          // 'featured' or 'content'
            $createdAt->year,         // 2024
            $createdAt->month,        // 01
            $createdAt->day           //
        );
    }
    
    /**
     * تحويل collection names إلى مسارات أنظف
     */
    private function getCollectionPath(string $collection): string
    {
        $mapping = [
            'featured_imgs' => 'featured',
            'content_imgs'  => 'content',
            'category_imgs' => 'categories',
        ];
        
        return $mapping[$collection] ?? $collection;
    }

    /*
     * Get the path for conversions of the given media.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . 'conversions/';
    }

    /*
     * Get the path for responsive images of the given media.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'responsive/';
    }
}


?>