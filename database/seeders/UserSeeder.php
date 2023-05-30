<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'full_name' => 'Admin',
            'username' => 'admin',
            'email' => 'faturrr145@gmail.com',
            'password' => bcrypt('admin123'),
            'avatar' => 'https://storage.googleapis.com/ecocrafters-api.appspot.com/avatar.png'
        ]);
    }
}
