<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaggedTag extends Model
{
    protected $fillable = ['tag_id'];

    public function tagable()
    {
        return $this->morphTo();
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_id', 'id');
    }


}
