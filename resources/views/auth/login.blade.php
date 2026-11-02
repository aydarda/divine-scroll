@extends('layouts.app')

@section('title', 'Sign in')
@section('subtitle', 'Account')

@section('content')
    <div class="auth-card">
        <h1>Sign in</h1>
        <p class="lede">Save passages and keep private notes.</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>
            <label class="check"><input type="checkbox" name="remember" value="1"> Keep me signed in on this device</label>
            <button class="btn" type="submit">Sign in</button>
        </form>

        <div class="auth-links">
            <a href="{{ route('password.request') }}">Forgot your password?</a>
            <a href="{{ route('register') }}">Create an account</a>
        </div>
    </div>
@endsection
