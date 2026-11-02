<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonFact extends Model
{
    protected $guarded = [];

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
