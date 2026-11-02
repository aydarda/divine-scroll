<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Note;
use App\Models\Passage;
use App\Models\Person;
use App\Support\FeedOrder;
use App\Support\PassagePresenter;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    private const PER_PAGE = 10;

    // GET /api/v1/feed?page=1&basis=stated
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $filter = array_key_exists((string) $request->query('basis'), FeedOrder::BASIS) ? $request->query('basis') : null;

        $ids = FeedOrder::ids($filter ? FeedOrder::BASIS[$filter] : null);
        $page = max(1, (int) $request->query('page', 1));
        $slice = $ids->forPage($page, self::PER_PAGE)->values();

        $passages = Passage::with(PassagePresenter::WITH)
            ->whereIn('id', $slice)
            ->get()
            ->sortBy(fn (Passage $p) => $slice->search($p->id))
            ->values();

        $saved = PassagePresenter::savedIds($user);

        return response()->json([
            'data'      => $passages->map(fn ($p) => PassagePresenter::card($p, $saved))->values(),
            'page'      => $page,
            'next_page' => $ids->count() > $page * self::PER_PAGE ? $page + 1 : null,
            'total'     => $ids->count(),
            'filter'    => $filter,
        ]);
    }

    // GET /api/v1/passages/{id}: one passage with its published comments and, if signed in, the user's note.
    public function show(Request $request, Passage $passage)
    {
        abort_unless($passage->status === 'approved' && $passage->attribution()->exists(), 404);

        $user = auth('sanctum')->user();
        $passage->load(PassagePresenter::WITH);

        $comments = Comment::where('passage_id', $passage->id)->approved()->visibleTo($user)->with('user:id,name')->oldest()->limit(200)->get();
        $mine = $user
            ? Comment::where('passage_id', $passage->id)->where('user_id', $user->id)->whereIn('status', ['pending', 'rejected'])->latest()->get()
            : collect();
        $note = $user ? Note::where('user_id', $user->id)->where('passage_id', $passage->id)->first() : null;

        $format = fn (Comment $c) => [
            'id'         => $c->id,
            'author_id'  => $c->user_id,
            'author'     => $c->user->name,
            'body'       => $c->body,
            'status'     => $c->status,
            'created_at' => $c->created_at->toIso8601String(),
            'mine'       => $user && $c->user_id === $user->id,
        ];

        return response()->json([
            'passage'       => PassagePresenter::card($passage, PassagePresenter::savedIds($user)),
            'comments'      => $comments->map($format)->values(),
            'my_comments'   => $mine->map($format)->values(),
            'note'          => $note ? ['body' => $note->body, 'updated_at' => $note->updated_at->toIso8601String()] : null,
            'comments_mode' => config('contributions.comments', 'open'),
        ]);
    }

    // GET /api/v1/voices
    public function voices()
    {
        $voices = Person::query()
            ->withCount(['attributions as passages_count' => fn ($q) => $q->whereHas('passage', fn ($p) => $p->where('status', 'approved'))])
            ->get()
            ->filter(fn ($p) => $p->passages_count > 0)
            ->sortByDesc('passages_count')
            ->values();

        return response()->json([
            'data' => $voices->map(fn ($p) => PassagePresenter::voice($p) + [
                'bio'            => $p->bio,
                'passages_count' => $p->passages_count,
            ]),
        ]);
    }

    // GET /api/v1/voices/{slug}?page=1: the voice's profile and their passages.
    public function voice(Request $request, Person $person)
    {
        $user = auth('sanctum')->user();
        $person->load(['facts', 'relations.relative']);

        $ids = Passage::approved()
            ->whereHas('attribution', fn ($q) => $q->where('person_id', $person->id))
            ->with('book')
            ->get()
            ->sortBy(fn (Passage $p) => sprintf('%02d%03d%03d', $p->book->position, $p->chapter, $p->verse_start))
            ->pluck('id')
            ->values();

        $page = max(1, (int) $request->query('page', 1));
        $slice = $ids->forPage($page, self::PER_PAGE)->values();

        $passages = Passage::with(PassagePresenter::WITH)
            ->whereIn('id', $slice)
            ->get()
            ->sortBy(fn (Passage $p) => $slice->search($p->id))
            ->values();

        $saved = PassagePresenter::savedIds($user);

        return response()->json([
            'voice'     => PassagePresenter::voice($person) + ['bio' => $person->bio],
            'facts'     => $person->facts->map(fn ($f) => [
                'kind'        => $f->kind,
                'value'       => $f->value,
                'year_from'   => $f->year_from,
                'year_to'     => $f->year_to,
                'basis'       => $f->basis,
                'basis_label' => $f->basisLabel(),
                'source'      => $f->source,
                'note'        => $f->note,
            ])->values(),
            'relations' => $person->relations->map(fn ($r) => [
                'relation'    => $r->relation,
                'name'        => $r->related_name,
                'voice_slug'  => $r->relative?->slug,
                'basis'       => $r->basis,
                'basis_label' => $r->basisLabel(),
                'source'      => $r->source,
                'note'        => $r->note,
            ])->values(),
            'passages'  => $passages->map(fn ($p) => PassagePresenter::card($p, $saved))->values(),
            'page'      => $page,
            'next_page' => $ids->count() > $page * self::PER_PAGE ? $page + 1 : null,
            'total'     => $ids->count(),
        ]);
    }
}
