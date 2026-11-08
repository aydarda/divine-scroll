<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Feed') · {{ config('app.name') }}</title>
    @hasSection('meta')
        @yield('meta')
    @else
        <meta name="description" content="Scripture, posted by the people who wrote and spoke it.">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ config('app.name') }}">
        <meta property="og:description" content="Scripture, posted by the people who wrote and spoke it.">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta name="twitter:card" content="summary">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Exact converted design — forced light theme (no dark mode override) */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #f7f6f2;
            color: #1f2937;
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            font-size: 15px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        a { color: #059669; text-decoration: none; }
        a:hover { text-decoration: underline; }

        .wrap {
            max-width: 672px;
            margin: 0 auto;
            min-height: 100vh;
            min-height: 100dvh;
            background: #f7f6f2;
            padding-bottom: max(64px, env(safe-area-inset-bottom, 0px));
        }

        /* Header — clear status bar / notch so top links are tappable */
        .top-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: max(10px, env(safe-area-inset-top, 0px)) 12px 10px;
            min-height: 56px;
            border-bottom: 1px solid rgba(229, 231, 235, 0.6);
            background: rgba(247, 246, 242, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .top-logo {
            font-weight: 600;
            font-size: 16px;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.01em;
            text-decoration: none;
            min-height: 44px;
            padding: 8px 4px 8px 0;
            -webkit-tap-highlight-color: transparent;
            flex-shrink: 0;
        }
        .top-logo:hover { text-decoration: none; }
        .top-logo .logo-mark {
            width: 28px;
            height: 28px;
            border-radius: 9999px;
            background: #059669;
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 13px;
            font-weight: 600;
            flex-shrink: 0;
        }
        .top-subtitle {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 9999px;
            transition: background 0.15s, color 0.15s;
        }
        .top-subtitle:hover {
            background: rgba(229, 231, 235, 0.6);
            color: #111827;
            text-decoration: none;
        }

        /* Feed */
        .feed-container {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Post card */
        .post {
            background: #ffffff;
            border: 1px solid rgba(229, 231, 235, 0.8);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            gap: 12px;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            transition: box-shadow 0.2s ease;
        }
        .post:hover {
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        }

        .avatar {
            flex: none;
            width: 36px;
            height: 36px;
            border-radius: 9999px;
            display: grid;
            place-items: center;
            font-weight: 600;
            color: #fff;
            background: #059669;
            font-size: 14px;
        }
        .kind-author    { background: #059669; }
        .kind-speaker   { background: #d97706; }
        .kind-anonymous { background: #6b7280; }
        .kind-prophet   { background: #7c3aed; }
        .kind-priest    { background: #0891b2; }
        .kind-king      { background: #ea580c; }

        .body { min-width: 0; flex: 1; }

        .head {
            display: flex;
            flex-wrap: wrap;
            gap: 2px 6px;
            align-items: baseline;
            margin-bottom: 2px;
        }
        .name {
            font-weight: 600;
            color: #111827;
            letter-spacing: -0.01em;
        }
        .handle {
            color: #9ca3af;
            font-size: 14px;
        }
        .ref {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }
        .muted { color: #6b7280; font-size: 14px; }

        .heading-text {
            margin: 4px 0 0;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #9ca3af;
        }

        .text {
            margin: 10px 0 14px;
            font-size: 15px;
            line-height: 1.65;
            overflow-wrap: anywhere;
            color: #1f2937;
            font-family: Georgia, 'Times New Roman', 'Palatino Linotype', serif;
        }

        .quote {
            display: block;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 14px;
            margin: 10px 0;
            background: #f7f6f2;
            color: inherit;
            transition: border-color 0.15s;
        }
        .quote:hover { border-color: #059669; text-decoration: none; }
        .quote .text { font-size: 14px; margin-bottom: 0; margin-top: 4px; }
        .quote .head { margin-bottom: 0; }

        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 0 0 12px;
        }
        .tag {
            color: #047857;
            font-size: 12px;
            background: #d1fae5;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 500;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        details.basis { font-size: 13px; }
        details.basis summary {
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 9999px;
            padding: 2px 10px 2px 8px;
            font-size: 12px;
            font-weight: 500;
            list-style: none;
            border: none;
        }
        details.basis summary::-webkit-details-marker { display: none; }
        details.basis summary::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            flex-shrink: 0;
        }

        details.basis.basis-stated summary,
        details.basis.basis-stated_in_text summary {
            color: #047857;
            background: #ecfdf5;
        }
        details.basis.basis-stated summary::before,
        details.basis.basis-stated_in_text summary::before {
            background: #10b981;
        }

        details.basis.basis-traditional summary {
            color: #b45309;
            background: #fffbeb;
        }
        details.basis.basis-traditional summary::before {
            background: #f59e0b;
        }

        details.basis.basis-speaker summary,
        details.basis.basis-speaker_not_author summary {
            color: #047857;
            background: #ecfdf5;
        }
        details.basis.basis-speaker summary::before,
        details.basis.basis-speaker_not_author summary::before {
            background: #10b981;
        }

        details.basis.basis-unattributed summary {
            color: #475569;
            background: #f1f5f9;
        }
        details.basis.basis-unattributed summary::before {
            background: #94a3b8;
        }

        details.basis p {
            margin: 8px 0 0;
            font-size: 12px;
            line-height: 1.5;
            color: #9ca3af;
            max-width: 42ch;
        }

        .share {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border: none;
            border-radius: 6px;
            background: transparent;
            color: #9ca3af;
            font: inherit;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: color 0.15s, background 0.15s;
        }
        .share:hover {
            color: #6b7280;
            background: #f3f4f6;
        }

        .echo {
            font-size: 13px;
            color: #6b7280;
            margin-top: 8px;
        }
        .echo a { font-weight: 500; }

        .profile {
            padding: 24px;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(229, 231, 235, 0.8);
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }
        .profile h1 {
            margin: 0 0 4px;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -0.02em;
            color: #111827;
        }
        .profile .muted { font-size: 14px; color: #6b7280; }
        .profile .bio {
            margin: 10px 0 0;
            line-height: 1.55;
            color: #1f2937;
            font-size: 15px;
        }

        .empty {
            padding: 28px 22px;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(229, 231, 235, 0.8);
            text-align: center;
            color: #6b7280;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }

        .back-row { padding: 12px 16px 0; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            padding: 6px 12px;
            border-radius: 9999px;
            transition: background 0.15s, color 0.15s;
        }
        .back-link:hover {
            background: #e5e7eb;
            color: #111827;
            text-decoration: none;
        }

        .more-wrap { padding: 8px 16px 16px; text-align: center; }
        .more {
            display: block;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 9999px;
            color: #059669;
            font-weight: 600;
            text-decoration: none;
            background: #ffffff;
            transition: background 0.2s, border-color 0.2s;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }
        .more:hover {
            background: #f3f4f6;
            border-color: #059669;
            text-decoration: none;
        }
        .more[aria-busy="true"] { opacity: .55; pointer-events: none; }


        /* Hero */
        .hero {
            text-align: center;
            padding: 48px 16px 32px;
        }
        .hero-eyebrow {
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 12px;
        }
        .hero-title {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 1.875rem;
            font-weight: 500;
            color: #111827;
            line-height: 1.25;
            margin-bottom: 16px;
            letter-spacing: -0.01em;
        }
        @media (min-width: 640px) {
            .hero-title { font-size: 2.25rem; }
        }
        .hero-sub {
            font-size: 14px;
            color: #6b7280;
            max-width: 28rem;
            margin: 0 auto;
            line-height: 1.6;
        }
        .hero-br { display: none; }
        @media (min-width: 640px) {
            .hero-br { display: inline; }
        }

        /* Filters */
        .filters {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 16px 40px;
        }
        .filter-btn {
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #4b5563;
            transition: background 0.15s, border-color 0.15s, color 0.15s, box-shadow 0.15s;
        }
        .filter-btn:hover {
            border-color: #d1d5db;
            background: #f9fafb;
        }
        .filter-btn.is-active {
            background: #111827;
            color: #ffffff;
            border-color: #111827;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }


        /* Accounts, library, notes */
        .top-right { display: flex; align-items: center; gap: 4px; min-width: 0; }
        .top-nav { display: flex; align-items: center; gap: 2px; flex-wrap: wrap; justify-content: flex-end; }
        .top-nav a {
            color: #4b5563; font-size: 14px; font-weight: 500;
            padding: 10px 12px; min-height: 44px;
            display: inline-flex; align-items: center;
            border-radius: 9999px;
            -webkit-tap-highlight-color: transparent;
        }
        .top-nav a:hover { background: rgba(229, 231, 235, 0.6); color: #111827; text-decoration: none; }
        .top-nav .nav-cta { background: #059669; color: #fff; }
        .top-nav .nav-cta:hover { background: #047857; color: #fff; }
        @media (max-width: 520px) { .top-subtitle { display: none; } }

        .flash { margin: 12px 16px 0; padding: 10px 14px; border-radius: 12px; background: #ecfdf5; color: #047857; font-size: 14px; }

        .auth-card { margin: 24px 16px; padding: 24px; background: #fff; border: 1px solid rgba(229, 231, 235, 0.8); border-radius: 16px; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); }
        .auth-card h1 { font-size: 22px; font-weight: 600; letter-spacing: -0.02em; color: #111827; margin-bottom: 4px; }
        .auth-card .lede { color: #6b7280; font-size: 14px; margin-bottom: 18px; }
        .policy-h { font-size: 15px; font-weight: 600; color: #111827; margin: 18px 0 4px; }
        .auth-card p { margin-bottom: 8px; font-size: 14px; color: #374151; }
        .field { margin-bottom: 14px; }
        .field label { display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 4px; }
        .field input[type=text], .field input[type=email], .field input[type=password], .note-box textarea {
            width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 10px;
            font: inherit; font-size: 15px; background: #fff; color: #111827;
        }
        .field input:focus, .note-box textarea:focus { outline: 2px solid #a7f3d0; border-color: #059669; }
        .field-error { color: #b91c1c; font-size: 13px; margin-top: 4px; }
        .check { display: flex; gap: 8px; align-items: flex-start; font-size: 14px; color: #374151; margin-bottom: 14px; }
        .btn { display: inline-block; padding: 10px 18px; border: none; border-radius: 9999px; background: #059669; color: #fff; font: inherit; font-size: 15px; font-weight: 600; cursor: pointer; }
        .btn:hover { background: #047857; color: #fff; text-decoration: none; }
        .btn-ghost { background: #fff; color: #374151; border: 1px solid #e5e7eb; }
        .btn-ghost:hover { background: #f3f4f6; color: #111827; }
        .btn-danger { background: #b91c1c; }
        .btn-danger:hover { background: #991b1b; }
        .account-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .auth-links { margin-top: 16px; font-size: 14px; display: flex; gap: 14px; flex-wrap: wrap; }
        .hp { position: absolute; left: -9999px; height: 0; overflow: hidden; }

        .tabs { display: flex; gap: 8px; padding: 16px 16px 0; flex-wrap: wrap; }
        .tabs .filter-btn { text-decoration: none; }
        .pager-row { display: flex; justify-content: space-between; padding: 4px 4px 0; font-size: 14px; }
        .note-item { background: #fff; border: 1px solid rgba(229, 231, 235, 0.8); border-radius: 16px; padding: 18px 20px; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); }
        .note-item .note-body { white-space: pre-wrap; margin: 8px 0 10px; font-size: 15px; line-height: 1.6; overflow-wrap: anywhere; }
        .note-box { margin: 0 16px 16px; background: #fff; border: 1px solid rgba(229, 231, 235, 0.8); border-radius: 16px; padding: 20px; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); }
        .feed-container .note-box { margin: 0; }
        .note-box h2 { font-size: 16px; font-weight: 600; color: #111827; }
        .note-box textarea { margin: 10px 0; resize: vertical; font-family: Georgia, 'Times New Roman', serif; line-height: 1.6; }
        .note-actions { display: flex; align-items: center; gap: 12px; }

        .action-btns { display: flex; align-items: center; gap: 2px; flex-wrap: wrap; }
        a.share, a.share:hover { text-decoration: none; }
        .save.is-saved { color: #047857; }
        .save.is-saved svg { fill: currentColor; }

        /* Voice profile */
        .profile-facts { margin-top: 16px; }
        .fact-row { display: flex; gap: 14px; padding: 10px 0; border-top: 1px solid #f3f4f6; }
        .fact-label { width: 92px; flex: none; font-size: 12px; font-weight: 500; letter-spacing: 0.05em; text-transform: uppercase; color: #9ca3af; padding-top: 3px; }
        .fact-body { flex: 1; min-width: 0; }
        .fact { margin-bottom: 8px; font-size: 15px; color: #1f2937; }
        .fact:last-child { margin-bottom: 0; }
        .fact-meta { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; margin-top: 2px; font-size: 12px; color: #9ca3af; }
        .pill { display: inline-block; padding: 1px 8px; border-radius: 9999px; font-size: 11px; font-weight: 500; }
        .pill-stated_in_text { background: #ecfdf5; color: #047857; }
        .pill-traditional { background: #fffbeb; color: #b45309; }
        .pill-scholarly_estimate { background: #eff6ff; color: #1d4ed8; }
        .pill-speaker_only { background: #ecfdf5; color: #047857; }
        .pill-unattributed { background: #f1f5f9; color: #475569; }
        .fact-note { margin-top: 2px; font-size: 12px; color: #9ca3af; line-height: 1.5; max-width: 46ch; }
        .not-recorded { color: #9ca3af; font-size: 14px; font-style: italic; }
        .profile-caveat { margin-top: 14px; font-size: 12px; color: #9ca3af; line-height: 1.5; }
        @media (max-width: 520px) { .fact-row { flex-direction: column; gap: 4px; } .fact-label { width: auto; } }


        /* Comments, suggestions, moderation */
        .field select { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 10px; font: inherit; font-size: 15px; background: #fff; color: #111827; }
        .field input[type=number] { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 10px; font: inherit; font-size: 15px; background: #fff; color: #111827; }
        .field select:focus, .field input[type=number]:focus, .field textarea:focus { outline: 2px solid #a7f3d0; border-color: #059669; }
        .field textarea { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 10px; font: inherit; font-size: 15px; background: #fff; color: #111827; resize: vertical; }
        .ref-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        @media (max-width: 480px) { .ref-grid { grid-template-columns: 1fr; } }
        .comment { padding: 12px 0; border-top: 1px solid #f3f4f6; }
        .comment-body { margin: 4px 0 4px; font-size: 15px; line-height: 1.6; white-space: pre-wrap; overflow-wrap: anywhere; }
        .comment-mine { background: #fafaf9; border-radius: 10px; padding: 12px; margin-top: 8px; border-top: none; }
        .inline-form { display: inline; }
        .link-btn { background: none; border: none; padding: 0; font: inherit; font-size: 12px; color: #9ca3af; cursor: pointer; }
        .link-btn:hover { color: #6b7280; text-decoration: underline; }
        .correction-summary { cursor: pointer; font-size: 14px; font-weight: 500; color: #4b5563; }
        details.note-box[open] .correction-summary { margin-bottom: 4px; color: #111827; }
        .admin-h { font-size: 14px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; color: #6b7280; margin: 8px 4px 0; }
        .admin-form { margin-top: 12px; padding-top: 12px; border-top: 1px solid #f3f4f6; }
        footer {
            padding: 28px 20px;
            color: #9ca3af;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="wrap">
    <header class="top-header">
        <a class="top-logo" href="{{ route('feed') }}">
            <span class="logo-mark">✦</span>
            {{ config('app.name') }}
        </a>
        <div class="top-right">
            <span class="top-subtitle">@yield('subtitle', 'Public feed')</span>
            <nav class="top-nav">
                <a href="{{ route('search') }}">Search</a>
                @auth
                    @if (auth()->user()->is_admin)
                        @php $queue = \App\Models\Comment::where('status', 'pending')->count() + \App\Models\Suggestion::where('status', 'pending')->count(); @endphp
                        <a href="{{ route('admin') }}">Admin{{ $queue ? ' (' . $queue . ')' : '' }}</a>
                    @endif
                    <a href="{{ route('library') }}">My library</a>
                    <a href="{{ route('account') }}">Account</a>
                @else
                    <a href="{{ route('login') }}">Sign in</a>
                    <a class="nav-cta" href="{{ route('register') }}">Join</a>
                @endauth
            </nav>
        </div>
    </header>

    @if (session('status'))
        <div class="flash" role="status">{{ session('status') }}</div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer>Scripture text: World English Bible, public domain. · <a href="{{ route('privacy') }}">Privacy</a> · <a href="{{ route('guidelines') }}">Guidelines</a> · <a href="{{ route('how') }}">How To Read</a></footer>
</div>

<script>
document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-share]');
    if (!btn) return;
    e.preventDefault();

    var data = { title: btn.dataset.title, text: btn.dataset.text, url: btn.dataset.url };
    var label = btn.querySelector('.share-label');

    function flash(message) {
        var old = label.textContent;
        label.textContent = message;
        setTimeout(function () { label.textContent = old; }, 1800);
    }

    function copy() {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(data.url).then(
                function () { flash('Link copied'); },
                function () { window.prompt('Copy this link', data.url); }
            );
        } else {
            window.prompt('Copy this link', data.url);
        }
    }

    if (navigator.share) {
        navigator.share(data).catch(function (err) {
            if (err && err.name !== 'AbortError') copy();
        });
    } else {
        copy();
    }
});

document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-bookmark]');
    if (!btn) return;
    e.preventDefault();
    if (btn.dataset.busy) return;
    btn.dataset.busy = '1';

    var label = btn.querySelector('.save-label');
    var token = document.querySelector('meta[name="csrf-token"]').content;

    fetch(btn.dataset.url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
        .then(function (res) {
            if (res.status === 401 || res.status === 419) { window.location = '{{ route('login') }}'; throw new Error('auth'); }
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function (data) {
            btn.classList.toggle('is-saved', data.saved);
            btn.setAttribute('aria-pressed', data.saved ? 'true' : 'false');
            label.textContent = data.saved ? 'Saved' : 'Save';
        })
        .catch(function () {})
        .then(function () { delete btn.dataset.busy; });
});
</script>
</body>
</html>
