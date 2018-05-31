<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class Experience extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'designation', 'company_name', 
        'location', 'latitude', 'longitude',
        'from_month', 'from_year', 
        'is_currently_working', 
        'to_month', 'to_year'
    ];


    
    /**
     * Get the user that owns the experience.
     *  return $this->belongsTo('App\Post', 'foreign_key', 'other_key');
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

  

   
}
