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
        $course = $assessment->course;
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
            
            // Retrieve only teacher reviews with a score for the logged-in student
            $receivedReviews = $assessment->reviews()
                ->where('reviewee_id', $user->id)
                ->whereNotNull('score') // Ensure only reviews with a score (i.e., from teachers)
                ->with('reviewer') // Load teacher information (reviewer)
                ->get();
                
            // Get the students enrolled in the course
            $students = $course->students;
    
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
        ]);
    
        // Check if the student has already submitted a review for this assessment
        $existingReview = Review::where('assessment_id', $request->assessment_id)
            ->where('reviewer_id', auth()->id()) // The student who is reviewing
            ->where('reviewee_id', auth()->id()) // Self-review
            ->first();
    
        if ($existingReview) {
            // If a review already exists, return with an error message
            return redirect()->back()->with('error', 'You cannot submit more than one review for this assessment.');
        }
    
        // Create a new review
        Review::create([
            'assessment_id' => $request->assessment_id,
            'reviewer_id' => auth()->id(),  // Reviewer is the logged-in student
            'reviewee_id' => auth()->id(),  // Self-review by the student
            'review_text' => $request->review_text,
        ]);
    
        // Redirect with success message
        return redirect()->back()->with('success', 'Review submitted successfully');
    }
    

    public function markStudent(Request $request, $id)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'score' => 'required|integer|min:0|max:100',  // Adjust max score dynamically if needed
        ]);
    
        // Check if a review already exists for this student and assessment
        $existingReview = Review::where('assessment_id', $id)
            ->where('reviewee_id', $request->student_id)
            ->where('reviewer_id', auth()->id()) // Teacher is the reviewer
            ->first();
    
        if ($existingReview) {
            // If a review exists, prevent further modifications
            return redirect()->back()->with('error', 'You cannot modify the score.');
        }
    
        // If no review exists, create a new one
        Review::create([
            'assessment_id' => $id,
            'reviewer_id' => auth()->id(),  // The teacher's ID
            'reviewee_id' => $request->student_id,
            'review_text' => 'Score assigned by teacher',
            'score' => $request->score,
        ]);
    
        // Redirect with success message
        return redirect()->back()->with('success', 'Score submitted successfully.');
    }
    
}
