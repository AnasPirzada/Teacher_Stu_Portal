<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Review;
use App\Models\User;

class AssessmentController extends Controller
{
    public function details($id)
    {
        $assessment = Assessment::findOrFail($id);
        $course = $assessment->course; // Get the associated course
        $user = auth()->user();
        $isTeacher = $user->role === 'teacher';
    
        if ($isTeacher) {
            // Logic for the teacher
            $students = $course->students()->paginate(10); // Paginate 10 students per page
    
            foreach ($students as $student) {
                $student->submittedReviews = $assessment->reviews()->where('reviewer_id', $student->id)->count();
                $student->receivedReviews = $assessment->reviews()->where('reviewee_id', $student->id)->count();
                $student->score = $assessment->reviews()->where('reviewee_id', $student->id)->avg('score'); // Average score
            }
    
            return view('assessments.teacher_view', compact('assessment', 'course', 'students', 'isTeacher'));
        } else {
            // Logic for the student
            $submittedReviews = $assessment->reviews()->where('reviewer_id', $user->id)->get();
            $receivedReviews = $assessment->reviews()->where('reviewee_id', $user->id)->get();
            
            // Get the students enrolled in the course
            $students = $course->students; // Retrieve all students in the course
    
            return view('assessments.student_view', compact('assessment', 'course', 'submittedReviews', 'receivedReviews', 'isTeacher', 'students'));
        }
    }
    

    

    

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'instruction' => 'required|string',
            'due_date' => 'required|date',
            'max_score' => 'required|integer|min:1',
        ]);

        Assessment::create([
            'title' => $request->title,
            'instruction' => $request->instruction,
            'due_date' => $request->due_date,
            'max_score' => $request->max_score,
            'course_id' => $request->course_id,
        ]);

        return redirect()->back()->with('success', 'Assessment created successfully');
    }

    public function storeReview(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
            'review_text' => 'required|string',
            'reviewee_id' => 'required|exists:users,id',
        ]);

        Review::create([
            'assessment_id' => $request->assessment_id,
            'reviewer_id' => auth()->id(),
            'reviewee_id' => $request->reviewee_id,
            'review_text' => $request->review_text,
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully');
    }
    public function markStudent(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'score' => 'required|integer|min:0|max:100',  // Adjust max score dynamically if needed
        ]);
    
        // Retrieve the assessment and student
        $assessment = Assessment::findOrFail($id);
        $studentId = $request->student_id;
    
        // Check if a review already exists for this student and assessment
        $review = Review::where('assessment_id', $id)
                        ->where('reviewee_id', $studentId)
                        ->where('reviewer_id', auth()->id()) // Assuming the teacher is the reviewer
                        ->first();
    
        if ($review) {
            // If a review exists, update the score
            $review->score = $request->score;
            $review->save();
        } else {
            // If no review exists, create a new one
            Review::create([
                'assessment_id' => $assessment->id,
                'reviewer_id' => auth()->id(),  // Assuming the teacher is logged in
                'reviewee_id' => $studentId,
                'review_text' => 'Score assigned by teacher',  // Default review text
                'score' => $request->score,
            ]);
        }
    
        return redirect()->back()->with('success', 'Score saved successfully.');
    }
    
    

}
