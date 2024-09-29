@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>{{ $assessment->title }}</h2>
            <p><strong>Instructions:</strong> {{ $assessment->instruction }}</p>
            <p><strong>Due Date:</strong> {{ $assessment->due_date }}</p>
            <p><strong>Maximum Score:</strong> {{ $assessment->max_score }}</p>

            <h4>Submit Your Peer Review</h4>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('review.store') }}">
                @csrf
                <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">

                <div class="form-group">
                    <label for="reviewee_id">Select a Peer to Review</label>
                    <select class="form-control @error('reviewee_id') is-invalid @enderror" id="reviewee_id" name="reviewee_id" required>
                        <option value="">Choose a student</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" {{ old('reviewee_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->s_number }})
                            </option>
                        @endforeach
                    </select>
                    @error('reviewee_id')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mt-3">
                    <label for="review_text">Review Text</label>
                    <textarea class="form-control @error('review_text') is-invalid @enderror" id="review_text" name="review_text" rows="4" required>{{ old('review_text') }}</textarea>
                    @error('review_text')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary w-100">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
