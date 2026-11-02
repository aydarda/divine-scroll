<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Passage;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // GET /api/v1/passages/{id}/comments?page=1: published comments, oldest first.
    public function index(Request $request, Passage $passage)
    {
        abort_unless($passage->status === 'approved', 404);

        $user = auth('sanctum')->user();

        $items = Comment::where('passage_id', $passage->id)
            ->approved()
            ->visibleTo($user)
            ->with('user:id,name')
            ->oldest()
            ->paginate(20);

        return response()->json([
            'data'      => $items->getCollection()->map(fn (Comment $c) => [
                'id'         => $c->id,
                'author_id'  => $c->user_id,
                'author'     => $c->user->name,
                'body'       => $c->body,
                'status'     => $c->status,
                'created_at' => $c->created_at->toIso8601String(),
                'mine'       => $user && $c->user_id === $user->id,
            ])->values(),
            'page'      => $items->currentPage(),
            'next_page' => $items->hasMorePages() ? $items->currentPage() + 1 : null,
            'total'     => $items->total(),
        ]);
    }

    // POST /api/v1/passages/{id}/comments  { "body": "..." }
    public function store(Request $request, Passage $passage)
    {
        abort_unless($passage->status === 'approved', 404);

        $mode = config('contributions.comments', 'open');
        abort_if($mode === 'off', 403, 'Comments are switched off.');

        $data = $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:1000', 'not_regex:/(https?:\/\/|www\.|\.[a-z]{2,}\/)/i'],
        ], [
            'body.not_regex' => 'Links aren’t allowed in comments.',
        ]);

        $comment = Comment::create([
            'user_id'    => $request->user()->id,
            'passage_id' => $passage->id,
            'body'       => trim($data['body']),
            'status'     => $mode === 'review' ? 'pending' : 'approved',
        ]);

        return response()->json([
            'comment' => [
                'id'         => $comment->id,
                'author_id'  => $request->user()->id,
                'author'     => $request->user()->name,
                'body'       => $comment->body,
                'status'     => $comment->status,
                'created_at' => $comment->created_at->toIso8601String(),
                'mine'       => true,
            ],
        ], 201);
    }

    // DELETE /api/v1/comments/{id}: people can remove their own comments.
    public function destroy(Request $request, Comment $comment)
    {
        abort_unless($comment->user_id === $request->user()->id, 403);

        $comment->delete();

        return response()->json(['ok' => true]);
    }

    // POST /api/v1/comments/{id}/report  { "reason": "..." }
    public function report(Request $request, Comment $comment)
    {
        abort_unless($comment->status === 'approved' && $comment->user_id !== $request->user()->id, 404);

        $data = $request->validate(['reason' => ['nullable', 'string', 'max:200']]);

        CommentReport::firstOrCreate(
            ['comment_id' => $comment->id, 'user_id' => $request->user()->id],
            ['reason' => $data['reason'] ?? null],
        );

        // Two different people reporting a comment hides it until a moderator looks at it.
        if ($comment->reports()->count() >= 2) {
            $comment->update(['status' => 'pending', 'moderated_by' => null, 'moderated_at' => null]);
        }

        return response()->json(['ok' => true]);
    }
}
