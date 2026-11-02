<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Suggestion;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    // GET /api/v1/me/export: everything we hold about the signed-in user.
    public function export(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'account' => [
                'name'       => $user->name,
                'email'      => $user->email,
                'created_at' => $user->created_at?->toIso8601String(),
            ],
            'saved_passages' => $user->bookmarkedPassages()->with('book')->get()->map(fn ($p) => [
                'reference' => $p->reference,
                'saved_at'  => $p->pivot->created_at?->toIso8601String(),
            ])->values(),
            'notes' => $user->notes()->with('passage.book')->get()->map(fn ($n) => [
                'reference'  => $n->passage?->reference,
                'note'       => $n->body,
                'updated_at' => $n->updated_at?->toIso8601String(),
            ])->values(),
            'comments' => Comment::where('user_id', $user->id)->with('passage.book')->get()->map(fn ($c) => [
                'reference'  => $c->passage?->reference,
                'comment'    => $c->body,
                'status'     => $c->status,
                'created_at' => $c->created_at?->toIso8601String(),
            ])->values(),
            'suggestions' => Suggestion::where('user_id', $user->id)->with(['book', 'passage.book'])->get()->map(fn ($s) => [
                'type'       => $s->type,
                'reference'  => $s->reference(),
                'reason'     => $s->reason,
                'status'     => $s->status,
                'created_at' => $s->created_at?->toIso8601String(),
            ])->values(),
        ]);
    }

    // GET /api/v1/me/contributions: the status of the user's own comments and suggestions.
    public function contributions(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'suggestions' => SuggestionController::presentMany(
                Suggestion::where('user_id', $user->id)->with(['book', 'passage.book'])->latest()->limit(50)->get()
            ),
            'comments' => Comment::where('user_id', $user->id)->with('passage.book')->latest()->limit(50)->get()->map(fn ($c) => [
                'id'         => $c->id,
                'passage_id' => $c->passage_id,
                'reference'  => $c->passage?->reference,
                'body'       => $c->body,
                'status'     => $c->status, // pending | approved | rejected
                'created_at' => $c->created_at->toIso8601String(),
            ])->values(),
        ]);
    }
}
