<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Passage;
use App\Models\Person;
use App\Models\Suggestion;
use App\Models\Verse;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    private const BASES = ['stated_in_text', 'traditional', 'speaker_only', 'unattributed'];

    public function create()
    {
        return view('suggest', [
            'books'   => Book::orderBy('position')->get(),
            'persons' => Person::orderBy('name')->get(),
        ]);
    }

    // Suggest a passage that isn't in the app yet.
    public function storePassage(Request $request)
    {
        $data = $request->validate([
            'book_id'      => ['required', 'exists:books,id'],
            'chapter'      => ['required', 'integer', 'min:1', 'max:150'],
            'verse_start'  => ['required', 'integer', 'min:1', 'max:200'],
            'verse_end'    => ['required', 'integer', 'gte:verse_start', 'max:200'],
            'person_id'    => ['nullable', 'exists:persons,id'],
            'person_other' => ['nullable', 'string', 'max:80'],
            'source'       => ['nullable', 'string', 'max:200'],
            'reason'       => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        if ($data['verse_end'] - $data['verse_start'] > 11) {
            return back()->withErrors(['verse_end' => 'Please suggest 12 verses or fewer at a time.'])->withInput();
        }

        $found = Verse::where('translation', 'WEB')
            ->where('book_id', $data['book_id'])
            ->where('chapter', $data['chapter'])
            ->whereBetween('verse', [$data['verse_start'], $data['verse_end']])
            ->where('text', '<>', '')
            ->count();

        if ($found !== $data['verse_end'] - $data['verse_start'] + 1) {
            return back()->withErrors(['verse_end' => 'That reference doesn’t exist in this text.'])->withInput();
        }

        $existing = Passage::with('book')
            ->where('book_id', $data['book_id'])
            ->where('chapter', $data['chapter'])
            ->where('verse_start', $data['verse_start'])
            ->where('verse_end', $data['verse_end'])
            ->first();

        if ($existing) {
            return back()->withErrors(['verse_end' => "That passage is already in the app: {$existing->reference}."])->withInput();
        }

        Suggestion::create([
            'user_id'             => $request->user()->id,
            'type'                => 'passage',
            'book_id'             => $data['book_id'],
            'chapter'             => $data['chapter'],
            'verse_start'         => $data['verse_start'],
            'verse_end'           => $data['verse_end'],
            'suggested_person_id' => $data['person_id'] ?? null,
            'person_other'        => $data['person_other'] ?? null,
            'source'              => $data['source'] ?? null,
            'reason'              => trim($data['reason']),
        ]);

        return redirect()->route('library', ['tab' => 'contributions'])
            ->with('status', 'Thanks. Your suggestion is waiting for review.');
    }

    // Suggest that a passage's voice or attribution badge is wrong.
    public function storeCorrection(Request $request, Passage $passage)
    {
        abort_unless($passage->status === 'approved', 404);

        $data = $request->validate([
            'person_id'       => ['nullable', 'exists:persons,id'],
            'person_other'    => ['nullable', 'string', 'max:80'],
            'suggested_basis' => ['nullable', 'in:' . implode(',', self::BASES)],
            'source'          => ['nullable', 'string', 'max:200'],
            'reason'          => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        if (empty($data['person_id']) && empty($data['person_other']) && empty($data['suggested_basis'])) {
            return back()->withErrors(['reason' => 'Say who the voice should be, or what the badge should say.'])->withInput();
        }

        Suggestion::create([
            'user_id'             => $request->user()->id,
            'type'                => 'attribution',
            'passage_id'          => $passage->id,
            'suggested_person_id' => $data['person_id'] ?? null,
            'person_other'        => $data['person_other'] ?? null,
            'suggested_basis'     => $data['suggested_basis'] ?? null,
            'source'              => $data['source'] ?? null,
            'reason'              => trim($data['reason']),
        ]);

        return redirect(route('passage', $passage))
            ->with('status', 'Thanks. Your correction is waiting for review.');
    }
}
