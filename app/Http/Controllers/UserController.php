<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UserController extends Controller
{

    public function authnication(Request $r){
        $name = $r->post('name');
        $password = $r->post('password');

        $user = DB::table('students')
        ->join('classes','students.class_id','=','classes.id')
        ->select('students.*','classes.class_name')
        ->where('login_id',$name)
        ->where('password',$password)
        ->first();

        if($user === null){
            return json_encode([
                'code' => 403,
                'message' => 'user cannnot authrized'
            ]);
        }

        $token = hash('sha256', $user->id);

        $r->session()->put($token, $user->id);

        return json_encode([
            'user_data' => $user,
            'token' => $token
        ]);
    }
    
    public function checkAuth(Request $r){
        $token = $r->post('token');
        $session = $r->session()->get();

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

    public function signout(Request $r){
        $token = $r->get('token');
        if($r->session()->exists($token)){
            $r->session()->forget($token);
        }
    }

    public function getTimetable(Request $r, $class_id){
        $day = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];

        $date = $r->get('date');
        $dt = new Carbon($date);

        $dayOfWeek = $day[$dt->dayOfWeek];

        $timetable = DB::table('additional_timetable')
        ->where('class_id', $class_id)
        ->where('date', $date)
        ->first();

        if($timetable === null){
            $timetable = DB::table($dayOfWeek)
            ->where('class_id', $class_id)
            ->first();
        }

        $lectures = DB::table('lectures')
        ->join('subjects', 'lectures.subject_id', '=' , 'subjects.id')
        ->join('teachers', 'lectures.teacher_id', '=', 'teachers.id')
        ->select('lectures.*', 'subjects.subject_name')
        ->get()
        ->where('class_id', $class_id);

        if(count($lectures) === 0){
            $lectures = DB::table('lectures')
            ->join('subjects', 'lectures.subject_id', '=' , 'subjects.id')
            ->select('lectures.*', 'subjects.subject_name')
            ->get()
            ->where('class_id', $class_id);
        }

        $res = [];
        foreach($lectures as $v){
            $res[] = $v;
        }

        return json_encode([
            'timetable' => $timetable,
            'lectures' => $res
        ]);
    }
}
