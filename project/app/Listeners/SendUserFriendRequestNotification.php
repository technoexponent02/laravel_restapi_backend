<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Events\UserFriendRequestSend;

use App\Notifications\UserFriendRequestNotification;
use Notification;

class SendUserFriendRequestNotification
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
    public function handle(UserFriendRequestSend $event)
    {

        // for multiple users
        $users = [$event->user_friend->user];
        Notification::send($users, new UserFriendRequestNotification($event->user_friend));
    }
}
