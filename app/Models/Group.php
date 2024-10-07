<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Import the User model

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'assessment_id'];

    public function students()
    {
        return $this->belongsToMany(User::class, 'group_student', 'group_id', 'student_id');
    }
}
