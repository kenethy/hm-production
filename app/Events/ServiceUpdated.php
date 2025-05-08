<?php

namespace App\Events;

use App\Models\Service;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The service instance.
     *
     * @var \App\Models\Service
     */
    public $service;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Service  $service
     * @return void
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }
}
