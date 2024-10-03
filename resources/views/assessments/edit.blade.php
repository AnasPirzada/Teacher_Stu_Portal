@extends('layouts.app')

@section('content')
<div class="container mx-auto p-5">
    <h1 class="text-3xl font-bold mb-4">Edit Assessment</h1>

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

    @if ($errors->any())
        <div class="alert alert-danger mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('assessments.update', $assessment->id) }}">
    @csrf
    @method('PUT')

    <!-- Readonly fields -->
    <div class="form-group">
        <label for="course_name">Course Name</label>
        <input type="text" id="course_name" class="form-control" value="{{ $course->name }}" readonly>
    </div>

    <div class="form-group mt-2">
        <label for="course_id">Course ID</label>
        <input type="text" id="course_id" class="form-control" value="{{ $course->id }}" readonly>
    </div>

    <!-- Editable fields -->
    <div class="form-group my-4">
        <label for="title">Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $assessment->title) }}" required>
    </div>

    <div class="form-group">
        <label for="instruction">Instructions</label>
        <textarea name="instruction" class="form-control" required>{{ old('instruction', $assessment->instruction) }}</textarea>
    </div>

    <div class="form-group my-4">
        <label for="num_reviews">Number of Reviews</label>
        <input type="number" name="num_reviews" class="form-control" value="{{ old('num_reviews', $assessment->num_reviews) }}" min="1" required>
    </div>

    <div class="form-group">
        <label for="max_score">Max Score</label>
        <input type="number" name="max_score" class="form-control" value="{{ old('max_score', $assessment->max_score) }}" min="1" max="100" required>
    </div>

    <div class="form-group my-4">
        <label for="due_date">Due Date</label>
        <input type="datetime-local" name="due_date" class="form-control" value="{{ \Carbon\Carbon::parse(old('due_date', $assessment->due_date))->format('Y-m-d\TH:i') }}" required>
    </div>

    <div class="form-group my-4">
        <label for="type">Type</label>
        <select name="type" class="form-control" required>
            <option value="student-select" {{ old('type', $assessment->type) == 'student-select' ? 'selected' : '' }}>Student Select</option>
            <option value="teacher-assign" {{ old('type', $assessment->type) == 'teacher-assign' ? 'selected' : '' }}>Teacher Assign</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Update Assessment</button>
    </form>
</div>
@endsection
