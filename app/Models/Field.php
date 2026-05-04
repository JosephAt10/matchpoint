<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'location',
        'type',
        'sport_type',
        'price_per_slot',
        'is_approved',
        'rejected_at',
        'description',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'type'           => 'string',
            'price_per_slot' => 'decimal:2',
            'is_approved'    => 'boolean',
            'rejected_at'    => 'datetime',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /** Only return fields visible to the public (BR-08) */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    public function scopeBySport($query, string $sport)
    {
        return $query->where('sport_type', $sport);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isOutdoor(): bool
    {
        return $this->type === 'Outdoor';
    }

    public function isIndoor(): bool
    {
        return $this->type === 'Indoor';
    }

    public function getImageUrlAttribute(): ?string
    {
        if (blank($this->image_path)) {
            return null;
        }

        return '/storage/' . ltrim($this->image_path, '/');
    }

    public function getApprovalStatusAttribute(): string
    {
        if ($this->is_approved) {
            return 'Approved';
        }

        if ($this->rejected_at) {
            return 'Rejected';
        }

        return 'Pending';
    }

    public function isRejected(): bool
    {
        return filled($this->rejected_at) && ! $this->is_approved;
    }

    public function isPendingApproval(): bool
    {
        return ! $this->is_approved && ! $this->isRejected();
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_fields')
            ->withTimestamps();
    }
}
