<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificationDecision extends Model
{
    use HasFactory;

    protected $table = 't_certification_decisions';

    protected $fillable = [
        'assessment_id',
        'schedule_id',
        'pleno_meeting_id',
        'decision',
        'decision_notes',
        'corrective_action',
        'validation_notes',
        'decided_by',
        'authority_role',
        'decided_at',
        'evidence_verified',
        'is_superseded',
        'superseded_by',
        'superseded_at',
        'supersede_reason',
        'status',
        'lock_version',
    ];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
            'superseded_at' => 'datetime',
            'evidence_verified' => 'boolean',
            'is_superseded' => 'boolean',
            'lock_version' => 'integer',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function plenoMeeting(): BelongsTo
    {
        return $this->belongsTo(PlenoMeeting::class);
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function supersededBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'superseded_by');
    }
}
