<?php

namespace App\Support;

use App\Models\Passage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Turns a passage into the JSON the mobile app receives.
class PassagePresenter
{
    public const WITH = [
        'book',
        'attribution',
        'outgoing.to.book',
        'outgoing.to.attribution',
        'incoming.from.book',
        'incoming.from.attribution',
    ];

    // Passage IDs the user has saved, as [id => position] for quick isset() checks.
    public static function savedIds(?User $user): array
    {
        if (! $user) {
            return [];
        }

        return DB::table('bookmarks')->where('user_id', $user->id)->pluck('passage_id')->flip()->all();
    }

    public static function card(Passage $p, array $saved = [], bool $withLinks = true): array
    {
        $attr = $p->attribution;
        $who = $attr?->person;

        $data = [
            'id'        => $p->id,
            'reference' => $p->reference,
            'heading'   => $p->heading,
            'text'      => $p->body,
            'themes'    => $p->themes ?? [],
            'voice'     => $who ? self::voice($who) : null,
            'basis'     => $attr ? [
                'key'   => $attr->basis,
                'label' => $attr->basisLabel(),
                'note'  => $attr->note,
            ] : null,
            'url'       => route('passage', $p),
            'saved'     => isset($saved[$p->id]),
        ];

        if ($withLinks) {
            $outgoing = $p->outgoing
                ->filter(fn ($l) => $l->to && $l->to->status === 'approved' && $l->to->attribution)
                ->sortBy(fn ($l) => $l->type === 'quote' ? 0 : 1);

            $incoming = $p->incoming
                ->filter(fn ($l) => $l->from && $l->from->status === 'approved' && $l->from->attribution)
                ->sortBy(fn ($l) => $l->type === 'quote' ? 0 : 1);

            $data['quotes'] = $outgoing->take(2)->map(fn ($l) => self::mini($l->to, $l->type))->values()->all();
            $data['echoed_by_count'] = $incoming->count();
            $data['echoed_by'] = $incoming->take(3)->map(fn ($l) => [
                'id'        => $l->from->id,
                'type'      => $l->type,
                'reference' => $l->from->reference,
                'voice'     => self::voice($l->from->attribution->person),
            ])->values()->all();
        }

        return $data;
    }

    public static function mini(Passage $p, string $type): array
    {
        return [
            'id'        => $p->id,
            'type'      => $type,
            'reference' => $p->reference,
            'heading'   => $p->heading,
            'text'      => $p->body,
            'voice'     => self::voice($p->attribution->person),
        ];
    }

    public static function voice($person): array
    {
        return [
            'slug'   => $person->slug,
            'name'   => $person->name,
            'handle' => $person->handle,
            'kind'   => $person->kind,
        ];
    }
}
