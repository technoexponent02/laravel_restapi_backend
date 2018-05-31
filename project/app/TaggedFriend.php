<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaggedFriend extends Model
{
    protected $fillable = ['user_id'];

    public function tag_friendable()
    {
        return $this->morphTo();
    }

    public function tagged_user_details()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
