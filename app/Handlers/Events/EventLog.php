<?php

namespace App\Handlers\Events;

use App\Events\AddEventLog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventLog
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
     * @param  AddEventLog  $event
     * @return void
     */
    public function handle(AddEventLog $event)
    {
        $data = date('Y-m-d H:i:s');
        \App\Models\EventLog::create([
            'type' => $event->type,
            'user_id' => $event->user_id,
            'text' => $event->text,
            'ip' => $event->ip,
            'created_at' => $data,
            'updated_at' => $data,
        ]);
    }
}
