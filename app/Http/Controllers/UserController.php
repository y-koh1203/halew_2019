<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function authnication(Request $r){
        $name = $r->post('name');
        $password = $r->post('password');

        $user = DB::table('students')
        ->where('name',$name)
        ->where('password', hash('sha256',$password))
        ->first();

        if($user === null){
            return json_encode([
                'code' => 403,
                'message' => 'user cannnot authrized'
            ]);
        }

        $token = hash('sha256', $user->id);

        $r->session()->put($user->id,'token');
        return json_encode([
            'user_data' => $user,
            'token' => $token
        ]);
    }
    
    public function checkAuth(Request $r){
        $token = $r->post('token');
        $session = $r->session->get();

        foreach($session as $k => $v){
            if($v === $token){
                return json_encode([
                    'result' => true,
                    'id' => $k
                ]);
            }
        }

        return json_encode([
            'result' => false
        ]);
    }

    public function getTimetable(Request $r){
        
    }
}
