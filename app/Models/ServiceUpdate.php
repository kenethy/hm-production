<?php

namespace App\Models;

use App\Services\CloudinaryService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class ServiceUpdate extends Model
{
    protected $fillable = [
        'service_id',
        'title',
        'description',
        'image_path',
        'update_type',
        'sent_to_customer',
        'sent_at',
    ];

    protected $casts = [
        'sent_to_customer' => 'boolean',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the service that owns the update.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the image URL
     *
     * @return string|null
     */
    public function getImageUrl()
    {
        if (!$this->image_path) {
            return null;
        }

        // Check if the image path is a Cloudinary URL
        if (strpos($this->image_path, 'cloudinary.com') !== false) {
            return $this->image_path;
        }

        // Check if the image path is a public ID
        if (strpos($this->image_path, '/') === false) {
            try {
                $cloudinary = App::make(CloudinaryService::class);
                return $cloudinary->getImageUrl($this->image_path);
            } catch (\Exception $e) {
                // Fallback to local storage
                return Storage::url($this->image_path);
            }
        }

        // Default to local storage
        return Storage::url($this->image_path);
    }

    /**
     * Get the WhatsApp-friendly image URL
     *
     * @return string|null
     */
    public function getWhatsAppImageUrl()
    {
        $url = $this->getImageUrl();

        if (!$url) {
            return null;
        }

        // Ensure the URL is absolute
        if (strpos($url, 'http') !== 0) {
            $url = config('app.url') . $url;
        }

        return $url;
    }
}
