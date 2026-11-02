@extends('layouts.app')

@section('title', 'Account')
@section('subtitle', 'Account')

@section('content')
    <div class="auth-card">
        <h1>{{ $user->name }}</h1>
        <p class="lede">{{ $user->email }} · {{ $savedCount }} saved · {{ $noteCount }} {{ \Illuminate\Support\Str::plural('note', $noteCount) }}</p>

        <div class="account-actions">
            <a class="btn btn-ghost" href="{{ route('library') }}">My library</a>
            <a class="btn btn-ghost" href="{{ route('account.export') }}">Download my data</a>
            <a class="btn btn-ghost" href="{{ route('blocked') }}">Blocked people</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button class="btn btn-ghost" type="submit">Sign out</button>
            </form>
        </div>
    </div>

    <div class="auth-card">
        <h1>Delete my account</h1>
        <p class="lede">This permanently removes your account, your saved passages, and your notes. It can’t be undone.</p>

        <form method="POST" action="{{ route('account.destroy') }}" onsubmit="return confirm('Delete your account and everything in it? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <div class="field">
                <label for="password">Confirm with your password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
                @error('password') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <button class="btn btn-danger" type="submit">Delete my account</button>
        </form>
    </div>
@endsection
