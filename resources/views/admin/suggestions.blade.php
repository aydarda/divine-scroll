@extends('layouts.app')

@section('title', 'Moderate suggestions')
@section('subtitle', 'Admin')

@section('content')
    @include('admin._nav')

    <div class="feed-container">
        <h2 class="admin-h">Waiting for review ({{ $items->count() }})</h2>

        @forelse ($items as $s)
            @php
                $current = $s->passage?->attribution;
                $defaultPerson = $s->suggested_person_id ?? $current?->person_id;
            @endphp
            <article class="note-item">
                <div class="head">
                    <span class="pill pill-traditional">{{ $s->typeLabel() }}</span>
                    <a class="name" href="{{ $s->passage ? route('passage', $s->passage) : '#' }}" @if ($s->passage) target="_blank" rel="noopener" @endif>{{ $s->reference() }}</a>
                    <span class="handle">· from {{ $s->user->name }} · {{ $s->created_at->diffForHumans() }}</span>
                </div>

                @if ($s->type === 'attribution' && $current)
                    <p class="muted">Now: {{ $current->person->name }}, {{ $current->basisLabel() }}</p>
                @endif
                <p class="muted">
                    Suggested voice: {{ $s->suggestedPerson?->name ?? ($s->person_other ?: 'not given') }}
                    @if ($s->suggested_basis) · badge: {{ str_replace('_', ' ', $s->suggested_basis) }} @endif
                    @if ($s->source) · cites {{ $s->source }} @endif
                </p>
                <p class="note-body">{{ $s->reason }}</p>

                <form method="POST" action="{{ route('admin.suggestion.accept', $s) }}" class="admin-form">
                    @csrf
                    <div class="ref-grid">
                        <div class="field">
                            <label>Voice</label>
                            <select name="person_id" required>
                                <option value="">Choose…</option>
                                @foreach ($persons as $p)
                                    <option value="{{ $p->id }}" @selected($defaultPerson == $p->id)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Badge</label>
                            <select name="basis" required>
                                @foreach (['stated_in_text' => 'Stated in text', 'traditional' => 'Traditional', 'speaker_only' => 'Speaker, not author', 'unattributed' => 'Unattributed'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($s->suggested_basis ?? $current?->basis) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label>Badge note (shown to readers)</label>
                        <input type="text" name="note" maxlength="500" value="{{ $current?->note }}">
                    </div>
                    @if ($s->type === 'passage')
                        <label class="check"><input type="checkbox" name="publish" value="1"> Publish straight away (otherwise it waits in the pending list)</label>
                    @endif
                    <div class="field">
                        <label>Note to the reader (optional)</label>
                        <input type="text" name="admin_note" maxlength="500">
                    </div>
                    <button class="btn" type="submit">Accept</button>
                </form>

                <form method="POST" action="{{ route('admin.suggestion.decline', $s) }}" class="admin-form">
                    @csrf
                    <div class="field">
                        <label>Reason for declining (optional, shown to the reader)</label>
                        <input type="text" name="admin_note" maxlength="500">
                    </div>
                    <button class="btn btn-danger" type="submit">Decline</button>
                </form>
            </article>
        @empty
            <p class="empty">Nothing waiting.</p>
        @endforelse
    </div>
@endsection
