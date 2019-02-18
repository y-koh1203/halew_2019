<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LectureController extends Controller
{
    public function registerLecture(Request $r, $class_id){
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        $classes = DB::table('classes')->get()
        ->where('homeroom_teacher_id',$id);
        
        return view('register_lecture')->with([
            'classes' => $classes,
            'class_id' => $class_id
        ]);
    }

    public function displayAllLecture(Request $r, $class_id){
        //未選択状態での非表示に対応させる
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }
        
        $lectures = [];
        $subjects = [];
        $teachers = [];

        $subjects = DB::table('subjects')->get();

        foreach($subjects as $subject){
            $sql = 'select * from teachers where charge_1 = '.$subject->id.' or charge_2 = '. $subject->id.' ;';
            $teachers[$subject->id] = DB::select($sql);
        }

        $classes = DB::table('classes')->get()
        ->where('homeroom_teacher_id',$id);
        
        return view('register_lecture')->with([
            'classes' => $classes,
            'class_id' => $class_id,
            'lectures' => $lectures,
            'subjects' => $subjects,
            'teachers' => $teachers
        ]);
    }

    public function registrationLecture(Request $r, $class_id){
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        $subjects = DB::table('subjects')->get();

        $charge = [];
        $days = [
            1 => 'sun',
            2 => 'mon',
            3 => 'tue',
            4 => 'wed',
            5 => 'thu',
            6 => 'fri',
            7 => 'sat'
        ];

        foreach($subjects as $subject){
            $charge[$subject->id] =  $r->post($subject->subject_name);
        }

        $dt = Carbon::now('Asia/Tokyo');
        $year = $dt->year;
        
        DB::beginTransaction();

        try{
            foreach($charge as $subject_id => $teacher_id){
                if($teacher_id == 0){
                    foreach($days as $d){
                        for($i = 1;$i <= 6; $i++){
                            $sql = "update `${d}` "
                            ."set `${i}` = case "
                            ."when `${i}` = ${teacher_id} then 0 "
                            ."else ${teacher_id} "
                            ."end where class_id = ${class_id} ;";
                            DB::update($sql);
                        }
                    }
                }

                $hash_key = hash('sha256', $class_id.$year.$subject_id);
                $sql = "INSERT INTO `lectures` (`teacher_id`, `subject_id`, `class_id`, `year`, `hash`, `created_at`, `updated_at`) "
                ."VALUES (${teacher_id}, ${subject_id}, ${class_id}, ${year}, '${hash_key}', null, null) "
                ."ON DUPLICATE KEY UPDATE "
                ."`teacher_id` = ${teacher_id} ;";
                DB::insert($sql);
            }
        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }

        DB::commit();
        
        // $classes = DB::table('classes')->get()
        // ->where('homeroom_teacher_id',$id);
        
        // return view('register_lecture')->with([
        //     'classes' => $classes,
        //     'class_id' => $class_id
        // ]);
    }

}
