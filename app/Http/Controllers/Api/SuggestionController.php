<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Passage;
use App\Models\Person;
use App\Models\Suggestion;
use App\Models\Verse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class SuggestionController extends Controller
{
    public const BASES = [
        'stated_in_text' => 'Stated in text',
        'traditional'    => 'Traditional attribution',
        'speaker_only'   => 'Speaker, not author',
        'unattributed'   => 'Unattributed',
    ];

    // GET /api/v1/suggestions/options: the lists the suggestion forms need.
    public function options()
    {
        return response()->json([
            'books'   => Book::orderBy('position')->get(['id', 'name', 'slug', 'testament']),
            'persons' => Person::orderBy('name')->get(['id', 'slug', 'name']),
            'bases'   => collect(self::BASES)->map(fn ($label, $key) => ['key' => $key, 'label' => $label])->values(),
        ]);
    }

    // POST /api/v1/suggestions  { book_id, chapter, verse_start, verse_end, person_id?, person_other?, source?, reason }
    public function store(Request $request)
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
            throw ValidationException::withMessages(['verse_end' => ['Please suggest 12 verses or fewer at a time.']]);
        }

        $found = Verse::where('translation', 'WEB')
            ->where('book_id', $data['book_id'])
            ->where('chapter', $data['chapter'])
            ->whereBetween('verse', [$data['verse_start'], $data['verse_end']])
            ->where('text', '<>', '')
            ->count();

        if ($found !== $data['verse_end'] - $data['verse_start'] + 1) {
            throw ValidationException::withMessages(['verse_end' => ['That reference doesn’t exist in this text.']]);
        }

        $existing = Passage::with('book')
            ->where('book_id', $data['book_id'])
            ->where('chapter', $data['chapter'])
            ->where('verse_start', $data['verse_start'])
            ->where('verse_end', $data['verse_end'])
            ->first();

        if ($existing) {
            throw ValidationException::withMessages(['verse_end' => ["That passage is already in the app: {$existing->reference}."]]);
        }

        $suggestion = Suggestion::create([
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

        return response()->json(['suggestion' => self::present($suggestion->load(['book', 'passage.book']))], 201);
    }

    // POST /api/v1/passages/{id}/correction  { person_id?, person_other?, suggested_basis?, source?, reason }
    public function correction(Request $request, Passage $passage)
    {
        abort_unless($passage->status === 'approved', 404);

        $data = $request->validate([
            'person_id'       => ['nullable', 'exists:persons,id'],
            'person_other'    => ['nullable', 'string', 'max:80'],
            'suggested_basis' => ['nullable', 'in:' . implode(',', array_keys(self::BASES))],
            'source'          => ['nullable', 'string', 'max:200'],
            'reason'          => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        if (empty($data['person_id']) && empty($data['person_other']) && empty($data['suggested_basis'])) {
            throw ValidationException::withMessages(['reason' => ['Say who the voice should be, or what the badge should say.']]);
        }

        $suggestion = Suggestion::create([
            'user_id'             => $request->user()->id,
            'type'                => 'attribution',
            'passage_id'          => $passage->id,
            'suggested_person_id' => $data['person_id'] ?? null,
            'person_other'        => $data['person_other'] ?? null,
            'suggested_basis'     => $data['suggested_basis'] ?? null,
            'source'              => $data['source'] ?? null,
            'reason'              => trim($data['reason']),
        ]);

        return response()->json(['suggestion' => self::present($suggestion->load(['book', 'passage.book']))], 201);
    }

    public static function present(Suggestion $s): array
    {
        return [
            'id'              => $s->id,
            'type'            => $s->type,                 // passage | attribution
            'type_label'      => $s->typeLabel(),
            'passage_id'      => $s->passage_id,
            'reference'       => $s->reference(),
            'suggested_voice' => $s->suggestedPerson?->name ?? $s->person_other,
            'suggested_basis' => $s->suggested_basis,
            'source'          => $s->source,
            'reason'          => $s->reason,
            'status'          => $s->status,               // pending | accepted | declined
            'admin_note'      => $s->admin_note,
            'created_at'      => $s->created_at->toIso8601String(),
        ];
    }

    public static function presentMany(Collection $items): Collection
    {
        return $items->load('suggestedPerson')->map(fn ($s) => self::present($s))->values();
    }
}
