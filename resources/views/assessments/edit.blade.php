@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-4 font-weight-bold">Edit Assessment</h1>
        </div>
    </div>

    <!-- Show errors if validation fails -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Peer Review Assessment Form -->
    <form method="POST" action="{{ route('teacher.update.assessment', $assessment->id) }}">
        @csrf
        <div class="form-row">
            <div class="form-group col-md-6 mt-2">
                <input type="text" name="title" placeholder="Assessment Title" value="{{ old('title', $assessment->title) }}" maxlength="20" required class="form-control">
            </div>
            <div class="form-group col-md-6 mt-2">
                <input type="text" name="instruction" placeholder="Instructions" value="{{ old('instruction', $assessment->instruction) }}" required class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4 mt-2">
                <input type="number" name="num_reviews" min="1" value="{{ old('num_reviews', $assessment->num_reviews) }}" placeholder="Number of Reviews" required class="form-control">
            </div>
            <div class="form-group col-md-4 mt-2">
                <input type="number" name="max_score" min="1" max="100" value="{{ old('max_score', $assessment->max_score) }}" placeholder="Max Score" required class="form-control">
            </div>
            <div class="form-group col-md-4 mt-2">
                <input type="datetime-local" name="due_date" value="{{ old('due_date', \Carbon\Carbon::parse($assessment->due_date)->format('Y-m-d\TH:i')) }}" required class="form-control">
            </div>
        </div>
        <div class="form-row mt-2">
            <div class="form-group col-md-6">
                <select name="type" required class="form-control">
                    <option value="student-select" {{ $assessment->type == 'student-select' ? 'selected' : '' }}>Student Select</option>
                    <option value="teacher-assign" {{ $assessment->type == 'teacher-assign' ? 'selected' : '' }}>Teacher Assign</option>
                </select>
            </div>
            <div class="form-group col-md-6 mt-2">
                <button type="submit" class="btn btn-primary w-100">Update Assessment</button>
            </div>
        </div>
    </form>
</div>
@endsection
