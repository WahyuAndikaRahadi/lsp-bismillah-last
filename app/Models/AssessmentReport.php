<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentReport extends Model
{
    use HasFactory;

    protected $table = 't_assessment_reports';

    protected $fillable = [
        'schedule_id',
        'assessor_id',
        'report_type',
        'revision',
        'roster_json',
        'content_json',
        'status',
        'assessor_signature',
        'assessor_signed_at',
        'submitted_at',
        'reviewer_id',
        'reviewed_at',
        'reviewer_notes',
        'lock_version',
    ];

    protected function casts(): array
    {
        return [
            'roster_json' => 'array',
            'content_json' => 'array',
            'assessor_signed_at' => 'datetime',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'revision' => 'integer',
            'lock_version' => 'integer',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
