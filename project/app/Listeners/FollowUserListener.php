<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Events\UserFriendFollow;

use App\Notifications\FollowUserNotification;
use Notification;

class FollowUserListener
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
    public function handle(UserFriendFollow $event)
    {

        // for multiple users
        $users = [$event->followed_friend->friend];
        Notification::send($users, new FollowUserNotification($event->followed_friend));
    }
}
