@extends('layouts.app')

@section('title', 'People')
@section('subtitle', 'Admin')

@section('content')
    @include('admin._nav')

    <div class="auth-card" style="margin-bottom: 0;">
        <form method="GET" action="{{ route('admin.users') }}" class="account-actions">
            <input type="search" name="q" value="{{ $q }}" placeholder="Name or email"
                   style="flex: 1; min-width: 180px; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 9999px; font: inherit;">
            <button class="btn" type="submit">Search</button>
        </form>
    </div>

    <div class="feed-container">
        @forelse ($users as $u)
            <article class="note-item">
                <div class="head">
                    <span class="name">{{ $u->name }}</span>
                    <span class="handle">{{ $u->email }} · joined {{ $u->created_at->diffForHumans() }}</span>
                    @if ($u->is_admin)<span class="pill pill-stated_in_text">Moderator</span>@endif
                    @if ($u->suspended_at)<span class="pill pill-scholarly_estimate">Suspended</span>@endif
                </div>

                @if ($u->suspended_at)
                    <p class="muted">Since {{ $u->suspended_at->diffForHumans() }}@if ($u->suspended_reason): {{ $u->suspended_reason }}@endif</p>
                    <form method="POST" action="{{ route('admin.user.unsuspend', $u) }}">
                        @csrf
                        <button class="btn btn-ghost" type="submit">Let them sign in again</button>
                    </form>
                @elseif (! $u->is_admin)
                    <form method="POST" action="{{ route('admin.user.suspend', $u) }}" class="admin-form"
                          onsubmit="return confirm('Suspend this person?');">
                        @csrf
                        <div class="field">
                            <label>Reason (optional, kept for your records)</label>
                            <input type="text" name="reason" maxlength="200">
                        </div>
                        <label class="check"><input type="checkbox" name="remove_comments" value="1" checked> Also hide all their comments</label>
                        <button class="btn btn-danger" type="submit">Suspend</button>
                    </form>
                @endif
            </article>
        @empty
            <p class="empty">No one found.</p>
        @endforelse

        <div class="pager-row">
            <span>@if ($users->previousPageUrl())<a href="{{ $users->previousPageUrl() }}">← Newer</a>@endif</span>
            <span>@if ($users->nextPageUrl())<a href="{{ $users->nextPageUrl() }}">Older →</a>@endif</span>
        </div>
    </div>
@endsection
