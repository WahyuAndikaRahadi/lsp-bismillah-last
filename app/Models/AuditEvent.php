<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditEvent extends Model
{
    protected $table = 't_audit_events';

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'before_payload',
        'after_payload',
        'reason',
        'request_id',
        'ip_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function record(
        string $action,
        ?Model $subject = null,
        ?string $reason = null,
        ?array $details = null,
        ?int $userId = null
    ): self {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'after_payload' => $details ? json_encode($details) : null,
            'reason' => $reason,
            'request_id' => request()?->header('X-Request-ID') ?? (string) \Illuminate\Support\Str::uuid(),
            'ip_address' => request()?->ip(),
        ]);
    }
}
