@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Reviews for {{ $student->name }}</h1>
    <h2>Submitted Reviews</h2>
    <ul>
        @foreach($submittedReviews as $review)
            <li>
                Reviewed: {{ $review->reviewee->name }} - {{ $review->review_text }}
            </li>
        @endforeach
    </ul>

    <h2>Received Reviews</h2>
    <ul>
        @foreach($receivedReviews as $review)
            <li>
                Reviewer: {{ $review->reviewer->name }} - {{ $review->review_text }}
            </li>
        @endforeach
    </ul>
</div>
@endsection
