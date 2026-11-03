<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleConsent extends Model
{
    protected $table = 't_schedule_consents';

    protected $fillable = [
        'schedule_id',
        'user_id',
        'purpose',
        'scope_version',
        'text_hash',
        'signature_data',
        'ip_address',
        'user_agent',
        'granted_at',
        'revoked_at',
        'revoked_by',
        'revocation_reason',
    ];

    protected $casts = [
        'scope_version' => 'integer',
        'granted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function revoker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function isActive(): bool
    {
        return $this->granted_at !== null && $this->revoked_at === null;
    }
}
