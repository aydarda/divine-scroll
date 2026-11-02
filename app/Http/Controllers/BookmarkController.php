<?php

namespace App\Http\Controllers;

use App\Models\Passage;
use App\Support\Library;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    // Save or un-save a passage for the signed-in user.
    public function toggle(Request $request, Passage $passage)
    {
        abort_unless($passage->status === 'approved', 404);

        $user = $request->user();
        $saved = $user->bookmarkedPassages()->where('passages.id', $passage->id)->exists();

        if ($saved) {
            $user->bookmarkedPassages()->detach($passage->id);
        } else {
            $user->bookmarkedPassages()->attach($passage->id);
        }

        Library::forget();

        if ($request->expectsJson()) {
            return response()->json(['saved' => ! $saved]);
        }

        return back();
    }
}
