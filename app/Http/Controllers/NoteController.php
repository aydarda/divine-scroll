<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Passage;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    // Create, update, or (when the box is empty) delete the signed-in user's note on a passage.
    public function save(Request $request, Passage $passage)
    {
        abort_unless($passage->status === 'approved', 404);

        $data = $request->validate(['body' => ['nullable', 'string', 'max:5000']]);
        $body = trim($data['body'] ?? '');
        $userId = $request->user()->id;

        if ($body === '') {
            Note::where('user_id', $userId)->where('passage_id', $passage->id)->delete();
            $message = 'Note deleted.';
        } else {
            Note::updateOrCreate(
                ['user_id' => $userId, 'passage_id' => $passage->id],
                ['body' => $body],
            );
            $message = 'Note saved.';
        }

        return redirect(route('passage', $passage) . '#note')->with('status', $message);
    }
}
