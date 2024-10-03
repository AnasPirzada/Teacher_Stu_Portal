<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Assessment;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Submit a peer review
    public function store(Request $request)
    {
        \Log::info($request->all());
        $request->validate([
            'review_text' => 'required|string|min:5',
            'reviewee_id' => 'required|exists:users,id',
            'assessment_id' => 'required|exists:assessments,id',
            'rating' => 'required|integer|between:1,5', // Validate the rating
            // 'score' => 'required|integer',
        ]);
    
// Check if the student has already submitted the maximum number of reviews
$reviewCount = Review::where('assessment_id', $request->assessment_id)
->where('reviewer_id', auth()->id())
->count();

// Retrieve the assessment
$assessment = Assessment::find($request->assessment_id);

// Ensure the student has not exceeded the required number of reviews
if ($reviewCount >= $assessment->num_reviews) {
return redirect()->back()->with('error', 'You have already submitted the maximum number of reviews for this assessment.');
}

// Check if the reviewer has already reviewed the same reviewee
$existingReview = Review::where('assessment_id', $request->assessment_id)
->where('reviewer_id', auth()->id())
->where('reviewee_id', $request->reviewee_id)
->first();

if ($existingReview) {
return redirect()->back()->with('error', 'You cannot submit multiple reviews for the same reviewee.');
}    
        Review::create([
            'review_text' => $request->review_text,
            'reviewer_id' => auth()->id(),
            'reviewee_id' => $request->reviewee_id,
            'assessment_id' => $request->assessment_id,
            'rating' => $request->rating, // Store the rating
            // 'score' => $request->score,
            
        ]);
    
        return redirect()->back()->with('success', 'Review submitted successfully.');
    }
    
    private function isInAssignedGroup($reviewer, $revieweeId, $assessment)
    {
        // Retrieve the group assignments for this assessment
        $groups = $this->assignStudentsToGroups($assessment);

        foreach ($groups as $group) {
            if (in_array($reviewer->id, array_column($group, 'id')) && in_array($revieweeId, array_column($group, 'id'))) {
                return true; // Both are in the same group
            }
        }

        return false; // Not in the same group
    }
    public function topReviewers()
    {
        $topReviewers = Review::select('reviewer_id', \DB::raw('AVG(rating) as average_rating'))
            ->groupBy('reviewer_id')
            ->orderBy('average_rating', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($review) {
                return [
                    'user' => User::find($review->reviewer_id),
                    'average_rating' => $review->average_rating,
                ];
            });
    
        return view('assessments.top_reviewers', compact('topReviewers'));
    }
    

}
