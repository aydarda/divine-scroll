<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Passage;
use App\Support\PassagePresenter;
use App\Support\Search;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private const PER_PAGE = 10;

    // GET /api/v1/search?q=love&page=1   or   ?q=john 3:16
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([
                'query' => $q, 'mode' => 'none', 'voices' => [], 'data' => [],
                'page' => 1, 'next_page' => null, 'total' => 0,
            ]);
        }

        $page = max(1, (int) $request->query('page', 1));
        $result = Search::run($q, $page, self::PER_PAGE);
        $ids = collect($result['ids']);

        $passages = $ids->isEmpty() ? collect() : Passage::with(PassagePresenter::WITH)
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Passage $p) => $ids->search($p->id))
            ->values();

        $saved = PassagePresenter::savedIds(auth('sanctum')->user());

        return response()->json([
            'query'     => $q,
            'mode'      => $result['mode'], // reference | text
            'voices'    => array_map(fn ($p) => ['slug' => $p->slug, 'name' => $p->name, 'handle' => $p->handle, 'kind' => $p->kind], $result['voices']),
            'data'      => $passages->map(fn ($p) => PassagePresenter::card($p, $saved))->values(),
            'page'      => $page,
            'next_page' => $result['next_page'],
            'total'     => $result['total'],
        ]);
    }
}
