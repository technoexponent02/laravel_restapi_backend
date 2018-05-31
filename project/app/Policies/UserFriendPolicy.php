<?php

namespace App\Policies;

use App\User;
use App\UserFriend;

class UserFriendPolicy
{

    // public function before($user, $ability)
    // {
    //     if ($user->isSuperAdmin()) {
    //         return true;
    //     }
    // }


    /**
     * Determine if the given friend can be accessed by the user.
     *
     * @param  \App\User  $user
     * @param  \App\UserFriend  $userfriend
     * @return bool
     */
    public function access(User $user, UserFriend $userfriend)
    {
        return $user->id === $userfriend->user_id;
    }

    /**
     * Determine if the given friend can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\UserFriend  $userfriend
     * @return bool
     */
    public function update(User $user, UserFriend $userfriend)
    {
        
        return $user->id === $userfriend->user_id;
    }

    /**
     * Determine if the given user can create posts.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine if the given friend can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\UserFriend  $userfriend
     * @return bool
     */
    public function delete(User $user, UserFriend $userfriend)
    {
        return $user->id === $userfriend->user_id;
    }

    /**
     * Determine if the given friend can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\UserFriend  $userfriend
     * @return bool
     */
    public function cantDeleteAcceptedRequest(User $user, UserFriend $userfriend)
    {
        //dd($userfriend->is_accepted);
        return $userfriend->is_accepted === 3 ? true : false;
    }
}
