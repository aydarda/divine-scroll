@php
    $attr = $passage->attribution;
    $who = $attr?->person;
@endphp

@if ($who)
<article class="post">
    <a class="avatar kind-{{ $who->kind }}" href="{{ route('person', $who) }}" aria-hidden="true" tabindex="-1">{{ mb_substr($who->name, 0, 1) }}</a>

    <div class="body">
        <div class="head">
            <a class="name" href="{{ route('person', $who) }}">{{ $who->name }}</a>
            <span class="handle">{{ $who->handle }}</span>
            <span class="handle">·</span>
            <a class="ref" href="{{ route('passage', $passage) }}">{{ $passage->reference }}</a>
        </div>

        @if ($passage->heading)
            <p class="heading-text">{{ $passage->heading }}</p>
        @endif

        <p class="text">{{ $passage->body }}</p>

        @php
            $outgoing = $passage->outgoing
                ->filter(fn ($l) => $l->to && $l->to->status === 'approved' && $l->to->attribution)
                ->sortBy(fn ($l) => $l->type === 'quote' ? 0 : 1)
                ->take(2);
            $incoming = $passage->incoming
                ->filter(fn ($l) => $l->from && $l->from->status === 'approved' && $l->from->attribution)
                ->sortBy(fn ($l) => $l->type === 'quote' ? 0 : 1)
                ->values();
        @endphp

        @foreach ($outgoing as $link)
            <a class="quote" href="{{ route('passage', $link->to) }}">
                <div class="muted" style="font-size: 12px; margin-bottom: 2px;">{{ $link->type === 'quote' ? 'Quoting' : 'Referring to' }}</div>
                <div class="head">
                    <span class="name">{{ $link->to->attribution->person->name }}</span>
                    <span class="handle">{{ $link->to->attribution->person->handle }}</span>
                    <span class="handle">· {{ $link->to->reference }}</span>
                </div>
                @if ($link->to->heading)
                    <p class="heading-text">{{ $link->to->heading }}</p>
                @endif
                <p class="text">{{ $link->to->body }}</p>
            </a>
        @endforeach

        @if (!empty($passage->themes))
            <div class="tag-list">
                @foreach ($passage->themes as $theme)
                    <span class="tag">#{{ $theme }}</span>
                @endforeach
            </div>
        @endif

        <div class="actions">
            <details class="basis basis-{{ $attr->basis }}">
                <summary>{{ $attr->basisLabel() }}</summary>
                @if ($attr->note)
                    <p>{{ $attr->note }}</p>
                @endif
            </details>

            <button type="button" class="share" data-share
                data-url="{{ route('passage', $passage) }}"
                data-title="{{ $who->name }} · {{ $passage->reference }}"
                data-text="“{{ \Illuminate\Support\Str::limit($passage->body, 200) }}” — {{ $who->name }}, {{ $passage->reference }}">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7"/><path d="M16 6l-4-4-4 4"/><path d="M12 2v13"/></svg>
                <span class="share-label">Share</span>
            </button>
        </div>

        @foreach ($incoming->take(3) as $link)
            <div class="echo">
                {{ $link->type === 'quote' ? 'Quoted by' : 'Echoed by' }}
                <a href="{{ route('passage', $link->from) }}">{{ $link->from->attribution->person->name }}</a>
                in {{ $link->from->reference }}
            </div>
        @endforeach
        @if ($incoming->count() > 3)
            <div class="echo">and {{ $incoming->count() - 3 }} more</div>
        @endif
    </div>
</article>
@endif
