<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'actor_id',
        'action',
        'entity_type',
        'entity_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata'   => 'array',
            'created_at' => 'datetime',
        ];
    }

    // ── Factory method ────────────────────────────────────────────────────────

    public static function record(
        string $action,
        Model  $entity,
        array  $metadata = [],
        ?int   $actorId  = null
    ): self {
        return self::create([
            'actor_id'    => $actorId ?? auth()->id(),
            'action'      => $action,
            'entity_type' => class_basename($entity),
            'entity_id'   => $entity->getKey(),
            'metadata'    => $metadata ?: null,
        ]);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    } // ← method closing brace was on the wrong level
}
