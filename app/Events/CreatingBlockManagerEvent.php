<?php

namespace App\Events;

use App\Models\Manager;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreatingBlockManagerEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    public $manager;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, Manager $manager)
    {
        //
        $this->request = $request;
        $this->manager = $manager;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
