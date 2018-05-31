<?php namespace Techno\Transformers;

class EducationTransformer extends Transformer {

    public function transform($education)
    {        
        return [
            'id'                    => $education['id'],
            'user_id'               => $education['user_id'],
            'school_college_name'   => $education['school_college_name'],
            'board_university_name' => $education['board_university_name'],
            'degree_name'           => $education['degree_name']
        ];
        
    }
    // TODO : Implement transform() method
}