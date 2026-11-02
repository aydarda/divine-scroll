@extends('layouts.app')

@section('title', 'Privacy')
@section('subtitle', 'Privacy')

@section('content')
    <div class="auth-card">
        <h1>Privacy policy</h1>
        <p class="lede"><strong>DRAFT. Fill in the brackets and have a lawyer review this before you rely on it.</strong></p>

        <h2 class="policy-h">Who we are</h2>
        <p>{{ config('app.name') }} is run by [your name or company], [your contact email].</p>

        <h2 class="policy-h">What we collect</h2>
        <p>Without an account, we collect nothing about you beyond the technical records every web server keeps, such as your IP address and the pages requested.</p>
        <p>With an account, we store your name, your email address, a scrambled (hashed) version of your password, the passages you save, and the notes you write. If you comment or send a suggestion, we also store what you wrote.</p>
        <p>Comments are public once a moderator approves them, and they appear with your account name. Suggestions are seen only by moderators.</p>

        <h2 class="policy-h">How we use it</h2>
        <p>Only to run your account: to sign you in, show your saved passages and notes, and send a password-reset email if you ask for one. Your notes and saved passages are private. We don’t sell your data or use it for advertising.</p>

        <h2 class="policy-h">Cookies</h2>
        <p>We use a session cookie and a security (CSRF) cookie so you can stay signed in and submit forms safely. We use no advertising or tracking cookies. [Update this if you add analytics.]</p>

        <h2 class="policy-h">Other companies</h2>
        <p>Page fonts are loaded from Google Fonts, so Google receives your IP address when a page loads. [Remove this line if you host the fonts yourself.] Our hosting provider is [provider].</p>

        <h2 class="policy-h">Your choices</h2>
        <p>You can download everything we hold about you, or delete your account and all of it, including your comments and suggestions, from your account page. [Say how long server backups keep deleted data, for example up to 14 days.]</p>

        <h2 class="policy-h">Contact</h2>
        <p>Questions or requests: [your contact email].</p>
    </div>
@endsection
