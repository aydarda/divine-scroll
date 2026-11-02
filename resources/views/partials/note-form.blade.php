<section class="note-box" id="note">
    @auth
        @php $note = \App\Models\Note::where('user_id', auth()->id())->where('passage_id', $passage->id)->first(); @endphp
        <h2>Your private note</h2>
        <p class="muted">Only you can see this. Clear the box and save to delete it.</p>
        <form method="POST" action="{{ route('note.save', $passage) }}">
            @csrf
            <textarea name="body" rows="5" maxlength="5000" placeholder="Write a note about this passage…">{{ old('body', $note?->body) }}</textarea>
            @error('body') <div class="field-error">{{ $message }}</div> @enderror
            <div class="note-actions">
                <button class="btn" type="submit">Save note</button>
                @if ($note)<span class="muted">Last saved {{ $note->updated_at->diffForHumans() }}</span>@endif
            </div>
        </form>
    @else
        <p class="muted"><a href="{{ route('login') }}">Sign in</a> or <a href="{{ route('register') }}">create an account</a> to save this passage and write private notes.</p>
    @endauth
</section>
