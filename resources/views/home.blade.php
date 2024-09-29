@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Welcome, {{ $user->name }} ({{ ucfirst($user->role) }})</h2>
    
    @if($courses->isEmpty())
        <p>You are not enrolled in or teaching any courses.</p>
    @else
        <h3>Your Courses:</h3>
        <ul>
            @foreach($courses as $course)
                <li><a href="{{ route('course.details', $course->id) }}">{{ $course->course_code }} - {{ $course->name }}</a></li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
