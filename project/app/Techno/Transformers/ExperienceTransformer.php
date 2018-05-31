<?php namespace Techno\Transformers;

class ExperienceTransformer extends Transformer {

    public function transform($experience)
    {        
        return [
            'id'                    => $experience['id'],
            'user_id'               => $experience['user_id'],
            'designation'           => $experience['designation'],
            'company_name'          => $experience['company_name'],
            'location'              => $experience['location'],
            'latitude'              => $experience['latitude'],
            'longitude'             => $experience['longitude'],
            'from_month'            => ($experience['from_month']>=10 ? "'".$experience['from_month']."'" : "0".$experience['from_month']) ,
            'from_year'             => $experience['from_year'],
            'is_currently_working'  => (boolean) $experience['is_currently_working'],
            'to_month'              => ($experience['to_month']>=10 ? "'".$experience['to_month']."'" : "0".$experience['to_month']),
            'to_year'               => $experience['to_year'],
        ];
        
    }
    // TODO : Implement transform() method
}