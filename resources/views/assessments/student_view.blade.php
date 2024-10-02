@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 text-center">{{ $assessment->title }}</h1>
        </div>
        <div class="col-12 text-center mb-4">
            <p><strong>Instruction:</strong> {{ $assessment->instruction }}</p>
            <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($assessment->due_date)->format('M d, Y') }}</p>
            <p><strong>Max Score:</strong> {{ $assessment->max_score }}</p>
        </div>

        {{-- Submitted Reviews Section --}}
        <div class="col-12">
            <h2 class="mb-3">Submitted Reviews</h2>
            @if($submittedReviews->isEmpty())
                <div class="alert alert-info text-center">You haven't submitted any reviews yet.</div>
            @else
                <ul class="list-group">
                    @foreach($submittedReviews as $review)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $review->review_text }}</span>
                            <span class="badge badge-secondary">Reviewee: {{ $review->reviewee->name }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger mt-4">
        {{ session('error') }}
    </div>
@endif

        {{-- Peer Review Submission Form --}}
        <div class="col-12 mt-5">
            <h2 class="mb-3">Submit Peer Review</h2>
            <form action="{{ route('assessment.submit_review') }}" method="POST" class="shadow p-4 rounded bg-light">
                @csrf
                <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">
                
                <!-- Showing the logged-in student's ID and name -->
                <div class="form-group">
                    <label for="reviewee_id">Student ID:</label>
                    <input type="text" class="form-control my-4" id="reviewee_id" value="{{ auth()->user()->s_number }}" disabled>
                </div>

                <div class="form-group">
                    <label for="review_text">Your Review:</label>
                    <textarea name="review_text" id="review_text" class="form-control" required minlength="5" rows="4" placeholder="Write at least 5 words"></textarea>
                </div>

                <button type="submit" class="btn btn-primary mt-4 btn-block">Submit Review</button>
            </form>
        </div>

        {{-- Received Reviews Section --}}
        <div class="col-12 mt-5">
            <h2 class="mb-3">Received Reviews</h2>
            @if($receivedReviews->isEmpty())
                <div class="alert alert-warning text-center">No reviews received from the teacher yet.</div>
            @else
                <ul class="list-group">
                    @foreach($receivedReviews as $review)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{-- Display review text --}}
                            <span class="text-black">{{ $review->review_text }}</span>
                            
                            {{-- Display teacher's name --}}
                            <span class="text-black">
                                Reviewer: {{ $review->reviewer->name }} 
                                <!-- (ID: {{ $review->reviewer->id }}) -->
                            </span>
                            
                            {{-- Display score assigned by the teacher --}}
                            <span class="text-black">
                                Score: {{ $review->score }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

    </div>
</div>
@endsection
