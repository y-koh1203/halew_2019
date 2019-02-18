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

        foreach($subjects as $subject){
            $charge[$subject->id] =  $r->post($subject->subject_name);
        }

        $dt = Carbon::now('Asia/Tokyo');
        $year = $dt->year;
        
        DB::beginTransaction();

        try{
            foreach($charge as $subject_id => $teacher_id){
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
