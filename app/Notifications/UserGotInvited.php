<?php

namespace App\Notifications;

use App\Invitation;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserGotInvited extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $invitation, $inviting, $invited, $task;
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
        $this->inviting = User::find($invitation->inviting_id);
        $this->inviting = User::find($invitation->invited_id);
        $this->task = User::find($invitation->task_id);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('You got Task Invitation')

                    //->line($this->inviting->username.' has invited you to follow his/her task: '. $this->task->title)
                    //->action('Accept Invitation', url('/api/invitations/'.$this->invitation->id.'/accept'))
                    ->markdown('emails.invitation',['task_id'=>$this->invitation->id,'inviting_username'=>$this->inviting->username, 'task_title' => $this->task->title ]);
                    //->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
