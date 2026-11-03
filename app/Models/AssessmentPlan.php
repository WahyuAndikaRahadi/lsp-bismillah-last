<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentPlan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'revision' => 'integer',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'lock_version' => 'integer',
        'approach_data' => 'array',
        'work_groups' => 'array',
        'modifications' => 'array',
        'confirmation_data' => 'array',
        'validated_at' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class);
    }

    public function schemeVersion(): BelongsTo
    {
        return $this->belongsTo(SchemeVersion::class, 'scheme_version_id');
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function isEditableBy(User $user): bool
    {
        if ($this->is_locked) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return (int) $user->id === (int) $this->assessor_id;
    }

    /**
     * Invalidate previously accepted AK-01 agreements when plan changes substantively.
     */
    public function invalidateAcceptedAgreements(string $reason = 'Rencana asesmen (MAPA-01) mengalami revisi substantif.'): int
    {
        $query = Assessment::query();

        if ($this->assessment_id) {
            $query->where('id', $this->assessment_id);
        } else {
            $query->where('schedule_id', $this->schedule_id);
        }

        $affected = $query->whereNotNull('ak01_agreed_at')->get();

        foreach ($affected as $assessment) {
            $assessment->update([
                'ak01_agreed_at' => null,
            ]);

            AuditEvent::record(
                action: 'mapa01_invalidated_ak01',
                subject: $assessment,
                reason: $reason,
                details: [
                    'plan_id' => $this->id,
                    'revision' => $this->revision,
                ]
            );
        }

        return $affected->count();
    }
}
