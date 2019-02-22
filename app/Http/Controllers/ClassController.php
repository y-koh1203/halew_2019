<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function index(Request $r){
        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        $classes = DB::table('classes')->get()->where('homeroom_teacher_id', '=', $id);

        return view('class')->with([
            'classes' => $classes
        ]);
    }

    //クラスに所属している生徒を一覧で取得(Ajax検討中)
    public function getBelongStudentInClass(Request $r, $class_id){
        //クラスに所属する生徒一覧を取得する

        $id = $r->session()->get('id');

        if($id === null){
            return view('admin');
        }

        $classes = DB::table('classes')->get()->where('homeroom_teacher_id', '=', $id);
        $students = DB::table('students')->get()->where('class_id','=',$class_id);

        return view('class')->with([
            'class_id' => $class_id,
            'classes' => $classes,
            'students' => $students
        ]);
    }
}
