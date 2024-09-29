<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;

// Authentication routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Home route for both students and teachers
Route::get('/home', [CourseController::class, 'index'])->middleware('auth')->name('home');

// Course Details
Route::get('/course/{id}', [CourseController::class, 'show'])->middleware('auth')->name('course.details');

// Assessment Details (For Students)
Route::get('/assessment/{id}', [AssessmentController::class, 'show'])->middleware('auth')->name('assessment.details');







// Assessment Details (For Teachers)
Route::get('/assessment/{id}/teacher-view', [AssessmentController::class, 'teacherView'])
    ->middleware('auth', 'teacher')
    ->name('assessment.teacher_view');

// Submit Peer Review (Student)
Route::post('/assessment/{id}/submit-review', [ReviewController::class, 'submitReview'])->middleware('auth')->name('submit.review');


// View Students form teacher 
Route::get('/assessment/{assessment_id}/student/{student_id}/reviews', [TeacherController::class, 'studentReviews'])->name('teacher.student_reviews');




// Teacher-specific routes
Route::middleware('auth', 'teacher')->group(function () {
    Route::post('/course/{id}/enroll', [TeacherController::class, 'enrollStudent'])->name('teacher.enroll');
    Route::post('/course/{id}/add-assessment', [TeacherController::class, 'addAssessment'])->name('teacher.add.assessment');
    Route::post('/assessment/{id}/update', [TeacherController::class, 'updateAssessment'])->name('teacher.update.assessment');
    Route::get('/assessment/{id}/marking', [TeacherController::class, 'markingPage'])->name('teacher.marking');
    Route::post('/assessment/{id}/mark-student', [TeacherController::class, 'markStudent'])->name('teacher.mark.student');
    Route::post('/course/upload-file', [TeacherController::class, 'uploadCourseFile'])->name('teacher.upload.file');
});

// Review rating feature
Route::post('/review/{id}/rate', [ReviewController::class, 'rateReview'])->middleware('auth')->name('review.rate');
Route::get('/top-reviewers', [ReviewController::class, 'topReviewers'])->middleware('auth')->name('top.reviewers');
