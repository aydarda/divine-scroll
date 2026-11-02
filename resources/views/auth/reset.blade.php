@extends('layouts.app')

@section('title', 'Choose a new password')
@section('subtitle', 'Account')

@section('content')
    <div class="auth-card">
        <h1>Choose a new password</h1>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required>
                @error('email') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="password">New password <span class="muted">(at least 10 characters)</span></label>
                <input id="password" type="password" name="password" required autocomplete="new-password">
                @error('password') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Repeat new password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>
            <button class="btn" type="submit">Change password</button>
        </form>
    </div>
@endsection
