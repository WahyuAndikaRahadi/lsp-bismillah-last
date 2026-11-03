<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleMinutes extends Model
{
    use HasFactory;

    protected $table = 't_schedule_minutes';

    protected $fillable = [
        'schedule_id',
        'document_code',
        'revision',
        'actual_date',
        'actual_start_time',
        'actual_end_time',
        'location',
        'enrolled_count',
        'present_count',
        'absent_count',
        'competent_count',
        'not_competent_count',
        'pending_count',
        'roster_summary_json',
        'coordinator_id',
        'coordinator_name',
        'coordinator_signature',
        'coordinator_signed_at',
        'lead_assessor_id',
        'lead_assessor_signature',
        'lead_assessor_signed_at',
        'notes',
        'status',
        'lock_version',
    ];

    protected function casts(): array
    {
        return [
            'actual_date' => 'date',
            'roster_summary_json' => 'array',
            'coordinator_signed_at' => 'datetime',
            'lead_assessor_signed_at' => 'datetime',
            'enrolled_count' => 'integer',
            'present_count' => 'integer',
            'absent_count' => 'integer',
            'competent_count' => 'integer',
            'not_competent_count' => 'integer',
            'pending_count' => 'integer',
            'revision' => 'integer',
            'lock_version' => 'integer',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function leadAssessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_assessor_id');
    }
}
