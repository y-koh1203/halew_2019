<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SpotTimetableController extends Controller
{
    public function index(Request $r){
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        $teacher_name = DB::table('teachers')
        ->where('id', $id)
        ->first();

        $classes = DB::table('classes')->get()
        ->where('homeroom_teacher_id', $id);
        
        return view('spot_timetable')->with([
            'classes' => $classes,
            'tn' => $teacher_name->name
        ]);
    }

    public function  registerSpot(Request $r, $class_id){
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        $teacher_name = DB::table('teachers')
        ->where('id', $id)
        ->first();

        $classes = DB::table('classes')->get()
        ->where('homeroom_teacher_id', $id);

        $lectures = DB::table('lectures')
        ->join('subjects', 'lectures.subject_id', '=' , 'subjects.id')
        ->join('teachers', 'lectures.teacher_id', '=', 'teachers.id')
        ->select('lectures.*', 'subjects.subject_name', 'teachers.name')
        ->get()
        ->where('class_id', $class_id);

        $ad_tt = DB::table('additional_timetable')
        ->get()
        ->where('class_id', $class_id);

        return view('spot_timetable')->with([
            'class_id' => $class_id,
            'classes' => $classes,
            'lectures' => $lectures,
            'registed' => $ad_tt,
            'tn' => $teacher_name->name
        ]);
    }

    public function  registration(Request $r, $class_id){
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        $teacher_name = DB::table('teachers')
        ->where('id', $id)
        ->first();

        $spots = [];

        for($i = 1; $i < 7; $i++){
            $spots[$i] = $r->post('spot-'.$i);
        }

        $dt = Carbon::now('Asia/Tokyo');
        $year = $dt->year;

        $date = $r->post('spot-date');
        $hash_base = $date.$class_id;
        $hash_key = hash('sha256', $hash_base);

        DB::beginTransaction();

        try{ 
            $sql = "INSERT INTO `additional_timetable` (`class_id`, `1`, `2`, `3`, `4`, `5`, `6`, `year`, `date`, `status`, `hash`, `created_at`, `updated_at`) "
            ."VALUES (${class_id}, '${spots[1]}', '${spots[2]}', '${spots[3]}', '${spots[4]}', '${spots[5]}', '${spots[6]}', '${year}', '${date}', '1', '${hash_key}', null, null) "
            ."ON DUPLICATE KEY UPDATE "
            ."`1` = '${spots[1]}', "
            ."`2` = '${spots[2]}', "
            ."`3` = '${spots[3]}', "
            ."`4` = '${spots[4]}', "
            ."`5` = '${spots[5]}', "
            ."`6` = '${spots[6]}' ;";

            DB::insert($sql);
        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }

        DB::commit();

        $classes = DB::table('classes')->get()
        ->where('homeroom_teacher_id', $id);

        $lectures = DB::table('lectures')
        ->join('subjects', 'lectures.subject_id', '=' , 'subjects.id')
        ->join('teachers', 'lectures.teacher_id', '=', 'teachers.id')
        ->select('lectures.*', 'subjects.subject_name', 'teachers.name')
        ->get()
        ->where('class_id', $class_id);

        $ad_tt = DB::table('additional_timetable')
        ->get()
        ->where('class_id', $class_id);

        return view('spot_timetable')->with([
            'class_id' => $class_id,
            'classes' => $classes,
            'lectures' => $lectures,
            'success' => true,
            'registed' => $ad_tt,
            'tn' => $teacher_name->name
        ]);
    }

    public function  delete(Request $r){
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        $teacher_name = DB::table('teachers')
        ->where('id', $id)
        ->first();

        $class_id = $r->post('class_id');
        $del_id = $r->post('del-id');
        DB::table('additional_timetable')->where('id', $del_id)->delete();

        $classes = DB::table('classes')->get()
        ->where('homeroom_teacher_id', $id);

        $lectures = DB::table('lectures')
        ->join('subjects', 'lectures.subject_id', '=' , 'subjects.id')
        ->join('teachers', 'lectures.teacher_id', '=', 'teachers.id')
        ->select('lectures.*', 'subjects.subject_name', 'teachers.name')
        ->get()
        ->where('class_id', $class_id);

        $ad_tt = DB::table('additional_timetable')
        ->get()
        ->where('class_id', $class_id);

        return view('spot_timetable')->with([
            'class_id' => $class_id,
            'classes' => $classes,
            'lectures' => $lectures,
            'registed' => $ad_tt,
            'tn' => $teacher_name->name
        ]);
    }
}
