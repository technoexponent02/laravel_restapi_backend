<?php
use App\User;
use App\Notifications\BroadcastMe;
use Illuminate\Support\Facades\Redis;

/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/

// Route::any('{all}', function(){

//     return view('welcome');

// })->where('all', '.*');

Route::get('/', function () {
	// Redis::set('name', 'Testing');
	// return Redis::get('name');
	// Cache::put('foo', 'bar', 10);
	// return Cache::get('foo');
    
    // $data = [
    //     'event' => 'UserSignedUp',
    //     'data' => [
    //         'username' => 'JohnDoe'
    //     ]
    // ];

    // // In Episode 4, we'll use Laravel's event broadcasting.
    // Redis::publish('test-channel', json_encode($data));
	$user = User::find(2);
	$user->notify(new BroadcastMe());
    return view('welcome');

});



// Auth::routes();



// Route::get('/home', 'HomeController@index')->name('home');







/**

 * Define uri prefix for admin control pages..

 */

define('ADMIN_PREFIX', 'admin');



/**

 * Admin control routes.

 */



Route::group(['namespace' => 'Admin', 'prefix' => ADMIN_PREFIX], function () {

	Route::get('/', 'Auth\LoginController@showLoginForm')->name('admin_login');

	// Authentication Routes...

	Route::get('login', 'Auth\LoginController@showLoginForm')->name('admin_login');

	Route::post('login', 'Auth\LoginController@login');

	Route::post('logout', 'Auth\LoginController@logout')->name('admin_logout');
	Route::get('logout', 'Auth\LoginController@logout');
	// Registration Routes...

	Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('admin_register');

	Route::post('register', 'Auth\RegisterController@register');

	

	Route::get('/home', 'HomeController@index')->name('home');
	Route::get('settings', 'HomeController@settings');
    Route::post('settings', 'HomeController@updateSettings');
    
    Route::get('confirmation-email-template', 'HomeController@viewConfirmationEmailTemplate');
    Route::post('confirmation-email-template', 'HomeController@updateConfirmationEmailTemplate');

    Route::get('reset-email-template', 'HomeController@viewResetEmailTemplate');
    Route::post('reset-email-template', 'HomeController@updateResetEmailTemplate');


    Route::get('welcome-email-template', 'HomeController@viewWelcomeEmailTemplate');
    Route::post('welcome-email-template', 'HomeController@updateWelcomeEmailTemplate');
    

});