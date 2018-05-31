<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmationMail;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Image;
use File;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Techno\Transformers\UserTransformer;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\UserFriend;
use App\UserDevice;
use App\Location;
use App\Region;

// use App\RegionFollower;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected $userTransformer;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserTransformer $userTransformer)
    {
        $this->middleware('auth:api', ['only' => [
            'authUser', 'changePassword', 'saveProfilePicture',
            'editProfile', 'saveCoverPicture', 'saveAccount',
            'deleteProfilePicture', 'deleteCoverPicture',
            'savePrivacy', 'savePassword', 'suggestedFriends',
            'username', 'userNotifications', 'markAsReadNotifications',
            'locations', 'locationDelete', 'locationDetails', 'locationPosts',
            'userLocationEnroll', 'userLocationFollowUnfollow'
            //,'markAsReadNotification'
        ]]);

        $this->userTransformer = $userTransformer;
    }

    public function checkValidEmail(Request $request)
    {
        $input = $request->all();

        $rules = [
            'email' => 'required|email|unique:users'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        // Email is valid.
        $response = [
            'message' => 'Email is valid and available.'
        ];
        return response()->json($response, 200);
    }

    public function checkEmail(Request $request)
    {
        $input = $request->all();

        $rules = [
            'email' => 'required|email'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        $user = User::where('email', '=', $input['email'])
                    ->where('is_active', '=', 'Y')
                    ->first();
        if ($user != null) {
            // Email is valid.
            $response = [
                'user' => $user
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'errors' => 'Account is not .'
            ];
            return response()->json($response, 400);
        }
    }

    public function username($username)
    {
        //DB::enableQueryLog();
        $user = User::getUserByUsername($username)->first();
        // $user->load(['userAcceptedFriendRequests' => function ($query) {
        //             $query->select('user_id','friend_id');
        //         }]);
        // $user->load(['userFriendRequests' => function ($query) {
        //             $query->select('user_id','friend_id');
        //         }]);
        //dd(DB::getQueryLog());
        if ($user == null) {
            $response = [
                'errors' => 'Username is not valid'
            ];
            return response()->json($response, 400);
        }
        /* check if you blocked the user*/
        $you_blocked_user = UserFriend::where('user_id', '=', auth()->id())
                            ->where('friend_id', '=', $user->id)
                            ->where('is_blocked', 1)
                            ->first();

        /* check if the user blocked you*/
        $user_blocked_you = UserFriend::where('user_id', '=', $user->id)
                            ->where('friend_id', '=', auth()->id())
                            ->where('is_blocked', 1)
                            ->first();
        $user->userblocked_status = 'N';

        if ($you_blocked_user != null || $user_blocked_you != null) {
            $user->userblocked_status = 'Y';
        }

        /* check if you followed the user*/
        $you_followed_user = UserFriend::where('user_id', '=', auth()->id())
                            ->where('friend_id', '=', $user->id)
                            ->where('is_followed', 1)
                            ->first();

        $user->userfollowed_status = 'N';
        if ($you_followed_user != null) {
            $user->userfollowed_status = 'Y';
        }

        // dd($user->toArray());
        return response()->json([
            'user' => $user->toArray()
        ], 200);
    }

    public function register(Request $request)
    {
        $input = $request->all();
        //dd($input);
        $messages = [
            'older_than' => 'Age must be greater than 18.',
        ];
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            //'date_of_birth' => 'required|date|olderThan:18',
            'date_of_birth' => 'required|date',
            'gender' => [
                    'required',
                    Rule::in(['M', 'F']),
                ],
            'location' => 'required|string|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
            'is_terms_accepted' => 'required'
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        event(new Registered($user = $this->create($input)));
        $confirm_link = env('SITE_URL') . '/register/confirm-email/' . $user->email_verification_token;
        Mail::to($user)->send(new ConfirmationMail($user, $confirm_link));
        $user = User::find($user->id);
        return response()->json([
            'user' => $this->userTransformer->transform($user->toArray())
        ], 201);
    }

    /**
     * Create a new talent user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $input_data = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'gender' => $data['gender'],
            'date_of_birth' => date('Y-m-d', strtotime($data['date_of_birth'])),
            'location' => $data['location'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'is_terms_accepted' => 'Y'
        ];
        //dd($input_data);
        $user = User::create($input_data);
        $user->username = $user->getUniqueUsername();
        $user->save();
        return $user;
    }

    public function confirmEmail(Request $request)
    {
        $user = User::where('email_verification_token', urldecode($request->input('email_verification_token')))->first();
        if ($user != null) {
            $user->confirmEmail();
            return response()->json([
                'user' => $user
            ], 200);
        } else {
            $response = [
                'errors' => 'Verification token not valid.'
            ];
            return response()->json($response, 400);
        }
    }

    public function authenticate(Request $request)
    {
        $input = $request->all();

        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        $user = User::where('email', $input['email'])->first();

        if ($user !== null && Hash::check($input['password'], $user->password)) {
            if ($user->is_active == 'N') {
                $response = [
                    'errors' => 'Account is not activated.'
                ];
                return response()->json($response, 400);
            }
            // create the api token if its null
            if ($user->api_token == null) {
                $user->api_token = create_api_token($user);
                $user->save();
            }
            /*register the fcm token*/
            if ($request->has('fcm_token') && $input['fcm_token'] != null) {
                UserDevice::saveFcmToken(
                    $input['fcm_token'],
                                          $request->device_information ?? null,
                                          $user->id
                                        );
            }
            return response()->json([
                'user' => $this->userTransformer->transform($user->toArray())
            ], 200);
        } else {
            $response = [
                'errors' => 'Login credentials do not match our records..'
            ];
            return response()->json($response, 400);
        }
    }

    public function socialLogin(Request $request)
    {
        $input = $request->all();

        $rules = [
            'email' => 'required|string|email|max:255'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        $user = User::where('email', '=', $input['email'])->first();
        if ($user != null) {
            if ($user->is_active == 'Y') {
                /*register the fcm token*/
                if ($request->has('fcm_token') && $input['fcm_token'] != null) {
                    UserDevice::saveFcmToken(
                        $input['fcm_token'],
                                              $request->device_information ?? null,
                                              $user->id
                                            );
                }
                return response()->json([
                    'user' => $this->userTransformer->transform($user->toArray())
                ], 200);
            } else {
                $response = [
                    'errors' => 'Please check your email and click on the verification link.'
                ];
                return response()->json($response, 400);
            }
        } else {
            $response = [
                'errors' => 'User do not exists.'
            ];
            return response()->json($response, 400);
        }
    }

    /**
     * Display authenticated user information.
     *
     * @return \Illuminate\Http\Response
     */
    public function authUser()
    {
        $response = $this->userTransformer->transform(Auth::user()->toArray());

        return response()->json($response, 200);
    }

    public function resetPasswordEmailToken(Request $request)
    {
        //dd(1);
        //$this->validateEmail($request);
        $input = $request->all();
        $rules = [
            'email' => 'required:email'
        ];

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            // dd(1);
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return response()->json([
                'res' => 0
            ], 200);
        } else {
            $response = [
                'errors' => $this->sendResetLinkFailedResponse($request, $response)
            ];
            return response()->json($response, 400);
            // return $response == Password::RESET_LINK_SENT
            // ? $this->sendResetLinkResponse($response)
            // : $this->sendResetLinkFailedResponse($request, $response);
        }
    }

    public function socialAuthenticate(Request $request)
    {
        $input = $request->all();

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            //'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            //'date_of_birth' => 'required|date|olderThan:18',
            'date_of_birth' => 'required|date',
            'gender' => [
                    'required',
                    Rule::in(['M', 'F']),
                ],
            'location' => 'required|string|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
            'is_terms_accepted' => 'required',
            'login_type' => [
                                'required',
                                Rule::in([2, 3]),
                            ]
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        $saved_data = [
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'login_type' => $input['login_type'],
            'gender' => $input['gender'],
            'date_of_birth' => $input['date_of_birth'],
            'location' => $input['location'],
            'latitude' => $input['latitude'],
            'longitude' => $input['longitude']
        ];

        if ($request->has('profile_picture')) {
            $saved_data['profile_picture'] = $input['profile_picture'];
        }

        $user = $this->handleDataImportsToUser($saved_data);

        $user = User::find($user->id);

        Mail::to($user)->send(new WelcomeMail($user));

        // Auth::loginUsingId($user->id);
        /*register the fcm token*/
        if ($request->has('fcm_token') && $input['fcm_token'] != null) {
            UserDevice::saveFcmToken(
                $input['fcm_token'],
                                      $request->device_information ?? null,
                                      $user->id
                                    );
        }
        return response()->json([
            'user' => $this->userTransformer->transform($user->toArray())
        ], 200);
    }

    private function handleDataImportsToUser($saved_data)
    {
        $cond_arr = ['email' => $saved_data['email']];

        $insert_data = [
                'first_name' => $saved_data['first_name'],
                'last_name' => $saved_data['last_name'],
                'email' => $saved_data['email'],
                'password' => bcrypt('123456'),
                'gender' => $saved_data['gender'],
                'date_of_birth' => date('Y-m-d', strtotime($saved_data['date_of_birth'])),
                'location' => $saved_data['location'],
                'latitude' => $saved_data['latitude'],
                'longitude' => $saved_data['longitude'],
                'is_terms_accepted' => 'Y',
                'login_type' => $saved_data['login_type']
        ];
        $find_user_already_exists = User::where('email', '=', $saved_data['email'])
                                    ->first();
        if ($find_user_already_exists != null) {
            unset($insert_data['password']);
        }

        $user = User::updateOrCreate($cond_arr, $insert_data);
        $user->is_active = 'Y';
        $user->is_terms_accepted = 'Y';
        $user->email_verification_token = null;
        $user->username = $user->getUniqueUsername();
        $user->save();

        $user = User::where('id', '=', $user->id)->first();

        if (isset($saved_data['profile_picture'])
            && $saved_data['profile_picture'] != null
            && $saved_data['profile_picture'] != 'NA') {
            if ($user->profile_picture != null) {
                $old_image = 'uploads/profile_pictures/' . $user->profile_picture;
                if (file_exists($old_image)) {
                    \File::delete($old_image);
                }
            }
            if ($user->profile_picture_small != null) {
                $old_image_small = 'uploads/profile_pictures/' . $user->profile_picture_small;
                if (file_exists($old_image_small)) {
                    \File::delete($old_image_small);
                }
            }

            //echo public_path(); die;
            $path = 'uploads/profile_pictures/';
            // $image  = $request->file('profile_picture');
            $save_name = time() . str_random(10) . '.png';
            $small_pic_save_name = time() . str_random(10) . '_small.png';
            // $tmp_name = $_FILES["profile_picture"]["tmp_name"];
            // move_uploaded_file($tmp_name, "$path$save_name");
            Image::make($saved_data['profile_picture'])->save($path . $save_name, 100);
            Image::make($saved_data['profile_picture'])->resize(1, 1)->save($path . $small_pic_save_name, 100);

            $user->profile_picture_small = $small_pic_save_name;
            $user->profile_picture = $save_name;
            $user->save();
        }

        return $user;
    }

    public function saveProfilePicture(Request $request)
    {
        $input = $request->all();

        $rules = [
            'profile_picture' => 'required|mimes:jpeg,png|max:10000'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        if ($request->hasFile('profile_picture')) {
            if (Auth::user()->profile_picture != null) {
                $old_image = 'uploads/profile_pictures/' . Auth::user()->profile_picture;
                if (file_exists($old_image)) {
                    \File::delete($old_image);
                }
            }
            if (Auth::user()->profile_picture_small != null) {
                $old_image_small = 'uploads/profile_pictures/' . Auth::user()->profile_picture_small;
                if (file_exists($old_image_small)) {
                    \File::delete($old_image_small);
                }
            }

            //echo public_path(); die;
            $path = 'uploads/profile_pictures/';
            $image = $request->file('profile_picture');
            $save_name = time() . str_random(10) . '.' . $image->getClientOriginalExtension();
            $small_pic_save_name = time() . str_random(10) . '_small.' . $image->getClientOriginalExtension();
            // $tmp_name = $_FILES["profile_picture"]["tmp_name"];
            // move_uploaded_file($tmp_name, "$path$save_name");
            Image::make($image->getRealPath())->save($path . $save_name, 100);
            Image::make($image->getRealPath())->resize(1, 1)->save($path . $small_pic_save_name, 100);

            Auth::user()->profile_picture_small = $small_pic_save_name;
            Auth::user()->profile_picture = $save_name;
            Auth::user()->save();
        }

        return response()->json([
            'user' => $this->userTransformer->transform(Auth::user()->toArray())
        ], 200);
    }

    public function saveCoverPicture(Request $request)
    {
        // echo public_path(); die;
        $input = $request->all();

        $rules = [
            'cover_picture' => 'required|mimes:jpeg,png|max:10000'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        if ($request->hasFile('cover_picture')) {
            if (Auth::user()->cover_picture != null) {
                $old_image = 'uploads/cover_pictures/' . Auth::user()->cover_picture;
                if (file_exists($old_image)) {
                    \File::delete($old_image);
                }
            }
            if (Auth::user()->cover_picture_small != null) {
                $old_image = 'uploads/cover_pictures/' . Auth::user()->cover_picture_small;
                if (file_exists($old_image)) {
                    \File::delete($old_image);
                }
            }

            $path = 'uploads/cover_pictures/';
            $image = $request->file('cover_picture');
            $save_name = time() . str_random(10) . '.' . $image->getClientOriginalExtension();
            $small_pic_save_name = time() . str_random(10) . '_small.' . $image->getClientOriginalExtension();

            Image::make($image->getRealPath())->save($path . $save_name, 100);
            Image::make($image->getRealPath())->resize(1, 1)->save($path . $small_pic_save_name, 100);

            Auth::user()->cover_picture_small = $small_pic_save_name;
            Auth::user()->cover_picture = $save_name;
            Auth::user()->save();
        }

        return response()->json([
            'user' => $this->userTransformer->transform(Auth::user()->toArray())
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveAccount(Request $request)
    {
        $input = $request->all();

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'location' => 'required|string|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
            'username' => 'nullable|alpha_num|max:255|unique:users,username,' . Auth::user()->id,
            'phone' => 'nullable|numeric|unique:users,phone,' . Auth::user()->id,
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        // $user = User::find(Auth::user()->id);

        Auth::user()->first_name = isset($input['first_name']) ? $input['first_name'] : '';
        Auth::user()->last_name = isset($input['last_name']) ? $input['last_name'] : '';
        Auth::user()->date_of_birth = isset($input['date_of_birth']) ?
                                date('Y-m-d', strtotime($input['date_of_birth'])) : '';
        Auth::user()->location = isset($input['location']) ? $input['location'] : '';
        Auth::user()->latitude = isset($input['latitude']) ? $input['latitude'] : '';
        Auth::user()->longitude = isset($input['longitude']) ? $input['longitude'] : '';
        Auth::user()->about_me = isset($input['about_me']) ? $input['about_me'] : '';
        Auth::user()->phone = isset($input['phone']) ? $input['phone'] : '';
        if ($request->has('username')) {
            Auth::user()->username = $input['username'];
        }
        Auth::user()->save();

        return response()->json([
            'user' => $this->userTransformer->transform(Auth::user()->toArray())
        ], 200);
    }

    public function deleteProfilePicture()
    {
        //dd(Auth::user());
        if (Auth::user()->profile_picture != null) {
            $old_image = 'uploads/profile_pictures/' . Auth::user()->profile_picture;
            if (file_exists($old_image)) {
                \File::delete($old_image);
            }
            Auth::user()->profile_picture = 'NA';
            Auth::user()->save();
            return response()->json([
                    'user' => $this->userTransformer->transform(Auth::user()->toArray())
                ], 200);
        } else {
            $response = [
                    'errors' => 'profile picture is null'
                ];
            return response()->json($response, 400);
        }
    }

    public function deleteCoverPicture()
    {
        if (Auth::user()->cover_picture != null) {
            $old_image = 'uploads/cover_pictures/' . Auth::user()->cover_picture;
            if (file_exists($old_image)) {
                \File::delete($old_image);
            }
            Auth::user()->cover_picture = 'NA';
            Auth::user()->save();
            return response()->json([
                    'user' => $this->userTransformer->transform(Auth::user()->toArray())
                ], 200);
        } else {
            $response = [
                    'errors' => 'cover picture is null'
                ];
            return response()->json($response, 400);
        }
    }

    public function savePrivacy(Request $request)
    {
        $input = $request->all();

        $rules = [
            'privacy_scope' => [
                'required',
                Rule::in([1, 2, 3]),
            ]
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        // $user = User::find(Auth::user()->id);

        Auth::user()->privacy_scope = isset($input['privacy_scope']) ? $input['privacy_scope'] : '';

        Auth::user()->save();

        return response()->json([
            'user' => $this->userTransformer->transform(Auth::user()->toArray())
        ], 200);
    }

    public function savePassword(Request $request)
    {
        $input = $request->all();
        //dd($input);

        $rules = [
            'current_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        //dd(Auth::user()->password);
        if (Hash::check($input['current_password'], Auth::user()->password)) {
            Auth::user()->password = bcrypt($input['new_password']);
            // Change unique api token.
            Auth::user()->api_token = create_api_token(Auth::user());
            Auth::user()->save();
            return response()->json([
                'user' => $this->userTransformer->transform(Auth::user()->toArray())
            ], 200);
        } else {
            $response = [
                'errors' => 'Current password did not match.'
            ];
            return response()->json($response, 400);
        }
    }

    public function suggestedFriends(Request $request)
    {
        //$total = count(Auth::user()->suggestedFriends()->get()->toArray());
        //dd(DB::getQueryLog());
        // if (count($total) == 0)
        // {
        //     $total = count(Auth::user()->activeUsers()->get()->toArray());
        // }
        // $page = LengthAwarePaginator::resolveCurrentPage();

        // $perPage = 1;

        // //Set the limit and offset for a given page.
        // $results = Auth::user()->suggestedFriends()->forPage($page, $perPage)->get();

        // $paginatedItems = new LengthAwarePaginator($results, $total, $perPage, $page, [
        //     'path' => LengthAwarePaginator::resolveCurrentPath(),
        // ]);
        //DB::enableQueryLog();
        $paginated_data = null;
        //dd(Auth::user()->suggestedFriends()->count());

        $search_name = request()->get('search_name', null);
        $location = request()->get('location', null);
        $school = request()->get('school', null);
        $college = request()->get('college', null);
        $company = request()->get('company', null);

        if (Auth::user()->suggestedFriends()->count() > 0 &&
            $search_name != null && $location != null && $school != null &&
            $college != null && $company != null) {
            $paginated_data = Auth::user()->suggestedFriends();
        } else {
            //DB::enableQueryLog();
            $paginated_data = Auth::user()->suggestedFriendsWithoutDistance();
        }
        //DB::enableQueryLog();

        if ($search_name != null) {
            $paginated_data->whereRaw('(first_name like "%' . $search_name . '%" or last_name like "%' . $search_name . '%")');
        }

        if ($location != null) {
            $paginated_data->whereRaw('location like "%' . $location . '%"');
        }

        if ($school != null) {
            $paginated_data = $paginated_data->whereHas('educations', function ($query) use ($school) {
                $query->where('school_college_name', 'like', "%$school%");
            });
            ;
        }

        if ($college != null) {
            $paginated_data = $paginated_data->whereHas('educations', function ($query) use ($college) {
                $query->where('school_college_name', 'like', "%$college%");
            });
            ;
        }

        if ($company != null) {
            $paginated_data = $paginated_data->whereHas('experiences', function ($query) use ($company) {
                $query->where('company_name', 'like', "%$company%");
            });
            ;
        }

        $paginated_data = $paginated_data->paginate(10);
        //dd($paginated_data->items());
        //DB::enableQueryLog();
        //Mutual Friends
        if (count($paginated_data->items()) > 0) {
            foreach ($paginated_data->items() as $key => $user) {
                $user_friends = $user->userFriends()->where('is_accepted', '=', 1)->get();
                $mutual_friend_count = 0;
                // if (count($user_friends) > 0)
                // {
                //     foreach ($user_friends as $kuser_friends => $user_friends) {
                //        $mutual_friend = $user_friends->friend->mutualFriendsOfLoggedInUser(Auth::user()->id)->first();
                //        if ($mutual_friend != null)
                //        {
                //          $mutual_friend_count = $mutual_friend_count + 1;
                //        }
                //     }
                // }
                if (count($user_friends) > 0) {
                    foreach ($user_friends as $kuser_friends => $user_friends) {
                        $mutual_friend = $user_friends->friend->userFriends()
                            ->where('is_accepted', '=', 1)
                            ->where('friend_id', '=', Auth::user()->id)->first();
                        if ($mutual_friend != null) {
                            $mutual_friend_count = $mutual_friend_count + 1;
                        }
                    }
                }
                $paginated_data->items()[$key]['mutual_friends'] = $mutual_friend_count;
            }
        }

        //dd($paginated_data->items()->asArray());
        //dd(DB::getQueryLog());
        return response()->json([
            'paginated_data' => $paginated_data
        ], 200);
    }

    public function userNotifications(Request $request)
    {
        $paginated_data = Auth::user()->userNotifications()->paginate(10);

        if (count($paginated_data->items()) > 0) {
            foreach ($paginated_data->items() as $key => $notification) {
                //dd($notification['data']['sender_id']);
                if ($notification['data']['sender_id'] != null) {
                    $paginated_data->items()[$key]['sender_details'] = User::find($notification['data']['sender_id']);
                }
            }
        }

        return response()->json([
            'paginated_data' => $paginated_data,
            'unread_notification_count' => Auth::user()->unreadNotifications()->count()
        ], 200);
    }

    public function markAsReadNotifications()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json([
            'markAsRead' => 'done'
        ], 200);
    }

    public function locations()
    {
        //DB::enableQueryLog();
        $locations = null;
        //dd(Auth::user()->locations->toArray());
        if (Auth::user()->locations()->count() > 0) {
            $locations = Auth::user()->locations()->with('region')->get()->toArray();
        }
        //dd(DB::getQueryLog());
        //dd(Auth::user()->locations()->with('region')->get());
        return response()->json([
            'regions' => $locations
        ], 200);
    }

    public function locationDelete(Location $location)
    {
        if ($location->locationable->id != auth()->user()->id) {
            return response()->json([
                'error' => 'Forbidden'
            ], 400);
        }
        $location->region->regionFollowers()->userExistsInRegion($location->region_id)->delete();
        $location->delete();

        return response()->json([
            'deleted' => 'done'
        ], 200);
    }

    public function locationDetails($region)
    {
        //dd($region);
        $region = Region::where(['url_slug' => $region])->first();
        if ($region == null) {
            return response()->json([
                'location' => null,
                'users' => null,
                'users_count' => null,
                'followers_list' => null
            ], 200);
        }
        $users = [];
        $locations = $region->locations();
        if ($locations->count() > 0) {
            // echo $region->locations()->count(); die;
            foreach ($locations->get() as $key => $location) {
                if ($location->locationable_type == 'App\\User') {
                    $users[] = $location->locationable;
                }
            }
        }
        $region_followers = $region->regionFollowers()->with('user', 'region')->get()->toArray();
        //dd($region->regionFollowers()->userExistsInRegion($region->id)->first());
        $is_followed = $region->regionFollowers()->userExistsInRegion($region->id)->first() == null ? false : true;
        return response()->json([
            'location' => $region,
            'users' => $users,
            //'users_count' => count($users),
            'followers_list' => $region_followers,
            'is_followed' => $is_followed
        ], 200);
    }

    public function locationPosts($region)
    {
        //dd($region);
        $region = Region::where(['url_slug' => $region])->first();
        if ($region == null) {
            return response()->json([
                'location' => null,
                'posts' => null
            ], 200);
        }
        $posts = [];

        $locations = $region->locations()->where(['locationable_type' => 'App\\Post'])->orderByDesc('created_at')->paginate(1);

        if ($locations->count() > 0) {
            // echo $region->locations()->count(); die;
            foreach ($locations as $key => $location) {
                // $locations[$key]['locationable'] = $location->locationable()->with(['posted_user', 'linked_images', 'tagged_tags' => function ($query) {
                //     $query->with('tag');
                // }, 'tagged_friends' => function ($query) {
                //     $query->with(['tagged_user_details' => function ($query) {
                //         $query->select('id', 'username', 'first_name', 'last_name', 'profile_picture', 'profile_picture_small');
                //     }, 'locations' => function ($query) {
                //         $query->with('region');
                //     }]);
                // }])->get();

                $locations[$key]['locationable'] = $location->locationable()->with(['posted_user', 'linked_images', 'tagged_tags' => function ($query) {
                    $query->with('tag');
                },
                    'tagged_friends' => function ($query) {
                        $query->with(['tagged_user_details' => function ($query) {
                            $query->select('id', 'username', 'first_name', 'last_name', 'profile_picture', 'profile_picture_small');
                        }]);
                    },
                    'locations' => function ($query) {
                        $query->with('region');
                    }])
                ->withCount(['likes', 'is_liked'])
                ->get();

                // $locations[$key]['linked_images'] = $location->locationable->linked_images;
                  // $locations[$key]['tagged_tags'] = $location->locationable->tagged_tags;
            }
        }
        return response()->json([
            'location' => $region,
            'posts' => $locations,
        ], 200);
    }

    /**
    * For location membership
    */
    public function userLocationEnroll(Request $request)
    {
        $input = $request->all();
        //dd($input);

        $rules = [
            'region_id' => 'required|exists:regions,id',
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        if (Auth::user()->locations()->count() <= 20 &&
            Auth::user()->locations()->where(['region_id' => $input['region_id']])->first() == null) {
            Auth::user()->locations()->create(['region_id' => $input['region_id']]);
        }
        return response()->json([
                'enrolled' => 'done'
            ], 200);
    }

    public function userLocationFollowUnfollow(Request $request)
    {
        $input = $request->all();
        //dd($input);

        $rules = [
            'region_id' => 'required|exists:regions,id',
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        $is_followed = false;
        $region = Region::find($input['region_id']);
        if ($region->regionFollowers()->userExistsInRegion($input['region_id'])->count() > 0) {
            $region->regionFollowers()->userExistsInRegion($input['region_id'])->delete();
        } else {
            $region->regionFollowers()->create(['region_id' => $input['region_id'], 'user_id' => Auth::user()->id]);
            $is_followed = true;
        }

        $region_followers = $region->regionFollowers()->with('user', 'region')->get()->toArray();
        return response()->json([
                'location_followers' => $region_followers,
                'is_followed' => $is_followed
            ], 200);
    }

    // public function markAsReadNotification(Request $request)
    // {

    //     Auth::user()->unreadNotifications->markAsRead();
    //     return response()->json([
    //         'markAsRead' => 'done'
    //     ] , 200);
    // }
}
