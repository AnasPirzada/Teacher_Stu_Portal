@extends('layouts.app')

@section('content')
<div class="container">
    <h1>All Courses</h1>

    @if($courses->isEmpty())
        <p>No courses available at the moment.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Course Code</th>
                    <th>Teacher</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                    <tr>
                        <td>{{ $course->name }}</td>
                        <td>{{ $course->course_code }}</td>
                        <td>{{ $course->teacher->name }}</td>
                        <td>
                            <a href="{{ route('courses.details', $course->id) }}" class="btn btn-info">View Details</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $courses->links() }}
        </div>
    @endif
</div>
@endsection
