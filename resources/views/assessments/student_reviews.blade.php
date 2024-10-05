@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Reviews for {{ $student->name }}</h1>

    <!-- Submitted Reviews Section -->
    <h2>Submitted Reviews</h2>
    @if($submittedReviews->isEmpty())
        <p>No reviews submitted by this student yet.</p>
    @else
        <ul>
            @foreach($submittedReviews as $review)
                <li>
                    Reviewed: {{ $review->reviewee->name }} - {{ $review->review_text }} - Rating: {{ $review->rating }}
                </li>
            @endforeach
        </ul>
    @endif

    <!-- Received Reviews Section -->
    <h2>Received Reviews</h2>
    @if($receivedReviews->isEmpty())
        <p>No reviews received yet.</p>
    @else
        <ul>
            @foreach($receivedReviews as $review)
                <li>
                    Reviewer: {{ $assessment->course->teacher->name }} - Score: {{ $review->score }}
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
