@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Course</h1>
    <form method="POST" action="{{ route('courses.store') }}">
        @csrf
        <div class="form-group">
            <label for="name">Course Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="course_code">Course Code</label>
            <input type="text" name="course_code" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create Course</button>
    </form>
</div>
@endsection
