<?php

namespace App\Listeners;

use App\Models\Block;
use App\Models\Supervisor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatingBlockSupervisorListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->request->post('is_blocked')) {
            $block = new Block();
            $block->description = $event->request->post('block_description');
            $block->position = Supervisor::POSITION;
            $block->blocked_id  = $event->supervisor->id;
            $block->from = $event->request->post('from_date') ?? null;
            $block->to = $event->request->post('to_date') ?? null;
            $block->save();
        } else {
            return;
        }
    }
}
