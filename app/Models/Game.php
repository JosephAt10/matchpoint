<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;

    /**
     * Laravel would guess the table as "games" by default.
     * Since your actual DB table is "matches", declare it explicitly.
     */
    protected $table = 'matches';

    protected $fillable = [
        'booking_id',
        'creator_id',
        'max_participants',
        'filled_slots',
        'participant_fee',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'participant_fee' => 'decimal:2',
            'status'          => 'string',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────────────────────
    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    public function scopeFull($query)
    {
        return $query->where('status', 'Full');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    public function isOpen(): bool
    {
        return $this->status === 'Open';
    }
    public function isFull(): bool
    {
        return $this->status === 'Full';
    }
    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }
    public function isCancelled(): bool
    {
        return $this->status === 'Cancelled';
    }

    public function hasAvailableSlot(): bool
    {
        return $this->isOpen() && $this->filled_slots < $this->max_participants;
    }

    public function slotsRemaining(): int
    {
        return max(0, $this->max_participants - $this->filled_slots);
    }

    // ── Relationships ─────────────────────────────────────────────────────────
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(MatchParticipant::class, 'match_id');
    }

    public function confirmedParticipants(): HasMany
    {
        return $this->hasMany(MatchParticipant::class, 'match_id')
            ->where('status', 'Confirmed');
    }
}
