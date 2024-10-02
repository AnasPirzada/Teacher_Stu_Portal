<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
