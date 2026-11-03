<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveillanceReport extends Model
{
    use HasFactory;

    protected $table = 't_surveillance_reports';

    protected $fillable = [
        'surveillance_plan_id',
        'certificate_id',
        'asesi_id',
        'activity_summary',
        'evidence_file_path',
        'submitted_at',
        'evaluator_id',
        'evaluator_notes',
        'evaluated_at',
        'decision',
        'decision_notes',
        'decided_by',
        'decided_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'evaluated_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SurveillancePlan::class, 'surveillance_plan_id');
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id');
    }

    public function asesi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asesi_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
