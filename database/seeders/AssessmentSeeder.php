<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Assessment;
use App\Models\Course;
class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assessments = [
            ['title' => 'Build a Responsive Website', 'instruction' => 'Create a multi-page responsive website using HTML, CSS, and JavaScript.'],
            ['title' => 'Python Data Analysis', 'instruction' => 'Analyze a dataset using Python libraries such as Pandas and Matplotlib.'],
            ['title' => 'SQL Database Design', 'instruction' => 'Design and implement a relational database using SQL.'],
            ['title' => 'Machine Learning Model', 'instruction' => 'Develop a simple machine learning model using scikit-learn and evaluate its performance.'],
            ['title' => 'Implementing Sorting Algorithms', 'instruction' => 'Implement and compare different sorting algorithms in a programming language of your choice.'],
        ];

        $courses = Course::all();
        
        foreach ($courses as $index => $course) {
            Assessment::create([
                'title' => $assessments[$index]['title'],
                'instruction' => $assessments[$index]['instruction'],
                'num_reviews' => 3,
                'max_score' => 100,
                'due_date' => '2024-12-31',
                'type' => 'student-select',
                'course_id' => $course->id,
            ]);
        }
    }
}
