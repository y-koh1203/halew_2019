<?php

use Illuminate\Database\Seeder;

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
    }
}
