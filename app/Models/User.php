<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Group;

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

    /**
     * Reviews submitted by this user (student).
     */
    public function submittedReviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Reviews received by this user (student).
     */
    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    /**
     * The groups the student belongs to.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_student', 'student_id', 'group_id');
    }

    /**
     * Helper method to get the group of the student for a specific assessment.
     */
    public function groupForAssessment($assessmentId)
    {
        return $this->groups()->where('assessment_id', $assessmentId)->first();
    }
}
