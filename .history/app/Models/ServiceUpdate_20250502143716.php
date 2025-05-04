<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
