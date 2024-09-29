<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assessment;  // Import Assessment model
use App\Models\Review;      // Import Review model
use App\Models\User;        // Import User model
use Illuminate\Support\Str; // Import Laravel's Str helper

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assessments = Assessment::all(); // Retrieve all assessments

        foreach ($assessments as $assessment) {
            $students = $assessment->course->students; // Get students enrolled in the course

            foreach ($students as $student) {
                // Select 3 random peers for each student to review
                $peers = $students->where('id', '!=', $student->id)->random(3);

                foreach ($peers as $peer) {
                    Review::create([
                        'review_text' => "Peer review by {$student->name}: " . Str::random(20),
                        'reviewer_id' => $student->id,
                        'reviewee_id' => $peer->id,
                        'assessment_id' => $assessment->id,
                    ]);
                }
            }
        }
    }
}
