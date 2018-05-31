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

class LikeController extends ApiController
{

    public function __construct()
    {
       
        $this->middleware('auth:api', ['only' => [
            'postlikeUnlike'
        ]]);
    }

    public function postlikeUnlike(Post $post)
    {
        if ($post->likes()->where(['user_id' => Auth::user()->id])->count()>0)
        {
            $post->likes()->where(['user_id' => Auth::user()->id])->delete();
        }
        else
        {
            $post->likes()->create(['user_id' => Auth::user()->id]);
        }
        
        return $this->respondWithSuccess('Like Unlike List', $post->likes);
    }
    
}
