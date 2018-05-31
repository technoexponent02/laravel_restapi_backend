<?php

namespace App\Policies;

use App\User;
use App\Experience;

class ExperiencePolicy
{

    // public function before($user, $ability)
    // {
    //     if ($user->isSuperAdmin()) {
    //         return true;
    //     }
    // }


    /**
     * Determine if the given experience can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\Experience  $experience
     * @return bool
     */
    public function update(User $user, Experience $experience)
    {
        return $user->id === $experience->user_id;
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
     * Determine if the given experience can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\Experience  $experience
     * @return bool
     */
    public function delete(User $user, Experience $experience)
    {
        return $user->id === $experience->user_id;
    }
}
