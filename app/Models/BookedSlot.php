<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookedSlot extends Model
{
    protected $fillable = [
        'timeslot_id',
        'booking_id',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'timeslot_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}

