<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentIaResponse extends Model
{
    use HasFactory;

    protected $table = 't_assessment_ia_responses';

    protected $fillable = [
        'assessment_id',
        'instrument_code',
        'started_at',
        'duration_seconds',
        'time_remaining_seconds',
        'submitted_at',
        'attempt_version',
        'is_expired',
        'data_json',
        'score',
        'assessor_notes',
        'asesi_feedback',
        'assessor_signature',
        'assessor_signed_at',
        'asesi_signature',
        'asesi_signed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'duration_seconds' => 'integer',
            'time_remaining_seconds' => 'integer',
            'attempt_version' => 'integer',
            'is_expired' => 'boolean',
            'data_json' => 'array',
            'score' => 'decimal:2',
            'assessor_signed_at' => 'datetime',
            'asesi_signed_at' => 'datetime',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null || $this->asesi_signed_at !== null;
    }
}
