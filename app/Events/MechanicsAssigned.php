<?php

namespace App\Events;

use App\Models\Service;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MechanicsAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The service instance.
     *
     * @var \App\Models\Service
     */
    public $service;

    /**
     * The previous mechanic IDs.
     *
     * @var array
     */
    public $previousMechanicIds;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Service  $service
     * @param  array  $previousMechanicIds
     * @return void
     */
    public function __construct(Service $service, array $previousMechanicIds = [])
    {
        $this->service = $service;
        $this->previousMechanicIds = $previousMechanicIds;
    }
}
