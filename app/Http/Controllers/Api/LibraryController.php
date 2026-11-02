<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Passage;
use App\Support\PassagePresenter;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    // GET /api/v1/library/saved?page=1
    public function saved(Request $request)
    {
        $items = $request->user()->bookmarkedPassages()
            ->approved()
            ->with(PassagePresenter::WITH)
            ->orderByPivot('created_at', 'desc')
            ->paginate(10);

        $everySaved = array_flip($items->pluck('id')->all());

        return response()->json([
            'data'      => $items->getCollection()->map(fn ($p) => PassagePresenter::card($p, $everySaved))->values(),
            'page'      => $items->currentPage(),
            'next_page' => $items->hasMorePages() ? $items->currentPage() + 1 : null,
            'total'     => $items->total(),
        ]);
    }

    // POST /api/v1/passages/{id}/save: toggles. Returns whether it is saved now.
    public function toggle(Request $request, Passage $passage)
    {
        abort_unless($passage->status === 'approved', 404);

        $user = $request->user();
        $saved = $user->bookmarkedPassages()->where('passages.id', $passage->id)->exists();

        $saved
            ? $user->bookmarkedPassages()->detach($passage->id)
            : $user->bookmarkedPassages()->attach($passage->id);

        return response()->json(['saved' => ! $saved]);
    }

    // PUT /api/v1/passages/{id}/save: makes sure the passage is saved (safe to repeat).
    public function save(Request $request, Passage $passage)
    {
        abort_unless($passage->status === 'approved', 404);

        $request->user()->bookmarkedPassages()->syncWithoutDetaching([$passage->id]);

        return response()->json(['saved' => true]);
    }

    // DELETE /api/v1/passages/{id}/save: makes sure it is not saved (safe to repeat).
    public function unsave(Request $request, Passage $passage)
    {
        $request->user()->bookmarkedPassages()->detach($passage->id);

        return response()->json(['saved' => false]);
    }

    // DELETE /api/v1/passages/{id}/note
    public function deleteNote(Request $request, Passage $passage)
    {
        Note::where('user_id', $request->user()->id)->where('passage_id', $passage->id)->delete();

        return response()->json(['note' => null]);
    }

    // GET /api/v1/library/notes?page=1
    public function notes(Request $request)
    {
        $items = Note::where('user_id', $request->user()->id)
            ->whereHas('passage', fn ($q) => $q->where('status', 'approved'))
            ->with(['passage.book', 'passage.attribution.person'])
            ->latest('updated_at')
            ->paginate(10);

        return response()->json([
            'data'      => $items->getCollection()->map(fn ($n) => [
                'passage_id' => $n->passage_id,
                'reference'  => $n->passage->reference,
                'voice'      => $n->passage->attribution ? PassagePresenter::voice($n->passage->attribution->person) : null,
                'body'       => $n->body,
                'updated_at' => $n->updated_at->toIso8601String(),
            ])->values(),
            'page'      => $items->currentPage(),
            'next_page' => $items->hasMorePages() ? $items->currentPage() + 1 : null,
            'total'     => $items->total(),
        ]);
    }

    // PUT /api/v1/passages/{id}/note  { "body": "..." }  (an empty body deletes the note)
    public function note(Request $request, Passage $passage)
    {
        abort_unless($passage->status === 'approved', 404);

        $data = $request->validate(['body' => ['nullable', 'string', 'max:5000']]);
        $body = trim($data['body'] ?? '');
        $userId = $request->user()->id;

        if ($body === '') {
            Note::where('user_id', $userId)->where('passage_id', $passage->id)->delete();

            return response()->json(['note' => null]);
        }

        $note = Note::updateOrCreate(['user_id' => $userId, 'passage_id' => $passage->id], ['body' => $body]);

        return response()->json(['note' => ['body' => $note->body, 'updated_at' => $note->updated_at->toIso8601String()]]);
    }
}
