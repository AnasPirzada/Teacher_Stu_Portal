@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Course Details</h1>
    
    <h3>{{ $course->name }} ({{ $course->course_code }})</h3>
    
    <h4>Teacher:</h4>
    <ul>
        <li>{{ $course->teacher->name }}</li>
    </ul>

    <h4>Assessments:</h4>
    <ul>
        @foreach($course->assessments as $assessment)
            <li>
                {{ $assessment->title }} - Due: {{ $assessment->due_date }}
                <a href="{{ route('assessment.details', $assessment->id) }}">View Details</a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
