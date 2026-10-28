@extends('layouts.app')

@section('title', $person->name)
@section('subtitle', $person->handle)

@section('meta')
    @php($about = $person->bio ?: 'Passages posted by ' . $person->name . '.')
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
        </div>

        @forelse ($posts as $passage)
            @include('partials.post', ['passage' => $passage])
        @empty
            <p class="empty">No approved posts from this voice yet.</p>
        @endforelse
    </div>
@endsection
