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
        $request->validate([
            'review_text' => 'required|string|min:5',
            'reviewee_id' => 'required|exists:users,id',
            'assessment_id' => 'required|exists:assessments,id',
        ]);

        $assessment = Assessment::find($request->assessment_id);

        // Check if the user already submitted the required number of reviews
        if (Review::where('reviewer_id', auth()->id())->where('assessment_id', $assessment->id)->count() >= $assessment->num_reviews) {
            return redirect()->back()->withErrors(['error' => 'You have already submitted the required number of reviews.']);
        }

        // Check if the user is submitting a review for a different reviewee
        if (Review::where('reviewer_id', auth()->id())->where('reviewee_id', $request->reviewee_id)->where('assessment_id', $assessment->id)->exists()) {
            return redirect()->back()->withErrors(['error' => 'You have already reviewed this student.']);
        }

        Review::create([
            'review_text' => $request->review_text,
            'reviewer_id' => auth()->id(),
            'reviewee_id' => $request->reviewee_id,
            'assessment_id' => $request->assessment_id,
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully.');
    }
    

}
