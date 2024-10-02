@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-3xl font-bold">{{ $assessment->title }}</h1>
    <p class="mt-4">{{ $assessment->instruction }}</p>
    <p><strong>Due Date:</strong> {{ $assessment->due_date }}</p>
    <p><strong>Max Score:</strong> {{ $assessment->max_score }}</p>
    <p><strong>Number of Reviews:</strong> {{ $assessment->num_reviews }}</p>

    @if ($isTeacher)
        <form method="POST" action="{{ route('teacher.submit.review', $assessment->id) }}">
            @csrf
            <h3 class="mt-4">Submit Your Review</h3>
            <textarea name="review" placeholder="Write your review here..." class="form-control" required></textarea>
            <button type="submit" class="btn btn-primary mt-2">Submit Review</button>
        </form>
    @else
        <h3 class="mt-4">Your Reviews:</h3>
        <ul class="list-unstyled">
            @foreach ($assessment->reviews as $review)
                <li>{{ $review->content }} - Reviewed by {{ $review->student->name }}</li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
