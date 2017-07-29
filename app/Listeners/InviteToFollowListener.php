<?php

namespace App\Listeners;

use App\Events\InviteToFollow;
use App\Notifications\UserGotInvited;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteToFollowListener
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
     * @param  InviteToFollow  $event
     * @return void
     */
    public function handle(InviteToFollow $event)
    {
        $invited = User::find($event->invitation->invited_id);
        $invited->notify(new UserGotInvited($event->invitation));
    }
}
