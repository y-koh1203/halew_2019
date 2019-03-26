<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use function GuzzleHttp\json_encode;

class TimetableController extends Controller
{
    public function index(Request $r){
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        $teacher_name = DB::table('teachers')
        ->where('id', $id)
        ->first();

        $classes = DB::table('classes')
        ->get()
        ->where('homeroom_teacher_id',$id);

        return view('timetable')->with([
            'classes' => $classes,
            'tn' => $teacher_name->name
        ]);
    }

    public function displayTimetable(Request $r, $class_id){
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        $teacher_name = DB::table('teachers')
        ->where('id', $id)
        ->first();

        $classes = DB::table('classes')->get();

        $timetable = [];

        $days = [
            1 => 'sun',
            2 => 'mon',
            3 => 'tue',
            4 => 'wed',
            5 => 'thu',
            6 => 'fri',
            7 => 'sat'
        ];

        $data = [];
        for($i = 1; $i <= 7; $i++){
            $tt[$i] = DB::table($days[$i])
            ->get()->where('class_id', '=', $class_id);
        }

        for($i = 1; $i <= 6; $i++){
            for($j = 1; $j <= 7; $j++){   
                foreach($tt[$j] as $v){
                    $data[$i][$j] = $v->$i;
                }  
            } 
        }

        $d = $this->getWeekCalender(true, Carbon::now());

        $dt = Carbon::now('Asia/Tokyo');
        $year = $dt->year;

        $addtional = [];
        for($i = 1;$i < 7; $i++){
            foreach($d as $k => $date){
                $res = DB::table('additional_timetable')
                ->where('class_id', $class_id)
                ->where('date', $year.'-'.$date['month'].'-'.$date['day'])
                ->first();
    
                if($res != null){
                    $addtional[$i][$k+1] = $res->$i;
                }else{
                    $addtional[$i][$k+1] = null;
                }
            }    
        }
      
        $lectures = DB::table('lectures')
        ->join('subjects', 'lectures.subject_id', '=' , 'subjects.id')
        ->join('teachers', 'lectures.teacher_id', '=', 'teachers.id')
        ->select('lectures.*', 'subjects.subject_name', 'teachers.name')
        ->get()
        ->where('class_id', $class_id);
        $classes = DB::table('classes')->get()->where('homeroom_teacher_id',$id);
       
        return view('timetable')->with([
            'class_id' => $class_id,
            'lectures' => $lectures,
            'classes' => $classes,
            'timetable' => $data,
            'date' => $d,
            'additional' => $addtional,
            'tn' => $teacher_name->name
        ]);
    }

    public function registerTimetable(Request $r, $class_id){
        //クラスに所属する生徒一覧を取得する
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        $teacher_name = DB::table('teachers')
        ->where('id', $id)
        ->first();

        $timetable = [];

        $days = [
            1 => 'sun',
            2 => 'mon',
            3 => 'tue',
            4 => 'wed',
            5 => 'thu',
            6 => 'fri',
            7 => 'sat'
        ];

        $data = [];
        for($i = 1; $i <= 7; $i++){
            $tt[$i] = DB::table($days[$i])
            ->get()->where('class_id', '=', $class_id);
        }

        for($i = 1; $i <= 6; $i++){
            for($j = 1; $j <= 7; $j++){   
                foreach($tt[$j] as $v){
                    $data[$i][$j] = $v->$i;
                }
            } 
        }

        $classes = DB::table('classes')->get()
        ->where('homeroom_teacher_id',$id);

        $lectures = DB::table('lectures')
        ->join('subjects', 'lectures.subject_id', '=' , 'subjects.id')
        ->join('teachers', 'lectures.teacher_id', '=', 'teachers.id')
        ->select('lectures.*', 'subjects.subject_name', 'teachers.name')
        ->get()
        ->where('class_id', $class_id);

        return view('timetable_register')->with([
            'class_id' => $class_id,
            'lectures'=> $lectures,
            'timetable' => $data,
            'classes' => $classes,
            'tn' => $teacher_name->name
        ]);
    }

    public function registrationTimetable(Request $r, $class_id){
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        $teacher_name = DB::table('teachers')
        ->where('id', $id)
        ->first();
        
        $timetable = [];
        $teachers = [];

        $days = [
            1 => 'sun',
            2 => 'mon',
            3 => 'tue',
            4 => 'wed',
            5 => 'thu',
            6 => 'fri',
            7 => 'sat'
        ];

        for($i = 1; $i <= 7; $i++){
            for($j = 0; $j <= 7; $j++){
                $timetable[$i][] = $r->post($i.'-'.$j);
            }
        }

        $dt = Carbon::now('Asia/Tokyo');
        $year = $dt->year;
        $year_from = $year.'-04-01';
        $year_to = $year + 1 .'-03-31';

        $d = $this->getWeekCalender(true, Carbon::now());

        $hash_base = $class_id.$year;
        $hash_key = hash('sha256', $hash_base);

        $addtional = [];
        for($i = 1;$i < 7; $i++){
            foreach($d as $k => $date){
                $res = DB::table('additional_timetable')
                ->where('class_id', $class_id)
                ->where('date', $year.'-'.$date['month'].'-'.$date['day'])
                ->first();
    
                if($res != null){
                    $addtional[$i][$k+1] = $res->$i;
                }else{
                    $addtional[$i][$k+1] = null;
                }
            }    
        }

        DB::beginTransaction();
        try{
            //時間割一覧を回す
            foreach($timetable as $k => $v){
                //$k 曜日
                //$v 時間割配列


                foreach($v as $time => $lecture_id){
                    //$time 時間
                    //$lecture_id その時間に対して設定されているlecture_id

                    if($lecture_id === null){
                        continue;
                    }

                    //lecture_idで、そもそもlectureに登録されているかを確認する
                    $check_lecture = DB::table('lectures')
                    ->where('id',$lecture_id)
                    ->first();

                    if($check_lecture === null){
                        continue;
                    }

                    //すでに登録ずみであればそのlectureに登録されているteacher_idを取得する
                    $exist_lecture = DB::table('teacher_tt_'.$days[$k])
                    ->where('teacher_id',$check_lecture->teacher_id)
                    ->first();

                    //その時間に授業が未設定(0)であれば、何もせず次のループへ
                    if($exist_lecture->$time != 0 && $exist_lecture->$time != null){
                       continue;
                    }

                    //教員時間割テーブルを更新する
                    DB::table('teacher_tt_'.$days[$k])
                    ->where('teacher_id',$check_lecture->teacher_id)
                    ->update([
                        $time => 0
                    ]);

                    //この後、lecture_idで入手したteacher_idを元に、
                    //該当の教員時間割をupdate

                    $new_teacher = DB::table('lectures')
                    ->where('id',$lecture_id)
                    ->first();

                    DB::table('teacher_tt_'.$days[$k])
                    ->where('teacher_id',$new_teacher->teacher_id)
                    ->update([
                        $time => $lecture_id
                    ]);
                }

                $sql = "INSERT INTO `${days[$k]}` (`class_id`, `1`, `2`, `3`, `4`, `5`, `6`, `year`, `year_from`, `year_to`, `status`, `hash`, `created_at`, `updated_at`) "
                ."VALUES (${class_id}, ${v[1]}, ${v[2]}, ${v[3]}, ${v[4]}, ${v[5]}, ${v[6]}, ${year}, '${year_from}', '${year_to}', 1, '${hash_key}', null, null) "
                ."ON DUPLICATE KEY UPDATE "
                ."`1` = ${v[1]}, "
                ."`2` = ${v[2]}, "
                ."`3` = ${v[3]}, "
                ."`4` = ${v[4]}, "
                ."`5` = ${v[5]}, "
                ."`6` = ${v[6]} ;"; 

                DB::insert($sql);
            }
        }catch(\PDOException $e){
            echo 'failed';
            DB::rollBack();
            return false;
        }
        DB::commit(); 

        $data = [];
        for($i = 1; $i <= 7; $i++){
            $tt[$i] = DB::table($days[$i])
            ->get()->where('class_id', '=', $class_id);
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
        ->select('lectures.*', 'subjects.subject_name', 'teachers.name')
        ->get()
        ->where('class_id', $class_id);
        $classes = DB::table('classes')->get()->where('homeroom_teacher_id',$id);
        
        return view('timetable')->with([
            'class_id' => $class_id,
            'lectures' => $lectures,
            'classes' => $classes,
            'timetable' => $data,
            'date' => $d,
            'tn' => $teacher_name->name,
            'additional' => $addtional
        ]);
    }

    //Ajaxで教員の重複を判定
    public function teacherExistCheck(Request $r, $day, $time, $lecture_id){
        $days = [
            1 => 'sun',
            2 => 'mon',
            3 => 'tue',
            4 => 'wed',
            5 => 'thu',
            6 => 'fri',
            7 => 'sat'
        ];

        $lecture = Db::table('lectures')
        ->where('id',$lecture_id)
        ->first();

        $teacher_timetable = DB::table('teacher_tt_'.$days[$day])
        ->where('teacher_id',$lecture->teacher_id)
        ->first();

        if($teacher_timetable->$time != 0 && $teacher_timetable->$time != null){
            return json_encode([
                'result' => false
            ]);
        }

        return json_encode([
            'result' => true
        ]);        
    }

    //指定された授業を、担当科目に持つ教員のみを取得
    public function getTeachers(Request $r, $class_id, $subject_id){
        $teachers = DB::select("select id, name from teachers where charge_1 = ${subject_id} or charge_2 = ${subject_id} ;");
        return json_encode($teachers);
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
