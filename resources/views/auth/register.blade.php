@extends('layouts.app')

@section('title', 'Create an account')
@section('subtitle', 'Account')

@section('content')
    <div class="auth-card">
        <h1>Create an account</h1>
        <p class="lede">An account lets you save passages and write private notes. Nothing you save is public.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="field">
                <label for="name">Name <span class="muted">(shown beside your comments)</span></label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="60" autocomplete="name">
                @error('name') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                @error('email') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="password">Password <span class="muted">(at least 10 characters)</span></label>
                <input id="password" type="password" name="password" required autocomplete="new-password">
                @error('password') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Repeat password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>

            {{-- Hidden trap for bots. Real visitors never see or fill it. --}}
            <div class="hp" aria-hidden="true">
                <label for="website">Leave this empty</label>
                <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <label class="check">
                <input type="checkbox" name="consent" value="1" @checked(old('consent')) required>
                <span>I have read the <a href="{{ route('privacy') }}" target="_blank" rel="noopener">privacy policy</a> and agree to it.</span>
            </label>
            @error('consent') <div class="field-error" style="margin:-8px 0 12px">{{ $message }}</div> @enderror

            <button class="btn" type="submit">Create account</button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">Already have an account? Sign in</a>
        </div>
    </div>
@endsection
