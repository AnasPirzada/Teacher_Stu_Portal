@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Course: {{ $course->name }}</h1>
    <form method="POST" action="{{ route('courses.update', $course->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Course Name</label>
            <input type="text" name="name" class="form-control" value="{{ $course->name }}" required>
        </div>
        <div class="form-group">
            <label for="course_code">Course Code</label>
            <input type="text" name="course_code" class="form-control" value="{{ $course->course_code }}" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control">{{ $course->description }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Course</button>
    </form>
</div>
@endsection
