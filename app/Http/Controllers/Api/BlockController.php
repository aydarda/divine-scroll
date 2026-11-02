<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BlockController extends Controller
{
    // GET /api/v1/me/blocks
    public function index(Request $request)
    {
        return response()->json([
            'data' => $request->user()->blocks()->orderBy('name')->get(['users.id', 'users.name'])
                ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])->values(),
        ]);
    }

    // POST /api/v1/users/{id}/block: their comments are hidden from you from now on.
    public function store(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            throw ValidationException::withMessages(['user' => ['You can’t block yourself.']]);
        }

        $request->user()->blocks()->syncWithoutDetaching([$user->id]);

        return response()->json(['blocked' => true]);
    }

    // DELETE /api/v1/users/{id}/block
    public function destroy(Request $request, User $user)
    {
        $request->user()->blocks()->detach($user->id);

        return response()->json(['blocked' => false]);
    }
}
