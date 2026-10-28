<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function passages(): HasMany
    {
        return $this->hasMany(Passage::class);
    }
}
