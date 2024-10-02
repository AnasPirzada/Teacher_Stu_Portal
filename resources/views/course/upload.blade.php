@extends('layouts.app')

@section('content')

<form method="POST" action="{{ route('course.upload') }}" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <input type="file" name="course_file" accept=".txt" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Upload Course</button>
</form>
@endsection
