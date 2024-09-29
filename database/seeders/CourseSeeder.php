<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;
class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $courses = [
                ['name' => 'Web Development', 'course_code' => 'WEB101'],
                ['name' => 'Python Programming', 'course_code' => 'PYTHON102'],
                ['name' => 'Database Management', 'course_code' => 'DB103'],
                ['name' => 'Introduction to Machine Learning', 'course_code' => 'ML104'],
                ['name' => 'Data Structures and Algorithms', 'course_code' => 'DSA105'],
            ];
    
            foreach ($courses as $courseData) {
                Course::create([
                    'name' => $courseData['name'],
                    'course_code' => $courseData['course_code'],
                    'teacher_id' => User::where('role', 'teacher')->inRandomOrder()->first()->id,
                ]);
            }
    
    }
}
