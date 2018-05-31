<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/**
 * Set the default documentation version...
 */
define('DEFAULT_API_VERSION', 'v1');

Route::group(['namespace' => 'Api', 'prefix' => DEFAULT_API_VERSION . '/'], function () {
    /*************Unauthenticated routes*****************/
    Route::post('check-valid-email', 'UserController@checkValidEmail');
    Route::post('register', 'UserController@register');
    Route::post('confirm-email', 'UserController@confirmEmail');
    Route::post('login', 'UserController@authenticate');
    Route::post('social', 'UserController@socialAuthenticate');
    Route::post('social-login', 'UserController@socialLogin');
    // Reset password.
    Route::post('password/email-token', 'UserController@resetPasswordEmailToken');
    Route::post('reset-password/reset', 'ResetPasswordController@reset');

    Route::get('user/username/{username}', 'UserController@username');

    /*************Authenticated routes*****************/
    Route::get('auth-user', 'UserController@authUser');
    Route::post('user/save-profile-picture', 'UserController@saveProfilePicture');
    Route::post('user/save-cover-picture', 'UserController@saveCoverPicture');
    Route::post('user/delete-profile-picture', 'UserController@deleteProfilePicture');
    Route::post('user/delete-cover-picture', 'UserController@deleteCoverPicture');
    Route::post('user/save-account', 'UserController@saveAccount');
    Route::post('user/save-privacy', 'UserController@savePrivacy');
    Route::post('user/save-password', 'UserController@savePassword');
    Route::get('user/suggested-friends', 'UserController@suggestedFriends');

    Route::get('experiences', 'ExperienceController@index');
    Route::get('experiences/{experience}', 'ExperienceController@show');
    Route::post('experiences', 'ExperienceController@store');
    Route::put('experiences/{experience}', 'ExperienceController@update');
    Route::delete('experiences/{experience}', 'ExperienceController@delete');

    Route::get('educations', 'EducationController@index');
    Route::get('educations/{education}', 'EducationController@show');
    Route::post('educations', 'EducationController@store');
    Route::put('educations/{education}', 'EducationController@update');
    Route::delete('educations/{education}', 'EducationController@delete');

    Route::get('user-friends/username/{username}', 'UserFriendController@username');
    Route::get('user-friends/requests', 'UserFriendController@requests');
    Route::get('user-friends/blocked-users', 'UserFriendController@blockedUsers');
    Route::get('user-friends/followed-users', 'UserFriendController@followedUsers');
    Route::get('user-friends', 'UserFriendController@index');
    Route::get('user-friends/{user_friend}', 'UserFriendController@show');
    Route::post('user-friends/unfriend', 'UserFriendController@unfriend');
    Route::post('user-friends/block', 'UserFriendController@block');
    Route::post('user-friends/unblock', 'UserFriendController@unblock');
    Route::post('user-friends/follow', 'UserFriendController@follow');
    Route::post('user-friends/unfollow', 'UserFriendController@unfollow');

    Route::post('user-friends', 'UserFriendController@store');
    Route::put('user-friends/{user_friend}', 'UserFriendController@update');
    Route::delete('user-friends/{user_friend}', 'UserFriendController@delete');

    Route::get('notifications', 'UserController@userNotifications');
    Route::get('mark-as-read-notifications', 'UserController@markAsReadNotifications');
    Route::get('locations', 'UserController@locations');
    Route::get('location-delete/{location}', 'UserController@locationDelete');
    Route::get('location/{region}', 'UserController@locationDetails');
    Route::post('user-location-enroll', 'UserController@userLocationEnroll');/*add as the location page member*/
    Route::post('user-location-follow-unfollow', 'UserController@userLocationFollowUnfollow');

    Route::get('post/tags', 'PostController@tags');
    Route::post('post/local-wall', 'PostController@localWall');

    Route::get('location-posts/{region}', 'UserController@locationPosts');
    Route::get('news-feed', 'PostController@index');
    Route::get('post-delete/{post}', 'PostController@postDelete');
    Route::post('update-local-wall/{post}', 'PostController@updateLocalWall');
    Route::get('post-like-unlike/{post}', 'LikeController@postlikeUnlike');
    Route::post('post-comment/{post}/{parent_id?}', 'CommentController@postComment');
    Route::get('post-comments/{post}/{page_limit?}', 'CommentController@postCommentListing');
});
