<?php

namespace App\Support;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

// Suspending a person: they can't sign in, every signed-in session and app token stops working,
// and (by default) all their comments are hidden.
class Suspension
{
    public static function suspend(User $user, User $by, ?string $reason = null, bool $removeComments = true): void
    {
        DB::transaction(function () use ($user, $by, $reason, $removeComments) {
            $user->forceFill(['suspended_at' => now(), 'suspended_reason' => $reason])->save();
            $user->tokens()->delete();

            if ($removeComments) {
                Comment::where('user_id', $user->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->update(['status' => 'rejected', 'moderated_by' => $by->id, 'moderated_at' => now()]);
            }
        });

        // Website sessions stored in the database (the Laravel default).
        try {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        } catch (Throwable $e) {
            // The app uses another session driver; the middleware still signs them out on their next request.
        }
    }

    public static function restore(User $user): void
    {
        $user->forceFill(['suspended_at' => null, 'suspended_reason' => null])->save();
    }
}
