<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => 'string',
            'status'            => 'string',
        ];
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function isAdmin(): bool        { return $this->role === 'Admin'; }
    public function isFieldOwner(): bool   { return $this->role === 'FieldOwner'; }
    public function isUser(): bool         { return $this->role === 'User'; }
    public function isActive(): bool       { return $this->status === 'Active'; }
    public function isPendingApproval(): bool { return $this->status === 'PendingApproval'; }
    public function isDeactivated(): bool  { return $this->status === 'Deactivated'; }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin'
            && $this->isAdmin()
            && $this->isActive();
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class, 'owner_id');
    }

    public function favoriteFields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class, 'favorite_fields')
            ->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function createdMatches(): HasMany
    {
        return $this->hasMany(Game::class, 'creator_id'); // ← Game, not Match
    }

    public function matchParticipations(): HasMany
    {
        return $this->hasMany(MatchParticipant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payer_id');
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_id');
    }
}
