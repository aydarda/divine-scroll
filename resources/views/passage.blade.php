@extends('layouts.app')

@section('title', $shareTitle)
@section('subtitle', $passage->reference)

@section('meta')
    <link rel="canonical" href="{{ route('passage', $passage) }}">
    <meta name="description" content="{{ $shareText }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $shareTitle }}">
    <meta property="og:description" content="{{ $shareText }}">
    <meta property="og:url" content="{{ route('passage', $passage) }}">
    <meta name="twitter:title" content="{{ $shareTitle }}">
    <meta name="twitter:description" content="{{ $shareText }}">
    @if ($cardUrl)
        <meta property="og:image" content="{{ $cardUrl }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="{{ $cardUrl }}">
    @else
        <meta name="twitter:card" content="summary">
    @endif
@endsection

@section('content')
    <div class="back-row">
        <a class="back-link" href="{{ route('feed') }}">← Back to feed</a>
    </div>
    <div class="feed-container">
        @include('partials.post', ['passage' => $passage])
    </div>
@endsection
