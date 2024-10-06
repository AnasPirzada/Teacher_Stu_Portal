@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="display-4">Reviews for {{ $student->name }}</h1>

    <!-- Submitted Reviews Section -->
    <div class="col-12 mt-4">
        <h2 class="font-weight-bold">Submitted Reviews by {{ $student->name }}</h2>
        @if($submittedReviews->isEmpty())
            <p class="alert alert-info">No reviews have been submitted by this student yet.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Reviewee Name</th> {{-- Student who was reviewed --}}
                        <th>Review Text</th> {{-- Text of the review --}}
                        <th>Rating</th> {{-- Rating given by the student --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($submittedReviews as $review)
                        <tr>
                            <td>{{ $review->reviewee->name }}</td> {{-- Show reviewee's name --}}
                            <td>{{ $review->review_text }}</td> {{-- Show review text --}}
                            <td>{{ $review->rating }}</td> {{-- Show rating --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Received Reviews Section -->
    <div class="col-12 mt-5">
        <h2 class="font-weight-bold">Reviews Received by Other Students</h2>
        @if($receivedReviews->isEmpty())
            <p class="alert alert-info">No reviews have been received by this student yet.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Reviewer Name</th> {{-- Student who reviewed --}}
                        <th>Review Text</th> {{-- Text of the review --}}
                        <th>Rating</th> {{-- Rating given to the student --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($receivedReviews as $review)
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
@endsection
