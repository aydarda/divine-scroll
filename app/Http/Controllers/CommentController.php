<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Passage;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // In "open" mode a comment is published at once. In "review" mode it waits for a moderator.
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

        Comment::create([
            'user_id'    => $request->user()->id,
            'passage_id' => $passage->id,
            'body'       => trim($data['body']),
            'status'     => $mode === 'review' ? 'pending' : 'approved',
        ]);

        return redirect(route('passage', $passage) . '#comments')->with(
            'status',
            $mode === 'review'
                ? 'Thanks. Your comment is waiting for review and will appear once it is approved.'
                : 'Your comment is posted.'
        );
    }

    // People can remove their own comments at any time.
    public function destroy(Request $request, Comment $comment)
    {
        abort_unless($comment->user_id === $request->user()->id, 403);

        $passage = $comment->passage;
        $comment->delete();

        return redirect(route('passage', $passage) . '#comments')->with('status', 'Comment deleted.');
    }

    // Report a published comment. Two different people reporting it pulls it back into the review queue.
    public function report(Request $request, Comment $comment)
    {
        abort_unless($comment->status === 'approved' && $comment->user_id !== $request->user()->id, 404);

        $data = $request->validate(['reason' => ['nullable', 'string', 'max:200']]);

        CommentReport::firstOrCreate(
            ['comment_id' => $comment->id, 'user_id' => $request->user()->id],
            ['reason' => $data['reason'] ?? null],
        );

        if ($comment->reports()->count() >= 2) {
            $comment->update(['status' => 'pending', 'moderated_by' => null, 'moderated_at' => null]);
        }

        return redirect(route('passage', $comment->passage_id) . '#comments')
            ->with('status', 'Thanks. A moderator will take a look.');
    }
}
