<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribution;
use App\Models\Comment;
use App\Models\Passage;
use App\Models\Person;
use App\Models\Suggestion;
use App\Models\User;
use App\Support\Suspension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    private const BASES = ['stated_in_text', 'traditional', 'speaker_only', 'unattributed'];

    public function index()
    {
        return view('admin.index', [
            'pendingComments'    => Comment::where('status', 'pending')->count(),
            'reportedComments'   => Comment::approved()->whereHas('reports')->count(),
            'pendingSuggestions' => Suggestion::where('status', 'pending')->count(),
            'pendingPassages'    => Passage::where('status', 'pending')->count(),
        ]);
    }

    public function comments()
    {
        return view('admin.comments', [
            'pending'  => Comment::where('status', 'pending')->with(['user', 'passage.book', 'passage.attribution'])->oldest()->limit(50)->get(),
            'reported' => Comment::approved()->whereHas('reports')->withCount('reports')->with(['user', 'passage.book', 'reports'])->latest()->limit(50)->get(),
            'recent'   => Comment::approved()->with(['user', 'passage.book'])->latest()->limit(50)->get(),
        ]);
    }

    public function approveComment(Request $request, Comment $comment)
    {
        $comment->update(['status' => 'approved', 'moderated_by' => $request->user()->id, 'moderated_at' => now()]);
        $comment->reports()->delete();

        return back()->with('status', 'Comment approved.');
    }

    public function rejectComment(Request $request, Comment $comment)
    {
        $comment->update(['status' => 'rejected', 'moderated_by' => $request->user()->id, 'moderated_at' => now()]);

        return back()->with('status', 'Comment rejected.');
    }

    // Keep a reported comment: clear the reports against it.
    public function dismissReports(Comment $comment)
    {
        $comment->reports()->delete();

        return back()->with('status', 'Reports dismissed.');
    }

    // People: search by name or email, suspend or restore.
    public function users(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $like = '%' . str_replace(['%', '_'], '', $q) . '%';
                $w->where('name', 'like', $like)->orWhere('email', 'like', $like);
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', ['users' => $users, 'q' => $q]);
    }

    public function suspendUser(Request $request, User $user)
    {
        if ($user->is_admin || $user->id === $request->user()->id) {
            return back()->with('status', 'Moderators can’t be suspended. Remove the moderator status first.');
        }

        $data = $request->validate([
            'reason'          => ['nullable', 'string', 'max:200'],
            'remove_comments' => ['nullable', 'boolean'],
        ]);

        Suspension::suspend($user, $request->user(), $data['reason'] ?? null, $request->boolean('remove_comments', true));

        return back()->with('status', "{$user->name} is suspended.");
    }

    public function unsuspendUser(User $user)
    {
        Suspension::restore($user);

        return back()->with('status', "{$user->name} can sign in again.");
    }

    public function suggestions()
    {
        return view('admin.suggestions', [
            'items'   => Suggestion::where('status', 'pending')
                ->with(['user', 'book', 'suggestedPerson', 'passage.book', 'passage.attribution.person'])
                ->oldest()->limit(50)->get(),
            'persons' => Person::orderBy('name')->get(),
        ]);
    }

    // Accept: create the passage (or apply the correction) with the details you choose here.
    public function acceptSuggestion(Request $request, Suggestion $suggestion)
    {
        abort_unless($suggestion->status === 'pending', 404);

        $data = $request->validate([
            'person_id'  => ['required', 'exists:persons,id'],
            'basis'      => ['required', 'in:' . implode(',', self::BASES)],
            'note'       => ['nullable', 'string', 'max:500'],
            'publish'    => ['nullable', 'boolean'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($suggestion, $data, $request) {
            if ($suggestion->type === 'passage') {
                $publish = $request->boolean('publish');

                $passage = Passage::firstOrCreate(
                    [
                        'book_id'     => $suggestion->book_id,
                        'chapter'     => $suggestion->chapter,
                        'verse_start' => $suggestion->verse_start,
                        'verse_end'   => $suggestion->verse_end,
                    ],
                    [
                        'status'      => $publish ? 'approved' : 'pending',
                        'approved_at' => $publish ? now() : null,
                        'reason'      => 'Suggested by a reader.',
                    ],
                );
            } else {
                $passage = $suggestion->passage;
            }

            $attribution = $passage->attribution()->first();
            $values = ['person_id' => $data['person_id'], 'basis' => $data['basis'], 'note' => $data['note'] ?? null];

            if ($attribution) {
                $attribution->update($values);
            } else {
                Attribution::create($values + ['passage_id' => $passage->id]);
            }

            $suggestion->update([
                'status'       => 'accepted',
                'admin_note'   => $data['admin_note'] ?? null,
                'moderated_by' => $request->user()->id,
                'moderated_at' => now(),
            ]);
        });

        return back()->with('status', 'Suggestion accepted.');
    }

    public function declineSuggestion(Request $request, Suggestion $suggestion)
    {
        abort_unless($suggestion->status === 'pending', 404);

        $data = $request->validate(['admin_note' => ['nullable', 'string', 'max:500']]);

        $suggestion->update([
            'status'       => 'declined',
            'admin_note'   => $data['admin_note'] ?? null,
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        return back()->with('status', 'Suggestion declined.');
    }
}
