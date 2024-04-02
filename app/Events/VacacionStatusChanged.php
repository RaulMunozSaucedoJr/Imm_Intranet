<?php

namespace App\Events;

use App\Models\Vacacion;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VacacionStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vacacion;

    /**
     * Create a new event instance.
     *
     * @param Vacacion $vacacion
     */
    public function __construct(Vacacion $vacacion)
    {
        $this->vacacion = $vacacion;
    }
}
