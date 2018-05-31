<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Admin;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('admins')->insert([
        //     'name' => 'admin',
        //     'email' => 'admin@admin.com',
        //     'password' => '123456',
        //     'api_token' => str_random(10)
        // ]);
        Admin::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => '123456',
            'api_token' => str_random(10)
        ]);
    }
}
