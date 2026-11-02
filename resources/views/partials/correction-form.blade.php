@auth
    @php $persons = \App\Models\Person::orderBy('name')->get(); @endphp
    <details class="note-box" id="correction">
        <summary class="correction-summary">Suggest a correction to this passage’s voice or badge</summary>
        <p class="muted" style="margin-top:8px">A moderator reviews every suggestion. Cite a verse if you can.</p>

        <form method="POST" action="{{ route('correction.store', $passage) }}" style="margin-top:10px">
            @csrf
            <div class="field">
                <label for="c-person">The voice should be</label>
                <select id="c-person" name="person_id">
                    <option value="">No change / not sure</option>
                    @foreach ($persons as $p)
                        <option value="{{ $p->id }}" @selected(old('person_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="c-other">…or someone not in the list</label>
                <input id="c-other" type="text" name="person_other" maxlength="80" value="{{ old('person_other') }}">
            </div>
            <div class="field">
                <label for="c-basis">The badge should say</label>
                <select id="c-basis" name="suggested_basis">
                    <option value="">No change / not sure</option>
                    <option value="stated_in_text">Stated in text</option>
                    <option value="traditional">Traditional attribution</option>
                    <option value="speaker_only">Speaker, not author</option>
                    <option value="unattributed">Unattributed</option>
                </select>
            </div>
            <div class="field">
                <label for="c-source">Supporting verse (optional)</label>
                <input id="c-source" type="text" name="source" maxlength="200" placeholder="e.g. Acts 4:25" value="{{ old('source') }}">
            </div>
            <div class="field">
                <label for="c-reason">Why?</label>
                <textarea id="c-reason" name="reason" rows="3" maxlength="1000" required>{{ old('reason') }}</textarea>
                @error('reason') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <button class="btn" type="submit">Send suggestion</button>
        </form>
    </details>
@endauth
