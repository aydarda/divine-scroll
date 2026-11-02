<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    protected $table = 'persons';
    protected $guarded = [];

    // Routes use the slug (/voices/david), not the numeric ID.
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function attributions(): HasMany
    {
        return $this->hasMany(Attribution::class);
    }

    public function facts(): HasMany
    {
        return $this->hasMany(PersonFact::class)->orderBy('sort');
    }

    public function relations(): HasMany
    {
        return $this->hasMany(PersonRelation::class)->orderBy('sort');
    }
}
