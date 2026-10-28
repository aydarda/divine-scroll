<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PassageReference extends Model
{
    protected $table = 'passage_references';
    protected $guarded = [];

    public function from(): BelongsTo
    {
        return $this->belongsTo(Passage::class, 'from_passage_id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(Passage::class, 'to_passage_id');
    }
}
