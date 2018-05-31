<?php namespace Techno\Transformers;


class UserTransformer extends Transformer {

    public function transform($user)
    {        
        
        return [
            'id'                    => $user['id'],
            'username'              => (isset($user['username']) && $user['username']== null ? 
                                        'NA' : $user['username']),
            'api_token'             => $user['api_token'],
            'first_name'            => $user['first_name'],
            'last_name'             => $user['last_name'],
            'email'                 => $user['email'],
            'date_of_birth'         => $user['date_of_birth'],
            'gender'                => $user['gender'],
            'location'              => $user['location'],
            'latitude'              => $user['latitude'],
            'longitude'             => $user['longitude'],
            'cover_picture'         => (isset($user['cover_picture']) && $user['cover_picture']=== null ? 
                                        'NA' : $user['cover_picture']),
            // 'cover_picture_small'              =>  url("uploads/cover_pictures")."/".$user['cover_picture_small'],
            'cover_picture_small'   =>  $user['cover_picture_small'],
            'profile_picture'       => ($user['profile_picture'] == null ? 
                                        'NA' : $user['profile_picture']),
            'profile_picture_small' => $user['cover_picture_small'],
            'about_me'              => $user['about_me'],
            'phone'                 => $user['phone'],
            'privacy_scope'         => ($user['privacy_scope']== null ? 
                                        'NA' : $user['privacy_scope'])
        ];
        
    }
    

    // TODO : Implement transform() method
}