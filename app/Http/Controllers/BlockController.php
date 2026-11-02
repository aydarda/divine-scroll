<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    // The people you have blocked.
    public function index(Request $request)
    {
        return view('blocked', [
            'users' => $request->user()->blocks()->orderBy('name')->get(['users.id', 'users.name']),
        ]);
    }

    // Stop seeing a person's comments.
    public function store(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('status', 'You can’t block yourself.');
        }

        $request->user()->blocks()->syncWithoutDetaching([$user->id]);

        return back()->with('status', 'Blocked. You won’t see their comments any more.');
    }

    public function destroy(Request $request, User $user)
    {
        $request->user()->blocks()->detach($user->id);

        return back()->with('status', 'Unblocked.');
    }
}
