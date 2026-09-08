<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Institute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users');
        if ($users->count() == 0) {
            User::create(array('firstname' => 'Mr.', 'lastname' => 'Admin', 'login' => 'admin', 'email' => 'admin@school.dev', 'group' => 'Admin', 'desc' => 'Admin Details Here', "password" => Hash::make("123456")));
            User::create(array('firstname' => 'Mr.', 'lastname' => 'Other', 'login' => 'other', 'email' => 'other@school.dev', 'group' => 'Other', 'desc' => 'other Deatils Here', "password" => Hash::make("123456")));
            User::create(array('firstname' => 'Mr.', 'lastname' => 'kashif', 'login' => 'ictkashif', 'email' => 'kashif@ictinnovations.com', 'group' => 'Admin', 'desc' => 'admin Deatils Here', "password" => Hash::make("123456")));
        }


        $institute  = Institute::select('*');
        if ($institute->count() == 0) {
            Institute::create(array('name' => 'Ict Vision', 'establish' => '2017', 'email' => 'info@ictinnovations.com', 'web' => 'http://ictvision.net/', 'phoneNo' => '923125555555', 'address' => 'Khawar center Multan'));
        }

        $student_path = 'sql/student.sql';
        DB::unprepared(file_get_contents($student_path));
        $this->command->info('Student table seeded!');

        $class_path = 'sql/class.sql';
        DB::unprepared(file_get_contents($class_path));
        $this->command->info('class table seeded!');

        $section_path = 'sql/section.sql';
        DB::unprepared(file_get_contents($section_path));
        $this->command->info('section table seeded!');

        $subjects_path = 'sql/subjects.sql';
        DB::unprepared(file_get_contents($subjects_path));
        $this->command->info('subject table seeded!');

        $marks_path = 'sql/marks.sql';
        DB::unprepared(file_get_contents($marks_path));
        $this->command->info('marks table seeded!');

        $grade_path = 'sql/grade.sql';
        DB::unprepared(file_get_contents($grade_path));
        $this->command->info('grade table seeded!');

        $teacher_path = 'sql/teacher.sql';
        DB::unprepared(file_get_contents($teacher_path));
        $this->command->info('teacher table seeded!');
    }
}
