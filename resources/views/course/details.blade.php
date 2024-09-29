@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $course->name }} ({{ $course->course_code }})</h1>

    <h3>Teacher: {{ $course->teacher->name }}</h3>

    <!-- List of Assessments -->
    <h3>Assessments:</h3>
    <ul>
        @foreach($course->assessments as $assessment)
            <li>
                {{ $assessment->title }} - Due: {{ $assessment->due_date }}
                <a href="{{ route('assessment.details', $assessment->id) }}">View Details</a>
            </li>
        @endforeach
    </ul>

    @if($isTeacher)
        <!-- For Teachers: Enroll Students and Add Assessments -->
        <h3>Enroll a Student:</h3>
        <form method="POST" action="{{ route('teacher.enroll', $course->id) }}">
            @csrf
            <input type="text" name="student_s_number" placeholder="Student S-Number" required>
            <button type="submit" class="btn btn-primary">Enroll Student</button>
        </form>

        <h3>Add a Peer Review Assessment:</h3>
        <form method="POST" action="{{ route('teacher.add.assessment', $course->id) }}">
            @csrf
            <input type="text" name="title" placeholder="Assessment Title" maxlength="20" required>
            <textarea name="instruction" placeholder="Instructions" required></textarea>
            <input type="number" name="num_reviews" min="1" placeholder="Number of Reviews" required>
            <input type="number" name="max_score" min="1" max="100" placeholder="Max Score" required>
            <input type="datetime-local" name="due_date" required>
            <select name="type" required>
                <option value="student-select">Student Select</option>
                <option value="teacher-assign">Teacher Assign</option>
            </select>
            <button type="submit" class="btn btn-primary">Add Assessment</button>
        </form>
    @endif

    @if(!$isTeacher)
        <!-- For Students: Enrolled Courses -->
        <h3>Your Enrollments:</h3>
        <ul>
            @foreach($course->students as $student)
                @if($student->id == auth()->id())
                    <li>You are enrolled in this course.</li>
                @endif
            @endforeach
        </ul>
    @endif
</div>
@endsection
