<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Assessment;
use App\Models\User;
use Illuminate\Support\Str;
class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assessments = Assessment::all();

        foreach ($assessments as $assessment) {
            $students = $assessment->course->students;

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
