<?php

namespace App\Http\Controllers;

use App\Models\Passage;
use App\Support\PassagePresenter;
use App\Support\Search;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));

        $result = mb_strlen($q) >= 2 ? Search::run($q, $page, self::PER_PAGE) : null;
        $posts = collect();

        if ($result && $result['ids']) {
            $ids = collect($result['ids']);

            $posts = Passage::with(PassagePresenter::WITH)
                ->whereIn('id', $ids)
                ->get()
                ->sortBy(fn (Passage $p) => $ids->search($p->id))
                ->values();
        }

        return view('search', [
            'q'      => $q,
            'result' => $result,
            'posts'  => $posts,
            'page'   => $page,
        ]);
    }
}
