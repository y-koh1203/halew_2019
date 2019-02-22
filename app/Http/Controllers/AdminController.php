<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use function GuzzleHttp\json_decode;

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

        $user_data = DB::table('teachers')->get()
        ->where('name', '=', $name)
        ->where('password', '=', hash('sha256',$password));

        if(count($user_data) === 0){
            return view('admin')->with([
                'error' => 'ユーザ名、またはパスワードが違います'
            ]);
        }
        
        $r->session()->put('id', $user_data[0]->id);

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

        foreach($days as $d){
            $lecture = DB::table($d)->get();
            foreach($lecture as $v){

            }
        }

        return view('home');
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

        return view('home')->with([
            'login' => $id
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
