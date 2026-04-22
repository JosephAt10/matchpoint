<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'field_id',
        'timeslot_id',
        'date',
        'status',
        'payment_deadline',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'date'             => 'date',
            'payment_deadline' => 'datetime',
            'status'           => 'string',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'Confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'Cancelled');
    }

    /** Bookings past their payment deadline with no verified payment (for auto-cancel scheduler) */
    public function scopeOverdue($query)
    {
        return $query->pending()->where('payment_deadline', '<', now());
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'Confirmed';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'Cancelled';
    }

    /**
     * BR-04 / BR-05: only confirmed outdoor bookings before the booking date
     * can be rescheduled. Indoor bookings cannot be rescheduled under any circumstances.
     */
    public function canBeRescheduled(): bool
    {
        return $this->isConfirmed()
            && $this->field->isOutdoor()
            && $this->date->isFuture();
    }

    /**
     * BR-01: down-payment amount = 50% of price_per_slot
     */
    public function downPaymentAmount(): float
    {
        return round($this->field->price_per_slot * 0.5, 2);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'timeslot_id');
    }

    public function bookedSlot(): HasOne
    {
        return $this->hasOne(BookedSlot::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function Game(): HasOne
    {
        return $this->hasOne(Game::class, 'booking_id');
    }
}
