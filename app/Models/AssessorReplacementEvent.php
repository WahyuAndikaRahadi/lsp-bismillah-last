<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessorReplacementEvent extends Model
{
    use HasFactory;

    protected $table = 't_assessor_replacement_events';

    protected $fillable = [
        'assessment_id',
        'schedule_id',
        'previous_assessor_id',
        'replacement_assessor_id',
        'reason',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
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

    public function previousAssessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'previous_assessor_id');
    }

    public function replacementAssessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replacement_assessor_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
