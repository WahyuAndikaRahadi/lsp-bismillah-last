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
}
