<?php

namespace App\Listeners;

use App\Events\UserFollowedTask;
use App\Notifications\TaskFollowed;
use App\Task;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserFollowedTaskListener
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
     * @param  UserFollowedTask  $event
     * @return void
     */
    public function handle(UserFollowedTask $event)
    {
        $user = User::find($event->task->user_id);
        $user->notify(new TaskFollowed($event->task, $event->follower));
    }
}
