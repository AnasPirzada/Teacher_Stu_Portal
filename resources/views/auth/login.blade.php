@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">{{ __('Login') }}</div>

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
                <div class="card-body">
                    <form method="POST" action="{{ route('login.submit') }}">
                        @csrf
                        <div class="form-group">
                            <label for="s_number">{{ __('Student Number') }}</label>
                            <input id="s_number" type="text" class="form-control @error('s_number') is-invalid @enderror" name="s_number" required autofocus>
                            @error('s_number')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label for="password">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary w-100">{{ __('Login') }}</button>
                        </div>

                    </form>
                    <div class="form-group mt-3">
                        <a href="{{ route('register') }}" class="text-secondary d-flex justify-content-center align-items-center ps-6">{{ __('Register') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
