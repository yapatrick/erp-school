<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();
        DB::table('users')->insert([
        	['name'=>'Administrateur', 'email'=>'admin-erpschool@gmail.com', 'password'=>Hash::make("Secret!100%")],
        	['name'=>'NTADI Patrick', 'email'=>'yapateou@gmail.com', 'password'=>Hash::make("P@ssw0rd")],
        ]);

         DB::table('user_roles')->insert([
            "user_id" => "1",
            "id_role" => "1"
        ]);

        DB::table('user_roles')->insert([
            "user_id" => "2",
            "id_role" => "2"
        ]);
    }
}
