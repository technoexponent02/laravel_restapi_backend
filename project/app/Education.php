<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use Notifiable;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'educations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'school_college_name',
        'board_university_name',
        'degree_name'
    ];

    /**
     * Get the user that owns the education.
     *  return $this->belongsTo('App\Post', 'foreign_key', 'other_key');
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
