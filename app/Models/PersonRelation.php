<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonRelation extends Model
{
    protected $guarded = [];

    public function relative(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'related_person_id');
    }

    public function basisLabel(): string
    {
        return match ($this->basis) {
            'stated_in_text'     => 'Stated in text',
            'traditional'        => 'Traditional',
            'scholarly_estimate' => 'Scholarly estimate',
            default              => 'Unknown',
        };
    }
}
