@extends('layouts.app')

@section('title', 'Moderate comments')
@section('subtitle', 'Admin')

@section('content')
    @include('admin._nav')

    <div class="feed-container">
        <h2 class="admin-h">Waiting for review ({{ $pending->count() }})</h2>

        @forelse ($pending as $c)
            <article class="note-item">
                <div class="head">
                    <a class="name" href="{{ route('passage', $c->passage) }}" target="_blank" rel="noopener">{{ $c->passage->reference }}</a>
                    <span class="handle">{{ $c->passage->attribution?->person->name }}</span>
                    <span class="handle">· by {{ $c->user->name }} ({{ $c->user->email }}) · {{ $c->created_at->diffForHumans() }}</span>
                </div>
                <p class="note-body">{{ $c->body }}</p>
                <div class="account-actions">
                    <form method="POST" action="{{ route('admin.comment.approve', $c) }}">@csrf<button class="btn" type="submit">Approve</button></form>
                    <form method="POST" action="{{ route('admin.comment.reject', $c) }}">@csrf<button class="btn btn-danger" type="submit">Reject</button></form>
                </div>
            @include('admin._suspend', ['user' => $c->user])
            </article>
        @empty
            <p class="empty">Nothing waiting.</p>
        @endforelse

        <h2 class="admin-h">Published, with reports ({{ $reported->count() }})</h2>

        @forelse ($reported as $c)
            <article class="note-item">
                <div class="head">
                    <a class="name" href="{{ route('passage', $c->passage) }}" target="_blank" rel="noopener">{{ $c->passage->reference }}</a>
                    <span class="handle">· by {{ $c->user->name }} · {{ $c->reports_count }} report(s)</span>
                </div>
                <p class="note-body">{{ $c->body }}</p>
                @foreach ($c->reports as $r)
                    @if ($r->reason)<p class="muted">Reason given: {{ $r->reason }}</p>@endif
                @endforeach
                <div class="account-actions">
                    <form method="POST" action="{{ route('admin.comment.dismiss', $c) }}">@csrf<button class="btn btn-ghost" type="submit">Keep it</button></form>
                    <form method="POST" action="{{ route('admin.comment.reject', $c) }}">@csrf<button class="btn btn-danger" type="submit">Remove it</button></form>
                </div>
            @include('admin._suspend', ['user' => $c->user])
            </article>
        @empty
            <p class="empty">No reports.</p>
        @endforelse

        <h2 class="admin-h">Latest published comments ({{ $recent->count() }})</h2>

        @forelse ($recent as $c)
            <article class="note-item">
                <div class="head">
                    <a class="name" href="{{ route('passage', $c->passage) }}#comments" target="_blank" rel="noopener">{{ $c->passage->reference }}</a>
                    <span class="handle">· by {{ $c->user->name }} · {{ $c->created_at->diffForHumans() }}</span>
                </div>
                <p class="note-body">{{ $c->body }}</p>
                <form method="POST" action="{{ route('admin.comment.reject', $c) }}" onsubmit="return confirm('Remove this comment?');">
                    @csrf
                    <button class="btn btn-danger" type="submit">Remove</button>
                </form>
            @include('admin._suspend', ['user' => $c->user])
            </article>
        @empty
            <p class="empty">No comments yet.</p>
        @endforelse
    </div>
@endsection
