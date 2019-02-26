<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use function GuzzleHttp\json_encode;

class TimetableController extends Controller
{
    public function displayTimetable(Request $r, $class_id){
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

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
            'timetable' => $data
        ]);
    }

    public function registerTimetable(Request $r, $class_id){
        //クラスに所属する生徒一覧を取得する
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        //display timetableの処理をほぼパクって実装すれば、
        //初期選択状態を実装できるのでは?

        $lectures = DB::table('lectures')
        ->join('subjects', 'lectures.subject_id', '=' , 'subjects.id')
        ->join('teachers', 'lectures.teacher_id', '=', 'teachers.id')
        ->select('lectures.*', 'subjects.subject_name', 'teachers.name')
        ->get()
        ->where('class_id', $class_id);

        return view('timetable_register')->with([
            'class_id' => $class_id,
            'lectures'=> $lectures        
        ]);
    }

    public function registrationTimetable(Request $r, $class_id){
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }
        
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

        $hash_base = $class_id.$year;
        $hash_key = hash('sha256', $hash_base);

        DB::beginTransaction();
        try{
            foreach($timetable as $k => $v){

                foreach($v as $time => $lecture_id){
                    $check_lecture = DB::table('lectures')
                    ->where('id',$lecture_id)
                    ->first();

                    if($check_lecture === null){
                        continue;
                    }

                    $exist_lecture = DB::table('teacher_tt_'.$days[$k])
                    ->where('teacher_id',$check_lecture->teacher_id)
                    ->first();

                    if($exist_lecture->$time != 0 && $exist_lecture->$time != null){
                        continue;
                    }

                    DB::table('teacher_tt_'.$days[$k])
                    ->where('teacher_id',$check_lecture->teacher_id)
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
            'timetable' => $data
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
}
