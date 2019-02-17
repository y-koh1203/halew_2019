<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TimetableController extends Controller
{
    public function displayTimetable(Request $r, $class_id){
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        $classes = DB::table('classes')->get();

        //配列にはどうも時間割がinsertされた順番が0から順に入るらしい
        //どっから持ってきてるの...

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

        $subjects = DB::table('subjects')->get();
        $classes = DB::table('classes')->get()->where('homeroom_teacher_id',$id);
        
        return view('timetable')->with([
            'class_id' => $class_id,
            'subjects' => $subjects,
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

        $subjects = DB::table('subjects')->get();
        
        return view('timetable_register')->with([
            'class_id' => $class_id,
            'subjects'=> $subjects        
        ]);
    }

    public function registrationTimetable(Request $r, $class_id){
        //クラスに所属する生徒一覧を取得する
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
                $teachers[$i][] = $r->post($i.'-'.$j.'-t');
            }
        }

        $dt = Carbon::now('Asia/Tokyo');
        $year = $dt->year;

        $hash_base = $class_id.$year;
        $hash_key = hash('sha256', $hash_base);

        DB::beginTransaction();
        try{
            foreach($timetable as $k => $v){
                $sql = "INSERT INTO `${days[$k]}` (`class_id`, `1`, `2`, `3`, `4`, `5`, `6`, `year`, `status`, `hash`, `created_at`, `updated_at`) "
                ."VALUES (${class_id}, ${v[1]}, ${v[2]}, ${v[3]}, ${v[4]}, ${v[5]}, ${v[6]}, ${year}, 1, '${hash_key}', null, null) "
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

        $subjects = DB::table('subjects')->get();
        $classes = DB::table('classes')->get();
        
        return view('timetable')->with([
            'class_id' => $class_id,
            'subjects' => $subjects,
            'classes' => $classes,
            'timetable' => $data
        ]);
    }

    //指定された授業を、担当科目に持つ教員のみを取得
    public function getTeachers(Request $r, $class_id, $subject_id){
        $teachers = DB::select("select id, name from teachers where charge_1 = ${subject_id} or charge_2 = ${subject_id} ;");
        return json_encode($teachers);
    }
}
