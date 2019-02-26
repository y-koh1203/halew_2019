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

        $lectures = DB::table('lectures')
        ->join('subjects', 'lectures.subject_id', '=' , 'subjects.id')
        ->join('teachers', 'lectures.teacher_id', '=', 'teachers.id')
        ->select('lectures.*', 'subjects.subject_name', 'teachers.name')
        ->get()
        ->where('class_id', $class_id);
        
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

        //登録ごのLectureを変更した場合に空白になる問題を修正

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
                /*
                    教員選択で、登録済みの教員から未選択に変更された場合に
                    授業タイムテーブルの教員選択をリセットする
                    0ではなかったら、更新する

                    下の処理だと、0の時だけリセットが走るので
                    0以外が選択せらていた時に、正常に更新されない

                    リセットかける前に、Lectureを取得しておいて
                    先に設定されていた教員を判別しなければならない
                */
                $check_lectures = DB::table('lectures')
                ->get()
                ->where('class_id',$class_id);

                if(count($check_lectures, COUNT_RECURSIVE) >= 6){

                    $lecture_check = null;

                    foreach($check_lectures as $cl){
                        if($cl->subject_id == $subject_id){
                            $lecture_check = $cl;
                        }
                    }
    
                    //設定が変更された教員の時間割を書き換え
                    foreach($days as $d){
                        for($i = 1;$i <= 6; $i++){
                            $sql = "update `teacher_tt_${d}` "
                            ."set `${i}` = case "
                            ."when `${i}` = ".$lecture_check->id." then 0 "
                            ."else 0 "
                            ."end where teacher_id =  ".$lecture_check->teacher_id." ;";
    
                            DB::update($sql);
                        }
                    }

                    //設定が変更された教員の時間割を書き換え
                    //教員が重複するリスクあり
                    // foreach($days as $d){
                    //     for($i = 1;$i <= 6; $i++){
                    //         $sql = "update `teacher_tt_${d}` "
                    //         ."set `${i}` = case "
                    //         ."when `${i}` = ".$lecture_check->id." then 0 "
                    //         ."else 0 "
                    //         ."end where teacher_id =  ".$teacher_id." ;";
    
                    //         DB::update($sql);
                    //     }
                    // }
    
                    if($teacher_id == 0){
                        foreach($days as $d){
                            for($i = 1;$i <= 6; $i++){
                                $sql = "update `${d}` "
                                ."set `${i}` = case "
                                ."when `${i}` = ".$lecture_check->id." then ".$lecture_check->id." "
                                ."else 0 "
                                ."end where class_id = ${class_id} ;";
                                
                                //echo nl2br('id:0 + '.$sql);
    
                                DB::update($sql);
                            }
                        }
                    }    
                }

                $hash_key = hash('sha256', $class_id.$year.$subject_id);
                $sql = "INSERT INTO `lectures` (`teacher_id`, `subject_id`, `class_id`, `day`, `time`, `year`, `hash`, `created_at`, `updated_at`) "
                ."VALUES (${teacher_id}, ${subject_id}, ${class_id}, null, null, ${year}, '${hash_key}', null, null) "
                ."ON DUPLICATE KEY UPDATE "
                ."`teacher_id` = ${teacher_id} ;";
                DB::insert($sql);

            }
        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }

        DB::commit();
        
        $classes = DB::table('classes')->get()
        ->where('homeroom_teacher_id',$id);

        $lectures = DB::table('lectures')
        ->join('subjects', 'lectures.subject_id', '=' , 'subjects.id')
        ->join('teachers', 'lectures.teacher_id', '=', 'teachers.id')
        ->select('lectures.*', 'subjects.subject_name', 'teachers.name')
        ->get()
        ->where('class_id', $class_id);

        $subjects = DB::table('subjects')->get();

        foreach($subjects as $subject){
            $sql = 'select * from teachers where charge_1 = '.$subject->id.' or charge_2 = '. $subject->id.' ;';
            $teachers[$subject->id] = DB::select($sql);
        }
        
        return view('register_lecture')->with([
            'classes' => $classes,
            'class_id' => $class_id,
            'lectures' => $lectures,
            'subjects' => $subjects,
            'teachers' => $teachers
        ]);
    }

}
