<?php

namespace App\Listeners;

use App\Events\TaskAboutToEnd;
use App\Notifications\NearDeadline;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
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
        $user = User::find($event->task->user_id);
        $user->notify(new NearDeadline($event->task) );
    }
}
