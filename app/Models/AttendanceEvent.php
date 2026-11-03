<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceEvent extends Model
{
    protected $table = 't_attendance_events';

    protected $fillable = [
        'schedule_id',
        'user_id',
        'session_code',
        'event_type',
        'source_record',
        'occurred_at',
        'recorded_by',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
