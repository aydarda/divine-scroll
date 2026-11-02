<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Note;
use App\Models\Suggestion;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    private const PER_PAGE = 10;

    private const WITH = [
        'book',
        'attribution',
        'outgoing.to.book',
        'outgoing.to.attribution',
        'incoming.from.book',
        'incoming.from.attribution',
    ];

    public function index(Request $request)
    {
        $user = $request->user();
        $tab = in_array($request->query('tab'), ['notes', 'contributions'], true) ? $request->query('tab') : 'saved';

        $items = null;
        $suggestions = $comments = collect();

        if ($tab === 'notes') {
            $items = Note::where('user_id', $user->id)
                ->whereHas('passage', fn ($q) => $q->where('status', 'approved'))
                ->with(['passage.book', 'passage.attribution'])
                ->latest('updated_at')
                ->paginate(self::PER_PAGE)
                ->withQueryString();
        } elseif ($tab === 'contributions') {
            $suggestions = Suggestion::where('user_id', $user->id)
                ->with(['book', 'passage.book'])
                ->latest()->limit(50)->get();
            $comments = Comment::where('user_id', $user->id)
                ->with('passage.book')
                ->latest()->limit(50)->get();
        } else {
            $items = $user->bookmarkedPassages()
                ->approved()
                ->with(self::WITH)
                ->orderByPivot('created_at', 'desc')
                ->paginate(self::PER_PAGE)
                ->withQueryString();
        }

        return view('library.index', [
            'tab'          => $tab,
            'items'        => $items,
            'suggestions'  => $suggestions,
            'comments'     => $comments,
            'savedCount'   => $user->bookmarkedPassages()->approved()->count(),
            'noteCount'    => $user->notes()->count(),
            'contribCount' => Suggestion::where('user_id', $user->id)->count() + Comment::where('user_id', $user->id)->count(),
        ]);
    }
}
