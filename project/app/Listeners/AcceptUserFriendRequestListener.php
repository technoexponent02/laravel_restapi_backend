<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Events\UserFriendRequestAccepted;

use App\Notifications\UserFriendRequestAcceptNotification;
use Notification;

class AcceptUserFriendRequestListener
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
    public function handle(UserFriendRequestAccepted $event)
    {
        // for multiple users
        $users = [$event->user_friend->user];
        Notification::send($users, new UserFriendRequestAcceptNotification($event->user_friend));
    }
}
