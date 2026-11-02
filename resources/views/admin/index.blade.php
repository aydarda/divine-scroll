@extends('layouts.app')

@section('title', 'Admin')
@section('subtitle', 'Admin')

@section('content')
    @include('admin._nav')

    <div class="auth-card">
        <h1>Needs your attention</h1>
        <p><a href="{{ route('admin.comments') }}">{{ $pendingComments }} comment(s) waiting for review</a></p>
        <p><a href="{{ route('admin.comments') }}">{{ $reportedComments }} published comment(s) with reports</a></p>
        <p><a href="{{ route('admin.suggestions') }}">{{ $pendingSuggestions }} suggestion(s) waiting for review</a></p>
        <p class="muted">{{ $pendingPassages }} passage(s) are still pending approval. Approve them with <code>php artisan passages:approve</code>.</p>
    </div>
@endsection
