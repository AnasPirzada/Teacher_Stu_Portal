<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TeacherController;

// Authentication routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Home route for both students and teachers
Route::get('/home', [CourseController::class, 'index'])->middleware('auth')->name('home');

// Course Details
Route::get('/course/{id}', [CourseController::class, 'show'])->middleware('auth')->name('course.details');
// web.php
Route::get('/course/{id}/details', [CourseController::class, 'showDetails'])->middleware('auth')->name('course.details');

// Assessment-specific routes
Route::get('/assessment/{id}/teacher', [AssessmentController::class, 'teacherView'])->middleware('auth')->name('assessment.teacher_view');

// Assessment Details (For Students)
Route::get('/assessment/{id}', [AssessmentController::class, 'show'])->middleware('auth')->name('assessment.details');

// Teacher-specific routes with inline teacher role check
Route::middleware(['auth'])->group(function () {
    Route::group(['middleware' => function ($request, $next) {
        if (Auth::user()->role === 'teacher') {
            return $next($request);
        }
        return redirect('/home')->withErrors('You are not authorized to access this page.');
    }], function () {
        Route::post('/course/{id}/enroll', [TeacherController::class, 'enrollStudent'])->name('teacher.enroll');
        Route::post('/course/{id}/add-assessment', [TeacherController::class, 'addAssessment'])->name('teacher.add.assessment');
        Route::post('/assessment/{id}/update', [TeacherController::class, 'updateAssessment'])->name('teacher.update.assessment');
        Route::get('/assessment/{id}/marking', [TeacherController::class, 'markingPage'])->name('teacher.marking');
        Route::post('/assessment/{id}/mark-student', [TeacherController::class, 'markStudent'])->name('teacher.mark.student');
        Route::post('/course/upload-file', [TeacherController::class, 'uploadCourseFile'])->name('teacher.upload.file');
    });
});

// Submit Peer Review (Student)
Route::post('/assessment/{id}/submit-review', [ReviewController::class, 'submitReview'])->middleware('auth')->name('submit.review');

// View Students from teacher
Route::get('/assessment/{assessment_id}/student/{student_id}/reviews', [TeacherController::class, 'studentReviews'])->name('teacher.student_reviews');

// Course details page to enroll students
Route::get('/course/{id}/details', [CourseController::class, 'showDetails'])->middleware(['auth'])->name('course.details');

// Review rating feature
Route::post('/review/{id}/rate', [ReviewController::class, 'rateReview'])->middleware('auth')->name('review.rate');
Route::get('/top-reviewers', [ReviewController::class, 'topReviewers'])->middleware('auth')->name('top.reviewers');
