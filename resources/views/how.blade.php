@extends('layouts.app')

@section('title', 'How to read the badges')
@section('subtitle', 'Guide')

@section('content')
    <div class="auth-card">
        <h1>How to read the badges</h1>
        <p class="lede">Every passage is traced to a voice. The badge on each card says how we know who is speaking. “All voices” on the feed shows everything, and the other filters show one badge at a time.</p>

        @include('partials.basis-guide')

        <p class="profile-caveat">If you think a badge is wrong, sign in and use “Suggest a correction” on that passage. A moderator reviews every suggestion.</p>
    </div>
@endsection