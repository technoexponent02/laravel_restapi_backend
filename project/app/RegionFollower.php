<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegionFollower extends Model
{
    protected $fillable = ['region_id', 'user_id'];

    public function region()
    {
    	return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function user()
    {
    	return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeUserExistsInRegion($query, $region_id)
    {
    	return $query->where(['region_id' => $region_id, 'user_id' => auth()->user()->id]);
    }
}
