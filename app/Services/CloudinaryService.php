<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = App::make('cloudinary');
    }

    /**
     * Upload an image to Cloudinary
     *
     * @param UploadedFile|string $file The file to upload (can be a path or UploadedFile)
     * @param string $folder The folder to upload to
     * @return array The upload result
     */
    public function uploadImage($file, $folder = 'service-updates')
    {
        $options = [
            'folder' => $folder,
            'resource_type' => 'image',
        ];

        // If the file is a path, upload it directly
        if (is_string($file) && file_exists($file)) {
            $result = $this->cloudinary->uploadApi()->upload($file, $options);
        } 
        // If the file is an UploadedFile, get the path and upload
        elseif ($file instanceof UploadedFile) {
            $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), $options);
        } else {
            throw new \InvalidArgumentException('Invalid file provided');
        }

        return $result;
    }

    /**
     * Get a Cloudinary URL for an image
     *
     * @param string $publicId The public ID of the image
     * @param array $options Transformation options
     * @return string The URL
     */
    public function getImageUrl($publicId, $options = [])
    {
        return $this->cloudinary->image($publicId)
            ->resize($options['width'] ?? null, $options['height'] ?? null)
            ->toUrl();
    }
}
