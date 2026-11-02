@extends('layouts.app')

@section('title', 'Blocked people')
@section('subtitle', 'Blocked')

@section('content')
    <div class="auth-card">
        <h1>People you’ve blocked</h1>
        <p class="lede">You don’t see comments from these people. They aren’t told.</p>

        @forelse ($users as $u)
            <div class="account-actions" style="justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <strong>{{ $u->name }}</strong>
                <form method="POST" action="{{ route('user.unblock', $u->id) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-ghost" type="submit">Unblock</button>
                </form>
            </div>
        @empty
            <p class="muted">You haven’t blocked anyone. To block someone, use <strong>Block</strong> under one of their comments.</p>
        @endforelse

        <div class="auth-links"><a href="{{ route('account') }}">← Back to account</a></div>
    </div>
@endsection
