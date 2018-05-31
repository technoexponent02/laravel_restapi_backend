<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['region_id'];

    public function locationable()
    {
        return $this->morphTo();
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }


}
