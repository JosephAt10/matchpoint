<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MatchParticipant extends Model
{
    protected $fillable = [
        'match_id',
        'user_id',
        'status',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'status'    => 'string',
            'joined_at' => 'datetime',
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isPending(): bool   { return $this->status === 'Pending'; }
    public function isConfirmed(): bool { return $this->status === 'Confirmed'; }
    public function isCancelled(): bool { return $this->status === 'Cancelled'; }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'match_id'); // ← Game + explicit FK
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'match_participant_id');
    }
}
