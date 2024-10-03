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
        $students = $course->students;  // Pass all students to the view

        if ($isTeacher) {
            // Logic for the teacher
            $students = $course->students()->paginate(10);
        
            // Call the method to assign students to groups
            $groups = $this->assignStudentsToGroups($assessment);
            
            // Attach the group info to students
            foreach ($students as $student) {
                // Assuming you have a method to check group assignments
                $student->group_assigned = $this->getAssignedGroup($student, $groups); // Create this method
                $student->submittedReviews = $assessment->reviews()->where('reviewer_id', $student->id)->count();
                $student->receivedReviews = $assessment->reviews()->where('reviewee_id', $student->id)->count();
                $student->score = $assessment->reviews()->where('reviewee_id', $student->id)->avg('score');
            }
    
            return view('assessments.teacher_view', compact('assessment', 'course', 'students', 'isTeacher', 'groups'));
        } else {
            // Logic for the student
            $submittedReviews = $assessment->reviews()->where('reviewer_id', $user->id)->get();
            $receivedReviews = $assessment->reviews()->where('reviewee_id', $user->id)->get();
            $students = $course->students;
    
            return view('assessments.student_view', compact('assessment', 'course', 'submittedReviews', 'receivedReviews', 'isTeacher', 'students'));
        }
    }
    
    

    public function store(Request $request, $courseId)
    {
        $request->validate([
            'title' => 'required|string|max:20',
            'instruction' => 'required|string',
            'num_reviews' => 'required|integer|min:1',
            'max_score' => 'required|integer|min:1|max:100',
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
            'course_id' => $courseId,
        ]);
    
        return redirect()->back()->with('success', 'Peer Review Assessment added successfully');
    }

    public function storeReview(Request $request)
{
    $request->validate([
        'assessment_id' => 'required|exists:assessments,id',
        // 'review_text' => 'required|string|min:5',
        'rating' => 'required|integer|min:1|max:5',  // Rating validation
    ]);

    $existingReview = Review::where('assessment_id', $request->assessment_id)
        ->where('reviewer_id', auth()->id()) // The student who is reviewing
        ->where('reviewee_id', $request->reviewee_id) // The reviewee is another student or self
        ->first();

    if ($existingReview) {
        // If a review exists, update only rating and review text, not score
        $existingReview->update([
            'review_text' => $request->review_text,
            'rating' => $request->rating, // Update rating
        ]);
    } else {
        // Create a new review
        Review::create([
            'assessment_id' => $request->assessment_id,
            'reviewer_id' => auth()->id(),  // Reviewer is the logged-in student
            'reviewee_id' => $request->reviewee_id,  // Reviewee is the selected student
            'review_text' => $request->review_text,
            'rating' => $request->rating, // Store the rating
            'score' => null,  // Do not set a score
        ]);
    }

    return redirect()->back()->with('success', 'Review submitted successfully');
}

    

public function markStudent(Request $request, $id)
{
    $request->validate([
        'student_id' => 'required|exists:users,id',
        'score' => 'required|integer|min:0|max:100',
    ]);

    $existingReview = Review::where('assessment_id', $id)
        ->where('reviewee_id', $request->student_id)
        ->first();

    if ($existingReview) {
        // Update only the score, not the rating
        $existingReview->update([
            'score' => $request->score,
            'review_text' => 'Score updated by teacher',
        ]);
    } else {
        // If no review exists, create one with the score only
        Review::create([
            'assessment_id' => $id,
            'reviewer_id' => auth()->id(),  // Teacher ID
            'reviewee_id' => $request->student_id,
            // 'review_text' => 'Score assigned by teacher',
            'score' => $request->score,
            'rating' => null,  // Rating remains unchanged
        ]);
    }

    return redirect()->back()->with('success', 'Score updated successfully.');
}

    
    public function edit($id)
        {
            $assessment = Assessment::findOrFail($id);
            $course = $assessment->course; // Get the related course

            // Check if there are any submissions for this assessment
            if ($assessment->reviews()->exists()) {
                return redirect()->back()->with('error', 'You cannot edit the assessment after submissions have been made.');
            }

            return view('assessments.edit', compact('assessment', 'course'));
        }

    public function update(Request $request, $id)
        {
            try {
                // Find the assessment by ID
                $assessment = Assessment::findOrFail($id);

                // Validate the request data (exclude course_name and course_id from validation)
                $validated = $request->validate([
                    'title' => 'required|string|max:255',
                    'instruction' => 'required|string',
                    'num_reviews' => 'required|integer|min:1',
                    'max_score' => 'required|integer|min:1|max:100',
                    'due_date' => 'required|date',
                    'type' => 'required|in:student-select,teacher-assign',
                ]);

                // Update the assessment with validated data
                $assessment->update($validated);

                // If successful, redirect with success message
                return redirect()->back()->with('success', 'Assessment updated successfully.');

            } catch (\Illuminate\Validation\ValidationException $e) {
                // Catch validation errors
                return redirect()->back()->withErrors($e->validator)->withInput();
            } catch (\Exception $e) {
                // Catch all other errors and log them
                \Log::error($e->getMessage());
                return redirect()->back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
            }
        }

        public function getAssignedGroup($student, $groups)
        {
            // Determine the group to which the student belongs
            foreach ($groups as $group) {
                if (in_array($student->id, array_column($group, 'id'))) {
                    return $group; // Return the group if found
                }
            }
            return null; // No group found
        }

        public function assignStudentsToGroups($assessment)
        {
            // Get the enrolled students
            $students = $assessment->course->students()->get();
            
            // Randomly assign students to review groups
            $groups = [];
            foreach ($students as $student) {
                // Create groups (for example, 5 students per group)
                $groupIndex = floor(count($groups) / 5);
                if (!isset($groups[$groupIndex])) {
                    $groups[$groupIndex] = [];
                }
                $groups[$groupIndex][] = $student;
            }
        
            // Save group assignments to the database (create a new model if needed)
            // You may need to create a new Group model to handle this association
            // Example: Group::create(['assessment_id' => $assessment->id, 'student_ids' => json_encode($groups)]);
            
            return $groups; // Return the group structure for further use
        }


        public function showStudentReviews($assessmentId)
        {
            // Fetch the assessment using the provided ID
            $assessment = Assessment::with('course.teacher')->findOrFail($assessmentId);
            $user = auth()->user();
            $isTeacher = $user->role === 'teacher';
        
            // Get submitted reviews and received reviews based on user role
            if (!$isTeacher) {
                $submittedReviews = Review::where('reviewer_id', $user->id)
                    ->where('assessment_id', $assessmentId)
                    ->with('reviewee')
                    ->get();
        
                $receivedReviews = Review::where('reviewee_id', $user->id)
                    ->where('assessment_id', $assessmentId)
                    ->with('reviewer')
                    ->get();
        
                return view('assessments.student_view', compact('assessment', 'submittedReviews', 'receivedReviews'));
            }
        
            $receivedReviews = Review::where('reviewee_id', $user->id)
                ->where('assessment_id', $assessmentId)
                ->with('reviewer')
                ->get();
        
            // Pass the teacher's details to the view (if needed)
            $teacherDetails = $assessment->course->teacher; // Assuming this relationship exists
        
            return view('assessments.teacher_view', compact('assessment', 'receivedReviews', 'teacherDetails'));
        }
        
        


}
