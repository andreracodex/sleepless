<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'actor_type',
        'actor_id',
        'action',
        'entity_type',
        'entity_id',
        'before_json',
        'after_json',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'before_json' => 'array',
        'after_json' => 'array',
        'created_at' => 'datetime',
    ];

    public static function log(
        string $action,
        string $entityType,
        string|int $entityId,
        ?array $before = null,
        ?array $after = null,
        ?string $actorType = 'system',
        ?string $actorId = null
    ): self {
        return self::create([
            'actor_type' => $actorType,
            'actor_id' => $actorId ?? '1',
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => (string) $entityId,
            'before_json' => $before,
            'after_json' => $after,
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent() ?? 'CLI/System',
            'created_at' => now(),
        ]);
    }
}
