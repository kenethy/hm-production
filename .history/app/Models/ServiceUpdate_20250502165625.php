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
}
