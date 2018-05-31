<?php namespace Techno\Transformers;

class UserFriendTransformer extends Transformer {

    public function transform($userfriend)
    {      
        //dd($userfriend);  
        return [
            'id'                    => $userfriend['id'],
            'user_id'               => $userfriend['user_id'],
            'friend_id'             => $userfriend['friend_id'],
            'is_requested'          => $userfriend['is_requested'],/*   0-got request, 1-send request  */
            'is_accepted'           => $userfriend['is_accepted'],/*  0-rejected request, 1-accepted request, 3-Pending decision  */
            'is_blocked'            => $userfriend['is_blocked'],/*   0-Not blocked, 1- blocked  */
            'is_followed'            => $userfriend['is_followed']/*   0-Not followed, 1- followed  */
        ];
        
    }
    // TODO : Implement transform() method
}