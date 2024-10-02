@extends('layouts.app')

@section('content')
<div class="container mx-auto p-5">
    <div class="bg-white shadow-md rounded-lg p-4 mb-4">
        <h1 class="text-3xl font-bold mb-2">{{ $course->name }} ({{ $course->course_code }})</h1>
        <h3 class="text-lg font-semibold">Teacher: <span class="text-primary">{{ $course->teacher->name }}</span></h3>
    </div>

    <div class="bg-light p-4 rounded-lg shadow-md mb-4">
        <h3 class="text-2xl font-semibold mb-3">Assessments:</h3>
        <ul class="list-unstyled">
            @foreach($course->assessments as $assessment)
                <li class="mb-2">
                    <span class="font-weight-bold">{{ $assessment->title }}</span> - Due: <span class="text-danger">{{ $assessment->due_date }}</span>
                    <a href="{{ route('assessment.details', $assessment->id) }}" class="text-info">View Details</a>
                </li>
            @endforeach
        </ul>
    </div>

    @if($isTeacher)
        <div class="bg-white p-4 rounded-lg shadow-md mb-4">
            <h3 class="text-2xl font-semibold mb-3">Enroll a Student:</h3>
            <form method="POST" action="{{ route('teacher.enroll', $course->id) }}" class="mb-4">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <input type="text" name="student_s_number" placeholder="Student S-Number" required class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <button type="submit" class="btn btn-primary w-100 mt-3">Enroll Student</button>
                    </div>
                </div>
            </form>
        







            <h3 class="text-2xl font-semibold mb-3 pt-4 ">Add a Peer Review Assessment:</h3>
            <form method="POST" action="{{ route('teacher.add.assessment', $course->id) }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6 mt-2">
                        <input type="text" name="title" placeholder="Assessment Title" maxlength="20" required class="form-control">
                    </div>
                    <div class="form-group col-md-6 mt-2">
                        <input type="text" name="instruction" placeholder="Instructions" required class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4 mt-2">
                        <input type="number" name="num_reviews" min="1" placeholder="Number of Reviews" required class="form-control">
                    </div>
                    <div class="form-group col-md-4 mt-2">
                        <input type="number" name="max_score" min="1" max="100" placeholder="Max Score" required class="form-control">
                    </div>
                    <div class="form-group col-md-4 mt-2">
                        <input type="datetime-local" name="due_date" required class="form-control">
                    </div>
                </div>
                <div class="form-row mt-2">
                    <div class="form-group col-md-6">
                        <select name="type" required class="form-control">
                            <option value="student-select">Student Select</option>
                            <option value="teacher-assign">Teacher Assign</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6 mt-2">
                        <button type="submit" class="btn btn-primary w-100">Add Assessment</button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    @if(!$isTeacher)
        <div class="bg-light p-4 rounded-lg shadow-md mb-4">
            <h3 class="text-2xl font-semibold mb-3">Your Enrollments:</h3>
            <ul class="list-unstyled">
                @foreach($course->students as $student)
                    @if($student->id == auth()->id())
                        <li>You are enrolled in this course.</li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
