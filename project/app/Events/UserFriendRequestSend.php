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

class UserFriendRequestSend
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_friend;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserFriend $user_friend)
    {
        //dd($user_friend);
        $this->user_friend = $user_friend;
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
