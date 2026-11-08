@extends('layouts.app')

@section('title', 'Today')
@section('subtitle', 'Public feed')

@section('content')
    {{-- Hero — exact converted design --}}
    <section class="hero">
        <p class="hero-eyebrow">Voices of Scripture</p>
        <h1 class="hero-title">
            The Bible, quoted<br class="hero-br"> in its own voices.
        </h1>
        <p class="hero-sub">
            Every passage is traced to the person behind it — stated in the text,<br class="hero-br">
            carried by tradition, or spoken where no author is named.
        </p>
    </section>

    {{-- Filters: real links, so they work without JavaScript; with JavaScript they swap the feed in place --}}
    @php
        $filterLabels = [
            'all'          => 'All voices',
            'stated'       => 'Stated in text',
            'traditional'  => 'Traditional',
            'speaker'      => 'Speaker, not author',
            'unattributed' => 'Unattributed',
        ];
        $active = $filter ?? 'all';
    @endphp
    <div class="filters" id="filters">
        @foreach ($filterLabels as $key => $label)
            <a class="filter-btn {{ $active === $key ? 'is-active' : '' }}"
               data-filter="{{ $key }}"
               href="{{ $key === 'all' ? route('feed') : route('feed', ['basis' => $key]) }}">{{ $label }}</a>
        @endforeach
    </div>

        <details class="note-box">
        <summary class="correction-summary">What do these filters mean?</summary>
        @include('partials.basis-guide')
    </details>

    <div id="feed" class="feed-container">
        @if ($posts->isEmpty())
            <p class="empty">No passages for this filter yet.</p>
        @else
            @include('partials.feed-items', ['posts' => $posts])
        @endif
    </div>

    <div class="more-wrap" id="more-wrap" @if (! $posts->nextPageUrl()) hidden @endif>
        <a id="more" class="more" href="{{ $posts->nextPageUrl() }}">Load More</a>
    </div>

    <p class="muted" id="end-note" style="text-align:center; padding:12px;" @if ($posts->nextPageUrl() || $posts->isEmpty()) hidden @endif>That’s everything for now.</p>

    <script>
    (function () {
        if (!window.fetch) return;

        var feed = document.getElementById('feed');
        var filters = document.getElementById('filters');
        var wrap = document.getElementById('more-wrap');
        var more = document.getElementById('more');
        var endNote = document.getElementById('end-note');
        var latest = 0;
        var loading = false;

        function getJson(url) {
            return fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(function (res) {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                });
        }

        function showNext(next, hasPosts) {
            if (next) { more.href = next; more.textContent = 'Load More'; }
            wrap.hidden = !next;
            endNote.hidden = !!next || !hasPosts;
        }

        // Load more
        more.addEventListener('click', function (e) {
            e.preventDefault();
            if (loading) return;
            loading = true;
            var mine = latest;
            more.textContent = 'Loading…';
            more.setAttribute('aria-busy', 'true');

            getJson(more.href)
                .then(function (data) {
                    if (mine !== latest) return; // a filter was chosen meanwhile
                    feed.insertAdjacentHTML('beforeend', data.html);
                    showNext(data.next, true);
                })
                .catch(function () {
                    more.textContent = 'Couldn’t load. Tap to retry.';
                })
                .then(function () {
                    loading = false;
                    more.removeAttribute('aria-busy');
                });
        });

        // Filters
        filters.addEventListener('click', function (e) {
            var btn = e.target.closest('.filter-btn');
            if (!btn) return;
            e.preventDefault();

            var url = btn.href;
            var mine = ++latest;

            filters.querySelectorAll('.filter-btn').forEach(function (b) { b.classList.remove('is-active'); });
            btn.classList.add('is-active');
            feed.style.opacity = '.5';

            getJson(url)
                .then(function (data) {
                    if (mine !== latest) return;
                    var hasPosts = data.html.trim() !== '';
                    feed.innerHTML = hasPosts ? data.html : '<p class="empty">No passages for this filter yet.</p>';
                    showNext(data.next, hasPosts);
                    history.replaceState(null, '', url);
                })
                .catch(function () {
                    if (mine !== latest) return;
                    feed.innerHTML = '<p class="empty">Couldn’t load this filter. <a href="' + url + '">Try again</a></p>';
                })
                .then(function () {
                    if (mine === latest) feed.style.opacity = '';
                });
        });
    })();
    </script>
@endsection
