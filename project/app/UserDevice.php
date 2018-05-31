<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $fillable = [
    	'user_id', 'fcm_token', 'device_information'
    ];

    /**
     * Get the user that owns the device.
     *  return $this->belongsTo('App\User', 'foreign_key', 'other_key');
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    public static function saveFcmToken($fcm_token, $device_information = null, 
    									$user_id)
    {
    	// $this->user_id = $user_id;
    	// $this->fcm_token = $fcm_token;
    	// $this->device_information = $device_information;
    	// $this->save();
    	return self::updateOrCreate(['fcm_token' => $fcm_token],
    		['user_id' => $user_id, 'device_information' => $device_information]);

    }
}
