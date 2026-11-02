<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\SuggestionController;
use App\Models\Attribution;
use App\Models\Comment;
use App\Models\Passage;
use App\Models\Suggestion;
use App\Models\User;
use App\Support\Suspension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Moderator tools. The routes are behind the EnsureAdmin middleware.
class ModerationController extends Controller
{
    // GET /api/v1/admin/summary
    public function summary()
    {
        return response()->json([
            'pending_comments'    => Comment::where('status', 'pending')->count(),
            'reported_comments'   => Comment::approved()->whereHas('reports')->count(),
            'pending_suggestions' => Suggestion::where('status', 'pending')->count(),
            'pending_passages'    => Passage::where('status', 'pending')->count(),
            'suspended_users'     => User::whereNotNull('suspended_at')->count(),
        ]);
    }

    // GET /api/v1/admin/comments?list=pending|reported|recent&page=1
    public function comments(Request $request)
    {
        $list = in_array($request->query('list'), ['pending', 'reported', 'recent'], true) ? $request->query('list') : 'pending';

        $query = Comment::query()->with(['user:id,name,email', 'passage.book'])->withCount('reports');

        $query = match ($list) {
            'reported' => $query->approved()->whereHas('reports')->latest(),
            'recent'   => $query->approved()->latest(),
            default    => $query->where('status', 'pending')->oldest(),
        };

        $items = $query->paginate(20);

        return response()->json([
            'list'      => $list,
            'data'      => $items->getCollection()->map(fn ($c) => [
                'id'            => $c->id,
                'passage_id'    => $c->passage_id,
                'reference'     => $c->passage?->reference,
                'author'        => ['id' => $c->user->id, 'name' => $c->user->name, 'email' => $c->user->email],
                'body'          => $c->body,
                'status'        => $c->status,
                'reports_count' => $c->reports_count,
                'created_at'    => $c->created_at->toIso8601String(),
            ])->values(),
            'page'      => $items->currentPage(),
            'next_page' => $items->hasMorePages() ? $items->currentPage() + 1 : null,
            'total'     => $items->total(),
        ]);
    }

    // POST /api/v1/admin/comments/{id}/approve
    public function approveComment(Request $request, Comment $comment)
    {
        $comment->update(['status' => 'approved', 'moderated_by' => $request->user()->id, 'moderated_at' => now()]);
        $comment->reports()->delete();

        return response()->json(['ok' => true, 'status' => 'approved']);
    }

    // POST /api/v1/admin/comments/{id}/reject  (also how a moderator removes a published comment)
    public function rejectComment(Request $request, Comment $comment)
    {
        $comment->update(['status' => 'rejected', 'moderated_by' => $request->user()->id, 'moderated_at' => now()]);

        return response()->json(['ok' => true, 'status' => 'rejected']);
    }

    // POST /api/v1/admin/comments/{id}/dismiss: keep a reported comment and clear its reports.
    public function dismissReports(Comment $comment)
    {
        $comment->reports()->delete();

        return response()->json(['ok' => true]);
    }

    // GET /api/v1/admin/users?q=&page=
    public function users(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $items = User::query()
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $like = '%' . str_replace(['%', '_'], '', $q) . '%';
                $w->where('name', 'like', $like)->orWhere('email', 'like', $like);
            }))
            ->latest()
            ->paginate(20);

        return response()->json([
            'data'      => $items->getCollection()->map(fn ($u) => [
                'id'               => $u->id,
                'name'             => $u->name,
                'email'            => $u->email,
                'is_admin'         => (bool) $u->is_admin,
                'suspended'        => $u->suspended_at !== null,
                'suspended_reason' => $u->suspended_reason,
                'joined'           => $u->created_at->toIso8601String(),
            ])->values(),
            'page'      => $items->currentPage(),
            'next_page' => $items->hasMorePages() ? $items->currentPage() + 1 : null,
            'total'     => $items->total(),
        ]);
    }

    // POST /api/v1/admin/users/{id}/suspend  { reason?, remove_comments? (default true) }
    public function suspendUser(Request $request, User $user)
    {
        abort_if($user->is_admin || $user->id === $request->user()->id, 422, 'Moderators can’t be suspended.');

        $data = $request->validate([
            'reason'          => ['nullable', 'string', 'max:200'],
            'remove_comments' => ['nullable', 'boolean'],
        ]);

        Suspension::suspend($user, $request->user(), $data['reason'] ?? null, $request->boolean('remove_comments', true));

        return response()->json(['ok' => true, 'suspended' => true]);
    }

    // POST /api/v1/admin/users/{id}/unsuspend
    public function unsuspendUser(User $user)
    {
        Suspension::restore($user);

        return response()->json(['ok' => true, 'suspended' => false]);
    }

    // GET /api/v1/admin/suggestions?page=1
    public function suggestions()
    {
        $items = Suggestion::where('status', 'pending')
            ->with(['user:id,name,email', 'book', 'suggestedPerson', 'passage.book', 'passage.attribution.person'])
            ->oldest()
            ->paginate(20);

        return response()->json([
            'data'      => $items->getCollection()->map(function ($s) {
                $current = $s->passage?->attribution;

                return SuggestionController::present($s) + [
                    'from'              => ['id' => $s->user->id, 'name' => $s->user->name, 'email' => $s->user->email],
                    'suggested_voice_id' => $s->suggested_person_id,
                    'current'           => $current ? [
                        'voice_id'    => $current->person_id,
                        'voice'       => $current->person->name,
                        'basis'       => $current->basis,
                        'basis_label' => $current->basisLabel(),
                        'note'        => $current->note,
                    ] : null,
                ];
            })->values(),
            'page'      => $items->currentPage(),
            'next_page' => $items->hasMorePages() ? $items->currentPage() + 1 : null,
            'total'     => $items->total(),
        ]);
    }

    // POST /api/v1/admin/suggestions/{id}/accept  { person_id, basis, note?, publish?, admin_note? }
    public function acceptSuggestion(Request $request, Suggestion $suggestion)
    {
        abort_unless($suggestion->status === 'pending', 404);

        $data = $request->validate([
            'person_id'  => ['required', 'exists:persons,id'],
            'basis'      => ['required', 'in:' . implode(',', array_keys(SuggestionController::BASES))],
            'note'       => ['nullable', 'string', 'max:500'],
            'publish'    => ['nullable', 'boolean'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $passageId = DB::transaction(function () use ($suggestion, $data, $request) {
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

            $values = ['person_id' => $data['person_id'], 'basis' => $data['basis'], 'note' => $data['note'] ?? null];
            $attribution = $passage->attribution()->first();

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

            return $passage->id;
        });

        return response()->json(['ok' => true, 'status' => 'accepted', 'passage_id' => $passageId]);
    }

    // POST /api/v1/admin/suggestions/{id}/decline  { admin_note? }
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

        return response()->json(['ok' => true, 'status' => 'declined']);
    }
}
