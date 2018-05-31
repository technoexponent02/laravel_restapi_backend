<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['region_name', 'url_slug', 'latitude', 'longitude'];
    protected $appends = array('is_followed_wrt_to_logged_in_user');
    /**
     * Get the administrator flag for the user.
     *
     * @return bool
     */
    public function getIsFollowedWrtToLoggedInUserAttribute()
    {
    	//dump($this->regionFollowers()->userExistsInRegion($this->id)->first());
    	// DB::enableQueryLog();
    	//dd($this->regionFollowers()->userExistsInRegion($this->id)->toSql());
        return $this->regionFollowers()->userExistsInRegion($this->id)->first() != null ? true : false;
    }
    public function locations()
    {
        return $this->hasMany(Location::class, 'region_id', 'id');
    }
    public function regionFollowers()
    {
    	return $this->hasMany(RegionFollower::class, 'region_id', 'id');
    }

}
