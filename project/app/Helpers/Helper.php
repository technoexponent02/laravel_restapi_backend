<?php
use App\User;
use App\Setting;
if (!function_exists('create_api_token')) {
    function create_api_token($user) {
        return $user->id.str_random(40);
    }
}
if (!function_exists('check_valid_user')) {
    function check_valid_user($user_id) {
        $count = User::where('is_active' , "=" , 'Y')->where('id' , "=" , $user_id)->count();
        if($count == 0)
        {
        	return false;
        }
        return true;
    }
}
if (! function_exists('get_general_settings')) {
    function get_general_settings($key)
    {
        $setting = Setting::find(1);
        return $setting->$key;
    }
}
if (! function_exists('admin_url')) {
    /**
     * Generate a url for the application.
     * @param string $path
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function admin_url($path = null)
    {
        $admin_path = ADMIN_PREFIX . '/';
        if (is_null($path)) {
            return url($admin_path);
        }
        return url($admin_path . $path);
    }
}
?>