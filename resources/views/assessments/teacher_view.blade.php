@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Assessment Title Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-4 font-weight-bold">Assessment: {{ $assessment->title }}</h1>
            <p class="text-muted"><strong>Course:</strong> {{ $assessment->course->name }}</p>
        </div>
    </div>

    <!-- Assessment Details Section -->
    <div class="row mb-5">
        <div class="col-md-4 col-12 mb-3">
            <div class="card px-3 py-3 h-100 shadow-sm">
                <h5 class="font-weight-bold">Instructions</h5>
                <p>{{ $assessment->instruction }}</p>
            </div>
        </div>
        <div class="col-md-4 col-12 mb-3">
            <div class="card px-3 py-3 h-100 shadow-sm">
                <h5 class="font-weight-bold">Due Date</h5>
                <p>{{ \Carbon\Carbon::parse($assessment->due_date)->format('M d, Y') }}</p>
            </div>
        </div>
        <div class="col-md-4 col-12 mb-3">
            <div class="card px-3 py-3 h-100 shadow-sm">
                <h5 class="font-weight-bold">Maximum Score</h5>
                <p>{{ $assessment->max_score }}</p>
            </div>
        </div>
    </div>

    <!-- Student List Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="font-weight-bold">Students in this Course</h2>
        </div>
    </div>

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

    <div class="text-right mb-4">
            <a href="{{ route('assessment.create_groups', ['assessmentId' => $assessment->id]) }}"
            class="btn btn-primary {{ $groups->isEmpty() ? '' : 'disabled' }}">
            Create Groups
            </a>
    </div>
</div>


<h2>Groups (A-Z)</h2>
@if(session('success'))
    <div class="alert alert-success mt-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger mt-4">
        {{ session('error') }}
    </div>
@endif


<!-- 

@foreach($groups as $group)
    <div class="group mt-4">
        <h3>Group {{ $group->name }}</h3>
        <ul>
            @foreach($group->students as $student)
                <li>{{ $student->name }} ({{ $student->s_number }})</li>
            @endforeach
        </ul>
    </div>
@endforeach -->



<h2>All students enrolled in this assessment</h2>
    <!-- Responsive Student Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
        <thead class="thead-dark">
    <tr>
        <th>Student Name</th>
        <th>Student Number</th>
        <th>Group</th> <!-- Column for Group -->
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
            <td>{{ $student->group ? $student->group->name : 'Not Assigned' }} <!-- Display group name or 'Not Assigned' -->
            </td>
            <td>{{ $student->submittedReviewsCount ?? '0' }}</td>
            <td>{{ $student->receivedReviewsCount ?? '0' }}</td>
            <td>
                <form method="POST" action="{{ route('assessment.mark_student', ['id' => $assessment->id]) }}">
                    @csrf
                    <div class="input-group">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <input type="number" name="score" value="{{ $student->score ?? '' }}" min="0" max="{{ $assessment->max_score }}" class="form-control" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-success ms-4">Save</button>
                        </div>
                    </div>
                </form>
            </td>
            <td>
                <a href="{{ route('teacher.student_reviews', ['assessment_id' => $assessment->id, 'student_id' => $student->id]) }}" class="btn btn-info btn-block">View Reviews</a>
            </td>
        </tr>
    @endforeach
</tbody>



        </table>
    </div>

    <!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    @if ($students->hasPages())
        <nav>
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($students->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">Previous</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $students->previousPageUrl() }}" rel="prev">Previous</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($students->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $students->nextPageUrl() }}" rel="next">Next</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">Next</span>
                    </li>
                @endif
            </ul>
        </nav>
    @endif
</div>



</div>
@endsection
