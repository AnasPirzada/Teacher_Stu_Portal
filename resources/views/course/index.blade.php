@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">Your Courses</h2>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($courses->isEmpty())
                <div class="alert alert-info">You are not enrolled in any courses.</div>
            @else
                <div class="list-group">
                    @foreach ($courses as $course)
                        <a href="{{ route('courses.show', $course->id) }}" class="list-group-item list-group-item-action">
                            <h5 class="mb-1">{{ $course->name }}</h5>
                            <small>{{ $course->course_code }}</small>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
