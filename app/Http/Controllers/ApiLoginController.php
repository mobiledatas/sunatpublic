<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class ApiLoginController extends Controller
{
    //
    public function login(Request $request)
    {
        $login = $request->validate([
            'email'=>'required|email',
            'password'=>'required|string'
        ]);

        if(!Auth::attempt($login)){
            return response(['message'=>'Invalid credentials']);
        }

        $token = Str::random(60);
 
        $request->user()->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();
 
        return response(['user'=>Auth::user(),'token'=>$token]);

    }
}
