<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    // Show details of a specific assessment for the student or teacher
    public function show($id)
    {
        $assessment = Assessment::findOrFail($id);

        if (auth()->user()->role == 'teacher') {
            // Show teacher view of the assessment
            return redirect()->route('assessment.teacher_view', $id);
        } else {
            // Show student view of the assessment
            return view('assessments.student_view', compact('assessment'));
        }
    }

    // Display the assessment view for the teacher with students and reviews
    public function teacherView($id)
    {
        $assessment = Assessment::with(['course', 'reviews'])->findOrFail($id);

        // Get all students enrolled in the course with pagination
        $students = $assessment->course->students()->paginate(10);

        // Retrieve each student's submitted and received reviews count
        foreach ($students as $student) {
            $student->submitted_reviews_count = $assessment->reviews()->where('reviewer_id', $student->id)->count();
            $student->received_reviews_count = $assessment->reviews()->where('reviewee_id', $student->id)->count();
            $student->score = $assessment->reviews()->where('reviewee_id', $student->id)->value('score');
        }

        return view('assessments.teacher_view', compact('assessment', 'students'));
    }

    // Add an assessment to a course
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:20',
            'instruction' => 'required|string',
            'num_reviews' => 'required|integer|min:1',
            'max_score' => 'required|integer|min:1|max:100',
            'due_date' => 'required|date',
            'type' => 'required|in:student-select,teacher-assign',
            'course_id' => 'required|exists:courses,id',
        ]);

        Assessment::create([
            'title' => $request->title,
            'instruction' => $request->instruction,
            'num_reviews' => $request->num_reviews,
            'max_score' => $request->max_score,
            'due_date' => $request->due_date,
            'type' => $request->type,
            'course_id' => $request->course_id,
        ]);

        return redirect()->route('courses.show', $request->course_id)->with('success', 'Assessment created successfully.');
    }

    // Update an assessment
    public function update(Request $request, $id)
    {
        $assessment = Assessment::findOrFail($id);

        // Check if the assessment has no submissions before allowing update
        if ($assessment->reviews->isEmpty()) {
            $request->validate([
                'title' => 'required|string|max:20',
                'instruction' => 'required|string',
                'num_reviews' => 'required|integer|min:1',
                'max_score' => 'required|integer|min:1|max:100',
                'due_date' => 'required|date',
                'type' => 'required|in:student-select,teacher-assign',
            ]);

            $assessment->update($request->all());
            return redirect()->route('courses.show', $assessment->course_id)->with('success', 'Assessment updated successfully.');
        }

        return redirect()->back()->withErrors(['error' => 'Assessment cannot be updated after submissions.']);
    }
}
