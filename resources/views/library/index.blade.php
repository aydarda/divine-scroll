@extends('layouts.app')

@section('title', 'My library')
@section('subtitle', 'My library')

@section('content')
    <div class="tabs">
        <a class="filter-btn {{ $tab === 'saved' ? 'is-active' : '' }}" href="{{ route('library') }}">Saved ({{ $savedCount }})</a>
        <a class="filter-btn {{ $tab === 'notes' ? 'is-active' : '' }}" href="{{ route('library', ['tab' => 'notes']) }}">My notes ({{ $noteCount }})</a>
        <a class="filter-btn {{ $tab === 'contributions' ? 'is-active' : '' }}" href="{{ route('library', ['tab' => 'contributions']) }}">My contributions ({{ $contribCount }})</a>
    </div>

    <div class="feed-container">
        @if ($tab === 'saved')
            @forelse ($items as $passage)
                @include('partials.post', ['passage' => $passage])
            @empty
                <p class="empty">Nothing saved yet. Tap <strong>Save</strong> on any passage and it will appear here.</p>
            @endforelse
        @elseif ($tab === 'notes')
            @forelse ($items as $note)
                @php $passage = $note->passage; $who = $passage->attribution?->person; @endphp
                <article class="note-item">
                    <div class="head">
                        <a class="name" href="{{ route('passage', $passage) }}">{{ $passage->reference }}</a>
                        @if ($who)<span class="handle">{{ $who->name }}</span>@endif
                        <span class="handle">· {{ $note->updated_at->diffForHumans() }}</span>
                    </div>
                    <p class="note-body">{{ $note->body }}</p>
                    <a class="ref" href="{{ route('passage', $passage) }}#note">Open and edit</a>
                </article>
            @empty
                <p class="empty">No notes yet. Open any passage and write one. Only you can see it.</p>
            @endforelse
        @else
            <div class="account-actions">
                <a class="btn" href="{{ route('suggest') }}">Suggest a passage</a>
            </div>

            <h2 class="admin-h">Suggestions</h2>
            @forelse ($suggestions as $s)
                <article class="note-item">
                    <div class="head">
                        <span class="pill {{ $s->status === 'accepted' ? 'pill-stated_in_text' : ($s->status === 'declined' ? 'pill-scholarly_estimate' : 'pill-traditional') }}">{{ ucfirst($s->status === 'pending' ? 'Waiting for review' : $s->status) }}</span>
                        <span class="name">{{ $s->typeLabel() }}</span>
                        <span class="handle">{{ $s->reference() }} · {{ $s->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="note-body">{{ $s->reason }}</p>
                    @if ($s->admin_note)<p class="muted">Moderator: {{ $s->admin_note }}</p>@endif
                </article>
            @empty
                <p class="empty">No suggestions yet.</p>
            @endforelse

            <h2 class="admin-h">Comments</h2>
            @forelse ($comments as $c)
                <article class="note-item">
                    <div class="head">
                        <span class="pill {{ $c->status === 'approved' ? 'pill-stated_in_text' : ($c->status === 'rejected' ? 'pill-scholarly_estimate' : 'pill-traditional') }}">{{ $c->status === 'pending' ? 'Waiting for review' : ($c->status === 'approved' ? 'Published' : 'Not published') }}</span>
                        <a class="name" href="{{ route('passage', $c->passage) }}#comments">{{ $c->passage->reference }}</a>
                        <span class="handle">· {{ $c->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="note-body">{{ $c->body }}</p>
                </article>
            @empty
                <p class="empty">No comments yet.</p>
            @endforelse
        @endif

        @if ($items)
            <div class="pager-row">
                <span>@if ($items->previousPageUrl())<a href="{{ $items->previousPageUrl() }}">← Newer</a>@endif</span>
                <span>@if ($items->nextPageUrl())<a href="{{ $items->nextPageUrl() }}">Older →</a>@endif</span>
            </div>
        @endif
    </div>
@endsection
