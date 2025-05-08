<?php

namespace App\Events;

use App\Models\Service;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The service instance.
     *
     * @var \App\Models\Service
     */
    public $service;

    /**
     * The previous status of the service.
     *
     * @var string|null
     */
    public $previousStatus;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Service  $service
     * @param  string|null  $previousStatus
     * @return void
     */
    public function __construct(Service $service, ?string $previousStatus = null)
    {
        $this->service = $service;
        $this->previousStatus = $previousStatus;
    }
}
