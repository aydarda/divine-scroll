<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return view('account.index', [
            'user'       => $user,
            'savedCount' => $user->bookmarkedPassages()->count(),
            'noteCount'  => $user->notes()->count(),
        ]);
    }

    // Everything we hold about the user, as a download.
    public function export(Request $request)
    {
        $user = $request->user();

        $data = [
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
        ];

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="my-data.json"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // Permanently delete the account, its saved passages and notes.
    public function destroy(Request $request)
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $user = $request->user();

        Auth::logout();
        $user->delete(); // saved passages and notes are removed with it
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('feed')->with('status', 'Your account and everything saved in it was deleted.');
    }
}
