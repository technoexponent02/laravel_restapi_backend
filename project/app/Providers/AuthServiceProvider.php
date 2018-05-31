<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Experience;
use App\Education;
use App\UserFriend;
use App\Policies\ExperiencePolicy;
use App\Policies\EducationPolicy;
use App\Policies\UserFriendPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Experience::class => ExperiencePolicy::class,
        Education::class => EducationPolicy::class,
        UserFriend::class => UserFriendPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
