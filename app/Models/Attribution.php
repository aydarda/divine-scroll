<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attribution extends Model
{
    protected $guarded = [];

    public function passage(): BelongsTo
    {
        return $this->belongsTo(Passage::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function basisLabel(): string
    {
        return match ($this->basis) {
            'stated_in_text' => 'Stated in text',
            'traditional'    => 'Traditional attribution',
            'speaker_only'   => 'Speaker, not author',
            default          => 'Unattributed',
        };
    }
}
