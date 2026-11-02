@php
    $approved = \App\Models\Comment::where('passage_id', $passage->id)->approved()->visibleTo(auth()->user())->with('user')->oldest()->get();
    $mine = auth()->check()
        ? \App\Models\Comment::where('passage_id', $passage->id)->where('user_id', auth()->id())->whereIn('status', ['pending', 'rejected'])->latest()->get()
        : collect();
@endphp

@php $mode = config('contributions.comments', 'open'); @endphp

<section class="note-box" id="comments">
    <h2>Comments ({{ $approved->count() }})</h2>
    <p class="muted">
        @if ($mode === 'review')
            Every comment is reviewed before it appears.
        @elseif ($mode === 'off')
            Commenting is switched off for now.
        @else
            Comments appear straight away, and a moderator can remove any of them.
        @endif
        Please read the <a href="{{ route('guidelines') }}">guidelines</a>.
    </p>

    @foreach ($approved as $comment)
        <div class="comment">
            <div class="head">
                <span class="name">{{ $comment->user->name }}</span>
                <span class="handle">· {{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p class="comment-body">{{ $comment->body }}</p>

            @auth
                @if ($comment->user_id === auth()->id())
                    <form class="inline-form" method="POST" action="{{ route('comment.destroy', $comment) }}" onsubmit="return confirm('Delete this comment?');">
                        @csrf @method('DELETE')
                        <button class="link-btn" type="submit">Delete</button>
                    </form>
                @else
                    <form class="inline-form" method="POST" action="{{ route('comment.report', $comment) }}" onsubmit="return confirm('Report this comment to a moderator?');">
                        @csrf
                        <button class="link-btn" type="submit">Report</button>
                    </form>
                    <form class="inline-form" method="POST" action="{{ route('user.block', $comment->user_id) }}" onsubmit="return confirm('Block this person? You will no longer see their comments.');">
                        @csrf
                        <button class="link-btn" type="submit">Block</button>
                    </form>
                @endif
                @if (auth()->user()->is_admin)
                    <form class="inline-form" method="POST" action="{{ route('admin.comment.reject', $comment) }}" onsubmit="return confirm('Remove this comment as moderator?');">
                        @csrf
                        <button class="link-btn" type="submit">Remove (moderator)</button>
                    </form>
                @endif
            @endauth
        </div>
    @endforeach

    @foreach ($mine as $comment)
        <div class="comment comment-mine">
            <div class="head">
                <span class="name">You</span>
                <span class="pill {{ $comment->status === 'pending' ? 'pill-traditional' : 'pill-scholarly_estimate' }}">{{ $comment->status === 'pending' ? 'Waiting for review' : 'Not published' }}</span>
            </div>
            <p class="comment-body">{{ $comment->body }}</p>
            <form class="inline-form" method="POST" action="{{ route('comment.destroy', $comment) }}" onsubmit="return confirm('Delete this comment?');">
                @csrf @method('DELETE')
                <button class="link-btn" type="submit">Delete</button>
            </form>
        </div>
    @endforeach

    @if ($mode !== 'off')
        @auth
            <form method="POST" action="{{ route('comment.store', $passage) }}" style="margin-top:14px">
                @csrf
                <textarea name="body" rows="3" maxlength="1000" placeholder="Add a comment (no links)…" required>{{ old('body') }}</textarea>
                @error('body') <div class="field-error">{{ $message }}</div> @enderror
                <div class="note-actions"><button class="btn" type="submit">{{ $mode === 'review' ? 'Submit for review' : 'Post comment' }}</button></div>
            </form>
        @else
            <p class="muted" style="margin-top:12px"><a href="{{ route('login') }}">Sign in</a> to comment.</p>
        @endauth
    @endif
</section>
