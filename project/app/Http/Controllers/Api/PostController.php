<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tag;
use App\Post;
use Validator;
use Auth;
use Image;
use DB;
use Illuminate\Validation\Rule;
use App\LinkedImage;
use App\TaggedFriend;
use App\TaggedTag;


class PostController extends ApiController
{
    protected $post_type = ['local-wall'];

    public function __construct()
    {
        $this->middleware('auth:api', ['only' => [
            'index', 'tags',
            'localWall', 'postDelete', 'updateLocalWall'
        ]]);
    }

    public function tags()
    {
        $search_tag_keyword = request()->get('search_tag_keyword', null);
        $tags = Tag::latest()->where('tag_name', 'like', "%$search_tag_keyword%")->paginate(20);
        //return $tags;
        return $this->respondWithSuccess('Tags list with search_tag_keyword parameter', $tags);
    }

    public function index()
    {
        //DB::enableQueryLog();
        $news_feed = Post::latest()
                            ->whereHas('posted_user', function ($query) {
                                $query->where('id', Auth::user()->id);
                            })
                            /* if the posted user is a friend of logged in user*/
                            ->orwhereHas('posted_user', function ($query) {
                                $query->whereHas('userAcceptedFriendRequests', function ($q) {
                                    $q->where('friend_id', Auth::user()->id);
                                });
                            })
                            /* if the posted user is followed by logged in user*/
                            ->orwhereHas('posted_user', function ($query) {
                                $query->whereHas('userFollowedFriends', function ($q) {
                                    $q->where('friend_id', Auth::user()->id);
                                });
                            })
                            ->with(['linked_images', 'tagged_tags' => function ($query) {
                                $query->with('tag');
                            }, 'tagged_friends' => function ($query) {
                                $query->with(['tagged_user_details' => function ($query) {
                                    $query->select('id', 'username', 'first_name', 'last_name', 'profile_picture', 'profile_picture_small');
                                }]);
                            }])
                            ->paginate(20);
        //dd(DB::getQueryLog());
        //return $tags;
        return $this->respondWithSuccess('News feed', $news_feed);
    }

    /**
     * For posting in local wall function
     *
     * @param Request $request
     * @return void
     */
    public function localWall(Request $request)
    {
        $input = $request->all();

        // print_r($input['tagged_friends'][0]['friendId']); die();
        $rules = [
            'post_description' => 'required|not_null',
            'location' => 'required|not_null',
            'latitude' => 'required|not_null',
            'longitude' => 'required|not_null',
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors());
        }

        $post = Post::create([
            'post_description' => $input['post_description'],
            'location' => $input['location'],
            'latitude' => $input['latitude'],
            'longitude' => $input['longitude'],
            'post_type' => 'local-wall',
            'post_visibility' => ($input['post_visibility'] == 'Private' ? 1 : 0),
            'user_id' => Auth::user()->id
        ]);
        // post locations get created while creating the model
        //linked_images
        //dd($request->linked_images);
        if ($request->has('linked_images')) {
            foreach ($request->linked_images as $photo) {
                $path = 'uploads/linked_images/';
                $save_name = time() . str_random(10) . '.png';
                $small_pic_save_name = time() . str_random(10) . '_small.png';
                Image::make($photo)->save($path . $save_name, 100);
                Image::make($photo)->resize(1, 1)->save($path . $small_pic_save_name, 100);
                $image['actual_image'] = $save_name;
                $image['pixelated_image'] = $small_pic_save_name;
                $post->add_image($image);
            }
        }
        //tagged_tags
        if (isset($input['tagged_tags']) &&
            $input['tagged_tags'] != null &&
            count($input['tagged_tags']) > 0) {
            $post->add_tags($input['tagged_tags']);
        }
        //post tagged_friends
        if (isset($input['tagged_friends']) &&
            $input['tagged_friends'] != null &&
            count($input['tagged_friends']) > 0) {
            $post->add_tagged_friends($input['tagged_friends']);
        }

        return $this->respondWithSuccess('Post created', $post);
    }

    public function postDelete(Post $post)
    {
        //dd($post);
        if ($post->user_id != Auth::user()->id) {
            return $this->respondWithForbiddenError($message = "You can't delete this post.");
        }
        $post->deleteImages();
        $post->locations()->delete();
        $post->tagged_tags()->delete();
        $post->tagged_friends()->delete();
        $post->delete();
        if (Post::all()->count() == 0) {
            Post::truncate();
        }
        return $this->respondWithSuccess('Deleted Post', []);
    }

    public function updateLocalWall(Post $post, Request $request)
    {
        if ($post->user_id != Auth::user()->id) {
            return $this->respondWithForbiddenError($message = "You can't update this post.");
        }
        $input = $request->all();
        //dd($input);
        // print_r($input['tagged_friends'][0]['friendId']); die();
        $rules = [
            'post_description' => 'required',
            'location' => 'required|not_null',
            'latitude' => 'required|not_null',
            'longitude' => 'required|not_null'
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors());
        }
        //dd($input);
        $post->update([
            'post_description' => $input['post_description'],
            'location' => $input['location'],
            'latitude' => $input['latitude'],
            'longitude' => $input['longitude'],
            'post_visibility' => $input['post_visibility']
        ]);

        $this->commonUpdateTasks($request, $post);

        return $this->respondWithSuccess('Updated Post', $post);
    }

    private function commonUpdateTasks($request, $post)
    {
        $input = $request->all();
        // post locations get created while creating the model
        //linked_images
        //dd($request->linked_images);

        if ($request->has('deletedpostImage')) {
            foreach ($request->deletedpostImage as $key => $value) {
                //echo $value;
                $image_details = LinkedImage::where(['id' => $value])->first();
                //dd($image_details);
                if ($image_details !=null)
                {
                    $old_actual_image = 'uploads/linked_images/' . $image_details->actual_image;
                    $old_pixelated_image = 'uploads/linked_images/' . $image_details->pixelated_image;
                    if (file_exists($old_actual_image)) {
                        \File::delete($old_actual_image);
                    }
                    if (file_exists($old_pixelated_image)) {
                        \File::delete($old_pixelated_image);
                    }
                    $image_details->delete();
                }
                
            }
        }

        //dd($request->linked_images);
        if ($request->has('linked_images')) {
            foreach ($request->linked_images as $photo) {
                $path = 'uploads/linked_images/';
                $save_name = time() . str_random(10) . '.png';
                $small_pic_save_name = time() . str_random(10) . '_small.png';
                Image::make($photo)->save($path . $save_name, 100);
                Image::make($photo)->resize(1, 1)->save($path . $small_pic_save_name, 100);
                $image['actual_image'] = $save_name;
                $image['pixelated_image'] = $small_pic_save_name;
                $post->add_image($image);
            }
        }
        

        //tagged_tags
        if ($request->has('deleted_tags')) {
            foreach ($request->deleted_tags as $key => $value) {
                $tag_relation_details = TaggedTag::where(['id' => $value])->first();   
                if ($tag_relation_details != null) {
                    $tag_relation_details->delete();
                }
            }
        }
        if (isset($input['tagged_tags']) &&
            $input['tagged_tags'] != null &&
            count($input['tagged_tags']) > 0) {
            $post->add_tags($input['tagged_tags']);
        }
        

        //post tagged_friends
        if ($request->has('deletedTagged_friends')) {
            foreach ($request->deletedTagged_friends as $key => $value) {
                $tag_friend_relation_details = TaggedFriend::where(['id' => $value])->first();  
                if ($tag_friend_relation_details != null) {
                    $tag_friend_relation_details->delete();
                }              
                
            }
        }
        if (isset($input['tagged_friends']) &&
            $input['tagged_friends'] != null &&
            count($input['tagged_friends']) > 0) {
            //dd($input['tagged_friends']);
            $post->add_tagged_friends_update($input['tagged_friends']);
        }
        
    }
}
