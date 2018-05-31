<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

use App\Events\UserFriendRequestSend;
use App\Events\UserFriendRequestAccepted;
use App\Events\UserFriendFollow;

class UserFriend extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'friend_id',
        'is_requested', /*   0-got request, 1-send request  */
        'is_accepted',/*  0-rejected request, 1-accepted request, 3-Pending decision  */
        'is_blocked',/*      0-Not blocked, 1- blocked  */
        'friend_link_id', /*0-request record, primary key be used for friend_link_id on parent relation creation*/
        'is_followed'/*   0-Not followed, 1- followed  */
    ];

    protected $attributes = [
        'is_blocked' => 0,
        'friend_link_id' => 0,
        'is_followed' => 0
    ];

    /**
     * Get the user that owns the friend.
     *  return $this->belongsTo('App\User', 'foreign_key', 'other_key');
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    /**
     * Get the friend that is owned by the user.
     *  return $this->belongsTo('App\User', 'foreign_key', 'other_key');
     */
    public function friend()
    {
        return $this->belongsTo('App\User', 'friend_id', 'id');
    }

    public function userFriendRequestSend($user_friend_request)
    {
        //dd($this);
        event(new UserFriendRequestSend($user_friend_request));
    }

    public function userFriendRequestAccept()
    {
        event(new UserFriendRequestAccepted($this));
    }

    public function userFollow($followed_friend)
    {
        //dd($this);
        event(new UserFriendFollow($followed_friend));
    }

}
