@extends('layouts.app')

@section('title', $q !== '' ? 'Search: ' . $q : 'Search')
@section('subtitle', 'Search')

@section('content')
    <style>
        .search-form { display: flex; gap: 8px; }
        .search-form input[type=search] { flex: 1; min-width: 0; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 9999px; font: inherit; font-size: 15px; background: #fff; color: #111827; }
        .search-form input[type=search]:focus { outline: 2px solid #a7f3d0; border-color: #059669; }
        .voice-chips { display: flex; flex-wrap: wrap; gap: 8px; padding: 12px 16px 0; }
        .voice-chip { padding: 4px 12px; border-radius: 9999px; background: #fff; border: 1px solid #e5e7eb; font-size: 14px; color: #374151; }
        .voice-chip:hover { border-color: #059669; text-decoration: none; }
        .result-count { padding: 12px 20px 0; font-size: 13px; color: #6b7280; }
    </style>

    <div class="auth-card" style="margin-bottom: 0;">
        <form method="GET" action="{{ route('search') }}" class="search-form" role="search">
            <input type="search" name="q" value="{{ $q }}" maxlength="100" placeholder="A word, a voice, or a reference like John 3:16" autofocus>
            <button class="btn" type="submit">Search</button>
        </form>
    </div>

    @if ($result === null)
        @if ($q !== '')
            <p class="empty" style="margin: 16px;">Type at least two characters.</p>
        @else
            <p class="empty" style="margin: 16px;">Search words in the passages, a voice such as “David,” or a reference such as “Psalm 23” or “Romans 8:28.”</p>
        @endif
    @else
        @if ($result['voices'])
            <div class="voice-chips">
                @foreach ($result['voices'] as $voice)
                    <a class="voice-chip" href="{{ route('person', $voice->slug) }}">{{ $voice->name }} <span class="muted">{{ $voice->handle }}</span></a>
                @endforeach
            </div>
        @endif

        <div class="result-count">
            {{ number_format($result['total']) }} {{ \Illuminate\Support\Str::plural('passage', $result['total']) }}
            {{ $result['mode'] === 'reference' ? 'at that reference' : 'match' }}
        </div>

        <div class="feed-container">
            @forelse ($posts as $passage)
                @include('partials.post', ['passage' => $passage])
            @empty
                <p class="empty">Nothing found. Try fewer or different words.</p>
            @endforelse

            <div class="pager-row">
                <span>@if ($page > 1)<a href="{{ route('search', ['q' => $q, 'page' => $page - 1]) }}">← Previous</a>@endif</span>
                <span>@if ($result['next_page'])<a href="{{ route('search', ['q' => $q, 'page' => $result['next_page']]) }}">More →</a>@endif</span>
            </div>
        </div>
    @endif
@endsection
