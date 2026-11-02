<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

// The day's feed order, shared by the mobile API. Same rules as the website: shuffled per voice,
// then interleaved so big voices don't crowd out small ones. Stable all day, different tomorrow.
class FeedOrder
{
    // Names used in the URL (?basis=stated) and the attribution badge each one means.
    public const BASIS = [
        'stated'       => 'stated_in_text',
        'traditional'  => 'traditional',
        'speaker'      => 'speaker_only',
        'unattributed' => 'unattributed',
    ];

    // 0 = every voice gets the same share, 1 = share proportional to size.
    private const VOICE_BALANCE = 0.3;

    public static function ids(?string $basis = null): Collection
    {
        $day = now()->toDateString();

        $rows = DB::table('passages')
            ->join('attributions', 'attributions.passage_id', '=', 'passages.id')
            ->where('passages.status', 'approved')
            ->when($basis, fn ($q) => $q->where('attributions.basis', $basis))
            ->get(['passages.id', 'attributions.person_id']);

        return $rows->groupBy('person_id')
            ->flatMap(function (Collection $group) use ($day) {
                $density = $group->count() ** self::VOICE_BALANCE;

                return $group
                    ->sortBy(fn ($r) => crc32("{$day}:{$r->id}"))
                    ->values()
                    ->map(fn ($r, $i) => [
                        'id'  => $r->id,
                        'key' => ($i + (crc32("j{$day}:{$r->id}") % 1000) / 1000) / $density,
                    ]);
            })
            ->sortBy('key')
            ->pluck('id')
            ->values();
    }
}
