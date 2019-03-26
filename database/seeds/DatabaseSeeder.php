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
            "国語", "数学", "理科", "社会", "音楽", "英語", "体育"
        ];
      
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
            DB::table('subjects')->insert([
                "subject_name" => $subject
            ]);
        }

        $classes = ['1A', '1B', '1C', '2A', '2B', '2C', '3A', '3B', '3C'];

        for($i = 1;$i < 10; $i++){
            DB::table('classes')->insert([
                'class_name' => $classes[$i - 1],
                'homeroom_teacher_id' => rand(1,9),
                'year' => substr($classes[$i -1], 0, 1)
            ]);

            
            foreach($days as $day){
                DB::table($day)->insert([
                    'class_id' => $i,
                    '1' => 0,
                    '2' => 0,
                    '3' => 0,
                    '4' => 0,
                    '5' => 0,
                    '6' => 0,
                    'year' => '2019',
                    'year_from' => '2018-04-01',
                    'year_to' => '2019-03-31',
                    'status' => '1',
                    'hash' => hash('sha256', $i.'2019')
                ]);
            }
        }

        DB::table('teachers')->insert([
            'name' => '大塚',
            'password' => hash('sha256','test'),
            'login_id' => 'ootsuka',
            'charge_1' => 1,
            'charge_2' => null,
            'status' => '1'
        ]);

        DB::table('teachers')->insert([
            'name' => '山下',
            'password' => hash('sha256','test'),
            'login_id' => 'yamashita',
            'charge_1' => 3,
            'charge_2' => 6,
            'status' => '1'
        ]);

        DB::table('teachers')->insert([
            'name' => '横井',
            'password' => hash('sha256','test'),
            'login_id' => 'yokoi',
            'charge_1' => 2,
            'charge_2' => 6,
            'status' => '1'
        ]);

        DB::table('teachers')->insert([
            'name' => '長野',
            'password' => hash('sha256','test'),
            'login_id' => 'nagano',
            'charge_1' => 6,
            'charge_2' => 4,
            'status' => '1'
        ]);

        DB::table('teachers')->insert([
            'name' => '飯塚',
            'password' => hash('sha256','test'),
            'login_id' => 'iizuka',
            'charge_1' => 7,
            'charge_2' => null,
            'status' => '1'
        ]);

        DB::table('teachers')->insert([
            'name' => '渡辺',
            'password' => hash('sha256','test'),
            'login_id' => 'watanabe',
            'charge_1' => 5,
            'charge_2' => null,
            'status' => '1'
        ]);

        DB::table('teachers')->insert([
            'name' => '原田',
            'password' => hash('sha256','test'),
            'login_id' => 'harada',
            'charge_1' => 1,
            'charge_2' => 4,
            'status' => '1'
        ]);

        DB::table('teachers')->insert([
            'name' => '坂口',
            'password' => hash('sha256','test'),
            'login_id' => 'sakaguchi',
            'charge_1' => 7,
            'charge_2' => 5,
            'status' => '1'
        ]);

        DB::table('teachers')->insert([
            'name' => '今井',
            'password' => hash('sha256','test'),
            'login_id' => 'imai',
            'charge_1' => 2,
            'charge_2' => 3,
            'status' => '1'
        ]);

        DB::table('teachers')->insert([
            'name' => '進藤',
            'password' => hash('sha256','test'),
            'login_id' => 'shindoh',
            'charge_1' => 7,
            'charge_2' => null,
            'status' => '1'
        ]);

        DB::table('teachers')->insert([
            'name' => '高橋',
            'password' => hash('sha256','test'),
            'login_id' => 'takahashi',
            'charge_1' => 4,
            'charge_2' => null,
            'status' => '1'
        ]);

        for($i = 1; $i <= 11; $i++){
            foreach($days as $day){
                DB::table('teacher_tt_'.$day)->insert([
                    'teacher_id' => $i,
                    '1' => 0,
                    '2' => 0,
                    '3' => 0,
                    '4' => 0,
                    '5' => 0,
                    '6' => 0,
                    'year' => '2019',
                    'year_from' => '2018-04-01',
                    'year_to' => '2019-03-31',
                    'status' => '1',
                    'hash' => hash('sha256', $i.'2019')
                ]);
            }
        }


        //パスワードは誕生日 +  出席番号

        $dt = Carbon::now();
        foreach($classes as $key => $class){

            $year = $dt->year;
            $cid = $key + 1;

            foreach($subjects as $sid => $subject){
                $hash = hash('sha256', ($key+1).$year.($sid+1));
                DB::table('lectures')->insert([
                    'teacher_id' => '0',
                    'subject_id' => $sid+1,
                    'class_id' => $key+1,
                    'day' => null,
                    'time' => null,
                    'year' => $year,
                    'hash' => $hash
                ]);
            }

            DB::table('students')->insert([
                'name' => '山本亘紀',
                'login_id' => 'koki_yamamoto'.$cid,
                'birthday' => $dt,
                'password' => 'test',
                'class_id' => $cid,
                'number' => 1,
                'status' => '1'
            ]);

            DB::table('students')->insert([
                'name' => '飯田和樹',
                'login_id' => 'kazuki_iida'.$cid,
                'birthday' => $dt,
                'password' => 'test',
                'class_id' => $cid,
                'number' => 2,
                'status' => '1'
            ]);

            DB::table('students')->insert([
                'name' => '高橋友和',
                'login_id' => 'tomokazu_takahasi'.$cid,
                'birthday' => $dt,
                'password' => 'test',
                'class_id' => $cid,
                'number' => 3,
                'status' => '1'
            ]);

            DB::table('students')->insert([
                'name' => '和田啓介',
                'login_id' => 'keisuke_wada'.$cid,
                'birthday' => $dt,
                'password' => 'test',
                'class_id' => $cid,
                'number' => 4,
                'status' => '1'
            ]);

            DB::table('students')->insert([
                'name' => '伊藤真司',
                'login_id' => 'shinji_itoh'.$cid,
                'birthday' => $dt,
                'password' => 'test',
                'class_id' => $cid,
                'number' => 5,
                'status' => '1'
            ]);

            DB::table('students')->insert([
                'name' => 'test'.$cid,
                'login_id' => 'test'.$cid,
                'birthday' => $dt,
                'password' => 'test',
                'class_id' => $cid,
                'number' => 6,
                'status' => '1'
            ]);
        }
    }
}
