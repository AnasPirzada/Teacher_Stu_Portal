<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Assessment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class CourseController extends Controller
{
    // Display the home page with courses
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'teacher') {
            // Show courses the teacher is teaching
            $courses = $user->courses;
        } else {
            // Show courses the student is enrolled in
            $courses = $user->enrolledCourses;
        }

        return view('home', compact('courses', 'user'));
    }

    // Show course details
    public function show($id)
    {
        $course = Course::with(['teacher', 'assessments'])->findOrFail($id);
        return view('course.details', compact('course'));
    }

    // Show course details with teacher/student logic
    public function showDetails($id)
    {
        $course = Course::with('students', 'assessments', 'teacher')->findOrFail($id);

        // Get the authenticated user
        $user = Auth::user();
        $isTeacher = $user->role === 'teacher';

        return view('course.details', compact('course', 'user', 'isTeacher'));
    }

    // New details method if needed
    public function details($id)
    {
        return $this->showDetails($id); // You can redirect this to the existing showDetails method
    }

    // Method for creating a new course
    public function create()
    {
        return view('courses.create');
    }

    // Store a new course
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'course_code' => 'required|string|max:10',
            'description' => 'nullable|string',
        ]);

        $course = new Course();
        $course->name = $request->name;
        $course->course_code = $request->course_code;
        $course->description = $request->description;
        $course->teacher_id = Auth::id(); // Assuming the teacher is creating the course
        $course->save();

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }

    // Show edit form for the course
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        return view('courses.edit', compact('course'));
    }

    // Update the course
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'course_code' => 'required|string|max:10',
            'description' => 'nullable|string',
        ]);

        $course = Course::findOrFail($id);
        $course->name = $request->name;
        $course->course_code = $request->course_code;
        $course->description = $request->description;
        $course->save();

        return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
    }

    // Delete the course
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }
// course upload file

public function uploadCourseFile(Request $request)
{
    $request->validate([
        'course_file' => 'required|file|mimes:json',
    ]);

    try {
        // Parse the JSON file content
        $fileContent = file_get_contents($request->file('course_file'));
        $data = json_decode($fileContent, true);

        // Validate JSON structure
        if (!isset($data['course'], $data['users'], $data['assessments'])) {
            throw new \Exception('Invalid JSON structure. Ensure "course", "users", and "assessments" fields are present.');
        }

        // Extract course data
        $courseData = $data['course'];

        // Check if the course already exists
        $existingCourse = Course::where('course_code', $courseData['course_code'])->first();
        if ($existingCourse) {
            throw new \Exception('Course with the same course code already exists.');
        }

        // Create a new course
        $course = Course::create([
            'name' => $courseData['name'],
            'course_code' => $courseData['course_code'],
            'teacher_id' => $courseData['teacher_id'],
        ]);

        // Process the users array (students and teachers)
        foreach ($data['users'] as $userData) {
            $user = Auth::user();

            $user = User::where('s_number', $userData['s_number'])->first();

            // If the user doesn't exist, create them
            if (!$user) {
                $user = Auth::user();

                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => $userData['password'],  // Hashed password should be provided
                    's_number' => $userData['s_number'],
                    'role' => $userData['role'],
                ]);
            }

            // Attach the user to the course as a student or teacher
            if ($user->role === 'teacher') {
                $course->teachers()->attach($user->id);
            } else {
                $course->students()->attach($user->id);
            }
        }

        // Create the assessments
        foreach ($data['assessments'] as $assessmentData) {
            Assessment::create([
                'course_id' => $course->id,
                'title' => $assessmentData['title'],
                'instruction' => $assessmentData['instruction'],
                'num_reviews' => $assessmentData['num_reviews'],
                'max_score' => $assessmentData['max_score'],
                'due_date' => $assessmentData['due_date'],
                'type' => $assessmentData['type'],
            ]);
        }

        // Success, redirect back with a success message
        return redirect()->back()->with('success', 'Course, students, teachers, and assessments created successfully.');

    } catch (\Exception $e) {
        // Catch and handle any exceptions, return with an error message
        return redirect()->back()->with('error', $e->getMessage());
    }
}





}
