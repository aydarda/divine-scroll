<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suggestion extends Model
{
    protected $guarded = [];

    protected $casts = ['moderated_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function passage(): BelongsTo
    {
        return $this->belongsTo(Passage::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function suggestedPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'suggested_person_id');
    }

    // "Psalms 23:1-6" for a new passage, or the reference of the passage being corrected.
    public function reference(): string
    {
        if ($this->type === 'attribution') {
            return $this->passage?->reference ?? 'a removed passage';
        }

        $ref = ($this->book?->name ?? '?') . " {$this->chapter}:{$this->verse_start}";

        return $this->verse_end > $this->verse_start ? "{$ref}-{$this->verse_end}" : $ref;
    }

    public function typeLabel(): string
    {
        return $this->type === 'passage' ? 'New passage' : 'Attribution correction';
    }
}
