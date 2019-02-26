<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class AdminController extends Controller
{
    //ロフインページを表示
    public function signin(){
        return view('admin');
    }

    public function signout(Request $r){
        $r->session()->flush();
        return view('admin');
    }

    //認証
    public function authnication(Request $r){
        $name = $r->post('name');
        $password = $r->post('password');

        $user_data = DB::table('teachers')
        ->where('name', '=', $name)
        ->where('password', '=', hash('sha256',$password))
        ->first();

        if($user_data === null){
            return view('admin')->with([
                'error' => 'ユーザ名、またはパスワードが違います'
            ]);
        }
        
        $r->session()->put('id', $user_data->id);

        //Homeに渡す時間割（教員用）    
        $days = [
            1 => 'sun',
            2 => 'mon',
            3 => 'tue',
            4 => 'wed',
            5 => 'thu',
            6 => 'fri',
            7 => 'sat'
        ];

        $lectures = [
            'sun' => [],
            'mon' => [],
            'tue' => [],
            'wed' => [],
            'thu' => [],
            'fri' => [],
            'sat' => []
        ];

        $teacher_data = DB::table('teachers')
        ->get()
        ->where('id', $user_data->id);

        $data = [];
        for($i = 1; $i <= 7; $i++){
            $tt[$i] = DB::table('teacher_tt_'.$days[$i])
            ->get()
            ->where('teacher_id', '=',$user_data->id);
        }

        for($i = 1; $i <= 6; $i++){
            for($j = 1; $j <= 7; $j++){   
                foreach($tt[$j] as $v){
                    $data[$i][$j] = $v->$i;
                }  
            } 
        }

        $lectures = DB::table('lectures')
        ->join('subjects', 'lectures.subject_id', '=' , 'subjects.id')
        ->join('teachers', 'lectures.teacher_id', '=', 'teachers.id')
        ->join('classes', 'lectures.class_id', '=', 'classes.id')
        ->select('lectures.*', 'subjects.subject_name', 'teachers.name', 'classes.class_name')
        ->get()
        ->where('teacher_id', $user_data->id);

        return view('home')->with([
            'timetable' => $data,
            'teacher_data' => $teacher_data,
            'lectures' => $lectures
        ]);
    }

    //教員用登録ページを表示
    public function signup(){
        $subjects = DB::table('subjects')->get();
        return view('register')->with([
            'subjects' => $subjects
        ]);
    }

    //教員の登録
    public function registration(Request $r){
        date_default_timezone_set('UTC');

        $name = $r->post('name');
        $password = $r->post('password');
        $c1 = $r->post('charge1');
        $c2 = $r->post('charge2');

        $res = DB::table('teachers')->insert([
            'name' => $name,
            'password' => hash('sha256', $password),
            'charge_1' => $c1,
            'charge_2' => $c2,
            'status' => '1'
        ]);

        if(!$res){
            return view('register')->with([
                'error' => '500'
            ]);
        }

        $id = DB::getPdo('teachers')->lastInsertId();
        $r->session()->put('id', $id);

        $days = [
            1 => 'sun',
            2 => 'mon',
            3 => 'tue',
            4 => 'wed',
            5 => 'thu',
            6 => 'fri',
            7 => 'sat'
        ];

        $dt = Carbon::now('Asia/Tokyo');
        $year = $dt->year;
        $year_from = $year.'-04-01';
        $year_to = $year + 1 .'-03-31';

        $hash_base = $id.$year;
        $hash_key = hash('sha256', $hash_base);

        foreach($days as $day){
            DB::table('teacher_tt_'.$day)->insert([
                'teacher_id' => $id,
                '1' => 0,
                '2' => 0,
                '3' => 0,
                '4' => 0,
                '5' => 0,
                '6' => 0,
                'year' => $year,
                'year_from' => $year_from,
                'year_to' => $year_to,
                'status' => '1',
                'hash' => $hash_key
            ]);
        }

        return view('home')->with([
            'login' => $id
        ]);
    }

    //ログインごのホームを表示
    public function displayHomePage(Request $r){
        $id = $r->session()->get('id');
        if($id === null){
            return view('admin');
        }

        //Homeに渡す時間割（教員用）    
        $days = [
            1 => 'sun',
            2 => 'mon',
            3 => 'tue',
            4 => 'wed',
            5 => 'thu',
            6 => 'fri',
            7 => 'sat'
        ];

        $lectures = [
            'sun' => [],
            'mon' => [],
            'tue' => [],
            'wed' => [],
            'thu' => [],
            'fri' => [],
            'sat' => []
        ];

        $teacher_data = DB::table('teachers')
        ->get()
        ->where('id', $id);

        $data = [];
        for($i = 1; $i <= 7; $i++){
            $tt[$i] = DB::table('teacher_tt_'.$days[$i])
            ->get()
            ->where('teacher_id', '=',$id);
        }

        for($i = 1; $i <= 6; $i++){
            for($j = 1; $j <= 7; $j++){   
                foreach($tt[$j] as $v){
                    $data[$i][$j] = $v->$i;
                }  
            } 
        }

        $lectures = DB::table('lectures')
        ->join('subjects', 'lectures.subject_id', '=' , 'subjects.id')
        ->join('teachers', 'lectures.teacher_id', '=', 'teachers.id')
        ->join('classes', 'lectures.class_id', '=', 'classes.id')
        ->select('lectures.*', 'subjects.subject_name', 'teachers.name', 'classes.class_name')
        ->get()
        ->where('teacher_id', $id);

        return view('home')->with([
            'timetable' => $data,
            'teacher_data' => $teacher_data,
            'lectures' => $lectures
        ]);
    }

    //生徒を登録するページを表示
    public function displayStudentRegistrationPage(Request $r){
        $id = $r->session()->get('id');
        if($id === null){
            return view('admin');
        }

        //クラス一覧取得
        //returnでクラス一覧を返す

        $classes = DB::table('classes')->get();

        return view('register_student')->with([
            'classes' => $classes
        ]);
    }

    //クラスに所属している生徒を一覧で取得(Ajax)
    public function getBelongStudentInClass(Request $r, $class_id){
        //クラスに所属する生徒一覧を取得する

        $res = DB::table('students')->get()->where('class_id','=',$class_id);

        return json_encode($res);
        //Ajaxなので、JSONを返す
    }

    //生徒の登録
    public function studentRegistration(Request $r){
        $id = $r->session()->get('id');
        if($id === null){
            return view('admin');
        }

        //生徒のinsert
        //出席番号周りの重複対策をする
    }
}
