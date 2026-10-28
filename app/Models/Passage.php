<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Passage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'themes'      => 'array',
        'approved_at' => 'datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function attribution(): HasOne
    {
        return $this->hasOne(Attribution::class)->with('person');
    }

    // Posts this passage quotes or replies to.
    public function outgoing(): HasMany
    {
        return $this->hasMany(PassageReference::class, 'from_passage_id');
    }

    // Posts that quote or reply to this passage.
    public function incoming(): HasMany
    {
        return $this->hasMany(PassageReference::class, 'to_passage_id');
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', 'approved');
    }

    // Query builder for the imported text of this passage. Text always comes from the verses table.
    public function verses(string $translation = 'WEB'): Builder
    {
        return Verse::query()
            ->where('translation', $translation)
            ->where('book_id', $this->book_id)
            ->where('chapter', $this->chapter)
            ->whereBetween('verse', [$this->verse_start, $this->verse_end])
            ->orderBy('verse');
    }

    public function getReferenceAttribute(): string
    {
        $ref = "{$this->book->name} {$this->chapter}:{$this->verse_start}";

        return $this->verse_end > $this->verse_start ? "{$ref}-{$this->verse_end}" : $ref;
    }

    // The chapter heading (e.g. "A Psalm by David."), shown only when the passage starts at verse 1.
    public function getHeadingAttribute(): ?string
    {
        if ((int) $this->verse_start !== 1) {
            return null;
        }

        return ChapterHeading::where('translation', 'WEB')
            ->where('book_id', $this->book_id)
            ->where('chapter', $this->chapter)
            ->value('text');
    }

    // What a post displays: words-only text when set (e.g. Jesus' spoken words), otherwise the verses.
    public function getBodyAttribute(): string
    {
        return $this->text_override ?: $this->verses()->pluck('text')->implode(' ');
    }

    public function getTextAttribute(): string
    {
        return $this->verses()->pluck('text')->implode(' ');
    }
}
