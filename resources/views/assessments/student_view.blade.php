@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 text-center">{{ $assessment->title }}</h1>
            <h4 class="text-center">Posted by: {{ $assessment->course->teacher->name }}</h4> {{-- Display teacher's name --}}
        </div>
        <div class="col-12 text-center mb-4">
    <p><strong>Instruction:</strong> {{ $assessment->instruction }}</p>
    <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($assessment->due_date)->format('M d, Y') }}</p>
    <p><strong>Max Score:</strong> {{ $assessment->max_score }}</p>
    <p><strong>Your Score:</strong> 
        @if($score !== null)
            {{ $score }}/{{ $assessment->max_score }}
        @else
            Score not added yet
        @endif
    </p>
</div>



        {{-- Submitted Reviews Section --}}
<div class="col-12">
    <h2 class="mb-3">Your Submitted Reviews</h2>
    @if($submittedReviews->isEmpty())
        <div class="alert alert-info text-center">You haven't submitted any reviews yet.</div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Review Text</th> {{-- Column for review text --}}
                    <th>Rating</th> {{-- Column for rating --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($submittedReviews as $review)
                    <tr>
                        <td>{{ $review->review_text }}</td> {{-- Show review text --}}
                        <td>{{ $review->rating }}</td> {{-- Show rating --}}
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

        @if(session('success'))
            <div class="alert alert-success mt-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mt-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Submit Peer Review Section --}}
<div class="col-12 mt-5">
    <h2 class="mb-3">Submit Peer Review</h2>
    <form action="{{ route('assessment.submit_review') }}" method="POST" class="shadow p-4 rounded bg-light">
        @csrf
        <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">

        <!-- <div class="form-group">
            <label for="reviewee_id">Select Reviewee:</label>
            <select name="reviewee_id" id="reviewee_id" class="form-control" required>
                <option value="" disabled selected>Select a student</option>
                @foreach ($students as $student)
                    @if ($student->id !== auth()->id()) <!-- Prevent reviewing self 
                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                    @endif
                @endforeach
            </select>
        </div> -->
        <div class="form-group">
    <label for="reviewee_id">Select Reviewee:</label>
    <select name="reviewee_id" id="reviewee_id" class="form-control" required>
        <option value="" disabled selected>Select a student</option>
        @if ($userGroup) <!-- If groups have been assigned -->
            @foreach ($userGroup->students as $student)
                @if ($student->id !== auth()->id()) <!-- Exclude self -->
                    <option value="{{ $student->id }}">{{ $student->name }} (Group {{ $userGroup->name }})</option>
                @endif
            @endforeach
        @else <!-- If groups have not been assigned -->
            @foreach ($students as $student)
                @if ($student->id !== auth()->id()) <!-- Exclude self -->
                    <option value="{{ $student->id }}">{{ $student->name }}</option>
                @endif
            @endforeach
        @endif
    </select>
</div>



        <div class="form-group">
            <label for="review_text">Your Review:</label>
            <textarea name="review_text" id="review_text" class="form-control" required minlength="5" rows="4" placeholder="Write at least 5 words"></textarea>
        </div>

        <div class="form-group">
            <label for="rating">Rating:</label>
            <select name="rating" id="rating" class="form-control" required>
                <option value="" disabled selected>Select a rating</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-4 btn-block">Submit Review</button>
    </form>
</div>


        
{{-- Received Reviews from Peers --}}
<div class="col-12 mt-5">
    <h2 class="mb-3">Received Reviews from Other Students</h2>
    @if($receivedReviews->isEmpty())
        <div class="alert alert-info text-center">You haven't received any reviews from other students yet.</div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Reviewer Name</th> {{-- Column for reviewer's name --}}
                    <th>Review Text</th> {{-- Column for review text --}}
                    <th>Rating</th> {{-- Column for rating --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($receivedReviews as $review)
                    <tr>
                        <td>{{ $review->reviewer->name }}</td> {{-- Show reviewer's name --}}
                        <td>{{ $review->review_text }}</td> {{-- Show review text --}}
                        <td>{{ $review->rating }}</td> {{-- Show rating --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>



    </div>
</div>
@endsection
