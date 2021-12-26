<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            'username' => 'codehater',
            'name' => 'Riky Permana Putra',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password'),
            'phone' => '082240376552',
            'role' => 'ADMIN'
        ]);
    }
}
