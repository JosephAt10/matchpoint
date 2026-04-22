<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'message',
        'type',
        'status',
        'notifiable_type',
        'notifiable_id',
    ];

    protected function casts(): array
    {
        return [
            'type'   => 'string',
            'status' => 'string',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('status', 'Unread');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 'Read');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function markAsRead(): void
    {
        $this->update(['status' => 'Read']);
    }

    public function isUnread(): bool
    {
        return $this->status === 'Unread';
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Polymorphic: links the notification back to its source entity */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}