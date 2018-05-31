<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'parent_id', 'comment'];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function commented_user()
    {
        return $this->belongTo(User::class, 'user_id', 'id');
    }

    public function parent()
    {
        return $this->hasOne('App\Comment', 'id', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Comment', 'parent_id', 'id');
    }
}
