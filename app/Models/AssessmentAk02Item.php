<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAk02Item extends Model
{
    use HasFactory;

    protected $table = 't_assessment_ak02_items';

    protected $fillable = [
        'assessment_id',
        'competency_unit_id',
        'methods_json',
        'is_competent',
        'finding_notes',
        'assessor_id',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'methods_json' => 'array',
            'is_competent' => 'boolean',
            'recorded_at' => 'datetime',
        ];
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function competencyUnit(): BelongsTo
    {
        return $this->belongsTo(CompetencyUnit::class);
    }
}
