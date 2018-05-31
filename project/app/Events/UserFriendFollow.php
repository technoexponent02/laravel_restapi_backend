<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\UserFriend;

class UserFriendFollow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $followed_friend;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserFriend $followed_friend)
    {
        //dd($user_friend);
        $this->followed_friend = $followed_friend;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //return new PrivateChannel('channel-name');
    }
}
