<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use App\Models\Group; // <-- Add this line to import the Group model

class AssessmentController extends Controller
{
    public function details($id)
{
    $assessment = Assessment::findOrFail($id);
    $course = $assessment->course;
    $user = auth()->user();
    $isTeacher = $user->role === 'teacher';

    if ($isTeacher) {
        // Logic for the teacher view
        $students = $course->students()->with(['groups' => function ($query) use ($assessment) {
            $query->where('assessment_id', $assessment->id);
        }])->paginate(10);

        foreach ($students as $student) {
            $student->group = $student->groupForAssessment($assessment->id);
            $student->submittedReviewsCount = $assessment->reviews()->where('reviewer_id', $student->id)->count();
            $student->receivedReviewsCount = $assessment->reviews()->where('reviewee_id', $student->id)->count();
            $student->score = $assessment->reviews()->where('reviewee_id', $student->id)->first()->score ?? null;
        }

        $groups = Group::where('assessment_id', $assessment->id)->with('students')->get();

        return view('assessments.teacher_view', compact('assessment', 'course', 'students', 'groups'));
    } else {
        // Logic for the student view
        $submittedReviews = $assessment->reviews()->where('reviewer_id', $user->id)->get();
        $receivedReviews = $assessment->reviews()->where('reviewee_id', $user->id)->get();

        // Find the group the student belongs to for this assessment, if groups are created
        $userGroup = Group::whereHas('students', function ($query) use ($user) {
            $query->where('student_id', $user->id);
        })->where('assessment_id', $assessment->id)->with('students')->first();
 // Get the exact score assigned by the teacher
 $review = $assessment->reviews()->where('reviewee_id', $user->id)->first();
 $score = $review ? $review->score : null;
        // Get all students in the course in case groups haven't been created
        $students = $course->students;

        return view('assessments.student_view', compact('assessment', 'course', 'submittedReviews', 'receivedReviews', 'userGroup', 'students','score'));
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
            'review_text' => 'required|string|min:5',
            'rating' => 'required|integer|min:1|max:5',
            'reviewee_id' => 'required|exists:users,id',
        ]);

        $existingReview = Review::where('assessment_id', $request->assessment_id)
            ->where('reviewer_id', auth()->id())
            ->where('reviewee_id', $request->reviewee_id)
            ->first();

        if ($existingReview) {
            // If a review exists, update it
            $existingReview->update([
                'review_text' => $request->review_text,
                'rating' => $request->rating,
            ]);
        } else {
            // Create a new review
            Review::create([
                'assessment_id' => $request->assessment_id,
                'reviewer_id' => auth()->id(),
                'reviewee_id' => $request->reviewee_id,
                'review_text' => $request->review_text,
                'rating' => $request->rating,
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
            // 'review_text' => 'Score updated by teacher',
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
        
        public function createGroups($assessmentId)
        {
            try {
                $assessment = Assessment::findOrFail($assessmentId);
                $students = $assessment->course->students()->get();
        
                // Check if there are enough students to form groups
                if ($students->isEmpty()) {
                    return redirect()->route('assessments.teacher_view', ['assessmentId' => $assessmentId])
                        ->with('error', 'No students are enrolled in this course to create groups.');
                }
        
                // Shuffle students for randomness
                $students = $students->shuffle();
        
                // Delete previous groups for this assessment if any exist
                Group::where('assessment_id', $assessment->id)->delete();
        
                // Assign students to groups with a minimum of 3 and a maximum of 5 students per group
                $totalStudents = count($students);
                $groupIndex = 0;
                $groupSize = 5;
        
                while ($students->isNotEmpty()) {
                    $groupName = chr(65 + $groupIndex); // Assign group names A, B, C, etc.
                    $groupSize = min($groupSize, $students->count()); // Adjust group size to not exceed remaining students
        
                    if ($groupSize < 3 && $students->count() > 3) {
                        $groupSize = 3;
                    }
        
                    $group = Group::create([
                        'name' => $groupName,
                        'assessment_id' => $assessment->id,
                    ]);
        
                    $groupStudents = $students->splice(0, $groupSize); // Get the next set of students
                    $group->students()->attach($groupStudents->pluck('id'));
        
                    $groupIndex++;
                }
        
                return redirect()->route('assessments.teacher_view', ['assessmentId' => $assessmentId])
                    ->with('success', 'Groups created successfully.');
        
            } catch (\Exception $e) {
                // Log the error for debugging
                \Log::error('Error creating groups: ' . $e->getMessage());
        
                return redirect()->route('assessments.teacher_view', ['assessmentId' => $assessmentId])
                    ->with('error', 'An error occurred while creating groups: ' . $e->getMessage());
            }
        }
        



        



}
