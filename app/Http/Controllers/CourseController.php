<?php

namespace App\Http\Controllers;

use App\Models\Course;
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
}
