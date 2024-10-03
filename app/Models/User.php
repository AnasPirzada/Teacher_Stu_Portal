<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        's_number',
        'password',
        'role', // either 'student' or 'teacher'
    ];

    /**
     * Hidden attributes for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The courses this teacher teaches.
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    /**
     * The assessments that this user has reviewed.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * The courses the student is enrolled in.
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id');
    }
    public function submittedReviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id'); // Assuming reviewer_id references the Student
    }

    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id'); // Assuming reviewee_id references the Student
    }
}
