<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\UserFriend;
use Validator;
use Auth;
use Illuminate\Validation\Rule;

use App\User;

use Techno\Transformers\UserFriendTransformer;
use DB;

class UserFriendController extends ApiController
{

    /**
     * @var Techno\Transformers\UserFriendTransformer
     */
    protected $userFriendTransformer;

    function __construct(UserFriendTransformer $userFriendTransformer)
    {
        $this->middleware('auth:api', ['only' => [
            'index', 'show', 'store',
            'update', 'delete',
            'create', 'requests',
            'block', 'unblock', 'blockedUsers',
            'follow', 'unfollow', 'followedUsers',
            'unfriend'
            ]]);
        $this->userFriendTransformer = $userFriendTransformer;
    }

    public function requests()
    {

        //$page = request()->get('page', 1);

        $paginatedItems =  UserFriend::with(array('friend'=>function($query){
            $query->select('id','username','first_name', 'last_name', 'profile_picture', 'profile_picture_small');
        }))
        ->where('user_id', '=', auth()->id())
        ->where('is_requested', '=', 0)
        ->where('is_accepted', '=', 3)
        ->paginate(10);
        //return $paginatedItems;
        // dd($paginatedItems);
        //Mutual Friends

        //DB::enableQueryLog();

        if (count($paginatedItems->items()) > 0) {
            foreach ($paginatedItems->items() as $key => $user) {

                $user_friends = $user->friend->userFriends()->where('is_accepted', '=', 1)->get();
                //dd($user_friends);
                $mutual_friend_count = 0;
                if (count($user_friends) > 0) {
                    foreach ($user_friends as $kuser_friends => $user_friends) {
                                $mutual_friend = $user_friends->friend->userFriends()
                                ->where('is_accepted', '=', 1)
                                ->where('friend_id', '=', Auth::user()->id)->first();
                       if ($mutual_friend != null)
                       {
                         $mutual_friend_count = $mutual_friend_count + 1;
                       }
                    }
                }

                $paginatedItems->items()[$key]['mutual_friends'] = $mutual_friend_count;
            }
        }
       //dd(DB::getQueryLog());

        return response()->json([
            'paginated_data' => $paginatedItems
        ] , 200);

    }

    public function index()
    {
        //$page = request()->get('page', 1);
        $search_name = request()->get('search_name', null);

        $paginatedItems = $this->userFriends(auth()->id(), $search_name);

        return response()->json([
            'paginated_data' => $paginatedItems
        ] , 200);

    }

    public function username($username)
    {
        $user = User::where('username', '=', $username)->first();

        if ($user == null)
        {
            return $this->respondWithForbiddenError($message = "Username is not valid.");
        }

        $search_name = request()->get('search_name', null);

        $paginatedItems = $this->userFriends($user->id, $search_name);
        return response()->json([
            'paginated_data' => $paginatedItems
        ] , 200);
    }

    protected function userFriends($user_id, $search_name = null)
    {
        //dd(Auth::user());
        //DB::enableQueryLog();
        /* who blocked you*/
        $user_blocked_you = UserFriend::where('friend_id', '=', $user_id)
                            ->where('is_blocked', 1)
                            ->get()->pluck('user_id')->toArray();

        //echo "search_name:".$search_name;
        $query =  UserFriend::with(array('friend'=>function($query){
            $query->select('id','username','first_name', 'last_name', 'profile_picture', 'profile_picture_small');
        }))
        ->where('user_id','=', $user_id)
        ->where('is_accepted','=', 1)
        ->where('is_blocked','=', 0);
        if (count($user_blocked_you) > 0)
        {
             $query->whereNotIn('friend_id', $user_blocked_you);
        }

        if ($search_name != null)
        {
            $query = $query->whereHas('friend', function($query) use ($search_name){
                    $query->where('first_name', 'like', "%$search_name%");
                    $query->orWhere('last_name', 'like', "%$search_name%");
                });
        }

        $paginatedItems = $query->paginate(10);
        //dd(DB::getQueryLog());

        //Mutual Friends

        //DB::enableQueryLog();

        if (count($paginatedItems->items()) > 0)
        {
            foreach ($paginatedItems->items() as $key => $user) {

                $user_friends = $user->friend->userFriends()->where('is_accepted', '=', 1)->get();
                //dd($user_friends);
                $mutual_friend_count = 0;
                if (count($user_friends) > 0)
                {
                    foreach ($user_friends as $kuser_friends => $user_friends) {

                            $mutual_friend = $user_friends->friend->userFriends()
                            ->where('is_accepted', '=', 1)
                            ->where('friend_id', '=', $user_id)->first();

                       if ($mutual_friend != null)
                       {
                         $mutual_friend_count = $mutual_friend_count + 1;
                       }
                    }
                }

                $paginatedItems->items()[$key]['mutual_friends'] = $mutual_friend_count;
            }
        }
       //dd(DB::getQueryLog());

        return $paginatedItems;

    }



    public function show(UserFriend $user_friend)
    {

        if (Auth::user()->cant('access', $user_friend)) {
            return $this->respondWithForbiddenError($message = "You are not the owner of the user friend");
        }

        if(! $user_friend)
        {
            return $this->respondNotFound('User Friend does not exist');
        }


        return $this->respondWithSuccess("User Friend Detail",
            $this->userFriendTransformer->transform($user_friend->toArray())
        );
    }

    public function store(Request $request)
    {

        //dd(Auth::user()->id);
        if (Auth::user()->cant('create', UserFriend::class)) {
            return $this->respondWithForbiddenError($message = "You are not the owner of the user friend");
        }
        // $request->merge([
        //     'is_blocked' => 0
        // ]);
        $input = $request->all();
        //dd($input);
        $validator = $this->validateResource($input);

        if ($validator != null)
        {

            return $this->respondWithValidationError($validator);
        }
        $input['is_requested'] = 1;
        $input['is_accepted'] = 3;
        $user_friend = $this->create($input);

        /* Add the requested user as a friend of the user who got the friend request.*/
        // $input_data = [
        //     'user_id'                   => $user_friend->friend_id,
        //     'friend_id'                 => $user_friend->user_id,
        //     'is_requested'              => 0,
        //     'is_accepted'               => 3,
        //     'friend_link_id'            => $user_friend->id
        // ];
        // $linked_friend = UserFriend::create($input_data);

        //echo $user_friend->id;
        $linked_friend = UserFriend::updateOrCreate(
            /*conditions to check if data exists with these column data*/
            ['user_id' => $user_friend->friend_id, 'friend_id' => $user_friend->user_id],
            /*it will update if record exists or create with it*/
            ['is_requested' => 0, 'is_accepted' => 3, 'friend_link_id' => $user_friend->id]
        );
        //echo $linked_friend->toJson();
        //dd($linked_friend);
        $linked_friend->userFriendRequestSend($linked_friend);
        //dd($user_friend->toArray());
        return $this->respondCreated($message = "User Friend request send",
            $this->userFriendTransformer->transform($user_friend->toArray())
        );
    }

    protected function validateResource($input, $update = false)
    {
        $rules_is_accepted = $update === true ? [1] : [3];
        //$rules_is_requested = $update === true ? [0] : [1];

        $rules = [
            //'user_id' => 'required',
            'friend_id' => 'required|numeric',
            // 'is_requested' => [
            //     'required',
            //     Rule::in([1]),
            // ],
            // 'is_accepted' => [
            //     'required',
            //     Rule::in($rules_is_accepted),
            // ]

        ];
        // $validator = Validator::make($input, $rules, $messages);
        $validator = Validator::make($input, $rules);

        $validator->after(function ($validator) use ($input, $update){
            if (check_valid_user($input['friend_id']) === false)
            {
                $validator->errors()->add('friend_id', 'You have not requested a valid user.');
            }
            else if ($update === false && $this->checkRequestSelf($input['friend_id']) === false) {
                $validator->errors()->add('user_id', 'You cannot sent friend request to yourself.');
            }
            else if ($update === false && $this->checkFriendRequestSent($input['friend_id']) === false) {
                $validator->errors()->add('friend_id', 'You have already sent friend request to this user.');
            }
            // else if ($update === false && $this->checkFriendRequestReceived($input['friend_id']) === false) {
            //     $validator->errors()->add('friend_id', 'You have already received friend request from this user.');
            // }

        });


        if ($validator->fails()) {

            return $validator->errors();
        }
        return null;
    }

    public function checkFriendRequestSent($friend_id)
    {
        $count = UserFriend::where('user_id', '=', Auth::user()->id)
        			->where('friend_id', '=', $friend_id)
        			->where('is_accepted', '=', 3)
        			->count();

        if ($count > 0) {
             return false;
        }

        return true;
    }

    // public function checkFriendRequestReceived($friend_id)
    // {
    //     $count = UserFriend::where('user_id', '=', $friend_id)
    //     					->where('friend_id', '=', Auth::user()->id)->count();

    //     if ($count > 0) {
    //          return false;
    //     }

    //     return true;
    // }

    public function checkRequestSelf($friend_id)
    {

        if (Auth::user()->id == $friend_id)
            {

                return false;
            }
        return true;
    }



    /**
     * Create a new user friend instance after a valid education.
     *
     * @param  array  $data
     * @return Education
     */
    protected function create(array $data)
    {
        // $input_data = [
        //     'user_id'                   => Auth::user()->id,
        //     'friend_id'                 => isset($data['friend_id']) ? $data['friend_id'] : null,
        //     'is_requested'              => isset($data['is_requested']) ? $data['is_requested'] : null,
        //     'is_accepted'               => isset($data['is_accepted']) ? $data['is_accepted'] : null,
        //     'friend_link_id'            => 0
        // ];
        //dd($input_data);
        //return UserFriend::create($input_data);
        return UserFriend::updateOrCreate(
            /*conditions to check if data exists with these column data*/
            ['user_id' => Auth::user()->id, 'friend_id' => $data['friend_id']],
            /*it will update if record exists or create with it*/
            ['is_requested' => 1, 'is_accepted' => 3, 'friend_link_id' => 0]
        );
    }


    public function update(Request $request, UserFriend $user_friend)
    {
        // dd(Auth::user()->can('update', $user_friend));
        if (Auth::user()->cant('update', $user_friend)) {
            return $this->respondWithForbiddenError($message = "You can't accept the friend request.");
        }
        $request->merge([
            'friend_id' => $user_friend->friend_id,
            //'is_requested' => $user_friend->is_requested
        ]);
        //dd($request->all());
        $input = $request->all();

        // $input['is_requested'] = 1;
        $input['is_accepted'] = 1;

        $validator = $this->validateResource($input, $update = true);

        if ($validator != null)
        {
            return $this->respondWithValidationError($validator);
        }


        $user_friend->update($input);
        /* Add the requested user as a friend of the user who got the friend request.*/
        //dd($user_friend->toArray());
        $requested_friend = UserFriend::where('id', '=', $user_friend->friend_link_id)->first();
        // dd($requested_friend->toArray());
        $requested_friend->is_accepted = 1;
        $requested_friend->save();

        $requested_friend->userFriendRequestAccept();

        //dd($user_friend);
        // return response()->json($user_friend, 201);
        return $this->respondWithSuccess($message = "User Friend accepted",
            $this->userFriendTransformer->transform($user_friend->toArray())
        );


    }

    public function delete(UserFriend $user_friend)
    {
        //dd($user_friend);
        if (Auth::user()->cant('delete', $user_friend)) {
            return $this->respondWithForbiddenError($message = "You can't delete the friend request.");
        }
        else if (Auth::user()->cant('cantDeleteAcceptedRequest', $user_friend)) {
            return $this->respondWithForbiddenError($message = "You can't delete an accepted friend request.");
        }
        //dd(Auth::user()->cant('cantDeleteAcceptedRequest', $user_friend));
        $requested_friend = UserFriend::where('id', '=', $user_friend->friend_link_id)->first();
        $requested_friend->delete();
        $user_friend->delete();

        return $this->respondDeleted();

    }

    public function unfriend(Request $request)
    {
    	$input = $request->all();
    	$friend_relation = UserFriend::where('user_id' ,'=', auth()->id())
    									->where('friend_id', '=', $input['friend_id'])->first();
    	if ($friend_relation == null)
    	{
    		return $this->respondWithForbiddenError($message = "Sorry you don't have such friend.");
    	}
		$friend_relation->is_accepted = 0;
    	$friend_relation->save();

    	$friend_relation_assoc = UserFriend::where('user_id' ,'=', $input['friend_id'])
    									->where('friend_id', '=', auth()->id())->first();
    	$friend_relation_assoc->is_accepted = 0;
    	$friend_relation_assoc->save();



    	return $this->respondWithSuccess($message = "User unfriend successfully",
            []
        );

    }


    public function block(Request $request)
    {
        $data = $request->all();
        $rules = [
            'friend_id' => 'required|numeric'
        ];
        $validator = Validator::make($data, $rules);

        $validator->after(function ($validator) use ($data){
            if (check_valid_user($data['friend_id']) === false)
            {
                $validator->errors()->add('friend_id', 'You have not requested a valid user.');
            }
            else if ($this->checkRequestSelf($data['friend_id']) === false) {
                $validator->errors()->add('friend_id', 'You cannot block yourself.');
            }
        });


        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors());
        }


        $block = UserFriend::updateOrCreate(
            /*conditions to check if data exists with these column data*/
            ['user_id' => Auth::user()->id, 'friend_id' => $data['friend_id']],
            /*it will update if record exists or create with it*/
            ['is_accepted' => 0, 'is_blocked' => 1, 'is_followed' => 0]
        );
        return $this->respondWithSuccess($message = "User blocked successfully",
            []
        );
    }

    public function unblock(Request $request)
    {
    	$data = $request->all();
        $rules = [
            'friend_id' => 'required|numeric'
        ];
        $validator = Validator::make($data, $rules);

        $validator->after(function ($validator) use ($data){
            if (check_valid_user($data['friend_id']) === false)
            {
                $validator->errors()->add('friend_id', 'You have not requested a valid user.');
            }
            else if ($this->checkRequestSelf($data['friend_id']) === false) {
                $validator->errors()->add('friend_id', 'You cannot unblock yourself.');
            }
        });


        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors());
        }

        $unblock = UserFriend::updateOrCreate(
            /*conditions to check if data exists with these column data*/
            ['user_id' => Auth::user()->id, 'friend_id' => $data['friend_id']],
            /*it will update if record exists or create with it*/
            ['is_blocked' => 0]
        );

        $unblock_assoc = UserFriend::updateOrCreate(
            /*conditions to check if data exists with these column data*/
            ['user_id' =>$data['friend_id'] , 'friend_id' => Auth::user()->id],
            /*it will update if record exists or create with it*/
            ['is_accepted' => 0, 'is_blocked' => 0, 'is_followed' => 0 ]
        );
        return $this->respondWithSuccess($message = "User unblocked successfully",
            []
        );
    }

    public function blockedUsers()
    {
    	$paginatedItems =  UserFriend::with(array('friend'=>function($query){
            $query->select('id','username','first_name', 'last_name', 'profile_picture', 'profile_picture_small');
        }))
        ->where('user_id','=', auth()->id())
        ->where('is_blocked','=', 1)
        ->get();

        return $this->respondWithSuccess($message = "User blocked List",
            $paginatedItems->toArray()
        );
    }

    public function follow(Request $request)
    {
        $data = $request->all();
        $rules = [
            'friend_id' => 'required|numeric'
        ];
        $validator = Validator::make($data, $rules);

        $validator->after(function ($validator) use ($data){
            if (check_valid_user($data['friend_id']) === false)
            {
                $validator->errors()->add('friend_id', 'You have not requested a valid user.');
            }
            else if ($this->checkRequestSelf($data['friend_id']) === false) {
                $validator->errors()->add('friend_id', 'You cannot follow yourself.');
            }
        });


        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors());
        }


        $followed_friend = UserFriend::updateOrCreate(
            /*conditions to check if data exists with these column data*/
            ['user_id' => Auth::user()->id, 'friend_id' => $data['friend_id']],
            /*it will update if record exists or create with it*/
            ['is_followed' => 1]
        );

        $followed_friend->userFollow($followed_friend);
        return $this->respondWithSuccess($message = "User followed successfully",
            []
        );
    }

    public function unfollow(Request $request)
    {
        $data = $request->all();
        $rules = [
            'friend_id' => 'required|numeric'
        ];
        $validator = Validator::make($data, $rules);

        $validator->after(function ($validator) use ($data){
            if (check_valid_user($data['friend_id']) === false)
            {
                $validator->errors()->add('friend_id', 'You have not requested a valid user.');
            }
            else if ($this->checkRequestSelf($data['friend_id']) === false) {
                $validator->errors()->add('friend_id', 'You cannot unfollow yourself.');
            }
        });


        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors());
        }

        $block = UserFriend::updateOrCreate(
            /*conditions to check if data exists with these column data*/
            ['user_id' => Auth::user()->id, 'friend_id' => $data['friend_id']],
            /*it will update if record exists or create with it*/
            ['is_followed' => 0]
        );
        return $this->respondWithSuccess($message = "User unblocked successfully",
            []
        );
    }

    public function followedUsers()
    {
        $search_name = request()->get('search_name', null);
        /* who blocked you*/
        $user_blocked_you = UserFriend::where('friend_id', '=', auth()->id())
                            ->where('is_blocked', 1)
                            ->get()->pluck('user_id')->toArray();

        $query =  UserFriend::with(array('friend'=>function($query){
            $query->select('id','username','first_name', 'last_name', 'profile_picture', 'profile_picture_small');
        }))
        ->where('user_id','=', auth()->id())
        ->where('is_followed','=', 1)
        ->where('is_blocked','=', 0);
        if (count($user_blocked_you) > 0)
        {
             $query->whereNotIn('friend_id', $user_blocked_you);
        }


        if ($search_name != null)
        {
            $query = $query->whereHas('friend', function($query) use ($search_name){
                    $query->where('first_name', 'like', "%$search_name%");
                    $query->orWhere('last_name', 'like', "%$search_name%");
                });
        }

        $paginatedItems = $query->paginate(10);

        //Mutual Friends

        //DB::enableQueryLog();

        if (count($paginatedItems->items()) > 0)
        {
            foreach ($paginatedItems->items() as $key => $user) {

                $user_friends = $user->friend->userFriends()->where('is_accepted', '=', 1)->get();
                //dd($user_friends);
                $mutual_friend_count = 0;
                if (count($user_friends) > 0)
                {
                    foreach ($user_friends as $kuser_friends => $user_friends) {
                            $mutual_friend = $user_friends->friend->userFriends()
                            ->where('is_accepted', '=', 1)
                            ->where('friend_id', '=', Auth::user()->id)->first();
                       if ($mutual_friend != null)
                       {
                         $mutual_friend_count = $mutual_friend_count + 1;
                       }
                    }
                }

                $paginatedItems->items()[$key]['mutual_friends'] = $mutual_friend_count;
            }
        }
       //dd(DB::getQueryLog());

        return response()->json([
            'paginated_data' => $paginatedItems
        ] , 200);
    }

}