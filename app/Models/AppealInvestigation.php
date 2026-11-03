<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppealInvestigation extends Model
{
    use HasFactory;

    protected $table = 't_appeal_investigations';

    protected $fillable = [
        'appeal_id',
        'investigator_id',
        'investigator_role',
        'investigation_date',
        'investigation_place',
        'factual_findings',
        'recommendation',
        'recommendation_notes',
        'decided_by',
        'decided_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'investigation_date' => 'date',
            'decided_at' => 'datetime',
        ];
    }

    public function appeal(): BelongsTo
    {
        return $this->belongsTo(AssessmentAppeal::class, 'appeal_id');
    }

    public function investigator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investigator_id');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
