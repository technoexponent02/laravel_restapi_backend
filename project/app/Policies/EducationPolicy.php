<?php

namespace App\Policies;

use App\User;
use App\Education;

class EducationPolicy
{

    // public function before($user, $ability)
    // {
    //     if ($user->isSuperAdmin()) {
    //         return true;
    //     }
    // }


    /**
     * Determine if the given education can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\Education  $education
     * @return bool
     */
    public function update(User $user, Education $education)
    {
        return $user->id === $education->user_id;
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
     * Determine if the given education can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\Education  $education
     * @return bool
     */
    public function delete(User $user, Education $education)
    {
        return $user->id === $education->user_id;
    }
}
