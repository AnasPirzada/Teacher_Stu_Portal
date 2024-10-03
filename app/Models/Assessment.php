<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'instruction',
        'num_reviews',
        'max_score',
        'due_date',
        'type',
        'course_id',
    ];

    /**
     * The course this assessment belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * The reviews associated with this assessment.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function hasSubmittedReviews()
    {
        return $this->reviews()->whereNotNull('review_text')->exists();
    }
}
