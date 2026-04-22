<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'match_participant_id',
        'payer_id',
        'amount',
        'proof',
        'type',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'type'   => 'string',
            'status' => 'string',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'Verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    public function scopeForBookings($query)
    {
        return $query->where('type', 'BookingDP');
    }

    public function scopeForMatches($query)
    {
        return $query->where('type', 'MatchFee');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isVerified(): bool
    {
        return $this->status === 'Verified';
    }

    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'Rejected';
    }

    public function isBookingDP(): bool
    {
        return $this->type === 'BookingDP';
    }

    public function isMatchFee(): bool
    {
        return $this->type === 'MatchFee';
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function matchParticipant(): BelongsTo
    {
        return $this->belongsTo(MatchParticipant::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }
}
