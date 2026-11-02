<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

// Answers "has the signed-in user saved this passage?" with one query per request,
// no matter how many posts are on the page.
class Library
{
    private static ?array $saved = null;

    public static function saved(int $passageId): bool
    {
        if (! auth()->check()) {
            return false;
        }

        self::$saved ??= DB::table('bookmarks')
            ->where('user_id', auth()->id())
            ->pluck('passage_id')
            ->flip()
            ->all();

        return isset(self::$saved[$passageId]);
    }

    public static function forget(): void
    {
        self::$saved = null;
    }
}
