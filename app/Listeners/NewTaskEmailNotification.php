<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\TaskCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewTaskEmailNotification
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
    public function handle(TaskCreated $event)
    {
        $user = User::find($event->taskCreated)->toArray();
        /* Mail::send('emails.mailEvent', $user, function($message) use ($user) {
            $message->to($user[0]['email']);
            $message->subject('Event Testing');
        }); */
    }
}
