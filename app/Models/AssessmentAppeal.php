<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssessmentAppeal extends Model
{
    use HasFactory;

    protected $table = 't_assessment_appeals';

    protected $fillable = [
        'assessment_id',
        'appeal_date',
        'prosedur_dijelaskan',
        'diskusi_dengan_asesor',
        'melibatkan_orang_lain',
        'asesi_signature',
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
            'prosedur_dijelaskan' => 'boolean',
            'diskusi_dengan_asesor' => 'boolean',
            'melibatkan_orang_lain' => 'boolean',
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

    public function investigations(): HasMany
    {
        return $this->hasMany(AppealInvestigation::class, 'appeal_id');
    }

    public function latestInvestigation(): HasOne
    {
        return $this->hasOne(AppealInvestigation::class, 'appeal_id')->latestOfMany();
    }

    public static function getAppealDeadlineDays(): ?int
    {
        return config('services.lsp.appeal_deadline_days', null);
    }

    public static function isAppealEligible(Assessment $assessment): bool
    {
        $days = self::getAppealDeadlineDays();
        if ($days === null) {
            return true;
        }

        $baseDate = $assessment->final_result_at ?? $assessment->completed_at ?? $assessment->updated_at;
        if (! $baseDate) {
            return true;
        }

        return now()->startOfDay()->lte($baseDate->copy()->addDays($days)->endOfDay());
    }
}
