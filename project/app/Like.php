<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['user_id'];

    public function likeable()
    {
        return $this->morphTo();
    }

    public function liked_user()
    {
        return $this->belongTo(User::class, 'user_id', 'id');
    }

}
