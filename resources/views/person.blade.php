@extends('layouts.app')

@section('title', $person->name)
@section('subtitle', $person->handle)

@section('meta')
    @php
        $about = $person->bio ?: 'Passages posted by ' . $person->name . '.';
    @endphp
    <link rel="canonical" href="{{ route('person', $person) }}">
    <meta name="description" content="{{ $about }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:type" content="profile">
    <meta property="og:title" content="{{ $person->name }} ({{ $person->handle }})">
    <meta property="og:description" content="{{ $about }}">
    <meta property="og:url" content="{{ route('person', $person) }}">
    <meta name="twitter:card" content="summary">
@endsection

@section('content')
    <div class="feed-container">
        <div class="profile">
            <h1>{{ $person->name }}</h1>
            <div class="muted">{{ $person->handle }} · {{ $posts->count() }} {{ \Illuminate\Support\Str::plural('post', $posts->count()) }}</div>
            @if ($person->bio)
                <p class="bio">{{ $person->bio }}</p>
            @endif

            @php
                $facts = $person->facts->groupBy('kind');
                $year = fn ($y) => $y < 0 ? abs($y) . ' BC' : 'AD ' . $y;
                $factRows = ['Born' => 'born', 'Home town' => 'hometown', 'Lived' => 'lived', 'Years' => 'years', 'Known as' => 'role'];
            @endphp

            <div class="profile-facts">
                @foreach ($factRows as $label => $kind)
                    <div class="fact-row">
                        <div class="fact-label">{{ $label }}</div>
                        <div class="fact-body">
                            @forelse ($facts->get($kind, collect()) as $fact)
                                <div class="fact">
                                    {{ $fact->value }}
                                    @if ($fact->year_from !== null)
                                        <span class="muted">(c. {{ $year($fact->year_from) }}{{ $fact->year_to !== null && $fact->year_to !== $fact->year_from ? ' to ' . $year($fact->year_to) : '' }})</span>
                                    @endif
                                    <div class="fact-meta">
                                        <span class="pill pill-{{ $fact->basis }}">{{ $fact->basisLabel() }}</span>
                                        @if ($fact->source)<span>{{ $fact->source }}</span>@endif
                                    </div>
                                    @if ($fact->note)<div class="fact-note">{{ $fact->note }}</div>@endif
                                </div>
                            @empty
                                <span class="not-recorded">Not recorded in the text</span>
                            @endforelse
                        </div>
                    </div>
                @endforeach

                <div class="fact-row">
                    <div class="fact-label">Family</div>
                    <div class="fact-body">
                        @forelse ($person->relations as $rel)
                            <div class="fact">
                                <span class="muted">{{ ucfirst($rel->relation) }}:</span>
                                @if ($rel->relative)
                                    <a href="{{ route('person', $rel->relative) }}">{{ $rel->related_name }}</a>
                                @else
                                    {{ $rel->related_name }}
                                @endif
                                <div class="fact-meta">
                                    <span class="pill pill-{{ $rel->basis }}">{{ $rel->basisLabel() }}</span>
                                    @if ($rel->source)<span>{{ $rel->source }}</span>@endif
                                </div>
                                @if ($rel->note)<div class="fact-note">{{ $rel->note }}</div>@endif
                            </div>
                        @empty
                            <span class="not-recorded">Not recorded in the text</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <p class="profile-caveat">Every line names the verse it comes from. Calendar dates are scholarly estimates, and for many voices the text records little or nothing.</p>
        </div>

        @forelse ($posts as $passage)
            @include('partials.post', ['passage' => $passage])
        @empty
            <p class="empty">No approved posts from this voice yet.</p>
        @endforelse
    </div>
@endsection
