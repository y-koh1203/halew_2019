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
        $lid = $r->post('login_id');
        $password = $r->post('password');

        $user_data = DB::table('teachers')
        ->where('login_id', '=', $lid)
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
        ->where('id', $user_data->id)
        ->first();

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

        $d = $this->getWeekCalender(true, Carbon::now());

        //dd($data);

        return view('home')->with([
            'timetable' => $data,
            'teacher_data' => $teacher_data,
            'lectures' => $lectures,
            'tn' => $teacher_data->name,
            'date' => $d
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
        $lid = $r->post('login_id');

        $res = DB::table('teachers')->insert([
            'name' => $name,
            'password' => hash('sha256', $password),
            'login_id' => $lid,
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

        $teacher_data = DB::table('teachers')
        ->where('id', $id)
        ->first();

        $lectures = DB::table('lectures')
        ->join('subjects', 'lectures.subject_id', '=' , 'subjects.id')
        ->join('teachers', 'lectures.teacher_id', '=', 'teachers.id')
        ->join('classes', 'lectures.class_id', '=', 'classes.id')
        ->select('lectures.*', 'subjects.subject_name', 'teachers.name', 'classes.class_name')
        ->get()
        ->where('teacher_id', $id);

        $d = $this->getWeekCalender(true, Carbon::now());

        return view('home')->with([
            'timetable' => $data,
            'teacher_data' => $teacher_data,
            'lectures' => $lectures,
            'date' => $d,
            'tn'  => $teacher_data->name
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

    private function getWeekCalender($isStartSun = true, $date = "") {
        $today = new Carbon( $date );
        $todayDay = $today->day;
        $startDate = $this->getStartDay( $today->toDateString("Y-m-d"), $isStartSun );
        $startDay = $startDate->day;
        // 週の最終日を取得
        // note. コピーを作成しないと元のインスタンスの値が変更される
        $lastDay = $startDate->copy()->addDay(7)->day;
        
        // 開始日のある月の最終日を取得
        $limitDay = $startDate->copy()->endOfMonth()->day;
        
        $month = $startDate->month;
        $offset = $limitDay - $startDay;
        $day = $startDay;
        $weekArr = [];
        $i = 0;
        while($i < 7) {
            $day = $startDay + $i;
            // 月を跨いだ時
            if( $day > $limitDay ) {
            $day = $i - $offset;
            if($day === 1) {
                $month += 1;
            }
            if($month > 12) {
                $month = 1;
            }
            }
            if($isStartSun) {
            $week = $this->getWeekByIndex($i);
            } else {
            $week = $this->getWeekByIndex($i+1);
            }
            $weekArr[] = [
            'month' => $month,
            'day'   => $day,
            'week'  => $week,
            'today' => $todayDay === $day? true : false,
            ];
            $i++;
        }
    
        return $weekArr;
    }

    private function getStartDay($today, $isStartSun) {
        $dt = new Carbon( $today );
        
        // $today が週の内何日目か (Sun = 0)
        $w = $dt->dayOfWeek;
        
        // 月曜始まりのとき
        if( !$isStartSun ) {
            // 今日が日曜なら前の月曜
            if($w === 0) {
            $w = 7;
            }
            $w -= 1;
        }
        
        return $dt->subDay( $w );
    }

    private function getWeekByIndex($i) {
        $arr = ['Sun.', 'Mon.', 'Tue.', 'Wed.', 'Thu.', 'Fri.', 'Sat.'];
        $len = count($arr);
    
        if($i >= $len) {
            $i -= $len;
        }
    
        return $arr[$i];
    }
}
