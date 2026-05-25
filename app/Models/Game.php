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
        'title',
        'description',
        'team_a_name',
        'team_b_name',
        'team_a_logo',
        'team_b_logo',
        'max_per_team',
        'max_participants',
        'filled_slots',
        'participant_fee',
        'gender',
        'skill_level',
        'match_type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'participant_fee' => 'decimal:2',
            'max_per_team' => 'integer',
            'status' => 'string',
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

    public function hasAvailableSlot(string $team): bool
    {
        return $this->isOpen() && match (strtoupper($team)) {
            'A' => $this->teamACount() < (int) $this->max_per_team,
            'B' => $this->teamBCount() < (int) $this->max_per_team,
            default => false,
        };
    }

    public function slotsRemaining(): int
    {
        return max(0, $this->max_participants - $this->filled_slots);
    }

    public function hasTeamsConfigured(): bool
    {
        return filled($this->team_a_name) || filled($this->team_b_name);
    }

    public function teamACount(): int
    {
        return $this->confirmedParticipantsCollection()
            ->where('team', 'A')
            ->count();
    }

    public function teamBCount(): int
    {
        return $this->confirmedParticipantsCollection()
            ->where('team', 'B')
            ->count();
    }

    public function isCreator(int|string|null $userId): bool
    {
        return (int) $this->creator_id === (int) $userId;
    }

    public function teamSlotsRemaining(string $team): int
    {
        if (! $this->max_per_team) {
            return 0;
        }

        return max(0, (int) $this->max_per_team - match (strtoupper($team)) {
            'A' => $this->teamACount(),
            'B' => $this->teamBCount(),
            default => 0,
        });
    }

    public function hasAvailableTeamSlot(string $team): bool
    {
        return $this->hasAvailableSlot($team);
    }

    public function refreshParticipationState(): void
    {
        $teamACount = $this->participants()
            ->where('status', 'Confirmed')
            ->where('team', 'A')
            ->count();

        $teamBCount = $this->participants()
            ->where('status', 'Confirmed')
            ->where('team', 'B')
            ->count();

        $attributes = [
            'filled_slots' => $teamACount + $teamBCount,
        ];

        if (! $this->isCompleted() && ! $this->isCancelled()) {
            $attributes['status'] = $this->max_per_team && $teamACount >= $this->max_per_team && $teamBCount >= $this->max_per_team
                ? 'Full'
                : 'Open';
        }

        $this->forceFill($attributes)->save();
        $this->refresh();
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

    private function confirmedParticipantsCollection()
    {
        $participants = $this->relationLoaded('participants')
            ? $this->participants
            : $this->participants()
                ->where('status', 'Confirmed')
                ->get();

        return $participants
            ->where('status', 'Confirmed')
            ->values();
    }
}
