<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'review_text',
        'reviewer_id',
        'reviewee_id',
        'assessment_id',
        'rating',
        'score',
        
    ];

    /**
     * The assessment this review belongs to.
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * The user who made this review.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * The user being reviewed.
     */
    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
    public function teacher()
{
    return $this->reviewee();
}


}
