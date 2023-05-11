<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $newUser = new User([
            'name'=>'MDSClient',
            'email'=>'soporte@gmail.com',
            'password'=>Hash::make('123mdsclient'),
            'api_token'=>Str::random(60),
        ]);
        $newUser->save();
    }
}
