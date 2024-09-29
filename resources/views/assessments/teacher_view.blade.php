@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Assessment: {{ $assessment->title }}</h1>
    <p><strong>Course:</strong> {{ $assessment->course->name }}</p>
    <p><strong>Instructions:</strong> {{ $assessment->instruction }}</p>
    <p><strong>Due Date:</strong> {{ $assessment->due_date }}</p>
    <p><strong>Maximum Score:</strong> {{ $assessment->max_score }}</p>

    <h2>Students in this Course</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Student Number</th>
                <th>Submitted Reviews</th>
                <th>Received Reviews</th>
                <th>Score</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->s_number }}</td>
                    <td>{{ $student->submitted_reviews_count }}</td>
                    <td>{{ $student->received_reviews_count }}</td>
                    <td>
                        <form method="POST" action="{{ route('teacher.mark.student', ['id' => $assessment->id]) }}">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <input type="number" name="score" value="{{ $student->score }}" min="0" max="{{ $assessment->max_score }}" class="form-control" required>
                    </td>
                    <td>
                            <button type="submit" class="btn btn-success">Save Score</button>
                        </form>
                        <a href="{{ route('teacher.student_reviews', ['assessment_id' => $assessment->id, 'student_id' => $student->id]) }}" class="btn btn-info mt-2">View Reviews</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $students->links() }}
    </div>
</div>
@endsection
