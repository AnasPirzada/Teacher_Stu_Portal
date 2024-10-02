<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AssessmentController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes (requires authentication)
Route::middleware('auth')->group(function () {

    // Home route to list courses
    Route::get('/home', [CourseController::class, 'index'])->name('home');

    // Course routes
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{id}/details', [CourseController::class, 'showDetails'])->name('courses.details');
    Route::get('/courses/{id}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{id}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->name('courses.destroy');

    // Assessment routes
    Route::get('/assessments/{id}', [AssessmentController::class, 'details'])->name('assessment.details'); // Details for teachers and students
    Route::post('/assessments/{id}/mark_student', [AssessmentController::class, 'markStudent'])->name('assessment.mark_student'); // Mark a student

    // Peer review submission
    Route::post('/assessments/submit_review', [AssessmentController::class, 'storeReview'])->name('assessment.submit_review'); // Submit peer review

    // Review routes
    Route::post('/review', [ReviewController::class, 'store'])->name('review.store');

    // Teacher-specific routes
    Route::get('/teacher/student_reviews/{assessment_id}/{student_id}', [TeacherController::class, 'studentReviews'])->name('teacher.student_reviews');
    Route::post('/teacher/enroll/{course_id}', [TeacherController::class, 'enroll'])->name('teacher.enroll');
    Route::post('/teacher/add_assessment/{course_id}', [TeacherController::class, 'addAssessment'])->name('teacher.add.assessment');
    Route::post('/teacher/courses/{course}/enroll', [TeacherController::class, 'enrollStudent'])->name('teacher.enroll');
});
