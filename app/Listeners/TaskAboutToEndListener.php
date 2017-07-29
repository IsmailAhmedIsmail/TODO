<?php

namespace App\Listeners;

use App\Events\TaskAboutToEnd;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TaskAboutToEndListener
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
     * @param  TaskAboutToEnd  $event
     * @return void
     */
    public function handle(TaskAboutToEnd $event)
    {
        //
    }
}
