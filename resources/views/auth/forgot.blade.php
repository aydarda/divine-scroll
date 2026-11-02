@extends('layouts.app')

@section('title', 'Reset your password')
@section('subtitle', 'Account')

@section('content')
    <div class="auth-card">
        <h1>Reset your password</h1>
        <p class="lede">Enter your email and we’ll send a link to choose a new password.</p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <button class="btn" type="submit">Send reset link</button>
        </form>

        <div class="auth-links"><a href="{{ route('login') }}">Back to sign in</a></div>
    </div>
@endsection
