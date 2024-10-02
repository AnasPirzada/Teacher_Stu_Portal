@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Welcome, {{ $user->name }} ({{ ucfirst($user->role) }})</h2>
    
    <!-- Display error message if any -->
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Display success message if any -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($courses->isEmpty())
        <p>You are not enrolled in or teaching any courses.</p>
    @else
        <h3>Your Courses:</h3>
        <ul>
            @foreach($courses as $course)
                <li><a href="{{ route('courses.details', $course->id) }}">{{ $course->course_code }} - {{ $course->name }}</a></li>
            @endforeach
        </ul>
    @endif

    <!-- Upload Course JSON File Form (only for teachers) -->
    @if($user->role === 'teacher')
        <div class="bg-white p-4 rounded-lg shadow-md mt-4">
            <h3>Upload Course File (JSON)</h3>
            <form method="POST" action="{{ route('courses.upload') }}" enctype="multipart/form-data">
                @csrf <!-- Ensure CSRF protection -->
                <div class="form-group">
                    <label for="course_file">Select JSON File</label>
                    <input type="file" name="course_file" accept=".json" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Upload Course</button>
            </form>
        </div>
    @endif
</div>
@endsection
