<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Post;
use Validator;
use Auth;
use Image;
use DB;
use Illuminate\Validation\Rule;
use App\Comment;

class CommentController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => [
            'postComment', 'postCommentListing'
        ]]);
    }

    public function postComment(Post $post, $parent_id = 0, Request $request)
    {
        $input = $request->all();
        $rules = [
            'comment' => 'required|string',
        ];
        $validator = Validator::make($input, $rules);

        if ($parent_id > 0)
        {
               $validator->after(function ($validator) use ($input, $parent_id) {
                if ($this->check_valid_parent($parent_id) === false) {
                    $validator->errors()->add('parent_id', 'You have not requested a valid parent.');
                }
            });
        }

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors());
        }
        $post->comments()->create(['user_id' => Auth::user()->id, 'comment' => $input['comment'], 'parent_id' => $parent_id]);

        return $this->respondWithSuccess('Commented successfully', $post->comment_list());
    }

    public function postCommentListing(Post $post, $page_limit=2)
    {
        return $this->respondWithSuccess('Comments', $post->comment_list($page_limit));
    }



    private function check_valid_parent($parent_id)
    {
        
        return (Comment::where(['id' => $parent_id, 'parent_id' => 0])->count() > 0 ? true : false);
    }
}
