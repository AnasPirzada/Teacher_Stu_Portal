<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Assessment;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    // Enroll a student into a course
    public function enrollStudent(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $student = User::where('s_number', $request->input('student_s_number'))->first();

        if (!$student || $student->role !== 'student') {
            return back()->withErrors(['enroll_error' => 'Invalid student S-number']);
        }
            // Check if the student is already enrolled in the course to avoid duplicates
            if ($course->students()->where('student_id', $student->id)->exists()) {
                return back()->withErrors(['enroll_error' => 'Student is already enrolled in this course']);
            }
        $course->students()->attach($student->id);

        return redirect()->route('course.details', $id)->with('success', 'Student enrolled successfully');
    
    }

    // Add a peer review assessment
    public function addAssessment(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:20',
            'instruction' => 'required|string',
            'num_reviews' => 'required|integer|min:1',
            'max_score' => 'required|integer|between:1,100',
            'due_date' => 'required|date',
            'type' => 'required|in:student-select,teacher-assign',
        ]);

        Assessment::create([
            'title' => $request->title,
            'instruction' => $request->instruction,
            'num_reviews' => $request->num_reviews,
            'max_score' => $request->max_score,
            'due_date' => $request->due_date,
            'type' => $request->type,
            'course_id' => $course->id,
        ]);

        return redirect()->route('course.details', $id)->with('success', 'Assessment added successfully');
    }

    // Method to mark student score
    public function markStudent(Request $request, $id)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'score' => 'required|integer|min:0|max:100',
        ]);

        // Find the review for the specific student and assessment
        $review = Review::where('assessment_id', $id)
            ->where('reviewee_id', $request->student_id)
            ->first();

        if ($review) {
            $review->score = $request->score;
            $review->save();
        }

        return redirect()->route('assessment.teacher_view', $id)->with('success', 'Score updated successfully!');
    }

    // View all reviews submitted and received by a student for an assessment
    public function studentReviews($assessment_id, $student_id)
    {
        $assessment = Assessment::findOrFail($assessment_id);
        $student = User::findOrFail($student_id);

        // Fetch reviews submitted and received by the student
        $submittedReviews = $assessment->reviews()->where('reviewer_id', $student_id)->get();
        $receivedReviews = $assessment->reviews()->where('reviewee_id', $student_id)->get();

        return view('assessments.student_reviews', compact('assessment', 'student', 'submittedReviews', 'receivedReviews'));
    }
}
