<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        mb_internal_encoding("UTF-8");

        $subjects = [
            "国語", "数学", "理科", "社会", "音楽", "英語"
        ];
      
        foreach($subjects as $subject){
            DB::table('subjects')->insert([
                "subject_name" => $subject
            ]);
        }

        $classes = ['1A', '1B', '2A', '2B', '3A', '3B'];

        foreach($classes as $class){
            DB::table('classes')->insert([
                'class_name' => $class,
                'homeroom_teacher_id' => '1',
                'year' => substr($class, 0, 1)
            ]);
        }

        //パスワードは誕生日 +  出席番号

        $dt = Carbon::now();
        foreach($classes as $key => $class){
            for($i = 1; $i <= 10; $i++){
                $cid = $key + 1;
                $date = explode(' ',$dt);
                $date = explode('-',$date[0]);
                $str_date = '';
                foreach($date as $k => $v){
                    if($k > 2) continue;
                    $str_date .= $v;
                }

                echo $cid;

                DB::table('students')->insert([
                    'name' => $i,
                    'birthday' => $dt,
                    'password' => hash('sha256', $str_date.$cid.$i),
                    'class_id' => $cid,
                    'number' => $i,
                    'status' => '1',
                    'year' => substr($class, 0, 1)
                ]);
            } 
        }
    }
}
