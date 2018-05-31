<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'admin_name',
        'admin_email',
        'site_title',
        'contact_email',
        'contact_name',
        'contact_phone',
        'site_logo',		
        'no_of_min_coin_tiped_balance',
        'no_of_days_for_solve_dispute'	
    ];
}
