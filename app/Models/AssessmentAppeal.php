<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAppeal extends Model
{
    use HasFactory;

    protected $table = 't_assessment_appeals';

    protected $fillable = [
        'assessment_id',
        'appeal_date',
        'appeal_reasons',
        'contested_units',
        'status',
        'decision_notes',
        'decision_date',
        'decided_by',
        'appeals_committee_chair',
        'assessor_notes',
    ];

    protected function casts(): array
    {
        return [
            'appeal_date' => 'date',
            'decision_date' => 'date',
            'contested_units' => 'array',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
