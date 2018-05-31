<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use App\Region;
// use App\Tag;
use Str;
use File;

class Post extends Model
{
    /**
      * The attributes that aren't mass assignable.
      *
      * @var array
      */
    protected $guarded = [];

    /**
     * Boot the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saved(function ($post) {
            $location = $post->location;

            if ($location != null) {
                $location_params = explode(',', $location);
                if (count($location_params) > 0) {
                    foreach ($location_params as $location_key => $region) {
                        $region = trim($region);
                        if ($region != null) {
                            $url_slug = Str::slug($region);
                            $region_details = Region::updateOrCreate(
                                  ['region_name' => $region, 'url_slug' => $url_slug],
                                  ['region_name' => $region, 'url_slug' => $url_slug]
                              );
                            if ($post->locations()->count() <= 20 && $post->locations()->where(['region_id' => $region_details->id])->first() == null) {
                                /**Adding member to location**/
                                $post->locations()->create(['region_id' => $region_details->id]);
                            }
                        }
                    }
                }
            }
        });
    }

    public function posted_user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function locations()
    {
        return $this->morphMany(Location::class, 'locationable');
    }

    public function tagged_tags()
    {
        return $this->morphMany(TaggedTag::class, 'tagable');
    }

    public function linked_images()
    {
        return $this->morphMany(LinkedImage::class, 'linked_imageable');
    }

    public function tagged_friends()
    {
        return $this->morphMany(TaggedFriend::class, 'tag_friendable');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function is_liked()
    {
        return $this->likes()->where('user_id', auth()->user()->id);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function comment_list($paginated_value)
    {
        return $this->comments()->where(['parent_id' => 0])->with(['children'])->paginate($paginated_value);
    }

    public function add_tags($tags)
    {
        foreach ($tags as $tag_key => $tag) {
            $tag_slug = Str::slug($tag);
            $tag_details = Tag::updateOrCreate(
                ['tag_name' => $tag, 'tag_slug' => $tag_slug],
                ['tag_name' => $tag, 'tag_slug' => $tag_slug]
            );
            if ($this->tagged_tags()->where(['tag_id' => $tag_details->id])->first() == null) {
                $this->tagged_tags()->create(['tag_id' => $tag_details->id]);
            }
        }
    }

    public function add_image($image)
    {
        $this->linked_images()->create($image);
    }

    public function add_tagged_friends($friends)
    {
        foreach ($friends as $friend_key => $friend) {
            if ($this->tagged_friends()->where(['user_id' => $friend['friendId']])->first() == null) {
                $this->tagged_friends()->create(['user_id' => $friend['friendId']]);
            }
        }
    }
    public function add_tagged_friends_update($friends)
    {
        foreach ($friends as $friend_key => $friend) {
            if ($this->tagged_friends()->where(['user_id' => $friend])->first() == null) {
                $this->tagged_friends()->create(['user_id' => $friend]);
            }
        }
    }

    public function deleteImages()
    {
        if ($this->linked_images()->count() > 0) {
            $linked_images = $this->linked_images;
            foreach ($linked_images as $linked_key => $linked_image) {
                //dd($linked_image->actual_image);
                $image_path = 'uploads/linked_images/';
                if (File::exists($image_path . $linked_image->actual_image)) {
                    //dd(1);
                    //dd($linked_image->actual_image);
                    File::delete($image_path . $linked_image->actual_image);
                }
                if (File::exists($image_path . $linked_image->pixelated_image)) {
                    File::delete($image_path . $linked_image->pixelated_image);
                }
            }
            //dd(2);
            $this->linked_images()->delete();
        }
    }
}
