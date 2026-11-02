<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Comment extends Model
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

    public function reports(): HasMany
    {
        return $this->hasMany(CommentReport::class);
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', 'approved');
    }

    // Leaves out comments from people the viewer has blocked.
    public function scopeVisibleTo(Builder $q, ?User $viewer): Builder
    {
        if (! $viewer) {
            return $q;
        }

        $blocked = DB::table('user_blocks')->where('blocker_id', $viewer->id)->pluck('blocked_id');

        return $blocked->isEmpty() ? $q : $q->whereNotIn('user_id', $blocked);
    }
}
