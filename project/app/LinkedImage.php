<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkedImage extends Model
{
    protected $fillable = ['actual_image', 'pixelated_image'];

    public function linked_imageable()
    {
        return $this->morphTo();
    }

}
