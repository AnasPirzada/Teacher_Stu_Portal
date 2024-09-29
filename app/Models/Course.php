<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'course_code', 'teacher_id'];

    // Relationship with the teacher (singular)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Relationship with assessments
    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    // Relationship with enrolled students
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id');
    }
}
