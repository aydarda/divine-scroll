@extends('layouts.app')

@section('title', 'Suggest a passage')
@section('subtitle', 'Suggest')

@section('content')
    <div class="auth-card">
        <h1>Suggest a passage</h1>
        <p class="lede">Know a passage that belongs here? Tell us where it is and who is speaking. A moderator reviews every suggestion, and you can follow its status in your library.</p>

        <form method="POST" action="{{ route('suggest.store') }}">
            @csrf
            <div class="field">
                <label for="book_id">Book</label>
                <select id="book_id" name="book_id" required>
                    <option value="">Choose a book…</option>
                    @foreach ($books as $book)
                        <option value="{{ $book->id }}" @selected(old('book_id') == $book->id)>{{ $book->name }}</option>
                    @endforeach
                </select>
                @error('book_id') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="ref-grid">
                <div class="field">
                    <label for="chapter">Chapter</label>
                    <input id="chapter" type="number" name="chapter" min="1" max="150" value="{{ old('chapter') }}" required>
                    @error('chapter') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label for="verse_start">From verse</label>
                    <input id="verse_start" type="number" name="verse_start" min="1" max="200" value="{{ old('verse_start') }}" required>
                    @error('verse_start') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label for="verse_end">To verse</label>
                    <input id="verse_end" type="number" name="verse_end" min="1" max="200" value="{{ old('verse_end') }}" required>
                    @error('verse_end') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="field">
                <label for="person_id">Who is speaking or writing?</label>
                <select id="person_id" name="person_id">
                    <option value="">Not sure</option>
                    @foreach ($persons as $p)
                        <option value="{{ $p->id }}" @selected(old('person_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="person_other">…or someone not in the list</label>
                <input id="person_other" type="text" name="person_other" maxlength="80" value="{{ old('person_other') }}">
            </div>
            <div class="field">
                <label for="source">Where does the text say so? (optional)</label>
                <input id="source" type="text" name="source" maxlength="200" placeholder="e.g. the heading, or Acts 4:25" value="{{ old('source') }}">
            </div>
            <div class="field">
                <label for="reason">Why should it be included?</label>
                <textarea id="reason" name="reason" rows="4" maxlength="1000" required>{{ old('reason') }}</textarea>
                @error('reason') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <button class="btn" type="submit">Send suggestion</button>
        </form>
    </div>
@endsection
