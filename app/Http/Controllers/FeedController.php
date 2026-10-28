<?php

namespace App\Http\Controllers;

use App\Models\Passage;
use App\Models\Person;
use App\Services\ShareCard;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class FeedController extends Controller
{
    private const PER_PAGE = 10;

    // Filter names used in the URL (?basis=stated) and the attribution basis each one means.
    private const BASIS = [
        'stated'       => 'stated_in_text',
        'traditional'  => 'traditional',
        'speaker'      => 'speaker_only',
        'unattributed' => 'unattributed',
    ];

    // 0 = every voice gets the same share of the feed, 1 = share proportional to how many passages it has.
    // Lower values stop big voices (Jesus, David) from drowning out small ones.
    private const VOICE_BALANCE = 0.3;

    private const WITH = [
        'book',
        'attribution',
        'outgoing.to.book',
        'outgoing.to.attribution',
        'incoming.from.book',
        'incoming.from.attribution',
    ];

    // The home feed: all approved passages, in an order that is stable for the whole day
    // and different tomorrow. No AI and no randomness at request time.
    public function index(Request $request)
    {
        $filter = array_key_exists((string) $request->query('basis'), self::BASIS) ? $request->query('basis') : null;
        $ids = $this->dailyOrder($filter ? self::BASIS[$filter] : null);

        $page = max(1, (int) $request->query('page', 1));
        $slice = $ids->forPage($page, self::PER_PAGE)->values();

        $passages = Passage::with(self::WITH)
            ->whereIn('id', $slice)
            ->get()
            ->sortBy(fn (Passage $p) => $slice->search($p->id))
            ->values();

        $posts = new LengthAwarePaginator($passages, $ids->count(), self::PER_PAGE, $page, [
            'path'  => $request->url(),
            'query' => $filter ? ['basis' => $filter] : [],
        ]);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.feed-items', ['posts' => $posts])->render(),
                'next' => $posts->nextPageUrl(),
            ]);
        }

        return view('feed', ['posts' => $posts, 'filter' => $filter]);
    }

    // Passage IDs in the day's feed order: shuffled per voice, then interleaved so each voice's
    // share of the early pages grows with its size more slowly than its size alone would give.
    // Stable for the whole day, different tomorrow, no randomness at request time.
    private function dailyOrder(?string $basis = null): Collection
    {
        $day = now()->toDateString();

        $rows = DB::table('passages')
            ->join('attributions', 'attributions.passage_id', '=', 'passages.id')
            ->where('passages.status', 'approved')
            ->when($basis, fn ($q) => $q->where('attributions.basis', $basis))
            ->get(['passages.id', 'attributions.person_id']);

        return $rows->groupBy('person_id')
            ->flatMap(function (Collection $group) use ($day) {
                $density = $group->count() ** self::VOICE_BALANCE;

                return $group
                    ->sortBy(fn ($r) => crc32("{$day}:{$r->id}"))
                    ->values()
                    ->map(fn ($r, $i) => [
                        'id'  => $r->id,
                        'key' => ($i + (crc32("j{$day}:{$r->id}") % 1000) / 1000) / $density,
                    ]);
            })
            ->sortBy('key')
            ->pluck('id')
            ->values();
    }

    // One voice's profile: every approved passage they posted, in Bible order.
    public function person(Person $person)
    {
        $posts = Passage::approved()
            ->whereHas('attribution', fn ($q) => $q->where('person_id', $person->id))
            ->with(self::WITH)
            ->get()
            ->sortBy(fn (Passage $p) => sprintf('%02d%03d%03d', $p->book->position, $p->chapter, $p->verse_start))
            ->values();

        return view('person', ['person' => $person, 'posts' => $posts]);
    }

    // Permalink for a single passage.
    public function show(Passage $passage)
    {
        abort_unless($passage->status === 'approved' && $passage->attribution()->exists(), 404);

        $passage->load(self::WITH);
        $who = $passage->attribution->person;

        return view('passage', [
            'passage'    => $passage,
            'shareTitle' => "{$who->name} · {$passage->reference}",
            'shareText'  => Str::limit($passage->body, 200),
            'cardUrl'    => ShareCard::available() ? route('passage.card', $passage) : null,
        ]);
    }

    // The 1200x630 preview image used when a passage link is shared.
    public function card(Passage $passage, ShareCard $cards)
    {
        abort_unless(
            $passage->status === 'approved' && $passage->attribution()->exists() && ShareCard::available(),
            404
        );

        $passage->load(['book', 'attribution']);

        return response()->file($cards->path($passage), [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
