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
        </div>

        <!-- {{-- Submitted Reviews Section --}}
        <div class="col-12">
            <h2 class="mb-3">Your Submitted Reviews</h2>
            @if($submittedReviews->isEmpty())
                <div class="alert alert-info text-center">You haven't submitted any reviews yet.</div>
            @else
                <ul class="list-group">
                @foreach ($submittedReviews as $review)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $review->review_text }}</span> {{-- Show review text --}}
                        <span>Rating: {{ $review->rating }}</span> {{-- Show rating --}}
                        <span>Score: {{ $review->score ?? 'Not scored yet' }}</span> {{-- Show score --}}
                    </li>
                @endforeach
                </ul>
            @endif
        </div> -->

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
                    <th>Score</th> {{-- Column for score --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($submittedReviews as $review)
                    <tr>
                        <td>{{ $review->review_text }}</td> {{-- Show review text --}}
                        <td>{{ $review->rating }}</td> {{-- Show rating --}}
                        <td>{{ $review->score ?? 'Not scored yet' }}</td> {{-- Show score --}}
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

        {{-- Peer Review Submission Form --}}
        <div class="col-12 mt-5">
            <h2 class="mb-3">Submit Peer Review</h2>
            <form action="{{ route('assessment.submit_review') }}" method="POST" class="shadow p-4 rounded bg-light">
                @csrf
                <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">
                
                <div class="form-group">
                    <label for="reviewee_id">Student ID:</label>
                    <input type="text" class="form-control my-4" id="reviewee_id" value="{{ auth()->user()->s_number }}" disabled>
                </div>
                <div class="form-group">
                    <label for="reviewee_id">Select Reviewee:</label>
                    <select name="reviewee_id" id="reviewee_id" class="form-control" required>
                        <option value="" disabled selected>Select a student</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                        @endforeach
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

        <!-- {{-- Received Reviews --}}
        <div class="col-12 mt-5">
            <h2 class="mb-3">Received Reviews from Teacher By You</h2>
            @if($receivedReviews->isEmpty())
                <div class="alert alert-warning text-center">Teacher haven't added any reviews yet.</div>
            @else
                <ul class="list-group">
                    @foreach($receivedReviews as $review)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-black">
                            Teacher: {{ $assessment->course->teacher->name }} {{-- Display the reviewer's name --}}
                            </span>
                            <span class="text-black">
                            Score: {{ $review->score }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div> -->
        {{-- Received Reviews --}}
<div class="col-12 mt-5">
    <h2 class="mb-3">Received Reviews from Teacher By You</h2>
    @if($receivedReviews->isEmpty())
        <div class="alert alert-warning text-center">Teacher haven't added any reviews yet.</div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Teacher</th> {{-- Column for teacher's name --}}
                    <th>Score</th> {{-- Column for score --}}
                </tr>
            </thead>
            <tbody>
                @foreach($receivedReviews as $review)
                    <tr>
                        <td class="text-black">
                            {{ $assessment->course->teacher->name }} {{-- Display the reviewer's name --}}
                        </td>
                        <td class="text-black">
                            {{ $review->score }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>


    </div>
</div>
@endsection
