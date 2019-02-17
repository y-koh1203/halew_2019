<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    
    public function getProfile(){
        $uid = 1;
        $profile = DB::table('users')->first()->where('id',$uid);

        return $profile;
        die();
    }

    public function registerProfile(Request $r){
        $user_data = json_decode($json);

        return $user_data;
    }

    public function editProfile(){

    }
}
