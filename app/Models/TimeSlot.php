<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_available_base',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week'       => 'string',
            'is_available_base' => 'boolean',
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Check whether this slot is available on a specific date.
     * A slot is available if:
     *   1. is_available_base is true (Field Owner hasn't disabled it)
     *   2. No BookedSlot record exists for (timeslot_id, date)
     */
    public function isAvailableOn(\DateTimeInterface|string $date): bool
    {
        if (! $this->is_available_base) {
            return false;
        }

        return ! $this->bookedSlots()
            ->whereDate('date', $date)
            ->exists();
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function bookedSlots(): HasMany
    {
        return $this->hasMany(BookedSlot::class, 'timeslot_id');
    }
}

